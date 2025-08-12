<?php

/**
 * Template part para mostrar el contenido de las páginas
 *
 * @package WP_Tailwind_Theme
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('bg-white rounded-2xl shadow-lg overflow-hidden mb-12 border border-gray-100/50 hover:shadow-xl transition-shadow duration-500'); ?>>
    <div class="p-10 md:p-12">
        <header class="entry-header mb-10">
            <?php the_title('<h1 class="entry-title text-4xl md:text-5xl lg:text-6xl fancy-text font-bold bg-gradient-to-r from-slate-800 via-gray-700 to-slate-800 bg-clip-text text-transparent mb-6 leading-tight">', '</h1>'); ?>
            
            <!-- Línea decorativa -->
            <div class="w-24 h-1 bg-gradient-to-r from-blue-500 to-teal-500 rounded-full mb-4"></div>
            
            <!-- Metainformación de la página si está disponible -->
            <?php if (get_the_excerpt()) : ?>
                <p class="text-xl text-gray-600 font-light leading-relaxed"><?php echo get_the_excerpt(); ?></p>
            <?php endif; ?>
        </header><!-- .entry-header -->

        <?php if (has_post_thumbnail()) : ?>
            <div class="post-thumbnail mb-12 overflow-hidden rounded-2xl shadow-md group">
                <div class="relative overflow-hidden">
                    <?php the_post_thumbnail('large', array('class' => 'w-full h-auto group-hover:scale-110 transition-transform duration-1000 ease-out')); ?>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                </div>
            </div><!-- .post-thumbnail -->
        <?php endif; ?>

        <div class="entry-content prose prose-lg max-w-none text-gray-700 
                    prose-headings:text-slate-800 prose-headings:font-bold
                    prose-h2:text-3xl prose-h2:mt-12 prose-h2:mb-6
                    prose-h3:text-2xl prose-h3:mt-8 prose-h3:mb-4
                    prose-p:leading-relaxed prose-p:mb-6
                    prose-a:text-blue-600 prose-a:font-medium prose-a:no-underline hover:prose-a:underline hover:prose-a:text-blue-700
                    prose-strong:text-slate-800 prose-strong:font-semibold
                    prose-ul:space-y-2 prose-li:text-gray-700
                    prose-blockquote:border-l-4 prose-blockquote:border-blue-500 prose-blockquote:bg-blue-50/50 prose-blockquote:py-4 prose-blockquote:px-6 prose-blockquote:rounded-r-lg prose-blockquote:font-medium prose-blockquote:text-slate-700"
            <?php
            the_content();

            wp_link_pages(
                array(
                    'before' => '<div class="page-links mt-12 pt-8 border-t border-gray-200/60 bg-gradient-to-r from-gray-50/50 to-blue-50/30 rounded-lg p-6">
                                    <span class="text-lg font-semibold text-slate-700 mb-4 block">' . esc_html__('Páginas:', 'wp-tailwind-theme') . '</span>
                                    <div class="flex flex-wrap gap-3">',
                    'after'  => '</div></div>',
                    'link_before' => '<span class="inline-flex items-center px-4 py-2 text-sm font-medium bg-white border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700 transition-all duration-300 shadow-sm hover:shadow-md">',
                    'link_after' => '</span>',
                )
            );
            ?>
        </div><!-- .entry-content -->

        <?php if (get_edit_post_link()) : ?>
            <footer class="entry-footer mt-12 pt-6 border-t border-gray-100/60 bg-gradient-to-r from-gray-50/30 to-transparent rounded-lg">
                <?php
                edit_post_link(
                    sprintf(
                        wp_kses(
                            /* translators: %s: Nombre de la página */
                            __('Editar <span class="screen-reader-text">%s</span>', 'wp-tailwind-theme'),
                            array(
                                'span' => array(
                                    'class' => array(),
                                ),
                            )
                        ),
                        get_the_title()
                    ),
                    '<span class="edit-link inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gradient-to-r hover:from-blue-50 hover:to-teal-50 hover:border-blue-300 hover:text-blue-700 transition-all duration-300 shadow-sm hover:shadow-md group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 group-hover:text-blue-600 transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>',
                    '</span>'
                );
                ?>
            </footer><!-- .entry-footer -->
        <?php endif; ?>
    </div>
</article><!-- #post-<?php the_ID(); ?> -->