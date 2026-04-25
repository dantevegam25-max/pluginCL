<?php
/**
 * Template Name: Taxonomy Anuncio Ubicacion
 * Fallback template for native taxonomy pages if accessed directly.
 * We want to redirect them or display them similar to the silo archive.
 */

// If accessed directly, try to use the archive template logic
// For MVP, we simply load the archive-anuncios.php if possible, or standard archive.

$custom_template = locate_template( array(
    'plugin-clasificados/archive-anuncios.php',
    'archive-anuncios.php',
) );

if ( ! $custom_template ) {
    $custom_template = PLUGIN_CLASIFICADOS_DIR . 'templates/archive-anuncios.php';
}

if ( file_exists( $custom_template ) ) {
    include( $custom_template );
} else {
    // Ultimate fallback
    get_header(); ?>
    <div id="primary" class="content-area">
        <main id="main" class="site-main">
            <header class="page-header">
                <?php
                the_archive_title( '<h1 class="page-title">', '</h1>' );
                the_archive_description( '<div class="archive-description">', '</div>' );
                ?>
            </header>
            <?php
            if ( have_posts() ) {
                while ( have_posts() ) {
                    the_post();
                    // Load basic content
                    the_title('<h2>', '</h2>');
                    the_excerpt();
                }
            } else {
                echo '<p>No hay anuncios aquí.</p>';
            }
            ?>
        </main>
    </div>
    <?php get_footer();
}
