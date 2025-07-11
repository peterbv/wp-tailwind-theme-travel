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
$departure_point = get_post_meta($tour_id, '_tour_departure_point', true);
$return_point = get_post_meta($tour_id, '_tour_return_point', true);
$google_maps_url = get_post_meta($tour_id, '_tour_google_maps_url', true);

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

        <!-- Hero Section con galería o imagen destacada -->
        <section class="tour-hero relative h-screen min-h-[600px] flex items-center justify-center overflow-hidden">
            
            <!-- Galería de fondo o imagen destacada -->
            <div class="absolute inset-0 z-0">
                <?php if (isset($gallery_data['use_as_featured']) && !empty($gallery_data['images'])) : ?>
                    <!-- Slider de galería -->
                    <div class="gallery-slider relative w-full h-full">
                        <?php foreach ($gallery_data['images'] as $index => $image_id) : ?>
                            <div class="gallery-slide absolute inset-0 transition-opacity duration-1000 <?php echo $index === 0 ? 'opacity-100' : 'opacity-0'; ?>" data-slide="<?php echo $index; ?>">
                                <?php echo wp_get_attachment_image($image_id, 'full', false, ['class' => 'w-full h-full object-cover']); ?>
                            </div>
                        <?php endforeach; ?>
                        
                        <!-- Navegación de galería -->
                        <?php if (count($gallery_data['images']) > 1) : ?>
                            <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 flex space-x-2 z-20">
                                <?php foreach ($gallery_data['images'] as $index => $image_id) : ?>
                                    <button class="gallery-dot w-3 h-3 rounded-full border-2 border-white transition-all duration-300 <?php echo $index === 0 ? 'bg-white' : 'bg-white/30 hover:bg-white/60'; ?>" data-slide="<?php echo $index; ?>"></button>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Flechas de navegación -->
                            <button class="gallery-prev absolute left-6 top-1/2 transform -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white p-3 rounded-full transition-all duration-300 z-20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <button class="gallery-next absolute right-6 top-1/2 transform -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white p-3 rounded-full transition-all duration-300 z-20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php elseif (has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail('full', ['class' => 'w-full h-full object-cover']); ?>
                <?php else : ?>
                    <!-- Fondo degradado por defecto -->
                    <div class="w-full h-full bg-gradient-to-br from-blue-600 via-teal-500 to-green-500"></div>
                <?php endif; ?>
                
                <!-- Overlay -->
                <div class="absolute inset-0 bg-black/40"></div>
            </div>

            <!-- Contenido del hero -->
            <div class="container mx-auto px-4 relative z-10 text-center text-white">
                <!-- Breadcrumb -->
                <nav class="mb-6">
                    <a href="<?php echo esc_url(get_post_type_archive_link('tours')); ?>" 
                       class="inline-flex items-center text-white/80 hover:text-white transition-colors duration-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <?php _e('Back to Tours', 'wptbt-tours'); ?>
                    </a>
                </nav>

                <!-- Badges del tour -->
                <?php if ($is_featured || $is_popular || $is_new) : ?>
                    <div class="flex justify-center space-x-3 mb-6">
                        <?php if ($is_featured) : ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-500 text-white">
                                ⭐ <?php _e('Featured', 'wptbt-tours'); ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($is_popular) : ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-500 text-white">
                                🔥 <?php _e('Popular', 'wptbt-tours'); ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($is_new) : ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-500 text-white">
                                🆕 <?php _e('New', 'wptbt-tours'); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Título y subtítulo -->
                <h1 class="text-4xl md:text-6xl font-bold mb-4 leading-tight">
                    <?php the_title(); ?>
                </h1>
                
                <?php if ($subtitle) : ?>
                    <p class="text-xl md:text-2xl mb-8 text-white/90 max-w-3xl mx-auto">
                        <?php echo esc_html($subtitle); ?>
                    </p>
                <?php endif; ?>

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
                <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-4">
                    <a href="#book-tour" class="inline-flex items-center px-8 py-4 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-full transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <?php _e('Book This Tour', 'wptbt-tours'); ?>
                    </a>
                    
                    <?php if ($google_maps_url) : ?>
                        <a href="<?php echo esc_url($google_maps_url); ?>" target="_blank" 
                           class="inline-flex items-center px-6 py-3 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white border border-white/30 rounded-full transition-all duration-300">
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
                    
                    <!-- Contenido principal -->
                    <div class="lg:col-span-2 space-y-12">
                        
                        <!-- Descripción -->
                        <div class="prose prose-lg max-w-none">
                            <h2 class="text-3xl font-bold text-gray-900 mb-6"><?php _e('Tour Overview', 'wptbt-tours'); ?></h2>
                            <?php the_content(); ?>
                        </div>

                        <!-- Itinerario -->
                        <?php if (!empty($itinerary)) : ?>
                            <div>
                                <h2 class="text-3xl font-bold text-gray-900 mb-8"><?php _e('Detailed Itinerary', 'wptbt-tours'); ?></h2>
                                <div class="space-y-6">
                                    <?php foreach ($itinerary as $index => $day) : ?>
                                        <div class="bg-gray-50 rounded-xl p-6 border-l-4 border-blue-500">
                                            <div class="flex items-center mb-4">
                                                <div class="flex-shrink-0 w-12 h-12 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold text-lg">
                                                    <?php echo $index + 1; ?>
                                                </div>
                                                <div class="ml-4">
                                                    <h3 class="text-xl font-bold text-gray-900">
                                                        <?php echo esc_html($day['title']); ?>
                                                    </h3>
                                                </div>
                                            </div>
                                            
                                            <div class="ml-16">
                                                <p class="text-gray-700 mb-4">
                                                    <?php echo esc_html($day['description']); ?>
                                                </p>
                                                
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                                    <?php if ($day['meals']) : ?>
                                                        <div class="flex items-center text-gray-600">
                                                            <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                            </svg>
                                                            <strong><?php _e('Meals:', 'wptbt-tours'); ?></strong>
                                                            <span class="ml-1"><?php echo esc_html($day['meals']); ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($day['accommodation']) : ?>
                                                        <div class="flex items-center text-gray-600">
                                                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                            </svg>
                                                            <strong><?php _e('Stay:', 'wptbt-tours'); ?></strong>
                                                            <span class="ml-1"><?php echo esc_html($day['accommodation']); ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Incluido y No Incluido -->
                        <?php if ($includes || $excludes) : ?>
                            <div>
                                <h2 class="text-3xl font-bold text-gray-900 mb-8"><?php _e("What's Included", 'wptbt-tours'); ?></h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    
                                    <?php if ($includes) : ?>
                                        <div class="bg-green-50 rounded-xl p-6 border border-green-200">
                                            <h3 class="text-xl font-bold text-green-800 mb-4 flex items-center">
                                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                <?php _e('Included', 'wptbt-tours'); ?>
                                            </h3>
                                            <div class="prose prose-green text-green-700">
                                                <?php echo wpautop(esc_html($includes)); ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($excludes) : ?>
                                        <div class="bg-red-50 rounded-xl p-6 border border-red-200">
                                            <h3 class="text-xl font-bold text-red-800 mb-4 flex items-center">
                                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                <?php _e('Not Included', 'wptbt-tours'); ?>
                                            </h3>
                                            <div class="prose prose-red text-red-700">
                                                <?php echo wpautop(esc_html($excludes)); ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="sticky top-8 space-y-8">
                            
                            <!-- Pricing Card -->
                            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                                <div class="bg-gradient-to-r from-blue-500 to-teal-500 p-6 text-white">
                                    <h3 class="text-xl font-bold mb-2"><?php _e('Tour Pricing', 'wptbt-tours'); ?></h3>
                                    <?php if ($pricing_data['duration']) : ?>
                                        <p class="text-blue-100"><?php echo esc_html($pricing_data['duration']); ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="p-6 space-y-4">
                                    <?php if ($pricing_data['promotion'] && $pricing_data['original']) : ?>
                                        <div class="text-center">
                                            <div class="text-sm text-red-600 font-medium mb-1">🎯 <?php _e('Special Offer', 'wptbt-tours'); ?></div>
                                            <div class="text-lg text-gray-500 line-through">
                                                <?php echo $pricing_data['symbol'] . $pricing_data['original']; ?>
                                            </div>
                                            <div class="text-3xl font-bold text-red-600">
                                                <?php echo $pricing_data['symbol'] . $pricing_data['promotion']; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($pricing_data['international']) : ?>
                                        <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                            <span class="text-sm font-medium text-blue-800">🌍 <?php _e('International', 'wptbt-tours'); ?></span>
                                            <span class="font-bold text-blue-900"><?php echo $pricing_data['symbol'] . $pricing_data['international']; ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($pricing_data['national']) : ?>
                                        <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                                            <span class="text-sm font-medium text-green-800">🏠 <?php _e('National', 'wptbt-tours'); ?></span>
                                            <span class="font-bold text-green-900"><?php echo $pricing_data['symbol'] . $pricing_data['national']; ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <a href="#book-tour" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-300 text-center block">
                                        <?php _e('Book Now', 'wptbt-tours'); ?>
                                    </a>
                                </div>
                            </div>

                            <!-- Tour Details Card -->
                            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                                <div class="bg-gray-900 p-6 text-white">
                                    <h3 class="text-xl font-bold"><?php _e('Tour Details', 'wptbt-tours'); ?></h3>
                                </div>
                                
                                <div class="p-6 space-y-4">
                                    <?php if ($difficulty) : ?>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600"><?php _e('Difficulty', 'wptbt-tours'); ?></span>
                                            <span class="font-medium">
                                                <?php
                                                $difficulty_labels = [
                                                    'easy' => '🟢 ' . __('Easy', 'wptbt-tours'),
                                                    'moderate' => '🟡 ' . __('Moderate', 'wptbt-tours'),
                                                    'challenging' => '🟠 ' . __('Challenging', 'wptbt-tours'),
                                                    'extreme' => '🔴 ' . __('Extreme', 'wptbt-tours')
                                                ];
                                                echo isset($difficulty_labels[$difficulty]) ? $difficulty_labels[$difficulty] : $difficulty;
                                                ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($min_age) : ?>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600"><?php _e('Min Age', 'wptbt-tours'); ?></span>
                                            <span class="font-medium"><?php echo esc_html($min_age); ?> <?php _e('years', 'wptbt-tours'); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($max_people) : ?>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600"><?php _e('Max Group', 'wptbt-tours'); ?></span>
                                            <span class="font-medium"><?php echo esc_html($max_people); ?> <?php _e('people', 'wptbt-tours'); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($departure_point) : ?>
                                        <div class="border-t pt-4">
                                            <span class="text-gray-600 block mb-2"><?php _e('Departure Point', 'wptbt-tours'); ?></span>
                                            <span class="font-medium"><?php echo esc_html($departure_point); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($return_point) : ?>
                                        <div>
                                            <span class="text-gray-600 block mb-2"><?php _e('Return Point', 'wptbt-tours'); ?></span>
                                            <span class="font-medium"><?php echo esc_html($return_point); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Departure Dates -->
                            <?php if (!empty($departure_dates) && is_array($departure_dates)) : ?>
                                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                                    <div class="bg-teal-500 p-6 text-white">
                                        <h3 class="text-xl font-bold"><?php _e('Available Dates', 'wptbt-tours'); ?></h3>
                                    </div>
                                    
                                    <div class="p-6">
                                        <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto">
                                            <?php foreach (array_slice($departure_dates, 0, 8) as $date) : ?>
                                                <div class="text-center p-2 bg-teal-50 rounded-lg border border-teal-200">
                                                    <div class="text-sm font-medium text-teal-800">
                                                        <?php echo date_i18n('M j', strtotime($date)); ?>
                                                    </div>
                                                    <div class="text-xs text-teal-600">
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
                                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                                    <div class="bg-green-500 p-6 text-white">
                                        <h3 class="text-xl font-bold"><?php _e('Contact Us', 'wptbt-tours'); ?></h3>
                                    </div>
                                    
                                    <div class="p-6 space-y-3">
                                        <?php if ($booking_info['whatsapp']) : ?>
                                            <a href="https://wa.me/<?php echo esc_attr(preg_replace('/[^0-9]/', '', $booking_info['whatsapp'])); ?>" 
                                               class="flex items-center text-green-600 hover:text-green-700 transition-colors">
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
                    // Mostrar formulario personalizado o shortcode
                    if (function_exists('wptbt_display_tour_booking_form')) {
                        wptbt_display_tour_booking_form();
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
                    ?>
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="h-48 overflow-hidden">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('medium_large', ['class' => 'w-full h-full object-cover hover:scale-110 transition-transform duration-700']); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <div class="p-6">
                                    <h3 class="text-xl font-bold text-gray-900 mb-2 hover:text-blue-600 transition-colors">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_title(); ?>
                                        </a>
                                    </h3>

                                    <p class="text-gray-600 text-sm mb-4">
                                        <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                                    </p>

                                    <div class="flex justify-between items-center">
                                        <?php if ($related_pricing['best_price']) : ?>
                                            <div class="text-lg font-bold text-blue-600">
                                                <?php echo $related_pricing['symbol'] . $related_pricing['best_price']; ?>
                                            </div>
                                        <?php endif; ?>

                                        <a href="<?php the_permalink(); ?>" 
                                           class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium transition-colors">
                                            <?php _e('View Details', 'wptbt-tours'); ?>
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
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

@media (max-width: 768px) {
    .tour-hero {
        background-attachment: scroll;
    }
}
</style>

<?php get_footer(); ?>