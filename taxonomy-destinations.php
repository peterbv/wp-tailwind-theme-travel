<?php
/**
 * Template para mostrar destinos individuales
 * Muestra información del destino y tours asociados
 *
 * @package WPTBT
 */

get_header();

// Obtener información del término actual
$current_term = get_queried_object();
$destination_image = WPTBT_Tours::get_destination_image($current_term->term_id, 'full');
$destination_description = term_description($current_term->term_id, 'destinations');

// Obtener tours de este destino
$tours_query = new WP_Query([
    'post_type' => 'tours',
    'posts_per_page' => -1,
    'tax_query' => [
        [
            'taxonomy' => 'destinations',
            'field'    => 'term_id',
            'terms'    => $current_term->term_id,
        ],
    ],
    'post_status' => 'publish'
]);
?>

<main id="primary" class="site-main">
    
    <!-- Hero Section del Destino -->
    <section class="destination-hero relative h-[calc(100vh-var(--header-height,80px))] min-h-[600px] flex items-end overflow-hidden">
        
        <!-- Imagen de fondo -->
        <div class="absolute inset-0 z-0">
            <?php if ($destination_image && isset($destination_image[0])) : ?>
                <img src="<?php echo esc_url($destination_image[0]); ?>" 
                     alt="<?php echo esc_attr($current_term->name); ?>" 
                     class="w-full h-full object-cover" />
            <?php else : ?>
                <!-- Fondo por defecto si no hay imagen -->
                <div class="w-full h-full bg-gradient-to-br from-blue-800 via-blue-700 to-purple-900"></div>
            <?php endif; ?>
            
            <!-- Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-black/30 to-transparent"></div>
        </div>

        <!-- Contenido del hero -->
        <div class="container mx-auto px-4 relative z-10 pb-16">
            <div class="max-w-4xl">
                <!-- Breadcrumb -->
                <nav class="mb-8">
                    <a href="<?php echo esc_url(home_url()); ?>" 
                       class="inline-flex items-center text-white/70 hover:text-white transition-all duration-300 text-sm backdrop-blur-sm bg-white/10 px-4 py-2 rounded-full border border-white/20">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <?php _e('Back to Home', 'wptbt-tours'); ?>
                    </a>
                </nav>

                <!-- Título del destino -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-3 mb-4">
                        <span class="px-3 py-1 bg-blue-600 text-white text-sm font-medium rounded-full">
                            <?php _e('Destination', 'wptbt-tours'); ?>
                        </span>
                        <span class="text-white/70 text-sm">
                            <?php printf(_n('%d tour available', '%d tours available', $tours_query->found_posts, 'wptbt-tours'), $tours_query->found_posts); ?>
                        </span>
                    </div>
                    
                    <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white leading-tight">
                        <?php echo esc_html($current_term->name); ?>
                    </h1>
                    
                    <?php if ($destination_description) : ?>
                        <div class="text-xl md:text-2xl text-white/90 leading-relaxed max-w-3xl">
                            <?php echo wp_kses_post($destination_description); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-wrap gap-4 mt-8">
                    <button onclick="document.querySelector('.tours-section').scrollIntoView({ behavior: 'smooth' })" 
                            class="bg-white text-gray-900 px-8 py-4 rounded-full font-semibold hover:bg-gray-100 transition-all duration-300 flex items-center">
                        <?php _e('Explore Tours', 'wptbt-tours'); ?>
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de Tours -->
    <?php if ($tours_query->have_posts()) : ?>
        <section class="tours-section py-20 bg-gray-50">
            <div class="container mx-auto px-4">
                
                <!-- Encabezado de sección -->
                <div class="text-center mb-16">
                    <span class="text-sm font-medium text-blue-600 uppercase tracking-wider mb-2 block">
                        <?php _e('Available Tours', 'wptbt-tours'); ?>
                    </span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                        <?php printf(__('Tours in %s', 'wptbt-tours'), esc_html($current_term->name)); ?>
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        <?php printf(__('Discover amazing experiences in %s with our carefully curated tours.', 'wptbt-tours'), esc_html($current_term->name)); ?>
                    </p>
                </div>

                <!-- Grid de Tours -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php while ($tours_query->have_posts()) : $tours_query->the_post(); ?>
                        <?php 
                        $tour_id = get_the_ID();
                        $pricing_data = WPTBT_Tours::get_tour_pricing_data($tour_id);
                        $duration = get_post_meta($tour_id, '_tour_duration', true);
                        $difficulty = get_post_meta($tour_id, '_tour_difficulty', true);
                        ?>
                        
                        <article class="tour-card bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 group">
                            <!-- Imagen del tour -->
                            <div class="relative h-64 overflow-hidden">
                                <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('large', ['class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-300']); ?>
                                    </a>
                                <?php else : ?>
                                    <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Badges -->
                                <div class="absolute top-4 left-4">
                                    <?php if ($difficulty) : ?>
                                        <span class="px-2 py-1 bg-white/90 text-gray-900 text-xs font-medium rounded-full backdrop-blur-sm">
                                            <?php echo esc_html($difficulty); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Contenido de la tarjeta -->
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors">
                                    <a href="<?php the_permalink(); ?>" class="stretched-link">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>
                                
                                <?php if (has_excerpt()) : ?>
                                    <p class="text-gray-600 mb-4 line-clamp-2">
                                        <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                                    </p>
                                <?php endif; ?>

                                <!-- Meta información -->
                                <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                    <?php if ($duration) : ?>
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <?php echo esc_html($duration); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Precio -->
                                <?php if ($pricing_data && isset($pricing_data['adult_price']) && $pricing_data['adult_price'] > 0) : ?>
                                    <div class="flex items-center justify-between">
                                        <span class="text-2xl font-bold text-gray-900">
                                            $<?php echo number_format($pricing_data['adult_price'], 0); ?>
                                        </span>
                                        <span class="text-sm text-gray-500"><?php _e('per person', 'wptbt-tours'); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>
    <?php else : ?>
        <!-- No tours found -->
        <section class="py-20 bg-gray-50">
            <div class="container mx-auto px-4 text-center">
                <div class="max-w-md mx-auto">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.267-5.82-3.271M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">
                        <?php _e('No tours available yet', 'wptbt-tours'); ?>
                    </h3>
                    <p class="text-gray-600">
                        <?php printf(__('Tours for %s are coming soon. Check back later for amazing experiences!', 'wptbt-tours'), esc_html($current_term->name)); ?>
                    </p>
                </div>
            </div>
        </section>
    <?php endif; ?>

</main>

<?php
// Restablecer query
wp_reset_postdata();
get_footer();
?>