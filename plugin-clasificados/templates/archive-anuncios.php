<?php
/**
 * Template Name: Archive Anuncios Silo
 * The template for displaying archive pages for our custom silos.
 *
 * Compatible with GeneratePress Pro structure.
 */

get_header();

// We no longer rely on rigid positions. We let WP_Query handle it because pre_get_posts set the tax_queries.
// But we can extract the path to build breadcrumbs and pagination correctly.
$silo_path = get_query_var( 'anuncio_silo_path' );

// Optimization: Use 'no_found_rows' if pagination isn't needed, but typically it is.
// Instead of new WP_Query, we rely on the main query that was already modified by pre_get_posts!
global $wp_query;
$anuncios_query = $wp_query;

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
            <div class="breadcrumbs" style="font-size:0.9em; margin-bottom: 20px;">
               <a href="<?php echo home_url(); ?>">Inicio</a>
               <?php
               if ( ! empty( $silo_path ) ) {
                   $parts = explode('/', $silo_path);
                   $current_path = '';
                   foreach ( $parts as $part ) {
                       if ( empty($part) ) continue;
                       $current_path .= '/' . $part;

                       // Try to get a nice name if it's a term
                       $name = ucfirst(str_replace('-', ' ', $part));
                       $term_cat = get_term_by('slug', $part, 'anuncio_categoria');
                       $term_loc = get_term_by('slug', $part, 'anuncio_ubicacion');

                       if ( $term_cat ) {
                           $name = $term_cat->name;
                       } elseif ( $term_loc ) {
                           $name = $term_loc->name;
                       }

                       echo ' &raquo; <a href="' . esc_url(home_url($current_path . '/')) . '">' . esc_html($name) . '</a>';
                   }
               }
               ?>
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
            $base_url = home_url('/' . $silo_path . '/');

            echo paginate_links( array(
                'base' => $base_url . '%_%',
                'format' => 'page/%#%/',
                'current' => max( 1, get_query_var('paged') ),
                'total' => $anuncios_query->max_num_pages,
                'prev_text' => __( 'Anterior', 'plugin-clasificados' ),
                'next_text' => __( 'Siguiente', 'plugin-clasificados' ),
            ) );

		else :
			?>
			<section class="no-results not-found">
				<header class="page-header">
					<h2 class="page-title"><?php _e( 'No hay anuncios', 'plugin-clasificados' ); ?></h2>
				</header><!-- .page-header -->
				<div class="page-content">
					<p><?php _e( 'Lo sentimos, no encontramos anuncios para esta búsqueda.', 'plugin-clasificados' ); ?></p>
				</div><!-- .page-content -->
			</section><!-- .no-results -->
			<?php
		endif;
		?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
