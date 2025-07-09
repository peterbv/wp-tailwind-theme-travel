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
  var RangeControl = components.RangeControl;
  var MediaUpload = editor.MediaUpload;
  var MediaUploadCheck = editor.MediaUploadCheck;

  // Colores predefinidos
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

  blocks.registerBlockType("wptbt/google-reviews-block", {
    title: __("Testimonios"),
    icon: "format-quote",
    category: "widgets",
    attributes: {
      title: { type: "string", default: "What They Say" },
      subtitle: { type: "string", default: "Testimonial" },
      description: { type: "string", default: "" },
      placeId: { type: "string", default: "" },
      apiKey: { type: "string", default: "" },
      reviewCount: { type: "number", default: 5 },
      minRating: { type: "number", default: 4 },
      displayName: { type: "boolean", default: true },
      displayAvatar: { type: "boolean", default: true },
      displayRating: { type: "boolean", default: true },
      displayDate: { type: "boolean", default: false },
      displayRole: { type: "boolean", default: true },
      clientRole: { type: "string", default: "Customer" },
      autoplay: { type: "boolean", default: true },
      autoplaySpeed: { type: "number", default: 5000 },
      backgroundColor: { type: "string", default: "#FFF5F3" },
      cardBackgroundColor: { type: "string", default: "#FFFFFF" },
      textColor: { type: "string", default: "#424242" },
      accentColor: { type: "string", default: "#D4B254" },
      carouselType: { type: "string", default: "slide" },
      useStaticData: { type: "boolean", default: true },
      staticReviews: {
        type: "array",
        default: [
          {
            author_name: "Wade Warren",
            profile_photo_url: "",
            profile_photo_id: 0,
            rating: 4,
            relative_time_description: "recently",
            text: "Suspendisse sit amet neque euismod, convallis quam eget, dignissim massa. Aliquam blandit risus purus, in congue.",
          },
          {
            author_name: "Wade Warren",
            profile_photo_url: "",
            profile_photo_id: 0,
            rating: 4,
            relative_time_description: "recently",
            text: "Suspendisse sit amet neque euismod, convallis quam eget, dignissim massa. Aliquam blandit risus purus, in congue.",
          },
          {
            author_name: "Robert Fox",
            profile_photo_url: "",
            profile_photo_id: 0,
            rating: 4,
            relative_time_description: "recently",
            text: "Suspendisse sit amet neque euismod, convallis quam eget, dignissim massa. Aliquam blandit risus purus, in congue.",
          },
        ],
      },
    },

    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;

      // Función para actualizar reseñas estáticas
      function updateReviewItem(index, key, value) {
        var newReviews = JSON.parse(JSON.stringify(attributes.staticReviews));
        newReviews[index][key] = value;
        setAttributes({ staticReviews: newReviews });
      }

      // Función para añadir nueva reseña
      function addReviewItem() {
        var newReviews = JSON.parse(JSON.stringify(attributes.staticReviews));
        newReviews.push({
          author_name: "New Reviewer",
          profile_photo_url: "",
          profile_photo_id: 0,
          rating: 5,
          relative_time_description: "recently",
          text: "Write your review text here...",
        });
        setAttributes({ staticReviews: newReviews });
      }

      // Función para eliminar reseña
      function removeReviewItem(index) {
        var newReviews = JSON.parse(JSON.stringify(attributes.staticReviews));
        newReviews.splice(index, 1);
        setAttributes({ staticReviews: newReviews });
      }

      // Función para manejar la selección de imagen
      function onSelectImage(index, media) {
        var newReviews = JSON.parse(JSON.stringify(attributes.staticReviews));

        if (media && media.url) {
          newReviews[index].profile_photo_url = media.url;
          newReviews[index].profile_photo_id = media.id;
        }

        setAttributes({ staticReviews: newReviews });
      }

      // Función para eliminar imagen
      function removeImage(index) {
        var newReviews = JSON.parse(JSON.stringify(attributes.staticReviews));
        newReviews[index].profile_photo_url = "";
        newReviews[index].profile_photo_id = 0;
        setAttributes({ staticReviews: newReviews });
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
            el(TextControl, {
              label: __("Descripción (opcional)"),
              value: attributes.description,
              onChange: function (description) {
                setAttributes({ description });
              },
            }),
            el(TextControl, {
              label: __("Texto para el rol del cliente"),
              value: attributes.clientRole,
              onChange: function (clientRole) {
                setAttributes({ clientRole });
              },
            })
          ),

          el(
            PanelBody,
            { title: __("Configuración de Google Maps"), initialOpen: false },
            el(ToggleControl, {
              label: __("Usar datos estáticos (sin API)"),
              checked: attributes.useStaticData,
              onChange: function (useStaticData) {
                setAttributes({ useStaticData });
              },
            }),
            !attributes.useStaticData && [
              el(TextControl, {
                label: __("ID del Lugar (Place ID)"),
                help: __("ID de tu negocio en Google Maps"),
                value: attributes.placeId,
                onChange: function (placeId) {
                  setAttributes({ placeId });
                },
              }),
              el(TextControl, {
                label: __("Clave API de Google"),
                help: __("Tu clave API de Google Maps Platform"),
                value: attributes.apiKey,
                onChange: function (apiKey) {
                  setAttributes({ apiKey });
                },
              }),
              el(RangeControl, {
                label: __("Número de reseñas"),
                value: attributes.reviewCount,
                min: 1,
                max: 10,
                onChange: function (reviewCount) {
                  setAttributes({ reviewCount });
                },
              }),
              el(RangeControl, {
                label: __("Calificación mínima"),
                value: attributes.minRating,
                min: 1,
                max: 5,
                onChange: function (minRating) {
                  setAttributes({ minRating });
                },
              }),
            ]
          ),

          attributes.useStaticData &&
            el(
              PanelBody,
              { title: __("Testimonios"), initialOpen: true },
              attributes.staticReviews.map(function (review, index) {
                return el(
                  "div",
                  {
                    key: index,
                    className: "review-item-control",
                    style: {
                      marginBottom: "20px",
                      padding: "15px",
                      backgroundColor: "#f9f9f9",
                      borderRadius: "8px",
                      border: "1px solid #e0e0e0",
                    },
                  },
                  el(
                    "div",
                    {
                      style: {
                        marginBottom: "15px",
                        display: "flex",
                        justifyContent: "space-between",
                        alignItems: "center",
                      },
                    },
                    el("strong", {}, "Testimonio " + (index + 1)),
                    el(
                      Button,
                      {
                        isDestructive: true,
                        isSmall: true,
                        onClick: function () {
                          removeReviewItem(index);
                        },
                      },
                      __("Eliminar")
                    )
                  ),

                  // Imagen del autor
                  el(
                    "div",
                    {
                      style: {
                        marginBottom: "15px",
                        padding: "10px",
                        backgroundColor: "#f1f1f1",
                        borderRadius: "4px",
                      },
                    },
                    el(
                      "label",
                      {
                        style: {
                          display: "block",
                          marginBottom: "8px",
                          fontWeight: "500",
                        },
                      },
                      __("Imagen del cliente")
                    ),

                    el(
                      "div",
                      {
                        style: {
                          display: "flex",
                          flexDirection: review.profile_photo_url
                            ? "column"
                            : "row",
                          gap: "10px",
                        },
                      },
                      review.profile_photo_url &&
                        el(
                          "div",
                          { style: { marginBottom: "10px" } },
                          el("img", {
                            src: review.profile_photo_url,
                            alt: review.author_name,
                            style: {
                              width: "100px",
                              height: "100px",
                              objectFit: "cover",
                              borderRadius: "8px",
                              display: "block",
                              marginBottom: "5px",
                            },
                          }),
                          el(
                            Button,
                            {
                              isDestructive: true,
                              isSmall: true,
                              onClick: function () {
                                removeImage(index);
                              },
                            },
                            __("Eliminar imagen")
                          )
                        )
                    )
                  ),

                  el(TextControl, {
                    label: __("Nombre del cliente"),
                    value: review.author_name,
                    onChange: function (value) {
                      updateReviewItem(index, "author_name", value);
                    },
                  }),
                  el(RangeControl, {
                    label: __("Calificación"),
                    value: review.rating,
                    min: 1,
                    max: 5,
                    onChange: function (value) {
                      updateReviewItem(index, "rating", value);
                    },
                  }),
                  el(TextareaControl, {
                    label: __("Texto del testimonio"),
                    value: review.text,
                    onChange: function (value) {
                      updateReviewItem(index, "text", value);
                    },
                  }),
                  el(TextControl, {
                    label: __("Fecha (opcional)"),
                    help: __(
                      "Ejemplo: '2 semanas atrás', 'Recientemente', etc."
                    ),
                    value: review.relative_time_description,
                    onChange: function (value) {
                      updateReviewItem(
                        index,
                        "relative_time_description",
                        value
                      );
                    },
                  })
                );
              }),
              el(
                Button,
                {
                  isPrimary: true,
                  onClick: addReviewItem,
                  style: {
                    marginTop: "15px",
                  },
                },
                __("Añadir nuevo testimonio")
              )
            ),

          el(
            PanelBody,
            { title: __("Opciones de visualización"), initialOpen: false },
            el(ToggleControl, {
              label: __("Mostrar nombre del cliente"),
              checked: attributes.displayName,
              onChange: function (displayName) {
                setAttributes({ displayName });
              },
            }),
            el(ToggleControl, {
              label: __("Mostrar avatar"),
              checked: attributes.displayAvatar,
              onChange: function (displayAvatar) {
                setAttributes({ displayAvatar });
              },
            }),
            el(ToggleControl, {
              label: __("Mostrar calificación"),
              checked: attributes.displayRating,
              onChange: function (displayRating) {
                setAttributes({ displayRating });
              },
            }),
            el(ToggleControl, {
              label: __("Mostrar fecha"),
              checked: attributes.displayDate,
              onChange: function (displayDate) {
                setAttributes({ displayDate });
              },
            }),
            el(ToggleControl, {
              label: __("Mostrar rol del cliente"),
              checked: attributes.displayRole,
              onChange: function (displayRole) {
                setAttributes({ displayRole });
              },
            }),
            el(ToggleControl, {
              label: __("Reproducción automática"),
              checked: attributes.autoplay,
              onChange: function (autoplay) {
                setAttributes({ autoplay });
              },
            }),
            attributes.autoplay &&
              el(RangeControl, {
                label: __("Velocidad de reproducción (ms)"),
                value: attributes.autoplaySpeed,
                min: 2000,
                max: 10000,
                step: 500,
                onChange: function (autoplaySpeed) {
                  setAttributes({ autoplaySpeed });
                },
              }),
            el(SelectControl, {
              label: __("Tipo de transición"),
              value: attributes.carouselType,
              options: [
                { label: __("Deslizar"), value: "slide" },
                { label: __("Desvanecer"), value: "fade" },
              ],
              onChange: function (carouselType) {
                setAttributes({ carouselType });
              },
            })
          ),

          el(
            PanelBody,
            { title: __("Colores"), initialOpen: false },
            el(
              "p",
              { className: "components-base-control__label" },
              __("Color de Fondo de la Sección")
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
              __("Color de Fondo de las Tarjetas")
            ),
            el(ColorPalette, {
              colors: colors,
              value: attributes.cardBackgroundColor,
              onChange: function (cardBackgroundColor) {
                setAttributes({ cardBackgroundColor });
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

        // Vista previa en el editor
        el(
          "div",
          {
            className:
              props.className + " wp-block-wptbt-google-reviews-block-editor",
          },
          el(
            "div",
            {
              className: "wptbt-google-reviews-block-editor",
              style: {
                backgroundColor: attributes.backgroundColor,
                color: attributes.textColor,
                padding: "30px",
                borderRadius: "8px",
                textAlign: "center",
              },
            },
            // Icono superior
            el(
              "div",
              {
                style: {
                  display: "flex",
                  justifyContent: "center",
                  marginBottom: "10px",
                },
              },
              el(
                "svg",
                {
                  width: "24",
                  height: "24",
                  viewBox: "0 0 24 24",
                  fill: "none",
                  style: { opacity: "0.7" },
                },
                el("path", {
                  d: "M12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2Z",
                  stroke: attributes.accentColor,
                  strokeWidth: "1.5",
                }),
                el("path", {
                  d: "M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2",
                  stroke: attributes.accentColor,
                  strokeWidth: "1.5",
                  strokeLinecap: "round",
                })
              )
            ),

            // Subtítulo
            el(
              "span",
              {
                style: {
                  color: attributes.accentColor,
                  fontStyle: "italic",
                  display: "block",
                  marginBottom: "10px",
                  fontFamily: "serif",
                  fontSize: "16px",
                },
              },
              attributes.subtitle
            ),

            // Título
            el(
              "h2",
              {
                style: {
                  fontSize: "28px",
                  marginBottom: "20px",
                  fontFamily: "serif",
                },
              },
              attributes.title
            ),

            // Vista previa de testimonios
            el(
              "div",
              { className: "testimonials-preview" },
              attributes.staticReviews.length > 0
                ? [
                    // Primeros dos testimonios en tarjetas
                    el(
                      "div",
                      {
                        style: {
                          display: "grid",
                          gridTemplateColumns:
                            "repeat(auto-fit, minmax(300px, 1fr))",
                          gap: "20px",
                          marginTop: "20px",
                        },
                      },
                      attributes.staticReviews
                        .slice(0, 2)
                        .map(function (review, index) {
                          return el(
                            "div",
                            {
                              key: index,
                              style: {
                                backgroundColor: attributes.cardBackgroundColor,
                                padding: "20px",
                                borderRadius: "8px",
                                boxShadow: "0 2px 8px rgba(0,0,0,0.05)",
                                textAlign: "left",
                                display: "flex",
                                flexDirection: "column",
                              },
                            },
                            // Calificación
                            attributes.displayRating &&
                              el(
                                "div",
                                {
                                  style: {
                                    display: "flex",
                                    marginBottom: "10px",
                                  },
                                },
                                Array(5)
                                  .fill()
                                  .map(function (_, i) {
                                    return el(
                                      "span",
                                      {
                                        key: i,
                                        style: {
                                          color:
                                            i < review.rating
                                              ? attributes.accentColor
                                              : "#E0E0E0",
                                          marginRight: "3px",
                                        },
                                      },
                                      "★"
                                    );
                                  })
                              ),

                            // Texto
                            el(
                              "p",
                              {
                                style: {
                                  marginBottom: "15px",
                                  flexGrow: 1,
                                },
                              },
                              review.text.length > 100
                                ? review.text.substring(0, 100) + "..."
                                : review.text
                            ),

                            // Sección del autor
                            el(
                              "div",
                              {
                                style: {
                                  display: "flex",
                                  alignItems: "center",
                                  marginTop: "auto",
                                },
                              },
                              attributes.displayAvatar &&
                                (review.profile_photo_url
                                  ? el("img", {
                                      src: review.profile_photo_url,
                                      alt: review.author_name,
                                      style: {
                                        width: "40px",
                                        height: "40px",
                                        borderRadius: "50%",
                                        marginRight: "10px",
                                        objectFit: "cover",
                                      },
                                    })
                                  : el(
                                      "div",
                                      {
                                        style: {
                                          width: "40px",
                                          height: "40px",
                                          borderRadius: "50%",
                                          backgroundColor: "#f1f1f1",
                                          color: attributes.accentColor,
                                          display: "flex",
                                          alignItems: "center",
                                          justifyContent: "center",
                                          fontWeight: "bold",
                                          marginRight: "10px",
                                        },
                                      },
                                      review.author_name.charAt(0)
                                    )),
                              el(
                                "div",
                                {},
                                attributes.displayName &&
                                  el(
                                    "div",
                                    {
                                      style: {
                                        fontWeight: "500",
                                        color: attributes.accentColor,
                                      },
                                    },
                                    review.author_name
                                  ),
                                attributes.displayRole &&
                                  el(
                                    "div",
                                    {
                                      style: {
                                        fontSize: "12px",
                                        color: "#666",
                                      },
                                    },
                                    attributes.clientRole
                                  )
                              )
                            )
                          );
                        })
                    ),

                    // Indicador de más testimonios
                    attributes.staticReviews.length > 2 &&
                      el(
                        "div",
                        {
                          style: {
                            marginTop: "15px",
                            fontSize: "13px",
                            color: "#666",
                            fontStyle: "italic",
                          },
                        },
                        "+" +
                          (attributes.staticReviews.length - 2) +
                          " más testimonios..."
                      ),
                  ]
                : el(
                    "div",
                    {
                      style: {
                        padding: "20px",
                        backgroundColor: "#f5f5f5",
                        borderRadius: "4px",
                      },
                    },
                    __("Configura tus testimonios en el panel lateral")
                  )
            ),

            // Nota informativa
            el(
              "p",
              {
                style: {
                  marginTop: "20px",
                  fontSize: "13px",
                  opacity: "0.7",
                  maxWidth: "500px",
                  margin: "20px auto 0",
                },
              },
              __(
                "Este bloque muestra testimonios en un carrusel. Configura todos los detalles en el panel lateral."
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
