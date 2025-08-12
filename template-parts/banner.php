<?php

/**
 * Template part para mostrar el banner con olas, textos individuales y soporte para videos (incluyendo videos de WordPress)
 * Se puede incluir en single.php, page.php, etc.
 *
 * @package WP_Tailwind_Theme
 */

// Comprobar si la página actual tiene un banner habilitado
$show_banner = get_post_meta(get_the_ID(), 'wptbt_show_banner', true);

if ($show_banner) {
    // Obtener el modo del banner (global o individual)
    $banner_mode = get_post_meta(get_the_ID(), 'wptbt_banner_mode', true);
    if (empty($banner_mode)) {
        $banner_mode = 'global'; // valor por defecto
    }

    // Obtener datos globales (para el modo global o como respaldo)
    $global_title = get_post_meta(get_the_ID(), 'wptbt_banner_title', true);
    $global_subtitle = get_post_meta(get_the_ID(), 'wptbt_banner_subtitle', true);
    $global_button_text = get_post_meta(get_the_ID(), 'wptbt_banner_button_text', true);
    $global_button_url = get_post_meta(get_the_ID(), 'wptbt_banner_button_url', true);
    $global_rating_text = get_post_meta(get_the_ID(), 'wptbt_banner_rating_text', true);
    $global_show_rating = get_post_meta(get_the_ID(), 'wptbt_banner_show_rating', true);
    
    // Obtener configuración del formulario de reserva
    $show_booking = get_post_meta(get_the_ID(), 'wptbt_banner_show_booking', true);
    $booking_button_text = get_post_meta(get_the_ID(), 'wptbt_banner_booking_text', true) ?: '🎯 Reservar Ahora';
    // Obtener slides configurados
    $slides = get_post_meta(get_the_ID(), 'wptbt_banner_slides', true);

    // Si no hay slides configurados, usar el método antiguo (compatibilidad)
    if (empty($slides) || !is_array($slides)) {
        $slides = array();

        // Obtener imágenes del banner (IDs separados por comas) - modo antiguo
        $banner_images = get_post_meta(get_the_ID(), 'wptbt_banner_images', true);
        $image_ids = !empty($banner_images) ? explode(',', $banner_images) : array();

        foreach ($image_ids as $image_id) {
            $slides[] = array(
                'type' => 'image',
                'media_id' => $image_id,
                'title' => '',
                'subtitle' => '',
                'button_text' => '',
                'button_url' => ''
            );
        }
    }

    // Verificar si hay slides
    if (empty($slides)) {
        return; // No hay slides, no mostrar banner
    }

    // Clase para el contenedor basada en si hay múltiples slides y el tipo
    $has_multiple_slides = count($slides) > 1;

    // Verificar si hay algún video
    $has_video = false;
    foreach ($slides as $slide) {
        if ($slide['type'] === 'video') {
            $has_video = true;
            break;
        }
    }

    // Establecer la clase del contenedor
    $container_class = 'banner-single';
    if ($has_multiple_slides && !$has_video) {
        $container_class = 'banner-carousel';
    } elseif ($has_video) {
        $container_class = 'banner-with-video';
    }

    // ID único para el carousel
    $carousel_id = 'banner-carousel-' . get_the_ID();

    // Altura personalizable del banner
    $banner_height = 'min-h-[700px]';
?>

    <div class="page-banner relative overflow-hidden <?php echo esc_attr($container_class); ?> <?php echo esc_attr($banner_height); ?>" id="<?php echo esc_attr($carousel_id); ?>">
        <?php if (!empty($slides)) : ?>
            <div class="banner-slides h-full">
                <?php foreach ($slides as $index => $slide) :
                    // Establecer si es el slide activo inicialmente
                    $visibility_class = ($index === 0) ? 'active' : '';

                    // Determinar el tipo de slide (imagen o video)
                    $slide_type = isset($slide['type']) ? $slide['type'] : 'image';

                    // Obtener los textos para este slide (modo individual) o los globales
                    if ($banner_mode === 'individual') {
                        $slide_title = isset($slide['title']) ? $slide['title'] : '';
                        $slide_subtitle = isset($slide['subtitle']) ? $slide['subtitle'] : '';
                        $slide_button_text = isset($slide['button_text']) ? $slide['button_text'] : '';
                        $slide_button_url = isset($slide['button_url']) ? $slide['button_url'] : '';
                        $slide_rating_text = isset($slide['rating_text']) ? $slide['rating_text'] : 'RATED 5 STARS BY CLIENTS';
                        $slide_show_rating = isset($slide['show_rating']) ? $slide['show_rating'] : '1';
                    } else {
                        $slide_title = $global_title;
                        $slide_subtitle = $global_subtitle;
                        $slide_button_text = $global_button_text;
                        $slide_button_url = $global_button_url;
                        $slide_rating_text = $global_rating_text;
                        $slide_show_rating = $global_show_rating;
                    }

                    // Si es imagen
                    if ($slide_type === 'image') :
                        $media_id = isset($slide['media_id']) ? $slide['media_id'] : 0;
                        $image_url = wp_get_attachment_image_url($media_id, 'full');
                        if (!$image_url) continue;
                ?>
                        <div class="banner-slide banner-image-slide <?php echo esc_attr($visibility_class); ?>" data-slide-index="<?php echo esc_attr($index); ?>">
                            <div class="banner-background animate-fade-zoom-in absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('<?php echo esc_url($image_url); ?>');">
                                <div class="absolute inset-0 bg-gradient-to-b from-black/30 to-black/50"></div>
                            </div>

                            <div class="banner-slide-content container mx-auto px-4 py-16 md:py-20 lg:py-24 relative z-20 text-white flex flex-col items-center justify-center h-full text-center">
                                <?php if (!empty($slide_title)) : ?>
                                    <h1 class="banner-title text-4xl md:text-5xl lg:text-6xl fancy-text font-bold mb-6 leading-tight animate-slide-up opacity-0">
                                        <?php echo wp_kses_post($slide_title); ?>
                                    </h1>
                                <?php endif; ?>

                                <?php if (!empty($slide_subtitle)) : ?>
                                    <div class="banner-subtitle text-xl md:text-2xl mb-8 max-w-2xl animate-slide-up animation-delay-300 opacity-0">
                                        <?php echo wp_kses_post($slide_subtitle); ?>
                                    </div>
                                <?php endif; ?>

                                <div class="flex flex-col sm:flex-row gap-4 items-center justify-center">
                                    <?php if (!empty($slide_button_text) && !empty($slide_button_url)) : ?>
                                        <a href="<?php echo esc_url($slide_button_url); ?>" class="banner-button inline-block px-10 py-4 bg-spa-accent hover:bg-[#c4a346] text-white font-medium uppercase tracking-wider text-lg rounded-sm transition-all duration-300 transform hover:translate-y-[-2px] hover:shadow-xl shadow-md min-w-[200px] text-center animate-slide-up animation-delay-600 opacity-0">
                                            <?php echo esc_html($slide_button_text); ?>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($show_booking == '1') : ?>
                                        <button type="button" onclick="openBookingModal()" class="booking-button-minimal animate-slide-up animation-delay-700">
                                            <?php echo esc_html($booking_button_text); ?>
                                        </button>
                                    <?php endif; ?>
                                </div>

                                <?php if ($slide_show_rating == '1') : ?>
                                    <div class="rating-block mt-8 animate-slide-up animation-delay-900 opacity-0">
                                        <div class="flex justify-center text-spa-accent">
                                            <span class="text-2xl">★★★★★</span>
                                        </div>
                                        <p class="text-sm mt-2 font-medium tracking-wide"><?php echo esc_html($slide_rating_text); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php
                    // Si es video
                    elseif ($slide_type === 'video') :
                        $media_id = isset($slide['media_id']) ? $slide['media_id'] : 0;
                        $video_url = isset($slide['video_url']) ? $slide['video_url'] : '';

                        // Si hay un ID de medios, es un video de WordPress
                        if (!empty($media_id)) {
                            $video_url = wp_get_attachment_url($media_id);
                        }

                        if (empty($video_url)) continue;

                        // Detectar si es YouTube, Vimeo u otro
                        $video_type = 'external';
                        $video_id = '';

                        // YouTube
                        if (
                            preg_match('/youtube\.com\/watch\?v=([^&]+)/', $video_url, $matches) ||
                            preg_match('/youtu\.be\/([^&]+)/', $video_url, $matches)
                        ) {
                            $video_type = 'youtube';
                            $video_id = $matches[1];
                        }
                        // Vimeo
                        elseif (preg_match('/vimeo\.com\/([0-9]+)/', $video_url, $matches)) {
                            $video_type = 'vimeo';
                            $video_id = $matches[1];
                        }
                        // Video local
                        elseif (!empty($media_id) || preg_match('/\.(mp4|webm|ogg)$/i', $video_url)) {
                            $video_type = 'local';
                        }
                    ?>
                        <div class="banner-slide banner-video-slide <?php echo esc_attr($visibility_class); ?>" data-slide-index="<?php echo esc_attr($index); ?>">
                            <div class="banner-video-container absolute inset-0 w-full h-full">
                                <?php if ($video_type === 'youtube' && !empty($video_id)) : ?>
                                    <div class="video-wrapper relative w-full h-full">
                                        <iframe
                                            src="https://www.youtube.com/embed/<?php echo esc_attr($video_id); ?>?autoplay=1&mute=1&loop=1&controls=0&disablekb=1&rel=0&showinfo=0&modestbranding=1&iv_load_policy=3"
                                            width="100%"
                                            height="100%"
                                            frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen
                                            class="absolute inset-0 w-full h-full"></iframe>
                                    </div>
                                <?php elseif ($video_type === 'vimeo' && !empty($video_id)) : ?>
                                    <div class="video-wrapper relative w-full h-full">
                                        <iframe
                                            src="https://player.vimeo.com/video/<?php echo esc_attr($video_id); ?>?autoplay=1&loop=1&background=1"
                                            width="100%"
                                            height="100%"
                                            frameborder="0"
                                            allow="autoplay; fullscreen"
                                            allowfullscreen
                                            class="absolute inset-0 w-full h-full"></iframe>
                                    </div>
                                <?php elseif ($video_type === 'local') : ?>
                                    <video
                                        autoplay
                                        loop
                                        muted
                                        playsinline
                                        class="absolute inset-0 w-full h-full object-cover">
                                        <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
                                    </video>
                                <?php else : ?>
                                    <div class="fallback-background absolute inset-0 bg-gradient-to-r from-spa-primary/90 to-spa-sage/90">
                                        <div class="absolute inset-0 bg-spa-secondary/10"></div>
                                    </div>
                                <?php endif; ?>

                                <div class="absolute inset-0 bg-gradient-to-b from-black/30 to-black/50 z-10"></div>
                            </div>

                            <div class="banner-slide-content container mx-auto px-4 py-16 md:py-20 lg:py-24 relative z-20 text-white flex flex-col items-center justify-center h-full text-center">
                                <?php if (!empty($slide_title)) : ?>
                                    <h1 class="banner-title text-4xl md:text-5xl lg:text-6xl fancy-text font-bold mb-6 leading-tight animate-slide-up opacity-0">
                                        <?php echo wp_kses_post($slide_title); ?>
                                    </h1>
                                <?php endif; ?>

                                <?php if (!empty($slide_subtitle)) : ?>
                                    <div class="banner-subtitle text-xl md:text-2xl mb-8 max-w-2xl animate-slide-up animation-delay-300 opacity-0">
                                        <?php echo wp_kses_post($slide_subtitle); ?>
                                    </div>
                                <?php endif; ?>

                                <div class="flex flex-col sm:flex-row gap-4 items-center justify-center">
                                    <?php if (!empty($slide_button_text) && !empty($slide_button_url)) : ?>
                                        <a href="<?php echo esc_url($slide_button_url); ?>" class="banner-button inline-block px-10 py-4 bg-spa-accent hover:bg-[#c4a346] text-white font-medium uppercase tracking-wider text-lg rounded-sm transition-all duration-300 transform hover:translate-y-[-2px] hover:shadow-xl shadow-md min-w-[200px] text-center animate-slide-up animation-delay-600 opacity-0">
                                            <?php echo esc_html($slide_button_text); ?>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($show_booking == '1') : ?>
                                        <button type="button" onclick="openBookingModal()" class="booking-button-minimal animate-slide-up animation-delay-700">
                                            <?php echo esc_html($booking_button_text); ?>
                                        </button>
                                    <?php endif; ?>
                                </div>

                                <?php if ($slide_show_rating == '1') : ?>
                                    <div class="rating-block mt-8 animate-slide-up animation-delay-900 opacity-0">
                                        <div class="flex justify-center text-spa-accent">
                                            <span class="text-2xl">★★★★★</span>
                                        </div>
                                        <p class="text-sm mt-2 font-medium tracking-wide"><?php echo esc_html($slide_rating_text); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="banner-fallback absolute inset-0 bg-gradient-to-r from-spa-primary/90 to-spa-sage/90">
                <div class="absolute inset-0 bg-spa-secondary/10"></div>
            </div>
        <?php endif; ?>

        <?php if ($has_multiple_slides && !$has_video) : ?>
            <!-- Indicadores de navegación para el carrusel (solo para múltiples imágenes sin video) -->
            <div class="banner-nav absolute bottom-32 left-0 right-0 flex justify-center space-x-3 z-20">
                <?php foreach ($slides as $index => $slide) :
                    if ($slide['type'] !== 'image') continue;
                    $media_id = isset($slide['media_id']) ? $slide['media_id'] : 0;
                    if (!wp_get_attachment_image_url($media_id, 'thumbnail')) continue;
                ?>
                    <button type="button" class="banner-nav-dot w-3 h-3 rounded-full bg-white bg-opacity-50 hover:bg-opacity-100 transition duration-300 <?php echo $index === 0 ? 'active bg-opacity-100' : ''; ?>"
                        data-slide="<?php echo esc_attr($index); ?>" aria-label="<?php echo esc_attr(sprintf(__('Slide %d', 'wp-tailwind-blocks'), $index + 1)); ?>"></button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Borde ondulado optimizado para todos los dispositivos -->

    </div>

    <!-- Estilos CSS para el botón de reserva minimalista pero visible -->
    <style>
        .booking-button-minimal {
            display: inline-block;
            padding: 18px 36px;
            background: #D4B254;
            color: white;
            font-weight: 600;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            border: 2px solid #D4B254;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 220px;
            text-align: center;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(212, 178, 84, 0.3);
        }

        .booking-button-minimal:hover {
            background: transparent;
            color: #D4B254;
            border-color: #D4B254;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 178, 84, 0.4);
        }

        /* Efecto sutil de pulso cada 5 segundos */
        .booking-button-minimal {
            animation: gentle-attention 8s ease-in-out infinite;
        }

        @keyframes gentle-attention {
            0%, 95%, 100% { 
                transform: translateY(0) scale(1);
                box-shadow: 0 4px 15px rgba(212, 178, 84, 0.3);
            }
            97.5% { 
                transform: translateY(-1px) scale(1.02);
                box-shadow: 0 6px 20px rgba(212, 178, 84, 0.4);
            }
        }

        /* Responsivo */
        @media (max-width: 768px) {
            .booking-button-minimal {
                min-width: 200px;
                padding: 16px 28px;
                font-size: 16px;
                letter-spacing: 1px;
            }
        }

        @media (max-width: 480px) {
            .booking-button-minimal {
                min-width: 180px;
                padding: 14px 24px;
                font-size: 15px;
            }
        }
    </style>

    <?php if ($show_booking == '1') : ?>
        <?php 
        // Cargar el componente de formulario de reserva
        if (function_exists('wptbt_load_solid_component')) {
            wptbt_load_solid_component('booking-form');
        }
        ?>
        <!-- Modal de reserva -->
        <div id="booking-modal" class="booking-modal-overlay fixed inset-0 bg-black bg-opacity-75 z-[9999] hidden items-center justify-center p-4">
            <div class="booking-modal-content bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden relative transform scale-95 transition-all duration-300">
                <!-- Botón de cerrar -->
                <button type="button" onclick="closeBookingModal()" class="absolute top-4 right-4 z-10 w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                
                <!-- Contenido del modal -->
                <div class="booking-modal-body overflow-y-auto max-h-[90vh] p-6">
                    <div id="solid-booking-form-modal" class="w-full"></div>
                </div>
            </div>
        </div>

        <script>
        // Asegurar que las variables AJAX estén disponibles
        if (typeof window.wptbt_ajax === 'undefined') {
            window.wptbt_ajax = {
                url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
                nonce: '<?php echo wp_create_nonce('wptbt_booking_nonce'); ?>'
            };
        }

        function openBookingModal() {
            const modal = document.getElementById('booking-modal');
            const modalContent = modal.querySelector('.booking-modal-content');
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            // Animar la apertura
            setTimeout(() => {
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
            
            // Cargar el formulario de reserva si no está cargado
            loadBookingFormInModal();
            
            // Prevenir scroll del body
            document.body.style.overflow = 'hidden';
        }

        function closeBookingModal() {
            const modal = document.getElementById('booking-modal');
            const modalContent = modal.querySelector('.booking-modal-content');
            
            // Animar el cierre
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                // Restaurar scroll del body
                document.body.style.overflow = '';
            }, 300);
        }

        function loadBookingFormInModal() {
            const container = document.getElementById('solid-booking-form-modal');
            
            // Si ya está cargado, no hacer nada
            if (container.children.length > 0) {
                return;
            }
            
            // Cargar el componente de reserva
            if (typeof window.initializeSolidBookingForm === 'function') {
                window.initializeSolidBookingForm(container, {
                    modalMode: true,
                    onComplete: function() {
                        closeBookingModal();
                        // Mostrar mensaje de éxito
                        if (typeof showBookingSuccessMessage === 'function') {
                            showBookingSuccessMessage();
                        }
                    }
                });
            } else {
                container.innerHTML = '<div class="text-center p-8"><p class="text-gray-600">Cargando formulario de reserva...</p></div>';
                console.warn('SolidBookingForm not available');
            }
        }

        // Cerrar modal con tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('booking-modal');
                if (modal && !modal.classList.contains('hidden')) {
                    closeBookingModal();
                }
            }
        });

        // Cerrar modal haciendo clic fuera
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('booking-modal');
            if (e.target === modal) {
                closeBookingModal();
            }
        });
        </script>
    <?php endif; ?>

<?php
} // fin del if show_banner
?>