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
const PLUGIN_FILE = __FILE__;
const PLUGIN_VERSION = '0.0.1';

const EP_FEED = 'tagd_feed';
const EP_TAG_AUTOCOMPLETE = 'tagd_autocomplete';
const EP_UPDATE = 'tagd_update';

const SCRIPT_TAGD = 'tagd_js';
const SCRIPT_BOOTSTRAP = 'tagd_bootstrap_js';
const SCRIPT_JQUERY_UI = 'tagd_jquery_ui';

const STYLE_TAGD_FRONT_END = 'tagd_css';
const STYLE_BOOTSTRAP = 'tagd_bootstrap_css';
const STYLE_JQUERY_UI = 'tagd_jquery_ui';

const IMG_PINKY = 'tagd_pinky';
const DIMS_PINKY_W = 160;
const DIMS_PINKY_H = 160;

require_once __DIR__ . '/views/base.php';
require_once __DIR__ . '/views/admin-settings.php';
require_once __DIR__ . '/views/viewer.php';
require_once __DIR__ . '/controllers/base.php';
require_once __DIR__ . '/models/settings.php';
require_once __DIR__ . '/models/item.php';
require_once __DIR__ . '/models/tag.php';
require_once __DIR__ . '/lib/loader.php';
require_once __DIR__ . '/lib/diagnostics.php';

load_controller( 'data-structure.php' );
load_controller( 'media.php' );
load_controller( 'assets.php' );
load_controller( 'router.php' );
load_admin_controller( 'admin-settings.php' );
load_ajax_controller( 'rpc.php' );


function tagd( $controller ) {
    return Loader::$controllers[ $controller ];
}