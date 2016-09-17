<?php

namespace Tagd\Views;

function context_wrapper( $view, $_view_path ) {
    require_once( $_view_path );
}

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
                context_wrapper( $this, $view_path );
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
    
    protected function require_args() {
        $args_list = func_get_args();
        $missing = array();
        
        foreach ( $args_list as $arg ) {
            if ( ! isset( $this->args[ $arg ] ) ) {
                $missing[] = $arg;
            }
        }
        
        if ( $missing ) {
            $message = sprintf( 'This view requires args: %s', implode( ',', $missing ) );
            $function = sprintf( '%s::__construct', get_called_class() );
            _doing_it_wrong( $function, $message, null );
        }
    }
}
