<?php


// Verificar que la clase base exista antes de extenderla
if (class_exists('WP_Customize_Control')) {
    /**
     * Clase para crear separadores en el Customizer
     */
    class WPTBT_Separator_Control extends WP_Customize_Control
    {
        /**
         * Tipo de control
         * @var string
         */
        public $type = 'separator';

        /**
         * Renderizar el contenido del control
         */
        public function render_content()
        {
?>
            <div style="margin: 12px -12px; border-bottom: 1px solid #ddd; padding: 4px 0;"></div>
            <?php if (!empty($this->label)) : ?>
                <span class="customize-control-title" style="font-weight: 600; margin-top: 16px; display: block;">
                    <?php echo esc_html($this->label); ?>
                </span>
            <?php endif; ?>
            <?php if (!empty($this->description)) : ?>
                <span class="description customize-control-description">
                    <?php echo esc_html($this->description); ?>
                </span>
            <?php endif; ?>
<?php
        }
    }
}
