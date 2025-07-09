<?php

/**
 * Funciones que modifican características de WordPress
 *
 * Este archivo contiene funciones que añaden, modifican o extienden
 * la funcionalidad básica de WordPress y afectan a la estructura general del tema.
 *
 * @package WP_Tailwind_Blocks
 */

/**
 * Añade clases adicionales al elemento <body>
 *
 * @param array $classes Clases para el elemento body.
 * @return array
 */
function wptbt_body_classes($classes)
{
    // Añadir clase para cuando no hay barra lateral
    if (!is_active_sidebar('sidebar-1') || !get_theme_mod('show_sidebar', false)) {
        $classes[] = 'no-sidebar';
    } else {
        $classes[] = 'has-sidebar';
        $classes[] = 'sidebar-' . get_theme_mod('sidebar_position', 'right');
    }

    // Añadir clases para páginas
    if (is_page()) {
        // Verificar si la página tiene imagen destacada
        if (has_post_thumbnail()) {
            $classes[] = 'has-featured-image';
        }

        // Verificar si es una página de plantilla
        if (is_page_template()) {
            $template_slug = get_page_template_slug();
            $template_parts = explode('/', $template_slug);
            $template_name = str_replace('.php', '', end($template_parts));
            $classes[] = 'page-template-' . sanitize_html_class($template_name);
        }
    }

    // Añadir clase para el tipo de contenido
    if (is_singular()) {
        $classes[] = 'singular-' . get_post_type();
    } elseif (is_archive()) {
        $classes[] = 'archive-' . get_post_type();
    }

    // Añadir clase especial para multisitio
    if (is_multisite()) {
        $classes[] = 'multisite';

        if (is_main_site()) {
            $classes[] = 'main-site';
        } else {
            $classes[] = 'network-site';
        }
    }

    // Añadir clase para el layout de contenido
    $content_width = get_theme_mod('content_width', 'container');
    $classes[] = 'content-' . $content_width;

    // Añadir clase para el tipo de menú móvil
    $classes[] = 'mobile-menu-slide';

    return $classes;
}
add_filter('body_class', 'wptbt_body_classes');

/**
 * Añadir un pingback url al head para sitios que lo consultan.
 */
function wptbt_pingback_header()
{
    if (is_singular() && pings_open()) {
        printf('<link rel="pingback" href="%s">', esc_url(get_bloginfo('pingback_url')));
    }
}
add_action('wp_head', 'wptbt_pingback_header');

/**
 * Función para mostrar cada comentario en un formato adecuado.
 *
 * @param object $comment Comentario a mostrar.
 * @param array  $args    Argumentos pasados al walker.
 * @param int    $depth   Nivel de anidación.
 */
function wptbt_comment($comment, $args, $depth)
{
?>
    <li id="comment-<?php comment_ID(); ?>" <?php comment_class('py-6 border-b border-gray-200'); ?>>
        <article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
            <footer class="comment-meta mb-2">
                <div class="comment-author vcard flex items-center">
                    <?php
                    if (0 != $args['avatar_size']) {
                        echo get_avatar($comment, $args['avatar_size'], '', '', array('class' => 'mr-3 rounded-full'));
                    }
                    ?>
                    <div>
                        <?php
                        printf(
                            '<b class="fn">%s</b>',
                            get_comment_author_link()
                        );
                        ?>
                        <div class="comment-metadata text-xs text-gray-500">
                            <a href="<?php echo esc_url(get_comment_link($comment->comment_ID)); ?>" class="hover:text-primary">
                                <time datetime="<?php comment_time('c'); ?>">
                                    <?php
                                    printf(
                                        /* translators: 1: fecha del comentario, 2: hora del comentario */
                                        esc_html__('%1$s a las %2$s', 'wp-tailwind-blocks'),
                                        get_comment_date(),
                                        get_comment_time()
                                    );
                                    ?>
                                </time>
                            </a>
                            <?php edit_comment_link(esc_html__('Editar', 'wp-tailwind-blocks'), '<span class="edit-link ml-2">', '</span>'); ?>
                        </div><!-- .comment-metadata -->
                    </div>
                </div><!-- .comment-author -->

                <?php if ('0' == $comment->comment_approved) : ?>
                    <p class="comment-awaiting-moderation text-yellow-600 text-sm mt-2"><?php esc_html_e('Tu comentario está en moderación.', 'wp-tailwind-blocks'); ?></p>
                <?php endif; ?>
            </footer><!-- .comment-meta -->

            <div class="comment-content prose max-w-none">
                <?php comment_text(); ?>
            </div><!-- .comment-content -->

            <div class="reply mt-2">
                <?php
                comment_reply_link(
                    array_merge(
                        $args,
                        array(
                            'depth'     => $depth,
                            'max_depth' => $args['max_depth'],
                            'before'    => '<div class="text-sm">',
                            'after'     => '</div>',
                        )
                    )
                );
                ?>
            </div>
        </article><!-- .comment-body -->
    <?php
}

/**
 * Modificar la longitud del extracto
 *
 * @param int $length Longitud actual del extracto.
 * @return int Nueva longitud del extracto.
 */
function wptbt_excerpt_length($length)
{
    return 30;
}
add_filter('excerpt_length', 'wptbt_excerpt_length');

/**
 * Modificar el texto "Read more" del extracto
 *
 * @param string $more Texto actual de "leer más".
 * @return string Nuevo texto de "leer más".
 */
function wptbt_excerpt_more($more)
{
    return '...';
}
add_filter('excerpt_more', 'wptbt_excerpt_more');

/**
 * Añadir soporte para excerpt en páginas
 */
function wptbt_add_excerpts_to_pages()
{
    add_post_type_support('page', 'excerpt');
}
add_action('init', 'wptbt_add_excerpts_to_pages');

/**
 * Añade la etiqueta de schema.org para mejorar el SEO
 */
function wptbt_schema_org()
{
    // Valores por defecto
    $type = 'WebPage';
    $schema = 'https://schema.org/';

    // Verificar el tipo de página
    if (is_home() || is_archive() || is_attachment() || is_tax() || is_single()) {
        $type = 'Blog';
    } elseif (is_author()) {
        $type = 'ProfilePage';
    } elseif (is_search()) {
        $type = 'SearchResultsPage';
    }

    // Si es un post individual, utilizar Article
    if (is_singular('post')) {
        $type = 'Article';
    }

    echo 'itemscope itemtype="' . esc_attr($schema) . esc_attr($type) . '"';
}

/**
 * Soporte para el paginador en sitios multisitio
 * 
 * @param array $args Argumentos del paginador.
 * @return array
 */
function wptbt_multisite_paginate_links($args = array())
{
    global $wp_rewrite;

    $pagenum_link = html_entity_decode(get_pagenum_link());

    // Si estamos en un entorno multisitio, asegurarnos de que los enlaces incluyan el blog_id correcto
    if (is_multisite() && !is_main_site()) {
        $pagenum_link = add_query_arg('blog_id', get_current_blog_id(), $pagenum_link);
    }

    $url_parts = explode('?', $pagenum_link);

    // Eliminar variables de consulta no deseadas
    if (isset($url_parts[1])) {
        wp_parse_str($url_parts[1], $query_args);

        // Mantener las variables de consulta necesarias
        $allowed_args = array('blog_id', 's', 'post_type', 'category', 'tag');
        foreach ($query_args as $key => $value) {
            if (!in_array($key, $allowed_args)) {
                unset($query_args[$key]);
            }
        }

        $pagenum_link = $url_parts[0];
        if (!empty($query_args)) {
            $pagenum_link = add_query_arg($query_args, $pagenum_link);
        }
    }

    // Establecer valores por defecto
    $defaults = array(
        'base' => $pagenum_link,
        'format' => $wp_rewrite->using_permalinks() ? user_trailingslashit($wp_rewrite->pagination_base . '/%#%', 'paged') : '?paged=%#%',
        'total' => 1,
        'current' => max(1, get_query_var('paged')),
        'show_all' => false,
        'prev_next' => true,
        'prev_text' => __('&laquo; Anterior', 'wp-tailwind-blocks'),
        'next_text' => __('Siguiente &raquo;', 'wp-tailwind-blocks'),
        'end_size' => 1,
        'mid_size' => 2,
        'type' => 'plain',
        'add_args' => array(),
        'add_fragment' => '',
        'before_page_number' => '',
        'after_page_number' => '',
    );

    // Combinar argumentos personalizados con los valores por defecto
    $args = wp_parse_args($args, $defaults);

    // Generar el paginador con los argumentos modificados
    return paginate_links($args);
}

/**
 * Modificar el título del archivo en multisitio
 *
 * @param string $title Título actual.
 * @return string Título modificado.
 */
function wptbt_multisite_archive_title($title)
{
    if (is_multisite() && !is_main_site()) {
        $blog_details = get_blog_details(get_current_blog_id());

        if (is_category()) {
            $title = sprintf(__('Categoría: %s - %s', 'wp-tailwind-blocks'), single_cat_title('', false), $blog_details->blogname);
        } elseif (is_tag()) {
            $title = sprintf(__('Etiqueta: %s - %s', 'wp-tailwind-blocks'), single_tag_title('', false), $blog_details->blogname);
        } elseif (is_author()) {
            $title = sprintf(__('Autor: %s - %s', 'wp-tailwind-blocks'), get_the_author(), $blog_details->blogname);
        } elseif (is_post_type_archive()) {
            $title = sprintf(__('Archivo: %s - %s', 'wp-tailwind-blocks'), post_type_archive_title('', false), $blog_details->blogname);
        } elseif (is_tax()) {
            $tax = get_taxonomy(get_queried_object()->taxonomy);
            $title = sprintf(__('%s: %s - %s', 'wp-tailwind-blocks'), $tax->labels->singular_name, single_term_title('', false), $blog_details->blogname);
        }
    }

    return $title;
}
add_filter('get_the_archive_title', 'wptbt_multisite_archive_title');

/**
 * Añadir clases a los elementos de navegación para TailwindCSS
 */
function wptbt_nav_menu_css_class($classes, $item, $args, $depth)
{
    if (isset($args->theme_location) && $args->theme_location == 'primary') {
        $classes[] = 'relative';

        // Añadir clases específicas basadas en el nivel
        if ($depth === 0) {
            $classes[] = 'group';
        }
    }

    return $classes;
}
add_filter('nav_menu_css_class', 'wptbt_nav_menu_css_class', 10, 4);

/**
 * Añadir clases a los enlaces de navegación para TailwindCSS
 */
function wptbt_nav_menu_link_attributes($atts, $item, $args, $depth)
{
    if (isset($args->theme_location)) {
        if ($args->theme_location == 'primary') {
            // Clases para menú principal
            if ($depth === 0) {
                $atts['class'] = 'block py-2 lg:py-1 px-4 lg:px-2 text-gray-700 hover:text-primary transition duration-200';
            } else {
                $atts['class'] = 'block py-2 px-4 text-gray-700 hover:text-primary hover:bg-gray-50 transition duration-200';
            }
        } elseif ($args->theme_location == 'footer') {
            // Clases para menú de pie de página
            $atts['class'] = 'text-gray-300 hover:text-white transition duration-200';
        }
    }

    return $atts;
}
add_filter('nav_menu_link_attributes', 'wptbt_nav_menu_link_attributes', 10, 4);

/**
 * Filtrar la clase del enlace actual en multisitio
 */
function wptbt_multisite_page_menu_link_class($class)
{
    if (is_multisite()) {
        if (in_array('current_page_item', $class)) {
            $class[] = 'text-primary';
        }
    }

    return $class;
}
add_filter('page_css_class', 'wptbt_multisite_page_menu_link_class', 10, 1);

/**
 * Añadir soporte para bloques anidados en Gutenberg
 */
function wptbt_allowed_block_types($allowed_blocks, $post)
{
    // Permitir todos los bloques por defecto
    return $allowed_blocks;
}
add_filter('allowed_block_types_all', 'wptbt_allowed_block_types', 10, 2);

/**
 * Optimizar registros CSS/JS por tipo de página
 */
function wptbt_optimize_assets()
{
    // Si no estamos en el admin y no es una solicitud AJAX
    if (!is_admin() && !wp_doing_ajax()) {
        // Desregistrar estilos y scripts que no son necesarios
        if (!is_singular('post')) {
            // Por ejemplo, desregistrar estilos específicos de comentarios si no estamos en un post individual
            wp_deregister_style('comment-reply');
        }

        if (!is_page_template('templates/contact.php')) {
            // Desregistrar scripts específicos del formulario de contacto si no estamos en la página de contacto
            // wp_deregister_script('contact-form-script');
        }
    }
}
add_action('wp_enqueue_scripts', 'wptbt_optimize_assets', 100);

/**
 * Mejorar el rendimiento para multisitio
 */
function wptbt_multisite_performance()
{
    if (is_multisite()) {
        // Caché para las consultas de red
        add_action('switch_blog', function () {
            wp_cache_flush();
        });
    }
}
add_action('init', 'wptbt_multisite_performance');

/**
 * Función para mostrar el mensaje de contenido no disponible
 */
function wptbt_no_content_message()
{
    ?>
        <div class="no-content-message bg-gray-50 p-12 text-center rounded-lg shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <h2 class="text-2xl font-bold text-gray-700 mb-2"><?php esc_html_e('No se encontró contenido', 'wp-tailwind-blocks'); ?></h2>
            <p class="text-gray-600 mb-6"><?php esc_html_e('No se encontró ningún contenido que coincida con tus criterios.', 'wp-tailwind-blocks'); ?></p>

            <?php if (is_search()) : ?>
                <div class="mt-6">
                    <p class="text-gray-600 mb-4"><?php esc_html_e('Prueba con diferentes palabras clave o navega por las categorías:', 'wp-tailwind-blocks'); ?></p>
                    <?php get_search_form(); ?>
                </div>
            <?php else : ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-block bg-primary text-white px-6 py-2 rounded-md hover:bg-blue-700 transition duration-200">
                    <?php esc_html_e('Volver al inicio', 'wp-tailwind-blocks'); ?>
                </a>
            <?php endif; ?>
        </div>
    <?php
}
