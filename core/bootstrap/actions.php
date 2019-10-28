<?php

use BP\Theme;

/**
 * Catch 404
 *
 * Catch 404 errors and display the
 * correct template if no custom
 * one exists in the _templates directory
 *
 * @param string $loaded Currently loaded template
 */
add_filter( 'template.render', function($loaded) use($_theme) {

    if ( $loaded == '' || $loaded == '404.php' && !is_readable( $_theme->get_dir() . '/404' . $_theme->get_ext() ) ) {
        require_once BP_PACKAGE_DIR . '/core/ui/404.php';
        exit;
    }

    return $loaded;

} );

/**
 * Load Docs in Admin Mode
 * 
 * This only runs when the following conditions are true:
 * - Boilerplate environment is dev
 * - The URL starts with /docs/
 */
add_action( 'init', function() {

    $conditions = [
        get('site.environment') === 'dev',
        get('page.index.0') === 'docs'
    ];

    if ( in_array(false, $conditions) ) return;

    // Setup theme
    $theme = new Theme(BP_PACKAGE_DIR . '/core/docs');
    $theme->set_ext('.twig');
    $theme->register_render_function('twig');

    // Clean up the URL
    $index = get('page.index');
    array_shift($index);
    $path = join('/', $index);
    $path = ( !empty($path) ? $path : 'index' );
    $template = $theme->load($path);

    // Add some useful variables
    set('site.version', VERSION);
    set('site.root_dir', ROOT_DIR);

    exit($theme->render($template, get()));

} );