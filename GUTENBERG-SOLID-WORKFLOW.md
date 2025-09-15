# Flujo de Trabajo: Bloques de Gutenberg con Solid.js

## Arquitectura del Sistema

Este tema utiliza una arquitectura híbrida que combina bloques de Gutenberg para el editor con componentes Solid.js para el frontend, proporcionando una experiencia de usuario reactiva y eficiente.

## Estructura de Directorios

```
wp-tailwind-theme-travel/
├── src/
│   ├── admin/
│   │   └── blocks/                     # Bloques de Gutenberg (editor)
│   │       ├── index.jsx               # Registro de bloques
│   │       └── info-box/               # Ejemplo de bloque
│   └── public/
│       └── js/
│           ├── solid-core.js           # Núcleo de Solid.js
│           └── components/
│               ├── solid/              # Componentes JSX de Solid.js
│               └── *-module.js         # Módulos bridge
├── assets/                             # Archivos compilados
│   └── public/js/
├── inc/classes/
│   └── class-wptbt-solid-js-loader.php # Sistema de carga de componentes
└── functions.php                       # Configuración principal
```

## Flujo de Trabajo Completo

### 1. Creación de Bloques de Gutenberg

**Editor Side (WordPress Admin):**
- Los bloques se crean en `src/admin/blocks/`
- Utilizan React.js y APIs de WordPress
- Se compilan para el editor de Gutenberg

**Frontend Side (Sitio público):**
- Los bloques renderizan contenedores HTML con `data-attributes`
- Los componentes Solid.js se montan en estos contenedores

### 2. Sistema de Carga de Componentes Solid.js

**Archivo principal:** `inc/classes/class-wptbt-solid-js-loader.php`

**Funcionalidades:**
- **Registro modular:** Cada componente se registra individualmente
- **Carga condicional:** Solo se cargan los componentes utilizados
- **Sistema de traducciones:** Traducciones específicas por componente
- **Funciones auxiliares:** Helpers PHP para generar HTML

### 3. Componentes Solid.js

**Ubicación:** `src/public/js/components/solid/`

**Características:**
- Reactividad con `createSignal`, `createEffect`
- Componentes optimizados para performance
- Sistema de traducciones integrado
- Propiedades configurables via data-attributes

### 4. Módulos Bridge

**Ubicación:** `src/public/js/components/*-module.js`

**Función:**
- Conectan componentes Solid.js con el sistema de carga
- Extraen propiedades de data-attributes
- Registran componentes en el núcleo

## Componentes Disponibles

| Componente | Archivo JSX | Módulo Bridge | Función PHP |
|------------|-------------|---------------|-------------|
| Formulario de Reservas | `SolidBookingForm.jsx` | `booking-form-module.js` | `wptbt_booking_form_component()` |
| Galería | `SolidGallery.jsx` | `gallery-module.js` | `wptbt_gallery_component()` |
| FAQ | `SolidFAQ.jsx` | `faq-module.js` | `wptbt_faq_component()` |
| Reseñas Google | `SolidGoogleReviews.jsx` | `google-reviews-module.js` | `wptbt_google_reviews_component()` |
| Mapa Interactivo | `SolidInteractiveMap.jsx` | `interactive-map-module.js` | `wptbt_interactive_map_component()` |
| Carousel de Tours | `ToursCarousel.jsx` | `tours-carousel-module.js` | `wptbt_tours_carousel_component()` |

## Cómo Crear un Nuevo Bloque

### Paso 1: Crear el Componente Solid.js

```jsx
// src/public/js/components/solid/MiComponente.jsx
import { createSignal } from "solid-js";
import { __ } from "../../solid-core";

const MiComponente = (props) => {
  const [state, setState] = createSignal(props.initialValue || "");
  
  return (
    <div class="mi-componente">
      <h2>{props.title || __("Default Title")}</h2>
      <p>{state()}</p>
    </div>
  );
};

export default MiComponente;
```

### Paso 2: Crear el Módulo Bridge

```javascript
// src/public/js/components/mi-componente-module.js
import { registerComponent } from "../solid-core";
import MiComponente from "./solid/MiComponente.jsx";

// Función para extraer propiedades del contenedor
function extractProps(container) {
  return {
    title: container.dataset.title || "",
    initialValue: container.dataset.initialValue || "",
    // ... más propiedades
  };
}

// Función de inicialización
function initMiComponente() {
  const containers = document.querySelectorAll('[data-solid-component="mi-componente"]');
  
  containers.forEach(container => {
    if (container.dataset.solidInitialized === "true") return;
    
    const props = extractProps(container);
    
    // Renderizar componente usando el núcleo
    window.solidCore.renderComponent("mi-componente", container, props);
  });
}

// Registrar el componente
registerComponent("mi-componente", MiComponente);

// Inicializar cuando el DOM esté listo
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initMiComponente);
} else {
  initMiComponente();
}
```

### Paso 3: Registrar en el Loader PHP

```php
// En class-wptbt-solid-js-loader.php, método register_scripts()
$this->register_component('mi-componente', 'components/mi-componente-module.js');
```

### Paso 4: Crear Función Auxiliar PHP

```php
// En class-wptbt-solid-js-loader.php
function wptbt_mi_componente_component($props = [], $container_attrs = []) {
    wptbt_load_solid_component('mi-componente');
    
    $container_id = $container_attrs['id'] ?? 'mi-componente-' . uniqid();
    
    $default_attrs = [
        'id' => $container_id,
        'class' => 'solid-mi-componente-container',
        'data-solid-component' => 'mi-componente',
        'data-title' => $props['title'] ?? '',
        'data-initial-value' => $props['initialValue'] ?? '',
    ];
    
    $container_attrs = array_merge($default_attrs, $container_attrs);
    
    // Construir HTML del contenedor
    $html = '<div';
    foreach ($container_attrs as $attr => $value) {
        $html .= ' ' . $attr . '="' . esc_attr($value) . '"';
    }
    $html .= '>';
    $html .= '<div class="loading">Cargando...</div>';
    $html .= '</div>';
    
    return $html;
}
```

### Paso 5: Crear el Bloque de Gutenberg

```javascript
// src/admin/blocks/mi-bloque/index.js
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';

registerBlockType('wp-tailwind-blocks/mi-bloque', {
    title: 'Mi Bloque',
    icon: 'lightbulb',
    category: 'wp-tailwind-blocks',
    
    attributes: {
        title: {
            type: 'string',
            default: 'Título por defecto'
        }
    },
    
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps();
        
        return (
            <div {...blockProps}>
                <p>Vista del editor para Mi Bloque</p>
            </div>
        );
    },
    
    save: ({ attributes }) => {
        const blockProps = useBlockProps.save();
        
        return (
            <div {...blockProps}>
                {/* Aquí va el HTML que se guarda en la base de datos */}
                <div 
                    data-solid-component="mi-componente"
                    data-title={attributes.title}
                >
                    <div class="loading">Cargando componente...</div>
                </div>
            </div>
        );
    }
});
```

## Sistema de Traducciones

### PHP (Loader)
```php
// En wptbt_get_component_translations()
if ($component_name === 'mi-componente') {
    $translations = [
        'Default Title' => __('Título por Defecto', $translate_name),
        'Loading...' => __('Cargando...', $translate_name),
    ];
}
```

### JavaScript (Componente)
```javascript
// En el componente Solid.js
import { __ } from "../../solid-core";

const title = __('Default Title', 'wptbt-mi-componente-block');
```

## Sistema de Build Dual

El proyecto utiliza un sistema de build híbrido que maneja diferentes tecnologías:

### Comando Principal
```bash
bun run build
```

Este comando ejecuta dos procesos de build en secuencia:
1. `vite build` - Para archivos WordPress tradicionales
2. `node build-solid.js` - Para componentes Solid.js específicos

### Build de Vite (`vite.config.js`)

**Entradas:**
- **Public:** Scripts y estilos del frontend (`src/public/`)
- **Admin:** Scripts y estilos del editor (`src/admin/`)

**Salida:** `assets/` con estructura:
```
assets/
├── public/
│   ├── js/
│   └── css/
└── admin/
    ├── js/
    └── css/
```

**Características:**
- Prefijos automáticos para organización
- Soporte para React (bloques Gutenberg)
- TailwindCSS integrado
- Hot Module Replacement (HMR)

### Build de Solid.js (`build-solid.js`)

**Proceso específico para componentes Solid.js:**

1. **solid-core.js** → `assets/public/js/solid-core.js`
2. **Módulos de componentes** → `assets/public/js/components/`
   - `booking-form-module.js`
   - `google-reviews-module.js`
   - `gallery-module.js`
   - `faq-module.js`
   - `interactive-map-module.js`
   - `tours-carousel-module.js`
   - `destinations-carousel-module.js`

**Características del build de Solid.js:**
- Plugin `vite-plugin-solid` para JSX
- Dependencias externas: WordPress APIs
- Alias para importaciones relativas
- Minificación optimizada
- Formato ES modules

### Rutas de Archivos

**Durante desarrollo (`src/`):**
```
src/
├── public/js/
│   ├── solid-core.js
│   └── components/
│       ├── solid/              # Componentes JSX
│       └── *-module.js         # Módulos bridge
└── admin/blocks/               # Bloques Gutenberg
```

**Después del build (`assets/`):**
```
assets/
└── public/js/
    ├── solid-core.js
    └── components/
        └── *-module.js
```

### Scripts de Package.json

```json
{
  "scripts": {
    "dev": "vite",                              // Desarrollo con HMR
    "build": "vite build && node build-solid.js", // Build completo
    "build:solid": "node build-solid.js",       // Solo Solid.js
    "preview": "vite preview",                  // Preview del build
    "serve": "NODE_ENV=development node ./vite-hmr-server.js"
  }
}
```

### Resolución de Dependencias

**En build-solid.js:**
- Alias para `../../solid-core` → ruta absoluta
- Externals: `@wordpress/*`, `wp-i18n`
- Globals: `solid-js`, `solid-js/web`

**En vite.config.js:**
- Alias: `@` → `src/`, `@components` → `components/`
- Externals: WordPress APIs
- OptimizeDeps: Exclusión de WordPress packages

## Problemas Comunes y Soluciones

### 1. Componente no se renderiza
- ✅ Verificar que el módulo esté registrado en PHP (`class-wptbt-solid-js-loader.php`)
- ✅ Comprobar que el data-attribute `data-solid-component` sea correcto
- ✅ Revisar la consola del navegador para errores
- ⚠️ **Ejecutar el build:** `bun run build` para compilar los componentes

### 2. Archivos no encontrados (404)
- ✅ Verificar que `bun run build` se haya ejecutado correctamente
- ✅ Comprobar que existan los archivos en `assets/public/js/`
- ✅ Revisar que las rutas en PHP apunten a `assets/` no a `src/`

### 3. Errores de importación en Solid.js
- ✅ Verificar alias en `build-solid.js`: `../../solid-core` → ruta absoluta
- ✅ Comprobar que dependencias externas estén en `external` array
- ✅ Asegurar que `vite-plugin-solid` esté instalado

### 4. Traducciones no funcionan
- ✅ Verificar que las traducciones estén definidas en PHP
- ✅ Comprobar que el dominio de traducción coincida
- ✅ Asegurar que `wp_set_script_translations` esté llamado

### 5. Estilos no se aplican
- ✅ Verificar que Tailwind CSS esté compilado
- ✅ Comprobar que las clases estén en el safelist
- ✅ Revisar que los estilos específicos estén incluidos

### 6. HMR no funciona en desarrollo
- ✅ Usar `bun run dev` para desarrollo con Vite
- ✅ Para Solid.js usar `bun run build:solid` después de cambios
- ⚠️ **Nota:** HMR solo funciona para archivos procesados por Vite, no para Solid.js

## Ventajas de esta Arquitectura

1. **Performance:** Solo se cargan los componentes utilizados
2. **Modularidad:** Cada componente es independiente
3. **Escalabilidad:** Fácil agregar nuevos componentes
4. **Compatibilidad:** Funciona con el editor de Gutenberg
5. **Reactividad:** Solid.js proporciona reactividad fina
6. **SEO:** Renderizado inicial en servidor con PHP

## Buenas Prácticas

1. **Nomenclatura consistente:** Usar prefijos `wptbt-` para identificadores
2. **Propiedades configurables:** Hacer componentes reutilizables
3. **Manejo de errores:** Incluir fallbacks para datos faltantes
4. **Optimización:** Lazy loading de componentes pesados
5. **Accesibilidad:** Seguir estándares WCAG
6. **Testing:** Probar en diferentes navegadores y dispositivos

Este sistema proporciona una base sólida para crear bloques de Gutenberg modernos y reactivos, combinando lo mejor de WordPress con la performance de Solid.js.