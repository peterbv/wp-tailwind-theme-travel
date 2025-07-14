<?php

/**
 * Solid.js Loader para WordPress
 * 
 * Este archivo gestiona la carga modular de componentes Solid.js en WordPress
 * 
 * @package WPTBT
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Clase que maneja la carga de componentes Solid.js
 */
class WPTBT_Solid_JS_Loader
{
    /**
     * Dependencias de scripts registradas
     * @var array
     */
    private $dependencies = [];

    /**
     * Componentes registrados
     * @var array
     */
    private $registered_components = [];

    /**
     * Componentes cargados en la página actual
     * @var array
     */
    private $loaded_components = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Registrar hooks
        add_action('wp_enqueue_scripts', [$this, 'register_scripts'], 10);
        add_action('wp_footer', [$this, 'maybe_load_core'], 1);

        // Añadir shortcode para componentes Solid.js
        add_shortcode('solid', [$this, 'solid_shortcode']);
    }

    /**
     * Sistema de traducciones modulares para componentes Solid.js
     */
    public function wptbt_get_component_translations($component_name)
    {
        $translate_name = 'wptbt-' . $component_name . '-block';
        $translations = [];

        // Traducciones específicas para el componente de reservas de tours
        if ($component_name === 'booking-form') {
            $translations = [
                'Book Now' => __('Reserve Tour', $translate_name),
                'BOOK NOW' => __('RESERVE TOUR', $translate_name),
                'Name' => __('Name', $translate_name),
                'Email' => __('Email', $translate_name),
                'Select Service' => __('Select Tour', $translate_name),
                'Select a service' => __('Select a tour', $translate_name),
                'Select Date' => __('Select Departure Date', $translate_name),
                'Select date' => __('Select departure date', $translate_name),
                'Select Duration' => __('Select Duration', $translate_name),
                'Select Time' => __('Select Departure Time', $translate_name),
                'Select available time' => __('Select available departure time', $translate_name),
                'No available times' => __('No available departure times', $translate_name),
                'Additional Information (Optional)' => __('Special Requests or Dietary Requirements (Optional)', $translate_name),
                'Processing...' => __('Processing...', $translate_name),
                'CONFIRM BOOKING' => __('CONFIRM RESERVATION', $translate_name),
                'Please enter your name' => __('Please enter your name', $translate_name),
                'Please enter your email' => __('Please enter your email', $translate_name),
                'Please select a service' => __('Please select a tour', $translate_name),
                'Please select a date for your booking' => __('Please select a departure date for your tour', $translate_name),
                'Please select a time' => __('Please select a departure time', $translate_name),
                'Processing your booking...' => __('Processing your reservation...', $translate_name),
                'Booking successfully made' => __('Tour reservation successfully made', $translate_name),
                'An error occurred. Please try again.' => __('An error occurred. Please try again.', $translate_name),
                'Connection error. Please try again later.' => __('Connection error. Please try again later.', $translate_name),
                'Full name' => __('Full name', $translate_name),
                'Number of Visitors' => __('Number of Travelers', $translate_name),
                'Number of people' => __('Number of travelers', $translate_name),
                'Select between 1 and 20 people' => __('Select between 1 and 20 travelers', $translate_name),
                'Decrease Visitors' => __('Decrease Travelers', $translate_name),
                'Increase Visitors' => __('Increase Travelers', $translate_name),
                'Personal Details' => __('Traveler Information', $translate_name),
                'Information to confirm your booking' => __('Information to confirm your tour reservation', $translate_name),
                'Confirm Booking' => __('Confirm Reservation', $translate_name),
                'Review all details before confirming' => __('Review all tour details before confirming', $translate_name),
                'Service Details' => __('Tour Details', $translate_name),
                'Appointment Date & Time' => __('Tour Departure Date & Time', $translate_name),
                'Contact Information' => __('Traveler Information', $translate_name),
                'Visitors' => __('Travelers', $translate_name),
                'Any special requests or additional information...' => __('Any special requests, dietary requirements, or additional information...', $translate_name),
            ];
        }

        // Traducciones para el componente de reseñas de Google
        elseif ($component_name === 'google-reviews') {
            $translations = [
                'What Our Clients Say' => __('What Our Clients Say', $translate_name),
                'Testimonials' => __('Testimonials', $translate_name),
                'Discover the experiences of those who have already enjoyed our services' => __('Discover the experiences of those who have already enjoyed our services', $translate_name),
                'Client' => __('Client', $translate_name),
                'Google Reviews: Place ID and API Key are required to load dynamic reviews' => __('Google Reviews: Place ID and API Key are required to load dynamic reviews', $translate_name),
                'Error in response' => __('Error in response', $translate_name),
                'Invalid response format' => __('Invalid response format', $translate_name),
                'Error loading reviews:' => __('Error loading reviews:', $translate_name),
                'Unable to load reviews. Please try again later.' => __('Unable to load reviews. Please try again later.', $translate_name),
                'Loading testimonials...' => __('Loading testimonials...', $translate_name),
                'Google Reviews' => __('Google Reviews', $translate_name),
                'Leave your review' => __('Leave your review', $translate_name),
                'Previous testimonial' => __('Previous testimonial', $translate_name),
                'Next testimonial' => __('Next testimonial', $translate_name),
                'Go to testimonial' => __('Go to testimonial', $translate_name),
            ];
        }

        // Traducciones para el componente de galería
        elseif ($component_name === 'gallery') {
            // Array con todas las cadenas traducibles en el componente
            $translations = [
                // Textos principales
                'Our Gallery' => __('Our Gallery', $translate_name),
                'Relaxation Spaces' => __('Relaxation Spaces', $translate_name),
                'Explore our facilities and services through our image gallery.' => __('Explore our facilities and services through our image gallery.', $translate_name),

                // Mensajes para editores
                'Please add images to the gallery from the block editor.' => __('Please add images to the gallery from the block editor.', $translate_name),

                // Lightbox y navegación
                'Gallery image' => __('Gallery image', $translate_name),
                'Thumbnail' => __('Thumbnail', $translate_name),
                'Previous slide' => __('Previous slide', $translate_name),
                'Next slide' => __('Next slide', $translate_name),
                'Go to slide' => __('Go to slide', $translate_name),
                'Previous image' => __('Previous image', $translate_name),
                'Next image' => __('Next image', $translate_name),
                'View full image' => __('View full image', $translate_name),
                'Close' => __('Close', $translate_name),

                // Estado de carga
                'Loading gallery...' => __('Loading gallery...', $translate_name),
            ];
        }

        // Traducciones para el componente de FAQ
        elseif ($component_name === 'faq') {
            $translations = [
                // Main texts
                'Frequently Asked Questions' => __('Frequently Asked Questions', $translate_name),
                'We answer your questions' => __('We answer your questions', $translate_name),
                'Do you have more questions?' => __('Do you have more questions?', $translate_name),

                // Navigation elements
                'Expand question' => __('Expand question', $translate_name),
                'Collapse question' => __('Collapse question', $translate_name),
                'View all questions' => __('View all questions', $translate_name),

                // Messages
                'Loading questions...' => __('Loading questions...', $translate_name),
                'No questions available' => __('No questions available', $translate_name),
                'Error loading questions' => __('Error loading questions', $translate_name),
            ];
        }
        // Traducciones para el componente de mapa interactivo
        elseif ($component_name === 'interactive-map') {
            $translations = [
                // Textos principales
                'Find Us' => __('Find Us', $translate_name),
                'Our Location' => __('Our Location', $translate_name),
                'Visit us and discover our relaxing spa in the heart of the city' => __('Visit us and discover our relaxing spa in the heart of the city', $translate_name),

                // Elementos de navegación y formulario
                'Get Directions' => __('Get Directions', $translate_name),
                'Starting point:' => __('Starting point:', $translate_name),
                'Enter address or location' => __('Enter address or location', $translate_name),
                'Use my current location' => __('Use my current location', $translate_name),
                'Travel mode:' => __('Travel mode:', $translate_name),
                'Driving' => __('Driving', $translate_name),
                'Walking' => __('Walking', $translate_name),
                'Bicycling' => __('Bicycling', $translate_name),
                'Transit' => __('Transit', $translate_name),
                'Go' => __('Go', $translate_name),
                'Route Info' => __('Route Info', $translate_name),
                'Distance' => __('Distance', $translate_name),
                'Duration' => __('Duration', $translate_name),
                'We couldn\'t get your current location. Please enable location services in your browser.' => __('We couldn\'t get your current location. Please enable location services in your browser.', $translate_name),

                // Puntos de interés
                'Points of Interest' => __('Points of Interest', $translate_name),
                'from destination' => __('from destination', $translate_name),

                // Contacto
                'Contact Information' => __('Contact Information', $translate_name),
                'Book an Appointment' => __('Book an Appointment', $translate_name),

                // Street View
                'Switch to Map View' => __('Switch to Map View', $translate_name),
                'Switch to Street View' => __('Switch to Street View', $translate_name),
                'Reset Map View' => __('Reset Map View', $translate_name),

                // Mensajes
                'Loading map...' => __('Loading map...', $translate_name),
                'Initializing map...' => __('Initializing map...', $translate_name),
            ];
        }

        // Traducciones comunes para todos los componentes
        $common_translations = [
            'Loading...' => __('Loading...', $translate_name),
            'Error' => __('Error', $translate_name),
            'Success' => __('Success', $translate_name),
        ];

        // Combinar traducciones específicas y comunes
        return array_merge($common_translations, $translations);
    }

    /**
     * Registrar scripts de Solid.js
     */
    public function register_scripts()
    {
        // Cargar explícitamente wp-i18n
        wp_enqueue_script('wp-i18n');

        // Registrar el núcleo de Solid.js
        wp_register_script(
            'wptbt-solid-core',
            get_template_directory_uri() . '/assets/public/js/solid-core.js',
            ['wp-i18n'], // Añadir wp-i18n como dependencia
            filemtime(get_template_directory() . '/assets/public/js/solid-core.js'),
            true
        );

        // Configurar traducciones para solid-core
        wp_set_script_translations('wptbt-solid-core', 'wp-tailwind-blocks', get_template_directory() . '/languages');


        // Marca el script como módulo
        add_filter('script_loader_tag', function ($tag, $handle, $src) {
            if ($handle === 'wptbt-solid-core' || strpos($handle, 'wptbt-solid-') === 0) {
                $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
            }
            return $tag;
        }, 10, 3);

        // Registrar componentes
        $this->register_component('booking-form', 'components/booking-form-module.js');

        $this->register_component('google-reviews', 'components/google-reviews-module.js');

        $this->register_component('gallery', 'components/gallery-module.js');

        $this->register_component('faq', 'components/faq-module.js');

        $this->register_component('interactive-map', 'components/interactive-map-module.js');
    }


    /**
     * Registrar un componente Solid.js
     * 
     * @param string $name Nombre del componente
     * @param string $path Ruta relativa al archivo JS del componente
     */
    public function register_component($name, $path)
    {
        $handle = 'wptbt-solid-' . $name;
        $full_path = get_template_directory_uri() . '/assets/public/js/' . $path;

        // Registrar el script (pero no cargarlo todavía)
        wp_register_script(
            $handle,
            $full_path,
            ['wptbt-solid-core', 'wp-i18n'], // Dependencia del núcleo
            filemtime(get_template_directory() . '/assets/public/js/' . $path),
            true // En footer
        );

        // Configurar traducciones para el componente
        wp_set_script_translations($handle, 'wp-tailwind-blocks', get_template_directory() . '/languages');

        // Localizar el script con traducciones específicas para este componente
        $translations = $this->wptbt_get_component_translations($name);
        if (!empty($translations)) {
            wp_localize_script($handle, 'wptbtI18n_' . str_replace('-', '_', $name), $translations);
        }

        // Almacenar información del componente
        $this->registered_components[$name] = [
            'handle' => $handle,
            'path' => $path
        ];
    }

    /**
     * Cargar un componente específico
     * 
     * @param string $component_name Nombre del componente a cargar
     * @return bool True si se cargó correctamente, false en caso contrario
     */
    public function load_component($component_name)
    {
        // Verificar si el componente está registrado
        if (!isset($this->registered_components[$component_name])) {
            error_log("Solid.js: Componente '$component_name' no encontrado");
            return false;
        }

        // Evitar cargar el mismo componente múltiples veces
        if (in_array($component_name, $this->loaded_components)) {
            return true;
        }

        // Marcar el componente como cargado
        $this->loaded_components[] = $component_name;

        // Cargar el script del componente
        $handle = $this->registered_components[$component_name]['handle'];
        wp_enqueue_script($handle);

        return true;
    }

    /**
     * Cargar el núcleo de Solid.js si hay componentes cargados
     */
    public function maybe_load_core()
    {
        if (!empty($this->loaded_components)) {
            wp_enqueue_script('wptbt-solid-core');
        }
    }

    /**
     * Shortcode para componentes Solid.js
     * 
     * @param array $atts Atributos del shortcode
     * @return string HTML del componente
     */
    public function solid_shortcode($atts)
    {
        $atts = shortcode_atts([
            'component' => '',
            'id' => 'solid-' . uniqid(),
            'class' => '',
            'title' => '',
            'subtitle' => '',
            'accent-color' => '#D4B254',
            'services' => '',
            'use-single-service' => 'false',
            'dark-mode' => 'false',
        ], $atts, 'solid');

        // Verificar componente
        if (empty($atts['component'])) {
            return '<div class="p-4 bg-red-100 text-red-800 rounded-md">Error: Componente no especificado</div>';
        }

        // Cargar el componente
        if (!$this->load_component($atts['component'])) {
            return '<div class="p-4 bg-red-100 text-red-800 rounded-md">Error: Componente "' . esc_html($atts['component']) . '" no disponible</div>';
        }

        // Extraer propiedades específicas del componente
        $props = [];
        $component_id = $atts['id'];

        // Construir el container con data-attributes
        $container_attrs = [
            'id' => $component_id,
            'class' => 'solid-component-container ' . esc_attr($atts['class']),
            'data-solid-component' => esc_attr($atts['component'])
        ];

        // Pasar atributos como data-attributes
        foreach ($atts as $key => $value) {
            if ($key !== 'component' && $key !== 'id' && $key !== 'class') {
                // Convertir guiones a camelCase para data-attributes
                $data_key = str_replace('-', '', $key);
                $container_attrs['data-' . $data_key] = esc_attr($value);
            }
        }

        // Construir HTML
        $html = '<div';
        foreach ($container_attrs as $attr => $value) {
            $html .= ' ' . $attr . '="' . $value . '"';
        }
        $html .= '>';

        // Estado de carga inicial
        $html .= '<div class="flex justify-center items-center p-4">';
        $html .= '<svg class="animate-spin h-8 w-8 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">';
        $html .= '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>';
        $html .= '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>';
        $html .= '</svg>';
        $html .= '<span class="ml-3 text-gray-600">Cargando componente...</span>';
        $html .= '</div>';

        $html .= '</div>';

        return $html;
    }
}

// Instancia global
global $wptbt_solid_loader;
$wptbt_solid_loader = new WPTBT_Solid_JS_Loader();

/**
 * Función auxiliar para cargar un componente Solid.js
 * 
 * @param string $component_name Nombre del componente a cargar
 * @return bool True si se cargó correctamente, false en caso contrario
 */
function wptbt_load_solid_component($component_name)
{
    global $wptbt_solid_loader;
    return $wptbt_solid_loader->load_component($component_name);
}

/**
 * Función auxiliar para generar HTML del componente de formulario de reserva de tours
 * 
 * @param array $props Propiedades para el componente
 * @param array $container_attrs Atributos adicionales para el contenedor
 * @return string HTML del componente
 */
function wptbt_booking_form_component($props = [], $container_attrs = [])
{
    // Cargar el componente
    wptbt_load_solid_component('booking-form');

    // ID único para el contenedor
    $container_id = isset($container_attrs['id']) ? $container_attrs['id'] : 'booking-form-' . uniqid();

    // Formato de tours (si hay) para el data-attribute
    $tours_json = isset($props['tours']) ? json_encode($props['tours']) : '';
    // Mantener compatibilidad con services para no romper código existente
    $services_json = isset($props['services']) ? json_encode($props['services']) : $tours_json;

    // Configurar atributos del contenedor
    $default_container_attrs = [
        'id' => $container_id,
        'class' => 'solid-booking-container',
        'data-services' => $services_json, // Mantener para compatibilidad
        'data-tours' => $tours_json, // Nuevo atributo específico para tours
        'data-dark-mode' => isset($props['darkMode']) && $props['darkMode'] ? 'true' : 'false',
        'data-accent-color' => isset($props['accentColor']) ? $props['accentColor'] : '#DC2626', // Color rojo para viajes
        'data-ajax-url' => admin_url('admin-ajax.php'),
        //'data-nonce' => wp_create_nonce('wptbt_booking_nonce'),
        'data-use-single-service' => isset($props['useSingleService']) && $props['useSingleService'] ? 'true' : 'false',
        'data-use-single-tour' => isset($props['useSingleTour']) && $props['useSingleTour'] ? 'true' : 'false',
        'data-email-recipient' => isset($props['emailRecipient']) ? $props['emailRecipient'] : '',
        'data-tour-id' => isset($props['tourId']) ? $props['tourId'] : '',
        'data-form-type' => 'tour-booking', // Identificador del tipo de formulario
    ];

    // Combinar atributos personalizados
    $container_attrs = array_merge($default_container_attrs, $container_attrs);

    // Construir HTML del contenedor
    $html = '<div';
    foreach ($container_attrs as $attr => $value) {
        $html .= ' ' . $attr . '="' . esc_attr($value) . '"';
    }
    $html .= '>';

    // Estado de carga inicial
    $html .= '<div class="flex justify-center items-center p-8">';
    $html .= '<svg class="animate-spin h-10 w-10 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">';
    $html .= '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>';
    $html .= '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>';
    $html .= '</svg>';
    $html .= '<span class="ml-3 text-gray-600">Cargando formulario de reserva de tours...</span>';
    $html .= '</div>';

    $html .= '</div>';

    return $html;
}

/**
 * Función auxiliar para generar HTML del componente Google Reviews
 * 
 * @param array $props Propiedades para el componente
 * @param array $container_attrs Atributos adicionales para el contenedor
 * @return string HTML del componente
 */
function wptbt_google_reviews_component($props = [], $container_attrs = [])
{
    // Cargar el componente
    wptbt_load_solid_component('google-reviews');

    // ID único para el contenedor
    $container_id = isset($container_attrs['id']) ? $container_attrs['id'] : 'google-reviews-' . uniqid();

    // Formato de reseñas y placeInfo para los data-attributes
    $reviews_json = isset($props['reviews']) ? json_encode($props['reviews']) : '';
    $place_info_json = isset($props['placeInfo']) ? json_encode($props['placeInfo']) : '';

    // Configurar atributos del contenedor
    $default_container_attrs = [
        'id' => $container_id,
        'class' => 'solid-google-reviews-container',
        'data-reviews' => $reviews_json,
        'data-place-info' => $place_info_json,
        'data-title' => isset($props['title']) ? $props['title'] : 'What Our Clients Say',
        'data-subtitle' => isset($props['subtitle']) ? $props['subtitle'] : 'Google Reviews',
        'data-description' => isset($props['description']) ? $props['description'] : '',
        'data-display-name' => isset($props['displayName']) && $props['displayName'] ? 'true' : 'false',
        'data-display-avatar' => isset($props['displayAvatar']) && $props['displayAvatar'] ? 'true' : 'false',
        'data-display-rating' => isset($props['displayRating']) && $props['displayRating'] ? 'true' : 'false',
        'data-display-date' => isset($props['displayDate']) && $props['displayDate'] ? 'true' : 'false',
        'data-autoplay' => isset($props['autoplay']) && $props['autoplay'] ? 'true' : 'false',
        'data-autoplay-speed' => isset($props['autoplaySpeed']) ? $props['autoplaySpeed'] : '5000',
        'data-background-color' => isset($props['backgroundColor']) ? $props['backgroundColor'] : '#FFFFFF',
        'data-text-color' => isset($props['textColor']) ? $props['textColor'] : '#424242',
        'data-accent-color' => isset($props['accentColor']) ? $props['accentColor'] : '#D4B254',
        'data-carousel-type' => isset($props['carouselType']) ? $props['carouselType'] : 'slide',
        'data-place-id' => isset($props['placeId']) ? $props['placeId'] : '',
        'data-api-key' => isset($props['apiKey']) ? $props['apiKey'] : '',
        'data-review-count' => isset($props['reviewCount']) ? $props['reviewCount'] : '5',
        'data-min-rating' => isset($props['minRating']) ? $props['minRating'] : '4',
        'data-dynamic' => isset($props['isDynamic']) && $props['isDynamic'] ? 'true' : 'false',
        'data-ajax-url' => admin_url('admin-ajax.php'),
        'data-nonce' => wp_create_nonce('wptbt_google_reviews_nonce'),
    ];

    // Combinar atributos personalizados
    $container_attrs = array_merge($default_container_attrs, $container_attrs);

    // Construir HTML del contenedor
    $html = '<div';
    foreach ($container_attrs as $attr => $value) {
        $html .= ' ' . $attr . '="' . esc_attr($value) . '"';
    }
    $html .= '>';

    // Estado de carga inicial
    $html .= '<div class="flex justify-center items-center p-8">';
    $html .= '<svg class="animate-spin h-10 w-10 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">';
    $html .= '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>';
    $html .= '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>';
    $html .= '</svg>';
    $html .= '<span class="ml-3 text-gray-600">Cargando reseñas...</span>';
    $html .= '</div>';

    $html .= '</div>';

    return $html;
}

/**
 * Función auxiliar para generar HTML del componente de Galería
 * 
 * @param array $props Propiedades para el componente
 * @param array $container_attrs Atributos adicionales para el contenedor
 * @return string HTML del componente
 */
function wptbt_gallery_component($props = [], $container_attrs = [])
{
    // Cargar el componente
    wptbt_load_solid_component('gallery');

    // ID único para el contenedor
    $container_id = isset($container_attrs['id']) ? $container_attrs['id'] : 'gallery-' . uniqid();

    // Formato de imágenes para el data-attribute
    $images_array = [];

    // Si hay imágenes, procesarlas
    if (isset($props['images']) && is_array($props['images'])) {
        foreach ($props['images'] as $image) {
            if (isset($image['id'])) {
                $img_id = $image['id'];
                $img_url = wp_get_attachment_image_url($img_id, isset($props['imageSize']) ? $props['imageSize'] : 'medium_large');
                $img_full_url = wp_get_attachment_image_url($img_id, 'full');
                $img_alt = get_post_meta($img_id, '_wp_attachment_image_alt', true) ?: get_the_title($img_id);
                $img_caption = wp_get_attachment_caption($img_id) ?: '';

                $images_array[] = [
                    'id' => $img_id,
                    'url' => $img_url,
                    'fullUrl' => $img_full_url,
                    'alt' => $img_alt,
                    'caption' => $img_caption
                ];
            }
        }
    }

    $images_json = json_encode($images_array);

    // Configurar atributos del contenedor
    $default_container_attrs = [
        'id' => $container_id,
        'class' => 'solid-gallery-container',
        'data-images' => $images_json,
        'data-title' => isset($props['title']) ? $props['title'] : 'Nuestra Galería',
        'data-subtitle' => isset($props['subtitle']) ? $props['subtitle'] : 'Espacios de relajación',
        'data-description' => isset($props['description']) ? $props['description'] : '',
        'data-columns' => isset($props['columns']) ? $props['columns'] : '3',
        'data-display-mode' => isset($props['displayMode']) ? $props['displayMode'] : 'grid',
        'data-hover-effect' => isset($props['hoverEffect']) ? $props['hoverEffect'] : 'zoom',
        'data-background-color' => isset($props['backgroundColor']) ? $props['backgroundColor'] : '#F9F5F2',
        'data-text-color' => isset($props['textColor']) ? $props['textColor'] : '#5D534F',
        'data-accent-color' => isset($props['accentColor']) ? $props['accentColor'] : '#D4B254',
        'data-secondary-color' => isset($props['secondaryColor']) ? $props['secondaryColor'] : '#8BAB8D',
        'data-full-width' => isset($props['fullWidth']) && $props['fullWidth'] ? 'true' : 'false',
        'data-enable-lightbox' => isset($props['enableLightbox']) && !$props['enableLightbox'] ? 'false' : 'true',
        'data-spacing' => isset($props['spacing']) ? $props['spacing'] : '16',
        'data-intersect-once' => 'true',
        'data-intersect-threshold' => '0.25',
    ];

    // Combinar atributos personalizados
    $container_attrs = array_merge($default_container_attrs, $container_attrs);

    // Construir HTML del contenedor
    $html = '<div';
    foreach ($container_attrs as $attr => $value) {
        $html .= ' ' . $attr . '="' . esc_attr($value) . '"';
    }
    $html .= '>';

    // Estado de carga inicial
    $html .= '<div class="flex justify-center items-center p-8">';
    $html .= '<svg class="animate-spin h-10 w-10 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">';
    $html .= '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>';
    $html .= '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>';
    $html .= '</svg>';
    $html .= '<span class="ml-3 text-gray-600">Cargando galería...</span>';
    $html .= '</div>';

    $html .= '</div>';

    return $html;
}

/**
 * Función auxiliar para generar HTML del componente FAQ
 * Añade esta función al final de class-wptbt-solid-js-loader.php o agrégala a functions.php
 * Esta función es crucial para el funcionamiento correcto del bloque FAQ
 *
 * @param array $props Propiedades para el componente
 * @param array $container_attrs Atributos adicionales para el contenedor
 * @return string HTML del componente
 */
function wptbt_faq_component($props = [], $container_attrs = [])
{
    // Cargar el componente
    wptbt_load_solid_component('faq');

    // ID único para el contenedor
    $container_id = isset($container_attrs['id']) ? $container_attrs['id'] : 'faq-' . uniqid();

    // Formatear FAQs para el data-attribute
    $faqs_json = isset($props['faqs']) ? json_encode($props['faqs']) : '';

    // Configurar atributos del contenedor
    $default_container_attrs = [
        'id' => $container_id,
        'class' => 'solid-faq-container',
        'data-faqs' => $faqs_json,
        'data-title' => isset($props['title']) ? $props['title'] : 'Preguntas Frecuentes',
        'data-subtitle' => isset($props['subtitle']) ? $props['subtitle'] : 'Resolvemos tus dudas',
        'data-background-color' => isset($props['backgroundColor']) ? $props['backgroundColor'] : '#F7EDE2',
        'data-text-color' => isset($props['textColor']) ? $props['textColor'] : '#424242',
        'data-accent-color' => isset($props['accentColor']) ? $props['accentColor'] : '#D4B254',
        'data-secondary-color' => isset($props['secondaryColor']) ? $props['secondaryColor'] : '#8BAB8D',
        'data-layout' => isset($props['layout']) ? $props['layout'] : 'full',
        'data-contact-text' => isset($props['contactText']) ? $props['contactText'] : '¿Tienes más preguntas?',
        'data-contact-url' => isset($props['contactUrl']) ? $props['contactUrl'] : '#contact',
        'data-show-contact-button' => isset($props['showContactButton']) && !$props['showContactButton'] ? 'false' : 'true',
        'data-open-first' => isset($props['openFirst']) && $props['openFirst'] ? 'true' : 'false',
        'data-single-open' => isset($props['singleOpen']) && $props['singleOpen'] ? 'true' : 'false',
        'data-show-top-wave' => isset($props['showTopWave']) && !$props['showTopWave'] ? 'false' : 'true',
        'data-show-bottom-wave' => isset($props['showBottomWave']) && !$props['showBottomWave'] ? 'false' : 'true',
        'data-animate-entrance' => isset($props['animateEntrance']) && !$props['animateEntrance'] ? 'false' : 'true',
        'data-intersect-once' => 'true',
        'data-intersect-threshold' => '0.25',
    ];

    // Combinar atributos personalizados
    $container_attrs = array_merge($default_container_attrs, $container_attrs);

    // Construir HTML del contenedor
    $html = '<div';
    foreach ($container_attrs as $attr => $value) {
        $html .= ' ' . $attr . '="' . esc_attr($value) . '"';
    }
    $html .= '>';

    // Estado de carga inicial
    $html .= '<div class="flex justify-center items-center p-8">';
    $html .= '<svg class="animate-spin h-10 w-10 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">';
    $html .= '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>';
    $html .= '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>';
    $html .= '</svg>';
    $html .= '<span class="ml-3 text-gray-600">Cargando preguntas...</span>';
    $html .= '</div>';

    $html .= '</div>';

    return $html;
}


// 3. Añadir la función auxiliar wptbt_interactive_map_component al final del archivo class-wptbt-solid-js-loader.php
// (Agregar después de las otras funciones auxiliares como wptbt_google_reviews_component)

/**
 * Función auxiliar para generar HTML del componente Mapa Interactivo
 * 
 * @param array $props Propiedades para el componente
 * @param array $container_attrs Atributos adicionales para el contenedor
 * @return string HTML del componente
 */
function wptbt_interactive_map_component($props = [], $container_attrs = [])
{
    // Cargar el componente
    wptbt_load_solid_component('interactive-map');

    // ID único para el contenedor
    $container_id = isset($container_attrs['id']) ? $container_attrs['id'] : 'interactive-map-' . uniqid();

    // Formato de puntos de interés para el data-attribute si existen
    $points_of_interest_json = isset($props['pointsOfInterest']) ? json_encode($props['pointsOfInterest']) : '';

    // Configurar atributos del contenedor
    $default_container_attrs = [
        'id' => $container_id,
        'class' => 'solid-interactive-map-container',
        'data-points-of-interest' => $points_of_interest_json,
        'data-title' => isset($props['title']) ? $props['title'] : 'Find Us',
        'data-subtitle' => isset($props['subtitle']) ? $props['subtitle'] : 'Our Location',
        'data-description' => isset($props['description']) ? $props['description'] : '',
        'data-latitude' => isset($props['latitude']) ? $props['latitude'] : -13.518333,
        'data-longitude' => isset($props['longitude']) ? $props['longitude'] : -71.978056,
        'data-zoom' => isset($props['zoom']) ? $props['zoom'] : 15,
        'data-marker-title' => isset($props['markerTitle']) ? $props['markerTitle'] : 'Mystical Terra Spa',
        'data-marker-description' => isset($props['markerDescription']) ? $props['markerDescription'] : 'Your wellness sanctuary',
        'data-map-height' => isset($props['mapHeight']) ? $props['mapHeight'] : '500px',
        'data-show-directions' => isset($props['showDirections']) && !$props['showDirections'] ? 'false' : 'true',
        'data-show-streetview' => isset($props['showStreetview']) && !$props['showStreetview'] ? 'false' : 'true',
        'data-background-color' => isset($props['backgroundColor']) ? $props['backgroundColor'] : '#F9F5F2',
        'data-text-color' => isset($props['textColor']) ? $props['textColor'] : '#5D534F',
        'data-accent-color' => isset($props['accentColor']) ? $props['accentColor'] : '#D4B254',
        'data-secondary-color' => isset($props['secondaryColor']) ? $props['secondaryColor'] : '#8BAB8D',
        'data-map-style' => isset($props['mapStyle']) ? $props['mapStyle'] : 'default',
        'data-api-key' => isset($props['apiKey']) ? $props['apiKey'] : '',
        'data-address' => isset($props['address']) ? $props['address'] : '',
        'data-phone' => isset($props['phone']) ? $props['phone'] : '',
        'data-email' => isset($props['email']) ? $props['email'] : '',
        'data-booking-url' => isset($props['bookingUrl']) ? $props['bookingUrl'] : '#booking',
        'data-intersect-once' => 'true',
        'data-intersect-threshold' => '0.25',
    ];

    // Combinar atributos personalizados
    $container_attrs = array_merge($default_container_attrs, $container_attrs);

    // Construir HTML del contenedor
    $html = '<div';
    foreach ($container_attrs as $attr => $value) {
        $html .= ' ' . $attr . '="' . esc_attr($value) . '"';
    }
    $html .= '>';

    // Estado de carga inicial
    $html .= '<div class="flex justify-center items-center p-8">';
    $html .= '<svg class="animate-spin h-10 w-10 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">';
    $html .= '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>';
    $html .= '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>';
    $html .= '</svg>';
    $html .= '<span class="ml-3 text-gray-600">Cargando mapa...</span>';
    $html .= '</div>';

    $html .= '</div>';

    return $html;
}
