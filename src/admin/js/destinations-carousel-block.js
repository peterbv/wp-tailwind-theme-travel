(function (blocks, editor, components, i18n, element, data) {
  var el = element.createElement;
  var __ = i18n.__;
  var RichText = editor.RichText;
  var InspectorControls = editor.InspectorControls;
  var PanelBody = components.PanelBody;
  var TextControl = components.TextControl;
  var RangeControl = components.RangeControl;
  var SelectControl = components.SelectControl;
  var ToggleControl = components.ToggleControl;
  var ColorPalette = components.ColorPalette;
  var Button = components.Button;
  var useSelect = data.useSelect;

  // Paleta de colores para agencia de viajes
  var colors = [
    { name: "Rojo Vibrante", color: "#DC2626" },
    { name: "Verde Aventura", color: "#059669" },
    { name: "Azul Océano", color: "#0EA5E9" },
    { name: "Naranja Atardecer", color: "#EA580C" },
    { name: "Púrpura Montaña", color: "#7C3AED" },
    { name: "Gris Claro", color: "#F8FAFC" },
    { name: "Gris Oscuro", color: "#1F2937" },
    { name: "Blanco", color: "#FFFFFF" },
    { name: "Negro", color: "#000000" }
  ];

  blocks.registerBlockType("wptbt/destinations-carousel", {
    title: __("Destinations Carousel"),
    icon: "location-alt",
    category: "wp-tailwind-blocks",
    attributes: {
      title: {
        type: "string",
        default: __("Explore Amazing Destinations")
      },
      subtitle: {
        type: "string",
        default: __("Popular Destinations")
      },
      description: {
        type: "string",
        default: __("Discover breathtaking destinations around the world with our curated travel experiences.")
      },
      numberOfDestinations: {
        type: "number",
        default: 6
      },
      autoplaySpeed: {
        type: "number",
        default: 3000
      },
      slidesToShow: {
        type: "number",
        default: 3
      },
      showDots: {
        type: "boolean",
        default: true
      },
      showArrows: {
        type: "boolean",
        default: true
      },
      pauseOnHover: {
        type: "boolean",
        default: true
      },
      animationDirection: {
        type: "string",
        default: "left"
      },
      backgroundColor: {
        type: "string",
        default: "#F8FAFC"
      },
      textColor: {
        type: "string",
        default: "#1F2937"
      },
      accentColor: {
        type: "string",
        default: "#DC2626"
      },
      secondaryColor: {
        type: "string",
        default: "#059669"
      },
      fullWidth: {
        type: "boolean",
        default: false
      }
    },

    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;
      var className = props.className;

      // Obtener destinos
      var destinations = useSelect(function (select) {
        return select('core').getEntityRecords('taxonomy', 'destinations', {
          per_page: 100
        });
      });

      // Función para renderizar la preview
      function renderPreview() {
        var destinationsToShow = destinations ? destinations.slice(0, attributes.numberOfDestinations) : [];
        
        if (!destinations) {
          return el('div', { className: 'destinations-carousel-loading' },
            el('p', {}, __('Loading destinations...'))
          );
        }

        if (destinationsToShow.length === 0) {
          return el('div', { className: 'destinations-carousel-empty' },
            el('p', {}, __('No destinations found. Create some destinations to display.'))
          );
        }

        return el('div', {
          className: 'destinations-carousel-preview',
          style: {
            backgroundColor: attributes.backgroundColor,
            color: attributes.textColor,
            padding: '20px',
            borderRadius: '8px'
          }
        }, [
          // Header
          el('div', { className: 'destinations-carousel-header text-center', style: { marginBottom: '16px' } }, [
            attributes.subtitle && el('p', {
              style: {
                color: attributes.accentColor,
                fontStyle: 'italic',
                marginBottom: '8px'
              }
            }, attributes.subtitle),
            attributes.title && el('h3', {
              style: {
                color: attributes.textColor,
                fontSize: '24px',
                fontWeight: 'bold',
                marginBottom: '8px'
              }
            }, attributes.title),
            attributes.description && el('p', {
              style: {
                color: attributes.textColor,
                opacity: '0.8'
              }
            }, attributes.description)
          ]),
          // Destinations Grid
          el('div', {
            className: 'destinations-carousel-items',
            style: {
              display: 'grid',
              gridTemplateColumns: 'repeat(' + Math.min(attributes.slidesToShow, destinationsToShow.length) + ', 1fr)',
              gap: '16px'
            }
          }, destinationsToShow.slice(0, attributes.slidesToShow).map(function (destination, index) {
            return el('div', {
              key: destination.id,
              className: 'destination-card-preview',
              style: {
                backgroundColor: '#FFFFFF',
                borderRadius: '12px',
                overflow: 'hidden',
                boxShadow: '0 4px 6px rgba(0,0,0,0.1)'
              }
            }, [
              el('div', {
                style: {
                  width: '100%',
                  height: '120px',
                  backgroundColor: '#E5E7EB',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  fontSize: '12px',
                  color: '#6B7280'
                }
              }, __('Destination image')),
              el('div', { style: { padding: '12px' } }, [
                el('h4', {
                  style: {
                    fontSize: '14px',
                    fontWeight: 'bold',
                    marginBottom: '4px',
                    color: '#1F2937'
                  }
                }, destination.name),
                el('p', {
                  style: {
                    fontSize: '12px',
                    color: '#6B7280',
                    margin: '0'
                  }
                }, destination.description ? destination.description.substring(0, 60) + '...' : __('Destination description')),
                destination.count > 0 && el('span', {
                  style: {
                    fontSize: '11px',
                    color: attributes.accentColor,
                    fontWeight: '500'
                  }
                }, destination.count + ' ' + (destination.count === 1 ? __('tour') : __('tours')))
              ])
            ]);
          })),
          // Controls Preview
          el('div', {
            className: 'carousel-controls-preview',
            style: {
              display: 'flex',
              justifyContent: 'center',
              alignItems: 'center',
              marginTop: '16px',
              gap: '8px'
            }
          }, [
            attributes.showArrows && el('div', {
              style: {
                width: '32px',
                height: '32px',
                backgroundColor: '#FFFFFF',
                borderRadius: '50%',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                boxShadow: '0 2px 4px rgba(0,0,0,0.1)'
              }
            }, '◀'),
            attributes.showDots && el('div', { style: { display: 'flex', gap: '4px' } }, [1, 2, 3].map(function (dot, index) {
              return el('div', {
                key: index,
                style: {
                  width: '8px',
                  height: '8px',
                  borderRadius: '50%',
                  backgroundColor: index === 0 ? attributes.accentColor : '#D1D5DB'
                }
              });
            })),
            attributes.showArrows && el('div', {
              style: {
                width: '32px',
                height: '32px',
                backgroundColor: '#FFFFFF',
                borderRadius: '50%',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                boxShadow: '0 2px 4px rgba(0,0,0,0.1)'
              }
            }, '▶')
          ])
        ]);
      }

      return [
        // Inspector Controls (sidebar)
        el(InspectorControls, { key: 'inspector' }, [
          el(PanelBody, { title: __('Carousel Settings'), initialOpen: true }, [
            el(RangeControl, {
              label: __('Number of destinations'),
              value: attributes.numberOfDestinations,
              onChange: function (value) { setAttributes({ numberOfDestinations: value }); },
              min: 1,
              max: 20
            }),
            el(RangeControl, {
              label: __('Visible destinations'),
              value: attributes.slidesToShow,
              onChange: function (value) { setAttributes({ slidesToShow: value }); },
              min: 1,
              max: 6
            }),
            el(RangeControl, {
              label: __('Autoplay speed (ms)'),
              value: attributes.autoplaySpeed,
              onChange: function (value) { setAttributes({ autoplaySpeed: value }); },
              min: 1000,
              max: 10000,
              step: 500
            }),
            el(SelectControl, {
              label: __('Animation direction'),
              value: attributes.animationDirection,
              options: [
                { label: __('Left'), value: 'left' },
                { label: __('Right'), value: 'right' }
              ],
              onChange: function (value) { setAttributes({ animationDirection: value }); }
            })
          ]),

          el(PanelBody, { title: __('Controls'), initialOpen: false }, [
            el(ToggleControl, {
              label: __('Show navigation dots'),
              checked: attributes.showDots,
              onChange: function (value) { setAttributes({ showDots: value }); }
            }),
            el(ToggleControl, {
              label: __('Show arrows'),
              checked: attributes.showArrows,
              onChange: function (value) { setAttributes({ showArrows: value }); }
            }),
            el(ToggleControl, {
              label: __('Pause on hover'),
              checked: attributes.pauseOnHover,
              onChange: function (value) { setAttributes({ pauseOnHover: value }); }
            }),
            el(ToggleControl, {
              label: __('Full width'),
              checked: attributes.fullWidth,
              onChange: function (value) { setAttributes({ fullWidth: value }); }
            })
          ]),

          el(PanelBody, { title: __('Content'), initialOpen: false }, [
            el(TextControl, {
              label: __('Title'),
              value: attributes.title,
              onChange: function (value) { setAttributes({ title: value }); }
            }),
            el(TextControl, {
              label: __('Subtitle'),
              value: attributes.subtitle,
              onChange: function (value) { setAttributes({ subtitle: value }); }
            }),
            el(TextControl, {
              label: __('Description'),
              value: attributes.description,
              onChange: function (value) { setAttributes({ description: value }); }
            })
          ]),

          el(PanelBody, { title: __('Colors'), initialOpen: false }, [
            el('p', { className: 'components-base-control__label' }, __('Background Color')),
            el(ColorPalette, {
              colors: colors,
              value: attributes.backgroundColor,
              onChange: function (value) { setAttributes({ backgroundColor: value }); }
            }),
            el('p', { className: 'components-base-control__label' }, __('Text Color')),
            el(ColorPalette, {
              colors: colors,
              value: attributes.textColor,
              onChange: function (value) { setAttributes({ textColor: value }); }
            }),
            el('p', { className: 'components-base-control__label' }, __('Accent Color')),
            el(ColorPalette, {
              colors: colors,
              value: attributes.accentColor,
              onChange: function (value) { setAttributes({ accentColor: value }); }
            }),
            el('p', { className: 'components-base-control__label' }, __('Secondary Color')),
            el(ColorPalette, {
              colors: colors,
              value: attributes.secondaryColor,
              onChange: function (value) { setAttributes({ secondaryColor: value }); }
            })
          ])
        ]),

        // Block content (main editor area)
        el('div', {
          key: 'content',
          className: className + ' wp-block-wptbt-destinations-carousel'
        }, el('div', { className: 'destinations-carousel-block-editor' }, [
          el('div', { className: 'block-header text-center', style: { marginBottom: '16px' } }, [
            el('h3', {
              style: {
                color: attributes.accentColor,
                fontSize: '18px',
                fontWeight: 'bold',
                margin: '0 0 8px 0'
              }
            }, '🌍 ' + __('Destinations Carousel')),
            el('p', {
              style: {
                color: '#6B7280',
                fontSize: '14px',
                margin: '0'
              }
            }, __('Preview of carousel - Configure in sidebar panel'))
          ]),
          renderPreview()
        ]))
      ];
    },

    save: function () {
      // El bloque es renderizado desde PHP
      return null;
    }
  });
})(
  window.wp.blocks,
  window.wp.blockEditor,
  window.wp.components,
  window.wp.i18n,
  window.wp.element,
  window.wp.data
);