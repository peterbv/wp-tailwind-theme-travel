<?php

/**
 * La plantilla para mostrar los resultados de búsqueda
 * Actualizada para compatibilidad con Tailwind CSS 4
 *
 * @package WP_Tailwind_Spa_Theme
 */

get_header();
?>

<div class="search-results-page bg-white">
    <!-- Cabecera de búsqueda con fondo sutil -->
    <div class="py-12 md:py-20 bg-gradient-to-b from-[#f7ede2] from-opacity-30 to-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-3xl md:text-4xl fancy-text font-bold mb-6 text-spa-primary">
                    <?php
                    printf(
                        /* translators: %s: search query. */
                        esc_html__('Resultados de búsqueda para: %s', 'wp-tailwind-theme'),
                        '<span class="text-spa-accent">' . get_search_query() . '</span>'
                    );
                    ?>
                </h1>

                <!-- Formulario de búsqueda mejorado -->
                <div class="search-form-container max-w-xl mx-auto mb-4">
                    <form role="search" method="get" class="search-form relative group" action="<?php echo esc_url(home_url('/')); ?>">
                        <input type="search" id="search-main" class="search-field w-full p-4 pl-12 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-spa-accent focus:border-spa-accent transition-all duration-300 shadow-sm" placeholder="<?php echo esc_attr_x('Refinar tu búsqueda...', 'placeholder', 'wp-tailwind-theme'); ?>" value="<?php echo get_search_query(); ?>" name="s" />

                        <button type="submit" class="search-submit absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 group-hover:text-spa-accent" aria-label="<?php echo esc_attr_x('Buscar', 'submit button', 'wp-tailwind-theme'); ?>">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </form>
                </div>

                <!-- Estadísticas de búsqueda -->
                <div class="text-sm text-gray-500">
                    <?php
                    global $wp_query;
                    $total_results = $wp_query->found_posts;

                    if ($total_results > 0) {
                        printf(
                            /* translators: %d: number of results */
                            _n(
                                'Se ha encontrado %d resultado',
                                'Se han encontrado %d resultados',
                                $total_results,
                                'wp-tailwind-theme'
                            ),
                            $total_results
                        );
                    } else {
                        esc_html_e('No se han encontrado resultados', 'wp-tailwind-theme');
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido principal de resultados -->
    <div class="search-content pt-12 pb-30">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                <?php if (have_posts()) : ?>
                    <!-- Filtros de tipo de contenido (opcional) -->
                    <div class="flex flex-wrap gap-2 mb-8 justify-center">
                        <button class="filter-btn active px-4 py-2 rounded-full bg-spa-primary text-white text-sm font-medium transition-colors duration-300 hover:bg-opacity-90" data-filter="all">
                            <?php echo esc_html__('Todos', 'wp-tailwind-theme'); ?>
                        </button>

                        <button class="filter-btn px-4 py-2 rounded-full bg-gray-100 text-gray-700 text-sm font-medium transition-colors duration-300 hover:bg-gray-200" data-filter="post">
                            <?php echo esc_html__('Artículos', 'wp-tailwind-theme'); ?>
                        </button>

                        <button class="filter-btn px-4 py-2 rounded-full bg-gray-100 text-gray-700 text-sm font-medium transition-colors duration-300 hover:bg-gray-200" data-filter="page">
                            <?php echo esc_html__('Páginas', 'wp-tailwind-theme'); ?>
                        </button>

                        <?php if (post_type_exists('servicio')) : ?>
                            <button class="filter-btn px-4 py-2 rounded-full bg-gray-100 text-gray-700 text-sm font-medium transition-colors duration-300 hover:bg-gray-200" data-filter="servicio">
                                <?php echo esc_html__('Servicios', 'wp-tailwind-theme'); ?>
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- Lista de resultados -->
                    <div class="search-results-list flex flex-col space-y-6">
                        <?php
                        while (have_posts()) :
                            the_post();

                            // Obtener el tipo de post para filtros
                            $post_type = get_post_type();
                            $post_type_class = 'item-type-' . $post_type;

                            // Preparar la imagen destacada
                            $thumbnail = '';
                            if (has_post_thumbnail()) {
                                $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
                                $thumbnail = '<div class="search-item-thumbnail">
                                    <img src="' . esc_url($thumbnail_url) . '" alt="' . esc_attr(get_the_title()) . '" class="w-16 h-16 md:w-20 md:h-20 object-cover rounded shadow-sm">
                                </div>';
                            } else {
                                // Icono predeterminado si no hay imagen
                                $thumbnail = '<div class="search-item-icon w-16 h-16 md:w-20 md:h-20 flex items-center justify-center rounded bg-[#f7ede2] text-spa-primary">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>';
                            }

                            // Obtener el nombre del tipo de post para mostrar
                            $post_type_name = get_post_type_object($post_type)->labels->singular_name;
                        ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class('search-item ' . $post_type_class . ' bg-white border border-gray-100 rounded-lg p-5 shadow-sm hover:shadow-md transition-all duration-300 flex gap-4'); ?>>
                                <?php echo $thumbnail; ?>

                                <div class="search-item-content flex-1">
                                    <header class="mb-2">
                                        <span class="text-xs font-medium text-spa-accent uppercase tracking-wider">
                                            <?php echo esc_html($post_type_name); ?>
                                        </span>
                                        <h2 class="entry-title text-lg md:text-xl fancy-text mt-1">
                                            <a href="<?php the_permalink(); ?>" class="text-gray-800 hover:text-spa-primary transition-colors duration-300">
                                                <?php the_title(); ?>
                                            </a>
                                        </h2>
                                    </header>

                                    <div class="entry-summary text-gray-600 text-sm md:text-base">
                                        <?php
                                        // Mostrar extracto con resaltado de términos de búsqueda
                                        $excerpt = get_the_excerpt();
                                        $search_term = get_search_query();

                                        if (!empty($search_term) && !empty($excerpt)) {
                                            $excerpt = preg_replace('/(' . preg_quote($search_term, '/') . ')/i', '<mark class="bg-amber-100 px-1 rounded">$1</mark>', $excerpt);
                                        }

                                        echo wp_kses_post($excerpt);
                                        ?>
                                    </div>

                                    <footer class="entry-footer mt-3 text-xs text-gray-500 flex items-center">
                                        <span class="mr-4">
                                            <svg class="w-4 h-4 inline-block mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <?php echo get_the_date(); ?>
                                        </span>

                                        <?php if ('post' === $post_type) : ?>
                                            <span>
                                                <svg class="w-4 h-4 inline-block mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                </svg>
                                                <?php
                                                $categories_list = get_the_category_list(', ');
                                                if ($categories_list) {
                                                    echo $categories_list;
                                                }
                                                ?>
                                            </span>
                                        <?php endif; ?>
                                    </footer>
                                </div>

                                <div class="search-item-action hidden md:flex items-center">
                                    <a href="<?php the_permalink(); ?>" class="text-spa-primary hover:text-spa-accent transition-colors duration-300">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                        </svg>
                                    </a>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>

                    <!-- Paginación -->
                    <div class="pagination mt-10 pt-8 border-t border-gray-100 flex justify-center">
                        <?php
                        echo paginate_links(
                            array(
                                'prev_text' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>' . esc_html__('Anterior', 'wp-tailwind-theme'),
                                'next_text' => esc_html__('Siguiente', 'wp-tailwind-theme') . '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>',
                                'class' => 'pagination-item',
                                'type' => 'list',
                                'end_size' => 2,
                                'mid_size' => 2,
                            )
                        );
                        ?>
                    </div>

                <?php else : ?>
                    <!-- No hay resultados -->
                    <div class="no-results bg-white p-10 text-center border border-gray-100 rounded-lg shadow-sm">
                        <div class="mb-6">
                            <svg class="w-20 h-20 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>

                        <h2 class="text-2xl fancy-text text-spa-primary mb-4">
                            <?php esc_html_e('No se encontraron resultados', 'wp-tailwind-theme'); ?>
                        </h2>

                        <p class="text-gray-600 mb-8 max-w-xl mx-auto">
                            <?php esc_html_e('Lo sentimos, no hemos podido encontrar resultados para tu búsqueda. Por favor, intenta con otros términos o explora nuestras sugerencias.', 'wp-tailwind-theme'); ?>
                        </p>

                        <div class="search-suggestions">
                            <h3 class="text-lg font-medium text-gray-800 mb-4">
                                <?php esc_html_e('Sugerencias:', 'wp-tailwind-theme'); ?>
                            </h3>

                            <ul class="list-disc list-inside text-left max-w-md mx-auto text-gray-600 mb-8">
                                <li class="mb-2"><?php esc_html_e('Verifica que todas las palabras estén escritas correctamente.', 'wp-tailwind-theme'); ?></li>
                                <li class="mb-2"><?php esc_html_e('Prueba con palabras clave diferentes.', 'wp-tailwind-theme'); ?></li>
                                <li class="mb-2"><?php esc_html_e('Utiliza términos más generales.', 'wp-tailwind-theme'); ?></li>
                                <li><?php esc_html_e('Reduce el número de palabras en tu búsqueda.', 'wp-tailwind-theme'); ?></li>
                            </ul>

                            <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-flex items-center justify-center px-6 py-3 bg-spa-primary text-white font-medium rounded-sm hover:bg-opacity-90 transition-all duration-300">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                <?php esc_html_e('Volver al inicio', 'wp-tailwind-theme'); ?>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (have_posts()) : ?>
                    <!-- Consultas populares (solo se muestra si hay resultados) -->
                    <div class="popular-searches mt-16 pt-8 border-t border-gray-100">
                        <h2 class="text-xl fancy-text text-center mb-6 text-spa-primary">
                            <?php esc_html_e('Búsquedas populares', 'wp-tailwind-theme'); ?>
                        </h2>

                        <div class="flex flex-wrap justify-center gap-2">
                            <?php
                            // Aquí puedes añadir términos de búsqueda populares en tu sitio
                            $popular_searches = array(
                                'Masajes',
                                'Tratamientos',
                                'Faciales',
                                'Relajación',
                                'Bienestar',
                                'Belleza',
                                'Terapias',
                                'Productos'
                            );

                            foreach ($popular_searches as $term) :
                            ?>
                                <a href="<?php echo esc_url(home_url('/?s=' . urlencode($term))); ?>" class="px-4 py-2 bg-[#f7ede2] bg-opacity-50 text-gray-700 text-sm rounded-full hover:bg-[#f7ede2] transition-colors duration-300">
                                    <?php echo esc_html($term); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Funcionalidad para los filtros de tipo de contenido
        const filterButtons = document.querySelectorAll('.filter-btn');
        const searchItems = document.querySelectorAll('.search-item');

        if (filterButtons.length > 0) {
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Quitar clase activa de todos los botones
                    filterButtons.forEach(btn => {
                        btn.classList.remove('active', 'bg-spa-primary', 'text-white');
                        btn.classList.add('bg-gray-100', 'text-gray-700');
                    });

                    // Añadir clase activa al botón clickeado
                    this.classList.add('active', 'bg-spa-primary', 'text-white');
                    this.classList.remove('bg-gray-100', 'text-gray-700');

                    const filter = this.getAttribute('data-filter');

                    // Filtrar elementos
                    searchItems.forEach(item => {
                        if (filter === 'all') {
                            item.style.display = 'flex';
                        } else {
                            if (item.classList.contains('item-type-' + filter)) {
                                item.style.display = 'flex';
                            } else {
                                item.style.display = 'none';
                            }
                        }
                    });
                });
            });
        }
    });
</script>

<?php
get_footer();
