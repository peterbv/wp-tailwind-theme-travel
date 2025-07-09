/**
 * Script mejorado para el bloque de beneficios con soporte de video
 */
(function () {
  var registerBlockType = wp.blocks.registerBlockType;
  var createElement = wp.element.createElement;
  var __ = wp.i18n.__;

  // Componentes para el panel lateral
  var InspectorControls = wp.blockEditor
    ? wp.blockEditor.InspectorControls
    : wp.editor.InspectorControls;
  var MediaUpload = wp.blockEditor
    ? wp.blockEditor.MediaUpload
    : wp.editor.MediaUpload;
  var PanelBody = wp.components.PanelBody;
  var TextControl = wp.components.TextControl;
  var TextareaControl = wp.components.TextareaControl;
  var Button = wp.components.Button;
  var ColorPalette = wp.components.ColorPalette;
  var RadioControl = wp.components.RadioControl;
  var ToggleControl = wp.components.ToggleControl;

  // Colores predefinidos para la paleta
  var colors = [
    { name: "Aguamarina", color: "#4F8A8B" },
    { name: "Verde Salvia", color: "#8BAB8D" },
    { name: "Dorado Suave", color: "#D4B254" },
    { name: "Rosa Polvo", color: "#D9ADB7" },
    { name: "Blanco", color: "#FFFFFF" },
    { name: "Crema", color: "#F7EDE2" },
    { name: "Gris Claro", color: "#F3F3F3" },
    { name: "Gris Oscuro", color: "#424242" },
    { name: "Negro", color: "#000000" },
  ];

  registerBlockType("wptbt/benefits-block", {
    title: __("Beneficios del Spa"),
    icon: "awards",
    category: "widgets",

    attributes: {
      title: {
        type: "string",
        default: "WHY CHOOSE US?",
      },
      subtitle: {
        type: "string",
        default: "Our benefits",
      },
      description: {
        type: "string",
        default: "Book and enjoy our special treatments",
      },
      content: {
        type: "string",
        default:
          "Experience relaxation and rejuvenation with our exclusive treatments tailored to your needs.",
      },
      mediaType: {
        type: "string",
        default: "image",
      },
      imageID: {
        type: "number",
      },
      imageURL: {
        type: "string",
      },
      videoURL: {
        type: "string",
        default: "",
      },
      videoEmbedCode: {
        type: "string",
        default: "",
      },
      useYouTube: {
        type: "boolean",
        default: false,
      },
      autoplayVideo: {
        type: "boolean",
        default: false,
      },
      benefits: {
        type: "array",
        default: [
          {
            title: "EXPERT STAFF",
            description:
              "Our team consists of certified professionals with years of experience in spa and wellness.",
          },
          {
            title: "BRILLIANT SERVICES",
            description:
              "We offer a wide range of premium treatments using only the highest quality products.",
          },
        ],
      },
      backgroundColor: {
        type: "string",
        default: "#F7EDE2",
      },
      textColor: {
        type: "string",
        default: "#424242",
      },
      accentColor: {
        type: "string",
        default: "#D4B254",
      },
      // Añadir atributos para las ondas
      showTopWave: {
        type: "boolean",
        default: true,
      },
      showBottomWave: {
        type: "boolean",
        default: true,
      },
    },

    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;

      // Función para actualizar un beneficio
      function updateBenefit(index, field, value) {
        var newBenefits = JSON.parse(JSON.stringify(attributes.benefits));
        newBenefits[index][field] = value;
        setAttributes({ benefits: newBenefits });
      }

      // Función para añadir un beneficio
      function addBenefit() {
        var newBenefits = JSON.parse(JSON.stringify(attributes.benefits));
        newBenefits.push({
          title: "NEW BENEFIT",
          description: "Description for the new benefit",
        });
        setAttributes({ benefits: newBenefits });
      }

      // Función para eliminar un beneficio
      function removeBenefit(index) {
        var newBenefits = JSON.parse(JSON.stringify(attributes.benefits));
        newBenefits.splice(index, 1);
        setAttributes({ benefits: newBenefits });
      }

      // Renderizar la previsualización de medios (imagen o video)
      function renderMediaPreview() {
        if (attributes.mediaType === "video") {
          if (attributes.useYouTube && attributes.videoEmbedCode) {
            // Esto no funcionará directamente en el editor, solo muestra un placeholder
            return createElement(
              "div",
              {
                style: {
                  backgroundColor: "#000",
                  height: "250px",
                  borderRadius: "8px",
                  display: "flex",
                  alignItems: "center",
                  justifyContent: "center",
                  color: "#fff",
                  fontSize: "14px",
                  position: "relative",
                  overflow: "hidden",
                },
              },
              createElement(
                "div",
                {
                  style: {
                    position: "absolute",
                    top: "50%",
                    left: "50%",
                    transform: "translate(-50%, -50%)",
                    width: "80px",
                    height: "80px",
                    borderRadius: "50%",
                    backgroundColor: "rgba(255,255,255,0.2)",
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "center",
                  },
                },
                createElement("span", {
                  className: "dashicons dashicons-controls-play",
                  style: {
                    fontSize: "36px",
                    width: "36px",
                    height: "36px",
                  },
                })
              ),
              createElement(
                "div",
                {
                  style: {
                    position: "absolute",
                    bottom: "10px",
                    left: "10px",
                    fontSize: "12px",
                    color: "#fff",
                    backgroundColor: "rgba(0,0,0,0.5)",
                    padding: "5px 10px",
                    borderRadius: "4px",
                    zIndex: "2",
                  },
                },
                "YouTube Video (visible en frontend)"
              )
            );
          } else if (attributes.videoURL) {
            return createElement("video", {
              src: attributes.videoURL,
              controls: true,
              style: {
                maxWidth: "100%",
                height: "auto",
                borderRadius: "8px",
                boxShadow: "0 4px 10px rgba(0,0,0,0.1)",
              },
            });
          } else {
            return createElement(
              "div",
              {
                style: {
                  backgroundColor: "#f0f0f0",
                  height: "250px",
                  borderRadius: "8px",
                  display: "flex",
                  alignItems: "center",
                  justifyContent: "center",
                  color: "#999",
                  fontSize: "14px",
                },
              },
              "Selecciona o sube un video desde el panel lateral"
            );
          }
        } else {
          if (attributes.imageURL) {
            return createElement("img", {
              src: attributes.imageURL,
              alt: attributes.title,
              style: {
                maxWidth: "100%",
                height: "auto",
                borderRadius: "8px",
                boxShadow: "0 4px 10px rgba(0,0,0,0.1)",
              },
            });
          } else {
            return createElement(
              "div",
              {
                style: {
                  backgroundColor: "#f0f0f0",
                  height: "250px",
                  borderRadius: "8px",
                  display: "flex",
                  alignItems: "center",
                  justifyContent: "center",
                  color: "#999",
                  fontSize: "14px",
                },
              },
              "Selecciona una imagen desde el panel lateral"
            );
          }
        }
      }

      return [
        // Panel de controles laterales
        createElement(
          InspectorControls,
          { key: "inspector" },
          // Panel de textos principales
          createElement(
            PanelBody,
            {
              title: __("Textos Principales"),
              initialOpen: true,
            },
            createElement(TextControl, {
              label: __("Título"),
              value: attributes.title,
              onChange: function (value) {
                setAttributes({ title: value });
              },
            }),
            createElement(TextControl, {
              label: __("Subtítulo"),
              value: attributes.subtitle,
              onChange: function (value) {
                setAttributes({ subtitle: value });
              },
            }),
            createElement(TextControl, {
              label: __("Descripción"),
              value: attributes.description,
              onChange: function (value) {
                setAttributes({ description: value });
              },
            }),
            createElement(TextareaControl, {
              label: __("Contenido"),
              value: attributes.content,
              onChange: function (value) {
                setAttributes({ content: value });
              },
            })
          ),

          // Panel de media
          createElement(
            PanelBody,
            {
              title: __("Imagen o Video"),
              initialOpen: false,
            },
            createElement(RadioControl, {
              label: __("Tipo de medio"),
              selected: attributes.mediaType,
              options: [
                { label: "Imagen", value: "image" },
                { label: "Video", value: "video" },
              ],
              onChange: function (value) {
                setAttributes({ mediaType: value });
              },
            }),

            // Controles para imagen
            attributes.mediaType === "image" &&
              createElement(MediaUpload, {
                onSelect: function (media) {
                  setAttributes({
                    imageURL: media.url,
                    imageID: media.id,
                  });
                },
                allowedTypes: ["image"],
                value: attributes.imageID,
                render: function (obj) {
                  return createElement(
                    Button,
                    {
                      className: attributes.imageID
                        ? "editor-post-featured-image__preview"
                        : "editor-post-featured-image__toggle",
                      onClick: obj.open,
                    },
                    attributes.imageID
                      ? [
                          attributes.imageURL &&
                            createElement("img", {
                              src: attributes.imageURL,
                              alt: __("Imagen seleccionada"),
                              style: {
                                maxHeight: "200px",
                                maxWidth: "100%",
                                marginBottom: "12px",
                              },
                            }),
                          createElement("p", {}, __("Reemplazar imagen")),
                        ]
                      : __("Seleccionar imagen")
                  );
                },
              }),

            // Controles para video
            attributes.mediaType === "video" && [
              createElement(ToggleControl, {
                label: __("Usar video de YouTube"),
                checked: attributes.useYouTube,
                onChange: function (value) {
                  setAttributes({ useYouTube: value });
                },
              }),

              attributes.useYouTube
                ? createElement(TextareaControl, {
                    label: __("Código de incrustación de YouTube"),
                    help: __("Pega aquí el código iframe de YouTube"),
                    value: attributes.videoEmbedCode,
                    onChange: function (value) {
                      setAttributes({ videoEmbedCode: value });
                    },
                  })
                : createElement(MediaUpload, {
                    onSelect: function (media) {
                      setAttributes({
                        videoURL: media.url,
                      });
                    },
                    allowedTypes: ["video"],
                    render: function (obj) {
                      return createElement(
                        Button,
                        {
                          className: attributes.videoURL
                            ? "editor-post-featured-image__preview"
                            : "editor-post-featured-image__toggle",
                          onClick: obj.open,
                        },
                        attributes.videoURL
                          ? __("Reemplazar video")
                          : __("Seleccionar video")
                      );
                    },
                  }),

              attributes.mediaType === "video" &&
                createElement(ToggleControl, {
                  label: __("Reproducción automática"),
                  checked: attributes.autoplayVideo,
                  onChange: function (value) {
                    setAttributes({ autoplayVideo: value });
                  },
                }),

              attributes.videoURL &&
                !attributes.useYouTube &&
                createElement(
                  "div",
                  { style: { marginTop: "10px" } },
                  createElement("video", {
                    src: attributes.videoURL,
                    controls: true,
                    style: {
                      maxWidth: "100%",
                      height: "auto",
                      borderRadius: "4px",
                    },
                  })
                ),

              (attributes.videoURL || attributes.videoEmbedCode) &&
                createElement(
                  Button,
                  {
                    isDestructive: true,
                    isSmall: true,
                    onClick: function () {
                      if (attributes.useYouTube) {
                        setAttributes({ videoEmbedCode: "" });
                      } else {
                        setAttributes({ videoURL: "" });
                      }
                    },
                    style: { marginTop: "10px" },
                  },
                  __("Eliminar video")
                ),
            ],

            attributes.imageID &&
              attributes.mediaType === "image" &&
              createElement(
                Button,
                {
                  isDestructive: true,
                  isSmall: true,
                  onClick: function () {
                    setAttributes({
                      imageID: undefined,
                      imageURL: "",
                    });
                  },
                  style: { marginTop: "10px" },
                },
                __("Eliminar imagen")
              )
          ),

          // Panel de configuración de ondas
          createElement(
            PanelBody,
            {
              title: __("Configuración de Ondas"),
              initialOpen: false,
            },
            createElement(ToggleControl, {
              label: __("Mostrar Onda Superior"),
              checked: attributes.showTopWave,
              onChange: function (value) {
                setAttributes({ showTopWave: value });
              },
              help: __(
                "Activa o desactiva la onda decorativa en la parte superior."
              ),
            }),
            createElement(ToggleControl, {
              label: __("Mostrar Onda Inferior"),
              checked: attributes.showBottomWave,
              onChange: function (value) {
                setAttributes({ showBottomWave: value });
              },
              help: __(
                "Activa o desactiva la onda decorativa en la parte inferior."
              ),
            })
          ),

          // Panel de beneficios
          createElement(
            PanelBody,
            {
              title: __("Beneficios"),
              initialOpen: false,
            },
            attributes.benefits.map(function (benefit, index) {
              return createElement(
                "div",
                {
                  key: "benefit-" + index,
                  className: "benefit-item-control",
                  style: {
                    marginBottom: "15px",
                    padding: "12px",
                    backgroundColor: "#f9f9f9",
                    borderRadius: "4px",
                    border: "1px solid #e0e0e0",
                  },
                },
                createElement(
                  "h4",
                  {
                    style: {
                      margin: "0 0 10px 0",
                      fontSize: "14px",
                      fontWeight: "bold",
                    },
                  },
                  "Beneficio " + (index + 1)
                ),
                createElement(TextControl, {
                  label: __("Título"),
                  value: benefit.title,
                  onChange: function (value) {
                    updateBenefit(index, "title", value);
                  },
                }),
                createElement(TextareaControl, {
                  label: __("Descripción"),
                  value: benefit.description,
                  onChange: function (value) {
                    updateBenefit(index, "description", value);
                  },
                }),
                createElement(
                  Button,
                  {
                    isDestructive: true,
                    isSmall: true,
                    onClick: function () {
                      removeBenefit(index);
                    },
                    style: { marginTop: "8px" },
                  },
                  __("Eliminar beneficio")
                )
              );
            }),
            createElement(
              Button,
              {
                isPrimary: true,
                onClick: addBenefit,
                style: { marginTop: "10px" },
              },
              __("Añadir beneficio")
            )
          ),

          // Panel de colores
          createElement(
            PanelBody,
            {
              title: __("Colores"),
              initialOpen: false,
            },
            createElement(
              "div",
              { className: "components-base-control" },
              createElement(
                "label",
                { className: "components-base-control__label" },
                __("Color de Fondo")
              ),
              createElement(ColorPalette, {
                colors: colors,
                value: attributes.backgroundColor,
                onChange: function (value) {
                  setAttributes({ backgroundColor: value });
                },
              })
            ),
            createElement(
              "div",
              { className: "components-base-control" },
              createElement(
                "label",
                { className: "components-base-control__label" },
                __("Color de Texto")
              ),
              createElement(ColorPalette, {
                colors: colors,
                value: attributes.textColor,
                onChange: function (value) {
                  setAttributes({ textColor: value });
                },
              })
            ),
            createElement(
              "div",
              { className: "components-base-control" },
              createElement(
                "label",
                { className: "components-base-control__label" },
                __("Color de Acento")
              ),
              createElement(ColorPalette, {
                colors: colors,
                value: attributes.accentColor,
                onChange: function (value) {
                  setAttributes({ accentColor: value });
                },
              })
            )
          )
        ),

        // Vista previa del bloque
        createElement(
          "div",
          {
            className: "wp-block-wptbt-benefits-block-preview",
            style: {
              padding: "30px",
              backgroundColor: attributes.backgroundColor || "#F7EDE2",
              color: attributes.textColor || "#424242",
              border: "1px dashed #d9adb7",
              borderRadius: "8px",
              position: "relative", // Añadido para posicionar las ondas
            },
          },
          // Visualización del estado de las ondas
          createElement(
            "div",
            {
              className: "wave-status-preview",
              style: {
                background: "#f0f0f0",
                padding: "8px 12px",
                borderRadius: "4px",
                marginBottom: "15px",
                fontSize: "12px",
                color: "#666",
                display: "flex",
                justifyContent: "space-between",
                alignItems: "center",
              },
            },
            createElement(
              "span",
              {},
              __("Onda Superior: ") + (attributes.showTopWave ? "✓" : "✗")
            ),
            createElement(
              "span",
              {},
              __("Onda Inferior: ") + (attributes.showBottomWave ? "✓" : "✗")
            )
          ),
          createElement(
            "div",
            {
              className: "benefits-preview-inner",
              style: {
                display: "flex",
                flexWrap: "wrap",
                alignItems: "center",
                justifyContent: "space-between",
              },
            },
            // Columna de texto
            createElement(
              "div",
              {
                className: "benefits-text-column",
                style: {
                  width: "50%",
                  padding: "20px",
                },
              },
              createElement(
                "span",
                {
                  style: {
                    display: "block",
                    marginBottom: "10px",
                    color: attributes.accentColor || "#D4B254",
                    fontStyle: "italic",
                  },
                },
                attributes.subtitle
              ),
              createElement(
                "h2",
                {
                  style: {
                    fontSize: "24px",
                    fontWeight: "500",
                    marginBottom: "15px",
                  },
                },
                attributes.title
              ),
              createElement(
                "p",
                {
                  style: {
                    fontSize: "18px",
                    fontWeight: "500",
                    marginBottom: "15px",
                    color: attributes.accentColor || "#D4B254",
                  },
                },
                attributes.description
              ),
              createElement(
                "p",
                {
                  style: {
                    fontSize: "14px",
                    marginBottom: "20px",
                    maxWidth: "500px",
                  },
                },
                attributes.content.length > 100
                  ? attributes.content.substring(0, 100) + "..."
                  : attributes.content
              ),
              // Lista de beneficios
              createElement(
                "div",
                {
                  className: "benefits-list-preview",
                  style: {
                    marginTop: "20px",
                  },
                },
                attributes.benefits.map(function (benefit, index) {
                  return createElement(
                    "div",
                    {
                      key: "benefit-preview-" + index,
                      style: {
                        display: "flex",
                        marginBottom: "15px",
                      },
                    },
                    createElement(
                      "div",
                      {
                        style: {
                          width: "24px",
                          height: "24px",
                          borderRadius: "50%",
                          backgroundColor: attributes.accentColor || "#D4B254",
                          marginRight: "12px",
                          flexShrink: 0,
                          display: "flex",
                          alignItems: "center",
                          justifyContent: "center",
                          color: "white",
                          fontSize: "12px",
                        },
                      },
                      "✓"
                    ),
                    createElement(
                      "div",
                      {},
                      createElement(
                        "h4",
                        {
                          style: {
                            margin: "0 0 5px 0",
                            fontSize: "16px",
                            fontWeight: "500",
                          },
                        },
                        benefit.title
                      ),
                      createElement(
                        "p",
                        {
                          style: {
                            margin: 0,
                            fontSize: "13px",
                            opacity: 0.8,
                          },
                        },
                        benefit.description.length > 60
                          ? benefit.description.substring(0, 60) + "..."
                          : benefit.description
                      )
                    )
                  );
                })
              )
            ),

            // Columna de media (imagen o video)
            createElement(
              "div",
              {
                className: "benefits-media-column",
                style: {
                  width: "50%",
                  padding: "20px",
                },
              },
              renderMediaPreview()
            )
          ),
          // Nota informativa
          createElement(
            "p",
            {
              style: {
                fontSize: "13px",
                color: "#888",
                fontStyle: "italic",
                marginTop: "20px",
                marginBottom: "0",
                textAlign: "center",
              },
            },
            "Este bloque muestra los beneficios de tu spa con una imagen o video complementario y puede incluir ondas decorativas en la parte superior e inferior."
          )
        ),
      ];
    },

    // Uso de PHP para renderizar el bloque
    save: function () {
      return null;
    },
  });
})();
