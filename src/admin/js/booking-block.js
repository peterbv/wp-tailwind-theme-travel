(function (blocks, editor, components, i18n, element) {
  var el = element.createElement;
  var __ = i18n.__;
  var RichText = editor.RichText;
  var InspectorControls = editor.InspectorControls;
  var MediaUpload = editor.MediaUpload;
  var PanelBody = components.PanelBody;
  var TextControl = components.TextControl;
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

  blocks.registerBlockType("wptbt/booking-block", {
    title: __("Formulario de Reservas Spa"),
    icon: "calendar-alt",
    category: "widgets",
    attributes: {
      title: {
        type: "string",
        default: "Book Now",
      },
      subtitle: {
        type: "string",
        default: "Appointment",
      },
      description: {
        type: "string",
        default: "Book your spa treatment and enjoy a moment of relaxation.",
      },
      imageID: {
        type: "number",
      },
      imageURL: {
        type: "string",
        default: "",
      },
      buttonText: {
        type: "string",
        default: "BOOK NOW",
      },
      buttonColor: {
        type: "string",
        default: "#D4B254",
      },
      textColor: {
        type: "string",
        default: "#FFFFFF",
      },
      accentColor: {
        type: "string",
        default: "#D4B254",
      },
      emailRecipient: {
        type: "string",
        default: "",
      },
      // Añadir los nuevos atributos para controlar las ondas
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

      function onSelectImage(media) {
        setAttributes({
          imageURL: media.url,
          imageID: media.id,
        });
      }

      return [
        // Panel de control
        el(
          InspectorControls,
          {},
          el(
            PanelBody,
            { title: __("Configuración General"), initialOpen: true },
            el(TextControl, {
              label: __("Correo Electrónico de Destino"),
              help: __(
                "Correo donde se enviarán las reservas. Si está vacío, se usará el correo del administrador."
              ),
              value: attributes.emailRecipient,
              onChange: function (value) {
                setAttributes({ emailRecipient: value });
              },
            })
          ),

          el(
            PanelBody,
            { title: __("Textos del Formulario"), initialOpen: false },
            el(TextControl, {
              label: __("Título"),
              value: attributes.title,
              onChange: function (value) {
                setAttributes({ title: value });
              },
            }),
            el(TextControl, {
              label: __("Subtítulo"),
              value: attributes.subtitle,
              onChange: function (value) {
                setAttributes({ subtitle: value });
              },
            }),
            el(TextControl, {
              label: __("Descripción"),
              value: attributes.description,
              onChange: function (value) {
                setAttributes({ description: value });
              },
            }),
            el(TextControl, {
              label: __("Texto del Botón"),
              value: attributes.buttonText,
              onChange: function (value) {
                setAttributes({ buttonText: value });
              },
            })
          ),

          el(
            PanelBody,
            { title: __("Imagen de Fondo"), initialOpen: false },
            el(MediaUpload, {
              onSelect: onSelectImage,
              allowedTypes: ["image"],
              value: attributes.imageID,
              render: function (obj) {
                return el(
                  Button,
                  {
                    className:
                      "components-button editor-post-featured-image__toggle",
                    onClick: obj.open,
                  },
                  attributes.imageURL
                    ? __("Cambiar imagen de fondo")
                    : __("Seleccionar imagen de fondo")
                );
              },
            }),
            attributes.imageURL &&
              el(
                "div",
                { className: "editor-post-featured-image" },
                el("img", {
                  src: attributes.imageURL,
                  style: {
                    maxHeight: "200px",
                    width: "auto",
                    marginTop: "10px",
                  },
                })
              ),
            attributes.imageURL &&
              el(
                Button,
                {
                  isLink: true,
                  isDestructive: true,
                  onClick: function () {
                    setAttributes({
                      imageURL: "",
                      imageID: undefined,
                    });
                  },
                  style: { marginTop: "10px" },
                },
                __("Eliminar imagen")
              )
          ),

          // Añadir un nuevo panel para configurar las ondas
          el(
            PanelBody,
            { title: __("Configuración de Ondas"), initialOpen: false },
            el(ToggleControl, {
              label: __("Mostrar Onda Superior"),
              checked: attributes.showTopWave,
              onChange: function (value) {
                setAttributes({ showTopWave: value });
              },
              help: __(
                "Activa o desactiva la onda decorativa en la parte superior."
              ),
            }),
            el(ToggleControl, {
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

          el(
            PanelBody,
            { title: __("Colores"), initialOpen: false },
            el(
              "p",
              { className: "components-base-control__label" },
              __("Color de Texto")
            ),
            el(ColorPalette, {
              colors: colors,
              value: attributes.textColor,
              onChange: function (color) {
                setAttributes({ textColor: color });
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
              onChange: function (color) {
                setAttributes({ accentColor: color });
              },
            }),
            el(
              "p",
              { className: "components-base-control__label" },
              __("Color del Botón")
            ),
            el(ColorPalette, {
              colors: colors,
              value: attributes.buttonColor,
              onChange: function (color) {
                setAttributes({ buttonColor: color });
              },
            })
          )
        ),

        // Vista del bloque en el editor
        el(
          "div",
          {
            className: props.className + " wp-block-wptbt-booking-block",
            style: {
              backgroundColor: "#f9f9f9",
              borderRadius: "8px",
              padding: "30px",
              border: "1px dashed #d9adb7",
              maxWidth: "100%",
              position: "relative",
              overflow: "hidden",
            },
          },
          // Miniatura de la imagen de fondo si está establecida
          attributes.imageURL &&
            el("div", {
              className: "booking-block-bg-preview",
              style: {
                position: "absolute",
                top: 0,
                left: 0,
                width: "100%",
                height: "100%",
                backgroundImage: `url(${attributes.imageURL})`,
                backgroundSize: "cover",
                backgroundPosition: "center",
                opacity: 0.2,
                zIndex: 0,
              },
            }),

          // Contenido del bloque en el editor
          el(
            "div",
            {
              className: "booking-block-editor-content",
              style: {
                position: "relative",
                zIndex: 1,
              },
            },
            // Icono
            el(
              "div",
              {
                className: "booking-icon-preview flex justify-center mb-6",
                style: { marginBottom: "20px" },
              },
              el(
                "div",
                {
                  style: {
                    width: "50px",
                    height: "50px",
                    borderRadius: "50%",
                    backgroundColor: attributes.accentColor,
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "center",
                  },
                },
                el(
                  "svg",
                  {
                    width: "24",
                    height: "24",
                    viewBox: "0 0 24 24",
                    fill: "none",
                    xmlns: "http://www.w3.org/2000/svg",
                  },
                  el("path", {
                    d: "M19 4H5C3.89543 4 3 4.89543 3 6V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V6C21 4.89543 20.1046 4 19 4Z",
                    stroke: "#FFFFFF",
                    strokeWidth: "2",
                    strokeLinecap: "round",
                    strokeLinejoin: "round",
                  }),
                  el("path", {
                    d: "M16 2V6",
                    stroke: "#FFFFFF",
                    strokeWidth: "2",
                    strokeLinecap: "round",
                    strokeLinejoin: "round",
                  }),
                  el("path", {
                    d: "M8 2V6",
                    stroke: "#FFFFFF",
                    strokeWidth: "2",
                    strokeLinecap: "round",
                    strokeLinejoin: "round",
                  }),
                  el("path", {
                    d: "M3 10H21",
                    stroke: "#FFFFFF",
                    strokeWidth: "2",
                    strokeLinecap: "round",
                    strokeLinejoin: "round",
                  })
                )
              )
            ),

            // Visualización del estado de las ondas (para el editor)
            el(
              "div",
              {
                className: "wave-status-preview",
                style: {
                  background: "#f0f0f0",
                  padding: "10px",
                  borderRadius: "4px",
                  marginBottom: "15px",
                  fontSize: "12px",
                  color: "#666",
                },
              },
              el(
                "div",
                {
                  style: {
                    display: "flex",
                    justifyContent: "space-between",
                    alignItems: "center",
                  },
                },
                el(
                  "span",
                  {},
                  __("Onda Superior: ") + (attributes.showTopWave ? "✓" : "✗")
                ),
                el(
                  "span",
                  {},
                  __("Onda Inferior: ") +
                    (attributes.showBottomWave ? "✓" : "✗")
                )
              )
            ),

            // Título editable
            el(
              "div",
              { className: "text-center mb-6" },
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
                className: "text-3xl font-medium mb-4",
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
                value: attributes.description,
                onChange: function (description) {
                  setAttributes({ description: description });
                },
                placeholder: __("Descripción del formulario"),
              })
            ),

            // Previsualización del formulario (campos de muestra)
            el(
              "div",
              {
                className:
                  "booking-form-preview max-w-md mx-auto bg-white p-6 rounded-lg shadow-sm",
                style: {
                  borderRadius: "8px",
                  padding: "24px",
                  backgroundColor: "white",
                  boxShadow: "0 2px 10px rgba(0,0,0,0.05)",
                },
              },
              el(
                "div",
                {
                  className: "form-fields-preview grid grid-cols-2 gap-4",
                  style: {
                    display: "grid",
                    gridTemplateColumns: "1fr 1fr",
                    gap: "16px",
                    marginBottom: "16px",
                  },
                },
                // Campo nombre
                el(
                  "div",
                  { style: { gridColumn: "span 1" } },
                  el(
                    "label",
                    {
                      className: "block text-sm font-medium mb-1",
                      style: {
                        fontSize: "14px",
                        marginBottom: "4px",
                        display: "block",
                        color: "#666",
                      },
                    },
                    __("Nombre")
                  ),
                  el("div", {
                    style: {
                      height: "36px",
                      backgroundColor: "#f9f9f9",
                      border: "1px solid #e0e0e0",
                      borderRadius: "4px",
                    },
                  })
                ),
                // Campo email
                el(
                  "div",
                  { style: { gridColumn: "span 1" } },
                  el(
                    "label",
                    {
                      className: "block text-sm font-medium mb-1",
                      style: {
                        fontSize: "14px",
                        marginBottom: "4px",
                        display: "block",
                        color: "#666",
                      },
                    },
                    __("Email")
                  ),
                  el("div", {
                    style: {
                      height: "36px",
                      backgroundColor: "#f9f9f9",
                      border: "1px solid #e0e0e0",
                      borderRadius: "4px",
                    },
                  })
                ),
                // Campo servicio
                el(
                  "div",
                  { style: { gridColumn: "span 1" } },
                  el(
                    "label",
                    {
                      className: "block text-sm font-medium mb-1",
                      style: {
                        fontSize: "14px",
                        marginBottom: "4px",
                        display: "block",
                        color: "#666",
                      },
                    },
                    __("Servicio")
                  ),
                  el("div", {
                    style: {
                      height: "36px",
                      backgroundColor: "#f9f9f9",
                      border: "1px solid #e0e0e0",
                      borderRadius: "4px",
                    },
                  })
                ),
                // Campo fecha
                el(
                  "div",
                  { style: { gridColumn: "span 1" } },
                  el(
                    "label",
                    {
                      className: "block text-sm font-medium mb-1",
                      style: {
                        fontSize: "14px",
                        marginBottom: "4px",
                        display: "block",
                        color: "#666",
                      },
                    },
                    __("Fecha")
                  ),
                  el("div", {
                    style: {
                      height: "36px",
                      backgroundColor: "#f9f9f9",
                      border: "1px solid #e0e0e0",
                      borderRadius: "4px",
                    },
                  })
                )
              ),

              // Campo mensaje
              el(
                "div",
                { style: { marginBottom: "20px" } },
                el(
                  "label",
                  {
                    className: "block text-sm font-medium mb-1",
                    style: {
                      fontSize: "14px",
                      marginBottom: "4px",
                      display: "block",
                      color: "#666",
                    },
                  },
                  __("Mensaje")
                ),
                el("div", {
                  style: {
                    height: "80px",
                    backgroundColor: "#f9f9f9",
                    border: "1px solid #e0e0e0",
                    borderRadius: "4px",
                  },
                })
              ),

              // Botón de envío
              el(
                "div",
                { className: "text-center" },
                el(
                  "div",
                  {
                    style: {
                      backgroundColor: attributes.buttonColor,
                      color: attributes.textColor,
                      padding: "10px 24px",
                      borderRadius: "4px",
                      fontWeight: "600",
                      display: "inline-block",
                    },
                  },
                  attributes.buttonText
                )
              )
            )
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
