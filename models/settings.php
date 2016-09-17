<?php

namespace Tagd\Models;

class Settings {
    public $option_prefix = 'tagd_';
    
    public $item_taxonomy = 'post_tag';
    
    public function save( $name, $value, $autoload = false ) {
        return update_option( $this->option_prefix . $name, $value, $autoload );
    }
    
    public function get( $name, $default = false ) {
        return get_option( $this->option_prefix . $name, $default);
    }
    
    public function __call( $method, $args ) {
        if ( substr( $method, 0, 5 ) === 'save_' ) {
            array_unshift( $args, substr( $method, 5 ) );
            return call_user_func_array( array( $this, 'save' ), $args );
        }
        
        if ( substr( $method, 0, 4 ) === 'get_' ) {
            array_unshift( $args, substr( $method, 4 ) );
            return call_user_func_array( array( $this, 'get' ), $args );
        }
    }
}
