<?php
/**
 * Plugin Name: Plugin Clasificados SEO Silos
 * Plugin URI: https://example.com/
 * Description: Un plugin de clasificados estilo Marketplace enfocado en escalabilidad SEO mediante estructura de silos dinámicos.
 * Version: 1.1.0
 * Author: Jules
 * Author URI: https://example.com/
 * License: GPLv2 or later
 * Text Domain: plugin-clasificados
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define plugin constants
define( 'PLUGIN_CLASIFICADOS_VERSION', '1.1.0' );
define( 'PLUGIN_CLASIFICADOS_DIR', plugin_dir_path( __FILE__ ) );
define( 'PLUGIN_CLASIFICADOS_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main Class Plugin_Clasificados
 */
class Plugin_Clasificados {

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Return an instance of this class.
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Include necessary files.
	 */
	private function includes() {
		// Core
		require_once PLUGIN_CLASIFICADOS_DIR . 'core/class-cpt-taxonomies.php';
		require_once PLUGIN_CLASIFICADOS_DIR . 'core/class-rewrite-rules.php';

		// Modules
		require_once PLUGIN_CLASIFICADOS_DIR . 'modules/class-template-loader.php';
		require_once PLUGIN_CLASIFICADOS_DIR . 'modules/class-seo-manager.php';

		// Admin
		if ( is_admin() ) {
			require_once PLUGIN_CLASIFICADOS_DIR . 'includes/class-admin-settings.php';
		}
	}

	/**
	 * Initialize hooks.
	 */
	private function init_hooks() {
		// Instantiate core classes
		Plugin_Clasificados_CPT_Taxonomies::init();
		Plugin_Clasificados_Rewrite_Rules::init();

		// Instantiate module classes
		Plugin_Clasificados_Template_Loader::init();
		Plugin_Clasificados_SEO_Manager::init();

		// Instantiate Admin
		if ( is_admin() ) {
			Plugin_Clasificados_Admin_Settings::init();
		}

		// Activation hook
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		// Deactivation hook
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
	}

	/**
	 * Plugin activation logic.
	 */
	public function activate() {
		// Ensure CPTs and Taxonomies are registered before flushing rewrite rules
		Plugin_Clasificados_CPT_Taxonomies::register_cpt();
		Plugin_Clasificados_CPT_Taxonomies::register_taxonomies();

		flush_rewrite_rules();
	}

	/**
	 * Plugin deactivation logic.
	 */
	public function deactivate() {
		flush_rewrite_rules();
	}
}

// Init Plugin
function plugin_clasificados_init() {
	Plugin_Clasificados::get_instance();
}
add_action( 'plugins_loaded', 'plugin_clasificados_init' );
