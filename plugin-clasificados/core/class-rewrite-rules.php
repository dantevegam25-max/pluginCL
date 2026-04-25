<?php
/**
 * Class Plugin_Clasificados_Rewrite_Rules
 * Handles custom routing for SEO Silos: /{categoria}/{subcategoria}/.../{ciudad}/{distrito}/
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

	/**
	 * Add query variables.
	 */
	public static function add_query_vars( $vars ) {
		$vars[] = 'anuncio_cat_silo'; // Stores the most specific category matched
		$vars[] = 'anuncio_ubicacion_silo'; // Stores the most specific location matched
        $vars[] = 'anuncio_silo_path'; // Stores the raw valid path for breadcrumbs
		return $vars;
	}

    /**
     * Intercept the request to detect our silos dynamically.
     * Evaluates URL segments dynamically against categories and locations.
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

        $base_slug = get_option( 'plugin_clasificados_base_slug', '' );
        if ( ! empty( $base_slug ) ) {
            if ( $parts[0] === $base_slug ) {
                array_shift( $parts );
            } else {
                return;
            }
        }

        if ( empty( $parts ) ) {
            return;
        }

        // Check for pagination and extract it early
        $paged = 1;
        $paged_index = array_search('page', $parts);
        if ($paged_index !== false && isset($parts[$paged_index + 1])) {
             $paged = intval($parts[$paged_index + 1]);
             array_splice($parts, $paged_index, 2);
        }

        $matched_category = '';
        $matched_location = '';
        $is_silo = false;

        // Iterate through parts to find valid terms
        foreach ( $parts as $part ) {
            // First check if it's a category
            $cat_term = get_term_by('slug', $part, 'anuncio_categoria');
            if ( $cat_term ) {
                $matched_category = $part; // Update to the most specific category found
                $is_silo = true;
                continue;
            }

            // If not category, check if it's a location
            $loc_term = get_term_by('slug', $part, 'anuncio_ubicacion');
            if ( $loc_term ) {
                $matched_location = $part; // Update to the most specific location found
                $is_silo = true;
                continue;
            }

            // If a part matches neither taxonomy, we break or invalidate.
            // For safety, we assume if we hit a non-matching segment, it's NOT our silo,
            // OR it's a single post.

            // Check if it's a single post
            $single_post = get_page_by_path( $part, OBJECT, 'anuncio' );
            if ( $single_post && $is_silo ) {
                // It's a single post accessed via silo structure
                // Let native WP handle it by passing the name
                $wp->query_vars['anuncio'] = $part;
                $wp->query_vars['name'] = $part;
                $wp->query_vars['post_type'] = 'anuncio';
                return;
            }

            // If it's not a post and not a valid term, it breaks our contiguous silo pattern.
            $is_silo = false;
            break;
        }

        // If we found a valid silo combination
        if ( $is_silo && ( ! empty($matched_category) || ! empty($matched_location) ) ) {

            if ( ! empty($matched_category) ) {
                $wp->query_vars['anuncio_cat_silo'] = $matched_category;
            }
            if ( ! empty($matched_location) ) {
                $wp->query_vars['anuncio_ubicacion_silo'] = $matched_location;
            }

            $wp->query_vars['anuncio_silo_path'] = implode('/', $parts);
            $wp->query_vars['paged'] = $paged;

            // Prevent native WP 404s
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
            $loc_silo = $query->get( 'anuncio_ubicacion_silo' );

            if ( ! empty( $cat_silo ) || ! empty( $loc_silo ) ) {

                $query->set( 'post_type', 'anuncio' );
                $query->is_archive = true;
                $query->is_post_type_archive = true;
                $query->is_home    = false;
                $query->is_single  = false;
                $query->is_page    = false;
                $query->is_404     = false;
                $query->is_tax     = false;

                $tax_query = array( 'relation' => 'AND' );

                if ( ! empty( $cat_silo ) ) {
                    $tax_query[] = array(
                        'taxonomy' => 'anuncio_categoria',
                        'field'    => 'slug',
                        'terms'    => $cat_silo,
                    );
                }

                if ( ! empty( $loc_silo ) ) {
                    $tax_query[] = array(
                        'taxonomy' => 'anuncio_ubicacion',
                        'field'    => 'slug',
                        'terms'    => $loc_silo,
                    );
                }

                if( count($tax_query) > 1 ) {
                    $query->set( 'tax_query', $tax_query );
                }
            }
        }
    }
}
