<?php

/**
 * Clase para el personalizador del tema
 * Permite configurar opciones del tema desde el panel de administración
 */
class WPTBT_Customizer
{
    /**
     * Inicializar el personalizador
     */
    public function init()
    {
        add_action('customize_register', array($this, 'customize_register'));
        add_action('customize_preview_init', array($this, 'customize_preview_js'));
    }

    /**
     * Registrar opciones del personalizador
     *
     * @param WP_Customize_Manager $wp_customize Objeto del personalizador.
     */
    public function customize_register($wp_customize)
    {
        // Registrar las secciones y controles del Customizer
        $this->register_site_options($wp_customize);
        $this->register_branding_options($wp_customize);
        $this->register_topbar_options($wp_customize);
        $this->register_header_options($wp_customize);
        $this->register_cta_button_options($wp_customize);
        $this->register_floating_buttons_options($wp_customize);
        $this->register_layout_options($wp_customize);
        $this->register_contact_options($wp_customize);
        $this->register_social_options($wp_customize);
        $this->register_color_options($wp_customize);
        $this->register_multisite_options($wp_customize);

        // Registrar opciones para archivo de servicios
        $this->register_services_archive_options($wp_customize);
    }

    /**
     * Registrar opciones generales del sitio
     *
     * @param WP_Customize_Manager $wp_customize Objeto del personalizador.
     */
    private function register_site_options($wp_customize)
    {
        // Sección para opciones del sitio
        $wp_customize->add_section('wptbt_site_options', array(
            'title'       => esc_html__('Opciones del Sitio', 'wp-tailwind-blocks'),
            'description' => esc_html__('Configura las opciones generales del sitio.', 'wp-tailwind-blocks'),
            'priority'    => 10,
        ));

        // Habilitar botón "Volver arriba"
        $wp_customize->add_setting('enable_back_to_top', array(
            'default'           => true,
            'sanitize_callback' => 'wptbt_sanitize_checkbox',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('enable_back_to_top', array(
            'label'    => esc_html__('Habilitar botón "Volver arriba"', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_site_options',
            'type'     => 'checkbox',
            'priority' => 10,
        ));
    }

    /**
     * Registrar opciones de branding y logo
     *
     * @param WP_Customize_Manager $wp_customize Objeto del personalizador.
     */
    private function register_branding_options($wp_customize)
    {
        // Sección para el logo y branding
        $wp_customize->add_section('wptbt_branding_options', array(
            'title'       => esc_html__('Logo y Branding', 'wp-tailwind-blocks'),
            'description' => esc_html__('Configura el logo y texto del sitio.', 'wp-tailwind-blocks'),
            'priority'    => 20,
        ));

        // Texto del logo (alternativa al logo personalizado)
        $wp_customize->add_setting('logo_text', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('logo_text', array(
            'label'       => esc_html__('Texto del logo', 'wp-tailwind-blocks'),
            'description' => esc_html__('Texto que se mostrará si no hay logo personalizado.', 'wp-tailwind-blocks'),
            'section'     => 'wptbt_branding_options',
            'type'        => 'text',
            'priority'    => 10,
        ));

        // Texto de eslogan
        $wp_customize->add_setting('tagline_text', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('tagline_text', array(
            'label'       => esc_html__('Texto de eslogan', 'wp-tailwind-blocks'),
            'description' => esc_html__('Texto adicional junto al logo.', 'wp-tailwind-blocks'),
            'section'     => 'wptbt_branding_options',
            'type'        => 'text',
            'priority'    => 20,
        ));
    }

    /**
     * Registrar opciones de barra superior (topbar)
     *
     * @param WP_Customize_Manager $wp_customize Objeto del personalizador.
     */
    private function register_topbar_options($wp_customize)
    {
        // Sección para la barra superior
        $wp_customize->add_section('wptbt_topbar_options', array(
            'title'       => esc_html__('Barra Superior', 'wp-tailwind-blocks'),
            'description' => esc_html__('Configura la barra superior.', 'wp-tailwind-blocks'),
            'priority'    => 30,
        ));

        // Mostrar/ocultar barra superior
        $wp_customize->add_setting('show_topbar', array(
            'default'           => true,
            'sanitize_callback' => 'wptbt_sanitize_checkbox',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('show_topbar', array(
            'label'    => esc_html__('Mostrar barra superior', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_topbar_options',
            'type'     => 'checkbox',
            'priority' => 10,
        ));

        // Texto personalizado para la barra superior
        $wp_customize->add_setting('topbar_text', array(
            'default'           => esc_html__('¡Bienvenido a nuestro sitio web!', 'wp-tailwind-blocks'),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('topbar_text', array(
            'label'    => esc_html__('Texto de la barra superior', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_topbar_options',
            'type'     => 'text',
            'priority' => 20,
        ));

        // Horario de negocio
        $wp_customize->add_setting('business_hours', array(
            'default'           => 'Everyday: From 10 AM to 10 PM',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('business_hours', array(
            'label'    => esc_html__('Horario de negocio', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_topbar_options',
            'type'     => 'text',
            'priority' => 30,
        ));

        // Estilo de la barra superior
        $wp_customize->add_setting('topbar_style', array(
            'default'           => 'default',
            'sanitize_callback' => 'wptbt_sanitize_select',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('topbar_style', array(
            'label'       => esc_html__('Estilo de la barra superior', 'wp-tailwind-blocks'),
            'section'     => 'wptbt_topbar_options',
            'type'        => 'select',
            'choices'     => array(
                'default' => esc_html__('Predeterminado', 'wp-tailwind-blocks'),
                'subtle' => esc_html__('Sutil (con gradiente)', 'wp-tailwind-blocks'),
                'elegant' => esc_html__('Elegante (con bordes decorativos)', 'wp-tailwind-blocks'),
            ),
            'priority'    => 40,
        ));
    }

    /**
     * Registrar opciones del encabezado (header)
     *
     * @param WP_Customize_Manager $wp_customize Objeto del personalizador.
     */
    private function register_header_options($wp_customize)
    {
        // Sección para Opciones del Header
        $wp_customize->add_section('wptbt_header_options', array(
            'title'       => esc_html__('Opciones del Header', 'wp-tailwind-blocks'),
            'description' => esc_html__('Personaliza la apariencia y funcionalidad del header', 'wp-tailwind-blocks'),
            'priority'    => 40,
        ));

        // Control para activar/desactivar búsqueda
        $wp_customize->add_setting('show_search', array(
            'default'           => true,
            'sanitize_callback' => 'wptbt_sanitize_checkbox',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('show_search', array(
            'label'       => esc_html__('Mostrar buscador en el header', 'wp-tailwind-blocks'),
            'section'     => 'wptbt_header_options',
            'type'        => 'checkbox',
            'priority'    => 10,
        ));

        // Control para activar/desactivar selector de idiomas
        $wp_customize->add_setting('show_language_switcher', array(
            'default'           => false,
            'sanitize_callback' => 'wptbt_sanitize_checkbox',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('show_language_switcher', array(
            'label'       => esc_html__('Mostrar selector de idiomas (requiere Polylang)', 'wp-tailwind-blocks'),
            'section'     => 'wptbt_header_options',
            'type'        => 'checkbox',
            'priority'    => 20,
        ));

        // Control para tipo de menú móvil
        $wp_customize->add_setting('mobile_menu_style', array(
            'default'           => 'dropdown',
            'sanitize_callback' => 'wptbt_sanitize_select',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('mobile_menu_style', array(
            'label'       => esc_html__('Estilo del menú móvil', 'wp-tailwind-blocks'),
            'section'     => 'wptbt_header_options',
            'type'        => 'select',
            'choices'     => array(
                'dropdown' => esc_html__('Desplegable (Default)', 'wp-tailwind-blocks'),
                'offcanvas' => esc_html__('Lateral (Off-canvas)', 'wp-tailwind-blocks'),
            ),
            'priority'    => 30,
        ));

        // Control para comportamiento del header al hacer scroll
        $wp_customize->add_setting('header_scroll_behavior', array(
            'default'           => 'auto_hide',
            'sanitize_callback' => 'wptbt_sanitize_select',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('header_scroll_behavior', array(
            'label'       => esc_html__('Comportamiento del header al hacer scroll', 'wp-tailwind-blocks'),
            'section'     => 'wptbt_header_options',
            'type'        => 'select',
            'choices'     => array(
                'auto_hide' => esc_html__('Ocultar automáticamente (Default)', 'wp-tailwind-blocks'),
                'always_visible' => esc_html__('Siempre visible', 'wp-tailwind-blocks'),
                'hide_topbar' => esc_html__('Ocultar solo la topbar', 'wp-tailwind-blocks'),
                'compact' => esc_html__('Modo compacto', 'wp-tailwind-blocks'),
            ),
            'priority'    => 40,
        ));
    }

    /**
     * Registrar opciones del botón CTA
     *
     * @param WP_Customize_Manager $wp_customize Objeto del personalizador.
     */
    private function register_cta_button_options($wp_customize)
    {
        // Sección para el botón CTA
        $wp_customize->add_section('wptbt_cta_options', array(
            'title'       => esc_html__('Botón de Acción', 'wp-tailwind-blocks'),
            'description' => esc_html__('Configura el botón de llamada a la acción en el menú.', 'wp-tailwind-blocks'),
            'priority'    => 50,
        ));

        // Mostrar/ocultar botón CTA
        $wp_customize->add_setting('show_cta_button', array(
            'default'           => true,
            'sanitize_callback' => 'wptbt_sanitize_checkbox',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('show_cta_button', array(
            'label'    => esc_html__('Mostrar botón CTA', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_cta_options',
            'type'     => 'checkbox',
            'priority' => 10,
        ));

        // Texto del botón CTA
        $wp_customize->add_setting('cta_button_text', array(
            'default'           => 'BOOK NOW',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('cta_button_text', array(
            'label'    => esc_html__('Texto del botón', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_cta_options',
            'type'     => 'text',
            'priority' => 20,
        ));

        // URL del botón CTA
        $wp_customize->add_setting('cta_button_url', array(
            'default'           => '#',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('cta_button_url', array(
            'label'    => esc_html__('URL del botón', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_cta_options',
            'type'     => 'url',
            'priority' => 30,
        ));

        // Color del botón CTA
        $wp_customize->add_setting('cta_button_color', array(
            'default'           => '#D4B254',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cta_button_color', array(
            'label'    => esc_html__('Color del botón', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_cta_options',
            'priority' => 40,
        )));

        // Forma del botón CTA
        $wp_customize->add_setting('cta_button_shape', array(
            'default'           => 'rounded',
            'sanitize_callback' => 'wptbt_sanitize_select',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('cta_button_shape', array(
            'label'       => esc_html__('Forma del botón CTA', 'wp-tailwind-blocks'),
            'section'     => 'wptbt_cta_options',
            'type'        => 'select',
            'choices'     => array(
                'square' => esc_html__('Cuadrado', 'wp-tailwind-blocks'),
                'rounded' => esc_html__('Redondeado (Default)', 'wp-tailwind-blocks'),
                'pill' => esc_html__('Pill (Muy redondeado)', 'wp-tailwind-blocks'),
            ),
            'priority'    => 50,
        ));

        // Efecto del botón CTA
        $wp_customize->add_setting('cta_button_effect', array(
            'default'           => 'wave',
            'sanitize_callback' => 'wptbt_sanitize_select',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('cta_button_effect', array(
            'label'       => esc_html__('Efecto del botón CTA', 'wp-tailwind-blocks'),
            'section'     => 'wptbt_cta_options',
            'type'        => 'select',
            'choices'     => array(
                'none' => esc_html__('Ninguno', 'wp-tailwind-blocks'),
                'wave' => esc_html__('Onda (Default)', 'wp-tailwind-blocks'),
                'shadow' => esc_html__('Sombra', 'wp-tailwind-blocks'),
                'glow' => esc_html__('Brillo', 'wp-tailwind-blocks'),
            ),
            'priority'    => 60,
        ));
    }

    /**
     * Registrar opciones para botones flotantes
     *
     * @param WP_Customize_Manager $wp_customize Objeto del personalizador.
     */
    private function register_floating_buttons_options($wp_customize)
    {
        // Sección para botones flotantes
        $wp_customize->add_section('wptbt_floating_buttons', array(
            'title'       => esc_html__('Botones Flotantes', 'wp-tailwind-blocks'),
            'description' => esc_html__('Configura los botones flotantes para contacto rápido.', 'wp-tailwind-blocks'),
            'priority'    => 60,
        ));

        // Mostrar/ocultar botones flotantes
        $wp_customize->add_setting('show_floating_buttons', array(
            'default'           => true,
            'sanitize_callback' => 'wptbt_sanitize_checkbox',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('show_floating_buttons', array(
            'label'    => esc_html__('Mostrar botones flotantes', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_floating_buttons',
            'type'     => 'checkbox',
            'priority' => 10,
        ));

        // Posición de los botones flotantes
        $wp_customize->add_setting('floating_buttons_position', array(
            'default'           => 'right',
            'sanitize_callback' => 'wptbt_sanitize_select',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('floating_buttons_position', array(
            'label'    => esc_html__('Posición de los botones', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_floating_buttons',
            'type'     => 'select',
            'choices'  => array(
                'left'  => esc_html__('Izquierda', 'wp-tailwind-blocks'),
                'right' => esc_html__('Derecha', 'wp-tailwind-blocks'),
            ),
            'priority' => 20,
        ));

        // Mostrar texto en hover
        $wp_customize->add_setting('floating_buttons_show_text', array(
            'default'           => true,
            'sanitize_callback' => 'wptbt_sanitize_checkbox',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('floating_buttons_show_text', array(
            'label'    => esc_html__('Mostrar texto al pasar el ratón', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_floating_buttons',
            'type'     => 'checkbox',
            'priority' => 30,
        ));

        // Separador de estilo
        $wp_customize->add_setting('floating_buttons_separator_1', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control(new WPTBT_Separator_Control($wp_customize, 'floating_buttons_separator_1', array(
            'section'  => 'wptbt_floating_buttons',
            'priority' => 40,
        )));

        // BOTÓN DE TELÉFONO
        // Activar botón de teléfono
        $wp_customize->add_setting('floating_phone_enabled', array(
            'default'           => true,
            'sanitize_callback' => 'wptbt_sanitize_checkbox',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('floating_phone_enabled', array(
            'label'    => esc_html__('Activar botón de teléfono', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_floating_buttons',
            'type'     => 'checkbox',
            'priority' => 50,
        ));

        // Número de teléfono
        $wp_customize->add_setting('floating_phone_number', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('floating_phone_number', array(
            'label'    => esc_html__('Número de teléfono', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_floating_buttons',
            'type'     => 'text',
            'priority' => 60,
        ));

        // Texto del botón de teléfono
        $wp_customize->add_setting('floating_phone_text', array(
            'default'           => esc_html__('Llámanos', 'wp-tailwind-blocks'),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('floating_phone_text', array(
            'label'    => esc_html__('Texto del botón de teléfono', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_floating_buttons',
            'type'     => 'text',
            'priority' => 70,
        ));

        // Separador de estilo
        $wp_customize->add_setting('floating_buttons_separator_2', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control(new WPTBT_Separator_Control($wp_customize, 'floating_buttons_separator_2', array(
            'section'  => 'wptbt_floating_buttons',
            'priority' => 80,
        )));

        // BOTÓN DE WHATSAPP
        // Activar botón de WhatsApp
        $wp_customize->add_setting('floating_whatsapp_enabled', array(
            'default'           => true,
            'sanitize_callback' => 'wptbt_sanitize_checkbox',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('floating_whatsapp_enabled', array(
            'label'    => esc_html__('Activar botón de WhatsApp', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_floating_buttons',
            'type'     => 'checkbox',
            'priority' => 90,
        ));

        // Número de WhatsApp
        $wp_customize->add_setting('floating_whatsapp_number', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('floating_whatsapp_number', array(
            'label'       => esc_html__('Número de WhatsApp', 'wp-tailwind-blocks'),
            'description' => esc_html__('Incluir código de país (ej: +34612345678)', 'wp-tailwind-blocks'),
            'section'     => 'wptbt_floating_buttons',
            'type'        => 'text',
            'priority'    => 100,
        ));

        // Texto del botón de WhatsApp
        $wp_customize->add_setting('floating_whatsapp_text', array(
            'default'           => esc_html__('WhatsApp', 'wp-tailwind-blocks'),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('floating_whatsapp_text', array(
            'label'    => esc_html__('Texto del botón de WhatsApp', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_floating_buttons',
            'type'     => 'text',
            'priority' => 110,
        ));

        // Separador de estilo
        $wp_customize->add_setting('floating_buttons_separator_3', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control(new WPTBT_Separator_Control($wp_customize, 'floating_buttons_separator_3', array(
            'section'  => 'wptbt_floating_buttons',
            'priority' => 120,
        )));

        // BOTÓN DE MAPS
        // Activar botón de Maps
        $wp_customize->add_setting('floating_maps_enabled', array(
            'default'           => true,
            'sanitize_callback' => 'wptbt_sanitize_checkbox',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('floating_maps_enabled', array(
            'label'    => esc_html__('Activar botón de ubicación', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_floating_buttons',
            'type'     => 'checkbox',
            'priority' => 130,
        ));

        // URL de Google Maps
        $wp_customize->add_setting('floating_maps_url', array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('floating_maps_url', array(
            'label'       => esc_html__('URL de Google Maps', 'wp-tailwind-blocks'),
            'description' => esc_html__('URL a tu ubicación en Google Maps', 'wp-tailwind-blocks'),
            'section'     => 'wptbt_floating_buttons',
            'type'        => 'url',
            'priority'    => 140,
        ));

        // Texto del botón de Maps
        $wp_customize->add_setting('floating_maps_text', array(
            'default'           => esc_html__('Ubicación', 'wp-tailwind-blocks'),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('floating_maps_text', array(
            'label'    => esc_html__('Texto del botón de ubicación', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_floating_buttons',
            'type'     => 'text',
            'priority' => 150,
        ));
    }

    /**
     * Registrar opciones de diseño y layout
     *
     * @param WP_Customize_Manager $wp_customize Objeto del personalizador.
     */
    private function register_layout_options($wp_customize)
    {
        // Sección para diseño de layout
        $wp_customize->add_section('wptbt_layout_options', array(
            'title'       => esc_html__('Opciones de Diseño', 'wp-tailwind-blocks'),
            'description' => esc_html__('Configura las opciones de diseño y layout del tema.', 'wp-tailwind-blocks'),
            'priority'    => 70,
        ));

        // Layout para archivo/blog
        $wp_customize->add_setting('archive_layout', array(
            'default'           => 'grid',
            'sanitize_callback' => 'wptbt_sanitize_select',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('archive_layout', array(
            'label'    => esc_html__('Layout de archivo/blog', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_layout_options',
            'type'     => 'select',
            'choices'  => array(
                'grid'    => esc_html__('Cuadrícula', 'wp-tailwind-blocks'),
                'list'    => esc_html__('Lista', 'wp-tailwind-blocks'),
                'masonry' => esc_html__('Masonry', 'wp-tailwind-blocks'),
            ),
            'priority' => 10,
        ));

        // Ancho del contenido
        $wp_customize->add_setting('content_width', array(
            'default'           => 'container',
            'sanitize_callback' => 'wptbt_sanitize_select',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('content_width', array(
            'label'    => esc_html__('Ancho del contenido', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_layout_options',
            'type'     => 'select',
            'choices'  => array(
                'container'    => esc_html__('Contenedor (1200px)', 'wp-tailwind-blocks'),
                'container-sm' => esc_html__('Pequeño (640px)', 'wp-tailwind-blocks'),
                'container-md' => esc_html__('Mediano (768px)', 'wp-tailwind-blocks'),
                'container-lg' => esc_html__('Grande (1024px)', 'wp-tailwind-blocks'),
                'container-xl' => esc_html__('Extra grande (1280px)', 'wp-tailwind-blocks'),
                'full-width'   => esc_html__('Ancho completo', 'wp-tailwind-blocks'),
            ),
            'priority' => 20,
        ));

        // Mostrar barra lateral
        $wp_customize->add_setting('show_sidebar', array(
            'default'           => false,
            'sanitize_callback' => 'wptbt_sanitize_checkbox',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('show_sidebar', array(
            'label'    => esc_html__('Mostrar barra lateral', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_layout_options',
            'type'     => 'checkbox',
            'priority' => 30,
        ));

        // Posición de la barra lateral
        $wp_customize->add_setting('sidebar_position', array(
            'default'           => 'right',
            'sanitize_callback' => 'wptbt_sanitize_select',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('sidebar_position', array(
            'label'    => esc_html__('Posición de la barra lateral', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_layout_options',
            'type'     => 'select',
            'choices'  => array(
                'left'  => esc_html__('Izquierda', 'wp-tailwind-blocks'),
                'right' => esc_html__('Derecha', 'wp-tailwind-blocks'),
            ),
            'priority' => 40,
            'active_callback' => function () {
                return get_theme_mod('show_sidebar', false);
            },
        ));
    }

    /**
     * Registrar opciones de contacto
     *
     * @param WP_Customize_Manager $wp_customize Objeto del personalizador.
     */
    private function register_contact_options($wp_customize)
    {
        // Sección para opciones de contacto
        $wp_customize->add_section('wptbt_contact_options', array(
            'title'       => esc_html__('Información de Contacto', 'wp-tailwind-blocks'),
            'description' => esc_html__('Configura la información de contacto que se mostrará en el sitio.', 'wp-tailwind-blocks'),
            'priority'    => 80,
        ));

        // Email de contacto
        $wp_customize->add_setting('contact_email', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_email',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('contact_email', array(
            'label'    => esc_html__('Email de contacto', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_contact_options',
            'type'     => 'email',
            'priority' => 10,
        ));

        // Teléfono de contacto
        $wp_customize->add_setting('contact_phone', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('contact_phone', array(
            'label'    => esc_html__('Teléfono de contacto', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_contact_options',
            'type'     => 'text',
            'priority' => 20,
        ));

        // Dirección de contacto
        $wp_customize->add_setting('contact_address', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('contact_address', array(
            'label'    => esc_html__('Dirección', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_contact_options',
            'type'     => 'textarea',
            'priority' => 30,
        ));
    }

    /**
     * Registrar opciones de redes sociales
     *
     * @param WP_Customize_Manager $wp_customize Objeto del personalizador.
     */
    private function register_social_options($wp_customize)
    {
        // Sección para redes sociales
        $wp_customize->add_section('wptbt_social_options', array(
            'title'       => esc_html__('Redes Sociales', 'wp-tailwind-blocks'),
            'description' => esc_html__('Configura los enlaces a tus redes sociales.', 'wp-tailwind-blocks'),
            'priority'    => 90,
        ));

        // Array con todas las redes sociales para facilitar el mantenimiento
        $social_networks = array(
            'facebook'  => esc_html__('URL de Facebook', 'wp-tailwind-blocks'),
            'twitter'   => esc_html__('URL de Twitter/X', 'wp-tailwind-blocks'),
            'instagram' => esc_html__('URL de Instagram', 'wp-tailwind-blocks'),
            'linkedin'  => esc_html__('URL de LinkedIn', 'wp-tailwind-blocks'),
            'youtube'   => esc_html__('URL de YouTube', 'wp-tailwind-blocks'),
            'pinterest' => esc_html__('URL de Pinterest', 'wp-tailwind-blocks'),
            'vimeo'     => esc_html__('URL de Vimeo', 'wp-tailwind-blocks'),
        );

        $priority = 10;
        foreach ($social_networks as $network => $label) {
            $wp_customize->add_setting('social_' . $network, array(
                'default'           => '',
                'sanitize_callback' => 'esc_url_raw',
                'transport'         => 'refresh',
            ));

            $wp_customize->add_control('social_' . $network, array(
                'label'    => $label,
                'section'  => 'wptbt_social_options',
                'type'     => 'url',
                'priority' => $priority,
            ));

            $priority += 10;
        }
    }

    /**
     * Registrar opciones de colores
     *
     * @param WP_Customize_Manager $wp_customize Objeto del personalizador.
     */
    private function register_color_options($wp_customize)
    {
        // Sección para opciones de colores de Tailwind
        $wp_customize->add_section('wptbt_colors_options', array(
            'title'       => esc_html__('Colores del Tema', 'wp-tailwind-blocks'),
            'description' => esc_html__('Personaliza los colores principales del tema.', 'wp-tailwind-blocks'),
            'priority'    => 100,
        ));

        // Color primario
        $wp_customize->add_setting('primary_color', array(
            'default'           => '#0d6efd',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'primary_color', array(
            'label'    => esc_html__('Color Primario', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_colors_options',
            'priority' => 10,
        )));

        // Color secundario
        $wp_customize->add_setting('secondary_color', array(
            'default'           => '#6c757d',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'secondary_color', array(
            'label'    => esc_html__('Color Secundario', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_colors_options',
            'priority' => 20,
        )));

        // Color de acento
        $wp_customize->add_setting('accent_color', array(
            'default'           => '#ffc107',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'accent_color', array(
            'label'    => esc_html__('Color de Acento', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_colors_options',
            'priority' => 30,
        )));
    }

    /**
     * Registrar opciones para multisite
     *
     * @param WP_Customize_Manager $wp_customize Objeto del personalizador.
     */
    private function register_multisite_options($wp_customize)
    {
        if (!is_multisite()) {
            return;
        }

        // Opciones específicas para multisite
        $wp_customize->add_section('wptbt_multisite_options', array(
            'title'       => esc_html__('Opciones de Multisite', 'wp-tailwind-blocks'),
            'description' => esc_html__('Configura opciones específicas para entornos multisite.', 'wp-tailwind-blocks'),
            'priority'    => 110,
        ));

        // Mostrar banner de red
        $wp_customize->add_setting('show_network_banner', array(
            'default'           => true,
            'sanitize_callback' => 'wptbt_sanitize_checkbox',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('show_network_banner', array(
            'label'    => esc_html__('Mostrar banner de red', 'wp-tailwind-blocks'),
            'description' => esc_html__('Muestra un banner indicando que el sitio es parte de una red.', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_multisite_options',
            'type'     => 'checkbox',
            'priority' => 10,
        ));

        // Texto personalizado del banner de red
        $wp_customize->add_setting('network_banner_text', array(
            'default'           => esc_html__('Este sitio es parte de nuestra red.', 'wp-tailwind-blocks'),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('network_banner_text', array(
            'label'    => esc_html__('Texto del banner de red', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_multisite_options',
            'type'     => 'text',
            'priority' => 20,
            'active_callback' => function () {
                return get_theme_mod('show_network_banner', true);
            },
        ));

        // Mostrar enlace para volver al sitio principal en sitios multisitio
        $wp_customize->add_setting('show_switch_link', array(
            'default'           => true,
            'sanitize_callback' => 'wptbt_sanitize_checkbox',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('show_switch_link', array(
            'label'           => esc_html__('Mostrar enlace al sitio principal', 'wp-tailwind-blocks'),
            'description'     => esc_html__('Muestra un enlace para volver al sitio principal en el pie de página.', 'wp-tailwind-blocks'),
            'section'         => 'wptbt_multisite_options',
            'type'            => 'checkbox',
            'priority'        => 30,
            'active_callback' => function () {
                return is_multisite() && !is_main_site();
            },
        ));

        // Mostrar listado de otros sitios
        $wp_customize->add_setting('show_sites_list', array(
            'default'           => true,
            'sanitize_callback' => 'wptbt_sanitize_checkbox',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('show_sites_list', array(
            'label'    => esc_html__('Mostrar listado de sitios', 'wp-tailwind-blocks'),
            'description' => esc_html__('Muestra un listado de otros sitios en la red en el pie de página.', 'wp-tailwind-blocks'),
            'section'  => 'wptbt_multisite_options',
            'type'     => 'checkbox',
            'priority' => 40,
            'active_callback' => function () {
                return is_main_site();
            },
        ));
    }

    /**
     * Registrar opciones para la página de archivo de servicios
     *
     * @param WP_Customize_Manager $wp_customize Objeto del personalizador.
     */
    private function register_services_archive_options($wp_customize)
    {
        // Sección para la página de archivo de servicios
        $wp_customize->add_section('wptbt_services_archive', array(
            'title'       => esc_html__('Archivo de Servicios', 'wptbt-services'),
            'description' => esc_html__('Configura la página de listado de servicios', 'wptbt-services'),
            'priority'    => 120,
        ));

        // Título de la página
        $wp_customize->add_setting('services_archive_title', array(
            'default'           => esc_html__('Our Services', 'wptbt-services'),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('services_archive_title', array(
            'label'    => esc_html__('Título de la página', 'wptbt-services'),
            'section'  => 'wptbt_services_archive',
            'type'     => 'text',
            'priority' => 10,
        ));

        // Subtítulo
        $wp_customize->add_setting('services_archive_subtitle', array(
            'default'           => esc_html__('Luxury treatments for your wellbeing', 'wptbt-services'),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('services_archive_subtitle', array(
            'label'    => esc_html__('Subtítulo', 'wptbt-services'),
            'section'  => 'wptbt_services_archive',
            'type'     => 'text',
            'priority' => 20,
        ));

        // Descripción
        $wp_customize->add_setting('services_archive_description', array(
            'default'           => esc_html__('Discover our complete range of services designed to provide you with an incomparable relaxation and wellness experience.', 'wptbt-services'),
            'sanitize_callback' => 'sanitize_textarea_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('services_archive_description', array(
            'label'    => esc_html__('Descripción', 'wptbt-services'),
            'section'  => 'wptbt_services_archive',
            'type'     => 'textarea',
            'priority' => 30,
        ));

        // Separador
        $wp_customize->add_setting('services_archive_separator_1', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control(new WPTBT_Separator_Control($wp_customize, 'services_archive_separator_1', array(
            'section'  => 'wptbt_services_archive',
            'priority' => 40,
        )));

        // Número de servicios por página
        $wp_customize->add_setting('services_per_page', array(
            'default'           => 9,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('services_per_page', array(
            'label'       => esc_html__('Servicios por página', 'wptbt-services'),
            'description' => esc_html__('Número de servicios a mostrar por página', 'wptbt-services'),
            'section'     => 'wptbt_services_archive',
            'type'        => 'number',
            'input_attrs' => array(
                'min'  => 3,
                'max'  => 24,
                'step' => 3,
            ),
            'priority'    => 50,
        ));

        // Mostrar filtros de categoría
        $wp_customize->add_setting('show_service_filters', array(
            'default'           => true,
            'sanitize_callback' => 'wptbt_sanitize_checkbox',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('show_service_filters', array(
            'label'    => esc_html__('Mostrar filtros de categoría', 'wptbt-services'),
            'section'  => 'wptbt_services_archive',
            'type'     => 'checkbox',
            'priority' => 60,
        ));

        // Separador
        $wp_customize->add_setting('services_archive_separator_2', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control(new WPTBT_Separator_Control($wp_customize, 'services_archive_separator_2', array(
            'section'  => 'wptbt_services_archive',
            'priority' => 70,
        )));

        // Sección de CTA

        // Mostrar banner CTA
        $wp_customize->add_setting('show_services_cta', array(
            'default'           => true,
            'sanitize_callback' => 'wptbt_sanitize_checkbox',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('show_services_cta', array(
            'label'    => esc_html__('Mostrar banner CTA', 'wptbt-services'),
            'section'  => 'wptbt_services_archive',
            'type'     => 'checkbox',
            'priority' => 80,
        ));

        // CTA Banner - Título
        $wp_customize->add_setting('services_archive_cta_title', array(
            'default'           => esc_html__('Ready to renew your wellbeing?', 'wptbt-services'),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('services_archive_cta_title', array(
            'label'    => esc_html__('Título del CTA', 'wptbt-services'),
            'section'  => 'wptbt_services_archive',
            'type'     => 'text',
            'priority' => 90,
            'active_callback' => function () {
                return get_theme_mod('show_services_cta', true);
            },
        ));

        // CTA Banner - Texto
        $wp_customize->add_setting('services_archive_cta_text', array(
            'default'           => esc_html__('Book now and enjoy our exclusive services designed for your relaxation and wellbeing.', 'wptbt-services'),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('services_archive_cta_text', array(
            'label'    => esc_html__('Texto del CTA', 'wptbt-services'),
            'section'  => 'wptbt_services_archive',
            'type'     => 'text',
            'priority' => 100,
            'active_callback' => function () {
                return get_theme_mod('show_services_cta', true);
            },
        ));

        // CTA Banner - Texto del botón
        $wp_customize->add_setting('services_archive_cta_button_text', array(
            'default'           => esc_html__('Book Now', 'wptbt-services'),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('services_archive_cta_button_text', array(
            'label'    => esc_html__('Texto del botón CTA', 'wptbt-services'),
            'section'  => 'wptbt_services_archive',
            'type'     => 'text',
            'priority' => 110,
            'active_callback' => function () {
                return get_theme_mod('show_services_cta', true);
            },
        ));

        // ========= NUEVAS OPCIONES PARA IMAGEN DE FONDO Y FORMULARIO DE RESERVA =========

        // Imagen de fondo del CTA
        $wp_customize->add_setting('services_archive_cta_bg_image', array(
            'default'           => '',
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'services_archive_cta_bg_image', array(
            'label'     => esc_html__('Imagen de fondo del CTA', 'wptbt-services'),
            'section'   => 'wptbt_services_archive',
            'mime_type' => 'image',
            'priority'  => 120,
            'active_callback' => function () {
                return get_theme_mod('show_services_cta', true);
            },
        )));

        // Separador para formulario de reserva
        $wp_customize->add_setting('services_archive_separator_3', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control(new WPTBT_Separator_Control($wp_customize, 'services_archive_separator_3', array(
            'section'  => 'wptbt_services_archive',
            'priority' => 130,
        )));

        // Título para la sección de formulario de reserva
        $wp_customize->add_setting('services_archive_booking_section_title', array(
            'default'           => esc_html__('Formulario de Reserva', 'wptbt-services'),
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control(new WPTBT_Separator_Control($wp_customize, 'services_archive_booking_section_title', array(
            'label'       => esc_html__('FORMULARIO DE RESERVA', 'wptbt-services'),
            'section'     => 'wptbt_services_archive',
            'priority'    => 140,
            'description' => esc_html__('Configura el formulario de reserva en la página de servicios', 'wptbt-services'),
        )));

        // Mostrar formulario de reserva
        $wp_customize->add_setting('show_services_booking_form', array(
            'default'           => false,
            'sanitize_callback' => 'wptbt_sanitize_checkbox',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('show_services_booking_form', array(
            'label'    => esc_html__('Mostrar formulario de reserva', 'wptbt-services'),
            'section'  => 'wptbt_services_archive',
            'type'     => 'checkbox',
            'priority' => 150,
        ));

        // Título del formulario
        $wp_customize->add_setting('services_booking_form_title', array(
            'default'           => esc_html__('Book Your Appointment', 'wptbt-services'),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('services_booking_form_title', array(
            'label'    => esc_html__('Título del formulario', 'wptbt-services'),
            'section'  => 'wptbt_services_archive',
            'type'     => 'text',
            'priority' => 160,
            'active_callback' => function () {
                return get_theme_mod('show_services_booking_form', false);
            },
        ));

        // Subtítulo del formulario
        $wp_customize->add_setting('services_booking_form_subtitle', array(
            'default'           => esc_html__('Appointment', 'wptbt-services'),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('services_booking_form_subtitle', array(
            'label'    => esc_html__('Subtítulo del formulario', 'wptbt-services'),
            'section'  => 'wptbt_services_archive',
            'type'     => 'text',
            'priority' => 170,
            'active_callback' => function () {
                return get_theme_mod('show_services_booking_form', false);
            },
        ));

        // Descripción del formulario
        $wp_customize->add_setting('services_booking_form_description', array(
            'default'           => esc_html__('Book your treatment now and enjoy a moment of relaxation.', 'wptbt-services'),
            'sanitize_callback' => 'sanitize_textarea_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('services_booking_form_description', array(
            'label'    => esc_html__('Descripción del formulario', 'wptbt-services'),
            'section'  => 'wptbt_services_archive',
            'type'     => 'textarea',
            'priority' => 180,
            'active_callback' => function () {
                return get_theme_mod('show_services_booking_form', false);
            },
        ));

        // Imagen de fondo del formulario
        $wp_customize->add_setting('services_booking_form_bg_image', array(
            'default'           => '',
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'services_booking_form_bg_image', array(
            'label'     => esc_html__('Imagen de fondo del formulario', 'wptbt-services'),
            'section'   => 'wptbt_services_archive',
            'mime_type' => 'image',
            'priority'  => 190,
            'active_callback' => function () {
                return get_theme_mod('show_services_booking_form', false);
            },
        )));

        // Texto del botón del formulario
        $wp_customize->add_setting('services_booking_form_button_text', array(
            'default'           => esc_html__('BOOK NOW', 'wptbt-services'),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('services_booking_form_button_text', array(
            'label'    => esc_html__('Texto del botón del formulario', 'wptbt-services'),
            'section'  => 'wptbt_services_archive',
            'type'     => 'text',
            'priority' => 200,
            'active_callback' => function () {
                return get_theme_mod('show_services_booking_form', false);
            },
        ));

        // Color de acento del formulario
        $wp_customize->add_setting('services_booking_form_accent_color', array(
            'default'           => '#D4B254',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'services_booking_form_accent_color', array(
            'label'    => esc_html__('Color de acento del formulario', 'wptbt-services'),
            'section'  => 'wptbt_services_archive',
            'priority' => 210,
            'active_callback' => function () {
                return get_theme_mod('show_services_booking_form', false);
            },
        )));

        // Email de destino para las reservas
        $wp_customize->add_setting('services_booking_form_email', array(
            'default'           => get_option('admin_email'),
            'sanitize_callback' => 'sanitize_email',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control('services_booking_form_email', array(
            'label'       => esc_html__('Email para recibir reservas', 'wptbt-services'),
            'description' => esc_html__('Las solicitudes de reserva se enviarán a este correo', 'wptbt-services'),
            'section'     => 'wptbt_services_archive',
            'type'        => 'email',
            'priority'    => 220,
            'active_callback' => function () {
                return get_theme_mod('show_services_booking_form', false);
            },
        ));
        // Separador para las opciones de ondas decorativas
        $wp_customize->add_setting('services_booking_form_waves_separator', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control(new WPTBT_Separator_Control($wp_customize, 'services_booking_form_waves_separator', array(
            'label'       => esc_html__('ONDAS DECORATIVAS', 'wptbt-services'),
            'section'     => 'wptbt_services_archive',
            'priority'    => 230,
            'description' => esc_html__('Configura las ondas decorativas del formulario de reserva', 'wptbt-services'),
            'active_callback' => function () {
                return get_theme_mod('show_services_booking_form', false);
            },
        )));

        // Mostrar onda superior
        $wp_customize->add_setting('services_booking_form_show_top_wave', array(
            'default'           => true,
            'sanitize_callback' => 'wptbt_sanitize_checkbox',
            'transport'         => 'refresh',
        ));
        $wp_customize->add_control('services_booking_form_show_top_wave', array(
            'label'    => esc_html__('Mostrar onda superior', 'wptbt-services'),
            'section'  => 'wptbt_services_archive',
            'type'     => 'checkbox',
            'priority' => 240,
            'active_callback' => function () {
                return get_theme_mod('show_services_booking_form', false);
            },
        ));
        // Mostrar onda inferior
        $wp_customize->add_setting('services_booking_form_show_bottom_wave', array(
            'default'           => true,
            'sanitize_callback' => 'wptbt_sanitize_checkbox',
            'transport'         => 'refresh',
        ));
        $wp_customize->add_control('services_booking_form_show_bottom_wave', array(
            'label'    => esc_html__('Mostrar onda inferior', 'wptbt-services'),
            'section'  => 'wptbt_services_archive',
            'type'     => 'checkbox',
            'priority' => 250,
            'active_callback' => function () {
                return get_theme_mod('show_services_booking_form', false);
            },
        ));
    }

    /**
     * Añadir JavaScript para la vista previa del personalizador
     */
    public function customize_preview_js()
    {
        wp_enqueue_script(
            'wptbt-customizer',
            WPTBT_URI . 'assets/admin/js/customizer.js',
            array('customize-preview', 'jquery'),
            WPTBT_VERSION,
            true
        );
        // Opcional: Pasar datos del sitio al script
        wp_localize_script(
            'wptbt-customizer',
            'wpData',
            array(
                'siteName' => get_bloginfo('name'),
            )
        );
    }
}

/**
 * Función para sanear checkbox
 *
 * @param bool $input Valor a sanear.
 * @return bool Valor saneado.
 */
function wptbt_sanitize_checkbox($input)
{
    return (isset($input) && true == $input) ? true : false;
}

/**
 * Función para sanear selects
 *
 * @param string $input Valor a sanear.
 * @param WP_Customize_Setting $setting Objeto de configuración.
 * @return string Valor saneado.
 */
function wptbt_sanitize_select($input, $setting)
{
    // Obtener la lista de opciones desde la instancia del control
    $choices = $setting->manager->get_control($setting->id)->choices;

    // Devolver el valor de entrada si existe en las opciones; de lo contrario, devolver el valor predeterminado
    return (array_key_exists($input, $choices) ? $input : $setting->default);
}
