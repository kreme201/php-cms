<?php

namespace Kreme;

use Mustache_Engine;
use Mustache_Loader_FilesystemLoader;

final class Application
{
    public function __construct()
    {

    }

    public function serve(): void
    {
        $mustache = new Mustache_Engine([
            'loader' => new Mustache_Loader_FilesystemLoader(dirname(__DIR__) . '/theme', [
                'extension' => '.html',
            ]),
        ]);

        echo $mustache->render('index', [
            'title' => 'test',
        ]);
    }
}