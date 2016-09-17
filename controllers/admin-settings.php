<?php

namespace Tagd\Controllers;

class AdminSettings extends Base {
    public $settings = array();
    
    public $main_menu_hook_suffix;
    
    protected $did_update = false;
    protected $settings_model;
    
    public function __construct() {
        $this->settings['main_menu'] = array( 
            'page_title' => __( 'Tagd Settings' ),
            'menu_title' => __( 'Tagd' ),
            'capability' => __( 'activate_plugins' ),
            'menu_slug' => 'tagd_settings',
            'function' => array( $this, 'do_settings_page' ),
            'icon_url' => 'dashicons-tag',
            'position' => 11,
            'nonce' => array(
                'action' => 'tagd_settings',
                'name' => 'csrf_tok',
                'referer' => true,
            ),
        );
        
        $this->settings_model = new \Tagd\Models\Settings();
        
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    }
    
    public function admin_menu() {
        $this->main_menu_hook_suffix = call_user_func_array( 'add_menu_page', $this->settings['main_menu'] );
    }
    
    public function do_settings_page() {
        $this->handle_input();
        
        $view = new \Tagd\Views\AdminSettingsView( array(
            'page_title' => $this->settings['main_menu']['page_title'],
            'nonce' => $this->settings['main_menu']['nonce'],
            'did_update' => $this->did_update,
            'settings_permalink' => $this->settings_model->get_permalink(),
        ) );
        $view->render();
    }
    
    protected function handle_input() {
        $input = isset( $_POST['tagd'] ) ? stripslashes_deep( $_POST['tagd'] ) : array();
        $nonce = isset( $input[ $this->settings['main_menu']['nonce']['name'] ] ) ? $input[ $this->settings['main_menu']['nonce']['name'] ] : null;
        $action = $this->settings['main_menu']['nonce']['action'];

        if ( $nonce && wp_verify_nonce( $nonce, $action ) ) {
            $this->did_update = $this->settings_model->save_permalink( $input['permalink'] );
        }
    }
}