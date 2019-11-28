<?php

use BP\Controller;
use BP\CoreTheme;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

// Setup Package Dir
define('BP_PACKAGE_DIR', __DIR__);

// Define Version
define('VERSION', '4.1.0');

if ( !defined('ROOT_DIR') ) {
    throw new Exception('Boilerplate requires a ROOT_DIR constant set.');
}

// Load Site Config
$_config = ( is_readable(ROOT_DIR . '/../.config.yml') ? '/../.config.yml' : '/.config.yml' );
$_config = Spyc::YAMLLoad(ROOT_DIR . $_config);

// Detect Admin Mode
if ( array_key_exists( 'admin_mode', $_config ) && $_config['admin_mode'] === true && $_config['environment'] === 'dev' ) {
    require_once BP_PACKAGE_DIR . '/core/ui/setup.php';
}

// Setup Whoops
$_whoops = new Run;

// For Development, show errors
if ( $_config['environment'] == 'dev' ) {
    $_whoops->prependHandler(new PrettyPageHandler);
}

$_whoops->register();

// Load Path/URL Setup
require_once BP_PACKAGE_DIR . '/core/bootstrap/url.php';

// Create Theme Object
$_theme = new CoreTheme('_templates');

// Include functions, classes & plugins
require_once BP_PACKAGE_DIR . '/core/includes.php';

// Action: template.init
do_action( 'template.init', $_theme );

// Run Router
router()->run();

// Render the Page
echo $_theme->render( apply_filters( 'template.render', $_theme->load($_path) ), get() );