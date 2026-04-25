<?php
/**
 * The template for displaying all single anuncios.
 *
 * Compatible with GeneratePress Pro.
 */

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

				<header class="entry-header">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
					<div class="entry-meta">
						<?php
                        // Display categories
                        $terms = get_the_terms( get_the_ID(), 'anuncio_categoria' );
                        if ( $terms && ! is_wp_error( $terms ) ) {
                            $cat_links = array();
                            foreach ( $terms as $term ) {
                                $cat_links[] = '<a href="' . esc_url( home_url('/' . $term->slug . '/') ) . '">' . esc_html( $term->name ) . '</a>';
                            }
                            echo '<span class="cat-links">' . __( 'Categoría: ', 'plugin-clasificados' ) . implode( ', ', $cat_links ) . '</span><br>';
                        }

                        // Display location (simplified for MVP)
                        $loc_terms = get_the_terms( get_the_ID(), 'anuncio_ubicacion' );
                        if ( $loc_terms && ! is_wp_error( $loc_terms ) ) {
                             $loc_names = array();
                             foreach ( $loc_terms as $lterm ) {
                                 $loc_names[] = $lterm->name;
                             }
                             echo '<span class="loc-links">' . __( 'Ubicación: ', 'plugin-clasificados' ) . implode( ', ', $loc_names ) . '</span>';
                        }
                        ?>
					</div><!-- .entry-meta -->
				</header><!-- .entry-header -->

				<?php if ( has_post_thumbnail() ) : ?>
					<div class="post-thumbnail" style="margin-top: 20px; margin-bottom: 20px;">
						<?php the_post_thumbnail( 'large' ); ?>
					</div><!-- .post-thumbnail -->
				<?php endif; ?>

				<div class="entry-content">
                    <?php
                    // ACF Pro implementation with fallback
                    $precio = function_exists('get_field') ? get_field('precio') : get_post_meta( get_the_ID(), 'precio', true );
                    if ( $precio ) {
                        echo '<div class="anuncio-precio-destacado" style="font-size: 1.5em; font-weight: bold; color: #2ecc71; margin-bottom: 20px;">' . esc_html($precio) . '</div>';
                    }

                    // Example of checking for other potential ACF fields (e.g. galería)
                    if ( function_exists('get_field') && get_field('galeria') ) {
                        $images = get_field('galeria');
                        if( $images ): ?>
                            <div class="anuncio-galeria" style="display:flex; gap:10px; margin-bottom:20px;">
                                <?php foreach( $images as $image ): ?>
                                    <img src="<?php echo esc_url($image['sizes']['thumbnail']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
                                <?php endforeach; ?>
                            </div>
                        <?php endif;
                    }
                    ?>

					<?php
					the_content();
					?>
				</div><!-- .entry-content -->

			</article><!-- #post-<?php the_ID(); ?> -->

			<?php
			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; // End of the loop.
		?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
