<?php

/**
 * Clase para manejar la carga de assets compilados por Vite
 */
class WPTBT_Vite_Assets
{
    /**
     * Ruta al archivo manifest.json generado por Vite
     */
    private $manifest_path;

    /**
     * URL base para los assets en desarrollo
     */
    private $dev_url;

    /**
     * Modo de desarrollo activo
     */
    private $is_dev_mode;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->manifest_path = WPTBT_DIR . 'assets/.vite/manifest.json';
        $this->dev_url = 'http://localhost:3000';

        // Puedes usar una constante en wp-config.php para controlar esto
        $this->is_dev_mode = defined('WP_ENV') && WP_ENV === 'development';

        // Alternativa: detectar automáticamente si el servidor Vite está disponible
        if ($this->is_dev_mode) {
            $this->is_dev_mode = $this->is_vite_dev_server_running();
        }
    }

    private function is_vite_dev_server_running()
    {
        $handle = curl_init($this->dev_url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_NOBODY, true);
        curl_exec($handle);
        $error = curl_errno($handle);
        curl_close($handle);
        return !$error;
    }
    /**
     * Inicializar
     */
    public function init()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_editor_assets'));

        if ($this->is_dev_mode) {
            add_action('wp_head', array($this, 'inject_dev_scripts'));
        }
    }

    /**
     * Obtener la URL del asset
     */
    private function get_asset_url($asset_name)
    {
        if ($this->is_dev_mode) {
            // En desarrollo, apuntar al servidor de Vite
            return $this->dev_url . '/' . $asset_name;
        } else {
            // En producción, usar el manifest para obtener la ruta hasheada
            $manifest = $this->get_manifest();

            if (isset($manifest[$asset_name])) {
                return WPTBT_URI . 'assets/' . $manifest[$asset_name]['file'];
            }

            // Si no encontramos el asset en el manifest, devolver la ruta original
            return WPTBT_URI . 'assets/' . $asset_name;
        }
    }

    /**
     * Obtener el manifest de Vite
     */
    private function get_manifest()
    {
        static $manifest = null;

        if ($manifest === null && file_exists($this->manifest_path)) {
            $manifest_content = file_get_contents($this->manifest_path);
            $manifest = json_decode($manifest_content, true);
        }

        return $manifest ?: array();
    }

    /**
     * Registrar y encolar assets para el frontend
     */
    public function enqueue_frontend_assets()
    {
        // Encolar CSS principal
        wp_enqueue_style(
            'wp-tailwind-blocks-style',
            $this->get_asset_url('src/public/sass/main.scss'),
            array(),
            WPTBT_VERSION
        );

        // Encolar JS principal
        wp_enqueue_script(
            'wp-tailwind-blocks-script',
            $this->get_asset_url('src/public/js/main.js'),
            array(),
            WPTBT_VERSION,
            true
        );

        // Si estamos en producción, también necesitamos cargar los chunks
        if (!$this->is_dev_mode) {
            $manifest = $this->get_manifest();

            // Buscar y cargar los CSS importados
            if (isset($manifest['src/public/sass/main.scss'])) {
                if (isset($manifest['src/public/sass/main.scss']['css'])) {
                    foreach ($manifest['src/public/sass/main.scss']['css'] as $css_file) {
                        wp_enqueue_style(
                            'wp-tailwind-blocks-' . basename($css_file, '.css'),
                            WPTBT_URI . 'assets/' . $css_file,
                            array(),
                            WPTBT_VERSION
                        );
                    }
                }
            }

            // Buscar y cargar los JS importados
            if (isset($manifest['src/public/js/main.js'])) {
                if (isset($manifest['src/public/js/main.js']['imports'])) {
                    foreach ($manifest['src/public/js/main.js']['imports'] as $import) {
                        wp_enqueue_script(
                            'wp-tailwind-blocks-' . basename($import, '.js'),
                            WPTBT_URI . 'assets/public' . $manifest[$import]['file'],
                            array(),
                            WPTBT_VERSION,
                            true
                        );
                    }
                }
            }
        }
    }

    /**
     * Registrar y encolar assets para el editor
     */
    public function enqueue_editor_assets()
    {
        // Encolar CSS del editor
        wp_enqueue_style(
            'wp-tailwind-blocks-editor-style',
            $this->get_asset_url('src/admin/sass/editor.scss'),
            array(),
            WPTBT_VERSION
        );

        // Encolar JS de bloques
        /*  wp_enqueue_script(
            'wp-tailwind-blocks-editor-script',
            $this->get_asset_url('src/admin/blocks/index.js'),
            array(
                'wp-blocks',
                'wp-dom-ready',
                'wp-edit-post',
                'wp-element',
                'wp-i18n',
                'wp-components'
            ),
            WPTBT_VERSION,
            true
        ); */

        // Si estamos en producción, también necesitamos cargar los chunks
        if (!$this->is_dev_mode) {
            $manifest = $this->get_manifest();

            // Buscar y cargar los CSS importados
            if (isset($manifest['src/admin/sass/editor.scss'])) {
                if (isset($manifest['src/admin/sass/editor.scss']['css'])) {
                    foreach ($manifest['src/admin/sass/editor.scss']['css'] as $css_file) {
                        wp_enqueue_style(
                            'wp-tailwind-blocks-editor-' . basename($css_file, '.css'),
                            WPTBT_URI . 'assets/' . $css_file,
                            array(),
                            WPTBT_VERSION
                        );
                    }
                }
            }

            // Buscar y cargar los JS importados
            if (isset($manifest['src/admin/blocks/index.js'])) {
                if (isset($manifest['src/admin/blocks/index.js']['imports'])) {
                    foreach ($manifest['src/admin/blocks/index.js']['imports'] as $import) {
                        wp_enqueue_script(
                            'wp-tailwind-blocks-editor-' . basename($import, '.js'),
                            WPTBT_URI . 'assets/' . $manifest[$import]['file'],
                            array(),
                            WPTBT_VERSION,
                            true
                        );
                    }
                }
            }
        }
    }

    /**
     * Inyectar scripts de desarrollo de Vite
     */
    public function inject_dev_scripts()
    {
        if ($this->is_dev_mode) {
            echo '<script type="module">
                import RefreshRuntime from "' . esc_url($this->dev_url) . '/@react-refresh"
                RefreshRuntime.injectIntoGlobalHook(window)
                window.$RefreshReg$ = () => {}
                window.$RefreshSig$ = () => (type) => type
                window.__vite_plugin_react_preamble_installed__ = true
            </script>';

            echo '<script type="module" src="' . esc_url($this->dev_url) . '/@vite/client"></script>';

            // Cargar scripts principales desde el servidor de desarrollo
            echo '<script type="module" src="' . esc_url($this->dev_url) . '/src/public/js/main.js"></script>';

            // Si estamos en el editor, cargar también el JS del editor
            if (is_admin() && function_exists('get_current_screen')) {
                $screen = get_current_screen();
                if ($screen && $screen->is_block_editor()) {
                    echo '<script type="module" src="' . esc_url($this->dev_url) . '/src/admin/blocks/index.jsx"></script>';
                    echo '<script type="module" src="' . esc_url($this->dev_url) . '/src/admin/js/editor.js"></script>';
                }
            }
        }
    }
}
