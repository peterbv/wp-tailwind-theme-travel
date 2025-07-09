<?php

/**
 * Implementación del selector de idiomas usando solo funciones API documentadas
 * Este código funciona con cualquier versión de MSLS
 */

/**
 * Crea un selector de idiomas personalizado para el tema de spa
 * Esta función debe añadirse a functions.php
 */
function wptbt_get_language_switcher()
{
    // Verificar si la función principal está disponible
    /* if (!function_exists('msls_blog_collection')) {
        return '';
    } */

    // Verificar si cualquiera de las funciones principales está disponible
    if (!function_exists('the_msls') && !function_exists('msls_output_switcher')) {
        return '';
    }

    // Obtener el idioma actual
    $current_locale = get_locale();

    // Lista de nombres de idiomas (puedes personalizarla según tus necesidades)
    $languages = [
        'es_ES' => 'Español',
        'en_US' => 'English',
        'fr_FR' => 'Français',
        'de_DE' => 'Deutsch',
        'it_IT' => 'Italiano',
        'pt_PT' => 'Português',
        'ar' => 'العربية',
        'zh_CN' => '简体中文',
        'ja' => '日本語',
        // Añade más idiomas según sea necesario
    ];

    // Obtener bandera del idioma actual
    $current_flag_url = '';
    if (function_exists('get_msls_flag_url')) {
        $current_flag_url = get_msls_flag_url($current_locale);
    }

    // Obtener nombre del idioma actual
    $current_language_name = isset($languages[$current_locale]) ?
        $languages[$current_locale] :
        $current_locale;

    // Iniciar salida
    ob_start();
?>
    <div class="language-switcher mr-4 hidden sm:block">
        <div class="relative group">
            <!-- Botón del idioma actual -->
            <button class="flex items-center text-spa-primary hover:text-spa-accent">
                <?php if (!empty($current_flag_url)) : ?>
                    <img src="<?php echo esc_url($current_flag_url); ?>" alt="<?php echo esc_attr($current_locale); ?>" class="w-4 h-4 mr-2" />
                <?php else : ?>
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                    </svg>
                <?php endif; ?>
                <span><?php echo esc_html($current_language_name); ?></span>
                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <!-- Lista desplegable de idiomas -->
            <div class="absolute right-0 mt-2 py-2 w-40 bg-white rounded-md shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                <?php
                // Esta función debe estar disponible si el plugin está activo
                if (function_exists('msls_output_switcher')) {
                    // Usar un buffer para capturar el HTML generado
                    ob_start();
                    msls_output_switcher();
                    $msls_output = ob_get_clean();

                    // Extraer los enlaces del HTML
                    preg_match_all('/<a\s+href="([^"]+)"[^>]*>.*?<img.+?alt="([^"]+)".*?<\/a>/s', $msls_output, $matches, PREG_SET_ORDER);

                    foreach ($matches as $match) {
                        $link_url = $match[1];
                        $link_locale = $match[2];

                        // Obtener bandera
                        $flag_url = '';
                        if (function_exists('get_msls_flag_url')) {
                            $flag_url = get_msls_flag_url($link_locale);
                        }

                        // Obtener nombre del idioma
                        $language_name = isset($languages[$link_locale]) ?
                            $languages[$link_locale] :
                            $link_locale;

                        // Mostrar enlace personalizado
                ?>
                        <a href="<?php echo esc_url($link_url); ?>" class="flex items-center px-4 py-2 text-gray-700 hover:bg-spa-secondary hover:text-spa-accent transition-colors duration-200">
                            <?php if (!empty($flag_url)) : ?>
                                <img src="<?php echo esc_url($flag_url); ?>" alt="<?php echo esc_attr($link_locale); ?>" class="w-4 h-4 mr-2" />
                            <?php endif; ?>
                            <?php echo esc_html($language_name); ?>
                        </a>
                <?php
                    }
                } elseif (function_exists('the_msls')) {
                    // Fallback con buffer de salida para reemplazar directamente
                    echo '<div class="px-4 py-2 custom-msls-wrapper">';

                    // Capturar la salida de the_msls en un buffer
                    ob_start();
                    the_msls([
                        'show_flags' => 1,
                        'show_current' => 1
                    ]);
                    $msls_output = ob_get_clean();

                    // Reemplazar los códigos de idioma con nombres
                    foreach ($languages as $code => $name) {
                        // Buscar varios patrones de aparición del código
                        $msls_output = str_replace('>' . $code . '<', '>' . $name . '<', $msls_output);
                        $msls_output = str_replace('">' . $code . '</a>', '">' . $name . '</a>', $msls_output);
                        $msls_output = str_replace(
                            'class="current-language-item">' . $code . '</span>',
                            'class="current-language-item">' . $name . '</span>',
                            $msls_output
                        );

                        // Búsqueda más agresiva usando expresiones regulares
                        $msls_output = preg_replace(
                            '/([^a-zA-Z0-9-])' . preg_quote($code) . '([^a-zA-Z0-9-])/i',
                            '$1' . $name . '$2',
                            $msls_output
                        );
                    }

                    // Mostrar el resultado modificado
                    echo $msls_output;
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
<?php

    // Obtener contenido del buffer
    return ob_get_clean();
}

/**
 * Función para mostrar el selector
 */
function wptbt_language_switcher()
{
    echo wptbt_get_language_switcher();
}

/**
 * Shortcode para usar en cualquier parte
 */
function wptbt_language_shortcode()
{
    return wptbt_get_language_switcher();
}
add_shortcode('spa_language', 'wptbt_language_shortcode');
