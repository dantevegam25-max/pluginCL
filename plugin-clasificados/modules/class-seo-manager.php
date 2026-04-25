<?php
/**
 * Class Plugin_Clasificados_SEO_Manager
 * Handles dynamic titles, meta descriptions, and programmatic SEO.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin_Clasificados_SEO_Manager {

	/**
	 * Init hooks.
	 */
	public static function init() {
        // Check if SEO features are enabled in settings
        if ( get_option( 'plugin_clasificados_enable_seo', 1 ) ) {
		    // Filter document title (works for WP 4.4+)
		    add_filter( 'document_title_parts', array( __CLASS__, 'dynamic_title' ) );
		    // Add meta description in head
		    add_action( 'wp_head', array( __CLASS__, 'dynamic_meta_description' ), 1 );
        }
	}

	/**
	 * Generate dynamic title for SEO silos.
	 */
	public static function dynamic_title( $title ) {
		$cat_slug = get_query_var( 'anuncio_cat_silo' );

		if ( ! empty( $cat_slug ) && get_term_by( 'slug', $cat_slug, 'anuncio_categoria' ) ) {

			$ciudad_slug = get_query_var( 'anuncio_ciudad' );
			$distrito_slug = get_query_var( 'anuncio_distrito' );

			$cat_term = get_term_by( 'slug', $cat_slug, 'anuncio_categoria' );
			$cat_name = $cat_term ? $cat_term->name : ucfirst( $cat_slug );

			$parts = array( $cat_name );

			if ( ! empty( $distrito_slug ) ) {
				$distrito_name = str_replace( '-', ' ', ucfirst( $distrito_slug ) );
				$parts[] = 'en ' . $distrito_name;
			} elseif ( ! empty( $ciudad_slug ) ) {
				$ciudad_name = str_replace( '-', ' ', ucfirst( $ciudad_slug ) );
				$parts[] = 'en ' . $ciudad_name;
			}

            if ( ! empty( $ciudad_slug ) && ! empty( $distrito_slug ) ) {
                 $ciudad_name = str_replace( '-', ' ', ucfirst( $ciudad_slug ) );
                 $parts[] = ', ' . $ciudad_name;
            }

			$parts[] = '| Compra y Venta';

			$title['title'] = implode( ' ', $parts );
			unset( $title['tagline'] ); // Optional: remove tagline to keep it clean
		}

		return $title;
	}

	/**
	 * Generate dynamic meta description.
	 */
	public static function dynamic_meta_description() {
		$cat_slug = get_query_var( 'anuncio_cat_silo' );

		if ( ! empty( $cat_slug ) && get_term_by( 'slug', $cat_slug, 'anuncio_categoria' ) ) {

			$ciudad_slug = get_query_var( 'anuncio_ciudad' );
			$distrito_slug = get_query_var( 'anuncio_distrito' );

			$cat_term = get_term_by( 'slug', $cat_slug, 'anuncio_categoria' );
			$cat_name = $cat_term ? $cat_term->name : ucfirst( $cat_slug );

			$location = 'tu área';
			if ( ! empty( $distrito_slug ) ) {
				$location = str_replace( '-', ' ', ucfirst( $distrito_slug ) );
			} elseif ( ! empty( $ciudad_slug ) ) {
				$location = str_replace( '-', ' ', ucfirst( $ciudad_slug ) );
			}

			$desc = sprintf( 'Encuentra los mejores %s en %s. Compra, vende y descubre oportunidades increíbles en nuestra plataforma de clasificados.', strtolower($cat_name), $location );

			echo '<meta name="description" content="' . esc_attr( $desc ) . '" />' . "\n";
		}
	}

    /**
     * Helper method to generate dynamic H1
     */
    public static function get_dynamic_h1() {
        $cat_slug = get_query_var( 'anuncio_cat_silo' );

        if ( ! empty( $cat_slug ) ) {
            $cat_term = get_term_by( 'slug', $cat_slug, 'anuncio_categoria' );
            if(!$cat_term) return '';

            $cat_name = $cat_term->name;

            $ciudad_slug = get_query_var( 'anuncio_ciudad' );
			$distrito_slug = get_query_var( 'anuncio_distrito' );

            $h1 = $cat_name;

            if ( ! empty( $distrito_slug ) ) {
				$distrito_name = str_replace( '-', ' ', ucfirst( $distrito_slug ) );
				$h1 .= ' en ' . $distrito_name;
			} elseif ( ! empty( $ciudad_slug ) ) {
				$ciudad_name = str_replace( '-', ' ', ucfirst( $ciudad_slug ) );
				$h1 .= ' en ' . $ciudad_name;
			}

            return $h1;
        }

        return '';
    }
}
