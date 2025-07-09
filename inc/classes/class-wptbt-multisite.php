<?php

/**
 * Soporte para WordPress Multisite
 * Incluir este archivo en la clase WPTBT_Setup o en functions.php
 */

/**
 * Clase para gestionar la compatibilidad con WordPress Multisite
 */
class WPTBT_Multisite
{
    /**
     * Inicializar soporte para multisite
     */
    public function init()
    {
        // Verificar si estamos en un entorno multisite
        if (is_multisite()) {
            // Agregar filtros y acciones específicas para multisite
            add_filter('network_admin_plugin_action_links', array($this, 'network_admin_links'), 10, 2);
            add_action('network_admin_menu', array($this, 'network_admin_menu'));
            add_action('wpmu_new_blog', array($this, 'new_site_setup'), 10, 6);
            add_action('delete_blog', array($this, 'site_deleted'), 10, 2);

            // Agregar soporte para opciones de tema a nivel de red
            add_action('after_setup_theme', array($this, 'network_theme_options'));
        }
    }

    /**
     * Configurar un nuevo sitio cuando se crea en la red
     * 
     * @param int $blog_id ID del nuevo blog
     * @param int $user_id ID del usuario que creó el blog
     * @param string $domain Dominio del nuevo blog
     * @param string $path Ruta del nuevo blog
     * @param int $site_id ID del sitio multisitio
     * @param array $meta Metadatos del nuevo blog
     */
    public function new_site_setup($blog_id, $user_id, $domain, $path, $site_id, $meta)
    {
        // Cambiar al blog recién creado
        switch_to_blog($blog_id);

        // Configurar opciones específicas del tema para este sitio
        $this->setup_theme_defaults();

        // Volver al blog actual
        restore_current_blog();
    }

    /**
     * Configurar opciones predeterminadas del tema
     */
    private function setup_theme_defaults()
    {
        // Ejemplo: configurar un logotipo predeterminado
        if (!get_theme_mod('custom_logo')) {
            set_theme_mod('custom_logo', '');
        }

        // Ejemplo: configurar colores predeterminados
        if (!get_theme_mod('primary_color')) {
            set_theme_mod('primary_color', '#0d6efd');
        }

        // Otras opciones predeterminadas del tema
    }

    /**
     * Manejar la eliminación de un sitio
     * 
     * @param int $blog_id ID del blog eliminado
     * @param bool $drop Indica si se eliminan las tablas
     */
    public function site_deleted($blog_id, $drop)
    {
        if ($drop) {
            // Limpiar datos específicos del tema cuando se elimina un sitio
            // Por ejemplo, eliminar directorios de caché personalizados
        }
    }

    /**
     * Agregar enlaces de administración en la red
     * 
     * @param array $links Enlaces actuales
     * @param string $file Archivo del plugin
     * @return array Enlaces modificados
     */
    public function network_admin_links($links, $file)
    {
        // Agregar enlaces personalizados para la administración de la red
        return $links;
    }

    /**
     * Agregar elementos al menú de administración de la red
     */
    public function network_admin_menu()
    {
        // Agregar páginas de administración a nivel de red
        add_menu_page(
            __('Configuración del Tema', 'wp-tailwind-blocks'),
            __('Configuración del Tema', 'wp-tailwind-blocks'),
            'manage_network_options',
            'wptbt-network-settings',
            array($this, 'network_settings_page'),
            'dashicons-admin-appearance',
            30
        );
    }

    /**
     * Página de configuración a nivel de red
     */
    public function network_settings_page()
    {
?>
        <div class="wrap">
            <h1><?php echo esc_html__('Configuración del Tema en Red', 'wp-tailwind-blocks'); ?></h1>
            <form method="post" action="edit.php?action=wptbt_update_network_options">
                <?php wp_nonce_field('wptbt_network_options'); ?>

                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><?php echo esc_html__('Opción Global 1', 'wp-tailwind-blocks'); ?></th>
                        <td>
                            <input name="wptbt_network_option1" type="text" value="<?php echo esc_attr(get_site_option('wptbt_network_option1', '')); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo esc_html__('Opción Global 2', 'wp-tailwind-blocks'); ?></th>
                        <td>
                            <input name="wptbt_network_option2" type="text" value="<?php echo esc_attr(get_site_option('wptbt_network_option2', '')); ?>" class="regular-text">
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr__('Guardar Cambios', 'wp-tailwind-blocks'); ?>">
                </p>
            </form>
        </div>
<?php
    }

    /**
     * Configurar opciones del tema a nivel de red
     */
    public function network_theme_options()
    {
        // Registrar acción para guardar opciones de red
        if (is_admin() && isset($_GET['action']) && $_GET['action'] == 'wptbt_update_network_options') {
            // Verificar nonce
            check_admin_referer('wptbt_network_options');

            // Guardar opciones
            if (isset($_POST['wptbt_network_option1'])) {
                update_site_option('wptbt_network_option1', sanitize_text_field($_POST['wptbt_network_option1']));
            }
            if (isset($_POST['wptbt_network_option2'])) {
                update_site_option('wptbt_network_option2', sanitize_text_field($_POST['wptbt_network_option2']));
            }

            // Redirigir de vuelta a la página de configuración
            wp_redirect(network_admin_url('admin.php?page=wptbt-network-settings&updated=true'));
            exit;
        }
    }
}
