<?php

// Taken from Laravel
// Run php serve from your command line to test your site
// using PHP's built in web server

$uri = urldecode( parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) );

// Pass the URI to $_GET['path'] so Boilerplate can use it
if ( $uri !== '/' ) {
    $_GET['path'] = ltrim($uri, '/');
}

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ( $uri !== '/' && file_exists( __DIR__ . '/../../' . $uri) ) {
    return false;
}

require_once __DIR__ . '/../../index.php';
