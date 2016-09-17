<?php

namespace Tagd\Controllers;

class Viewer extends Base {
    public $permalink;
    
    public $query_var = 'tagd_view';
    
    public $template_name = 'tagd.php';
    
    protected $view_is_active = false;
    
    public function __construct() {
        $settings = new \Tagd\Models\Settings();
        $this->permalink = $settings->get_permalink();
        
        add_action( 'init', array( $this, 'add_rewrite_rules' ) );
        add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
        add_action( 'template_redirect', array( $this, 'route' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'configure_assets' ), 99 );
    }
    
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
            wp_enqueue_style( \Tagd\STYLE_TAGD_FRONT_END );
            wp_enqueue_style( \Tagd\STYLE_BOOTSTRAP );
        }
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
    
    protected function find_template() {
        $tpl = locate_template( $this->template_name );
        
        if ( ! $tpl ) {
            $tpl = sprintf( '%s/views/templates/viewer.php', $this->plugin_dir() );
        }
        
        return $tpl;
    }
    
    public function add_rewrite_rules() {
        add_rewrite_rule( $this->permalink,
                          sprintf( 'index.php?%s=default', $this->query_var ),
                          'top' );
    }
    
    public function add_query_vars( $vars ) {
        $vars[] = $this->query_var;
        return $vars;
    }
}