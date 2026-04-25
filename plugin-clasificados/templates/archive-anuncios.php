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

                       // Need to prefix with base slug if configured or standard 'anuncios'/'ubicacion'
                       // For simplicity in MVP breadcrumbs, we build relative to root.
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

                    // Fetch ACF fields for the card
                    $precio    = function_exists('get_field') ? get_field('precio') : get_post_meta( get_the_ID(), 'precio', true );
                    $operacion = function_exists('get_field') ? get_field('operacion') : '';
                    $marca     = function_exists('get_field') ? get_field('marca') : '';
                    $modelo    = function_exists('get_field') ? get_field('modelo') : '';
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'anuncio-card' ); ?> style="border: 1px solid #ddd; padding: 15px; border-radius: 8px; display: flex; flex-direction: column; background: #fff;">

                        <!-- Image Area -->
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="anuncio-thumbnail" style="margin-bottom: 15px; position: relative;">
                                <?php if ( $operacion ) : ?>
                                    <span style="position: absolute; top: 10px; left: 10px; background: rgba(2, 132, 199, 0.9); color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.75em; font-weight: bold; text-transform: uppercase; z-index: 2;">
                                        <?php echo esc_html( $operacion ); ?>
                                    </span>
                                <?php endif; ?>
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( 'medium', array( 'style' => 'width: 100%; height: 200px; object-fit: cover; border-radius: 4px;' ) ); ?>
								</a>
							</div>
						<?php endif; ?>

                        <!-- Content Area -->
						<header class="entry-header" style="flex-grow: 1;">
							<?php the_title( '<h2 class="entry-title" style="font-size: 1.2em; margin: 0 0 10px 0; line-height: 1.3;"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark" style="color: #333; text-decoration: none;">', '</a></h2>' ); ?>

                            <?php if($marca || $modelo): ?>
                                <div class="anuncio-meta" style="font-size: 0.85em; color: #666; margin-bottom: 10px;">
                                    <?php echo esc_html( implode(' • ', array_filter([$marca, $modelo])) ); ?>
                                </div>
                            <?php endif; ?>
						</header>

                        <!-- Footer Area (Price) -->
                        <div class="anuncio-footer" style="margin-top: auto; padding-top: 15px; border-top: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                            <div class="anuncio-precio">
                                <?php if ( $precio ) : ?>
                                    <strong style="color: #16a34a; font-size: 1.3em;">$<?php echo esc_html( number_format($precio, 0) ); ?></strong>
                                <?php else: ?>
                                    <strong style="color: #666; font-size: 1.1em;">A tratar</strong>
                                <?php endif; ?>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="button" style="font-size: 0.85em; padding: 5px 10px;">Ver detalle</a>
                        </div>

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
