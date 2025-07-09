// src/public/js/components/solid/SolidInteractiveMapSEO.jsx
import {
    createSignal,
    createEffect,
    onMount,
    onCleanup,
    For,
    Show,
    createMemo,
  } from "solid-js";
  import { __ } from "../../solid-core";
  
  /**
   * Componente de Mapa Interactivo con mejoras SEO
   * Mantiene toda la funcionalidad original + optimizaciones SEO
   *
   * @param {Object} props Propiedades del componente
   * @returns {JSX.Element} Componente Solid
   */
  const SolidInteractiveMapSEO = (props) => {
    // Estados para el control de carga
    const [isScriptLoading, setIsScriptLoading] = createSignal(false);
    const [scriptLoadError, setScriptLoadError] = createSignal(null);
    const [mapLibrary, setMapLibrary] = createSignal(null);
  
    // Propiedades con valores por defecto - usando createMemo para derivar valores
    const config = createMemo(() => ({
      title: props.title || __("Find Us", "wp-tailwind-blocks"),
      subtitle: props.subtitle || __("Our Location", "wp-tailwind-blocks"),
      description: props.description ||
        __("Visit us to experience our services in person", "wp-tailwind-blocks"),
      mainLocation: {
        lat: props.mainLocation?.lat || -13.53168,
        lng: props.mainLocation?.lng || -71.96741,
        title: props.mainLocation?.title || "Mystical Terra Spa",
        address: props.mainLocation?.address || "Calle Principal 123, Cusco, Perú",
        description: props.mainLocation?.description || "Our main spa location",
        // Nuevas propiedades SEO (opcionales)
        postalCode: props.mainLocation?.postalCode || "08000",
        city: props.mainLocation?.city || "Cusco",
        region: props.mainLocation?.region || "Cusco",
        country: props.mainLocation?.country || "Peru",
        phone: props.mainLocation?.phone || "+51 84 123456",
        email: props.mainLocation?.email || "info@mysticalterraspa.com",
        website: props.mainLocation?.website || "https://mysticalterraspa.com",
        openingHours: props.mainLocation?.openingHours || ["Mo-Su 09:00-20:00"],
      },
      pointsOfInterest: Array.isArray(props.pointsOfInterest) ? props.pointsOfInterest : [],
      zoom: props.zoom || 14,
      mapHeight: props.mapHeight || 500,
      accentColor: props.accentColor || "#D4B254",
      secondaryColor: props.secondaryColor || "#8BAB8D",
      backgroundColor: props.backgroundColor || "#F9F5F2",
      textColor: props.textColor || "#5D534F",
      showDirectionsLink: props.showDirectionsLink !== false,
      showPointsOfInterest: props.showPointsOfInterest !== false,
      customMapStyle: props.customMapStyle || "default",
      enableFullscreen: props.enableFullscreen !== false,
      enableZoomControls: props.enableZoomControls !== false,
      enableClustering: props.enableClustering !== false,
      mapProvider: props.mapProvider === "google" ? "google" : "osm",
      apiKey: props.apiKey || "",
      showStreetview: props.showStreetview !== false && props.mapProvider === "google",
      address: props.address || "",
      phone: props.phone || "",
      email: props.email || "",
      bookingUrl: props.bookingUrl || "#booking",
      // Nuevas propiedades SEO
      businessType: props.businessType || "Spa",
      priceRange: props.priceRange || "$$",
      businessHours: props.businessHours || [],
      socialProfiles: props.socialProfiles || [],
      reviews: props.reviews || [],
      amenities: props.amenities || [],
      languages: props.languages || ["es", "en"],
    }));
  
    // Estados originales (sin cambios)
    const [map, setMap] = createSignal(null);
    const [isLoading, setIsLoading] = createSignal(true);
    const [error, setError] = createSignal(null);
    const [activeMarker, setActiveMarker] = createSignal(null);
    const [mainMarker, setMainMarker] = createSignal(null);
    const [poiMarkers, setPoiMarkers] = createSignal([]);
    const [isMobile, setIsMobile] = createSignal(false);
    const [isFullscreen, setIsFullscreen] = createSignal(false);
    const [streetViewPanorama, setStreetViewPanorama] = createSignal(null);
    const [showingStreetView, setShowingStreetView] = createSignal(false);

    // Nuevo estado para anuncios de accesibilidad (solo para SEO)
    const [announceRegion, setAnnounceRegion] = createSignal("");
  
    // Referencia al elemento DOM del mapa (sin cambios)
    let mapContainer;
    let panoramaElement;

    // Nueva función para anuncios de pantalla (solo SEO)
    const announceToScreenReader = (message) => {
      setAnnounceRegion(message);
      setTimeout(() => setAnnounceRegion(""), 1000);
    };

    // Función para generar Schema.org JSON-LD (NUEVA - SEO)
    const generateSchemaMarkup = () => {
      const location = config().mainLocation;
      const schema = {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "@id": location.website || `#business-${location.title?.replace(/\s+/g, '-').toLowerCase()}`,
        "name": location.title,
        "description": location.description,
        "url": location.website,
        "telephone": location.phone,
        "email": location.email,
        "priceRange": config().priceRange,
        "address": {
          "@type": "PostalAddress",
          "streetAddress": location.address,
          "addressLocality": location.city,
          "addressRegion": location.region,
          "postalCode": location.postalCode,
          "addressCountry": location.country
        },
        "geo": {
          "@type": "GeoCoordinates",
          "latitude": location.lat,
          "longitude": location.lng
        },
        "hasMap": `https://maps.google.com/?q=${location.lat},${location.lng}`,
        "sameAs": config().socialProfiles,
        "amenityFeature": config().amenities.map(amenity => ({
          "@type": "LocationFeatureSpecification",
          "name": amenity
        })),
        "knowsLanguage": config().languages
      };

      // Agregar horarios si están disponibles
      if (location.openingHours && location.openingHours.length > 0) {
        schema.openingHours = location.openingHours;
      }

      // Agregar reseñas si están disponibles
      if (config().reviews.length > 0) {
        schema.review = config().reviews.map(review => ({
          "@type": "Review",
          "author": {
            "@type": "Person",
            "name": review.author
          },
          "reviewRating": {
            "@type": "Rating",
            "ratingValue": review.rating,
            "bestRating": "5"
          },
          "reviewBody": review.text
        }));

        // Calcular rating agregado
        const avgRating = config().reviews.reduce((sum, review) => sum + review.rating, 0) / config().reviews.length;
        schema.aggregateRating = {
          "@type": "AggregateRating",
          "ratingValue": avgRating.toFixed(1),
          "reviewCount": config().reviews.length,
          "bestRating": "5"
        };
      }

      return JSON.stringify(schema, null, 2);
    };

    // Función para insertar Schema markup (NUEVA - SEO)
    const insertSchemaMarkup = () => {
      try {
        // Remover schema existente si existe
        const existingSchema = document.querySelector('script[type="application/ld+json"][data-map-schema]');
        if (existingSchema) {
          existingSchema.remove();
        }

        // Crear nuevo elemento script con schema
        const script = document.createElement('script');
        script.type = 'application/ld+json';
        script.setAttribute('data-map-schema', 'true');
        script.textContent = generateSchemaMarkup();
        document.head.appendChild(script);
      } catch (error) {
        console.warn('Error inserting schema markup:', error);
      }
    };

    // Función para insertar Open Graph tags (NUEVA - SEO)
    const insertOpenGraphTags = () => {
      try {
        const location = config().mainLocation;
        const metaTags = [
          { property: 'og:type', content: 'business.business' },
          { property: 'og:title', content: `${config().title} - ${location.title}` },
          { property: 'og:description', content: config().description },
          { property: 'og:url', content: location.website || window.location.href },
          { property: 'business:contact_data:street_address', content: location.address },
          { property: 'business:contact_data:locality', content: location.city },
          { property: 'business:contact_data:region', content: location.region },
          { property: 'business:contact_data:postal_code', content: location.postalCode },
          { property: 'business:contact_data:country_name', content: location.country },
          { property: 'place:location:latitude', content: location.lat.toString() },
          { property: 'place:location:longitude', content: location.lng.toString() },
        ];

        metaTags.forEach(tag => {
          let existingTag = document.querySelector(`meta[property="${tag.property}"]`);
          if (!existingTag) {
            existingTag = document.createElement('meta');
            existingTag.setAttribute('property', tag.property);
            document.head.appendChild(existingTag);
          }
          existingTag.setAttribute('content', tag.content);
        });
      } catch (error) {
        console.warn('Error inserting Open Graph tags:', error);
      }
    };
  
    // Función para obtener traducciones específicas del componente (sin cambios)
    const getTranslation = (text, domain = "wptbt-interactive-map-block") => {
      const componentTranslations = window.wptbtI18n_interactive_map || {};
      if (componentTranslations[text]) {
        return componentTranslations[text];
      }
      return __(text, domain);
    };
  
    // TODAS LAS FUNCIONES ORIGINALES SIN CAMBIOS
    const loadMapProvider = async () => {
      setIsScriptLoading(true);
      try {
        const provider = config().mapProvider;
        
        if (provider === "google") {
          await loadGoogleMapsScript();
          setMapLibrary("google");
        } else {
          await loadLeaflet();
          setMapLibrary("leaflet");
        }
        setIsScriptLoading(false);
        return true;
      } catch (error) {
        console.error("Error cargando biblioteca de mapas:", error);
        setScriptLoadError(error.message);
        setIsScriptLoading(false);
        return false;
      }
    };

    const loadLeaflet = async () => {
      if (window.L) return Promise.resolve();

      try {
        if (!document.querySelector('link[href*="leaflet.css"]')) {
          const linkElement = document.createElement("link");
          linkElement.rel = "stylesheet";
          linkElement.href = "https://unpkg.com/leaflet@1.9.4/dist/leaflet.css";
          document.head.appendChild(linkElement);
        }

        return new Promise((resolve, reject) => {
          const scriptTimeout = setTimeout(() => {
            reject(new Error("Timeout loading Leaflet script after 10 seconds"));
          }, 10000);

          const script = document.createElement("script");
          script.src = "https://unpkg.com/leaflet@1.9.4/dist/leaflet.js";
          script.integrity = "sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=";
          script.crossOrigin = "";
          
          script.onload = () => {
            clearTimeout(scriptTimeout);
            resolve();
          };
          
          script.onerror = () => {
            clearTimeout(scriptTimeout);
            reject(new Error("Failed to load Leaflet script"));
          };
          
          document.head.appendChild(script);
        });
      } catch (error) {
        console.error("Error loading Leaflet:", error);
        throw new Error(`Error cargando Leaflet: ${error.message}`);
      }
    };

    const loadGoogleMapsScript = () => {
      return new Promise((resolve, reject) => {
        if (window.google && window.google.maps) {
          resolve();
          return;
        }

        const scriptTimeout = setTimeout(() => {
          reject(new Error("Timeout loading Google Maps API after 10 seconds"));
        }, 10000);

        window.gm_authFailure = () => {
          clearTimeout(scriptTimeout);
          reject(new Error("Google Maps API authentication failed. Check your API key."));
        };

        const script = document.createElement("script");
        const key = config().apiKey || "";
        script.src = `https://maps.googleapis.com/maps/api/js?key=${key}&libraries=places,geometry`;
        script.async = true;
        script.defer = true;

        script.onload = () => {
          clearTimeout(scriptTimeout);
          resolve();
        };

        script.onerror = () => {
          clearTimeout(scriptTimeout);
          reject(new Error("Failed to load Google Maps API"));
        };

        document.head.appendChild(script);
      });
    };

    const loadMarkerCluster = async () => {
      if (!config().enableClustering || config().mapProvider === "google") 
        return Promise.resolve();
      
      if (window.L && window.L.markerClusterGroup) 
        return Promise.resolve();

      try {
        if (!document.querySelector('link[href*="MarkerCluster.css"]')) {
          const linkElement = document.createElement("link");
          linkElement.rel = "stylesheet";
          linkElement.href =
            "https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css";
          document.head.appendChild(linkElement);
        }

        if (!document.querySelector('link[href*="MarkerCluster.Default.css"]')) {
          const linkElement2 = document.createElement("link");
          linkElement2.rel = "stylesheet";
          linkElement2.href =
            "https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css";
          document.head.appendChild(linkElement2);
        }

        return new Promise((resolve, reject) => {
          const scriptTimeout = setTimeout(() => {
            reject(new Error("Timeout loading MarkerCluster after 5 seconds"));
          }, 5000);

          const script = document.createElement("script");
          script.src =
            "https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js";
          
          script.onload = () => {
            clearTimeout(scriptTimeout);
            resolve();
          };
          
          script.onerror = () => {
            clearTimeout(scriptTimeout);
            reject(new Error("Failed to load MarkerCluster"));
          };
          
          document.head.appendChild(script);
        });
      } catch (error) {
        console.error("Error loading MarkerCluster:", error);
        console.warn("MarkerCluster failed to load, continuing without clustering");
        return Promise.resolve();
      }
    };

    const loadGoogleMarkerClusterer = async () => {
      if (!config().enableClustering || config().mapProvider !== "google") 
        return Promise.resolve();
      
      if (window.MarkerClusterer) 
        return Promise.resolve();

      try {
        return new Promise((resolve, reject) => {
          const scriptTimeout = setTimeout(() => {
            reject(new Error("Timeout loading Google MarkerClusterer after 5 seconds"));
          }, 5000);

          const script = document.createElement("script");
          script.src = "https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js";
          
          script.onload = () => {
            clearTimeout(scriptTimeout);
            resolve();
          };
          
          script.onerror = () => {
            clearTimeout(scriptTimeout);
            reject(new Error("Failed to load Google MarkerClusterer"));
          };
          
          document.head.appendChild(script);
        });
      } catch (error) {
        console.error("Error loading Google MarkerClusterer:", error);
        console.warn("MarkerClusterer failed to load, continuing without clustering");
        return Promise.resolve();
      }
    };

    // Función de inicialización con pequeñas mejoras SEO
    const initializeMap = async () => {
      try {
        setIsLoading(true);
        setError(null);
        announceToScreenReader(getTranslation("Loading interactive map...")); // SEO: anuncio
        console.log("Iniciando carga de mapa con proveedor:", config().mapProvider);
        
        const scriptsLoaded = await loadMapProvider();
        if (!scriptsLoaded) {
          throw new Error("No se pudieron cargar los scripts necesarios");
        }
        
        if (config().mapProvider === "google") {
          if (config().enableClustering) {
            try {
              await loadGoogleMarkerClusterer();
            } catch (err) {
              console.warn("Error al cargar MarkerClusterer:", err);
            }
          }
          await initializeGoogleMap();
        } else {
          if (config().enableClustering) {
            try {
              await loadMarkerCluster();
            } catch (err) {
              console.warn("Error al cargar MarkerCluster:", err);
            }
          }
          await initializeLeafletMap();
        }
        
        setIsLoading(false);
        announceToScreenReader(getTranslation("Map loaded successfully")); // SEO: anuncio

        // NUEVO: Insertar metadata SEO después de que el mapa se inicialice
        insertSchemaMarkup();
        insertOpenGraphTags();
        
      } catch (error) {
        console.error("Error initializing map:", error);
        setError(getTranslation("Failed to load the map. Please try again later.") + 
                 ` (${error.message})`);
        setIsLoading(false);
      }
    };

    // TODAS LAS FUNCIONES DE INICIALIZACIÓN ORIGINALES SIN CAMBIOS
    const initializeLeafletMap = () => {
      return new Promise((resolve, reject) => {
        try {
          if (!window.L) {
            throw new Error("Leaflet no está cargado correctamente");
          }
          
          const L = window.L;
        
          if (!mapContainer) {
            throw new Error("El contenedor del mapa no está disponible");
          }

          console.log("Creando mapa Leaflet en:", mapContainer);
          console.log("Coordenadas:", config().mainLocation.lat, config().mainLocation.lng);
          
          try {
            const mapInstance = L.map(mapContainer, {
              center: [config().mainLocation.lat, config().mainLocation.lng],
              zoom: config().zoom,
              zoomControl: config().enableZoomControls,
            });
            
            let tileLayer;
            switch (config().customMapStyle) {
              case "light":
                tileLayer = L.tileLayer(
                  "https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png",
                  {
                    attribution:
                      '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
                  }
                );
                break;
              case "dark":
                tileLayer = L.tileLayer(
                  "https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png",
                  {
                    attribution:
                      '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
                  }
                );
                break;
              case "satellite":
                tileLayer = L.tileLayer(
                  "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}",
                  {
                    attribution:
                      "Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community",
                  }
                );
                break;
              case "terrain":
                tileLayer = L.tileLayer(
                  "https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png",
                  {
                    attribution:
                      'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)',
                  }
                );
                break;
              default:
                tileLayer = L.tileLayer(
                  "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
                  {
                    attribution:
                      '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                  }
                );
            }
          
            tileLayer.addTo(mapInstance);
          
            const mainIcon = L.divIcon({
              html: `
                <div class="map-main-marker" style="
                  width: 40px;
                  height: 40px;
                  background-color: ${config().accentColor};
                  border-radius: 50%;
                  border: 3px solid white;
                  box-shadow: 0 3px 10px rgba(0,0,0,0.2);
                  display: flex;
                  align-items: center;
                  justify-content: center;
                  color: white;
                  font-size: 18px;
                  position: relative;
                ">
                  <span style="transform: translateY(-2px);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M12 2C7.6 2 4 5.6 4 10c0 4.4 7 12 8 12s8-7.6 8-12c0-4.4-3.6-8-8-8zm0 10c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/>
                    </svg>
                  </span>
                </div>
              `,
              className: "",
              iconSize: [40, 40],
              iconAnchor: [20, 40],
            });
          
            const poiIcon = L.divIcon({
              html: `
                <div class="map-poi-marker" style="
                  width: 30px;
                  height: 30px;
                  background-color: ${config().secondaryColor};
                  border-radius: 50%;
                  border: 2px solid white;
                  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                  display: flex;
                  align-items: center;
                  justify-content: center;
                  color: white;
                  font-size: 14px;
                ">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M12 8v4M12 16h.01"></path>
                  </svg>
                </div>
              `,
              className: "",
              iconSize: [30, 30],
              iconAnchor: [15, 30],
            });
          
            const mainMarkerInstance = L.marker(
              [config().mainLocation.lat, config().mainLocation.lng],
              { icon: mainIcon }
            ).addTo(mapInstance);
          
            const popupContent = `
              <div class="map-popup" style="min-width: 200px; max-width: 300px;">
                <h3 style="color: ${config().textColor}; font-weight: 600; margin-bottom: 5px; font-size: 16px;">
                  ${config().mainLocation.title || "Mystical Terra Spa"}
                </h3>
                <div style="margin-bottom: 8px; font-size: 13px; color: #666;">
                  ${config().mainLocation.address || ""}
                </div>
                <p style="margin-bottom: 12px; font-size: 14px; color: ${config().textColor};">
                  ${config().mainLocation.description || ""}
                </p>
                ${
                  config().showDirectionsLink
                    ? `<a href="https://www.google.com/maps/dir/?api=1&destination=${
                        config().mainLocation.lat
                      },${config().mainLocation.lng}" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   style="display: inline-block; padding: 6px 12px; background-color: ${config().accentColor}; color: white; text-decoration: none; border-radius: 4px; font-size: 12px; font-weight: 500;">
                     ${getTranslation("Get Directions")}
                   </a>`
                    : ""
                }
              </div>
            `;
          
            mainMarkerInstance.bindPopup(popupContent);
            setMainMarker(mainMarkerInstance);
          
            if (
              config().showPointsOfInterest &&
              config().pointsOfInterest &&
              config().pointsOfInterest.length > 0
            ) {
              let markersGroup;
              console.log("Agregando POIs:", config().pointsOfInterest);
          
              if (config().enableClustering && L.markerClusterGroup) {
                try {
                  markersGroup = L.markerClusterGroup({
                    disableClusteringAtZoom: 16,
                    spiderfyOnMaxZoom: true,
                    showCoverageOnHover: false,
                    zoomToBoundsOnClick: true,
                    maxClusterRadius: 60,
                    iconCreateFunction: function (cluster) {
                      return L.divIcon({
                        html: `<div style="
                          background-color: ${config().secondaryColor};
                          color: white;
                          width: 36px;
                          height: 36px;
                          border-radius: 50%;
                          display: flex;
                          align-items: center;
                          justify-content: center;
                          font-weight: bold;
                          border: 2px solid white;
                          box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                        ">${cluster.getChildCount()}</div>`,
                        className: "",
                        iconSize: L.point(36, 36),
                      });
                    },
                  });
                } catch (e) {
                  console.warn("Error creando grupo de cluster:", e);
                  markersGroup = L.layerGroup();
                }
              } else {
                markersGroup = L.layerGroup();
              }
          
              const markers = config().pointsOfInterest.map((poi) => {
                if (!poi || typeof poi.lat !== 'number' || typeof poi.lng !== 'number') {
                  console.warn("POI inválido:", poi);
                  return null;
                }
                
                try {
                  const marker = L.marker([poi.lat, poi.lng], { icon: poiIcon });
            
                  const poiPopupContent = `
                    <div class="map-popup" style="min-width: 180px; max-width: 250px;">
                      <h3 style="color: ${config().textColor}; font-weight: 600; margin-bottom: 5px; font-size: 14px;">
                        ${poi.title || "Point of Interest"}
                      </h3>
                      <div style="margin-bottom: 5px; font-size: 12px; color: #666;">
                        ${poi.category || ""}
                      </div>
                      <p style="margin-bottom: 10px; font-size: 13px; color: ${config().textColor};">
                        ${poi.description || ""}
                      </p>
                      ${
                        poi.website
                          ? `<a href="${poi.website}" 
                         target="_blank" 
                         rel="noopener noreferrer" 
                         style="display: inline-block; padding: 4px 8px; background-color: ${config().secondaryColor}; color: white; text-decoration: none; border-radius: 4px; font-size: 11px;">
                           ${getTranslation("Visit Website")}
                         </a>`
                          : ""
                      }
                      ${
                        config().showDirectionsLink
                          ? `<a href="https://www.google.com/maps/dir/?api=1&destination=${
                              poi.lat
                            },${poi.lng}" 
                         target="_blank" 
                         rel="noopener noreferrer" 
                         style="display: inline-block; padding: 4px 8px; background-color: ${config().accentColor}; color: white; text-decoration: none; border-radius: 4px; font-size: 11px; margin-left: ${
                           poi.website ? "5px" : "0"
                         };  margin-top: ${!poi.website ? "0" : "5px"};">
                           ${getTranslation("Directions")}
                         </a>`
                          : ""
                      }
                    </div>
                  `;
            
                  marker.bindPopup(poiPopupContent);
                  marker.on("click", () => {
                    setActiveMarker(poi);
                  });
            
                  return marker;
                } catch (e) {
                  console.warn("Error creando marcador:", e);
                  return null;
                }
              }).filter(Boolean);
          
              markers.forEach((marker) => {
                if (marker) markersGroup.addLayer(marker);
              });
          
              markersGroup.addTo(mapInstance);
              setPoiMarkers(markers);
            }
          
            setMap(mapInstance);
          
            const handleResize = () => {
              if (mapInstance) {
                mapInstance.invalidateSize();
                setIsMobile(window.innerWidth < 768);
              }
            };
            
            window.addEventListener("resize", handleResize);
            mapContainer._resizeHandler = handleResize;
          
            setTimeout(() => {
              mapInstance.invalidateSize();
            }, 100);
            
            resolve();
          } catch (mapError) {
            console.error("Error al inicializar el mapa Leaflet:", mapError);
            reject(new Error(`Error al inicializar el mapa Leaflet: ${mapError.message}`));
          }
        } catch (error) {
          console.error("Error general en initializeLeafletMap:", error);
          reject(error);
        }
      });
    };

    // RESTO DE FUNCIONES ORIGINALES SIN CAMBIOS...
    const initializeGoogleMap = () => {
      return new Promise((resolve, reject) => {
        try {
          if (!window.google || !window.google.maps) {
            throw new Error("Google Maps API no está cargada correctamente");
          }
          
          if (!mapContainer) {
            throw new Error("El contenedor del mapa no está disponible");
          }
          
          console.log("Creando mapa Google Maps en:", mapContainer);
          console.log("Coordenadas:", config().mainLocation.lat, config().mainLocation.lng);
      
          const mapStyles = getGoogleMapStyles();
          
          let mapTypeId = window.google.maps.MapTypeId.ROADMAP;
          if (config().customMapStyle === "satellite") {
            mapTypeId = window.google.maps.MapTypeId.SATELLITE;
          } else if (config().customMapStyle === "terrain") {
            mapTypeId = window.google.maps.MapTypeId.TERRAIN;
          }
          
          try {
            const mapInstance = new window.google.maps.Map(mapContainer, {
              center: { lat: config().mainLocation.lat, lng: config().mainLocation.lng },
              zoom: config().zoom,
              styles: mapStyles,
              mapTypeId: mapTypeId,
              mapTypeControl: true,
              mapTypeControlOptions: {
                style: window.google.maps.MapTypeControlStyle.DROPDOWN_MENU,
              },
              streetViewControl: false,
              fullscreenControl: false,
              zoomControl: config().enableZoomControls,
              zoomControlOptions: {
                position: window.google.maps.ControlPosition.RIGHT_BOTTOM,
              },
            });
            
            if (panoramaElement && config().showStreetview) {
              try {
                const panorama = new window.google.maps.StreetViewPanorama(
                  panoramaElement,
                  {
                    position: { lat: config().mainLocation.lat, lng: config().mainLocation.lng },
                    pov: { heading: 165, pitch: 0 },
                    zoom: 1,
                  }
                );
                setStreetViewPanorama(panorama);
              } catch (svError) {
                console.warn("Error al inicializar Street View:", svError);
              }
            }
            
            const mainMarkerInstance = new window.google.maps.Marker({
              position: { lat: config().mainLocation.lat, lng: config().mainLocation.lng },
              map: mapInstance,
              title: config().mainLocation.title || "Mystical Terra Spa",
              animation: window.google.maps.Animation.DROP,
              icon: {
                path: window.google.maps.SymbolPath.CIRCLE,
                scale: 15,
                fillColor: config().accentColor,
                fillOpacity: 0.8,
                strokeColor: "white",
                strokeWeight: 2,
                anchor: new window.google.maps.Point(0, 0),
              },
            });
            
            const infoWindow = new window.google.maps.InfoWindow({
              content: `
                <div class="map-popup" style="min-width: 200px; max-width: 300px;">
                  <h3 style="color: ${config().textColor}; font-weight: 600; margin-bottom: 5px; font-size: 16px;">
                    ${config().mainLocation.title || "Mystical Terra Spa"}
                  </h3>
                  <div style="margin-bottom: 8px; font-size: 13px; color: #666;">
                    ${config().mainLocation.address || ""}
                  </div>
                  <p style="margin-bottom: 12px; font-size: 14px; color: ${config().textColor};">
                    ${config().mainLocation.description || ""}
                  </p>
                  ${
                    config().showDirectionsLink
                      ? `<a href="https://www.google.com/maps/dir/?api=1&destination=${
                          config().mainLocation.lat
                        },${config().mainLocation.lng}" 
                     target="_blank" 
                     rel="noopener noreferrer" 
                     style="display: inline-block; padding: 6px 12px; background-color: ${config().accentColor}; color: white; text-decoration: none; border-radius: 4px; font-size: 12px; font-weight: 500;">
                       ${getTranslation("Get Directions")}
                     </a>`
                      : ""
                  }
                </div>
              `,
            });
            
            mainMarkerInstance.addListener("click", () => {
              infoWindow.open(mapInstance, mainMarkerInstance);
            });
            
            setMainMarker(mainMarkerInstance);
            
            if (config().showPointsOfInterest && config().pointsOfInterest && config().pointsOfInterest.length > 0) {
              console.log("Agregando POIs para Google Maps:", config().pointsOfInterest);
              
              const markers = config().pointsOfInterest
                .filter(poi => poi && typeof poi.lat === 'number' && typeof poi.lng === 'number')
                .map((poi) => {
                  try {
                    const poiMarker = new window.google.maps.Marker({
                      position: { lat: poi.lat, lng: poi.lng },
                      map: mapInstance,
                      title: poi.title || "Point of Interest",
                      animation: window.google.maps.Animation.DROP,
                      icon: {
                        path: window.google.maps.SymbolPath.CIRCLE,
                        scale: 10,
                        fillColor: config().secondaryColor,
                        fillOpacity: 0.7,
                        strokeColor: "white",
                        strokeWeight: 1.5,
                      },
                    });
                    
                    const poiInfoWindow = new window.google.maps.InfoWindow({
                      content: `
                        <div class="map-popup" style="min-width: 180px; max-width: 250px;">
                          <h3 style="color: ${config().textColor}; font-weight: 600; margin-bottom: 5px; font-size: 14px;">
                            ${poi.title || "Point of Interest"}
                          </h3>
                          <div style="margin-bottom: 5px; font-size: 12px; color: #666;">
                            ${poi.category || ""}
                          </div>
                          <p style="margin-bottom: 10px; font-size: 13px; color: ${config().textColor};">
                            ${poi.description || ""}
                          </p>
                          ${
                            poi.website
                              ? `<a href="${poi.website}" 
                             target="_blank" 
                             rel="noopener noreferrer" 
                             style="display: inline-block; padding: 4px 8px; background-color: ${config().secondaryColor}; color: white; text-decoration: none; border-radius: 4px; font-size: 11px;">
                               ${getTranslation("Visit Website")}
                             </a>`
                              : ""
                          }
                          ${
                            config().showDirectionsLink
                              ? `<a href="https://www.google.com/maps/dir/?api=1&destination=${
                                  poi.lat
                                },${poi.lng}" 
                             target="_blank" 
                             rel="noopener noreferrer" 
                             style="display: inline-block; padding: 4px 8px; background-color: ${config().accentColor}; color: white; text-decoration: none; border-radius: 4px; font-size: 11px; margin-left: ${
                               poi.website ? "5px" : "0"
                             }; margin-top: ${!poi.website ? "0" : "5px"};">
                               ${getTranslation("Directions")}
                             </a>`
                              : ""
                          }
                        </div>
                      `,
                    });
                    
                    poiMarker.addListener("click", () => {
                      poiInfoWindow.open(mapInstance, poiMarker);
                      setActiveMarker(poi);
                    });
                    
                    return poiMarker;
                  } catch (markerError) {
                    console.warn("Error al crear marcador POI:", markerError);
                    return null;
                  }
                }).filter(Boolean);
              
              setPoiMarkers(markers);
              
              if (config().enableClustering && window.MarkerClusterer && markers.length > 0) {
                try {
                  new window.MarkerClusterer({
                    map: mapInstance,
                    markers: markers,
                    renderer: {
                      render: ({ count, position }) => {
                        return new window.google.maps.Marker({
                          position,
                          label: {
                            text: String(count),
                            color: "white",
                            fontSize: "12px",
                            fontWeight: "bold",
                          },
                          icon: {
                            path: window.google.maps.SymbolPath.CIRCLE,
                            scale: 18,
                            fillColor: config().secondaryColor,
                            fillOpacity: 0.8,
                            strokeColor: "white",
                            strokeWeight: 2,
                          },
                          zIndex: Number(window.google.maps.Marker.MAX_ZINDEX) + count,
                        });
                      },
                    },
                    onClusterClick: (event, cluster, map) => {
                      map.fitBounds(cluster.bounds);
                    }
                  });
                } catch (clusterError) {
                  console.warn("Error al crear clustering:", clusterError);
                }
              }
            }
            
            setMap(mapInstance);
            
            const handleResize = () => {
              if (mapInstance) {
                window.google.maps.event.trigger(mapInstance, 'resize');
                setIsMobile(window.innerWidth < 768);
              }
            };
            
            window.addEventListener("resize", handleResize);
            mapContainer._resizeHandler = handleResize;
            
            setTimeout(() => {
              window.google.maps.event.trigger(mapInstance, 'resize');
            }, 100);
            
            resolve();
          } catch (mapError) {
            console.error("Error al inicializar el mapa Google Maps:", mapError);
            reject(new Error(`Error al inicializar Google Maps: ${mapError.message}`));
          }
        } catch (error) {
          console.error("Error general en initializeGoogleMap:", error);
          reject(error);
        }
      });
    };

    // Función getGoogleMapStyles simplificada (mantener original si existe)
    const getGoogleMapStyles = () => {
      switch (config().customMapStyle) {
        case "light":
          return [
            { "elementType": "geometry", "stylers": [{"color": "#f5f5f5"}] },
            { "elementType": "labels.icon", "stylers": [{"visibility": "off"}] },
          ];
        case "dark":
          return [
            { "elementType": "geometry", "stylers": [{"color": "#212121"}] },
            { "elementType": "labels.icon", "stylers": [{"visibility": "off"}] },
          ];
        default:
          return [];
      }
    };

    // RESTO DE FUNCIONES ORIGINALES
    const toggleFullscreen = () => {
      if (!map()) return;

      try {
        const mapEl = mapContainer;
        if (!isFullscreen()) {
          if (mapEl.requestFullscreen) {
            mapEl.requestFullscreen();
          } else if (mapEl.mozRequestFullScreen) {
            mapEl.mozRequestFullScreen();
          } else if (mapEl.webkitRequestFullscreen) {
            mapEl.webkitRequestFullscreen();
          } else if (mapEl.msRequestFullscreen) {
            mapEl.msRequestFullscreen();
          } else {
            console.warn("Fullscreen API no soportada en este navegador");
          }
          setIsFullscreen(true);
        } else {
          if (document.exitFullscreen) {
            document.exitFullscreen();
          } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
          } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
          } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
          } else {
            console.warn("Fullscreen API no soportada en este navegador");
          }
          setIsFullscreen(false);
        }

        setTimeout(() => {
          if (config().mapProvider === "google" && map()) {
            window.google.maps.event.trigger(map(), 'resize');
          } else if (map()) {
            map().invalidateSize();
          }
        }, 200);
      } catch (error) {
        console.warn("Error al alternar pantalla completa:", error);
      }
    };

    const toggleStreetView = () => {
      if (config().mapProvider !== "google" || !map()) return;
      
      try {
        setShowingStreetView(!showingStreetView());
        
        if (mapContainer && panoramaElement) {
          if (showingStreetView()) {
            mapContainer.style.display = "none";
            panoramaElement.style.display = "block";
          } else {
            mapContainer.style.display = "block";
            panoramaElement.style.display = "none";
          }
        }
      } catch (error) {
        console.warn("Error al alternar Street View:", error);
        setShowingStreetView(false);
      }
    };

    const retryMapLoad = () => {
      setError(null);
      setIsLoading(true);
      initializeMap();
    };

    // onMount original con pequeños ajustes SEO
    onMount(() => {
      const handleFullscreenChange = () => {
        setIsFullscreen(
          document.fullscreenElement ||
            document.mozFullScreenElement ||
            document.webkitFullscreenElement ||
            document.msFullscreenElement
        );

        if (config().mapProvider === "google" && map()) {
          window.google.maps.event.trigger(map(), 'resize');
        } else if (map()) {
          map().invalidateSize();
        }
      };

      document.addEventListener("fullscreenchange", handleFullscreenChange);
      document.addEventListener("mozfullscreenchange", handleFullscreenChange);
      document.addEventListener("webkitfullscreenchange", handleFullscreenChange);
      document.addEventListener("msfullscreenchange", handleFullscreenChange);

      setIsMobile(window.innerWidth < 768);
      console.log("Componente montado, inicializando mapa");
      initializeMap();

      onCleanup(() => {
        console.log("Limpiando recursos del componente de mapa");
        document.removeEventListener("fullscreenchange", handleFullscreenChange);
        document.removeEventListener("mozfullscreenchange", handleFullscreenChange);
        document.removeEventListener("webkitfullscreenchange", handleFullscreenChange);
        document.removeEventListener("msfullscreenchange", handleFullscreenChange);

        if (mapContainer && mapContainer._resizeHandler) {
          window.removeEventListener("resize", mapContainer._resizeHandler);
        }

        // NUEVO: Limpiar schema markup al desmontar
        const schemaScript = document.querySelector('script[data-map-schema]');
        if (schemaScript) {
          schemaScript.remove();
        }

        if (map()) {
          if (config().mapProvider === "google") {
            if (mainMarker()) {
              mainMarker().setMap(null);
            }
            poiMarkers().forEach(marker => {
              if (marker) marker.setMap(null);
            });
          } else {
            map().remove();
          }
        }
      });
    });

    createEffect(() => {
      const mapInstance = map();
      if (!mapInstance) return;
      
      try {
        if (config().mapProvider === "google") {
          if (mainMarker()) {
            mainMarker().setPosition({ 
              lat: config().mainLocation.lat, 
              lng: config().mainLocation.lng 
            });
          }
          
          mapInstance.setCenter({ 
            lat: config().mainLocation.lat, 
            lng: config().mainLocation.lng 
          });
          mapInstance.setZoom(config().zoom);
        } else {
          if (mainMarker()) {
            mainMarker().setLatLng([config().mainLocation.lat, config().mainLocation.lng]);
          }
          
          mapInstance.setView([config().mainLocation.lat, config().mainLocation.lng], config().zoom);
        }
      } catch (error) {
        console.warn("Error al actualizar coordenadas del mapa:", error);
      }
    });

    // RENDERIZADO CON MEJORAS SEO MÍNIMAS
    return (
      <div
        class="solid-interactive-map-container w-full overflow-hidden"
        style={{
          "background-color": config().backgroundColor,
          color: config().textColor,
          position: "relative",
        }}
        // NUEVOS atributos SEO
        role="region"
        aria-label={getTranslation("Interactive map showing our location")}
        itemScope
        itemType="https://schema.org/LocalBusiness"
      >
        {/* NUEVO: Microdata oculta para SEO */}
        <div style="display: none;" itemScope itemType="https://schema.org/LocalBusiness">
          <span itemProp="name">{config().mainLocation.title}</span>
          <span itemProp="description">{config().mainLocation.description}</span>
          <span itemProp="telephone">{config().mainLocation.phone}</span>
          <span itemProp="email">{config().mainLocation.email}</span>
          <div itemProp="address" itemScope itemType="https://schema.org/PostalAddress">
            <span itemProp="streetAddress">{config().mainLocation.address}</span>
            <span itemProp="addressLocality">{config().mainLocation.city}</span>
            <span itemProp="addressRegion">{config().mainLocation.region}</span>
            <span itemProp="postalCode">{config().mainLocation.postalCode}</span>
            <span itemProp="addressCountry">{config().mainLocation.country}</span>
          </div>
          <div itemProp="geo" itemScope itemType="https://schema.org/GeoCoordinates">
            <meta itemProp="latitude" content={config().mainLocation.lat.toString()} />
            <meta itemProp="longitude" content={config().mainLocation.lng.toString()} />
          </div>
        </div>

        {/* NUEVO: Anuncios para lectores de pantalla */}
        <div
          aria-live="polite"
          aria-atomic="true"
          class="sr-only"
          role="status"
        >
          {announceRegion()}
        </div>

        {/* Estado de carga (sin cambios visuales) */}
        <Show when={isLoading()}>
          <div 
            class="absolute inset-0 bg-white bg-opacity-80 flex flex-col justify-center items-center z-30"
            role="status"
            aria-label={getTranslation("Loading map")}
          >
            <div class="relative w-16 h-16">
              <div class="absolute top-0 left-0 w-full h-full border-4 border-gray-200 rounded-full"></div>
              <div
                class="absolute top-0 left-0 w-full h-full border-4 rounded-full animate-spin"
                style={{
                  "border-color": `${config().accentColor} transparent transparent transparent`,
                  "animation-duration": "1.5s",
                }}
              ></div>
            </div>
            <span
              class="mt-4 text-lg fancy-text italic"
              style={{ color: config().textColor }}
            >
              {isScriptLoading() 
                ? getTranslation("Loading map dependencies...") 
                : getTranslation("Initializing map...")}
            </span>
          </div>
        </Show>

        {/* Mensaje de error (sin cambios visuales) */}
        <Show when={error()}>
          <div 
            class="bg-red-50 border border-red-100 text-red-700 p-6 rounded-lg shadow-sm mb-8 fancy-text italic text-center"
            role="alert"
          >
            {error()}
            <button 
              onClick={retryMapLoad}
              class="mt-4 px-4 py-2 bg-white text-red-700 border border-red-300 rounded-md hover:bg-red-50 transition-colors"
              aria-describedby="error-message"
            >
              {getTranslation("Retry")}
            </button>
          </div>
        </Show>

        {/* Encabezado de la sección (sin cambios visuales) */}
        <div class="container mx-auto px-4 relative">
          <div class="text-center mb-12 relative">
            <Show when={config().subtitle}>
              <span
                class="block text-lg italic font-medium mb-2"
                style={{ color: config().accentColor }}
              >
                {config().subtitle}
              </span>
            </Show>

            <Show when={config().title}>
              <div class="relative inline-block">
                <h2 class="text-3xl md:text-4xl lg:text-5xl fancy-text font-medium mb-4">
                  {config().title}
                </h2>
                <div
                  class="absolute -bottom-2 left-1/2 w-24 h-0.5 transform -translate-x-1/2"
                  style={{ "background-color": config().accentColor }}
                  aria-hidden="true"
                >
                  <div
                    class="absolute left-1/2 top-1/2 w-2 h-2 rounded-full -translate-x-1/2 -translate-y-1/2"
                    style={{ "background-color": config().accentColor }}
                  ></div>
                </div>
              </div>
            </Show>

            <Show when={config().description}>
              <p class="text-xl md:text-2xl fancy-text font-light mt-8 max-w-2xl mx-auto italic opacity-80">
                {config().description}
              </p>
            </Show>
          </div>
        </div>

        {/* Contenedor del mapa (sin cambios visuales) */}
        <div class="map-wrapper relative mx-auto w-full px-4 relative">
          <div
            class="map-container relative rounded-lg overflow-hidden shadow-xl"
            style={{
              height: `${config().mapHeight}px`,
              "border-radius": "8px",
              "box-shadow": "0 10px 25px rgba(0, 0, 0, 0.1)",
              border: "1px solid rgba(0, 0, 0, 0.1)",
            }}
            role="application"
            aria-label={getTranslation("Interactive map")}
          >
            {/* Botones de control (sin cambios visuales) */}
            <Show when={map() && !error()}>
              <div
                class="absolute top-4 right-4 z-10 flex flex-col gap-2"
                style={{ "pointer-events": "auto" }}
                role="toolbar"
                aria-label={getTranslation("Map controls")}
              >
                <Show when={config().enableFullscreen}>
                  <button
                    type="button"
                    onClick={toggleFullscreen}
                    class="bg-white rounded-full p-2 shadow-md hover:shadow-lg transition-all duration-300 flex items-center justify-center"
                    title={getTranslation(
                      isFullscreen() ? "Exit Fullscreen" : "Fullscreen"
                    )}
                    aria-label={getTranslation(
                      isFullscreen() ? "Exit Fullscreen" : "Fullscreen"
                    )}
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="16"
                      height="16"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke={isFullscreen() ? "#E53E3E" : config().accentColor}
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      aria-hidden="true"
                    >
                      {isFullscreen() ? (
                        <path d="M8 3v3a2 2 0 0 1-2 2H3m18 0h-3a2 2 0 0 1-2-2V3m0 18v-3a2 2 0 0 1 2-2h3M3 16h3a2 2 0 0 1 2 2v3"></path>
                      ) : (
                        <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path>
                      )}
                    </svg>
                  </button>
                </Show>

                <Show when={config().mapProvider === "google" && config().showStreetview}>
                  <button
                    type="button"
                    onClick={toggleStreetView}
                    class="bg-white rounded-full p-2 shadow-md hover:shadow-lg transition-all duration-300 flex items-center justify-center"
                    title={getTranslation(
                      showingStreetView() ? "Switch to Map View" : "Switch to Street View"
                    )}
                    aria-label={getTranslation(
                      showingStreetView() ? "Switch to Map View" : "Switch to Street View"
                    )}
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="16"
                      height="16"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke={showingStreetView() ? config().secondaryColor : config().accentColor}
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      aria-hidden="true"
                    >
                      {showingStreetView() ? (
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                      ) : (
                        <path d="M18 10h-4v4h4m-4-8v4m-8 0v6a2 2 0 0 0 2 2h4"></path>
                      )}
                    </svg>
                  </button>
                </Show>
                
                <button
                  type="button"
                  onClick={() => {
                    if (map()) {
                      if (config().mapProvider === "google") {
                        map().setCenter({ 
                          lat: config().mainLocation.lat, 
                          lng: config().mainLocation.lng 
                        });
                        map().setZoom(config().zoom);
                      } else {
                        map().setView([config().mainLocation.lat, config().mainLocation.lng], config().zoom);
                      }
                    }
                  }}
                  class="bg-white rounded-full p-2 shadow-md hover:shadow-lg transition-all duration-300 flex items-center justify-center"
                  title={getTranslation("Reset Map View")}
                  aria-label={getTranslation("Reset Map View")}
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke={config().accentColor}
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                  >
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="16"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                  </svg>
                </button>
              </div>
            </Show>

            {/* Elemento del mapa (sin cambios) */}
            <div
              ref={mapContainer}
              class="w-full h-full"
              style={{
                "z-index": "1",
                display: config().mapProvider === "google" && showingStreetView() ? "none" : "block"
              }}
            ></div>
            
            {/* Elemento para Street View (sin cambios) */}
            <Show when={config().mapProvider === "google" && config().showStreetview}>
              <div
                ref={panoramaElement}
                class="w-full h-full"
                style={{
                  "z-index": "1",
                  display: showingStreetView() ? "block" : "none"
                }}
                aria-label={getTranslation("Street view of location")}
              ></div>
            </Show>
          </div>

          {/* Información complementaria (sin cambios visuales) */}
          <div class="map-info absolute bottom-16 left-1/2 -translate-x-1/2 z-10">
            <Show when={config().mainLocation.contactInfo}>
              <div
                class="contact-info p-4 bg-white rounded-lg shadow-md max-w-md mx-auto"
                style={{
                  "border-left": `3px solid ${config().accentColor}`,
                  "margin-top": "-40px",
                  position: "relative",
                  "z-index": "2",
                }}
              >
                <div class="font-medium text-lg mb-2">{config().mainLocation.title}</div>
                <div class="text-sm opacity-80 mb-1">{config().mainLocation.address}</div>
                <div class="text-sm opacity-80">{config().mainLocation.contactInfo}</div>
              </div>
            </Show>
          </div>
        </div>
      </div>
    );
  };
  
  export default SolidInteractiveMapSEO;