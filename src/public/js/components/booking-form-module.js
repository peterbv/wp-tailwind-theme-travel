// src/public/js/components/booking-form-module.js
// ACTUALIZADO para múltiples precios
import solidCore, { registerComponent } from "../../solid-core";
import SolidBookingForm from "./solid/SolidBookingForm";

// Registrar el componente de formulario de reserva
registerComponent("booking-form", SolidBookingForm);

/**
 * Inicializa los formularios de reserva en la página
 */
function initBookingForms() {
  // Buscar contenedores de formulario
  const containers = document.querySelectorAll(".solid-booking-container");

  if (containers.length === 0) {
    return; // No hay contenedores, salir
  }

  // Para cada contenedor, inicializar el componente
  containers.forEach((container) => {
    try {
      // Extraer datos del contenedor
      let services = [];
      let useSingleService = container.dataset.useSingleService === "true";

      try {
        // Intentar parsear JSON de servicios
        const servicesData = container.dataset.services;
        if (servicesData) {
          // Parsear el JSON base
          const parsedServices = JSON.parse(servicesData);

          // CORREGIDO: Procesar cada servicio con validación mejorada
          services = parsedServices.map((service) => {
            // Inicializar array de duraciones
            let durations = [];

            // CORREGIDO: Usar el array de duraciones si está disponible
            if (Array.isArray(service.durations) && service.durations.length > 0) {
              durations = service.durations.map(duration => {
                // VERIFICACIÓN: Asegurar que todos los campos necesarios existen
                const minutes = duration.minutes || parseInt(duration.duration) || 0;
                const price = duration.price || '';
                const durationText = duration.duration || `${minutes}`;
                
                return {
                  minutes: minutes,
                  price: price,
                  text: duration.text || `${durationText} días - $${price.replace('$', '')}`,
                  duration: durationText,
                  value: duration.value || `${durationText}days-${price}` // Asegurar que value existe
                };
              });
            } else {
              // Fallback: formato anterior con validación
              if (service.duration1 && service.price1) {
                const days = parseInt(service.duration1.replace(/[^0-9]/g, '')) || 0;
                durations.push({
                  minutes: days * 24 * 60, // Convert days to minutes for compatibility
                  price: service.price1,
                  text: `${service.duration1} días - $${service.price1.replace('$', '')}`,
                  duration: service.duration1,
                  value: `${days}days-${service.price1}`
                });
              }
              if (service.duration2 && service.price2) {
                const days = parseInt(service.duration2.replace(/[^0-9]/g, '')) || 0;
                durations.push({
                  minutes: days * 24 * 60, // Convert days to minutes for compatibility
                  price: service.price2,
                  text: `${service.duration2} días - $${service.price2.replace('$', '')}`,
                  duration: service.duration2,
                  value: `${days}days-${service.price2}`
                });
              }
            }

            // CORREGIDO: Asegurar que hours sea un array válido
            let hours = [];
            if (Array.isArray(service.hours)) {
              hours = service.hours.filter(hour => hour && typeof hour === 'string');
            }

            // VERIFICACIÓN: Log de debug para cada servicio
            console.log(`Processing service ${service.id}: ${hours.length} hours, ${durations.length} durations`);
            console.log(`Service ${service.id} booking_config:`, service.booking_config);
            
            // Devolver el servicio con formato mejorado
            return {
              id: String(service.id), // IMPORTANTE: Convertir a string para consistencia
              title: service.title || "", // Título del servicio
              subtitle: service.subtitle || "", // Subtítulo del servicio
              hours: hours, // Horarios disponibles (array filtrado)
              durations: durations, // Duraciones y precios en nuevo formato
              // NUEVO: Incluir configuración del tour
              booking_config: service.booking_config || null, // Configuración del formulario de reservas
              // Mantener compatibilidad
              duration1: service.duration1 || '',
              price1: service.price1 || '',
              duration2: service.duration2 || '',
              price2: service.price2 || ''
            };
          });

          console.log("Servicios procesados con múltiples precios:", services);
          
          // VERIFICACIÓN ADICIONAL: Validar datos críticos
          services.forEach(service => {
            if (service.hours.length === 0) {
              console.warn(`Service ${service.title} has no hours configured`);
            }
            if (service.durations.length === 0) {
              console.warn(`Service ${service.title} has no durations configured`);
            }
          });
        }
      } catch (error) {
        console.error("Error al parsear datos de servicios:", error);
        console.error("Raw services data:", container.dataset.services);
      }

      // VERIFICACIÓN: Solo continuar si tenemos servicios válidos
      if (services.length === 0) {
        console.warn("No valid services found, cannot initialize booking form");
        container.innerHTML = `
          <div class="p-4 bg-amber-100 text-amber-800 rounded-md">
            <p>No services available for booking</p>
          </div>
        `;
        return;
      }

      // Configurar props para el componente
      const props = {
        formId: container.id || "booking-form",
        services: services,
        isDarkMode: container.dataset.darkMode === "true",
        accentColor: container.dataset.accentColor || "#D4B254",
        ajaxUrl: container.dataset.ajaxUrl || "",
        nonce: container.dataset.nonce || "",
        useSingleService: useSingleService,
        emailRecipient: container.dataset.emailRecipient || ""
      };

      // VERIFICACIÓN: Log de props finales
      console.log("Final props for Solid component:", props);

      // Renderizar el componente de Solid.js
      const dispose = solidCore.renderComponent(
        "booking-form",
        container,
        props
      );

      // Guardar referencia al método dispose para posible limpieza
      container._solidDispose = dispose;

      console.log("Formulario de reserva con Solid.js y múltiples precios cargado correctamente");
    } catch (error) {
      console.error("Error al inicializar formulario con Solid.js:", error);

      // Mostrar mensaje de error en el contenedor
      container.innerHTML = `
        <div class="p-4 bg-red-100 text-red-800 rounded-md">
          <p>Error al cargar el formulario de reserva: ${error.message}</p>
          <details class="mt-2">
            <summary class="cursor-pointer">Debug Info</summary>
            <pre class="mt-2 text-xs">${error.stack}</pre>
          </details>
        </div>
      `;
    }
  });
}
// Inicializar cuando el DOM esté listo
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initBookingForms);
} else {
  initBookingForms();
}

/**
 * Función global para inicializar un formulario de reserva en un contenedor específico
 */
function initializeSolidBookingForm(container, options = {}) {
  try {
    // Función para obtener todos los tours del sitio
    async function fetchAvailableTours() {
      try {
        const response = await fetch(window.wptbt_ajax?.url || '/wp-admin/admin-ajax.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: new URLSearchParams({
            action: 'get_tours_for_booking',
            nonce: window.wptbt_ajax?.nonce || ''
          })
        });
        
        const data = await response.json();
        if (data.success && data.data) {
          return data.data;
        }
      } catch (error) {
        console.warn('Error fetching tours:', error);
      }
      
      // Fallback por defecto
      return [
        {
          id: "general",
          title: "Reserva General",
          subtitle: "Contacta para más información",
          hours: ["09:00", "10:00", "11:00", "14:00", "15:00", "16:00"],
          durations: [
            {
              minutes: 480,
              price: "Consultar precio",
              text: "Tour personalizado - Consultar precio",
              duration: "1",
              value: "custom-tour"
            }
          ]
        }
      ];
    }

    // Función para inicializar el formulario con los tours
    async function initForm() {
      const services = options.services || await fetchAvailableTours();
      
      const props = {
        formId: options.formId || "modal-booking-form",
        services: services,
        isDarkMode: options.isDarkMode || false,
        accentColor: options.accentColor || "#D4B254",
        ajaxUrl: options.ajaxUrl || (window.wptbt_ajax?.url || ""),
        nonce: options.nonce || (window.wptbt_ajax?.nonce || ""),
        useSingleService: options.useSingleService || false, // CORREGIDO: Permitir selección múltiple
        emailRecipient: options.emailRecipient || "",
        modalMode: options.modalMode || false,
        onComplete: options.onComplete || (() => {})
      };

      console.log("Initializing booking form in modal with props:", props);

      // Renderizar el componente
      const dispose = solidCore.renderComponent(
        "booking-form",
        container,
        props
      );

      // Guardar referencia para limpieza
      container._solidDispose = dispose;

      return dispose;
    }
    
    // Inicializar el formulario
    return initForm();
    
  } catch (error) {
    console.error("Error initializing booking form:", error);
    container.innerHTML = `
      <div class="p-4 bg-red-100 text-red-800 rounded-md">
        <p>Error al cargar el formulario de reserva: ${error.message}</p>
      </div>
    `;
    return null;
  }
}

// Hacer la función disponible globalmente
if (typeof window !== 'undefined') {
  window.initializeSolidBookingForm = initializeSolidBookingForm;
}

// Exportar funciones para posible uso externo
export { initBookingForms, initializeSolidBookingForm };