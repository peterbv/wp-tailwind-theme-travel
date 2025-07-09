<?php

/**
 * Bloque de Testimonios de Google Maps
 * Versión optimizada con Solid.js modular
 * Con soporte completo para internacionalización (i18n)
 *
 * @package WPTBT
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Class WPTBT_Google_Reviews_Block
 */
class WPTBT_Google_Reviews_Block
{

    private $translate = '';
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translate = 'wptbt-google-reviews-block';
        // Registrar bloque
        add_action('init', [$this, 'register_block']);

        // Agregar shortcode como método alternativo
        add_shortcode('wptbt_google_reviews', [$this, 'render_google_reviews_shortcode']);

        // Añadir AJAX handler para obtener reseñas de forma dinámica (opcional)
        add_action('wp_ajax_get_google_reviews', [$this, 'ajax_get_google_reviews']);
        add_action('wp_ajax_nopriv_get_google_reviews', [$this, 'ajax_get_google_reviews']);
    }

    /**
     * Registrar el bloque de testimonios de Google Maps
     */
    public function register_block()
    {
        // Verificar que la función existe (Gutenberg está activo)
        if (!function_exists('register_block_type')) {
            return;
        }

        // Registrar script del bloque
        wp_register_script(
            'wptbt-google-reviews-block-editor',
            get_template_directory_uri() . '/assets/admin/js/google-reviews-block.js',
            ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'],
            filemtime(get_template_directory() . '/assets/admin/js/google-reviews-block.js')
        );

        // Configurar traducciones para el script del editor
        wp_set_script_translations('wptbt-google-reviews-block-editor', $this->translate, get_template_directory() . '/languages');

        // Registrar estilos para el editor
        wp_register_style(
            'wptbt-google-reviews-block-editor-style',
            get_template_directory_uri() . '/assets/admin/css/google-reviews-block-style.css',
            ['wp-edit-blocks'],
            filemtime(get_template_directory() . '/assets/admin/css/google-reviews-block-style.css')
        );

        // Registrar el bloque
        register_block_type('wptbt/google-reviews-block', [
            'editor_script' => 'wptbt-google-reviews-block-editor',
            'editor_style'  => 'wptbt-google-reviews-block-editor-style',
            'render_callback' => [$this, 'render_google_reviews_block'],
            'attributes' => [
                'title' => [
                    'type' => 'string',
                    'default' => __('WHAT OUR CLIENTS SAY', $this->translate)
                ],
                'subtitle' => [
                    'type' => 'string',
                    'default' => __('Google Reviews', $this->translate)
                ],
                'description' => [
                    'type' => 'string',
                    'default' => __('See what our customers are saying about us', $this->translate)
                ],
                'placeId' => [
                    'type' => 'string',
                    'default' => ''
                ],
                'apiKey' => [
                    'type' => 'string',
                    'default' => ''
                ],
                'reviewCount' => [
                    'type' => 'number',
                    'default' => 5
                ],
                'minRating' => [
                    'type' => 'number',
                    'default' => 4
                ],
                'displayName' => [
                    'type' => 'boolean',
                    'default' => true
                ],
                'displayAvatar' => [
                    'type' => 'boolean',
                    'default' => true
                ],
                'displayRating' => [
                    'type' => 'boolean',
                    'default' => true
                ],
                'displayDate' => [
                    'type' => 'boolean',
                    'default' => true
                ],
                'autoplay' => [
                    'type' => 'boolean',
                    'default' => true
                ],
                'autoplaySpeed' => [
                    'type' => 'number',
                    'default' => 5000
                ],
                'backgroundColor' => [
                    'type' => 'string',
                    'default' => '#FFFFFF'
                ],
                'textColor' => [
                    'type' => 'string',
                    'default' => '#424242'
                ],
                'accentColor' => [
                    'type' => 'string',
                    'default' => '#D4B254'
                ],
                'carouselType' => [
                    'type' => 'string',
                    'default' => 'slide' // 'slide' o 'fade'
                ],
                'staticReviews' => [
                    'type' => 'array',
                    'default' => [
                        [
                            'author_name' => 'John Doe',
                            'profile_photo_url' => '',
                            'rating' => 5,
                            'relative_time_description' => '1 month ago',
                            'text' => __('Excellent service! I highly recommend this place to everyone.', $this->translate)
                        ],
                        [
                            'author_name' => 'Jane Smith',
                            'profile_photo_url' => '',
                            'rating' => 5,
                            'relative_time_description' => '2 weeks ago',
                            'text' => __('Amazing experience. The staff was very professional and friendly.', $this->translate)
                        ]
                    ]
                ],
                'useStaticData' => [
                    'type' => 'boolean',
                    'default' => true
                ]
            ]
        ]);
    }

    /**
     * Cargar scripts necesarios para el frontend
     * No es necesario enqueue_frontend_scripts ya que usamos el sistema modular
     */
    public function enqueue_frontend_scripts()
    {
        // Ya no necesitamos cargar scripts directamente aquí
        // En su lugar, usar el sistema modular
        wptbt_load_solid_component('google-reviews');
    }

    /**
     * Obtener reseñas desde la API de Google Places
     * 
     * @param string $place_id ID del lugar en Google Maps
     * @param string $api_key Clave API de Google
     * @param int $review_count Número de reseñas a obtener
     * @param int $min_rating Calificación mínima (1-5)
     * @return array Reseñas obtenidas
     */
    public function get_google_reviews($place_id, $api_key, $review_count = 5, $min_rating = 4)
    {
        // Si no hay place_id o api_key, devolver datos de ejemplo
        if (empty($place_id) || empty($api_key)) {
            return $this->get_example_reviews();
        }

        // Construir URL de la API
        $url = add_query_arg(
            [
                'placeid' => $place_id,
                'key' => $api_key,
                'fields' => 'reviews,rating,user_ratings_total,name,vicinity',
                'reviews_sort' => 'most_relevant',
                'language' => get_locale()
            ],
            'https://maps.googleapis.com/maps/api/place/details/json'
        );

        // Obtener datos de transient (caché)
        $transient_key = 'wptbt_google_reviews_' . md5($url);
        $reviews_data = get_transient($transient_key);

        // Si no hay datos en caché o estamos forzando la actualización
        if (false === $reviews_data) {
            // Realizar petición a la API
            $response = wp_remote_get($url);

            // Verificar si hay errores
            if (is_wp_error($response)) {
                return $this->get_example_reviews();
            }

            // Parsear respuesta
            $data = json_decode(wp_remote_retrieve_body($response), true);

            // Verificar si hay datos
            if (isset($data['result']['reviews'])) {
                $all_reviews = $data['result']['reviews'];

                // Filtrar por calificación mínima
                $filtered_reviews = array_filter($all_reviews, function ($review) use ($min_rating) {
                    return isset($review['rating']) && $review['rating'] >= $min_rating;
                });

                // Limitar número de reseñas
                $reviews_data = array_slice($filtered_reviews, 0, $review_count);

                // Añadir información del lugar
                $place_info = [
                    'name' => isset($data['result']['name']) ? $data['result']['name'] : '',
                    'rating' => isset($data['result']['rating']) ? $data['result']['rating'] : '',
                    'user_ratings_total' => isset($data['result']['user_ratings_total']) ? $data['result']['user_ratings_total'] : '',
                    'vicinity' => isset($data['result']['vicinity']) ? $data['result']['vicinity'] : '',
                ];

                // Guardar en caché por 24 horas
                set_transient($transient_key, [
                    'reviews' => $reviews_data,
                    'place_info' => $place_info
                ], 24 * HOUR_IN_SECONDS);

                return [
                    'reviews' => $reviews_data,
                    'place_info' => $place_info
                ];
            }

            // Si algo falla, devolver datos de ejemplo
            return $this->get_example_reviews();
        }

        return $reviews_data;
    }

    /**
     * Obtener reseñas de ejemplo (cuando no hay API disponible)
     * 
     * @return array Reseñas de ejemplo
     */
    private function get_example_reviews()
    {
        return [
            'reviews' => [
                [
                    'author_name' => 'John Doe',
                    'profile_photo_url' => 'https://via.placeholder.com/50',
                    'rating' => 5,
                    'relative_time_description' => '1 month ago',
                    'text' => __('Excellent service! I highly recommend this place to everyone. The attention to detail and customer service were outstanding. I will definitely be coming back.', $this->translate)
                ],
                [
                    'author_name' => 'Jane Smith',
                    'profile_photo_url' => 'https://via.placeholder.com/50',
                    'rating' => 5,
                    'relative_time_description' => '2 weeks ago',
                    'text' => __('Amazing experience. The staff was very professional and friendly. They made sure I was comfortable throughout my appointment. Truly a 5-star service!', $this->translate)
                ],
                [
                    'author_name' => 'Michael Brown',
                    'profile_photo_url' => 'https://via.placeholder.com/50',
                    'rating' => 4,
                    'relative_time_description' => '3 months ago',
                    'text' => __('Very good service and friendly staff. The only reason I\'m not giving 5 stars is because I had to wait a bit longer than expected. Otherwise, everything was perfect.', $this->translate)
                ],
                [
                    'author_name' => 'Sarah Johnson',
                    'profile_photo_url' => 'https://via.placeholder.com/50',
                    'rating' => 5,
                    'relative_time_description' => '1 week ago',
                    'text' => __('I\'ve been coming here for years and have never been disappointed. The quality of service is consistently excellent. Highly recommended!', $this->translate)
                ],
                [
                    'author_name' => 'David Wilson',
                    'profile_photo_url' => 'https://via.placeholder.com/50',
                    'rating' => 5,
                    'relative_time_description' => '1 day ago',
                    'text' => __('Fantastic experience from start to finish. The booking process was simple, the facilities were clean, and the staff was extremely professional. Will definitely return!', $this->translate)
                ]
            ],
            'place_info' => [
                'name' => 'Example Business',
                'rating' => 4.8,
                'user_ratings_total' => 125,
                'vicinity' => '123 Main St, Anytown, USA'
            ]
        ];
    }

    /**
     * Manejador AJAX para obtener reseñas
     */
    public function ajax_get_google_reviews()
    {
        // Verificar nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wptbt_google_reviews_nonce')) {
            wp_send_json_error(__('Invalid security token', $this->translate));
        }

        // Obtener parámetros
        $place_id = isset($_POST['place_id']) ? sanitize_text_field($_POST['place_id']) : '';
        $api_key = isset($_POST['api_key']) ? sanitize_text_field($_POST['api_key']) : '';
        $review_count = isset($_POST['review_count']) ? intval($_POST['review_count']) : 5;
        $min_rating = isset($_POST['min_rating']) ? intval($_POST['min_rating']) : 4;

        // Obtener reseñas
        $reviews = $this->get_google_reviews($place_id, $api_key, $review_count, $min_rating);

        // Enviar respuesta
        wp_send_json_success($reviews);
    }

    /**
     * Renderizar el bloque de testimonios de Google Maps
     * Versión con componente Solid.js modular
     *
     * @param array $attributes Atributos del bloque.
     * @return string HTML del bloque.
     */
    public function render_google_reviews_block($attributes)
    {
        // Extraer atributos
        $title = isset($attributes['title']) ? $attributes['title'] : __('What Our Clients Say', $this->translate);
        $subtitle = isset($attributes['subtitle']) ? $attributes['subtitle'] : __('Testimonials', $this->translate);
        $description = isset($attributes['description']) ? $attributes['description'] : '';
        $place_id = isset($attributes['placeId']) ? $attributes['placeId'] : '';
        $api_key = isset($attributes['apiKey']) ? $attributes['apiKey'] : '';
        $review_count = isset($attributes['reviewCount']) ? intval($attributes['reviewCount']) : 5;
        $min_rating = isset($attributes['minRating']) ? intval($attributes['minRating']) : 4;
        $display_name = isset($attributes['displayName']) ? $attributes['displayName'] : true;
        $display_avatar = isset($attributes['displayAvatar']) ? $attributes['displayAvatar'] : true;
        $display_rating = isset($attributes['displayRating']) ? $attributes['displayRating'] : true;
        $display_date = isset($attributes['displayDate']) ? $attributes['displayDate'] : false;
        $autoplay = isset($attributes['autoplay']) ? $attributes['autoplay'] : true;
        $autoplay_speed = isset($attributes['autoplaySpeed']) ? intval($attributes['autoplaySpeed']) : 5000;
        $background_color = isset($attributes['backgroundColor']) ? $attributes['backgroundColor'] : '#FFFFFF';
        $text_color = isset($attributes['textColor']) ? $attributes['textColor'] : '#424242';
        $accent_color = isset($attributes['accentColor']) ? $attributes['accentColor'] : '#D4B254';
        $carousel_type = isset($attributes['carouselType']) ? $attributes['carouselType'] : 'slide';
        $use_static_data = isset($attributes['useStaticData']) ? $attributes['useStaticData'] : true;
        $static_reviews = isset($attributes['staticReviews']) ? $attributes['staticReviews'] : [];

        // ID único para este contenedor
        $container_id = 'google-reviews-' . uniqid();

        // Cargar el componente Solid.js
        wptbt_load_solid_component('google-reviews');

        // Obtener reseñas (estáticas o dinámicas)
        if ($use_static_data && !empty($static_reviews)) {
            $reviews_data = [
                'reviews' => $static_reviews,
                'place_info' => [
                    'name' => 'Your Business',
                    'rating' => 5,
                    'user_ratings_total' => count($static_reviews),
                    'vicinity' => ''
                ]
            ];
        } else {
            $reviews_data = $this->get_google_reviews($place_id, $api_key, $review_count, $min_rating);
        }

        // Verificar si hay reseñas
        if (empty($reviews_data['reviews'])) {
            $reviews_data = $this->get_example_reviews();
        }

        // Generar el HTML usando el componente Solid.js
        return wptbt_google_reviews_component(
            [
                'title' => $title,
                'subtitle' => $subtitle,
                'description' => $description,
                'reviews' => $reviews_data['reviews'],
                'placeInfo' => $reviews_data['place_info'],
                'displayName' => $display_name,
                'displayAvatar' => $display_avatar,
                'displayRating' => $display_rating,
                'displayDate' => $display_date,
                'autoplay' => $autoplay,
                'autoplaySpeed' => $autoplay_speed,
                'backgroundColor' => $background_color,
                'textColor' => $text_color,
                'accentColor' => $accent_color,
                'carouselType' => $carousel_type,
                'isDynamic' => !$use_static_data,
                'placeId' => $place_id,
                'apiKey' => $api_key,
                'reviewCount' => $review_count,
                'minRating' => $min_rating,
            ],
            [
                'id' => $container_id,
                'class' => 'solid-google-reviews-container reveal-item opacity-0 translate-y-8',
                'data-dynamic' => $use_static_data ? 'false' : 'true',
                'style' => "background-color: {$background_color}; color: {$text_color};",
            ]
        );
    }

    /**
     * Renderizar shortcode de testimonios de Google
     *
     * @param array $atts Atributos del shortcode.
     * @return string HTML del shortcode.
     */
    public function render_google_reviews_shortcode($atts)
    {
        $attributes = shortcode_atts(
            array(
                'title' => __('WHAT OUR CLIENTS SAY', $this->translate),
                'subtitle' => __('Google Reviews', $this->translate),
                'description' => __('See what our customers are saying about us', $this->translate),
                'place_id' => '',
                'api_key' => '',
                'review_count' => 5,
                'min_rating' => 4,
                'display_name' => 'true',
                'display_avatar' => 'true',
                'display_rating' => 'true',
                'display_date' => 'true',
                'autoplay' => 'true',
                'autoplay_speed' => 5000,
                'background_color' => '#FFFFFF',
                'text_color' => '#424242',
                'accent_color' => '#D4B254',
                'carousel_type' => 'slide',
                'use_static_data' => 'true'
            ),
            $atts
        );

        // Convertir atributos para el formato que espera render_google_reviews_block
        $block_attributes = array(
            'title' => $attributes['title'],
            'subtitle' => $attributes['subtitle'],
            'description' => $attributes['description'],
            'placeId' => $attributes['place_id'],
            'apiKey' => $attributes['api_key'],
            'reviewCount' => intval($attributes['review_count']),
            'minRating' => intval($attributes['min_rating']),
            'displayName' => $attributes['display_name'] === 'true',
            'displayAvatar' => $attributes['display_avatar'] === 'true',
            'displayRating' => $attributes['display_rating'] === 'true',
            'displayDate' => $attributes['display_date'] === 'true',
            'autoplay' => $attributes['autoplay'] === 'true',
            'autoplaySpeed' => intval($attributes['autoplay_speed']),
            'backgroundColor' => $attributes['background_color'],
            'textColor' => $attributes['text_color'],
            'accentColor' => $attributes['accent_color'],
            'carouselType' => $attributes['carousel_type'],
            'useStaticData' => $attributes['use_static_data'] === 'true',
            'staticReviews' => $this->get_example_reviews()['reviews']
        );

        return $this->render_google_reviews_block($block_attributes);
    }
}

// Inicializar la clase
new WPTBT_Google_Reviews_Block();
