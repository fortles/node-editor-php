<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitf25ecc9f6dcbab74f7d78697022a5d4d
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInitf25ecc9f6dcbab74f7d78697022a5d4d', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitf25ecc9f6dcbab74f7d78697022a5d4d', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitf25ecc9f6dcbab74f7d78697022a5d4d::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
