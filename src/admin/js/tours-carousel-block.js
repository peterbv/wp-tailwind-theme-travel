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
    { name: "Negro", color: "#000000" },
  ];

  blocks.registerBlockType("wptbt/tours-carousel-block", {
    title: __("Carousel de Tours"),
    icon: "slides",
    category: "wp-tailwind-blocks",
    attributes: {
      title: {
        type: "string",
        default: "Nuestros Tours",
      },
      subtitle: {
        type: "string",
        default: "Experiencias Únicas",
      },
      description: {
        type: "string",
        default: "Descubre nuestros increíbles tours y vive aventuras inolvidables.",
      },
      tourIds: {
        type: "array",
        default: [],
      },
      postsPerPage: {
        type: "number",
        default: 6,
      },
      orderBy: {
        type: "string",
        default: "date",
      },
      order: {
        type: "string",
        default: "DESC",
      },
      autoplaySpeed: {
        type: "number",
        default: 3000,
      },
      slidesToShow: {
        type: "number",
        default: 3,
      },
      showDots: {
        type: "boolean",
        default: true,
      },
      showArrows: {
        type: "boolean",
        default: true,
      },
      pauseOnHover: {
        type: "boolean",
        default: true,
      },
      infinite: {
        type: "boolean",
        default: true,
      },
      animationDirection: {
        type: "string",
        default: "left",
      },
      backgroundColor: {
        type: "string",
        default: "#F8FAFC",
      },
      textColor: {
        type: "string",
        default: "#1F2937",
      },
      accentColor: {
        type: "string",
        default: "#DC2626",
      },
      secondaryColor: {
        type: "string",
        default: "#059669",
      },
      fullWidth: {
        type: "boolean",
        default: false,
      },
      categories: {
        type: "array",
        default: [],
      },
      destinations: {
        type: "array",
        default: [],
      },
    },

    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;

      // Obtener tours disponibles
      var tours = useSelect(function (select) {
        return select("core").getEntityRecords("postType", "tours", {
          per_page: 20,
          _embed: true,
        });
      });

      // Obtener categorías de tours
      var tourCategories = useSelect(function (select) {
        return select("core").getEntityRecords("taxonomy", "tour-categories", {
          per_page: 100,
        });
      });

      // Obtener destinos
      var destinations = useSelect(function (select) {
        return select("core").getEntityRecords("taxonomy", "destinations", {
          per_page: 100,
        });
      });

      // Función para generar la vista previa del carousel
      function renderCarouselPreview() {
        var previewTours = tours ? tours.slice(0, attributes.postsPerPage) : [];
        
        if (!tours) {
          return el(
            "div",
            { className: "tours-carousel-loading" },
            el("p", {}, __("Cargando tours..."))
          );
        }

        if (previewTours.length === 0) {
          return el(
            "div",
            { className: "tours-carousel-empty" },
            el("p", {}, __("No se encontraron tours. Crea algunos tours para mostrar."))
          );
        }

        return el(
          "div",
          {
            className: "tours-carousel-preview",
            style: {
              backgroundColor: attributes.backgroundColor,
              color: attributes.textColor,
              padding: "20px",
              borderRadius: "8px",
            },
          },
          el(
            "div",
            { className: "tours-carousel-header text-center mb-4" },
            attributes.subtitle && el(
              "p",
              {
                style: {
                  color: attributes.accentColor,
                  fontStyle: "italic",
                  marginBottom: "8px",
                },
              },
              attributes.subtitle
            ),
            attributes.title && el(
              "h3",
              {
                style: {
                  color: attributes.textColor,
                  fontSize: "24px",
                  fontWeight: "bold",
                  marginBottom: "8px",
                },
              },
              attributes.title
            ),
            attributes.description && el(
              "p",
              {
                style: {
                  color: attributes.textColor,
                  opacity: "0.8",
                },
              },
              attributes.description
            )
          ),
          el(
            "div",
            {
              className: "tours-carousel-items",
              style: {
                display: "grid",
                gridTemplateColumns: "repeat(" + Math.min(attributes.slidesToShow, previewTours.length) + ", 1fr)",
                gap: "16px",
              },
            },
            previewTours.slice(0, attributes.slidesToShow).map(function (tour, index) {
              var featuredImage = tour._embedded && tour._embedded["wp:featuredmedia"] 
                ? tour._embedded["wp:featuredmedia"][0] 
                : null;

              return el(
                "div",
                {
                  key: tour.id,
                  className: "tour-card-preview",
                  style: {
                    backgroundColor: "#FFFFFF",
                    borderRadius: "12px",
                    overflow: "hidden",
                    boxShadow: "0 4px 6px rgba(0,0,0,0.1)",
                  },
                },
                featuredImage && el(
                  "div",
                  {
                    style: {
                      width: "100%",
                      height: "120px",
                      backgroundImage: "url(" + featuredImage.source_url + ")",
                      backgroundSize: "cover",
                      backgroundPosition: "center",
                    },
                  }
                ),
                el(
                  "div",
                  { style: { padding: "12px" } },
                  el(
                    "h4",
                    {
                      style: {
                        fontSize: "14px",
                        fontWeight: "bold",
                        marginBottom: "4px",
                        color: "#1F2937",
                      },
                    },
                    tour.title.rendered
                  ),
                  el(
                    "p",
                    {
                      style: {
                        fontSize: "12px",
                        color: "#6B7280",
                        margin: "0",
                      },
                    },
                    tour.excerpt.rendered.replace(/<[^>]*>/g, "").substring(0, 60) + "..."
                  )
                )
              );
            })
          ),
          el(
            "div",
            {
              className: "carousel-controls-preview",
              style: {
                display: "flex",
                justifyContent: "center",
                alignItems: "center",
                marginTop: "16px",
                gap: "8px",
              },
            },
            attributes.showArrows && el(
              "div",
              {
                style: {
                  width: "32px",
                  height: "32px",
                  backgroundColor: "#FFFFFF",
                  borderRadius: "50%",
                  display: "flex",
                  alignItems: "center",
                  justifyContent: "center",
                  boxShadow: "0 2px 4px rgba(0,0,0,0.1)",
                },
              },
              "◀"
            ),
            attributes.showDots && el(
              "div",
              { style: { display: "flex", gap: "4px" } },
              [1, 2, 3].map(function (dot, index) {
                return el("div", {
                  key: index,
                  style: {
                    width: "8px",
                    height: "8px",
                    borderRadius: "50%",
                    backgroundColor: index === 0 ? attributes.accentColor : "#D1D5DB",
                  },
                });
              })
            ),
            attributes.showArrows && el(
              "div",
              {
                style: {
                  width: "32px",
                  height: "32px",
                  backgroundColor: "#FFFFFF",
                  borderRadius: "50%",
                  display: "flex",
                  alignItems: "center",
                  justifyContent: "center",
                  boxShadow: "0 2px 4px rgba(0,0,0,0.1)",
                },
              },
              "▶"
            )
          )
        );
      }

      return [
        // Panel de control
        el(
          InspectorControls,
          {},
          el(
            PanelBody,
            { title: __("Configuración del Carousel"), initialOpen: true },
            el(RangeControl, {
              label: __("Número de tours"),
              value: attributes.postsPerPage,
              onChange: function (postsPerPage) {
                setAttributes({ postsPerPage: postsPerPage });
              },
              min: 1,
              max: 20,
            }),
            el(SelectControl, {
              label: __("Ordenar por"),
              value: attributes.orderBy,
              options: [
                { label: __("Fecha"), value: "date" },
                { label: __("Título"), value: "title" },
                { label: __("Orden del menú"), value: "menu_order" },
                { label: __("Aleatorio"), value: "rand" },
              ],
              onChange: function (orderBy) {
                setAttributes({ orderBy: orderBy });
              },
            }),
            el(SelectControl, {
              label: __("Orden"),
              value: attributes.order,
              options: [
                { label: __("Descendente"), value: "DESC" },
                { label: __("Ascendente"), value: "ASC" },
              ],
              onChange: function (order) {
                setAttributes({ order: order });
              },
            }),
            el(RangeControl, {
              label: __("Tours visibles"),
              value: attributes.slidesToShow,
              onChange: function (slidesToShow) {
                setAttributes({ slidesToShow: slidesToShow });
              },
              min: 1,
              max: 6,
            }),
            el(RangeControl, {
              label: __("Velocidad autoplay (ms)"),
              value: attributes.autoplaySpeed,
              onChange: function (autoplaySpeed) {
                setAttributes({ autoplaySpeed: autoplaySpeed });
              },
              min: 1000,
              max: 10000,
              step: 500,
            }),
            el(SelectControl, {
              label: __("Dirección de animación"),
              value: attributes.animationDirection,
              options: [
                { label: __("Izquierda"), value: "left" },
                { label: __("Derecha"), value: "right" },
              ],
              onChange: function (animationDirection) {
                setAttributes({ animationDirection: animationDirection });
              },
            })
          ),

          el(
            PanelBody,
            { title: __("Controles"), initialOpen: false },
            el(ToggleControl, {
              label: __("Mostrar puntos de navegación"),
              checked: attributes.showDots,
              onChange: function (showDots) {
                setAttributes({ showDots: showDots });
              },
            }),
            el(ToggleControl, {
              label: __("Mostrar flechas"),
              checked: attributes.showArrows,
              onChange: function (showArrows) {
                setAttributes({ showArrows: showArrows });
              },
            }),
            el(ToggleControl, {
              label: __("Pausar al pasar el mouse"),
              checked: attributes.pauseOnHover,
              onChange: function (pauseOnHover) {
                setAttributes({ pauseOnHover: pauseOnHover });
              },
            }),
            el(ToggleControl, {
              label: __("Carousel infinito"),
              checked: attributes.infinite,
              onChange: function (infinite) {
                setAttributes({ infinite: infinite });
              },
            }),
            el(ToggleControl, {
              label: __("Ancho completo"),
              checked: attributes.fullWidth,
              onChange: function (fullWidth) {
                setAttributes({ fullWidth: fullWidth });
              },
            })
          ),

          el(
            PanelBody,
            { title: __("Textos"), initialOpen: false },
            el(TextControl, {
              label: __("Título"),
              value: attributes.title,
              onChange: function (title) {
                setAttributes({ title: title });
              },
            }),
            el(TextControl, {
              label: __("Subtítulo"),
              value: attributes.subtitle,
              onChange: function (subtitle) {
                setAttributes({ subtitle: subtitle });
              },
            }),
            el(TextControl, {
              label: __("Descripción"),
              value: attributes.description,
              onChange: function (description) {
                setAttributes({ description: description });
              },
            })
          ),

          el(
            PanelBody,
            { title: __("Filtros"), initialOpen: false },
            tourCategories && el(SelectControl, {
              label: __("Categorías"),
              value: attributes.categories,
              options: [
                { label: __("Todas las categorías"), value: "" },
              ].concat(
                tourCategories.map(function (category) {
                  return {
                    label: category.name,
                    value: category.id.toString(),
                  };
                })
              ),
              multiple: true,
              onChange: function (categories) {
                setAttributes({ categories: categories });
              },
            }),
            destinations && el(SelectControl, {
              label: __("Destinos"),
              value: attributes.destinations,
              options: [
                { label: __("Todos los destinos"), value: "" },
              ].concat(
                destinations.map(function (destination) {
                  return {
                    label: destination.name,
                    value: destination.id.toString(),
                  };
                })
              ),
              multiple: true,
              onChange: function (destinations) {
                setAttributes({ destinations: destinations });
              },
            })
          ),

          el(
            PanelBody,
            { title: __("Colores"), initialOpen: false },
            el(
              "p",
              { className: "components-base-control__label" },
              __("Color de Fondo")
            ),
            el(ColorPalette, {
              colors: colors,
              value: attributes.backgroundColor,
              onChange: function (backgroundColor) {
                setAttributes({ backgroundColor: backgroundColor });
              },
            }),
            el(
              "p",
              { className: "components-base-control__label" },
              __("Color de Texto")
            ),
            el(ColorPalette, {
              colors: colors,
              value: attributes.textColor,
              onChange: function (textColor) {
                setAttributes({ textColor: textColor });
              },
            }),
            el(
              "p",
              { className: "components-base-control__label" },
              __("Color Principal")
            ),
            el(ColorPalette, {
              colors: colors,
              value: attributes.accentColor,
              onChange: function (accentColor) {
                setAttributes({ accentColor: accentColor });
              },
            }),
            el(
              "p",
              { className: "components-base-control__label" },
              __("Color Secundario")
            ),
            el(ColorPalette, {
              colors: colors,
              value: attributes.secondaryColor,
              onChange: function (secondaryColor) {
                setAttributes({ secondaryColor: secondaryColor });
              },
            })
          )
        ),

        // Vista del bloque en el editor
        el(
          "div",
          {
            className: props.className + " wp-block-wptbt-tours-carousel-block",
          },
          el(
            "div",
            { className: "tours-carousel-block-editor" },
            el(
              "div",
              { className: "block-header text-center mb-4" },
              el(
                "h3",
                {
                  style: {
                    color: attributes.accentColor,
                    fontSize: "18px",
                    fontWeight: "bold",
                    margin: "0 0 8px 0",
                  },
                },
                __("🎠 Carousel de Tours")
              ),
              el(
                "p",
                {
                  style: {
                    color: "#6B7280",
                    fontSize: "14px",
                    margin: "0",
                  },
                },
                __("Vista previa del carousel - Configuración en el panel lateral")
              )
            ),
            renderCarouselPreview()
          )
        ),
      ];
    },

    save: function () {
      return null; // Usar render_callback del servidor
    },
  });
})(
  window.wp.blocks,
  window.wp.editor,
  window.wp.components,
  window.wp.i18n,
  window.wp.element,
  window.wp.data
);