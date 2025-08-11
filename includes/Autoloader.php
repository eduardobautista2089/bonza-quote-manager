<?php
namespace BonzaQuote;

/**
 * Class Autoloader
 *
 * Handles PSR-4-style autoloading for the Bonza Quote plugin classes.
 * Assumes all plugin classes are in the `includes/` directory under the plugin root.
 */
class Autoloader {

    /**
     * Registers the autoloader function with SPL.
     *
     * @return void
     */
    public static function register() {
        spl_autoload_register( [ __CLASS__, 'autoload' ] );
    }

    /**
     * Autoloads classes from the BonzaQuote namespace.
     *
     * @param string $class Fully-qualified class name.
     * @return void
     */
    private static function autoload( $class ) {
        // Only handle classes from this plugin's namespace.
        if ( strpos( $class, __NAMESPACE__ . '\\' ) !== 0 ) {
            return;
        }

        // Remove namespace prefix and convert to a file path.
        $relative_path = str_replace( '\\', '/', substr( $class, strlen( __NAMESPACE__ . '\\' ) ) );

        // Build the absolute path to the class file.
        $file = trailingslashit( BONZA_QUOTE_PLUGIN_PATH ) . 'includes/' . $relative_path . '.php';

        // Require the file if it exists.
        if ( is_readable( $file ) ) {
            require $file;
        }
    }
}
