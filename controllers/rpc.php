<?php

namespace Tagd\Controllers;

class RPC {
    public $public_endpoints = array(
        \Tagd\EP_TAG_AUTOCOMPLETE,
        \Tagd\EP_FEED,
        \Tagd\EP_UPDATE,
    );
    
    public $private_endpoints = array();
    
    public $maximum_suggestions = 15;
    
    public $feed_args = array(
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'paged' => 1,
        'posts_per_page' => 100,
        'orderby' => 'post_date',
        'order' => 'desc',
    );
    
    public $feed_filters = array(
        'ids' => false,
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
            
            case \Tagd\EP_UPDATE:
                $this->update();
                break;
        }
        
        die;
    }
    
    protected function send_json( $object ) {
        header( 'Content-type: application/json' );
        echo json_encode( $object );
    }
    
    protected function feed() {
        $user_filters = wp_unslash( isset( $_GET['filters'] ) ? $_GET['filters'] : array() );
        $filters = wp_parse_args( $user_filters, $this->feed_filters );
        
        if ( $filters['items'] ) {
            $query = $this->feed_posts( $filters );
        } else {
            $query = $this->feed_search( $filters );
        }
        $feed = array(
            'items' => array_map( array( '\Tagd\Models\Item', 'get' ), $query->posts ),
            'request' => $filters,
            'total_items' => $query->post_count,
            'page' => $query_args['paged'],
            'total_pages' => $query->max_num_pages,
        );
        
        $this->send_json( $feed );

    }
    
    protected function feed_posts( $filters ) {
        $settings = new \Tagd\Models\Settings();
        $query_args = $this->feed_args;
        
        $post_ids = array_map( 'intval', array_map( 'trim', explode( ',', $filters['items'] ) ) );
        $query_args['post__in'] = $post_ids;
        
        return new \WP_Query( $query_args );
    }
    
    protected function feed_search( $filters ) {
        $settings = new \Tagd\Models\Settings();
        $query_args = $this->feed_args;

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
        
        if ( $filters['unrated'] ) {
            $query_args['meta_query'] = array(
                array(
                    'key' => \Tagd\Models\Item::META_RATING,
                    'compare' => 'NOT EXISTS',
                )
            );
        }
        else if ( $filters['ratings'] ) {
            $query_args['meta_query'] = array(
                array(
                    'key' => \Tagd\Models\Item::META_RATING,
                    'compare' => 'IN',
                    'value' => array_map( create_function( '$x', 'return (int) $x + 1;' ), array_keys( $filters['ratings'], true ) ),
                )
            );
        }
        
        $query_args['paged'] = (int) $filters['page'];
        $query_args['orderby'] = 'rand';
 
        return new \WP_Query( $query_args );
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
    
    protected function update() {
        $item_id = isset( $_POST['id'] ) ? (int) $_POST['id'] : false;
        
        if ( ! $item_id ) {
            $this->send_json( false );
            return;
        }
        
        $rating = isset( $_POST['rating'] ) ? wp_unslash( $_POST['rating'] ) : false;
        $new_tag_id = isset( $_POST['new_tag_id'] ) ? (int) $_POST['new_tag_id'] : false;
        $new_tag_str = isset( $_POST['new_tag_str'] ) ? wp_unslash( $_POST['new_tag_str'] ) : false;
        $remove_tag_id = isset( $_POST['remove_tag_id'] ) ? (int) $_POST['remove_tag_id'] : false;

        $item = new \Tagd\Models\Item( $item_id );
        $response = array(
            'updated_item' => $item,
        );
        
        if ( $rating ) {
            $item->rate( $rating );
            $response[ 'new_rating' ] = $rating;
        }
        
        if ( $new_tag_str ) {
            $response['new_tag'] = $tag = \Tagd\Models\Tag::get_or_make( $new_tag_str );
            $item->add_tag( $tag );
        }
        
        if ( $new_tag_id ) {
            $response['new_tag'] = $tag = new \Tagd\Models\Tag( $new_tag_id );
            $item->add_tag( $tag );
        }
        
        if ( $remove_tag_id ) {
            $response[ 'removed_tag' ] = $tag = new \Tagd\Models\Tag( $remove_tag_id );
            $item->remove_tag( $tag );
        }
        
        $this->send_json( $response );
    }
    
    protected function sort_terms_by_count( $a, $b ) {
        return (int) $b->count - (int) $a->count;
    }
}