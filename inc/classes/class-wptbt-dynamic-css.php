<?php

/**
 * Clase para generar CSS dinámico a partir de las opciones del Customizer
 */
class WPTBT_Dynamic_CSS
{
    /**
     * Inicializar
     */
    public function init()
    {
        add_action('wp_head', array($this, 'output_custom_css'));
        add_action('customize_preview_init', array($this, 'live_preview'));
    }

    /**
     * Generar y mostrar CSS personalizado
     */
    public function output_custom_css()
    {
        $primary_color = get_theme_mod('primary_color', '#0d6efd');
        $secondary_color = get_theme_mod('secondary_color', '#6c757d');
        $accent_color = get_theme_mod('accent_color', '#ffc107');

        // Crear variables CSS personalizadas
        $custom_css = "
        <style type='text/css' id='wptbt-dynamic-css'>
            :root {
                --color-primary: {$primary_color};
                --color-primary-rgb: " . $this->hex_to_rgb($primary_color) . ";
                --color-secondary: {$secondary_color};
                --color-secondary-rgb: " . $this->hex_to_rgb($secondary_color) . ";
                --color-accent: {$accent_color};
                --color-accent-rgb: " . $this->hex_to_rgb($accent_color) . ";
            }
            
            /* Sobreescribir clases de Tailwind con variables CSS */
            .text-primary {
                color: var(--color-primary) !important;
            }
            
            .bg-primary {
                background-color: var(--color-primary) !important;
            }
            
            .border-primary {
                border-color: var(--color-primary) !important;
            }
            
            .hover\\:text-primary:hover {
                color: var(--color-primary) !important;
            }
            
            .hover\\:bg-primary:hover {
                background-color: var(--color-primary) !important;
            }
            
            .hover\\:border-primary:hover {
                border-color: var(--color-primary) !important;
            }
            
            .focus\\:ring-primary:focus {
                --tw-ring-color: var(--color-primary) !important;
            }
            
            .text-secondary {
                color: var(--color-secondary) !important;
            }
            
            .bg-secondary {
                background-color: var(--color-secondary) !important;
            }
            
            .border-secondary {
                border-color: var(--color-secondary) !important;
            }
            
            /* Colores de acento */
            .text-accent {
                color: var(--color-accent) !important;
            }
            
            .bg-accent {
                background-color: var(--color-accent) !important;
            }
            
            .border-accent {
                border-color: var(--color-accent) !important;
            }
            
            /* Estilos específicos para Multisite */
            " . (is_multisite() ? "
            .network-bar {
                background-color: var(--color-primary) !important;
            }
            
            .site-footer .network-sites a {
                color: var(--color-accent) !important;
            }
            " : "") . "
            
            /* Estilos de layout condicionales */
            " . (get_theme_mod('content_width', 'container') === 'full-width' ? "
            .site-content .container {
                max-width: 100% !important;
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
            " : "") . "
            
            /* Disposición de la barra lateral */
            " . (get_theme_mod('show_sidebar', false) && get_theme_mod('sidebar_position', 'right') === 'left' ? "
            @media (min-width: 768px) {
                .content-area {
                    order: 2;
                }
                
                .widget-area {
                    order: 1;
                }
            }
            " : "") . "
        </style>
        ";

        echo $custom_css;
    }

    /**
     * Cargar JavaScript para actualización en vivo en el personalizador
     */
    public function live_preview()
    {
        wp_enqueue_script(
            'wptbt-customizer-preview',
            WPTBT_URI . 'assets/amin/js/customizer-preview.js',
            array('jquery', 'customize-preview'),
            WPTBT_VERSION,
            true
        );
    }

    /**
     * Convertir color hex a rgb
     *
     * @param string $hex Código de color hexadecimal.
     * @return string Código RGB como cadena separada por comas.
     */
    private function hex_to_rgb($hex)
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

        return $r . ',' . $g . ',' . $b;
    }
}
