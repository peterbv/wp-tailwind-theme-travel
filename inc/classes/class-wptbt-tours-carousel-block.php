<?php

/**
 * Bloque de Carousel de Tours para Agencia de Viajes con Solid.js
 * Carousel infinito con animación automática
 *
 * @package WPTBT
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Class WPTBT_Tours_Carousel_Block
 */
class WPTBT_Tours_Carousel_Block
{
    private $translate = '';
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translate = 'wptbt-tours-carousel';

        // Registrar bloque
        add_action('init', [$this, 'register_block']);

        // Agregar shortcode como método alternativo
        add_shortcode('wptbt_tours_carousel', [$this, 'render_carousel_shortcode']);
    }

    /**
     * Registrar el bloque de carousel de tours
     */
    public function register_block()
    {
        // Verificar que la función existe (Gutenberg está activo)
        if (!function_exists('register_block_type')) {
            return;
        }

        // Registrar script del bloque
        wp_register_script(
            'wptbt-tours-carousel-block-editor',
            get_template_directory_uri() . '/assets/admin/js/tours-carousel-block.js',
            ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data', 'wp-i18n'],
            filemtime(get_template_directory() . '/assets/admin/js/tours-carousel-block.js')
        );

        // Configurar traducciones para el script del editor
        wp_set_script_translations('wptbt-tours-carousel-block-editor', $this->translate, get_template_directory() . '/languages');

        // Registrar estilos para el editor
        wp_register_style(
            'wptbt-tours-carousel-block-editor-style',
            get_template_directory_uri() . '/assets/admin/css/tours-carousel-block-style.css',
            ['wp-edit-blocks'],
            filemtime(get_template_directory() . '/assets/admin/css/tours-carousel-block-style.css')
        );

        // Registrar el bloque
        register_block_type('wptbt/tours-carousel-block', [
            'editor_script' => 'wptbt-tours-carousel-block-editor',
            'editor_style'  => 'wptbt-tours-carousel-block-editor-style',
            'render_callback' => [$this, 'render_carousel_block'],
            'attributes' => [
                'title' => [
                    'type' => 'string',
                    'default' => __('Nuestros Tours', $this->translate)
                ],
                'subtitle' => [
                    'type' => 'string',
                    'default' => __('Experiencias Únicas', $this->translate)
                ],
                'description' => [
                    'type' => 'string',
                    'default' => __('Descubre nuestros increíbles tours y vive aventuras inolvidables.', $this->translate)
                ],
                'tourIds' => [
                    'type' => 'array',
                    'default' => []
                ],
                'postsPerPage' => [
                    'type' => 'number',
                    'default' => 6
                ],
                'orderBy' => [
                    'type' => 'string',
                    'default' => 'date' // 'date', 'title', 'menu_order', 'rand'
                ],
                'order' => [
                    'type' => 'string',
                    'default' => 'DESC' // 'ASC', 'DESC'
                ],
                'autoplaySpeed' => [
                    'type' => 'number',
                    'default' => 3000
                ],
                'slidesToShow' => [
                    'type' => 'number',
                    'default' => 3
                ],
                'showDots' => [
                    'type' => 'boolean',
                    'default' => true
                ],
                'showArrows' => [
                    'type' => 'boolean',
                    'default' => true
                ],
                'pauseOnHover' => [
                    'type' => 'boolean',
                    'default' => true
                ],
                'infinite' => [
                    'type' => 'boolean',
                    'default' => true
                ],
                'animationDirection' => [
                    'type' => 'string',
                    'default' => 'left' // 'left', 'right'
                ],
                'backgroundColor' => [
                    'type' => 'string',
                    'default' => '#F8FAFC'
                ],
                'textColor' => [
                    'type' => 'string',
                    'default' => '#1F2937'
                ],
                'accentColor' => [
                    'type' => 'string',
                    'default' => '#DC2626'
                ],
                'secondaryColor' => [
                    'type' => 'string',
                    'default' => '#059669'
                ],
                'fullWidth' => [
                    'type' => 'boolean',
                    'default' => false
                ],
                'categories' => [
                    'type' => 'array',
                    'default' => []
                ],
                'destinations' => [
                    'type' => 'array',
                    'default' => []
                ]
            ]
        ]);
    }

    /**
     * Obtener tours para el carousel
     *
     * @param array $attributes Atributos del bloque.
     * @return array Array de tours.
     */
    private function get_tours($attributes)
    {
        $args = [
            'post_type' => 'tours',
            'post_status' => 'publish',
            'posts_per_page' => isset($attributes['postsPerPage']) ? intval($attributes['postsPerPage']) : 6,
            'orderby' => isset($attributes['orderBy']) ? $attributes['orderBy'] : 'date',
            'order' => isset($attributes['order']) ? $attributes['order'] : 'DESC',
            'meta_query' => [],
            'tax_query' => []
        ];

        // Si se especificaron IDs específicos
        if (!empty($attributes['tourIds'])) {
            $args['post__in'] = $attributes['tourIds'];
            $args['orderby'] = 'post__in';
        }

        // Filtrar por categorías
        if (!empty($attributes['categories'])) {
            $args['tax_query'][] = [
                'taxonomy' => 'tour-categories',
                'field' => 'term_id',
                'terms' => $attributes['categories']
            ];
        }

        // Filtrar por destinos
        if (!empty($attributes['destinations'])) {
            $args['tax_query'][] = [
                'taxonomy' => 'destinations',
                'field' => 'term_id', 
                'terms' => $attributes['destinations']
            ];
        }

        // Si hay múltiples tax_query, usar relación AND
        if (count($args['tax_query']) > 1) {
            $args['tax_query']['relation'] = 'AND';
        }

        $query = new WP_Query($args);
        $tours = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                
                // Obtener datos del tour
                $pricing_data = WPTBT_Tours::get_tour_pricing_data($post_id);
                $featured_image = get_the_post_thumbnail_url($post_id, 'large');
                
                // Obtener meta datos
                $subtitle = get_post_meta($post_id, '_wptbt_tour_subtitle', true);
                $duration = get_post_meta($post_id, '_tour_duration', true);
                $difficulty = get_post_meta($post_id, '_tour_difficulty', true);
                $min_age = get_post_meta($post_id, '_tour_min_age', true);
                $max_people = get_post_meta($post_id, '_tour_max_people', true);
                
                // Obtener precios
                $price_promotion = get_post_meta($post_id, '_tour_price_promotion', true);
                $price_original = get_post_meta($post_id, '_tour_price_original', true);
                $price_international = get_post_meta($post_id, '_tour_price_international', true);
                $price_national = get_post_meta($post_id, '_tour_price_national', true);
                $currency = get_post_meta($post_id, '_tour_currency', true) ?: 'USD';
                
                // Obtener términos de taxonomías
                $destinations = get_the_terms($post_id, 'destinations');
                $categories = get_the_terms($post_id, 'tour-categories');
                
                $location = '';
                if ($destinations && !is_wp_error($destinations)) {
                    $location = $destinations[0]->name;
                }

                $tours[] = [
                    'id' => $post_id,
                    'title' => get_the_title(),
                    'subtitle' => $subtitle ?: '',
                    'excerpt' => get_the_excerpt(),
                    'permalink' => get_permalink(),
                    'slug' => get_post_field('post_name', $post_id),
                    'featured_image' => $featured_image ?: '',
                    'price' => $pricing_data['adult_price'] ?? '',
                    'price_promotion' => $price_promotion ?: '',
                    'price_original' => $price_original ?: '',
                    'price_international' => $price_international ?: '',
                    'price_national' => $price_national ?: '',
                    'currency' => $currency,
                    'duration' => $duration ?: '',
                    'difficulty' => $difficulty ?: '',
                    'location' => $location,
                    'min_age' => $min_age ?: '',
                    'max_people' => $max_people ?: '',
                    'categories' => $categories && !is_wp_error($categories) ? wp_list_pluck($categories, 'name') : [],
                    'destinations' => $destinations && !is_wp_error($destinations) ? wp_list_pluck($destinations, 'name') : [],
                ];
            }
            wp_reset_postdata();
        }

        return $tours;
    }

    /**
     * Renderizar el bloque de carousel de tours
     *
     * @param array $attributes Atributos del bloque.
     * @return string HTML del bloque.
     */
    public function render_carousel_block($attributes)
    {
        // Extraer atributos
        $title = isset($attributes['title']) ? $attributes['title'] : '';
        $subtitle = isset($attributes['subtitle']) ? $attributes['subtitle'] : '';
        $description = isset($attributes['description']) ? $attributes['description'] : '';
        $autoplay_speed = isset($attributes['autoplaySpeed']) ? intval($attributes['autoplaySpeed']) : 3000;
        $slides_to_show = isset($attributes['slidesToShow']) ? intval($attributes['slidesToShow']) : 3;
        $show_dots = isset($attributes['showDots']) ? $attributes['showDots'] : true;
        $show_arrows = isset($attributes['showArrows']) ? $attributes['showArrows'] : true;
        $pause_on_hover = isset($attributes['pauseOnHover']) ? $attributes['pauseOnHover'] : true;
        $infinite = isset($attributes['infinite']) ? $attributes['infinite'] : true;
        $animation_direction = isset($attributes['animationDirection']) ? $attributes['animationDirection'] : 'left';
        $background_color = isset($attributes['backgroundColor']) ? $attributes['backgroundColor'] : '#F8FAFC';
        $text_color = isset($attributes['textColor']) ? $attributes['textColor'] : '#1F2937';
        $accent_color = isset($attributes['accentColor']) ? $attributes['accentColor'] : '#DC2626';
        $secondary_color = isset($attributes['secondaryColor']) ? $attributes['secondaryColor'] : '#059669';
        $full_width = isset($attributes['fullWidth']) ? $attributes['fullWidth'] : false;

        // Obtener tours
        $tours = $this->get_tours($attributes);

        // Si no hay tours, mostrar mensaje para editores
        if (empty($tours) && current_user_can('edit_posts')) {
            return '<div class="wptbt-tours-carousel-empty" style="padding: 2rem; text-align: center; background-color: #f8f9fa; border: 1px dashed #ccc; border-radius: 4px;">
                <p>' . __('No tours found. Please check your settings or create some tours.', $this->translate) . '</p>
            </div>';
        } elseif (empty($tours)) {
            return ''; // No mostrar nada a usuarios normales si no hay tours
        }

        // Cargar el componente Solid.js
        wptbt_load_solid_component('tours-carousel');

        // ID único para este contenedor
        $container_id = 'tours-carousel-' . uniqid();

        // Generar el HTML usando el componente Solid.js
        return wptbt_tours_carousel_component(
            [
                'title' => $title,
                'subtitle' => $subtitle,
                'description' => $description,
                'tours' => $tours,
                'autoplaySpeed' => $autoplay_speed,
                'slidesToShow' => $slides_to_show,
                'showDots' => $show_dots,
                'showArrows' => $show_arrows,
                'pauseOnHover' => $pause_on_hover,
                'infinite' => $infinite,
                'animationDirection' => $animation_direction,
                'backgroundColor' => $background_color,
                'textColor' => $text_color,
                'accentColor' => $accent_color,
                'secondaryColor' => $secondary_color,
                'fullWidth' => $full_width
            ],
            [
                'id' => $container_id,
                'class' => 'solid-tours-carousel-container reveal-item opacity-0 translate-y-8',
            ]
        );
    }

    /**
     * Renderizar shortcode de carousel de tours
     *
     * @param array $atts Atributos del shortcode.
     * @return string HTML del shortcode.
     */
    public function render_carousel_shortcode($atts)
    {
        $attributes = shortcode_atts(
            array(
                'title' => __('Nuestros Tours', $this->translate),
                'subtitle' => __('Experiencias Únicas', $this->translate),
                'description' => __('Descubre nuestros increíbles tours y vive aventuras inolvidables.', $this->translate),
                'posts_per_page' => 6,
                'order_by' => 'date',
                'order' => 'DESC',
                'autoplay_speed' => 3000,
                'slides_to_show' => 3,
                'show_dots' => true,
                'show_arrows' => true,
                'pause_on_hover' => true,
                'infinite' => true,
                'animation_direction' => 'left',
                'background_color' => '#F8FAFC',
                'text_color' => '#1F2937',
                'accent_color' => '#DC2626',
                'secondary_color' => '#059669',
                'full_width' => false,
                'categories' => '', // IDs separados por comas
                'destinations' => '' // IDs separados por comas
            ),
            $atts
        );

        // Convertir valores boolean de string a boolean
        $attributes['show_dots'] = filter_var($attributes['show_dots'], FILTER_VALIDATE_BOOLEAN);
        $attributes['show_arrows'] = filter_var($attributes['show_arrows'], FILTER_VALIDATE_BOOLEAN);
        $attributes['pause_on_hover'] = filter_var($attributes['pause_on_hover'], FILTER_VALIDATE_BOOLEAN);
        $attributes['infinite'] = filter_var($attributes['infinite'], FILTER_VALIDATE_BOOLEAN);
        $attributes['full_width'] = filter_var($attributes['full_width'], FILTER_VALIDATE_BOOLEAN);

        // Convertir categories y destinations de string a array
        if (!empty($attributes['categories'])) {
            $attributes['categories'] = array_map('trim', explode(',', $attributes['categories']));
        } else {
            $attributes['categories'] = [];
        }

        if (!empty($attributes['destinations'])) {
            $attributes['destinations'] = array_map('trim', explode(',', $attributes['destinations']));
        } else {
            $attributes['destinations'] = [];
        }

        // Convertir atributos para el formato que espera render_carousel_block
        $block_attributes = array(
            'title' => $attributes['title'],
            'subtitle' => $attributes['subtitle'],
            'description' => $attributes['description'],
            'postsPerPage' => intval($attributes['posts_per_page']),
            'orderBy' => $attributes['order_by'],
            'order' => $attributes['order'],
            'autoplaySpeed' => intval($attributes['autoplay_speed']),
            'slidesToShow' => intval($attributes['slides_to_show']),
            'showDots' => $attributes['show_dots'],
            'showArrows' => $attributes['show_arrows'],
            'pauseOnHover' => $attributes['pause_on_hover'],
            'infinite' => $attributes['infinite'],
            'animationDirection' => $attributes['animation_direction'],
            'backgroundColor' => $attributes['background_color'],
            'textColor' => $attributes['text_color'],
            'accentColor' => $attributes['accent_color'],
            'secondaryColor' => $attributes['secondary_color'],
            'fullWidth' => $attributes['full_width'],
            'categories' => $attributes['categories'],
            'destinations' => $attributes['destinations']
        );

        return $this->render_carousel_block($block_attributes);
    }
}

// Inicializar la clase
new WPTBT_Tours_Carousel_Block();