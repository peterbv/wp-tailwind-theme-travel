<?php

/**
 * Plantilla parcial para mostrar contenido de artículos
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('bg-white rounded-lg shadow-md overflow-hidden transition-shadow duration-300 hover:shadow-lg'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <div class="post-thumbnail">
            <a href="<?php the_permalink(); ?>">
                <?php
                the_post_thumbnail('medium', array(
                    'class' => 'w-full h-48 object-cover',
                    'alt' => get_the_title()
                ));
                ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="p-6">
        <header class="entry-header mb-4">
            <?php if (is_sticky() && is_home() && !is_paged()) : ?>
                <span class="inline-block bg-primary text-white text-xs font-bold py-1 px-2 rounded-full mr-2 uppercase">
                    <?php echo esc_html__('Destacado', 'wp-tailwind-blocks'); ?>
                </span>
            <?php endif; ?>

            <?php the_title(sprintf('<h2 class="entry-title text-xl font-bold mb-2"><a href="%s" class="text-gray-900 hover:text-primary" rel="bookmark">', esc_url(get_permalink())), '</a></h2>'); ?>

            <?php if ('post' === get_post_type()) : ?>
                <div class="entry-meta text-sm text-gray-600 mb-3">
                    <?php
                    wptbt_posted_on();
                    wptbt_posted_by();
                    ?>
                </div>
            <?php endif; ?>
        </header><!-- .entry-header -->

        <div class="entry-content prose">
            <?php
            if (is_singular()) :
                the_content(
                    sprintf(
                        wp_kses(
                            /* translators: %s: Nombre del artículo */
                            __('Continuar leyendo<span class="screen-reader-text"> "%s"</span>', 'wp-tailwind-blocks'),
                            array(
                                'span' => array(
                                    'class' => array(),
                                ),
                            )
                        ),
                        get_the_title()
                    )
                );

                wp_link_pages(
                    array(
                        'before' => '<div class="page-links">' . esc_html__('Páginas:', 'wp-tailwind-blocks'),
                        'after'  => '</div>',
                    )
                );
            else :
                the_excerpt();
            ?>
                <div class="mt-4">
                    <a href="<?php the_permalink(); ?>" class="inline-block mt-2 px-4 py-2 bg-primary text-white rounded hover:bg-blue-700 transition duration-200">
                        <?php echo esc_html__('Leer más', 'wp-tailwind-blocks'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div><!-- .entry-content -->

        <footer class="entry-footer mt-4 pt-4 border-t border-gray-100">
            <div class="flex flex-wrap gap-2 text-xs text-gray-600">
                <?php
                // Categorías
                if (has_category()) :
                ?>
                    <div class="post-categories mr-4">
                        <span class="inline-block mr-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </span>
                        <?php
                        $categories_list = get_the_category_list(', ');
                        if ($categories_list) {
                            printf('<span class="cat-links">%s</span>', $categories_list);
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <?php
                // Etiquetas
                if (has_tag()) :
                ?>
                    <div class="post-tags">
                        <span class="inline-block mr-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                        <?php
                        $tags_list = get_the_tag_list('', ', ');
                        if ($tags_list) {
                            printf('<span class="tag-links">%s</span>', $tags_list);
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (comments_open()) : ?>
                    <div class="post-comments ml-auto">
                        <span class="inline-block mr-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </span>
                        <?php
                        comments_popup_link(
                            sprintf(
                                wp_kses(
                                    /* translators: %s: Nombre del artículo */
                                    __('Deja un comentario<span class="screen-reader-text"> en %s</span>', 'wp-tailwind-blocks'),
                                    array(
                                        'span' => array(
                                            'class' => array(),
                                        ),
                                    )
                                ),
                                get_the_title()
                            )
                        );
                        ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (is_singular() && is_multisite()) : ?>
                <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-500">
                    <?php
                    $current_blog_id = get_current_blog_id();
                    $current_blog_details = get_blog_details($current_blog_id);
                    printf(
                        esc_html__('Publicado en: %s', 'wp-tailwind-blocks'),
                        '<a href="' . esc_url($current_blog_details->siteurl) . '" class="text-primary hover:underline">' . esc_html($current_blog_details->blogname) . '</a>'
                    );
                    ?>
                </div>
            <?php endif; ?>

            <?php
            // Si estamos en un entorno de edición, mostramos el botón editar
            edit_post_link(
                sprintf(
                    wp_kses(
                        /* translators: %s: Nombre del artículo */
                        __('Editar<span class="screen-reader-text"> %s</span>', 'wp-tailwind-blocks'),
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                        )
                    ),
                    get_the_title()
                ),
                '<div class="edit-link mt-2 text-xs">',
                '</div>'
            );
            ?>
        </footer><!-- .entry-footer -->
    </div>
</article><!-- #post-<?php the_ID(); ?> -->