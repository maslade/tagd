<?php

namespace Tagd;

function load_controller( $filename ) {
    $basedir = sprintf( '%s/controllers', PLUGIN_PATH );
    $path = sprintf( '%s/%s', $basedir, $filename );
    $classname = substr( preg_replace_callback( '/(^.|-.)/', __NAMESPACE__ . '\loader_callback', $filename ), 0, -4 );
    $class = sprintf( '%s\Controllers\%s', __NAMESPACE__, $classname );
    
    require_once( $path );
    new $class();
}

function loader_callback( $matches ) {
    return strtoupper( strlen( $matches[1] ) === 1 ? $matches[1] : substr( $matches[1], 1 ) );
}