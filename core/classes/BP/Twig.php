<?php

namespace BP;

use Spyc;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\TwigFunction;

class Twig {

    protected $instance; // Twig instance
    protected $theme; // Boilerplate Theme instance
    protected $context = [];
    protected $template;

    public function __construct( $context = [], $template, $theme ) {

        // Twig Environment Options
        $options = apply_filters( 'twig.environment', ['cache' => false] );

        // Twig Directory
        $dir = $theme->get_dir();

        // Setup Twig Environment
        $loader             = new FilesystemLoader($dir);
        $this->instance     = new Environment($loader, $options);

        $this->context = $context;
        $this->template = $template;
        $this->theme = $theme;

    }
    
    public function render() {

        // Register Functions
        $this->register_functions($this->instance);

        // Allow other plugins to interect with Twig
        do_action( 'twig.init', $this->instance );

        // Set context
        set( 'this', $this->context );

        // Setup filename
        $filename = $this->template . $this->theme->get_ext();

        // Read Front Matter
        $this->read_front_matter($filename, $this->theme);

        // Render the view
        $rendered = $this->instance->render( $filename, get() );

        // Allow other plugins to interect with Twig
        do_action( 'twig.rendered', $rendered, $this->instance );

        // Return the rendered view
        return $rendered;

    }

    protected function register_functions() {

        $functions = [
            'head',
            'footer',
            'get_stylesheets',
            'get_scripts',
            'assets_dir',
            'is_home',
            'is_page',
            'is_path',
            'path_contains',
            'plugin_dir',
            'do_action',
            'dump',
            'use_library_stylesheet',
            'use_library_script'
        ];

        /**
         * @filter twig.functions
         * 
         * Add a key/value pair to the array and return it to add
         * named functions.
         * 
         * e.g. my_func => some_function
         * would be called via my_func in a Twig template
         */
        $functions = apply_filters( 'twig.functions', $functions );
    
        foreach ( $functions as $key => $function ) {
            $name = ( is_numeric($key) ? $function : $key );
            $this->instance->addFunction( new TwigFunction($name, $function) );
        }

    }

    protected function read_front_matter($file, $theme) {

        // Get file
        $template = sprintf('%s/%s', $theme->get_dir(), $file);

        // Make sure we can read the template
        if ( !is_readable( $template ) ) return;

        // Open the file
        $contents = @file_get_contents($template);

        // Get the front matter
        $contents = explode('---#}', $contents);

        // Remove the top comment tag
        $front_matter_string = ltrim($contents[0], '{#---');

        // Parse it!
        $front_matter = Spyc::YAMLLoadString($front_matter_string);

        // Run a filter
        $front_matter = apply_filters( 'twig.front_matter', $front_matter );

        // Done, merge it with the page array!
        $page = get('page');
        set( 'page', array_merge( $page, $front_matter ) );

    }

}