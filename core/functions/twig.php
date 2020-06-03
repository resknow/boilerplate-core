<?php

use BP\Twig;

// Setup Twig
function twig($context = [], $template, $theme, $echo = false)
{
    $twig = new Twig($context, $template, $theme);

    if ($echo !== true) {
        return $twig->render();
    }

    echo $twig->render();
}

/**
 * Read Front Matter
 *
 * Parses YAML front matter from a Twig template
 * @param string $file Path to template
 */
function read_front_matter($file)
{

    // Make sure we can read the template
    if (!is_readable($file)) return;

    // Open the file
    $contents = @file_get_contents($file);

    // Get the front matter
    $contents = explode('---#}', $contents);

    // Remove the top comment tag
    $front_matter_string = ltrim($contents[0], '{#---');

    // Parse it!
    $front_matter = Spyc::YAMLLoadString($front_matter_string);

    // Run a filter
    $front_matter = apply_filters('twig.front_matter', $front_matter);

    return $front_matter;
}

// Register custom render function for the core theme
add_action('template.init', function ($theme) {

    // Use Twig globally?
    $use_twig_globally = get('site.twig', true);

    if ($use_twig_globally) {
        $theme->set_ext('.twig');
        $theme->register_render_function('twig');
    }
});

// Register Scripts & Stylesheets declared in front matter
add_filter('twig.front_matter', function ($front_matter) {

    // Stylesheets
    if (array_key_exists('stylesheets', $front_matter) && is_array($front_matter)) {
        foreach ($front_matter['stylesheets'] as $id => $url) {
            add_stylesheet($id, $url);
        }
    }

    // Scripts
    if (array_key_exists('scripts', $front_matter) && is_array($front_matter)) {
        foreach ($front_matter['scripts'] as $id => $url) {
            add_script($id, $url);
        }
    }

    return $front_matter;
});

// Enable Twig Caching in Prod
add_filter('twig.environment', function ($config) {

    // If we can, let's enable caching for Twig
    if (get('site.environment') === 'prod') {
        $dir = ROOT_DIR . '/_templates/cache';
        $dir_exists = is_dir($dir);

        // Attempt to create a cache directory if
        // it doesn't exist already
        if (!$dir_exists) @mkdir($dir, 775, true);

        $dir_is_writeable = is_writeable($dir);
        $config['cache'] = $dir_is_writeable ? $dir : false;
    }

    return $config;
});
