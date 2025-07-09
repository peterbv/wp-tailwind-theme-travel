<?php

/**
 * Template para mostrar servicios individuales
 * Versión optimizada con formulario de reserva integrado y múltiples precios
 * MEJORADO: Mayor visibilidad de precios y mejor UX
 *
 * @package WPTBT
 */

get_header();

/**
 * Obtener precios de un servicio (compatibilidad con formato antiguo y nuevo)
 */
function get_service_prices_for_single($post_id)
{
    // Primero intentar obtener el formato nuevo (múltiples precios)
    $prices = get_post_meta($post_id, '_wptbt_service_prices', true);
    
    if (!empty($prices) && is_array($prices)) {
        return $prices;
    }
    
    // Si no existe el formato nuevo, usar el formato antiguo
    $legacy_prices = [];
    
    $precio_duracion1 = get_post_meta($post_id, '_wptbt_service_duration1', true);
    $precio_valor1 = get_post_meta($post_id, '_wptbt_service_price1', true);
    $precio_duracion2 = get_post_meta($post_id, '_wptbt_service_duration2', true);
    $precio_valor2 = get_post_meta($post_id, '_wptbt_service_price2', true);
    
    if (!empty($precio_duracion1) && !empty($precio_valor1)) {
        $legacy_prices[] = ['duration' => $precio_duracion1, 'price' => $precio_valor1];
    }
    if (!empty($precio_duracion2) && !empty($precio_valor2)) {
        $legacy_prices[] = ['duration' => $precio_duracion2, 'price' => $precio_valor2];
    }
    
    // Fallback al precio simple antiguo
    if (empty($legacy_prices)) {
        $precio = get_post_meta($post_id, '_wptbt_service_price', true);
        if (!empty($precio)) {
            $legacy_prices[] = ['duration' => '', 'price' => $precio];
        }
    }
    
    return $legacy_prices;
}

// Obtener metadatos del servicio usando la nueva función
$prices = get_service_prices_for_single(get_the_ID());
$horarios = get_post_meta(get_the_ID(), '_wptbt_service_hours', true);
$subtitle = get_post_meta(get_the_ID(), '_wptbt_service_subtitle', true);

// Mantener compatibilidad para código legacy que pueda referenciar estas variables
$precio_duracion1 = !empty($prices[0]['duration']) ? $prices[0]['duration'] : '';
$precio_valor1 = !empty($prices[0]['price']) ? $prices[0]['price'] : '';
$precio_duracion2 = !empty($prices[1]['duration']) ? $prices[1]['duration'] : '';
$precio_valor2 = !empty($prices[1]['price']) ? $prices[1]['price'] : '';
$precio = !empty($prices[0]['price']) ? $prices[0]['price'] : get_post_meta(get_the_ID(), '_wptbt_service_price', true);
?>

<main id="primary" class="site-main">

    <?php while (have_posts()) : the_post(); ?>

        <!-- Encabezado del servicio con fondo de imagen si existe -->
        <section class="service-header relative bg-spa-secondary overflow-hidden <?php echo has_post_thumbnail() ? 'pt-32 pb-64' : 'py-24'; ?>">

            <?php if (has_post_thumbnail()) : ?>
                <div class="absolute inset-0 z-0">
                    <?php the_post_thumbnail('full', array('class' => 'w-full h-full object-cover')); ?>
                    <div class="absolute inset-0 bg-amber-950 opacity-20"></div>
                </div>
            <?php endif; ?>

            <div class="container mx-auto px-4 relative z-20">
                <a href="<?php echo esc_url(get_post_type_archive_link('servicio')); ?>" class="inline-flex items-center text-white bg-spa-primary bg-opacity-60 hover:bg-opacity-80 px-4 py-2 rounded-full mb-6 transition duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <?php echo esc_html__('Back to Services', 'wptbt-services'); ?>
                </a>

                <div class="text-center max-w-4xl mx-auto">
                    <h1 class="text-4xl md:text-5xl fancy-text font-medium mb-4 <?php echo has_post_thumbnail() ? 'text-white' : 'text-spa-primary'; ?> reveal-item opacity-0 translate-y-8">
                        <?php the_title(); ?>
                    </h1>
                    <?php if (!empty($subtitle)) : ?>
                        <p class="text-xl md:text-2xl mb-6 <?php echo has_post_thumbnail() ? 'text-white' : 'text-spa-primary'; ?> reveal-item opacity-0 translate-y-8" style="margin-top: -8px;">
                            <?php echo esc_html($subtitle); ?>
                        </p>
                    <?php endif; ?>

                    <!-- Precios con contraste fijo y limpio -->
                    <?php if (!empty($prices)) : ?>
                        <div class="mb-8 reveal-item opacity-0 translate-y-8">
                            <!-- Precio "Desde" con contraste garantizado -->
                            <!--<div class="text-center mb-4">
                                <span class="text-sm text-white opacity-90 uppercase tracking-wide">
                                    <?php esc_html_e('Starting from', 'wptbt-services'); ?>
                                </span>
                                <div class="text-3xl md:text-4xl font-bold text-white">
                                    <?php echo esc_html($prices[0]['price']); ?>
                                </div>
                            </div>-->
                            
                            <!-- Precios limpios sin efectos confusos -->
                            <!--<div class="flex flex-wrap justify-center gap-3 max-w-4xl mx-auto">
                                <?php foreach ($prices as $index => $price_item) : ?>
                                    <div class="flex-shrink-0">
                                        <div class="bg-white bg-opacity-95 text-gray-800 px-4 py-3 rounded-lg shadow-md">
                                            <div class="flex items-center space-x-2">
                                                <?php if (!empty($price_item['duration'])) : ?>
                                                    <span class="text-sm font-medium text-gray-600">
                                                        <?php echo esc_html($price_item['duration']); ?><?php esc_html_e('min', 'wptbt-services'); ?>
                                                    </span>
                                                    <span class="text-gray-400">→</span>
                                                <?php endif; ?>
                                                
                                                <span class="text-lg font-bold ">
                                                    <?php echo esc_html($price_item['price']); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>-->
                        </div>
                    <?php endif; ?>

                    <?php if (has_excerpt()) : ?>
                        <div class="prose max-w-2xl mx-auto <?php echo has_post_thumbnail() ? 'text-white' : 'text-gray-600'; ?> reveal-item opacity-0 translate-y-8">
                            <?php the_excerpt(); ?>
                        </div>
                    <?php endif; ?>

                    <!-- NUEVO: Call-to-action prominente -->
                    <div class="mt-8 reveal-item opacity-0 translate-y-8">
                        <a href="#reservar-servicio" class="inline-flex items-center px-8 py-4 bg-spa-accent text-white text-lg font-bold rounded-full shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <?php esc_html_e('Book Now', 'wptbt-services'); ?>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Borde ondulado en la parte inferior -->
            <div class="absolute bottom-0 left-0 right-0 z-10">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100" preserveAspectRatio="none" class="w-full h-20 md:h-24 lg:h-28 hidden md:block">
                    <path fill="white" d="M0,85.7L20,75.4C40,65.1,80,44.6,120,33.9C160,23.2,200,22.4,240,28.6C280,34.8,320,48.1,360,50C400,51.9,440,42.5,480,39.8C520,37.2,560,41.3,600,48.3C640,55.2,680,65,720,62.3C760,59.5,800,44.2,840,41.9C880,39.7,920,50.5,960,54.3C1000,58.2,1040,55.2,1080,58.2C1120,61.1,1160,69.9,1200,73.3C1240,76.8,1280,74.9,1320,70.9C1360,67,1400,61,1420,58L1440,55L1440,100L1420,100C1400,100,1360,100,1320,100C1280,100,1240,100,1200,100C1160,100,1120,100,1080,100C1040,100,1000,100,960,100C920,100,880,100,840,100C800,100,760,100,720,100C680,100,640,100,600,100C560,100,520,100,480,100C440,100,400,100,360,100C320,100,280,100,240,100C200,100,160,100,120,100C80,100,40,100,20,100L0,100Z"></path>
                </svg>

                <!-- Versión simplificada para móviles -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 60" preserveAspectRatio="none" class="w-full h-10 md:hidden">
                    <path fill="white" d="M0,40L50,35C100,30,200,20,300,20C400,20,500,30,550,35L600,40L600,60L550,60C500,60,400,60,300,60C200,60,100,60,50,60L0,60Z"></path>
                </svg>
            </div>
        </section>

        <!-- Sticky pricing bar para móviles - con contraste fijo -->
        <div id="sticky-pricing" class="fixed bottom-0 left-0 right-0 bg-white shadow-xl border-t border-gray-200 p-3 transform translate-y-full transition-transform duration-300 z-50 md:hidden">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-gray-600"><?php esc_html_e('From', 'wptbt-services'); ?></div>
                    <div class="text-xl font-bold ">
                        <?php echo !empty($prices[0]['price']) ? esc_html($prices[0]['price']) : ''; ?>
                    </div>
                </div>
                <a href="#reservar-servicio" class="px-4 py-2 bg-amber-600 text-white font-semibold rounded-lg shadow-md hover:bg-amber-700 transition-colors">
                    <?php esc_html_e('Book', 'wptbt-services'); ?>
                </a>
            </div>
        </div>

        <!-- Detalles del servicio -->
        <section class="service-details py-16 bg-white relative">
            <!-- Si hay imagen destacada, crear un efecto de superposición -->
            <?php if (has_post_thumbnail()) : ?>
                <div class="container mx-auto px-4 -mt-48 mb-16 relative z-20">
                    <div class="bg-white rounded-lg shadow-xl p-8 max-w-4xl mx-auto">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Tarjeta de información rápida SIEMPRE VISIBLE -->
                            <div class="md:col-span-1 bg-spa-secondary rounded-lg p-6 reveal-item opacity-0 translate-y-8">
                                <h3 class="text-xl fancy-text font-medium mb-4 text-spa-primary">
                                    <?php esc_html_e('Quick Information', 'wptbt-services'); ?>
                                </h3>

                                <ul class="space-y-4">
                                    <!-- MEJORADO: Precios siempre visibles -->

                                    <?php if (!empty($prices)) : ?>
                                        <li class="flex items-start">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-spa-accent mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div class="flex-1">
                                                <span class="block text-sm text-gray-500 mb-2"><?php esc_html_e('Available options:', 'wptbt-services'); ?></span>
                                                <div class="flex flex-wrap gap-2">
                                                    <?php foreach ($prices as $price_item) : ?>
                                                        <div class="flex justify-between items-center bg-white px-3 py-2 rounded-md shadow-sm border border-amber-200">
                                                            <?php if (!empty($price_item['duration'])) : ?>
                                                                <span class="text-sm font-medium text-gray-700">
                                                                    <?php echo esc_html($price_item['duration']); ?> <?php esc_html_e('min', 'wptbt-services'); ?>.
                                                                </span>
                                                            <?php else : ?>
                                                                <span class="text-sm font-medium text-gray-700">
                                                                    <?php esc_html_e('Standard', 'wptbt-services'); ?>
                                                                </span>
                                                            <?php endif; ?>
                                                            <!--<span class="font-bold  text-lg">
                                                                <?php echo esc_html($price_item['price']); ?>
                                                            </span>-->
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (!empty($horarios) && is_array($horarios)) : ?>
                                        <li class="flex items-start">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-spa-accent mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div>
                                                <span class="block text-sm text-gray-500 mb-2"><?php esc_html_e('Available hours:', 'wptbt-services'); ?></span>
                                                <div class="flex flex-wrap gap-2">
                                                    <?php foreach ($horarios as $hora) : ?>
                                                        <span class="bg-white px-2 py-1 text-xs rounded font-medium text-spa-primary shadow-sm">
                                                            <?php echo esc_html($hora); ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endif; ?>

                                    <li class="flex items-start">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-spa-accent mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <div>
                                            <span class="block text-sm text-gray-500"><?php esc_html_e('Benefits:', 'wptbt-services'); ?></span>
                                            <span class="font-medium"><?php esc_html_e('Relaxing environment, premium products', 'wptbt-services'); ?></span>
                                        </div>
                                    </li>
                                </ul>

                                <!-- Botón de reserva con color fijo -->
                                <a href="#reservar-servicio" class="mt-6 inline-block w-full px-4 py-3 bg-amber-600 text-white text-center font-semibold rounded-lg transition-all duration-300 hover:bg-amber-700 hover:shadow-lg hover:transform hover:scale-105">
                                    <span class="flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <?php esc_html_e('Book Now', 'wptbt-services'); ?>
                                    </span>
                                </a>
                            </div>

                            <!-- Contenido principal del servicio -->
                            <div class="md:col-span-2 reveal-item opacity-0 translate-y-8">
                                <div class="prose max-w-none">
                                    <?php the_content(); ?>
                                </div>

                                <?php
                                // Obtener categorías o etiquetas relacionadas si existen
                                $categories = get_the_category();
                                if (!empty($categories)) :
                                ?>
                                    <div class="mt-8 pt-6 border-t border-gray-200">
                                        <h3 class="text-lg font-medium text-spa-primary mb-3"><?php esc_html_e('Categories', 'wptbt-services'); ?></h3>
                                        <div class="flex flex-wrap gap-2">
                                            <?php foreach ($categories as $category) : ?>
                                                <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="px-3 py-1 bg-spa-secondary rounded-full text-sm text-spa-primary hover:bg-spa-accent hover:text-white transition duration-300">
                                                    <?php echo esc_html($category->name); ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <!-- Si no hay imagen destacada, mostrar el contenido normal -->
                <div class="container mx-auto px-4">
                    <div class="max-w-4xl mx-auto">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <!-- Tarjeta de información rápida -->
                            <div class="md:col-span-1 bg-spa-secondary rounded-lg p-6 reveal-item opacity-0 translate-y-8">
                                <h3 class="text-xl fancy-text font-medium mb-4 text-spa-primary">
                                    <?php esc_html_e('Quick Information', 'wptbt-services'); ?>
                                </h3>

                                <ul class="space-y-4">
                                    <?php if (!empty($prices)) : ?>
                                        <li class="flex items-start">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-spa-accent mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div class="flex-1">
                                                <span class="block text-sm text-gray-500 mb-2"><?php esc_html_e('Available options:', 'wptbt-services'); ?></span>
                                                <div class="space-y-2">
                                                    <?php foreach ($prices as $price_item) : ?>
                                                        <div class="flex justify-between items-center bg-white px-3 py-2 rounded-md shadow-sm border border-amber-200">
                                                            <?php if (!empty($price_item['duration'])) : ?>
                                                                <span class="text-sm font-medium text-gray-700">
                                                                    <?php echo esc_html($price_item['duration']); ?> <?php esc_html_e('min', 'wptbt-services'); ?>.
                                                                </span>
                                                            <?php else : ?>
                                                                <span class="text-sm font-medium text-gray-700">
                                                                    <?php esc_html_e('Standard', 'wptbt-services'); ?>
                                                                </span>
                                                            <?php endif; ?>
                                                            <span class="font-bold  text-lg">
                                                                <?php echo esc_html($price_item['price']); ?>
                                                            </span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (!empty($horarios) && is_array($horarios)) : ?>
                                        <li class="flex items-start">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-spa-accent mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div>
                                                <span class="block text-sm text-gray-500 mb-2"><?php esc_html_e('Available hours:', 'wptbt-services'); ?></span>
                                                <div class="flex flex-wrap gap-2">
                                                    <?php foreach ($horarios as $hora) : ?>
                                                        <span class="bg-white px-2 py-1 text-xs rounded font-medium text-spa-primary shadow-sm">
                                                            <?php echo esc_html($hora); ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endif; ?>

                                    <li class="flex items-start">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-spa-accent mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <div>
                                            <span class="block text-sm text-gray-500"><?php esc_html_e('Benefits:', 'wptbt-services'); ?></span>
                                            <span class="font-medium"><?php esc_html_e('Relaxing environment, premium products', 'wptbt-services'); ?></span>
                                        </div>
                                    </li>
                                </ul>

                                <!-- Botón de reserva con color fijo -->
                                <a href="#reservar-servicio" class="mt-6 inline-block w-full px-4 py-3 bg-amber-600 text-white text-center font-semibold rounded-lg transition-all duration-300 hover:bg-amber-700 hover:shadow-lg hover:transform hover:scale-105">
                                    <span class="flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <?php esc_html_e('Book Now', 'wptbt-services'); ?>
                                    </span>
                                </a>
                            </div>

                            <!-- Contenido principal del servicio -->
                            <div class="md:col-span-2 reveal-item opacity-0 translate-y-8">
                                <div class="prose max-w-none">
                                    <?php the_content(); ?>
                                </div>

                                <?php
                                // Obtener categorías o etiquetas relacionadas si existen
                                $categories = get_the_category();
                                if (!empty($categories)) :
                                ?>
                                    <div class="mt-8 pt-6 border-t border-gray-200">
                                        <h3 class="text-lg font-medium text-spa-primary mb-3"><?php esc_html_e('Categories', 'wptbt-services'); ?></h3>
                                        <div class="flex flex-wrap gap-2">
                                            <?php foreach ($categories as $category) : ?>
                                                <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="px-3 py-1 bg-spa-secondary rounded-full text-sm text-spa-primary hover:bg-spa-accent hover:text-white transition duration-300">
                                                    <?php echo esc_html($category->name); ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <!-- Servicios relacionados -->
        <section class="related-services py-16 bg-spa-secondary">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl fancy-text font-medium mb-8 text-center text-spa-primary reveal-item opacity-0 translate-y-8">
                    <?php esc_html_e('Other services that may interest you', 'wptbt-services'); ?>
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <?php
                    // Obtener servicios relacionados por categoría
                    $current_id = get_the_ID();
                    $categories = get_the_category();
                    $category_ids = array();

                    if (!empty($categories)) {
                        foreach ($categories as $category) {
                            $category_ids[] = $category->term_id;
                        }
                    }

                    $args = array(
                        'post_type' => 'servicio',
                        'posts_per_page' => 3,
                        'post__not_in' => array($current_id),
                        'orderby' => 'rand'
                    );

                    if (!empty($category_ids)) {
                        $args['category__in'] = $category_ids;
                    }

                    $related_services = new WP_Query($args);

                    if ($related_services->have_posts()) :
                        $delay = 300;
                        while ($related_services->have_posts()) : $related_services->the_post();
                            // Obtener precios usando la nueva función
                            $rel_prices = get_service_prices_for_single(get_the_ID());
                    ?>
                            <div class="service-card reveal-item opacity-0 translate-y-8 delay-<?php echo $delay; ?>">
                                <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-100 h-full group transition-all duration-300 hover:shadow-lg relative">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="service-thumbnail h-56 overflow-hidden relative">
                                            <?php the_post_thumbnail('medium_large', array('class' => 'w-full h-full object-cover group-hover:scale-110 transition-transform duration-700')); ?>
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-70"></div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="p-6 relative <?php echo has_post_thumbnail() ? '-mt-20' : ''; ?>">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <div class="bg-white rounded-lg shadow-md p-5 mb-4 relative">
                                        <?php endif; ?>

                                        <h3 class="text-xl fancy-text font-medium mb-2 text-spa-primary transition-colors duration-300 group-hover:text-spa-accent">
                                            <?php the_title(); ?>
                                        </h3>

                                        <div class="text-gray-600 text-sm mb-4">
                                            <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                        </div>

                                        <?php if (!empty($rel_prices)) : ?>
                                            <div class="flex flex-wrap gap-2 mb-4">
                                                <?php 
                                                // Mostrar solo los primeros 2 precios para no sobrecargar la tarjeta
                                                $displayed_prices = array_slice($rel_prices, 0, 2);
                                                foreach ($displayed_prices as $price_item) : 
                                                ?>
                                                    <div class="flex justify-between items-center px-3 py-2 bg-amber-50 border border-amber-200 rounded-lg">
                                                        <?php if (!empty($price_item['duration'])) : ?>
                                                            <span class="text-sm font-medium text-gray-700">
                                                                <?php echo esc_html($price_item['duration']); ?> <?php esc_html_e('min', 'wptbt-services'); ?>.
                                                            </span>
                                                        <?php else : ?>
                                                            <span class="text-sm font-medium text-gray-700">
                                                                <?php esc_html_e('Standard', 'wptbt-services'); ?>
                                                            </span>
                                                        <?php endif; ?>
                                                        <span class="font-bold ml-2">
                                                            <?php echo esc_html($price_item['price']); ?>
                                                        </span>
                                                    </div>
                                                <?php endforeach; ?>
                                                
                                                <?php if (count($rel_prices) > 2) : ?>
                                                    <div class="text-xs text-gray-500 text-center mt-2">
                                                        +<?php echo count($rel_prices) - 2; ?> <?php echo esc_html__('more options', 'wptbt-services'); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php else : ?>
                                            <div class="px-3 py-2 bg-gray-100 rounded-lg mb-4 text-center">
                                                <span class="text-sm font-medium text-gray-700">
                                                    <?php esc_html_e('Contact for pricing', 'wptbt-services'); ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (has_post_thumbnail()) : ?>
                                            </div>
                                        <?php endif; ?>

                                        <a href="<?php the_permalink(); ?>" class="inline-block px-4 py-2 bg-amber-600 text-white text-sm font-semibold text-center rounded-lg transition-all duration-300 hover:bg-amber-700 hover:transform hover:scale-105 hover:shadow-md">
                                            <?php esc_html_e('View details', 'wptbt-services'); ?> <span class="ml-1">→</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php
                            $delay = ($delay + 300) % 900;
                        endwhile;
                        wp_reset_postdata();
                    else :
                        ?>
                        <div class="col-span-full text-center">
                            <p class="text-gray-500"><?php esc_html_e('There are no related services available.', 'wptbt-services'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Sección de formulario de reserva -->
        <section id="reservar-servicio" class="py-20 bg-spa-secondary/30 relative overflow-hidden">
            <!-- Elementos decorativos para el fondo -->
            <div class="absolute top-0 right-0 w-64 h-64 rounded-full bg-spa-sage opacity-10 transform translate-x-1/4 -translate-y-1/4"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full bg-spa-rose opacity-10 transform -translate-x-1/4 translate-y-1/4"></div>

            <!-- Bordes decorativos -->
            <div class="absolute top-0 left-0 right-0 z-10 transform rotate-180">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100" preserveAspectRatio="none" class="w-full h-16 md:h-20 fill-current text-white">
                    <path d="M0,85.7L20,75.4C40,65.1,80,44.6,120,33.9C160,23.2,200,22.4,240,28.6C280,34.8,320,48.1,360,50C400,51.9,440,42.5,480,39.8C520,37.2,560,41.3,600,48.3C640,55.2,680,65,720,62.3C760,59.5,800,44.2,840,41.9C880,39.7,920,50.5,960,54.3C1000,58.2,1040,55.2,1080,58.2C1120,61.1,1160,69.9,1200,73.3C1240,76.8,1280,74.9,1320,70.9C1360,67,1400,61,1420,58L1440,55L1440,100L1420,100C1400,100,1360,100,1320,100C1280,100,1240,100,1200,100C1160,100,1120,100,1080,100C1040,100,1000,100,960,100C920,100,880,100,840,100C800,100,760,100,720,100C680,100,640,100,600,100C560,100,520,100,480,100C440,100,400,100,360,100C320,100,280,100,240,100C200,100,160,100,120,100C80,100,40,100,20,100L0,100Z"></path>
                </svg>
            </div>

            <div class="container mx-auto px-4 relative z-10">
                <?php
                // Mostrar el formulario de reserva personalizado
                if (function_exists('wptbt_display_service_booking_form')) {
                    wptbt_display_service_booking_form();
                } else {
                    $service_name = get_the_title();
                    echo do_shortcode('[solid 
                    component="booking-form" 
                    accent-color="#D4B254" 
                    use-single-service="true" 
                    title="' . esc_attr__('Book Now', 'wptbt-services') . ' ' . esc_attr($service_name) . '"]');
                }
                ?>
            </div>

            <!-- Borde inferior decorativo -->
            <div class="absolute bottom-0 left-0 right-0 z-10">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100" preserveAspectRatio="none" class="w-full h-16 md:h-20 fill-current text-white">
                    <path d="M0,85.7L20,75.4C40,65.1,80,44.6,120,33.9C160,23.2,200,22.4,240,28.6C280,34.8,320,48.1,360,50C400,51.9,440,42.5,480,39.8C520,37.2,560,41.3,600,48.3C640,55.2,680,65,720,62.3C760,59.5,800,44.2,840,41.9C880,39.7,920,50.5,960,54.3C1000,58.2,1040,55.2,1080,58.2C1120,61.1,1160,69.9,1200,73.3C1240,76.8,1280,74.9,1320,70.9C1360,67,1400,61,1420,58L1440,55L1440,100L1420,100C1400,100,1360,100,1320,100C1280,100,1240,100,1200,100C1160,100,1120,100,1080,100C1040,100,1000,100,960,100C920,100,880,100,840,100C800,100,760,100,720,100C680,100,640,100,600,100C560,100,520,100,480,100C440,100,400,100,360,100C320,100,280,100,240,100C200,100,160,100,120,100C80,100,40,100,20,100L0,100Z"></path>
                </svg>
            </div>
        </section>

    <?php endwhile; ?>

</main>

<!-- NUEVO: JavaScript para sticky pricing bar y animaciones -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sticky pricing bar para móviles
    const stickyPricing = document.getElementById('sticky-pricing');
    const serviceHeader = document.querySelector('.service-header');
    
    if (stickyPricing && serviceHeader) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) {
                    stickyPricing.classList.remove('translate-y-full');
                } else {
                    stickyPricing.classList.add('translate-y-full');
                }
            });
        }, { threshold: 0.1 });
        
        observer.observe(serviceHeader);
    }
    
    // Smooth scroll para botones de reserva
    document.querySelectorAll('a[href="#reservar-servicio"]').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.getElementById('reservar-servicio');
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Animaciones reveal para elementos
    const revealItems = document.querySelectorAll('.reveal-item');
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.remove('opacity-0', 'translate-y-8');
                entry.target.classList.add('opacity-100', 'translate-y-0');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
    
    revealItems.forEach(item => {
        revealObserver.observe(item);
    });
});
</script>

<?php get_footer(); ?>