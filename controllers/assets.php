<?php

namespace Tagd\Controllers;

class Assets extends Base {
    public $scripts = array(
        \Tagd\SCRIPT_JQUERY_UI => array( 'file' => 'assets/jquery-ui/jquery-ui.min.js', 'deps' => array( 'jquery' ) ),
        \Tagd\SCRIPT_BOOTSTRAP => array( 'file' => 'assets/bootstrap/bootstrap.min.js' ),
        \Tagd\SCRIPT_TAGD => array( 'file' => 'assets/js/tagd.js', 'deps' => array( 'jquery', \Tagd\SCRIPT_JQUERY_UI ) ),
    );
    
    public $script_manifests = array();
    
    public $css = array(
        \Tagd\STYLE_JQUERY_UI => array( 'file' => 'assets/jquery-ui/jquery-ui.min.css' ),
        \Tagd\STYLE_BOOTSTRAP => array( 'file' => 'assets/bootstrap/bootstrap.css' ),
        \Tagd\STYLE_TAGD_FRONT_END => array( 'file' => 'assets/css/tagd-front.css', 'deps' => array( \Tagd\STYLE_JQUERY_UI ) ),
    );
    
    public function __construct() {
        $this->script_manifests[ \Tagd\SCRIPT_TAGD ] = array(
            'rpc' => array( 
                'feed' => $this->rpc_url( \Tagd\EP_FEED ),
                'tag_autocomplete' => $this->rpc_url( \Tagd\EP_TAG_AUTOCOMPLETE ),
            ),
            'lang' => array(
                'no_results' => __( 'No results.', 'tagd' ),
            ),
        );

        add_action( 'wp_enqueue_scripts', array( $this, 'register' ) );
    }
    
    protected function rpc_url( $endpoint ) {
        return add_query_arg( 'action', $endpoint, admin_url( 'admin-ajax.php' ) );
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
        
        $this->maybe_register_manifest( $handle );
    }
    
    protected function maybe_register_manifest( $handle ) {
        if ( isset( $this->script_manifests[ $handle ] ) ) {
            wp_localize_script( $handle, $handle, $this->script_manifests[ $handle ] );
        }
    }
}
