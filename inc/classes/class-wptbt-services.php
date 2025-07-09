<?php

/**
 * Servicios Custom Post Type
 *
 * @package WPTBT
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Class WPTBT_Services
 */
class WPTBT_Services
{
    private $translate = '';

    private $site_slugs = [];
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translate = 'wptbt-services';

        // Configura los slugs para cada sitio de la red
        $this->site_slugs = [
            1 => 'services',     // Sitio principal (inglés)
            2 => 'servicios',    // Sitio en español
            3 => 'servicos', // Sitio en alemán
            // Añade más mapeos según sea necesario
        ];
        // Registrar Custom Post Type
        add_action('init', [$this, 'register_post_type'], 11);

        // Añadir meta box para el precio
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);

        // Guardar metadatos
        add_action('save_post', [$this, 'save_meta_box_data']);
    }

    /**
     * Obtener el slug apropiado para el sitio actual
     */
    private function get_site_slug()
    {
        $blog_id = get_current_blog_id();

        if (isset($this->site_slugs[$blog_id])) {
            return $this->site_slugs[$blog_id];
        }

        // Valor predeterminado si no hay mapeo específico
        return 'services';
    }

    /**
     * Registrar el Custom Post Type Servicios
     */
    public function register_post_type()
    {
        $labels = [
            'name'               => _x('Services', 'post type general name', $this->translate),
            'singular_name'      => _x('Service', 'post type singular name', $this->translate),
            'menu_name'          => _x('Services', 'admin menu', $this->translate),
            'name_admin_bar'     => _x('Service', 'add new on admin bar', $this->translate),
            'add_new'            => _x('Add New', 'service', $this->translate),
            'add_new_item'       => __('Add New Service', $this->translate),
            'new_item'           => __('New Service', $this->translate),
            'edit_item'          => __('Edit Service', $this->translate),
            'view_item'          => __('View Service', $this->translate),
            'all_items'          => __('All Services', $this->translate),
            'search_items'       => __('Search Services', $this->translate),
            'parent_item_colon'  => __('Parent Services:', $this->translate),
            'not_found'          => __('No services found.', $this->translate),
            'not_found_in_trash' => __('No services found in trash.', $this->translate)
        ];
        $site_slug = $this->get_site_slug();
        $args = [
            'labels'             => $labels,
            'description'        => __('Services offered', $this->translate),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => [
                'slug' => $site_slug,
                'with_front' => false  // Añadir esto evita que se use /blog/ como prefijo
            ],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-clipboard',
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt']
        ];

        register_post_type('servicio', $args);
    }

    /**
     * Añadir meta boxes para campos personalizados
     */
    public function add_meta_boxes()
    {
        add_meta_box(
            'wptbt_service_price',
            __('Service Prices', $this->translate),
            [$this, 'render_price_meta_box'],
            'servicio',
            'normal',
            'high'
        );

        add_meta_box(
            'wptbt_service_hours',
            __('Available Hours', $this->translate),
            [$this, 'render_hours_meta_box'],
            'servicio',
            'normal',
            'high'
        );
        add_meta_box(
            'wptbt_service_subtitle',
            __('Service Subtitle', $this->translate),
            [$this, 'render_subtitle_meta_box'],
            'servicio',
            'normal',
            'high'
        );
    }

    /**
     * Renderizar el meta box de horarios
     *
     * @param WP_Post $post El objeto post.
     */
    public function render_hours_meta_box($post)
    {
        // Añadir nonce para verificación
        wp_nonce_field('wptbt_save_service_hours', 'wptbt_service_hours_nonce');

        // Obtener horarios guardados
        $hours = get_post_meta($post->ID, '_wptbt_service_hours', true);
        if (!$hours || !is_array($hours)) {
            $hours = [];
        }

        // Ordenar horarios
        sort($hours);
?>
        <p><?php _e('Set the available hours for this service.', $this->translate); ?></p>

        <div class="wptbt-hours-container">
            <div class="wptbt-hours-tools" style="margin-bottom: 15px;">
                <button type="button" class="button add-hour"><?php _e('Add Hour', $this->translate); ?></button>
                <button type="button" class="button add-multiple-hours" style="margin-left: 10px;"><?php _e('Add Multiple Hours', $this->translate); ?></button>
            </div>

            <div id="hours-container" style="max-height: 300px; overflow-y: auto; padding: 10px; border: 1px solid #ddd; background: #f9f9f9;">
                <?php
                // Mostrar horarios existentes
                if (!empty($hours)) {
                    foreach ($hours as $hour) {
                ?>
                        <div class="hour-item" style="display: flex; align-items: center; margin-bottom: 10px;">
                            <input type="time" name="service_hours[]" value="<?php echo esc_attr($hour); ?>" class="widefat" style="margin-right: 10px;" />
                            <button type="button" class="button button-small remove-hour"><?php _e('Remove', $this->translate); ?></button>
                        </div>
                <?php
                    }
                }
                ?>
                <!-- Plantilla para nuevos horarios -->
                <div class="hour-item-template screen-reader-text" style="display: none;">
                    <div class="hour-item" style="display: flex; align-items: center; margin-bottom: 10px;">
                        <input type="time" name="service_hours[]" value="" class="widefat" style="margin-right: 10px;" />
                        <button type="button" class="button button-small remove-hour"><?php _e('Remove', $this->translate); ?></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para agregar múltiples horarios -->
        <div id="add-multiple-hours-modal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
            <div style="background-color: #fff; margin: 10% auto; padding: 20px; border-radius: 5px; width: 50%; max-width: 500px;">
                <h3><?php _e('Add Multiple Hours', $this->translate); ?></h3>

                <div style="margin: 15px 0;">
                    <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <div>
                            <label><?php _e('Start time', $this->translate); ?></label>
                            <input type="time" id="start-time" class="widefat">
                        </div>
                        <div>
                            <label><?php _e('End time', $this->translate); ?></label>
                            <input type="time" id="end-time" class="widefat">
                        </div>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label><?php _e('Interval (minutes)', $this->translate); ?></label>
                        <select id="interval" class="widefat">
                            <option value="15">15 <?php _e('minutes', $this->translate); ?></option>
                            <option value="30" selected>30 <?php _e('minutes', $this->translate); ?></option>
                            <option value="60">60 <?php _e('minutes', $this->translate); ?></option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="button" id="cancel-multiple-hours"><?php _e('Cancel', $this->translate); ?></button>
                    <button type="button" class="button button-primary" id="add-multiple-hours-confirm"><?php _e('Add', $this->translate); ?></button>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Añadir nuevo horario
                $('.add-hour').on('click', function() {
                    var template = $('.hour-item-template').html();
                    $('#hours-container').append(template);
                });

                // Mostrar modal para añadir múltiples horarios
                $('.add-multiple-hours').on('click', function() {
                    $('#add-multiple-hours-modal').show();
                });

                // Cancelar modal
                $('#cancel-multiple-hours').on('click', function() {
                    $('#add-multiple-hours-modal').hide();
                });

                // Eliminar horario
                $(document).on('click', '.remove-hour', function() {
                    $(this).closest('.hour-item').remove();
                });

                // Añadir múltiples horarios
                $('#add-multiple-hours-confirm').on('click', function() {
                    var startTime = $('#start-time').val();
                    var endTime = $('#end-time').val();
                    var interval = parseInt($('#interval').val(), 10);

                    if (!startTime || !endTime) {
                        alert('<?php echo esc_js(__('Please enter start and end times.', $this->translate)); ?>');
                        return;
                    }

                    // Convertir a minutos para cálculos
                    var startMinutes = convertTimeToMinutes(startTime);
                    var endMinutes = convertTimeToMinutes(endTime);

                    if (startMinutes >= endMinutes) {
                        alert('<?php echo esc_js(__('End time must be after start time.', $this->translate)); ?>');
                        return;
                    }

                    // Generar horarios
                    for (var currentMinutes = startMinutes; currentMinutes < endMinutes; currentMinutes += interval) {
                        var timeString = convertMinutesToTime(currentMinutes);
                        var template = $('.hour-item-template').html();
                        var newItem = $(template);
                        newItem.find('input[type="time"]').val(timeString);
                        $('#hours-container').append(newItem);
                    }

                    // Cerrar modal
                    $('#add-multiple-hours-modal').hide();
                });

                // Función para convertir "HH:MM" a minutos
                function convertTimeToMinutes(timeString) {
                    var parts = timeString.split(':');
                    return parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
                }

                // Función para convertir minutos a "HH:MM"
                function convertMinutesToTime(minutes) {
                    var hours = Math.floor(minutes / 60);
                    var mins = minutes % 60;
                    return (hours < 10 ? '0' : '') + hours + ':' + (mins < 10 ? '0' : '') + mins;
                }
            });
        </script>
        <style>
            #add-multiple-hours-modal {
                transition: all 0.3s ease;
            }

            #hours-container {
                border-radius: 4px;
            }

            .hour-item {
                transition: all 0.2s ease;
            }

            .hour-item:hover {
                background-color: #f0f0f0;
            }
        </style>
    <?php
    }

    /**
     * Renderizar el meta box de precios (NUEVO - Múltiples precios dinámicos)
     *
     * @param WP_Post $post El objeto post.
     */
    public function render_price_meta_box($post)
    {
        // Añadir nonce para verificación
        wp_nonce_field('wptbt_save_service_price', 'wptbt_service_price_nonce');

        // Obtener precios guardados (nuevo formato)
        $prices = get_post_meta($post->ID, '_wptbt_service_prices', true);
        
        // Si no existe el nuevo formato, migrar desde el formato antiguo
        if (empty($prices) || !is_array($prices)) {
            $prices = [];
            
            // Migrar precios existentes del formato antiguo
            $precio_duracion1 = get_post_meta($post->ID, '_wptbt_service_duration1', true);
            $precio_valor1 = get_post_meta($post->ID, '_wptbt_service_price1', true);
            $precio_duracion2 = get_post_meta($post->ID, '_wptbt_service_duration2', true);
            $precio_valor2 = get_post_meta($post->ID, '_wptbt_service_price2', true);
            
            if (!empty($precio_duracion1) && !empty($precio_valor1)) {
                $prices[] = ['duration' => $precio_duracion1, 'price' => $precio_valor1];
            }
            if (!empty($precio_duracion2) && !empty($precio_valor2)) {
                $prices[] = ['duration' => $precio_duracion2, 'price' => $precio_valor2];
            }
            
            // Si no hay precios del formato antiguo, crear uno vacío
            if (empty($prices)) {
                $prices[] = ['duration' => '', 'price' => ''];
            }
        }
    ?>
        <div class="wptbt-prices-container">
            <h4><?php echo __('Prices by duration', $this->translate); ?></h4>
            <p><?php echo __('Configure multiple duration and price options for this service.', $this->translate); ?></p>
            
            <div class="wptbt-prices-tools" style="margin-bottom: 15px;">
                <button type="button" class="button add-price"><?php _e('Add Price Option', $this->translate); ?></button>
            </div>

            <div id="prices-container" style="max-height: 400px; overflow-y: auto; padding: 10px; border: 1px solid #ddd; background: #f9f9f9; border-radius: 4px;">
                <?php foreach ($prices as $index => $price) : ?>
                    <div class="price-item" style="display: flex; gap: 10px; margin-bottom: 15px; padding: 15px; background: white; border-radius: 4px; border: 1px solid #e0e0e0;">
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">
                                <?php echo __('Duration (minutes)', $this->translate); ?>
                            </label>
                            <input type="text" 
                                   name="service_prices[<?php echo $index; ?>][duration]" 
                                   value="<?php echo esc_attr($price['duration']); ?>" 
                                   placeholder="30"
                                   style="width: 100%;" />
                        </div>
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">
                                <?php echo __('Price (with $ symbol)', $this->translate); ?>
                            </label>
                            <input type="text" 
                                   name="service_prices[<?php echo $index; ?>][price]" 
                                   value="<?php echo esc_attr($price['price']); ?>" 
                                   placeholder="$50"
                                   style="width: 100%;" />
                        </div>
                        <div style="display: flex; align-items: end;">
                            <button type="button" class="button button-small remove-price" style="margin-bottom: 0;">
                                <?php _e('Remove', $this->translate); ?>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <!-- Plantilla para nuevos precios -->
                <div class="price-item-template" style="display: none;">
                    <div class="price-item" style="display: flex; gap: 10px; margin-bottom: 15px; padding: 15px; background: white; border-radius: 4px; border: 1px solid #e0e0e0;">
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">
                                <?php echo __('Duration (minutes)', $this->translate); ?>
                            </label>
                            <input type="text" 
                                   name="service_prices[INDEX][duration]" 
                                   value="" 
                                   placeholder="30"
                                   style="width: 100%;" />
                        </div>
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">
                                <?php echo __('Price (with $ symbol)', $this->translate); ?>
                            </label>
                            <input type="text" 
                                   name="service_prices[INDEX][price]" 
                                   value="" 
                                   placeholder="$50"
                                   style="width: 100%;" />
                        </div>
                        <div style="display: flex; align-items: end;">
                            <button type="button" class="button button-small remove-price" style="margin-bottom: 0;">
                                <?php _e('Remove', $this->translate); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                var priceIndex = <?php echo count($prices); ?>;

                // Añadir nuevo precio
                $('.add-price').on('click', function() {
                    var template = $('.price-item-template').html();
                    template = template.replace(/INDEX/g, priceIndex);
                    $('#prices-container').append(template);
                    priceIndex++;
                });

                // Eliminar precio
                $(document).on('click', '.remove-price', function() {
                    var priceItems = $('.price-item').length;
                    if (priceItems > 1) {
                        $(this).closest('.price-item').remove();
                    } else {
                        alert('<?php echo esc_js(__('At least one price option is required.', $this->translate)); ?>');
                    }
                });
            });
        </script>

        <style>
            .wptbt-prices-container .price-item {
                transition: all 0.2s ease;
            }
            
            .wptbt-prices-container .price-item:hover {
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            
            .wptbt-prices-container input[type="text"] {
                transition: border-color 0.2s ease;
            }
            
            .wptbt-prices-container input[type="text"]:focus {
                border-color: #007cba;
                box-shadow: 0 0 0 1px #007cba;
            }
        </style>
    <?php
    }

    /**
     * Renderizar el meta box del subtítulo
     *
     * @param WP_Post $post El objeto post.
     */
    public function render_subtitle_meta_box($post)
    {
        // Añadir nonce para verificación
        wp_nonce_field('wptbt_save_service_subtitle', 'wptbt_service_subtitle_nonce');

        // Obtener valor guardado
        $subtitle = get_post_meta($post->ID, '_wptbt_service_subtitle', true);
    ?>
        <p><?php echo __('Add a subtitle for this service.', $this->translate); ?></p>
        <div>
            <input type="text" id="wptbt_service_subtitle" name="wptbt_service_subtitle"
                value="<?php echo esc_attr($subtitle); ?>" style="width: 100%;" />
        </div>
<?php
    }

    /**
     * Guardar los datos del meta box (ACTUALIZADO para múltiples precios)
     *
     * @param int $post_id ID del post.
     */
    public function save_meta_box_data($post_id)
    {
        // Verificar si debemos guardar
        if (!isset($_POST['wptbt_service_price_nonce'])) {
            return;
        }

        // Verificar que el nonce es válido
        if (!wp_verify_nonce($_POST['wptbt_service_price_nonce'], 'wptbt_save_service_price')) {
            return;
        }

        // Si es autoguardado, no hacemos nada
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Verificar permisos
        if (isset($_POST['post_type']) && 'servicio' == $_POST['post_type']) {
            if (!current_user_can('edit_post', $post_id)) {
                return;
            }
        }

        // Guardar múltiples precios (nuevo formato)
        if (isset($_POST['service_prices']) && is_array($_POST['service_prices'])) {
            $prices = [];
            
            foreach ($_POST['service_prices'] as $price_data) {
                if (!empty($price_data['duration']) && !empty($price_data['price'])) {
                    $prices[] = [
                        'duration' => sanitize_text_field($price_data['duration']),
                        'price' => sanitize_text_field($price_data['price'])
                    ];
                }
            }
            
            if (!empty($prices)) {
                update_post_meta($post_id, '_wptbt_service_prices', $prices);
                
                // Mantener compatibilidad con el formato antiguo - usar el primer precio
                update_post_meta($post_id, '_wptbt_service_duration1', $prices[0]['duration']);
                update_post_meta($post_id, '_wptbt_service_price1', $prices[0]['price']);
                update_post_meta($post_id, '_wptbt_service_price', $prices[0]['price']);
                
                // Si hay un segundo precio, guardarlo también
                if (isset($prices[1])) {
                    update_post_meta($post_id, '_wptbt_service_duration2', $prices[1]['duration']);
                    update_post_meta($post_id, '_wptbt_service_price2', $prices[1]['price']);
                } else {
                    delete_post_meta($post_id, '_wptbt_service_duration2');
                    delete_post_meta($post_id, '_wptbt_service_price2');
                }
                
            } else {
                delete_post_meta($post_id, '_wptbt_service_prices');
                delete_post_meta($post_id, '_wptbt_service_duration1');
                delete_post_meta($post_id, '_wptbt_service_price1');
                delete_post_meta($post_id, '_wptbt_service_duration2');
                delete_post_meta($post_id, '_wptbt_service_price2');
                delete_post_meta($post_id, '_wptbt_service_price');
            }
        }

        // Verificar el nonce del subtítulo
        if (isset($_POST['wptbt_service_subtitle_nonce']) && wp_verify_nonce($_POST['wptbt_service_subtitle_nonce'], 'wptbt_save_service_subtitle')) {
            // Guardar subtítulo
            if (isset($_POST['wptbt_service_subtitle'])) {
                $subtitle = sanitize_text_field($_POST['wptbt_service_subtitle']);
                update_post_meta($post_id, '_wptbt_service_subtitle', $subtitle);
            } else {
                delete_post_meta($post_id, '_wptbt_service_subtitle');
            }
        }

        // Verificar el nonce de horarios
        if (isset($_POST['wptbt_service_hours_nonce']) && wp_verify_nonce($_POST['wptbt_service_hours_nonce'], 'wptbt_save_service_hours')) {

            // Guardar horarios
            if (isset($_POST['service_hours']) && is_array($_POST['service_hours'])) {
                $hours = [];
                $posted_hours = $_POST['service_hours'];

                foreach ($posted_hours as $hour) {
                    if (!empty($hour)) {
                        $hours[] = sanitize_text_field($hour);
                    }
                }

                // Ordenar los horarios antes de guardarlos
                sort($hours);

                update_post_meta($post_id, '_wptbt_service_hours', $hours);
            } else {
                // Si no hay horarios, eliminar el meta
                delete_post_meta($post_id, '_wptbt_service_hours');
            }
        }
    }
}

// Inicializar la clase
new WPTBT_Services();