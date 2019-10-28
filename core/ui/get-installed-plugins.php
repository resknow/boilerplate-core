<?php

// Setup headers
header('Content-type: application/json');

// Setup directories
$pluginDir = __DIR__ . '/../../../_plugins';

// Get list of installed plugins
$installed = glob($pluginDir . '/*', GLOB_ONLYDIR);
$installed = array_map( function($plugin) use ($pluginDir) {
    return str_replace($pluginDir . '/', '', $plugin);
}, $installed );

echo json_encode(['code' => 200, 'message' => 'Currently installed plugins', 'plugins' => $installed]);
