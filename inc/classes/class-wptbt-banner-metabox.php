<?php

/**
 * Metabox para configurar el banner personalizado por página
 * Incluir este archivo en functions.php
 *
 * @package WP_Tailwind_Theme
 */

/**
 * Clase para el metabox del banner personalizable
 */
class WPTBT_Banner_Metabox
{
    /**
     * Inicializar el metabox
     */
    public function init()
    {
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        add_action('save_post', array($this, 'save_meta_box'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Registrar el metabox
     */
    public function add_meta_box()
    {
        add_meta_box(
            'wptbt_banner_metabox',
            __('Banner Personalizado', 'wp-tailwind-theme'),
            array($this, 'render_meta_box'),
            array('page', 'post'), // Tipos de post donde mostrar el metabox
            'normal',
            'high'
        );
    }

    /**
     * Cargar scripts y estilos para el metabox
     *
     * @param string $hook Página actual del admin
     */
    public function enqueue_scripts($hook)
    {
        if (!in_array($hook, array('post.php', 'post-new.php'))) {
            return;
        }

        // Cargar los estilos del media uploader de WordPress
        wp_enqueue_media();

        // Cargar estilos para el metabox
        wp_enqueue_style(
            'wptbt-banner-admin-styles',
            WPTBT_URI . 'assets/admin/css/banner-style.css',
            array(),
            WPTBT_VERSION
        );

        // Cargar tu script personalizado
        wp_enqueue_script(
            'wptbt-banner-admin',
            WPTBT_URI . 'assets/admin/js/banner-admin.js',
            array('jquery', 'jquery-ui-sortable'),
            WPTBT_VERSION,
            true
        );
    }

    /**
     * Renderizar el contenido del metabox
     *
     * @param WP_Post $post Objeto post actual
     */
    public function render_meta_box($post)
    {
        // Añadir nonce para verificación
        wp_nonce_field('wptbt_banner_metabox', 'wptbt_banner_metabox_nonce');

        // Obtener los valores guardados
        $show_banner = get_post_meta($post->ID, 'wptbt_show_banner', true);

        // Obtener configuración global para todos los slides (texto único)
        $global_banner_title = get_post_meta($post->ID, 'wptbt_banner_title', true);
        $global_banner_subtitle = get_post_meta($post->ID, 'wptbt_banner_subtitle', true);
        $global_banner_button_text = get_post_meta($post->ID, 'wptbt_banner_button_text', true);
        $global_banner_button_url = get_post_meta($post->ID, 'wptbt_banner_button_url', true);

        // Obtener la lista de slides (imágenes/videos con textos independientes)
        $banner_slides = get_post_meta($post->ID, 'wptbt_banner_slides', true);
        if (empty($banner_slides) || !is_array($banner_slides)) {
            $banner_slides = array();
        }

        // Obtener el modo de banner (único o slide específico)
        $banner_mode = get_post_meta($post->ID, 'wptbt_banner_mode', true);
        if (empty($banner_mode)) {
            $banner_mode = 'global'; // global o individual
        }

        // Obtener la lista antigua de IDs de imágenes (para compatibilidad con versiones anteriores)
        $banner_images = get_post_meta($post->ID, 'wptbt_banner_images', true);
        // Convertir la lista de IDs en un array
        $image_ids = !empty($banner_images) ? explode(',', $banner_images) : array();

        // Si hay imágenes antiguas pero no hay slides, convertir las imágenes a slides
        if (!empty($image_ids) && empty($banner_slides)) {
            foreach ($image_ids as $image_id) {
                $banner_slides[] = array(
                    'type' => 'image',
                    'media_id' => $image_id,
                    'title' => '',
                    'subtitle' => '',
                    'button_text' => '',
                    'button_url' => ''
                );
            }
        }
?>

        <div class="wptbt-banner-metabox">
            <p>
                <label>
                    <input type="checkbox" name="wptbt_show_banner" value="1" <?php checked($show_banner, '1'); ?> />
                    <?php _e('Mostrar banner en esta página', 'wp-tailwind-theme'); ?>
                </label>
            </p>

            <div class="wptbt-banner-options" style="<?php echo empty($show_banner) ? 'display: none;' : ''; ?>">
                <div class="wptbt-banner-mode">
                    <p class="banner-mode-selector">
                        <label style="margin-right: 15px;">
                            <input type="radio" name="wptbt_banner_mode" value="global" <?php checked($banner_mode, 'global'); ?> />
                            <?php _e('Usar texto único para todas las imágenes/videos', 'wp-tailwind-theme'); ?>
                        </label>
                        <label>
                            <input type="radio" name="wptbt_banner_mode" value="individual" <?php checked($banner_mode, 'individual'); ?> />
                            <?php _e('Configurar texto individual para cada imagen/video', 'wp-tailwind-theme'); ?>
                        </label>
                    </p>
                </div>

                <!-- Modo Global: Un solo texto para todos los slides -->
                <div class="wptbt-global-content" style="<?php echo $banner_mode === 'individual' ? 'display: none;' : ''; ?>">
                    <p>
                        <label for="wptbt_banner_title" style="display: block; font-weight: bold; margin-bottom: 5px;">
                            <?php _e('Título del banner:', 'wp-tailwind-theme'); ?>
                        </label>
                        <input type="text" id="wptbt_banner_title" name="wptbt_banner_title" value="<?php echo esc_attr($global_banner_title); ?>" style="width: 100%;" />
                    </p>

                    <p>
                        <label for="wptbt_banner_subtitle" style="display: block; font-weight: bold; margin-bottom: 5px;">
                            <?php _e('Subtítulo:', 'wp-tailwind-theme'); ?>
                        </label>
                        <textarea id="wptbt_banner_subtitle" name="wptbt_banner_subtitle" rows="3" style="width: 100%;"><?php echo esc_textarea($global_banner_subtitle); ?></textarea>
                    </p>

                    <div class="banner-button-section" style="display: flex; gap: 10px; margin-bottom: 15px;">
                        <div style="flex: 1;">
                            <label for="wptbt_banner_button_text" style="display: block; font-weight: bold; margin-bottom: 5px;">
                                <?php _e('Texto del botón:', 'wp-tailwind-theme'); ?>
                            </label>
                            <input type="text" id="wptbt_banner_button_text" name="wptbt_banner_button_text" value="<?php echo esc_attr($global_banner_button_text); ?>" style="width: 100%;" />
                        </div>

                        <div style="flex: 1;">
                            <label for="wptbt_banner_button_url" style="display: block; font-weight: bold; margin-bottom: 5px;">
                                <?php _e('URL del botón:', 'wp-tailwind-theme'); ?>
                            </label>
                            <input type="url" id="wptbt_banner_button_url" name="wptbt_banner_button_url" value="<?php echo esc_url($global_banner_button_url); ?>" style="width: 100%;" />
                        </div>
                    </div>
                    <div class="banner-rating-section" style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #eee;">
                        <h4 style="margin-bottom: 10px; font-weight: bold;"><?php _e('Configuración de calificaciones:', 'wp-tailwind-theme'); ?></h4>

                        <p>
                            <label>
                                <input type="checkbox" name="wptbt_banner_show_rating" value="1" <?php checked(get_post_meta($post->ID, 'wptbt_banner_show_rating', true), '1'); ?> />
                                <?php _e('Mostrar bloque de calificaciones', 'wp-tailwind-theme'); ?>
                            </label>
                        </p>

                        <p>
                            <label for="wptbt_banner_rating_text" style="display: block; font-weight: bold; margin-bottom: 5px;">
                                <?php _e('Texto de calificación:', 'wp-tailwind-theme'); ?>
                            </label>
                            <input type="text" id="wptbt_banner_rating_text" name="wptbt_banner_rating_text"
                                value="<?php echo esc_attr(get_post_meta($post->ID, 'wptbt_banner_rating_text', true) ?: 'RATED 5 STARS BY CLIENTS'); ?>"
                                style="width: 100%;" />
                        </p>
                    </div>
                </div>


                <!-- Sección de slides (imágenes o videos) -->
                <div class="banner-slides-section">
                    <div class="banner-slides-header">
                        <h3><?php _e('Slides del banner (Imágenes o Videos):', 'wp-tailwind-theme'); ?></h3>
                        <div class="banner-actions">
                            <button type="button" class="button" id="wptbt_add_image">
                                <?php _e('Añadir Imagen', 'wp-tailwind-theme'); ?>
                            </button>
                            <button type="button" class="button" id="wptbt_add_video_wp">
                                <?php _e('Añadir Video de Galería', 'wp-tailwind-theme'); ?>
                            </button>
                            <button type="button" class="button" id="wptbt_add_video_url">
                                <?php _e('Añadir Video por URL', 'wp-tailwind-theme'); ?>
                            </button>
                        </div>
                    </div>

                    <div class="banner-slides-container" id="banner_slides_container">
                        <?php if (empty($banner_slides)) : ?>
                            <div class="banner-no-slides">
                                <p><?php _e('No hay slides configurados. Añade una imagen o video.', 'wp-tailwind-theme'); ?></p>
                            </div>
                        <?php else : ?>
                            <?php foreach ($banner_slides as $index => $slide) :
                                // Obtener información dependiendo del tipo
                                $slide_type = isset($slide['type']) ? $slide['type'] : 'image';
                                $media_id = isset($slide['media_id']) ? $slide['media_id'] : '';
                                $video_url = isset($slide['video_url']) ? $slide['video_url'] : '';
                                $slide_title = isset($slide['title']) ? $slide['title'] : '';
                                $slide_subtitle = isset($slide['subtitle']) ? $slide['subtitle'] : '';
                                $slide_button_text = isset($slide['button_text']) ? $slide['button_text'] : '';
                                $slide_button_url = isset($slide['button_url']) ? $slide['button_url'] : '';

                                // Preparar vista previa
                                $preview_html = '';
                                if ($slide_type === 'image' && !empty($media_id)) {
                                    $image_url = wp_get_attachment_image_url($media_id, 'thumbnail');
                                    if ($image_url) {
                                        $preview_html = '<img src="' . esc_url($image_url) . '" style="width: 100%; height: auto; display: block;" />';
                                    }
                                } elseif ($slide_type === 'video' && !empty($media_id)) {
                                    // Video de WordPress
                                    $video_url = wp_get_attachment_url($media_id);
                                    $preview_html = '<div class="video-preview"><span class="dashicons dashicons-video-alt3"></span>' . esc_html(basename($video_url)) . '</div>';
                                } elseif ($slide_type === 'video' && !empty($video_url)) {
                                    // Video externo por URL
                                    $preview_html = '<div class="video-preview"><span class="dashicons dashicons-video-alt3"></span>' . esc_html(basename($video_url)) . '</div>';
                                }
                            ?>
                                <div class="banner-slide-item" data-index="<?php echo esc_attr($index); ?>" data-type="<?php echo esc_attr($slide_type); ?>">
                                    <div class="slide-header">
                                        <div class="slide-preview">
                                            <?php echo $preview_html; ?>
                                        </div>
                                        <div class="slide-actions">
                                            <button type="button" class="button slide-move">
                                                <span class="dashicons dashicons-move"></span>
                                            </button>
                                            <button type="button" class="button slide-edit">
                                                <span class="dashicons dashicons-edit"></span>
                                            </button>
                                            <button type="button" class="button slide-remove">
                                                <span class="dashicons dashicons-trash"></span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="slide-content" style="<?php echo $banner_mode === 'global' ? 'display: none;' : ''; ?>">
                                        <!-- Campos para cada slide individual -->
                                        <input type="hidden" name="wptbt_banner_slides[<?php echo $index; ?>][type]" value="<?php echo esc_attr($slide_type); ?>" />

                                        <?php if ($slide_type === 'image' || ($slide_type === 'video' && !empty($media_id))) : ?>
                                            <input type="hidden" name="wptbt_banner_slides[<?php echo $index; ?>][media_id]" value="<?php echo esc_attr($media_id); ?>" />
                                        <?php endif; ?>

                                        <?php if ($slide_type === 'video' && !empty($video_url) && empty($media_id)) : ?>
                                            <p>
                                                <label style="display: block; font-weight: bold; margin-bottom: 5px;">
                                                    <?php _e('URL del Video:', 'wp-tailwind-theme'); ?>
                                                </label>
                                                <input type="url" name="wptbt_banner_slides[<?php echo $index; ?>][video_url]" value="<?php echo esc_url($video_url); ?>" style="width: 100%;" />
                                            </p>
                                        <?php endif; ?>

                                        <p>
                                            <label style="display: block; font-weight: bold; margin-bottom: 5px;">
                                                <?php _e('Título:', 'wp-tailwind-theme'); ?>
                                            </label>
                                            <input type="text" name="wptbt_banner_slides[<?php echo $index; ?>][title]" value="<?php echo esc_attr($slide_title); ?>" style="width: 100%;" />
                                        </p>

                                        <p>
                                            <label style="display: block; font-weight: bold; margin-bottom: 5px;">
                                                <?php _e('Subtítulo:', 'wp-tailwind-theme'); ?>
                                            </label>
                                            <textarea name="wptbt_banner_slides[<?php echo $index; ?>][subtitle]" rows="2" style="width: 100%;"><?php echo esc_textarea($slide_subtitle); ?></textarea>
                                        </p>

                                        <div class="slide-button-section" style="display: flex; gap: 10px; margin-bottom: 15px;">
                                            <div style="flex: 1;">
                                                <label style="display: block; font-weight: bold; margin-bottom: 5px;">
                                                    <?php _e('Texto del botón:', 'wp-tailwind-theme'); ?>
                                                </label>
                                                <input type="text" name="wptbt_banner_slides[<?php echo $index; ?>][button_text]" value="<?php echo esc_attr($slide_button_text); ?>" style="width: 100%;" />
                                            </div>
                                            <div style="flex: 1;">
                                                <label style="display: block; font-weight: bold; margin-bottom: 5px;">
                                                    <?php _e('URL del botón:', 'wp-tailwind-theme'); ?>
                                                </label>
                                                <input type="url" name="wptbt_banner_slides[<?php echo $index; ?>][button_url]" value="<?php echo esc_url($slide_button_url); ?>" style="width: 100%;" />
                                            </div>
                                        </div>
                                        <div class="slide-rating-section" style="margin-top: 15px; padding-top: 10px; border-top: 1px solid #eee;">
                                            <p>
                                                <label>
                                                    <input type="checkbox" name="wptbt_banner_slides[{{index}}][show_rating]" value="1" checked />
                                                    <?php _e('Mostrar calificaciones', 'wp-tailwind-theme'); ?>
                                                </label>
                                            </p>

                                            <p>
                                                <label style="display: block; font-weight: bold; margin-bottom: 5px;">
                                                    <?php _e('Texto de calificación:', 'wp-tailwind-theme'); ?>
                                                </label>
                                                <input type="text" name="wptbt_banner_slides[{{index}}][rating_text]"
                                                    value="RATED 5 STARS BY CLIENTS" style="width: 100%;" />
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Template para nuevos slides -->
                    <script type="text/template" id="slide_template_image">
                        <div class="banner-slide-item" data-index="{{index}}" data-type="image">
                            <div class="slide-header">
                                <div class=font-"slide-preview">
                                    <img src="{{image_url}}" style="width: 100%; height: auto; display: block;" />
                                </div>
                                <div class="slide-actions">
                                    <button type="button" class="button slide-move">
                                        <span class="dashicons dashicons-move"></span>
                                    </button>
                                    <button type="button" class="button slide-edit">
                                        <span class="dashicons dashicons-edit"></span>
                                    </button>
                                    <button type="button" class="button slide-remove">
                                        <span class="dashicons dashicons-trash"></span>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="slide-content" style="{{content_style}}">
                                <input type="hidden" name="wptbt_banner_slides[{{index}}][type]" value="image" />
                                <input type="hidden" name="wptbt_banner_slides[{{index}}][media_id]" value="{{media_id}}" />
                                
                                <p>
                                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">
                                        <?php _e('Título:', 'wp-tailwind-theme'); ?>
                                    </label>
                                    <input type="text" name="wptbt_banner_slides[{{index}}][title]" value="" style="width: 100%;" />
                                </p>
                                
                                <p>
                                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">
                                        <?php _e('Subtítulo:', 'wp-tailwind-theme'); ?>
                                    </label>
                                    <textarea name="wptbt_banner_slides[{{index}}][subtitle]" rows="2" style="width: 100%;"></textarea>
                                </p>
                                
                                <div class="slide-button-section" style="display: flex; gap: 10px; margin-bottom: 15px;">
                                    <div style="flex: 1;">
                                        <label style="display: block; font-weight: bold; margin-bottom: 5px;">
                                            <?php _e('Texto del botón:', 'wp-tailwind-theme'); ?>
                                        </label>
                                        <input type="text" name="wptbt_banner_slides[{{index}}][button_text]" value="" style="width: 100%;" />
                                    </div>
                                    <div style="flex: 1;">
                                        <label style="display: block; font-weight: bold; margin-bottom: 5px;">
                                            <?php _e('URL del botón:', 'wp-tailwind-theme'); ?>
                                        </label>
                                        <input type="url" name="wptbt_banner_slides[{{index}}][button_url]" value="" style="width: 100%;" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </script>

                    <script type="text/template" id="slide_template_video_url">
                        <div class="banner-slide-item" data-index="{{index}}" data-type="video">
                            <div class="slide-header">
                                <div class="slide-preview">
                                    <div class="video-preview"><span class="dashicons dashicons-video-alt3"></span>{{video_url}}</div>
                                </div>
                                <div class="slide-actions">
                                    <button type="button" class="button slide-move">
                                        <span class="dashicons dashicons-move"></span>
                                    </button>
                                    <button type="button" class="button slide-edit">
                                        <span class="dashicons dashicons-edit"></span>
                                    </button>
                                    <button type="button" class="button slide-remove">
                                        <span class="dashicons dashicons-trash"></span>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="slide-content" style="{{content_style}}">
                                <input type="hidden" name="wptbt_banner_slides[{{index}}][type]" value="video" />
                                
                                <p>
                                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">
                                        <?php _e('URL del Video:', 'wp-tailwind-theme'); ?>
                                    </label>
                                    <input type="url" name="wptbt_banner_slides[{{index}}][video_url]" value="{{video_url}}" style="width: 100%;" />
                                </p>
                                
                                <p>
                                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">
                                        <?php _e('Título:', 'wp-tailwind-theme'); ?>
                                    </label>
                                    <input type="text" name="wptbt_banner_slides[{{index}}][title]" value="" style="width: 100%;" />
                                </p>
                                
                                <p>
                                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">
                                        <?php _e('Subtítulo:', 'wp-tailwind-theme'); ?>
                                    </label>
                                    <textarea name="wptbt_banner_slides[{{index}}][subtitle]" rows="2" style="width: 100%;"></textarea>
                                </p>
                                font-
                                <div class="slide-button-section" style="display: flex; gap: 10px; margin-bottom: 15px;">
                                    <div style="flex: 1;">
                                        <label style="display: block; font-weight: bold; margin-bottom: 5px;">
                                            <?php _e('Texto del botón:', 'wp-tailwind-theme'); ?>
                                        </label>
                                        <input type="text" name="wptbt_banner_slides[{{index}}][button_text]" value="" style="width: 100%;" />
                                    </div>
                                    <div style="flex: 1;">
                                        <label style="display: block; font-weight: bold; margin-bottom: 5px;">
                                            <?php _e('URL del botón:', 'wp-tailwind-theme'); ?>
                                        </label>
                                        <input type="url" name="wptbt_banner_slides[{{index}}][button_url]" value="" style="width: 100%;" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </script>

                    <script type="text/template" id="slide_template_video_wp">
                        <div class="banner-slide-item" data-index="{{index}}" data-type="video">
                            <div class="slide-header">
                                <div class="slide-preview">
                                    <div class="video-preview"><span class="dashicons dashicons-video-alt3"></span>{{video_filename}}</div>
                                </div>
                                <div class="slide-actions">
                                    <button type="button" class="button slide-move">
                                        <span class="dashicons dashicons-move"></span>
                                    </button>
                                    <button type="button" class="button slide-edit">
                                        <span class="dashicons dashicons-edit"></span>
                                    </button>
                                    <button type="button" class="button slide-remove">
                                        <span class="dashicons dashicons-trash"></span>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="slide-content" style="{{content_style}}">
                                <input type="hidden" name="wptbt_banner_slides[{{index}}][type]" value="video" />
                                <input type="hidden" name="wptbt_banner_slides[{{index}}][media_id]" value="{{media_id}}" />
                                
                                <p>
                                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">
                                        <?php _e('Título:', 'wp-tailwind-theme'); ?>
                                    </label>
                                    <input type="text" name="wptbt_banner_slides[{{index}}][title]" value="" style="width: 100%;" />
                                </p>
                                
                                <p>
                                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">
                                        <?php _e('Subtítulo:', 'wp-tailwind-theme'); ?>
                                    </label>
                                    <textarea name="wptbt_banner_slides[{{index}}][subtitle]" rows="2" style="width: 100%;"></textarea>
                                </p>
                                
                                <div class="slide-button-section" style="display: flex; gap: 10px; margin-bottom: 15px;">
                                    <div style="flex: 1;">
                                        <label style="display: block; font-weight: bold; margin-bottom: 5px;">
                                            <?php _e('Texto del botón:', 'wp-tailwind-theme'); ?>
                                        </label>
                                        <input type="text" name="wptbt_banner_slides[{{index}}][button_text]" value="" style="width: 100%;" />
                                    </div>
                                    <div style="flex: 1;">
                                        <label style="display: block; font-weight: bold; margin-bottom: 5px;">
                                            <?php _e('URL del botón:', 'wp-tailwind-theme'); ?>
                                        </label>
                                        <input type="url" name="wptbt_banner_slides[{{index}}][button_url]" value="" style="width: 100%;" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </script>
                </div>
            </div>
        </div>

        <script>
            // Script simple para mostrar/ocultar las opciones del banner
            (function($) {
                $(document).ready(function() {
                    // Mostrar/ocultar opciones del banner
                    $('input[name="wptbt_show_banner"]').change(function() {
                        if ($(this).is(':checked')) {
                            $('.wptbt-banner-options').show();
                        } else {
                            $('.wptbt-banner-options').hide();
                        }
                    });

                    // Cambiar entre modo global e individual
                    $('input[name="wptbt_banner_mode"]').change(function() {
                        if ($(this).val() === 'global') {
                            $('.wptbt-global-content').show();
                            $('.slide-content').hide();
                        } else {
                            $('.wptbt-global-content').hide();
                            $('.slide-content').show();
                        }
                    });
                });
            })(jQuery);
        </script>
<?php
    }

    /**
     * Guardar los datos del metabox
     *
     * @param int $post_id ID del post
     */
    public function save_meta_box($post_id)
    {
        // Verificar nonce
        if (!isset($_POST['wptbt_banner_metabox_nonce']) || !wp_verify_nonce($_POST['wptbt_banner_metabox_nonce'], 'wptbt_banner_metabox')) {
            return;
        }

        // Verificar permisos de edición
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Evitar guardar durante autoguardado
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Guardar checkbox de mostrar banner
        $show_banner = isset($_POST['wptbt_show_banner']) ? '1' : '';
        update_post_meta($post_id, 'wptbt_show_banner', $show_banner);

        // Solo guardar el resto de campos si el banner está habilitado
        if ($show_banner) {
            // Guardar modo del banner
            $banner_mode = isset($_POST['wptbt_banner_mode']) ? sanitize_text_field($_POST['wptbt_banner_mode']) : 'global';
            update_post_meta($post_id, 'wptbt_banner_mode', $banner_mode);

            // Guardar datos globales
            if (isset($_POST['wptbt_banner_title'])) {
                update_post_meta($post_id, 'wptbt_banner_title', sanitize_text_field($_POST['wptbt_banner_title']));
            }

            if (isset($_POST['wptbt_banner_subtitle'])) {
                update_post_meta($post_id, 'wptbt_banner_subtitle', wp_kses_post($_POST['wptbt_banner_subtitle']));
            }

            if (isset($_POST['wptbt_banner_button_text'])) {
                update_post_meta($post_id, 'wptbt_banner_button_text', sanitize_text_field($_POST['wptbt_banner_button_text']));
            }

            if (isset($_POST['wptbt_banner_button_url'])) {
                update_post_meta($post_id, 'wptbt_banner_button_url', esc_url_raw($_POST['wptbt_banner_button_url']));
            }
            // Guardar configuración de calificaciones global
            $show_rating = isset($_POST['wptbt_banner_show_rating']) ? '1' : '';
            update_post_meta($post_id, 'wptbt_banner_show_rating', $show_rating);

            if (isset($_POST['wptbt_banner_rating_text'])) {
                update_post_meta($post_id, 'wptbt_banner_rating_text', sanitize_text_field($_POST['wptbt_banner_rating_text']));
            }

            // Guardar slides individuales
            $slides = array();

            if (isset($_POST['wptbt_banner_slides']) && is_array($_POST['wptbt_banner_slides'])) {
                foreach ($_POST['wptbt_banner_slides'] as $slide) {
                    $type = isset($slide['type']) ? sanitize_text_field($slide['type']) : 'image';

                    $clean_slide = array(
                        'type' => $type
                    );

                    if (isset($slide['media_id'])) {
                        $clean_slide['media_id'] = intval($slide['media_id']);
                    }

                    if ($type === 'video' && isset($slide['video_url']) && empty($slide['media_id'])) {
                        $clean_slide['video_url'] = esc_url_raw($slide['video_url']);
                    }

                    $clean_slide['title'] = isset($slide['title']) ? sanitize_text_field($slide['title']) : '';
                    $clean_slide['subtitle'] = isset($slide['subtitle']) ? wp_kses_post($slide['subtitle']) : '';
                    $clean_slide['button_text'] = isset($slide['button_text']) ? sanitize_text_field($slide['button_text']) : '';
                    $clean_slide['button_url'] = isset($slide['button_url']) ? esc_url_raw($slide['button_url']) : '';
                    $clean_slide['show_rating'] = isset($slide['show_rating']) ? '1' : '';
                    $clean_slide['rating_text'] = isset($slide['rating_text']) ? sanitize_text_field($slide['rating_text']) : '';

                    $slides[] = $clean_slide;
                }
            }

            update_post_meta($post_id, 'wptbt_banner_slides', $slides);

            // Para compatibilidad con versión anterior, actualizar también el campo de imágenes
            $image_ids = array();
            foreach ($slides as $slide) {
                if ($slide['type'] === 'image' && !empty($slide['media_id'])) {
                    $image_ids[] = $slide['media_id'];
                }
            }

            update_post_meta($post_id, 'wptbt_banner_images', implode(',', $image_ids));
        }
    }
}
