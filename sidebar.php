<?php

/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WP_Tailwind_Theme
 */

if (!is_active_sidebar('sidebar-1')) {
    return;
}
?>

<aside id="secondary" class="widget-area">
    <?php
    // Mostrar formulario de búsqueda directamente en la barra lateral (opcional)
    if (get_theme_mod('show_sidebar_search', true)) :
    ?>
        <div class="widget widget_search mb-8 bg-white rounded-lg shadow-sm p-6 border border-gray-100">
            <h2 class="widget-title text-xl fancy-text font-bold mb-4 text-spa-primary flex items-center">
                <svg class="w-5 h-5 mr-2 text-spa-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <?php esc_html_e('Buscar', 'wp-tailwind-theme'); ?>
            </h2>
            <form role="search" method="get" class="search-form relative" action="<?php echo esc_url(home_url('/')); ?>">
                <label>
                    <span class="screen-reader-text"><?php esc_html_e('Buscar:', 'wp-tailwind-theme'); ?></span>
                    <input type="search" class="search-field w-full p-3 pr-10 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-spa-accent focus:border-spa-accent" placeholder="<?php echo esc_attr_x('Buscar...', 'placeholder', 'wp-tailwind-theme'); ?>" value="<?php echo get_search_query(); ?>" name="s" />
                </label>
                <button type="submit" class="search-submit absolute right-3 top-1/2 transform -translate-y-1/2 text-spa-primary hover:text-spa-accent transition-colors" aria-label="<?php echo esc_attr_x('Buscar', 'submit button', 'wp-tailwind-theme'); ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </form>
        </div>
    <?php endif; ?>

    <?php
    // Widget de posts destacados/populares (opcional - solo si existe el custom field)
    if (get_theme_mod('show_popular_posts', true)) :
        $args = array(
            'post_type'      => 'post',
            'posts_per_page' => get_theme_mod('popular_posts_count', 4),
            'meta_key'       => 'post_views_count', // Necesitarás un plugin o función para contar vistas
            'orderby'        => 'meta_value_num',
            'order'          => 'DESC',
        );

        // Fallback a posts recientes si no existe el campo personalizado
        global $wpdb;
        $meta_key_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->postmeta WHERE meta_key = %s", 'post_views_count'));

        if (!$meta_key_exists) {
            $args = array(
                'post_type'      => 'post',
                'posts_per_page' => get_theme_mod('popular_posts_count', 4),
                'orderby'        => 'comment_count', // Usar comentarios como indicador de popularidad
                'order'          => 'DESC',
            );
        }

        $popular_posts = new WP_Query($args);

        if ($popular_posts->have_posts()) :
    ?>
            <div class="widget widget_popular_posts mb-8 bg-white rounded-lg shadow-sm p-6 border border-gray-100">
                <h2 class="widget-title text-xl fancy-text font-bold mb-4 text-spa-primary flex items-center">
                    <svg class="w-5 h-5 mr-2 text-spa-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    <?php esc_html_e('Artículos populares', 'wp-tailwind-theme'); ?>
                </h2>
                <ul class="space-y-4">
                    <?php
                    while ($popular_posts->have_posts()) :
                        $popular_posts->the_post();
                    ?>
                        <li class="popular-post flex group">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>" class="flex-shrink-0 mr-3 overflow-hidden rounded" style="width: 80px; height: 60px;">
                                    <?php the_post_thumbnail('thumbnail', array('class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-300')); ?>
                                </a>
                            <?php endif; ?>
                            <div class="flex flex-col">
                                <h3 class="text-sm font-medium leading-snug group-hover:text-spa-accent transition-colors">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <span class="text-xs text-gray-500 mt-1 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <?php echo get_the_date(); ?>
                                </span>
                            </div>
                        </li>
                    <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </ul>
            </div>
    <?php
        endif;
    endif;
    ?>

    <?php
    // Widget de categorías con contador
    if (get_theme_mod('show_categories_widget', true)) :
        $categories = get_categories(array(
            'orderby' => 'count',
            'order'   => 'DESC',
            'number'  => 10,
        ));

        if (!empty($categories)) :
    ?>
            <div class="widget widget_categories mb-8 bg-white rounded-lg shadow-sm p-6 border border-gray-100">
                <h2 class="widget-title text-xl fancy-text font-bold mb-4 text-spa-primary flex items-center">
                    <svg class="w-5 h-5 mr-2 text-spa-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <?php esc_html_e('Categorías', 'wp-tailwind-theme'); ?>
                </h2>
                <ul class="space-y-2">
                    <?php
                    foreach ($categories as $category) :
                    ?>
                        <li class="category-item flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                            <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="text-gray-700 hover:text-spa-accent transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-2 text-spa-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                <?php echo esc_html($category->name); ?>
                            </a>
                            <span class="bg-spa-secondary text-spa-primary text-xs font-medium px-2 py-1 rounded-full">
                                <?php echo number_format_i18n($category->count); ?>
                            </span>
                        </li>
                    <?php
                    endforeach;
                    ?>
                </ul>
            </div>
    <?php
        endif;
    endif;
    ?>

    <?php
    // Widget de etiquetas (tag cloud)
    if (get_theme_mod('show_tags_widget', true)) :
        $tags = get_tags(array(
            'orderby' => 'count',
            'order'   => 'DESC',
            'number'  => 20,
        ));

        if (!empty($tags)) :
    ?>
            <div class="widget widget_tag_cloud mb-8 bg-white rounded-lg shadow-sm p-6 border border-gray-100">
                <h2 class="widget-title text-xl fancy-text font-bold mb-4 text-spa-primary flex items-center">
                    <svg class="w-5 h-5 mr-2 text-spa-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <?php esc_html_e('Etiquetas', 'wp-tailwind-theme'); ?>
                </h2>
                <div class="tagcloud flex flex-wrap gap-2">
                    <?php
                    foreach ($tags as $tag) :
                        // Tamaño de fuente relativo basado en la cantidad de posts
                        $tag_count_percentage = ($tag->count / max(array_column($tags, 'count'))) * 100;
                        $font_size = 11 + ($tag_count_percentage / 10);
                        $opacity = 0.7 + ($tag_count_percentage / 100);
                    ?>
                        <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="inline-block px-3 py-1 text-sm bg-spa-secondary text-spa-primary rounded-full hover:bg-spa-primary hover:text-white transition-colors duration-300" style="font-size: <?php echo esc_attr($font_size); ?>px; opacity: <?php echo esc_attr($opacity); ?>;">
                            <?php echo esc_html($tag->name); ?>
                        </a>
                    <?php
                    endforeach;
                    ?>
                </div>
            </div>
    <?php
        endif;
    endif;
    ?>

    <?php
    // Widget de promoción personalizada (opcional)
    if (get_theme_mod('show_promo_widget', true)) :
        $promo_title = get_theme_mod('promo_widget_title', __('¿Necesitas nuestros servicios?', 'wp-tailwind-theme'));
        $promo_text = get_theme_mod('promo_widget_text', __('Reserva una cita y disfruta de nuestros tratamientos exclusivos de spa.', 'wp-tailwind-theme'));
        $promo_button_text = get_theme_mod('promo_widget_button_text', __('Reservar ahora', 'wp-tailwind-theme'));
        $promo_button_url = get_theme_mod('promo_widget_button_url', '#');
        $promo_bg_color = get_theme_mod('promo_widget_bg_color', '#4F8A8B'); // color-spa-primary
    ?>
        <div class="widget widget_promotion mb-8 rounded-lg shadow-sm overflow-hidden relative" style="background-color: <?php echo esc_attr($promo_bg_color); ?>;">
            <!-- Elementos decorativos para el fondo -->
            <div class="absolute top-0 right-0 w-32 h-32 rounded-full bg-white opacity-10 transform translate-x-1/4 -translate-y-1/4"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 rounded-full bg-white opacity-10 transform -translate-x-1/4 translate-y-1/4"></div>

            <div class="p-6 text-white relative z-10">
                <h2 class="widget-title text-xl fancy-text font-bold mb-3 text-white">
                    <?php echo esc_html($promo_title); ?>
                </h2>
                <p class="mb-4 text-white/90">
                    <?php echo esc_html($promo_text); ?>
                </p>
                <a href="<?php echo esc_url($promo_button_url); ?>" class="inline-block w-full px-6 py-3 bg-spa-accent text-white text-center font-medium rounded-sm hover:bg-white hover:text-spa-primary transition-colors duration-300">
                    <?php echo esc_html($promo_button_text); ?>
                </a>
            </div>
        </div>
    <?php endif; ?>

    <?php
    // Widgets adicionales
    dynamic_sidebar('sidebar-1');
    ?>
</aside><!-- #secondary -->