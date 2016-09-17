<?php

namespace Tagd\Views;

class Base {
    public $args;
    
    public function __construct( $args = array() ) {
        $this->args = $args;
    }
    
    public function render() {
        if ( isset( $this->args['view'] ) && $this->args['view'] ) {
            $basedir = sprintf( '%s/templates', __DIR__ );
            $view_path = realpath( sprintf( '%s/%s', $basedir, $this->args['view'] ) );
            
            if ( $basedir === substr( $view_path, 0, strlen( $basedir ) ) ) {
                require_once( $view_path );
            }
        }
    }
    
    public function get_rendered() {
        ob_start();
        $this->render();
        $rendered = ob_get_contents();
        ob_end_clean();
        
        return $rendered;
    }
}
