<?php

/**
 * Tours Custom Post Type MEJORADO
 * Sistema completo para agencia de viajes con galería y funcionalidades avanzadas
 */
class WPTBT_Tours
{
    private $translate = 'wptbt-tours';
    private $site_slugs = [];

    public function __construct()
    {
        // Registrar CPT y Taxonomía
        add_action('init', [$this, 'register_post_type'], 11);
        add_action('init', [$this, 'register_destinations_taxonomy'], 12);
        add_action('init', [$this, 'register_tour_categories_taxonomy'], 13);

        // Meta boxes
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post', [$this, 'save_meta_box_data']);
        
        // Scripts y estilos para admin
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        
        // Columnas personalizadas en admin
        add_filter('manage_tours_posts_columns', [$this, 'add_admin_columns']);
        add_action('manage_tours_posts_custom_column', [$this, 'show_admin_columns'], 10, 2);
        
        $this->site_slugs = [
            1 => 'tours',     // Inglés
            2 => 'tours',     // Español  
            3 => 'tours',     // Portugués
        ];
    }

    private function get_site_slug()
    {
        $blog_id = get_current_blog_id();
        if (isset($this->site_slugs[$blog_id])) {
            return $this->site_slugs[$blog_id];
        }
        return 'tours';
    }

    public function register_post_type()
    {
        $labels = [
            'name'               => _x('Tours', 'post type general name', $this->translate),
            'singular_name'      => _x('Tour', 'post type singular name', $this->translate),
            'menu_name'          => _x('Tours', 'admin menu', $this->translate),
            'name_admin_bar'     => _x('Tour', 'add new on admin bar', $this->translate),
            'add_new'            => _x('Add New', 'tour', $this->translate),
            'add_new_item'       => __('Add New Tour', $this->translate),
            'new_item'           => __('New Tour', $this->translate),
            'edit_item'          => __('Edit Tour', $this->translate),
            'view_item'          => __('View Tour', $this->translate),
            'all_items'          => __('All Tours', $this->translate),
            'search_items'       => __('Search Tours', $this->translate),
            'parent_item_colon'  => __('Parent Tours:', $this->translate),
            'not_found'          => __('No Tours found.', $this->translate),
            'not_found_in_trash' => __('No Tours found in trash.', $this->translate)
        ];
        
        $site_slug = $this->get_site_slug();
        $args = [
            'labels'             => $labels,
            'description'        => __('Travel tours offered', $this->translate),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => [
                'slug' => $site_slug,
                'with_front' => false
            ],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-airplane',
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt'],
            'taxonomies'         => ['destinations', 'tour-categories'],
            'show_in_rest'       => true,
        ];

        register_post_type('tours', $args);
    }

    public function register_destinations_taxonomy()
    {
        $labels = [
            'name'              => _x('Destinations', 'taxonomy general name', $this->translate),
            'singular_name'     => _x('Destination', 'taxonomy singular name', $this->translate),
            'search_items'      => __('Search Destinations', $this->translate),
            'all_items'         => __('All Destinations', $this->translate),
            'edit_item'         => __('Edit Destination', $this->translate),
            'add_new_item'      => __('Add New Destination', $this->translate),
        ];

        register_taxonomy('destinations', ['tours'], [
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'destino'],
            'show_in_rest'      => true,
        ]);
    }

    // NUEVA: Taxonomía para categorías de tours
    public function register_tour_categories_taxonomy()
    {
        $labels = [
            'name'              => _x('Tour Categories', 'taxonomy general name', $this->translate),
            'singular_name'     => _x('Tour Category', 'taxonomy singular name', $this->translate),
            'search_items'      => __('Search Categories', $this->translate),
            'all_items'         => __('All Categories', $this->translate),
            'edit_item'         => __('Edit Category', $this->translate),
            'add_new_item'      => __('Add New Category', $this->translate),
        ];

        register_taxonomy('tour-categories', ['tours'], [
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'categoria-tour'],
            'show_in_rest'      => true,
        ]);
    }

    public function enqueue_admin_scripts($hook)
    {
        if ('post.php' == $hook || 'post-new.php' == $hook) {
            global $post_type;
            if ('tours' == $post_type) {
                wp_enqueue_media();
                wp_enqueue_script('jquery-ui-sortable');
                wp_enqueue_script('jquery-ui-datepicker');
                wp_enqueue_style('jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css');
            }
        }
    }

    public function add_meta_boxes()
    {
        // NUEVA: Galería de imágenes
        add_meta_box(
            'wptbt_tour_gallery',
            __('🖼️ Tour Image Gallery', $this->translate),
            [$this, 'render_gallery_meta_box'],
            'tours',
            'normal',
            'high'
        );

        // Meta box de precios actualizado
        add_meta_box(
            'wptbt_tour_pricing',
            __('💰 Tour Pricing', $this->translate),
            [$this, 'render_pricing_meta_box'],
            'tours',
            'normal',
            'high'
        );

        // NUEVA: Información de contacto y reservas
        add_meta_box(
            'wptbt_tour_booking',
            __('📞 Booking Information', $this->translate),
            [$this, 'render_booking_meta_box'],
            'tours',
            'normal',
            'high'
        );

        // Meta box de detalles del tour
        add_meta_box(
            'wptbt_tour_details',
            __('ℹ️ Tour Details', $this->translate),
            [$this, 'render_tour_details_meta_box'],
            'tours',
            'normal',
            'high'
        );

        // NUEVA: Itinerario del tour
        add_meta_box(
            'wptbt_tour_itinerary',
            __('🗓️ Tour Itinerary', $this->translate),
            [$this, 'render_itinerary_meta_box'],
            'tours',
            'normal',
            'high'
        );

        // Meta box de fechas de salida
        add_meta_box(
            'wptbt_tour_dates',
            __('📅 Departure Dates', $this->translate),
            [$this, 'render_dates_meta_box'],
            'tours',
            'normal',
            'high'
        );

        // NUEVA: Ubicación y mapas
        add_meta_box(
            'wptbt_tour_location',
            __('📍 Location & Maps', $this->translate),
            [$this, 'render_location_meta_box'],
            'tours',
            'side',
            'high'
        );

        // Meta box de subtítulo
        add_meta_box(
            'wptbt_tour_subtitle',
            __('📝 Tour Subtitle', $this->translate),
            [$this, 'render_subtitle_meta_box'],
            'tours',
            'side',
            'high'
        );

        // NUEVA: SEO y promoción
        add_meta_box(
            'wptbt_tour_seo',
            __('🚀 SEO & Promotion', $this->translate),
            [$this, 'render_seo_meta_box'],
            'tours',
            'side',
            'default'
        );

        // Meta box para horarios y disponibilidad del formulario de reservas
        add_meta_box(
            'wptbt_tour_schedule',
            __('🕒 Tour Schedule & Availability', $this->translate),
            [$this, 'render_schedule_meta_box'],
            'tours',
            'normal',
            'high'
        );

        // Meta box para precios del formulario de reservas
        add_meta_box(
            'wptbt_tour_booking_prices',
            __('💰 Booking Prices & Options', $this->translate),
            [$this, 'render_booking_prices_meta_box'],
            'tours',
            'normal',
            'high'
        );
    }

    /**
     * NUEVA: Meta box de galería de imágenes
     */
    public function render_gallery_meta_box($post)
    {
        wp_nonce_field('wptbt_save_tour_gallery', 'wptbt_tour_gallery_nonce');
        
        $gallery_images = get_post_meta($post->ID, '_tour_gallery_images', true);
        if (!is_array($gallery_images)) {
            $gallery_images = [];
        }
        
        $use_gallery_as_featured = get_post_meta($post->ID, '_tour_use_gallery_as_featured', true);
        ?>
        
        <div class="tour-gallery-container">
            <div style="margin-bottom: 20px; padding: 15px; background: #e7f3ff; border-left: 4px solid #007cba; border-radius: 4px;">
                <label style="display: flex; align-items: center; gap: 10px; font-weight: 600;">
                    <input type="checkbox" name="tour_use_gallery_as_featured" value="1" 
                           <?php checked($use_gallery_as_featured, '1'); ?> />
                    <?php _e('Use gallery as featured image in single tour view', $this->translate); ?>
                </label>
                <p style="margin: 8px 0 0 0; font-size: 13px; color: #666;">
                    <?php _e('When enabled, the gallery will replace the featured image on single tour pages.', $this->translate); ?>
                </p>
            </div>

            <div class="gallery-actions" style="margin-bottom: 20px;">
                <button type="button" class="button button-primary" id="add-gallery-images">
                    <?php _e('Add Images to Gallery', $this->translate); ?>
                </button>
                <button type="button" class="button" id="clear-gallery" style="margin-left: 10px;">
                    <?php _e('Clear Gallery', $this->translate); ?>
                </button>
            </div>

            <div id="tour-gallery-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; padding: 20px; border: 2px dashed #ddd; border-radius: 8px; min-height: 200px; background: #fafafa;">
                <?php if (empty($gallery_images)): ?>
                    <div id="gallery-empty-state" style="grid-column: 1 / -1; text-align: center; color: #666; padding: 40px 20px;">
                        <p style="margin: 0; font-size: 16px;">📷</p>
                        <p style="margin: 5px 0 0 0;"><?php _e('No images in gallery yet.', $this->translate); ?></p>
                        <p style="margin: 5px 0 0 0; font-size: 13px;"><?php _e('Click "Add Images" to start building your gallery.', $this->translate); ?></p>
                    </div>
                <?php else: ?>
                    <?php foreach ($gallery_images as $index => $image_id): 
                        $image = wp_get_attachment_image_src($image_id, 'thumbnail');
                        if ($image): ?>
                            <div class="gallery-item" data-id="<?php echo $image_id; ?>" style="position: relative; border: 2px solid #ddd; border-radius: 8px; overflow: hidden; background: white; cursor: move;">
                                <img src="<?php echo esc_url($image[0]); ?>" alt="" style="width: 100%; height: 120px; object-fit: cover; display: block;" />
                                <div style="position: absolute; top: 5px; right: 5px;">
                                    <button type="button" class="remove-gallery-image" data-id="<?php echo $image_id; ?>" 
                                            style="background: rgba(220, 53, 69, 0.9); color: white; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px; line-height: 1;">
                                        ×
                                    </button>
                                </div>
                                <div style="position: absolute; bottom: 5px; left: 5px; background: rgba(0, 0, 0, 0.7); color: white; padding: 2px 6px; border-radius: 4px; font-size: 11px;">
                                    #<?php echo $index + 1; ?>
                                </div>
                                <input type="hidden" name="tour_gallery_images[]" value="<?php echo $image_id; ?>" />
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <p style="margin-top: 15px; color: #666; font-size: 13px;">
                <strong><?php _e('Tip:', $this->translate); ?></strong> 
                <?php _e('You can drag and drop images to reorder them. The first image will be the main gallery image.', $this->translate); ?>
            </p>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Hacer sortable la galería
                $('#tour-gallery-container').sortable({
                    items: '.gallery-item',
                    cursor: 'move',
                    opacity: 0.8,
                    placeholder: 'ui-sortable-placeholder',
                    tolerance: 'pointer'
                });

                // Agregar imágenes
                $('#add-gallery-images').on('click', function(e) {
                    e.preventDefault();
                    
                    var mediaUploader = wp.media({
                        title: '<?php echo esc_js(__('Select Images for Gallery', $this->translate)); ?>',
                        button: {
                            text: '<?php echo esc_js(__('Add to Gallery', $this->translate)); ?>'
                        },
                        multiple: true,
                        library: {
                            type: 'image'
                        }
                    });

                    mediaUploader.on('select', function() {
                        var attachments = mediaUploader.state().get('selection').toJSON();
                        var container = $('#tour-gallery-container');
                        
                        // Remover empty state si existe
                        $('#gallery-empty-state').remove();
                        
                        attachments.forEach(function(attachment, index) {
                            // Verificar si la imagen ya está en la galería
                            if (container.find('.gallery-item[data-id="' + attachment.id + '"]').length > 0) {
                                return; // Skip si ya existe
                            }
                            
                            var currentCount = container.find('.gallery-item').length + 1;
                            var itemHtml = '<div class="gallery-item" data-id="' + attachment.id + '" style="position: relative; border: 2px solid #ddd; border-radius: 8px; overflow: hidden; background: white; cursor: move;">' +
                                '<img src="' + attachment.sizes.thumbnail.url + '" alt="" style="width: 100%; height: 120px; object-fit: cover; display: block;" />' +
                                '<div style="position: absolute; top: 5px; right: 5px;">' +
                                    '<button type="button" class="remove-gallery-image" data-id="' + attachment.id + '" style="background: rgba(220, 53, 69, 0.9); color: white; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px; line-height: 1;">×</button>' +
                                '</div>' +
                                '<div style="position: absolute; bottom: 5px; left: 5px; background: rgba(0, 0, 0, 0.7); color: white; padding: 2px 6px; border-radius: 4px; font-size: 11px;">#' + currentCount + '</div>' +
                                '<input type="hidden" name="tour_gallery_images[]" value="' + attachment.id + '" />' +
                            '</div>';
                            
                            container.append(itemHtml);
                        });
                        
                        updateGalleryNumbers();
                    });

                    mediaUploader.open();
                });

                // Remover imagen individual
                $(document).on('click', '.remove-gallery-image', function(e) {
                    e.preventDefault();
                    $(this).closest('.gallery-item').remove();
                    updateGalleryNumbers();
                    
                    // Si no quedan imágenes, mostrar empty state
                    if ($('#tour-gallery-container .gallery-item').length === 0) {
                        showEmptyState();
                    }
                });

                // Limpiar galería completa
                $('#clear-gallery').on('click', function(e) {
                    e.preventDefault();
                    if (confirm('<?php echo esc_js(__('Are you sure you want to remove all images from the gallery?', $this->translate)); ?>')) {
                        $('#tour-gallery-container').empty();
                        showEmptyState();
                    }
                });

                function updateGalleryNumbers() {
                    $('#tour-gallery-container .gallery-item').each(function(index) {
                        $(this).find('div:last-child').text('#' + (index + 1));
                    });
                }

                function showEmptyState() {
                    var emptyHtml = '<div id="gallery-empty-state" style="grid-column: 1 / -1; text-align: center; color: #666; padding: 40px 20px;">' +
                        '<p style="margin: 0; font-size: 16px;">📷</p>' +
                        '<p style="margin: 5px 0 0 0;"><?php echo esc_js(__('No images in gallery yet.', $this->translate)); ?></p>' +
                        '<p style="margin: 5px 0 0 0; font-size: 13px;"><?php echo esc_js(__('Click "Add Images" to start building your gallery.', $this->translate)); ?></p>' +
                    '</div>';
                    $('#tour-gallery-container').html(emptyHtml);
                }
            });
        </script>

        <style>
            .ui-sortable-placeholder {
                background: #f0f0f0;
                border: 2px dashed #ccc;
                border-radius: 8px;
                height: 120px;
                visibility: visible !important;
            }
            .gallery-item:hover {
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                transform: translateY(-2px);
                transition: all 0.2s ease;
            }
        </style>
        <?php
    }

    /**
     * NUEVA: Meta box de información de reservas
     */
    public function render_booking_meta_box($post)
    {
        wp_nonce_field('wptbt_save_tour_booking', 'wptbt_tour_booking_nonce');

        $whatsapp = get_post_meta($post->ID, '_tour_whatsapp', true);
        $phone = get_post_meta($post->ID, '_tour_phone', true);
        $email = get_post_meta($post->ID, '_tour_email', true);
        $booking_url = get_post_meta($post->ID, '_tour_booking_url', true);
        $advance_payment = get_post_meta($post->ID, '_tour_advance_payment', true);
        $cancellation_policy = get_post_meta($post->ID, '_tour_cancellation_policy', true);
        ?>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <h4><?php _e('Contact Information', $this->translate); ?></h4>
                <table class="form-table">
                    <tr>
                        <th><label for="tour_whatsapp"><?php _e('WhatsApp:', $this->translate); ?></label></th>
                        <td><input type="text" id="tour_whatsapp" name="tour_whatsapp" value="<?php echo esc_attr($whatsapp); ?>" placeholder="+51 999 999 999" style="width: 100%;" /></td>
                    </tr>
                    <tr>
                        <th><label for="tour_phone"><?php _e('Phone:', $this->translate); ?></label></th>
                        <td><input type="text" id="tour_phone" name="tour_phone" value="<?php echo esc_attr($phone); ?>" placeholder="+51 84 123456" style="width: 100%;" /></td>
                    </tr>
                    <tr>
                        <th><label for="tour_email"><?php _e('Email:', $this->translate); ?></label></th>
                        <td><input type="email" id="tour_email" name="tour_email" value="<?php echo esc_attr($email); ?>" placeholder="info@agencia.com" style="width: 100%;" /></td>
                    </tr>
                    <tr>
                        <th><label for="tour_booking_url"><?php _e('Booking URL:', $this->translate); ?></label></th>
                        <td><input type="url" id="tour_booking_url" name="tour_booking_url" value="<?php echo esc_attr($booking_url); ?>" placeholder="https://..." style="width: 100%;" /></td>
                    </tr>
                </table>
            </div>
            
            <div>
                <h4><?php _e('Booking Policies', $this->translate); ?></h4>
                <table class="form-table">
                    <tr>
                        <th><label for="tour_advance_payment"><?php _e('Advance Payment:', $this->translate); ?></label></th>
                        <td><input type="text" id="tour_advance_payment" name="tour_advance_payment" value="<?php echo esc_attr($advance_payment); ?>" placeholder="50% advance required" style="width: 100%;" /></td>
                    </tr>
                    <tr>
                        <th><label for="tour_cancellation_policy"><?php _e('Cancellation Policy:', $this->translate); ?></label></th>
                        <td><textarea id="tour_cancellation_policy" name="tour_cancellation_policy" rows="4" style="width: 100%;"><?php echo esc_textarea($cancellation_policy); ?></textarea></td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
    }

    /**
     * NUEVA: Meta box de itinerario del tour
     */
    public function render_itinerary_meta_box($post)
    {
        wp_nonce_field('wptbt_save_tour_itinerary', 'wptbt_tour_itinerary_nonce');

        $itinerary = get_post_meta($post->ID, '_tour_itinerary', true);
        if (!is_array($itinerary)) {
            $itinerary = [];
        }
        ?>
        
        <div class="itinerary-container">
            <div style="margin-bottom: 15px;">
                <button type="button" class="button button-primary" id="add-itinerary-day">
                    <?php _e('Add Day', $this->translate); ?>
                </button>
            </div>

            <div id="itinerary-days" style="max-height: 400px; overflow-y: auto;">
                <?php foreach ($itinerary as $index => $day): ?>
                    <?php $this->render_itinerary_day($index, $day); ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Template para nuevo día -->
        <script type="text/template" id="itinerary-day-template">
            <?php $this->render_itinerary_day('{{INDEX}}', []); ?>
        </script>

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                var dayCount = <?php echo count($itinerary); ?>;

                $('#add-itinerary-day').on('click', function() {
                    var template = $('#itinerary-day-template').html();
                    var newDay = template.replace(/\{\{INDEX\}\}/g, dayCount);
                    $('#itinerary-days').append(newDay);
                    dayCount++;
                });

                $(document).on('click', '.remove-day', function() {
                    $(this).closest('.itinerary-day').remove();
                    updateDayNumbers();
                });
                
                // Manejar horarios
                $(document).on('click', '.add-schedule-item', function() {
                    var dayIndex = $(this).data('day');
                    var container = $('.schedule-items-' + dayIndex);
                    var itemCount = container.find('.schedule-item').length;
                    
                    var newItem = '<div class="schedule-item" style="display: flex; gap: 10px; margin-bottom: 8px; align-items: center;">' +
                        '<input type="text" name="tour_itinerary[' + dayIndex + '][schedule][' + itemCount + '][time]" placeholder="<?php esc_attr_e('e.g., 08:00', $this->translate); ?>" style="width: 80px;" />' +
                        '<input type="text" name="tour_itinerary[' + dayIndex + '][schedule][' + itemCount + '][activity]" placeholder="<?php esc_attr_e('Activity description...', $this->translate); ?>" style="flex: 1;" />' +
                        '<button type="button" class="button button-small remove-schedule-item"><?php _e('Remove', $this->translate); ?></button>' +
                        '</div>';
                    
                    container.append(newItem);
                });
                
                $(document).on('click', '.remove-schedule-item', function() {
                    $(this).closest('.schedule-item').remove();
                });

                function updateDayNumbers() {
                    $('.itinerary-day').each(function(index) {
                        $(this).find('.day-number').text('Day ' + (index + 1));
                        $(this).find('input, textarea').each(function() {
                            var name = $(this).attr('name');
                            if (name) {
                                var newName = name.replace(/\[\d+\]/, '[' + index + ']');
                                $(this).attr('name', newName);
                            }
                        });
                        
                        // Actualizar contenedores de horarios
                        $(this).find('[class*="schedule-container-"]').each(function() {
                            this.className = this.className.replace(/schedule-container-\d+/, 'schedule-container-' + index);
                        });
                        $(this).find('[class*="schedule-items-"]').each(function() {
                            this.className = this.className.replace(/schedule-items-\d+/, 'schedule-items-' + index);
                        });
                        $(this).find('.add-schedule-item').attr('data-day', index);
                    });
                }
            });
        </script>
        <?php
    }

    private function render_itinerary_day($index, $day)
    {
        $title = isset($day['title']) ? $day['title'] : '';
        $description = isset($day['description']) ? $day['description'] : '';
        $meals = isset($day['meals']) ? $day['meals'] : '';
        $accommodation = isset($day['accommodation']) ? $day['accommodation'] : '';
        $schedule = isset($day['schedule']) && is_array($day['schedule']) ? $day['schedule'] : [];
        ?>
        <div class="itinerary-day" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h4 class="day-number" style="margin: 0; color: #007cba;">
                    <?php echo $index !== '{{INDEX}}' ? 'Day ' . ($index + 1) : 'Day {{INDEX_PLUS_1}}'; ?>
                </h4>
                <button type="button" class="button button-small remove-day">
                    <?php _e('Remove Day', $this->translate); ?>
                </button>
            </div>
            
            <table class="form-table" style="margin: 0;">
                <tr>
                    <th style="width: 150px;"><label><?php _e('Title:', $this->translate); ?></label></th>
                    <td><input type="text" name="tour_itinerary[<?php echo $index; ?>][title]" value="<?php echo esc_attr($title); ?>" style="width: 100%;" placeholder="<?php esc_attr_e('e.g., Arrival in Cusco', $this->translate); ?>" /></td>
                </tr>
                <tr>
                    <th><label><?php _e('Description:', $this->translate); ?></label></th>
                    <td><textarea name="tour_itinerary[<?php echo $index; ?>][description]" rows="3" style="width: 100%;" placeholder="<?php esc_attr_e('Detailed description of activities...', $this->translate); ?>"><?php echo esc_textarea($description); ?></textarea></td>
                </tr>
                <tr>
                    <th><label><?php _e('Meals:', $this->translate); ?></label></th>
                    <td><input type="text" name="tour_itinerary[<?php echo $index; ?>][meals]" value="<?php echo esc_attr($meals); ?>" style="width: 100%;" placeholder="<?php esc_attr_e('Breakfast, Lunch, Dinner', $this->translate); ?>" /></td>
                </tr>
                <tr>
                    <th><label><?php _e('Accommodation:', $this->translate); ?></label></th>
                    <td><input type="text" name="tour_itinerary[<?php echo $index; ?>][accommodation]" value="<?php echo esc_attr($accommodation); ?>" style="width: 100%;" placeholder="<?php esc_attr_e('Hotel name or type', $this->translate); ?>" /></td>
                </tr>
                <tr>
                    <th style="vertical-align: top; padding-top: 15px;"><label><?php _e('Schedule:', $this->translate); ?></label></th>
                    <td>
                        <div class="schedule-container-<?php echo $index; ?>" style="border: 1px solid #ddd; border-radius: 4px; padding: 10px; background: white;">
                            <div style="margin-bottom: 10px;">
                                <button type="button" class="button button-small add-schedule-item" data-day="<?php echo $index; ?>">
                                    <?php _e('Add Time Slot', $this->translate); ?>
                                </button>
                            </div>
                            <div class="schedule-items-<?php echo $index; ?>">
                                <?php foreach ($schedule as $s_index => $time_slot): ?>
                                    <div class="schedule-item" style="display: flex; gap: 10px; margin-bottom: 8px; align-items: center;">
                                        <input type="text" name="tour_itinerary[<?php echo $index; ?>][schedule][<?php echo $s_index; ?>][time]" 
                                               value="<?php echo esc_attr($time_slot['time']); ?>" 
                                               placeholder="<?php esc_attr_e('e.g., 08:00', $this->translate); ?>" 
                                               style="width: 80px;" />
                                        <input type="text" name="tour_itinerary[<?php echo $index; ?>][schedule][<?php echo $s_index; ?>][activity]" 
                                               value="<?php echo esc_attr($time_slot['activity']); ?>" 
                                               placeholder="<?php esc_attr_e('Activity description...', $this->translate); ?>" 
                                               style="flex: 1;" />
                                        <button type="button" class="button button-small remove-schedule-item"><?php _e('Remove', $this->translate); ?></button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }

    /**
     * NUEVA: Meta box de ubicación y mapas
     */
    public function render_location_meta_box($post)
    {
        wp_nonce_field('wptbt_save_tour_location', 'wptbt_tour_location_nonce');

        $departure_point = get_post_meta($post->ID, '_tour_departure_point', true);
        $return_point = get_post_meta($post->ID, '_tour_return_point', true);
        $latitude = get_post_meta($post->ID, '_tour_latitude', true);
        $longitude = get_post_meta($post->ID, '_tour_longitude', true);
        $google_maps_url = get_post_meta($post->ID, '_tour_google_maps_url', true);
        ?>
        
        <table class="form-table">
            <tr>
                <th><label for="tour_departure_point"><?php _e('Departure Point:', $this->translate); ?></label></th>
                <td><input type="text" id="tour_departure_point" name="tour_departure_point" value="<?php echo esc_attr($departure_point); ?>" style="width: 100%;" placeholder="<?php esc_attr_e('Hotel pickup / Meeting point', $this->translate); ?>" /></td>
            </tr>
            <tr>
                <th><label for="tour_return_point"><?php _e('Return Point:', $this->translate); ?></label></th>
                <td><input type="text" id="tour_return_point" name="tour_return_point" value="<?php echo esc_attr($return_point); ?>" style="width: 100%;" placeholder="<?php esc_attr_e('Same as departure / City center', $this->translate); ?>" /></td>
            </tr>
            <tr>
                <th><label for="tour_google_maps_url"><?php _e('Google Maps URL:', $this->translate); ?></label></th>
                <td><input type="url" id="tour_google_maps_url" name="tour_google_maps_url" value="<?php echo esc_attr($google_maps_url); ?>" style="width: 100%;" placeholder="https://maps.google.com/..." /></td>
            </tr>
        </table>
        
        <div style="margin-top: 20px;">
            <h4><?php _e('Coordinates (Optional)', $this->translate); ?></h4>
            <div style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label for="tour_latitude"><?php _e('Latitude:', $this->translate); ?></label>
                    <input type="number" id="tour_latitude" name="tour_latitude" value="<?php echo esc_attr($latitude); ?>" step="any" style="width: 100%;" placeholder="-13.531950" />
                </div>
                <div style="flex: 1;">
                    <label for="tour_longitude"><?php _e('Longitude:', $this->translate); ?></label>
                    <input type="number" id="tour_longitude" name="tour_longitude" value="<?php echo esc_attr($longitude); ?>" step="any" style="width: 100%;" placeholder="-71.967463" />
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * NUEVA: Meta box de SEO y promoción
     */
    public function render_seo_meta_box($post)
    {
        wp_nonce_field('wptbt_save_tour_seo', 'wptbt_tour_seo_nonce');

        $featured = get_post_meta($post->ID, '_tour_featured', true);
        $popular = get_post_meta($post->ID, '_tour_popular', true);
        $new_tour = get_post_meta($post->ID, '_tour_new', true);
        $meta_description = get_post_meta($post->ID, '_tour_meta_description', true);
        $keywords = get_post_meta($post->ID, '_tour_keywords', true);
        ?>
        
        <div style="margin-bottom: 20px;">
            <h4><?php _e('Tour Badges', $this->translate); ?></h4>
            
            <label style="display: block; margin-bottom: 10px;">
                <input type="checkbox" name="tour_featured" value="1" <?php checked($featured, '1'); ?> />
                <span style="margin-left: 8px;">⭐ <?php _e('Featured Tour', $this->translate); ?></span>
            </label>
            
            <label style="display: block; margin-bottom: 10px;">
                <input type="checkbox" name="tour_popular" value="1" <?php checked($popular, '1'); ?> />
                <span style="margin-left: 8px;">🔥 <?php _e('Popular Tour', $this->translate); ?></span>
            </label>
            
            <label style="display: block; margin-bottom: 10px;">
                <input type="checkbox" name="tour_new" value="1" <?php checked($new_tour, '1'); ?> />
                <span style="margin-left: 8px;">🆕 <?php _e('New Tour', $this->translate); ?></span>
            </label>
        </div>

        <div>
            <h4><?php _e('SEO Information', $this->translate); ?></h4>
            
            <div style="margin-bottom: 15px;">
                <label for="tour_meta_description"><?php _e('Meta Description:', $this->translate); ?></label>
                <textarea id="tour_meta_description" name="tour_meta_description" rows="3" style="width: 100%; margin-top: 5px;" maxlength="160" placeholder="<?php esc_attr_e('Brief description for search engines (max 160 characters)', $this->translate); ?>"><?php echo esc_textarea($meta_description); ?></textarea>
                <div style="text-align: right; font-size: 12px; color: #666; margin-top: 5px;">
                    <span id="meta-desc-count">0</span>/160
                </div>
            </div>
            
            <div>
                <label for="tour_keywords"><?php _e('Keywords:', $this->translate); ?></label>
                <input type="text" id="tour_keywords" name="tour_keywords" value="<?php echo esc_attr($keywords); ?>" style="width: 100%; margin-top: 5px;" placeholder="<?php esc_attr_e('cusco, machu picchu, inca trail', $this->translate); ?>" />
                <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">
                    <?php _e('Separate keywords with commas', $this->translate); ?>
                </p>
            </div>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                function updateMetaDescCount() {
                    var count = $('#tour_meta_description').val().length;
                    $('#meta-desc-count').text(count);
                    $('#meta-desc-count').parent().css('color', count > 160 ? '#d63638' : '#666');
                }
                
                $('#tour_meta_description').on('input', updateMetaDescCount);
                updateMetaDescCount(); // Initial count
            });
        </script>
        <?php
    }

    // Mantener los otros meta boxes originales con pequeñas mejoras...
    public function render_pricing_meta_box($post)
    {
        wp_nonce_field('wptbt_save_tour_pricing', 'wptbt_tour_pricing_nonce');

        // Obtener precios guardados
        $tour_duration = get_post_meta($post->ID, '_tour_duration', true);
        $price_international = get_post_meta($post->ID, '_tour_price_international', true);
        $price_national = get_post_meta($post->ID, '_tour_price_national', true);
        $price_promotion = get_post_meta($post->ID, '_tour_price_promotion', true);
        $price_original = get_post_meta($post->ID, '_tour_price_original', true);
        $currency = get_post_meta($post->ID, '_tour_currency', true) ?: 'USD';
        
        ?>
        <div class="tour-pricing-container">
            <h4><?php echo __('Tour Duration & Pricing', $this->translate); ?></h4>
            
            <!-- Duración del tour -->
            <table class="form-table">
                <tr>
                    <th><label for="tour_duration"><?php _e('Tour Duration:', $this->translate); ?></label></th>
                    <td>
                        <input type="text" id="tour_duration" name="tour_duration" 
                               value="<?php echo esc_attr($tour_duration); ?>" 
                               placeholder="3 días / 2 noches" 
                               style="width: 300px;" />
                        <p class="description"><?php _e('Example: "3 días / 2 noches" or "Full Day"', $this->translate); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="tour_currency"><?php _e('Currency:', $this->translate); ?></label></th>
                    <td>
                        <select id="tour_currency" name="tour_currency">
                            <option value="USD" <?php selected($currency, 'USD'); ?>>USD ($)</option>
                            <option value="PEN" <?php selected($currency, 'PEN'); ?>>PEN (S/)</option>
                            <option value="EUR" <?php selected($currency, 'EUR'); ?>>EUR (€)</option>
                            <option value="BOB" <?php selected($currency, 'BOB'); ?>>BOB (Bs)</option>
                        </select>
                    </td>
                </tr>
            </table>

            <!-- Precios flexibles -->
            <div style="margin-top: 20px;">
                <h4><?php echo __('Pricing Options', $this->translate); ?></h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px;">
                    
                    <!-- Precio Internacional -->
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #007cba;">
                        <h5 style="margin: 0 0 10px 0; color: #007cba;">
                            🌍 <?php _e('International Price', $this->translate); ?>
                        </h5>
                        <input type="number" 
                               name="tour_price_international" 
                               value="<?php echo esc_attr($price_international); ?>" 
                               placeholder="150"
                               step="0.01"
                               style="width: 100%; margin-bottom: 5px;" />
                        <p class="description" style="margin: 0; font-size: 12px;">
                            <?php _e('Price for foreign tourists', $this->translate); ?>
                        </p>
                    </div>

                    <!-- Precio Nacional -->
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #00a32a;">
                        <h5 style="margin: 0 0 10px 0; color: #00a32a;">
                            🏠 <?php _e('National Price', $this->translate); ?>
                        </h5>
                        <input type="number" 
                               name="tour_price_national" 
                               value="<?php echo esc_attr($price_national); ?>" 
                               placeholder="120"
                               step="0.01"
                               style="width: 100%; margin-bottom: 5px;" />
                        <p class="description" style="margin: 0; font-size: 12px;">
                            <?php _e('Price for local/national tourists', $this->translate); ?>
                        </p>
                    </div>

                    <!-- Precio Original (para tachar) -->
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #dba617;">
                        <h5 style="margin: 0 0 10px 0; color: #dba617;">
                            💰 <?php _e('Original Price', $this->translate); ?>
                        </h5>
                        <input type="number" 
                               name="tour_price_original" 
                               value="<?php echo esc_attr($price_original); ?>" 
                               placeholder="200"
                               step="0.01"
                               style="width: 100%; margin-bottom: 5px;" />
                        <p class="description" style="margin: 0; font-size: 12px;">
                            <?php _e('Original price (will be shown crossed out)', $this->translate); ?>
                        </p>
                    </div>

                    <!-- Precio Promocional -->
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #d63638;">
                        <h5 style="margin: 0 0 10px 0; color: #d63638;">
                            🎯 <?php _e('Promotional Price', $this->translate); ?>
                        </h5>
                        <input type="number" 
                               name="tour_price_promotion" 
                               value="<?php echo esc_attr($price_promotion); ?>" 
                               placeholder="99"
                               step="0.01"
                               style="width: 100%; margin-bottom: 5px;" />
                        <p class="description" style="margin: 0; font-size: 12px;">
                            <?php _e('Special offer price (highlighted)', $this->translate); ?>
                        </p>
                    </div>
                </div>

                <!-- Preview de cómo se verá -->
                <div id="price-preview" style="margin-top: 20px; padding: 15px; background: white; border: 1px solid #ddd; border-radius: 8px;">
                    <h5><?php _e('Price Display Preview:', $this->translate); ?></h5>
                    <div id="preview-content" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                        <!-- Se llenará con JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Función para actualizar el preview
                function updatePreview() {
                    var currency = $('#tour_currency').val();
                    var symbol = currency === 'USD' ? '$' : (currency === 'PEN' ? 'S/' : (currency === 'EUR' ? '€' : 'Bs'));
                    
                    var international = $('input[name="tour_price_international"]').val();
                    var national = $('input[name="tour_price_national"]').val();
                    var original = $('input[name="tour_price_original"]').val();
                    var promotion = $('input[name="tour_price_promotion"]').val();
                    
                    var html = '<div style="display: flex; gap: 20px; flex-wrap: wrap;">';
                    
                    if (promotion && original) {
                        html += '<div style="padding: 10px; border: 2px solid #d63638; border-radius: 8px; background: #fff;">';
                        html += '<div style="font-size: 12px; color: #d63638; font-weight: bold;">🎯 OFERTA ESPECIAL</div>';
                        html += '<div style="font-size: 14px; color: #666; text-decoration: line-through;">' + symbol + original + '</div>';
                        html += '<div style="font-size: 24px; color: #d63638; font-weight: bold;">' + symbol + promotion + '</div>';
                        html += '</div>';
                    }
                    
                    if (international) {
                        html += '<div style="padding: 10px; border: 1px solid #007cba; border-radius: 8px;">';
                        html += '<div style="font-size: 12px; color: #007cba;">🌍 Internacional</div>';
                        html += '<div style="font-size: 18px; color: #007cba; font-weight: bold;">' + symbol + international + '</div>';
                        html += '</div>';
                    }
                    
                    if (national) {
                        html += '<div style="padding: 10px; border: 1px solid #00a32a; border-radius: 8px;">';
                        html += '<div style="font-size: 12px; color: #00a32a;">🏠 Nacional</div>';
                        html += '<div style="font-size: 18px; color: #00a32a; font-weight: bold;">' + symbol + national + '</div>';
                        html += '</div>';
                    }
                    
                    html += '</div>';
                    
                    if (!international && !national && !promotion) {
                        html = '<p style="color: #666; font-style: italic;">Ingresa al menos un precio para ver el preview</p>';
                    }
                    
                    $('#preview-content').html(html);
                }
                
                // Actualizar preview en tiempo real
                $('input[name^="tour_price"], #tour_currency').on('input change', updatePreview);
                
                // Actualizar al cargar
                updatePreview();
            });
        </script>

        <style>
            .tour-pricing-container input[type="number"] {
                transition: border-color 0.2s ease;
            }
            .tour-pricing-container input[type="number"]:focus {
                border-color: #007cba;
                box-shadow: 0 0 0 1px #007cba;
            }
        </style>
        <?php
    }

    public function render_tour_details_meta_box($post)
    {
        wp_nonce_field('wptbt_save_tour_details', 'wptbt_tour_details_nonce');

        // Campos específicos para tours
        $difficulty = get_post_meta($post->ID, '_tour_difficulty', true);
        $min_age = get_post_meta($post->ID, '_tour_min_age', true);
        $max_people = get_post_meta($post->ID, '_tour_max_people', true);
        $includes = get_post_meta($post->ID, '_tour_includes', true);
        $excludes = get_post_meta($post->ID, '_tour_excludes', true);
        
        // Asegurar que includes y excludes sean arrays
        if (!is_array($includes)) $includes = [];
        if (!is_array($excludes)) $excludes = [];
        ?>
        <table class="form-table">
            <tr>
                <th><label for="tour_difficulty"><?php _e('Difficulty Level:', $this->translate); ?></label></th>
                <td>
                    <select id="tour_difficulty" name="tour_difficulty">
                        <option value=""><?php _e('Select...', $this->translate); ?></option>
                        <option value="easy" <?php selected($difficulty, 'easy'); ?>><?php _e('Easy', $this->translate); ?></option>
                        <option value="moderate" <?php selected($difficulty, 'moderate'); ?>><?php _e('Moderate', $this->translate); ?></option>
                        <option value="challenging" <?php selected($difficulty, 'challenging'); ?>><?php _e('Challenging', $this->translate); ?></option>
                        <option value="extreme" <?php selected($difficulty, 'extreme'); ?>><?php _e('Extreme', $this->translate); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="tour_min_age"><?php _e('Minimum Age:', $this->translate); ?></label></th>
                <td><input type="number" id="tour_min_age" name="tour_min_age" value="<?php echo esc_attr($min_age); ?>" min="0" /></td>
            </tr>
            <tr>
                <th><label for="tour_max_people"><?php _e('Maximum People:', $this->translate); ?></label></th>
                <td><input type="number" id="tour_max_people" name="tour_max_people" value="<?php echo esc_attr($max_people); ?>" min="1" /></td>
            </tr>
            <tr>
                <th style="vertical-align: top; padding-top: 15px;"><label><?php _e('Includes:', $this->translate); ?></label></th>
                <td>
                    <div class="includes-container" style="border: 1px solid #ddd; border-radius: 4px; padding: 10px; background: white;">
                        <div style="margin-bottom: 10px;">
                            <button type="button" class="button button-small add-include-item">
                                <?php _e('Add Item', $this->translate); ?>
                            </button>
                        </div>
                        <div class="includes-list">
                            <?php foreach ($includes as $index => $item): ?>
                                <div class="include-item" style="display: flex; gap: 10px; margin-bottom: 8px; align-items: center;">
                                    <input type="text" name="tour_includes[<?php echo $index; ?>]" 
                                           value="<?php echo esc_attr($item); ?>" 
                                           placeholder="<?php esc_attr_e('e.g., Professional guide', $this->translate); ?>" 
                                           style="flex: 1;" />
                                    <button type="button" class="button button-small remove-include-item"><?php _e('Remove', $this->translate); ?></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th style="vertical-align: top; padding-top: 15px;"><label><?php _e('Does not include:', $this->translate); ?></label></th>
                <td>
                    <div class="excludes-container" style="border: 1px solid #ddd; border-radius: 4px; padding: 10px; background: white;">
                        <div style="margin-bottom: 10px;">
                            <button type="button" class="button button-small add-exclude-item">
                                <?php _e('Add Item', $this->translate); ?>
                            </button>
                        </div>
                        <div class="excludes-list">
                            <?php foreach ($excludes as $index => $item): ?>
                                <div class="exclude-item" style="display: flex; gap: 10px; margin-bottom: 8px; align-items: center;">
                                    <input type="text" name="tour_excludes[<?php echo $index; ?>]" 
                                           value="<?php echo esc_attr($item); ?>" 
                                           placeholder="<?php esc_attr_e('e.g., Personal expenses', $this->translate); ?>" 
                                           style="flex: 1;" />
                                    <button type="button" class="button button-small remove-exclude-item"><?php _e('Remove', $this->translate); ?></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Manejar includes
                $('.add-include-item').on('click', function() {
                    var container = $('.includes-list');
                    var itemCount = container.find('.include-item').length;
                    
                    var newItem = '<div class="include-item" style="display: flex; gap: 10px; margin-bottom: 8px; align-items: center;">' +
                        '<input type="text" name="tour_includes[' + itemCount + ']" placeholder="<?php esc_attr_e('e.g., Professional guide', $this->translate); ?>" style="flex: 1;" />' +
                        '<button type="button" class="button button-small remove-include-item"><?php _e('Remove', $this->translate); ?></button>' +
                        '</div>';
                    
                    container.append(newItem);
                });
                
                $(document).on('click', '.remove-include-item', function() {
                    $(this).closest('.include-item').remove();
                });
                
                // Manejar excludes
                $('.add-exclude-item').on('click', function() {
                    var container = $('.excludes-list');
                    var itemCount = container.find('.exclude-item').length;
                    
                    var newItem = '<div class="exclude-item" style="display: flex; gap: 10px; margin-bottom: 8px; align-items: center;">' +
                        '<input type="text" name="tour_excludes[' + itemCount + ']" placeholder="<?php esc_attr_e('e.g., Personal expenses', $this->translate); ?>" style="flex: 1;" />' +
                        '<button type="button" class="button button-small remove-exclude-item"><?php _e('Remove', $this->translate); ?></button>' +
                        '</div>';
                    
                    container.append(newItem);
                });
                
                $(document).on('click', '.remove-exclude-item', function() {
                    $(this).closest('.exclude-item').remove();
                });
            });
        </script>
        <?php
    }

    public function render_dates_meta_box($post)
    {
        wp_nonce_field('wptbt_save_tour_dates', 'wptbt_tour_dates_nonce');

        $departure_dates = get_post_meta($post->ID, '_tour_departure_dates', true);
        if (!$departure_dates || !is_array($departure_dates)) {
            $departure_dates = [];
        }
        ?>
        <p><?php _e('Set the available departure dates for this tour.', $this->translate); ?></p>

        <div class="wptbt-dates-container">
            <div class="wptbt-dates-tools" style="margin-bottom: 15px;">
                <button type="button" class="button add-date"><?php _e('Add Date', $this->translate); ?></button>
                <button type="button" class="button add-monthly-dates" style="margin-left: 10px;"><?php _e('Add Monthly Dates', $this->translate); ?></button>
            </div>

            <div id="dates-container" style="max-height: 300px; overflow-y: auto; padding: 10px; border: 1px solid #ddd; background: #f9f9f9; border-radius: 4px;">
                <?php foreach ($departure_dates as $date) : ?>
                    <div class="date-item" style="display: flex; align-items: center; margin-bottom: 10px;">
                        <input type="date" name="tour_departure_dates[]" value="<?php echo esc_attr($date); ?>" class="widefat" style="margin-right: 10px;" />
                        <button type="button" class="button button-small remove-date"><?php _e('Remove', $this->translate); ?></button>
                    </div>
                <?php endforeach; ?>

                <!-- Template para nuevas fechas -->
                <div class="date-item-template screen-reader-text" style="display: none;">
                    <div class="date-item" style="display: flex; align-items: center; margin-bottom: 10px;">
                        <input type="date" name="tour_departure_dates[]" value="" class="widefat" style="margin-right: 10px;" />
                        <button type="button" class="button button-small remove-date"><?php _e('Remove', $this->translate); ?></button>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('.add-date').on('click', function() {
                    var template = $('.date-item-template').html();
                    $('#dates-container').append(template);
                });

                $('.add-monthly-dates').on('click', function() {
                    // Agregar fechas de todos los sábados del próximo mes
                    var today = new Date();
                    var nextMonth = new Date(today.getFullYear(), today.getMonth() + 1, 1);
                    var endMonth = new Date(today.getFullYear(), today.getMonth() + 2, 0);
                    
                    var saturdays = [];
                    for (var date = new Date(nextMonth); date <= endMonth; date.setDate(date.getDate() + 1)) {
                        if (date.getDay() === 6) { // Sábado
                            saturdays.push(new Date(date));
                        }
                    }
                    
                    saturdays.forEach(function(saturday) {
                        var template = $('.date-item-template').html();
                        var newItem = $(template);
                        var dateString = saturday.toISOString().split('T')[0];
                        newItem.find('input[type="date"]').val(dateString);
                        $('#dates-container').append(newItem);
                    });
                });

                $(document).on('click', '.remove-date', function() {
                    $(this).closest('.date-item').remove();
                });
            });
        </script>
        <?php
    }

    public function render_subtitle_meta_box($post)
    {
        wp_nonce_field('wptbt_save_tour_subtitle', 'wptbt_tour_subtitle_nonce');
        $subtitle = get_post_meta($post->ID, '_wptbt_tour_subtitle', true);
        ?>
        <p><?php echo __('Add a subtitle for this tour.', $this->translate); ?></p>
        <div>
            <input type="text" id="wptbt_tour_subtitle" name="wptbt_tour_subtitle"
                value="<?php echo esc_attr($subtitle); ?>" style="width: 100%;" 
                placeholder="<?php echo esc_attr__('e.g., "Adventure in the Sacred Valley"', $this->translate); ?>" />
        </div>
        <?php
    }

    /**
     * Meta box para horarios y disponibilidad del tour
     */
    public function render_schedule_meta_box($post)
    {
        wp_nonce_field('wptbt_save_tour_schedule', 'wptbt_tour_schedule_nonce');
        
        $tour_hours = get_post_meta($post->ID, '_wptbt_tour_hours', true) ?: [];
        
        ?>
        <div class="tour-schedule-meta-box">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label><?php _e('Available Departure Times', $this->translate); ?></label>
                    </th>
                    <td>
                        <div id="tour-hours-container">
                            <?php if (empty($tour_hours)) : ?>
                                <div class="tour-hour-row">
                                    <input type="time" name="wptbt_tour_hours[]" value="" />
                                    <button type="button" class="remove-hour button"><?php _e('Remove', $this->translate); ?></button>
                                </div>
                            <?php else : ?>
                                <?php foreach ($tour_hours as $hour) : ?>
                                    <div class="tour-hour-row">
                                        <input type="time" name="wptbt_tour_hours[]" value="<?php echo esc_attr($hour); ?>" />
                                        <button type="button" class="remove-hour button"><?php _e('Remove', $this->translate); ?></button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button type="button" id="add-tour-hour" class="button button-secondary">
                            <?php _e('Add Departure Time', $this->translate); ?>
                        </button>
                        <p class="description">
                            <?php _e('Set the available departure times for this tour.', $this->translate); ?>
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('#add-tour-hour').click(function() {
                var newRow = '<div class="tour-hour-row">' +
                    '<input type="time" name="wptbt_tour_hours[]" value="" />' +
                    '<button type="button" class="remove-hour button"><?php echo esc_js(__('Remove', $this->translate)); ?></button>' +
                    '</div>';
                $('#tour-hours-container').append(newRow);
            });

            $(document).on('click', '.remove-hour', function() {
                $(this).closest('.tour-hour-row').remove();
            });
        });
        </script>

        <style>
        .tour-hour-row {
            margin-bottom: 10px;
        }
        .tour-hour-row input {
            margin-right: 10px;
        }
        </style>
        <?php
    }

    /**
     * Meta box para precios del formulario de reservas
     */
    public function render_booking_prices_meta_box($post)
    {
        wp_nonce_field('wptbt_save_tour_booking_prices', 'wptbt_tour_booking_prices_nonce');
        
        $booking_prices = get_post_meta($post->ID, '_wptbt_tour_prices', true) ?: [];
        
        ?>
        <div class="booking-prices-meta-box">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label><?php _e('Tour Pricing Options', $this->translate); ?></label>
                    </th>
                    <td>
                        <div id="booking-prices-container">
                            <?php if (empty($booking_prices)) : ?>
                                <div class="price-option-row">
                                    <label><?php _e('Duration (days)', $this->translate); ?></label>
                                    <input type="number" name="wptbt_tour_prices[0][duration]" value="" min="1" step="1" />
                                    
                                    <label><?php _e('Price', $this->translate); ?></label>
                                    <input type="text" name="wptbt_tour_prices[0][price]" value="" placeholder="$299" />
                                    
                                    <button type="button" class="remove-price-option button"><?php _e('Remove', $this->translate); ?></button>
                                </div>
                            <?php else : ?>
                                <?php foreach ($booking_prices as $index => $price_option) : ?>
                                    <div class="price-option-row">
                                        <label><?php _e('Duration (days)', $this->translate); ?></label>
                                        <input type="number" name="wptbt_tour_prices[<?php echo $index; ?>][duration]" 
                                               value="<?php echo esc_attr($price_option['duration'] ?? ''); ?>" min="1" step="1" />
                                        
                                        <label><?php _e('Price', $this->translate); ?></label>
                                        <input type="text" name="wptbt_tour_prices[<?php echo $index; ?>][price]" 
                                               value="<?php echo esc_attr($price_option['price'] ?? ''); ?>" placeholder="$299" />
                                        
                                        <button type="button" class="remove-price-option button"><?php _e('Remove', $this->translate); ?></button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button type="button" id="add-price-option" class="button button-secondary">
                            <?php _e('Add Price Option', $this->translate); ?>
                        </button>
                        <p class="description">
                            <?php _e('Configure different pricing options for tour durations. These will appear in the booking form.', $this->translate); ?>
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <script>
        jQuery(document).ready(function($) {
            var priceIndex = <?php echo count($booking_prices); ?>;
            
            $('#add-price-option').click(function() {
                var newRow = '<div class="price-option-row">' +
                    '<label><?php echo esc_js(__('Duration (days)', $this->translate)); ?></label>' +
                    '<input type="number" name="wptbt_tour_prices[' + priceIndex + '][duration]" value="" min="1" step="1" />' +
                    '<label><?php echo esc_js(__('Price', $this->translate)); ?></label>' +
                    '<input type="text" name="wptbt_tour_prices[' + priceIndex + '][price]" value="" placeholder="$299" />' +
                    '<button type="button" class="remove-price-option button"><?php echo esc_js(__('Remove', $this->translate)); ?></button>' +
                    '</div>';
                $('#booking-prices-container').append(newRow);
                priceIndex++;
            });

            $(document).on('click', '.remove-price-option', function() {
                $(this).closest('.price-option-row').remove();
            });
        });
        </script>

        <style>
        .price-option-row {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            background: #f9f9f9;
        }
        .price-option-row label {
            display: inline-block;
            width: 120px;
            margin-right: 10px;
            font-weight: bold;
        }
        .price-option-row input {
            margin-right: 15px;
            width: 150px;
        }
        </style>
        <?php
    }

    /**
     * Guardar todos los meta box data (AMPLIADO)
     */
    public function save_meta_box_data($post_id)
    {
        // Verificar permisos básicos
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;

        // Guardar galería
        if (isset($_POST['wptbt_tour_gallery_nonce']) && wp_verify_nonce($_POST['wptbt_tour_gallery_nonce'], 'wptbt_save_tour_gallery')) {
            if (isset($_POST['tour_gallery_images']) && is_array($_POST['tour_gallery_images'])) {
                $gallery_images = array_map('intval', $_POST['tour_gallery_images']);
                update_post_meta($post_id, '_tour_gallery_images', $gallery_images);
            } else {
                delete_post_meta($post_id, '_tour_gallery_images');
            }
            
            $use_gallery = isset($_POST['tour_use_gallery_as_featured']) ? '1' : '0';
            update_post_meta($post_id, '_tour_use_gallery_as_featured', $use_gallery);
        }

        // Guardar información de reservas
        if (isset($_POST['wptbt_tour_booking_nonce']) && wp_verify_nonce($_POST['wptbt_tour_booking_nonce'], 'wptbt_save_tour_booking')) {
            $booking_fields = ['tour_whatsapp', 'tour_phone', 'tour_email', 'tour_booking_url', 'tour_advance_payment'];
            foreach ($booking_fields as $field) {
                if (isset($_POST[$field])) {
                    update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
                }
            }
            
            if (isset($_POST['tour_cancellation_policy'])) {
                update_post_meta($post_id, '_tour_cancellation_policy', sanitize_textarea_field($_POST['tour_cancellation_policy']));
            }
        }

        // Guardar itinerario
        if (isset($_POST['wptbt_tour_itinerary_nonce']) && wp_verify_nonce($_POST['wptbt_tour_itinerary_nonce'], 'wptbt_save_tour_itinerary')) {
            if (isset($_POST['tour_itinerary']) && is_array($_POST['tour_itinerary'])) {
                $itinerary = [];
                foreach ($_POST['tour_itinerary'] as $day) {
                    if (!empty($day['title']) || !empty($day['description'])) {
                        // Procesar horarios si existen
                        $schedule = [];
                        if (isset($day['schedule']) && is_array($day['schedule'])) {
                            foreach ($day['schedule'] as $time_slot) {
                                if (!empty($time_slot['time']) || !empty($time_slot['activity'])) {
                                    $schedule[] = [
                                        'time' => sanitize_text_field($time_slot['time']),
                                        'activity' => sanitize_text_field($time_slot['activity'])
                                    ];
                                }
                            }
                        }
                        
                        $itinerary[] = [
                            'title' => sanitize_text_field($day['title']),
                            'description' => sanitize_textarea_field($day['description']),
                            'meals' => sanitize_text_field($day['meals']),
                            'accommodation' => sanitize_text_field($day['accommodation']),
                            'schedule' => $schedule
                        ];
                    }
                }
                update_post_meta($post_id, '_tour_itinerary', $itinerary);
            } else {
                delete_post_meta($post_id, '_tour_itinerary');
            }
        }

        // Guardar ubicación
        if (isset($_POST['wptbt_tour_location_nonce']) && wp_verify_nonce($_POST['wptbt_tour_location_nonce'], 'wptbt_save_tour_location')) {
            $location_fields = ['tour_departure_point', 'tour_return_point', 'tour_google_maps_url', 'tour_latitude', 'tour_longitude'];
            foreach ($location_fields as $field) {
                if (isset($_POST[$field])) {
                    update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
                }
            }
        }

        // Guardar SEO
        if (isset($_POST['wptbt_tour_seo_nonce']) && wp_verify_nonce($_POST['wptbt_tour_seo_nonce'], 'wptbt_save_tour_seo')) {
            $checkboxes = ['tour_featured', 'tour_popular', 'tour_new'];
            foreach ($checkboxes as $field) {
                $value = isset($_POST[$field]) ? '1' : '0';
                update_post_meta($post_id, '_' . $field, $value);
            }
            
            if (isset($_POST['tour_meta_description'])) {
                update_post_meta($post_id, '_tour_meta_description', sanitize_textarea_field($_POST['tour_meta_description']));
            }
            
            if (isset($_POST['tour_keywords'])) {
                update_post_meta($post_id, '_tour_keywords', sanitize_text_field($_POST['tour_keywords']));
            }
        }

        // Guardar precios (original)
        if (isset($_POST['wptbt_tour_pricing_nonce']) && wp_verify_nonce($_POST['wptbt_tour_pricing_nonce'], 'wptbt_save_tour_pricing')) {
            $pricing_fields = [
                'tour_duration', 'tour_currency',
                'tour_price_international', 'tour_price_national', 
                'tour_price_promotion', 'tour_price_original'
            ];
            
            foreach ($pricing_fields as $field) {
                if (isset($_POST[$field])) {
                    update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
                }
            }
        }

        // Guardar detalles del tour (mejorado para listas)
        if (isset($_POST['wptbt_tour_details_nonce']) && wp_verify_nonce($_POST['wptbt_tour_details_nonce'], 'wptbt_save_tour_details')) {
            $simple_fields = ['tour_difficulty', 'tour_min_age', 'tour_max_people'];
            foreach ($simple_fields as $field) {
                if (isset($_POST[$field])) {
                    update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
                }
            }
            
            // Guardar includes como array
            if (isset($_POST['tour_includes']) && is_array($_POST['tour_includes'])) {
                $includes = array_filter(array_map('sanitize_text_field', $_POST['tour_includes']));
                update_post_meta($post_id, '_tour_includes', $includes);
            } else {
                delete_post_meta($post_id, '_tour_includes');
            }
            
            // Guardar excludes como array
            if (isset($_POST['tour_excludes']) && is_array($_POST['tour_excludes'])) {
                $excludes = array_filter(array_map('sanitize_text_field', $_POST['tour_excludes']));
                update_post_meta($post_id, '_tour_excludes', $excludes);
            } else {
                delete_post_meta($post_id, '_tour_excludes');
            }
        }

        // Guardar fechas de salida (original)
        if (isset($_POST['wptbt_tour_dates_nonce']) && wp_verify_nonce($_POST['wptbt_tour_dates_nonce'], 'wptbt_save_tour_dates')) {
            if (isset($_POST['tour_departure_dates']) && is_array($_POST['tour_departure_dates'])) {
                $dates = array_filter($_POST['tour_departure_dates'], function ($date) {
                    return !empty($date);
                });
                sort($dates);
                update_post_meta($post_id, '_tour_departure_dates', $dates);
            } else {
                delete_post_meta($post_id, '_tour_departure_dates');
            }
        }

        // Guardar horarios de tours para formulario de reservas
        if (isset($_POST['wptbt_tour_schedule_nonce']) && wp_verify_nonce($_POST['wptbt_tour_schedule_nonce'], 'wptbt_save_tour_schedule')) {
            if (isset($_POST['wptbt_tour_hours']) && is_array($_POST['wptbt_tour_hours'])) {
                $tour_hours = array_filter(array_map('sanitize_text_field', $_POST['wptbt_tour_hours']));
                update_post_meta($post_id, '_wptbt_tour_hours', $tour_hours);
            } else {
                delete_post_meta($post_id, '_wptbt_tour_hours');
            }
        }

        // Guardar precios de tours para formulario de reservas
        if (isset($_POST['wptbt_tour_booking_prices_nonce']) && wp_verify_nonce($_POST['wptbt_tour_booking_prices_nonce'], 'wptbt_save_tour_booking_prices')) {
            if (isset($_POST['wptbt_tour_prices']) && is_array($_POST['wptbt_tour_prices'])) {
                $booking_prices = [];
                foreach ($_POST['wptbt_tour_prices'] as $price_data) {
                    if (!empty($price_data['duration']) && !empty($price_data['price'])) {
                        $booking_prices[] = [
                            'duration' => sanitize_text_field($price_data['duration']),
                            'price' => sanitize_text_field($price_data['price'])
                        ];
                    }
                }
                update_post_meta($post_id, '_wptbt_tour_prices', $booking_prices);
            } else {
                delete_post_meta($post_id, '_wptbt_tour_prices');
            }
        }

        // Guardar subtítulo (original)
        if (isset($_POST['wptbt_tour_subtitle_nonce']) && wp_verify_nonce($_POST['wptbt_tour_subtitle_nonce'], 'wptbt_save_tour_subtitle')) {
            if (isset($_POST['wptbt_tour_subtitle'])) {
                $subtitle = sanitize_text_field($_POST['wptbt_tour_subtitle']);
                update_post_meta($post_id, '_wptbt_tour_subtitle', $subtitle);
            } else {
                delete_post_meta($post_id, '_wptbt_tour_subtitle');
            }
        }
    }

    /**
     * NUEVA: Columnas personalizadas en admin
     */
    public function add_admin_columns($columns)
    {
        $new_columns = array();
        foreach ($columns as $key => $title) {
            $new_columns[$key] = $title;
            if ($key == 'title') {
                $new_columns['tour_price'] = __('Price', $this->translate);
                $new_columns['tour_duration'] = __('Duration', $this->translate);
                $new_columns['tour_difficulty'] = __('Difficulty', $this->translate);
                $new_columns['tour_badges'] = __('Badges', $this->translate);
            }
        }
        return $new_columns;
    }

    public function show_admin_columns($column, $post_id)
    {
        switch ($column) {
            case 'tour_price':
                $pricing = self::get_tour_pricing_data($post_id);
                if ($pricing['best_price']) {
                    echo $pricing['symbol'] . $pricing['best_price'];
                    if ($pricing['promotion'] && $pricing['original']) {
                        echo ' <small style="color: #d63638;">🎯</small>';
                    }
                } else {
                    echo '—';
                }
                break;
                
            case 'tour_duration':
                $duration = get_post_meta($post_id, '_tour_duration', true);
                echo $duration ? esc_html($duration) : '—';
                break;
                
            case 'tour_difficulty':
                $difficulty = get_post_meta($post_id, '_tour_difficulty', true);
                $levels = [
                    'easy' => '🟢 Easy',
                    'moderate' => '🟡 Moderate', 
                    'challenging' => '🟠 Challenging',
                    'extreme' => '🔴 Extreme'
                ];
                echo isset($levels[$difficulty]) ? $levels[$difficulty] : '—';
                break;
                
            case 'tour_badges':
                $badges = [];
                if (get_post_meta($post_id, '_tour_featured', true) == '1') $badges[] = '⭐';
                if (get_post_meta($post_id, '_tour_popular', true) == '1') $badges[] = '🔥';
                if (get_post_meta($post_id, '_tour_new', true) == '1') $badges[] = '🆕';
                echo !empty($badges) ? implode(' ', $badges) : '—';
                break;
        }
    }

    /**
     * FUNCIONES HELPER MEJORADAS
     */
    public static function get_tour_best_price($tour_id)
    {
        $promotion = get_post_meta($tour_id, '_tour_price_promotion', true);
        $international = get_post_meta($tour_id, '_tour_price_international', true);
        $national = get_post_meta($tour_id, '_tour_price_national', true);
        
        // Prioridad: promocional > nacional > internacional
        if (!empty($promotion)) return $promotion;
        if (!empty($national)) return $national;
        if (!empty($international)) return $international;
        
        return null;
    }

    public static function get_tour_pricing_data($tour_id)
    {
        $currency = get_post_meta($tour_id, '_tour_currency', true) ?: 'USD';
        $symbol = $currency === 'USD' ? '$' : ($currency === 'PEN' ? 'S/' : ($currency === 'EUR' ? '€' : 'Bs'));
        
        return [
            'currency' => $currency,
            'symbol' => $symbol,
            'duration' => get_post_meta($tour_id, '_tour_duration', true),
            'international' => get_post_meta($tour_id, '_tour_price_international', true),
            'national' => get_post_meta($tour_id, '_tour_price_national', true),
            'promotion' => get_post_meta($tour_id, '_tour_price_promotion', true),
            'original' => get_post_meta($tour_id, '_tour_price_original', true),
            'best_price' => self::get_tour_best_price($tour_id)
        ];
    }

    /**
     * NUEVA: Obtener galería de imágenes
     */
    public static function get_tour_gallery($tour_id)
    {
        $gallery_images = get_post_meta($tour_id, '_tour_gallery_images', true);
        if (!is_array($gallery_images)) {
            return [];
        }
        
        $use_as_featured = get_post_meta($tour_id, '_tour_use_gallery_as_featured', true);
        
        return [
            'images' => $gallery_images,
            'use_as_featured' => $use_as_featured == '1'
        ];
    }

    /**
     * NUEVA: Obtener itinerario
     */
    public static function get_tour_itinerary($tour_id)
    {
        $itinerary = get_post_meta($tour_id, '_tour_itinerary', true);
        return is_array($itinerary) ? $itinerary : [];
    }

    /**
     * NUEVA: Obtener información de contacto
     */
    public static function get_tour_booking_info($tour_id)
    {
        return [
            'whatsapp' => get_post_meta($tour_id, '_tour_whatsapp', true),
            'phone' => get_post_meta($tour_id, '_tour_phone', true),
            'email' => get_post_meta($tour_id, '_tour_email', true),
            'booking_url' => get_post_meta($tour_id, '_tour_booking_url', true),
            'advance_payment' => get_post_meta($tour_id, '_tour_advance_payment', true),
            'cancellation_policy' => get_post_meta($tour_id, '_tour_cancellation_policy', true)
        ];
    }

    /**
     * NUEVA: Obtener datos específicos para el formulario de reservas SolidJS
     */
    public static function get_tour_booking_form_data($tour_id)
    {
        $tour_hours = get_post_meta($tour_id, '_wptbt_tour_hours', true) ?: [];
        $booking_prices = get_post_meta($tour_id, '_wptbt_tour_prices', true) ?: [];
        $tour_title = get_the_title($tour_id);
        $tour_subtitle = get_post_meta($tour_id, '_wptbt_tour_subtitle', true) ?: '';

        // Formatear precios para compatibilidad con el formulario existente
        $durations = [];
        foreach ($booking_prices as $price_data) {
            if (!empty($price_data['duration']) && !empty($price_data['price'])) {
                $days = intval($price_data['duration']);
                $durations[] = [
                    'duration' => $price_data['duration'],
                    'price' => $price_data['price'],
                    'minutes' => $days * 24 * 60, // Convertir días a minutos para compatibilidad
                    'text' => $price_data['duration'] . ' días - ' . $price_data['price'],
                    'value' => $days . 'days-' . $price_data['price']
                ];
            }
        }

        return [
            'id' => (string)$tour_id,
            'title' => $tour_title,
            'subtitle' => $tour_subtitle,
            'hours' => array_values($tour_hours),
            'durations' => array_values($durations),
            // Mantener compatibilidad con código existente
            'duration1' => !empty($durations[0]) ? $durations[0]['duration'] : '',
            'price1' => !empty($durations[0]) ? $durations[0]['price'] : '',
            'duration2' => !empty($durations[1]) ? $durations[1]['duration'] : '',
            'price2' => !empty($durations[1]) ? $durations[1]['price'] : ''
        ];
    }

    /**
     * NUEVA: Verificar si un tour tiene configuración completa para reservas
     */
    public static function is_tour_bookable($tour_id)
    {
        $tour_hours = get_post_meta($tour_id, '_wptbt_tour_hours', true) ?: [];
        $booking_prices = get_post_meta($tour_id, '_wptbt_tour_prices', true) ?: [];
        
        return !empty($tour_hours) && !empty($booking_prices);
    }

    /**
     * NUEVA: Obtener el precio mínimo de un tour para mostrar
     */
    public static function get_tour_min_price($tour_id)
    {
        $booking_prices = get_post_meta($tour_id, '_wptbt_tour_prices', true) ?: [];
        $prices = [];
        
        foreach ($booking_prices as $price_data) {
            if (!empty($price_data['price'])) {
                // Extraer solo números del precio
                $numeric_price = preg_replace('/[^\d.]/', '', $price_data['price']);
                if (is_numeric($numeric_price)) {
                    $prices[] = floatval($numeric_price);
                }
            }
        }
        
        return !empty($prices) ? min($prices) : null;
    }

    /**
     * NUEVA: Obtener etiqueta de precio formateada para mostrar
     */
    public static function get_tour_price_label($tour_id)
    {
        $min_price = self::get_tour_min_price($tour_id);
        if ($min_price === null) {
            return __('Contact for pricing', 'wptbt-tours');
        }
        
        $currency = get_post_meta($tour_id, '_tour_currency', true) ?: 'USD';
        $symbol = $currency === 'USD' ? '$' : ($currency === 'PEN' ? 'S/' : ($currency === 'EUR' ? '€' : 'Bs'));
        
        return sprintf(__('From %s%s', 'wptbt-tours'), $symbol, number_format($min_price, 0));
    }

    /**
     * NUEVA: Obtener configuración del formulario de reservas para JavaScript
     */
    public static function get_booking_form_config($tour_id)
    {
        $booking_data = self::get_tour_booking_form_data($tour_id);
        $booking_info = self::get_tour_booking_info($tour_id);
        
        return [
            'tourId' => $tour_id,
            'tourData' => [$booking_data],
            'isBookable' => self::is_tour_bookable($tour_id),
            'contactInfo' => $booking_info,
            'emailRecipient' => get_theme_mod('tours_booking_form_email', get_option('admin_email')),
            'accentColor' => get_theme_mod('tours_booking_form_accent_color', '#DC2626'),
            'currency' => get_post_meta($tour_id, '_tour_currency', true) ?: 'USD'
        ];
    }
}

new WPTBT_Tours();