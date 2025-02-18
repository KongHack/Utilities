<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite18daa173c840f985aa54b0113ec39ff
{
    public static $prefixLengthsPsr4 = array (
        'G' => 
        array (
            'GCWorld\\Utilities\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'GCWorld\\Utilities\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite18daa173c840f985aa54b0113ec39ff::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite18daa173c840f985aa54b0113ec39ff::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite18daa173c840f985aa54b0113ec39ff::$classMap;

        }, null, ClassLoader::class);
    }
}
