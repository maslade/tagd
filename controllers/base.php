<?php

namespace Tagd\Controllers;


class Base {
    public function plugin_url() {
        return rtrim( plugin_dir_url( \Tagd\PLUGIN_FILE ), '/' );
    }
}