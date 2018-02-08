<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd7a8f27923188b1e0ca914112ef55d4a
{
    public static $files = array (
        'ad155f8f1cf0d418fe49e248db8c661b' => __DIR__ . '/..' . '/react/promise/src/functions_include.php',
    );

    public static $prefixLengthsPsr4 = array (
        'm' => 
        array (
            'mpyw\\Cowitter\\' => 14,
            'mpyw\\Co\\' => 8,
        ),
        'R' => 
        array (
            'React\\Promise\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'mpyw\\Cowitter\\' => 
        array (
            0 => __DIR__ . '/..' . '/mpyw/cowitter/src',
        ),
        'mpyw\\Co\\' => 
        array (
            0 => __DIR__ . '/..' . '/mpyw/co/src',
        ),
        'React\\Promise\\' => 
        array (
            0 => __DIR__ . '/..' . '/react/promise/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd7a8f27923188b1e0ca914112ef55d4a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd7a8f27923188b1e0ca914112ef55d4a::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
