<?php

/**
 * Dump
 * 
 * Pretty print an array or object to the screen
 * 
 * @param mixed $data Array/Object/data to print
 * @since 2.8.2
 */
function dump( $data ) {
    $string = print_r($data, true);
    printf('<pre>%s</pre>', $string);
}