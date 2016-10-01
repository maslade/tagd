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
    
    public function label() {
        return sprintf( self::$label_format, $this->term->name, $this->term->count );
    }
    
    public function jsonSerialize() {
        return array(
            'name' => $this->term->name,
            'slug' => $this->term->slug,
            'label' => $this->label(),
            'count' => $this->term->count,
            'desc' => $this->term->description,
        );
    }
}