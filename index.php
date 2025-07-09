<?php

/**
 * La plantilla principal del tema
 *
 * Esta es la plantilla más genérica en la jerarquía de WordPress.
 */

get_header();
?>

<main id="primary" class="site-main py-12">
    <div class="container mx-auto px-4">

        <?php if (is_home() && !is_front_page() && get_option('page_for_posts')) : ?>
            <header class="page-header mb-12">
                <h1 class="page-title text-4xl font-bold mb-4">
                    <?php single_post_title(); ?>
                </h1>
                <?php
                $description = get_the_archive_description();
                if ($description) :
                ?>
                    <div class="archive-description prose max-w-none">
                        <?php echo wp_kses_post($description); ?>
                    </div>
                <?php endif; ?>
            </header>
        <?php endif; ?>

        <?php if (have_posts()) : ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                while (have_posts()) :
                    the_post();

                    // Incluir la plantilla parcial para cada artículo
                    get_template_part('template-parts/content', get_post_type());

                endwhile;
                ?>
            </div>

            <div class="pagination-container mt-12">
                <?php
                the_posts_pagination(array(
                    'mid_size'  => 2,
                    'prev_text' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> ' . esc_html__('Anterior', 'wp-tailwind-blocks'),
                    'next_text' => esc_html__('Siguiente', 'wp-tailwind-blocks') . ' <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>',
                    'class'     => 'flex justify-center',
                ));
                ?>
            </div>

        <?php else : ?>

            <?php get_template_part('template-parts/content', 'none'); ?>

        <?php endif; ?>

    </div>
</main><!-- #main -->

<?php if (is_active_sidebar('sidebar-1')) : ?>
    <aside id="secondary" class="widget-area">
        <?php dynamic_sidebar('sidebar-1'); ?>
    </aside><!-- #secondary -->
<?php endif; ?>

<?php
get_footer();
