<?php

/**
 * Bloque de Reservas para Spa
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
                    'default' => __('Book Now', $this->translate)
                ],
                'subtitle' => [
                    'type' => 'string',
                    'default' => __('Appointment', $this->translate)
                ],
                'description' => [
                    'type' => 'string',
                    'default' => __('Book your spa treatment and enjoy a moment of relaxation.', $this->translate)
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
                        ['name' => __('Swedish Massage', $this->translate), 'duration' => __('60 min', $this->translate), 'price' => __('$90', $this->translate)],
                        ['name' => __('Deep Tissue Massage', $this->translate), 'duration' => __('60 min', $this->translate), 'price' => __('$120', $this->translate)],
                        ['name' => __('Hot Stone Massage', $this->translate), 'duration' => __('90 min', $this->translate), 'price' => __('$150', $this->translate)],
                        ['name' => __('Aromatherapy Massage', $this->translate), 'duration' => __('60 min', $this->translate), 'price' => __('$110', $this->translate)]
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
     * Función para obtener los servicios con sus datos de duración y precio
     * Agrega esta función en class-wptbt-booking-block.php
     */
    private function get_services_with_durations()
    {
        // Obtener todos los servicios
        $args = [
            'post_type'      => 'servicio',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'DESC',
        ];

        $servicios = get_posts($args);

        // Verificar si hay servicios disponibles
        if (empty($servicios)) {
            return '<div class="p-8 bg-white rounded-lg shadow-md text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <h3 class="text-xl font-medium text-gray-800 mb-2">' . esc_html__('No hay servicios disponibles', $this->translate) . '</h3>
            <p class="text-gray-600">' . esc_html__('Por favor, añade servicios desde el panel de administración para habilitar las reservas.', $this->translate) . '</p>
        </div>';
        }

        $services_data = [];

        // Procesar cada servicio
        foreach ($servicios as $servicio) {
            // Obtener horarios disponibles
            $hours = get_post_meta($servicio->ID, '_wptbt_service_hours', true) ?: [];

            // CORREGIDO: Obtener múltiples precios del nuevo formato
            $prices = get_post_meta($servicio->ID, '_wptbt_service_prices', true);
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
                // Fallback: usar formato anterior si el nuevo no existe
                $duration1 = get_post_meta($servicio->ID, '_wptbt_service_duration1', true) ?: '';
                $price1 = get_post_meta($servicio->ID, '_wptbt_service_price1', true) ?: '';
                $duration2 = get_post_meta($servicio->ID, '_wptbt_service_duration2', true) ?: '';
                $price2 = get_post_meta($servicio->ID, '_wptbt_service_price2', true) ?: '';

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

            // Obtener subtítulo del servicio si existe
            $subtitle = get_post_meta($servicio->ID, '_wptbt_service_subtitle', true) ?: '';

            // VERIFICACIÓN: Log de debug para identificar problemas
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("Service {$servicio->ID} ({$servicio->post_title}): " .
                    count($hours) . " hours, " . count($durations) . " durations");
            }

            // Añadir servicio a la lista
            $services_data[] = [
                'id' => (string)$servicio->ID, // IMPORTANTE: Convertir a string para consistencia
                'title' => $servicio->post_title,
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

        $servicios_data = $this->get_services_with_durations();

        // Si hay servicios pero no se usa Solid.js, preparamos las opciones de servicio para el formulario tradicional
        $service_options = '';
        $first_service = null;
        $first_service_hours = [];

        if (!$useSolidJs) {
            // Preparar opciones para el formulario tradicional
            foreach ($servicios as $index => $servicio) {
                $selected = ($index === 0) ? 'selected' : '';
                $hours = get_post_meta($servicio->ID, '_wptbt_service_hours', true);
                if (!$hours) $hours = [];

                // Guardar información del primer servicio para usarla más tarde
                if ($index === 0) {
                    $first_service = $servicio;
                    $first_service_hours = $hours;
                }

                // Duraciones y precios para mostrar en la opción
                $duration1 = get_post_meta($servicio->ID, '_wptbt_service_duration1', true) ?: '';
                $price1 = get_post_meta($servicio->ID, '_wptbt_service_price1', true) ?: '';

                $service_options .= '<option value="' . esc_attr($servicio->post_title) . '" ' . $selected . '>' .
                    esc_html($servicio->post_title);

                // Añadir información de precio/duración si está disponible
                if (!empty($duration1) && !empty($price1)) {
                    $service_options .= ' (' . esc_html($duration1) . ' - ' . esc_html($price1) . ')';
                }

                $service_options .= '</option>';
            }
        }

        // Configurar datos JSON para Solid.js
        $json_data = wp_json_encode($servicios_data);
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
                                            'services' => $servicios_data,
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

        // Obtener el servicio - podría ser un ID o un título
        $service = sanitize_text_field($_POST['service']);
        $service_title = '';

        // Si el servicio es numérico, probablemente sea un ID
        if (is_numeric($service)) {
            // Intentar obtener el título del servicio usando el ID
            $service_post = get_post(intval($service));
            if ($service_post && $service_post->post_type == 'servicio') {
                $service_title = $service_post->post_title;
            }
        }

        // Si se encontró un título, usarlo; de lo contrario, usar el valor original
        // Esto también funciona si el valor ya era el título del servicio
        $service = !empty($service_title) ? $service_title : $service;

        // Alternativamente, si se envió service_title directamente (como en el formulario de single-servicio.php)
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

        // Destinatario del email - Prioridad:
        // 1. Valor enviado en el formulario 
        // 2. Valor del customizer
        // 3. Email del administrador del sitio
        $recipient = isset($_POST['recipient_email']) && !empty($_POST['recipient_email'])
            ? sanitize_email($_POST['recipient_email'])
            : get_theme_mod('services_booking_form_email', get_option('admin_email'));

        // Asunto del email
        $subject = sprintf(__('New Booking from %s', $this->translate), $name);

        // Crear un email HTML atractivo
        $email_html = $this->create_html_email_template($name, $email, $service, $duration, $date, $time, $message, $visitors);

        // Cabeceras del email
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . $recipient . '>',
            'Reply-To: ' . $name . ' <' . $email . '>'
        );

        // Enviar email
        $mail_sent = wp_mail($recipient, $subject, $email_html, $headers);

        if ($mail_sent) {
            // Guardar la reserva en la base de datos
            $this->save_booking_to_database($name, $email, $service, $date, $time, $message, $duration, $visitors);

            // Opcional: Enviar confirmación al cliente
            $this->send_confirmation_email($name, $email, $service, $duration, $date, $time, $recipient, $visitors);

            wp_send_json_success(__('Your booking has been received. We will contact you soon to confirm.', $this->translate));
        } else {
            wp_send_json_error(__('There was a problem submitting your booking. Please try again later or contact us directly.', $this->translate));
        }

        exit;
    }

    /**
     * Crear plantilla HTML atractiva para el correo
     */
    private function create_html_email_template($name, $email, $service, $duration, $date, $time, $message, $visitors = 1)
    {
        // Obtener colores del tema
        $accent_color = get_theme_mod('services_booking_form_accent_color', '#D4B254');
        $dark_color = '#333333';
        $light_color = '#f7f7f7';

        // Formatear la duración para mostrarla bonita
        $formatted_duration = '';
        if (!empty($duration)) {
            // Formatear la duración para el email (ejemplo: "60min-$90" a "60 minutos - $90")
            $formatted_duration = str_replace('min-', ' minutos - ', $duration);
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

        // Construir el HTML del correo
        $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>' . __('New Booking', $this->translate) . '</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                background-color: #f9f9f9;
                margin: 0;
                padding: 0;
            }
            .email-container {
                max-width: 600px;
                margin: 0 auto;
                background-color: #fff;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            }
            .email-header {
                background-color: ' . $accent_color . ';
                color: white;
                padding: 24px;
                text-align: center;
            }
            .email-body {
                padding: 24px;
            }
            .email-footer {
                background-color: ' . $light_color . ';
                padding: 15px 24px;
                text-align: center;
                color: #666;
                font-size: 14px;
            }
            .logo {
                max-width: 200px;
                height: auto;
                margin-bottom: 15px;
            }
            h1 {
                margin: 0;
                font-size: 24px;
                font-weight: normal;
            }
            h2 {
                margin: 0 0 20px 0;
                font-size: 20px;
                color: ' . $accent_color . ';
                border-bottom: 1px solid #eee;
                padding-bottom: 10px;
            }
            .booking-details {
                background-color: ' . $light_color . ';
                border-radius: 6px;
                padding: 20px;
                margin-bottom: 20px;
            }
            .booking-row {
                margin-bottom: 12px;
                padding-bottom: 12px;
                border-bottom: 1px solid #e8e8e8;
            }
            .booking-row:last-child {
                margin-bottom: 0;
                padding-bottom: 0;
                border-bottom: none;
            }
            .label {
                font-weight: bold;
                color: ' . $dark_color . ';
                padding-right: 10px;
            }
            .value {
                color: #555;
            }
            .message-section {
                margin-top: 20px;
                background-color: #fff;
                border-left: 4px solid ' . $accent_color . ';
                padding: 15px;
            }
            .button {
                display: inline-block;
                background-color: ' . $accent_color . ';
                color: white;
                padding: 12px 24px;
                text-decoration: none;
                border-radius: 4px;
                margin-top: 20px;
            }
        </style>
    </head>
    <body>
        <div class="email-container">
            <div class="email-header">
                ' . ($logo_url ? '<img src="' . esc_url($logo_url) . '" alt="' . esc_attr($site_name) . '" class="logo">' : '<h1>' . esc_html($site_name) . '</h1>') . '
                <h1>' . __('New Booking Reservation', $this->translate) . '</h1>
            </div>
            <div class="email-body">
                <p>' . __('A new booking has been submitted with the following details:', $this->translate) . '</p>
                
                <div class="booking-details">
                    <div class="booking-row">
                        <span class="label">' . __('Name:', $this->translate) . '</span>
                        <span class="value">' . esc_html($name) . '</span>
                    </div>
                    <div class="booking-row">
                        <span class="label">' . __('Email:', $this->translate) . '</span>
                        <span class="value">' . esc_html($email) . '</span>
                    </div>
                    <div class="booking-row">
                        <span class="label">' . __('Service:', $this->translate) . '</span>
                        <span class="value">' . esc_html($service) . '</span>
                    </div>';

        // Añadir duración si está disponible
        if (!empty($formatted_duration)) {
            $html .= '
                    <div class="booking-row">
                        <span class="label">' . __('Duration/Price:', $this->translate) . '</span>
                        <span class="value">' . esc_html($formatted_duration) . '</span>
                    </div>';
        }

        $html .= '
                    <div class="booking-row">
                        <span class="label">' . __('Date:', $this->translate) . '</span>
                        <span class="value">' . esc_html($date) . '</span>
                    </div>
                    <div class="booking-row">
                        <span class="label">' . __('Time:', $this->translate) . '</span>
                        <span class="value">' . esc_html($time) . '</span>
                    </div>
                    <div class="booking-row">
                        <span class="label">' . __('Number of Visitors:', $this->translate) . '</span>
                        <span class="value">' . esc_html($visitors) . '</span>
                    </div>
                </div>';

        // Añadir mensaje si está disponible
        if (!empty($message)) {
            $html .= '
                <div class="message-section">
                    <p class="label">' . __('Message from customer:', $this->translate) . '</p>
                    <p class="value">' . nl2br(esc_html($message)) . '</p>
                </div>';
        }

        $html .= '
                <p>' . __('Please contact the customer to confirm their appointment.', $this->translate) . '</p>
                
                <a href="mailto:' . esc_attr($email) . '" class="button">' . __('Reply to Customer', $this->translate) . '</a>
            </div>
            <div class="email-footer">
                <p>' . sprintf(__('This booking was submitted from %s on %s', $this->translate), get_bloginfo('name'), date_i18n(get_option('date_format') . ' ' . get_option('time_format'))) . '</p>
            </div>
        </div>
    </body>
    </html>';

        return $html;
    }

    /**
     * Enviar correo de confirmación al cliente
     */
    private function send_confirmation_email($name, $email, $service, $duration, $date, $time, $recipient, $visitors = 1)
    {
        // Asunto del email de confirmación
        $subject = sprintf(__('Your booking at %s - Confirmation', $this->translate), get_bloginfo('name'));

        // Obtener colores del tema
        $accent_color = get_theme_mod('services_booking_form_accent_color', '#D4B254');
        $dark_color = '#333333';
        $light_color = '#f7f7f7';

        // Formatear la duración para mostrarla bonita
        $formatted_duration = '';
        if (!empty($duration)) {
            $formatted_duration = str_replace('min-', ' minutos - ', $duration);
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

        // Construir el HTML del correo de confirmación
        $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>' . __('Booking Confirmation', $this->translate) . '</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                background-color: #f9f9f9;
                margin: 0;
                padding: 0;
            }
            .email-container {
                max-width: 600px;
                margin: 0 auto;
                background-color: #fff;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            }
            .email-header {
                background-color: ' . $accent_color . ';
                color: white;
                padding: 24px;
                text-align: center;
            }
            .email-body {
                padding: 24px;
            }
            .email-footer {
                background-color: ' . $light_color . ';
                padding: 15px 24px;
                text-align: center;
                color: #666;
                font-size: 14px;
            }
            .logo {
                max-width: 200px;
                height: auto;
                margin-bottom: 15px;
            }
            h1 {
                margin: 0;
                font-size: 24px;
                font-weight: normal;
            }
            h2 {
                margin: 0 0 20px 0;
                font-size: 20px;
                color: ' . $accent_color . ';
                border-bottom: 1px solid #eee;
                padding-bottom: 10px;
            }
            .booking-details {
                background-color: ' . $light_color . ';
                border-radius: 6px;
                padding: 20px;
                margin-bottom: 20px;
            }
            .booking-row {
                margin-bottom: 12px;
                padding-bottom: 12px;
                border-bottom: 1px solid #e8e8e8;
            }
            .booking-row:last-child {
                margin-bottom: 0;
                padding-bottom: 0;
                border-bottom: none;
            }
            .label {
                font-weight: bold;
                color: ' . $dark_color . ';
                padding-right: 10px;
            }
            .value {
                color: #555;
            }
            .message-box {
                background-color: #f8f9fa;
                border-left: 4px solid ' . $accent_color . ';
                padding: 15px;
                margin: 20px 0;
            }
            .button {
                display: inline-block;
                background-color: ' . $accent_color . ';
                color: white;
                padding: 12px 24px;
                text-decoration: none;
                border-radius: 4px;
                margin-top: 20px;
            }
        </style>
    </head>
    <body>
        <div class="email-container">
            <div class="email-header">
                ' . ($logo_url ? '<img src="' . esc_url($logo_url) . '" alt="' . esc_attr($site_name) . '" class="logo">' : '<h1>' . esc_html($site_name) . '</h1>') . '
                <h1>' . __('Booking Confirmation', $this->translate) . '</h1>
            </div>
            <div class="email-body">
                <p>' . sprintf(__('Dear %s,', $this->translate), esc_html($name)) . '</p>
                
                <p>' . __('Thank you for your booking. We have received your reservation request and our team will contact you shortly to confirm it.', $this->translate) . '</p>
                
                <h2>' . __('Your Booking Details', $this->translate) . '</h2>
                
                <div class="booking-details">
                    <div class="booking-row">
                        <span class="label">' . __('Service:', $this->translate) . '</span>
                        <span class="value">' . esc_html($service) . '</span>
                    </div>';

        // Añadir duración si está disponible
        if (!empty($formatted_duration)) {
            $html .= '
                    <div class="booking-row">
                        <span class="label">' . __('Duration/Price:', $this->translate) . '</span>
                        <span class="value">' . esc_html($formatted_duration) . '</span>
                    </div>';
        }

        $html .= '
                    <div class="booking-row">
                        <span class="label">' . __('Date:', $this->translate) . '</span>
                        <span class="value">' . esc_html($date) . '</span>
                    </div>
                    <div class="booking-row">
                        <span class="label">' . __('Time:', $this->translate) . '</span>
                        <span class="value">' . esc_html($time) . '</span>
                    </div>
                    <div class="booking-row">
                        <span class="label">' . __('Number of Visitors:', $this->translate) . '</span>
                        <span class="value">' . esc_html($visitors) . '</span>
                    </div>
                </div>
                
                <div class="message-box">
                    <p>' . __('Please note: This is an automatic confirmation of your booking request. Our staff will contact you to confirm the availability and finalize your reservation.', $this->translate) . '</p>
                </div>
                
                <p>' . __('If you need to make any changes to your reservation, please contact us directly.', $this->translate) . '</p>
                
                <p>' . __('We look forward to seeing you soon!', $this->translate) . '</p>
                
                <p>' . sprintf(__('The %s Team', $this->translate), esc_html($site_name)) . '</p>
            </div>
            <div class="email-footer">
                <p>' . sprintf(__('This email was sent from %s | %s', $this->translate), esc_html($site_name), esc_url(home_url())) . '</p>
            </div>
        </div>
    </body>
    </html>';

        // Cabeceras
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . $recipient . '>'
        );

        // Enviar el correo de confirmación al cliente
        wp_mail($email, $subject, $html, $headers);
    }

    /**
     * Guardar la reserva en la base de datos (opcional)
     */
    private function save_booking_to_database($name, $email, $service, $date, $time, $message, $duration = '', $visitors = 1)
    {
        global $wpdb;

        // Comprobar si la tabla existe, si no, crearla
        $table_name = $wpdb->prefix . 'spa_bookings';

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time_created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            name tinytext NOT NULL,
            email tinytext NOT NULL,
            service tinytext NOT NULL,
            date date NOT NULL,
            time_slot time NOT NULL,
            message text,
            duration varchar(50),
            visitors int DEFAULT 1 NOT NULL,
            status varchar(20) DEFAULT 'pending' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }

        // Insertar la reserva en la base de datos
        $wpdb->insert(
            $table_name,
            array(
                'time_created' => current_time('mysql'),
                'name' => $name,
                'email' => $email,
                'service' => $service,
                'date' => $date,
                'time_slot' => $time,
                'message' => $message,
                'duration' => $duration,
                'visitors' => $visitors,
                'status' => 'pending'
            )
        );
    }

    /**
     * Renderizar shortcode de reservas
     *
     * @param array $atts Atributos del shortcode.
     * @return string HTML del shortcode.
     */
    public function render_booking_shortcode($atts)
    {
        $attributes = shortcode_atts(
            array(
                'title' => __('Book Now', $this->translate),
                'subtitle' => __('Appointment', $this->translate),
                'description' => __('Book your spa treatment and enjoy a moment of relaxation.', $this->translate),
                'image_id' => '',
                'image_url' => '',
                'button_text' => __('BOOK NOW', $this->translate),
                'button_color' => '#D4B254',
                'text_color' => '#FFFFFF',
                'accent_color' => '#D4B254',
                'email_recipient' => get_option('admin_email'),
                'use_solid_js' => true,
                'show_top_wave' => true,
                'show_bottom_wave' => true
            ),
            $atts
        );

        // Convertir atributos para el formato que espera render_booking_block
        $block_attributes = array(
            'title' => $attributes['title'],
            'subtitle' => $attributes['subtitle'],
            'description' => $attributes['description'],
            'imageID' => !empty($attributes['image_id']) ? (int)$attributes['image_id'] : null,
            'imageURL' => $attributes['image_url'],
            'buttonText' => $attributes['button_text'],
            'buttonColor' => $attributes['button_color'],
            'textColor' => $attributes['text_color'],
            'accentColor' => $attributes['accent_color'],
            'emailRecipient' => $attributes['email_recipient'],
            'useSolidJs' => $attributes['use_solid_js'],
            'showTopWave' => $attributes['show_top_wave'],
            'showBottomWave' => $attributes['show_bottom_wave']
        );

        return $this->render_booking_block($block_attributes);
    }

    /**
     * Formatear hora para mostrar en formato AM/PM
     * 
     * @param string $time24h Hora en formato 24h
     * @return string Hora en formato AM/PM
     */
    public function formatTimeForDisplay($time24h)
    {
        // Convertir de formato 24h a 12h con AM/PM
        $time_parts = explode(':', $time24h);
        if (count($time_parts) < 2) return $time24h;

        $hours = intval($time_parts[0]);
        $minutes = $time_parts[1];

        if ($hours === 0) {
            return "12:{$minutes} AM";
        } elseif ($hours < 12) {
            return "{$hours}:{$minutes} AM";
        } elseif ($hours === 12) {
            return "12:{$minutes} PM";
        } else {
            return ($hours - 12) . ":{$minutes} PM";
        }
    }
}

// Inicializar la clase
new WPTBT_Booking_Block();
