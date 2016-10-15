<?php

namespace Tagd\Controllers;

class Router extends Base {    
    public $query_var = 'tagd_view';
    
    public $template_name = 'tagd.php';
    
    protected $view_is_active = false;
    
    public function configure_assets() {
        global $wp_query;
        
        if ( $this->view_is_active ) {
            global $wp_scripts, $wp_styles;

            $wp_scripts->queue = array();
            
            foreach ( $wp_styles->queue as $index => $style ) {
                if ( $style !== 'admin-bar' ) {
                    unset( $wp_styles->queue[ $index ] );
                }
            }
            
            wp_enqueue_script( \Tagd\SCRIPT_TAGD );
            wp_enqueue_script( \Tagd\SCRIPT_BOOTSTRAP );
            wp_enqueue_script( 'wp-mediaelement' );
            wp_enqueue_style( \Tagd\STYLE_TAGD_FRONT_END );
            wp_enqueue_style( \Tagd\STYLE_BOOTSTRAP );
    		wp_enqueue_style( 'wp-mediaelement' );
        }
    }
    
    public function find_template() {
        $tpl = locate_template( $this->template_name );
        
        if ( ! $tpl ) {
            $tpl = sprintf( '%s/views/templates/viewer.php', $this->plugin_dir() );
        }
        
        return $tpl;
    }

    public function __construct() {
        add_action( 'template_redirect', array( $this, 'route' ) );
        add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'configure_assets' ), 99 );
    }

    public function add_query_vars( $vars ) {
        $vars[] = $this->query_var;
        return $vars;
    }
    
    public function add_rewrite_rules( $permalink ) {
        add_rewrite_rule( $permalink, sprintf( 'index.php?%s=default', $this->query_var ), 'top' );
        flush_rewrite_rules();
    }
    
    public function route() {
        global $wp_query;
        $view = $wp_query->get( $this->query_var );
        
        if ( $view === 'default' ) {
            $tpl = $this->find_template();
            if ( $tpl ) {
                $this->view_is_active = true;
                include( $tpl );
                die;
            }
        }
    }
}