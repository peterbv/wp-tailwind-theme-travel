// vite.config.js
import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import path from "path";
import tailwindcss from "@tailwindcss/vite";

// Define entradas por separado para public y admin
const publicEntries = {
  // JavaScript
  main: path.resolve(__dirname, "src/public/js/main.js"),
  navigation: path.resolve(__dirname, "src/public/js/navigation.js"),
  "floating-buttons": path.resolve(
    __dirname,
    "src/public/js/floating-buttons.js"
  ),
  "banner-carousel": path.resolve(
    __dirname,
    "src/public/js/banner-carousel.js"
  ),
  // CSS/SASS
  style: path.resolve(__dirname, "src/public/sass/main.scss"),
  banner: path.resolve(__dirname, "src/public/sass/banner.scss"),
  "datepicker-style": path.resolve(
    __dirname,
    "src/public/sass/datepicker.scss"
  ),
};

const adminEntries = {
  // JavaScript
  "banner-admin": path.resolve(__dirname, "src/admin/js/banner-admin.js"),
  blocks: path.resolve(__dirname, "src/admin/blocks/index.jsx"),
  "services-block": path.resolve(__dirname, "src/admin/js/services-block.js"),
  "benefits-block": path.resolve(__dirname, "src/admin/js/benefits-block.js"),
  editor: path.resolve(__dirname, "src/admin/js/editor.js"),
  customize: path.resolve(__dirname, "src/admin/js/customize.js"),
  "booking-block": path.resolve(__dirname, "src/admin/js/booking-block.js"),
  "faq-block": path.resolve(__dirname, "src/admin/js/faq-block.js"),
  "google-reviews-block": path.resolve(
    __dirname,
    "src/admin/js/google-reviews-block.js"
  ),
  "gallery-block": path.resolve(__dirname, "src/admin/js/gallery-block.js"),
  "tours-carousel-block": path.resolve(__dirname, "src/admin/js/tours-carousel-block.js"),
  "destinations-carousel-block": path.resolve(__dirname, "src/admin/js/destinations-carousel-block.js"),
  "interactive-map-block": path.resolve(__dirname, "src/admin/js/interactive-map-block.js"),

  // CSS/SASS
  editorStyle: path.resolve(__dirname, "src/admin/sass/editor.scss"),
  "banner-style": path.resolve(__dirname, "src/admin/css/banner.css"),
  "services-block-style": path.resolve(
    __dirname,
    "src/admin/css/services-block.css"
  ),
  "benefits-block-style": path.resolve(
    __dirname,
    "src/admin/css/benefits-block.css"
  ),
  "booking-block-style": path.resolve(
    __dirname,
    "src/admin/css/booking-block.css"
  ),
  "faq-block-style": path.resolve(__dirname, "src/admin/css/faq-block.css"),
  "google-reviews-block-style": path.resolve(
    __dirname,
    "src/admin/css/google-reviews-block.css"
  ),
  "gallery-block-style": path.resolve(
    __dirname,
    "src/admin/css/gallery-block.css"
  ),
  "tours-carousel-block-style": path.resolve(
    __dirname,
    "src/admin/css/tours-carousel-block-style.css"
  ),
  "destinations-carousel-block-style": path.resolve(
    __dirname,
    "src/admin/css/destinations-carousel-block-style.css"
  ),
  "interactive-map-block-style": path.resolve(
    __dirname,
    "src/admin/css/interactive-map-block.css"
  ),
};

// Función para generar rutas con prefijos
function prefixEntries(entries, prefix) {
  const result = {};
  Object.entries(entries).forEach(([key, value]) => {
    result[`${prefix}/${key}`] = value;
  });
  return result;
}

// Combinar ambos objetos con sus prefijos
const allEntries = {
  ...prefixEntries(publicEntries, "public"),
  ...prefixEntries(adminEntries, "admin"),
};

export default defineConfig({
  plugins: [
    // React plugin for existing React components
    react(),
    tailwindcss(),
  ],
  root: "./",
  base:
    process.env.NODE_ENV === "production"
      ? "/wp-content/themes/wp-tailwind-theme/assets/"
      : "/",
  build: {
    outDir: "assets",
    assetsDir: "",
    manifest: true,
    emptyOutDir: true,
    rollupOptions: {
      input: allEntries,
      output: {
        entryFileNames: (chunkInfo) => {
          // Extract prefix (public or admin)
          const [prefix, ...rest] = chunkInfo.name.split("/");
          return `${prefix}/js/${rest.join("-")}.js`;
        },
        chunkFileNames: "js/[name].js",
        assetFileNames: (assetInfo) => {
          // Obtener el nombre sin la extensión para evitar duplicaciones
          const fileName = assetInfo.name.split(".").slice(0, -1).join(".");
          const extType = assetInfo.name.split(".").pop();

          // Si el nombre incluye una ruta con / (como public/ o admin/)
          if (fileName.includes("/")) {
            const [prefix, ...rest] = fileName.split("/");

            // CSS a su propia carpeta dentro del prefijo
            if (extType === "css") {
              return `${prefix}/css/${rest.join("-")}[extname]`;
            }

            // Manejo de fuentes
            if (["ttf", "woff", "woff2", "eot"].includes(extType)) {
              return `${prefix}/fonts/${rest.join("-")}[extname]`;
            }

            // Otros assets
            return `${prefix}/assets/${rest.join("-")}[extname]`;
          }

          // Assets sin prefijo específico
          if (extType === "css") {
            return "css/[name][extname]";
          }
          // Manejo de fuentes sin prefijo
          if (["ttf", "woff", "woff2", "eot"].includes(extType)) {
            return "fonts/[name][extname]";
          }
          return "assets/[name][extname]";
        },
      },
      external: [/^@wordpress\/.+$/],
    },
  },
  server: {
    host: "localhost",
    port: 3000,
    strictPort: true,
    cors: true,
    hmr: {
      host: "localhost",
      port: 3000,
      protocol: "ws",
    },
  },
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "src"),
      "@images": "/src/assets/images",
      "@components": path.resolve(__dirname, "src/public/js/components"),
    },
  },
  esbuild: {
    loader: "jsx",
    include: /\.(jsx|js)$/,
    exclude: [],
  },
  optimizeDeps: {
    exclude: [
      "@wordpress/block-editor",
      "@wordpress/blocks",
      "@wordpress/components",
      "@wordpress/element",
      "@wordpress/i18n",
    ],
  },
});
