<?php

/**
 * Clase para manejar los bloques de Gutenberg
 */
class WPTBT_Blocks
{
    /**
     * Inicializar bloques
     */
    public function init()
    {
        add_action('init', array($this, 'register_block_categories'), 9);
        add_action('init', array($this, 'register_blocks'));
        add_action('enqueue_block_assets', array($this, 'block_assets'));
    }

    /**
     * Registrar categorías de bloques
     */
    public function register_block_categories()
    {
        if (function_exists('register_block_type')) {
            add_filter('block_categories_all', function ($categories) {
                return array_merge(
                    $categories,
                    array(
                        array(
                            'slug'  => 'wp-tailwind-blocks',
                            'title' => esc_html__('Bloques Personalizados', 'wp-tailwind-blocks'),
                        ),
                    )
                );
            });
        }
    }

    /**
     * Registrar bloques personalizados
     */
    public function register_blocks()
    {
        if (function_exists('register_block_type') && file_exists(WPTBT_DIR . 'build/blocks.json')) {
            // Registrar automáticamente todos los bloques definidos en blocks.json
            register_block_type(WPTBT_DIR . 'build/blocks.json');
        }
    }

    /**
     * Cargar assets para bloques en frontend y editor
     */
    public function block_assets()
    {
        /* if (is_admin()) {
            // Si estamos en el admin, cargar estilos específicos
            wp_enqueue_style(
                'wp-tailwind-blocks-block-editor',
                WPTBT_URI . 'assets/admin/css/blocks.css',
                array(),
                WPTBT_VERSION
            );
        } */
    }
}
