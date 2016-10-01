<?php

namespace Tagd\Controllers;

class RPC {
    public $public_endpoints = array(
        \Tagd\EP_TAG_AUTOCOMPLETE,
        \Tagd\EP_FEED
    );
    
    public $private_endpoints = array();
    
    public $maximum_suggestions = 15;
    
    public $feed_args = array(
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'paged' => 1,
        'posts_per_page' => 30,
        'orderby' => 'post_date',
        'order' => 'desc',
    );
    
    public $feed_filters = array(
        'page' => 1,
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
        
        if ( ! is_array( $filters['tags'] ) ) {
            $filters['tags'] = array();
        }
        if ( $filters['tags'] ) {
            $filters['tags'] = array_map( 'intval', $filters['tags'] );
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => $settings->item_taxonomy,
                    'operator' => 'AND',
                    'field' => 'term_id',
                    'terms' => $filters['tags'],
                )
            );
        }
        
        $query_args['paged'] = (int) $filters['page'];
 
        $query = new \WP_Query( $query_args );
        
        $feed = array(
            'items' => array_map( array( '\Tagd\Models\Item', 'get' ), $query->posts ),
            'request' => $filters,
            'total_items' => $query->post_count,
            'page' => $query_args['paged'],
            'total_pages' => $query->max_num_pages,
        );
        
        $this->send_json( $feed );
    }
    
    protected function tag_autocomplete() {
        $settings = new \Tagd\Models\Settings();
        $search = wp_unslash( $_GET['term'] );
        $args = array( 'name__like' => $search,
                       'hide_empty' => false,
                       'taxonomy' => $settings->item_taxonomy,
                       'number' => $this->maximum_suggestions,
        );
        
        $terms = get_terms( $args );
        usort( $terms, array( $this, 'sort_terms_by_count' ) );
        $tags = array_map( array( '\Tagd\Models\Tag', 'get' ), $terms );
        
        $suggestions = array();
        
        foreach ( $tags as $tag ) {
            $suggestions[] = array(
                'label' => $tag->term->name,
                'value' => $tag->term->term_id
            );
        }
        
        $this->send_json( $suggestions );
    }
    
    protected function sort_terms_by_count( $a, $b ) {
        return (int) $b->count - (int) $a->count;
    }
}