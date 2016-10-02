<?php

namespace Tagd\Controllers;

class Media extends Base {
    public function __construct() {
        add_action( 'init', array( $this, 'init' ) );
    }
    
    public function init() {
        $crop = true;
        
        add_image_size( \Tagd\IMG_PINKY,
                        \Tagd\DIMS_PINKY_W,
                        \Tagd\DIMS_PINKY_H,
                        $crop
        );
    }
}