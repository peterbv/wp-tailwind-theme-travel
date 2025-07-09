</div><!-- #content -->

<footer id="colophon" class="site-footer relative">
    <!-- Onda decorativa superior -->
    <div class="absolute -top-15 md:-top-20 left-0 right-0 z-10 transform">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100" preserveAspectRatio="none" class="w-full h-16 md:h-20 fill-current">
            <path fill="#424242" d="M0,85.7L20,75.4C40,65.1,80,44.6,120,33.9C160,23.2,200,22.4,240,28.6C280,34.8,320,48.1,360,50C400,51.9,440,42.5,480,39.8C520,37.2,560,41.3,600,48.3C640,55.2,680,65,720,62.3C760,59.5,800,44.2,840,41.9C880,39.7,920,50.5,960,54.3C1000,58.2,1040,55.2,1080,58.2C1120,61.1,1160,69.9,1200,73.3C1240,76.8,1280,74.9,1320,70.9C1360,67,1400,61,1420,58L1440,55L1440,100L1420,100C1400,100,1360,100,1320,100C1280,100,1240,100,1200,100C1160,100,1120,100,1080,100C1040,100,1000,100,960,100C920,100,880,100,840,100C800,100,760,100,720,100C680,100,640,100,600,100C560,100,520,100,480,100C440,100,400,100,360,100C320,100,280,100,240,100C200,100,160,100,120,100C80,100,40,100,20,100L0,100Z"></path>
        </svg>
    </div>

    <div class="bg-[#424242] text-white pt-16 pb-12 relative z-0">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-10">
                <!-- Columna de información -->
                <div class="footer-widget">
                    <h3 class="text-xl fancy-text font-medium mb-6 text-[#D4B254]"><?php bloginfo('name'); ?></h3>
                    <p class="text-gray-300 mb-6 leading-relaxed"><?php bloginfo('description'); ?></p>
                    <?php if (is_multisite()) : ?>
                        <p class="text-sm text-gray-400">
                            <?php
                            printf(
                                esc_html__('Part of the network %s', 'wp-tailwind-blocks'),
                                get_network()->site_name
                            );
                            ?>
                        </p>
                    <?php endif; ?>

                    <!-- Redes Sociales (Movidas a primera columna) -->
                    <div class="mt-6">
                        <div class="flex space-x-3">
                            <?php if (get_theme_mod('social_facebook')) : ?>
                                <a href="<?php echo esc_url(get_theme_mod('social_facebook')); ?>" class="h-10 w-10 flex items-center justify-center rounded-full bg-gray-700 hover:bg-[#4F8A8B] text-white transition duration-300" target="_blank" rel="noopener noreferrer">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            <?php endif; ?>

                            <?php if (get_theme_mod('social_twitter')) : ?>
                                <a href="<?php echo esc_url(get_theme_mod('social_twitter')); ?>" class="h-10 w-10 flex items-center justify-center rounded-full bg-gray-700 hover:bg-[#4F8A8B] text-white transition duration-300" target="_blank" rel="noopener noreferrer">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                                    </svg>
                                </a>
                            <?php endif; ?>

                            <?php if (get_theme_mod('social_instagram')) : ?>
                                <a href="<?php echo esc_url(get_theme_mod('social_instagram')); ?>" class="h-10 w-10 flex items-center justify-center rounded-full bg-gray-700 hover:bg-[#D9ADB7] text-white transition duration-300" target="_blank" rel="noopener noreferrer">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            <?php endif; ?>

                            <?php if (get_theme_mod('social_linkedin')) : ?>
                                <a href="<?php echo esc_url(get_theme_mod('social_linkedin')); ?>" class="h-10 w-10 flex items-center justify-center rounded-full bg-gray-700 hover:bg-[#8BAB8D] text-white transition duration-300" target="_blank" rel="noopener noreferrer">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                                    </svg>
                                </a>
                            <?php endif; ?>

                            <?php if (get_theme_mod('social_youtube')) : ?>
                                <a href="<?php echo esc_url(get_theme_mod('social_youtube')); ?>" class="h-10 w-10 flex items-center justify-center rounded-full bg-gray-700 hover:bg-[#D4B254] text-white transition duration-300" target="_blank" rel="noopener noreferrer">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M19.812 5.418c.861.23 1.538.907 1.768 1.768C21.998 8.746 22 12 22 12s0 3.255-.418 4.814a2.504 2.504 0 0 1-1.768 1.768c-1.56.419-7.814.419-7.814.419s-6.255 0-7.814-.419a2.505 2.505 0 0 1-1.768-1.768C2 15.255 2 12 2 12s0-3.255.417-4.814a2.507 2.507 0 0 1 1.768-1.768C5.744 5 11.998 5 11.998 5s6.255 0 7.814.418ZM15.194 12 10 15V9l5.194 3Z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Columna de enlaces rápidos -->
                <div class="footer-widget">
                    <h3 class="text-xl fancy-text font-medium mb-6 text-[#D4B254]"><?php echo esc_html__('Quick Links', 'wp-tailwind-blocks'); ?></h3>
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'footer',
                            'menu_id'        => 'footer-menu',
                            'container'      => false,
                            'menu_class'     => 'footer-links',
                            'depth'          => 1,
                            'fallback_cb'    => '__return_false',
                            'items_wrap'     => '<ul class="space-y-3">%3$s</ul>',
                            'link_before'    => '<span class="text-gray-300 hover:text-white transition duration-300 flex items-center"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-[#8BAB8D]" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>',
                            'link_after'     => '</span>',
                        )
                    );
                    ?>
                </div>

                <!-- Columna de contacto -->
                <div class="footer-widget">
                    <h3 class="text-xl fancy-text font-medium mb-6 text-[#D4B254]"><?php echo esc_html__('Contact', 'wp-tailwind-blocks'); ?></h3>
                    <ul class="space-y-4">
                        <?php if (get_theme_mod('contact_email')) : ?>
                            <li class="text-gray-300 flex items-start">
                                <svg class="h-5 w-5 text-[#4F8A8B] mr-3 mt-0.5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                                <a href="mailto:<?php echo esc_attr(get_theme_mod('contact_email')); ?>" class="hover:text-white transition duration-300">
                                    <?php echo esc_html(get_theme_mod('contact_email')); ?>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (get_theme_mod('contact_phone')) : ?>
                            <li class="text-gray-300 flex items-start">
                                <svg class="h-5 w-5 text-[#8BAB8D] mr-3 mt-0.5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                </svg>
                                <a href="tel:<?php echo esc_attr(get_theme_mod('contact_phone')); ?>" class="hover:text-white transition duration-300">
                                    <?php echo esc_html(get_theme_mod('contact_phone')); ?>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (get_theme_mod('contact_address')) : ?>
                            <li class="text-gray-300 flex items-start">
                                <svg class="h-5 w-5 text-[#D9ADB7] mr-3 mt-0.5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                <span><?php echo esc_html(get_theme_mod('contact_address')); ?></span>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Columna de horario -->
                <div class="footer-widget">
                    <h3 class="text-xl fancy-text font-medium mb-6 text-[#D4B254]"><?php echo esc_html__('Business Hours', 'wp-tailwind-blocks'); ?></h3>
                    <ul class="space-y-3">
                        <li class="text-gray-300 flex items-start">
                            <svg class="h-5 w-5 text-[#D4B254] mr-3 mt-0.5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                            <span class="font-medium">Mon - Fri:</span>
                            <span class="ml-2">8:00 AM - 9:00 PM</span>
                        </li>
                        <li class="text-gray-300 flex items-start">
                            <svg class="h-5 w-5 text-[#D4B254] mr-3 mt-0.5 opacity-0 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                            <span class="font-medium">Saturday:</span>
                            <span class="ml-2">8:00 AM - 9:00 PM</span>
                        </li>
                        <li class="text-gray-300 flex items-start">
                            <svg class="h-5 w-5 text-[#D4B254] mr-3 mt-0.5 opacity-0 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                            <span class="font-medium">Sun:</span>
                            <span class="ml-2">8:00 AM - 9:00 PM</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Separador con elementos decorativos -->
            <div class="border-t border-gray-700 mt-12 pt-8 relative">
                <div class="absolute left-1/4 top-0 transform -translate-y-1/2">
                    <div class="w-16 h-[1px] bg-[#D4B254]"></div>
                </div>
                <div class="absolute left-1/2 top-0 transform -translate-x-1/2 -translate-y-1/2">
                    <div class="w-8 h-8 rounded-full border border-[#D4B254] flex items-center justify-center">
                        <div class="w-6 h-6 rounded-full bg-[#D4B254]"></div>
                    </div>
                </div>
                <div class="absolute right-1/4 top-0 transform -translate-y-1/2">
                    <div class="w-16 h-[1px] bg-[#D4B254]"></div>
                </div>

                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="text-gray-400 text-sm mb-4 md:mb-0">
                        &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>.
                        <?php echo esc_html__('Todos los derechos reservados.', 'wp-tailwind-blocks'); ?>
                    </div>

                    <?php
                    // Lista de sitios multisite si estamos en el sitio principal
                    if (is_multisite() && is_main_site()) {
                        $sites = get_sites(array('public' => 1));
                        if (count($sites) > 1) {
                            echo '<div class="text-gray-400 text-sm">';
                            echo '<span class="mr-2">' . esc_html__('Nuestros sitios:', 'wp-tailwind-blocks') . '</span>';

                            $site_links = array();
                            foreach ($sites as $site) {
                                $site_details = get_blog_details($site->blog_id);
                                if ($site->blog_id != get_main_site_id()) {
                                    $site_links[] = '<a href="' . esc_url(get_site_url($site->blog_id)) . '" class="text-gray-300 hover:text-white transition duration-300">' . esc_html($site_details->blogname) . '</a>';
                                }
                            }

                            echo implode(' | ', $site_links);
                            echo '</div>';
                        }
                    }
                    ?>
                </div>

                <?php if (is_multisite() && !is_main_site() && get_theme_mod('show_switch_link', true)) : ?>
                    <div class="mt-4 text-center text-xs text-gray-500">
                        <a href="<?php echo esc_url(get_site_url(get_main_site_id())); ?>" class="text-gray-400 hover:text-white transition duration-300">
                            <?php echo esc_html__('Volver al sitio principal', 'wp-tailwind-blocks'); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</footer><!-- #colophon -->

<?php if (get_theme_mod('enable_back_to_top', true)) : ?>
    <button id="back-to-top" class="fixed z-50 bottom-8 right-8 bg-[#D4B254] text-white rounded-full p-3 hover:bg-[#c4a346] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#D4B254] transform transition-transform duration-300 scale-0">
        <span class="sr-only"><?php echo esc_html__('Volver arriba', 'wp-tailwind-blocks'); ?></span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>
<?php endif; ?>

</div><!-- #page -->

<?php wp_footer(); ?>

<!-- Botones flotantes con paleta de spa -->
<?php
// Solo mostrar si están habilitados
if (get_theme_mod('show_floating_buttons', true)) :
    // Posición de los botones (izquierda o derecha)
    $position_class = (get_theme_mod('floating_buttons_position', 'right') === 'right')
        ? 'right-5'
        : 'left-5';

    // Si se muestra el texto en hover
    $show_text = get_theme_mod('floating_buttons_show_text', true);

    // Botones configurados
    $phone_enabled = get_theme_mod('floating_phone_enabled', true);
    $phone_number = get_theme_mod('floating_phone_number', '');
    $phone_text = get_theme_mod('floating_phone_text', 'Llámanos');

    $whatsapp_enabled = get_theme_mod('floating_whatsapp_enabled', true);
    $whatsapp_number = get_theme_mod('floating_whatsapp_number', '');
    $whatsapp_text = get_theme_mod('floating_whatsapp_text', 'WhatsApp');

    $maps_enabled = get_theme_mod('floating_maps_enabled', true);
    $maps_url = get_theme_mod('floating_maps_url', '');
    $maps_text = get_theme_mod('floating_maps_text', 'Ubicación');
?>

    <div class="floating-buttons fixed z-50 bottom-5 <?php echo esc_attr($position_class); ?> flex flex-col gap-3">

        <?php if ($phone_enabled && !empty($phone_number)) : ?>
            <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone_number)); ?>"
                class="floating-button bg-blue-500 text-white rounded-full w-12 h-12 flex items-center justify-center shadow-lg transform transition-transform hover:scale-110"
                aria-label="<?php echo esc_attr($phone_text); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                <?php if ($show_text) : ?>
                    <span class="floating-button-text hidden md:inline opacity-0 absolute <?php echo get_theme_mod('floating_buttons_position', 'right') === 'right' ? 'right-full mr-2' : 'left-full ml-2'; ?> transition-opacity whitespace-nowrap bg-black bg-opacity-80 text-white text-sm py-1 px-3 rounded-md group-hover:opacity-100">
                        <?php echo esc_html($phone_text); ?>
                    </span>
                <?php endif; ?>
            </a>
        <?php endif; ?>

        <?php if ($whatsapp_enabled && !empty($whatsapp_number)) :
            $whatsapp_url = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $whatsapp_number);
        ?>
            <a href="<?php echo esc_url($whatsapp_url); ?>"
                target="_blank"
                rel="noopener noreferrer"
                class="floating-button bg-green-500 text-white rounded-full w-12 h-12 flex items-center justify-center shadow-lg transform transition-transform hover:scale-110"
                aria-label="<?php echo esc_attr($whatsapp_text); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                </svg>
                <?php if ($show_text) : ?>
                    <span class="floating-button-text hidden md:inline opacity-0 absolute <?php echo get_theme_mod('floating_buttons_position', 'right') === 'right' ? 'right-full mr-2' : 'left-full ml-2'; ?> transition-opacity whitespace-nowrap bg-black bg-opacity-80 text-white text-sm py-1 px-3 rounded-md group-hover:opacity-100">
                        <?php echo esc_html($whatsapp_text); ?>
                    </span>
                <?php endif; ?>
            </a>
        <?php endif; ?>

        <?php if ($maps_enabled && !empty($maps_url)) : ?>
            <a href="<?php echo esc_url($maps_url); ?>"
                target="_blank"
                rel="noopener noreferrer"
                class="floating-button bg-red-500 text-white rounded-full w-12 h-12 flex items-center justify-center shadow-lg transform transition-transform hover:scale-110"
                aria-label="<?php echo esc_attr($maps_text); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <?php if ($show_text) : ?>
                    <span class="floating-button-text hidden md:inline opacity-0 absolute <?php echo get_theme_mod('floating_buttons_position', 'right') === 'right' ? 'right-full mr-2' : 'left-full ml-2'; ?> transition-opacity whitespace-nowrap bg-black bg-opacity-80 text-white text-sm py-1 px-3 rounded-md group-hover:opacity-100">
                        <?php echo esc_html($maps_text); ?>
                    </span>
                <?php endif; ?>
            </a>
        <?php endif; ?>
    </div>

<?php endif; ?>

<script>
    // Mejoras JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        // Botón "Volver arriba"
        const backToTopButton = document.getElementById('back-to-top');

        if (backToTopButton) {
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    backToTopButton.classList.remove('scale-0');
                    backToTopButton.classList.add('scale-100');
                } else {
                    backToTopButton.classList.remove('scale-100');
                    backToTopButton.classList.add('scale-0');
                }
            });

            backToTopButton.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }

        // Mejorar la interacción con los botones flotantes
        const floatingButtons = document.querySelectorAll('.floating-button');

        floatingButtons.forEach(button => {
            button.addEventListener('mouseenter', function() {
                const tooltip = this.querySelector('.floating-button-text');
                if (tooltip) {
                    tooltip.classList.remove('opacity-0');
                    tooltip.classList.add('opacity-100');
                }
            });

            button.addEventListener('mouseleave', function() {
                const tooltip = this.querySelector('.floating-button-text');
                if (tooltip) {
                    tooltip.classList.remove('opacity-100');
                    tooltip.classList.add('opacity-0');
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Manejo de selección de duración
        const durationOptions = document.querySelectorAll('input[name="duration_option"]');
        const durationInput = document.querySelector('.selected-duration');

        durationOptions.forEach(option => {
            option.addEventListener('change', function() {
                // Actualizar el valor del campo oculto
                if (this.checked) {
                    durationInput.value = this.value;

                    // Actualizar estilos visuales
                    document.querySelectorAll('.duration-option label').forEach(label => {
                        label.classList.remove('ring-2', 'ring-spa-accent');
                        label.classList.add('border-gray-700');
                    });

                    this.nextElementSibling.classList.add('ring-2', 'ring-spa-accent');
                    this.nextElementSibling.classList.remove('border-gray-700');
                }
            });
        });
    });
</script>

</body>

</html>