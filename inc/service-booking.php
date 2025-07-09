<?php

/**
 * Funciones para renderizar el formulario de reserva en la página de servicio individual
 * ACTUALIZADO: Sistema modular Solid.js con múltiples precios y CustomSelect
 * Con soporte completo para internacionalización (i18n)
 * 
 * @package WPTBT
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Renderiza el formulario de reserva para un servicio específico
 * ACTUALIZADO: Ahora usa el sistema de múltiples precios automáticamente
 * 
 * @param int|WP_Post $service_id_or_post ID del servicio o objeto WP_Post
 * @return string HTML del formulario de reserva
 */
function wptbt_render_service_booking_form($service_id_or_post)
{
    // Obtener el objeto post del servicio
    if (is_numeric($service_id_or_post)) {
        $service_post = get_post($service_id_or_post);
    } elseif ($service_id_or_post instanceof WP_Post) {
        $service_post = $service_id_or_post;
    } else {
        global $post;
        $service_post = $post;
    }

    if (!$service_post || $service_post->post_type !== 'servicio') {
        return '<div class="p-4 bg-red-100 text-red-800 rounded-md">Error: Invalid service data</div>';
    }

    $service_id = $service_post->ID;
    $service_title = $service_post->post_title;
    $form_id = 'booking-form-' . uniqid();

    // CORREGIDO: Obtener múltiples precios con formato consistente
    $prices = get_post_meta($service_id, '_wptbt_service_prices', true);
    $durations = [];

    if (!empty($prices) && is_array($prices)) {
        // Usar el nuevo formato de múltiples precios
        foreach ($prices as $price_data) {
            if (!empty($price_data['duration']) && !empty($price_data['price'])) {
                $minutes = intval($price_data['duration']);
                $durations[] = [
                    'duration' => $price_data['duration'],
                    'price' => $price_data['price'],
                    'minutes' => $minutes,
                    'text' => $price_data['duration'] . ' min - ' . $price_data['price'],
                    'value' => $minutes . 'min-' . $price_data['price'] // AGREGADO: Campo faltante
                ];
            }
        }
    } else {
        // Fallback: usar formato anterior
        $duration1 = get_post_meta($service_id, '_wptbt_service_duration1', true) ?: '';
        $price1 = get_post_meta($service_id, '_wptbt_service_price1', true) ?: '';
        $duration2 = get_post_meta($service_id, '_wptbt_service_duration2', true) ?: '';
        $price2 = get_post_meta($service_id, '_wptbt_service_price2', true) ?: '';

        if (!empty($duration1) && !empty($price1)) {
            $minutes1 = intval($duration1);
            $durations[] = [
                'duration' => $duration1,
                'price' => $price1,
                'minutes' => $minutes1,
                'text' => $duration1 . ' - ' . $price1,
                'value' => $minutes1 . 'min-' . $price1
            ];
        }

        if (!empty($duration2) && !empty($price2)) {
            $minutes2 = intval($duration2);
            $durations[] = [
                'duration' => $duration2,
                'price' => $price2,
                'minutes' => $minutes2,
                'text' => $duration2 . ' - ' . $price2,
                'value' => $minutes2 . 'min-' . $price2
            ];
        }
    }

    // Obtener horarios disponibles
    $service_hours = get_post_meta($service_id, '_wptbt_service_hours', true) ?: [];
    $service_subtitle = get_post_meta($service_id, '_wptbt_service_subtitle', true) ?: '';

    // VERIFICACIÓN: Log de debug
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log("Single service {$service_id}: " . count($service_hours) . " hours, " . count($durations) . " durations");
        error_log("Hours: " . print_r($service_hours, true));
        error_log("Durations: " . print_r($durations, true));
    }

    // Preparar la información de servicio para JSON (FORMATO CORREGIDO)
    $service_data = [
        [
            'id' => (string)$service_id, // IMPORTANTE: Convertir a string
            'title' => $service_title,
            'subtitle' => $service_subtitle,
            'hours' => array_values($service_hours), // CORREGIDO: Asegurar array indexado
            'durations' => array_values($durations), // CORREGIDO: Asegurar array indexado
            // Mantener compatibilidad
            'duration1' => !empty($durations[0]) ? $durations[0]['duration'] : '',
            'price1' => !empty($durations[0]) ? $durations[0]['price'] : '',
            'duration2' => !empty($durations[1]) ? $durations[1]['duration'] : '',
            'price2' => !empty($durations[1]) ? $durations[1]['price'] : ''
        ]
    ];

    $json_data = wp_json_encode($service_data);
    $form_email = get_theme_mod('services_booking_form_email', get_option('admin_email'));

    wptbt_load_solid_component('booking-form');

    ob_start();
?>
    <div class="service-booking-form max-w-4xl mx-auto reveal-item opacity-0 translate-y-8 relative">
        <!-- El resto del HTML permanece igual -->
        <div class="absolute top-0 left-0 right-0 -translate-y-1/2 flex justify-center">
            <div class="w-32 h-0.5 bg-spa-accent/30"></div>
        </div>

        <div class="rounded-lg shadow-xl mb-30">
            <div class="bg-spa-secondary/20 p-8 text-center">
                <span class="block text-lg italic font-medium mb-2 text-spa-accent">
                    <?php echo esc_html__('Book your appointment', 'wptbt-services'); ?>
                </span>
                <h3 class="text-3xl fancy-text font-medium mb-6 text-gray-800 relative inline-block">
                    <?php echo esc_html($service_title); ?>
                    <span class="absolute bottom-0 left-0 w-full h-1 mt-2 bg-spa-accent transform" style="transform: scaleX(0.3); transform-origin: center;"></span>
                </h3>
                
                <?php if (!empty($service_subtitle)) : ?>
                    <p class="text-lg text-gray-700 font-medium mb-4">
                        <?php echo esc_html($service_subtitle); ?>
                    </p>
                <?php endif; ?>

                <p class="text-gray-600 max-w-md mx-auto mt-6">
                    <?php echo esc_html(sprintf(__('Complete the form to book your %s session', 'wptbt-services'), $service_title)); ?>
                </p>

                <?php if (!empty($durations)) : ?>
                    <!--<div class="mt-6 flex flex-wrap justify-center gap-3">
                        <?php foreach ($durations as $duration) : ?>
                            <div class="bg-white/70 backdrop-blur-sm px-4 py-2 rounded-full border border-spa-accent/20">
                                <span class="text-sm font-medium text-gray-700">
                                    <?php echo esc_html($duration['text']); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>-->
                <?php endif; ?>
            </div>

            <div class="p-8 bg-white">
                <?php if (empty($service_hours) && empty($durations)) : ?>
                    <!-- Mensaje de error si no hay configuración -->
                    <div class="text-center py-8">
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-6">
                            <svg class="w-12 h-12 mx-auto text-amber-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <h4 class="text-lg font-medium text-amber-800 mb-2">
                                <?php echo esc_html__('Service Configuration Needed', 'wptbt-services'); ?>
                            </h4>
                            <p class="text-amber-700 mb-4">
                                <?php echo esc_html__('This service needs available times and prices before bookings can be made.', 'wptbt-services'); ?>
                            </p>
                            <?php if (current_user_can('edit_posts')) : ?>
                                <a href="<?php echo esc_url(get_edit_post_link($service_id)); ?>" 
                                   class="inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    <?php echo esc_html__('Configure Service', 'wptbt-services'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else : ?>
                    <?php
                    echo wptbt_booking_form_component(
                        [
                            'services' => $service_data,
                            'darkMode' => false,
                            'accentColor' => get_theme_mod('services_booking_form_accent_color', '#D4B254'),
                            'useSingleService' => true,
                            'emailRecipient' => $form_email 
                        ],
                        [
                            'id' => 'solid-booking-form-' . esc_attr($form_id),
                            'class' => 'solid-booking-container',
                        ]
                    );
                    ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="absolute bottom-0 left-0 right-0 translate-y-1/2 flex justify-center">
            <div class="w-32 h-0.5 bg-spa-accent/30"></div>
        </div>
    </div>

    <!-- Debug info para desarrollo -->
    <?php if (defined('WP_DEBUG') && WP_DEBUG && current_user_can('manage_options')) : ?>
        <div class="mt-4 p-4 bg-gray-100 rounded text-xs">
            <details>
                <summary class="cursor-pointer font-medium">Debug Info (Admin Only)</summary>
                <div class="mt-2 space-y-2">
                    <p><strong>Service ID:</strong> <?php echo esc_html($service_id); ?></p>
                    <p><strong>Durations:</strong> <?php echo esc_html(count($durations)); ?></p>
                    <p><strong>Hours:</strong> <?php echo esc_html(count($service_hours)); ?></p>
                    <p><strong>JSON Data:</strong> <code style="word-break: break-all;"><?php echo esc_html($json_data); ?></code></p>
                </div>
            </details>
        </div>
    <?php endif; ?>
<?php
    return ob_get_clean();
}

/**
 * NUEVA FUNCIÓN: Renderiza el formulario de reserva usando el post global actual
 * Función de conveniencia para usar en templates
 * 
 * @return string HTML del formulario de reserva
 */
function wptbt_render_current_service_booking_form()
{
    global $post;
    
    if (!$post || $post->post_type !== 'servicio') {
        return '<div class="p-4 bg-red-100 text-red-800 rounded-md">Error: Not a service page</div>';
    }

    return wptbt_render_service_booking_form($post);
}

/**
 * FUNCIÓN DE COMPATIBILIDAD: Mantiene la firma anterior pero usando el nuevo sistema
 * Para evitar romper código existente
 * 
 * @deprecated Usar wptbt_render_service_booking_form() con service ID en su lugar
 */
function wptbt_render_service_booking_form_legacy($service_title, $service_duration1 = '', $service_price1 = '', $service_duration2 = '', $service_price2 = '', $service_hours = [])
{
    // Crear datos de servicio temporal para compatibilidad
    $durations = [];
    
    if (!empty($service_duration1) && !empty($service_price1)) {
        $durations[] = [
            'duration' => $service_duration1,
            'price' => $service_price1,
            'minutes' => intval($service_duration1),
            'text' => $service_duration1 . ' - ' . $service_price1,
            'value' => intval($service_duration1) . 'min-' . $service_price1
        ];
    }
    
    if (!empty($service_duration2) && !empty($service_price2)) {
        $durations[] = [
            'duration' => $service_duration2,
            'price' => $service_price2,
            'minutes' => intval($service_duration2),
            'text' => $service_duration2 . ' - ' . $service_price2,
            'value' => intval($service_duration2) . 'min-' . $service_price2
        ];
    }

    $service_data = [
        [
            'id' => 'legacy',
            'title' => $service_title,
            'subtitle' => '',
            'hours' => $service_hours,
            'durations' => $durations,
            'duration1' => $service_duration1,
            'price1' => $service_price1,
            'duration2' => $service_duration2,
            'price2' => $service_price2
        ]
    ];

    $form_id = 'booking-form-' . uniqid();
    $form_email = get_theme_mod('services_booking_form_email', get_option('admin_email'));
    
    wptbt_load_solid_component('booking-form');

    ob_start();
?>
    <div class="service-booking-form max-w-4xl mx-auto">
        <div class="rounded-lg shadow-xl bg-white p-8">
            <div class="text-center mb-8">
                <h3 class="text-3xl font-medium mb-6 text-gray-800">
                    <?php echo esc_html($service_title); ?>
                </h3>
                <p class="text-gray-600">
                    Complete the form to book your session
                </p>
            </div>

            <?php
            echo wptbt_booking_form_component(
                [
                    'services' => $service_data,
                    'darkMode' => false,
                    'accentColor' => '#D4B254',
                    'useSingleService' => true,
                    'emailRecipient' => $form_email 
                ],
                [
                    'id' => 'solid-booking-form-' . esc_attr($form_id),
                    'class' => 'solid-booking-container',
                ]
            );
            ?>
        </div>
    </div>
<?php
    return ob_get_clean();
}