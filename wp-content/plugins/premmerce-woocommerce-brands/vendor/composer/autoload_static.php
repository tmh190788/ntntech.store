<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit504c112dc1dae0bef4677abb39cebb7f
{
    public static $files = array (
        'aeb9dbae972bed1708021f3040d225cc' => __DIR__ . '/../..' . '/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Premmerce\\SDK\\' => 14,
            'Premmerce\\Brands\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Premmerce\\SDK\\' => 
        array (
            0 => __DIR__ . '/..' . '/premmerce/wordpress-sdk/src',
        ),
        'Premmerce\\Brands\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit504c112dc1dae0bef4677abb39cebb7f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit504c112dc1dae0bef4677abb39cebb7f::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
