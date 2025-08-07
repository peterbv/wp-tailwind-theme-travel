<?php

/**
 * Bloque de Carousel de Destinos para Agencia de Viajes con Solid.js
 * Carousel infinito con animación automática
 *
 * @package WPTBT
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Class WPTBT_Destinations_Carousel_Block
 */
class WPTBT_Destinations_Carousel_Block
{
    private $translate = '';
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translate = 'wptbt-destinations-carousel';

        // Registrar bloque
        add_action('init', [$this, 'register_block']);

        // Agregar shortcode como método alternativo
        add_shortcode('wptbt_destinations_carousel', [$this, 'render_carousel_shortcode']);
    }

    /**
     * Registrar el bloque de carousel de destinos
     */
    public function register_block()
    {
        // Verificar que la función existe (Gutenberg está activo)
        if (!function_exists('register_block_type')) {
            return;
        }

        // Registrar script del bloque
        wp_register_script(
            'wptbt-destinations-carousel-block-editor',
            get_template_directory_uri() . '/assets/admin/js/destinations-carousel-block.js',
            ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data', 'wp-i18n'],
            filemtime(get_template_directory() . '/assets/admin/js/destinations-carousel-block.js')
        );

        // Configurar traducciones para el script del editor
        wp_set_script_translations('wptbt-destinations-carousel-block-editor', $this->translate, get_template_directory() . '/languages');

        // Registrar estilos para el editor
        wp_register_style(
            'wptbt-destinations-carousel-block-editor-style',
            get_template_directory_uri() . '/assets/admin/css/destinations-carousel-block-style.css',
            ['wp-edit-blocks'],
            filemtime(get_template_directory() . '/assets/admin/css/destinations-carousel-block-style.css')
        );

        // Registrar estilo frontend
        wp_register_style(
            'wptbt-destinations-carousel-style',
            get_template_directory_uri() . '/src/public/css/destinations-carousel.css',
            [],
            filemtime(get_template_directory() . '/src/public/css/destinations-carousel.css')
        );

        // Registrar el bloque
        register_block_type('wptbt/destinations-carousel', [
            'editor_script' => 'wptbt-destinations-carousel-block-editor',
            'editor_style'  => 'wptbt-destinations-carousel-block-editor-style',
            'style'         => 'wptbt-destinations-carousel-style',
            'render_callback' => [$this, 'render_block'],
            'attributes' => $this->get_block_attributes()
        ]);
    }

    /**
     * Definir atributos del bloque
     */
    private function get_block_attributes()
    {
        return [
            'title' => [
                'type' => 'string',
                'default' => __('Explore Amazing Destinations', $this->translate)
            ],
            'subtitle' => [
                'type' => 'string',
                'default' => __('Popular Destinations', $this->translate)
            ],
            'description' => [
                'type' => 'string',
                'default' => __('Discover breathtaking destinations around the world with our curated travel experiences.', $this->translate)
            ],
            'selectedDestinations' => [
                'type' => 'array',
                'default' => []
            ],
            'numberOfDestinations' => [
                'type' => 'number',
                'default' => 6
            ],
            'autoplaySpeed' => [
                'type' => 'number',
                'default' => 3000
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
            'slidesToShow' => [
                'type' => 'number',
                'default' => 3
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
            'animationDirection' => [
                'type' => 'string',
                'default' => 'left'
            ],
        ];
    }

    /**
     * Renderizar el bloque
     */
    public function render_block($attributes, $content)
    {
        // Obtener destinos
        $destinations_data = $this->get_destinations_data($attributes);
        
        if (empty($destinations_data)) {
            return '<div class="destinations-carousel-empty">' . __('No destinations found.', $this->translate) . '</div>';
        }

        // Generar ID único para el carousel
        $carousel_id = 'destinations-carousel-' . wp_generate_uuid4();
        
        // Preparar datos para el componente
        $component_data = [
            'title' => $attributes['title'] ?? '',
            'subtitle' => $attributes['subtitle'] ?? '',
            'description' => $attributes['description'] ?? '',
            'destinations' => $destinations_data,
            'autoplaySpeed' => $attributes['autoplaySpeed'] ?? 3000,
            'showDots' => $attributes['showDots'] ?? true,
            'showArrows' => $attributes['showArrows'] ?? true,
            'pauseOnHover' => $attributes['pauseOnHover'] ?? true,
            'slidesToShow' => $attributes['slidesToShow'] ?? 3,
            'backgroundColor' => $attributes['backgroundColor'] ?? '#F8FAFC',
            'textColor' => $attributes['textColor'] ?? '#1F2937',
            'accentColor' => $attributes['accentColor'] ?? '#DC2626',
            'secondaryColor' => $attributes['secondaryColor'] ?? '#059669',
            'fullWidth' => $attributes['fullWidth'] ?? false,
            'animationDirection' => $attributes['animationDirection'] ?? 'left',
            'carouselId' => $carousel_id,
        ];

        // Cargar el script del componente
        wp_enqueue_script(
            'wptbt-destinations-carousel-module', 
            get_template_directory_uri() . '/assets/public/js/components/destinations-carousel-module.js',
            ['wptbt-solid-core'], 
            filemtime(get_template_directory() . '/assets/public/js/components/destinations-carousel-module.js'),
            true
        );

        // Cargar traducciones del componente
        $this->enqueue_component_translations();

        ob_start();
        ?>
        <div 
            class="solid-destinations-carousel-container"
            data-component="destinations-carousel"
            data-title="<?php echo esc_attr($component_data['title']); ?>"
            data-subtitle="<?php echo esc_attr($component_data['subtitle']); ?>"
            data-description="<?php echo esc_attr($component_data['description']); ?>"
            data-destinations="<?php echo esc_attr(wp_json_encode($component_data['destinations'])); ?>"
            data-autoplay-speed="<?php echo esc_attr($component_data['autoplaySpeed']); ?>"
            data-show-dots="<?php echo esc_attr($component_data['showDots'] ? 'true' : 'false'); ?>"
            data-show-arrows="<?php echo esc_attr($component_data['showArrows'] ? 'true' : 'false'); ?>"
            data-pause-on-hover="<?php echo esc_attr($component_data['pauseOnHover'] ? 'true' : 'false'); ?>"
            data-slides-to-show="<?php echo esc_attr($component_data['slidesToShow']); ?>"
            data-background-color="<?php echo esc_attr($component_data['backgroundColor']); ?>"
            data-text-color="<?php echo esc_attr($component_data['textColor']); ?>"
            data-accent-color="<?php echo esc_attr($component_data['accentColor']); ?>"
            data-secondary-color="<?php echo esc_attr($component_data['secondaryColor']); ?>"
            data-full-width="<?php echo esc_attr($component_data['fullWidth'] ? 'true' : 'false'); ?>"
            data-animation-direction="<?php echo esc_attr($component_data['animationDirection']); ?>"
            data-carousel-id="<?php echo esc_attr($component_data['carouselId']); ?>"
        >
            <!-- Fallback content -->
            <div class="destinations-carousel-loading">
                <p><?php _e('Loading destinations...', $this->translate); ?></p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Obtener datos de destinos
     */
    private function get_destinations_data($attributes)
    {
        $destinations = [];
        
        // Obtener términos de la taxonomía destinations
        $terms = get_terms([
            'taxonomy' => 'destinations',
            'hide_empty' => false,
            'number' => $attributes['numberOfDestinations'] ?? 6,
            'orderby' => 'count',
            'order' => 'DESC'
        ]);

        if (is_wp_error($terms) || empty($terms)) {
            return [];
        }

        foreach ($terms as $term) {
            // Obtener imagen del destino
            $image_data = WPTBT_Tours::get_destination_image($term->term_id, 'large');
            $image_url = $image_data ? $image_data[0] : '';

            // Contar tours de este destino
            $tour_count = $term->count;

            // URL del destino
            $destination_url = get_term_link($term);

            $destinations[] = [
                'id' => $term->term_id,
                'name' => $term->name,
                'slug' => $term->slug,
                'description' => $term->description,
                'image' => $image_url,
                'tourCount' => $tour_count,
                'link' => is_wp_error($destination_url) ? '' : $destination_url,
            ];
        }

        return $destinations;
    }

    /**
     * Cargar traducciones del componente
     */
    private function enqueue_component_translations()
    {
        $translations = [
            'Explore Destination' => __('Explore Destination', $this->translate),
            'tour' => __('tour', $this->translate),
            'tours' => __('tours', $this->translate),
            'Previous destinations' => __('Previous destinations', $this->translate),
            'Next destinations' => __('Next destinations', $this->translate),
            'Go to slide' => __('Go to slide', $this->translate),
            'Destination image' => __('Destination image', $this->translate),
            'Loading destinations...' => __('Loading destinations...', $this->translate),
        ];

        wp_localize_script(
            'wptbt-destinations-carousel-module',
            'wptbtI18n_destinations_carousel',
            $translations
        );
    }

    /**
     * Render shortcode
     */
    public function render_carousel_shortcode($atts)
    {
        $attributes = shortcode_atts([
            'title' => __('Explore Amazing Destinations', $this->translate),
            'subtitle' => __('Popular Destinations', $this->translate),
            'description' => __('Discover breathtaking destinations around the world.', $this->translate),
            'number' => 6,
            'autoplay_speed' => 3000,
            'show_dots' => 'true',
            'show_arrows' => 'true',
            'slides_to_show' => 3,
            'accent_color' => '#DC2626',
            'full_width' => 'false'
        ], $atts);

        // Convertir atributos del shortcode al formato del bloque
        $block_attributes = [
            'title' => $attributes['title'],
            'subtitle' => $attributes['subtitle'],
            'description' => $attributes['description'],
            'numberOfDestinations' => intval($attributes['number']),
            'autoplaySpeed' => intval($attributes['autoplay_speed']),
            'showDots' => $attributes['show_dots'] === 'true',
            'showArrows' => $attributes['show_arrows'] === 'true',
            'slidesToShow' => intval($attributes['slides_to_show']),
            'accentColor' => $attributes['accent_color'],
            'fullWidth' => $attributes['full_width'] === 'true'
        ];

        return $this->render_block($block_attributes, '');
    }
}

// Inicializar la clase
new WPTBT_Destinations_Carousel_Block();