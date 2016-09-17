<?php

namespace Tagd\Views;

class AdminSettingsView extends Base {
    public $updated_msg = <<<'HTML'
        <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
            <p>
                <strong>Settings saved.</strong>
            </p>
            <button type="button" class="notice-dismiss">
                <span class="screen-reader-text">Dismiss this notice.</span>
            </button>
        </div>
HTML;
    
    public $ctl_permalink = <<<'HTML'
        <input name="tagd[permalink]" id="permalink" value="%1$s" autopopulate="off" class="regular-text" type="text">
HTML;
    
    public function __construct( $args ) {
        $args = wp_parse_args( $args, array(
            'view' => 'admin-settings.php',
            'permalink_help' => 'Enter the URL you want tagd to live at.  Note that you should not use this URL for anything else, such as a post or page, or these will supercede Tagd.',
            'did_update' => false,
            'settings_permalink' => '',
        ) );
        parent::__construct( $args );
        
        $this->require_args( 'nonce' );
    }
    
    public function page_title() {
        esc_html_e( $this->args['page_title'] );
    }
    
    public function nonce() {
        $name = sprintf( 'tagd[%s]', $this->args['nonce']['name'] );
        wp_nonce_field( $this->args['nonce']['action'], $name, $this->args['nonce']['referer'], true );
    }
    
    public function ctl_permalink() {
        printf( $this->ctl_permalink, $this->args['settings_permalink'] );
    }
    
    public function base_url() {
        printf( '%s/', esc_html( get_home_url() ) );
    }
    
    public function permalink_help() {
        esc_html_e( $this->args['permalink_help'] );
    }
    
    public function update_message() {
        if ( $this->args['did_update'] ) {
            echo $this->updated_msg;
        }
    }
}