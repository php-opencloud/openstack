<?php

use Sami\Sami;

$path = dirname(dirname(__DIR__));

return new Sami($path . '/src', [
    'title'         => 'Test',
    'theme'         => 'new_theme',
    'build_dir'     => __DIR__ . '/build',
    'cache_dir'     => __DIR__ . '/cache',
    'template_dirs' => [
        __DIR__ . '/template'
    ],
]);
