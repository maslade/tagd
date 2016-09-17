<?php

namespace Tagd\Models;

class Tag implements \JsonSerializable {
    public static $label_format = '%s (%d)';

    public $term;
    
    public function __construct( $term ) {
        $this->term = get_term( $term );
    }
    
    public static function get( $term ) {
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