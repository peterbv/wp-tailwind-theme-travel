// tailwind.config.js
module.exports = {
  content: [
    "./inc/**/*.php",
    "./template-parts/**/*.php",
    "./*.php",
    "./HTML/*.html",
    "./src/**/*.js",
    "./src/**/*.jsx",
  ],
  theme: {
    extend: {
      colors: {
        primary: "#0d6efd",
        secondary: "#6c757d",
        success: "#198754",
        info: "#0dcaf0",
        warning: "#ffc107",
        danger: "#dc3545",
        light: "#f8f9fa",
        dark: "#212529",
        travel: {
          primary: "#DC2626", // Rojo principal elegante
          secondary: "#F9FAFB", // Gris muy claro profesional
          accent: "#EF4444", // Rojo vibrante para CTAs
          dark: "#1F2937", // Gris oscuro para textos
          light: "#F3F4F6", // Gris claro para fondos
          gold: "#F59E0B", // Dorado para detalles especiales
        },
        // Mantener compatibilidad con clases existentes
        spa: {
          primary: "#DC2626", // Mapear a travel primary (rojo)
          secondary: "#F9FAFB", // Mapear a travel secondary
          accent: "#EF4444", // Mapear a travel accent (rojo vibrante)
          sage: "#1F2937", // Mapear a travel dark
          rose: "#F59E0B", // Mapear a travel gold
        },
      },
      // Configuración de fuentes
      fontFamily: {
        sans: ["Montserrat", "ui-sans-serif", "system-ui", "sans-serif"],
        //serif: ["Playfair Display", "ui-serif", "Georgia", "serif"],
      },
      // Definición de animaciones
      animation: {
        "fade-up": "fadeUp 0.7s ease-out forwards",
        "fade-down": "fadeDown 0.7s ease-out forwards",
        "fade-left": "fadeLeft 0.7s ease-out forwards",
        "fade-right": "fadeRight 0.7s ease-out forwards",
        "fade-zoom": "fadeZoom 0.7s ease-out forwards",
      },
      keyframes: {
        fadeUp: {
          "0%": { opacity: "0", transform: "translateY(30px)" },
          "100%": { opacity: "1", transform: "translateY(0)" },
        },
        fadeDown: {
          "0%": { opacity: "0", transform: "translateY(-30px)" },
          "100%": { opacity: "1", transform: "translateY(0)" },
        },
        fadeLeft: {
          "0%": { opacity: "0", transform: "translateX(30px)" },
          "100%": { opacity: "1", transform: "translateX(0)" },
        },
        fadeRight: {
          "0%": { opacity: "0", transform: "translateX(-30px)" },
          "100%": { opacity: "1", transform: "translateX(0)" },
        },
        fadeZoom: {
          "0%": { opacity: "0", transform: "scale(0.95)" },
          "100%": { opacity: "1", transform: "scale(1)" },
        },
      },

      transitionTimingFunction: {
        elastic: "cubic-bezier(0.16, 1.11, 0.3, 1.02)",
        bounce: "cubic-bezier(0.175, 0.885, 0.32, 1.275)",
        smooth: "cubic-bezier(0.33, 1, 0.68, 1)",
      },
    },
  },
  plugins: [
    // Plugin para añadir la variante intersection observer
    function ({ addVariant }) {
      // Variantes para el sistema de animaciones
      addVariant("reveal-visible", '&:where([data-reveal="visible"])');
      addVariant("reveal-hidden", '&:where([data-reveal="hidden"])');

      // Variantes para elementos dentro de un contenedor revelado
      addVariant("reveal-parent-visible", ':where([data-reveal="visible"]) &');

      // Variantes para retrasos personalizados
      addVariant("delay-75", "&.delay-75");
      addVariant("delay-150", "&.delay-150");
      addVariant("delay-300", "&.delay-300");
      addVariant("delay-500", "&.delay-500");
      addVariant("delay-700", "&.delay-700");
      addVariant("delay-1000", "&.delay-1000");
    },
  ],
  safelist: [
    "wp-block",
    "alignfull",
    "alignwide",
    "has-text-align-center",
    "has-text-align-left",
    "has-text-align-right",
    // Clases de animación e intersección
    "opacity-0",
    "motion-safe:opacity-0",
    "intersect-once:animate-fade",
    "intersect-once:animate-slide-up",
    "intersect-once:animate-fade-zoom-in",
    "animation-delay-300",
    "animation-delay-600",
    "animation-delay-900",
    "active",
  ],
};
