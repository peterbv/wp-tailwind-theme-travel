<?php

/**
 * Functions.php optimizado para el tema WP Tailwind Spa
 * 
 * Incluye optimizaciones de rendimiento y mejor organización de código
 */

// Prevenir acceso directo al archivo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes del tema
define('WPTBT_VERSION', '1.0.1'); // Actualizado
define('WPTBT_DIR', trailingslashit(get_template_directory()));
define('WPTBT_URI', trailingslashit(get_template_directory_uri()));

/**
 * Clase principal para la inicialización del tema
 */
class WPTBT_Theme_Init
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Cargar archivos principales
        $this->load_core_files();

        // Inicializar el tema
        add_action('after_setup_theme', [$this, 'init_theme']);

        // Inicializar widgets
        add_action('widgets_init', [$this, 'register_widget_areas']);

        // Cargar assets
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);

        // Color dinámico para botones CTA
        add_action('wp_head', [$this, 'custom_button_color'], 999);

        // Inicializar el banner personalizado
        add_action('after_setup_theme', [$this, 'init_banner']);
    }

    /**
     * Cargar archivos de clases principales
     */
    private function load_core_files()
    {
        $core_files = [
            'inc/classes/class-wptbt-theme.php',
            'inc/classes/class-wptbt-blocks.php',
            'inc/classes/class-wptbt-solid-js-loader.php',
            'inc/classes/class-wptbt-vite-assets.php',
            'inc/classes/class-wptbt-setup.php',
            'inc/classes/class-wptbt-multisite.php',
            'inc/classes/class-wptbt-customizer.php',
            'inc/classes/class-wptbt-dynamic-css.php',
            'inc/classes/class-wptbt-tours.php',
            'inc/classes/class-wptbt-services-block.php',
            'inc/classes/class-wptbt-benefits-block.php',
            'inc/classes/class-wptbt-booking-block.php',
            'inc/classes/class-wptbt-faq-block.php',
            'inc/classes/class-wptbt-google-reviews-block.php',
            'inc/classes/class-wptbt-gallery-block.php',
            'inc/classes/class-wptbt-interactive-map-block.php',
            'inc/classes/class-wptbt-walker-nav-menu.php',
            'inc/classes/class-wptbt-banner-metabox.php',
            'inc/template-functions.php',
            'inc/template-tags.php',
            '/inc/tour-booking.php',
            'inc/wptbt-get-language-switcher.php'
        ];

        foreach ($core_files as $file) {
            $path = WPTBT_DIR . $file;
            if (file_exists($path)) {
                require_once $path;
            }
        }

        // Cargar clases del customizer
        add_action('customize_register', function () {
            require_once WPTBT_DIR . 'inc/classes/class-wptbt-separator-control.php';
        }, 0);
    }

    /**
     * Inicializar el tema
     */
    public function init_theme()
    {
        // Registrar e inicializar la clase que maneja los assets
        $assets = new WPTBT_Vite_Assets();
        $assets->init();

        // Crear instancia del tema y ejecutar
        $theme = new WPTBT_Theme();
        $theme->run();

        // Inicializar el personalizador
        $customizer = new WPTBT_Customizer();
        $customizer->init();

        // Inicializar CSS dinámico
        $dynamic_css = new WPTBT_Dynamic_CSS();
        $dynamic_css->init();

        // Soporte para opciones de ACF
        $this->setup_acf_options();
    }

    /**
     * Inicializar el metabox del banner personalizable
     */
    public function init_banner()
    {
        $banner_metabox = new WPTBT_Banner_Metabox();
        $banner_metabox->init();
    }

    /**
     * Configurar opciones de ACF si está disponible
     */
    private function setup_acf_options()
    {
        if (!function_exists('acf_add_options_page')) {
            return;
        }

        acf_add_options_page([
            'page_title' => esc_html__('Opciones del Tema', 'wp-tailwind-blocks'),
            'menu_title' => esc_html__('Opciones del Tema', 'wp-tailwind-blocks'),
            'menu_slug'  => 'theme-general-settings',
            'capability' => 'edit_posts',
            'redirect'   => false
        ]);

        if (is_multisite()) {
            acf_add_options_page([
                'page_title'  => esc_html__('Opciones de Multisite', 'wp-tailwind-blocks'),
                'menu_title'  => esc_html__('Opciones de Multisite', 'wp-tailwind-blocks'),
                'menu_slug'   => 'multisite-settings',
                'capability'  => 'manage_network',
                'parent_slug' => 'theme-general-settings',
                'redirect'    => false
            ]);
        }
    }

    /**
     * Registrar widget areas
     */
    public function register_widget_areas()
    {
        // Widget areas
        $widget_areas = [
            [
                'name'          => esc_html__('Barra lateral', 'wp-tailwind-blocks'),
                'id'            => 'sidebar-1',
                'description'   => esc_html__('Añade widgets aquí.', 'wp-tailwind-blocks'),
                'before_widget' => '<section id="%1$s" class="widget %2$s bg-white rounded-lg shadow p-6 mb-6">',
                'after_widget'  => '</section>',
                'before_title'  => '<h2 class="widget-title text-lg font-bold mb-4">',
                'after_title'   => '</h2>',
            ],
            [
                'name'          => esc_html__('Pie de página 1', 'wp-tailwind-blocks'),
                'id'            => 'footer-1',
                'description'   => esc_html__('Añade widgets aquí.', 'wp-tailwind-blocks'),
                'before_widget' => '<section id="%1$s" class="widget %2$s mb-6">',
                'after_widget'  => '</section>',
                'before_title'  => '<h2 class="widget-title text-lg font-bold mb-4 text-white">',
                'after_title'   => '</h2>',
            ],
            [
                'name'          => esc_html__('Pie de página 2', 'wp-tailwind-blocks'),
                'id'            => 'footer-2',
                'description'   => esc_html__('Añade widgets aquí.', 'wp-tailwind-blocks'),
                'before_widget' => '<section id="%1$s" class="widget %2$s mb-6">',
                'after_widget'  => '</section>',
                'before_title'  => '<h2 class="widget-title text-lg font-bold mb-4 text-white">',
                'after_title'   => '</h2>',
            ],
            [
                'name'          => esc_html__('Pie de página 3', 'wp-tailwind-blocks'),
                'id'            => 'footer-3',
                'description'   => esc_html__('Añade widgets aquí.', 'wp-tailwind-blocks'),
                'before_widget' => '<section id="%1$s" class="widget %2$s mb-6">',
                'after_widget'  => '</section>',
                'before_title'  => '<h2 class="widget-title text-lg font-bold mb-4 text-white">',
                'after_title'   => '</h2>',
            ],
        ];

        foreach ($widget_areas as $widget_area) {
            register_sidebar($widget_area);
        }
    }

    /**
     * Cargar JavaScript y CSS del tema
     */
    public function enqueue_assets()
    {
        // Scripts principales
        $scripts = [
            [
                'handle' => 'wp-tailwind-theme-navigation',
                'src'    => WPTBT_URI . 'assets/public/js/navigation.js',
                'deps'   => [],
                'footer' => true,
            ],
            [
                'handle' => 'wptbt-floating-buttons',
                'src'    => WPTBT_URI . 'assets/public/js/floating-buttons.js',
                'deps'   => [],
                'footer' => true,
            ],
            [
                'handle' => 'wptbt-intersection-observer',
                'src'    => WPTBT_URI . 'assets/public/js/main.js',
                'deps'   => [],
                'footer' => true,
            ],
        ];

        // Cargar scripts condicionalmente
        $this->load_conditional_scripts();

        // Registrar y encolar scripts principales
        foreach ($scripts as $script) {
            wp_enqueue_script(
                $script['handle'],
                $script['src'],
                $script['deps'] ?? [],
                WPTBT_VERSION,
                $script['footer'] ?? false
            );
        }
    }

    /**
     * Cargar scripts condicionalmente según la página
     */
    private function load_conditional_scripts()
    {
        global $post;

        // Banner script - solo si está activo en la página actual
        if (is_singular() && $post) {
            $show_banner = get_post_meta($post->ID, 'wptbt_show_banner', true);

            if ($show_banner) {
                $banner_images = get_post_meta($post->ID, 'wptbt_banner_images', true);
                $image_ids = !empty($banner_images) ? explode(',', $banner_images) : [];

                // Solo cargar el carrusel si hay múltiples imágenes
                if (count($image_ids) > 1) {
                    wp_enqueue_script(
                        'wptbt-banner-carousel',
                        WPTBT_URI . 'assets/public/js/banner-carousel.js',
                        [],
                        WPTBT_VERSION,
                        true
                    );
                }

                // Cargar estilos del banner
                wp_enqueue_style(
                    'wptbt-banner-styles',
                    WPTBT_URI . 'assets/public/css/banner.css',
                    [],
                    WPTBT_VERSION
                );
            }
        }
    }

    /**
     * Aplicar dinámicamente el color del botón CTA desde el Customizer
     */
    public function custom_button_color()
    {
        $button_color = get_theme_mod('cta_button_color', '#D4B254');
        $button_color_dark = $this->adjust_brightness($button_color, -20);

?>
        <style type="text/css">
            /* Variables CSS */
            :root {
                --color-spa-accent: <?php echo esc_attr($button_color); ?>;
                --color-spa-accent-dark: <?php echo esc_attr($button_color_dark); ?>;
            }

            /* Aplicar colores directamente */
            .bg-spa-accent,
            a.bg-spa-accent,
            .inline-block.bg-spa-accent {
                background-color: var(--color-spa-accent) !important;
            }

            .hover\:bg-spa-accent-dark:hover {
                background-color: var(--color-spa-accent-dark) !important;
            }

            .text-spa-accent,
            .hover\:text-spa-accent:hover,
            .main-navigation .menu-item>a:hover,
            .main-navigation .menu-item>a:focus,
            .main-navigation .menu-item.current-menu-item>a,
            .main-navigation .menu-item.current-menu-ancestor>a {
                color: var(--color-spa-accent) !important;
            }

            .main-navigation .menu-item>a::after {
                background-color: var(--color-spa-accent) !important;
            }
        </style>
    <?php
    }

    /**
     * Ajustar el brillo de un color hexadecimal
     *
     * @param string $hex Color hexadecimal
     * @param int $steps Pasos para ajustar (-255 a 255)
     * @return string Color hexadecimal ajustado
     */
    private function adjust_brightness($hex, $steps)
    {
        // Eliminar # si está presente
        $hex = ltrim($hex, '#');

        // Convertir a RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Ajustar brillo
        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));

        // Convertir de nuevo a hexadecimal
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
}

/**
 * Función auxiliar para incluir el banner en las plantillas
 */
function wptbt_display_banner()
{
    get_template_part('template-parts/banner');
}

/**
 * Obtener datos del banner para el actual post/página
 * Útil para obtener datos del banner desde cualquier plantilla
 * 
 * @return array|bool Datos del banner o false si no está habilitado
 */
function wptbt_get_banner_data()
{
    global $post;

    if (!$post) {
        return false;
    }

    $show_banner = get_post_meta($post->ID, 'wptbt_show_banner', true);

    if (!$show_banner) {
        return false;
    }

    return [
        'title'       => get_post_meta($post->ID, 'wptbt_banner_title', true),
        'subtitle'    => get_post_meta($post->ID, 'wptbt_banner_subtitle', true),
        'button_text' => get_post_meta($post->ID, 'wptbt_banner_button_text', true),
        'button_url'  => get_post_meta($post->ID, 'wptbt_banner_button_url', true),
        'images'      => get_post_meta($post->ID, 'wptbt_banner_images', true),
        'slides'      => get_post_meta($post->ID, 'wptbt_banner_slides', true),
        'mode'        => get_post_meta($post->ID, 'wptbt_banner_mode', true) ?: 'global',
    ];
}









/**
 * Funciones para integrar el formulario de reserva en la página de servicio individual
 * Agrega estas funciones a tu archivo functions.php
 */

if (!function_exists('wptbt_get_service_booking_form')) {
    /**
     * Obtiene el HTML del formulario de reserva para un servicio específico
     * 
     * @param int|null $service_id ID del servicio (opcional, usa el ID del post actual si no se proporciona)
     * @return string HTML del formulario de reserva
     */
    function wptbt_get_service_booking_form($service_id = null)
    {
        // Si no se proporciona ID, usar el post actual
        if (null === $service_id) {
            global $post;
            if (!$post || 'servicio' !== get_post_type($post)) {
                return '';
            }
            $service_id = $post->ID;
        }

        // Obtener datos del servicio
        $service_title = get_the_title($service_id);

        // Obtener metadatos del servicio
        $precio = get_post_meta($service_id, '_wptbt_service_price', true);
        $precio_duracion1 = get_post_meta($service_id, '_wptbt_service_duration1', true);
        $precio_valor1 = get_post_meta($service_id, '_wptbt_service_price1', true);
        $precio_duracion2 = get_post_meta($service_id, '_wptbt_service_duration2', true);
        $precio_valor2 = get_post_meta($service_id, '_wptbt_service_price2', true);
        $horarios = get_post_meta($service_id, '_wptbt_service_hours', true);



        // Generar y devolver el formulario
        return wptbt_render_service_booking_form(
            $service_title,
            $precio_duracion1,
            $precio_valor1 ?: $precio,
            $precio_duracion2,
            $precio_valor2,
            $horarios
        );
    }
}

if (!function_exists('wptbt_display_service_booking_form')) {
    /**
     * Muestra el formulario de reserva para un servicio específico
     * 
     * @param int|null $service_id ID del servicio (opcional, usa el ID del post actual si no se proporciona)
     */
    function wptbt_display_service_booking_form($service_id = null)
    {
        echo wptbt_get_service_booking_form($service_id);
    }
}

/**
 * Registrar scripts y estilos necesarios para el formulario de reserva de servicios
 */
function wptbt_register_service_booking_assets()
{
    // Registrar script del formulario de reserva si aún no está registrado
    if (!wp_script_is('wptbt-booking-form', 'registered')) {
        wp_register_script(
            'wptbt-booking-form',
            get_template_directory_uri() . '/assets/public/js/booking-form.js',
            ['jquery'],
            WPTBT_VERSION,
            true
        );

        // Localizar script con la URL para AJAX
        wp_localize_script('wptbt-booking-form', 'wptbtBooking', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wptbt_booking_nonce')
        ]);
    }
}
add_action('wp_enqueue_scripts', 'wptbt_register_service_booking_assets');


/**
 * Carga múltiples dominios de texto para el tema
 * Esta función permite cargar varios dominios de texto para diferentes componentes del tema
 */
function wptbt_load_theme_textdomains()
{
    // Define los dominios que quieres cargar - añade aquí todos los que necesites
    $domains = array(
        'wp-tailwind-blocks',       // Dominio principal del tema
        'wptbt-services',           // Para el CPT de servicios
        'wptbt-gallery-block',             // Para el bloque de galería
        'wptbt-google-reviews-block',
        'wptbt-booking-form-block',
        'wptbt-faq-block',
        'wptbt-benefits-block',
        'wptbt-interactive-map-block'
        // Añade más dominios según necesites
    );

    $locale = get_locale();
    $results = array();

    foreach ($domains as $domain) {
        // Intentar primero la ubicación del tema
        $theme_mofile = get_template_directory() . '/languages/' . $domain . '-' . $locale . '.mo';
        $loaded = load_textdomain($domain, $theme_mofile);

        // Si no se pudo cargar, intentar la ubicación de red de WP
        if (!$loaded) {
            $wp_mofile = WP_LANG_DIR . '/themes/' . $domain . '-' . $locale . '.mo';
            $loaded = load_textdomain($domain, $wp_mofile);
        }

        // Registro de depuración
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("Dominio: $domain - Locale: $locale");
            error_log("Tema MO: $theme_mofile - Existe: " . (file_exists($theme_mofile) ? 'SÍ' : 'NO'));
            if (isset($wp_mofile)) {
                error_log("WP MO: $wp_mofile - Existe: " . (file_exists($wp_mofile) ? 'SÍ' : 'NO'));
            }
            error_log("Carga exitosa de '$domain': " . ($loaded ? 'SÍ' : 'NO'));
        }

        $results[$domain] = $loaded;
    }

    return $results;
}

/**
 * Función alternativa que utiliza load_theme_textdomain
 * Útil cuando el tema sigue la estructura estándar de WordPress
 */
function wptbt_load_theme_textdomains_standard()
{
    // Cargar el dominio principal del tema
    $main_loaded = load_theme_textdomain('wp-tailwind-blocks', get_template_directory() . '/languages');

    // Cargar dominios adicionales (opcional)
    $services_loaded = load_textdomain('wptbt-services', get_template_directory() . '/languages/wptbt-services-' . get_locale() . '.mo');
    $gallery_loaded = load_textdomain('wptbt-gallery', get_template_directory() . '/languages/wptbt-gallery-' . get_locale() . '.mo');

    // Registro de depuración
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log("Carga de dominios - Principal: " . ($main_loaded ? 'SÍ' : 'NO'));
        error_log("Carga de dominios - Servicios: " . ($services_loaded ? 'SÍ' : 'NO'));
        error_log("Carga de dominios - Galería: " . ($gallery_loaded ? 'SÍ' : 'NO'));
    }

    return $main_loaded;
}

// Elegir cuál de las funciones quieres utilizar
add_action('after_setup_theme', 'wptbt_load_theme_textdomains', 1);
// O alternativamente:
// add_action('after_setup_theme', 'wptbt_load_theme_textdomains_standard', 1);



// Añade a functions.php
function wptbt_component_translation_debugger()
{
    if (!is_admin() && current_user_can('manage_options')) {
    ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    // Buscar todos los objetos de traducción
                    var translationObjects = {};

                    Object.keys(window).forEach(function(key) {
                        if (key.startsWith('wptbtI18n_') || key === 'wptbtI18n') {
                            translationObjects[key] = Object.keys(window[key]).length;
                        }
                    });

                    // Mostrar en consola
                    console.log('Objetos de traducción:', translationObjects);

                    // Crear indicador visual
                    if (Object.keys(translationObjects).length > 0) {
                        var debugDiv = document.createElement('div');
                        debugDiv.style.position = 'fixed';
                        debugDiv.style.bottom = '10px';
                        debugDiv.style.right = '10px';
                        debugDiv.style.backgroundColor = 'rgba(41, 128, 185, 0.9)';
                        debugDiv.style.color = 'white';
                        debugDiv.style.padding = '10px';
                        debugDiv.style.zIndex = '9999';
                        debugDiv.style.borderRadius = '4px';
                        debugDiv.style.fontSize = '12px';
                        debugDiv.style.fontFamily = 'monospace';

                        var content = '<strong>Component Translations:</strong><br>';

                        Object.keys(translationObjects).forEach(function(key) {
                            content += `${key}: ${translationObjects[key]} strings<br>`;
                        });

                        debugDiv.innerHTML = content + '<small>(Click to close)</small>';

                        debugDiv.addEventListener('click', function() {
                            document.body.removeChild(debugDiv);
                        });

                        document.body.appendChild(debugDiv);
                    }
                }, 1000);
            });
        </script>
<?php
    }
}
add_action('wp_footer', 'wptbt_component_translation_debugger', 999);


/**
 * Pasar datos del customizer al JavaScript
 */
function wptbt_localize_header_script()
{
    // Asegúrate de que el script de navegación esté registrado
    if (wp_script_is('wp-tailwind-theme-navigation', 'registered')) {
        // Recopilar datos de opciones
        $header_data = array(
            'headerScrollBehavior' => get_theme_mod('header_scroll_behavior', 'auto_hide'),
            'mobileMenuStyle' => get_theme_mod('mobile_menu_style', 'dropdown'),
            'ctaButtonShape' => get_theme_mod('cta_button_shape', 'rounded'),
            'ctaButtonEffect' => get_theme_mod('cta_button_effect', 'wave'),
            'showSearch' => get_theme_mod('show_search', true),
            'showLanguageSwitcher' => get_theme_mod('show_language_switcher', false),
            'topbarStyle' => get_theme_mod('topbar_style', 'default'),
        );

        // Localizar el script
        wp_localize_script(
            'wp-tailwind-theme-navigation',
            'wpData',
            $header_data
        );
    }
}
add_action('wp_enqueue_scripts', 'wptbt_localize_header_script', 20);


// Desactivar wpautop solo para el tipo de post "servicio"
function disable_wpautop_for_services()
{
    remove_filter('the_content', 'wpautop');
}
add_action('wp', 'disable_wpautop_for_services');



/**
 * Función auxiliar para obtener la URL de la imagen de fondo del CTA
 * Se puede usar en archive-servicio.php
 */
function wptbt_get_cta_background_image()
{
    $bg_image_id = get_theme_mod('services_archive_cta_bg_image', '');
    if (!empty($bg_image_id)) {
        return wp_get_attachment_image_url($bg_image_id, 'full');
    }
    return ''; // Devuelve una cadena vacía si no hay imagen
}

/**
 * Modificar la consulta principal para la página de archivo de servicios
 */
function wptbt_modify_services_query($query)
{
    if (!is_admin() && $query->is_main_query() && is_post_type_archive('servicio')) {
        $services_per_page = get_theme_mod('services_per_page', 9);
        $query->set('posts_per_page', $services_per_page);
    }
}
add_action('pre_get_posts', 'wptbt_modify_services_query');

/**
 * Cargar los estilos personalizados para el banner CTA con imagen de fondo
 */
function wptbt_services_cta_styles()
{
    if (is_post_type_archive('servicio')) {
        $bg_image_url = wptbt_get_cta_background_image();
        if (!empty($bg_image_url)) {
            echo '<style type="text/css">
                .cta-banner {
                    background-image: url(' . esc_url($bg_image_url) . ');
                    background-size: cover;
                    background-position: center;
                }
                .cta-banner::before {
                    content: "";
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: linear-gradient(to bottom, rgba(0,0,0,0.6), rgba(0,0,0,0.8));
                    z-index: 0;
                }
                .cta-banner .container {
                    position: relative;
                    z-index: 1;
                }
            </style>';
        }
    }
}
add_action('wp_head', 'wptbt_services_cta_styles', 100);



/**
 * Funciones adicionales para mejorar el blog
 */

/**
 * Función para calcular y mostrar el tiempo de lectura estimado
 */
if (!function_exists('wptbt_reading_time')) {
    function wptbt_reading_time()
    {
        $content = get_post_field('post_content', get_the_ID());
        $word_count = str_word_count(strip_tags($content));
        $reading_time = ceil($word_count / 200); // 200 palabras por minuto

        if ($reading_time < 1) {
            $reading_time = 1; // Mínimo 1 minuto
        }

        echo '<span class="reading-time">';
        printf(esc_html(_n('%d min de lectura', '%d mins de lectura', $reading_time, 'wp-tailwind-theme')), $reading_time);
        echo '</span>';
    }
}

/**
 * Función para generar una tabla de contenidos para posts largos
 */
if (!function_exists('wptbt_generate_toc')) {
    function wptbt_generate_toc()
    {
        $content = get_post_field('post_content', get_the_ID());

        // Buscar todos los encabezados h2 y h3
        preg_match_all('/<h([2|3]).*?>(.*?)<\/h[2|3]>/i', $content, $matches, PREG_SET_ORDER);

        if (empty($matches)) {
            return; // No hay encabezados para generar TOC
        }

        echo '<ul class="toc-list space-y-2 text-gray-700">';

        foreach ($matches as $match) {
            $level = $match[1]; // 2 para h2, 3 para h3
            $title = strip_tags($match[2]);
            $anchor = sanitize_title($title);

            // Agregar un id al encabezado original en el contenido
            $content = preg_replace(
                '/<h' . $level . '(.*?)>' . preg_quote($match[2], '/') . '<\/h' . $level . '>/i',
                '<h' . $level . '$1 id="' . $anchor . '">' . $match[2] . '</h' . $level . '>',
                $content,
                1
            );

            // Añadir ítem a la tabla de contenidos con el nivel correcto
            if ($level == 2) {
                echo '<li class="toc-item"><a href="#' . esc_attr($anchor) . '" class="flex items-center hover:text-spa-accent transition-colors duration-300">';
                echo '<svg class="w-4 h-4 mr-1 text-spa-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">';
                echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>';
                echo esc_html($title) . '</a></li>';
            } else {
                echo '<li class="toc-item pl-6"><a href="#' . esc_attr($anchor) . '" class="flex items-center hover:text-spa-accent transition-colors duration-300">';
                echo '<svg class="w-3 h-3 mr-1 text-spa-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">';
                echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>';
                echo esc_html($title) . '</a></li>';
            }
        }

        echo '</ul>';

        // Actualizar el contenido con los IDs de anclaje
        wp_update_post(array(
            'ID' => get_the_ID(),
            'post_content' => $content
        ));
    }
}

/**
 * Función para obtener posts relacionados por categoría
 */
if (!function_exists('wptbt_get_related_posts')) {
    function wptbt_get_related_posts($post_id, $num_posts = 3)
    {
        $categories = get_the_category($post_id);

        if (empty($categories)) {
            return array();
        }

        $category_ids = wp_list_pluck($categories, 'term_id');

        $args = array(
            'post_type' => 'post',
            'posts_per_page' => $num_posts,
            'post__not_in' => array($post_id),
            'category__in' => $category_ids,
            'orderby' => 'rand'
        );

        return new WP_Query($args);
    }
}

/**
 * Registrar sidebar adicional para la página de archivo
 */
function wptbt_register_archive_sidebar()
{
    register_sidebar(array(
        'name'          => esc_html__('Sidebar de Archivo', 'wp-tailwind-theme'),
        'id'            => 'archive-sidebar',
        'description'   => esc_html__('Añade widgets aquí para mostrarlos en las páginas de archivo.', 'wp-tailwind-theme'),
        'before_widget' => '<section id="%1$s" class="widget %2$s mb-8 bg-white rounded-lg shadow-sm p-6 border border-gray-100">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title text-xl fancy-text font-bold mb-4 text-spa-primary">',
        'after_title'   => '</h2>',
    ));

    register_sidebar(array(
        'name'          => esc_html__('Filtros de Archivo', 'wp-tailwind-theme'),
        'id'            => 'archive-filters',
        'description'   => esc_html__('Añade widgets de filtrado aquí para mostrarlos en las páginas de archivo.', 'wp-tailwind-theme'),
        'before_widget' => '<div id="%1$s" class="widget %2$s mb-4">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title text-lg font-medium mb-3 text-spa-primary">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'wptbt_register_archive_sidebar');

/**
 * Añadir contador de vistas de posts (simple)
 */
function wptbt_count_post_views()
{
    if (is_single()) {
        $post_id = get_the_ID();
        $count = get_post_meta($post_id, 'post_views_count', true);

        if ($count === '') {
            $count = 0;
        }

        // No contabilizar las visitas de administradores y editores
        if (!current_user_can('edit_posts')) {
            $count++;
            update_post_meta($post_id, 'post_views_count', $count);
        }
    }
}
add_action('wp_head', 'wptbt_count_post_views');

/**
 * Modificar el título de los archivos
 */
function wptbt_archive_title($title)
{
    if (is_category()) {
        $title = single_cat_title('', false);
    } elseif (is_tag()) {
        $title = single_tag_title('', false);
    } elseif (is_author()) {
        $title = get_the_author();
    } elseif (is_post_type_archive()) {
        $title = post_type_archive_title('', false);
    } elseif (is_tax()) {
        $title = single_term_title('', false);
    }

    return $title;
}
add_filter('get_the_archive_title', 'wptbt_archive_title');

/**
 * Función para obtener el número de comentarios de un usuario
 */
function get_user_comment_count($user_id)
{
    global $wpdb;

    $count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE user_id = %d AND comment_approved = '1'",
        $user_id
    ));

    return $count ? $count : 0;
}

/**
 * Añadir soporte para metadatos de autor
 */
function wptbt_add_user_social_links($user_contact)
{
    $user_contact['twitter'] = __('Twitter URL', 'wp-tailwind-theme');
    $user_contact['facebook'] = __('Facebook URL', 'wp-tailwind-theme');
    $user_contact['instagram'] = __('Instagram URL', 'wp-tailwind-theme');
    $user_contact['linkedin'] = __('LinkedIn URL', 'wp-tailwind-theme');

    return $user_contact;
}
add_filter('user_contactmethods', 'wptbt_add_user_social_links');

/**
 * Crear migas de pan (breadcrumbs)
 */
function wptbt_breadcrumbs()
{
    $delimiter = '<span class="delimiter px-2">/</span>';
    $home = __('Inicio', 'wp-tailwind-theme');
    $before = '<span class="current text-spa-primary font-medium">';
    $after = '</span>';

    echo '<div class="breadcrumbs flex flex-wrap items-center text-gray-600">';

    global $post;
    $homeLink = home_url('/');

    echo '<a href="' . $homeLink . '" class="home hover:text-spa-accent transition-colors flex items-center">';
    echo '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">';
    echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>';
    echo '</svg>' . $home . '</a>';

    if (is_category()) {
        echo $delimiter;
        $thisCat = get_category(get_query_var('cat'), false);

        if ($thisCat->parent != 0) {
            $parents = get_category_parents($thisCat->parent, true, $delimiter);
            echo str_replace('<a', '<a class="hover:text-spa-accent transition-colors"', $parents);
        }

        echo $before . __('Categoría: ', 'wp-tailwind-theme') . single_cat_title('', false) . $after;
    } elseif (is_tag()) {
        echo $delimiter;
        echo $before . __('Etiqueta: ', 'wp-tailwind-theme') . single_tag_title('', false) . $after;
    } elseif (is_author()) {
        echo $delimiter;
        global $author;
        $userdata = get_userdata($author);
        echo $before . __('Artículos de ', 'wp-tailwind-theme') . $userdata->display_name . $after;
    } elseif (is_day()) {
        echo $delimiter;
        echo '<a href="' . get_year_link(get_the_time('Y')) . '" class="hover:text-spa-accent transition-colors">' . get_the_time('Y') . '</a>';
        echo $delimiter;
        echo '<a href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '" class="hover:text-spa-accent transition-colors">' . get_the_time('F') . '</a>';
        echo $delimiter;
        echo $before . get_the_time('d') . $after;
    } elseif (is_month()) {
        echo $delimiter;
        echo '<a href="' . get_year_link(get_the_time('Y')) . '" class="hover:text-spa-accent transition-colors">' . get_the_time('Y') . '</a>';
        echo $delimiter;
        echo $before . get_the_time('F') . $after;
    } elseif (is_year()) {
        echo $delimiter;
        echo $before . get_the_time('Y') . $after;
    } elseif (is_single() && !is_attachment()) {
        if (get_post_type() != 'post') {
            $post_type = get_post_type_object(get_post_type());
            $slug = $post_type->rewrite;
            echo '<a href="' . $homeLink . $slug['slug'] . '/" class="hover:text-spa-accent transition-colors">' . $post_type->labels->singular_name . '</a>';
            echo $delimiter;
            echo $before . get_the_title() . $after;
        } else {
            $cat = get_the_category();
            if ($cat) {
                $cat = $cat[0];
                $parents = get_category_parents($cat, true, $delimiter);
                $parents = str_replace('<a', '<a class="hover:text-spa-accent transition-colors"', $parents);
                echo $parents;
            }
            echo $before . get_the_title() . $after;
        }
    } elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
        $post_type = get_post_type_object(get_post_type());
        if ($post_type) {
            echo $before . $post_type->labels->singular_name . $after;
        }
    } elseif (is_page() && !$post->post_parent) {
        echo $delimiter;
        echo $before . get_the_title() . $after;
    } elseif (is_page() && $post->post_parent) {
        $parent_id = $post->post_parent;
        $breadcrumbs = array();

        while ($parent_id) {
            $page = get_page($parent_id);
            $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '" class="hover:text-spa-accent transition-colors">' . get_the_title($page->ID) . '</a>';
            $parent_id = $page->post_parent;
        }

        $breadcrumbs = array_reverse($breadcrumbs);

        foreach ($breadcrumbs as $crumb) {
            echo $crumb . $delimiter;
        }

        echo $before . get_the_title() . $after;
    } elseif (is_search()) {
        echo $delimiter;
        echo $before . __('Resultados de búsqueda para: ', 'wp-tailwind-theme') . get_search_query() . $after;
    } elseif (is_404()) {
        echo $delimiter;
        echo $before . __('Error 404', 'wp-tailwind-theme') . $after;
    }

    echo '</div>';
}

add_action('pre_get_posts', function($query) {
    if (!is_admin() && $query->is_main_query() && is_post_type_archive('servicio')) {
        $query->set('orderby', 'ID');
        $query->set('order', 'ASC');
    }
}, 99);

// Iniciar el tema
new WPTBT_Theme_Init();
