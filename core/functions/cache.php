<?php

use BP\Cache;

if ($_config['environment'] === 'prod') {
    $_cache = new Cache('_templates', '.twig');
    $_cache->get_cached_html($_path);
}
