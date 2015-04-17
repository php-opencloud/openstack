<?php

use Sami\Sami;

$path = __DIR__ . '/src';

return new Sami($path, [
    'title'         => 'Test',
    'theme'         => 'new_theme',
    'build_dir'     => __DIR__ . '/build',
    'template_dirs' => [
        __DIR__ . '/template'
    ],
]);