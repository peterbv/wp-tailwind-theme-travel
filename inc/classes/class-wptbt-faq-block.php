<?php

/**
 * Bloque de FAQ con Solid.js
 *
 * @package WPTBT
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Class WPTBT_Solid_FAQ_Block
 */
class WPTBT_Solid_FAQ_Block
{
    private $translate = '';
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translate = 'wptbt-faq-block';
        // Registrar bloque
        add_action('init', [$this, 'register_block']);

        // Agregar shortcode como método alternativo
        add_shortcode('wptbt_solid_faq', [$this, 'render_faq_shortcode']);
    }

    /**
     * Registrar el bloque de FAQ
     */
    public function register_block()
    {
        // Verificar que la función existe (Gutenberg está activo)
        if (!function_exists('register_block_type')) {
            return;
        }

        // Registrar script del bloque para el editor
        wp_register_script(
            'wptbt-solid-faq-block-editor',
            get_template_directory_uri() . '/assets/admin/js/faq-block.js',
            ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components'],
            filemtime(get_template_directory() . '/assets/admin/js/faq-block.js')
        );

        // Registrar estilos para el editor
        wp_register_style(
            'wptbt-solid-faq-block-editor-style',
            get_template_directory_uri() . '/assets/admin/css/faq-block-style.css',
            ['wp-edit-blocks'],
            filemtime(get_template_directory() . '/assets/admin/css/faq-block-style.css')
        );

        // Registrar el bloque con los mismos atributos que usas actualmente
        register_block_type('wptbt/faq-block', [
            'editor_script' => 'wptbt-solid-faq-block-editor',
            'editor_style'  => 'wptbt-solid-faq-block-editor-style',
            'render_callback' => [$this, 'render_faq_block'],
            'attributes' => [
                'title' => [
                    'type' => 'string',
                    'default' => __('Preguntas Frecuentes', $this->translate)
                ],
                'subtitle' => [
                    'type' => 'string',
                    'default' => __('Resolvemos tus dudas sobre viajes', $this->translate)
                ],
                'faqs' => [
                    'type' => 'array',
                    'default' => [
                        [
                            'question' => __('¿Qué incluyen nuestros tours?', $this->translate),
                            'answer' => __('Nuestros tours incluyen alojamiento, transporte, guía experto, algunas comidas según el itinerario y todas las actividades programadas. Los vuelos internacionales no están incluidos a menos que se especifique.', $this->translate)
                        ],
                        [
                            'question' => __('¿Cuál es la política de cancelación?', $this->translate),
                            'answer' => __('Ofrecemos cancelación gratuita hasta 30 días antes del viaje. Entre 30 y 15 días se aplica un 50% de penalización, y menos de 15 días el 100% del costo del tour.', $this->translate)
                        ],
                        [
                            'question' => __('¿Necesito seguro de viaje?', $this->translate),
                            'answer' => __('Recomendamos encarecidamente contratar un seguro de viaje que cubra gastos médicos, cancelaciones y pérdida de equipaje. Podemos ayudarte a encontrar las mejores opciones.', $this->translate)
                        ]
                    ]
                ],
                'backgroundColor' => [
                    'type' => 'string',
                    'default' => '#F7EDE2'
                ],
                'textColor' => [
                    'type' => 'string',
                    'default' => '#424242'
                ],
                'accentColor' => [
                    'type' => 'string',
                    'default' => '#F59E0B'
                ],
                'layout' => [
                    'type' => 'string',
                    'default' => 'full' // 'full' o 'boxed'
                ],
                'showContactButton' => [
                    'type' => 'boolean',
                    'default' => true
                ],
                'contactText' => [
                    'type' => 'string',
                    'default' => __('¿Tienes más preguntas?', $this->translate)
                ],
                'contactUrl' => [
                    'type' => 'string',
                    'default' => '#contact'
                ],
                'openFirst' => [
                    'type' => 'boolean',
                    'default' => false
                ],
                'singleOpen' => [
                    'type' => 'boolean',
                    'default' => false
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
     * Renderizar el bloque de FAQ
     *
     * @param array $attributes Atributos del bloque.
     * @return string HTML del bloque.
     */
    public function render_faq_block($attributes)
    {
        // Intentar usar Solid.js si está disponible
        if (function_exists('wptbt_load_solid_component')) {
            // Cargar el componente Solid.js
            $solid_loaded = wptbt_load_solid_component('faq');

            if ($solid_loaded) {
                return $this->render_solid_faq($attributes);
            }
        }

        // Fallback al método original si Solid.js no está disponible
        return $this->render_traditional_faq($attributes);
    }

    /**
     * Renderizar el FAQ usando el componente Solid.js
     *
     * @param array $attributes Atributos del bloque.
     * @return string HTML del componente Solid.js
     */
    private function render_solid_faq($attributes)
    {
        // Extraer atributos
        $title = isset($attributes['title']) ? $attributes['title'] : __('Frequently Asked Questions', $this->translate);
        $subtitle = isset($attributes['subtitle']) ? $attributes['subtitle'] : __('We answer your questions', $this->translate);
        $faqs = isset($attributes['faqs']) ? $attributes['faqs'] : [];
        $backgroundColor = isset($attributes['backgroundColor']) ? $attributes['backgroundColor'] : '#F7EDE2';
        $textColor = isset($attributes['textColor']) ? $attributes['textColor'] : '#424242';
        $accentColor = isset($attributes['accentColor']) ? $attributes['accentColor'] : '#D4B254';
        $layout = isset($attributes['layout']) ? $attributes['layout'] : 'full';
        $showContactButton = isset($attributes['showContactButton']) ? $attributes['showContactButton'] : true;
        $contactText = isset($attributes['contactText']) ? $attributes['contactText'] : __('Do you have more questions?', $this->translate);
        $contactUrl = isset($attributes['contactUrl']) ? $attributes['contactUrl'] : '#contact';
        $openFirst = isset($attributes['openFirst']) ? $attributes['openFirst'] : false;
        $singleOpen = isset($attributes['singleOpen']) ? $attributes['singleOpen'] : false;
        $showTopWave = isset($attributes['showTopWave']) ? (bool)$attributes['showTopWave'] : true;
        $showBottomWave = isset($attributes['showBottomWave']) ? (bool)$attributes['showBottomWave'] : true;

        // Configurar propiedades para el componente
        $props = [
            'title' => $title,
            'subtitle' => $subtitle,
            'faqs' => $faqs,
            'backgroundColor' => $backgroundColor,
            'textColor' => $textColor,
            'accentColor' => $accentColor,
            'secondaryColor' => '#8BAB8D', // Valor predeterminado para el color secundario
            'layout' => $layout,
            'showContactButton' => $showContactButton,
            'contactText' => $contactText,
            'contactUrl' => $contactUrl,
            'openFirst' => $openFirst,
            'singleOpen' => $singleOpen,
            'showTopWave' => $showTopWave,
            'showBottomWave' => $showBottomWave,
            'animateEntrance' => true
        ];

        // ID único para el contenedor
        $container_id = 'solid-faq-' . uniqid();

        // Atributos para el contenedor
        $container_attrs = [
            'id' => $container_id,
            'class' => 'solid-faq-container'
        ];

        // Usar la función auxiliar global para generar el componente
        // Esta debe estar definida en tu clase WPTBT_Solid_JS_Loader
        return wptbt_faq_component($props, $container_attrs);
    }

    /**
     * Método tradicional para renderizar el FAQ (como fallback)
     *
     * @param array $attributes Atributos del bloque.
     * @return string HTML del bloque.
     */
    private function render_traditional_faq($attributes)
    {
        // Extraer atributos
        $title = isset($attributes['title']) ? $attributes['title'] : __('Frequently Asked Questions', $this->translate);
        $subtitle = isset($attributes['subtitle']) ? $attributes['subtitle'] : __('We answer your questions', $this->translate);
        $faqs = isset($attributes['faqs']) ? $attributes['faqs'] : [];
        $backgroundColor = isset($attributes['backgroundColor']) ? $attributes['backgroundColor'] : '#F7EDE2';
        $textColor = isset($attributes['textColor']) ? $attributes['textColor'] : '#424242';
        $accentColor = isset($attributes['accentColor']) ? $attributes['accentColor'] : '#D4B254';
        $layout = isset($attributes['layout']) ? $attributes['layout'] : 'full';
        $showContactButton = isset($attributes['showContactButton']) ? $attributes['showContactButton'] : true;
        $contactText = isset($attributes['contactText']) ? $attributes['contactText'] : __('Do you have more questions?', $this->translate);
        $contactUrl = isset($attributes['contactUrl']) ? $attributes['contactUrl'] : '#contact';
        $showTopWave = isset($attributes['showTopWave']) ? (bool)$attributes['showTopWave'] : true;
        $showBottomWave = isset($attributes['showBottomWave']) ? (bool)$attributes['showBottomWave'] : true;

        // Enqueue scripts
        wp_enqueue_script('wptbt-faq-script');

        // Generar ID único para el accordeon
        $accordion_id = 'faq-accordion-' . uniqid();

        // Iniciar buffer de salida
        ob_start();

        // Comprobar si es diseño a ancho completo
        if ($layout === 'full') {
            echo '<div class="wptbt-faq-wrapper w-full" style="margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw); width: 100vw; max-width: 100vw;">';
        }
?>
        <div class="wptbt-faq-container py-16 md:py-20 lg:py-24 relative reveal-item opacity-0 translate-y-8" style="background-color: <?php echo esc_attr($backgroundColor); ?>; color: <?php echo esc_attr($textColor); ?>;">
            <!-- Onda decorativa superior (opcional) -->
            <?php if ($layout === 'full' && $showTopWave) : ?>
                <div class="absolute top-0 left-0 right-0 z-10 transform rotate-180">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100" preserveAspectRatio="none" class="w-full h-16 md:h-20">
                        <path fill="white" d="M0,85.7L20,75.4C40,65.1,80,44.6,120,33.9C160,23.2,200,22.4,240,28.6C280,34.8,320,48.1,360,50C400,51.9,440,42.5,480,39.8C520,37.2,560,41.3,600,48.3C640,55.2,680,65,720,62.3C760,59.5,800,44.2,840,41.9C880,39.7,920,50.5,960,54.3C1000,58.2,1040,55.2,1080,58.2C1120,61.1,1160,69.9,1200,73.3C1240,76.8,1280,74.9,1320,70.9C1360,67,1400,61,1420,58L1440,55L1440,100L1420,100C1400,100,1360,100,1320,100C1280,100,1240,100,1200,100C1160,100,1120,100,1080,100C1040,100,1000,100,960,100C920,100,880,100,840,100C800,100,760,100,720,100C680,100,640,100,600,100C560,100,520,100,480,100C440,100,400,100,360,100C320,100,280,100,240,100C200,100,160,100,120,100C80,100,40,100,20,100L0,100Z"></path>
                    </svg>
                </div>
            <?php endif; ?>

            <div class="container mx-auto px-4">

                <!-- Encabezado de la sección - Estilo consistente con otros bloques -->
                <div class="text-center mb-12 md:mb-16">
                    <span class="block text-lg italic font-medium mb-2" style="color: <?php echo esc_attr($accentColor); ?>;">
                        <?php echo esc_html($subtitle); ?>
                    </span>
                    <div class="relative inline-block">
                        <h2 class="text-3xl md:text-4xl lg:text-5xl fancy-text font-medium mb-4">
                            <?php echo esc_html($title); ?>
                        </h2>
                        <span class="absolute bottom-0 left-0 w-full h-1" style="background-color: <?php echo esc_attr($accentColor); ?>; transform: scaleX(0.3); transform-origin: center;"></span>
                    </div>

                </div>

                <!-- Acordeón de FAQ -->
                <div class="max-w-3xl mx-auto" id="<?php echo esc_attr($accordion_id); ?>">
                    <?php foreach ($faqs as $index => $faq) :
                        $question_id = 'faq-' . $accordion_id . '-' . $index;
                        $answer_id = 'answer-' . $accordion_id . '-' . $index;
                    ?>
                        <div class="faq-item mb-4 rounded-lg overflow-hidden border border-gray-200 bg-white">
                            <h3>
                                <button
                                    class="faq-question w-full text-left p-5 relative pr-12 font-medium text-lg flex items-center transition-colors duration-300 hover:text-[<?php echo esc_attr($accentColor); ?>]"
                                    id="<?php echo esc_attr($question_id); ?>"
                                    aria-expanded="false"
                                    aria-controls="<?php echo esc_attr($answer_id); ?>">
                                    <span class="faq-icon flex-shrink-0 mr-4 w-6 h-6 rounded-full grid place-items-center" style="background-color: <?php echo esc_attr($accentColor); ?>;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                    <span><?php echo esc_html($faq['question']); ?></span>
                                </button>
                            </h3>
                            <div
                                id="<?php echo esc_attr($answer_id); ?>"
                                role="region"
                                aria-labelledby="<?php echo esc_attr($question_id); ?>"
                                class="faq-answer px-5 pb-5 pt-0 hidden"
                                style="padding-left: 61px;">
                                <div class="prose max-w-none">
                                    <?php echo wp_kses_post($faq['answer']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Botón opcional para más información -->
                <?php if ($showContactButton) : ?>
                    <div class="text-center mt-12">
                        <a href="<?php echo esc_url($contactUrl); ?>" class="inline-block px-8 py-3 rounded-sm text-white font-medium transition-all duration-300 hover:shadow-lg" style="background-color: <?php echo esc_attr($accentColor); ?>;">
                            <?php echo esc_html($contactText); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Onda decorativa inferior (opcional) -->
            <?php if ($layout === 'full' && $showBottomWave) : ?>
                <div class="absolute bottom-0 left-0 right-0 z-10">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100" preserveAspectRatio="none" class="w-full h-16 md:h-20">
                        <path fill="white" d="M0,85.7L20,75.4C40,65.1,80,44.6,120,33.9C160,23.2,200,22.4,240,28.6C280,34.8,320,48.1,360,50C400,51.9,440,42.5,480,39.8C520,37.2,560,41.3,600,48.3C640,55.2,680,65,720,62.3C760,59.5,800,44.2,840,41.9C880,39.7,920,50.5,960,54.3C1000,58.2,1040,55.2,1080,58.2C1120,61.1,1160,69.9,1200,73.3C1240,76.8,1280,74.9,1320,70.9C1360,67,1400,61,1420,58L1440,55L1440,100L1420,100C1400,100,1360,100,1320,100C1280,100,1240,100,1200,100C1160,100,1120,100,1080,100C1040,100,1000,100,960,100C920,100,880,100,840,100C800,100,760,100,720,100C680,100,640,100,600,100C560,100,520,100,480,100C440,100,400,100,360,100C320,100,280,100,240,100C200,100,160,100,120,100C80,100,40,100,20,100L0,100Z"></path>
                    </svg>
                </div>
            <?php endif; ?>
        </div>
<?php
        if ($layout === 'full') {
            echo '</div>';
        }

        return ob_get_clean();
    }

    /**
     * Renderizar shortcode de FAQ
     *
     * @param array $atts Atributos del shortcode.
     * @return string HTML del shortcode.
     */
    public function render_faq_shortcode($atts)
    {
        $attributes = shortcode_atts(
            array(
                'title' => __('Frequently Asked Questions', $this->translate),
                'subtitle' => __('We answer your questions', $this->translate),
                'background_color' => '#F7EDE2',
                'text_color' => '#424242',
                'accent_color' => '#D4B254',
                'layout' => 'boxed',
                'contact_text' => __('Do you have more questions?', $this->translate),
                'contact_url' => '#contact',
                'show_contact_button' => 'true',
                'open_first' => 'false',
                'single_open' => 'false',
                'show_top_wave' => true,
                'show_bottom_wave' => true
            ),
            $atts
        );

        // Preguntas predeterminadas para el shortcode
        $faqs = [
            [
                'question' => __('What should I do before my first massage session?', $this->translate),
                'answer' => __('We recommend arriving 15 minutes before your appointment to complete a brief health questionnaire. Wear comfortable clothing and avoid heavy meals or alcohol before your session. If you have any medical conditions or concerns, please notify us in advance.', $this->translate)
            ],
            [
                'question' => __('How long does a typical massage session last?', $this->translate),
                'answer' => __('Our standard massage sessions last 60 minutes, but we also offer 30, 90, and 120-minute options depending on your needs and preferences.', $this->translate)
            ],
            [
                'question' => __('Is it necessary to undress completely for a massage?', $this->translate),
                'answer' => __('It is not necessary. You can undress to your comfort level. During the massage, you will be covered with sheets, and only the part of the body being treated will be uncovered.', $this->translate)
            ]
        ];

        // Convertir atributos para el formato que espera render_faq_block
        $block_attributes = array(
            'title' => $attributes['title'],
            'subtitle' => $attributes['subtitle'],
            'backgroundColor' => $attributes['background_color'],
            'textColor' => $attributes['text_color'],
            'accentColor' => $attributes['accent_color'],
            'layout' => $attributes['layout'],
            'showContactButton' => $attributes['show_contact_button'] === 'true',
            'contactText' => $attributes['contact_text'],
            'contactUrl' => $attributes['contact_url'],
            'openFirst' => $attributes['open_first'] === 'true',
            'singleOpen' => $attributes['single_open'] === 'true',
            'faqs' => $faqs,
            'showTopWave' => $attributes['show_top_wave'],
            'showBottomWave' => $attributes['show_bottom_wave']
        );

        return $this->render_faq_block($block_attributes);
    }
}

// Inicializar la clase
new WPTBT_Solid_FAQ_Block();
