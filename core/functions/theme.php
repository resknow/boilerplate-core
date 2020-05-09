<?php

use BP\Theme;

/**
 * Head
 *
 * Runs template/head action
 */
function head() {
    do_action( 'template.head' );
}


/**
 * Footer
 *
 * Runs template/footer action
 */
function footer() {
    do_action( 'template.footer' );
}

/**
 * Get Partial
 * Get and render a partial template file.
 *
 * @since 1.0.0
 *
 * $part: (string) name of the partial, relative to
 * the _templates/partials directory.
 * $context (mixed) scoped variable available
 * inside the partial
 */
function get_partial( $part, $context = false ) {

    // Get theme object
    global $_theme;
    return $_theme->get_partial($part, $context);

}

/**
 * Get Header
 * Get header partial.
 *
 * @since 1.0.0
 *
 * $name: (string) name of custom header file.
 */
function get_header( $name = 'header' ) {
    return get_partial($name);
}

/**
 * Get Footer
 * Get footer partial.
 *
 * @since 1.0.0
 *
 * $name: (string) name of custom footer file.
 */
function get_footer( $name = 'footer' ) {
    return get_partial($name);
}

/**
 * Get Sidebar
 * Get sidebar partial.
 *
 * @since 1.0.0
 *
 * $name: (string) name of custom sidebar file.
 */
function get_sidebar( $name = 'sidebar' ) {
    return get_partial($name);
}

/**
 * Assets Dir
 * Return location of assets relative to
 * the ROOT_DIR.
 *
 * @since 1.0.1
 *
 * $prefix: (string) string to prepend to the returned value.
 */
function assets_dir( $suffix = false ) {
    global $_theme;
    $location = $_theme->get_dir();
    $path = ( $suffix ? sprintf('/%s/assets/%s', $location, $suffix) : sprintf('/%s/assets', $location) );
    return apply_filters( 'assets_dir', $path );
}

/**
 * Render
 * 
 * Render a Twig template with some context
 * 
 * @param string $template Template path relative to /_templates
 * @param array $context Template context
 * @return string Rendered twig template
 */
function render( $template, $context = [] ) {
    $dir = ROOT_DIR . '/_templates';
    $theme = new Theme($dir);
    $theme->set_ext('.twig');
    $theme->register_render_function( 'twig' );
    return $theme->render($template . '.twig', $context);
}

/**
 * Output
 * 
 * Wraps the render function but echo's the
 * result
 * 
 * @param string $template Template path relative to /_templates
 * @param array $context Template context
 */
function output( $template, $context = [] ) {
    echo render($template, $context);
}