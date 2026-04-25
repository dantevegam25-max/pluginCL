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
        if ( get_option( 'plugin_clasificados_enable_seo', 1 ) ) {
		    add_filter( 'document_title_parts', array( __CLASS__, 'dynamic_title' ) );
		    add_action( 'wp_head', array( __CLASS__, 'dynamic_meta_description' ), 1 );
        }
	}

	/**
	 * Generate dynamic title for SEO silos.
	 */
	public static function dynamic_title( $title ) {
		$cat_slug = get_query_var( 'anuncio_cat_silo' );
        $loc_slug = get_query_var( 'anuncio_ubicacion_silo' );

		if ( ! empty( $cat_slug ) || ! empty( $loc_slug ) ) {

			$parts = array();

            if ( ! empty( $cat_slug ) ) {
                $cat_term = get_term_by( 'slug', $cat_slug, 'anuncio_categoria' );
                if ( $cat_term ) {
                    $parts[] = $cat_term->name;
                }
            } else {
                 $parts[] = __( 'Anuncios', 'plugin-clasificados' );
            }

			if ( ! empty( $loc_slug ) ) {
                $loc_term = get_term_by( 'slug', $loc_slug, 'anuncio_ubicacion' );
                if ( $loc_term ) {
                    $parts[] = 'en ' . $loc_term->name;
                }
			}

			$parts[] = '| Compra y Venta';

			$title['title'] = implode( ' ', $parts );
			unset( $title['tagline'] );
		}

		return $title;
	}

	/**
	 * Generate dynamic meta description.
	 */
	public static function dynamic_meta_description() {
		$cat_slug = get_query_var( 'anuncio_cat_silo' );
        $loc_slug = get_query_var( 'anuncio_ubicacion_silo' );

		if ( ! empty( $cat_slug ) || ! empty( $loc_slug ) ) {

            $cat_name = 'anuncios';
            if ( ! empty( $cat_slug ) ) {
                $cat_term = get_term_by( 'slug', $cat_slug, 'anuncio_categoria' );
                if ( $cat_term ) {
                    $cat_name = strtolower($cat_term->name);
                }
            }

			$location = 'tu área';
			if ( ! empty( $loc_slug ) ) {
                $loc_term = get_term_by( 'slug', $loc_slug, 'anuncio_ubicacion' );
                if ( $loc_term ) {
                    $location = $loc_term->name;
                }
			}

			$desc = sprintf( 'Encuentra los mejores %s en %s. Compra, vende y descubre oportunidades increíbles en nuestra plataforma de clasificados.', $cat_name, $location );

			echo '<meta name="description" content="' . esc_attr( $desc ) . '" />' . "\n";
		}
	}

    /**
     * Helper method to generate dynamic H1
     */
    public static function get_dynamic_h1() {
        $cat_slug = get_query_var( 'anuncio_cat_silo' );
        $loc_slug = get_query_var( 'anuncio_ubicacion_silo' );

        if ( ! empty( $cat_slug ) || ! empty( $loc_slug ) ) {
            $h1 = __( 'Anuncios', 'plugin-clasificados' );

            if ( ! empty( $cat_slug ) ) {
                $cat_term = get_term_by( 'slug', $cat_slug, 'anuncio_categoria' );
                if ( $cat_term ) {
                    $h1 = $cat_term->name;
                }
            }

            if ( ! empty( $loc_slug ) ) {
                $loc_term = get_term_by( 'slug', $loc_slug, 'anuncio_ubicacion' );
                if ( $loc_term ) {
                    $h1 .= ' en ' . $loc_term->name;
                }
            }

            return $h1;
        }

        return '';
    }
}
