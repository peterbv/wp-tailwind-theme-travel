<?php

/**
 * Bloque de Beneficios
 *
 * @package WPTBT
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Class WPTBT_Benefits_Block
 */
class WPTBT_Benefits_Block
{
    private $translate = '';
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translate = 'wptbt-benefits-block';
        // Registrar bloque
        add_action('init', [$this, 'register_block']);

        // Agregar shortcode como método alternativo
        add_shortcode('wptbt_benefits', [$this, 'render_benefits_shortcode']);
    }

    /**
     * Registrar el bloque de beneficios
     */
    public function register_block()
    {
        // Verificar que la función existe (Gutenberg está activo)
        if (!function_exists('register_block_type')) {
            return;
        }

        // Registrar script del bloque
        wp_register_script(
            'wptbt-benefits-block-editor',
            get_template_directory_uri() . '/assets/admin/js/benefits-block.js',
            ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components'],
            filemtime(get_template_directory() . '/assets/admin/js/benefits-block.js')
        );

        // Registrar estilos para el editor
        wp_register_style(
            'wptbt-benefits-block-editor-style',
            get_template_directory_uri() . '/assets/admin/css/benefits-block-style.css',
            ['wp-edit-blocks'],
            filemtime(get_template_directory() . '/assets/admin/css/benefits-block-style.css')
        );

        // Registrar el bloque
        register_block_type('wptbt/benefits-block', [
            'editor_script' => 'wptbt-benefits-block-editor',
            'editor_style'  => 'wptbt-benefits-block-editor-style',
            'render_callback' => [$this, 'render_benefits_block'],
            'attributes' => [
                'title' => [
                    'type' => 'string',
                    'default' => __('Experience True Relaxation', $this->translate)
                ],
                'subtitle' => [
                    'type' => 'string',
                    'default' => __('WHY CHOOSE US', $this->translate)
                ],
                'description' => [
                    'type' => 'string',
                    'default' => __('Discover what makes our spa treatments special', $this->translate)
                ],
                'content' => [
                    'type' => 'string',
                    'default' => __('Experience relaxation and rejuvenation with our exclusive treatments tailored to your needs. Our holistic approach ensures that every visit leaves you feeling refreshed and balanced.', $this->translate)
                ],
                'mediaType' => [
                    'type' => 'string',
                    'default' => 'image'
                ],
                'imageID' => [
                    'type' => 'number'
                ],
                'imageURL' => [
                    'type' => 'string'
                ],
                'videoURL' => [
                    'type' => 'string',
                    'default' => ''
                ],
                'videoEmbedCode' => [
                    'type' => 'string',
                    'default' => ''
                ],
                'useYouTube' => [
                    'type' => 'boolean',
                    'default' => false
                ],
                'autoplayVideo' => [
                    'type' => 'boolean',
                    'default' => false
                ],
                'benefits' => [
                    'type' => 'array',
                    'default' => [
                        [
                            'title' => __('EXPERT THERAPISTS', $this->translate),
                            'description' => __('Our team consists of certified professionals with years of experience in spa and wellness.', $this->translate)
                        ],
                        [
                            'title' => __('PREMIUM TREATMENTS', $this->translate),
                            'description' => __('We offer a wide range of exclusive treatments using only the highest quality products.', $this->translate)
                        ],
                        [
                            'title' => __('PEACEFUL ATMOSPHERE', $this->translate),
                            'description' => __('Our carefully designed spaces create the perfect environment for complete relaxation.', $this->translate)
                        ]
                    ]
                ],
                'backgroundColor' => [
                    'type' => 'string',
                    'default' => '#F9F5F2'
                ],
                'textColor' => [
                    'type' => 'string',
                    'default' => '#424242'
                ],
                'accentColor' => [
                    'type' => 'string',
                    'default' => '#D4B254'
                ],
                'secondaryColor' => [
                    'type' => 'string',
                    'default' => '#8BAB8D'
                ],
                'layout' => [
                    'type' => 'string',
                    'default' => 'full' // 'full' o 'boxed'
                ],
                'showWaves' => [
                    'type' => 'boolean',
                    'default' => true
                ],
                'imagePosition' => [
                    'type' => 'string',
                    'default' => 'right' // 'right' o 'left'
                ],
                'showTopWave' => [
                    'type' => 'boolean',
                    'default' => true
                ],
                'showBottomWave' => [
                    'type' => 'boolean',
                    'default' => true
                ],
            ]
        ]);
    }

    /**
     * Renderizar el bloque de beneficios mejorado con soporte de video
     *
     * @param array $attributes Atributos del bloque.
     * @return string HTML del bloque.
     */
    public function render_benefits_block($attributes)
    {
        // Extraer atributos
        $title = isset($attributes['title']) ? $attributes['title'] : __('Experience True Relaxation', $this->translate);
        $subtitle = isset($attributes['subtitle']) ? $attributes['subtitle'] : __('WHY CHOOSE US', $this->translate);
        $description = isset($attributes['description']) ? $attributes['description'] : __('Discover what makes our spa treatments special', $this->translate);
        $content = isset($attributes['content']) ? $attributes['content'] : __('Experience relaxation and rejuvenation with our exclusive treatments tailored to your needs. Our holistic approach ensures that every visit leaves you feeling refreshed and balanced.', $this->translate);
        $benefits = isset($attributes['benefits']) ? $attributes['benefits'] : [];
        $mediaType = isset($attributes['mediaType']) ? $attributes['mediaType'] : 'image';
        $imageURL = isset($attributes['imageURL']) ? $attributes['imageURL'] : '';
        $videoURL = isset($attributes['videoURL']) ? $attributes['videoURL'] : '';
        $videoEmbedCode = isset($attributes['videoEmbedCode']) ? $attributes['videoEmbedCode'] : '';
        $useYouTube = isset($attributes['useYouTube']) ? $attributes['useYouTube'] : false;
        $autoplayVideo = isset($attributes['autoplayVideo']) ? $attributes['autoplayVideo'] : false;
        $backgroundColor = isset($attributes['backgroundColor']) ? $attributes['backgroundColor'] : '#F9F5F2';
        $textColor = isset($attributes['textColor']) ? $attributes['textColor'] : '#424242';
        $accentColor = isset($attributes['accentColor']) ? $attributes['accentColor'] : '#D4B254';
        $secondaryColor = isset($attributes['secondaryColor']) ? $attributes['secondaryColor'] : '#8BAB8D';
        $layout = isset($attributes['layout']) ? $attributes['layout'] : 'full';
        $showTopWave = isset($attributes['showTopWave']) ? (bool)$attributes['showTopWave'] : true;
        $showBottomWave = isset($attributes['showBottomWave']) ? (bool)$attributes['showBottomWave'] : true;
        $imagePosition = isset($attributes['imagePosition']) ? $attributes['imagePosition'] : 'right';

        // Si no hay imagen seleccionada y se está usando imagen, usar una por defecto
        if ($mediaType === 'image' && empty($imageURL) && isset($attributes['imageID'])) {
            $imageURL = wp_get_attachment_image_url($attributes['imageID'], 'full');
        }

        if ($mediaType === 'image' && empty($imageURL)) {
            $imageURL = get_template_directory_uri() . '/assets/images/default-spa.jpg';
        }

        // Procesar código de YouTube para quitar width/height fijos si existen
        if ($useYouTube && !empty($videoEmbedCode)) {
            // Eliminar atributos width y height del iframe
            $videoEmbedCode = preg_replace('/(width|height)=["\']\d+["\']/i', '', $videoEmbedCode);

            // Añadir atributos class y style para responsive
            $videoEmbedCode = str_replace('<iframe ', '<iframe class="youtube-embed" style="width:100%; aspect-ratio:16/9;" ', $videoEmbedCode);

            // Añadir autoplay si está activado
            if ($autoplayVideo) {
                if (strpos($videoEmbedCode, 'autoplay=1') === false) {
                    if (strpos($videoEmbedCode, '?') !== false) {
                        $videoEmbedCode = str_replace('?', '?autoplay=1&mute=1&', $videoEmbedCode);
                    } else if (strpos($videoEmbedCode, 'src="') !== false) {
                        $videoEmbedCode = str_replace('src="', 'src="?autoplay=1&mute=1&', $videoEmbedCode);
                    }
                }
            }
        }

        // Iniciar buffer de salida
        ob_start();

        // Comprobar si es diseño a ancho completo
        if ($layout === 'full') {
            echo '<div class="wptbt-benefits-wrapper w-full" style="margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw); width: 100vw; max-width: 100vw;">';
        }
?>
        <div class="wptbt-benefits-container w-full py-16 md:py-24 relative overflow-hidden"
            style="background-color: <?php echo esc_attr($backgroundColor); ?>; 
                   color: <?php echo esc_attr($textColor); ?>;
                   background-image: radial-gradient(circle at 10% 20%, <?php echo esc_attr($this->hex2rgba($secondaryColor, 0.05)); ?> 0%, <?php echo esc_attr($this->hex2rgba($secondaryColor, 0)); ?> 20%),
                                     radial-gradient(circle at 90% 80%, <?php echo esc_attr($this->hex2rgba($accentColor, 0.07)); ?> 0%, <?php echo esc_attr($this->hex2rgba($accentColor, 0)); ?> 20%);">

            <!-- Elementos decorativos flotantes -->
            <div class="absolute -left-16 top-1/4 w-48 h-48 opacity-5 pointer-events-none rounded-full"
                style="background-color: <?php echo esc_attr($secondaryColor); ?>">
            </div>
            <div class="absolute -right-16 bottom-1/4 w-32 h-32 opacity-5 pointer-events-none rounded-full"
                style="background-color: <?php echo esc_attr($accentColor); ?>">
            </div>

            <?php if ($layout === 'full' && $showTopWave) : ?>
                <!-- Onda decorativa superior -->
                <div class="absolute top-0 left-0 right-0 z-10 transform rotate-180">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100" preserveAspectRatio="none" class="w-full h-16 md:h-20">
                        <path fill="white" d="M0,85.7L20,75.4C40,65.1,80,44.6,120,33.9C160,23.2,200,22.4,240,28.6C280,34.8,320,48.1,360,50C400,51.9,440,42.5,480,39.8C520,37.2,560,41.3,600,48.3C640,55.2,680,65,720,62.3C760,59.5,800,44.2,840,41.9C880,39.7,920,50.5,960,54.3C1000,58.2,1040,55.2,1080,58.2C1120,61.1,1160,69.9,1200,73.3C1240,76.8,1280,74.9,1320,70.9C1360,67,1400,61,1420,58L1440,55L1440,100L1420,100C1400,100,1360,100,1320,100C1280,100,1240,100,1200,100C1160,100,1120,100,1080,100C1040,100,1000,100,960,100C920,100,880,100,840,100C800,100,760,100,720,100C680,100,640,100,600,100C560,100,520,100,480,100C440,100,400,100,360,100C320,100,280,100,240,100C200,100,160,100,120,100C80,100,40,100,20,100L0,100Z"></path>
                    </svg>
                </div>
            <?php endif; ?>

            <div class="container mx-auto px-4 relative">
                <!-- Encabezado de la sección - Estilo elegante -->
                <div class="text-center mb-12 md:mb-16 relative">
                    <!-- Icono decorativo -->
                    <div class="absolute left-1/2 top-0 -translate-x-1/2 -translate-y-1/2 opacity-10 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="<?php echo esc_attr($secondaryColor); ?>" class="transform rotate-12">
                            <path d="M19.939 12.003c-.108 1.852-1.944 1.422-1.944 3.999 0 .831.662 1.498 1.498 1.498.831 0 1.498-.667 1.498-1.498 0-.358-.278-1.945-.278-1.945.355-2.053 1.283-3.943 1.283-5.261 0-3.192-2.804-5.797-6.002-5.797-2.197 0-4.102 1.179-5.147 2.936-1.045-1.757-2.95-2.936-5.147-2.936-3.197 0-6.001 2.605-6.001 5.797 0 1.318.932 3.208 1.283 5.261 0 0-.278 1.587-.278 1.945 0 .831.662 1.498 1.498 1.498.831 0 1.498-.667 1.498-1.498 0-2.577-1.836-2.147-1.944-3.999-.051-.856-.07-1.799-.07-2.763 0-1.894 1.595-3.428 3.562-3.428 1.96 0 3.554 1.534 3.554 3.428 0 0 .273 1.216 1.493 1.216s1.493-1.216 1.493-1.216c0-1.894 1.594-3.428 3.555-3.428 1.966 0 3.561 1.534 3.561 3.428 0 .964-.022 1.907-.071 2.763z" />
                        </svg>
                    </div>

                    <span class="block text-lg italic font-medium mb-2 reveal-item opacity-0" style="color: <?php echo esc_attr($accentColor); ?>;">
                        <?php echo esc_html($subtitle); ?>
                    </span>

                    <div class="relative inline-block">
                        <h2 class="text-3xl md:text-4xl lg:text-5xl fancy-text font-medium mb-4 reveal-item opacity-0">
                            <?php echo esc_html($title); ?>
                        </h2>
                        <div class="absolute -bottom-2 left-1/2 w-24 h-0.5 transform -translate-x-1/2"
                            style="background-color: <?php echo esc_attr($accentColor); ?>">
                            <div class="absolute left-1/2 top-1/2 w-2 h-2 rounded-full -translate-x-1/2 -translate-y-1/2"
                                style="background-color: <?php echo esc_attr($accentColor); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Descripción centrada con estilo elegante -->
                    <p class="text-xl md:text-2xl fancy-text font-light mt-6 max-w-2xl mx-auto reveal-item opacity-0" style="color: <?php echo esc_attr($accentColor); ?>;">
                        <?php echo esc_html($description); ?>
                    </p>
                </div>

                <!-- Disposición flexible para el contenido principal -->
                <div class="flex flex-col <?php echo $imagePosition === 'right' ? 'lg:flex-row' : 'lg:flex-row-reverse'; ?> items-center lg:items-stretch gap-10 lg:gap-16">
                    <!-- Columna de texto/beneficios -->
                    <div class="w-full lg:w-2/5 space-y-8 reveal-item opacity-0 translate-x-8">
                        <!-- Contenido principal -->
                        <p class="text-base md:text-lg leading-relaxed">
                            <?php echo esc_html($content); ?>
                        </p>

                        <!-- Lista de beneficios con estilo mejorado -->
                        <div class="benefits-list space-y-6 mt-8">
                            <?php
                            $counter = 0;
                            foreach ($benefits as $benefit) :
                                $counter++;
                                $benefitTitle = isset($benefit['title']) ? $benefit['title'] : '';
                                $benefitDesc = isset($benefit['description']) ? $benefit['description'] : '';
                                $delay = $counter * 0.1 + 0.3; // Retardo escalonado
                            ?>
                                <div class="benefit-item flex group reveal-item opacity-0 translate-y-4"
                                    style="transition-delay: <?php echo esc_attr($delay); ?>s; transform-origin: <?php echo $imagePosition === 'right' ? 'left' : 'right'; ?>">
                                    <div class="benefit-icon flex-shrink-0 mr-4 mt-1">
                                        <span class="flex items-center justify-center w-10 h-10 rounded-full shadow-sm transition-all duration-300 group-hover:shadow-md"
                                            style="background-color: <?php echo esc_attr($accentColor); ?>;">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white transition-transform duration-300 group-hover:scale-110" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="benefit-content flex-grow transform transition-transform duration-500 group-hover:translate-x-2">
                                        <h3 class="text-lg md:text-xl font-medium mb-2" style="color: <?php echo esc_attr($secondaryColor); ?>;">
                                            <?php echo esc_html($benefitTitle); ?>
                                        </h3>
                                        <p class="text-sm md:text-base opacity-80 group-hover:opacity-100 transition-opacity duration-300">
                                            <?php echo esc_html($benefitDesc); ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Columna de media (imagen o video) con mayor protagonismo -->
                    <div class="w-full lg:w-3/5 reveal-item opacity-0 <?php echo $imagePosition === 'right' ? 'translate-x-8' : '-translate-x-8'; ?>">
                        <?php if ($mediaType === 'video') : ?>
                            <?php if ($useYouTube && !empty($videoEmbedCode)) : ?>
                                <!-- Video de YouTube -->
                                <div class="relative rounded-xl overflow-hidden shadow-xl">
                                    <!-- Elemento decorativo -->
                                    <div class="absolute top-0 <?php echo $imagePosition === 'right' ? 'right-0' : 'left-0'; ?> w-24 h-24 md:w-32 md:h-32 -translate-y-1/2 <?php echo $imagePosition === 'right' ? 'translate-x-1/2' : '-translate-x-1/2'; ?> rounded-full opacity-20 z-20 transform rotate-12" style="background-color: <?php echo esc_attr($accentColor); ?>;"></div>
                                    <div class="absolute bottom-0 <?php echo $imagePosition === 'right' ? 'left-0' : 'right-0'; ?> w-16 h-16 translate-y-1/3 <?php echo $imagePosition === 'right' ? '-translate-x-1/3' : 'translate-x-1/3'; ?> rounded-full opacity-20 z-20" style="background-color: <?php echo esc_attr($secondaryColor); ?>;"></div>

                                    <!-- Borde interno -->
                                    <div class="absolute inset-0 rounded-xl border-4 border-white/20 z-10 pointer-events-none"></div>

                                    <!-- Video de YouTube -->
                                    <div class="youtube-video-container aspect-video w-full rounded-xl overflow-hidden transform transition-transform duration-700 hover:scale-[1.02]">
                                        <?php echo $videoEmbedCode; ?>
                                    </div>
                                </div>
                            <?php elseif (!empty($videoURL)) : ?>
                                <!-- Video subido -->
                                <div class="relative rounded-xl overflow-hidden shadow-xl">
                                    <!-- Elemento decorativo -->
                                    <div class="absolute top-0 <?php echo $imagePosition === 'right' ? 'right-0' : 'left-0'; ?> w-24 h-24 md:w-32 md:h-32 -translate-y-1/2 <?php echo $imagePosition === 'right' ? 'translate-x-1/2' : '-translate-x-1/2'; ?> rounded-full opacity-20 z-20 transform rotate-12" style="background-color: <?php echo esc_attr($accentColor); ?>;"></div>
                                    <div class="absolute bottom-0 <?php echo $imagePosition === 'right' ? 'left-0' : 'right-0'; ?> w-16 h-16 translate-y-1/3 <?php echo $imagePosition === 'right' ? '-translate-x-1/3' : 'translate-x-1/3'; ?> rounded-full opacity-20 z-20" style="background-color: <?php echo esc_attr($secondaryColor); ?>;"></div>

                                    <!-- Borde interno -->
                                    <div class="absolute inset-0 rounded-xl border-4 border-white/20 z-10 pointer-events-none"></div>

                                    <!-- Video subido -->
                                    <div class="overflow-hidden rounded-xl transform transition-transform duration-700 hover:scale-[1.02]">
                                        <video class="w-full aspect-video object-cover rounded-xl" <?php echo $autoplayVideo ? 'autoplay muted loop playsinline' : 'controls'; ?>>
                                            <source src="<?php echo esc_url($videoURL); ?>" type="video/mp4">
                                            <?php echo esc_html__('Your browser does not support the video tag.', $this->translate); ?>
                                        </video>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else : ?>
                            <!-- Imagen con estilo elegante -->
                            <div class="relative rounded-xl overflow-hidden shadow-xl transform transition-transform duration-700 hover:scale-[1.02]">
                                <!-- Borde interno -->
                                <div class="absolute inset-0 rounded-xl border-4 border-white/20 z-10 pointer-events-none"></div>

                                <!-- Gradiente sutil en la parte inferior de la imagen -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/15 to-transparent opacity-70 z-10"></div>

                                <!-- Elementos decorativos -->
                                <div class="absolute top-0 <?php echo $imagePosition === 'right' ? 'right-0' : 'left-0'; ?> w-24 h-24 md:w-32 md:h-32 -translate-y-1/2 <?php echo $imagePosition === 'right' ? 'translate-x-1/2' : '-translate-x-1/2'; ?> rounded-full opacity-20 z-20 transform rotate-12" style="background-color: <?php echo esc_attr($accentColor); ?>;"></div>
                                <div class="absolute bottom-0 <?php echo $imagePosition === 'right' ? 'left-0' : 'right-0'; ?> w-16 h-16 translate-y-1/3 <?php echo $imagePosition === 'right' ? '-translate-x-1/3' : 'translate-x-1/3'; ?> rounded-full opacity-20 z-20" style="background-color: <?php echo esc_attr($secondaryColor); ?>;"></div>

                                <!-- Imagen principal -->
                                <img
                                    src="<?php echo esc_url($imageURL); ?>"
                                    alt="<?php echo esc_attr($title); ?>"
                                    class="w-full h-auto object-cover rounded-xl aspect-[5/3] transition-transform duration-700 hover:scale-110" />
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($layout === 'full' && $showBottomWave) : ?>
                <!-- Onda decorativa inferior -->
                <div class="absolute bottom-0 left-0 right-0 z-10">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100" preserveAspectRatio="none" class="w-full h-16 md:h-20">
                        <path fill="white" d="M0,85.7L20,75.4C40,65.1,80,44.6,120,33.9C160,23.2,200,22.4,240,28.6C280,34.8,320,48.1,360,50C400,51.9,440,42.5,480,39.8C520,37.2,560,41.3,600,48.3C640,55.2,680,65,720,62.3C760,59.5,800,44.2,840,41.9C880,39.7,920,50.5,960,54.3C1000,58.2,1040,55.2,1080,58.2C1120,61.1,1160,69.9,1200,73.3C1240,76.8,1280,74.9,1320,70.9C1360,67,1400,61,1420,58L1440,55L1440,100L1420,100C1400,100,1360,100,1320,100C1280,100,1240,100,1200,100C1160,100,1120,100,1080,100C1040,100,1000,100,960,100C920,100,880,100,840,100C800,100,760,100,720,100C680,100,640,100,600,100C560,100,520,100,480,100C440,100,400,100,360,100C320,100,280,100,240,100C200,100,160,100,120,100C80,100,40,100,20,100L0,100Z"></path>
                    </svg>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($layout === 'full') {
            echo '</div>';
        } ?>

        <?php if ($mediaType === 'video' && $useYouTube && $autoplayVideo) : ?>
            <!-- Script para garantizar autoplay en ciertos navegadores -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var videoIframe = document.querySelector('.youtube-embed');
                    if (videoIframe) {
                        var src = videoIframe.getAttribute('src');
                        if (src.indexOf('autoplay=1') === -1) {
                            if (src.indexOf('?') !== -1) {
                                videoIframe.setAttribute('src', src + '&autoplay=1&mute=1');
                            } else {
                                videoIframe.setAttribute('src', src + '?autoplay=1&mute=1');
                            }
                        }
                    }
                });
            </script>
        <?php endif; ?>

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
                            item.classList.remove('translate-x-8');
                            item.classList.remove('-translate-x-8');
                        }
                    });
                }

                window.addEventListener('scroll', revealOnScroll);
                revealOnScroll(); // Comprobar elementos visibles al cargar
            });
        </script>

        <!-- Estilos adicionales para la integración de video -->
        <style>
            .wptbt-benefits-container .reveal-item {
                transition: opacity 0.8s ease-out, transform 0.8s ease-out;
            }

            .youtube-video-container {
                position: relative;
                overflow: hidden;
            }

            .youtube-video-container iframe {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                border-radius: 0.75rem;
            }
        </style>
<?php
        return ob_get_clean();
    }

    /**
     * Renderizar shortcode de beneficios
     *
     * @param array $atts Atributos del shortcode.
     * @return string HTML del shortcode.
     */
    public function render_benefits_shortcode($atts)
    {
        $attributes = shortcode_atts(
            array(
                'title' => __('Experience True Relaxation', $this->translate),
                'subtitle' => __('WHY CHOOSE US', $this->translate),
                'description' => __('Discover what makes our spa treatments special', $this->translate),
                'content' => __('Experience relaxation and rejuvenation with our exclusive treatments tailored to your needs. Our holistic approach ensures that every visit leaves you feeling refreshed and balanced.', $this->translate),
                'image_id' => '',
                'image_url' => '',
                'background_color' => '#F9F5F2',
                'text_color' => '#424242',
                'accent_color' => '#D4B254',
                'secondary_color' => '#8BAB8D',
                'layout' => 'boxed',
                'image_position' => 'right',
                'show_top_wave' => true,
                'show_bottom_wave' => true
            ),
            $atts
        );

        // Configurar beneficios predeterminados con traducciones
        $default_benefits = [
            [
                'title' => __('EXPERT THERAPISTS', $this->translate),
                'description' => __('Our team consists of certified professionals with years of experience in spa and wellness.', $this->translate)
            ],
            [
                'title' => __('PREMIUM TREATMENTS', $this->translate),
                'description' => __('We offer a wide range of exclusive treatments using only the highest quality products.', $this->translate)
            ],
            [
                'title' => __('PEACEFUL ATMOSPHERE', $this->translate),
                'description' => __('Our carefully designed spaces create the perfect environment for complete relaxation.', $this->translate)
            ]
        ];

        // Convertir atributos para el formato que espera render_benefits_block
        $block_attributes = array(
            'title' => $attributes['title'],
            'subtitle' => $attributes['subtitle'],
            'description' => $attributes['description'],
            'content' => $attributes['content'],
            'imageID' => !empty($attributes['image_id']) ? (int)$attributes['image_id'] : null,
            'imageURL' => $attributes['image_url'],
            'backgroundColor' => $attributes['background_color'],
            'textColor' => $attributes['text_color'],
            'accentColor' => $attributes['accent_color'],
            'secondaryColor' => $attributes['secondary_color'],
            'layout' => $attributes['layout'],
            'imagePosition' => $attributes['image_position'],
            'benefits' => $default_benefits,
            'showTopWave' => $attributes['show_top_wave'],
            'showBottomWave' => $attributes['show_bottom_wave']
        );

        return $this->render_benefits_block($block_attributes);
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
new WPTBT_Benefits_Block();
