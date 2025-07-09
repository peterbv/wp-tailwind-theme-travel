<?php

/**
 * La plantilla para mostrar la página 404 (no encontrada)
 * Actualizada para compatibilidad con Tailwind CSS 4
 *
 * @package WP_Tailwind_Spa_Theme
 */

get_header();
?>

<div class="error-404 py-20 md:py-28 bg-gradient-to-b from-white to-[#f7ede2] bg-opacity-20">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-16">
                <h1 class="text-7xl md:text-9xl fancy-text font-bold mb-6 text-spa-primary opacity-20">404</h1>

                <div class="relative -mt-16 mb-10">
                    <svg class="w-24 h-24 md:w-32 md:h-32 mx-auto text-spa-accent opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>

                <h2 class="text-2xl md:text-3xl fancy-text text-spa-primary mb-4">
                    <?php esc_html_e('Página no encontrada', 'wp-tailwind-theme'); ?>
                </h2>

                <p class="text-lg text-gray-600 mb-8 max-w-xl mx-auto">
                    <?php esc_html_e('Lo sentimos, la página que estás buscando no existe o ha sido trasladada. ¿Por qué no intentas buscar lo que necesitas o exploras nuestras secciones más populares?', 'wp-tailwind-theme'); ?>
                </p>

                <div class="flex flex-col md:flex-row gap-6 justify-center mb-12">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-sm bg-spa-primary text-white hover:bg-opacity-90 transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <?php esc_html_e('Volver al inicio', 'wp-tailwind-theme'); ?>
                    </a>

                    <button id="search-404-toggle" class="inline-flex items-center justify-center px-6 py-3 border border-spa-accent text-base font-medium rounded-sm text-spa-accent hover:bg-spa-accent hover:text-white transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <?php esc_html_e('Búsqueda avanzada', 'wp-tailwind-theme'); ?>
                    </button>
                </div>

                <!-- Formulario de búsqueda -->
                <div id="search-404-form" class="search-form-container max-w-xl mx-auto hidden">
                    <form role="search" method="get" class="search-form relative group" action="<?php echo esc_url(home_url('/')); ?>">
                        <label class="screen-reader-text" for="search-404">
                            <?php esc_html_e('Buscar:', 'wp-tailwind-theme'); ?>
                        </label>

                        <input type="search" id="search-404" class="search-field w-full p-4 pl-12 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-spa-accent focus:border-spa-accent transition-all duration-300" placeholder="<?php echo esc_attr_x('¿Qué estás buscando?', 'placeholder', 'wp-tailwind-theme'); ?>" value="<?php echo get_search_query(); ?>" name="s" />

                        <button type="submit" class="search-submit absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 group-hover:text-spa-accent" aria-label="<?php echo esc_attr_x('Buscar', 'submit button', 'wp-tailwind-theme'); ?>">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Enlaces populares -->
            <div class="bg-white rounded-lg shadow-md p-8 border border-gray-100">
                <h3 class="text-xl fancy-text font-medium text-spa-primary mb-6">
                    <?php esc_html_e('Quizás te interese explorar', 'wp-tailwind-theme'); ?>
                </h3>

                <div class="grid md:grid-cols-2 gap-4">
                    <?php
                    // Obtener las páginas más populares o servicios destacados
                    $popular_pages = get_posts(array(
                        'post_type' => 'page',
                        'posts_per_page' => 4,
                        'orderby' => 'menu_order',
                        'order' => 'ASC',
                        'meta_query' => array(
                            array(
                                'key' => '_wp_page_template',
                                'value' => 'default',
                                'compare' => '!='
                            )
                        )
                    ));

                    if (empty($popular_pages)) {
                        // Alternativa: mostrar entradas recientes si no hay páginas destacadas
                        $popular_pages = get_posts(array(
                            'posts_per_page' => 4
                        ));
                    }

                    foreach ($popular_pages as $page) :
                    ?>
                        <a href="<?php echo esc_url(get_permalink($page->ID)); ?>" class="p-4 rounded hover:bg-[#f7ede2] hover:bg-opacity-20 transition-all duration-300 flex items-start gap-3">
                            <div class="bg-spa-primary bg-opacity-10 rounded-full p-2 mt-1">
                                <svg class="w-5 h-5 text-spa-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800 hover:text-spa-accent transition-colors duration-300">
                                    <?php echo esc_html(get_the_title($page->ID)); ?>
                                </h4>
                                <?php if (has_excerpt($page->ID)) : ?>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <?php echo wp_trim_words(get_the_excerpt($page->ID), 10, '...'); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchToggle = document.getElementById('search-404-toggle');
        const searchForm = document.getElementById('search-404-form');

        if (searchToggle && searchForm) {
            searchToggle.addEventListener('click', function() {
                searchForm.classList.toggle('hidden');

                if (!searchForm.classList.contains('hidden')) {
                    // Focus en el campo de búsqueda cuando se muestra
                    document.getElementById('search-404').focus();
                }
            });
        }
    });
</script>

<?php
get_footer();
