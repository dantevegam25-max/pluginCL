<?php
/**
 * Class Plugin_Clasificados_Admin_Settings
 * Expanded MVP admin panel for settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin_Clasificados_Admin_Settings {

	/**
	 * Init hooks.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	/**
	 * Add admin menu.
	 */
	public static function add_admin_menu() {
		add_menu_page(
			__( 'Ajustes Clasificados', 'plugin-clasificados' ),
			__( 'Clasificados', 'plugin-clasificados' ),
			'manage_options',
			'plugin-clasificados-settings',
			array( __CLASS__, 'settings_page' ),
			'dashicons-admin-generic',
			30
		);
	}

	/**
	 * Register settings.
	 */
	public static function register_settings() {
		register_setting( 'plugin_clasificados_options_group', 'plugin_clasificados_base_slug' );
		register_setting( 'plugin_clasificados_options_group', 'plugin_clasificados_enable_seo' );
	}

	/**
	 * Render settings page.
	 */
	public static function settings_page() {
		if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) {
			echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Ajustes guardados correctamente.', 'plugin-clasificados' ) . '</p></div>';
		}
		?>
		<div class="wrap">
			<h1><?php _e( 'Ajustes de Plugin Clasificados SEO', 'plugin-clasificados' ); ?></h1>
			<p><?php _e( 'Configuración avanzada del plugin.', 'plugin-clasificados' ); ?></p>

			<form method="post" action="options.php">
				<?php settings_fields( 'plugin_clasificados_options_group' ); ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e( 'Base Slug (Opcional)', 'plugin-clasificados' ); ?></th>
						<td>
							<input type="text" name="plugin_clasificados_base_slug" value="<?php echo esc_attr( get_option( 'plugin_clasificados_base_slug' ) ); ?>" />
							<p class="description"><?php _e( 'Por defecto, el plugin usa la ruta raíz (ej. /vehiculos/). Si experimentas conflictos con páginas nativas, puedes establecer un slug base aquí (ej. "clasificados" para usar /clasificados/vehiculos/). Déjalo en blanco para usar la raíz.', 'plugin-clasificados' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Habilitar SEO Dinámico', 'plugin-clasificados' ); ?></th>
						<td>
							<input type="checkbox" name="plugin_clasificados_enable_seo" value="1" <?php checked( 1, get_option( 'plugin_clasificados_enable_seo', 1 ), true ); ?> />
							<p class="description"><?php _e( 'Habilita la generación automática de títulos y meta descripciones en las páginas de silo. Desactiva esto si usas un plugin SEO externo (como Yoast o RankMath) y prefieres controlarlo desde allí.', 'plugin-clasificados' ); ?></p>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
