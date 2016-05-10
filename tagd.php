<?php
/*
Plugin Name: Tagd
Plugin URI: http://www.example.com/
Description: Taggable image board.
Version: 0.0.1
Author: Sandfox
*/

namespace Tagd;

const PLUGIN_PATH = __DIR__;

require_once __DIR__ . '/views/base.php';
require_once __DIR__ . '/lib/loader.php';

load_controller( 'shortcode-tagd.php' );