<?php

// Setup headers
header('Content-type: application/json');

// Get Plugin name
$name = urldecode($_GET['name']);

// Setup directories
$pluginDir = __DIR__ . '/../../../../../../_plugins';

// Make sure it's writeable
if ( !is_writeable($pluginDir) ) {
    exit(json_encode(['code' => 400, 'message' => 'Your plugins directory is not writeable.']));
}

function delTree($dir) {
    $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
} 

delTree($pluginDir . '/' . $name);

exit(json_encode(['code' => 200, 'message' => 'Plugin deleted!']));
