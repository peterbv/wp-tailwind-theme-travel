<?php
/**
 * Template para mostrar tours individuales
 * Diseño completamente renovado para agencia de viajes
 * Integra todas las nuevas funcionalidades del post type mejorado
 *
 * @package WPTBT
 */

get_header();

// Obtener todos los datos del tour usando las nuevas funciones
$tour_id = get_the_ID();
$pricing_data = WPTBT_Tours::get_tour_pricing_data($tour_id);
$gallery_data = WPTBT_Tours::get_tour_gallery($tour_id);
$itinerary = WPTBT_Tours::get_tour_itinerary($tour_id);
$booking_info = WPTBT_Tours::get_tour_booking_info($tour_id);

// Meta datos del tour
$subtitle = get_post_meta($tour_id, '_wptbt_tour_subtitle', true);
$departure_dates = get_post_meta($tour_id, '_tour_departure_dates', true);
$difficulty = get_post_meta($tour_id, '_tour_difficulty', true);
$min_age = get_post_meta($tour_id, '_tour_min_age', true);
$max_people = get_post_meta($tour_id, '_tour_max_people', true);
$includes = get_post_meta($tour_id, '_tour_includes', true);
$excludes = get_post_meta($tour_id, '_tour_excludes', true);
$recommendations = get_post_meta($tour_id, '_tour_recommendations', true);
$what_to_bring = get_post_meta($tour_id, '_tour_what_to_bring', true);
$departure_point = get_post_meta($tour_id, '_tour_departure_point', true);
$return_point = get_post_meta($tour_id, '_tour_return_point', true);
$google_maps_url = get_post_meta($tour_id, '_tour_google_maps_url', true);

// Nuevos campos técnicos
$total_duration = get_post_meta($tour_id, '_tour_total_duration', true);
$alternative_route = get_post_meta($tour_id, '_tour_alternative_route', true);
$altitude_points = get_post_meta($tour_id, '_tour_altitude_points', true);
$technical_description = get_post_meta($tour_id, '_tour_technical_description', true);

// Badges del tour
$is_featured = get_post_meta($tour_id, '_tour_featured', true) == '1';
$is_popular = get_post_meta($tour_id, '_tour_popular', true) == '1';
$is_new = get_post_meta($tour_id, '_tour_new', true) == '1';

// Términos de taxonomías
$destinations = get_the_terms($tour_id, 'destinations');
$categories = get_the_terms($tour_id, 'tour-categories');
?>

<main id="primary" class="site-main">
    <?php while (have_posts()) : the_post(); ?>

        <!-- Hero Section moderno -->
        <section class="tour-hero relative h-[calc(100vh-var(--header-height,80px))] min-h-[700px] flex items-end overflow-hidden">
            
            <!-- Imagen de fondo simplificada -->
            <div class="absolute inset-0 z-0">
                <?php if (has_post_thumbnail()) : ?>
                    <!-- Imagen destacada de WordPress tiene prioridad -->
                    <?php the_post_thumbnail('full', ['class' => 'w-full h-full object-cover']); ?>
                <?php elseif (isset($gallery_data['use_as_featured']) && !empty($gallery_data['images'])) : ?>
                    <!-- Primera imagen de la galería como fallback si no hay imagen destacada -->
                    <?php 
                    $featured_image_id = $gallery_data['images'][0];
                    $featured_image = wp_get_attachment_image_src($featured_image_id, 'full');
                    ?>
                    <img src="<?php echo esc_url($featured_image[0]); ?>" 
                         alt="<?php echo esc_attr(get_the_title()); ?>" 
                         class="w-full h-full object-cover" />
                <?php else : ?>
                    <!-- Fondo profesional por defecto -->
                    <div class="w-full h-full bg-gradient-to-br from-gray-800 via-gray-700 to-gray-900"></div>
                <?php endif; ?>
                
                <!-- Botón de galería si existe galería - lado izquierdo -->
                <?php if (isset($gallery_data['use_as_featured']) && !empty($gallery_data['images']) && count($gallery_data['images']) > 1) : ?>
                    <div class="absolute top-6 left-6 z-30">
                        <button class="hero-gallery-open bg-black/30 hover:bg-black/50 backdrop-blur-md text-white rounded-lg px-4 py-2 flex items-center space-x-2 transition-all duration-300 hover:scale-105 group"
                                onclick="document.getElementById('tab-gallery') && document.querySelector('[data-tab=gallery]').click(); document.getElementById('tab-gallery').scrollIntoView({ behavior: 'smooth' });">
                            <svg class="w-4 h-4 transform group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm font-medium"><?php printf(__('Gallery (%d)', 'wptbt-tours'), count($gallery_data['images'])); ?></span>
                        </button>
                    </div>
                <?php endif; ?>
                
                <!-- Overlay moderno con gradiente -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-black/30 to-transparent"></div>
            </div>

            <!-- Contenido del hero moderno -->
            <div class="container mx-auto px-4 relative z-10 pb-16">
                <div class="max-w-4xl">
                    <!-- Breadcrumb minimalista -->
                    <nav class="mb-8">
                        <a href="<?php echo esc_url(get_post_type_archive_link('tours')); ?>" 
                           class="inline-flex items-center text-white/70 hover:text-white transition-all duration-300 text-sm backdrop-blur-sm bg-white/10 px-4 py-2 rounded-full border border-white/20">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            <?php _e('Explore Tours', 'wptbt-tours'); ?>
                        </a>
                    </nav>

                    <!-- Badges modernos -->
                    <?php if ($is_featured || $is_popular || $is_new) : ?>
                        <div class="flex flex-wrap gap-3 mb-8">
                            <?php if ($is_featured) : ?>
                                <span class="inline-flex items-center px-4 py-2 text-sm font-medium bg-gradient-to-r from-amber-400/20 to-orange-400/20 text-amber-100 border border-amber-400/30 rounded-full backdrop-blur-md">
                                    <span class="w-2 h-2 bg-amber-400 rounded-full mr-2 animate-pulse"></span>
                                    <?php _e('Featured', 'wptbt-tours'); ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($is_popular) : ?>
                                <span class="inline-flex items-center px-4 py-2 text-sm font-medium bg-gradient-to-r from-red-400/20 to-pink-400/20 text-red-100 border border-red-400/30 rounded-full backdrop-blur-md">
                                    <span class="w-2 h-2 bg-red-400 rounded-full mr-2 animate-pulse"></span>
                                    <?php _e('Popular', 'wptbt-tours'); ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($is_new) : ?>
                                <span class="inline-flex items-center px-4 py-2 text-sm font-medium bg-gradient-to-r from-emerald-400/20 to-teal-400/20 text-emerald-100 border border-emerald-400/30 rounded-full backdrop-blur-md">
                                    <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2 animate-pulse"></span>
                                    <?php _e('New', 'wptbt-tours'); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Título y subtítulo modernos -->
                    <div class="space-y-6">
                        <h1 class="text-4xl md:text-7xl lg:text-8xl font-bold mb-6 leading-[0.9] tracking-tight">
                            <span class="block text-white drop-shadow-lg">
                                <?php the_title(); ?>
                            </span>
                        </h1>
                        
                        <?php if ($subtitle) : ?>
                            <p class="text-lg md:text-xl text-white/80 max-w-2xl leading-relaxed">
                                <?php echo esc_html($subtitle); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                <!-- Precio destacado -->
                <?php if ($pricing_data['best_price']) : ?>
                    <div class="mb-8">
                        <div class="inline-block bg-white/10 backdrop-blur-sm rounded-2xl px-8 py-6 border border-white/20">
                            <?php if ($pricing_data['promotion'] && $pricing_data['original']) : ?>
                                <div class="text-sm text-white/80 mb-2"><?php _e('Special Offer', 'wptbt-tours'); ?></div>
                                <div class="text-lg text-white/70 line-through mb-1">
                                    <?php echo $pricing_data['symbol'] . $pricing_data['original']; ?>
                                </div>
                                <div class="text-3xl md:text-4xl font-bold text-white">
                                    <?php echo $pricing_data['symbol'] . $pricing_data['promotion']; ?>
                                </div>
                            <?php else : ?>
                                <div class="text-sm text-white/80 mb-2"><?php _e('Starting from', 'wptbt-tours'); ?></div>
                                <div class="text-3xl md:text-4xl font-bold text-white">
                                    <?php echo $pricing_data['symbol'] . $pricing_data['best_price']; ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($pricing_data['duration']) : ?>
                                <div class="text-sm text-white/80 mt-2">
                                    <?php echo esc_html($pricing_data['duration']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- CTA Principal -->
                <div class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-4">
                    <a href="#book-tour" class="inline-flex items-center px-8 py-3 bg-white text-gray-900 font-semibold rounded-lg transition-all duration-300 hover:bg-gray-100 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <?php _e('Book This Tour', 'wptbt-tours'); ?>
                    </a>
                    
                    <?php if ($google_maps_url) : ?>
                        <a href="<?php echo esc_url($google_maps_url); ?>" target="_blank" 
                           class="inline-flex items-center px-6 py-3 bg-transparent hover:bg-white/10 text-white border border-white/40 rounded-lg transition-all duration-300">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <?php _e('View on Map', 'wptbt-tours'); ?>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Scroll indicator -->
                <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                </div>
            </div>
        </section>

        <!-- Tour Overview -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                    
                    <!-- Contenido principal con tabs -->
                    <div class="lg:col-span-2">
                        
                        <!-- Sistema de pestañas -->
                        <div class="tour-content-tabs">
                            <!-- Navegación de pestañas - Diseño profesional y minimalista -->
                            <div class="tab-navigation sticky top-4 z-10 bg-white/95 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 mb-8 overflow-hidden">
                                <div class="flex overflow-x-auto scrollbar-hidden">
                                    <button class="tab-btn active flex-shrink-0" data-tab="overview">
                                        <span class="tab-icon">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                        </span>
                                        <span class="tab-text"><?php _e('Overview', 'wptbt-tours'); ?></span>
                                    </button>
                                    <?php if (!empty($itinerary)) : ?>
                                        <button class="tab-btn flex-shrink-0" data-tab="itinerary">
                                            <span class="tab-icon">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </span>
                                            <span class="tab-text"><?php _e('Itinerary', 'wptbt-tours'); ?></span>
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($includes || $excludes) : ?>
                                        <button class="tab-btn flex-shrink-0" data-tab="includes">
                                            <span class="tab-icon">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                                </svg>
                                            </span>
                                            <span class="tab-text"><?php _e('What\'s Included', 'wptbt-tours'); ?></span>
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($recommendations) : ?>
                                        <button class="tab-btn flex-shrink-0" data-tab="preparation">
                                            <span class="tab-icon">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 10V7a1 1 0 011-1h3a1 1 0 011 1v10a1 1 0 01-1 1h-3a1 1 0 01-1-1z"></path>
                                                </svg>
                                            </span>
                                            <span class="tab-text"><?php _e('Preparation', 'wptbt-tours'); ?></span>
                                        </button>
                                    <?php endif; ?>
                                    <?php if (isset($gallery_data['images']) && !empty($gallery_data['images'])) : ?>
                                        <button class="tab-btn flex-shrink-0" data-tab="gallery">
                                            <span class="tab-icon">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </span>
                                            <span class="tab-text"><?php _e('Gallery', 'wptbt-tours'); ?></span>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Contenido de pestañas -->
                            <div class="tab-content-container">
                                
                                <!-- Tab: Overview -->
                                <div class="tab-content active" id="tab-overview">
                                    <div class="mb-8">
                                        <span class="text-sm font-medium text-stone-600 uppercase tracking-wider mb-2 block">Experience</span>
                                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6"><?php _e('Tour Overview', 'wptbt-tours'); ?></h2>
                                    </div>
                                    
                                    <!-- Contenido principal del tour -->
                                    <div class="prose prose-lg max-w-none text-gray-700 mb-8">
                                        <?php the_content(); ?>
                                    </div>

                                    <!-- Información Técnica -->
                                    <?php if ($total_duration || $categories || $difficulty || $altitude_points || $technical_description || $min_age || $max_people || $departure_point || $return_point) : ?>
                                        <div class="bg-stone-50/50 rounded-2xl p-8 mb-12 border border-stone-200/50">
                                            <div class="mb-6">
                                                <div class="flex items-center mb-4">
                                                    <div class="w-10 h-10 bg-stone-700 rounded-xl flex items-center justify-center mr-4">
                                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h6a2 2 0 002-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 00-2 2v6a2 2 0 01-2 2H9z"></path>
                                                        </svg>
                                                    </div>
                                                    <h3 class="text-2xl font-semibold text-stone-900"><?php _e('Technical Information', 'wptbt-tours'); ?></h3>
                                                </div>
                                                
                                                <?php if ($technical_description) : ?>
                                                    <div class="prose max-w-none text-gray-700 mb-6">
                                                        <?php echo wp_kses_post($technical_description); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <!-- Información básica -->
                                                <div class="space-y-4">
                                                    <?php if ($total_duration) : ?>
                                                        <div class="flex items-center p-4 bg-white rounded-xl border border-stone-200/50 shadow-sm">
                                                            <div class="w-8 h-8 bg-stone-100 rounded-full flex items-center justify-center mr-3">
                                                                <svg class="w-4 h-4 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <p class="font-medium text-stone-900"><?php _e('Total Duration', 'wptbt-tours'); ?></p>
                                                                <p class="text-stone-600"><?php echo esc_html($total_duration); ?></p>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if ($difficulty) : ?>
                                                        <div class="flex items-center p-4 bg-white rounded-xl border border-stone-200/50 shadow-sm">
                                                            <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center mr-3">
                                                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <p class="font-medium text-stone-900"><?php _e('Difficulty', 'wptbt-tours'); ?></p>
                                                                <p class="text-stone-600"><?php echo esc_html(ucfirst(str_replace('-', ' - ', $difficulty))); ?></p>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if ($categories && !is_wp_error($categories)) : ?>
                                                        <div class="flex items-center p-4 bg-white rounded-xl border border-stone-200/50 shadow-sm">
                                                            <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center mr-3">
                                                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <p class="font-medium text-stone-900"><?php _e('Tour Category', 'wptbt-tours'); ?></p>
                                                                <p class="text-stone-600">
                                                                    <?php 
                                                                    $category_names = array();
                                                                    foreach ($categories as $category) {
                                                                        $category_names[] = $category->name;
                                                                    }
                                                                    echo esc_html(implode(', ', $category_names));
                                                                    ?>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if ($min_age) : ?>
                                                        <div class="flex items-center p-4 bg-white rounded-xl border border-stone-200/50 shadow-sm">
                                                            <div class="w-8 h-8 bg-slate-100 rounded-full flex items-center justify-center mr-3">
                                                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <p class="font-medium text-stone-900"><?php _e('Minimum Age', 'wptbt-tours'); ?></p>
                                                                <p class="text-stone-600"><?php echo esc_html($min_age); ?> <?php _e('years', 'wptbt-tours'); ?></p>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if ($max_people) : ?>
                                                        <div class="flex items-center p-4 bg-white rounded-xl border border-stone-200/50 shadow-sm">
                                                            <div class="w-8 h-8 bg-sky-100 rounded-full flex items-center justify-center mr-3">
                                                                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <p class="font-medium text-stone-900"><?php _e('Maximum Group Size', 'wptbt-tours'); ?></p>
                                                                <p class="text-stone-600"><?php echo esc_html($max_people); ?> <?php _e('people', 'wptbt-tours'); ?></p>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if ($departure_point) : ?>
                                                        <!--<div class="flex items-center p-4 bg-white rounded-xl border border-stone-200/50 shadow-sm">
                                                            <div class="w-8 h-8 bg-teal-100 rounded-full flex items-center justify-center mr-3">
                                                                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <p class="font-medium text-stone-900"><?php _e('Departure Point', 'wptbt-tours'); ?></p>
                                                                <p class="text-stone-600"><?php echo esc_html($departure_point); ?></p>
                                                            </div>
                                                        </div>-->
                                                    <?php endif; ?>

                                                    <?php if ($return_point) : ?>
                                                        <!--<div class="flex items-center p-4 bg-white rounded-xl border border-stone-200/50 shadow-sm">
                                                            <div class="w-8 h-8 bg-rose-100 rounded-full flex items-center justify-center mr-3">
                                                                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <p class="font-semibold text-gray-900"><?php _e('Return Point', 'wptbt-tours'); ?></p>
                                                                <p class="text-gray-600"><?php echo esc_html($return_point); ?></p>
                                                            </div>
                                                        </div>-->
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Altitude Points -->
                                                <?php if ($altitude_points && is_array($altitude_points) && !empty($altitude_points)) : ?>
                                                    <div>
                                                        <h4 class="font-bold text-gray-900 mb-4 flex items-center">
                                                            <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"></path>
                                                            </svg>
                                                            <?php _e('Altitude Points', 'wptbt-tours'); ?>
                                                        </h4>
                                                        <div class="space-y-3">
                                                            <?php foreach ($altitude_points as $point) : ?>
                                                                <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-gray-200">
                                                                    <span class="font-medium text-gray-900"><?php echo esc_html($point['location']); ?>:</span>
                                                                    <span class="text-purple-600 font-semibold"><?php echo esc_html($point['altitude']); ?></span>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Resumen de otras secciones -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                                        
                                        <?php if (!empty($itinerary)) : ?>
                                            <!-- Resumen del Itinerario -->
                                            <div class="bg-blue-50 rounded-xl p-6 border border-blue-100 hover:shadow-lg transition-all duration-300">
                                                <div class="flex items-center mb-4">
                                                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                    <h3 class="font-bold text-gray-900"><?php _e('Daily Itinerary', 'wptbt-tours'); ?></h3>
                                                </div>
                                                <p class="text-gray-600 text-sm mb-4">
                                                    <?php printf(__('Detailed %d-day schedule with activities, meals, and accommodations planned for each day.', 'wptbt-tours'), count($itinerary)); ?>
                                                </p>
                                                <button onclick="document.querySelector('[data-tab=itinerary]').click()" class="text-blue-600 hover:text-blue-700 font-medium text-sm flex items-center">
                                                    <?php _e('View Full Itinerary', 'wptbt-tours'); ?>
                                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($includes || $excludes) : ?>
                                            <!-- Resumen de lo que incluye -->
                                            <div class="bg-green-50 rounded-xl p-6 border border-green-100 hover:shadow-lg transition-all duration-300">
                                                <div class="flex items-center mb-4">
                                                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                                        </svg>
                                                    </div>
                                                    <h3 class="font-bold text-gray-900"><?php _e('What\'s Included', 'wptbt-tours'); ?></h3>
                                                </div>
                                                <p class="text-gray-600 text-sm mb-4">
                                                    <?php _e('Comprehensive list of services, activities, and amenities included in your tour package.', 'wptbt-tours'); ?>
                                                </p>
                                                <button onclick="document.querySelector('[data-tab=includes]').click()" class="text-green-600 hover:text-green-700 font-medium text-sm flex items-center">
                                                    <?php _e('View Package Details', 'wptbt-tours'); ?>
                                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($recommendations || $what_to_bring) : ?>
                                            <!-- Resumen de preparación -->
                                            <div class="bg-purple-50 rounded-xl p-6 border border-purple-100 hover:shadow-lg transition-all duration-300">
                                                <div class="flex items-center mb-4">
                                                    <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 10V7a1 1 0 011-1h3a1 1 0 011 1v10a1 1 0 01-1 1h-3a1 1 0 01-1-1z"></path>
                                                        </svg>
                                                    </div>
                                                    <h3 class="font-bold text-gray-900"><?php _e('Tour Preparation', 'wptbt-tours'); ?></h3>
                                                </div>
                                                <p class="text-gray-600 text-sm mb-4">
                                                    <?php _e('Important recommendations and essential items to bring for the best tour experience.', 'wptbt-tours'); ?>
                                                </p>
                                                <button onclick="document.querySelector('[data-tab=preparation]').click()" class="text-purple-600 hover:text-purple-700 font-medium text-sm flex items-center">
                                                    <?php _e('View Preparation Guide', 'wptbt-tours'); ?>
                                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        <?php endif; ?>


                                        <?php if (isset($gallery_data['images']) && !empty($gallery_data['images'])) : ?>
                                            <!-- Resumen de galería -->
                                            <div class="bg-orange-50 rounded-xl p-6 border border-orange-100 hover:shadow-lg transition-all duration-300">
                                                <div class="flex items-center mb-4">
                                                    <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                    <h3 class="font-bold text-gray-900"><?php _e('Photo Gallery', 'wptbt-tours'); ?></h3>
                                                </div>
                                                <p class="text-gray-600 text-sm mb-4">
                                                    <?php printf(__('Browse %d stunning photos showcasing the destinations and experiences on this tour.', 'wptbt-tours'), count($gallery_data['images'])); ?>
                                                </p>
                                                <button onclick="document.querySelector('[data-tab=gallery]').click()" class="text-orange-600 hover:text-orange-700 font-medium text-sm flex items-center">
                                                    <?php _e('View Photo Gallery', 'wptbt-tours'); ?>
                                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                </div>

                                <!-- Tab: Itinerary -->
                                <?php if (!empty($itinerary)) : ?>
                                    <div class="tab-content" id="tab-itinerary">
                                        <div class="mb-8 relative">
                                            <!-- Timeline integrated header -->
                                            <div class="timeline-item relative flex items-start mb-8">
                                                <!-- Header Timeline Marker - matches the day markers -->
                                                <div class="timeline-marker relative z-10 flex-shrink-0">
                                                    <div class="w-16 h-16 bg-gradient-to-br from-stone-600 to-stone-700 border-4 border-white rounded-full flex items-center justify-center shadow-lg">
                                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                
                                                <!-- Header Content -->
                                                <div class="timeline-content flex-1 ml-8">
                                                    <h2 class="text-2xl md:text-3xl font-bold text-stone-900 leading-tight mb-2"><?php _e('Detailed Itinerary', 'wptbt-tours'); ?></h2>
                                                    <p class="text-stone-600 text-sm"><?php printf(__('Follow your %d-day journey step by step', 'wptbt-tours'), count($itinerary)); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Interactive Route Map usando el sistema oficial -->
                                        <?php
                                        // Prepare points from itinerary locations
                                        $tour_locations = [];
                                        $has_locations = false;
                                        
                                        foreach ($itinerary as $day_index => $day) {
                                            if (isset($day['locations']) && is_array($day['locations'])) {
                                                foreach ($day['locations'] as $location) {
                                                    if (!empty($location['name']) && !empty($location['lat']) && !empty($location['lng'])) {
                                                        $lat = floatval($location['lat']);
                                                        $lng = floatval($location['lng']);
                                                        
                                                        $tour_locations[] = [
                                                            'lat' => $lat,
                                                            'lng' => $lng,
                                                            'title' => esc_html($location['name']),
                                                            'description' => sprintf(__('Day %d: %s', 'wptbt-tours'), $day_index + 1, esc_html($day['title'] ?: 'Day ' . ($day_index + 1))),
                                                            'day' => $day_index + 1
                                                        ];
                                                        $has_locations = true;
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                        
                                        <?php if ($has_locations) : ?>
                                            <div class="mb-10 bg-blue-50/30 rounded-2xl p-6 border border-blue-200/50">
                                                <div class="flex items-center mb-4">
                                                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center mr-3">
                                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                                        </svg>
                                                    </div>
                                                    <h3 class="text-xl font-bold text-stone-900"><?php _e('Interactive Tour Route', 'wptbt-tours'); ?></h3>
                                                </div>
                                                <p class="text-stone-600 mb-6"><?php _e('Explore the complete route of your journey with all the stops and destinations mapped out for you.', 'wptbt-tours'); ?></p>
                                                
                                                <?php
                                                // Use the official component system with manual initialization
                                                echo wptbt_interactive_map_component([
                                                    // Configuración específica para tours - sin título ni subtítulo
                                                    'title' => '', // Vacío para tours
                                                    'subtitle' => '', // Vacío para tours  
                                                    'description' => '', // Vacío para tours
                                                    'latitude' => !empty($tour_locations) ? $tour_locations[0]['lat'] : -13.53168,
                                                    'longitude' => !empty($tour_locations) ? $tour_locations[0]['lng'] : -71.96741,
                                                    'markerTitle' => get_the_title() . ' - Tour Route',
                                                    'markerDescription' => __('Complete tour route with all stops', 'wptbt-tours'),
                                                    'pointsOfInterest' => $tour_locations,
                                                    'zoom' => 10,
                                                    'mapHeight' => 400,
                                                    'accentColor' => '#3B82F6',
                                                    'secondaryColor' => '#10B981',
                                                    'backgroundColor' => '#F8FAFC',
                                                    'textColor' => '#1F2937',
                                                    'showDirections' => true,
                                                    'enableFullscreen' => true,
                                                    'enableZoomControls' => true,
                                                    'enableClustering' => false,
                                                    'mapProvider' => 'osm',
                                                    // Marcar como mapa de tour para identificarlo
                                                    'mapContext' => 'tour'
                                                ], [
                                                    'id' => 'tour-route-map-' . get_the_ID(), // ID único para el mapa del tour
                                                    'data-intersect-once' => 'false', // Disable auto-initialization
                                                    'data-tab-map' => 'true' // Marcar como mapa en tab para manejo especial
                                                ]);
                                                ?>
                                                
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- El mapa maneja automáticamente el redimensionamiento con ResizeObserver e IntersectionObserver -->
                                        
                                        <!-- Timeline Container -->
                                        <div class="timeline-container relative">
                                            <!-- Línea vertical principal -->
                                            <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gradient-to-b from-stone-300 via-stone-200 to-stone-300"></div>
                                            
                                            <div class="space-y-5">
                                                <?php foreach ($itinerary as $index => $day) : ?>
                                                    <!-- Day Header Timeline Item -->
                                                    <div class="timeline-item timeline-day relative flex items-start group">
                                                        <!-- Timeline Marker with Progress -->
                                                        <div class="timeline-marker relative z-10 flex-shrink-0">
                                                            <div class="w-16 h-16 bg-gradient-to-br from-stone-600 to-stone-700 border-4 border-white rounded-full flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 relative">
                                                                <!-- Day number as subtle overlay -->
                                                                <div class="absolute -top-2 -right-2 w-6 h-6 bg-stone-800 text-white text-xs font-bold rounded-full flex items-center justify-center">
                                                                    <?php echo $index + 1; ?>
                                                                </div>
                                                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                </svg>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Day Header Content -->
                                                        <div class="timeline-content flex-1 ml-6 md:ml-8 pb-8">
                                                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-3">
                                                                <h3 class="text-xl md:text-2xl font-bold text-stone-900 mb-1 sm:mb-0">
                                                                    <?php echo esc_html($day['title'] ?: 'Day ' . ($index + 1)); ?>
                                                                </h3>
                                                                <!-- Progress indicator text -->
                                                                <span class="text-xs font-medium text-stone-500 bg-stone-100 px-2 py-1 rounded-full">
                                                                    <?php echo $index + 1; ?> of <?php echo count($itinerary); ?>
                                                                </span>
                                                            </div>
                                                            
                                                            <?php if (!empty($day['date_label'] ?? '')) : ?>
                                                                <p class="text-stone-600 text-sm md:text-base mb-3">
                                                                    <?php echo esc_html($day['date_label'] ?? ''); ?>
                                                                </p>
                                                            <?php endif; ?>
                                                            
                                                            <?php if (!empty($day['description'] ?? '')) : ?>
                                                                <div class="text-stone-700 leading-relaxed prose prose-sm max-w-none mb-4 text-sm md:text-base">
                                                                    <?php echo wp_kses_post($day['description'] ?? ''); ?>
                                                                </div>
                                                            <?php endif; ?>
                                                            
                                                            <!-- Meals and Accommodation - Mobile optimized -->
                                                            <?php if (!empty($day['meals'] ?? '') || !empty($day['accommodation'] ?? '')) : ?>
                                                                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 mt-3">
                                                                    <?php if (!empty($day['meals'] ?? '')) : ?>
                                                                        <div class="flex items-center text-stone-700 bg-emerald-50 px-3 py-2 rounded-lg border border-emerald-200/50 text-sm">
                                                                            <svg class="w-4 h-4 mr-2 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                                            </svg>
                                                                            <span><strong class="text-emerald-900"><?php _e('Meals:', 'wptbt-tours'); ?></strong> <?php echo esc_html($day['meals'] ?? ''); ?></span>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                    
                                                                    <?php if (!empty($day['accommodation'] ?? '')) : ?>
                                                                        <div class="flex items-center text-stone-700 bg-amber-50 px-3 py-2 rounded-lg border border-amber-200/50 text-sm">
                                                                            <svg class="w-4 h-4 mr-2 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                                            </svg>
                                                                            <span><strong class="text-amber-900"><?php _e('Stay:', 'wptbt-tours'); ?></strong> <?php echo esc_html($day['accommodation'] ?? ''); ?></span>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Schedule Timeline Items - Tighter spacing -->
                                                    <div class="mb-15">
                                                        <?php if (isset($day['schedule']) && !empty($day['schedule'])) : ?>
                                                        <div class="space-y-3 md:space-y-4">
                                                            <?php foreach ($day['schedule'] as $schedule_index => $time_slot) : ?>
                                                                <?php if (!empty($time_slot['time_range'] ?? '') || !empty($time_slot['time'] ?? '') || !empty($time_slot['activity'] ?? '')) : ?>
                                                                    <div class="timeline-item timeline-schedule relative flex items-start group ml-6 md:ml-8">
                                                                        <!-- Simple Timeline Dot -->
                                                                        <div class="timeline-marker relative z-10 flex-shrink-0">
                                                                            <div class="-ml-1 w-2 h-2 md:w-3 md:h-3 bg-stone-400 rounded-full mt-2 group-hover:bg-stone-500 transition-colors duration-300"></div>
                                                                        </div>
                                                                        
                                                                        <!-- Schedule Content - Mobile optimized -->
                                                                        <div class="timeline-content flex-1 ml-4 md:ml-6 pb-2">
                                                                            <div class="flex flex-col gap-2 md:flex-row md:items-start md:gap-3">
                                                                                <!-- Time Badge - More prominent on mobile -->
                                                                                <div class="flex-shrink-0">
                                                                                    <div class="bg-stone-200 text-stone-800 px-2 py-1 rounded text-xs md:text-sm font-semibold">
                                                                                        <?php echo esc_html(($time_slot['time_range'] ?? '') ?: ($time_slot['time'] ?? '') ?: ''); ?>
                                                                                    </div>
                                                                                </div>
                                                                                
                                                                                <!-- Activity Content - Better hierarchy -->
                                                                                <div class="flex-1">
                                                                                    <div class="text-stone-600 leading-relaxed text-xs md:text-sm">
                                                                                        <?php echo wp_kses_post($time_slot['activity'] ?? ''); ?>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Tab: Incluido y No Incluido -->
                                <?php if ($includes || $excludes) : ?>
                                    <div class="tab-content" id="tab-includes">
                                        <div class="mb-8">
                                            <span class="text-sm font-medium text-stone-600 uppercase tracking-wider mb-2 block">Package Details</span>
                                            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6"><?php _e("What's Included", 'wptbt-tours'); ?></h2>
                                        </div>
                                        <div class="space-y-8">
                                            <!-- Primera fila: Included y Not Included -->
                                            <?php if ($includes || $excludes) : ?>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                                    <?php if ($includes) : ?>
                                                        <div class="bg-emerald-50/50 rounded-2xl p-6 border border-emerald-200/50 hover:shadow-lg transition-shadow">
                                                            <h3 class="text-lg font-medium text-stone-900 mb-4 flex items-center">
                                                                <div class="w-8 h-8 bg-emerald-600 rounded-xl flex items-center justify-center mr-3">
                                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"></path>
                                                                    </svg>
                                                                </div>
                                                                <?php _e('Included', 'wptbt-tours'); ?>
                                                            </h3>
                                                            <ul class="space-y-3 text-stone-700">
                                                                <?php if (is_array($includes)) : ?>
                                                                    <?php foreach ($includes as $item) : ?>
                                                                        <li class="flex items-start">
                                                                            <svg class="w-4 h-4 text-emerald-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"></path>
                                                                            </svg>
                                                                            <span><?php echo esc_html($item); ?></span>
                                                                        </li>
                                                                    <?php endforeach; ?>
                                                                <?php else : ?>
                                                                    <li><?php echo wpautop(esc_html($includes)); ?></li>
                                                                <?php endif; ?>
                                                            </ul>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if ($excludes) : ?>
                                                        <div class="bg-rose-50/50 rounded-2xl p-6 border border-rose-200/50 hover:shadow-lg transition-shadow">
                                                            <h3 class="text-lg font-medium text-stone-900 mb-4 flex items-center">
                                                                <div class="w-8 h-8 bg-rose-600 rounded-xl flex items-center justify-center mr-3">
                                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"></path>
                                                                    </svg>
                                                                </div>
                                                                <?php _e('Not Included', 'wptbt-tours'); ?>
                                                            </h3>
                                                            <ul class="space-y-3 text-stone-700">
                                                                <?php if (is_array($excludes)) : ?>
                                                                    <?php foreach ($excludes as $item) : ?>
                                                                        <li class="flex items-start">
                                                                            <svg class="w-4 h-4 text-rose-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"></path>
                                                                            </svg>
                                                                            <span><?php echo esc_html($item); ?></span>
                                                                        </li>
                                                                    <?php endforeach; ?>
                                                                <?php else : ?>
                                                                    <li><?php echo wpautop(esc_html($excludes)); ?></li>
                                                                <?php endif; ?>
                                                            </ul>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>


                                        </div>
                                    </div>
                                <?php endif; ?>


                                <!-- Tab: Preparation -->
                                <?php if ($recommendations || $what_to_bring) : ?>
                                    <div class="tab-content" id="tab-preparation">
                                        <div class="mb-8">
                                            <span class="text-sm font-medium text-stone-600 uppercase tracking-wider mb-2 block">Get Ready</span>
                                            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6"><?php _e('Tour Preparation', 'wptbt-tours'); ?></h2>
                                            <p class="text-gray-600 text-lg leading-relaxed"><?php _e('Everything you need to know to prepare for an amazing tour experience.', 'wptbt-tours'); ?></p>
                                        </div>
                                        
                                        <div class="space-y-8">
                                            <!-- Recommendations Section -->
                                            <?php if ($recommendations) : ?>
                                                <div class="bg-blue-50/50 rounded-2xl p-6 border border-blue-200/50 hover:shadow-lg transition-shadow">
                                                    <h3 class="text-lg font-medium text-stone-900 mb-4 flex items-center">
                                                        <div class="w-8 h-8 bg-blue-600 rounded-xl flex items-center justify-center mr-3">
                                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                                            </svg>
                                                        </div>
                                                        <?php _e('Important Recommendations', 'wptbt-tours'); ?>
                                                    </h3>
                                                    <ul class="space-y-3 text-stone-700">
                                                        <?php if (is_array($recommendations)) : ?>
                                                            <?php foreach ($recommendations as $item) : ?>
                                                                <li class="flex items-start">
                                                                    <svg class="w-4 h-4 text-blue-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                    </svg>
                                                                    <span><?php echo esc_html($item); ?></span>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        <?php else : ?>
                                                            <li><?php echo wpautop(esc_html($recommendations)); ?></li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>

                                            <!-- What to Bring Section -->
                                            <?php if ($what_to_bring) : ?>
                                                <div class="bg-teal-50/50 rounded-2xl p-6 border border-teal-200/50 hover:shadow-lg transition-shadow">
                                                    <h3 class="text-lg font-medium text-stone-900 mb-4 flex items-center">
                                                        <div class="w-8 h-8 bg-teal-600 rounded-xl flex items-center justify-center mr-3">
                                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                            </svg>
                                                        </div>
                                                        <?php _e('What to Bring', 'wptbt-tours'); ?>
                                                    </h3>
                                                    <ul class="grid grid-cols-1 md:grid-cols-2 gap-3 text-stone-700">
                                                        <?php if (is_array($what_to_bring)) : ?>
                                                            <?php foreach ($what_to_bring as $item) : ?>
                                                                <li class="flex items-start">
                                                                    <svg class="w-4 h-4 text-teal-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                    </svg>
                                                                    <span><?php echo esc_html($item); ?></span>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        <?php else : ?>
                                                            <li class="md:col-span-2"><?php echo wpautop(esc_html($what_to_bring)); ?></li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Tab: Gallery -->
                                <?php if (isset($gallery_data['images']) && !empty($gallery_data['images'])) : ?>
                                    <div class="tab-content" id="tab-gallery">
                                        <div class="mb-8">
                                            <span class="text-sm font-medium text-red-600 uppercase tracking-wide mb-2 block">Visual Tour</span>
                                            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6"><?php _e('Photo Gallery', 'wptbt-tours'); ?></h2>
                                            <p class="text-gray-600 text-lg leading-relaxed"><?php _e('Explore our stunning collection of images showcasing the beauty and experiences of this tour.', 'wptbt-tours'); ?></p>
                                        </div>
                                        
                                        <?php if (!empty($gallery_data['images'])) : ?>
                                            <?php
                                            // Usar el shortcode de galería existente con configuración para tours
                                            $gallery_image_ids = implode(',', $gallery_data['images']);
                                            
                                            // Shortcode optimizado para tours de viajes - sin títulos extras
                                            echo do_shortcode('[wptbt_gallery 
                                                ids="' . $gallery_image_ids . '" 
                                                title="" 
                                                subtitle="" 
                                                description="" 
                                                columns="3" 
                                                display_mode="masonry" 
                                                hover_effect="zoom" 
                                                background_color="#FFFFFF" 
                                                text_color="#1F2937" 
                                                accent_color="#DC2626" 
                                                secondary_color="#059669" 
                                                full_width="false" 
                                                enable_lightbox="true" 
                                                spacing="20"
                                            ]');
                                            ?>
                                        <?php else : ?>
                                            <!-- Mensaje cuando no hay imágenes -->
                                            <div class="text-center py-12">
                                                <div class="max-w-md mx-auto">
                                                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <h3 class="text-lg font-medium text-gray-900 mb-2"><?php _e('No images available', 'wptbt-tours'); ?></h3>
                                                    <p class="text-gray-600"><?php _e('Gallery images will be displayed here once they are added to this tour.', 'wptbt-tours'); ?></p>
                                                    <?php if (current_user_can('edit_posts')) : ?>
                                                        <a href="<?php echo esc_url(get_edit_post_link()); ?>" 
                                                           class="inline-flex items-center mt-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                            </svg>
                                                            <?php _e('Add Images', 'wptbt-tours'); ?>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="sticky top-8 space-y-8">
                            
                            <!-- Pricing Card -->
                            <div class="bg-white rounded-2xl border border-stone-200/50 overflow-hidden shadow-sm">
                                <div class="bg-gradient-to-r from-stone-700 to-stone-800 p-6 text-white">
                                    <h3 class="text-xl font-semibold mb-2"><?php _e('Tour Pricing', 'wptbt-tours'); ?></h3>
                                    <?php if ($pricing_data['duration']) : ?>
                                        <p class="text-stone-200"><?php echo esc_html($pricing_data['duration']); ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="p-6 space-y-4">
                                    <?php if ($pricing_data['promotion'] && $pricing_data['original']) : ?>
                                        <div class="text-center">
                                            <div class="text-sm text-gray-600 font-medium mb-1"><?php _e('Special Offer', 'wptbt-tours'); ?></div>
                                            <div class="text-lg text-gray-500 line-through">
                                                <?php echo $pricing_data['symbol'] . $pricing_data['original']; ?>
                                            </div>
                                            <div class="text-2xl font-bold text-gray-900">
                                                <?php echo $pricing_data['symbol'] . $pricing_data['promotion']; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($pricing_data['international']) : ?>
                                        <div class="flex justify-between items-center p-4 bg-stone-50/50 rounded-xl border border-stone-200/30">
                                            <span class="text-sm font-medium text-stone-700"><?php _e('International', 'wptbt-tours'); ?></span>
                                            <span class="font-semibold text-stone-900"><?php echo $pricing_data['symbol'] . $pricing_data['international']; ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($pricing_data['national']) : ?>
                                        <div class="flex justify-between items-center p-4 bg-stone-50/50 rounded-xl border border-stone-200/30">
                                            <span class="text-sm font-medium text-stone-700"><?php _e('National', 'wptbt-tours'); ?></span>
                                            <span class="font-semibold text-stone-900"><?php echo $pricing_data['symbol'] . $pricing_data['national']; ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <a href="#book-tour" class="w-full bg-stone-700 hover:bg-stone-800 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-300 text-center block transform hover:scale-[1.02] shadow-md hover:shadow-lg">
                                        <?php _e('Book Now', 'wptbt-tours'); ?>
                                    </a>
                                </div>
                            </div>


                            <!-- Departure Dates -->
                            <?php if (!empty($departure_dates) && is_array($departure_dates)) : ?>
                                <div class="bg-white rounded-2xl border border-stone-200/50 overflow-hidden shadow-sm">
                                    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 p-6 text-white">
                                        <h3 class="text-xl font-semibold"><?php _e('Available Dates', 'wptbt-tours'); ?></h3>
                                    </div>
                                    
                                    <div class="p-6">
                                        <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto">
                                            <?php foreach (array_slice($departure_dates, 0, 8) as $date) : ?>
                                                <div class="text-center p-3 bg-emerald-50/50 rounded-xl border border-emerald-200/50 hover:border-emerald-300/50 transition-all duration-200 hover:shadow-sm">
                                                    <div class="text-sm font-semibold text-emerald-800">
                                                        <?php echo date_i18n('M j', strtotime($date)); ?>
                                                    </div>
                                                    <div class="text-xs text-emerald-600">
                                                        <?php echo date_i18n('Y', strtotime($date)); ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <?php if (count($departure_dates) > 8) : ?>
                                            <p class="text-sm text-gray-500 mt-4 text-center">
                                                +<?php echo count($departure_dates) - 8; ?> <?php _e('more dates available', 'wptbt-tours'); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Contact Info -->
                            <?php if ($booking_info['whatsapp'] || $booking_info['phone'] || $booking_info['email']) : ?>
                                <div class="bg-white rounded-2xl border border-stone-200/50 overflow-hidden shadow-sm">
                                    <div class="bg-gradient-to-r from-slate-700 to-stone-700 p-6 text-white">
                                        <h3 class="text-xl font-semibold"><?php _e('Contact Us', 'wptbt-tours'); ?></h3>
                                    </div>
                                    
                                    <div class="p-6 space-y-3">
                                        <?php if ($booking_info['whatsapp']) : ?>
                                            <a href="https://wa.me/<?php echo esc_attr(preg_replace('/[^0-9]/', '', $booking_info['whatsapp'])); ?>" 
                                               class="flex items-center text-gray-700 hover:text-gray-900 transition-colors">
                                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                                </svg>
                                                <?php echo esc_html($booking_info['whatsapp']); ?>
                                            </a>
                                        <?php endif; ?>

                                        <?php if ($booking_info['phone']) : ?>
                                            <a href="tel:<?php echo esc_attr($booking_info['phone']); ?>" 
                                               class="flex items-center text-blue-600 hover:text-blue-700 transition-colors">
                                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                </svg>
                                                <?php echo esc_html($booking_info['phone']); ?>
                                            </a>
                                        <?php endif; ?>

                                        <?php if ($booking_info['email']) : ?>
                                            <a href="mailto:<?php echo esc_attr($booking_info['email']); ?>" 
                                               class="flex items-center text-gray-600 hover:text-gray-700 transition-colors">
                                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                </svg>
                                                <?php echo esc_html($booking_info['email']); ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Booking Section -->
        <section id="book-tour" class="py-20 bg-gradient-to-br from-blue-50 to-teal-50">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto">
                    <div class="text-center mb-12">
                        <h2 class="text-4xl font-bold text-gray-900 mb-4">
                            <?php _e('Book Your Adventure', 'wptbt-tours'); ?>
                        </h2>
                        <p class="text-xl text-gray-600">
                            <?php _e('Ready to start your journey? Contact us now!', 'wptbt-tours'); ?>
                        </p>
                    </div>

                    <?php
                    // Mostrar formulario personalizado SolidJS optimizado para tours
                    if (function_exists('wptbt_display_tour_booking_form')) {
                        // DEBUG: Mostrar información de configuración (solo para administradores)
                        if (current_user_can('manage_options') && function_exists('wptbt_debug_tour_config')) {
                            wptbt_debug_tour_config($tour_id);
                        }
                        
                        // Verificar si hay datos de reserva disponibles
                        $tour_booking_data = WPTBT_Tours::get_tour_booking_form_data($tour_id);
                        if (!empty($tour_booking_data['hours']) || !empty($tour_booking_data['durations'])) {
                            wptbt_display_tour_booking_form();
                        } else {
                            // Mostrar mensaje para configurar el tour
                            ?>
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-6 text-center">
                                <svg class="w-12 h-12 mx-auto text-amber-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <h4 class="text-lg font-medium text-amber-800 mb-2">
                                    <?php _e('Tour Configuration Needed', 'wptbt-tours'); ?>
                                </h4>
                                <p class="text-amber-700 mb-4">
                                    <?php _e('This tour needs available departure times and pricing before reservations can be made.', 'wptbt-tours'); ?>
                                </p>
                                
                                <div class="bg-white p-4 rounded-lg mb-4 text-left">
                                    <h5 class="font-semibold text-amber-800 mb-2">📋 Pasos para configurar:</h5>
                                    <ol class="list-decimal list-inside text-sm text-amber-700 space-y-1">
                                        <li>Haz clic en "Configurar Tour" abajo</li>
                                        <li>Busca los meta boxes: "🕒 Tour Schedule & Availability" y "💰 Booking Prices & Options"</li>
                                        <li>Agrega horarios de salida (ej: 09:00, 14:00)</li>
                                        <li>Agrega precios por duración (ej: 3 días - $299)</li>
                                        <li>Guarda el tour</li>
                                    </ol>
                                </div>
                                
                                <?php if (current_user_can('edit_posts')) : ?>
                                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                        <a href="<?php echo esc_url(get_edit_post_link($tour_id)); ?>" 
                                           class="inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            <?php _e('Configure Tour', 'wptbt-tours'); ?>
                                        </a>
                                        
                                        <?php if (function_exists('wptbt_create_sample_tour_data')) : ?>
                                            <button onclick="createSampleData()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                </svg>
                                                Crear Datos de Ejemplo
                                            </button>
                                            
                                            <script>
                                            function createSampleData() {
                                                if (confirm('¿Crear datos de ejemplo para este tour?')) {
                                                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                                                        method: 'POST',
                                                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                                                        body: 'action=create_sample_tour_data&tour_id=<?php echo $tour_id; ?>&nonce=<?php echo wp_create_nonce('sample_tour_data'); ?>'
                                                    }).then(() => location.reload());
                                                }
                                            }
                                            </script>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php
                        }
                    } else {
                        // Formulario básico de ejemplo
                        ?>
                        <div class="bg-white rounded-2xl shadow-xl p-8">
                            <form class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <?php _e('Your Name', 'wptbt-tours'); ?>
                                        </label>
                                        <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <?php _e('Email', 'wptbt-tours'); ?>
                                        </label>
                                        <input type="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <?php _e('Phone', 'wptbt-tours'); ?>
                                        </label>
                                        <input type="tel" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <?php _e('Number of Travelers', 'wptbt-tours'); ?>
                                        </label>
                                        <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                            <option>1</option>
                                            <option>2</option>
                                            <option>3</option>
                                            <option>4</option>
                                            <option>5+</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <?php _e('Preferred Date', 'wptbt-tours'); ?>
                                    </label>
                                    <input type="date" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <?php _e('Special Requirements / Questions', 'wptbt-tours'); ?>
                                    </label>
                                    <textarea rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"></textarea>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="inline-flex items-center px-8 py-4 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-full transition-all duration-300 transform hover:scale-105 shadow-lg">
                                        <?php _e('Send Booking Request', 'wptbt-tours'); ?>
                                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </section>

        <!-- Related Tours -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">
                    <?php _e('You Might Also Like', 'wptbt-tours'); ?>
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <?php
                    // Tours relacionados
                    $related_args = array(
                        'post_type' => 'tours',
                        'posts_per_page' => 3,
                        'post__not_in' => array($tour_id),
                        'orderby' => 'rand'
                    );

                    if ($destinations && !is_wp_error($destinations)) {
                        $related_args['tax_query'] = array(
                            array(
                                'taxonomy' => 'destinations',
                                'field'    => 'term_id',
                                'terms'    => wp_list_pluck($destinations, 'term_id'),
                            ),
                        );
                    }

                    $related_tours = new WP_Query($related_args);

                    if ($related_tours->have_posts()) :
                        while ($related_tours->have_posts()) : $related_tours->the_post();
                            $related_pricing = WPTBT_Tours::get_tour_pricing_data(get_the_ID());
                            $related_subtitle = get_post_meta(get_the_ID(), '_wptbt_tour_subtitle', true);
                            $related_difficulty = get_post_meta(get_the_ID(), '_tour_difficulty', true);
                            $related_duration = get_post_meta(get_the_ID(), '_tour_duration', true);
                            $related_destinations = get_the_terms(get_the_ID(), 'destinations');
                            $related_location = '';
                            if ($related_destinations && !is_wp_error($related_destinations)) {
                                $related_location = $related_destinations[0]->name;
                            }
                    ?>
                            <!-- Card con el estilo del carousel -->
                            <article class="tour-card bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-2xl relative h-96 md:h-[500px]">
                                <!-- Imagen que ocupa todo el card -->
                                <div class="tour-image absolute inset-0 overflow-hidden">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('large', ['class' => 'w-full h-full object-cover transition-transform duration-700 hover:scale-110']); ?>
                                        </a>
                                    <?php else : ?>
                                        <div class="w-full h-full bg-gradient-to-br from-gray-800 via-gray-700 to-gray-900"></div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Degradado de oscuro a transparente -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                                
                                <!-- Badge de precio especial -->
                                <?php if ($related_pricing['promotion'] && $related_pricing['original']) : ?>
                                    <div class="absolute top-4 right-4 px-3 py-1 rounded-full text-white font-semibold text-sm shadow-md z-10 bg-red-600">
                                        Special Price
                                    </div>
                                <?php endif; ?>



                                <!-- Contenido del card superpuesto -->
                                <div class="tour-content absolute bottom-0 left-0 right-0 p-6 z-10">
                                    <h3 class="tour-title fancy-text text-lg font-bold mb-2 line-clamp-2 hover:text-opacity-80 transition-colors">
                                        <a href="<?php the_permalink(); ?>" class="text-white hover:text-gray-200 transition-colors">
                                            <?php the_title(); ?>
                                        </a>
                                    </h3>

                                    <?php if ($related_subtitle) : ?>
                                        <p class="tour-subtitle text-white/90 text-sm mb-4 line-clamp-3">
                                            <?php echo esc_html($related_subtitle); ?>
                                        </p>
                                    <?php endif; ?>

                                    <!-- Meta información -->
                                    <div class="tour-meta flex items-center justify-between text-sm text-white/80 mb-4">
                                        <?php if ($related_location) : ?>
                                            <span class="flex items-center text-white/80">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                <?php echo esc_html($related_location); ?>
                                            </span>
                                        <?php endif; ?>

                                        <?php if ($related_difficulty) : ?>
                                            <span class="flex items-center text-white/80">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                </svg>
                                                <?php echo esc_html($related_difficulty); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Botón CTA y Precio -->
                                    <div class="flex items-center justify-between">
                                        <a href="<?php the_permalink(); ?>" class="tour-cta-btn inline-flex items-center justify-center px-6 py-2 rounded-lg text-white font-medium transition-all duration-300 hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 bg-red-600 hover:bg-red-700">
                                            <?php _e('View Tour', 'wptbt-tours'); ?>
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                            </svg>
                                        </a>
                                        
                                        <?php if ($related_pricing['best_price']) : ?>
                                            <div class="text-white font-bold">
                                                <?php if ($related_pricing['promotion'] && $related_pricing['original']) : ?>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-lg font-bold"><?php echo $related_pricing['symbol'] . $related_pricing['promotion']; ?></span>
                                                        <span class="text-sm line-through opacity-75"><?php echo $related_pricing['symbol'] . $related_pricing['original']; ?></span>
                                                    </div>
                                                <?php else : ?>
                                                    <span class="text-lg font-bold"><?php echo $related_pricing['symbol'] . $related_pricing['best_price']; ?></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </article>
                    <?php
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>
            </div>
        </section>

    <?php endwhile; ?>
</main>

<!-- Floating Action Button (Mobile) -->
<div class="fixed bottom-6 right-6 z-50 md:hidden">
    <a href="#book-tour" class="flex items-center justify-center w-14 h-14 bg-orange-500 hover:bg-orange-600 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
    </a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gallery slider functionality
    const slides = document.querySelectorAll('.gallery-slide');
    const dots = document.querySelectorAll('.gallery-dot');
    const prevBtn = document.querySelector('.gallery-prev');
    const nextBtn = document.querySelector('.gallery-next');
    let currentSlide = 0;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.toggle('opacity-100', i === index);
            slide.classList.toggle('opacity-0', i !== index);
        });
        
        dots.forEach((dot, i) => {
            dot.classList.toggle('bg-white', i === index);
            dot.classList.toggle('bg-white/30', i !== index);
        });
        
        currentSlide = index;
    }

    // Auto-advance slides
    if (slides.length > 1) {
        setInterval(() => {
            const nextIndex = (currentSlide + 1) % slides.length;
            showSlide(nextIndex);
        }, 5000);
    }

    // Manual navigation
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => showSlide(index));
    });

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            const prevIndex = currentSlide === 0 ? slides.length - 1 : currentSlide - 1;
            showSlide(prevIndex);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            const nextIndex = (currentSlide + 1) % slides.length;
            showSlide(nextIndex);
        });
    }

    // Tab system functionality
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetTab = button.dataset.tab;
            
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button and corresponding content
            button.classList.add('active');
            const targetContent = document.getElementById(`tab-${targetTab}`);
            if (targetContent) {
                targetContent.classList.add('active');
            }
            
            // Smooth scroll to content if needed
            const tabNavigation = document.querySelector('.tab-navigation');
            if (tabNavigation) {
                tabNavigation.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    });

    // Gallery functionality now handled by SolidJS component

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fadeInUp');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe elements for animation
    document.querySelectorAll('.prose, .tour-card, .bg-white').forEach(el => {
        observer.observe(el);
    });
});
</script>

<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeInUp {
    animation: fadeInUp 0.6s ease-out forwards;
}

/* Tab system styles */
.tab-navigation {
    backdrop-filter: blur(10px);
}

.tab-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 16px 20px;
    border: none;
    background: transparent;
    color: #78716c;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-weight: 500;
    border-radius: 12px;
    position: relative;
    white-space: nowrap;
    overflow: hidden;
}

.tab-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(120, 113, 108, 0.08), rgba(168, 162, 158, 0.05));
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 12px;
}

.tab-btn:hover {
    color: #57534e;
    transform: translateY(-1px);
}

.tab-btn:hover::before {
    opacity: 1;
}

.tab-btn.active {
    color: #44403c;
    background: rgba(120, 113, 108, 0.08);
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(120, 113, 108, 0.15);
}

.tab-btn.active::before {
    opacity: 1;
    background: linear-gradient(135deg, rgba(120, 113, 108, 0.12), rgba(168, 162, 158, 0.08));
}

.tab-btn .tab-icon {
    font-size: 16px;
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

.tab-btn.active .tab-icon {
    opacity: 1;
}

.tab-btn .tab-text {
    font-weight: inherit;
    letter-spacing: 0.025em;
}

/* Tab content */
.tab-content {
    display: none;
    animation: fadeInUp 0.4s ease-out;
}

.tab-content.active {
    display: block;
}

/* Scrollbar hidden for mobile tab navigation */
.scrollbar-hidden {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hidden::-webkit-scrollbar {
    display: none;
}

/* Gallery lightbox styles */
.lightbox-modal {
    backdrop-filter: blur(4px);
}

.lightbox-image {
    border-radius: 8px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* Enhanced gallery items */
.gallery-item {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.gallery-item:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    transform: translateY(-2px);
}

/* Enhanced schedule styling */
.tab-content .bg-gray-50 {
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
    border: 1px solid #e5e7eb;
}

/* Custom scrollbar for dates */
.max-h-48::-webkit-scrollbar {
    width: 4px;
}

.max-h-48::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 2px;
}

.max-h-48::-webkit-scrollbar-thumb {
    background: #64748b;
    border-radius: 2px;
}

.max-h-48::-webkit-scrollbar-thumb:hover {
    background: #475569;
}

/* Hero parallax effect */
.tour-hero {
    background-attachment: fixed;
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
}

/* Responsive design */
@media (max-width: 768px) {
    .tour-hero {
        background-attachment: scroll;
    }
    
    .tab-btn {
        padding: 12px 16px;
        min-width: 120px;
        justify-content: center;
    }
    
    .tab-btn .tab-text {
        font-size: 14px;
    }
    
    .tab-navigation {
        margin-bottom: 24px;
    }
    
    .tab-navigation .flex {
        padding: 8px;
    }
}

@media (max-width: 640px) {
    .tab-btn {
        flex-direction: column;
        gap: 4px;
        padding: 12px 8px;
        min-width: 80px;
    }
    
    .tab-btn .tab-icon {
        font-size: 16px;
    }
    
    .tab-btn .tab-text {
        font-size: 12px;
    }
}

/* Timeline Styles - Professional Design with Integrated Schedule */
.timeline-container {
    position: relative;
}

.timeline-item {
    position: relative;
}

.timeline-marker {
    position: relative;
    z-index: 10;
}

.timeline-content {
    position: relative;
}

/* Main timeline connector line */
.timeline-container .absolute {
    left: 32px !important;
}

/* Day timeline items */
.timeline-item:not(.timeline-schedule) .timeline-marker > div {
    background: linear-gradient(135deg, #57534e, #44403c);
    border: 4px solid white;
    box-shadow: 0 4px 12px rgba(87, 83, 78, 0.3);
}

/* Schedule timeline items - minimal dots */
.timeline-schedule {
    margin-left: 2rem;
}

.timeline-schedule .timeline-marker > div {
    width: 0.75rem;
    height: 0.75rem;
    background-color: #a8a29e;
    border-radius: 50%;
    margin-top: 0.5rem;
    transition: background-color 0.3s ease;
}

.timeline-schedule:hover .timeline-marker > div {
    background-color: #78716c;
}

/* Subtle connecting line from day to schedule */
.timeline-schedule::before {
    content: '';
    position: absolute;
    left: -32px;
    top: 14px;
    width: 20px;
    height: 2px;
    background: #78716c;
    opacity: 0.6;
}

/* Timeline marker hover effects */
.timeline-item:hover .timeline-marker > div {
    transform: scale(1.05);
    transition: all 0.3s ease;
}

.timeline-item:not(.timeline-schedule):hover .timeline-marker > div {
    box-shadow: 0 8px 25px rgba(87, 83, 78, 0.4);
}

/* Timeline content hover effects */
.timeline-item:hover .timeline-content {
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

/* Schedule content - minimal styling */
.timeline-schedule .timeline-content {
    background: transparent;
}

/* Enhanced responsive timeline */
@media (max-width: 768px) {
    .timeline-container .absolute {
        left: 32px !important;
    }
    
    /* Day markers - smaller on mobile */
    .timeline-day .timeline-marker > div {
        width: 56px !important;
        height: 56px !important;
    }
    
    /* Day number badge adjustments */
    .timeline-day .timeline-marker .absolute {
        width: 20px !important;
        height: 20px !important;
        font-size: 10px !important;
    }
    
    /* Schedule markers - even smaller on mobile */
    .timeline-schedule .timeline-marker > div {
        width: 8px !important;
        height: 8px !important;
        margin-top: 8px !important;
    }
    
    /* Tighter content spacing on mobile */
    .timeline-content {
        margin-left: 1.5rem !important;
    }
    
    .timeline-schedule {
        margin-left: 1.5rem !important;
    }
    
    .timeline-schedule .timeline-content {
        margin-left: 1rem !important;
    }
    
    /* Connecting line adjustments */
    .timeline-schedule::before {
        left: -24px;
        width: 12px;
        top: 12px;
    }
    
    /* Better text hierarchy on mobile */
    .timeline-day h3 {
        font-size: 1.25rem !important;
        line-height: 1.3 !important;
    }
    
}

/* Extra small screens */
@media (max-width: 480px) {
    .timeline-container .absolute {
        left: 28px !important;
    }
    
    .timeline-day .timeline-marker > div {
        width: 48px !important;
        height: 48px !important;
    }
    
    .timeline-schedule {
        margin-left: 1rem !important;
    }
    
    .timeline-content {
        margin-left: 1.25rem !important;
    }
}

/* Enhanced hover effects */
.tour-content-tabs .tab-content > div > div {
    transition: all 0.3s ease;
}

.tour-content-tabs .hover\:shadow-lg:hover {
    transform: translateY(-2px);
}

/* Loading animation for tab content */
@keyframes tabFadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.tab-content.active {
    animation: tabFadeIn 0.3s ease-out;
}

/* Simple gallery button styles */
.hero-gallery-open {
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}

.hero-gallery-open:hover {
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
}
</style>

<script>
// Simple tour page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('Tour page loaded successfully');
});
</script>

<?php get_footer(); ?>