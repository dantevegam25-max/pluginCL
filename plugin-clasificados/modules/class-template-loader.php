<?php
/**
 * Class Plugin_Clasificados_Template_Loader
 * Intercepts template loading to use custom templates for silos.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin_Clasificados_Template_Loader {

	/**
	 * Init hooks.
	 */
	public static function init() {
		add_filter( 'template_include', array( __CLASS__, 'load_template' ), 99 );
        add_action( 'template_redirect', array( __CLASS__, 'force_200_status' ) );
	}

    /**
     * Force 200 OK status for our silos to prevent 404s.
     */
    public static function force_200_status() {
        global $wp_query;
        $anuncio_cat = get_query_var( 'anuncio_cat_silo' );
        $anuncio_loc = get_query_var( 'anuncio_ubicacion_silo' );

        if ( ! empty( $anuncio_cat ) || ! empty( $anuncio_loc ) ) {
            status_header( 200 );
            $wp_query->is_404 = false;
        }
    }

	/**
	 * Load custom templates based on query vars.
	 *
	 * @param string $template The path of the template to include.
	 * @return string The modified template path.
	 */
	public static function load_template( $template ) {
		$anuncio_cat = get_query_var( 'anuncio_cat_silo' );
        $anuncio_loc = get_query_var( 'anuncio_ubicacion_silo' );

		// Check if our custom query vars are present
		if ( ! empty( $anuncio_cat ) || ! empty( $anuncio_loc ) ) {
            $custom_template = self::locate_template( 'archive-anuncios.php' );
            if ( $custom_template ) {
                return $custom_template;
            }
		}

		// Single 'anuncio' template
		if ( is_singular( 'anuncio' ) ) {
			$custom_template = self::locate_template( 'single-anuncio.php' );
			if ( $custom_template ) {
				return $custom_template;
			}
		}

		// Taxonomy template for native taxonomy pages (if used directly)
		if ( is_tax( 'anuncio_ubicacion' ) || is_tax( 'anuncio_categoria' ) ) {
			$custom_template = self::locate_template( 'taxonomy-anuncio_ubicacion.php' );
			if ( $custom_template ) {
				return $custom_template;
			}
		}

		return $template;
	}

	/**
	 * Locate template in theme or plugin.
	 * Allows theme to override plugin templates.
	 */
	private static function locate_template( $template_name ) {
		$template = locate_template( array(
			'plugin-clasificados/' . $template_name,
			$template_name,
		) );

		if ( ! $template ) {
			$template = PLUGIN_CLASIFICADOS_DIR . 'templates/' . $template_name;
		}

		if ( file_exists( $template ) ) {
			return $template;
		}

		return false;
	}
}
