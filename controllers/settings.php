<?php

namespace Tagd\Controllers;

class Settings extends Base {
    public $settings = array();
    
    public $main_menu_hook_suffix;
    
    public function __construct() {
        $this->settings['main_menu'] = array( 
            'page_title' => __( 'Tagd Settings' ),
            'menu_title' => __( 'Tagd' ),
            'capability' => __( 'activate_plugins' ),
            'menu_slug' => 'tagd_settings',
            'function' => array( $this, 'do_settings_page' ),
            'icon_url' => 'dashicons-tag',
            'position' => 11,
        );            
        
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    }
    
    public function admin_menu() {
        $this->main_menu_hook_suffix = call_user_func_array( 'add_menu_page', $this->settings['main_menu'] );
    }
    
    public function do_settings_page() {
        $view = new \Tagd\Views\Base( array(
            'view' => 'admin-settings.php',
            'page_title' => $this->settings['main_menu']['page_title'],
        ) );
        $view->render();
    }
}