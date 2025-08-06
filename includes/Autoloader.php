<?php
namespace BonzaQuote;

/**
 * Class Autoloader
 * 
 * Handles PSR-4-style autoloading for the Bonza Quote plugin classes.
 * Looks in the 'includes/' directory for namespaced classes.
 */
class Autoloader {
    /**
     * Registers the autoloader function using SPL.
     */
    public static function register() {
        spl_autoload_register( [ __CLASS__, 'autoload' ] );
    }

    /**
     * Autoloads classes within the BonzaQuote namespace.
     *
     * @param string $class Fully-qualified class name.
     */
    private static function autoload( $class ) {
        // Check if the class belongs to this plugin's namespace
        if ( strpos( $class, __NAMESPACE__ . '\\' ) === 0 ) {
            // Remove the namespace prefix and convert to file path
            $path = str_replace( '\\', '/', substr( $class, strlen( __NAMESPACE__ . '\\' ) ) );

            // Build the full path to the class file
            $file = BONZA_QUOTE_PLUGIN_PATH . 'includes/' . $path . '.php';

            // Include the file if it exists
            if ( file_exists( $file ) ) {
                require $file;
            }
        }
    }
}