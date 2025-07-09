// src/public/js/solid-core.js
import { render } from "solid-js/web";

// Obtener todas las variables de traducción disponibles
const getAllTranslations = () => {
  const translations = {};

  // Buscar todas las variables de traducción en la ventana global
  Object.keys(window).forEach((key) => {
    if (key.startsWith("wptbtI18n_")) {
      Object.assign(translations, window[key]);
    }
  });

  // También incluir el objeto principal si existe
  if (window.wptbtI18n) {
    Object.assign(translations, window.wptbtI18n);
  }

  return translations;
};

// Función de traducción modular
export const __ = (text, domain) => {
  // 1. Intentar con wp.i18n primero si está disponible
  if (
    typeof window.wp !== "undefined" &&
    typeof window.wp.i18n !== "undefined"
  ) {
    return window.wp.i18n.__(text, domain || "wp-tailwind-blocks");
  }

  // 2. Buscar en todos los objetos de traducción disponibles
  const allTranslations = getAllTranslations();
  if (allTranslations[text]) {
    return allTranslations[text];
  }

  // 3. Devolver el texto original si no hay traducción
  return text;
};

/**
 * Sistema modular para Solid.js en WordPress
 * Permite registrar y renderizar componentes Solid.js
 */

// Almacenamiento de componentes registrados
const components = {};

// Verificar si el DOM está cargado
const isDOMReady = () => document.readyState !== "loading";

/**
 * Registrar un componente Solid.js
 * @param {string} name - Nombre único del componente
 * @param {Function} Component - Componente Solid.js
 * @returns {void}
 */
export function registerComponent(name, Component) {
  if (typeof name !== "string" || name.trim() === "") {
    console.error("El nombre del componente debe ser una cadena válida");
    return;
  }

  if (typeof Component !== "function") {
    console.error(
      `El componente ${name} debe ser una función válida de Solid.js`
    );
    return;
  }

  // Registrar componente
  components[name] = Component;
  console.log(`Componente '${name}' registrado correctamente`);
}

/**
 * Obtener un componente registrado
 * @param {string} name - Nombre del componente a obtener
 * @returns {Function|null} - Componente Solid.js o null si no existe
 */
export function getComponent(name) {
  return components[name] || null;
}

/**
 * Renderizar un componente registrado en un contenedor DOM
 * @param {string} name - Nombre del componente a renderizar
 * @param {HTMLElement} container - Contenedor DOM donde se renderizará
 * @param {Object} props - Propiedades a pasar al componente
 * @returns {Function|null} - Función de limpieza o null si falló
 */
export function renderComponent(name, container, props = {}) {
  const Component = getComponent(name);

  if (!Component) {
    console.error(`El componente '${name}' no está registrado`);
    return null;
  }

  if (!container || !(container instanceof HTMLElement)) {
    console.error(
      "Se requiere un contenedor DOM válido para renderizar el componente"
    );
    return null;
  }

  try {
    // Limpiar el contenedor antes de renderizar
    while (container.firstChild) {
      container.removeChild(container.firstChild);
    }

    // Renderizar el componente
    const dispose = render(() => Component(props), container);

    // Marcar el contenedor como inicializado
    container.dataset.solidInitialized = "true";

    return dispose;
  } catch (error) {
    console.error(`Error al renderizar el componente '${name}':`, error);

    // Mostrar mensaje de error en el contenedor
    container.innerHTML = `
      <div class="p-4 bg-red-100 text-red-800 rounded-md">
        <p>Error al cargar el componente: ${error.message}</p>
      </div>
    `;

    return null;
  }
}

/**
 * Inicializar componentes automáticamente en contenedores con data-solid-component
 */
export function initComponents() {
  const containers = document.querySelectorAll("[data-solid-component]");

  if (containers.length === 0) {
    return; // No hay contenedores, salir
  }

  containers.forEach((container) => {
    const componentName = container.dataset.solidComponent;
    if (!componentName) return;

    // Evitar inicialización doble
    if (container.dataset.solidInitialized === "true") return;

    // Obtener propiedades del atributo data-props si existe
    let props = {};
    try {
      if (container.dataset.props) {
        props = JSON.parse(container.dataset.props);
      }
    } catch (error) {
      console.warn(
        `Error al parsear propiedades para ${componentName}:`,
        error
      );
    }

    // Renderizar componente
    renderComponent(componentName, container, props);
  });
}

// Inicializar componentes cuando el DOM está listo
if (isDOMReady()) {
  initComponents();
} else {
  document.addEventListener("DOMContentLoaded", initComponents);
}

// API pública
const solidCore = {
  registerComponent,
  getComponent,
  renderComponent,
  initComponents,
};

// Exponer el API globalmente para acceso desde otros scripts
window.solidCore = solidCore;

export default solidCore;
