<?php

/**
 * Bloque de Tours y Destinos para Agencia de Viajes
 *
 * @package WPTBT
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Class WPTBT_Services_Block - Tours and Destinations
 */
class WPTBT_Services_Block
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Registrar bloque
        add_action('init', [$this, 'register_block']);

        // Agregar shortcode como método alternativo
        add_shortcode('wptbt_tours', [$this, 'render_tours_shortcode']);
        // Mantener compatibilidad con shortcode anterior
        add_shortcode('wptbt_services', [$this, 'render_tours_shortcode']);
    }

    /**
     * Registrar el bloque de tours/destinos
     */
    public function register_block()
    {
        // Verificar que la función existe (Gutenberg está activo)
        if (!function_exists('register_block_type')) {
            return;
        }

        // Registrar script del bloque
        wp_register_script(
            'wptbt-tours-block-editor',
            get_template_directory_uri() . '/assets/admin/js/services-block.js',
            array('wp-blocks', 'wp-element', 'wp-i18n'),
            time(), // Usar time() para evitar cachés
            true
        );

        // Localización para el script
        wp_localize_script(
            'wptbt-tours-block-editor',
            'wptbtToursBlock',
            array(
                'title' => __('Descubre Nuestros Destinos', 'wptbt'),
                'subtitle' => __('TOURS & DESTINOS', 'wptbt'),
                'view_tour' => __('Ver Detalles', 'wptbt'),
                'no_tours' => __('No hay tours disponibles.', 'wptbt'),
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wptbt_services_nonce')
            )
        );

        // Registrar estilos para el editor
        wp_register_style(
            'wptbt-tours-block-editor-style',
            get_template_directory_uri() . '/assets/admin/css/services-block-style.css',
            array('wp-edit-blocks'),
            filemtime(get_template_directory() . '/assets/admin/css/services-block-style.css')
        );

        // Asegurarse que los directorios existen
        $js_path = get_template_directory() . '/assets/admin/js/services-block.js';
        $css_path = get_template_directory() . '/assets/admin/css/services-block-style.css';

        // Solo usar filemtime si el archivo existe
        $js_version = file_exists($js_path) ? filemtime($js_path) : '1.0';
        $css_version = file_exists($css_path) ? filemtime($css_path) : '1.0';

        // Simplifica la forma de registrar el bloque
        register_block_type('wptbt/tours-block', array(
            'editor_script' => 'wptbt-tours-block-editor',
            'editor_style'  => 'wptbt-tours-block-editor-style',
            'render_callback' => array($this, 'render_tours_block'),
            'attributes' => array(
                'title' => array(
                    'type' => 'string',
                    'default' => 'Descubre Nuestros Destinos'
                ),
                'subtitle' => array(
                    'type' => 'string',
                    'default' => 'TOURS & DESTINOS'
                ),
                'layout' => array(
                    'type' => 'string',
                    'default' => 'grid'
                ),
                'columns' => array(
                    'type' => 'number',
                    'default' => 2
                ),
                'showImage' => array(
                    'type' => 'boolean',
                    'default' => false
                ),
                'backgroundColor' => array(
                    'type' => 'string',
                    'default' => '#FFFFFF'
                ),
                'textColor' => array(
                    'type' => 'string',
                    'default' => '#424242'
                ),
                'accentColor' => array(
                    'type' => 'string',
                    'default' => '#F59E0B'
                ),
                'categoryId' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'postsPerPage' => array(
                    'type' => 'number',
                    'default' => -1
                )
            )
        ));
    }

    /**
     * Obtener precios de un tour (compatibilidad con formato antiguo y nuevo)
     */
    private function get_tour_prices($post_id)
    {
        // Primero intentar obtener el formato nuevo (múltiples precios)
        $prices = get_post_meta($post_id, '_wptbt_service_prices', true);

        if (!empty($prices) && is_array($prices)) {
            // Limitar a máximo 2 precios
            return array_slice($prices, 0, 2);
        }

        // Si no existe el formato nuevo, usar el formato antiguo
        $legacy_prices = [];

        $precio_duracion1 = get_post_meta($post_id, '_wptbt_service_duration1', true);
        $precio_valor1 = get_post_meta($post_id, '_wptbt_service_price1', true);
        $precio_duracion2 = get_post_meta($post_id, '_wptbt_service_duration2', true);
        $precio_valor2 = get_post_meta($post_id, '_wptbt_service_price2', true);

        if (!empty($precio_duracion1) && !empty($precio_valor1)) {
            $legacy_prices[] = ['duration' => $precio_duracion1, 'price' => $precio_valor1];
        }
        if (!empty($precio_duracion2) && !empty($precio_valor2)) {
            $legacy_prices[] = ['duration' => $precio_duracion2, 'price' => $precio_valor2];
        }

        // Fallback al precio simple antiguo
        if (empty($legacy_prices)) {
            $precio = get_post_meta($post_id, '_wptbt_service_price', true);
            if (!empty($precio)) {
                $legacy_prices[] = ['duration' => '', 'price' => $precio];
            }
        }

        return $legacy_prices;
    }

    /**
     * Renderizar el bloque de tours
     *
     * @param array $attributes Atributos del bloque.
     * @return string HTML del bloque.
     */
    public function render_tours_block($attributes)
    {
        // Extraer atributos
        $title = isset($attributes['title']) ? $attributes['title'] : 'Descubre Nuestros Destinos';
        $subtitle = isset($attributes['subtitle']) ? $attributes['subtitle'] : 'TOURS & DESTINOS';
        $layout = isset($attributes['layout']) ? $attributes['layout'] : 'grid';
        $columns = isset($attributes['columns']) ? intval($attributes['columns']) : 2;
        $showImage = isset($attributes['showImage']) ? $attributes['showImage'] : false;
        $backgroundColor = isset($attributes['backgroundColor']) ? $attributes['backgroundColor'] : '#FFFFFF';
        $textColor = isset($attributes['textColor']) ? $attributes['textColor'] : '#424242';
        $accentColor = isset($attributes['accentColor']) ? $attributes['accentColor'] : '#F59E0B';
        $secondaryColor = '#8BAB8D'; // Color secundario predeterminado
        $categoryId = isset($attributes['categoryId']) ? $attributes['categoryId'] : '';
        $postsPerPage = isset($attributes['postsPerPage']) ? intval($attributes['postsPerPage']) : -1;

        // Configurar la consulta para obtener tours
        $args = [
            'post_type'      => 'tours',
            'posts_per_page' => $postsPerPage,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ];
        
        // Fallback para compatibilidad con 'servicio'
        if (!post_type_exists('tours')) {
            $args['post_type'] = 'servicio';
        }

        // Filtrar por categoría si se especifica
        if (!empty($categoryId)) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'category',
                    'field'    => 'term_id',
                    'terms'    => $categoryId,
                ]
            ];
        }

        $tours_query = new WP_Query($args);

        // Iniciar buffer de salida
        ob_start();
?>

        <div class="wptbt-tours-wrapper w-full reveal-item opacity-0 translate-y-8">
            <div class="wptbt-tours-container py-10 md:py-14"
                style="background-color: <?php echo esc_attr($backgroundColor); ?>; 
                        color: <?php echo esc_attr($textColor); ?>;
                        background-image: radial-gradient(circle at 10% 20%, <?php echo esc_attr($this->hex2rgba($secondaryColor, 0.05)); ?> 0%, <?php echo esc_attr($this->hex2rgba($secondaryColor, 0)); ?> 20%),
                                        radial-gradient(circle at 90% 80%, <?php echo esc_attr($this->hex2rgba($accentColor, 0.07)); ?> 0%, <?php echo esc_attr($this->hex2rgba($accentColor, 0)); ?> 20%);">

                <!-- Elementos decorativos sutiles -->
                <div class="absolute -left-16 top-1/4 w-48 h-48 opacity-5 pointer-events-none rounded-full"
                    style="background-color: <?php echo esc_attr($secondaryColor); ?>">
                </div>
                <div class="absolute -right-16 bottom-1/4 w-32 h-32 opacity-5 pointer-events-none rounded-full"
                    style="background-color: <?php echo esc_attr($accentColor); ?>">
                </div>

                <div class="container mx-auto px-4 relative">
                    <!-- Encabezado de la sección - Estilo mejorado pero compacto -->
                    <div class="text-center mb-8 md:mb-10 relative">
                        <span class="block text-lg italic font-medium mb-2 reveal-item opacity-0" style="color: <?php echo esc_attr($accentColor); ?>;">
                            <?php echo esc_html($subtitle); ?>
                        </span>

                        <div class="relative inline-block">
                            <h2 class="text-3xl md:text-4xl fancy-text font-medium mb-4 reveal-item opacity-0">
                                <?php echo esc_html($title); ?>
                            </h2>
                            <div class="absolute -bottom-2 left-1/2 w-24 h-0.5 transform -translate-x-1/2"
                                style="background-color: <?php echo esc_attr($accentColor); ?>">
                                <div class="absolute left-1/2 top-1/2 w-2 h-2 rounded-full -translate-x-1/2 -translate-y-1/2"
                                    style="background-color: <?php echo esc_attr($accentColor); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($tours_query->have_posts()) : ?>
                        <?php if ($layout === 'grid') : ?>
                            <!-- Diseño de cuadrícula compacto -->
                            <div class="grid grid-cols-1 md:grid-cols-<?php echo esc_attr($columns); ?> gap-4">
                                <?php
                                $counter = 0;
                                while ($tours_query->have_posts()) :
                                    $tours_query->the_post();
                                    $counter++;

                                    // Obtener precios usando la nueva función
                                    $prices = $this->get_tour_prices(get_the_ID());
                                    $subtitle = get_post_meta(get_the_ID(), '_wptbt_service_subtitle', true);
                                    $delay = $counter * 0.1;
                                ?>
                                    <div class="tour-item bg-white rounded-lg overflow-hidden shadow-sm border border-gray-100 group hover:shadow-md transition-all duration-500 relative reveal-item opacity-0 translate-y-4"
                                        style="transition-delay: <?php echo esc_attr($delay); ?>s">

                                        <?php if (has_post_thumbnail()) : ?>
                                            <!-- Imagen que aparece en hover (posicionada absolutamente) -->
                                            <div class="tour-thumbnail absolute inset-0 w-full h-full opacity-0 group-hover:opacity-100 transition-all duration-500 z-0 overflow-hidden">
                                                <?php the_post_thumbnail('medium', ['class' => 'w-full h-full object-cover scale-110 group-hover:scale-100 transition-transform duration-700']); ?>
                                                <div class="absolute inset-0 bg-black/50"></div>
                                            </div>
                                        <?php endif; ?>

                                        <div class="p-4 relative z-10 bg-white group-hover:bg-transparent transition-all duration-300 group-hover:text-white">
                                            <h3 class="text-lg font-medium mb-1 transition-colors duration-300"><?php the_title(); ?></h3>

                                            <?php if ($subtitle) : ?>
                                                <div class="text-gray-500 text-xs mb-3 group-hover:text-gray-200 transition-colors duration-300">
                                                    <?php echo esc_html($subtitle); ?>
                                                </div>
                                            <?php endif; ?>

                                            <div class="price-container space-y-1">
                                                <?php if (!empty($prices)) : ?>
                                                    <?php foreach ($prices as $price_item) : ?>
                                                        <div class="flex justify-between items-center py-1 border-b border-gray-50 group-hover:border-white/30 last:border-b-0">
                                                            <?php if (!empty($price_item['duration'])) : ?>
                                                                <span class="text-xs font-bold px-2 py-1 rounded-sm bg-[<?php echo esc_attr($secondaryColor); ?>]/20 text-[#31503B] inline-block transition-colors duration-300 group-hover:text-white group-hover:bg-white/20">
                                                                    <?php echo esc_html($price_item['duration']); ?> <?php echo esc_html__('DÍAS', 'wptbt'); ?>
                                                                </span>
                                                            <?php else : ?>
                                                                <span></span>
                                                            <?php endif; ?>
                                                            <span class="font-semibold text-lg group-hover:text-white transition-colors duration-300" style="color: <?php echo esc_attr($accentColor); ?>;">
                                                                <?php echo esc_html($price_item['price']); ?>
                                                            </span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else : ?>
                                                    <div class="text-center py-2 text-gray-500 group-hover:text-gray-200">
                                                        <?php echo esc_html__('Contact for pricing', 'wptbt'); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Enlace que cubre toda la tarjeta -->
                                        <a href="<?php the_permalink(); ?>" class="absolute inset-0 z-20" aria-label="<?php echo esc_attr__('View service details', 'wptbt'); ?>: <?php the_title_attribute(); ?>"></a>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else : ?>
                            <!-- Diseño de lista compacta -->
                            <div class="space-y-3">
                                <?php
                                $counter = 0;
                                while ($tours_query->have_posts()) :
                                    $tours_query->the_post();
                                    $counter++;

                                    // Obtener precios usando la nueva función
                                    $prices = $this->get_tour_prices(get_the_ID());
                                    $has_thumbnail = has_post_thumbnail() && $showImage;
                                    $delay = $counter * 0.1;
                                ?>
                                    <div class="service-item bg-white rounded-lg overflow-hidden shadow-sm border border-gray-100 group hover:shadow-md transition-all duration-300 relative reveal-item opacity-0 translate-y-4"
                                        style="transition-delay: <?php echo esc_attr($delay); ?>s">
                                        <div class="flex flex-row items-center">
                                            <?php if ($has_thumbnail) : ?>
                                                <div class="service-thumbnail w-16 h-16 overflow-hidden hidden md:block group-hover:hidden">
                                                    <?php the_post_thumbnail('thumbnail', ['class' => 'w-full h-full object-cover']); ?>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (has_post_thumbnail()) : ?>
                                                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-all duration-500 z-0 overflow-hidden">
                                                    <?php the_post_thumbnail('medium', ['class' => 'w-full h-full object-cover scale-110 group-hover:scale-100 transition-transform duration-700']); ?>
                                                    <div class="absolute inset-0 bg-black/40"></div>
                                                </div>
                                            <?php endif; ?>

                                            <div class="p-3 flex-1 relative z-10 bg-white group-hover:bg-transparent transition-all duration-300 group-hover:text-white">
                                                <h3 class="text-base font-medium transition-colors duration-300"><?php the_title(); ?></h3>
                                            </div>

                                            <div class="flex flex-col px-3 relative z-10 bg-white group-hover:bg-transparent transition-all duration-300 min-w-0 flex-shrink-0">
                                                <?php if (!empty($prices)) : ?>
                                                    <?php
                                                    // Mostrar solo los primeros 2 precios en vista de lista para no sobrecargar
                                                    $displayed_prices = array_slice($prices, 0, 2);
                                                    foreach ($displayed_prices as $index => $price_item) :
                                                    ?>
                                                        <div class="flex items-center gap-2 <?php echo $index > 0 ? 'mt-1' : ''; ?>">
                                                            <?php if (!empty($price_item['duration'])) : ?>
                                                                <span class="text-xs font-medium transition-colors duration-300 text-[<?php echo esc_attr($secondaryColor); ?>] group-hover:text-white whitespace-nowrap">
                                                                    <?php echo esc_html($price_item['duration']); ?> <?php echo esc_html__('DÍAS', 'wptbt'); ?>
                                                                </span>
                                                            <?php endif; ?>
                                                            <span class="font-semibold text-base group-hover:text-white transition-colors duration-300 whitespace-nowrap" style="color: <?php echo esc_attr($accentColor); ?>;">
                                                                <?php echo esc_html($price_item['price']); ?>
                                                            </span>
                                                        </div>
                                                    <?php endforeach; ?>

                                                    <?php if (count($prices) > 2) : ?>
                                                        <div class="text-xs text-gray-500 group-hover:text-gray-300 mt-1">
                                                            +<?php echo count($prices) - 2; ?> <?php echo esc_html__('more options', 'wptbt'); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php else : ?>
                                                    <span class="font-semibold text-base group-hover:text-white transition-colors duration-300" style="color: <?php echo esc_attr($accentColor); ?>;">
                                                        <?php echo esc_html__('Contact for pricing', 'wptbt'); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <a href="<?php the_permalink(); ?>" class="p-3 text-white hover:opacity-90 transition-opacity relative z-10 flex-shrink-0"
                                                style="background-color: <?php echo esc_attr($accentColor); ?>;"
                                                aria-label="<?php echo esc_attr__('View service details', 'wptbt'); ?>: <?php the_title_attribute(); ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform duration-300 group-hover:translate-x-1" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        </div>

                                        <?php if (!$has_thumbnail) : ?>
                                            <!-- Enlace que cubre toda la tarjeta si no hay imagen visible -->
                                            <a href="<?php the_permalink(); ?>" class="absolute inset-0 z-20" aria-label="<?php echo esc_attr__('View service details', 'wptbt'); ?>: <?php the_title_attribute(); ?>"></a>
                                        <?php endif; ?>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php endif; ?>

                        <?php wp_reset_postdata(); ?>
                    <?php else : ?>
                        <div class="text-center py-6 bg-white/80 backdrop-blur-sm rounded-lg p-6 shadow-sm border border-gray-100">
                            <p class="text-gray-500"><?php echo esc_html__('No hay tours disponibles.', 'wptbt'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Script para animar elementos al hacer scroll -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const revealItems = document.querySelectorAll('.reveal-item');

                function revealOnScroll() {
                    revealItems.forEach(item => {
                        const itemTop = item.getBoundingClientRect().top;
                        const windowHeight = window.innerHeight;

                        if (itemTop < windowHeight * 0.85) {
                            item.classList.add('opacity-100');
                            item.classList.remove('opacity-0');
                            item.classList.remove('translate-y-4');
                            item.classList.remove('translate-y-8');
                        }
                    });
                }

                window.addEventListener('scroll', revealOnScroll);
                revealOnScroll(); // Comprobar elementos visibles al cargar
            });
        </script>
<?php
        return ob_get_clean();
    }

    /**
     * Renderizar shortcode de tours
     *
     * @param array $atts Atributos del shortcode.
     * @return string HTML del shortcode.
     */
    public function render_tours_shortcode($atts)
    {
        $attributes = shortcode_atts(
            array(
                'title' => 'Descubre Nuestros Destinos',
                'subtitle' => 'TOURS & DESTINOS',
                'layout' => 'grid',
                'columns' => 2,
                'show_image' => false,
                'background_color' => '#FFFFFF',
                'text_color' => '#424242',
                'accent_color' => '#F59E0B',
                'category_id' => '',
                'posts_per_page' => -1
            ),
            $atts
        );

        // Convertir atributos para el formato que espera render_services_block
        $block_attributes = array(
            'title' => $attributes['title'],
            'subtitle' => $attributes['subtitle'],
            'layout' => $attributes['layout'],
            'columns' => intval($attributes['columns']),
            'showImage' => filter_var($attributes['show_image'], FILTER_VALIDATE_BOOLEAN),
            'backgroundColor' => $attributes['background_color'],
            'textColor' => $attributes['text_color'],
            'accentColor' => $attributes['accent_color'],
            'categoryId' => $attributes['category_id'],
            'postsPerPage' => intval($attributes['posts_per_page'])
        );

        return $this->render_tours_block($block_attributes);
    }

    /**
     * Convierte un color hexadecimal a rgba
     *
     * @param string $hex Color hexadecimal
     * @param float $alpha Valor de opacidad (0-1)
     * @return string Color en formato rgba
     */
    private function hex2rgba($hex, $alpha = 1)
    {
        $hex = str_replace('#', '', $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        return "rgba($r, $g, $b, $alpha)";
    }
}

// Inicializar la clase
new WPTBT_Services_Block();
