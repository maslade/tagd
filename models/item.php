<?php

namespace Tagd\Models;

class Item implements \JsonSerializable {
    public static $dimensions_format = '%d x %d';

    public $attachment;
    
    public function __construct( $attachment = null ) {
        $this->attachment = get_post( $attachment );
        
        if ( $attachment->post_type !== 'attachment' ) {
            \Tagd\doing_it_wrong( __METHOD__, __FILE__, __LINE__, 'Attempting to load a non-attachment as an item.' );
        }
    }
    
    public static function get( $post ) {
        return new self( $post );
    }
    
    public function tags() {
        $settings = new Settings();
        $terms = get_the_terms( $this->attachment, $settings->item_taxonomy );
        return array_map( array( '\Tagd\Models\Tag', 'get' ), $terms );
    }
    
    public function dimensions() {
        $info = wp_get_attachment_metadata( $this->attachment->ID );
        $fmt = __( self::$dimensions_format, 'tatd' );
        return sprintf( $fmt, $info['width'], $info['height'] );
    }
    
    public function jsonSerialize() {
        return array(
            'id' => $this->attachment->ID,
            'tags' => $this->tags(),
            'date' => $this->attachment->post_date,
            'modified' => $this->attachment->post_modified,
            'title' => $this->attachment->post_title,
            'markup_full' => wp_get_attachment_image( $this->attachment->ID, 'full' ),
            'markup_thumb' => wp_get_attachment_image( $this->attachment->ID, 'thumbnail' ),
            'markup_pinky' => '',
            'dimensions' => $this->dimensions()
        );
    }
}