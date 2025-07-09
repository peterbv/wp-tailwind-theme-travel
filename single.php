<?php

/**
 * The template for displaying single posts with enhanced features
 * 
 * @package WP_Tailwind_Theme
 */

get_header();

// Mostrar el banner personalizado si está habilitado
if (function_exists('wptbt_display_banner')) {
    wptbt_display_banner();
}
?>

<main id="primary" class="site-main py-12">
    <div class="container mx-auto px-4">
        <!-- Breadcrumbs -->
        <?php if (function_exists('wptbt_breadcrumbs')) : ?>
            <div class="breadcrumbs mb-8 text-sm text-gray-600">
                <?php wptbt_breadcrumbs(); ?>
            </div>
        <?php endif; ?>

        <div class="flex flex-wrap -mx-4">
            <div class="w-full lg:w-2/3 px-4">
                <?php
                while (have_posts()) :
                    the_post();
                ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('bg-white rounded-lg shadow-sm p-6 border border-gray-100 mb-8'); ?>>
                        <!-- Featured Image - Con efecto de zoom al hacer hover -->
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail mb-6 rounded-lg overflow-hidden">
                                <?php the_post_thumbnail('large', array('class' => 'w-full h-auto hover:scale-105 transition-transform duration-500')); ?>
                            </div>
                        <?php endif; ?>

                        <header class="entry-header mb-6">
                            <!-- Categorías -->
                            <div class="post-categories mb-3">
                                <?php
                                $categories = get_the_category();
                                if (!empty($categories)) :
                                    echo '<div class="flex flex-wrap gap-2">';
                                    foreach ($categories as $category) :
                                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="inline-block px-3 py-1 bg-spa-secondary text-spa-primary text-xs font-medium rounded-full hover:bg-spa-primary hover:text-white transition duration-300">' . esc_html($category->name) . '</a>';
                                    endforeach;
                                    echo '</div>';
                                endif;
                                ?>
                            </div>

                            <!-- Título -->
                            <h1 class="entry-title text-3xl md:text-4xl fancy-text font-bold mb-4 text-spa-primary">
                                <?php the_title(); ?>
                            </h1>

                            <!-- Metadatos del post -->
                            <div class="entry-meta flex flex-wrap items-center gap-4 text-sm text-gray-600 border-b border-gray-100 pb-4">
                                <!-- Autor con avatar -->
                                <div class="post-author flex items-center">
                                    <div class="author-avatar w-8 h-8 rounded-full overflow-hidden mr-2 border border-spa-secondary">
                                        <?php echo get_avatar(get_the_author_meta('ID'), 96); ?>
                                    </div>
                                    <span>
                                        <?php esc_html_e('Por ', 'wp-tailwind-theme'); ?>
                                        <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" class="font-medium hover:text-spa-accent transition-colors">
                                            <?php echo esc_html(get_the_author()); ?>
                                        </a>
                                    </span>
                                </div>

                                <!-- Fecha -->
                                <div class="post-date flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                        <?php echo esc_html(get_the_date()); ?>
                                    </time>
                                </div>

                                <!-- Tiempo de lectura estimado (función personalizada) -->
                                <?php if (function_exists('wptbt_reading_time')) : ?>
                                    <div class="post-reading-time flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <?php wptbt_reading_time(); ?>
                                    </div>
                                <?php else : ?>
                                    <div class="post-reading-time flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <?php
                                        // Estimación simple del tiempo de lectura
                                        $content = get_post_field('post_content', get_the_ID());
                                        $word_count = str_word_count(strip_tags($content));
                                        $reading_time = ceil($word_count / 200); // 200 palabras por minuto
                                        printf(esc_html(_n('%d min de lectura', '%d mins de lectura', $reading_time, 'wp-tailwind-theme')), $reading_time);
                                        ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Comentarios -->
                                <?php if (comments_open()) : ?>
                                    <div class="post-comments flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                        <a href="#comments" class="hover:text-spa-accent transition-colors">
                                            <?php
                                            $comments_number = get_comments_number();
                                            if ($comments_number == 0) {
                                                esc_html_e('Sin comentarios', 'wp-tailwind-theme');
                                            } elseif ($comments_number == 1) {
                                                esc_html_e('1 comentario', 'wp-tailwind-theme');
                                            } else {
                                                printf(esc_html(_n('%d comentario', '%d comentarios', $comments_number, 'wp-tailwind-theme')), $comments_number);
                                            }
                                            ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </header>

                        <!-- Tabla de contenidos para posts largos (opcional) -->
                        <?php
                        // Si el contenido tiene más de 1000 palabras y tiene encabezados H2, mostrar TOC
                        $content = get_post_field('post_content', get_the_ID());
                        $word_count = str_word_count(strip_tags($content));
                        $has_headings = (strpos($content, '<h2') !== false || strpos($content, '<h3') !== false);

                        if ($word_count > 1000 && $has_headings) :
                            // Solo mostrarlo si existe la función
                            if (function_exists('wptbt_generate_toc')) :
                        ?>
                                <div class="table-of-contents mb-8 bg-spa-secondary/20 p-5 rounded-lg">
                                    <h3 class="fancy-text font-bold text-lg mb-3 text-spa-primary flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                        </svg>
                                        <?php esc_html_e('Índice de contenidos', 'wp-tailwind-theme'); ?>
                                    </h3>
                                    <?php wptbt_generate_toc(); ?>
                                </div>
                        <?php
                            endif;
                        endif;
                        ?>

                        <!-- Contenido del post con estilos mejorados -->
                        <div class="entry-content prose max-w-none prose-headings:fancy-text prose-headings:text-spa-primary prose-a:text-spa-accent hover:prose-a:text-spa-primary prose-a:no-underline prose-img:rounded-lg">
                            <?php the_content(); ?>
                        </div>

                        <footer class="entry-footer mt-8 pt-6 border-t border-gray-100">
                            <!-- Etiquetas -->
                            <?php the_tags('<div class="post-tags mb-6"><span class="font-medium text-spa-primary mr-3">' . esc_html__('Etiquetas:', 'wp-tailwind-theme') . '</span>', '', '</div>', 'wp-tailwind-theme'); ?>

                            <!-- Botones de compartir -->
                            <div class="share-buttons flex flex-wrap items-center gap-2 mb-6">
                                <span class="font-medium text-spa-primary mr-2"><?php esc_html_e('Compartir:', 'wp-tailwind-theme'); ?></span>

                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_url(get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="share-button facebook inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#3b5998] text-white hover:opacity-90 transition-opacity">
                                    <span class="sr-only"><?php esc_html_e('Compartir en Facebook', 'wp-tailwind-theme'); ?></span>
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                                    </svg>
                                </a>

                                <a href="https://twitter.com/intent/tweet?text=<?php echo esc_attr(get_the_title()); ?>&url=<?php echo esc_url(get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="share-button twitter inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#1da1f2] text-white hover:opacity-90 transition-opacity">
                                    <span class="sr-only"><?php esc_html_e('Compartir en Twitter', 'wp-tailwind-theme'); ?></span>
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                                    </svg>
                                </a>

                                <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo esc_url(get_permalink()); ?>&title=<?php echo esc_attr(get_the_title()); ?>" target="_blank" rel="noopener noreferrer" class="share-button linkedin inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#0077b5] text-white hover:opacity-90 transition-opacity">
                                    <span class="sr-only"><?php esc_html_e('Compartir en LinkedIn', 'wp-tailwind-theme'); ?></span>
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                                    </svg>
                                </a>

                                <a href="mailto:?subject=<?php echo esc_attr(get_the_title()); ?>&body=<?php echo esc_url(get_permalink()); ?>" class="share-button email inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-600 text-white hover:opacity-90 transition-opacity">
                                    <span class="sr-only"><?php esc_html_e('Compartir por Email', 'wp-tailwind-theme'); ?></span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </a>

                                <a href="whatsapp://send?text=<?php echo esc_attr(get_the_title()) . ' ' . esc_url(get_permalink()); ?>" data-action="share/whatsapp/share" class="share-button whatsapp inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#25D366] text-white hover:opacity-90 transition-opacity">
                                    <span class="sr-only"><?php esc_html_e('Compartir en WhatsApp', 'wp-tailwind-theme'); ?></span>
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                    </svg>
                                </a>
                            </div>

                            <!-- Información del autor -->
                            <div class="author-bio mt-8 p-6 bg-spa-secondary/20 rounded-lg flex flex-col md:flex-row items-center md:items-start gap-6">
                                <div class="author-avatar w-24 h-24 rounded-full overflow-hidden flex-shrink-0 border-4 border-white shadow-md">
                                    <?php echo get_avatar(get_the_author_meta('ID'), 200, '', '', array('class' => 'w-full h-full object-cover')); ?>
                                </div>
                                <div class="author-info text-center md:text-left">
                                    <h3 class="text-xl font-bold text-spa-primary mb-2">
                                        <?php echo get_the_author_meta('display_name'); ?>
                                    </h3>
                                    <?php if ($author_bio = get_the_author_meta('description')) : ?>
                                        <p class="text-gray-700 mb-4"><?php echo esc_html($author_bio); ?></p>
                                    <?php endif; ?>
                                    <div class="author-links flex flex-wrap gap-2 justify-center md:justify-start">
                                        <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" class="inline-flex items-center justify-center px-4 py-2 bg-spa-accent text-white text-sm font-medium rounded-md hover:bg-opacity-90 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                            </svg>
                                            <?php esc_html_e('Ver todos sus artículos', 'wp-tailwind-theme'); ?>
                                        </a>
                                        <?php
                                        // Enlaces sociales del autor (si se han configurado)
                                        $social_profiles = array(
                                            'twitter' => get_the_author_meta('twitter'),
                                            'facebook' => get_the_author_meta('facebook'),
                                            'instagram' => get_the_author_meta('instagram'),
                                            'linkedin' => get_the_author_meta('linkedin'),
                                            'website' => get_the_author_meta('url')
                                        );

                                        foreach ($social_profiles as $platform => $url) :
                                            if (!empty($url)) :
                                                $icon_class = $platform === 'website' ? 'globe' : $platform;
                                        ?>
                                                <a href="<?php echo esc_url($url); ?>" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-200 text-gray-700 hover:bg-spa-primary hover:text-white transition-colors" target="_blank" rel="noopener noreferrer">
                                                    <span class="sr-only"><?php echo esc_html(ucfirst($platform)); ?></span>
                                                    <i class="fa fa-<?php echo esc_attr($icon_class); ?>"></i>
                                                </a>
                                        <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </footer>
                    </article>

                    <!-- Posts relacionados -->
                    <?php
                    // Obtener posts relacionados por categoría
                    $categories = get_the_category();
                    $category_ids = wp_list_pluck($categories, 'term_id');

                    $related_args = array(
                        'post_type' => 'post',
                        'posts_per_page' => 3,
                        'post__not_in' => array(get_the_ID()),
                        'category__in' => $category_ids,
                        'orderby' => 'rand'
                    );

                    $related_query = new WP_Query($related_args);

                    if ($related_query->have_posts()) :
                    ?>
                        <div class="related-posts bg-white rounded-lg shadow-sm p-6 border border-gray-100 mb-8">
                            <h3 class="text-xl fancy-text font-bold mb-6 text-spa-primary">
                                <?php esc_html_e('Artículos relacionados', 'wp-tailwind-theme'); ?>
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <?php
                                while ($related_query->have_posts()) :
                                    $related_query->the_post();
                                ?>
                                    <div class="related-post group overflow-hidden flex flex-col h-full">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <a href="<?php the_permalink(); ?>" class="block rounded-lg overflow-hidden mb-3">
                                                <?php the_post_thumbnail('medium', array('class' => 'w-full h-auto group-hover:scale-105 transition-transform duration-500')); ?>
                                            </a>
                                        <?php endif; ?>
                                        <h4 class="text-md font-medium mb-2 group-hover:text-spa-accent transition-colors">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h4>
                                        <div class="text-gray-600 text-sm mb-3 flex-grow">
                                            <?php echo wp_trim_words(get_the_excerpt(), 12); ?>
                                        </div>
                                        <a href="<?php the_permalink(); ?>" class="text-spa-accent font-medium text-sm hover:text-spa-primary transition-colors">
                                            <?php esc_html_e('Leer más', 'wp-tailwind-theme'); ?> →
                                        </a>
                                    </div>
                                <?php
                                endwhile;
                                wp_reset_postdata();
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Navegación de posts (anterior/siguiente) -->
                    <div id="post-navigation" class="post-navigation mb-8 bg-white rounded-lg shadow-sm p-6 border border-gray-100">
                        <?php
                        the_post_navigation(
                            array(
                                'prev_text' => '<div class="text-sm text-[#8BAB8D] mb-1">' . esc_html__('Anterior', 'wp-tailwind-theme') . '</div><div class="text-lg font-medium text-spa-primary hover:text-spa-accent transition-colors">%title</div>',
                                'next_text' => '<div class="text-sm text-[#8BAB8D] mb-1">' . esc_html__('Siguiente', 'wp-tailwind-theme') . '</div><div class="text-lg font-medium text-spa-primary hover:text-spa-accent transition-colors">%title</div>',
                                'class' => 'flex flex-col md:flex-row justify-between gap-6',
                            )
                        );
                        ?>
                    </div>

                    <!-- Comentarios -->
                    <?php if (comments_open() || get_comments_number()) : ?>
                        <div id="comments" class="comments-section bg-white rounded-lg shadow-sm p-8 border border-gray-100">
                            <?php comments_template(); ?>
                        </div>
                    <?php endif; ?>

                <?php endwhile; ?>
            </div>

            <!-- Barra lateral -->
            <?php if (is_active_sidebar('sidebar-1')) : ?>
                <div class="w-full lg:w-1/3 px-4 mt-8 lg:mt-0">
                    <div class="sticky top-6">
                        <?php get_sidebar(); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
get_footer();
