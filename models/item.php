<?php

namespace Tagd\Models;

class Item implements \JsonSerializable {
    const META_RATING = 'tagd_rating';
    
    public static $dimensions_format = '%d x %d';

    public $attachment;
    
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
        return array_map( array( '\Tagd\Models\Tag', 'get' ), $terms ? $terms : array() );
    }
    
    public function add_tag( $term_id ) {
        $settings = new Settings();
        wp_add_object_terms( $this->attachment->ID, $term_id, $settings->item_taxonomy );
    }
    
    public function remove_tag( $term_id ) {
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
        return array(
            'id' => $this->attachment->ID,
            'rating' => $this->rating(),
            'tags' => $this->tags(),
            'date' => $this->attachment->post_date,
            'modified' => $this->attachment->post_modified,
            'title' => $this->filename(),
            'markup_full' => wp_get_attachment_image( $this->attachment->ID, $this->size( 'full' ) ),
            'markup_thumb' => wp_get_attachment_image( $this->attachment->ID, $this->size( 'medium' ) ),
            'markup_pinky' => wp_get_attachment_image( $this->attachment->ID, $this->size( 'thumb' ) ),
            'dimensions' => $this->dimensions()
        );
    }
    
    protected function size( $size ) {
        switch ( $size ) {
            case 'thumb':  return \Tagd\IMG_PINKY;
            case 'medium': return 'medium';
            default:       return 'full';
        }
    }
}