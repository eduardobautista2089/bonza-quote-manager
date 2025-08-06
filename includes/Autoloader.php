<?php
namespace BonzaQuote;

class Autoloader {
    public static function register() {
        spl_autoload_register( [ __CLASS__, 'autoload' ] );
    }

    private static function autoload( $class ) {
        if ( strpos( $class, __NAMESPACE__ . '\\' ) === 0 ) {
            $path = str_replace( '\\', '/', substr( $class, strlen( __NAMESPACE__ . '\\' ) ) );
            $file = BONZA_QOUTE_PLUGIN_PATH . 'includes/' . $path . '.php';

            if ( file_exists( $file ) ) {
                require $file;
            }
        }
    }
}