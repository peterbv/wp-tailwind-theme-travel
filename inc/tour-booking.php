<?php

/**
 * Funciones para renderizar el formulario de reserva en la página de tour individual
 * ACTUALIZADO: Sistema modular Solid.js con múltiples precios y CustomSelect
 * Con soporte completo para internacionalización (i18n)
 * 
 * @package WPTBT
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Renderiza el formulario de reserva para un tour específico
 * ACTUALIZADO: Ahora usa el sistema de múltiples precios automáticamente
 * 
 * @param int|WP_Post $tour_id_or_post ID del tour o objeto WP_Post
 * @return string HTML del formulario de reserva
 */
function wptbt_render_tour_booking_form($tour_id_or_post)
{
    // Obtener el objeto post del tour
    if (is_numeric($tour_id_or_post)) {
        $tour_post = get_post($tour_id_or_post);
    } elseif ($tour_id_or_post instanceof WP_Post) {
        $tour_post = $tour_id_or_post;
    } else {
        global $post;
        $tour_post = $post;
    }

    if (!$tour_post || $tour_post->post_type !== 'tours') {
        return '<div class="p-4 bg-red-100 text-red-800 rounded-md">Error: Invalid tour data</div>';
    }

    $tour_id = $tour_post->ID;
    $tour_title = $tour_post->post_title;
    $form_id = 'booking-form-' . uniqid();

    // Usar el nuevo método optimizado de la clase WPTBT_Tours
    $tour_booking_data = WPTBT_Tours::get_tour_booking_form_data($tour_id);
    $durations = $tour_booking_data['durations'];
    $tour_hours = $tour_booking_data['hours'];
    $tour_subtitle = $tour_booking_data['subtitle'];

    // VERIFICACIÓN: Log de debug
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log("Single tour {$tour_id}: " . count($tour_hours) . " hours, " . count($durations) . " durations");
        error_log("Hours: " . print_r($tour_hours, true));
        error_log("Durations: " . print_r($durations, true));
    }

    // Preparar la información de tour para JSON usando el nuevo método
    $tour_data = [$tour_booking_data];

    $json_data = wp_json_encode($tour_data);
    $form_email = get_theme_mod('tours_booking_form_email', get_option('admin_email'));

    wptbt_load_solid_component('booking-form');

    ob_start();
?>
    <div class="tour-booking-form max-w-4xl mx-auto reveal-item opacity-0 translate-y-8 relative">
        <!-- El resto del HTML permanece igual -->
        <div class="absolute top-0 left-0 right-0 -translate-y-1/2 flex justify-center">
            <div class="w-32 h-0.5 bg-travel-accent/30"></div>
        </div>

        <div class="rounded-lg shadow-xl mb-30">
            <div class="bg-travel-secondary/20 p-8 text-center">
                <span class="block text-lg italic font-medium mb-2 text-travel-accent">
                    <?php echo esc_html__('Reserve your tour', 'wptbt-tours'); ?>
                </span>
                <h3 class="text-3xl fancy-text font-medium mb-6 text-gray-800 relative inline-block">
                    <?php echo esc_html($tour_title); ?>
                    <span class="absolute bottom-0 left-0 w-full h-1 mt-2 bg-travel-accent transform" style="transform: scaleX(0.3); transform-origin: center;"></span>
                </h3>
                
                <?php if (!empty($tour_subtitle)) : ?>
                    <p class="text-lg text-gray-700 font-medium mb-4">
                        <?php echo esc_html($tour_subtitle); ?>
                    </p>
                <?php endif; ?>

                <p class="text-gray-600 max-w-md mx-auto mt-6">
                    <?php echo esc_html(sprintf(__('Complete the form to reserve your %s tour', 'wptbt-tours'), $tour_title)); ?>
                </p>

                <?php if (!empty($durations)) : ?>
                    <!--<div class="mt-6 flex flex-wrap justify-center gap-3">
                        <?php foreach ($durations as $duration) : ?>
                            <div class="bg-white/70 backdrop-blur-sm px-4 py-2 rounded-full border border-travel-accent/20">
                                <span class="text-sm font-medium text-gray-700">
                                    <?php echo esc_html($duration['text']); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>-->
                <?php endif; ?>
            </div>

            <div class="p-8 bg-white">
                <?php if (empty($tour_hours) && empty($durations)) : ?>
                    <!-- Mensaje de error si no hay configuración -->
                    <div class="text-center py-8">
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-6">
                            <svg class="w-12 h-12 mx-auto text-amber-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <h4 class="text-lg font-medium text-amber-800 mb-2">
                                <?php echo esc_html__('tour Configuration Needed', 'wptbt-tours'); ?>
                            </h4>
                            <p class="text-amber-700 mb-4">
                                <?php echo esc_html__('This tour needs available departure times and prices before reservations can be made.', 'wptbt-tours'); ?>
                            </p>
                            <?php if (current_user_can('edit_posts')) : ?>
                                <a href="<?php echo esc_url(get_edit_post_link($tour_id)); ?>" 
                                   class="inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    <?php echo esc_html__('Configure tour', 'wptbt-tours'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else : ?>
                    <?php
                    echo wptbt_booking_form_component(
                        [
                            'tours' => $tour_data,
                            'darkMode' => false,
                            'accentColor' => get_theme_mod('tours_booking_form_accent_color', '#DC2626'),
                            'useSingletour' => true,
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
            <div class="w-32 h-0.5 bg-travel-accent/30"></div>
        </div>
    </div>

    <!-- Debug info para desarrollo -->
    <?php if (defined('WP_DEBUG') && WP_DEBUG && current_user_can('manage_options')) : ?>
        <div class="mt-4 p-4 bg-gray-100 rounded text-xs">
            <details>
                <summary class="cursor-pointer font-medium">Debug Info (Admin Only)</summary>
                <div class="mt-2 space-y-2">
                    <p><strong>tour ID:</strong> <?php echo esc_html($tour_id); ?></p>
                    <p><strong>Durations:</strong> <?php echo esc_html(count($durations)); ?></p>
                    <p><strong>Hours:</strong> <?php echo esc_html(count($tour_hours)); ?></p>
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
function wptbt_render_current_tour_booking_form()
{
    global $post;
    
    if (!$post || $post->post_type !== 'tours') {
        return '<div class="p-4 bg-red-100 text-red-800 rounded-md">Error: Not a tour page</div>';
    }

    return wptbt_render_tour_booking_form($post);
}

/**
 * FUNCIÓN DE TEMPLATE: Muestra el formulario de reserva para tours
 * Esta función es llamada directamente desde single-tours.php
 */
function wptbt_display_tour_booking_form()
{
    global $post;
    
    if (!$post || $post->post_type !== 'tours') {
        echo '<div class="p-4 bg-red-100 text-red-800 rounded-md">Error: Not a tour page</div>';
        return;
    }

    // Verificar si el tour es reservable antes de mostrar el formulario
    if (!WPTBT_Tours::is_tour_bookable($post->ID)) {
        echo '<div class="p-4 bg-amber-100 text-amber-800 rounded-md">';
        echo '<strong>Configuración requerida:</strong> Este tour necesita horarios de salida y precios configurados antes de poder aceptar reservas.';
        if (current_user_can('edit_posts')) {
            echo ' <a href="' . esc_url(get_edit_post_link($post->ID)) . '" class="underline">Configurar tour</a>';
        }
        echo '</div>';
        return;
    }

    echo wptbt_render_tour_booking_form($post);
}

/**
 * FUNCIÓN DE COMPATIBILIDAD: Mantiene la firma anterior pero usando el nuevo sistema
 * Para evitar romper código existente
 * 
 * @deprecated Usar wptbt_render_tour_booking_form() con tour ID en su lugar
 */
function wptbt_render_tour_booking_form_legacy($tour_title, $tour_duration1 = '', $tour_price1 = '', $tour_duration2 = '', $tour_price2 = '', $tour_hours = [])
{
    // Crear datos de tour temporal para compatibilidad
    $durations = [];
    
    if (!empty($tour_duration1) && !empty($tour_price1)) {
        $durations[] = [
            'duration' => $tour_duration1,
            'price' => $tour_price1,
            'minutes' => intval($tour_duration1) * 24 * 60, // Convertir días a minutos
            'text' => $tour_duration1 . ' días - ' . $tour_price1,
            'value' => intval($tour_duration1) . 'days-' . $tour_price1
        ];
    }
    
    if (!empty($tour_duration2) && !empty($tour_price2)) {
        $durations[] = [
            'duration' => $tour_duration2,
            'price' => $tour_price2,
            'minutes' => intval($tour_duration2) * 24 * 60, // Convertir días a minutos
            'text' => $tour_duration2 . ' días - ' . $tour_price2,
            'value' => intval($tour_duration2) . 'days-' . $tour_price2
        ];
    }

    $tour_data = [
        [
            'id' => 'legacy',
            'title' => $tour_title,
            'subtitle' => '',
            'hours' => $tour_hours,
            'durations' => $durations,
            'duration1' => $tour_duration1,
            'price1' => $tour_price1,
            'duration2' => $tour_duration2,
            'price2' => $tour_price2
        ]
    ];

    $form_id = 'booking-form-' . uniqid();
    $form_email = get_theme_mod('tours_booking_form_email', get_option('admin_email'));
    
    wptbt_load_solid_component('booking-form');

    ob_start();
?>
    <div class="tour-booking-form max-w-4xl mx-auto">
        <div class="rounded-lg shadow-xl bg-white p-8">
            <div class="text-center mb-8">
                <h3 class="text-3xl font-medium mb-6 text-gray-800">
                    <?php echo esc_html($tour_title); ?>
                </h3>
                <p class="text-gray-600">
                    Complete the form to reserve your tour
                </p>
            </div>

            <?php
            echo wptbt_booking_form_component(
                [
                    'tours' => $tour_data,
                    'darkMode' => false,
                    'accentColor' => '#DC2626',
                    'useSingletour' => true,
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

/**
 * NUEVA FUNCIÓN: Mostrar badge de precio mínimo para listados de tours
 */
function wptbt_tour_price_badge($tour_id, $classes = 'bg-travel-accent text-white px-3 py-1 rounded-full text-sm font-medium')
{
    $price_label = WPTBT_Tours::get_tour_price_label($tour_id);
    
    if ($price_label) {
        echo '<span class="' . esc_attr($classes) . '">' . esc_html($price_label) . '</span>';
    }
}

/**
 * NUEVA FUNCIÓN: Mostrar estado de reservabilidad de un tour
 */
function wptbt_tour_booking_status($tour_id)
{
    if (WPTBT_Tours::is_tour_bookable($tour_id)) {
        echo '<span class="inline-flex items-center text-green-600 text-sm">';
        echo '<svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">';
        echo '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>';
        echo '</svg>';
        echo __('Available for booking', 'wptbt-tours');
        echo '</span>';
    } else {
        echo '<span class="inline-flex items-center text-amber-600 text-sm">';
        echo '<svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">';
        echo '<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>';
        echo '</svg>';
        echo __('Contact for booking', 'wptbt-tours');
        echo '</span>';
    }
}

/**
 * NUEVA FUNCIÓN: Obtener configuración JSON para el formulario de reservas
 * Útil para pasar datos a JavaScript
 */
function wptbt_get_booking_form_json_config($tour_id)
{
    $config = WPTBT_Tours::get_booking_form_config($tour_id);
    return wp_json_encode($config);
}

/**
 * FUNCIÓN DE DEBUGGING: Crear datos de ejemplo para testing
 */
function wptbt_create_sample_tour_data($tour_id)
{
    // Datos de ejemplo para horarios
    $sample_hours = ['09:00', '14:00', '16:00'];
    update_post_meta($tour_id, '_wptbt_tour_hours', $sample_hours);
    
    // Datos de ejemplo para precios
    $sample_prices = [
        ['duration' => '3', 'price' => '$299'],
        ['duration' => '5', 'price' => '$449'],
        ['duration' => '7', 'price' => '$599']
    ];
    update_post_meta($tour_id, '_wptbt_tour_prices', $sample_prices);
    
    return "Datos de ejemplo creados para tour ID: $tour_id";
}

/**
 * FUNCIÓN DE DEBUGGING: Verificar configuración de tour
 */
function wptbt_debug_tour_config($tour_id)
{
    $hours = get_post_meta($tour_id, '_wptbt_tour_hours', true);
    $prices = get_post_meta($tour_id, '_wptbt_tour_prices', true);
    
    echo "<div style='background: #f0f0f0; padding: 20px; margin: 20px 0; border-left: 4px solid #0073aa;'>";
    echo "<h3>🔍 Debug Info para Tour ID: $tour_id</h3>";
    echo "<p><strong>Horarios guardados:</strong> " . (empty($hours) ? "❌ Ninguno" : "✅ " . count($hours) . " horarios") . "</p>";
    if (!empty($hours)) {
        echo "<ul>";
        foreach ($hours as $hour) {
            echo "<li>$hour</li>";
        }
        echo "</ul>";
    }
    
    echo "<p><strong>Precios guardados:</strong> " . (empty($prices) ? "❌ Ninguno" : "✅ " . count($prices) . " opciones de precio") . "</p>";
    if (!empty($prices)) {
        echo "<ul>";
        foreach ($prices as $price) {
            echo "<li>{$price['duration']} días - {$price['price']}</li>";
        }
        echo "</ul>";
    }
    
    echo "<p><strong>Estado:</strong> " . (WPTBT_Tours::is_tour_bookable($tour_id) ? "✅ Listo para reservas" : "❌ Necesita configuración") . "</p>";
    echo "</div>";
}

/**
 * AJAX Handler: Crear datos de ejemplo para un tour
 */
function wptbt_ajax_create_sample_tour_data()
{
    // Verificar nonce y permisos
    if (!wp_verify_nonce($_POST['nonce'], 'sample_tour_data') || !current_user_can('edit_posts')) {
        wp_die('Unauthorized');
    }
    
    $tour_id = intval($_POST['tour_id']);
    if ($tour_id > 0) {
        $result = wptbt_create_sample_tour_data($tour_id);
        wp_send_json_success($result);
    } else {
        wp_send_json_error('Invalid tour ID');
    }
}

// Registrar el handler AJAX
add_action('wp_ajax_create_sample_tour_data', 'wptbt_ajax_create_sample_tour_data');