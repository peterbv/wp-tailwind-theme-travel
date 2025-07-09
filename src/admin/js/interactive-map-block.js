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
  
    blocks.registerBlockType("wptbt/interactive-map-block", {
      title: __("Mapa Interactivo"),
      icon: "location-alt",
      category: "widgets",
      attributes: {
        title: { type: "string", default: "FIND US" },
        subtitle: { type: "string", default: "Our Location" },
        description: { type: "string", default: "Visit us and discover our relaxing spa in the heart of the city" },
        latitude: { type: "number", default: -13.518333 }, // Cusco, Perú
        longitude: { type: "number", default: -71.978056 }, // Cusco, Perú
        zoom: { type: "number", default: 15 },
        markerTitle: { type: "string", default: "Mystical Terra Spa" },
        markerDescription: { type: "string", default: "Your wellness sanctuary" },
        mapHeight: { type: "string", default: "500px" },
        showDirections: { type: "boolean", default: true },
        showStreetview: { type: "boolean", default: true },
        backgroundColor: { type: "string", default: "#F9F5F2" },
        textColor: { type: "string", default: "#5D534F" },
        accentColor: { type: "string", default: "#D4B254" },
        secondaryColor: { type: "string", default: "#8BAB8D" },
        mapStyle: { type: "string", default: "default" },
        mapProvider: { type: "string", default: "osm" }, // "osm" o "google"
        apiKey: { type: "string", default: "" },
        pointsOfInterest: { 
          type: "array", 
          default: [
            {
              title: "Plaza de Armas",
              description: "Main square of Cusco",
              latitude: -13.516599,
              longitude: -71.978775,
              image: ""
            },
            {
              title: "Qorikancha",
              description: "The Inca's Sun Temple",
              latitude: -13.520791,
              longitude: -71.975437,
              image: ""
            }
          ]
        },
        address: { type: "string", default: "Calle Plateros 334, Cusco 08001, Perú" },
        phone: { type: "string", default: "+51 84 123456" },
        email: { type: "string", default: "info@mysticalterra.com" },
        bookingUrl: { type: "string", default: "#booking" },
        enableFullscreen: { type: "boolean", default: true },
        enableZoomControls: { type: "boolean", default: true },
        enableClustering: { type: "boolean", default: true }
      },
  
      edit: function (props) {
        var attributes = props.attributes;
        var setAttributes = props.setAttributes;
  
        // Función para actualizar puntos de interés
        function updatePoiItem(index, key, value) {
          var newPois = JSON.parse(JSON.stringify(attributes.pointsOfInterest));
          newPois[index][key] = value;
          setAttributes({ pointsOfInterest: newPois });
        }
  
        // Función para añadir nuevo punto de interés
        function addPoiItem() {
          var newPois = JSON.parse(JSON.stringify(attributes.pointsOfInterest));
          newPois.push({
            title: "New Point of Interest",
            description: "Description of this place",
            latitude: attributes.latitude,
            longitude: attributes.longitude,
            image: ""
          });
          setAttributes({ pointsOfInterest: newPois });
        }
  
        // Función para eliminar punto de interés
        function removePoiItem(index) {
          var newPois = JSON.parse(JSON.stringify(attributes.pointsOfInterest));
          newPois.splice(index, 1);
          setAttributes({ pointsOfInterest: newPois });
        }
  
        // Función para manejar la selección de imagen
        function onSelectImage(index, media) {
          var newPois = JSON.parse(JSON.stringify(attributes.pointsOfInterest));
  
          if (media && media.url) {
            newPois[index].image = media.url;
          }
  
          setAttributes({ pointsOfInterest: newPois });
        }
  
        // Función para eliminar imagen
        function removeImage(index) {
          var newPois = JSON.parse(JSON.stringify(attributes.pointsOfInterest));
          newPois[index].image = "";
          setAttributes({ pointsOfInterest: newPois });
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
              el(TextareaControl, {
                label: __("Descripción (opcional)"),
                value: attributes.description,
                onChange: function (description) {
                  setAttributes({ description });
                },
              }),
              el(TextControl, {
                label: __("Altura del mapa"),
                help: __("Ejemplo: 500px, 60vh, etc."),
                value: attributes.mapHeight,
                onChange: function (mapHeight) {
                  setAttributes({ mapHeight });
                },
              })
            ),
  
            el(
              PanelBody,
              { title: __("Ubicación y Marcador"), initialOpen: false },
              el(TextControl, {
                label: __("Latitud"),
                type: "number",
                step: "0.000001",
                value: attributes.latitude,
                onChange: function (latitude) {
                  setAttributes({ latitude: parseFloat(latitude) });
                },
              }),
              el(TextControl, {
                label: __("Longitud"),
                type: "number",
                step: "0.000001",
                value: attributes.longitude,
                onChange: function (longitude) {
                  setAttributes({ longitude: parseFloat(longitude) });
                },
              }),
              el(RangeControl, {
                label: __("Nivel de zoom"),
                value: attributes.zoom,
                min: 1,
                max: 20,
                onChange: function (zoom) {
                  setAttributes({ zoom });
                },
              }),
              el(TextControl, {
                label: __("Título del marcador"),
                value: attributes.markerTitle,
                onChange: function (markerTitle) {
                  setAttributes({ markerTitle });
                },
              }),
              el(TextareaControl, {
                label: __("Descripción del marcador"),
                value: attributes.markerDescription,
                onChange: function (markerDescription) {
                  setAttributes({ markerDescription });
                },
              })
            ),
  
            el(
              PanelBody,
              { title: __("Opciones del Mapa"), initialOpen: false },
              el(
                "div",
                { style: { marginBottom: "24px" } },
                el(
                  "label",
                  { className: "components-base-control__label" },
                  __("Proveedor de mapas")
                ),
                el(
                  SelectControl,
                  {
                    value: attributes.mapProvider,
                    options: [
                      { label: __("OpenStreetMap (gratuito)"), value: "osm" },
                      { label: __("Google Maps (requiere API key)"), value: "google" },
                    ],
                    onChange: function (mapProvider) {
                      setAttributes({ mapProvider });
                    },
                  }
                ),
                attributes.mapProvider === "google" && !attributes.apiKey && el(
                  "p",
                  { className: "components-base-control__help", style: { color: "#d63638" } },
                  __("Es necesario proporcionar una API key de Google Maps para usar este proveedor.")
                ),
                attributes.mapProvider === "osm" && el(
                  "p",
                  { className: "components-base-control__help" },
                  __("OpenStreetMap es gratuito y no requiere API key, pero tiene funcionalidades limitadas.")
                )
              ),
  
              attributes.mapProvider === "google" && el(TextControl, {
                label: __("API Key de Google Maps"),
                help: __("Opcional. Ingresa tu API key de Google Maps para usar el mapa en producción."),
                value: attributes.apiKey,
                onChange: function (apiKey) {
                  setAttributes({ apiKey });
                },
              }),
              
              // Selector de estilo según el proveedor
              attributes.mapProvider === "google" ? el(
                "div",
                { style: { marginBottom: "24px" } },
                el(
                  "label",
                  { className: "components-base-control__label" },
                  __("Estilo del mapa")
                ),
                el(
                  SelectControl,
                  {
                    value: attributes.mapStyle,
                    options: [
                      { label: __("Estándar"), value: "default" },
                      { label: __("Plateado"), value: "silver" },
                      { label: __("Retro"), value: "retro" },
                      { label: __("Oscuro"), value: "dark" },
                      { label: __("Noche"), value: "night" },
                      { label: __("Berenjena"), value: "aubergine" },
                    ],
                    onChange: function (mapStyle) {
                      setAttributes({ mapStyle });
                    },
                  }
                ),
                // Vista previa del estilo
                attributes.mapStyle !== "default" && el(
                  "div",
                  { className: "map-style-preview", style: { marginTop: "8px" } },
                  el(
                    "img",
                    {
                      src: `https://developers.google.com/maps/documentation/javascript/examples/full/images/map-${attributes.mapStyle}.png`,
                      alt: __("Vista previa del estilo"),
                      style: {
                        width: "100%",
                        height: "auto",
                        borderRadius: "4px",
                        border: "1px solid #ddd",
                      },
                    }
                  )
                )
              ) : el(
                "div",
                { style: { marginBottom: "24px" } },
                el(
                  "label",
                  { className: "components-base-control__label" },
                  __("Estilo del mapa")
                ),
                el(
                  SelectControl,
                  {
                    value: attributes.mapStyle,
                    options: [
                      { label: __("Estándar"), value: "default" },
                      { label: __("Claro"), value: "light" },
                      { label: __("Oscuro"), value: "dark" },
                      { label: __("Satélite"), value: "satellite" },
                      { label: __("Terreno"), value: "terrain" },
                    ],
                    onChange: function (mapStyle) {
                      setAttributes({ mapStyle });
                    },
                  }
                )
              ),
  
              el(ToggleControl, {
                label: __("Mostrar controles de zoom"),
                checked: attributes.enableZoomControls,
                onChange: function (enableZoomControls) {
                  setAttributes({ enableZoomControls });
                },
              }),
              el(ToggleControl, {
                label: __("Habilitar pantalla completa"),
                checked: attributes.enableFullscreen,
                onChange: function (enableFullscreen) {
                  setAttributes({ enableFullscreen });
                },
              }),
              el(ToggleControl, {
                label: __("Habilitar agrupación de marcadores"),
                help: __("Agrupa marcadores cercanos cuando hay muchos puntos de interés"),
                checked: attributes.enableClustering,
                onChange: function (enableClustering) {
                  setAttributes({ enableClustering });
                },
              }),
              el(ToggleControl, {
                label: __("Mostrar direcciones"),
                checked: attributes.showDirections,
                onChange: function (showDirections) {
                  setAttributes({ showDirections });
                },
              }),
              el(ToggleControl, {
                label: __("Mostrar Street View"),
                help: attributes.mapProvider === "osm" ? 
                  __("Con OpenStreetMap se usará Mapillary como alternativa") : 
                  __("Mostrar vista a nivel de calle con Google Street View"),
                checked: attributes.showStreetview,
                onChange: function (showStreetview) {
                  setAttributes({ showStreetview });
                },
              })
            ),
  
            el(
              PanelBody,
              { title: __("Puntos de Interés"), initialOpen: false },
              attributes.pointsOfInterest.map(function (poi, index) {
                return el(
                  "div",
                  {
                    key: index,
                    className: "poi-item",
                  },
                  el(
                    "div",
                    { className: "poi-item-header" },
                    el("strong", {}, __("Punto") + " " + (index + 1)),
                    el(
                      Button,
                      {
                        isDestructive: true,
                        isSmall: true,
                        onClick: function () {
                          removePoiItem(index);
                        },
                        className: "poi-remove-button",
                      },
                      __("Eliminar")
                    )
                  ),
                  el(TextControl, {
                    label: __("Título"),
                    value: poi.title,
                    onChange: function (value) {
                      updatePoiItem(index, "title", value);
                    },
                  }),
                  el(TextareaControl, {
                    label: __("Descripción"),
                    value: poi.description,
                    onChange: function (value) {
                      updatePoiItem(index, "description", value);
                    },
                  }),
                  el(TextControl, {
                    label: __("Latitud"),
                    type: "number",
                    step: "0.000001",
                    value: poi.latitude,
                    onChange: function (value) {
                      updatePoiItem(index, "latitude", parseFloat(value));
                    },
                  }),
                  el(TextControl, {
                    label: __("Longitud"),
                    type: "number",
                    step: "0.000001",
                    value: poi.longitude,
                    onChange: function (value) {
                      updatePoiItem(index, "longitude", parseFloat(value));
                    },
                  }),
                  el(
                    "div",
                    { className: "poi-image-container" },
                    el(
                      "label",
                      { className: "components-base-control__label" },
                      __("Imagen (opcional)")
                    ),
                    poi.image && el("img", { 
                      src: poi.image, 
                      className: "poi-image-preview" 
                    }),
                    el(
                      "div",
                      { className: "poi-image-actions", style: { marginTop: "10px" } },
                      el(
                        MediaUploadCheck,
                        {},
                        el(
                          MediaUpload,
                          {
                            onSelect: function(media) {
                              onSelectImage(index, media);
                            },
                            allowedTypes: ["image"],
                            value: poi.image,
                            render: function(obj) {
                              return el(
                                Button,
                                {
                                  isPrimary: true,
                                  isSmall: true,
                                  onClick: obj.open,
                                  style: { marginRight: "8px" }
                                },
                                poi.image ? __("Cambiar imagen") : __("Añadir imagen")
                              );
                            }
                          }
                        )
                      ),
                      poi.image && el(
                        Button,
                        {
                          isDestructive: true,
                          isSmall: true,
                          onClick: function() {
                            removeImage(index);
                          }
                        },
                        __("Eliminar imagen")
                      )
                    )
                  )
                );
              }),
              el(
                Button,
                {
                  isPrimary: true,
                  onClick: addPoiItem,
                  className: "poi-add-button",
                },
                __("Añadir punto de interés")
              )
            ),
  
            el(
              PanelBody,
              { title: __("Información de Contacto"), initialOpen: false },
              el(TextControl, {
                label: __("Dirección"),
                value: attributes.address,
                onChange: function (address) {
                  setAttributes({ address });
                },
              }),
              el(TextControl, {
                label: __("Teléfono"),
                value: attributes.phone,
                onChange: function (phone) {
                  setAttributes({ phone });
                },
              }),
              el(TextControl, {
                label: __("Correo electrónico"),
                value: attributes.email,
                onChange: function (email) {
                  setAttributes({ email });
                },
              }),
              el(TextControl, {
                label: __("URL de Reserva"),
                value: attributes.bookingUrl,
                onChange: function (bookingUrl) {
                  setAttributes({ bookingUrl });
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
                  setAttributes({ secondaryColor });
                },
              })
            )
          ),
  
          // Vista previa en el editor
          el(
            "div",
            {
              className:
                props.className + " wp-block-wptbt-interactive-map-block",
              style: {
                backgroundColor: attributes.backgroundColor,
                color: attributes.textColor,
              },
            },
            el(
              "div",
              { className: "map-header text-center" },
              el(
                "span",
                {
                  className: "text-lg italic font-medium mb-2",
                  style: { color: attributes.accentColor },
                },
                attributes.subtitle
              ),
              el(
                "h2",
                {
                  className: "text-3xl fancy-text font-medium mb-4",
                },
                attributes.title
              ),
              attributes.description && el(
                "p",
                { className: "max-w-2xl mx-auto opacity-80" },
                attributes.description
              )
            ),
            el(
              "div",
              { className: "wptbt-map-preview" },
              el(
                "div",
                { className: "map-preview-container", style: { height: attributes.mapHeight } },
                el(
                  "div",
                  { className: "map-preview-mock" },
                  el(
                    "div",
                    { className: "map-marker-icon" },
                    el(
                      "svg",
                      { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24" },
                      el("path", { d: "M12 0c-4.198 0-8 3.403-8 7.602 0 4.198 3.469 9.21 8 16.398 4.531-7.188 8-12.2 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z" })
                    )
                  ),
                  el(
                    "div",
                    { className: "map-preview-text" },
                    __("Vista previa del mapa (se mostrará el mapa real en el frontend)")
                  )
                )
              ),
              el(
                "div",
                { className: "flex justify-between items-center mt-4 text-sm text-gray-600 italic" },
                el("span", {}, __("Latitud") + ": " + attributes.latitude),
                el("span", {}, __("Longitud") + ": " + attributes.longitude),
                el("span", {}, __("Zoom") + ": " + attributes.zoom)
              ),
              // Indicador de proveedor
              el(
                "div", 
                { className: "text-center mt-2 text-sm italic" },
                el("span", { 
                  style: { 
                    padding: "2px 8px", 
                    borderRadius: "10px",
                    backgroundColor: attributes.mapProvider === "osm" ? "#dff0d8" : "#d9edf7",
                    color: attributes.mapProvider === "osm" ? "#3c763d" : "#31708f"
                  }
                }, 
                attributes.mapProvider === "osm" ? 
                  __("Proveedor: OpenStreetMap (gratuito)") : 
                  __("Proveedor: Google Maps")
                )
              ),
              attributes.pointsOfInterest.length > 0 && el(
                "div",
                { className: "mt-4 p-4 bg-gray-50 rounded-md" },
                el(
                  "h3",
                  { className: "text-sm font-medium mb-2" },
                  __("Puntos de interés configurados: ") + attributes.pointsOfInterest.length
                ),
                el(
                  "ul",
                  { className: "text-xs text-gray-600 list-disc pl-4" },
                  attributes.pointsOfInterest.slice(0, 3).map(function(poi, index) {
                    return el(
                      "li",
                      { key: index, className: "mb-1" },
                      poi.title
                    );
                  }),
                  attributes.pointsOfInterest.length > 3 && el(
                    "li",
                    { className: "italic" },
                    "... " + (attributes.pointsOfInterest.length - 3) + " " + __("más")
                  )
                )
              )
            ),
            el(
              "div",
              { className: "mt-4 text-center text-sm text-gray-500 italic" },
              __("Configura todos los detalles del mapa en el panel lateral.")
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