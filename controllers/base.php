<?php

namespace Tagd\Controllers;


class Base {
    public function plugin_dir() {
        return rtrim( plugin_dir_path( \Tagd\PLUGIN_FILE ), '/' );
    }
    
    public function plugin_url() {
        return rtrim( plugin_dir_url( \Tagd\PLUGIN_FILE ), '/' );
    }
}