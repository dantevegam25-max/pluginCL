<?php
/**
 * Template Name: Archive Anuncios Silo
 * The template for displaying archive pages for our custom silos.
 *
 * Compatible with GeneratePress Pro structure.
 */

get_header();

$cat_slug = get_query_var( 'anuncio_cat_silo' );
$ciudad_slug = get_query_var( 'anuncio_ciudad' );
$distrito_slug = get_query_var( 'anuncio_distrito' );

// Build query arguments based on silos
$tax_queries = array( 'relation' => 'AND' );

if ( ! empty( $cat_slug ) ) {
	$tax_queries[] = array(
		'taxonomy' => 'anuncio_categoria',
		'field'    => 'slug',
		'terms'    => $cat_slug,
	);
}

// Logic for hierarchical locations
if ( ! empty( $distrito_slug ) ) {
    // If district is provided, filter by district
	$tax_queries[] = array(
		'taxonomy' => 'anuncio_ubicacion',
		'field'    => 'slug',
		'terms'    => $distrito_slug,
	);
} elseif ( ! empty( $ciudad_slug ) ) {
     // If only city is provided, filter by city (and its districts automatically if hierarchical)
	$tax_queries[] = array(
		'taxonomy' => 'anuncio_ubicacion',
		'field'    => 'slug',
		'terms'    => $ciudad_slug,
	);
}

// Optimization: Use 'no_found_rows' if pagination isn't needed, but typically it is.
$args = array(
	'post_type'      => 'anuncio',
	'posts_per_page' => 10,
	'paged'          => get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1,
	'tax_query'      => $tax_queries,
);

$anuncios_query = new WP_Query( $args );

// Generate Dynamic H1
$dynamic_h1 = Plugin_Clasificados_SEO_Manager::get_dynamic_h1();
if ( empty($dynamic_h1) ) {
    $dynamic_h1 = __( 'Clasificados', 'plugin-clasificados' );
}

?>

<div id="primary" class="content-area">
	<main id="main" class="site-main">

		<header class="page-header">
			<h1 class="page-title"><?php echo esc_html( $dynamic_h1 ); ?></h1>
            <div class="breadcrumbs">
               <!-- Basic Breadcrumbs MVP -->
               <a href="<?php echo home_url(); ?>">Inicio</a> &raquo;
               <a href="<?php echo home_url('/' . $cat_slug . '/'); ?>"><?php echo esc_html(ucfirst($cat_slug)); ?></a>
               <?php if ( ! empty($ciudad_slug) ) : ?>
                    &raquo; <a href="<?php echo home_url('/' . $cat_slug . '/' . $ciudad_slug . '/'); ?>"><?php echo esc_html(ucfirst(str_replace('-',' ',$ciudad_slug))); ?></a>
               <?php endif; ?>
               <?php if ( ! empty($distrito_slug) ) : ?>
                    &raquo; <span><?php echo esc_html(ucfirst(str_replace('-',' ',$distrito_slug))); ?></span>
               <?php endif; ?>
            </div>
		</header><!-- .page-header -->

		<?php if ( $anuncios_query->have_posts() ) : ?>

			<div class="anuncios-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;">
				<?php
				while ( $anuncios_query->have_posts() ) :
					$anuncios_query->the_post();
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'anuncio-card' ); ?> style="border: 1px solid #ddd; padding: 15px; border-radius: 5px;">

						<?php if ( has_post_thumbnail() ) : ?>
							<div class="anuncio-thumbnail" style="margin-bottom: 10px;">
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( 'medium', array( 'style' => 'width: 100%; height: auto;' ) ); ?>
								</a>
							</div>
						<?php endif; ?>

						<header class="entry-header">
							<?php the_title( '<h2 class="entry-title" style="font-size: 1.2em; margin-bottom: 10px;"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
						</header><!-- .entry-header -->

						<div class="entry-summary">
							<?php the_excerpt(); ?>
						</div><!-- .entry-summary -->

                        <!-- MVP custom fields display with ACF fallback -->
                        <?php
                        $precio = function_exists('get_field') ? get_field('precio') : get_post_meta( get_the_ID(), 'precio', true );
                        if ( $precio ) {
                            echo '<p class="anuncio-precio"><strong>Precio:</strong> ' . esc_html($precio) . '</p>';
                        }
                        ?>

					</article><!-- #post-<?php the_ID(); ?> -->
					<?php
				endwhile;
				?>
			</div>

			<?php
			// Pagination
            $base_url = home_url('/' . $cat_slug . '/');
            if ( ! empty($ciudad_slug) ) $base_url .= $ciudad_slug . '/';
            if ( ! empty($distrito_slug) ) $base_url .= $distrito_slug . '/';

            echo paginate_links( array(
                'base' => $base_url . '%_%',
                'format' => 'page/%#%/',
                'current' => max( 1, get_query_var('paged') ),
                'total' => $anuncios_query->max_num_pages,
                'prev_text' => __( 'Anterior', 'plugin-clasificados' ),
                'next_text' => __( 'Siguiente', 'plugin-clasificados' ),
            ) );

			wp_reset_postdata();

		else :
			?>
			<section class="no-results not-found">
				<header class="page-header">
					<h2 class="page-title"><?php _e( 'No hay anuncios', 'plugin-clasificados' ); ?></h2>
				</header><!-- .page-header -->
				<div class="page-content">
					<p><?php _e( 'Lo sentimos, no encontramos anuncios para esta ubicación y categoría.', 'plugin-clasificados' ); ?></p>
				</div><!-- .page-content -->
			</section><!-- .no-results -->
			<?php
		endif;
		?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php
// Optional sidebar could be loaded here based on theme compatibility
get_sidebar();
get_footer();
