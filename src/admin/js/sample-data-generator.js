/**
 * Generador de Datos de Ejemplo - Panel de Administración
 * 
 * Este archivo se compila automáticamente con Vite desde src/admin/js/sample-data-generator.js
 * hacia assets/admin/js/sample-data-generator.js
 * 
 * Para desarrollo:
 * - Editar: src/admin/js/sample-data-generator.js
 * - Compilar: npm run build (automático en desarrollo con npm run dev)
 * - No editar directamente el archivo en assets/ ya que se sobreescribirá
 */

jQuery(document).ready(function($) {
    let isGenerating = false;

    // Elementos del DOM
    const $generateAll = $('#generate-all');
    const $generateDestinations = $('#generate-destinations');
    const $generateTours = $('#generate-tours');
    const $generatePages = $('#generate-pages');
    const $cleanData = $('#clean-data');
    const $progressContainer = $('#generation-progress');
    const $progressFill = $('.progress-fill');
    const $progressText = $('#progress-text');
    const $controls = $('#generation-controls');
    const $results = $('#generation-results');
    const $resultsContent = $('#results-content');
    const $resetGenerator = $('#reset-generator');

    // Función para mostrar progreso
    function showProgress(text, progress = 0) {
        $controls.hide();
        $progressContainer.show();
        $progressText.text(text);
        $progressFill.css('width', progress + '%');
    }

    // Función para mostrar resultados
    function showResults(results) {
        $progressContainer.hide();
        let html = '<div class="notice notice-success"><h4>¡Generación completada!</h4>';
        
        if (results.destinations) {
            html += `<p><strong>Destinos:</strong> ${results.destinations.created} creados de ${results.destinations.total}</p>`;
        }
        
        if (results.tours) {
            html += `<p><strong>Tours:</strong> ${results.tours.created} creados de ${results.tours.total}</p>`;
        }
        
        if (results.pages) {
            html += `<p><strong>Páginas:</strong> ${results.pages.created} creadas de ${results.pages.total}</p>`;
            
            // Mostrar URLs de páginas creadas
            if (results.pages.pages_created && results.pages.pages_created.length > 0) {
                html += '<div style="margin-top: 10px;"><strong>Páginas creadas:</strong><ul style="margin-left: 20px;">';
                results.pages.pages_created.forEach(page => {
                    html += `<li><a href="${page.url}" target="_blank">${page.title}</a> (ID: ${page.id})</li>`;
                });
                html += '</ul></div>';
            }
        }
        
        if (results.menu) {
            html += `<p><strong>Menú:</strong> ${results.menu.created ? 'Creado exitosamente' : 'Ya existía'}</p>`;
        }
        
        if (results.cleaned) {
            html += '<h4>Datos eliminados:</h4>';
            html += `<p>Tours: ${results.cleaned.tours}, Páginas: ${results.cleaned.pages}, Destinos: ${results.cleaned.destinations}</p>`;
        }
        
        html += '</div>';
        
        // Mostrar errores si existen
        let hasErrors = false;
        if (results.destinations && results.destinations.errors.length > 0) {
            hasErrors = true;
            html += '<div class="notice notice-warning"><h4>Advertencias en destinos:</h4><ul>';
            results.destinations.errors.forEach(error => {
                html += `<li>${error}</li>`;
            });
            html += '</ul></div>';
        }
        
        if (results.tours && results.tours.errors.length > 0) {
            hasErrors = true;
            html += '<div class="notice notice-warning"><h4>Advertencias en tours:</h4><ul>';
            results.tours.errors.forEach(error => {
                html += `<li>${error}</li>`;
            });
            html += '</ul></div>';
        }
        
        $resultsContent.html(html);
        $results.show();
        isGenerating = false;
    }

    // Función para manejar errores
    function showError(message) {
        $progressContainer.hide();
        $controls.show();
        
        const html = `<div class="notice notice-error"><h4>Error</h4><p>${message}</p></div>`;
        $resultsContent.html(html);
        $results.show();
        isGenerating = false;
    }

    // Función para realizar petición AJAX
    function makeRequest(actionType, progressText) {
        if (isGenerating) {
            alert('Ya hay una generación en progreso. Por favor, espera.');
            return;
        }

        isGenerating = true;
        showProgress(progressText, 10);

        $.ajax({
            url: sampleData.ajax_url,
            type: 'POST',
            data: {
                action: 'generate_sample_data',
                action_type: actionType,
                nonce: sampleData.nonce
            },
            beforeSend: function() {
                $progressFill.css('width', '30%');
            },
            success: function(response) {
                $progressFill.css('width', '100%');
                $progressText.text('¡Completado!');
                
                setTimeout(function() {
                    if (response.success) {
                        showResults(response.data);
                    } else {
                        showError(response.data || 'Error desconocido');
                    }
                }, 500);
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', error);
                showError('Error de conexión: ' + error);
            }
        });
    }

    // Event listeners
    $generateAll.on('click', function() {
        if (confirm('Esto generará todos los datos de ejemplo. ¿Continuar?')) {
            makeRequest('generate_all', 'Generando todos los datos de ejemplo...');
        }
    });

    $generateDestinations.on('click', function() {
        makeRequest('generate_destinations', 'Generando destinos...');
    });

    $generateTours.on('click', function() {
        makeRequest('generate_tours', 'Generando tours...');
    });

    $generatePages.on('click', function() {
        makeRequest('generate_pages', 'Generando páginas...');
    });

    $cleanData.on('click', function() {
        const confirmation = confirm('¿Estás seguro de que quieres eliminar TODOS los tours, destinos y páginas de ejemplo? Esta acción no se puede deshacer.');
        
        if (confirmation) {
            const secondConfirmation = confirm('Esta acción eliminará permanentemente todo el contenido generado. ¿Proceder?');
            
            if (secondConfirmation) {
                makeRequest('clean_data', 'Limpiando datos de ejemplo...');
            }
        }
    });

    $resetGenerator.on('click', function() {
        $results.hide();
        $controls.show();
        isGenerating = false;
    });

    // Añadir tooltips informativos
    $generateAll.attr('title', 'Genera tours, destinos, páginas y menú de navegación completo');
    $generateDestinations.attr('title', 'Solo genera los destinos turísticos');
    $generateTours.attr('title', 'Solo genera los tours con galerías de imágenes');
    $generatePages.attr('title', 'Solo genera páginas institucionales básicas');
    $cleanData.attr('title', 'Elimina todo el contenido de ejemplo generado');

    // Mejorar la experiencia visual con animaciones
    $('.button').hover(
        function() {
            $(this).css('transform', 'translateY(-2px)');
        },
        function() {
            $(this).css('transform', 'translateY(0)');
        }
    );

    // Añadir estilos CSS dinámicamente para mejorar la apariencia
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .button {
                transition: all 0.2s ease;
                position: relative;
            }
            
            .button-hero {
                font-size: 16px !important;
                padding: 12px 24px !important;
                height: auto !important;
            }
            
            .progress-bar {
                position: relative;
                overflow: hidden;
            }
            
            .progress-fill {
                position: relative;
            }
            
            .progress-fill::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(
                    90deg, 
                    rgba(255,255,255,0) 0%, 
                    rgba(255,255,255,0.4) 50%, 
                    rgba(255,255,255,0) 100%
                );
                animation: progressShine 2s infinite;
            }
            
            @keyframes progressShine {
                0% { transform: translateX(-100%); }
                100% { transform: translateX(100%); }
            }
            
            .notice {
                margin: 15px 0;
                padding: 12px;
                border-radius: 4px;
            }
            
            .notice-success {
                background: #d4edda;
                border-left: 4px solid #28a745;
                color: #155724;
            }
            
            .notice-warning {
                background: #fff3cd;
                border-left: 4px solid #ffc107;
                color: #856404;
            }
            
            .notice-error {
                background: #f8d7da;
                border-left: 4px solid #dc3545;
                color: #721c24;
            }
            
            .notice-info {
                background: #d1ecf1;
                border-left: 4px solid #17a2b8;
                color: #0c5460;
            }
        `)
        .appendTo('head');
});