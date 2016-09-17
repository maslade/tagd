<?php

namespace Tagd\Controllers;

class RPC {
    public $public_endpoints = array( \Tagd\EP_TAG_AUTOCOMPLETE );
    
    public $maximum_results = 15;
    
    public function __construct() {
        add_action( 'init', array( $this, 'register_endpoints' ) );
    }
    
    public function register_endpoints() {
        foreach ( $this->public_endpoints as $ep ) {
            add_action( sprintf( 'wp_ajax_%s', $ep ), array( $this, 'ep_handler' ) );
            add_action( sprintf( 'wp_ajax_nopriv_%s', $ep ), array( $this, 'ep_handler' ) );
        }
        foreach ( $this->private_endpoints as $ep ) {
            add_action( sprintf( 'wp_ajax_%s', $ep ), array( $this, 'ep_handler' ) );
        }
    }
    
    public function ep_handler() {
        $action = wp_unslash( $_GET['action'] );
        
        switch ( $action ) {
            case \Tagd\EP_TAG_AUTOCOMPLETE:
                $this->tag_autocomplete();
                break;
        }
        
        die;
    }
    
    public function send_json( $object ) {
        header( 'Content-type: application/json' );
        echo json_encode( $object );
    }
    
    public function tag_autocomplete() {
        $settings = new \Tagd\Models\Settings();
        $search = wp_unslash( $_GET['tag'] );
        $args = array( 'name__like' => $search,
                       'hide_empty' => false,
                       'taxonomy' => $settings->item_taxonomy,
                       'number' => $this->maximum_results,
        );
        
        $terms = get_terms( $args );
        usort( $terms, array( $this, 'sort_terms_by_count' ) );
        
        $suggestions = array();
        
        foreach ( $terms as $term ) {
            $suggestions[] = array(
                'label' => sprintf( '%s (%d)', $term->name, $term->count ),
                'value' => $term->slug
            );
        }
        
        $this->send_json( $suggestions );
    }
    
    public function sort_terms_by_count( $a, $b ) {
        return (int) $b->count - (int) $a->count;
    }
}