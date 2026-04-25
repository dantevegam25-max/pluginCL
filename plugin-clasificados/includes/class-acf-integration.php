<?php
/**
 * Class Plugin_Clasificados_ACF_Integration
 * Handles ACF JSON sync to automatically load field groups defined by the plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin_Clasificados_ACF_Integration {

	/**
	 * Init hooks.
	 */
	public static function init() {
        // Only run if ACF is active
        if ( class_exists( 'ACF' ) ) {
            add_filter( 'acf/settings/load_json', array( __CLASS__, 'load_acf_json' ) );
            // Optional: Save changes made via the admin interface back to the plugin folder
            // add_filter( 'acf/settings/save_json', array( __CLASS__, 'save_acf_json' ) );
        }
	}

    /**
     * Tell ACF to load JSON field groups from our plugin directory.
     *
     * @param array $paths Array of paths to load ACF JSON files from.
     * @return array
     */
    public static function load_acf_json( $paths ) {
        // Append path
        $paths[] = PLUGIN_CLASIFICADOS_DIR . 'acf-json';

        return $paths;
    }

    /**
     * Tell ACF to save JSON field groups to our plugin directory.
     * (Commented out by default to prevent users from accidentally overwriting plugin files,
     * but useful for development).
     *
     * @param string $path Path to save ACF JSON files to.
     * @return string
     */
    public static function save_acf_json( $path ) {
        // Update path
        $path = PLUGIN_CLASIFICADOS_DIR . 'acf-json';

        return $path;
    }
}
