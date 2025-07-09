(function (blocks, editor, components, i18n, element) {
  var el = element.createElement;
  var __ = i18n.__;
  var RichText = editor.RichText;
  var MediaUpload = editor.MediaUpload;
  var MediaPlaceholder = editor.MediaPlaceholder;
  var InspectorControls = editor.InspectorControls;
  var PanelBody = components.PanelBody;
  var TextControl = components.TextControl;
  var RangeControl = components.RangeControl;
  var SelectControl = components.SelectControl;
  var ToggleControl = components.ToggleControl;
  var ColorPalette = components.ColorPalette;
  var Button = components.Button;

  // Paleta de colores para el spa
  var colors = [
    { name: "Aguamarina", color: "#4F8A8B" },
    { name: "Verde Salvia", color: "#8BAB8D" },
    { name: "Dorado Suave", color: "#D4B254" },
    { name: "Rosa Polvo", color: "#D9ADB7" },
    { name: "Blanco", color: "#FFFFFF" },
    { name: "Gris Claro", color: "#F9F9F9" },
    { name: "Gris Oscuro", color: "#424242" },
    { name: "Negro", color: "#000000" },
  ];

  blocks.registerBlockType("wptbt/gallery-block", {
    title: __("Galería de Spa"),
    icon: "format-gallery",
    category: "widgets",
    attributes: {
      title: {
        type: "string",
        default: "Nuestra Galería",
      },
      subtitle: {
        type: "string",
        default: "Espacios de relajación",
      },
      description: {
        type: "string",
        default:
          "Explora nuestras instalaciones y servicios a través de nuestra galería de imágenes.",
      },
      images: {
        type: "array",
        default: [],
      },
      columns: {
        type: "number",
        default: 3,
      },
      displayMode: {
        type: "string",
        default: "grid",
      },
      hoverEffect: {
        type: "string",
        default: "zoom",
      },
      backgroundColor: {
        type: "string",
        default: "#FFFFFF",
      },
      textColor: {
        type: "string",
        default: "#424242",
      },
      accentColor: {
        type: "string",
        default: "#D4B254",
      },
      fullWidth: {
        type: "boolean",
        default: false,
      },
      imageSize: {
        type: "string",
        default: "medium_large",
      },
      enableLightbox: {
        type: "boolean",
        default: true,
      },
      spacing: {
        type: "number",
        default: 16,
      },
    },

    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;

      // Función para seleccionar imágenes
      function onSelectImages(media) {
        setAttributes({
          images: media.map(function (item) {
            return { id: item.id, url: item.url, caption: item.caption };
          }),
        });
      }

      // Función para actualizar textos editables
      function updateText(key, value) {
        var newAttributes = {};
        newAttributes[key] = value;
        setAttributes(newAttributes);
      }

      // Generar la vista previa de la galería según el modo
      function renderGalleryPreview() {
        if (attributes.images.length === 0) {
          return el(
            "div",
            { className: "wptbt-gallery-empty-placeholder" },
            el(
              "p",
              { className: "wptbt-gallery-empty-text" },
              __("Selecciona imágenes para la galería")
            ),
            el(MediaPlaceholder, {
              icon: "format-gallery",
              labels: {
                title: __("Galería"),
                instructions: __(
                  "Arrastra imágenes aquí, sube nuevas o selecciona de tu biblioteca."
                ),
              },
              onSelect: onSelectImages,
              accept: "image/*",
              allowedTypes: ["image"],
              multiple: true,
              value: attributes.images,
            })
          );
        }

        var galleryClassName = "wptbt-gallery-preview";
        var containerStyle = {};
        var itemStyle = {};

        // Estilo para el modo grid
        if (attributes.displayMode === "grid") {
          galleryClassName += " wptbt-gallery-grid";
          containerStyle = {
            display: "flex",
            flexWrap: "wrap",
            gap: attributes.spacing + "px",
            justifyContent: "flex-start",
          };
          itemStyle = {
            flex: `0 0 calc(${100 / attributes.columns}% - ${
              (attributes.spacing * (attributes.columns - 1)) /
              attributes.columns
            }px)`,
          };
        }
        // Estilo para el modo masonry (simulado)
        else if (attributes.displayMode === "masonry") {
          galleryClassName += " wptbt-gallery-masonry";
          containerStyle = {
            columnCount: attributes.columns,
            columnGap: attributes.spacing + "px",
          };
          itemStyle = {
            marginBottom: attributes.spacing + "px",
            breakInside: "avoid",
          };
        }
        // Estilo para el modo slider
        else {
          galleryClassName += " wptbt-gallery-slider";
          containerStyle = {
            display: "flex",
            flexDirection: "column",
          };
          itemStyle = {
            width: "100%",
            marginBottom: attributes.spacing + "px",
          };
        }

        return el(
          "div",
          {
            className: galleryClassName,
            style: {
              backgroundColor: attributes.backgroundColor,
              color: attributes.textColor,
            },
          },
          el(MediaUpload, {
            onSelect: onSelectImages,
            allowedTypes: ["image"],
            multiple: true,
            gallery: true,
            value: attributes.images.map(function (img) {
              return img.id;
            }),
            render: function (obj) {
              return el(
                "div",
                {
                  className: "wptbt-gallery-items-container",
                  style: containerStyle,
                },
                el(
                  Button,
                  {
                    className: "wptbt-edit-gallery-button",
                    onClick: obj.open,
                  },
                  el(
                    "span",
                    { className: "dashicons dashicons-edit" },
                    __("Editar Galería")
                  )
                ),

                attributes.images.map(function (img, index) {
                  return el(
                    "div",
                    {
                      className: "wptbt-gallery-item",
                      key: img.id || index,
                      style: itemStyle,
                    },
                    el("img", {
                      src: img.url,
                      alt: __("Imagen de vista previa"),
                    }),
                    el(
                      "div",
                      { className: "wptbt-gallery-item-actions" },
                      el(
                        Button,
                        {
                          className: "wptbt-remove-image-button",
                          onClick: function () {
                            var newImages = attributes.images.filter(
                              function (item, i) {
                                return i !== index;
                              }
                            );
                            setAttributes({ images: newImages });
                          },
                        },
                        el("span", { className: "dashicons dashicons-no" })
                      )
                    )
                  );
                })
              );
            },
          })
        );
      }

      return [
        // Panel de control
        el(
          InspectorControls,
          {},
          el(
            PanelBody,
            { title: __("Configuración de Galería"), initialOpen: true },
            el(RangeControl, {
              label: __("Columnas"),
              value: attributes.columns,
              onChange: function (columns) {
                setAttributes({ columns: columns });
              },
              min: 1,
              max: 6,
            }),
            el(SelectControl, {
              label: __("Modo de visualización"),
              value: attributes.displayMode,
              options: [
                { label: __("Cuadrícula"), value: "grid" },
                { label: __("Masonry"), value: "masonry" },
                { label: __("Slider"), value: "slider" },
              ],
              onChange: function (displayMode) {
                setAttributes({ displayMode: displayMode });
              },
            }),
            el(SelectControl, {
              label: __("Efecto al pasar el ratón"),
              value: attributes.hoverEffect,
              options: [
                { label: __("Zoom"), value: "zoom" },
                { label: __("Fundido"), value: "fade" },
                { label: __("Deslizar"), value: "slide" },
                { label: __("Ninguno"), value: "none" },
              ],
              onChange: function (hoverEffect) {
                setAttributes({ hoverEffect: hoverEffect });
              },
            }),
            el(RangeControl, {
              label: __("Espaciado (px)"),
              value: attributes.spacing,
              onChange: function (spacing) {
                setAttributes({ spacing: spacing });
              },
              min: 0,
              max: 40,
            }),
            el(SelectControl, {
              label: __("Tamaño de imagen"),
              value: attributes.imageSize,
              options: [
                { label: __("Miniatura"), value: "thumbnail" },
                { label: __("Mediano"), value: "medium" },
                { label: __("Mediano Grande"), value: "medium_large" },
                { label: __("Grande"), value: "large" },
                { label: __("Completo"), value: "full" },
              ],
              onChange: function (imageSize) {
                setAttributes({ imageSize: imageSize });
              },
            }),
            el(ToggleControl, {
              label: __("Habilitar Lightbox"),
              checked: attributes.enableLightbox,
              onChange: function (enableLightbox) {
                setAttributes({ enableLightbox: enableLightbox });
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
              __("Color de Acento")
            ),
            el(ColorPalette, {
              colors: colors,
              value: attributes.accentColor,
              onChange: function (accentColor) {
                setAttributes({ accentColor: accentColor });
              },
            })
          )
        ),

        // Vista del bloque en el editor
        el(
          "div",
          {
            className: props.className + " wp-block-wptbt-gallery-block",
          },
          el(
            "div",
            { className: "gallery-block-editor" },

            // Encabezado de la galería
            el(
              "div",
              { className: "gallery-header text-center mb-6" },
              el(RichText, {
                tagName: "span",
                className: "block text-lg italic font-medium mb-2",
                style: { color: attributes.accentColor },
                value: attributes.subtitle,
                onChange: function (subtitle) {
                  setAttributes({ subtitle: subtitle });
                },
                placeholder: __("Subtítulo"),
              }),
              el(RichText, {
                tagName: "h2",
                className: "text-3xl fancy-text font-medium mb-4",
                style: { color: attributes.textColor },
                value: attributes.title,
                onChange: function (title) {
                  setAttributes({ title: title });
                },
                placeholder: __("Título Principal"),
              }),
              // Línea decorativa
              el("div", {
                style: {
                  width: "60px",
                  height: "3px",
                  backgroundColor: attributes.accentColor,
                  margin: "0 auto 20px",
                },
              }),
              el(RichText, {
                tagName: "p",
                className: "text-gray-600 max-w-md mx-auto",
                style: { color: attributes.textColor },
                value: attributes.description,
                onChange: function (description) {
                  setAttributes({ description: description });
                },
                placeholder: __("Descripción de la galería"),
              })
            ),

            // Previsualización de la galería
            renderGalleryPreview()
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
  window.wp.element
);
