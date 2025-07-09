/**
 * JavaScript optimizado para la navegación del tema de spa
 */
document.addEventListener("DOMContentLoaded", () => {
  // Elementos del DOM
  const menuToggle = document.getElementById("mobile-menu-toggle");
  const mobileMenu = document.getElementById("mobile-menu");
  const siteHeader = document.getElementById("masthead");
  const searchToggle = document.getElementById("search-toggle");
  const mobileSearchToggle = document.getElementById("mobile-search-toggle");
  const searchModal = document.getElementById("search-modal");
  const searchInput = document.getElementById("search-input");

  // Estado inicial
  let lastScrollTop = 0;
  let isMenuOpen = false;
  let isSearchOpen = false;
  let headerHeight = siteHeader?.offsetHeight || 0;
  let isHeaderFixed = false;
  let paddingAdded = false;
  let scrollTimer = null;

  /**
   * Función para alternar el menú móvil con mejores transiciones
   */
  const toggleMobileMenu = () => {
    // Si la búsqueda está abierta, cerrarla primero
    if (isSearchOpen) {
      toggleSearch(false);
    }

    isMenuOpen = !isMenuOpen;

    if (isMenuOpen) {
      // Abrir menú
      mobileMenu.classList.remove("hidden");

      // Pequeño retraso para permitir la transición CSS
      requestAnimationFrame(() => {
        mobileMenu.classList.add("opacity-100");
        document.body.classList.add("mobile-menu-open");

        // Bloquear el scroll
        document.body.style.overflow = "hidden";

        // Enfoque al cerrar el menú para accesibilidad
        const firstFocusableElement = mobileMenu.querySelector("a, button");
        if (firstFocusableElement) {
          firstFocusableElement.focus();
        }
      });
    } else {
      // Cerrar menú
      mobileMenu.classList.remove("opacity-100");
      document.body.classList.remove("mobile-menu-open");

      // Desbloquear el scroll
      document.body.style.overflow = "";

      // Esperar a que termine la transición
      setTimeout(() => {
        mobileMenu.classList.add("hidden");
      }, 300);

      // Devolver el foco al botón de menú
      menuToggle.focus();
    }

    // Accesibilidad
    menuToggle.setAttribute("aria-expanded", String(isMenuOpen));

    // Cambiar icono
    const toggleIcon = menuToggle.querySelector("svg");
    if (toggleIcon) {
      toggleIcon.innerHTML = isMenuOpen
        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'
        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>';

      menuToggle.classList.toggle("text-spa-accent", isMenuOpen);
    }
  };

  /**
   * Función para alternar el modal de búsqueda
   * @param {boolean|undefined} force - Forzar un estado específico
   */
  const toggleSearch = (force) => {
    // Determinar el nuevo estado
    isSearchOpen = force !== undefined ? force : !isSearchOpen;

    // Si el menú está abierto, cerrarlo primero
    if (isMenuOpen && isSearchOpen) {
      toggleMobileMenu();
    }

    if (isSearchOpen) {
      // Mostrar modal de búsqueda
      searchModal.classList.remove("hidden");

      // Retraso para permitir animación
      requestAnimationFrame(() => {
        searchModal.classList.add("opacity-100");
        searchModal.classList.remove("transform", "-translate-y-4");

        // Enfocar el campo de búsqueda
        setTimeout(() => {
          searchInput.focus();
        }, 100);
      });
    } else {
      // Ocultar modal de búsqueda
      searchModal.classList.remove("opacity-100");
      searchModal.classList.add("transform", "-translate-y-4");

      // Esperar a que termine la transición
      setTimeout(() => {
        searchModal.classList.add("hidden");
      }, 300);
    }

    // Accesibilidad
    if (searchToggle) {
      searchToggle.setAttribute("aria-expanded", String(isSearchOpen));
    }
    if (mobileSearchToggle) {
      mobileSearchToggle.setAttribute("aria-expanded", String(isSearchOpen));
    }
  };

  /**
   * Configurar submenús en móvil con mejor accesibilidad
   */
  const setupMobileSubmenus = () => {
    if (!mobileMenu) return;

    const menuItems = mobileMenu.querySelectorAll(".menu-item-has-children");

    menuItems.forEach((menuItem, index) => {
      // Solo agregar botones si no existen ya
      if (!menuItem.querySelector(".submenu-expand")) {
        const link = menuItem.querySelector("a");
        const subMenu = menuItem.querySelector(".sub-menu");
        const submenuId = `mobile-submenu-${index}`;

        // Establecer ID para el submenú para accesibilidad
        if (subMenu) {
          subMenu.id = submenuId;
        }

        // Crear botón de expansión con mejores atributos ARIA
        const expandBtn = document.createElement("button");
        expandBtn.className = "submenu-expand";
        expandBtn.setAttribute(
          "aria-label",
          `Expandir submenú: ${link.textContent}`
        );
        expandBtn.setAttribute("aria-expanded", "false");
        expandBtn.setAttribute("aria-controls", submenuId);
        expandBtn.innerHTML = `
          <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        `;

        // Insertar después del enlace
        link.after(expandBtn);

        // Evento para expandir/contraer
        expandBtn.addEventListener("click", (e) => {
          e.preventDefault();
          e.stopPropagation();

          const isExpanded = subMenu.classList.contains("active");

          // Actualizar clases y atributos
          subMenu.classList.toggle("active", !isExpanded);
          expandBtn.classList.toggle("expanded", !isExpanded);
          expandBtn.setAttribute("aria-expanded", String(!isExpanded));

          // Rotar ícono con mayor suavidad
          const icon = expandBtn.querySelector("svg");
          if (icon) {
            icon.style.transform = !isExpanded
              ? "rotate(180deg)"
              : "rotate(0deg)";
            icon.style.transition = "transform 0.3s ease";
          }
        });
      }
    });
  };

  /**
   * Función para manejar el header sticky con diferentes comportamientos
   * según las opciones del customizer
   */
  const handleStickyHeader = () => {
    if (!siteHeader) return;

    // Obtener el comportamiento configurado desde los datos del tema
    // Esta variable debe ser pasada desde PHP a JavaScript
    const headerBehavior = wpData?.headerScrollBehavior || "auto_hide";
    const scrollTop = window.scrollY || document.documentElement.scrollTop;
    const scrollingDown = scrollTop > lastScrollTop;
    const scrollDelta = Math.abs(scrollTop - lastScrollTop);
    const topbar = document.querySelector(".topbar");

    // Solo procesar si el cambio de scroll es significativo para mejor rendimiento
    if (scrollDelta < 5 && isHeaderFixed) {
      lastScrollTop = scrollTop;
      return;
    }

    // Fijar el header después de scroll (para todos los comportamientos)
    if (scrollTop > headerHeight && !isHeaderFixed) {
      siteHeader.classList.add("fixed");

      // Añadir padding al body para evitar saltos
      if (!paddingAdded) {
        document.body.style.paddingTop = `${headerHeight}px`;
        paddingAdded = true;
      }

      isHeaderFixed = true;

      // Para modo compacto, añadir la clase de compacto
      if (headerBehavior === "compact") {
        siteHeader.classList.add("compact-mode");
      }
    }
    // Quitar fijación cuando se regresa al inicio
    else if (scrollTop <= 10 && isHeaderFixed) {
      siteHeader.classList.remove("fixed");
      siteHeader.classList.remove("compact-mode");
      document.body.style.paddingTop = "0";
      paddingAdded = false;
      isHeaderFixed = false;
    }

    // Aplicar comportamientos específicos cuando el header está fijo
    if (isHeaderFixed) {
      switch (headerBehavior) {
        case "auto_hide":
          // Ocultar al bajar después de cierta distancia y velocidad
          if (
            scrollingDown &&
            scrollTop > headerHeight * 2 &&
            scrollDelta > 10
          ) {
            siteHeader.classList.add("is-hidden");

            // Si la búsqueda está abierta, cerrarla
            if (isSearchOpen) {
              toggleSearch(false);
            }
          } else if (!scrollingDown || scrollDelta > 30) {
            // Mostrar al subir o con cambios grandes de scroll
            siteHeader.classList.remove("is-hidden");
          }
          break;

        case "always_visible":
          // Siempre visible - asegurarnos de que nunca tenga la clase is-hidden
          siteHeader.classList.remove("is-hidden");
          break;

        case "hide_topbar":
          // Solo ocultar la topbar al hacer scroll
          if (topbar && scrollingDown && scrollTop > headerHeight) {
            topbar.classList.add("is-hidden");
          } else if (topbar && (!scrollingDown || scrollTop <= headerHeight)) {
            topbar.classList.remove("is-hidden");
          }
          break;

        case "compact":
          // Ya aplicamos la clase compact-mode arriba, no hacer nada más aquí
          break;
      }
    }

    lastScrollTop = scrollTop;

    // Limpiar el temporizador anterior y establecer uno nuevo para optimización
    if (scrollTimer) {
      clearTimeout(scrollTimer);
    }

    scrollTimer = setTimeout(() => {
      // Si el usuario dejó de hacer scroll por un momento, mostrar el header
      // (Excepto en modo hide_topbar, donde la topbar permanece oculta)
      if (isHeaderFixed && headerBehavior !== "hide_topbar") {
        siteHeader.classList.remove("is-hidden");
      }
    }, 1500);
  };

  /**
   * Añadir efecto hover a los elementos del menú con animación mejorada
   */
  const addMenuHoverEffects = () => {
    const menuLinks = document.querySelectorAll(
      ".main-navigation > ul > li > a"
    );

    menuLinks.forEach((link) => {
      // Solo agregar si no existe ya
      if (!link.querySelector(".menu-underline")) {
        // Asegurarse que el enlace tenga posición relativa
        link.style.position = "relative";
        link.style.display = "inline-block";

        // Marcar elementos activos
        const isActive =
          link.classList.contains("active") ||
          link.parentElement.classList.contains("current-menu-item") ||
          link.parentElement.classList.contains("current-menu-ancestor");

        if (isActive) {
          link.classList.add("active-menu-item");
        }
      }
    });
  };

  /**
   * Manejo de eventos de teclado para accesibilidad mejorada
   */
  const setupKeyboardNavigation = () => {
    // Cerrar menú/búsqueda con Escape
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") {
        if (isMenuOpen) {
          toggleMobileMenu();
        }
        if (isSearchOpen) {
          toggleSearch(false);
        }
      }
    });

    // Permitir navegación con teclado en submenús
    const menuItems = document.querySelectorAll(".menu-item-has-children");

    menuItems.forEach((menuItem) => {
      const link = menuItem.querySelector("a");
      const submenu = menuItem.querySelector(".sub-menu");

      if (link && submenu) {
        // Abrir submenú con Enter en elemento de menú principal (para desktop)
        link.addEventListener("keydown", (e) => {
          if (e.key === "Enter" || e.key === " ") {
            // Verificar si estamos en viewport móvil donde no usamos hover
            const isMobile = window.innerWidth < 768;

            if (!isMobile) {
              e.preventDefault();

              // Simular hover para mostrar submenú
              submenu.style.opacity = "1";
              submenu.style.visibility = "visible";
              submenu.style.transform = "translateY(0)";

              // Enfocar el primer elemento del submenú
              const firstSubmenuLink = submenu.querySelector("a");
              if (firstSubmenuLink) {
                firstSubmenuLink.focus();
              }
            }
          }
        });

        // Gestionar navegación con teclado dentro del submenú
        const submenuLinks = submenu.querySelectorAll("a");

        submenuLinks.forEach((submenuLink, index) => {
          submenuLink.addEventListener("keydown", (e) => {
            // Cerrar submenú con Escape
            if (e.key === "Escape") {
              link.focus();

              // Solo cerrar si estamos en desktop
              if (window.innerWidth >= 768) {
                submenu.style.opacity = "";
                submenu.style.visibility = "";
                submenu.style.transform = "";
              }
            }

            // Ir al siguiente/anterior elemento del submenú
            if (e.key === "ArrowDown" || e.key === "ArrowUp") {
              e.preventDefault();

              const direction = e.key === "ArrowDown" ? 1 : -1;
              const nextIndex =
                (index + direction + submenuLinks.length) % submenuLinks.length;
              submenuLinks[nextIndex].focus();
            }
          });
        });
      }
    });
  };

  /**
   * Detectar click fuera para cerrar menús
   */
  const setupClickOutside = () => {
    document.addEventListener("click", (e) => {
      // Cerrar búsqueda al hacer clic fuera
      if (
        isSearchOpen &&
        searchModal &&
        !searchModal.contains(e.target) &&
        e.target !== searchToggle &&
        e.target !== mobileSearchToggle &&
        !searchToggle?.contains(e.target) &&
        !mobileSearchToggle?.contains(e.target)
      ) {
        toggleSearch(false);
      }

      // No cerramos el menú móvil con clicks fuera porque ya tiene overlay
    });
  };

  /**
   * Cerrar menús al cambiar de tamaño de ventana
   */
  const handleWindowResize = () => {
    // Actualizar altura del header si no está fijo
    if (!isHeaderFixed && siteHeader) {
      headerHeight = siteHeader.offsetHeight;
    }

    // Cerrar menú móvil en cambio de tamaño a desktop
    if (window.innerWidth >= 768 && isMenuOpen) {
      toggleMobileMenu();
    }

    // Cerrar búsqueda al cambiar tamaño
    if (isSearchOpen) {
      toggleSearch(false);
    }
  };

  /**
   * Inicialización y eventos
   */
  const init = () => {
    // Solo inicializar si existen los elementos necesarios
    if (!siteHeader) return;

    // Guardar altura inicial del header
    headerHeight = siteHeader.offsetHeight;

    // Event listeners para el menú móvil
    if (menuToggle && mobileMenu) {
      menuToggle.addEventListener("click", toggleMobileMenu);
      document.getElementById("book_now_botton").addEventListener("click", toggleMobileMenu);
    }

    // Event listeners para la búsqueda
    if (searchToggle) {
      searchToggle.addEventListener("click", () => toggleSearch());
    }

    if (mobileSearchToggle) {
      mobileSearchToggle.addEventListener("click", () => toggleSearch());
    }

    // Event listeners para scroll y resize
    window.addEventListener("scroll", handleStickyHeader, { passive: true });
    window.addEventListener("resize", handleWindowResize);

    // Inicializar funcionalidades
    setupMobileSubmenus();
    addMenuHoverEffects();
    setupKeyboardNavigation();
    setupClickOutside();

    // Aplicar estado inicial del header al cargar
    handleStickyHeader();
  };

  // Iniciar todo el sistema de navegación
  init();
});
