// build-solid.js actualizado
import { build } from "vite";
import { resolve } from "path";
import solidPlugin from "vite-plugin-solid";

// Construir componentes Solid.js de forma modular
async function buildSolidComponents() {
  console.log("Compilando componentes Solid.js de forma modular...");

  try {
    // 1. Primero compilar el núcleo
    console.log("📦 Construyendo solid-core.js...");
    await build({
      configFile: false,
      plugins: [solidPlugin()],
      build: {
        outDir: "assets/public/js",
        emptyOutDir: false,
        minify: true,
        lib: {
          entry: resolve(process.cwd(), "src/public/js/solid-core.js"),
          formats: ["es"],
          fileName: () => "solid-core.js",
        },
        rollupOptions: {
          external: [/^@wordpress\/.+$/],
          output: {
            globals: {
              "solid-js": "solid",
              "solid-js/web": "solidWeb",
            },
          },
        },
      },
      optimizeDeps: {
        include: ["solid-js", "solid-js/web"],
      },
      resolve: {
        alias: {
          "@": resolve(process.cwd(), "src"),
        },
      },
    });
    console.log("✓ solid-core.js construido correctamente!");

    // 2. Construir el módulo de formulario de reservas
    console.log("📦 Construyendo booking-form-module.js...");
    await build({
      configFile: false,
      plugins: [solidPlugin()],
      build: {
        outDir: "assets/public/js/components",
        emptyOutDir: false,
        minify: true,
        lib: {
          entry: resolve(
            process.cwd(),
            "src/public/js/components/booking-form-module.js"
          ),
          formats: ["es"],
          fileName: () => "booking-form-module.js",
        },
        rollupOptions: {
          external: [
            /^@wordpress\/.+$/,
            "../solid-core.js",
            "../../solid-core.js",
            "wp-i18n", // Añadir wp-i18n como dependencia externa
          ],
          output: {
            globals: {
              "solid-js": "solid",
              "solid-js/web": "solidWeb",
              "wp-i18n": "wp.i18n", // Añadir referencia global
            },
          },
        },
      },
      optimizeDeps: {
        include: ["solid-js", "solid-js/web"],
      },
      resolve: {
        alias: {
          "@": resolve(process.cwd(), "src"),
          "../../solid-core": resolve(
            process.cwd(),
            "src/public/js/solid-core.js"
          ),
        },
      },
    });
    console.log("✓ booking-form-module.js construido correctamente!");

    console.log("📦 Construyendo google-reviews-module.js...");
    await build({
      configFile: false,
      plugins: [solidPlugin()],
      build: {
        outDir: "assets/public/js/components",
        emptyOutDir: false,
        minify: true,
        lib: {
          entry: resolve(
            process.cwd(),
            "src/public/js/components/google-reviews-module.js"
          ),
          formats: ["es"],
          fileName: () => "google-reviews-module.js",
        },
        rollupOptions: {
          external: [
            /^@wordpress\/.+$/,
            "../solid-core.js",
            "../../solid-core.js",
            "wp-i18n", // Añadir wp-i18n como dependencia externa
          ],
          output: {
            globals: {
              "solid-js": "solid",
              "solid-js/web": "solidWeb",
              "wp-i18n": "wp.i18n", // Añadir referencia global
            },
          },
        },
      },
      optimizeDeps: {
        include: ["solid-js", "solid-js/web"],
      },
      resolve: {
        alias: {
          "@": resolve(process.cwd(), "src"),
          "../../solid-core": resolve(
            process.cwd(),
            "src/public/js/solid-core.js"
          ),
        },
      },
    });
    console.log("✓ google-reviews-module.js construido correctamente!");

    // Construir el módulo de galería
    console.log("📦 Construyendo gallery-module.js...");
    await build({
      configFile: false,
      plugins: [solidPlugin()],
      build: {
        outDir: "assets/public/js/components",
        emptyOutDir: false,
        minify: true,
        lib: {
          entry: resolve(
            process.cwd(),
            "src/public/js/components/gallery-module.js"
          ),
          formats: ["es"],
          fileName: () => "gallery-module.js",
        },
        rollupOptions: {
          external: [
            /^@wordpress\/.+$/,
            "../solid-core.js",
            "../../solid-core.js",
            "wp-i18n", // Añadir wp-i18n como dependencia externa
          ],
          output: {
            globals: {
              "solid-js": "solid",
              "solid-js/web": "solidWeb",
              "wp-i18n": "wp.i18n", // Añadir referencia global
            },
          },
        },
      },
      optimizeDeps: {
        include: ["solid-js", "solid-js/web"],
      },
      resolve: {
        alias: {
          "@": resolve(process.cwd(), "src"),
          "../../solid-core": resolve(
            process.cwd(),
            "src/public/js/solid-core.js"
          ),
        },
      },
    });
    console.log("✓ gallery-module.js construido correctamente!");

    // Construir el módulo de FAQ
    console.log("📦 Construyendo faq-module.js...");
    await build({
      configFile: false,
      plugins: [solidPlugin()],
      build: {
        outDir: "assets/public/js/components",
        emptyOutDir: false,
        minify: true,
        lib: {
          entry: resolve(
            process.cwd(),
            "src/public/js/components/faq-module.js"
          ),
          formats: ["es"],
          fileName: () => "faq-module.js",
        },
        rollupOptions: {
          external: [
            /^@wordpress\/.+$/,
            "../solid-core.js",
            "../../solid-core.js",
            "wp-i18n", // Añadir wp-i18n como dependencia externa
          ],
          output: {
            globals: {
              "solid-js": "solid",
              "solid-js/web": "solidWeb",
              "wp-i18n": "wp.i18n", // Añadir referencia global
            },
          },
        },
      },
      optimizeDeps: {
        include: ["solid-js", "solid-js/web"],
      },
      resolve: {
        alias: {
          "@": resolve(process.cwd(), "src"),
          "../../solid-core": resolve(
            process.cwd(),
            "src/public/js/solid-core.js"
          ),
        },
      },
    });
    console.log("✓ faq-module.js construido correctamente!");

    console.log("📦 Construyendo interactive-map-module.js...");
    await build({
      configFile: false,
      plugins: [solidPlugin()],
      build: {
        outDir: "assets/public/js/components",
        emptyOutDir: false,
        minify: true,
        lib: {
          entry: resolve(
            process.cwd(),
            "src/public/js/components/interactive-map-module.js"
          ),
          formats: ["es"],
          fileName: () => "interactive-map-module.js",
        },
        rollupOptions: {
          external: [
            /^@wordpress\/.+$/,
            "../solid-core.js",
            "../../solid-core.js",
            "wp-i18n", // Añadir wp-i18n como dependencia externa
          ],
          output: {
            globals: {
              "solid-js": "solid",
              "solid-js/web": "solidWeb",
              "wp-i18n": "wp.i18n", // Añadir referencia global
            },
          },
        },
      },
      optimizeDeps: {
        include: ["solid-js", "solid-js/web"],
      },
      resolve: {
        alias: {
          "@": resolve(process.cwd(), "src"),
          "../../solid-core": resolve(
            process.cwd(),
            "src/public/js/solid-core.js"
          ),
        },
      },
    });
    console.log("✓ interactive-map-module.js construido correctamente!");

    // Construir el módulo de tours carousel
    console.log("📦 Construyendo tours-carousel-module.js...");
    await build({
      configFile: false,
      plugins: [solidPlugin()],
      build: {
        outDir: "assets/public/js/components",
        emptyOutDir: false,
        minify: true,
        lib: {
          entry: resolve(
            process.cwd(),
            "src/public/js/components/tours-carousel-module.js"
          ),
          formats: ["es"],
          fileName: () => "tours-carousel-module.js",
        },
        rollupOptions: {
          external: [
            /^@wordpress\/.+$/,
            "../solid-core.js",
            "../../solid-core.js",
            "wp-i18n", // Añadir wp-i18n como dependencia externa
          ],
          output: {
            globals: {
              "solid-js": "solid",
              "solid-js/web": "solidWeb",
              "wp-i18n": "wp.i18n", // Añadir referencia global
            },
          },
        },
      },
      optimizeDeps: {
        include: ["solid-js", "solid-js/web"],
      },
      resolve: {
        alias: {
          "@": resolve(process.cwd(), "src"),
          "../../solid-core": resolve(
            process.cwd(),
            "src/public/js/solid-core.js"
          ),
        },
      },
    });
    console.log("✓ tours-carousel-module.js construido correctamente!");

    // Construir el módulo de destinations carousel
    console.log("📦 Construyendo destinations-carousel-module.js...");
    await build({
      configFile: false,
      plugins: [solidPlugin()],
      build: {
        outDir: "assets/public/js/components",
        emptyOutDir: false,
        minify: true,
        lib: {
          entry: resolve(
            process.cwd(),
            "src/public/js/components/destinations-carousel-module.js"
          ),
          formats: ["es"],
          fileName: () => "destinations-carousel-module.js",
        },
        rollupOptions: {
          external: [
            /^@wordpress\/.+$/,
            "../solid-core.js",
            "../../solid-core.js",
            "wp-i18n", // Añadir wp-i18n como dependencia externa
          ],
          output: {
            globals: {
              "solid-js": "solid",
              "solid-js/web": "solidWeb",
              "wp-i18n": "wp.i18n", // Añadir referencia global
            },
          },
        },
      },
      optimizeDeps: {
        include: ["solid-js", "solid-js/web"],
      },
      resolve: {
        alias: {
          "@": resolve(process.cwd(), "src"),
          "../../solid-core": resolve(
            process.cwd(),
            "src/public/js/solid-core.js"
          ),
        },
      },
    });
    console.log("✓ destinations-carousel-module.js construido correctamente!");

    // 3. Aquí puedes añadir otros módulos a construir
    // Por ejemplo: otros componentes futuros, etc.

    console.log(
      "✅ Todos los componentes Solid.js han sido construidos correctamente!"
    );
  } catch (error) {
    console.error("❌ Error al construir componentes Solid.js:", error);
    process.exit(1);
  }
}

// Ejecutar la construcción
buildSolidComponents();
