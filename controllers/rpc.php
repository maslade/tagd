<?php

namespace Tagd\Controllers;

class RPC {
    public $public_endpoints = array( \Tagd\EP_TAG_AUTOCOMPLETE );
    
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
        $search = wp_unslash( $_GET['tag'] );
        $suggestions = get_tag_regex( $search );
        $args = array( 'name__like' => $search, 'hide_empty' => false, 'taxonomy' => 'post_tag' );
        $this->send_json( get_terms( $args ) );
    }
}