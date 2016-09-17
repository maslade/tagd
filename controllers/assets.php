<?php

namespace Tagd\Controllers;

class Assets extends Base {
    public $scripts = array(
        \Tagd\SCRIPT_TAGD => array( 'file' => 'assets/js/tagd.js' ),
    );
    
    public $css = array(
        \Tagd\STYLE_TAGD_FRONT_END => array( 'file' => 'assets/js/tagd-front.js' ),
    );
    
    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'register' ) );
    }
    
    public function register() {
        array_map( array( $this, 'register_script' ), array_keys( $this->scripts ), $this->scripts );
        array_map( array( $this, 'register_style' ), array_keys( $this->css ), $this->css );
    }
    
    protected function register_style( $handle, $stylesheet ) {
        if ( ! $stylesheet[ 'file' ] ) {
            _doing_it_wrong( __FUNCTION__, '$stylesheet must include key "file"', null );
        }
        
        $src = sprintf( '%s/%s', $this->plugin_url(), $stylesheet['file'] );
        $deps = isset( $stylesheet['deps'] ) && is_array( $stylesheet['deps'] ) ? $stylesheet['deps'] : array();
        $media = isset( $stylesheet['media'] ) ? $stylesheet['media'] : 'all';
        
        wp_register_style( $handle, $src, $deps, \Tagd\PLUGIN_VERSION, $media );
    }
    
    protected function register_script( $handle, $script ) {
        if ( ! $script[ 'file' ] ) {
            _doing_it_wrong( __FUNCTION__, '$script must include key "file"', null );
        }
        
        $src = sprintf( '%s/%s', $this->plugin_url(), $script['file'] );
        $deps = isset( $script['deps'] ) && is_array( $script['deps'] ) ? $script['deps'] : array();
        $in_footer = isset( $scripts['in_footer'] ) ? (bool) $scripts['in_footer'] : true;
        
        wp_register_script( $handle, $src, $deps, \Tagd\PLUGIN_VERSION, $in_footer );
    }
}
