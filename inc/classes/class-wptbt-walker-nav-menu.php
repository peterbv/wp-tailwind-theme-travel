<?php

/**
 * Clase Walker personalizada para menús con TailwindCSS
 */
class WPTBT_Walker_Nav_Menu extends Walker_Nav_Menu
{
    /**
     * Inicia el elemento del nivel
     */
    public function start_lvl(&$output, $depth = 0, $args = array())
    {
        $indent = str_repeat("\t", $depth);
        $sub_menu_classes = 'sub-menu pl-4 lg:absolute lg:left-0 lg:w-48 lg:pl-0 lg:bg-white lg:shadow-lg lg:rounded-md lg:py-2 lg:z-10 lg:hidden group-hover:lg:block';

        $output .= "\n$indent<ul class=\"$sub_menu_classes\">\n";
    }

    /**
     * Inicia el elemento
     */
    public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0)
    {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $classes = empty($item->classes) ? array() : (array) $item->classes;

        // Agregar clases de Tailwind
        if ($depth === 0) {
            $classes[] = 'group relative';
        } else {
            $classes[] = '';
        }

        // Verificar si el elemento tiene hijos
        $has_children = in_array('menu-item-has-children', $classes);

        // Clase para elementos activos
        if (in_array('current-menu-item', $classes)) {
            $classes[] = 'text-primary';
        }

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';

        $output .= $indent . '<li' . $id . $class_names . '>';

        $atts = array();
        $atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target) ? $item->target : '';
        $atts['rel']    = !empty($item->xfn) ? $item->xfn : '';
        $atts['href']   = !empty($item->url) ? $item->url : '';

        // Clases específicas para el enlace según el nivel
        if ($depth === 0) {
            $atts['class'] = 'block py-2 lg:py-1 px-4 lg:px-2 text-gray-700 hover:text-primary transition duration-200';
        } else {
            $atts['class'] = 'block py-2 px-4 text-gray-700 hover:text-primary hover:bg-gray-50 transition duration-200';
        }

        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $title = apply_filters('the_title', $item->title, $item->ID);
        $title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);

        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . $title . $args->link_after;


        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}
