<?php

namespace Tagd;

function load_controller( $filename ) {
    Loader::load_controller( $filename );
}

function load_admin_controller( $filename ) {
    if ( is_admin() ) {
        return load_controller( $filename );
    }
}

function load_ajax_controller( $filename ) {
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        return load_controller( $filename );
    }
}

function load_frontend_controller( $filename ) {
    if ( ! is_admin() ) {
        return load_controller( $filename );
    }
}

class Loader {
    public static $controllers = array();
    
    public static function load_controller( $filename ) {
        $basedir = sprintf( '%s/controllers', PLUGIN_PATH );
        $path = sprintf( '%s/%s', $basedir, $filename );
        $classname = substr( preg_replace_callback( '/(^.|-.)/', array( __CLASS__, 'loader_classname_callback' ), $filename ), 0, -4 );
        $class = sprintf( '%s\Controllers\%s', __NAMESPACE__, $classname );

        require_once( $path );
        return self::$controllers[ $classname ] = new $class();
    }
    
    protected static function loader_classname_callback( $matches ) {
        return strtoupper( strlen( $matches[1] ) === 1 ? $matches[1] : substr( $matches[1], 1 ) );
    }
}