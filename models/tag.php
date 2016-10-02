<?php

namespace Tagd\Models;

class Tag implements \JsonSerializable {
    public static $label_format = '%s (%d)';

    public $term;

    /**
     * Create a tag from a term_id, a WP_Term, or the raw row results from the
     * terms table.
     * 
     * @param int|stdClass|WP_Term $term
     */
    public function __construct( $term ) {
        $this->term = get_term( $term );
    }
    
    /**
     * Provided as a convenient target for array_map( $term_objects, ...).
     * 
     * @param \WP_Term $term
     * @return \Tagd\Models\Tag
     */
    public static function get( \WP_Term $term ) {
        return new self( $term );
    }
    
    /**
     * Return a term by name, creating it if it does not exist.
     * 
     * @param string $term_name
     */
    public static function get_or_make( $term_name ) {
        $settings = new Settings();
        $term = get_term_by( 'name', $term_name, $settings->item_taxonomy );
        
        if ( ! $term ) {
            $term_ids = wp_insert_term( $term_name, $settings->item_taxonomy );
            $term = get_term( $term['term_id'], $settings->item_taxonomy );
        }
        
        return new self( $term );
    }
    
    public function label() {
        return sprintf( self::$label_format, $this->term->name, $this->term->count );
    }
    
    public function jsonSerialize() {
        return array(
            'name' => $this->term->name,
            'id' => $this->term->term_id,
            'label' => $this->label(),
            'count' => $this->term->count,
            'desc' => $this->term->description,
        );
    }
}