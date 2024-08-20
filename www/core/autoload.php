<?php declare(strict_types=1);

namespace Kreme;

spl_autoload_register(function ($class) {
    $file = str_replace(
                [__NAMESPACE__, "\\"],
                [__DIR__ . '/classes', DIRECTORY_SEPARATOR],
                $class,
            ) . ".php";

    if (file_exists($file)) {
        require_once $file;

        return true;
    }

    return false;
});
