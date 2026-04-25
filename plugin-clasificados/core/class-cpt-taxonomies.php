<?php
/**
 * Class Plugin_Clasificados_CPT_Taxonomies
 * Handles the registration of Custom Post Types and Taxonomies.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin_Clasificados_CPT_Taxonomies {

	/**
	 * Init hooks.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_cpt' ) );
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ) );
	}

	/**
	 * Register the 'anuncio' Custom Post Type.
	 */
	public static function register_cpt() {
		$labels = array(
			'name'                  => _x( 'Anuncios', 'Post Type General Name', 'plugin-clasificados' ),
			'singular_name'         => _x( 'Anuncio', 'Post Type Singular Name', 'plugin-clasificados' ),
			'menu_name'             => __( 'Anuncios', 'plugin-clasificados' ),
			'name_admin_bar'        => __( 'Anuncio', 'plugin-clasificados' ),
			'archives'              => __( 'Archivos de Anuncios', 'plugin-clasificados' ),
			'attributes'            => __( 'Atributos de Anuncio', 'plugin-clasificados' ),
			'parent_item_colon'     => __( 'Anuncio Padre:', 'plugin-clasificados' ),
			'all_items'             => __( 'Todos los Anuncios', 'plugin-clasificados' ),
			'add_new_item'          => __( 'Agregar Nuevo Anuncio', 'plugin-clasificados' ),
			'add_new'               => __( 'Agregar Nuevo', 'plugin-clasificados' ),
			'new_item'              => __( 'Nuevo Anuncio', 'plugin-clasificados' ),
			'edit_item'             => __( 'Editar Anuncio', 'plugin-clasificados' ),
			'update_item'           => __( 'Actualizar Anuncio', 'plugin-clasificados' ),
			'view_item'             => __( 'Ver Anuncio', 'plugin-clasificados' ),
			'view_items'            => __( 'Ver Anuncios', 'plugin-clasificados' ),
			'search_items'          => __( 'Buscar Anuncio', 'plugin-clasificados' ),
			'not_found'             => __( 'No encontrado', 'plugin-clasificados' ),
			'not_found_in_trash'    => __( 'No encontrado en la Papelera', 'plugin-clasificados' ),
			'featured_image'        => __( 'Imagen Destacada', 'plugin-clasificados' ),
			'set_featured_image'    => __( 'Establecer imagen destacada', 'plugin-clasificados' ),
			'remove_featured_image' => __( 'Remover imagen destacada', 'plugin-clasificados' ),
			'use_featured_image'    => __( 'Usar como imagen destacada', 'plugin-clasificados' ),
			'insert_into_item'      => __( 'Insertar en anuncio', 'plugin-clasificados' ),
			'uploaded_to_this_item' => __( 'Subido a este anuncio', 'plugin-clasificados' ),
			'items_list'            => __( 'Lista de anuncios', 'plugin-clasificados' ),
			'items_list_navigation' => __( 'Navegación de lista de anuncios', 'plugin-clasificados' ),
			'filter_items_list'     => __( 'Filtrar lista de anuncios', 'plugin-clasificados' ),
		);
		$args = array(
			'label'                 => __( 'Anuncio', 'plugin-clasificados' ),
			'description'           => __( 'Publicaciones de clasificados', 'plugin-clasificados' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'excerpt' ),
			'taxonomies'            => array( 'anuncio_categoria', 'anuncio_ubicacion' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-megaphone',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => 'anuncios',
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'rewrite'               => array( 'slug' => 'anuncio', 'with_front' => false ),
			'capability_type'       => 'post',
			'show_in_rest'          => true, // Needed for Gutenberg
		);
		register_post_type( 'anuncio', $args );
	}

	/**
	 * Register 'anuncio_categoria' and 'anuncio_ubicacion' Taxonomies.
	 */
	public static function register_taxonomies() {
		// Categoria
		$labels_cat = array(
			'name'                       => _x( 'Categorías', 'Taxonomy General Name', 'plugin-clasificados' ),
			'singular_name'              => _x( 'Categoría', 'Taxonomy Singular Name', 'plugin-clasificados' ),
			'menu_name'                  => __( 'Categorías', 'plugin-clasificados' ),
			'all_items'                  => __( 'Todas las Categorías', 'plugin-clasificados' ),
			'parent_item'                => __( 'Categoría Padre', 'plugin-clasificados' ),
			'parent_item_colon'          => __( 'Categoría Padre:', 'plugin-clasificados' ),
			'new_item_name'              => __( 'Nuevo Nombre de Categoría', 'plugin-clasificados' ),
			'add_new_item'               => __( 'Agregar Nueva Categoría', 'plugin-clasificados' ),
			'edit_item'                  => __( 'Editar Categoría', 'plugin-clasificados' ),
			'update_item'                => __( 'Actualizar Categoría', 'plugin-clasificados' ),
			'view_item'                  => __( 'Ver Categoría', 'plugin-clasificados' ),
			'separate_items_with_commas' => __( 'Separar categorías con comas', 'plugin-clasificados' ),
			'add_or_remove_items'        => __( 'Agregar o eliminar categorías', 'plugin-clasificados' ),
			'choose_from_most_used'      => __( 'Elegir de las más usadas', 'plugin-clasificados' ),
			'popular_items'              => __( 'Categorías Populares', 'plugin-clasificados' ),
			'search_items'               => __( 'Buscar Categorías', 'plugin-clasificados' ),
			'not_found'                  => __( 'No encontrado', 'plugin-clasificados' ),
			'no_terms'                   => __( 'No hay categorías', 'plugin-clasificados' ),
			'items_list'                 => __( 'Lista de categorías', 'plugin-clasificados' ),
			'items_list_navigation'      => __( 'Navegación de lista de categorías', 'plugin-clasificados' ),
		);
		$args_cat = array(
			'labels'                     => $labels_cat,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
			// Enable hierarchical rewrite so get_term_link() outputs /anuncios/mascotas/gatos/
			'rewrite'                    => array( 'slug' => 'anuncios', 'with_front' => false, 'hierarchical' => true ),
			'show_in_rest'               => true,
		);
		register_taxonomy( 'anuncio_categoria', array( 'anuncio' ), $args_cat );

		// Ubicacion (País > Ciudad > Distrito)
		$labels_loc = array(
			'name'                       => _x( 'Ubicaciones', 'Taxonomy General Name', 'plugin-clasificados' ),
			'singular_name'              => _x( 'Ubicación', 'Taxonomy Singular Name', 'plugin-clasificados' ),
			'menu_name'                  => __( 'Ubicaciones', 'plugin-clasificados' ),
			'all_items'                  => __( 'Todas las Ubicaciones', 'plugin-clasificados' ),
			'parent_item'                => __( 'Ubicación Padre', 'plugin-clasificados' ),
			'parent_item_colon'          => __( 'Ubicación Padre:', 'plugin-clasificados' ),
			'new_item_name'              => __( 'Nuevo Nombre de Ubicación', 'plugin-clasificados' ),
			'add_new_item'               => __( 'Agregar Nueva Ubicación', 'plugin-clasificados' ),
			'edit_item'                  => __( 'Editar Ubicación', 'plugin-clasificados' ),
			'update_item'                => __( 'Actualizar Ubicación', 'plugin-clasificados' ),
			'view_item'                  => __( 'Ver Ubicación', 'plugin-clasificados' ),
			'separate_items_with_commas' => __( 'Separar ubicaciones con comas', 'plugin-clasificados' ),
			'add_or_remove_items'        => __( 'Agregar o eliminar ubicaciones', 'plugin-clasificados' ),
			'choose_from_most_used'      => __( 'Elegir de las más usadas', 'plugin-clasificados' ),
			'popular_items'              => __( 'Ubicaciones Populares', 'plugin-clasificados' ),
			'search_items'               => __( 'Buscar Ubicaciones', 'plugin-clasificados' ),
			'not_found'                  => __( 'No encontrado', 'plugin-clasificados' ),
			'no_terms'                   => __( 'No hay ubicaciones', 'plugin-clasificados' ),
			'items_list'                 => __( 'Lista de ubicaciones', 'plugin-clasificados' ),
			'items_list_navigation'      => __( 'Navegación de lista de ubicaciones', 'plugin-clasificados' ),
		);
		$args_loc = array(
			'labels'                     => $labels_loc,
			'hierarchical'               => true, // Essential for Country > City > District
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => false,
			// Enable hierarchical rewrite so get_term_link() outputs /ubicacion/lima/san-borja/
			'rewrite'                    => array( 'slug' => 'ubicacion', 'with_front' => false, 'hierarchical' => true ),
			'show_in_rest'               => true,
		);
		register_taxonomy( 'anuncio_ubicacion', array( 'anuncio' ), $args_loc );
	}
}
