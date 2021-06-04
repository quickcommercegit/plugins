<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit39412799c1e469cf0a400386b80396e7
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Services\\' => 9,
        ),
        'M' => 
        array (
            'Models\\' => 7,
        ),
        'I' => 
        array (
            'Interfaces\\' => 11,
        ),
        'H' => 
        array (
            'Helpers\\' => 8,
        ),
        'C' => 
        array (
            'Controllers\\' => 12,
        ),
        'B' => 
        array (
            'Bases\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Services\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Services',
        ),
        'Models\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Models',
        ),
        'Interfaces\\' => 
        array (
            0 => __DIR__ . '/../..' . '/core/interfaces',
        ),
        'Helpers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Helpers',
        ),
        'Controllers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Controllers',
        ),
        'Bases\\' => 
        array (
            0 => __DIR__ . '/../..' . '/core/bases',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit39412799c1e469cf0a400386b80396e7::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit39412799c1e469cf0a400386b80396e7::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
