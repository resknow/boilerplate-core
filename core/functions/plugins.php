<?php

/**
 * Plugin Dir
 * Returns plugin directory path
 *
 * @since 1.0.0
 * @updated 2.0.0
 *
 * @param $root (bool) Prefix with ROOT_DIR, defaults to true
 */
function plugin_dir($root = true)
{
    $dir = '/_plugins';
    return ($root === true ? ROOT_DIR . $dir : $dir);
}

/**
 * Plugin Exists
 *
 * @since 1.0.0
 *
 * $plugin: (string) name of the plugin to check
 */
function plugin_exists($plugin)
{
    return is_dir(plugin_dir() . '/' . $plugin);
}

/**
 * Plugin Is Active
 *
 * @since 1.0.0
 *
 * $plugin: (string) name of the plugin to check
 */
function plugin_is_active($plugin)
{
    return file_exists(plugin_dir() . '/' . $plugin . '/plugin.php');
}

/**
 * Plugin Requires Version
 *
 * @since 1.4.0
 *
 * @param $plugin (string) Plugin slug/name
 * @param $version (string|float) Minimum version number required
 */
function plugin_requires_version($plugin, $version)
{
    if (version_compare(VERSION, $version, '<')) {
        throw new Exception(sprintf(
            '%s requires at least Boilerplate version %s.',
            $plugin,
            (string) $version
        ));
    }
}
