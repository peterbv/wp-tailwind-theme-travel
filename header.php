<?php

/**
 * Plantilla del encabezado para agencia de viajes
 * Optimizada para tour operadores y agencias de turismo
 */

// Obtener las opciones del customizer
$topbar_style = get_theme_mod('topbar_style', 'default');
$mobile_menu_style = get_theme_mod('mobile_menu_style', 'dropdown');
$cta_button_shape = get_theme_mod('cta_button_shape', 'rounded');
$cta_button_effect = get_theme_mod('cta_button_effect', 'wave');

// Definir clases basadas en las opciones
$topbar_class = $topbar_style !== 'default' ? ' topbar-style-' . $topbar_style : '';
$body_class = $mobile_menu_style === 'offcanvas' ? ' mobile-menu-style-offcanvas' : '';
$cta_button_class = ' cta-shape-' . $cta_button_shape . ' cta-effect-' . $cta_button_effect;

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php bloginfo('description'); ?>">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <div id="page" class="site max-w-full overflow-x-hidden">
        <a class="skip-link screen-reader-text" href="#content">
            <?php esc_html_e('Saltar al contenido', 'wp-tailwind-theme'); ?>
        </a>

        <?php if (get_theme_mod('show_topbar', true)) : ?>
            <div class="topbar py-2 text-sm border-b border-gray-100 bg-travel-secondary text-travel-primary transition-all duration-300<?php echo esc_attr($topbar_class); ?>">
                <div class="container mx-auto px-4 flex flex-wrap justify-between items-center">
                    <div class="topbar-left flex items-center gap-4">
                        <?php
                        // Obtener el email de contacto con lógica de fallback
                        $contact_email = get_theme_mod('contact_email', get_theme_mod('email', ''));

                        // Mostrar email si existe
                        if (!empty($contact_email)) :
                        ?>
                            <a href="mailto:<?php echo esc_attr($contact_email); ?>" class="inline-flex items-center hover:text-travel-accent transition duration-300" aria-label="Email de contacto">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span><?php echo esc_html($contact_email); ?></span>
                            </a>
                        <?php endif; ?>

                        <?php if ($contact_phone = get_theme_mod('contact_phone')) : ?>
                            <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $contact_phone)); ?>" class="inline-flex items-center hover:text-travel-accent transition duration-300" aria-label="Teléfono de contacto">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span><?php echo esc_html($contact_phone); ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="topbar-right flex items-center">
                        <div class="mr-6 hidden sm:flex items-center">
                            <?php if ($business_hours = get_theme_mod('business_hours', 'Lun-Vie: 8AM-6PM | Sáb: 9AM-5PM')) : ?>
                                <span class="inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-travel-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span><?php echo esc_html($business_hours); ?></span>
                                </span>
                            <?php endif; ?>
                        </div>
                        <?php
                        // Selector de idiomas
                        /*if (function_exists('wptbt_language_switcher')) {
                            wptbt_language_switcher();
                        } elseif (shortcode_exists('spa_language')) {
                            echo do_shortcode('[spa_language]');
                        }*/
                        ?>

                        <div class="social-icons flex gap-3">
                            <?php
                            // Array de redes sociales para simplificar el código
                            $social_networks = [
                                'facebook' => [
                                    'hover' => 'text-red-600',
                                    'svg' => '<path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />'
                                ],
                                'instagram' => [
                                    'hover' => 'text-travel-gold',
                                    'svg' => '<path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />'
                                ],
                                'twitter' => [
                                    'hover' => 'text-blue-400',
                                    'svg' => '<path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />'
                                ],
                                'youtube' => [
                                    'hover' => 'text-red-500',
                                    'svg' => '<path fill-rule="evenodd" d="M19.812 5.418c.861.23 1.538.907 1.768 1.768C21.998 8.746 22 12 22 12s0 3.255-.418 4.814a2.504 2.504 0 0 1-1.768 1.768c-1.56.419-7.814.419-7.814.419s-6.255 0-7.814-.419a2.505 2.505 0 0 1-1.768-1.768C2 15.255 2 12 2 12s0-3.255.417-4.814a2.507 2.507 0 0 1 1.768-1.768C5.744 5 11.998 5 11.998 5s6.255 0 7.814.418ZM15.194 12 10 15V9l5.194 3Z" clip-rule="evenodd" />'
                                ]
                            ];

                            foreach ($social_networks as $network => $data) :
                                $url = get_theme_mod("social_{$network}");
                                if (!empty($url)) :
                            ?>
                                    <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer"
                                        class="text-travel-primary hover:<?php echo esc_attr($data['hover']); ?> transition duration-300"
                                        aria-label="<?php echo esc_attr(ucfirst($network)); ?>">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <?php echo $data['svg']; ?>
                                        </svg>
                                    </a>
                            <?php
                                endif;
                            endforeach;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <header id="masthead" class="site-header bg-white sticky-header shadow-sm z-50">
            <div class="container mx-auto px-4">
                <div class="py-4 flex flex-wrap justify-between items-center">
                    <div class="site-branding flex items-center">
                        <?php if (has_custom_logo()) : ?>
                            <div class="site-logo">
                                <?php the_custom_logo(); ?>
                            </div>
                        <?php else : ?>
                            <div class="site-title">
                                <a href="<?php echo esc_url(home_url('/')); ?>" class="text-2xl fancy-text font-bold flex items-center" rel="home">
                                    <svg class="w-8 h-8 mr-2 text-travel-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <?php if ($logo_text = get_theme_mod('logo_text')): ?>
                                        <span class="text-travel-dark"><?php echo esc_html($logo_text); ?></span>
                                    <?php else: ?>
                                        <span class="text-travel-dark"><?php bloginfo('name'); ?></span>
                                    <?php endif; ?>

                                    <?php if ($tagline_text = get_theme_mod('tagline_text')): ?>
                                        <span class="ml-2 text-sm text-gray-500 hidden md:inline"><?php echo esc_html($tagline_text); ?></span>
                                    <?php endif; ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div><!-- .site-branding -->

                    <!-- Nuevo botón de búsqueda en desktop -->
                    <?php if (get_theme_mod('show_search', true)) : ?>
                        <div class="search-button hidden md:flex items-center mr-4">
                            <button id="search-toggle" class="p-1 text-travel-primary hover:text-travel-accent focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-travel-accent" aria-label="Buscar" aria-expanded="false">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </div>
                    <?php endif; ?>

                    <nav id="site-navigation" class="main-navigation hidden md:flex items-center">
                        <?php
                        wp_nav_menu(
                            array(
                                'theme_location' => 'primary',
                                'menu_id'        => 'primary-menu',
                                'container'      => false,
                                'menu_class'     => 'flex gap-8',
                                'fallback_cb'    => function () {
                                    echo '<ul class="flex"><li><a href="' . esc_url(admin_url('nav-menus.php')) . '" class="nav-menu-link">Añadir menú</a></li></ul>';
                                },
                                'walker'         => new WPTBT_Walker_Nav_Menu(),
                            )
                        );
                        ?>

                        <?php if (get_theme_mod('show_cta_button', true)) : ?>
                            <div class="ml-8">
                                <a href="<?php echo esc_url(get_theme_mod('cta_button_url', '#')); ?>"
                                    class="inline-block px-6 py-2 bg-travel-accent text-white font-medium rounded-sm hover:bg-opacity-90 transition duration-300 transform hover:-translate-y-0.5 hover:shadow-md<?php echo esc_attr($cta_button_class); ?>">
                                    <?php echo esc_html(get_theme_mod('cta_button_text', 'RESERVAR VIAJE')); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </nav><!-- #site-navigation -->

                    <div class="md:hidden flex items-center">
                        <!-- Botón de búsqueda en móvil -->
                        <?php if (get_theme_mod('show_search', true)) : ?>
                            <button id="mobile-search-toggle" class="p-2 text-travel-primary hover:text-travel-accent focus:outline-none mr-1" aria-label="Buscar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        <?php endif; ?>

                        <!-- Botón del menú móvil -->
                        <button id="mobile-menu-toggle" class="flex md:hidden p-2 text-travel-primary hover:text-travel-accent focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-travel-accent transition-all duration-300" aria-label="Menú" aria-expanded="false" aria-controls="mobile-menu">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Formulario de búsqueda desplegable -->
            <?php if (get_theme_mod('show_search', true)) : ?>
                <div id="search-modal" class="hidden search-modal absolute top-full left-0 right-0 bg-white shadow-lg border-t border-gray-100 py-4 px-4 z-50 opacity-0 transform -translate-y-4 transition-all duration-300">
                    <div class="container mx-auto">
                        <form role="search" method="get" class="search-form relative" action="<?php echo esc_url(home_url('/')); ?>">
                            <label class="screen-reader-text" for="search-input"><?php _e('Buscar:', 'wp-tailwind-theme'); ?></label>
                            <input type="search" id="search-input" class="search-field w-full p-3 pr-10 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-travel-accent" placeholder="<?php echo esc_attr_x('Buscar...', 'placeholder', 'wp-tailwind-theme'); ?>" value="<?php echo get_search_query(); ?>" name="s" />
                            <button type="submit" class="search-submit absolute right-3 top-1/2 transform -translate-y-1/2 text-travel-primary hover:text-travel-accent" aria-label="<?php echo esc_attr_x('Buscar', 'submit button', 'wp-tailwind-theme'); ?>">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Menú móvil optimizado -->
            <div id="mobile-menu" class="hidden opacity-0 md:hidden bg-white border-t border-gray-100 transition-all duration-300 transform origin-top">
                <div class="container mx-auto px-4 py-3">
                    <?php
                    if (has_nav_menu('primary')) {
                        wp_nav_menu(
                            array(
                                'theme_location' => 'primary',
                                'menu_id'        => 'mobile-menu-items',
                                'container'      => false,
                                'menu_class'     => 'mobile-menu-items space-y-3',
                                'fallback_cb'    => false,
                                'link_before'    => '<span class="menu-item-link transition-all duration-300">',
                                'link_after'     => '</span>',
                                'depth'          => 2,
                            )
                        );
                    } else {
                        echo '<ul class="mobile-menu-items"><li><a href="' . esc_url(admin_url('nav-menus.php')) . '" class="block py-2 text-gray-600 hover:text-travel-accent">Añadir menú</a></li></ul>';
                    }
                    ?>

                    <!-- Selector de idiomas en móvil -->
                    <?php if (get_theme_mod('show_language_switcher', false) && function_exists('pll_the_languages')) : ?>
                        <div class="mobile-languages mt-4 pb-3 border-t border-gray-100 pt-3">
                            <h4 class="text-sm font-semibold uppercase text-gray-500 mb-2"><?php _e('Idiomas', 'wp-tailwind-theme'); ?></h4>
                            <div class="flex flex-wrap gap-3">
                                <?php
                                pll_the_languages(array(
                                    'show_flags' => 1,
                                    'show_names' => 1,
                                    'display_names_as' => 'name',
                                    'hide_current' => 0,
                                    'echo' => 1
                                ));
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (get_theme_mod('show_cta_button', true)) : ?>
                        <div class="mt-6 pb-2">
                            <a id="book_now_botton" href="<?php echo esc_url(get_theme_mod('cta_button_url', '#')); ?>" class="block w-full px-6 py-3 bg-travel-accent text-white font-medium text-center rounded-sm hover:bg-opacity-90 transition duration-300 transform hover:-translate-y-0.5">
                                <?php echo esc_html(get_theme_mod('cta_button_text', 'RESERVAR VIAJE')); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </header><!-- #masthead -->

        <div id="content" class="site-content">