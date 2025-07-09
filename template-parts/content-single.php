<?php

/**
 * Template part para mostrar el contenido de posts individuales
 *
 * @package WP_Tailwind_Theme
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('bg-white rounded-lg shadow-sm overflow-hidden mb-8 border border-gray-100'); ?>>
    <div class="p-8">
        <header class="entry-header mb-6">
            <?php the_title('<h1 class="entry-title text-3xl md:text-4xl fancy-text font-medium text-[#424242] mb-4">', '</h1>'); ?>

            <div class="entry-meta text-sm text-gray-600 mb-6 flex flex-wrap items-center">
                <?php
                // Autor
                echo '<span class="inline-flex items-center mr-6 mb-2">';
                echo '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-[#8BAB8D]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>';
                the_author_posts_link();
                echo '</span>';

                // Fecha
                echo '<span class="inline-flex items-center mr-6 mb-2">';
                echo '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-[#D9ADB7]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>';
                echo '<time datetime="' . esc_attr(get_the_date('c')) . '">' . esc_html(get_the_date()) . '</time>';
                echo '</span>';

                // Comentarios
                if (comments_open()) {
                    echo '<span class="inline-flex items-center mb-2">';
                    echo '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-[#4F8A8B]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                          </svg>';
                    comments_popup_link(
                        __('Sin comentarios', 'wp-tailwind-theme'),
                        __('1 comentario', 'wp-tailwind-theme'),
                        __('% comentarios', 'wp-tailwind-theme')
                    );
                    echo '</span>';
                }
                ?>
            </div>
        </header><!-- .entry-header -->

        <?php if (has_post_thumbnail()) : ?>
            <div class="post-thumbnail mb-8 overflow-hidden rounded-lg">
                <?php the_post_thumbnail('large', array('class' => 'w-full h-auto hover:scale-105 transition duration-700')); ?>
            </div><!-- .post-thumbnail -->
        <?php endif; ?>

        <div class="entry-content prose max-w-none text-gray-700">
            <?php
            the_content();

            wp_link_pages(
                array(
                    'before' => '<div class="page-links mt-6 pt-4 border-t border-gray-200">' . esc_html__('Páginas:', 'wp-tailwind-theme'),
                    'after'  => '</div>',
                )
            );
            ?>
        </div><!-- .entry-content -->

        <footer class="entry-footer mt-8 pt-4 border-t border-gray-100">
            <?php
            // Categorías
            if (has_category()) :
                echo '<div class="categories-links mb-3">';
                echo '<span class="text-sm text-[#4F8A8B] mr-2 font-medium">' . esc_html__('Categorías:', 'wp-tailwind-theme') . '</span>';
                echo '<span class="text-sm">' . get_the_category_list(', ') . '</span>';
                echo '</div>';
            endif;

            // Etiquetas
            if (has_tag()) :
                echo '<div class="tags-links">';
                echo '<span class="text-sm text-[#8BAB8D] mr-2 font-medium">' . esc_html__('Etiquetas:', 'wp-tailwind-theme') . '</span>';
                echo '<span class="text-sm">' . get_the_tag_list('', ', ') . '</span>';
                echo '</div>';
            endif;

            // Enlace de edición
            edit_post_link(
                sprintf(
                    wp_kses(
                        /* translators: %s: Nombre del post */
                        __('Editar <span class="screen-reader-text">%s</span>', 'wp-tailwind-theme'),
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                        )
                    ),
                    get_the_title()
                ),
                '<div class="edit-link mt-4 text-sm flex items-center hover:text-[#4F8A8B] transition duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>',
                '</div>'
            );
            ?>
        </footer><!-- .entry-footer -->
    </div>
</article><!-- #post-<?php the_ID(); ?> -->