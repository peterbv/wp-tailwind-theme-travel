<?php

/**
 * Template part para mostrar el contenido de las páginas
 *
 * @package WP_Tailwind_Theme
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('bg-white rounded-lg shadow-sm overflow-hidden mb-8 border border-gray-100'); ?>>
    <div class="p-8">
        <header class="entry-header mb-8">
            <?php the_title('<h1 class="entry-title text-3xl md:text-4xl fancy-text font-medium text-[#424242] mb-4">', '</h1>'); ?>
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

        <?php if (get_edit_post_link()) : ?>
            <footer class="entry-footer mt-8 pt-4 border-t border-gray-100 text-sm text-gray-500">
                <?php
                edit_post_link(
                    sprintf(
                        wp_kses(
                            /* translators: %s: Nombre de la página */
                            __('Editar <span class="screen-reader-text">%s</span>', 'wp-tailwind-theme'),
                            array(
                                'span' => array(
                                    'class' => array(),
                                ),
                            )
                        ),
                        get_the_title()
                    ),
                    '<span class="edit-link flex items-center hover:text-[#4F8A8B] transition duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>',
                    '</span>'
                );
                ?>
            </footer><!-- .entry-footer -->
        <?php endif; ?>
    </div>
</article><!-- #post-<?php the_ID(); ?> -->