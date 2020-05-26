<?php

/**
 * Dump
 *
 * Pretty print an array or object to the screen
 *
 * @param mixed $data Array/Object/data to print
 * @since 2.8.2
 */
function dump($data)
{
    $string = print_r($data, true);
    printf('<pre>%s</pre>', $string);
}

/**
 * JSON
 *
 * Return some data as JSON and set the response code and
 * content type header
 *
 * @param array|object $data
 * @param int $response_code
 * @return string JSON encoded string
 */
function json($data, $response_code = 200)
{
    header('Content-type: application/json');
    http_response_code($response_code);
    return json_encode($data);
}
