(function (blocks, editor, components, i18n, element) {
  var el = element.createElement;
  var __ = i18n.__;
  var InspectorControls = editor.InspectorControls;
  var PanelBody = components.PanelBody;
  var TextControl = components.TextControl;
  var TextareaControl = components.TextareaControl;
  var SelectControl = components.SelectControl;
  var ColorPalette = components.ColorPalette;
  var Button = components.Button;
  var ToggleControl = components.ToggleControl;

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

  blocks.registerBlockType("wptbt/faq-block", {
    title: __("Preguntas Frecuentes"),
    icon: "editor-help",
    category: "widgets",
    attributes: {
      title: {
        type: "string",
        default: "Preguntas Frecuentes",
      },
      subtitle: {
        type: "string",
        default: "Resolvemos tus dudas",
      },
      faqs: {
        type: "array",
        default: [
          {
            question: "¿Qué debo hacer antes de mi primera sesión de masaje?",
            answer:
              "Recomendamos llegar 15 minutos antes de tu cita para completar un breve cuestionario de salud. Viste ropa cómoda y evita comidas pesadas o alcohol antes de tu sesión. Si tienes alguna condición médica o preocupación, por favor notifícalo con anticipación.",
          },
          {
            question: "¿Cuánto dura una sesión de masaje típica?",
            answer:
              "Nuestras sesiones de masaje estándar duran 60 minutos, pero también ofrecemos opciones de 30, 90 y 120 minutos según tus necesidades y preferencias.",
          },
          {
            question: "¿Es necesario desvestirse completamente para un masaje?",
            answer:
              "No es necesario. Te puedes desvestir al nivel de tu comodidad. Durante el masaje estarás cubierto con sábanas y solo se descubrirá la parte del cuerpo que está siendo tratada.",
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
      layout: {
        type: "string",
        default: "full",
      },
      // Añadir nuevos atributos para las ondas
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

      // Función para actualizar preguntas y respuestas
      function updateFaqItem(index, key, value) {
        var newFaqs = JSON.parse(JSON.stringify(attributes.faqs));
        newFaqs[index][key] = value;
        setAttributes({ faqs: newFaqs });
      }

      // Función para añadir nueva pregunta
      function addFaqItem() {
        var newFaqs = JSON.parse(JSON.stringify(attributes.faqs));
        newFaqs.push({
          question: "Nueva pregunta...",
          answer: "Escribe aquí la respuesta...",
        });
        setAttributes({ faqs: newFaqs });
      }

      // Función para eliminar pregunta
      function removeFaqItem(index) {
        var newFaqs = JSON.parse(JSON.stringify(attributes.faqs));
        newFaqs.splice(index, 1);
        setAttributes({ faqs: newFaqs });
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
              label: __("Título"),
              value: attributes.title,
              onChange: function (title) {
                setAttributes({ title });
              },
            }),
            el(TextControl, {
              label: __("Subtítulo"),
              value: attributes.subtitle,
              onChange: function (subtitle) {
                setAttributes({ subtitle });
              },
            }),
            el(SelectControl, {
              label: __("Diseño"),
              value: attributes.layout,
              options: [
                { label: __("Ancho completo"), value: "full" },
                { label: __("Contenedor"), value: "boxed" },
              ],
              onChange: function (layout) {
                setAttributes({ layout });
              },
            })
          ),

          // Añadir panel para la configuración de ondas
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
            { title: __("Preguntas y Respuestas"), initialOpen: true },
            attributes.faqs.map(function (faq, index) {
              return el(
                "div",
                {
                  key: index,
                  className: "faq-item-control",
                  style: {
                    marginBottom: "20px",
                    padding: "10px",
                    backgroundColor: "#f9f9f9",
                    borderRadius: "4px",
                    border: "1px solid #e0e0e0",
                  },
                },
                el(
                  "div",
                  { style: { marginBottom: "10px" } },
                  el("strong", {}, "Pregunta " + (index + 1))
                ),
                el(TextControl, {
                  value: faq.question,
                  onChange: function (value) {
                    updateFaqItem(index, "question", value);
                  },
                }),
                el(TextareaControl, {
                  label: __("Respuesta"),
                  value: faq.answer,
                  onChange: function (value) {
                    updateFaqItem(index, "answer", value);
                  },
                }),
                el(
                  Button,
                  {
                    isDestructive: true,
                    isSmall: true,
                    onClick: function () {
                      removeFaqItem(index);
                    },
                    style: {
                      marginTop: "5px",
                    },
                  },
                  __("Eliminar pregunta")
                )
              );
            }),
            el(
              Button,
              {
                isPrimary: true,
                onClick: addFaqItem,
                style: {
                  marginTop: "10px",
                },
              },
              __("Añadir nueva pregunta")
            )
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
                setAttributes({ backgroundColor });
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
                setAttributes({ textColor });
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
                setAttributes({ accentColor });
              },
            })
          )
        ),

        // Vista del bloque en el editor
        el(
          "div",
          {
            className: props.className + " wp-block-wptbt-faq-block-editor",
            style: {
              backgroundColor: attributes.backgroundColor || "#F7EDE2",
              color: attributes.textColor || "#424242",
              border: "1px dashed #d9adb7",
              borderRadius: "8px",
              padding: "30px",
              position: "relative", // Para posicionar las ondas correctamente
            },
          },
          // Visualización del estado de las ondas
          el(
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
            el(
              "span",
              {},
              __("Onda Superior: ") + (attributes.showTopWave ? "✓" : "✗")
            ),
            el(
              "span",
              {},
              __("Onda Inferior: ") + (attributes.showBottomWave ? "✓" : "✗")
            )
          ),
          el(
            "div",
            { className: "wptbt-faq-block-editor" },
            el("img", {
              src: "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='40' height='40'><path fill='%23d9adb7' d='M12 4C7.6 4 4 7.6 4 12s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm0 14c-3.3 0-6-2.7-6-6s2.7-6 6-6 6 2.7 6 6-2.7 6-6 6zm1-2h-2v-2h2v2zm0-4h-2V7h2v5z'/></svg>",
              className: "faq-icon-preview",
              alt: "FAQ icon",
              style: {
                display: "block",
                margin: "0 auto 20px",
              },
            }),
            el(
              "h2",
              {
                style: {
                  textAlign: "center",
                  fontSize: "24px",
                  marginBottom: "10px",
                  color: attributes.textColor || "#424242",
                },
              },
              attributes.title
            ),
            el(
              "div",
              {
                className: "faq-subtitle",
                style: {
                  textAlign: "center",
                  color: attributes.accentColor || "#D4B254",
                  marginBottom: "30px",
                  fontStyle: "italic",
                },
              },
              attributes.subtitle
            ),
            el(
              "div",
              {
                className: "faq-preview",
                style: {
                  maxWidth: attributes.layout === "boxed" ? "800px" : "100%",
                  margin: "0 auto",
                },
              },
              attributes.faqs.length > 0
                ? [
                    // Primera pregunta expandida (para mostrar cómo se ve)
                    el(
                      "div",
                      {
                        className: "faq-preview-item",
                        key: "preview-0",
                        style: {
                          backgroundColor: "rgba(255, 255, 255, 0.7)",
                          borderRadius: "8px",
                          padding: "15px 20px",
                          marginBottom: "15px",
                          boxShadow: "0 2px 5px rgba(0,0,0,0.05)",
                        },
                      },
                      el(
                        "h3",
                        {
                          style: {
                            fontWeight: "500",
                            display: "flex",
                            alignItems: "center",
                            justifyContent: "space-between",
                            marginBottom: "10px",
                            color: attributes.textColor || "#424242",
                          },
                        },
                        attributes.faqs[0].question.length > 40
                          ? attributes.faqs[0].question.substring(0, 40) + "..."
                          : attributes.faqs[0].question,
                        el(
                          "span",
                          {
                            style: {
                              backgroundColor:
                                attributes.accentColor || "#D4B254",
                              color: "#fff",
                              width: "24px",
                              height: "24px",
                              borderRadius: "50%",
                              display: "flex",
                              alignItems: "center",
                              justifyContent: "center",
                              fontSize: "16px",
                              fontWeight: "bold",
                            },
                          },
                          "-"
                        )
                      ),
                      el(
                        "p",
                        {
                          style: {
                            fontSize: "14px",
                            lineHeight: "1.6",
                            margin: 0,
                          },
                        },
                        attributes.faqs[0].answer.length > 100
                          ? attributes.faqs[0].answer.substring(0, 100) + "..."
                          : attributes.faqs[0].answer
                      )
                    ),

                    // Mostrar algunas preguntas más como contraídas
                    attributes.faqs.slice(1, 3).map(function (faq, index) {
                      return el(
                        "div",
                        {
                          className: "faq-preview-item-collapsed",
                          key: "preview-" + (index + 1),
                          style: {
                            backgroundColor: "rgba(255, 255, 255, 0.7)",
                            borderRadius: "8px",
                            padding: "15px 20px",
                            marginBottom: "15px",
                            boxShadow: "0 2px 5px rgba(0,0,0,0.05)",
                          },
                        },
                        el(
                          "h3",
                          {
                            style: {
                              fontWeight: "500",
                              display: "flex",
                              alignItems: "center",
                              justifyContent: "space-between",
                              margin: 0,
                              color: attributes.textColor || "#424242",
                            },
                          },
                          faq.question.length > 40
                            ? faq.question.substring(0, 40) + "..."
                            : faq.question,
                          el(
                            "span",
                            {
                              style: {
                                backgroundColor:
                                  attributes.accentColor || "#D4B254",
                                color: "#fff",
                                width: "24px",
                                height: "24px",
                                borderRadius: "50%",
                                display: "flex",
                                alignItems: "center",
                                justifyContent: "center",
                                fontSize: "16px",
                                fontWeight: "bold",
                              },
                            },
                            "+"
                          )
                        )
                      );
                    }),

                    // Indicador de más preguntas si hay más de 3
                    attributes.faqs.length > 3
                      ? el(
                          "div",
                          {
                            className: "faq-preview-more",
                            key: "preview-more",
                            style: {
                              textAlign: "center",
                              padding: "10px",
                              fontStyle: "italic",
                              color: attributes.accentColor || "#D4B254",
                            },
                          },
                          "+" +
                            (attributes.faqs.length - 3) +
                            " preguntas más..."
                        )
                      : null,
                  ]
                : el(
                    "p",
                    {
                      className: "faq-preview-empty",
                      style: {
                        textAlign: "center",
                        padding: "40px 20px",
                        backgroundColor: "rgba(255,255,255,0.5)",
                        borderRadius: "8px",
                      },
                    },
                    "Añade preguntas desde el panel lateral"
                  )
            ),
            el(
              "p",
              {
                className: "faq-editor-note",
                style: {
                  fontSize: "13px",
                  color: "#888",
                  fontStyle: "italic",
                  marginTop: "20px",
                  textAlign: "center",
                },
              },
              __(
                "Este bloque muestra las preguntas frecuentes en formato de acordeón. Las ondas decorativas pueden ser activadas o desactivadas desde el panel lateral."
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
