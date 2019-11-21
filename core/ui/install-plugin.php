<?php

// Setup headers
header('Content-type: application/json');

// Get Plugin URL
$url = urldecode($_GET['url']);

// Setup directories
$pluginDir = __DIR__ . '/../../../../../../_plugins';

// Make sure it's writeable
if ( !is_writeable($pluginDir) ) {
    exit(json_encode(['code' => 400, 'message' => 'Your plugins directory is not writeable.']));
}

// Get the plugin
$plugin = @file_get_contents($url);

// Check it worked
if ( $plugin === false ) {
    exit(json_encode(['code' => 400, 'message' => 'Plugin download failed.']));
}

// Save the file
$zipFile = file_put_contents($pluginDir . '/temp-plugin.zip', $plugin);

// Install it
$zip = new ZipArchive();
$zip->open($pluginDir . '/temp-plugin.zip');

// Unzip it
$zip->extractTo($pluginDir);

// Delete the temp file
unlink($pluginDir . '/temp-plugin.zip');

exit(json_encode(['code' => 200, 'message' => 'Plugin installed!']));
