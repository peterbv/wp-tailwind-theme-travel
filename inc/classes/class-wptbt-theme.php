<?php

/**
 * Clase principal del tema
 */
class WPTBT_Theme
{
    /**
     * Instancias de las clases
     */
    private $blocks;
    private $assets;
    private $setup;
    private $multisite;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->blocks = new WPTBT_Blocks();
        $this->assets = new WPTBT_Vite_Assets();
        $this->setup = new WPTBT_Setup();
        $this->multisite = new WPTBT_Multisite();
    }

    /**
     * Iniciar todas las funcionalidades del tema
     */
    public function run()
    {
        $this->setup->init();
        $this->assets->init();
        $this->blocks->init();
        $this->multisite->init(); // Inicializar soporte para multisite

        // Ejecutar theme_supports directamente en vez de añadirlo como hook
        $this->theme_supports();

        // Acciones y filtros adicionales
        add_action('after_setup_theme', array($this, 'theme_supports'));
    }

    /**
     * Configurar soporte del tema
     */
    public function theme_supports()
    {
        // Soporte para características básicas de WordPress
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('automatic-feed-links');
        add_theme_support('customize-selective-refresh-widgets');
        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script'
        ));

        // Soporte para editor de bloques
        add_theme_support('wp-block-styles');
        add_theme_support('align-wide');
        add_theme_support('editor-styles');
        add_theme_support('responsive-embeds');

        // Soporte para el customizer
        add_theme_support('custom-logo', array(
            'height'      => 100,
            'width'       => 400,
            'flex-height' => true,
            'flex-width'  => true,
        ));

        add_theme_support('custom-header');
        add_theme_support('custom-background');

        // Soporte para menús
        register_nav_menus(array(
            'primary' => esc_html__('Menú Principal', 'wp-tailwind-blocks'),
            'footer'  => esc_html__('Menú del Pie de Página', 'wp-tailwind-blocks'),
        ));
    }
}
