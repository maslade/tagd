<?php

namespace Tagd;

function load_controller( $filename ) {
    Loader::load_controller( $filename );
}

function loader_callback( $matches ) {
    return strtoupper( strlen( $matches[1] ) === 1 ? $matches[1] : substr( $matches[1], 1 ) );
}

class Loader {
    static $controllers = array();
    
    public static function load_controller( $filename ) {
        $basedir = sprintf( '%s/controllers', PLUGIN_PATH );
        $path = sprintf( '%s/%s', $basedir, $filename );
        $classname = substr( preg_replace_callback( '/(^.|-.)/', __NAMESPACE__ . '\loader_callback', $filename ), 0, -4 );
        $class = sprintf( '%s\Controllers\%s', __NAMESPACE__, $classname );

        require_once( $path );
        return self::$controllers[ $classname ] = new $class();
    }
}