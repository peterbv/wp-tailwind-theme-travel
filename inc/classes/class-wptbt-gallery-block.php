<?php

/**
 * Bloque de Galería para Spa con Solid.js
 * Versión mejorada con sistema modular Solid.js
 *
 * @package WPTBT
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Class WPTBT_Gallery_Block
 */
class WPTBT_Gallery_Block
{
    private $translate = '';
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translate = 'wptbt-gallery-block';

        // Registrar bloque
        add_action('init', [$this, 'register_block']);

        // Agregar shortcode como método alternativo
        add_shortcode('wptbt_gallery', [$this, 'render_gallery_shortcode']);
    }

    /**
     * Registrar el bloque de galería
     */
    public function register_block()
    {
        // Verificar que la función existe (Gutenberg está activo)
        if (!function_exists('register_block_type')) {
            return;
        }

        // Registrar script del bloque
        wp_register_script(
            'wptbt-gallery-block-editor',
            get_template_directory_uri() . '/assets/admin/js/gallery-block.js',
            ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data', 'wp-i18n'],
            filemtime(get_template_directory() . '/assets/admin/js/gallery-block.js')
        );

        // Configurar traducciones para el script del editor
        wp_set_script_translations('wptbt-gallery-block-editor', $this->translate, get_template_directory() . '/languages');

        // Registrar estilos para el editor
        wp_register_style(
            'wptbt-gallery-block-editor-style',
            get_template_directory_uri() . '/assets/admin/css/gallery-block-style.css',
            ['wp-edit-blocks'],
            filemtime(get_template_directory() . '/assets/admin/css/gallery-block-style.css')
        );

        // Registrar el bloque
        register_block_type('wptbt/gallery-block', [
            'editor_script' => 'wptbt-gallery-block-editor',
            'editor_style'  => 'wptbt-gallery-block-editor-style',
            'render_callback' => [$this, 'render_gallery_block'],
            'attributes' => [
                'title' => [
                    'type' => 'string',
                    'default' => __('Our Gallery', $this->translate)
                ],
                'subtitle' => [
                    'type' => 'string',
                    'default' => __('Relaxation Spaces', $this->translate)
                ],
                'description' => [
                    'type' => 'string',
                    'default' => __('Explore our facilities and services through our image gallery.', $this->translate)
                ],
                'images' => [
                    'type' => 'array',
                    'default' => []
                ],
                'columns' => [
                    'type' => 'number',
                    'default' => 3
                ],
                'displayMode' => [
                    'type' => 'string',
                    'default' => 'grid' // 'grid', 'masonry', o 'slider'
                ],
                'hoverEffect' => [
                    'type' => 'string',
                    'default' => 'zoom' // 'zoom', 'fade', 'slide', 'none'
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
                'fullWidth' => [
                    'type' => 'boolean',
                    'default' => false
                ],
                'imageSize' => [
                    'type' => 'string',
                    'default' => 'medium_large' // Predeterminado a tamaño mediano-grande
                ],
                'enableLightbox' => [
                    'type' => 'boolean',
                    'default' => true
                ],
                'spacing' => [
                    'type' => 'number',
                    'default' => 16
                ]
            ]
        ]);
    }

    /**
     * Renderizar el bloque de galería
     *
     * @param array $attributes Atributos del bloque.
     * @return string HTML del bloque.
     */
    public function render_gallery_block($attributes)
    {
        // Extraer atributos
        $title = isset($attributes['title']) ? $attributes['title'] : __('Our Gallery', $this->translate);
        $subtitle = isset($attributes['subtitle']) ? $attributes['subtitle'] : __('Relaxation Spaces', $this->translate);
        $description = isset($attributes['description']) ? $attributes['description'] : __('Explore our facilities and services through our image gallery.', $this->translate);
        $images = isset($attributes['images']) ? $attributes['images'] : [];
        $columns = isset($attributes['columns']) ? intval($attributes['columns']) : 3;
        $display_mode = isset($attributes['displayMode']) ? $attributes['displayMode'] : 'grid';
        $hover_effect = isset($attributes['hoverEffect']) ? $attributes['hoverEffect'] : 'zoom';
        $background_color = isset($attributes['backgroundColor']) ? $attributes['backgroundColor'] : '#F9F5F2';
        $text_color = isset($attributes['textColor']) ? $attributes['textColor'] : '#5D534F';
        $accent_color = isset($attributes['accentColor']) ? $attributes['accentColor'] : '#D4B254';
        $secondary_color = isset($attributes['secondaryColor']) ? $attributes['secondaryColor'] : '#8BAB8D';
        $full_width = isset($attributes['fullWidth']) ? $attributes['fullWidth'] : false;
        $image_size = isset($attributes['imageSize']) ? $attributes['imageSize'] : 'medium_large';
        $enable_lightbox = isset($attributes['enableLightbox']) ? $attributes['enableLightbox'] : true;
        $spacing = isset($attributes['spacing']) ? intval($attributes['spacing']) : 16;

        // Si no hay imágenes, mostrar mensaje para editores
        if (empty($images) && current_user_can('edit_posts')) {
            return '<div class="wptbt-gallery-empty" style="padding: 2rem; text-align: center; background-color: #f8f9fa; border: 1px dashed #ccc; border-radius: 4px;">
                <p>' . __('Please add images to the gallery from the block editor.', $this->translate) . '</p>
            </div>';
        } elseif (empty($images)) {
            return ''; // No mostrar nada a usuarios normales si no hay imágenes
        }

        // Cargar el componente Solid.js
        wptbt_load_solid_component('gallery');

        // ID único para este contenedor
        $container_id = 'gallery-' . uniqid();

        // Generar el HTML usando el componente Solid.js
        return wptbt_gallery_component(
            [
                'title' => $title,
                'subtitle' => $subtitle,
                'description' => $description,
                'images' => $images,
                'columns' => $columns,
                'displayMode' => $display_mode,
                'hoverEffect' => $hover_effect,
                'backgroundColor' => $background_color,
                'textColor' => $text_color,
                'accentColor' => $accent_color,
                'secondaryColor' => $secondary_color,
                'fullWidth' => $full_width,
                'imageSize' => $image_size,
                'enableLightbox' => $enable_lightbox,
                'spacing' => $spacing
            ],
            [
                'id' => $container_id,
                'class' => 'solid-gallery-container reveal-item opacity-0 translate-y-8',
            ]
        );
    }

    /**
     * Renderizar shortcode de galería
     *
     * @param array $atts Atributos del shortcode.
     * @return string HTML del shortcode.
     */
    public function render_gallery_shortcode($atts)
    {
        $attributes = shortcode_atts(
            array(
                'title' => __('Our Gallery', $this->translate),
                'subtitle' => __('Relaxation Spaces', $this->translate),
                'description' => __('Explore our facilities and services through our image gallery.', $this->translate),
                'ids' => '', // IDs de imágenes separados por comas
                'columns' => 3,
                'display_mode' => 'grid',
                'hover_effect' => 'zoom',
                'background_color' => '#F9F5F2',
                'text_color' => '#5D534F',
                'accent_color' => '#D4B254',
                'secondary_color' => '#8BAB8D',
                'full_width' => false,
                'enable_lightbox' => true,
                'spacing' => 16
            ),
            $atts
        );

        // Convertir IDs de string a array
        $image_ids = array();
        if (!empty($attributes['ids'])) {
            $ids = explode(',', $attributes['ids']);
            foreach ($ids as $id) {
                $image_ids[] = array('id' => trim($id));
            }
        }

        // Convertir valores boolean de string a boolean
        $attributes['full_width'] = filter_var($attributes['full_width'], FILTER_VALIDATE_BOOLEAN);
        $attributes['enable_lightbox'] = filter_var($attributes['enable_lightbox'], FILTER_VALIDATE_BOOLEAN);

        // Convertir atributos para el formato que espera render_gallery_block
        $block_attributes = array(
            'title' => $attributes['title'],
            'subtitle' => $attributes['subtitle'],
            'description' => $attributes['description'],
            'images' => $image_ids,
            'columns' => intval($attributes['columns']),
            'displayMode' => $attributes['display_mode'],
            'hoverEffect' => $attributes['hover_effect'],
            'backgroundColor' => $attributes['background_color'],
            'textColor' => $attributes['text_color'],
            'accentColor' => $attributes['accent_color'],
            'secondaryColor' => $attributes['secondary_color'],
            'fullWidth' => $attributes['full_width'],
            'enableLightbox' => $attributes['enable_lightbox'],
            'spacing' => intval($attributes['spacing'])
        );

        return $this->render_gallery_block($block_attributes);
    }
}

// Inicializar la clase
new WPTBT_Gallery_Block();
