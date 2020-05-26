<?php

/**
 * Router
 *
 * Returns the singleton instance of
 * the Router class for managing routes
 *
 * @since 2.5.0
 */
function router()
{
    $router = BP\Router::get_instance();

    // Override default 404
    $router->router()->set404(function () {
        return;
    });

    return $router->router();
}
