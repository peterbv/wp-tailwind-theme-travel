<?php

/**
 * Bloque de Reservas para Tours
 * Versión integrada con Solid.js para una mejor experiencia de usuario
 * Version con soporte para internacionalización (i18n)
 *
 * @package WPTBT
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Class WPTBT_Booking_Block
 */
class WPTBT_Booking_Block
{
    private $translate = '';
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translate = 'wptbt-booking-form-block';
        // Registrar bloque
        add_action('init', [$this, 'register_block']);

        // Agregar shortcode como método alternativo
        add_shortcode('wptbt_booking', [$this, 'render_booking_shortcode']);

        // Procesar el formulario de reserva
        add_action('wp_ajax_wptbt_submit_booking', [$this, 'process_booking']);
        add_action('wp_ajax_nopriv_wptbt_submit_booking', [$this, 'process_booking']);

        // NUEVO: Endpoint para obtener nonce fresco
        add_action('wp_ajax_wptbt_get_fresh_nonce', [$this, 'get_fresh_nonce']);
        add_action('wp_ajax_nopriv_wptbt_get_fresh_nonce', [$this, 'get_fresh_nonce']);
        
        // NUEVO: Endpoint para obtener tours disponibles para booking
        add_action('wp_ajax_get_tours_for_booking', [$this, 'get_tours_for_booking']);
        add_action('wp_ajax_nopriv_get_tours_for_booking', [$this, 'get_tours_for_booking']);
    }

    /**
     * NUEVO MÉTODO: Obtener nonce fresco via AJAX
     */
    public function get_fresh_nonce()
    {
        // Generar un nonce fresco
        $fresh_nonce = wp_create_nonce('wptbt_booking_nonce');

        // Responder con el nonce
        wp_send_json_success([
            'nonce' => $fresh_nonce,
            'timestamp' => time()
        ]);
    }

    /**
     * NUEVO MÉTODO: Obtener todos los tours disponibles para booking via AJAX
     */
    public function get_tours_for_booking()
    {
        // Verificar nonce si está presente
        if (isset($_POST['nonce']) && !wp_verify_nonce($_POST['nonce'], 'wptbt_booking_nonce')) {
            wp_send_json_error(['message' => 'Nonce verification failed']);
            return;
        }

        try {
            // Obtener todos los tours publicados
            $tours_query = new WP_Query([
                'post_type' => 'tours',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'orderby' => 'menu_order title',
                'order' => 'ASC'
            ]);

            $tours_data = [];

            if ($tours_query->have_posts()) {
                while ($tours_query->have_posts()) {
                    $tours_query->the_post();
                    $tour_id = get_the_ID();
                    
                    // Usar las funciones existentes de la clase WPTBT_Tours
                    $pricing_data = WPTBT_Tours::get_tour_pricing_data($tour_id);
                    $booking_info = WPTBT_Tours::get_tour_booking_info($tour_id);
                    
                    // NEW: Obtener configuración del formulario de reservas
                    $booking_config = WPTBT_Tours::get_tour_booking_config($tour_id);
                    
                    // Formatear datos para el componente Solid.js
                    $tour_data = [
                        'id' => (string)$tour_id,
                        'title' => get_the_title(),
                        'subtitle' => get_post_meta($tour_id, '_wptbt_tour_subtitle', true) ?: '',
                        'hours' => [],
                        'durations' => [],
                        // NEW: Configuración del formulario
                        'booking_config' => $booking_config
                    ];

                    // Procesar horarios según configuración
                    if ($booking_config['has_flexible_schedule']) {
                        // Solo mostrar horarios si el tour tiene horario flexible
                        if (!empty($booking_info['available_times']) && is_array($booking_info['available_times'])) {
                            $tour_data['hours'] = $booking_info['available_times'];
                        } else {
                            // Horarios por defecto para tours flexibles
                            $tour_data['hours'] = ["07:00", "08:00", "09:00", "14:00", "15:00"];
                        }
                    } else {
                        // Tour con horario fijo - usar los horarios configurados en Departure Times
                        $departure_times = get_post_meta($tour_id, '_wptbt_tour_hours', true);
                        if (!empty($departure_times) && is_array($departure_times)) {
                            $tour_data['hours'] = $departure_times;
                        } else {
                            // Horario fijo por defecto si no hay configuración
                            $tour_data['hours'] = ["08:00"];
                        }
                    }

                    // Procesar precios y duraciones
                    if (!empty($pricing_data['durations']) && is_array($pricing_data['durations'])) {
                        foreach ($pricing_data['durations'] as $duration_data) {
                            if (is_array($duration_data) && !empty($duration_data['days']) && !empty($duration_data['price'])) {
                                $days = (int)$duration_data['days'];
                                $price = $duration_data['price'];
                                
                                $tour_data['durations'][] = [
                                    'minutes' => $days * 24 * 60, // Convertir días a minutos
                                    'price' => $price,
                                    'text' => $days . ' día' . ($days > 1 ? 's' : '') . ' - ' . $price,
                                    'duration' => (string)$days,
                                    'value' => $days . 'days-' . str_replace(['$', ' '], '', $price)
                                ];
                            }
                        }
                    }
                    
                    // Si no hay duraciones, agregar una por defecto
                    if (empty($tour_data['durations'])) {
                        $default_price = get_post_meta($tour_id, '_tour_price', true) ?: 'Consultar precio';
                        $tour_data['durations'][] = [
                            'minutes' => 480, // 8 horas por defecto
                            'price' => $default_price,
                            'text' => '1 día - ' . $default_price,
                            'duration' => '1',
                            'value' => '1day-' . str_replace(['$', ' '], '', $default_price)
                        ];
                    }

                    $tours_data[] = $tour_data;
                }
            }
            
            wp_reset_postdata();

            // Si no hay tours, devolver datos de ejemplo
            if (empty($tours_data)) {
                $tours_data[] = [
                    'id' => 'sample',
                    'title' => 'Consulta Personalizada',
                    'subtitle' => 'Contacta para más información',
                    'hours' => ["09:00", "10:00", "14:00", "15:00"],
                    'durations' => [
                        [
                            'minutes' => 480,
                            'price' => 'Consultar precio',
                            'text' => 'Consulta personalizada - Precio a consultar',
                            'duration' => '1',
                            'value' => 'custom-inquiry'
                        ]
                    ]
                ];
            }

            wp_send_json_success($tours_data);
            
        } catch (Exception $e) {
            wp_send_json_error(['message' => 'Error al obtener tours: ' . $e->getMessage()]);
        }
    }

    /**
     * Registrar el bloque de reservas
     */
    public function register_block()
    {
        // Verificar que la función existe (Gutenberg está activo)
        if (!function_exists('register_block_type')) {
            return;
        }

        // Registrar script del bloque
        wp_register_script(
            'wptbt-booking-block-editor',
            get_template_directory_uri() . '/assets/admin/js/booking-block.js',
            ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'],
            filemtime(get_template_directory() . '/assets/admin/js/booking-block.js')
        );

        // Agregar traducción al script del editor
        wp_set_script_translations('wptbt-booking-block-editor', $this->translate, get_template_directory() . '/languages');

        // Registrar estilos para el editor
        wp_register_style(
            'wptbt-booking-block-editor-style',
            get_template_directory_uri() . '/assets/admin/css/booking-block-style.css',
            ['wp-edit-blocks'],
            filemtime(get_template_directory() . '/assets/admin/css/booking-block-style.css')
        );

        // Register block
        register_block_type('wptbt/booking-block', [
            'editor_script' => 'wptbt-booking-block-editor',
            'editor_style'  => 'wptbt-booking-block-editor-style',
            'render_callback' => [$this, 'render_booking_block'],
            'attributes' => [
                'title' => [
                    'type' => 'string',
                    'default' => __('Reserva Ahora', $this->translate)
                ],
                'subtitle' => [
                    'type' => 'string',
                    'default' => __('Tu Aventura', $this->translate)
                ],
                'description' => [
                    'type' => 'string',
                    'default' => __('Reserva tu tour y vive una experiencia increíble.', $this->translate)
                ],
                'imageID' => [
                    'type' => 'number'
                ],
                'imageURL' => [
                    'type' => 'string'
                ],
                'services' => [
                    'type' => 'array',
                    'default' => [
                        ['name' => __('Tour Montaña', $this->translate), 'duration' => __('3 días', $this->translate), 'price' => __('$299', $this->translate)],
                        ['name' => __('Tour Playa', $this->translate), 'duration' => __('5 días', $this->translate), 'price' => __('$450', $this->translate)],
                        ['name' => __('Tour Cultural', $this->translate), 'duration' => __('7 días', $this->translate), 'price' => __('$650', $this->translate)],
                        ['name' => __('Tour Aventura', $this->translate), 'duration' => __('10 días', $this->translate), 'price' => __('$899', $this->translate)]
                    ]
                ],
                'buttonText' => [
                    'type' => 'string',
                    'default' => __('BOOK NOW', $this->translate)
                ],
                'buttonColor' => [
                    'type' => 'string',
                    'default' => '#D4B254'
                ],
                'textColor' => [
                    'type' => 'string',
                    'default' => '#FFFFFF'
                ],
                'accentColor' => [
                    'type' => 'string',
                    'default' => '#D4B254'
                ],
                'emailRecipient' => [
                    'type' => 'string',
                    'default' => ''
                ],
                'useSolidJs' => [
                    'type' => 'boolean',
                    'default' => true
                ],
                'showTopWave' => [
                    'type' => 'boolean',
                    'default' => true
                ],
                'showBottomWave' => [
                    'type' => 'boolean',
                    'default' => true
                ],
            ]
        ]);
    }

    public function add_module_type_attribute($tag, $handle, $src)
    {
        // Add type="module" only to our Solid.js script
        if ('wptbt-booking-solid' === $handle) {
            $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
        }
        return $tag;
    }

    /**
     * Función para obtener los tours con sus datos de duración y precio
     * Actualizada para usar tours en lugar de servicios
     */
    private function get_tours_with_durations()
    {
        // Obtener todos los tours
        $args = [
            'post_type'      => 'tours',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'DESC',
        ];

        // Fallback para compatibilidad
        if (!post_type_exists('tours')) {
            $args['post_type'] = 'servicio';
        }

        $tours = get_posts($args);

        // Verificar si hay tours disponibles
        if (empty($tours)) {
            return '<div class="p-8 bg-white rounded-lg shadow-md text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <h3 class="text-xl font-medium text-gray-800 mb-2">' . esc_html__('No hay tours disponibles', $this->translate) . '</h3>
            <p class="text-gray-600">' . esc_html__('Por favor, añade tours desde el panel de administración para habilitar las reservas.', $this->translate) . '</p>
        </div>';
        }

        $services_data = [];

        // Procesar cada tour
        foreach ($tours as $tour) {
            // Obtener horarios disponibles usando los nuevos meta fields
            $hours = get_post_meta($tour->ID, '_wptbt_tour_hours', true) ?: [];
            
            // Fallback para compatibilidad con servicios antiguos
            if (empty($hours)) {
                $hours = get_post_meta($tour->ID, '_wptbt_service_hours', true) ?: [];
            }

            // CORREGIDO: Obtener múltiples precios del nuevo formato para tours
            $prices = get_post_meta($tour->ID, '_wptbt_tour_prices', true);
            
            // Fallback para compatibilidad con servicios antiguos
            if (empty($prices)) {
                $prices = get_post_meta($tour->ID, '_wptbt_service_prices', true);
            }
            $durations = [];

            if (!empty($prices) && is_array($prices)) {
                // Usar el nuevo formato de múltiples precios
                foreach ($prices as $price_data) {
                    if (!empty($price_data['duration']) && !empty($price_data['price'])) {
                        $days = intval($price_data['duration']);
                        $durations[] = [
                            'duration' => $price_data['duration'],
                            'price' => $price_data['price'],
                            'minutes' => $days * 24 * 60, // Convert days to minutes for compatibility
                            'text' => $price_data['duration'] . ' días - $' . str_replace('$', '', $price_data['price']),
                            'value' => $days . 'days-' . $price_data['price']
                        ];
                    }
                }
            } else {
                // Fallback: usar formato anterior si el nuevo no existe
                $duration1 = get_post_meta($tour->ID, '_wptbt_service_duration1', true) ?: '';
                $price1 = get_post_meta($tour->ID, '_wptbt_service_price1', true) ?: '';
                $duration2 = get_post_meta($tour->ID, '_wptbt_service_duration2', true) ?: '';
                $price2 = get_post_meta($tour->ID, '_wptbt_service_price2', true) ?: '';

                if (!empty($duration1) && !empty($price1)) {
                    $days1 = intval($duration1);
                    $durations[] = [
                        'duration' => $duration1,
                        'price' => $price1,
                        'minutes' => $days1 * 24 * 60, // Convert days to minutes for compatibility
                        'text' => $duration1 . ' días - $' . str_replace('$', '', $price1),
                        'value' => $days1 . 'days-' . $price1
                    ];
                }

                if (!empty($duration2) && !empty($price2)) {
                    $days2 = intval($duration2);
                    $durations[] = [
                        'duration' => $duration2,
                        'price' => $price2,
                        'minutes' => $days2 * 24 * 60, // Convert days to minutes for compatibility
                        'text' => $duration2 . ' días - $' . str_replace('$', '', $price2),
                        'value' => $days2 . 'days-' . $price2
                    ];
                }
            }

            // Obtener subtítulo del tour si existe
            $subtitle = get_post_meta($tour->ID, '_wptbt_tour_subtitle', true) ?: '';
            
            // Fallback para compatibilidad con servicios antiguos
            if (empty($subtitle)) {
                $subtitle = get_post_meta($tour->ID, '_wptbt_service_subtitle', true) ?: '';
            }

            // VERIFICACIÓN: Log de debug para identificar problemas
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("Tour {$tour->ID} ({$tour->post_title}): " .
                    count($hours) . " hours, " . count($durations) . " durations");
            }

            // Añadir tour a la lista
            $services_data[] = [
                'id' => (string)$tour->ID, // IMPORTANTE: Convertir a string para consistencia
                'title' => $tour->post_title,
                'subtitle' => $subtitle,
                'hours' => array_values($hours), // CORREGIDO: Asegurar array indexado
                'durations' => array_values($durations), // CORREGIDO: Asegurar array indexado
                // Mantener compatibilidad con formato anterior
                'duration1' => !empty($durations[0]) ? $durations[0]['duration'] : '',
                'price1' => !empty($durations[0]) ? $durations[0]['price'] : '',
                'duration2' => !empty($durations[1]) ? $durations[1]['duration'] : '',
                'price2' => !empty($durations[1]) ? $durations[1]['price'] : ''
            ];
        }

        return $services_data;
    }

    /**
     * Renderizar el bloque de reservas
     *
     * @param array $attributes Atributos del bloque.
     * @return string HTML del bloque.
     */
    public function render_booking_block($attributes)
    {
        // En su lugar, usar el sistema modular
        wptbt_load_solid_component('booking-form');

        // Podemos pasar datos específicos si es necesario
        wp_localize_script('wptbt-solid-booking-form', 'wptbtBooking', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wptbt_booking_nonce')
        ]);

        // Extraer atributos
        $title = isset($attributes['title']) ? $attributes['title'] : __('Book Now', $this->translate);
        $subtitle = isset($attributes['subtitle']) ? $attributes['subtitle'] : __('Appointment', $this->translate);
        $description = isset($attributes['description']) ? $attributes['description'] : __('Book your spa treatment and enjoy a moment of relaxation.', $this->translate);
        $services = isset($attributes['services']) ? $attributes['services'] : [];
        $buttonText = isset($attributes['buttonText']) ? $attributes['buttonText'] : __('BOOK NOW', $this->translate);
        $buttonColor = isset($attributes['buttonColor']) ? $attributes['buttonColor'] : '#D4B254';
        $textColor = isset($attributes['textColor']) ? $attributes['textColor'] : '#FFFFFF';
        $accentColor = isset($attributes['accentColor']) ? $attributes['accentColor'] : '#D4B254';
        $imageURL = isset($attributes['imageURL']) ? $attributes['imageURL'] : '';
        $useSolidJs = isset($attributes['useSolidJs']) ? (bool)$attributes['useSolidJs'] : true; // Use Solid.js by default
        $emailRecipient = isset($attributes['emailRecipient'])
            ? $attributes['emailRecipient']
            : '';
        $showTopWave = isset($attributes['showTopWave']) ? (bool)$attributes['showTopWave'] : true;
        $showBottomWave = isset($attributes['showBottomWave']) ? (bool)$attributes['showBottomWave'] : true;
        // Generar un ID único para el formulario
        $form_id = 'booking-form-' . uniqid();
        $input_id = 'date-input-' . uniqid();

        // Si no hay imagen seleccionada, usar una por defecto
        if (empty($imageURL) && isset($attributes['imageID'])) {
            $imageURL = wp_get_attachment_image_url($attributes['imageID'], 'full');
        }

        if (empty($imageURL)) {
            $imageURL = get_template_directory_uri() . '/assets/images/default-spa.jpg';
        }

        $tours_data = $this->get_tours_with_durations();

        // Si hay tours pero no se usa Solid.js, preparamos las opciones de tour para el formulario tradicional
        $service_options = '';
        $first_service = null;
        $first_service_hours = [];

        if (!$useSolidJs) {
            // Preparar opciones para el formulario tradicional
            foreach ($tours_data as $index => $tour) {
                $selected = ($index === 0) ? 'selected' : '';
                $hours = get_post_meta($tour['id'], '_wptbt_tour_hours', true);
                
                // Fallback para compatibilidad
                if (empty($hours)) {
                    $hours = get_post_meta($tour['id'], '_wptbt_service_hours', true);
                }
                if (!$hours) $hours = [];

                // Guardar información del primer tour para usarla más tarde
                if ($index === 0) {
                    $first_service = $tour;
                    $first_service_hours = $hours;
                }

                // Usar los datos ya procesados del tour
                $duration1 = !empty($tour['duration1']) ? $tour['duration1'] : '';
                $price1 = !empty($tour['price1']) ? $tour['price1'] : '';

                $service_options .= '<option value="' . esc_attr($tour['title']) . '" ' . $selected . '>' .
                    esc_html($tour['title']);

                // Añadir información de precio/duración si está disponible
                if (!empty($duration1) && !empty($price1)) {
                    $service_options .= ' (' . esc_html($duration1) . ' - ' . esc_html($price1) . ')';
                }

                $service_options .= '</option>';
            }
        }

        // Configurar datos JSON para Solid.js
        $json_data = wp_json_encode($tours_data);
        $is_dark_mode = true; // El formulario siempre está sobre fondo oscuro

        // Iniciar buffer de salida con un wrapper para ancho completo
        ob_start();
?>
        <!-- Este div abre un nuevo scope que ocupará todo el ancho -->
        <div id="wptbt-booking-wrapper" class="wptbt-booking-wrapper w-full relative reveal-item opacity-0 translate-y-8" style="margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw); width: 100vw; max-width: 100vw;">
            <?php if ($showTopWave): ?>
                <!-- Borde ondulado en la parte superior -->
                <div class="absolute top-0 left-0 right-0 z-10 transform rotate-180">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100" preserveAspectRatio="none" class="w-full h-20 md:h-24 lg:h-28 hidden md:block">
                        <path fill="white" d="M0,85.7L20,75.4C40,65.1,80,44.6,120,33.9C160,23.2,200,22.4,240,28.6C280,34.8,320,48.1,360,50C400,51.9,440,42.5,480,39.8C520,37.2,560,41.3,600,48.3C640,55.2,680,65,720,62.3C760,59.5,800,44.2,840,41.9C880,39.7,920,50.5,960,54.3C1000,58.2,1040,55.2,1080,58.2C1120,61.1,1160,69.9,1200,73.3C1240,76.8,1280,74.9,1320,70.9C1360,67,1400,61,1420,58L1440,55L1440,100L1420,100C1400,100,1360,100,1320,100C1280,100,1240,100,1200,100C1160,100,1120,100,1080,100C1040,100,1000,100,960,100C920,100,880,100,840,100C800,100,760,100,720,100C680,100,640,100,600,100C560,100,520,100,480,100C440,100,400,100,360,100C320,100,280,100,240,100C200,100,160,100,120,100C80,100,40,100,20,100L0,100Z"></path>
                    </svg>

                    <!-- Versión simplificada para móviles -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 60" preserveAspectRatio="none" class="w-full h-10 md:hidden">
                        <path fill="white" d="M0,40L50,35C100,30,200,20,300,20C400,20,500,30,550,35L600,40L600,60L550,60C500,60,400,60,300,60C200,60,100,60,50,60L0,60Z"></path>
                    </svg>
                </div>
            <?php endif; ?>

            <div class="wptbt-booking-container relative w-full text-white" style="background-image: url('<?php echo esc_url($imageURL); ?>'); background-size: cover; background-position: center;">
                <!-- Overlay con gradiente para mejorar la legibilidad -->
                <div class="absolute inset-0 bg-gradient-to-t from-black via-gray-900/70 to-black/40"></div>

                <div class="container mx-auto px-4 py-28 md:py-36 lg:py-44 relative z-20">
                    <div class="md:max-w-4xl mx-auto grid md:grid-cols-5 gap-8 lg:gap-12">
                        <!-- Columna izquierda: Información -->
                        <div class="md:col-span-2 text-center md:text-left">
                            <!-- Logo/Icono decorativo con animación sutil -->
                            <div class="flex justify-center md:justify-start mb-6">
                                <div class="p-4 rounded-full bg-opacity-20 border border-white/20 backdrop-blur-sm transform transition-all duration-700 hover:scale-110" style="background-color: rgba(<?php
                                                                                                                                                                                                        $accentColorRGB = sscanf($accentColor, "#%02x%02x%02x");
                                                                                                                                                                                                        if (is_array($accentColorRGB) && count($accentColorRGB) === 3) {
                                                                                                                                                                                                            echo "{$accentColorRGB[0]}, {$accentColorRGB[1]}, {$accentColorRGB[2]}";
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            echo "212, 178, 84"; // Color por defecto
                                                                                                                                                                                                        }
                                                                                                                                                                                                        ?>, 0.2);">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 2L14.5 9H21L15.5 13.5L17.5 20.5L12 16L6.5 20.5L8.5 13.5L3 9H9.5L12 2Z" fill="<?php echo esc_attr($accentColor); ?>" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Título y subtítulo con mejor jerarquía y espaciado -->
                            <div class="mb-8">
                                <span class="block text-lg italic font-medium mb-2" style="color: <?php echo esc_attr($accentColor); ?>;">
                                    <?php echo esc_html($subtitle); ?>
                                </span>
                                <h2 class="text-4xl md:text-5xl lg:text-6xl fancy-text font-medium mt-2 mb-6 relative inline-block">
                                    <?php echo esc_html($title); ?>
                                    <span class="absolute bottom-0 left-0 w-full h-1 mt-2" style="background-color: <?php echo esc_attr($accentColor); ?>; transform: scaleX(0.5); transform-origin: left;"></span>
                                </h2>
                                <p class="text-gray-200 text-lg max-w-md mx-auto md:mx-0 mt-6">
                                    <?php echo esc_html($description); ?>
                                </p>
                            </div>

                            <!-- Beneficios rápidos para motivar la reserva -->
                            <div class="hidden md:block space-y-6 mt-12">
                                <div class="flex items-start space-x-4 transform transition-all duration-300 hover:translate-x-2">
                                    <div class="flex-shrink-0 rounded-full p-2" style="background-color: <?php echo esc_attr($accentColor); ?>;">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-medium mb-1"><?php echo esc_html__('Professional Therapists', $this->translate); ?></h3>
                                        <p class="text-gray-300 text-sm"><?php echo esc_html__('Certified experts with years of experience', $this->translate); ?></p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-4 transform transition-all duration-300 hover:translate-x-2">
                                    <div class="flex-shrink-0 rounded-full p-2" style="background-color: <?php echo esc_attr($accentColor); ?>;">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-medium mb-1"><?php echo esc_html__('Flexible Scheduling', $this->translate); ?></h3>
                                        <p class="text-gray-300 text-sm"><?php echo esc_html__('Choose the time that works best for you', $this->translate); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna derecha: Formulario -->
                        <div class="md:col-span-3">
                            <div class="bg-gray-900/80 backdrop-blur-md p-6 md:p-8 rounded-lg shadow-2xl border border-white/10 transform transition-all duration-500 hover:shadow-[0_20px_50px_rgba(0,0,0,0.4)]">
                                <?php if ($useSolidJs): ?>
                                    <!-- Solid.js version -->
                                    <?php
                                    // Usar la función auxiliar para cargar el componente de formulario
                                    echo wptbt_booking_form_component(
                                        [
                                            'services' => $tours_data,
                                            'darkMode' => true,
                                            'accentColor' => $accentColor,
                                            'useSingleService' => false,
                                            'emailRecipient' => $emailRecipient, // Añadir este valor
                                        ],
                                        [
                                            'id' => 'solid-booking-form-' . esc_attr($form_id),
                                            'class' => 'solid-booking-container',
                                        ]
                                    );
                                    ?>
                                <?php else: ?>

                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($showBottomWave): ?>
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
                <?php endif; ?>
            </div>
        </div>

        <?php if (!$useSolidJs): ?>
            <script type="text/javascript">
                document.addEventListener('DOMContentLoaded', function() {
                    // JavaScript para el formulario jQuery y manejo de servicios
                    // Este script solo se incluye si no usamos Solid.js

                    const formId = '<?php echo esc_js($form_id); ?>';
                    const form = document.getElementById(formId);

                    // Marcar este formulario como perteneciente a un bloque para que booking-form.js lo ignore
                    if (form) {
                        form.setAttribute('data-block-form', 'true');
                    }

                    // Código específico del formulario jQuery...
                });
            </script>
        <?php endif; ?>

<?php

        return ob_get_clean();
    }

    /**
     * Procesar el formulario de reserva con envío de correo mejorado
     */
    public function process_booking()
    {
        // Verificar nonce para seguridad
        if (!isset($_POST['booking_nonce']) || !wp_verify_nonce($_POST['booking_nonce'], 'wptbt_booking_nonce')) {
            wp_send_json_error(__('Security check failed', $this->translate));
            exit;
        }

        // Validar campos requeridos
        $required_fields = ['name', 'email', 'service', 'date', 'time', 'visitors'];
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                wp_send_json_error(__('Please fill all required fields', $this->translate));
                exit;
            }
        }

        // Sanitizar datos
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $visitors = absint($_POST['visitors']);

        // Obtener el tour - podría ser un ID o un título
        $service = sanitize_text_field($_POST['service']);
        $service_title = '';

        // Si el tour es numérico, probablemente sea un ID
        if (is_numeric($service)) {
            // Intentar obtener el título del tour usando el ID
            $service_post = get_post(intval($service));
            if ($service_post && ($service_post->post_type == 'tours' || $service_post->post_type == 'servicio')) {
                $service_title = $service_post->post_title;
            }
        }

        // Si se encontró un título, usarlo; de lo contrario, usar el valor original
        // Esto también funciona si el valor ya era el título del tour
        $service = !empty($service_title) ? $service_title : $service;

        // Alternativamente, si se envió service_title directamente (compatibilidad)
        if (isset($_POST['service_title']) && !empty($_POST['service_title'])) {
            $service = sanitize_text_field($_POST['service_title']);
        }

        $duration = '';
        if (isset($_POST['duration']) && !empty($_POST['duration'])) {
            $duration = sanitize_text_field($_POST['duration']);
        }

        $date = sanitize_text_field($_POST['date']);
        $time = sanitize_text_field($_POST['time']);
        $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';
        
        // Sanitizar campos específicos de tours
        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
        $accommodation = isset($_POST['accommodation']) ? sanitize_text_field($_POST['accommodation']) : '';
        $room_config = isset($_POST['room_config']) ? sanitize_text_field($_POST['room_config']) : '';
        $pickup_location = isset($_POST['pickup_location']) ? sanitize_textarea_field($_POST['pickup_location']) : '';
        $emergency_contact = isset($_POST['emergency_contact']) ? sanitize_text_field($_POST['emergency_contact']) : '';
        $special_requests = isset($_POST['special_requests']) ? sanitize_textarea_field($_POST['special_requests']) : '';
        $guide_language = isset($_POST['guide_language']) ? sanitize_text_field($_POST['guide_language']) : '';
        
        // Handle travelers data JSON
        $travelers_data = [];
        if (isset($_POST['travelers_data']) && !empty($_POST['travelers_data'])) {
            $travelers_json = wp_unslash($_POST['travelers_data']);
            $decoded_travelers = json_decode($travelers_json, true);
            if (is_array($decoded_travelers)) {
                $travelers_data = $decoded_travelers;
            }
        }

        // Destinatario del email - Prioridad:
        // 1. Valor enviado en el formulario 
        // 2. Valor del customizer para tours
        // 3. Email del administrador del sitio
        $recipient = isset($_POST['recipient_email']) && !empty($_POST['recipient_email'])
            ? sanitize_email($_POST['recipient_email'])
            : get_theme_mod('tours_booking_form_email', get_theme_mod('services_booking_form_email', get_option('admin_email')));

        // Asunto del email específico para tours
        $subject = sprintf(__('Nueva Reserva de Tour - %s', $this->translate), $name);

        // Crear un email HTML específico para tours
        $email_html = $this->create_tour_email_template($name, $email, $phone, $service, $duration, $date, $time, $message, $visitors, $accommodation, $room_config, $pickup_location, $special_requests, $guide_language, $travelers_data);

        // Cabeceras del email
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . $recipient . '>',
            'Reply-To: ' . $name . ' <' . $email . '>'
        );

        // Enviar email
        $mail_sent = wp_mail($recipient, $subject, $email_html, $headers);

        if ($mail_sent) {
            // Guardar la reserva de tour en la base de datos
            $this->save_tour_booking_to_database($name, $email, $phone, $service, $date, $time, $message, $duration, $visitors, $accommodation, $room_config, $pickup_location, $special_requests, $guide_language, $travelers_data);

            // Enviar confirmación al cliente para tour
            $this->send_tour_confirmation_email($name, $email, $service, $duration, $date, $time, $recipient, $visitors, $phone, $pickup_location);

            wp_send_json_success(__('Tu reserva de tour ha sido recibida. Te contactaremos pronto para confirmar los detalles del viaje.', $this->translate));
        } else {
            wp_send_json_error(__('There was a problem submitting your booking. Please try again later or contact us directly.', $this->translate));
        }

        exit;
    }

    /**
     * Crear plantilla HTML específica para correos de reserva de tours
     */
    private function create_tour_email_template($name, $email, $phone, $service, $duration, $date, $time, $message, $visitors, $accommodation, $room_config, $pickup_location, $special_requests, $guide_language = '', $travelers_data = [])
    {
        // Obtener colores del tema
        $accent_color = get_theme_mod('tours_booking_form_accent_color', '#4F8A8B');
        $dark_color = '#2C3E50';
        $light_color = '#F7EDE2';
        $adventure_color = '#2D5016';

        // Formatear la duración para mostrarla bonita
        $formatted_duration = '';
        if (!empty($duration)) {
            // Formatear la duración para el email (ejemplo: "3days-$450" a "3 días - $450")
            $formatted_duration = str_replace('days-', ' días - $', $duration);
        }

        // Obtener el logo del sitio
        $logo_url = '';
        $custom_logo_id = get_theme_mod('custom_logo');
        if ($custom_logo_id) {
            $logo_info = wp_get_attachment_image_src($custom_logo_id, 'full');
            if ($logo_info) {
                $logo_url = $logo_info[0];
            }
        }

        // Encabezado del sitio si no hay logo
        $site_name = get_bloginfo('name');

        // Construir el HTML del correo específico para tours
        $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>' . __('Nueva Reserva de Tour', $this->translate) . '</title>
        <style>
            body {
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                line-height: 1.6;
                color: #333;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                margin: 0;
                padding: 20px;
            }
            .email-container {
                max-width: 650px;
                margin: 0 auto;
                background-color: #fff;
                border-radius: 15px;
                overflow: hidden;
                box-shadow: 0 15px 35px rgba(0,0,0,0.15);
            }
            .email-header {
                background: linear-gradient(135deg, ' . $accent_color . ' 0%, ' . $adventure_color . ' 100%);
                color: white;
                padding: 30px 24px;
                text-align: center;
                position: relative;
            }
            .email-header::after {
                content: "";
                position: absolute;
                bottom: -10px;
                left: 0;
                right: 0;
                height: 20px;
                background: url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 1200 120\' fill=\'white\'%3E%3Cpath d=\'M0,60 Q300,120 600,60 T1200,60 L1200,120 L0,120 Z\'/%3E%3C/svg%3E") no-repeat center bottom;
                background-size: cover;
            }
            .email-body {
                padding: 30px 24px;
            }
            .email-footer {
                background: linear-gradient(135deg, ' . $light_color . ' 0%, #E8D5C4 100%);
                padding: 20px 24px;
                text-align: center;
                color: #666;
                font-size: 14px;
            }
            .logo {
                max-width: 180px;
                height: auto;
                margin-bottom: 15px;
                filter: brightness(0) invert(1);
            }
            h1 {
                margin: 0;
                font-size: 28px;
                font-weight: 600;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            }
            h2 {
                margin: 0 0 25px 0;
                font-size: 22px;
                color: ' . $accent_color . ';
                border-bottom: 2px solid ' . $accent_color . ';
                padding-bottom: 12px;
                display: flex;
                align-items: center;
            }
            h2 svg {
                margin-right: 10px;
                width: 24px;
                height: 24px;
            }
            .tour-details {
                background: linear-gradient(135deg, ' . $light_color . ' 0%, #F0F8FF 100%);
                border-radius: 12px;
                padding: 25px;
                margin-bottom: 25px;
                border-left: 5px solid ' . $accent_color . ';
            }
            .detail-row {
                margin-bottom: 15px;
                padding-bottom: 15px;
                border-bottom: 1px solid #e8e8e8;
                display: flex;
                align-items: center;
            }
            .detail-row:last-child {
                margin-bottom: 0;
                padding-bottom: 0;
                border-bottom: none;
            }
            .detail-icon {
                width: 24px;
                height: 24px;
                margin-right: 12px;
                color: ' . $accent_color . ';
                flex-shrink: 0;
            }
            .label {
                font-weight: 600;
                color: ' . $dark_color . ';
                min-width: 140px;
                margin-right: 15px;
            }
            .value {
                color: #555;
                flex-grow: 1;
            }
            .accommodation-section, .travel-section, .special-section {
                background: #fff;
                border-radius: 10px;
                padding: 20px;
                margin-bottom: 20px;
                border: 1px solid #e0e0e0;
            }
            .accommodation-section {
                border-left: 5px solid #FFA500;
            }
            .travel-section {
                border-left: 5px solid #228B22;
            }
            .special-section {
                border-left: 5px solid #FF6347;
            }
            .traveler-count {
                background: linear-gradient(135deg, #4CAF50, #45a049);
                color: white;
                padding: 8px 16px;
                border-radius: 20px;
                font-weight: bold;
                display: inline-block;
                margin-left: 10px;
            }
            .highlight-box {
                background: linear-gradient(135deg, #FFE4B5, #F0E68C);
                border-radius: 8px;
                padding: 15px;
                margin: 15px 0;
                border-left: 4px solid #DAA520;
            }
        </style>
    </head>
    <body>
        <div class="email-container">
            <div class="email-header">
                ' . ($logo_url ? '<img src="' . esc_url($logo_url) . '" alt="' . esc_attr($site_name) . '" class="logo">' : '<h1>' . esc_html($site_name) . '</h1>') . '
                <h1>🎯 Nueva Reserva de Tour</h1>
                <p style="margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;">Solicitud de viaje recibida</p>
            </div>
            <div class="email-body">
                <p style="font-size: 16px; margin-bottom: 25px;">Se ha recibido una nueva solicitud de reserva de tour con los siguientes detalles:</p>
                
                <div class="tour-details">
                    <h2>
                        <svg fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                        Información del Tour
                    </h2>
                    
                    <div class="detail-row">
                        <svg class="detail-icon" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        <span class="label">Tour Seleccionado:</span>
                        <span class="value"><strong>' . esc_html($service) . '</strong></span>
                    </div>';

        // Añadir duración si está disponible
        if (!empty($formatted_duration)) {
            $html .= '
                    <div class="detail-row">
                        <svg class="detail-icon" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M16.2,16.2L11,13V7H12.5V12.2L17,14.9L16.2,16.2Z"/>
                        </svg>
                        <span class="label">Duración/Precio:</span>
                        <span class="value"><strong>' . esc_html($formatted_duration) . '</strong></span>
                    </div>';
        }

        $html .= '
                    <div class="detail-row">
                        <svg class="detail-icon" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                        </svg>
                        <span class="label">Fecha de Salida:</span>
                        <span class="value">' . esc_html($date) . '</span>
                    </div>
                    <div class="detail-row">
                        <svg class="detail-icon" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M16.2,16.2L11,13V7H12.5V12.2L17,14.9L16.2,16.2Z"/>
                        </svg>
                        <span class="label">Hora de Salida:</span>
                        <span class="value">' . esc_html($time) . '</span>
                    </div>
                    <div class="detail-row">
                        <svg class="detail-icon" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zM4 18v-6h13v-2.5c0-1.1-.9-2-2-2h-2V4h6.3c.42 0 .8.15 1.1.44l1.6 1.6c.3.3.45.68.45 1.1V17c0 .55-.45 1-1 1s-1-.45-1-1v-2H4v2c0 .55-.45 1-1 1s-1-.45-1-1z"/>
                        </svg>
                        <span class="label">Número de Viajeros:</span>
                        <span class="value">' . esc_html($visitors) . ' ' . ($visitors == 1 ? 'viajero' : 'viajeros') . '<span class="traveler-count">' . $visitors . '</span></span>
                    </div>
                </div>

                <h2>
                    <svg fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2c-4 0-8 .5-8 4v9.5C4 17.43 5.57 19 7.5 19L6 20.5v.5h2.23l2-2H14l2 2h2.23v-.5L17 19c1.93 0 3.5-1.57 3.5-3.5V6c0-3.5-4-4-8-4zM7.5 17c-.83 0-1.5-.67-1.5-1.5S6.67 14 7.5 14s1.5.67 1.5 1.5S8.33 17 7.5 17zm9 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
                    </svg>
                    Información de Contacto
                </h2>
                
                <div class="tour-details">
                    <div class="detail-row">
                        <svg class="detail-icon" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        <span class="label">Nombre Completo:</span>
                        <span class="value"><strong>' . esc_html($name) . '</strong></span>
                    </div>
                    <div class="detail-row">
                        <svg class="detail-icon" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                        </svg>
                        <span class="label">Email:</span>
                        <span class="value">' . esc_html($email) . '</span>
                    </div>
                    <div class="detail-row">
                        <svg class="detail-icon" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                        </svg>
                        <span class="label">Teléfono/WhatsApp:</span>
                        <span class="value">' . esc_html($phone) . '</span>
                    </div>
                </div>';

        // Añadir sección de alojamiento si está disponible
        if (!empty($accommodation) || !empty($room_config)) {
            $html .= '
                <div class="accommodation-section">
                    <h2>
                        <svg fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7 13c1.66 0 3-1.34 3-3S8.66 7 7 7s-3 1.34-3 3 1.34 3 3 3zm12-6h-8v7H3V6H1v15h2v-3h18v3h2v-9c0-2.21-1.79-4-4-4z"/>
                        </svg>
                        Detalles de Alojamiento
                    </h2>';
            
            if (!empty($accommodation)) {
                $accommodation_formatted = str_replace('-', ' ', $accommodation);
                $accommodation_formatted = ucwords(str_replace(['hotel', 'stars'], ['Hotel', 'Estrellas'], $accommodation_formatted));
                $html .= '
                    <div class="detail-row">
                        <svg class="detail-icon" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7 13c1.66 0 3-1.34 3-3S8.66 7 7 7s-3 1.34-3 3 1.34 3 3 3zm12-6h-8v7H3V6H1v15h2v-3h18v3h2v-9c0-2.21-1.79-4-4-4z"/>
                        </svg>
                        <span class="label">Tipo de Alojamiento:</span>
                        <span class="value">' . esc_html($accommodation_formatted) . '</span>
                    </div>';
            }
            
            if (!empty($room_config)) {
                $room_formatted = ucwords(str_replace('-', ' ', $room_config));
                $html .= '
                    <div class="detail-row">
                        <svg class="detail-icon" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7 13c1.66 0 3-1.34 3-3S8.66 7 7 7s-3 1.34-3 3 1.34 3 3 3zm12-6h-8v7H3V6H1v15h2v-3h18v3h2v-9c0-2.21-1.79-4-4-4z"/>
                        </svg>
                        <span class="label">Configuración de Habitación:</span>
                        <span class="value">' . esc_html($room_formatted) . '</span>
                    </div>';
            }
            
            $html .= '</div>';
        }

        // Añadir sección de recojo si está disponible
        if (!empty($pickup_location)) {
            $html .= '
                <div class="travel-section">
                    <h2>
                        <svg fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                        Detalles de Recojo
                    </h2>
                    <div class="detail-row">
                        <svg class="detail-icon" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                        <span class="label">Lugar de Recojo:</span>
                        <span class="value">' . esc_html($pickup_location) . '</span>
                    </div>
                </div>';
        }

        // Añadir sección de mensajes/solicitudes especiales si está disponible
        if (!empty($message) || !empty($special_requests)) {
            $html .= '
                <div class="special-section">
                    <h2>
                        <svg fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
                        </svg>
                        Solicitudes y Comentarios
                    </h2>';
            
            if (!empty($message)) {
                $html .= '
                    <div class="highlight-box">
                        <p style="margin: 0 0 10px 0; font-weight: bold; color: #8B4513;">Preferencias de Viaje:</p>
                        <p style="margin: 0; color: #654321;">' . nl2br(esc_html($message)) . '</p>
                    </div>';
            }
            
            if (!empty($special_requests)) {
                $html .= '
                    <div class="highlight-box" style="background: linear-gradient(135deg, #FFE4E1, #FFF8DC);">
                        <p style="margin: 0 0 10px 0; font-weight: bold; color: #B8860B;">Solicitudes Especiales:</p>
                        <p style="margin: 0; color: #8B7355;">' . nl2br(esc_html($special_requests)) . '</p>
                    </div>';
            }
            
            $html .= '</div>';
        }
        
        // Add guide language section if provided
        if (!empty($guide_language)) {
            $language_names = [
                'spanish' => 'Español',
                'english' => 'Inglés', 
                'portuguese' => 'Portugués',
                'french' => 'Francés',
                'german' => 'Alemán',
                'italian' => 'Italiano'
            ];
            $language_display = isset($language_names[$guide_language]) ? $language_names[$guide_language] : ucfirst($guide_language);
            
            $html .= '
                <div class="special-section">
                    <h2>
                        <svg style="width: 24px; height: 24px; margin-right: 8px; vertical-align: middle;" fill="' . $accent_color . '" viewBox="0 0 24 24">
                            <path d="M12.87 15.07l-2.54-2.51.03-.03c1.74-1.94 2.01-4.65.75-6.78l-.46-.67c-.14-.2-.42-.2-.56 0l-.46.68c-1.26 2.13-.99 4.84.75 6.78l.03.03-2.54 2.51c-.42.42-.42 1.1 0 1.52s1.1.42 1.52 0L12 14.59l2.35 2.36c.42.42 1.1.42 1.52 0s.42-1.1 0-1.52z"/>
                        </svg>
                        Idioma del Guía Solicitado
                    </h2>
                    <div class="highlight-box" style="background: linear-gradient(135deg, #E6F3FF, #F0F8FF);">
                        <p style="margin: 0; font-size: 16px; color: #2C5282; font-weight: 600;">' . esc_html($language_display) . '</p>
                    </div>
                </div>';
        }
        
        // Add travelers data section if provided
        if (!empty($travelers_data) && is_array($travelers_data)) {
            $html .= '
                <div class="special-section">
                    <h2>
                        <svg style="width: 24px; height: 24px; margin-right: 8px; vertical-align: middle;" fill="' . $accent_color . '" viewBox="0 0 24 24">
                            <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zM4 18v-4.8c0-1.45.98-2.69 2.4-3.05l3.6-.93V8.45c0-.8.65-1.45 1.45-1.45h4.1c.8 0 1.45.65 1.45 1.45V10l3.6.93c1.42.36 2.4 1.6 2.4 3.05V18h-19z"/>
                        </svg>
                        Información de Viajeros
                    </h2>
                    <div class="highlight-box" style="background: linear-gradient(135deg, #FFF5EE, #FFFAF0);">';
            
            foreach ($travelers_data as $index => $traveler) {
                $traveler_num = $index + 1;
                $html .= '<div style="margin-bottom: 15px; padding: 10px; background: rgba(255,255,255,0.7); border-radius: 8px;">';
                $html .= '<h4 style="margin: 0 0 8px 0; color: #8B4513; font-size: 14px;">Viajero ' . $traveler_num . '</h4>';
                
                if (!empty($traveler['name'])) {
                    $html .= '<p style="margin: 2px 0; font-size: 13px;"><strong>Nombre:</strong> ' . esc_html($traveler['name']) . '</p>';
                }
                if (!empty($traveler['age'])) {
                    $html .= '<p style="margin: 2px 0; font-size: 13px;"><strong>Edad:</strong> ' . esc_html($traveler['age']) . ' años</p>';
                }
                if (!empty($traveler['documentType']) && !empty($traveler['documentNumber'])) {
                    $doc_types = ['dni' => 'DNI', 'passport' => 'Pasaporte', 'ce' => 'Carnet de Extranjería'];
                    $doc_type_display = isset($doc_types[$traveler['documentType']]) ? $doc_types[$traveler['documentType']] : ucfirst($traveler['documentType']);
                    $html .= '<p style="margin: 2px 0; font-size: 13px;"><strong>Documento:</strong> ' . esc_html($doc_type_display) . ' - ' . esc_html($traveler['documentNumber']) . '</p>';
                }
                if (!empty($traveler['dietaryRestrictions'])) {
                    $html .= '<p style="margin: 2px 0; font-size: 13px;"><strong>Restricciones Alimentarias:</strong> ' . esc_html($traveler['dietaryRestrictions']) . '</p>';
                }
                if (!empty($traveler['medicalConditions'])) {
                    $html .= '<p style="margin: 2px 0; font-size: 13px;"><strong>Condiciones Médicas:</strong> ' . esc_html($traveler['medicalConditions']) . '</p>';
                }
                
                $html .= '</div>';
            }
            
            $html .= '</div>
                </div>';
        }

        $html .= '
            </div>
            <div class="email-footer">
                <p style="margin: 0 0 10px 0;"><strong>📧 Responder a este email:</strong> ' . esc_html($email) . '</p>
                <p style="margin: 0; font-size: 12px; color: #999;">Esta reserva fue enviada desde ' . esc_html($site_name) . ' el ' . date('d/m/Y') . ' a las ' . date('H:i') . '</p>
            </div>
        </div>
    </body>
    </html>';

        return $html;
    }
}

// Inicializar la clase
new WPTBT_Booking_Block();
