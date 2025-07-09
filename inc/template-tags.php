<?php

/**
 * Funciones de etiquetas de plantilla personalizadas para este tema
 *
 * Estas funciones son específicas para mostrar elementos de contenido
 * como metadatos, imágenes destacadas y otros elementos visuales.
 *
 * @package WP_Tailwind_Blocks
 */

if (!function_exists('wptbt_posted_on')) :
    /**
     * Imprime la fecha de publicación del artículo con formato HTML.
     */
    function wptbt_posted_on()
    {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if (get_the_time('U') !== get_the_modified_time('U')) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated hidden" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf(
            $time_string,
            esc_attr(get_the_date(DATE_W3C)),
            esc_html(get_the_date()),
            esc_attr(get_the_modified_date(DATE_W3C)),
            esc_html(get_the_modified_date())
        );

        $posted_on = sprintf(
            /* translators: %s: fecha de publicación */
            esc_html_x('Publicado el %s', 'fecha de publicación', 'wp-tailwind-blocks'),
            '<a href="' . esc_url(get_permalink()) . '" rel="bookmark" class="text-primary hover:underline">' . $time_string . '</a>'
        );

        echo '<span class="posted-on mr-4">' . $posted_on . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
endif;

if (!function_exists('wptbt_posted_by')) :
    /**
     * Imprime el nombre del autor del artículo con formato HTML.
     */
    function wptbt_posted_by()
    {
        $byline = sprintf(
            /* translators: %s: nombre del autor */
            esc_html_x('por %s', 'nombre del autor', 'wp-tailwind-blocks'),
            '<span class="author vcard"><a class="url fn n text-primary hover:underline" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span>'
        );

        echo '<span class="byline">' . $byline . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
endif;

if (!function_exists('wptbt_entry_footer')) :
    /**
     * Imprime HTML con meta información para categorías, etiquetas y comentarios.
     */
    function wptbt_entry_footer()
    {
        // Ocultar categoría y etiqueta para páginas.
        if ('post' === get_post_type()) {
            /* translators: usado entre elementos de la lista, solo visible para lectores de pantalla */
            $categories_list = get_the_category_list(esc_html__(', ', 'wp-tailwind-blocks'));
            if ($categories_list) {
                /* translators: 1: lista de categorías */
                printf('<span class="cat-links mr-4"><span class="inline-block mr-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg></span>' . esc_html__('Categorías: ', 'wp-tailwind-blocks') . '%1$s</span>', $categories_list); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }

            /* translators: usado entre elementos de la lista, solo visible para lectores de pantalla */
            $tags_list = get_the_tag_list('', esc_html__(', ', 'wp-tailwind-blocks'));
            if ($tags_list) {
                /* translators: 1: lista de etiquetas */
                printf('<span class="tags-links"><span class="inline-block mr-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg></span>' . esc_html__('Etiquetas: ', 'wp-tailwind-blocks') . '%1$s</span>', $tags_list); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
        }

        if (!is_single() && !post_password_required() && (comments_open() || get_comments_number())) {
            echo '<span class="comments-link ml-4"><span class="inline-block mr-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg></span>';
            comments_popup_link(
                sprintf(
                    wp_kses(
                        /* translators: %s: nombre del artículo */
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
            echo '</span>';
        }

        edit_post_link(
            sprintf(
                wp_kses(
                    /* translators: %s: nombre del artículo */
                    __('Editar<span class="screen-reader-text"> %s</span>', 'wp-tailwind-blocks'),
                    array(
                        'span' => array(
                            'class' => array(),
                        ),
                    )
                ),
                get_the_title()
            ),
            '<span class="edit-link ml-4"><span class="inline-block mr-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></span>',
            '</span>'
        );
    }
endif;

if (!function_exists('wptbt_post_thumbnail')) :
    /**
     * Muestra la imagen destacada con diferentes formatos según el contexto.
     */
    function wptbt_post_thumbnail()
    {
        if (post_password_required() || is_attachment() || !has_post_thumbnail()) {
            return;
        }

        if (is_singular()) :
?>
            <div class="post-thumbnail mb-6">
                <?php the_post_thumbnail('large', array('class' => 'w-full h-auto rounded-lg shadow-md')); ?>
            </div><!-- .post-thumbnail -->
        <?php else : ?>
            <a class="post-thumbnail block" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                <?php
                the_post_thumbnail(
                    'medium',
                    array(
                        'class' => 'w-full h-48 object-cover transition duration-300 ease-in-out transform hover:scale-105',
                        'alt' => the_title_attribute(array('echo' => false)),
                    )
                );
                ?>
            </a>
        <?php
        endif;
    }
endif;

if (!function_exists('wptbt_get_site_logo')) :
    /**
     * Muestra el logo del sitio con clases TailwindCSS.
     */
    function wptbt_get_site_logo()
    {
        if (has_custom_logo()) {
            $custom_logo_id = get_theme_mod('custom_logo');
            $logo = wp_get_attachment_image_src($custom_logo_id, 'full');

            if ($logo) {
                echo '<a href="' . esc_url(home_url('/')) . '" class="logo-link" rel="home">';
                echo '<img src="' . esc_url($logo[0]) . '" alt="' . get_bloginfo('name') . '" class="h-10 w-auto">';
                echo '</a>';
            }
        } else {
            echo '<a href="' . esc_url(home_url('/')) . '" class="text-2xl font-bold text-gray-900 hover:text-primary transition duration-200" rel="home">';
            echo get_bloginfo('name');
            echo '</a>';

            $description = get_bloginfo('description', 'display');
            if ($description || is_customize_preview()) {
                echo '<p class="site-description text-sm text-gray-600">' . $description . '</p>';
            }
        }
    }
endif;

if (!function_exists('wptbt_categories_badges')) :
    /**
     * Muestra las categorías como badges con TailwindCSS.
     */
    function wptbt_categories_badges()
    {
        if ('post' !== get_post_type()) {
            return;
        }

        $categories = get_the_category();
        if (empty($categories)) {
            return;
        }

        echo '<div class="category-badges mb-4">';
        foreach ($categories as $category) {
            echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="inline-block bg-gray-100 text-gray-800 text-xs font-semibold mr-2 mb-2 px-2.5 py-0.5 rounded hover:bg-primary hover:text-white transition duration-200">';
            echo esc_html($category->name);
            echo '</a>';
        }
        echo '</div>';
    }
endif;

if (!function_exists('wptbt_related_posts')) :
    /**
     * Muestra artículos relacionados por categoría.
     */
    function wptbt_related_posts()
    {
        if ('post' !== get_post_type()) {
            return;
        }

        $categories = get_the_category();
        if (empty($categories)) {
            return;
        }

        $category_ids = array();
        foreach ($categories as $category) {
            $category_ids[] = $category->term_id;
        }

        $args = array(
            'post_type' => 'post',
            'posts_per_page' => 3,
            'post__not_in' => array(get_the_ID()),
            'category__in' => $category_ids,
            'orderby' => 'rand'
        );

        $related_query = new WP_Query($args);

        if ($related_query->have_posts()) :
        ?>
            <div class="related-posts mt-12 pt-8 border-t border-gray-200">
                <h3 class="text-xl font-bold mb-6"><?php esc_html_e('Artículos relacionados', 'wp-tailwind-blocks'); ?></h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <?php
                    while ($related_query->have_posts()) : $related_query->the_post();
                    ?>
                        <div class="related-post bg-white rounded-lg shadow-sm overflow-hidden transition-shadow duration-300 hover:shadow-md">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium', array('class' => 'w-full h-40 object-cover')); ?>
                                </a>
                            <?php endif; ?>
                            <div class="p-4">
                                <?php the_title('<h4 class="text-lg font-semibold mb-2"><a href="' . esc_url(get_permalink()) . '" class="text-gray-900 hover:text-primary" rel="bookmark">', '</a></h4>'); ?>
                                <div class="text-sm text-gray-600">
                                    <?php echo esc_html(get_the_date()); ?>
                                </div>
                            </div>
                        </div>
                    <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
<?php
        endif;
    }
endif;

if (!function_exists('wptbt_social_sharing')) :
    /**
     * Muestra botones para compartir en redes sociales.
     */
    function wptbt_social_sharing()
    {
        if (!is_singular('post')) {
            return;
        }

        $post_url = urlencode(get_permalink());
        $post_title = urlencode(get_the_title());

        $facebook_url = 'https://www.facebook.com/sharer/sharer.php?u=' . $post_url;
        $twitter_url = 'https://twitter.com/intent/tweet?url=' . $post_url . '&text=' . $post_title;
        $linkedin_url = 'https://www.linkedin.com/shareArticle?mini=true&url=' . $post_url . '&title=' . $post_title;
        $pinterest_url = 'https://pinterest.com/pin/create/button/?url=' . $post_url . '&description=' . $post_title;

        echo '<div class="social-sharing mt-6 pt-6 border-t border-gray-100">';
        echo '<span class="text-sm font-medium mr-4">' . esc_html__('Compartir:', 'wp-tailwind-blocks') . '</span>';

        echo '<a href="' . esc_url($facebook_url) . '" target="_blank" rel="noopener noreferrer" class="inline-block p-2 bg-blue-600 text-white rounded-full mr-2 hover:bg-blue-700 transition duration-200" aria-label="' . esc_attr__('Compartir en Facebook', 'wp-tailwind-blocks') . '">';
        echo '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" /></svg>';
        echo '</a>';

        echo '<a href="' . esc_url($twitter_url) . '" target="_blank" rel="noopener noreferrer" class="inline-block p-2 bg-blue-400 text-white rounded-full mr-2 hover:bg-blue-500 transition duration-200" aria-label="' . esc_attr__('Compartir en Twitter', 'wp-tailwind-blocks') . '">';
        echo '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" /></svg>';
        echo '</a>';

        echo '<a href="' . esc_url($linkedin_url) . '" target="_blank" rel="noopener noreferrer" class="inline-block p-2 bg-blue-700 text-white rounded-full mr-2 hover:bg-blue-800 transition duration-200" aria-label="' . esc_attr__('Compartir en LinkedIn', 'wp-tailwind-blocks') . '">';
        echo '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" /></svg>';
        echo '</a>';

        echo '<a href="' . esc_url($pinterest_url) . '" target="_blank" rel="noopener noreferrer" class="inline-block p-2 bg-red-600 text-white rounded-full mr-2 hover:bg-red-700 transition duration-200" aria-label="' . esc_attr__('Compartir en Pinterest', 'wp-tailwind-blocks') . '">';
        echo '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 0a12 12 0 00-4.373 23.182c-.017-.319-.031-.878.068-1.257.091-.345.601-2.202.601-2.202s-.152-.305-.152-.756c0-.709.411-1.238.926-1.238.436 0 .646.327.646.72 0 .437-.276 1.092-.421 1.7-.12.508.254.921.752.921.902 0 1.596-.958 1.596-2.344 0-1.223-.871-2.191-2.109-2.191-1.439 0-2.281 1.088-2.281 2.211 0 .438.138.903.358 1.157.033.045.042.083.033.121l-.141.583c-.021.09-.071.109-.164.066-.615-.292-.997-1.199-.997-1.925 0-1.53 1.098-2.957 3.148-2.957 1.656 0 2.946 1.25 2.946 2.915 0 1.748-1.09 3.129-2.6 3.129-.522 0-.941-.269-1.099-.63l-.299 1.136c-.109.42-.399.943-.594 1.263.447.132.932.205 1.429.205 2.762 0 5.222-1.283 6.826-3.284C20.719 15.221 22 12.754 22 10c0-5.514-4.486-10-10-10z" /></svg>';
        echo '</a>';

        echo '<a href="mailto:?subject=' . $post_title . '&body=' . $post_url . '" class="inline-block p-2 bg-gray-600 text-white rounded-full hover:bg-gray-700 transition duration-200" aria-label="' . esc_attr__('Compartir por email', 'wp-tailwind-blocks') . '">';
        echo '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>';
        echo '</a>';

        echo '</div>';
    }
endif;

if (!function_exists('wptbt_reading_time')) :
    /**
     * Calcula y muestra el tiempo estimado de lectura.
     */
    function wptbt_reading_time()
    {
        $content = get_post_field('post_content', get_the_ID());
        $word_count = str_word_count(strip_tags($content));
        $reading_time = ceil($word_count / 200); // Asumiendo 200 palabras por minuto

        echo '<span class="reading-time text-sm text-gray-600 mr-4">';
        echo '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';

        printf(
            _n(
                '%d minuto de lectura',
                '%d minutos de lectura',
                $reading_time,
                'wp-tailwind-blocks'
            ),
            $reading_time
        );

        echo '</span>';
    }
endif;
