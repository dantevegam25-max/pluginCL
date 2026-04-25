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
					<div class="entry-meta" style="margin-bottom: 20px; font-size: 0.9em; color: #666;">
						<?php
                        // Display categories
                        $terms = get_the_terms( get_the_ID(), 'anuncio_categoria' );
                        if ( $terms && ! is_wp_error( $terms ) ) {
                            $cat_links = array();
                            foreach ( $terms as $term ) {
                                $cat_links[] = '<a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a>';
                            }
                            echo '<span class="cat-links">' . __( 'Categoría: ', 'plugin-clasificados' ) . implode( ', ', $cat_links ) . '</span> &bull; ';
                        }

                        // Display location
                        $loc_terms = get_the_terms( get_the_ID(), 'anuncio_ubicacion' );
                        if ( $loc_terms && ! is_wp_error( $loc_terms ) ) {
                             $loc_names = array();
                             foreach ( $loc_terms as $lterm ) {
                                 $loc_names[] = '<a href="' . esc_url( get_term_link( $lterm ) ) . '">' . esc_html( $lterm->name ) . '</a>';
                             }
                             echo '<span class="loc-links">' . __( 'Ubicación: ', 'plugin-clasificados' ) . implode( ', ', $loc_names ) . '</span>';
                        }
                        ?>
					</div><!-- .entry-meta -->
				</header><!-- .entry-header -->

                <div class="anuncio-layout" style="display: flex; flex-wrap: wrap; gap: 30px;">
                    <!-- Left Column: Image & Description -->
                    <div class="anuncio-content-col" style="flex: 1 1 60%;">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="post-thumbnail" style="margin-bottom: 20px;">
                                <?php the_post_thumbnail( 'large', array('style' => 'width:100%; height:auto; border-radius: 8px;') ); ?>
                            </div>
                        <?php endif; ?>

                        <div class="entry-content">
                            <h3><?php _e('Descripción del anuncio', 'plugin-clasificados'); ?></h3>
                            <?php the_content(); ?>
                        </div>
                    </div>

                    <!-- Right Column: ACF Details & CTA -->
                    <div class="anuncio-sidebar-col" style="flex: 1 1 30%; min-width: 300px;">
                        <div class="anuncio-details-box" style="background: #f9f9f9; padding: 20px; border-radius: 8px; border: 1px solid #eee;">

                            <?php
                            // ACF Fetch logic (with fallback for precio)
                            $precio      = function_exists('get_field') ? get_field('precio') : get_post_meta( get_the_ID(), 'precio', true );
                            $operacion   = function_exists('get_field') ? get_field('operacion') : '';
                            $marca       = function_exists('get_field') ? get_field('marca') : '';
                            $modelo      = function_exists('get_field') ? get_field('modelo') : '';
                            $transmision = function_exists('get_field') ? get_field('transmision') : '';
                            $tipo_auto   = function_exists('get_field') ? get_field('tipo_de_auto') : '';
                            $telefono    = function_exists('get_field') ? get_field('telefono') : '';
                            $email       = function_exists('get_field') ? get_field('email') : '';
                            ?>

                            <!-- Pricing Block -->
                            <div class="precio-block" style="text-align: center; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #ddd;">
                                <?php if ( $operacion ) : ?>
                                    <span style="display:inline-block; background: #e0f2fe; color: #0284c7; padding: 4px 8px; border-radius: 4px; font-size: 0.8em; font-weight: bold; text-transform: uppercase; margin-bottom: 10px;">
                                        <?php echo esc_html( $operacion ); ?>
                                    </span><br>
                                <?php endif; ?>

                                <?php if ( $precio ) : ?>
                                    <span class="anuncio-precio-destacado" style="font-size: 2em; font-weight: bold; color: #16a34a;">
                                        $<?php echo esc_html( number_format($precio, 0) ); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="anuncio-precio-destacado" style="font-size: 1.5em; font-weight: bold; color: #666;">
                                        Precio a tratar
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Vehicle Details -->
                            <?php if ( $marca || $modelo || $transmision || $tipo_auto ) : ?>
                                <h4 style="margin-top: 0;">Detalles del Vehículo</h4>
                                <ul style="list-style: none; padding: 0; margin: 0 0 20px 0; font-size: 0.95em;">
                                    <?php if($marca): ?>
                                        <li style="padding: 8px 0; border-bottom: 1px dashed #ddd;"><strong>Marca:</strong> <span style="float:right;"><?php echo esc_html($marca); ?></span></li>
                                    <?php endif; ?>
                                    <?php if($modelo): ?>
                                        <li style="padding: 8px 0; border-bottom: 1px dashed #ddd;"><strong>Modelo/Año:</strong> <span style="float:right;"><?php echo esc_html($modelo); ?></span></li>
                                    <?php endif; ?>
                                    <?php if($transmision): ?>
                                        <li style="padding: 8px 0; border-bottom: 1px dashed #ddd;"><strong>Transmisión:</strong> <span style="float:right;"><?php echo esc_html($transmision); ?></span></li>
                                    <?php endif; ?>
                                    <?php if($tipo_auto): ?>
                                        <li style="padding: 8px 0;"><strong>Tipo:</strong> <span style="float:right;"><?php echo esc_html($tipo_auto); ?></span></li>
                                    <?php endif; ?>
                                </ul>
                            <?php endif; ?>

                            <!-- Contact Actions -->
                            <div class="anuncio-actions" style="margin-top: 20px;">
                                <?php if ( $telefono ) :
                                    // Clean phone for wa.me link
                                    $clean_phone = preg_replace('/[^0-9]/', '', $telefono);
                                ?>
                                    <a href="https://wa.me/<?php echo esc_attr($clean_phone); ?>?text=Hola,%20me%20interesa%20el%20anuncio:%20<?php echo urlencode(get_the_title()); ?>" target="_blank" style="display: block; background: #25D366; color: white; text-align: center; padding: 12px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-bottom: 10px;">
                                        <span class="dashicons dashicons-whatsapp" style="vertical-align: middle;"></span> Contactar por WhatsApp
                                    </a>
                                <?php endif; ?>

                                <?php if ( $email ) : ?>
                                    <a href="mailto:<?php echo esc_attr($email); ?>?subject=Consulta sobre anuncio: <?php echo esc_attr(get_the_title()); ?>" style="display: block; background: #333; color: white; text-align: center; padding: 12px; border-radius: 6px; text-decoration: none; font-weight: bold;">
                                        <span class="dashicons dashicons-email-alt" style="vertical-align: middle;"></span> Enviar Email
                                    </a>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                </div>

			</article><!-- #post-<?php the_ID(); ?> -->

			<?php
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
