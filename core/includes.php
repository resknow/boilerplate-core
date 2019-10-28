<?php

// Load Dev Functions
require_once ROOT_DIR . '/_includes/core/functions/dev.php';

// Load Router, Filters & Triggers
require_once ROOT_DIR . '/_includes/core/functions/router.php';
require_once ROOT_DIR . '/_includes/core/functions/filters.php';
require_once ROOT_DIR . '/_includes/core/functions/triggers.php';
require_once ROOT_DIR . '/_includes/core/functions/plugins.php';

// Run core actions/filters
require_once ROOT_DIR . '/_includes/core/bootstrap/actions.php';

// Setup Twig
require_once ROOT_DIR . '/_includes/core/functions/twig.php';

// Get available plugins
$_plugin_files = glob('_plugins/**/plugin.php');

// Include plugins
if ( is_array($_plugin_files) ) {

    foreach ( $_plugin_files as $plugin ) {
        require_once $plugin;
    }

}

// Clean up
unset($_plugin_files);

// Action: plugins.loaded
do_action( 'plugins.loaded' );

/**
 * Get theme functions.php
 *
 * @NOTE This is optional but can be useful
 * for applying filters as they need to be
 * declared before they get executed!
 */
 // Get available functions
$_function_files = glob('_functions/*.php');

 // Include plugins
if ( is_array($_function_files) ) {

    foreach ( $_function_files as $function ) {
        require_once $function;
    }

    // Action: template.functions.loaded
    do_action( 'template.functions.loaded' );

}

// Clean up
unset($_function_files);

// Get Functions
require_once ROOT_DIR . '/_includes/core/functions/forms.php';
require_once ROOT_DIR . '/_includes/core/functions/path.php';
require_once ROOT_DIR . '/_includes/core/functions/theme.php';

// Setup Global Variables
require_once ROOT_DIR . '/_includes/core/variables.php';

// Action: variables.loaded
do_action( 'variables.loaded' );

// Load Assets
require_once ROOT_DIR . '/_includes/core/functions/assets.php';

// Action: assets.loaded
do_action( 'assets.loaded', $_assets );

// Init
do_action( 'init' );
