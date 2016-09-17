<?php

namespace Tagd\Controllers;

class RPC {
    public $public_endpoints = array(
        \Tagd\EP_TAG_AUTOCOMPLETE,
        \Tagd\EP_FEED
    );
    
    public $private_endpoints = array();
    
    public $maximum_results = 15;
    
    public $feed_args = array(
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'paged' => 1,
        'posts_per_page' => 3,
    );
    
    public $feed_filters = array(
        'page' => 0,
        'tags' => array(),
        'ratings' => null,
        'unrated' => false,
    );
    
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
            
            case \Tagd\EP_FEED:
                $this->feed();
                break;
        }
        
        die;
    }
    
    protected function send_json( $object ) {
        header( 'Content-type: application/json' );
        echo json_encode( $object );
    }
    
    protected function feed() {
        $settings = new \Tagd\Models\Settings();
        
        $query_args = $this->feed_args;
        $user_filters = wp_unslash( isset( $_GET['filters'] ) ? $_GET['filters'] : array() );
        $filters = wp_parse_args( $user_filters, $this->feed_filters );
        
        if ( is_string( $filters['tags'] ) ) {
            $filters['tags'] = explode( ',', $filters['tags'] );
        }
        
        if ( $filters['tags'] ) {
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => $settings->item_taxonomy,
                    'operator' => 'IN',
                    'field' => 'slug',
                    'terms' => $filters['tags'],
                )
            );
        }

        $query = new \WP_Query( $query_args );
        
        $feed = array(
            'items' => array_map( array( $this, 'format_for_output' ), $query->posts ),
            'filters' => $filters,
            'total_items' => $query->post_count,
            'page' => $query->paged,
            'total_pages' => $query->max_num_pages,
        );
        
        $this->send_json( $feed );
    }
    
    protected function format_for_output( $post ) {
        return array(
            'id' => $post->ID,
            'date' => $post->post_date,
            'modified' => $post->post_modified,
            'title' => $post->post_title,
            'markup_full' => wp_get_attachment_image( $post->ID, 'full' ),
            'markup_thumb' => wp_get_attachment_image( $post->ID, 'thumbnail' ),
            'markup_pinky' => '',
        );
    }
    
    protected function tag_autocomplete() {
        $settings = new \Tagd\Models\Settings();
        $search = wp_unslash( $_GET['term'] );
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
    
    protected function sort_terms_by_count( $a, $b ) {
        return (int) $b->count - (int) $a->count;
    }
}