<?php

/**
 * Bloque de Mapa Interactivo
 * Versión optimizada con Solid.js modular
 * Con soporte completo para internacionalización (i18n)
 * Soporta OpenStreetMap (Leaflet) y Google Maps
 *
 * @package WPTBT
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Class WPTBT_Interactive_Map_Block
 */
class WPTBT_Interactive_Map_Block
{

    private $translate = '';
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translate = 'wptbt-interactive-map-block';
        // Registrar bloque
        add_action('init', [$this, 'register_block']);

        // Agregar shortcode como método alternativo
        add_shortcode('wptbt_interactive_map', [$this, 'render_interactive_map_shortcode']);
    }

    /**
     * Registrar el bloque de mapa interactivo
     */
    public function register_block()
    {
        // Verificar que la función existe (Gutenberg está activo)
        if (!function_exists('register_block_type')) {
            return;
        }

        // Registrar script del bloque
        wp_register_script(
            'wptbt-interactive-map-block-editor',
            get_template_directory_uri() . '/assets/admin/js/interactive-map-block.js',
            ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'],
            filemtime(get_template_directory() . '/assets/admin/js/interactive-map-block.js')
        );

        // Configurar traducciones para el script del editor
        wp_set_script_translations('wptbt-interactive-map-block-editor', $this->translate, get_template_directory() . '/languages');

        // Registrar estilos para el editor
        wp_register_style(
            'wptbt-interactive-map-block-editor-style',
            get_template_directory_uri() . '/assets/admin/css/interactive-map-block-style.css',
            ['wp-edit-blocks'],
            filemtime(get_template_directory() . '/assets/admin/css/interactive-map-block-style.css')
        );

        // Registrar el bloque
        register_block_type('wptbt/interactive-map-block', [
            'editor_script' => 'wptbt-interactive-map-block-editor',
            'editor_style'  => 'wptbt-interactive-map-block-editor-style',
            'render_callback' => [$this, 'render_interactive_map_block'],
            'attributes' => [
                'title' => [
                    'type' => 'string',
                    'default' => __('FIND US', $this->translate)
                ],
                'subtitle' => [
                    'type' => 'string',
                    'default' => __('Our Location', $this->translate)
                ],
                'description' => [
                    'type' => 'string',
                    'default' => __('Visit us and discover our relaxing spa in the heart of the city', $this->translate)
                ],
                'latitude' => [
                    'type' => 'number',
                    'default' => -13.518333 // Cusco, Perú
                ],
                'longitude' => [
                    'type' => 'number',
                    'default' => -71.978056 // Cusco, Perú
                ],
                'zoom' => [
                    'type' => 'number',
                    'default' => 15
                ],
                'markerTitle' => [
                    'type' => 'string',
                    'default' => __('Mystical Terra Spa', $this->translate)
                ],
                'markerDescription' => [
                    'type' => 'string',
                    'default' => __('Your wellness sanctuary', $this->translate)
                ],
                'mapHeight' => [
                    'type' => 'string',
                    'default' => '500px'
                ],
                'showDirections' => [
                    'type' => 'boolean',
                    'default' => true
                ],
                'showStreetview' => [
                    'type' => 'boolean',
                    'default' => true
                ],
                'backgroundColor' => [
                    'type' => 'string',
                    'default' => '#F9F5F2'
                ],
                'textColor' => [
                    'type' => 'string',
                    'default' => '#5D534F'
                ],
                'accentColor' => [
                    'type' => 'string',
                    'default' => '#D4B254'
                ],
                'secondaryColor' => [
                    'type' => 'string',
                    'default' => '#8BAB8D'
                ],
                'mapStyle' => [
                    'type' => 'string',
                    'default' => 'default' // "default", "light", "dark", etc.
                ],
                'mapProvider' => [
                    'type' => 'string',
                    'default' => 'osm' // 'osm' o 'google'
                ],
                'apiKey' => [
                    'type' => 'string',
                    'default' => ''
                ],
                'pointsOfInterest' => [
                    'type' => 'array',
                    'default' => [
                        [
                            'title' => __('Plaza de Armas', $this->translate),
                            'description' => __('Main square of Cusco', $this->translate),
                            'latitude' => -13.516599,
                            'longitude' => -71.978775,
                            'image' => ''
                        ],
                        [
                            'title' => __('Qorikancha', $this->translate),
                            'description' => __('The Inca\'s Sun Temple', $this->translate),
                            'latitude' => -13.520791,
                            'longitude' => -71.975437,
                            'image' => ''
                        ]
                    ]
                ],
                'address' => [
                    'type' => 'string',
                    'default' => 'Calle Plateros 334, Cusco 08001, Perú'
                ],
                'phone' => [
                    'type' => 'string',
                    'default' => '+51 84 123456'
                ],
                'email' => [
                    'type' => 'string',
                    'default' => 'info@mysticalterra.com'
                ],
                'bookingUrl' => [
                    'type' => 'string',
                    'default' => '#booking'
                ],
                'enableFullscreen' => [
                    'type' => 'boolean',
                    'default' => true
                ],
                'enableZoomControls' => [
                    'type' => 'boolean',
                    'default' => true
                ],
                'enableClustering' => [
                    'type' => 'boolean',
                    'default' => true
                ]
            ]
        ]);
    }

    
    /**
     * Mejoras para el método render_interactive_map_block en class-wptbt-interactive-map-block.php
     * Reemplaza este método en tu archivo original
     */
    
    public function render_interactive_map_block($attributes)
    {
        // Extraer atributos con mejor validación
        $title = isset($attributes['title']) ? $attributes['title'] : __('Find Us', $this->translate);
        $subtitle = isset($attributes['subtitle']) ? $attributes['subtitle'] : __('Our Location', $this->translate);
        $description = isset($attributes['description']) ? $attributes['description'] : '';
        $latitude = isset($attributes['latitude']) ? floatval($attributes['latitude']) : -13.518333;
        $longitude = isset($attributes['longitude']) ? floatval($attributes['longitude']) : -71.978056;
        $zoom = isset($attributes['zoom']) ? intval($attributes['zoom']) : 15;
        $marker_title = isset($attributes['markerTitle']) ? $attributes['markerTitle'] : __('Mystical Terra Spa', $this->translate);
        $marker_description = isset($attributes['markerDescription']) ? $attributes['markerDescription'] : __('Your wellness sanctuary', $this->translate);
        $map_height = isset($attributes['mapHeight']) ? $attributes['mapHeight'] : '500px';
        $show_directions = isset($attributes['showDirections']) ? $attributes['showDirections'] : true;
        $show_streetview = isset($attributes['showStreetview']) ? $attributes['showStreetview'] : true;
        $background_color = isset($attributes['backgroundColor']) ? $attributes['backgroundColor'] : '#F9F5F2';
        $text_color = isset($attributes['textColor']) ? $attributes['textColor'] : '#5D534F';
        $accent_color = isset($attributes['accentColor']) ? $attributes['accentColor'] : '#D4B254';
        $secondary_color = isset($attributes['secondaryColor']) ? $attributes['secondaryColor'] : '#8BAB8D';
        $map_style = isset($attributes['mapStyle']) ? $attributes['mapStyle'] : 'default';
        $map_provider = isset($attributes['mapProvider']) ? $attributes['mapProvider'] : 'osm';
        $api_key = isset($attributes['apiKey']) ? $attributes['apiKey'] : '';
        $enable_fullscreen = isset($attributes['enableFullscreen']) ? $attributes['enableFullscreen'] : true;
        $enable_zoom_controls = isset($attributes['enableZoomControls']) ? $attributes['enableZoomControls'] : true;
        $enable_clustering = isset($attributes['enableClustering']) ? $attributes['enableClustering'] : true;
        
        // Procesar puntos de interés
        $points_of_interest = isset($attributes['pointsOfInterest']) ? $attributes['pointsOfInterest'] : [];
        
        // Convertir puntos de interés al formato esperado por el componente (lat/lng)
        $formatted_points_of_interest = [];
        foreach ($points_of_interest as $poi) {
            if (isset($poi['latitude']) && isset($poi['longitude'])) {
                // Asegurar que los valores sean numéricos
                $lat = floatval($poi['latitude']);
                $lng = floatval($poi['longitude']);
                
                $formatted_poi = [
                    'lat' => $lat,
                    'lng' => $lng,
                    'title' => isset($poi['title']) ? $poi['title'] : '',
                    'description' => isset($poi['description']) ? $poi['description'] : '',
                    'image' => isset($poi['image']) ? $poi['image'] : '',
                    'category' => isset($poi['category']) ? $poi['category'] : '',
                    'website' => isset($poi['website']) ? $poi['website'] : '',
                ];
                $formatted_points_of_interest[] = $formatted_poi;
            }
        }
        
        // Información de contacto 
        $address = isset($attributes['address']) ? $attributes['address'] : 'Calle Plateros 334, Cusco 08001, Perú';
        $phone = isset($attributes['phone']) ? $attributes['phone'] : '+51 84 123456';
        $email = isset($attributes['email']) ? $attributes['email'] : 'info@mysticalterra.com';
        $booking_url = isset($attributes['bookingUrl']) ? $attributes['bookingUrl'] : '#booking';
    
        // ID único para este contenedor
        $container_id = 'interactive-map-' . uniqid();
    
        // Cargar el componente Solid.js
        wptbt_load_solid_component('interactive-map');
    
        // Crear la estructura principal de ubicación
        $main_location = [
            'lat' => $latitude,
            'lng' => $longitude,
            'title' => $marker_title,
            'address' => $address,
            'description' => $marker_description,
            'contactInfo' => $phone . ' | ' . $email
        ];
    
        // Asegurar que los datos estén correctamente codificados para JSON
        $points_of_interest_json = wp_json_encode($formatted_points_of_interest);
        $main_location_json = wp_json_encode($main_location);
    
        // Generar el HTML usando el componente Solid.js
        $container_attrs = [
            'id' => $container_id,
            'class' => 'solid-interactive-map-container reveal-item opacity-0 translate-y-8',
            'data-intersect-once' => 'true',
            'style' => "background-color: {$background_color}; color: {$text_color};",
            'data-title' => $title,
            'data-subtitle' => $subtitle,
            'data-description' => $description,
            'data-latitude' => $latitude,
            'data-longitude' => $longitude,
            'data-zoom' => $zoom,
            'data-marker-title' => $marker_title,
            'data-marker-description' => $marker_description,
            'data-map-height' => str_replace('px', '', $map_height), // Quitar 'px' si existe
            'data-accent-color' => $accent_color,
            'data-secondary-color' => $secondary_color,
            'data-background-color' => $background_color,
            'data-text-color' => $text_color,
            'data-show-directions-link' => $show_directions ? 'true' : 'false',
            'data-show-points-of-interest' => !empty($formatted_points_of_interest) ? 'true' : 'false',
            'data-map-style' => $map_style,
            'data-enable-fullscreen' => $enable_fullscreen ? 'true' : 'false',
            'data-enable-zoom-controls' => $enable_zoom_controls ? 'true' : 'false',
            'data-enable-clustering' => $enable_clustering ? 'true' : 'false',
            'data-map-provider' => $map_provider,
            'data-api-key' => $api_key,
            'data-show-streetview' => $show_streetview ? 'true' : 'false',
            'data-address' => $address,
            'data-phone' => $phone,
            'data-email' => $email,
            'data-booking-url' => $booking_url,
            'data-main-location' => $main_location_json,
            'data-points-of-interest' => $points_of_interest_json
        ];
    
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
        $html .= '<span class="ml-3 text-gray-600">' . esc_html__('Loading map...', $this->translate) . '</span>';
        $html .= '</div>';
    
        $html .= '</div>';
    
        // Añadir script de depuración si está en modo de desarrollo
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $html .= '<script>
            console.log("Interactive Map Block Debug Info:");
            console.log("Main Location:", ' . $main_location_json . ');
            console.log("Points of Interest:", ' . $points_of_interest_json . ');
            console.log("Map Provider:", "' . esc_js($map_provider) . '");
            </script>';
        }
    
        return $html;
    }

    /**
     * Renderizar shortcode de mapa interactivo
     *
     * @param array $atts Atributos del shortcode.
     * @return string HTML del shortcode.
     */
    public function render_interactive_map_shortcode($atts)
    {
        $attributes = shortcode_atts(
            array(
                'title' => __('FIND US', $this->translate),
                'subtitle' => __('Our Location', $this->translate),
                'description' => __('Visit us and discover our relaxing spa in the heart of the city', $this->translate),
                'latitude' => -13.518333, // Cusco
                'longitude' => -71.978056, // Cusco
                'zoom' => 15,
                'marker_title' => __('Mystical Terra Spa', $this->translate),
                'marker_description' => __('Your wellness sanctuary', $this->translate),
                'map_height' => '500px',
                'show_directions' => 'true',
                'show_streetview' => 'true',
                'background_color' => '#F9F5F2',
                'text_color' => '#5D534F',
                'accent_color' => '#D4B254',
                'secondary_color' => '#8BAB8D',
                'map_style' => 'default',
                'map_provider' => 'osm',
                'api_key' => '',
                'address' => 'Calle Plateros 334, Cusco 08001, Perú',
                'phone' => '+51 84 123456',
                'email' => 'info@mysticalterra.com',
                'booking_url' => '#booking',
                'enable_fullscreen' => 'true',
                'enable_zoom_controls' => 'true',
                'enable_clustering' => 'true',
            ),
            $atts
        );

        // Convertir atributos para el formato que espera render_interactive_map_block
        $block_attributes = array(
            'title' => $attributes['title'],
            'subtitle' => $attributes['subtitle'],
            'description' => $attributes['description'],
            'latitude' => floatval($attributes['latitude']),
            'longitude' => floatval($attributes['longitude']),
            'zoom' => intval($attributes['zoom']),
            'markerTitle' => $attributes['marker_title'],
            'markerDescription' => $attributes['marker_description'],
            'mapHeight' => $attributes['map_height'],
            'showDirections' => $attributes['show_directions'] === 'true',
            'showStreetview' => $attributes['show_streetview'] === 'true',
            'backgroundColor' => $attributes['background_color'],
            'textColor' => $attributes['text_color'],
            'accentColor' => $attributes['accent_color'],
            'secondaryColor' => $attributes['secondary_color'],
            'mapStyle' => $attributes['map_style'],
            'mapProvider' => $attributes['map_provider'],
            'apiKey' => $attributes['api_key'],
            'address' => $attributes['address'],
            'phone' => $attributes['phone'],
            'email' => $attributes['email'],
            'bookingUrl' => $attributes['booking_url'],
            'enableFullscreen' => $attributes['enable_fullscreen'] === 'true',
            'enableZoomControls' => $attributes['enable_zoom_controls'] === 'true',
            'enableClustering' => $attributes['enable_clustering'] === 'true',
            // No incluimos pointsOfInterest aquí porque es más complejo para un shortcode
            'pointsOfInterest' => []
        );

        return $this->render_interactive_map_block($block_attributes);
    }
}

// Inicializar la clase
new WPTBT_Interactive_Map_Block();