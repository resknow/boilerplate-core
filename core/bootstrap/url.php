<?php

// Check Localhost
$is_dev = (array_key_exists('environment', $_config) && $_config['environment'] === 'dev');

// Get Protocol
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';

// Force Domain?
if (!$is_dev && array_key_exists('force_url', $_config) && $_config['force_url'] === true) {

    // Clean up URL
    $url = $protocol . $_SERVER['HTTP_HOST'];

    if ($url !== $_config['url']) {
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $_config['url'] . $_SERVER['REQUEST_URI']);
        exit;
    }
}

// Set Current Path
// @NOTE: Defaults to 'index' when no path is specified (e.g. homepage)
$_path   = (isset($_GET['path']) && !empty($_GET['path']) ? rtrim($_GET['path'], '/') : 'index');
$_index  = explode('/', $_path);
$_path_with_slash = $_path . '/';

// Make sure the path we load matches the URL
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && isset($_GET['path']) && $_path_with_slash !== $_GET['path']) {
    if (strpos($_path, '.') === false) {
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: /' . $_path_with_slash);
        exit;
    }
}
