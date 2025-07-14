<?php

/**
 * The template for displaying all pages
 */

get_header();

// Mostrar el banner personalizado si está habilitado
if (function_exists('wptbt_display_banner')) {
    wptbt_display_banner();
}
?>

<main id="primary" class="site-main pt-12 -mb-2">
    <?php
    while (have_posts()) :
        the_post();

        // Obtener el contenido de la página como bloques
        $blocks = parse_blocks(get_the_content());

        // Si no hay bloques, mostrar el contenido normalmente
        if (empty($blocks)) {
            echo '<div class="container mx-auto px-4">';
            get_template_part('template-parts/content', 'page');
            echo '</div>';
        } else {
            // Procesar cada bloque
            foreach ($blocks as $block) {
                // Comprobar si es un bloque de beneficios u otro bloque de ancho completo
                if (
                    $block['blockName'] === 'wptbt/benefits-block' ||
                    $block['blockName'] === 'wptbt/gallery-block' ||
                    $block['blockName'] === 'wptbt/google-reviews-block' ||
                    $block['blockName'] === 'wptbt/tours-block' ||
                    $block['blockName'] === 'wptbt/interactive-map-block' ||
                    $block['blockName'] === 'core/group' && isset($block['attrs']['align']) && $block['attrs']['align'] === 'full'
                ) {
                    // Renderizar el bloque sin contenedor
                    echo render_block($block);
                } else {
                    // Renderizar otros bloques con el contenedor
                    echo '<div class="container mx-auto px-4 mb-8">';
                    echo render_block($block);
                    echo '</div>';
                }
            }
        }

        // Si los comentarios están habilitados y hay al menos un comentario
        if (comments_open() || get_comments_number()) :
            echo '<div class="container mx-auto px-4">';
            echo '<div class="comments-section mt-8 bg-white rounded-lg shadow-sm p-6 border border-gray-100">';
            comments_template();
            echo '</div>';
            echo '</div>';
        endif;

    endwhile;
    ?>
</main>

<?php
get_footer();
