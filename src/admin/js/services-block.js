/**
 * Script para el bloque de tours con controles en el sidebar
 */
(function () {
  var registerBlockType = wp.blocks.registerBlockType;
  var createElement = wp.element.createElement;
  var __ = wp.i18n.__;

  // Componentes para el panel lateral
  var InspectorControls = wp.blockEditor
    ? wp.blockEditor.InspectorControls
    : wp.editor.InspectorControls;
  var PanelBody = wp.components.PanelBody;
  var TextControl = wp.components.TextControl;
  var SelectControl = wp.components.SelectControl;
  var RangeControl = wp.components.RangeControl;
  var ToggleControl = wp.components.ToggleControl;
  var ColorPalette = wp.components.ColorPalette;

  // Colores predefinidos para la paleta de viajes
  var colors = [
    { name: "Rojo Viaje", color: "#DC2626" },
    { name: "Rojo Claro", color: "#EF4444" },
    { name: "Naranja Aventura", color: "#F59E0B" },
    { name: "Azul Océano", color: "#2563EB" },
    { name: "Verde Naturaleza", color: "#059669" },
    { name: "Blanco", color: "#FFFFFF" },
    { name: "Gris Claro", color: "#F3F4F6" },
    { name: "Gris Oscuro", color: "#374151" },
    { name: "Negro", color: "#111827" },
  ];

  registerBlockType("wptbt/tours-block", {
    title: __("Tours y Destinos"),
    icon: "clipboard",
    category: "widgets",

    // Mantener exactamente los mismos atributos que están en el PHP
    attributes: {
      title: {
        type: "string",
        default: "Descubre Nuestros Destinos",
      },
      subtitle: {
        type: "string",
        default: "TOURS & DESTINOS",
      },
      layout: {
        type: "string",
        default: "grid",
      },
      columns: {
        type: "number",
        default: 2,
      },
      showImage: {
        type: "boolean",
        default: false,
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
        default: "#DC2626",
      },
      categoryId: {
        type: "string",
        default: "",
      },
      postsPerPage: {
        type: "number",
        default: -1,
      },
    },

    // Vista en el editor con controles laterales
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;

      return [
        // Panel de controles laterales
        createElement(
          InspectorControls,
          { key: "inspector" },
          // Panel de texto
          createElement(
            PanelBody,
            {
              title: __("Textos"),
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
            })
          ),

          // Panel de diseño
          createElement(
            PanelBody,
            {
              title: __("Diseño"),
              initialOpen: false,
            },
            createElement(SelectControl, {
              label: __("Tipo de Layout"),
              value: attributes.layout,
              options: [
                { label: __("Cuadrícula"), value: "grid" },
                { label: __("Lista"), value: "list" },
              ],
              onChange: function (value) {
                setAttributes({ layout: value });
              },
            }),
            attributes.layout === "grid" &&
              createElement(RangeControl, {
                label: __("Número de Columnas"),
                value: attributes.columns,
                min: 1,
                max: 4,
                onChange: function (value) {
                  setAttributes({ columns: value });
                },
              }),
            createElement(ToggleControl, {
              label: __("Mostrar Imágenes"),
              checked: attributes.showImage,
              onChange: function (value) {
                setAttributes({ showImage: value });
              },
            })
          ),

          // Panel de filtrado
          createElement(
            PanelBody,
            {
              title: __("Filtros"),
              initialOpen: false,
            },
            createElement(TextControl, {
              label: __("ID de Categoría"),
              help: __(
                "(Opcional) Introduce el ID de categoría para filtrar tours"
              ),
              value: attributes.categoryId,
              onChange: function (value) {
                setAttributes({ categoryId: value });
              },
            }),
            createElement(RangeControl, {
              label: __("Número de Tours"),
              help: __("-1 para mostrar todos"),
              value: attributes.postsPerPage,
              min: -1,
              max: 20,
              onChange: function (value) {
                setAttributes({ postsPerPage: value });
              },
            })
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
            className: "wp-block-wptbt-services-block-preview",
            style: {
              padding: "30px",
              textAlign: "center",
              backgroundColor: attributes.backgroundColor || "#f9f9f9",
              color: attributes.textColor || "#424242",
              border: "1px dashed #d9adb7",
              borderRadius: "8px",
            },
          },
          // Icono
          createElement(
            "div",
            { style: { marginBottom: "15px" } },
            createElement("span", {
              className: "dashicons dashicons-clipboard",
              style: {
                fontSize: "40px",
                width: "40px",
                height: "40px",
                color: "#D9ADB7",
              },
            })
          ),
          // Título y subtítulo
          createElement(
            "h3",
            {
              style: {
                marginBottom: "5px",
                color: attributes.accentColor || "#D4B254",
              },
            },
            attributes.subtitle
          ),
          createElement(
            "h2",
            {
              style: {
                fontSize: "24px",
                marginTop: "0",
                color: attributes.textColor || "#424242",
              },
            },
            attributes.title
          ),
          // Línea decorativa
          createElement("div", {
            style: {
              width: "60px",
              height: "2px",
              backgroundColor: attributes.accentColor || "#D4B254",
              margin: "10px auto 20px",
            },
          }),
          // Visualización de servicios
          createElement(
            "div",
            { style: { marginBottom: "20px" } },
            createElement(
              "div",
              {
                style: {
                  display: "grid",
                  gridTemplateColumns:
                    attributes.layout === "grid"
                      ? "repeat(" + (attributes.columns || 2) + ", 1fr)"
                      : "1fr",
                  gap: "15px",
                  maxWidth: "600px",
                  margin: "0 auto",
                  textAlign: "left",
                },
              },
              // Servicio 1
              createElement(
                "div",
                {
                  style: {
                    backgroundColor: "white",
                    padding: "15px",
                    borderRadius: "5px",
                    boxShadow: "0 2px 4px rgba(0,0,0,0.05)",
                  },
                },
                attributes.showImage &&
                  createElement(
                    "div",
                    {
                      style: {
                        backgroundColor: "#f0f0f0",
                        height: "100px",
                        marginBottom: "10px",
                        display: "flex",
                        alignItems: "center",
                        justifyContent: "center",
                        color: "#999",
                        fontSize: "12px",
                      },
                    },
                    "Imagen del tour"
                  ),
                createElement(
                  "h4",
                  { style: { margin: "0 0 10px 0" } },
                  "Tour a Machu Picchu"
                ),
                createElement(
                  "div",
                  {
                    style: {
                      display: "flex",
                      justifyContent: "space-between",
                      alignItems: "center",
                    },
                  },
                  createElement(
                    "span",
                    { style: { color: "#DC2626", fontSize: "13px" } },
                    "3 DÍAS"
                  ),
                  createElement(
                    "span",
                    {
                      style: {
                        color: attributes.accentColor || "#DC2626",
                        fontWeight: "bold",
                      },
                    },
                    "$299"
                  )
                )
              ),
              // Tour 2 (solo si estamos en grid)
              attributes.layout === "grid" &&
                createElement(
                  "div",
                  {
                    style: {
                      backgroundColor: "white",
                      padding: "15px",
                      borderRadius: "5px",
                      boxShadow: "0 2px 4px rgba(0,0,0,0.05)",
                    },
                  },
                  attributes.showImage &&
                    createElement(
                      "div",
                      {
                        style: {
                          backgroundColor: "#f0f0f0",
                          height: "100px",
                          marginBottom: "10px",
                          display: "flex",
                          alignItems: "center",
                          justifyContent: "center",
                          color: "#999",
                          fontSize: "12px",
                        },
                      },
                      "Imagen del tour"
                    ),
                  createElement(
                    "h4",
                    { style: { margin: "0 0 10px 0" } },
                    "Amazonas Explorer"
                  ),
                  createElement(
                    "div",
                    {
                      style: {
                        display: "flex",
                        justifyContent: "space-between",
                        alignItems: "center",
                      },
                    },
                    createElement(
                      "span",
                      { style: { color: "#DC2626", fontSize: "13px" } },
                      "5 DÍAS"
                    ),
                    createElement(
                      "span",
                      {
                        style: {
                          color: attributes.accentColor || "#DC2626",
                          fontWeight: "bold",
                        },
                      },
                      "$449"
                    )
                  )
                ),
              // Vista adicional para diseño de lista
              attributes.layout !== "grid" &&
                createElement(
                  "div",
                  {
                    style: {
                      backgroundColor: "white",
                      padding: "15px",
                      borderRadius: "5px",
                      boxShadow: "0 2px 4px rgba(0,0,0,0.05)",
                      display: "flex",
                      alignItems: "center",
                      marginTop: "15px",
                    },
                  },
                  attributes.showImage &&
                    createElement(
                      "div",
                      {
                        style: {
                          backgroundColor: "#f0f0f0",
                          width: "70px",
                          height: "70px",
                          marginRight: "15px",
                          display: "flex",
                          alignItems: "center",
                          justifyContent: "center",
                          color: "#999",
                          fontSize: "10px",
                        },
                      },
                      "Imagen"
                    ),
                  createElement(
                    "div",
                    { style: { flex: "1" } },
                    createElement(
                      "h4",
                      { style: { margin: "0 0 5px 0" } },
                      "Cusco Cultural"
                    )
                  ),
                  createElement(
                    "div",
                    {
                      style: {
                        display: "flex",
                        flexDirection: "column",
                        alignItems: "flex-end",
                      },
                    },
                    createElement(
                      "span",
                      { style: { color: "#DC2626", fontSize: "12px" } },
                      "7 DÍAS"
                    ),
                    createElement(
                      "span",
                      {
                        style: {
                          color: attributes.accentColor || "#DC2626",
                          fontWeight: "bold",
                        },
                      },
                      "$599"
                    )
                  )
                )
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
                marginBottom: "0",
              },
            },
            "Este bloque muestra automáticamente los tours con sus precios."
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
