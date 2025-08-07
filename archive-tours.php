<?php

/**
 * The template for displaying archive pages for destinations
 *
 * @package WPTBT
 */

// Modificar la consulta principal para mostrar destinations en orden descendente

add_action('pre_get_posts', function ($query) {
    if (!is_admin() && $query->is_main_query() && is_post_type_archive('tours')) {
        // Agregar filtro por destino si se especifica
        if (isset($_GET['destination']) && !empty($_GET['destination'])) {
            $query->set('tax_query', array(
                array(
                    'taxonomy' => 'destinations',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field($_GET['destination']),
                ),
            ));
        }
    }
});


get_header();

// Establecer variables para schema.org
$organization_name = get_bloginfo('name');
$organization_url = get_home_url();
$archive_title = get_theme_mod('tours_archive_title', __('Our Tours', 'wptbt-tours'));
$archive_desc = get_theme_mod('tours_archive_description', __('Explore our amazing collection of tours and adventures designed to create unforgettable memories and unique travel experiences.', 'wptbt-tours'));
?>

<!-- Schema.org para la página de archivo de destinations - SEO Improvement -->
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "CollectionPage",
        "name": "<?php echo esc_js($archive_title); ?>",
        "description": "<?php echo esc_js($archive_desc); ?>",
        "url": "<?php echo esc_url(get_post_type_archive_link('tour')); ?>",
        "publisher": {
            "@type": "Organization",
            "name": "<?php echo esc_js($organization_name); ?>",
            "url": "<?php echo esc_url($organization_url); ?>"
        },
        "inLanguage": "<?php echo esc_attr(get_bloginfo('language')); ?>"
    }
</script>

<main id="primary" class="site-main" itemscope itemtype="https://schema.org/WebPage">
    <!-- Encabezado de la página con estilo spa -->
    <section class="page-header relative bg-spa-secondary py-16 md:py-24 overflow-hidden">
        <div class="container mx-auto px-4 relative z-20">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-5xl lg:text-6xl fancy-text font-medium mb-4 text-spa-primary reveal-item opacity-0 translate-y-8" itemprop="headline">
                    <?php echo esc_html(get_theme_mod('tours_archive_title', __('Our Tours', 'wptbt-tours'))); ?>
                </h1>

                <p class="text-xl md:text-2xl text-spa-accent italic mb-8 reveal-item opacity-0 translate-y-8 delay-300">
                    <?php echo esc_html(get_theme_mod('tours_archive_subtitle', __('Discover amazing adventures and unforgettable experiences', 'wptbt-tours'))); ?>
                </p>

                <div class="prose max-w-2xl mx-auto text-center text-gray-600 reveal-item opacity-0 translate-y-8 delay-600" itemprop="description">
                    <p><?php echo esc_html(get_theme_mod('tours_archive_description', __('Explore our amazing collection of tours and adventures designed to create unforgettable memories and unique travel experiences.', 'wptbt-tours'))); ?></p>
                </div>
            </div>
        </div>

        <!-- Onda decorativa inferior -->
        <div class="absolute bottom-0 left-0 right-0 z-10" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100" preserveAspectRatio="none" class="w-full h-16 md:h-20 fill-current text-white">
                <path d="M0,85.7L20,75.4C40,65.1,80,44.6,120,33.9C160,23.2,200,22.4,240,28.6C280,34.8,320,48.1,360,50C400,51.9,440,42.5,480,39.8C520,37.2,560,41.3,600,48.3C640,55.2,680,65,720,62.3C760,59.5,800,44.2,840,41.9C880,39.7,920,50.5,960,54.3C1000,58.2,1040,55.2,1080,58.2C1120,61.1,1160,69.9,1200,73.3C1240,76.8,1280,74.9,1320,70.9C1360,67,1400,61,1420,58L1440,55L1440,100L1420,100C1400,100,1360,100,1320,100C1280,100,1240,100,1200,100C1160,100,1120,100,1080,100C1040,100,1000,100,960,100C920,100,880,100,840,100C800,100,760,100,720,100C680,100,640,100,600,100C560,100,520,100,480,100C440,100,400,100,360,100C320,100,280,100,240,100C200,100,160,100,120,100C80,100,40,100,20,100L0,100Z"></path>
            </svg>
        </div>
    </section>

    <!-- Filtro de categorías de destinations (opcional) -->
    <?php if (get_theme_mod('show_service_filters', true)) : ?>
        <?php
        // Obtener categorías de destinations (si estás utilizando taxonomías)
        $terms = get_terms(array(
            'taxonomy' => 'category',
            'hide_empty' => true,
            'object_ids' => get_posts(array(
                'post_type' => 'servicio',
                'fields' => 'ids',
                'posts_per_page' => -1,
                'orderby' => 'ID',
                'order' => 'ASC',
            ))
        ));

        if (!is_wp_error($terms) && !empty($terms)) :
        ?>
            <nav class="service-filters py-8 bg-white" aria-label="<?php esc_attr_e('Service Categories', 'wptbt-tours'); ?>">
                <div class="container mx-auto px-4">
                    <div class="flex flex-wrap justify-center items-center gap-4 reveal-item opacity-0 translate-y-8">
                        <a href="#todos" class="filter-btn active px-4 py-2 rounded-sm bg-amber-50 text-gray-700 border border-amber-200 hover:bg-amber-600 hover:text-white transition duration-300"
                            aria-current="true"
                            data-category="all">
                            <?php esc_html_e('All', 'wptbt-tours'); ?>
                        </a>
                        <?php foreach ($terms as $term) : ?>
                            <a href="#<?php echo esc_attr($term->slug); ?>" class="filter-btn px-4 py-2 rounded-sm bg-amber-50 text-gray-700 border border-amber-200 hover:bg-amber-600 hover:text-white transition duration-300"
                                aria-current="false"
                                data-category="<?php echo esc_attr($term->slug); ?>">
                                <?php echo esc_html($term->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </nav>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Lista de destinations -->
    <section class="services-list py-12 md:py-16 bg-white" aria-label="<?php esc_attr_e('Services List', 'wptbt-tours'); ?>">
        <div class="container mx-auto px-4">
            <!-- Schema.org para la lista de destinations - SEO Improvement -->
            <script type="application/ld+json">
                {
                    "@context": "https://schema.org",
                    "@type": "ItemList",
                    "itemListElement": [
                        <?php
                        $counter = 0;
                        if (have_posts()) :
                            while (have_posts()) : the_post();
                                if ($counter > 0) {
                                    echo ",";
                                }
                                echo '{
                                "@type": "ListItem",
                                "position": ' . ($counter + 1) . ',
                                "name": "' . esc_js(get_the_title()) . '",
                                "url": "' . esc_url(get_permalink()) . '"
                            }';
                                $counter++;
                            endwhile;
                            rewind_posts(); // Restaurar para el bucle principal
                        endif;
                        ?>
                    ]
                }
            </script>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                if (have_posts()) :
                    $delay = 0;
                    while (have_posts()) : the_post();
                        // Obtener datos del tour usando las nuevas funciones
                        $tour_id = get_the_ID();
                        $pricing_data = WPTBT_Tours::get_tour_pricing_data($tour_id);
                        $subtitle = get_post_meta($tour_id, '_wptbt_tour_subtitle', true);
                        $difficulty = get_post_meta($tour_id, '_tour_difficulty', true);
                        $duration = get_post_meta($tour_id, '_tour_duration', true);
                        
                        // Obtener términos de taxonomías para filtrado
                        $destinations = get_the_terms($tour_id, 'destinations');
                        $categories = get_the_terms($tour_id, 'tour-categories');
                        
                        $location = '';
                        if ($destinations && !is_wp_error($destinations)) {
                            $location = $destinations[0]->name;
                        }
                        
                        // Classes para filtrado
                        $category_classes = '';
                        $category_names = array();
                        if ($categories && !is_wp_error($categories)) {
                            foreach ($categories as $category) {
                                $category_classes .= ' cat-' . $category->slug;
                                $category_names[] = $category->name;
                            }
                        }
                        $category_text = !empty($category_names) ? implode(', ', $category_names) : '';

                        // Obtener la descripción de la imagen para el alt text
                        $image_id = get_post_thumbnail_id();
                        $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                        $image_alt = empty($image_alt) ? get_the_title() . ' - ' . $organization_name : $image_alt;
                ?>
                        <!-- Card con el estilo del carousel -->
                        <article class="tour-card reveal-item opacity-0 translate-y-8 delay-<?php echo $delay; ?> filter-item<?php echo $category_classes; ?> bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-2xl relative h-96 md:h-[500px]"
                            itemscope itemtype="https://schema.org/TouristAttraction">
                            <!-- Imagen que ocupa todo el card -->
                            <div class="tour-image absolute inset-0 overflow-hidden">
                                <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('large', array(
                                            'class' => 'w-full h-full object-cover transition-transform duration-700 hover:scale-110',
                                            'alt' => esc_attr($image_alt),
                                            'itemprop' => 'image'
                                        )); ?>
                                    </a>
                                <?php else : ?>
                                    <div class="w-full h-full bg-gradient-to-br from-gray-800 via-gray-700 to-gray-900"></div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Degradado de oscuro a transparente -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                            
                            <!-- Badge de precio especial -->
                            <?php if ($pricing_data['promotion'] && $pricing_data['original']) : ?>
                                <div class="absolute top-4 right-4 px-3 py-1 rounded-full text-white font-semibold text-sm shadow-md z-10 bg-red-600">
                                    Special Price
                                </div>
                            <?php endif; ?>


                            <!-- Schema.org meta data -->
                            <?php if (!empty($category_text)) : ?>
                                <meta itemprop="category" content="<?php echo esc_attr($category_text); ?>">
                            <?php endif; ?>
                            <meta itemprop="name" content="<?php echo esc_attr(get_the_title()); ?>">
                            <meta itemprop="description" content="<?php echo esc_attr(wp_trim_words(get_the_excerpt(), 20)); ?>">
                            <meta itemprop="url" content="<?php echo esc_url(get_permalink()); ?>">

                            <!-- Contenido del card superpuesto -->
                            <div class="tour-content absolute bottom-0 left-0 right-0 p-6 z-10">
                                <h3 class="tour-title fancy-text text-lg font-bold mb-2 line-clamp-2 hover:text-opacity-80 transition-colors" itemprop="name">
                                    <a href="<?php the_permalink(); ?>" class="text-white hover:text-gray-200 transition-colors">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>

                                <?php if ($subtitle) : ?>
                                    <p class="tour-subtitle text-white/90 text-sm mb-4 line-clamp-3">
                                        <?php echo esc_html($subtitle); ?>
                                    </p>
                                <?php endif; ?>

                                <!-- Meta información -->
                                <div class="tour-meta flex items-center justify-between text-sm text-white/80 mb-4">
                                    <?php if ($location) : ?>
                                        <span class="flex items-center text-white/80">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <?php echo esc_html($location); ?>
                                        </span>
                                    <?php endif; ?>

                                    <?php if ($difficulty) : ?>
                                        <span class="flex items-center text-white/80">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                            <?php echo esc_html($difficulty); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Botón CTA y Precio -->
                                <div class="flex items-center justify-between">
                                    <a href="<?php the_permalink(); ?>" 
                                       class="tour-cta-btn inline-flex items-center justify-center px-6 py-2 rounded-lg text-white font-medium transition-all duration-300 hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 bg-red-600 hover:bg-red-700"
                                       itemprop="url"
                                       title="<?php echo esc_attr(sprintf(__('View details of %s', 'wptbt-tours'), get_the_title())); ?>">
                                        <?php _e('View Tour', 'wptbt-tours'); ?>
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                        </svg>
                                    </a>
                                    
                                    <?php if ($pricing_data['best_price']) : ?>
                                        <div class="text-white font-bold" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                                            <meta itemprop="priceCurrency" content="<?php echo esc_attr($pricing_data['currency']); ?>">
                                            <?php if ($pricing_data['promotion'] && $pricing_data['original']) : ?>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-lg font-bold" itemprop="price" content="<?php echo esc_attr($pricing_data['promotion']); ?>">
                                                        <?php echo $pricing_data['symbol'] . $pricing_data['promotion']; ?>
                                                    </span>
                                                    <span class="text-sm line-through opacity-75">
                                                        <?php echo $pricing_data['symbol'] . $pricing_data['original']; ?>
                                                    </span>
                                                </div>
                                            <?php else : ?>
                                                <span class="text-lg font-bold" itemprop="price" content="<?php echo esc_attr($pricing_data['best_price']); ?>">
                                                    <?php echo $pricing_data['symbol'] . $pricing_data['best_price']; ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Provider information for schema.org -->
                                <div itemprop="provider" itemscope itemtype="https://schema.org/Organization" class="hidden">
                                    <meta itemprop="name" content="<?php echo esc_attr($organization_name); ?>">
                                    <meta itemprop="url" content="<?php echo esc_url($organization_url); ?>">
                                </div>
                            </div>
                        </article>
                    <?php
                        // Rotamos los retrasos de animación
                        $delay = ($delay + 300) % 900;
                    endwhile;

                    // Paginación mejorada
                    echo '<nav class="col-span-full mt-12 text-center" aria-label="' . esc_attr__('Pagination', 'wptbt-tours') . '">';
                    the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg><span class="sr-only">' . __('Previous page', 'wptbt-tours') . '</span>',
                        'next_text' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg><span class="sr-only">' . __('Next page', 'wptbt-tours') . '</span>',
                        'class' => 'pagination flex justify-center gap-2',
                        'before_page_number' => '<span class="sr-only">' . __('Page', 'wptbt-tours') . ' </span>',
                    ));
                    echo '</nav>';
                else :
                    ?>
                    <div class="col-span-full text-center py-16">
                        <p class="text-xl text-gray-500"><?php esc_html_e('No services found.', 'wptbt-tours'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Banner de CTA -->
    <?php if (get_theme_mod('show_tours_cta', true)) : ?>
        <section class="cta-banner bg-spa-primary text-white py-16 relative overflow-hidden">
            <!-- Elementos decorativos (solo visibles si no hay imagen de fondo) -->
            <?php if (empty(wptbt_get_cta_background_image())) : ?>
                <div class="absolute top-0 right-0 w-64 h-64 rounded-full bg-spa-accent opacity-10 transform translate-x-1/4 -translate-y-1/4" aria-hidden="true"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full bg-spa-rose opacity-10 transform -translate-x-1/4 translate-y-1/4" aria-hidden="true"></div>
            <?php endif; ?>

            <div class="container mx-auto px-4 relative z-10">
                <div class="max-w-2xl mx-auto text-center">
                    <h2 class="text-3xl md:text-4xl fancy-text font-medium mb-4 reveal-item opacity-0 translate-y-8">
                        <?php echo esc_html(get_theme_mod('tours_archive_cta_title', __('Ready for your next adventure?', 'wptbt-tours'))); ?>
                    </h2>
                    <p class="text-xl opacity-90 mb-8 reveal-item opacity-0 translate-y-8 delay-300">
                        <?php echo esc_html(get_theme_mod('tours_archive_cta_text', __('Discover amazing tours and create unforgettable memories with our exclusive travel experiences.', 'wptbt-tours'))); ?>
                    </p>
                    <!-- MEJORADO: Botón CTA con colores de contraste fijo -->
                    <a href="<?php echo esc_url(get_theme_mod('cta_button_url', '#')); ?>" class="inline-block px-8 py-3 bg-amber-600 text-white font-semibold rounded-lg transition-all duration-300 hover:bg-amber-700 transform hover:-translate-y-1 hover:shadow-lg reveal-item opacity-0 translate-y-8 delay-600"
                        title="<?php echo esc_attr(get_theme_mod('tours_archive_cta_button_text', __('Explore Tours', 'wptbt-tours'))); ?>">
                        <?php echo esc_html(get_theme_mod('tours_archive_cta_button_text', __('Explore Tours', 'wptbt-tours'))); ?>
                    </a>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php
    // Formulario de reserva
    if (get_theme_mod('show_tours_booking_form', false)) :
        // Obtener la imagen de fondo
        $bg_image_id = get_theme_mod('tours_booking_form_bg_image', '');
        $bg_image_url = '';

        if (!empty($bg_image_id)) {
            $bg_image_url = wp_get_attachment_image_url($bg_image_id, 'full');
        } else {
            $bg_image_url = get_template_directory_uri() . '/assets/images/default-spa.jpg';
        }

        // Obtener las opciones del formulario
        $form_title = get_theme_mod('tours_booking_form_title', __('Book Your Appointment', 'wptbt-tours'));
        $form_subtitle = get_theme_mod('tours_booking_form_subtitle', __('Appointment', 'wptbt-tours'));
        $form_description = get_theme_mod('tours_booking_form_description', __('Book your treatment now and enjoy a moment of relaxation.', 'wptbt-tours'));
        $form_button_text = get_theme_mod('tours_booking_form_button_text', __('BOOK NOW', 'wptbt-tours'));
        $form_accent_color = get_theme_mod('tours_booking_form_accent_color', '#D4B254');
        $form_email = get_theme_mod('tours_booking_form_email', get_option('admin_email'));
        $show_top_wave = get_theme_mod('tours_booking_form_show_top_wave', true);
        $show_bottom_wave = get_theme_mod('tours_booking_form_show_bottom_wave', true);
        // Configurar las opciones para el bloque de reserva
        $booking_attributes = array(
            'title' => $form_title,
            'subtitle' => $form_subtitle,
            'description' => $form_description,
            'imageURL' => $bg_image_url,
            'buttonText' => $form_button_text,
            'buttonColor' => $form_accent_color,
            'textColor' => '#FFFFFF',
            'accentColor' => $form_accent_color,
            'emailRecipient' => $form_email,
            'useSolidJs' => true,
            'showTopWave' => $show_top_wave,
            'showBottomWave' => $show_bottom_wave
        );

        // Cargar el bloque de reservas si la clase existe
        if (class_exists('WPTBT_Booking_Block')) {
            $booking_block = new WPTBT_Booking_Block();
            echo $booking_block->render_booking_block($booking_attributes);
        }
    endif;
    ?>
</main>

<!-- Scripts de animación y mejoras del comportamiento del filtro -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sistema de filtrado de destinations por categoría - mejorado para SEO
        const filterBtns = document.querySelectorAll('.filter-btn');
        const filterItems = document.querySelectorAll('.filter-item');

        // Función para actualizar título y meta description cuando se filtra
        function updateMetaInfo(category) {
            // Actualizar solo el título si estamos filtrando
            const metaTitle = document.querySelector('title');
            const metaDesc = document.querySelector('meta[name="description"]');
            const siteName = '<?php echo esc_js(get_bloginfo('name')); ?>';
            const baseTitle = '<?php echo esc_js($archive_title); ?>';

            if (category && category !== 'todos') {
                // Encontrar el nombre de la categoría
                const categoryBtn = document.querySelector(`.filter-btn[data-category="${category}"]`);
                const categoryName = categoryBtn ? categoryBtn.textContent.trim() : '';

                if (categoryName && metaTitle) {
                    metaTitle.textContent = `${categoryName} - ${baseTitle} | ${siteName}`;
                }

                if (metaDesc) {
                    const categoryDesc = `Descubre nuestros destinations de ${categoryName} diseñados para brindar una experiencia excepcional de relajación y bienestar en Cusco.`;
                    metaDesc.setAttribute('content', categoryDesc);
                }
            } else {
                // Restaurar el título original
                if (metaTitle) {
                    metaTitle.textContent = `${baseTitle} | ${siteName}`;
                }

                if (metaDesc) {
                    metaDesc.setAttribute('content', '<?php echo esc_js($archive_desc); ?>');
                }
            }
        }

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                // Quitar clase active y aria-current de todos los botones
                filterBtns.forEach(item => {
                    item.classList.remove('active');
                    item.setAttribute('aria-current', 'false');
                });

                // Añadir clase active y aria-current al botón actual
                this.classList.add('active');
                this.setAttribute('aria-current', 'true');

                // Obtener categoría a filtrar
                const filterValue = this.getAttribute('href').substring(1);
                const dataCategory = this.getAttribute('data-category');

                // Actualizar la URL sin recargar
                if (history.pushState) {
                    const newUrl = filterValue === 'todos' ?
                        window.location.pathname :
                        window.location.pathname + '#' + filterValue;
                    window.history.pushState({
                        path: newUrl
                    }, '', newUrl);
                }

                // Actualizar meta información para SEO
                updateMetaInfo(dataCategory);

                // Mostrar u ocultar elementos según la categoría
                if (filterValue === 'todos') {
                    filterItems.forEach(item => {
                        item.style.display = 'block';
                        item.setAttribute('aria-hidden', 'false');
                    });
                } else {
                    filterItems.forEach(item => {
                        if (item.classList.contains('cat-' + filterValue)) {
                            item.style.display = 'block';
                            item.setAttribute('aria-hidden', 'false');
                        } else {
                            item.style.display = 'none';
                            item.setAttribute('aria-hidden', 'true');
                        }
                    });
                }

                // Anunciar a lectores de pantalla que los resultados han sido filtrados
                const resultCount = document.querySelectorAll('.filter-item[style="display: block"]').length;
                const announcement = document.createElement('div');
                announcement.setAttribute('role', 'status');
                announcement.setAttribute('aria-live', 'polite');
                announcement.className = 'sr-only';
                announcement.textContent = `${resultCount} destinations mostrados`;
                document.body.appendChild(announcement);

                // Eliminar el anuncio después de 5 segundos
                setTimeout(() => {
                    document.body.removeChild(announcement);
                }, 5000);
            });
        });

        // Activar filtro según hash de URL
        if (window.location.hash) {
            const hash = window.location.hash.substring(1);
            const targetBtn = document.querySelector(`.filter-btn[href="#${hash}"]`);
            if (targetBtn) {
                targetBtn.click();
            }
        }

        // Sistema de revelado de elementos al hacer scroll - con Intersection Observer
        const revealItems = document.querySelectorAll('.reveal-item');

        // Usar Intersection Observer en lugar de eventos de scroll
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    observer.unobserve(entry.target); // Dejar de observar una vez revelado
                }
            });
        }, observerOptions);

        revealItems.forEach(item => {
            observer.observe(item);
        });

        // Alternativa para navegadores que no soportan Intersection Observer
        if (!('IntersectionObserver' in window)) {
            // Fallback a la función original
            const revealOnScroll = function() {
                for (let i = 0; i < revealItems.length; i++) {
                    const windowHeight = window.innerHeight;
                    const elementTop = revealItems[i].getBoundingClientRect().top;
                    const elementVisible = 150;

                    if (elementTop < windowHeight - elementVisible) {
                        revealItems[i].classList.add('revealed');
                    }
                }
            };

            // Ejecutar al cargar la página
            revealOnScroll();

            // Ejecutar al hacer scroll
            window.addEventListener('scroll', revealOnScroll);
        }

        // Agregar alt text a las imágenes que no lo tengan
        document.querySelectorAll('img:not([alt])').forEach(img => {
            const parent = img.closest('.service-card');
            if (parent) {
                const title = parent.querySelector('h3')?.textContent.trim() || 'Servicio de spa';
                img.alt = `${title} - <?php echo esc_js($organization_name); ?>`;
            } else {
                img.alt = 'Imagen de servicio de spa';
            }
        });
    });
</script>

<style>
    /* Estilos adicionales para las animaciones */
    .reveal-item {
        transition: all 0.6s ease;
    }

    .reveal-item.revealed {
        opacity: 1 !important;
        transform: translateY(0) !important;
    }

    /* Clases de retraso */
    .delay-300 {
        transition-delay: 0.3s;
    }

    .delay-600 {
        transition-delay: 0.6s;
    }

    .delay-900 {
        transition-delay: 0.9s;
    }

    /* Estilos para la paginación con mejoras de accesibilidad */
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .pagination .page-numbers {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin: 0 5px;
        background-color: #fef3c7;
        /* amber-50 equivalent */
        color: #92400e;
        /* amber-700 equivalent */
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
        position: relative;
        /* Para elementos sr-only */
        border: 1px solid #fbbf24;
        /* amber-400 equivalent */
    }

    .pagination .page-numbers:hover {
        background-color: #d97706;
        /* amber-600 equivalent */
        color: white;
        border-color: #d97706;
    }

    .pagination .current {
        background-color: #d97706;
        /* amber-600 equivalent */
        color: white;
        border-color: #d97706;
    }

    /* Estilos para el botón de filtro activo con colores fijos */
    .filter-btn.active {
        background-color: #d97706 !important;
        /* amber-600 */
        color: white !important;
        border-color: #d97706 !important;
    }

    .filter-btn:hover:not(.active) {
        background-color: #d97706;
        /* amber-600 */
        color: white;
        border-color: #d97706;
    }

    /* Clase para elemento visualmente oculto pero accesible a lectores de pantalla */
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border-width: 0;
    }

    /* Adiciones para usuarios que prefieren menos movimiento */
    @media (prefers-reduced-motion: reduce) {
        .reveal-item {
            transition: none !important;
            opacity: 1 !important;
            transform: none !important;
        }

        .filter-btn,
        .service-card,
        .cta-banner {
            animation: none !important;
            opacity: 1 !important;
        }

        .pagination .page-numbers,
        a {
            transition: none !important;
        }
    }
</style>

<?php
get_footer();
