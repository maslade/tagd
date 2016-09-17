<?php

namespace Tagd\Controllers;

class ShortcodeTagd extends Base  {
    const SHORTCODE = 'tagd';
    
    public function __construct() {
        add_shortcode( self::SHORTCODE, array( $this, 'do_shortcode' ) );
    }
    
    public function do_shortcode( $atts ) {
        // No $atts supported yet.
        wp_enqueue_script( \Tagd\SCRIPT_TAGP );
        $args = array( 'view' => 'slideshow.php' );
        $view = new \Tagd\Views\Base( $args );
        return $view->get_rendered();
    }
}
