<?php

/**
 * Clase para manejar la configuración inicial
 */
class WPTBT_Setup
{
    /**
     * Inicializar configuraciones
     */
    public function init()
    {
        add_action('after_setup_theme', array($this, 'content_width'), 0);
        add_action('widgets_init', array($this, 'register_sidebars'));
    }

    /**
     * Configurar ancho de contenido
     */
    public function content_width()
    {
        $GLOBALS['content_width'] = apply_filters('wptbt_content_width', 1200);
    }

    /**
     * Registrar áreas de widgets
     */
    public function register_sidebars()
    {
        register_sidebar(array(
            'name'          => esc_html__('Barra lateral', 'wp-tailwind-blocks'),
            'id'            => 'sidebar-1',
            'description'   => esc_html__('Añade widgets aquí.', 'wp-tailwind-blocks'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ));
    }
}
