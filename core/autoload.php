<?php

namespace Kreme;

use Mustache_Autoloader;

require_once __DIR__ . '/libs/mustache/Autoloader.php';
Mustache_Autoloader::register();

spl_autoload_register(
    function ($class) {
        if (!class_exists($class)) {
            $class_path = str_replace(
                    [__NAMESPACE__, '\\'],
                    [__DIR__ . '/classes', DIRECTORY_SEPARATOR],
                    $class,
                ) . '.php';

            if (file_exists($class_path)) {
                require_once $class_path;
            }
        }
    },
);
