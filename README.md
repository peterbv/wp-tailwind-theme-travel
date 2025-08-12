# Travel Vibes - Tema WordPress para Agencias de Viajes

![Travel Vibes Theme](https://img.shields.io/badge/Version-1.0.0-blue.svg)
![WordPress](https://img.shields.io/badge/WordPress-6.0+-green.svg)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.4+-06B6D4.svg)
![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4.svg)

Un tema WordPress moderno y completamente responsivo diseñado específicamente para agencias de viajes, operadores turísticos y empresas del sector turismo. Construido con Tailwind CSS para un diseño elegante y rendimiento optimizado.

## 🌟 Características Principales

### ✈️ **Específico para Turismo**
- **Custom Post Type "Tours"** con campos personalizados completos
- **Taxonomías especializadas**: Categorías de tours, Destinos, Duración
- **Campos meta avanzados**: Precio, duración, dificultad, incluye/excluye, itinerario
- **Formulario de reservas** integrado en cada tour
- **Sistema de filtros** por precio, categoría, destino y duración

### 🎨 **Diseño Moderno**
- **Totalmente responsivo** - Optimizado para móviles, tablets y desktop
- **Tailwind CSS 3.4+** - Framework CSS moderno y eficiente
- **Componentes reutilizables** - Sistema de diseño consistente
- **Animaciones suaves** - Transiciones y efectos visuales atractivos
- **Modo oscuro preparado** - Fácil implementación de tema oscuro

### 🚀 **Rendimiento Optimizado**
- **Lazy Loading** para imágenes
- **Minimización de CSS/JS** en producción
- **Código limpio y semántico**
- **Core Web Vitals optimizado**
- **SEO friendly** con meta tags y estructuras correctas

### 🔧 **Funcionalidades Avanzadas**
- **Buscador avanzado** de tours con filtros múltiples
- **Sistema de favoritos** (wishlist) con localStorage
- **Integración WhatsApp** para consultas directas
- **Formularios de contacto** optimizados
- **Newsletter** con validación
- **Breadcrumbs** automáticos
- **Widgets personalizados** para sidebar
- **Generador de datos de ejemplo** - Sistema completo para cargar contenido de demostración

## 📋 Requisitos

- **WordPress:** 5.0 o superior
- **PHP:** 7.4 o superior
- **Node.js:** 16.0+ (para desarrollo)
- **NPM:** 8.0+ (para desarrollo)

## 🔧 Instalación

### 1. Descargar e Instalar el Tema

```bash
# Clonar el repositorio
git clone https://github.com/peterbv/wp-tailwind-theme-travel.git

# O descargar como ZIP desde GitHub
```

### 2. Subir al WordPress

1. Copia la carpeta del tema a `/wp-content/themes/`
2. Ve a **Apariencia > Temas** en tu WordPress
3. Activa el tema **Travel Vibes**

### 3. Configuración de Desarrollo (Opcional)

Si quieres personalizar los estilos:

```bash
# Instalar dependencias
npm install

# Desarrollo (watch mode)
npm run dev

# Producción (minificado)
npm run build
```

## ⚙️ Configuración

### 1. Configuración Básica

1. **Logotipo**: Ve a **Apariencia > Personalizar > Identidad del sitio**
2. **Menús**: Configura en **Apariencia > Menús**
   - Menú Principal
   - Menú Footer
   - Menú Tours (opcional)

### 2. Custom Post Types

El tema crea automáticamente:

- **Tours** (`/tours/`)
- **Categorías de Tours** (`/categoria-tour/`)
- **Destinos** (`/destino/`)
- **Duración** (`/duracion/`)

### 3. Widgets Disponibles

- **Sidebar Principal**: Para páginas y posts
- **Footer Widget 1, 2, 3**: Para el pie de página

### 4. Configuración del Personalizador

Ve a **Apariencia > Personalizar** para configurar:

- **Información de Contacto**
  - Dirección
  - Teléfono
  - Email
  - WhatsApp
- **Redes Sociales**
  - Facebook
  - Instagram
  - Twitter
  - YouTube
- **Descripción Footer**

## 📖 Uso del Tema

### Crear Tours

1. Ve a **Tours > Agregar Nuevo**
2. Completa la información básica:
   - Título del tour
   - Descripción
   - Imagen destacada
   - Extracto

3. En **Detalles del Tour**, completa:
   - **Precio**: En USD
   - **Duración**: Ej: "5 días / 4 noches"
   - **Dificultad**: Fácil, Moderado, Difícil, Extremo
   - **Máximo de Personas**: Número entero
   - **Incluye**: Lista de lo que incluye el tour
   - **No Incluye**: Lista de lo que no incluye
   - **Itinerario**: Descripción detallada día a día

4. Asigna **Categorías** y **Destinos**

### Configurar Menús

1. Ve a **Apariencia > Menús**
2. Crea un menú para "Menú Principal"
3. Agrega elementos:
   - Inicio
   - Tours (enlace a archivo de tours)
   - Categorías de tours
   - Blog
   - Contacto

### Configurar Widgets

1. Ve a **Apariencia > Widgets**
2. Arrastra widgets a las áreas disponibles:
   - **Sidebar Principal**: Aparece en blog y páginas
   - **Footer Widgets**: Aparecen en el pie de página

## 🎨 Personalización

### Colores y Estilos

El tema usa un sistema de colores definido en `tailwind.config.js`:

```javascript
colors: {
  primary: { /* Azul */ },
  secondary: { /* Verde */ },
  accent: { /* Naranja */ }
}
```

### Componentes Principales

- `.travel-card`: Tarjetas de tours
- `.hero-travel`: Sección hero
- `.btn-book-now`: Botón de reserva
- `.destination-grid`: Grid de destinos

### Hooks Disponibles

```php
// Después de setup del tema
do_action('travel_vibes_after_setup');

// Antes de mostrar tours
do_action('travel_vibes_before_tours');

// Después de mostrar tours
do_action('travel_vibes_after_tours');
```

## 📱 Páginas Incluidas

### Templates Principales

- `index.php` - Página principal y blog
- `single-tours.php` - Página individual de tour
- `archive-tours.php` - Archivo de todos los tours
- `taxonomy-tour_category.php` - Categorías de tours
- `header.php` - Cabecera del sitio
- `footer.php` - Pie de página
- `sidebar.php` - Barra lateral

### Funcionalidades JavaScript

- **Menú móvil** responsive
- **Búsqueda desplegable**
- **Filtros de tours** dinámicos
- **Sistema de favoritos**
- **Formularios AJAX**
- **Lightbox para imágenes**
- **Scroll suave**
- **Back to top**

## 🛠️ Desarrollo

### Estructura de Archivos

```
travel-vibes/
├── style.css                 # CSS principal (compilado)
├── index.php                 # Template principal
├── functions.php             # Funciones del tema
├── header.php               # Cabecera
├── footer.php               # Pie de página
├── sidebar.php              # Barra lateral
├── single-tours.php         # Tour individual
├── archive-tours.php        # Archivo de tours
├── assets/
│   ├── js/
│   │   └── main.js         # JavaScript principal
│   ├── css/
│   └── images/
├── src/
│   └── tailwind.css        # Archivo fuente de Tailwind
├── template-parts/          # Partes de templates
├── inc/                     # Archivos de funciones
├── languages/               # Archivos de traducción
├── package.json             # Dependencias npm
├── tailwind.config.js       # Configuración Tailwind
├── postcss.config.js        # Configuración PostCSS
└── README.md
```

### Scripts NPM

```bash
# Desarrollo con watch
npm run dev

# Build de producción
npm run build

# Linting CSS
npm run lint

# Formatear código
npm run format

# Limpiar node_modules
npm run clean
```

### Configuración Tailwind

El archivo `tailwind.config.js` incluye:

- **Colores personalizados** para turismo
- **Componentes** reutilizables (`.btn`, `.card`)
- **Utilidades** específicas (`.text-shadow`, `.glass`)
- **Animaciones** personalizadas
- **Responsive** breakpoints optimizados

## 🌐 Internacionalización

El tema está preparado para traducciones:

- **Text Domain**: `travel-vibes`
- **Archivos .pot** incluidos en `/languages/`
- **Funciones** `__()` y `_e()` implementadas

### Idiomas Soportados

- **Español** (por defecto)
- **Inglés** (preparado)

## 🤝 Soporte y Contribuciones

### Reportar Problemas

Si encuentras algún problema:

1. Revisa los [issues existentes](https://github.com/peterbv/wp-tailwind-theme-travel/issues)
2. Crea un nuevo issue con:
   - Descripción del problema
   - Pasos para reproducir
   - Screenshots (si aplica)
   - Versión de WordPress y PHP

### Contribuir

1. Fork el repositorio
2. Crea una rama para tu feature (`git checkout -b feature/nueva-caracteristica`)
3. Commit tus cambios (`git commit -am 'Agregar nueva característica'`)
4. Push a la rama (`git push origin feature/nueva-caracteristica`)
5. Crea un Pull Request

## 📄 Licencia

Este tema está licenciado bajo [GPL v2 o posterior](https://www.gnu.org/licenses/gpl-2.0.html).

## 🙏 Créditos

### Frameworks y Librerías

- [Tailwind CSS](https://tailwindcss.com/) - Framework CSS
- [WordPress](https://wordpress.org/) - CMS
- [Lucide Icons](https://lucide.dev/) - Iconos SVG

### Inspiración

- Diseños modernos de agencias de viajes
- Mejores prácticas de UX/UI para turismo
- Tendencias de diseño web 2024

## 📧 Contacto

- **Autor**: Peter BV
- **Email**: [tu-email@ejemplo.com](mailto:tu-email@ejemplo.com)
- **GitHub**: [@peterbv](https://github.com/peterbv)
- **Website**: [tu-website.com](https://tu-website.com)

---

**Travel Vibes Theme** - Transformando la manera en que las agencias de viajes se presentan en línea ✈️🌍

¿Te gusta este tema? ¡Dale una ⭐ en GitHub!