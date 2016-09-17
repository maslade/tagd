<?php

namespace Tagd\Controllers;

class DataStructure extends Base {
    public $item_taxonomy;
            
    public function __construct() {
        $settings = new \Tagd\Models\Settings();
        $this->item_taxonomy = $settings->item_taxonomy;
        add_action( 'init', array( $this, 'countable_attachment_terms' ) );
        add_action( 'init', array( $this, 'register_taxonomies' ) );
    }
    
    public function countable_attachment_terms() {
        global $wp_taxonomies;
        $wp_taxonomies['post_tag']->update_count_callback = '_update_generic_term_count';
    }
    
    public function register_taxonomies() {
        register_taxonomy_for_object_type( $this->item_taxonomy, 'attachment' );
    }
}