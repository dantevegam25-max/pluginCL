<?php
/**
 * Class Plugin_Clasificados_Rewrite_Rules
 * Handles custom routing for SEO Silos: /{categoria}/{ciudad}/{distrito}/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin_Clasificados_Rewrite_Rules {

	/**
	 * Init hooks.
	 */
	public static function init() {
		add_filter( 'query_vars', array( __CLASS__, 'add_query_vars' ) );
        add_action( 'parse_request', array( __CLASS__, 'parse_request_silos' ), 10, 1 );
        add_action( 'pre_get_posts', array( __CLASS__, 'pre_get_posts_silos' ) );
	}

    // Kept for backward compatibility or activation flush if needed, but we don't strictly need rules anymore.
    public static function add_rewrite_rules() {
        // Empty to avoid generic rules polluting WP.
    }

	/**
	 * Add query variables.
	 */
	public static function add_query_vars( $vars ) {
		$vars[] = 'anuncio_cat_silo';
		$vars[] = 'anuncio_ciudad';
		$vars[] = 'anuncio_distrito';
		return $vars;
	}

    /**
     * Intercept the request to detect our silos dynamically without conflicting with native pages.
     */
    public static function parse_request_silos( $wp ) {
        if ( is_admin() ) {
            return;
        }

        $request = isset( $wp->request ) ? $wp->request : '';
        if ( empty( $request ) ) {
            return;
        }

        $parts = explode( '/', $request );

        // Exclude native WP paths
        if ( in_array( $parts[0], array( 'wp-admin', 'wp-json', 'wp-content', 'wp-includes' ) ) ) {
            return;
        }

        // Optionally check if we have a base slug from admin settings
        $base_slug = get_option( 'plugin_clasificados_base_slug', '' );
        if ( ! empty( $base_slug ) ) {
            if ( $parts[0] === $base_slug ) {
                array_shift( $parts ); // Remove the base slug
            } else {
                return; // Doesn't match our base slug
            }
        }

        if ( empty( $parts ) ) {
            return;
        }

        // Check if the first part is a valid category
        $term = term_exists( $parts[0], 'anuncio_categoria' );

        if ( $term ) {
            // Check for pagination
            $paged_index = array_search('page', $parts);
            if ($paged_index !== false && isset($parts[$paged_index + 1])) {
                 $wp->query_vars['paged'] = intval($parts[$paged_index + 1]);
                 // Remove pagination parts for location mapping
                 array_splice($parts, $paged_index, 2);
            }

            $wp->query_vars['anuncio_cat_silo'] = $parts[0];

            if ( isset( $parts[1] ) ) {
                 $wp->query_vars['anuncio_ciudad'] = $parts[1];
            }
            if ( isset( $parts[2] ) ) {
                 $wp->query_vars['anuncio_distrito'] = $parts[2];
            }

            // To ensure WP doesn't try to load a page with the same slug and 404s
            // we remove the native query vars that trigger single queries
            if ( isset( $wp->query_vars['pagename'] ) ) unset( $wp->query_vars['pagename'] );
            if ( isset( $wp->query_vars['name'] ) ) unset( $wp->query_vars['name'] );
            if ( isset( $wp->query_vars['page'] ) ) unset( $wp->query_vars['page'] );
            if ( isset( $wp->query_vars['error'] ) ) unset( $wp->query_vars['error'] );
        }
    }

    /**
     * Modify the main query to properly serve a 200 OK archive page.
     */
    public static function pre_get_posts_silos( $query ) {
        if ( ! is_admin() && $query->is_main_query() ) {
            $cat_silo = $query->get( 'anuncio_cat_silo' );
            if ( ! empty( $cat_silo ) ) {
                $term = term_exists( $cat_silo, 'anuncio_categoria' );
                if ( $term ) {
                    // Convert main query to our custom archive query
                    $query->set( 'post_type', 'anuncio' );

                    $query->is_archive = true;
                    $query->is_post_type_archive = true;
                    $query->is_home    = false;
                    $query->is_single  = false;
                    $query->is_page    = false;
                    $query->is_404     = false;
                    $query->is_tax     = false;

                    // Set taxonomy queries
                    $tax_query = array( 'relation' => 'AND' );
                    $tax_query[] = array(
                        'taxonomy' => 'anuncio_categoria',
                        'field'    => 'slug',
                        'terms'    => $cat_silo,
                    );

                    $ciudad = $query->get( 'anuncio_ciudad' );
                    $distrito = $query->get( 'anuncio_distrito' );

                    if ( ! empty( $distrito ) ) {
                        $tax_query[] = array(
                            'taxonomy' => 'anuncio_ubicacion',
                            'field'    => 'slug',
                            'terms'    => $distrito,
                        );
                    } elseif ( ! empty( $ciudad ) ) {
                        $tax_query[] = array(
                            'taxonomy' => 'anuncio_ubicacion',
                            'field'    => 'slug',
                            'terms'    => $ciudad,
                        );
                    }

                    $query->set( 'tax_query', $tax_query );
                }
            }
        }
    }
}
