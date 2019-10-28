<?php

use BP\Twig;

// Setup Twig
function twig( $context = [], $template, $theme, $echo = true ) {
    $twig = new Twig( $context, $template, $theme);
    
    if ( $echo !== true ) {
        return $twig->render();
    }

    echo $twig->render();
}

// Register custom render function for the core theme
add_action( 'template.init', function( $theme ) {

    // Use Twig globally?
    $use_twig_globally = get('site.twig', true);

    if ( $use_twig_globally ) {
        $theme->set_ext('.twig');
        $theme->register_render_function( 'twig' );
    }
} );

// Register Scripts & Stylesheets declared in front matter
add_filter( 'twig.front_matter', function($front_matter) {

    // Stylesheets
    if ( array_key_exists('stylesheets', $front_matter) && is_array($front_matter) ) {
        foreach ( $front_matter['stylesheets'] as $id => $url ) {
            add_stylesheet($id, $url);
        }
    }

    // Scripts
    if ( array_key_exists('scripts', $front_matter) && is_array($front_matter) ) {
        foreach ( $front_matter['scripts'] as $id => $url ) {
            add_script($id, $url);
        }
    }

    return $front_matter;

} );
