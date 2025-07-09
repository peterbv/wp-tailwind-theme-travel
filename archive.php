<?php
/**
 * Template para mostrar servicios individuales
 * Versión optimizada con formulario de reserva integrado y SEO mejorado
 *
 * @package WPTBT
 */

get_header();

// Obtener metadatos del servicio
$precio = get_post_meta(get_the_ID(), '_wptbt_service_price', true);
$precio_duracion1 = get_post_meta(get_the_ID(), '_wptbt_service_duration1', true);
$precio_valor1 = get_post_meta(get_the_ID(), '_wptbt_service_price1', true);
$precio_duracion2 = get_post_meta(get_the_ID(), '_wptbt_service_duration2', true);
$precio_valor2 = get_post_meta(get_the_ID(), '_wptbt_service_price2', true);
$horarios = get_post_meta(get_the_ID(), '_wptbt_service_hours', true);

// Variables para schema.org
$organization_name = get_bloginfo('name');
$organization_url = get_home_url();
$organization_logo = get_theme_mod('custom_logo') ? wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'full') : '';
$service_image = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'full') : '';
$service_description = get_the_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 30);
$service_url = get_permalink();
?>

<!-- Schema.org JSON-LD para servicio -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Service",
  "name": "<?php echo esc_js(get_the_title()); ?>",
  "url": "<?php echo esc_url($service_url); ?>",
  "description": "<?php echo esc_js(wp_strip_all_tags($service_description)); ?>",
  <?php if ($service_image) : ?>
  "image": "<?php echo esc_url($service_image); ?>",
  <?php endif; ?>
  "provider": {
    "@type": "Organization",
    "name": "<?php echo esc_js($organization_name); ?>",
    "url": "<?php echo esc_url($organization_url); ?>"
    <?php if ($organization_logo) : ?>,
    "logo": "<?php echo esc_url($organization_logo); ?>"
    <?php endif; ?>
  },
  <?php if (!empty($precio_valor1) || !empty($precio)) : ?>
  "offers": {
    "@type": "Offer",
    "price": "<?php echo esc_js(!empty($precio_valor1) ? $precio_valor1 : $precio); ?>",
    "priceCurrency": "PEN"
  },
  <?php endif; ?>
  <?php if (!empty($horarios) && is_array($horarios)) : ?>
  "availableChannel": {
    "@type": "ServiceChannel",
    "serviceUrl": "<?php echo esc_url($service_url); ?>#reservar-servicio",
    "availableLanguage": "es",
    "processingTime": {
      "@type": "Duration",
      "name": "<?php echo esc_js(!empty($precio_duracion1) ? $precio_duracion1 : ''); ?>"
    }
  },
  <?php endif; ?>
  "serviceType": "<?php 
    $categories = get_the_category();
    if (!empty($categories)) {
      $category_names = array();
      foreach ($categories as $category) {
        $category_names[] = $category->name;
      }
      echo esc_js(implode(', ', $category_names));
    } else {
      echo 'Spa Service';
    }
  ?>"
}
</script>

<!-- Schema.org para breadcrumbs -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "<?php echo esc_js(get_bloginfo('name')); ?>",
      "item": "<?php echo esc_url(home_url()); ?>"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "<?php echo esc_js(__('Services', 'wptbt-services')); ?>",
      "item": "<?php echo esc_url(get_post_type_archive_link('servicio')); ?>"
    },
    {
      "@type": "ListItem",
      "position": 3,
      "name": "<?php echo esc_js(get_the_title()); ?>",
      "item": "<?php echo esc_url(get_permalink()); ?>"
    }
  ]
}
</script>

<main id="primary" class="site-main" itemscope itemtype="https://schema.org/WebPage">

    <?php while (have_posts()) : the_post(); ?>

        <!-- Navegación de migas de pan para SEO -->
        <nav class="breadcrumbs container mx-auto px-4 py-4" aria-label="<?php esc_attr_e('Breadcrumb', 'wptbt-services'); ?>">
            <ol class="flex flex-wrap items-center text-sm text-gray-500">
                <li class="flex items-center">
                    <a href="<?php echo esc_url(home_url()); ?>" class="hover:text-spa-accent transition duration-300">
                        <?php echo esc_html(get_bloginfo('name')); ?>
                    </a>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </li>
                <li class="flex items-center">
                    <a href="<?php echo esc_url(get_post_type_archive_link('servicio')); ?>" class="hover:text-spa-accent transition duration-300">
                        <?php echo esc_html__('Services', 'wptbt-services'); ?>
                    </a>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </li>
                <li aria-current="page">
                    <span class="text-spa-accent font-medium"><?php the_title(); ?></span>
                </li>
            </ol>
        </nav>

        <!-- Encabezado del servicio con fondo de imagen si existe -->
        <section class="service-header relative bg-spa-secondary overflow-hidden <?php echo has_post_thumbnail() ? 'pt-32 pb-64' : 'py-24'; ?>" itemprop="mainEntity" itemscope itemtype="https://schema.org/Service">

            <?php if (has_post_thumbnail()) : 
                // Obtener datos de la imagen
                $image_id = get_post_thumbnail_id();
                $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                $image_alt = empty($image_alt) ? get_the_title() . ' - ' . $organization_name : $image_alt;
            ?>
                <div class="absolute inset-0 z-0">
                    <?php the_post_thumbnail('full', array(
                        'class' => 'w-full h-full object-cover',
                        'alt' => esc_attr($image_alt),
                        'itemprop' => 'image'
                    )); ?>
                    <div class="absolute inset-0 bg-spa-primary opacity-50"></div>
                </div>
                <meta itemprop="image" content="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'full')); ?>">
            <?php endif; ?>

            <div class="container mx-auto px-4 relative z-20">
                <a href="<?php echo esc_url(get_post_type_archive_link('servicio')); ?>" class="inline-flex items-center text-white bg-spa-primary bg-opacity-60 hover:bg-opacity-80 px-4 py-2 rounded-full mb-6 transition duration-300" aria-label="<?php esc_attr_e('Back to all services', 'wptbt-services'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <?php echo esc_html__('Back to Services', 'wptbt-services'); ?>
                </a>

                <div class="text-center max-w-3xl mx-auto">
                    <h1 class="text-4xl md:text-5xl fancy-text font-medium mb-4 <?php echo has_post_thumbnail() ? 'text-white' : 'text-spa-primary'; ?> reveal-item opacity-0 translate-y-8" itemprop="name">
                        <?php the_title(); ?>
                    </h1>

                    <?php
                    // Mostrar precios
                    if (!empty($precio_duracion1) && !empty($precio_valor1) || !empty($precio_duracion2) && !empty($precio_valor2) || !empty($precio)) :
                    ?>
                        <div class="flex flex-wrap justify-center gap-3 mb-8 reveal-item opacity-0 translate-y-8">
                            <?php if (!empty($precio_duracion1) && !empty($precio_valor1)) : ?>
                                <div class="inline-block px-4 py-2 rounded-full <?php echo has_post_thumbnail() ? 'bg-white text-spa-primary' : 'bg-spa-primary text-white'; ?>">
                                    <span class="font-medium" itemprop="serviceType"><?php echo esc_html($precio_duracion1); ?></span>
                                    <span class="text-spa-accent font-bold" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                                        <meta itemprop="priceCurrency" content="PEN">
                                        <span itemprop="price"><?php echo esc_html($precio_valor1); ?></span>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($precio_duracion2) && !empty($precio_valor2)) : ?>
                                <div class="inline-block px-4 py-2 rounded-full <?php echo has_post_thumbnail() ? 'bg-white text-spa-primary' : 'bg-spa-primary text-white'; ?>">
                                    <span class="font-medium" itemprop="serviceType"><?php echo esc_html($precio_duracion2); ?></span>
                                    <span class="text-spa-accent font-bold" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                                        <meta itemprop="priceCurrency" content="PEN">
                                        <span itemprop="price"><?php echo esc_html($precio_valor2); ?></span>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <?php if (empty($precio_duracion1) && empty($precio_valor1) && empty($precio_duracion2) && empty($precio_valor2) && !empty($precio)) : ?>
                                <div class="inline-block px-4 py-2 rounded-full <?php echo has_post_thumbnail() ? 'bg-white text-spa-primary' : 'bg-spa-primary text-white'; ?>">
                                    <span class="font-medium"><?php esc_html_e('Price from:', 'wptbt-services'); ?></span>
                                    <span class="text-spa-accent font-bold" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                                        <meta itemprop="priceCurrency" content="PEN">
                                        <span itemprop="price"><?php echo esc_html($precio); ?></span>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (has_excerpt()) : ?>
                        <div class="prose max-w-2xl mx-auto <?php echo has_post_thumbnail() ? 'text-white' : 'text-gray-600'; ?> reveal-item opacity-0 translate-y-8" itemprop="description">
                            <?php the_excerpt(); ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Provider information for schema.org -->
                    <div itemprop="provider" itemscope itemtype="https://schema.org/Organization" class="hidden">
                        <meta itemprop="name" content="<?php echo esc_attr($organization_name); ?>">
                        <meta itemprop="url" content="<?php echo esc_url($organization_url); ?>">
                        <?php if ($organization_logo) : ?>
                            <meta itemprop="logo" content="<?php echo esc_url($organization_logo); ?>">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- Borde ondulado en la parte inferior -->
            <div class="absolute bottom-0 left-0 right-0 z-10" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100" preserveAspectRatio="none" class="w-full h-20 md:h-24 lg:h-28 hidden md:block">
                    <path fill="white" d="M0,85.7L20,75.4C40,65.1,80,44.6,120,33.9C160,23.2,200,22.4,240,28.6C280,34.8,320,48.1,360,50C400,51.9,440,42.5,480,39.8C520,37.2,560,41.3,600,48.3C640,55.2,680,65,720,62.3C760,59.5,800,44.2,840,41.9C880,39.7,920,50.5,960,54.3C1000,58.2,1040,55.2,1080,58.2C1120,61.1,1160,69.9,1200,73.3C1240,76.8,1280,74.9,1320,70.9C1360,67,1400,61,1420,58L1440,55L1440,100L1420,100C1400,100,1360,100,1320,100C1280,100,1240,100,1200,100C1160,100,1120,100,1080,100C1040,100,1000,100,960,100C920,100,880,100,840,100C800,100,760,100,720,100C680,100,640,100,600,100C560,100,520,100,480,100C440,100,400,100,360,100C320,100,280,100,240,100C200,100,160,100,120,100C80,100,40,100,20,100L0,100Z"></path>
                </svg>

                <!-- Versión simplificada para móviles -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 60" preserveAspectRatio="none" class="w-full h-10 md:hidden">
                    <path fill="white" d="M0,40L50,35C100,30,200,20,300,20C400,20,500,30,550,35L600,40L600,60L550,60C500,60,400,60,300,60C200,60,100,60,50,60L0,60Z"></path>
                </svg>
            </div>
        </section>

        <!-- Detalles del servicio -->
        <section class="service-details py-16 bg-white relative" aria-label="<?php esc_attr_e('Service Details', 'wptbt-services'); ?>">
            <!-- Si hay imagen destacada, crear un efecto de superposición -->
            <?php if (has_post_thumbnail()) : ?>
                <div class="container mx-auto px-4 -mt-48 mb-16 relative z-20">
                    <div class="bg-white rounded-lg shadow-xl p-8 max-w-4xl mx-auto">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Tarjeta de información rápida -->
                            <div class="md:col-span-1 bg-spa-secondary rounded-lg p-6 reveal-item opacity-0 translate-y-8">
                                <h2 class="text-xl fancy-text font-medium mb-4 text-spa-primary">
                                    <?php esc_html_e('Quick Information', 'wptbt-services'); ?>
                                </h2>

                                <ul class="space-y-4">
                                    <?php if (!empty($precio_duracion1) && !empty($precio_valor1)) : ?>
                                        <li class="flex items-start">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-spa-accent mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div>
                                                <span class="block text-sm text-gray-500"><?php esc_html_e('Available hours:', 'wptbt-services'); ?></span>
                                                <div class="flex flex-wrap gap-2 mt-1">
                                                    <?php if (!empty($horarios) && is_array($horarios)) : ?>
                                                        <?php foreach ($horarios as $hora) : ?>
                                                            <span class="bg-white px-2 py-1 text-xs rounded font-medium text-spa-primary">
                                                                <?php echo esc_html($hora); ?>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endif; ?>
                                </ul>

                                <!-- Botón de reserva que abre el modal/scroll hacia el formulario -->
                                <a href="#reservar-servicio" class="mt-6 inline-block w-full px-4 py-3 bg-spa-accent text-white text-center font-medium rounded-sm transition-all duration-300 hover:bg-opacity-90 hover:shadow-md"
                                   title="<?php echo esc_attr(sprintf(__('Book %s now', 'wptbt-services'), get_the_title())); ?>">
                                    <?php esc_html_e('Book Now', 'wptbt-services'); ?>
                                </a>
                            </div>

                            <!-- Contenido principal del servicio -->
                            <div class="md:col-span-2 reveal-item opacity-0 translate-y-8">
                                <div class="prose max-w-none" itemprop="description">
                                    <?php the_content(); ?>
                                </div>

                                <?php
                                // Obtener categorías o etiquetas relacionadas si existen
                                $categories = get_the_category();
                                if (!empty($categories)) :
                                ?>
                                    <div class="mt-8 pt-6 border-t border-gray-200">
                                        <h3 class="text-lg font-medium text-spa-primary mb-3"><?php esc_html_e('Categories', 'wptbt-services'); ?></h3>
                                        <div class="flex flex-wrap gap-2">
                                            <?php foreach ($categories as $category) : ?>
                                                <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="px-3 py-1 bg-spa-secondary rounded-full text-sm text-spa-primary hover:bg-spa-accent hover:text-white transition duration-300"
                                                   title="<?php echo esc_attr(sprintf(__('View all %s services', 'wptbt-services'), $category->name)); ?>">
                                                    <?php echo esc_html($category->name); ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <!-- Si no hay imagen destacada, mostrar el contenido normal -->
                <div class="container mx-auto px-4">
                    <div class="max-w-4xl mx-auto">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <!-- Tarjeta de información rápida -->
                            <div class="md:col-span-1 bg-spa-secondary rounded-lg p-6 reveal-item opacity-0 translate-y-8">
                                <h2 class="text-xl fancy-text font-medium mb-4 text-spa-primary">
                                    <?php esc_html_e('Quick Information', 'wptbt-services'); ?>
                                </h2>

                                <ul class="space-y-4">
                                    <?php if (!empty($precio_duracion1) && !empty($precio_valor1)) : ?>
                                        <li class="flex items-start">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-spa-accent mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div>
                                                <span class="block text-sm text-gray-500"><?php esc_html_e('Duration:', 'wptbt-services'); ?></span>
                                                <span class="font-medium"><?php echo esc_html($precio_duracion1); ?> <?php esc_html_e('minutes', 'wptbt-services'); ?></span>
                                            </div>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (!empty($precio_valor1) || !empty($precio)) : ?>
                                        <li class="flex items-start">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-spa-accent mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div>
                                                <span class="block text-sm text-gray-500"><?php esc_html_e('Price from:', 'wptbt-services'); ?></span>
                                                <span class="font-medium"><?php echo !empty($precio_valor1) ? esc_html($precio_valor1) : esc_html($precio); ?></span>
                                            </div>
                                        </li>
                                    <?php endif; ?>

                                    <li class="flex items-start">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-spa-accent mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <div>
                                            <span class="block text-sm text-gray-500"><?php esc_html_e('Benefits:', 'wptbt-services'); ?></span>
                                            <span class="font-medium"><?php esc_html_e('Relaxing environment, premium products', 'wptbt-services'); ?></span>
                                        </div>
                                    </li>

                                    <?php if (!empty($horarios) && is_array($horarios)) : ?>
                                        <li class="flex items-start">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-spa-accent mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div>
                                                <span class="block text-sm text-gray-500"><?php esc_html_e('Available hours:', 'wptbt-services'); ?></span>
                                                <div class="flex flex-wrap gap-2 mt-1">
                                                    <?php foreach ($horarios as $hora) : ?>
                                                        <span class="bg-white px-2 py-1 text-xs rounded font-medium text-spa-primary">
                                                            <?php echo esc_html($hora); ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endif; ?>
                                </ul>

                                <!-- Botón de reserva -->
                                <a href="#reservar-servicio" class="mt-6 inline-block w-full px-4 py-3 bg-spa-accent text-white text-center font-medium rounded-sm transition-all duration-300 hover:bg-opacity-90 hover:shadow-md"
                                   title="<?php echo esc_attr(sprintf(__('Book %s now', 'wptbt-services'), get_the_title())); ?>">
                                    <?php esc_html_e('Book Now', 'wptbt-services'); ?>
                                </a>
                            </div>

                            <!-- Contenido principal del servicio -->
                            <div class="md:col-span-2 reveal-item opacity-0 translate-y-8">
                                <div class="prose max-w-none" itemprop="description">
                                    <?php the_content(); ?>
                                </div>

                                <?php
                                // Obtener categorías o etiquetas relacionadas si existen
                                $categories = get_the_category();
                                if (!empty($categories)) :
                                ?>
                                    <div class="mt-8 pt-6 border-t border-gray-200">
                                        <h3 class="text-lg font-medium text-spa-primary mb-3"><?php esc_html_e('Categories', 'wptbt-services'); ?></h3>
                                        <div class="flex flex-wrap gap-2">
                                            <?php foreach ($categories as $category) : ?>
                                                <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="px-3 py-1 bg-spa-secondary rounded-full text-sm text-spa-primary hover:bg-spa-accent hover:text-white transition duration-300"
                                                   title="<?php echo esc_attr(sprintf(__('View all %s services', 'wptbt-services'), $category->name)); ?>">
                                                    <?php echo esc_html($category->name); ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <!-- Servicios relacionados -->
        <section class="related-services py-16 bg-spa-secondary" aria-label="<?php esc_attr_e('Related Services', 'wptbt-services'); ?>">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl fancy-text font-medium mb-8 text-center text-spa-primary reveal-item opacity-0 translate-y-8">
                    <?php esc_html_e('Other services that may interest you', 'wptbt-services'); ?>
                </h2>

                <!-- Schema.org JSON-LD para servicios relacionados -->
                <script type="application/ld+json">
                {
                  "@context": "https://schema.org",
                  "@type": "ItemList",
                  "itemListElement": [
                    <?php
                    // Obtener servicios relacionados por categoría
                    $current_id = get_the_ID();
                    $categories = get_the_category();
                    $category_ids = array();

                    if (!empty($categories)) {
                        foreach ($categories as $category) {
                            $category_ids[] = $category->term_id;
                        }
                    }

                    $args = array(
                        'post_type' => 'servicio',
                        'posts_per_page' => 3,
                        'post__not_in' => array($current_id),
                        'orderby' => 'rand'
                    );

                    if (!empty($category_ids)) {
                        $args['category__in'] = $category_ids;
                    }

                    $related_services = new WP_Query($args);
                    
                    if ($related_services->have_posts()) :
                        $position = 1;
                        while ($related_services->have_posts()) : $related_services->the_post();
                            if ($position > 1) echo ',';
                            echo '{
                              "@type": "ListItem",
                              "position": ' . $position . ',
                              "item": {
                                "@type": "Service",
                                "name": "' . esc_js(get_the_title()) . '",
                                "url": "' . esc_url(get_permalink()) . '"
                                ' . (has_post_thumbnail() ? ',"image": "' . esc_url(get_the_post_thumbnail_url(get_the_ID(), 'medium_large')) . '"' : '') . '
                              }
                            }';
                            $position++;
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                  ]
                }
                </script>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <?php
                    if ($related_services->have_posts()) :
                        $delay = 300;
                        while ($related_services->have_posts()) : $related_services->the_post();
                            // Obtener metadatos del servicio
                            $rel_precio = get_post_meta(get_the_ID(), '_wptbt_service_price', true);
                            $rel_precio_duracion1 = get_post_meta(get_the_ID(), '_wptbt_service_duration1', true);
                            $rel_precio_valor1 = get_post_meta(get_the_ID(), '_wptbt_service_price1', true);
                            $rel_precio_duracion2 = get_post_meta(get_the_ID(), '_wptbt_service_duration2', true);
                            $rel_precio_valor2 = get_post_meta(get_the_ID(), '_wptbt_service_price2', true);
                            
                            // Obtener alt text para la imagen
                            $rel_image_id = get_post_thumbnail_id();
                            $rel_image_alt = get_post_meta($rel_image_id, '_wp_attachment_image_alt', true);
                            $rel_image_alt = empty($rel_image_alt) ? get_the_title() . ' - ' . $organization_name : $rel_image_alt;
                    ?>
                            <div class="service-card reveal-item opacity-0 translate-y-8 delay-<?php echo $delay; ?>" itemscope itemtype="https://schema.org/Service">
                                <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-100 h-full group transition-all duration-300 hover:shadow-lg relative">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="service-thumbnail h-56 overflow-hidden relative">
                                            <?php the_post_thumbnail('medium_large', array(
                                                'class' => 'w-full h-full object-cover group-hover:scale-110 transition-transform duration-700',
                                                'alt' => esc_attr($rel_image_alt),
                                                'itemprop' => 'image'
                                            )); ?>
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-70"></div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="p-6 relative <?php echo has_post_thumbnail() ? '-mt-20' : ''; ?>">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <div class="bg-white rounded-lg shadow-md p-5 mb-4 relative">
                                            <?php endif; ?>

                                            <h3 class="text-xl fancy-text font-medium mb-2 text-spa-primary transition-colors duration-300 group-hover:text-spa-accent" itemprop="name">
                                                <?php the_title(); ?>
                                            </h3>

                                            <div class="text-gray-600 text-sm mb-4" itemprop="description">
                                                <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                            </div>

                                            <div class="flex flex-wrap gap-2 mb-4">
                                                <?php if (!empty($rel_precio_duracion1) && !empty($rel_precio_valor1)) : ?>
                                                    <div class="inline-block px-3 py-1 rounded-full bg-spa-secondary text-sm font-medium text-spa-primary">
                                                        <span itemprop="serviceType"><?php echo esc_html($rel_precio_duracion1); ?></span> 
                                                        <span class="text-spa-accent" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                                                            <meta itemprop="priceCurrency" content="PEN">
                                                            <span itemprop="price"><?php echo esc_html($rel_precio_valor1); ?></span>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if (!empty($rel_precio_duracion2) && !empty($rel_precio_valor2)) : ?>
                                                    <div class="inline-block px-3 py-1 rounded-full bg-spa-secondary text-sm font-medium text-spa-primary">
                                                        <span itemprop="serviceType"><?php echo esc_html($rel_precio_duracion2); ?></span> 
                                                        <span class="text-spa-accent" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                                                            <meta itemprop="priceCurrency" content="PEN">
                                                            <span itemprop="price"><?php echo esc_html($rel_precio_valor2); ?></span>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if (empty($rel_precio_duracion1) && empty($rel_precio_valor1) && empty($rel_precio_duracion2) && empty($rel_precio_valor2) && !empty($rel_precio)) : ?>
                                                    <div class="inline-block px-3 py-1 rounded-full bg-spa-secondary text-sm font-medium text-spa-primary">
                                                        <?php esc_html_e('Price from:', 'wptbt-services'); ?> 
                                                        <span class="text-spa-accent" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                                                            <meta itemprop="priceCurrency" content="PEN">
                                                            <span itemprop="price"><?php echo esc_html($rel_precio); ?></span>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <?php if (has_post_thumbnail()) : ?>
                                            </div>
                                        <?php endif; ?>

                                        <a href="<?php the_permalink(); ?>" class="inline-block px-4 py-2 bg-spa-accent text-white text-sm font-medium rounded-sm transition-all duration-300 hover:bg-opacity-90 hover:translate-y-[-2px] hover:shadow-md" 
                                           itemprop="url"
                                           title="<?php echo esc_attr(sprintf(__('View details of %s', 'wptbt-services'), get_the_title())); ?>">
                                            <?php esc_html_e('View details', 'wptbt-services'); ?> <span class="ml-1" aria-hidden="true">→</span>
                                        </a>
                                        
                                        <!-- Provider information -->
                                        <div itemprop="provider" itemscope itemtype="https://schema.org/Organization" class="hidden">
                                            <meta itemprop="name" content="<?php echo esc_attr($organization_name); ?>">
                                            <meta itemprop="url" content="<?php echo esc_url($organization_url); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                            $delay = ($delay + 300) % 900;
                        endwhile;
                        wp_reset_postdata();
                    else :
                        ?>
                        <div class="col-span-full text-center">
                            <p class="text-gray-500"><?php esc_html_e('There are no related services available.', 'wptbt-services'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Sección de formulario de reserva -->
        <section id="reservar-servicio" class="py-20 bg-spa-secondary/30 relative overflow-hidden" aria-label="<?php esc_attr_e('Booking Form', 'wptbt-services'); ?>">
            <!-- Elementos decorativos para el fondo -->
            <div class="absolute top-0 right-0 w-64 h-64 rounded-full bg-spa-sage opacity-10 transform translate-x-1/4 -translate-y-1/4" aria-hidden="true"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full bg-spa-rose opacity-10 transform -translate-x-1/4 translate-y-1/4" aria-hidden="true"></div>

            <!-- Bordes decorativos -->
            <div class="absolute top-0 left-0 right-0 z-10 transform rotate-180" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100" preserveAspectRatio="none" class="w-full h-16 md:h-20 fill-current text-white">
                    <path d="M0,85.7L20,75.4C40,65.1,80,44.6,120,33.9C160,23.2,200,22.4,240,28.6C280,34.8,320,48.1,360,50C400,51.9,440,42.5,480,39.8C520,37.2,560,41.3,600,48.3C640,55.2,680,65,720,62.3C760,59.5,800,44.2,840,41.9C880,39.7,920,50.5,960,54.3C1000,58.2,1040,55.2,1080,58.2C1120,61.1,1160,69.9,1200,73.3C1240,76.8,1280,74.9,1320,70.9C1360,67,1400,61,1420,58L1440,55L1440,100L1420,100C1400,100,1360,100,1320,100C1280,100,1240,100,1200,100C1160,100,1120,100,1080,100C1040,100,1000,100,960,100C920,100,880,100,840,100C800,100,760,100,720,100C680,100,640,100,600,100C560,100,520,100,480,100C440,100,400,100,360,100C320,100,280,100,240,100C200,100,160,100,120,100C80,100,40,100,20,100L0,100Z"></path>
                </svg>
            </div>

            <div class="container mx-auto px-4 relative z-10">
                <!-- Schema.org para Action con oferta -->
                <script type="application/ld+json">
                {
                  "@context": "https://schema.org",
                  "@type": "ReservationPackage",
                  "name": "<?php echo esc_js(sprintf(__('Booking for %s', 'wptbt-services'), get_the_title())); ?>",
                  "description": "<?php echo esc_js(sprintf(__('Book your %s service at %s', 'wptbt-services'), get_the_title(), $organization_name)); ?>",
                  "potentialAction": {
                    "@type": "ReserveAction",
                    "target": {
                      "@type": "EntryPoint",
                      "urlTemplate": "<?php echo esc_url(get_permalink()); ?>#reservar-servicio",
                      "inLanguage": "<?php echo get_bloginfo('language'); ?>",
                      "actionPlatform": "http://schema.org/DesktopWebPlatform"
                    },
                    "result": {
                      "@type": "Reservation",
                      "reservationFor": {
                        "@type": "Service",
                        "name": "<?php echo esc_js(get_the_title()); ?>"
                      }
                    }
                  }
                }
                </script>

                <?php
                // Mostrar el formulario de reserva personalizado
                if (function_exists('wptbt_display_service_booking_form')) {
                    wptbt_display_service_booking_form();
                } else {
                    $service_name = get_the_title();
                    echo do_shortcode('[solid 
                    component="booking-form" 
                    accent-color="#D4B254" 
                    use-single-service="true" 
                    title="' . esc_attr__('Book Now', 'wptbt-services') . ' ' . esc_attr($service_name) . '"]');
                }
                ?>
            </div>

            <!-- Borde inferior decorativo -->
            <div class="absolute bottom-0 left-0 right-0 z-10" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100" preserveAspectRatio="none" class="w-full h-16 md:h-20 fill-current text-white">
                    <path d="M0,85.7L20,75.4C40,65.1,80,44.6,120,33.9C160,23.2,200,22.4,240,28.6C280,34.8,320,48.1,360,50C400,51.9,440,42.5,480,39.8C520,37.2,560,41.3,600,48.3C640,55.2,680,65,720,62.3C760,59.5,800,44.2,840,41.9C880,39.7,920,50.5,960,54.3C1000,58.2,1040,55.2,1080,58.2C1120,61.1,1160,69.9,1200,73.3C1240,76.8,1280,74.9,1320,70.9C1360,67,1400,61,1420,58L1440,55L1440,100L1420,100C1400,100,1360,100,1320,100C1280,100,1240,100,1200,100C1160,100,1120,100,1080,100C1040,100,1000,100,960,100C920,100,880,100,840,100C800,100,760,100,720,100C680,100,640,100,600,100C560,100,520,100,480,100C440,100,400,100,360,100C320,100,280,100,240,100C200,100,160,100,120,100C80,100,40,100,20,100L0,100Z"></path>
                </svg>
            </div>
        </section>

    <?php endwhile; ?>

</main>

<!-- Script para efectos de animación mejorados con Intersection Observer -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Utilizar Intersection Observer para animaciones
    const revealItems = document.querySelectorAll('.reveal-item');
    
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                observer.unobserve(entry.target); // Dejar de observar una vez revelado
            }
        });
    }, observerOptions);
    
    revealItems.forEach(item => {
        observer.observe(item);
    });
    
    // Alternativa para navegadores que no soportan Intersection Observer
    if (!('IntersectionObserver' in window)) {
        // Fallback a la función original
        const revealOnScroll = function() {
            for (let i = 0; i < revealItems.length; i++) {
                const windowHeight = window.innerHeight;
                const elementTop = revealItems[i].getBoundingClientRect().top;
                const elementVisible = 150;

                if (elementTop < windowHeight - elementVisible) {
                    revealItems[i].classList.add('revealed');
                }
            }
        };

        // Ejecutar al cargar la página
        revealOnScroll();

        // Ejecutar al hacer scroll
        window.addEventListener('scroll', revealOnScroll);
    }
    
    // Smooth scroll para los enlaces internos
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // Agregar el hash a la URL sin recargar la página
                history.pushState(null, null, targetId);
            }
        });
    });

    // Agregar alt text a las imágenes que no lo tengan
    document.querySelectorAll('img:not([alt])').forEach(img => {
        const parent = img.closest('.service-card, .service-thumbnail');
        if (parent) {
            const title = parent.querySelector('h3')?.textContent.trim() || '<?php echo esc_js(get_the_title()); ?>';
            img.alt = `${title} - <?php echo esc_js($organization_name); ?>`;
        } else {
            img.alt = '<?php echo esc_js(get_the_title()); ?> - <?php echo esc_js($organization_name); ?>';
        }
    });
});
</script>

<style>
/* Estilos adicionales para las animaciones */
.reveal-item {
    transition: all 0.6s ease;
}

.reveal-item.revealed {
    opacity: 1 !important;
    transform: translateY(0) !important;
}

/* Clases de retraso */
.delay-300 {
    transition-delay: 0.3s;
}

.delay-600 {
    transition-delay: 0.6s;
}

.delay-900 {
    transition-delay: 0.9s;
}

/* Estilos para migas de pan */
.breadcrumbs ol {
    list-style: none;
    padding: 0;
    margin: 0;
}

/* Clase para elemento visualmente oculto pero accesible a lectores de pantalla */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}

/* Soporte para preferencia de movimiento reducido */
@media (prefers-reduced-motion: reduce) {
    .reveal-item {
        transition: none !important;
        opacity: 1 !important;
        transform: none !important;
    }

    .service-card,
    .cta-banner,
    a, button {
        transition: none !important;
    }
    
    .service-thumbnail img {
        transition: none !important;
    }
}

/* Estilos para Focus visible (accesibilidad) */
a:focus-visible, button:focus-visible {
    outline: 2px solid var(--color-spa-accent, #D4B254);
    outline-offset: 2px;
}
</style>

<?php get_footer(); ?>