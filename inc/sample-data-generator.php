<?php
/**
 * Generador de datos de ejemplo para el tema de agencia de viajes
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPTBT_Sample_Data_Generator
{
    private $tours_data = [];
    private $destinations_data = [];
    private $pages_data = [];
    private $images_urls = [];

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('wp_ajax_generate_sample_data', [$this, 'handle_ajax_request']);
        add_action('wp_ajax_check_sample_content', [$this, 'check_sample_content']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        
        $this->init_sample_data();
    }

    private function get_home_page_content()
    {
        return '<!-- wp:wptbt/tours-carousel-block {"title":"Tours Destacados","subtitle":"Descubre nuestras aventuras más populares","postsPerPage":6,"backgroundColor":"#f8fafc","autoplaySpeed":3000,"showDots":true,"showArrows":true} /-->

<!-- wp:wptbt/destinations-carousel {"title":"Destinos Populares","subtitle":"Explora los lugares más increíbles de Sudamérica","showViewMore":true,"backgroundColor":"#ffffff","autoplay":true} /-->

<!-- wp:wptbt/benefits-block {"title":"¿Por qué viajar con nosotros?","subtitle":"Más de 15 años creando experiencias inolvidables","benefits":[{"icon":"🏔️","title":"Guías Expertos","description":"Profesionales locales certificados que conocen cada rincón"},{"icon":"🌱","title":"Turismo Sostenible","description":"Comprometidos con el cuidado del medio ambiente"},{"icon":"👥","title":"Grupos Pequeños","description":"Máximo 12 personas para experiencias personalizadas"},{"icon":"📞","title":"Soporte 24/7","description":"Asistencia completa durante todo tu viaje"}],"backgroundColor":"#f1f5f9"} /-->

<!-- wp:wptbt/services-block {"title":"Nuestros Servicios","subtitle":"Todo lo que necesitas para tu aventura perfecta","services":[{"icon":"🗺️","title":"Tours Personalizados","description":"Diseñamos itinerarios únicos según tus intereses y presupuesto","buttonText":"Ver Tours","buttonUrl":"/tours"},{"icon":"🏨","title":"Alojamientos Premium","description":"Desde hoteles boutique hasta lodges eco-friendly en destinos remotos","buttonText":"Ver Opciones","buttonUrl":"/servicios"},{"icon":"🚐","title":"Transporte Privado","description":"Vehículos privados y transporte especializado para aventuras","buttonText":"Consultar","buttonUrl":"/contacto"}],"backgroundColor":"#ffffff"} /-->

<!-- wp:wptbt/booking-block {"title":"¿Listo para tu próxima aventura?","subtitle":"Consulta disponibilidad y precios para cualquiera de nuestros tours","backgroundColor":"#0f172a","textColor":"#ffffff"} /-->

<!-- wp:wptbt/gallery-block {"title":"Galería de Aventuras","subtitle":"Momentos únicos capturados en nuestros tours","images":[{"id":1,"url":"https://picsum.photos/600/400?random=1001","alt":"Machu Picchu al amanecer"},{"id":2,"url":"https://picsum.photos/600/400?random=1002","alt":"Camino Inca"},{"id":3,"url":"https://picsum.photos/600/400?random=1003","alt":"Oasis de Huacachina"},{"id":4,"url":"https://picsum.photos/600/400?random=1004","alt":"Amazonas"},{"id":5,"url":"https://picsum.photos/600/400?random=1005","alt":"Salar de Uyuni"},{"id":6,"url":"https://picsum.photos/600/400?random=1006","alt":"Laguna 69"}],"backgroundColor":"#f8fafc"} /-->

<!-- wp:wptbt/google-reviews-block {"title":"Lo que dicen nuestros viajeros","subtitle":"Experiencias reales de personas que han vivido aventuras con nosotros","reviews":[{"name":"María González","location":"Madrid, España","rating":5,"text":"Una experiencia increíble en Machu Picchu. El guía fue excelente y la organización perfecta. Sin duda repetiré.","avatar":"https://picsum.photos/100/100?random=2001"},{"name":"Carlos Rodríguez","location":"Ciudad de México","rating":5,"text":"El Camino Inca fue la aventura de mi vida. Todo perfectamente organizado, guías profesionales y paisajes increíbles.","avatar":"https://picsum.photos/100/100?random=2002"},{"name":"Ana Silva","location":"São Paulo, Brasil","rating":5,"text":"Tour por el Amazonas espectacular. Vimos tantos animales y la experiencia en el lodge fue única. Altamente recomendado.","avatar":"https://picsum.photos/100/100?random=2003"}],"backgroundColor":"#ffffff"} /-->

<!-- wp:wptbt/faq-block {"title":"Preguntas Frecuentes","subtitle":"Resolvemos las dudas más comunes sobre nuestros tours","faqs":[{"question":"¿Qué incluyen los tours?","answer":"Cada tour incluye detalles específicos en su descripción. Generalmente incluimos transporte, alojamiento, algunas comidas, guía profesional y todas las entradas necesarias."},{"question":"¿Se requiere seguro de viaje?","answer":"Altamente recomendado para todas las actividades. Podemos ayudarte a conseguir el seguro adecuado según tu destino y actividades planificadas."},{"question":"¿Cuál es la política de cancelación?","answer":"Cancelaciones con 30+ días de anticipación: reembolso del 90%. Entre 15-29 días: 50%. Menos de 15 días: no reembolsable. Consulta términos específicos por tour."},{"question":"¿Proporcionan equipos para trekking?","answer":"Sí, ofrecemos equipos básicos de trekking incluidos. Equipos especializados como bastones, sleeping bags premium pueden alquilarse por un costo adicional."},{"question":"¿Hay descuentos para grupos?","answer":"Sí, ofrecemos descuentos especiales para grupos de 8 o más personas. Contacta con nosotros para obtener una cotización personalizada."}],"backgroundColor":"#f1f5f9"} /-->

<!-- wp:wptbt/interactive-map-block {"title":"Nuestros Destinos","subtitle":"Explora todos los lugares increíbles que visitamos","mapCenter":{"lat":-13.5320,"lng":-71.9675},"mapZoom":6,"markers":[{"lat":-13.1631,"lng":-72.5450,"title":"Machu Picchu","description":"La ciudadela inca más famosa del mundo"},{"lat":-13.5320,"lng":-71.9675,"title":"Cusco","description":"Capital histórica del Imperio Inca"},{"lat":-12.0464,"lng":-77.0428,"title":"Lima","description":"Capital gastronómica de América"},{"lat":-3.7437,"lng":-73.2516,"title":"Iquitos","description":"Puerta de entrada a la Amazonía"},{"lat":-14.0875,"lng":-75.7626,"title":"Huacachina","description":"Oasis en el desierto peruano"},{"lat":-20.1338,"lng":-67.4891,"title":"Salar de Uyuni","description":"El salar más grande del mundo"}],"backgroundColor":"#ffffff"} /-->';
    }

    public function add_admin_menu()
    {
        add_submenu_page(
            'tools.php',
            'Datos de Ejemplo',
            'Datos de Ejemplo',
            'manage_options',
            'sample-data-generator',
            [$this, 'admin_page']
        );
    }

    public function enqueue_admin_scripts($hook)
    {
        if ('tools_page_sample-data-generator' !== $hook) {
            return;
        }

        wp_enqueue_script('jquery');
        wp_enqueue_script(
            'sample-data-generator',
            get_template_directory_uri() . '/assets/admin/js/sample-data-generator.js',
            ['jquery'],
            WPTBT_VERSION,
            true
        );

        wp_localize_script('sample-data-generator', 'sampleData', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sample_data_nonce')
        ]);
    }

    private function init_sample_data()
    {
        $this->tours_data = [
            [
                'title' => 'Tour Machu Picchu Clásico 4D/3N',
                'subtitle' => 'La experiencia definitiva en la ciudadela sagrada de los incas',
                'description' => 'Descubre la majestuosa ciudadela de Machu Picchu en este tour clásico de 4 días y 3 noches. Incluye tren panorámico, guía experto y alojamiento en hoteles boutique.',
                'technical_description' => 'Este tour combina historia, aventura y comodidad. Incluye traslados en vehículos privados, tren panorámico Vistadome, guías especializados en historia inca y alojamiento en hoteles 4 estrellas.',
                // Pricing
                'tour_price_international' => '450',
                'tour_price_national' => '380',
                'tour_price_promotion' => '420',
                'tour_price_original' => '500',
                'tour_currency' => 'USD',
                'tour_duration' => '4 días / 3 noches',
                'booking_prices' => [
                    ['duration' => '4', 'price' => '$450'],
                    ['duration' => '3', 'price' => '$350']
                ],
                // Details
                'difficulty' => 'Moderado',
                'min_age' => '12',
                'max_people' => '12',
                'total_duration' => '4 días',
                'alternative_route' => 'Camino Inca 2 días disponible',
                // Location
                'departure_point' => 'Plaza de Armas - Cusco',
                'return_point' => 'Hotel en Cusco',
                'latitude' => -13.5320,
                'longitude' => -71.9675,
                'google_maps_url' => 'https://maps.google.com/?q=Machu+Picchu',
                // Lists
                'includes' => [
                    'Transporte privado Cusco-Ollantaytambo-Cusco',
                    'Tren panorámico Vistadome ida y vuelta',
                    'Bus subida y bajada a Machu Picchu',
                    'Entradas a Machu Picchu',
                    '3 noches de alojamiento (hoteles 4 estrellas)',
                    'Desayunos en hoteles',
                    'Guía profesional bilingüe',
                    'Asistencia 24/7'
                ],
                'excludes' => [
                    'Vuelos domésticos',
                    'Almuerzo y cena (días 1, 2 y 3)',
                    'Bebidas alcohólicas',
                    'Propinas',
                    'Seguro de viaje',
                    'Gastos personales'
                ],
                'what_to_bring' => [
                    'Pasaporte original vigente',
                    'Ropa cómoda para caminar',
                    'Zapatos de trekking',
                    'Chaqueta impermeable',
                    'Protector solar FPS 50+',
                    'Sombrero o gorra',
                    'Cámara fotográfica',
                    'Botella de agua reutilizable'
                ],
                'recommendations' => [
                    'Llegar a Cusco al menos 1 día antes para aclimatación',
                    'Consultar con médico si tiene problemas cardíacos',
                    'Evitar comidas pesadas el día del tour',
                    'Mantenerse hidratado durante todo el recorrido',
                    'Respetar las normas del sitio arqueológico'
                ],
                // Booking info
                'whatsapp' => '+51984123456',
                'phone' => '+51984123456',
                'email' => 'reservas@mysticalterra.com',
                'booking_url' => '',
                'advance_payment' => '30',
                'cancellation_policy' => 'Cancelación gratuita hasta 30 días antes. Entre 15-29 días: 50% de reembolso. Menos de 15 días: No reembolsable.',
                // Configuration
                'duration_days' => 4,
                'includes_accommodation' => true,
                'requires_documents' => true,
                'has_flexible_schedule' => false,
                'pickup_required' => true,
                'emergency_contact_required' => true,
                'required_traveler_fields' => ['full_name', 'passport_number', 'emergency_contact', 'dietary_restrictions'],
                'languages_available' => ['Español', 'Inglés', 'Francés'],
                // SEO & Marketing
                'featured' => true,
                'popular' => true,
                'new' => false,
                'meta_description' => 'Tour Machu Picchu 4D/3N con tren panorámico, guía experto y hoteles 4 estrellas. ¡Reserva tu aventura inca!',
                'keywords' => 'machu picchu, tour cusco, ciudadela inca, tren panorámico, guía bilingüe',
                // Dates and hours
                'departure_dates' => ['2024-09-15', '2024-09-22', '2024-09-29', '2024-10-06', '2024-10-13'],
                'tour_hours' => ['06:00', '08:00', '14:00'],
                // Itinerary
                'itinerary' => [
                    [
                        'title' => 'Día 1: Cusco - Valle Sagrado',
                        'date_label' => 'Día 1',
                        'description' => 'Recojo del hotel y traslado al Valle Sagrado. Visita a Pisaq y Ollantaytambo.',
                        'meals' => 'Desayuno',
                        'accommodation' => 'Hotel Valle Sagrado 4 estrellas',
                        'schedule' => [
                            ['time' => '07:00', 'time_range' => '07:00 – 07:30 a.m.', 'activity' => 'Recojo del hotel en Cusco'],
                            ['time' => '10:00', 'time_range' => '10:00 – 12:00 p.m.', 'activity' => 'Visita guiada en Pisaq - Mercado artesanal y sitio arqueológico'],
                            ['time' => '13:00', 'time_range' => '13:00 – 14:30 p.m.', 'activity' => 'Almuerzo en restaurante local con vista al valle'],
                            ['time' => '16:00', 'time_range' => '16:00 – 18:00 p.m.', 'activity' => 'Exploración de Ollantaytambo - Fortaleza inca viviente'],
                            ['time' => '19:00', 'time_range' => '19:00 – 20:00 p.m.', 'activity' => 'Check-in hotel Valle Sagrado y cena libre']
                        ],
                        'locations' => [
                            ['name' => 'Pisaq', 'lat' => -13.4169, 'lng' => -71.8475],
                            ['name' => 'Ollantaytambo', 'lat' => -13.2593, 'lng' => -72.2635]
                        ]
                    ],
                    [
                        'title' => 'Día 2: Tren a Aguas Calientes',
                        'date_label' => 'Día 2', 
                        'description' => 'Viaje en tren panorámico a Aguas Calientes y preparativos para Machu Picchu.',
                        'meals' => 'Desayuno',
                        'accommodation' => 'Hotel Aguas Calientes 3 estrellas',
                        'schedule' => [
                            ['time' => '06:30', 'time_range' => '06:30 – 07:30 a.m.', 'activity' => 'Desayuno en hotel y check-out'],
                            ['time' => '07:30', 'time_range' => '07:30 – 08:00 a.m.', 'activity' => 'Traslado a estación de tren Ollantaytambo'],
                            ['time' => '08:00', 'time_range' => '08:00 – 10:15 a.m.', 'activity' => 'Tren panorámico Vistadome con vistas espectaculares'],
                            ['time' => '10:15', 'time_range' => '10:15 – 11:00 a.m.', 'activity' => 'Llegada a Aguas Calientes y traslado al hotel'],
                            ['time' => '14:00', 'time_range' => '14:00 – 18:00 p.m.', 'activity' => 'Tarde libre para explorar el pueblo y relajarse en aguas termales']
                        ],
                        'locations' => [
                            ['name' => 'Estación Ollantaytambo', 'lat' => -13.2593, 'lng' => -72.2635],
                            ['name' => 'Aguas Calientes', 'lat' => -13.1544, 'lng' => -72.5254]
                        ]
                    ],
                    [
                        'title' => 'Día 3: Machu Picchu - Cusco',
                        'date_label' => 'Día 3',
                        'description' => 'Tour completo de Machu Picchu con guía especializado y retorno a Cusco.',
                        'meals' => 'Desayuno, box lunch',
                        'accommodation' => 'Hotel Cusco 4 estrellas',
                        'schedule' => [
                            ['time' => '05:30', 'time_range' => '05:30 – 06:00 a.m.', 'activity' => 'Bus desde Aguas Calientes a Machu Picchu'],
                            ['time' => '06:00', 'time_range' => '06:00 – 06:30 a.m.', 'activity' => 'Amanecer en Machu Picchu y sesión fotográfica'],
                            ['time' => '07:00', 'time_range' => '07:00 – 10:00 a.m.', 'activity' => 'Tour guiado completo de la ciudadela inca'],
                            ['time' => '12:00', 'time_range' => '12:00 – 13:00 p.m.', 'activity' => 'Tiempo libre y descenso a Aguas Calientes'],
                            ['time' => '15:00', 'time_range' => '15:00 – 17:30 p.m.', 'activity' => 'Tren retorno Aguas Calientes - Ollantaytambo'],
                            ['time' => '18:00', 'time_range' => '18:00 – 20:00 p.m.', 'activity' => 'Traslado privado Ollantaytambo - Cusco']
                        ],
                        'locations' => [
                            ['name' => 'Machu Picchu', 'lat' => -13.1631, 'lng' => -72.5450]
                        ]
                    ],
                    [
                        'title' => 'Día 4: Cusco City Tour',
                        'date_label' => 'Día 4',
                        'description' => 'Tour por la ciudad del Cusco visitando los principales sitios arqueológicos.',
                        'meals' => 'Desayuno',
                        'accommodation' => 'No aplica',
                        'schedule' => [
                            ['time' => '08:30', 'time_range' => '08:30 – 10:00 a.m.', 'activity' => 'Visita al Qoricancha (Templo del Sol)'],
                            ['time' => '10:30', 'time_range' => '10:30 – 12:00 p.m.', 'activity' => 'Catedral del Cusco y Plaza de Armas'],
                            ['time' => '14:00', 'time_range' => '14:00 – 17:00 p.m.', 'activity' => 'Sacsayhuamán, Qenqo, Puca Pucara y Tambomachay'],
                            ['time' => '17:30', 'time_range' => '17:30 – 18:00 p.m.', 'activity' => 'Traslado al aeropuerto o hotel']
                        ],
                        'locations' => [
                            ['name' => 'Qoricancha', 'lat' => -13.5189, 'lng' => -71.9761],
                            ['name' => 'Sacsayhuamán', 'lat' => -13.5088, 'lng' => -71.9828]
                        ]
                    ]
                ],
                'altitude_points' => [
                    ['location' => 'Cusco', 'altitude' => '3,399 msnm'],
                    ['location' => 'Valle Sagrado', 'altitude' => '2,792 msnm'],
                    ['location' => 'Machu Picchu', 'altitude' => '2,430 msnm']
                ],
                'destinations' => ['cusco', 'machu-picchu'],
                'categories' => ['cultural', 'aventura'],
                'gallery' => [
                    'https://picsum.photos/800/600?random=3001',
                    'https://picsum.photos/800/600?random=3002',
                    'https://picsum.photos/800/600?random=3003'
                ]
            ],
            [
                'title' => 'Camino Inca 2 Días',
                'subtitle' => 'La aventura del legendario sendero inca en formato express',
                'description' => 'Experimenta la aventura del famoso Camino Inca en una versión de 2 días perfecta para viajeros con tiempo limitado.',
                'technical_description' => 'Trekking moderadamente desafiante por senderos originales incas. Incluye camping en sitio arqueológico y entrada por Puerta del Sol a Machu Picchu.',
                // Pricing
                'tour_price_international' => '320',
                'tour_price_national' => '280',
                'tour_price_promotion' => '300',
                'tour_price_original' => '350',
                'tour_currency' => 'USD',
                'tour_duration' => '2 días / 1 noche',
                'booking_prices' => [
                    ['duration' => '2', 'price' => '$320']
                ],
                // Details
                'difficulty' => 'Desafiante',
                'min_age' => '16',
                'max_people' => '16',
                'total_duration' => '2 días',
                'alternative_route' => 'Tour clásico Machu Picchu 4D/3N disponible',
                // Location
                'departure_point' => 'Estación de tren Ollantaytambo',
                'return_point' => 'Aguas Calientes',
                'latitude' => -13.1631,
                'longitude' => -72.5450,
                'google_maps_url' => 'https://maps.google.com/?q=Camino+Inca',
                // Lists
                'includes' => [
                    'Permisos de ingreso al Camino Inca',
                    'Guía profesional especializado',
                    'Equipo de camping completo',
                    'Todas las comidas (2 desayunos, 2 almuerzos, 1 cena)',
                    'Entrada a Machu Picchu',
                    'Bus bajada Machu Picchu-Aguas Calientes',
                    'Tren retorno Aguas Calientes-Ollantaytambo'
                ],
                'excludes' => [
                    'Sleeping bag (se puede alquilar $25)',
                    'Bastones de trekking (se pueden alquilar $15)',
                    'Propinas para guía y porteadores',
                    'Seguro de viaje personal',
                    'Gastos personales y bebidas'
                ],
                'what_to_bring' => [
                    'Pasaporte original (obligatorio)',
                    'Mochila de trekking 40-50L',
                    'Sleeping bag (o alquilar)',
                    'Ropa de abrigo e impermeable',
                    'Zapatos de trekking impermeables',
                    'Linterna frontal con baterías extra',
                    'Botella de agua 2L mínimo',
                    'Protector solar y repelente'
                ],
                'recommendations' => [
                    'Estado físico bueno es indispensable',
                    'Aclimatación previa en Cusco mínimo 2 días',
                    'Reservar con 6 meses de anticipación',
                    'Entrenar caminatas previas recomendable'
                ],
                // Booking info
                'whatsapp' => '+51984123456',
                'phone' => '+51984123456',
                'email' => 'trekking@mysticalterra.com',
                'booking_url' => '',
                'advance_payment' => '50',
                'cancellation_policy' => 'Los permisos del Camino Inca son intransferibles. Cancelación con más de 45 días: 80% reembolso. Menos de 45 días: No reembolsable.',
                // Configuration
                'duration_days' => 2,
                'includes_accommodation' => true,
                'requires_documents' => true,
                'has_flexible_schedule' => false,
                'pickup_required' => false,
                'emergency_contact_required' => true,
                'required_traveler_fields' => ['full_name', 'passport_number', 'emergency_contact', 'medical_conditions'],
                'languages_available' => ['Español', 'Inglés'],
                // SEO & Marketing
                'featured' => true,
                'popular' => true,
                'new' => false,
                'meta_description' => 'Camino Inca 2 días con camping y entrada por Puerta del Sol. Aventura auténtica en senderos originales incas.',
                'keywords' => 'camino inca, trekking peru, machu picchu hiking, sendero inca, camping cusco',
                // Dates and hours
                'departure_dates' => ['2024-09-20', '2024-09-27', '2024-10-04', '2024-10-11', '2024-10-18'],
                'tour_hours' => ['05:30'],
                'destinations' => ['cusco', 'machu-picchu'],
                'categories' => ['aventura', 'trekking'],
                'gallery' => [
                    'https://picsum.photos/800/600?random=3004',
                    'https://picsum.photos/800/600?random=3005'
                ]
            ],
            [
                'title' => 'Tour Laguna 69 - Huacachina',
                'subtitle' => 'Dos mundos en una aventura: montañas glaciares y oasis desértico',
                'description' => 'Aventura completa que combina la impresionante Laguna 69 en Huascaran con la experiencia del oasis de Huacachina.',
                'technical_description' => 'Tour combinado que incluye trekking de alta montaña a 4,600m y sandboarding en el desierto costero. Contraste único de ecosistemas peruanos.',
                // Pricing
                'tour_price_international' => '280',
                'tour_price_national' => '250',
                'tour_price_promotion' => '260',
                'tour_price_original' => '320',
                'tour_currency' => 'USD',
                'tour_duration' => '3 días / 2 noches',
                'booking_prices' => [
                    ['duration' => '3', 'price' => '$280']
                ],
                // Details
                'difficulty' => 'moderate-high',
                'min_age' => '14',
                'max_people' => '10',
                'total_duration' => '3 días',
                'alternative_route' => 'Solo Laguna 69 (1 día) o solo Huacachina (1 día) disponibles',
                // Location
                'departure_point' => 'Lima - Plaza de Armas',
                'return_point' => 'Lima - Hotel',
                'latitude' => -9.0882,
                'longitude' => -77.6111,
                'google_maps_url' => 'https://maps.google.com/?q=Laguna+69+Peru',
                // Lists
                'includes' => [
                    'Transporte privado Lima-Huacachina-Huaraz-Lima',
                    '2 noches de alojamiento (Huacachina + Huaraz)',
                    'Desayunos incluidos',
                    'Guía especializado en montaña',
                    'Entrada al Parque Nacional Huascarán',
                    'Sandboarding y buggy en Huacachina',
                    'Equipo básico de trekking'
                ],
                'excludes' => [
                    'Almuerzos y cenas (excepto donde se especifique)',
                    'Bastones de trekking (se pueden alquilar)',
                    'Ropa de montaña especializada',
                    'Seguro de alta montaña',
                    'Gastos personales',
                    'Propinas'
                ],
                'what_to_bring' => [
                    'Ropa de abrigo para alta montaña',
                    'Chaqueta cortaviento impermeable',
                    'Zapatos de trekking impermeables',
                    'Gafas de sol categoría 4',
                    'Protector solar FPS 50+',
                    'Gorro de abrigo y guantes',
                    'Botella de agua 1.5L mínimo',
                    'Snacks energéticos',
                    'Ropa ligera para el desierto'
                ],
                'recommendations' => [
                    'Excelente condición física requerida para Laguna 69',
                    'Aclimatación previa en altitud recomendable',
                    'No recomendado para personas con problemas cardíacos',
                    'Consultar clima antes de viajar',
                    'Hidratación constante durante el trekking'
                ],
                // Booking info
                'whatsapp' => '+51984123456',
                'phone' => '+51984123456',
                'email' => 'aventura@mysticalterra.com',
                'booking_url' => '',
                'advance_payment' => '40',
                'cancellation_policy' => 'Cancelación gratuita hasta 15 días antes. Entre 7-14 días: 50% reembolso. Menos de 7 días: No reembolsable por condiciones climáticas.',
                // Configuration
                'duration_days' => 3,
                'includes_accommodation' => true,
                'requires_documents' => false,
                'has_flexible_schedule' => true,
                'pickup_required' => true,
                'emergency_contact_required' => true,
                'required_traveler_fields' => ['full_name', 'emergency_contact', 'fitness_level', 'medical_conditions'],
                'languages_available' => ['Español', 'Inglés'],
                // SEO & Marketing
                'featured' => false,
                'popular' => true,
                'new' => false,
                'meta_description' => 'Tour combinado Laguna 69 y Huacachina. Trekking glaciar + sandboarding en oasis. Aventura completa en 3 días.',
                'keywords' => 'laguna 69, huacachina oasis, trekking peru, sandboarding, huascaran, aventura',
                // Dates and hours
                'departure_dates' => ['2024-09-25', '2024-10-02', '2024-10-09', '2024-10-16', '2024-10-23'],
                'tour_hours' => ['06:00'],
                // Itinerary
                'itinerary' => [
                    [
                        'title' => 'Día 1: Lima - Huacachina',
                        'date_label' => 'Día 1',
                        'description' => 'Traslado a Huacachina y tarde de sandboarding en las dunas.',
                        'meals' => 'No incluido',
                        'accommodation' => 'Hotel oasis Huacachina',
                        'schedule' => [
                            ['time' => '06:00', 'time_range' => '06:00 – 06:30 a.m.', 'activity' => 'Recojo del hotel en Lima'],
                            ['time' => '10:30', 'time_range' => '10:30 – 11:00 a.m.', 'activity' => 'Llegada a Huacachina y check-in'],
                            ['time' => '16:00', 'time_range' => '16:00 – 18:30 p.m.', 'activity' => 'Sandboarding y paseo en buggy por las dunas'],
                            ['time' => '19:00', 'time_range' => '19:00 – 20:00 p.m.', 'activity' => 'Atardecer en las dunas y cena libre']
                        ],
                        'locations' => [
                            ['name' => 'Oasis de Huacachina', 'lat' => -14.0875, 'lng' => -75.7626]
                        ]
                    ],
                    [
                        'title' => 'Día 2: Huacachina - Huaraz',
                        'date_label' => 'Día 2',
                        'description' => 'Viaje de Huacachina a Huaraz, ciudad base para la Cordillera Blanca.',
                        'meals' => 'Desayuno',
                        'accommodation' => 'Hotel Huaraz 3 estrellas',
                        'schedule' => [
                            ['time' => '07:00', 'time_range' => '07:00 – 08:00 a.m.', 'activity' => 'Desayuno y check-out del hotel'],
                            ['time' => '08:30', 'time_range' => '08:30 – 16:00 p.m.', 'activity' => 'Viaje por carretera Lima-Huaraz (paisajes espectaculares)'],
                            ['time' => '17:00', 'time_range' => '17:00 – 18:00 p.m.', 'activity' => 'Llegada a Huaraz y check-in hotel'],
                            ['time' => '19:00', 'time_range' => '19:00 – 20:00 p.m.', 'activity' => 'Cena libre y descanso para aclimatación']
                        ],
                        'locations' => [
                            ['name' => 'Huaraz', 'lat' => -9.5288, 'lng' => -77.5269]
                        ]
                    ],
                    [
                        'title' => 'Día 3: Laguna 69 - Retorno Lima',
                        'date_label' => 'Día 3',
                        'description' => 'Trekking temprano a la Laguna 69 y retorno a Lima.',
                        'meals' => 'Desayuno, box lunch',
                        'accommodation' => 'No aplica',
                        'schedule' => [
                            ['time' => '05:00', 'time_range' => '05:00 – 06:00 a.m.', 'activity' => 'Desayuno temprano y salida a Llanganuco'],
                            ['time' => '07:30', 'time_range' => '07:30 – 08:00 a.m.', 'activity' => 'Inicio del trekking desde Cebollapampa (3,900m)'],
                            ['time' => '10:30', 'time_range' => '10:30 – 12:00 p.m.', 'activity' => 'Llegada a Laguna 69 (4,600m) - Tiempo libre'],
                            ['time' => '14:00', 'time_range' => '14:00 – 15:30 p.m.', 'activity' => 'Descenso y almuerzo box lunch'],
                            ['time' => '16:00', 'time_range' => '16:00 – 22:00 p.m.', 'activity' => 'Retorno a Lima']
                        ],
                        'locations' => [
                            ['name' => 'Laguna 69', 'lat' => -9.0882, 'lng' => -77.6111],
                            ['name' => 'Cebollapampa', 'lat' => -9.0950, 'lng' => -77.6180]
                        ]
                    ]
                ],
                'altitude_points' => [
                    ['location' => 'Huacachina', 'altitude' => '406 msnm'],
                    ['location' => 'Huaraz', 'altitude' => '3,052 msnm'],
                    ['location' => 'Cebollapampa', 'altitude' => '3,900 msnm'],
                    ['location' => 'Laguna 69', 'altitude' => '4,600 msnm']
                ],
                'destinations' => ['huacachina', 'huascaran'],
                'categories' => ['aventura', 'naturaleza'],
                'gallery' => [
                    'https://picsum.photos/800/600?random=3006',
                    'https://picsum.photos/800/600?random=3007'
                ]
            ],
            [
                'title' => 'Circuito Amazónico Iquitos',
                'description' => 'Inmersión total en la selva amazónica peruana con navegación por el río Amazonas, observación de fauna y comunidades nativas.',
                'price' => '380',
                'duration' => '4 días / 3 noches',
                'difficulty' => 'Fácil',
                'min_people' => '4',
                'max_people' => '20',
                'includes' => 'Lodge, todas las comidas, excursiones, guía naturalista',
                'destinations' => ['iquitos', 'amazonas'],
                'categories' => ['naturaleza', 'ecoturismo'],
                'gallery' => [
                    'https://picsum.photos/800/600?random=3008',
                    'https://picsum.photos/800/600?random=3009'
                ]
            ],
            [
                'title' => 'Tour Gastronómico Lima',
                'description' => 'Descubre la capital gastronómica de América Latina con este tour culinario por los mejores restaurantes y mercados de Lima.',
                'price' => '150',
                'duration' => '1 día',
                'difficulty' => 'Fácil',
                'min_people' => '2',
                'max_people' => '8',
                'includes' => 'Guía gastronómico, degustaciones, transporte urbano',
                'destinations' => ['lima'],
                'categories' => ['gastronomico', 'cultural'],
                'gallery' => [
                    'https://picsum.photos/800/600?random=3010',
                    'https://picsum.photos/800/600?random=3011'
                ]
            ],
            [
                'title' => 'Salar de Uyuni 3D/2N',
                'description' => 'Experiencia única en el salar más grande del mundo con alojamiento en hotel de sal y tours fotográficos al amanecer.',
                'price' => '420',
                'duration' => '3 días / 2 noches',
                'difficulty' => 'Fácil',
                'min_people' => '4',
                'max_people' => '12',
                'includes' => 'Hotel de sal, todas las comidas, transporte 4x4, guía',
                'destinations' => ['uyuni'],
                'categories' => ['paisajistico', 'fotografico'],
                'gallery' => [
                    'https://picsum.photos/800/600?random=3012',
                    'https://picsum.photos/800/600?random=3013'
                ]
            ]
        ];

        $this->destinations_data = [
            [
                'name' => 'Cusco',
                'slug' => 'cusco',
                'description' => 'La capital histórica del Imperio Inca, puerta de entrada a Machu Picchu.',
                'image' => 'https://picsum.photos/400/300?random=4001'
            ],
            [
                'name' => 'Machu Picchu',
                'slug' => 'machu-picchu',
                'description' => 'La ciudadela inca más famosa del mundo, Patrimonio de la Humanidad.',
                'image' => 'https://picsum.photos/400/300?random=4002'
            ],
            [
                'name' => 'Lima',
                'slug' => 'lima',
                'description' => 'Capital gastronómica de América y centro histórico colonial.',
                'image' => 'https://images.unsplash.com/photo-1531968455001-5c5272a41129?w=400&h=300&fit=crop'
            ],
            [
                'name' => 'Iquitos',
                'slug' => 'iquitos',
                'description' => 'Puerta de entrada a la Amazonía peruana.',
                'image' => 'https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?w=400&h=300&fit=crop'
            ],
            [
                'name' => 'Huacachina',
                'slug' => 'huacachina',
                'description' => 'Oasis natural en medio del desierto peruano.',
                'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400&h=300&fit=crop'
            ],
            [
                'name' => 'Salar de Uyuni',
                'slug' => 'uyuni',
                'description' => 'El salar más grande del mundo con paisajes surrealistas.',
                'image' => 'https://images.unsplash.com/photo-1586901533048-0e856dff47cd?w=400&h=300&fit=crop'
            ],
            [
                'name' => 'Amazonas',
                'slug' => 'amazonas',
                'description' => 'La selva tropical más biodiversa del planeta.',
                'image' => 'https://images.unsplash.com/photo-1518837695005-2083093ee35b?w=400&h=300&fit=crop'
            ],
            [
                'name' => 'Huascarán',
                'slug' => 'huascaran',
                'description' => 'Parque Nacional con la montaña tropical más alta del mundo.',
                'image' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?w=400&h=300&fit=crop'
            ]
        ];

        $this->pages_data = [
            [
                'title' => 'Inicio',
                'slug' => 'inicio',
                'content' => $this->get_home_page_content(),
                'is_home' => true
            ],
            [
                'title' => 'Acerca de Nosotros',
                'slug' => 'acerca-de',
                'content' => '<div class="bg-gradient-to-r from-blue-50/50 to-teal-50/50 rounded-2xl p-8 mb-12 border border-blue-100/50">
    <h2 class="text-3xl font-bold text-slate-800 mb-6">Nuestra Historia</h2>
    <p class="text-lg text-gray-700 leading-relaxed mb-6">Somos <strong>Mystical Terra</strong>, una agencia de viajes especializada en destinos de América del Sur con más de <span class="font-semibold text-blue-700">15 años de experiencia</span>. Nuestro equipo de expertos locales te guiará en aventuras inolvidables a través de paisajes místicos y culturas ancestrales.</p>
    
    <blockquote class="border-l-4 border-blue-500 bg-blue-50/50 py-4 px-6 rounded-r-lg font-medium text-slate-700 mb-8">
        "Creemos que cada viaje es una oportunidad de transformación personal y conexión con la naturaleza y las culturas locales."
    </blockquote>
</div>

<h3 class="text-2xl font-bold text-slate-800 mb-6">¿Por qué elegirnos?</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
    <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow duration-300 border border-gray-100">
        <div class="w-12 h-12 mb-4 text-blue-600">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </div>
        <h4 class="text-xl font-semibold text-slate-800 mb-3">Guías Locales Certificados</h4>
        <p class="text-gray-700">Profesionales nacidos y criados en cada destino, con certificaciones internacionales y conocimiento ancestral.</p>
    </div>
    
    <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow duration-300 border border-gray-100">
        <div class="w-12 h-12 mb-4 text-emerald-600">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
        </div>
        <h4 class="text-xl font-semibold text-slate-800 mb-3">Grupos Pequeños</h4>
        <p class="text-gray-700">Máximo 12 personas para experiencias personalizadas y un impacto mínimo en el medio ambiente.</p>
    </div>
    
    <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow duration-300 border border-gray-100">
        <div class="w-12 h-12 mb-4 text-green-600">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
        </div>
        <h4 class="text-xl font-semibold text-slate-800 mb-3">Turismo Sostenible</h4>
        <p class="text-gray-700">Compromiso real con la conservación del medio ambiente y el desarrollo de comunidades locales.</p>
    </div>
    
    <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow duration-300 border border-gray-100">
        <div class="w-12 h-12 mb-4 text-purple-600">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
            </svg>
        </div>
        <h4 class="text-xl font-semibold text-slate-800 mb-3">Soporte 24/7</h4>
        <p class="text-gray-700">Asistencia completa durante todo tu viaje, desde la planificación hasta el regreso a casa.</p>
    </div>
</div>

<div class="bg-gradient-to-r from-slate-800 to-slate-700 text-white rounded-2xl p-8 text-center">
    <p class="text-xl font-light leading-relaxed">Descubre destinos únicos con la <strong>seguridad y confianza</strong> que solo una empresa con nuestra trayectoria puede ofrecerte. Tu aventura comienza aquí.</p>
    <div class="mt-6">
        <a href="/tours" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition-colors duration-300">
            Ver Nuestros Tours
            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </a>
    </div>
</div>'
            ],
            [
                'title' => 'Nuestros Servicios',
                'slug' => 'servicios',
                'content' => '<div class="text-center mb-16">
    <h2 class="text-4xl font-bold text-slate-800 mb-6">Servicios Completos de Viaje</h2>
    <p class="text-xl text-gray-600 max-w-3xl mx-auto">Todo lo que necesitas para tu aventura perfecta en un solo lugar. Desde la planificación hasta el último detalle de tu regreso.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-16">
    <div class="bg-gradient-to-br from-blue-600 to-blue-700 text-white rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-shadow duration-500">
        <div class="w-16 h-16 mb-6 text-blue-100">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
            </svg>
        </div>
        <h3 class="text-2xl font-bold mb-4">Tours Personalizados</h3>
        <p class="text-blue-100 leading-relaxed mb-6">Diseñamos itinerarios únicos según tus intereses, presupuesto y nivel de aventura. Desde caminatas suaves hasta expediciones extremas.</p>
        <ul class="text-blue-100 space-y-2">
            <li class="flex items-center"><span class="mr-2">✓</span> Itinerarios a medida</li>
            <li class="flex items-center"><span class="mr-2">✓</span> Flexibilidad total</li>
            <li class="flex items-center"><span class="mr-2">✓</span> Todos los niveles</li>
        </ul>
    </div>
    
    <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 text-white rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-shadow duration-500">
        <div class="w-16 h-16 mb-6 text-emerald-100">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
        </div>
        <h3 class="text-2xl font-bold mb-4">Alojamientos Premium</h3>
        <p class="text-emerald-100 leading-relaxed mb-6">Desde hoteles boutique en centros históricos hasta lodges eco-friendly en ubicaciones remotas e impresionantes.</p>
        <ul class="text-emerald-100 space-y-2">
            <li class="flex items-center"><span class="mr-2">✓</span> Hoteles boutique</li>
            <li class="flex items-center"><span class="mr-2">✓</span> Lodges eco-friendly</li>
            <li class="flex items-center"><span class="mr-2">✓</span> Ubicaciones únicas</li>
        </ul>
    </div>
    
    <div class="bg-gradient-to-br from-purple-600 to-purple-700 text-white rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-shadow duration-500">
        <div class="w-16 h-16 mb-6 text-purple-100">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
            </svg>
        </div>
        <h3 class="text-2xl font-bold mb-4">Transporte Privado</h3>
        <p class="text-purple-100 leading-relaxed mb-6">Vehículos privados, vuelos domésticos y transporte especializado para llegar a los lugares más remotos y espectaculares.</p>
        <ul class="text-purple-100 space-y-2">
            <li class="flex items-center"><span class="mr-2">✓</span> Vehículos 4x4</li>
            <li class="flex items-center"><span class="mr-2">✓</span> Vuelos internos</li>
            <li class="flex items-center"><span class="mr-2">✓</span> Traslados VIP</li>
        </ul>
    </div>
    
    <div class="bg-gradient-to-br from-orange-600 to-orange-700 text-white rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-shadow duration-500">
        <div class="w-16 h-16 mb-6 text-orange-100">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </div>
        <h3 class="text-2xl font-bold mb-4">Guías Especializados</h3>
        <p class="text-orange-100 leading-relaxed mb-6">Profesionales certificados que hablan varios idiomas y conocen cada rincón, historia y secreto de nuestros destinos.</p>
        <ul class="text-orange-100 space-y-2">
            <li class="flex items-center"><span class="mr-2">✓</span> Multilingües</li>
            <li class="flex items-center"><span class="mr-2">✓</span> Certificados</li>
            <li class="flex items-center"><span class="mr-2">✓</span> Conocimiento local</li>
        </ul>
    </div>
</div>

<div class="bg-gradient-to-r from-slate-50 to-gray-100 rounded-2xl p-12 text-center">
    <h3 class="text-3xl font-bold text-slate-800 mb-6">¿Listo para tu próxima aventura?</h3>
    <p class="text-lg text-gray-700 mb-8 max-w-2xl mx-auto">Contacta con nuestro equipo de expertos para diseñar el viaje perfecto según tus sueños y expectativas.</p>
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="/contacto" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition-colors duration-300 shadow-lg hover:shadow-xl">
            Planifica tu Viaje
            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </a>
        <a href="/tours" class="inline-flex items-center bg-white hover:bg-gray-50 text-slate-700 font-semibold py-3 px-8 rounded-lg transition-colors duration-300 border border-gray-300 shadow-lg hover:shadow-xl">
            Ver Tours Disponibles
        </a>
    </div>
</div>'
            ],
            [
                'title' => 'Contacto',
                'slug' => 'contacto',
                'content' => '<h2>Contáctanos</h2>
<p>¿Listo para tu próxima aventura? Estamos aquí para ayudarte a planificar el viaje perfecto.</p>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
<div>
<h3>Información de Contacto</h3>
<p><strong>Teléfono:</strong> +51 984 123 456</p>
<p><strong>Email:</strong> info@mysticalterra.com</p>
<p><strong>WhatsApp:</strong> +51 984 123 456</p>
</div>

<div>
<h3>Horarios de Atención</h3>
<p><strong>Lunes a Viernes:</strong> 8:00 AM - 8:00 PM</p>
<p><strong>Sábados:</strong> 9:00 AM - 6:00 PM</p>
<p><strong>Domingos:</strong> 10:00 AM - 4:00 PM</p>
</div>
</div>

<p>También puedes visitarnos en nuestras oficinas en Cusco para una consulta personalizada.</p>'
            ],
            [
                'title' => 'Preguntas Frecuentes',
                'slug' => 'faq',
                'content' => '<!-- wp:wptbt/faq-block {"title":"Preguntas Frecuentes","subtitle":"Resolvemos las dudas más comunes sobre nuestros tours y servicios","faqs":[{"question":"¿Qué incluyen los tours?","answer":"Cada tour incluye detalles específicos en su descripción. Generalmente incluimos transporte, alojamiento, algunas comidas, guía profesional bilingüe, todas las entradas necesarias y seguro básico de viaje. Los tours premium incluyen equipos especializados."},{"question":"¿Se requiere seguro de viaje?","answer":"Altamente recomendado para todas las actividades de aventura. Ofrecemos seguros especializados según tu destino y actividades planificadas. Nuestro equipo te ayudará a elegir la cobertura adecuada."},{"question":"¿Cuál es la política de cancelación?","answer":"Cancelaciones con 30+ días de anticipación: reembolso del 90%. Entre 15-29 días: 50%. Menos de 15 días: no reembolsable, salvo por emergencias médicas documentadas. Consulta términos específicos por tour."},{"question":"¿Proporcionan equipos para trekking?","answer":"Sí, incluimos equipos básicos de trekking (bastones, capas de lluvia, botiquín grupal). Equipos premium como sleeping bags de alta montaña, carpas técnicas y crampones pueden alquilarse."},{"question":"¿Hay descuentos para grupos?","answer":"Sí, ofrecemos descuentos progresivos: 4-7 personas (5% desc.), 8-11 personas (10% desc.), 12+ personas (15% desc.). Grupos corporativos y familias tienen tarifas especiales."},{"question":"¿Qué pasa si el clima no acompaña?","answer":"Tenemos planes alternativos para cada tour. En casos de clima extremo que impida la actividad principal, ofrecemos tours alternativos o reembolso parcial según las circunstancias."},{"question":"¿Los guías hablan otros idiomas además del español?","answer":"Sí, todos nuestros guías principales son bilingües (español-inglés). También contamos con guías que hablan portugués, francés y alemán para grupos específicos."},{"question":"¿Cómo es la comida durante los tours?","answer":"Ofrecemos cocina local auténtica preparada por chefs especializados en alta montaña. Adaptamos menús para vegetarianos, veganos y restricciones alimentarias. Siempre incluye opciones nutritivas y energéticas."}],"backgroundColor":"#f8fafc"} /-->'
            ],
            [
                'title' => 'Destinos Imperdibles',
                'slug' => 'destinos',
                'content' => '<!-- wp:wptbt/destinations-carousel {"title":"Destinos que Transforman Vidas","subtitle":"Explora los lugares más místicos y espectaculares de Sudamérica","showViewMore":true,"backgroundColor":"#ffffff","autoplay":true} /-->

<!-- wp:wptbt/interactive-map-block {"title":"Mapa de Aventuras","subtitle":"Cada pin es una puerta a experiencias únicas e inolvidables","mapCenter":{"lat":-13.5320,"lng":-71.9675},"mapZoom":5,"markers":[{"lat":-13.1631,"lng":-72.5450,"title":"Machu Picchu","description":"La ciudadela inca más famosa del mundo, declarada Patrimonio de la Humanidad"},{"lat":-13.5320,"lng":-71.9675,"title":"Cusco","description":"Capital histórica del Imperio Inca, puerta de entrada a la cultura ancestral"},{"lat":-12.0464,"lng":-77.0428,"title":"Lima","description":"Capital gastronómica de América, fusión perfecta de tradición e innovación"},{"lat":-3.7437,"lng":-73.2516,"title":"Iquitos","description":"Puerta de entrada a la Amazonía peruana, biodiversidad única"},{"lat":-14.0875,"lng":-75.7626,"title":"Huacachina","description":"Oasis mágico en el desierto, aventuras en buggy y sandboard"},{"lat":-20.1338,"lng":-67.4891,"title":"Salar de Uyuni","description":"El espejo del cielo, el salar más grande del mundo"},{"lat":-9.0965,"lng":-77.6051,"title":"Cordillera Blanca","description":"Montañas nevadas tropicales, paraíso del trekking"},{"lat":-16.4090,"lng":-72.1470,"title":"Cañón del Colca","description":"Uno de los cañones más profundos del mundo, vuelo de cóndores"}],"backgroundColor":"#f8fafc"} /-->

<!-- wp:wptbt/gallery-block {"title":"Momentos Mágicos","subtitle":"Cada foto cuenta una historia de aventura y descubrimiento","images":[{"id":1,"url":"https://picsum.photos/600/400?random=5001","alt":"Amanecer dorado en Machu Picchu"},{"id":2,"url":"https://picsum.photos/600/400?random=5002","alt":"Sendero ancestral del Camino Inca"},{"id":3,"url":"https://picsum.photos/600/400?random=5003","alt":"Atardecer mágico en Huacachina"},{"id":4,"url":"https://picsum.photos/600/400?random=5004","alt":"Vida salvaje en el Amazonas"},{"id":5,"url":"https://picsum.photos/600/400?random=5005","alt":"Reflejos infinitos en Uyuni"},{"id":6,"url":"https://picsum.photos/600/400?random=5006","alt":"Laguna turquesa de montaña"}],"backgroundColor":"#ffffff"} /-->'
            ],
            [
                'title' => 'Experiencias Premium',
                'slug' => 'experiencias-premium',
                'content' => '<!-- wp:wptbt/tours-carousel-block {"title":"Tours de Lujo y Aventura","subtitle":"Experiencias exclusivas diseñadas para viajeros exigentes","postsPerPage":8,"backgroundColor":"#0f172a","textColor":"#ffffff","autoplaySpeed":4000,"showDots":true,"showArrows":true} /-->

<!-- wp:wptbt/benefits-block {"title":"Experiencias que Marcan la Diferencia","subtitle":"Cada detalle pensado para crear recuerdos únicos","benefits":[{"icon":"🏔️","title":"Acceso Exclusivo","description":"Lugares y experiencias que solo nosotros podemos ofrecer gracias a nuestros contactos locales"},{"icon":"👨‍🍳","title":"Gastronomía Gourmet","description":"Chefs especializados que combinan técnicas modernas con ingredientes ancestrales"},{"icon":"🏨","title":"Alojamientos Únicos","description":"Desde ecolodges de lujo hasta glamping en ubicaciones espectaculares"},{"icon":"🚁","title":"Transporte Premium","description":"Helicópteros, vuelos panorámicos y vehículos de alta gama para máximo confort"},{"icon":"📸","title":"Fotografía Profesional","description":"Sesiones con fotógrafos especializados en paisajes y aventura incluidas"},{"icon":"💎","title":"Grupos VIP","description":"Máximo 6 personas por tour para experiencias completamente personalizadas"}],"backgroundColor":"#f1f5f9"} /-->

<!-- wp:wptbt/google-reviews-block {"title":"Testimonios de Lujo","subtitle":"Lo que dicen nuestros viajeros más exigentes sobre sus experiencias premium","reviews":[{"name":"Isabella Fontana","location":"Milano, Italia","rating":5,"text":"Experiencia absolutamente extraordinaria. Cada detalle fue perfecto, desde el helicopter al Machu Picchu hasta la cena gourmet con vista a los Andes. Servicio impecable.","avatar":"https://images.unsplash.com/photo-1494790108755-2616b612b765?w=100&h=100&fit=crop&crop=face"},{"name":"James Wellington","location":"London, UK","rating":5,"text":"As a luxury travel connoisseur, I can say this exceeded all expectations. The exclusive access and personalized service made this the trip of a lifetime.","avatar":"https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop&crop=face"},{"name":"Sofia Andersson","location":"Stockholm, Sweden","rating":5,"text":"Incredible attention to detail. The private lodge in the Amazon with gourmet dining and spa services was beyond anything I could have imagined. Simply magical.","avatar":"https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&h=100&fit=crop&crop=face"}],"backgroundColor":"#ffffff"} /-->

<!-- wp:wptbt/booking-block {"title":"¿Listo para lo Extraordinario?","subtitle":"Reserva tu experiencia premium con anticipación - plazas limitadas","backgroundColor":"#0f172a","textColor":"#ffffff"} /-->'
            ],
            [
                'title' => 'Home Example - Todos los Bloques',
                'slug' => 'home-example',
                'content' => '<!-- wp:heading {"level":2,"textAlign":"center","style":{"typography":{"fontSize":"3rem","fontWeight":"700"},"color":{"text":"#1e293b"}}} -->
<h2 class="has-text-align-center has-text-color" style="color:#1e293b;font-size:3rem;font-weight:700">Demostración Completa de Bloques</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.25rem"},"color":{"text":"#64748b"},"spacing":{"margin":{"bottom":"3rem"}}}} -->
<p class="has-text-align-center has-text-color" style="color:#64748b;font-size:1.25rem;margin-bottom:3rem">Esta página muestra todos los bloques personalizados disponibles en el tema para agencias de viajes</p>
<!-- /wp:paragraph -->

<!-- wp:separator {"backgroundColor":"primary","className":"is-style-wide"} -->
<hr class="wp-block-separator has-primary-background-color has-background is-style-wide"/>
<!-- /wp:separator -->

<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.5rem","fontWeight":"600"},"color":{"text":"#374151"},"spacing":{"margin":{"top":"2rem","bottom":"1rem"}}}} -->
<h3 class="has-text-color" style="color:#374151;font-size:1.5rem;font-weight:600;margin-top:2rem;margin-bottom:1rem">Tours Carousel Block</h3>
<!-- /wp:heading -->

<!-- wp:wptbt/tours-carousel-block {"title":"Tours Destacados","subtitle":"Explora nuestras aventuras más populares con navegación automática","postsPerPage":8,"backgroundColor":"#f8fafc","autoplaySpeed":3000,"showDots":true,"showArrows":true,"infinite":true,"pauseOnHover":true} /-->

<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.5rem","fontWeight":"600"},"color":{"text":"#374151"},"spacing":{"margin":{"top":"3rem","bottom":"1rem"}}}} -->
<h3 class="has-text-color" style="color:#374151;font-size:1.5rem;font-weight:600;margin-top:3rem;margin-bottom:1rem">Destinations Carousel Block</h3>
<!-- /wp:heading -->

<!-- wp:wptbt/destinations-carousel {"title":"Destinos Populares","subtitle":"Presenta destinos populares de forma atractiva con efectos visuales","showViewMore":true,"backgroundColor":"#ffffff","autoplay":true} /-->

<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.5rem","fontWeight":"600"},"color":{"text":"#374151"},"spacing":{"margin":{"top":"3rem","bottom":"1rem"}}}} -->
<h3 class="has-text-color" style="color:#374151;font-size:1.5rem;font-weight:600;margin-top:3rem;margin-bottom:1rem">Benefits Block</h3>
<!-- /wp:heading -->

<!-- wp:wptbt/benefits-block {"title":"Ventajas y Beneficios","subtitle":"Destaca las ventajas y beneficios de tus servicios","benefits":[{"icon":"target","title":"Precisión Total","description":"Cada detalle planificado meticulosamente para tu experiencia perfecta"},{"icon":"star","title":"Calidad Premium","description":"Servicios de la más alta calidad con estándares internacionales"},{"icon":"rocket","title":"Innovación Constante","description":"Incorporamos las últimas tecnologías y tendencias del turismo"},{"icon":"diamond","title":"Experiencias Únicas","description":"Acceso exclusivo a lugares y vivencias que otros no pueden ofrecer"},{"icon":"shield","title":"Seguridad Garantizada","description":"Protocolos de seguridad rigurosos y seguros especializados"},{"icon":"globe","title":"Sostenibilidad","description":"Compromiso real con el medio ambiente y comunidades locales"}],"backgroundColor":"#f1f5f9"} /-->

<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.5rem","fontWeight":"600"},"color":{"text":"#374151"},"spacing":{"margin":{"top":"3rem","bottom":"1rem"}}}} -->
<h3 class="has-text-color" style="color:#374151;font-size:1.5rem;font-weight:600;margin-top:3rem;margin-bottom:1rem">Services Block</h3>
<!-- /wp:heading -->

<!-- wp:wptbt/services-block {"title":"Servicios Profesionales","subtitle":"Organiza y presenta tus servicios de manera profesional","services":[{"icon":"map","title":"Planificación Personalizada","description":"Diseñamos itinerarios únicos adaptados a tus intereses específicos y presupuesto disponible","buttonText":"Planificar Viaje","buttonUrl":"/contacto"},{"icon":"building","title":"Alojamientos Exclusivos","description":"Desde boutique hotels hasta eco-lodges en las ubicaciones más espectaculares","buttonText":"Ver Hoteles","buttonUrl":"/alojamientos"},{"icon":"truck","title":"Transporte VIP","description":"Helicópteros, jets privados, vehículos 4x4 y todo lo necesario para llegar donde otros no pueden","buttonText":"Ver Opciones","buttonUrl":"/transporte"},{"icon":"camera","title":"Fotografía Profesional","description":"Sesiones con fotógrafos especializados en viajes y aventura incluidas en tours premium","buttonText":"Ver Galería","buttonUrl":"/galeria"}],"backgroundColor":"#ffffff"} /-->

<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.5rem","fontWeight":"600"},"color":{"text":"#374151"},"spacing":{"margin":{"top":"3rem","bottom":"1rem"}}}} -->
<h3 class="has-text-color" style="color:#374151;font-size:1.5rem;font-weight:600;margin-top:3rem;margin-bottom:1rem">Gallery Block</h3>
<!-- /wp:heading -->

<!-- wp:wptbt/gallery-block {"title":"Galería de Aventuras","subtitle":"Muestra imágenes de forma elegante con efectos visuales","images":[{"id":1,"url":"https://picsum.photos/600/400?random=6001","alt":"Paisaje montañoso al amanecer"},{"id":2,"url":"https://picsum.photos/600/400?random=6002","alt":"Aventura en el desierto"},{"id":3,"url":"https://picsum.photos/600/400?random=6003","alt":"Exploración de selva tropical"},{"id":4,"url":"https://picsum.photos/600/400?random=6004","alt":"Cultura y tradiciones locales"},{"id":5,"url":"https://picsum.photos/600/400?random=6005","alt":"Gastronomía regional auténtica"},{"id":6,"url":"https://picsum.photos/600/400?random=6006","alt":"Aventuras acuáticas"},{"id":7,"url":"https://picsum.photos/600/400?random=6007","alt":"Arte y arquitectura"},{"id":8,"url":"https://picsum.photos/600/400?random=6008","alt":"Vida nocturna y entretenimiento"}],"backgroundColor":"#f8fafc"} /-->

<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.5rem","fontWeight":"600"},"color":{"text":"#374151"},"spacing":{"margin":{"top":"3rem","bottom":"1rem"}}}} -->
<h3 class="has-text-color" style="color:#374151;font-size:1.5rem;font-weight:600;margin-top:3rem;margin-bottom:1rem">Google Reviews Block</h3>
<!-- /wp:heading -->

<!-- wp:wptbt/google-reviews-block {"title":"Testimonios de Clientes","subtitle":"Muestra testimonios reales de clientes satisfechos","reviews":[{"name":"Alexandra Thompson","location":"Sydney, Australia","rating":5,"text":"Absolutely phenomenal experience! Every detail was perfectly orchestrated. The guides were knowledgeable, passionate, and made our journey truly unforgettable. Exceeded all expectations!","avatar":"https://picsum.photos/100/100?random=7001"},{"name":"Marco Rossi","location":"Roma, Italia","rating":5,"text":"Un viaggio straordinario! L\'organizzazione impeccabile, guide esperte e paesaggi mozzafiato. Un\'esperienza che porterò per sempre nel cuore. Altamente raccomandato!","avatar":"https://picsum.photos/100/100?random=7002"},{"name":"Hans Mueller","location":"Berlin, Deutschland","rating":5,"text":"Fantastische Reise! Perfekte Organisation, erstklassige Unterkünfte und unvergessliche Erlebnisse. Das Team ging weit über unsere Erwartungen hinaus. Absolut empfehlenswert!","avatar":"https://picsum.photos/100/100?random=7003"},{"name":"Sophie Dubois","location":"Paris, France","rating":5,"text":"Voyage exceptionnel ! L\'attention aux détails, la qualité des services et l\'expertise des guides ont rendu cette expérience inoubliable. Un professionnalisme remarquable !","avatar":"https://picsum.photos/100/100?random=7004"}],"backgroundColor":"#ffffff"} /-->

<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.5rem","fontWeight":"600"},"color":{"text":"#374151"},"spacing":{"margin":{"top":"3rem","bottom":"1rem"}}}} -->
<h3 class="has-text-color" style="color:#374151;font-size:1.5rem;font-weight:600;margin-top:3rem;margin-bottom:1rem">FAQ Block</h3>
<!-- /wp:heading -->

<!-- wp:wptbt/faq-block {"title":"Preguntas Frecuentes","subtitle":"Responde preguntas frecuentes de forma organizada y accesible","faqs":[{"question":"¿Cómo funciona el sistema de reservas?","answer":"Nuestro sistema de reservas es completamente digital y seguro. Puedes reservar online con confirmación inmediata, pagar con tarjeta o transferencia, y recibir todos los documentos por email. También ofrecemos atención personalizada 24/7."},{"question":"¿Qué está incluido en los paquetes premium?","answer":"Los paquetes premium incluyen: alojamiento en hoteles 5 estrellas, transporte privado de lujo, guías especializados bilingües, todas las comidas gourmet, actividades exclusivas, seguro premium, fotografía profesional y concierge personal."},{"question":"¿Cómo manejan situaciones imprevistas?","answer":"Tenemos protocolos establecidos para cualquier eventualidad: seguros completos, planes de contingencia, comunicación 24/7, equipos de emergencia locales, y flexibilidad total para reprogramar o modificar itinerarios según sea necesario."},{"question":"¿Ofrecen tours completamente personalizados?","answer":"¡Absolutamente! Nuestro equipo de diseño de experiencias crea itinerarios únicos basados en tus intereses, presupuesto, fechas y nivel de aventura deseado. Cada viaje es una obra maestra personalizada."},{"question":"¿Cuál es su política de sostenibilidad?","answer":"Estamos comprometidos con el turismo responsable: trabajamos solo con proveedores locales certificados, compensamos la huella de carbono, apoyamos proyectos comunitarios, y seguimos estrictos protocolos de conservación ambiental."},{"question":"¿Qué diferencia tienen sus guías?","answer":"Nuestros guías son profesionales certificados, locales nativos con estudios especializados, multilingües (mínimo 3 idiomas), con años de experiencia y pasión genuina por compartir su cultura y conocimientos ancestrales."}],"backgroundColor":"#f1f5f9"} /-->

<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.5rem","fontWeight":"600"},"color":{"text":"#374151"},"spacing":{"margin":{"top":"3rem","bottom":"1rem"}}}} -->
<h3 class="has-text-color" style="color:#374151;font-size:1.5rem;font-weight:600;margin-top:3rem;margin-bottom:1rem">Interactive Map Block</h3>
<!-- /wp:heading -->

<!-- wp:wptbt/interactive-map-block {"title":"Mapa Interactivo","subtitle":"Mapa interactivo que muestra destinos y puntos de interés","mapCenter":{"lat":-13.5320,"lng":-71.9675},"mapZoom":4,"markers":[{"lat":-13.1631,"lng":-72.5450,"title":"Machu Picchu","description":"Ciudadela inca Patrimonio de la Humanidad - Experiencia mística al amanecer"},{"lat":-13.5320,"lng":-71.9675,"title":"Cusco","description":"Capital del Imperio Inca - Centro histórico colonial y mercados artesanales"},{"lat":-12.0464,"lng":-77.0428,"title":"Lima","description":"Capital gastronómica mundial - Fusión de culturas milenarias"},{"lat":-3.7437,"lng":-73.2516,"title":"Iquitos","description":"Amazonía peruana - Biodiversidad única y culturas ancestrales"},{"lat":-14.0875,"lng":-75.7626,"title":"Huacachina","description":"Oasis del desierto - Aventuras en dunas y sandboarding extremo"},{"lat":-20.1338,"lng":-67.4891,"title":"Salar de Uyuni","description":"Espejo del cielo - Paisajes surrealistas y fotografía única"},{"lat":-9.0965,"lng":-77.6051,"title":"Cordillera Blanca","description":"Montañas glaciares tropicales - Trekking de alta montaña"},{"lat":-16.4090,"lng":-72.1470,"title":"Cañón del Colca","description":"Uno de los cañones más profundos - Vuelo de cóndores gigantes"},{"lat":-11.8775,"lng":-77.0090,"title":"Cordillera Huayhuash","description":"Circuito de trekking extremo - Para aventureros experimentados"},{"lat":-5.1951,"lng":-80.6328,"title":"Máncora","description":"Paraíso surfero - Playas tropicales y vida nocturna"}],"backgroundColor":"#ffffff"} /-->

<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.5rem","fontWeight":"600"},"color":{"text":"#374151"},"spacing":{"margin":{"top":"3rem","bottom":"1rem"}}}} -->
<h3 class="has-text-color" style="color:#374151;font-size:1.5rem;font-weight:600;margin-top:3rem;margin-bottom:1rem">Booking Block</h3>
<!-- /wp:heading -->

<!-- wp:wptbt/booking-block {"title":"Reserva tu Aventura","subtitle":"Call-to-action prominente para generar conversiones y reservas inmediatas","backgroundColor":"#0f172a","textColor":"#ffffff"} /-->

<!-- wp:heading {"level":2,"textAlign":"center","style":{"typography":{"fontSize":"2.5rem","fontWeight":"600"},"color":{"text":"#059669"},"spacing":{"margin":{"top":"4rem","bottom":"2rem"}}}} -->
<h2 class="has-text-align-center has-text-color" style="color:#059669;font-size:2.5rem;font-weight:600;margin-top:4rem;margin-bottom:2rem">Todos los Bloques en Acción</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.125rem"},"color":{"text":"#6b7280"},"spacing":{"margin":{"bottom":"3rem"}}}} -->
<p class="has-text-align-center has-text-color" style="color:#6b7280;font-size:1.125rem;margin-bottom:3rem">Esta página demuestra la versatilidad y potencia de todos los bloques personalizados disponibles en el tema. Cada bloque está optimizado para conversión, SEO y experiencia de usuario.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"primary","textColor":"white","style":{"border":{"radius":"50px"},"spacing":{"padding":{"left":"2rem","right":"2rem","top":"1rem","bottom":"1rem"}},"typography":{"fontWeight":"600"}}} -->
<div class="wp-block-button"><a class="wp-block-button__link has-white-color has-primary-background-color has-text-color has-background wp-element-button" style="border-radius:50px;padding-top:1rem;padding-right:2rem;padding-bottom:1rem;padding-left:2rem;font-weight:600">Ver Implementación en Vivo</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->'
            ]
        ];
    }

    public function admin_page()
    {
        ?>
        <div class="wrap">
            <h1>Generador de Datos de Ejemplo</h1>
            <p>Esta herramienta te permite cargar datos de ejemplo para ver el tema funcionando con contenido completo.</p>
            
            <div class="card">
                <h2>¿Qué se generará?</h2>
                <ul>
                    <li><strong><?php echo count($this->tours_data); ?> Tours</strong> - Con galerías de imágenes, precios y detalles completos</li>
                    <li><strong><?php echo count($this->destinations_data); ?> Destinos</strong> - Con imágenes y descripciones</li>
                    <li><strong><?php echo count($this->pages_data); ?> Páginas</strong> - Incluyendo una página de inicio completa con todos los bloques</li>
                    <li><strong>Página de Inicio</strong> - Con banner carousel, tours, destinos, servicios, reservas, galería, reseñas, FAQ y mapa</li>
                    <li><strong>Categorías</strong> - Categorías de tours organizadas</li>
                    <li><strong>Menús</strong> - Menú de navegación principal</li>
                    <li><strong>Banner Personalizado</strong> - Para la página de inicio con 4 imágenes rotativas</li>
                </ul>
                
                <div style="background: #e7f3ff; padding: 15px; border-radius: 5px; margin-top: 15px;">
                    <h4 style="margin: 0 0 10px 0; color: #0073aa;">✨ Nuevo: Página de Inicio Completa</h4>
                    <p style="margin: 0; font-size: 14px;">La nueva página de inicio incluye <strong>todos los bloques disponibles</strong> en tu tema: carruseles de tours y destinos, beneficios, servicios, formulario de reserva, galería, reseñas de Google, preguntas frecuentes y mapa interactivo.</p>
                </div>
            </div>

            <div class="card">
                <h2>Generar Contenido</h2>
                <p><strong>Nota:</strong> Esta acción creará nuevo contenido. Si ya tienes datos, se añadirán a los existentes.</p>
                
                <div id="generation-progress" style="display: none;">
                    <h3>Generando contenido...</h3>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 0%;"></div>
                    </div>
                    <div id="progress-text">Iniciando...</div>
                </div>

                <div id="generation-controls">
                    <button type="button" id="generate-all" class="button button-primary button-hero">
                        Generar Todos los Datos de Ejemplo
                    </button>
                    
                    <h3>O generar por categoría:</h3>
                    <button type="button" id="generate-destinations" class="button">Solo Destinos</button>
                    <button type="button" id="generate-tours" class="button">Solo Tours</button>
                    <button type="button" id="generate-pages" class="button">Solo Páginas</button>
                </div>

                <div id="generation-results" style="display: none;">
                    <h3>Resultados:</h3>
                    <div id="results-content"></div>
                    <button type="button" id="reset-generator" class="button">Generar Más Contenido</button>
                </div>
            </div>

            <div class="card">
                <h2>Limpiar Datos</h2>
                <p>Si quieres empezar desde cero, puedes eliminar todo el contenido generado.</p>
                <button type="button" id="clean-data" class="button button-secondary">
                    Limpiar Datos de Ejemplo
                </button>
                <p><small><strong>Advertencia:</strong> Esto eliminará TODOS los tours, destinos y páginas. Esta acción no se puede deshacer.</small></p>
            </div>
        </div>

        <style>
        .card {
            background: #fff;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            padding: 20px;
            margin: 20px 0;
        }
        .progress-bar {
            background: #f1f1f1;
            border-radius: 10px;
            height: 20px;
            margin: 10px 0;
        }
        .progress-fill {
            background: #0073aa;
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s ease;
        }
        #progress-text {
            font-style: italic;
            color: #666;
        }
        </style>
        <?php
    }

    public function handle_ajax_request()
    {
        check_ajax_referer('sample_data_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('No tienes permisos para realizar esta acción');
        }

        $action_type = sanitize_text_field($_POST['action_type']);
        $results = [];

        switch ($action_type) {
            case 'generate_all':
                $results['destinations'] = $this->generate_destinations();
                $results['tours'] = $this->generate_tours();
                $results['pages'] = $this->generate_pages();
                $results['menu'] = $this->create_navigation_menu();
                
                // Flush rewrite rules para evitar 404s
                flush_rewrite_rules();
                
                // Verificar permalinks después del flush
                $results['permalink_check'] = $this->verify_permalinks();
                
                break;
                
            case 'generate_destinations':
                $results['destinations'] = $this->generate_destinations();
                flush_rewrite_rules();
                break;
                
            case 'generate_tours':
                $results['tours'] = $this->generate_tours();
                flush_rewrite_rules();
                break;
                
            case 'generate_pages':
                $results['pages'] = $this->generate_pages();
                flush_rewrite_rules();
                break;
                
            case 'clean_data':
                $results['cleaned'] = $this->clean_sample_data();
                break;
                
            default:
                wp_die('Acción no válida');
        }

        wp_send_json_success($results);
    }

    private function generate_destinations()
    {
        $created = 0;
        $errors = [];

        foreach ($this->destinations_data as $destination) {
            // Verificar si ya existe
            if (term_exists($destination['slug'], 'destinations')) {
                continue;
            }

            $term = wp_insert_term(
                $destination['name'],
                'destinations',
                [
                    'slug' => $destination['slug'],
                    'description' => $destination['description']
                ]
            );

            if (!is_wp_error($term)) {
                // Guardar imagen del destino
                update_term_meta($term['term_id'], 'destination_image_url', $destination['image']);
                $created++;
            } else {
                $errors[] = $term->get_error_message();
            }
        }

        return [
            'created' => $created,
            'errors' => $errors,
            'total' => count($this->destinations_data)
        ];
    }

    private function generate_tours()
    {
        $created = 0;
        $errors = [];

        // Crear categorías de tours
        $categories = [
            'aventura' => 'Aventura',
            'cultural' => 'Cultural',
            'naturaleza' => 'Naturaleza',
            'gastronomico' => 'Gastronómico',
            'trekking' => 'Trekking',
            'ecoturismo' => 'Ecoturismo',
            'paisajistico' => 'Paisajístico',
            'fotografico' => 'Fotográfico'
        ];

        foreach ($categories as $slug => $name) {
            if (!term_exists($slug, 'tour-categories')) {
                wp_insert_term($name, 'tour-categories', ['slug' => $slug]);
            }
        }

        foreach ($this->tours_data as $tour) {
            // Verificar si ya existe un tour similar
            $existing = get_page_by_title($tour['title'], OBJECT, 'tours');
            if ($existing) {
                continue;
            }

            // Preparar metadatos completos
            $meta_input = [];
            
            // Solo agregar campos si están definidos en el array
            $simple_fields = [
                '_wptbt_tour_subtitle' => 'subtitle',
                '_tour_technical_description' => 'technical_description',
                '_tour_price_international' => 'tour_price_international',
                '_tour_price_national' => 'tour_price_national',
                '_tour_price_promotion' => 'tour_price_promotion',
                '_tour_price_original' => 'tour_price_original',
                '_tour_currency' => 'tour_currency',
                '_tour_duration' => 'tour_duration',
                '_tour_difficulty' => 'difficulty',
                '_tour_min_age' => 'min_age',
                '_tour_max_people' => 'max_people',
                '_tour_total_duration' => 'total_duration',
                '_tour_alternative_route' => 'alternative_route',
                '_tour_departure_point' => 'departure_point',
                '_tour_return_point' => 'return_point',
                '_tour_latitude' => 'latitude',
                '_tour_longitude' => 'longitude',
                '_tour_google_maps_url' => 'google_maps_url',
                '_tour_whatsapp' => 'whatsapp',
                '_tour_phone' => 'phone',
                '_tour_email' => 'email',
                '_tour_booking_url' => 'booking_url',
                '_tour_advance_payment' => 'advance_payment',
                '_tour_cancellation_policy' => 'cancellation_policy',
                '_tour_duration_days' => 'duration_days',
                '_tour_meta_description' => 'meta_description',
                '_tour_keywords' => 'keywords'
            ];
            
            foreach ($simple_fields as $meta_key => $tour_key) {
                if (isset($tour[$tour_key])) {
                    $meta_input[$meta_key] = $tour[$tour_key];
                }
            }
            
            // Campos booleanos
            $boolean_fields = [
                '_tour_includes_accommodation' => 'includes_accommodation',
                '_tour_requires_documents' => 'requires_documents',
                '_tour_has_flexible_schedule' => 'has_flexible_schedule',
                '_tour_pickup_required' => 'pickup_required',
                '_tour_emergency_contact_required' => 'emergency_contact_required',
                '_tour_featured' => 'featured',
                '_tour_popular' => 'popular',
                '_tour_new' => 'new'
            ];
            
            foreach ($boolean_fields as $meta_key => $tour_key) {
                if (isset($tour[$tour_key])) {
                    $meta_input[$meta_key] = $tour[$tour_key] ? '1' : '0';
                }
            }
            
            // Arrays simples
            $array_fields = [
                '_tour_includes' => 'includes',
                '_tour_excludes' => 'excludes',
                '_tour_what_to_bring' => 'what_to_bring',
                '_tour_recommendations' => 'recommendations',
                '_tour_required_traveler_fields' => 'required_traveler_fields',
                '_tour_languages_available' => 'languages_available',
                '_tour_departure_dates' => 'departure_dates',
                '_wptbt_tour_hours' => 'tour_hours'
            ];
            
            foreach ($array_fields as $meta_key => $tour_key) {
                if (isset($tour[$tour_key]) && is_array($tour[$tour_key])) {
                    $meta_input[$meta_key] = $tour[$tour_key];
                }
            }
            
            // Arrays complejos
            if (isset($tour['booking_prices']) && is_array($tour['booking_prices'])) {
                $meta_input['_wptbt_tour_prices'] = $tour['booking_prices'];
            }
            
            if (isset($tour['itinerary']) && is_array($tour['itinerary'])) {
                $meta_input['_tour_itinerary'] = $tour['itinerary'];
            }
            
            if (isset($tour['altitude_points']) && is_array($tour['altitude_points'])) {
                $meta_input['_tour_altitude_points'] = $tour['altitude_points'];
            }
            
            // Galería como string separado por comas
            if (isset($tour['gallery']) && is_array($tour['gallery'])) {
                $meta_input['_wptbt_tour_gallery_urls'] = implode(',', $tour['gallery']);
            }

            $tour_id = wp_insert_post([
                'post_title' => $tour['title'],
                'post_content' => $tour['description'],
                'post_status' => 'publish',
                'post_type' => 'tours',
                'meta_input' => $meta_input
            ]);

            if (!is_wp_error($tour_id) && $tour_id > 0) {
                // Asignar destinos
                if (isset($tour['destinations'])) {
                    wp_set_object_terms($tour_id, $tour['destinations'], 'destinations');
                }

                // Asignar categorías
                if (isset($tour['categories'])) {
                    wp_set_object_terms($tour_id, $tour['categories'], 'tour-categories');
                }

                // Establecer imagen destacada desde la primera imagen de galería
                if (!empty($tour['gallery'][0])) {
                    $this->set_featured_image_from_url($tour_id, $tour['gallery'][0]);
                }

                $created++;
            } else {
                $errors[] = is_wp_error($tour_id) ? $tour_id->get_error_message() : 'Error desconocido';
            }
        }

        return [
            'created' => $created,
            'errors' => $errors,
            'total' => count($this->tours_data)
        ];
    }

    private function generate_pages()
    {
        $created = 0;
        $errors = [];

        foreach ($this->pages_data as $page) {
            // Verificar si ya existe
            $existing = get_page_by_path($page['slug']);
            if ($existing) {
                continue;
            }

            $page_data = [
                'post_title' => $page['title'],
                'post_content' => $page['content'],
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => $page['slug'],
                'comment_status' => 'closed',
                'ping_status' => 'closed'
            ];

            $page_id = wp_insert_post($page_data);

            if (!is_wp_error($page_id) && $page_id > 0) {
                // Si es la página de inicio, configurarla como tal
                if (isset($page['is_home']) && $page['is_home']) {
                    update_option('show_on_front', 'page');
                    update_option('page_on_front', $page_id);
                    
                    // Configurar banner personalizado para la página de inicio
                    update_post_meta($page_id, 'wptbt_show_banner', true);
                    update_post_meta($page_id, 'wptbt_banner_title', 'Aventuras Únicas en Sudamérica');
                    update_post_meta($page_id, 'wptbt_banner_subtitle', 'Descubre paisajes increíbles, culturas milenarias y experiencias que cambiarán tu vida para siempre');
                    update_post_meta($page_id, 'wptbt_banner_button_text', 'Ver Todos los Tours');
                    // Detectar si hay /blog/ en la estructura y adaptarse
                    $tours_url = '/tours';
                    $permalink_structure = get_option('permalink_structure');
                    if (strpos($permalink_structure, '/blog/') !== false) {
                        $tours_url = '/tours'; // WordPress manejará automáticamente el prefijo
                    }
                    update_post_meta($page_id, 'wptbt_banner_button_url', $tours_url);
                    update_post_meta($page_id, 'wptbt_banner_images', $this->get_banner_images());
                    update_post_meta($page_id, 'wptbt_banner_mode', 'custom');
                }
                
                $created++;
                
                // Añadir información de la página creada para debug
                $page_url = get_permalink($page_id);
                if (!isset($results)) $results = [];
                $results[] = [
                    'title' => $page['title'],
                    'url' => $page_url,
                    'id' => $page_id
                ];
            } else {
                $errors[] = is_wp_error($page_id) ? $page_id->get_error_message() : 'Error desconocido';
            }
        }

        return [
            'created' => $created,
            'errors' => $errors,
            'total' => count($this->pages_data),
            'pages_created' => isset($results) ? $results : []
        ];
    }

    private function create_navigation_menu()
    {
        $menu_name = 'Menú Principal';
        $menu_exists = wp_get_nav_menu_object($menu_name);
        
        if ($menu_exists) {
            return ['message' => 'El menú ya existe'];
        }

        $menu_id = wp_create_nav_menu($menu_name);

        if (is_wp_error($menu_id)) {
            return ['error' => $menu_id->get_error_message()];
        }

        // Añadir elementos al menú
        $menu_items = [
            ['title' => 'Inicio', 'url' => home_url('/')],
            ['title' => 'Tours', 'url' => get_post_type_archive_link('tours')],
            ['title' => 'Destinos', 'url' => get_term_link('destinations', 'destinations')],
            ['title' => 'Acerca de', 'url' => home_url('/acerca-de/')],
            ['title' => 'Servicios', 'url' => home_url('/servicios/')],
            ['title' => 'Contacto', 'url' => home_url('/contacto/')]
        ];

        foreach ($menu_items as $item) {
            wp_update_nav_menu_item($menu_id, 0, [
                'menu-item-title' => $item['title'],
                'menu-item-url' => $item['url'],
                'menu-item-status' => 'publish',
                'menu-item-type' => 'custom'
            ]);
        }

        // Asignar el menú a la ubicación principal
        $locations = get_theme_mod('nav_menu_locations');
        $locations['primary'] = $menu_id;
        set_theme_mod('nav_menu_locations', $locations);

        return ['created' => true, 'menu_id' => $menu_id];
    }

    private function set_featured_image_from_url($post_id, $image_url)
    {
        $upload_dir = wp_upload_dir();
        $image_data = file_get_contents($image_url);
        
        if ($image_data === false) {
            return false;
        }

        $filename = basename($image_url) . '.jpg';
        $filepath = $upload_dir['path'] . '/' . $filename;

        if (wp_mkdir_p($upload_dir['path'])) {
            file_put_contents($filepath, $image_data);
        } else {
            return false;
        }

        $filetype = wp_check_filetype($filename, null);
        $attachment = [
            'post_mime_type' => $filetype['type'],
            'post_title' => sanitize_file_name($filename),
            'post_content' => '',
            'post_status' => 'inherit'
        ];

        $attach_id = wp_insert_attachment($attachment, $filepath, $post_id);
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attach_id, $filepath);
        wp_update_attachment_metadata($attach_id, $attach_data);
        set_post_thumbnail($post_id, $attach_id);

        return $attach_id;
    }

    private function clean_sample_data()
    {
        $cleaned = [];

        // Eliminar tours
        $tours = get_posts(['post_type' => 'tours', 'posts_per_page' => -1]);
        foreach ($tours as $tour) {
            wp_delete_post($tour->ID, true);
        }
        $cleaned['tours'] = count($tours);

        // Eliminar páginas de ejemplo
        $pages_to_delete = ['acerca-de', 'servicios', 'contacto', 'faq'];
        $deleted_pages = 0;
        foreach ($pages_to_delete as $slug) {
            $page = get_page_by_path($slug);
            if ($page) {
                wp_delete_post($page->ID, true);
                $deleted_pages++;
            }
        }
        $cleaned['pages'] = $deleted_pages;

        // Eliminar destinos
        $destinations = get_terms(['taxonomy' => 'destinations']);
        foreach ($destinations as $destination) {
            wp_delete_term($destination->term_id, 'destinations');
        }
        $cleaned['destinations'] = count($destinations);

        // Eliminar categorías de tours
        $tour_cats = get_terms(['taxonomy' => 'tour-categories']);
        foreach ($tour_cats as $cat) {
            wp_delete_term($cat->term_id, 'tour-categories');
        }
        $cleaned['tour_categories'] = count($tour_cats);

        return $cleaned;
    }

    public function check_sample_content()
    {
        check_ajax_referer('sample_data_nonce', 'nonce');
        
        $tours_count = wp_count_posts('tours');
        $destinations_count = wp_count_terms('destinations');
        $pages_count = wp_count_posts('page');
        
        $isEmpty = ($tours_count->publish == 0 && $destinations_count == 0 && $pages_count->publish <= 2);
        
        wp_send_json_success(['isEmpty' => $isEmpty]);
    }

    private function get_banner_images()
    {
        // URLs de imágenes para el banner de inicio
        $banner_images = [
            'https://images.unsplash.com/photo-1526392060635-9d6019884377?w=1920&h=800&fit=crop', // Machu Picchu
            'https://images.unsplash.com/photo-1464822759844-d150ad6730a7?w=1920&h=800&fit=crop', // Camino Inca
            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1920&h=800&fit=crop', // Huacachina
            'https://images.unsplash.com/photo-1586901533048-0e856dff47cd?w=1920&h=800&fit=crop', // Salar de Uyuni
        ];
        
        return implode(',', $banner_images);
    }

    private function verify_permalinks()
    {
        $verification = [];
        
        // Verificar estructura de permalinks
        $permalink_structure = get_option('permalink_structure');
        $verification['permalink_structure'] = $permalink_structure;
        
        // Detectar y corregir problema con /blog/ prefix
        if (strpos($permalink_structure, '/blog/') !== false) {
            $verification['blog_prefix_detected'] = true;
            $verification['message'] = 'Se detectó el prefijo /blog/ en permalinks. Se recomienda cambiar a /%postname%/ en Configuración > Enlaces permanentes';
            
            // Intentar cambiar automáticamente
            update_option('permalink_structure', '/%postname%/');
            flush_rewrite_rules(true);
            
            $verification['auto_fix_attempted'] = true;
            $verification['new_structure'] = get_option('permalink_structure');
        }
        
        // Verificar páginas recién creadas
        $recent_pages = get_posts([
            'post_type' => 'page',
            'posts_per_page' => 5,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_status' => 'publish'
        ]);
        
        $pages_check = [];
        foreach ($recent_pages as $page) {
            $pages_check[] = [
                'title' => $page->post_title,
                'slug' => $page->post_name,
                'url' => get_permalink($page->ID),
                'status' => get_post_status($page->ID)
            ];
        }
        $verification['recent_pages'] = $pages_check;
        
        // Verificar si necesitamos forzar un flush adicional
        if (empty($permalink_structure)) {
            $verification['warning'] = 'Permalinks están en modo "Plain" - esto puede causar problemas con páginas personalizadas';
        }
        
        return $verification;
    }
}

// Inicializar la clase
new WPTBT_Sample_Data_Generator();