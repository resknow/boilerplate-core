<?php

function bp_environment_check() {

    // PHP Version Check
    $min = '7.2';
    $current = phpversion();
    if ( version_compare( $current, $min, '<' ) ) {
        throw new Exception( sprintf('Boilerplate requires PHP %s+ to work properly.', $min) );
    }

}