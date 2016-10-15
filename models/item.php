<?php

namespace Tagd\Models;

class Item implements \JsonSerializable {
    const META_RATING = 'tagd_rating';
    
    public static $dimensions_format = '%d x %d';

    public $attachment;
    
    public $autoplay = true; // for videos
    
    public function __construct( $attachment = null ) {
        $this->attachment = get_post( $attachment );
        
        if ( $this->attachment->post_type !== 'attachment' ) {
            \Tagd\doing_it_wrong( __METHOD__, __FILE__, __LINE__, 'Attempting to load a non-attachment as an item.' );
        }
    }
    
    public static function get( $post ) {
        return new self( $post );
    }
    
    public function tags() {
        $settings = new Settings();
        $terms = get_the_terms( $this->attachment, $settings->item_taxonomy );
        $terms = array_map( array( '\Tagd\Models\Tag', 'get' ), $terms ? $terms : array() );
        usort( $terms, array( $this, 'sort_tags' ) );
        return $terms;
    }
    
    protected function sort_tags( $a, $b) { 
        return $b->term->count - $a->term->count;
    }
    
    public function add_tag( $term_id ) {
        if ( $term_id instanceof Tag ) {
            $term_id = $term_id->term->term_id;
        }
        
        $settings = new Settings();
        wp_add_object_terms( $this->attachment->ID, $term_id, $settings->item_taxonomy );
    }
    
    public function remove_tag( $term_id ) {
        if ( $term_id instanceof Tag ) {
            $term_id = $term_id->term->term_id;
        }
        
        $settings = new Settings();
        wp_remove_object_terms( $this->attachment->ID, $term_id, $settings->item_taxonomy);
    }
    
    public function dimensions() {
        $info = wp_get_attachment_metadata( $this->attachment->ID );
        $fmt = __( self::$dimensions_format, 'tagd' );
        return sprintf( $fmt, $info['width'], $info['height'] );
    }
    
    public function filename() {
        return basename( get_attached_file( $this->attachment->ID ) );
    }
    
    public function rate( $new_rating ) {
        return update_post_meta( $this->attachment->ID, self::META_RATING, (int) $new_rating );
    }
    
    public function rating() {
        return get_post_meta( $this->attachment->ID, self::META_RATING, true );
    }
    
    public function jsonSerialize() {
        $serial = array(
            'id' => $this->attachment->ID,
            'rating' => $this->rating(),
            'tags' => $this->tags(),
            'date' => $this->attachment->post_date,
            'modified' => $this->attachment->post_modified,
            'title' => $this->filename(),
            'markup_full' => $this->display_markup(),
            'markup_pinky' => $this->pinky_markup(),
            'dimensions' => $this->dimensions()
        );
        
        if ( current_user_can( 'upload_files' ) ) {
            $serial['admin_edit'] = str_replace( '&amp;', '&', get_edit_post_link( $this->attachment->ID ) );
        }
        
        return $serial;
    }
    
    public function pinky_markup() {
        $icon = false;
        
        if ( strpos( $this->attachment->post_mime_type, 'video/' ) === 0 ) {
            $icon = true;
        }
        
        return wp_get_attachment_image( $this->attachment->ID, $this->size( 'thumb' ), $icon, array( 'class' => 'img-thumbnail' ) );
    }
    
    public function display_markup() {
        if ( strpos( $this->attachment->post_mime_type, 'video/' ) === 0 ) {
            $embed = wp_video_shortcode( array( 'src' => wp_get_attachment_url( $this->attachment->ID ),
                                         'autoplay' => $this->autoplay ) );

            if ( in_array( $this->attachment->post_mime_type, array( 'video/x-flv', 'video/x-ms-wmv' ) ) ) {
                $embed = sprintf( '<div id="%1$s">%2$s</div><script type="text/javascript">if ( typeof MediaElementPlayer !== "undefined" ) window.tagd_mep = MediaElementPlayer( jQuery( "#%1$s video" ).get( 0 ) );</script>',
                    uniqid(),
                    $embed
                );
            }

            return $embed;
        }
        
        return wp_get_attachment_image( $this->attachment->ID, $this->size( 'full' ) );
    }
    
    protected function size( $size ) {
        switch ( $size ) {
            case 'thumb':  return \Tagd\IMG_PINKY;
            case 'medium': return 'medium';
            default:       return 'full';
        }
    }
}