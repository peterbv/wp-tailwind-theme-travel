document.addEventListener("DOMContentLoaded", function () {
  // Seleccionar elementos con la clase reveal-item
  const revealItems = document.querySelectorAll(".reveal-item");

  if (!revealItems.length) return;

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          // Pequeño retraso para prevenir parpadeos
          setTimeout(() => {
            // Añadir la clase revealed cuando el elemento es visible
            entry.target.classList.add("revealed");
          }, 50);

          observer.unobserve(entry.target);
        }
      });
    },
    {
      rootMargin: "0px 0px -10% 0px",
      threshold: 0.1,
    }
  );

  // Observar todos los elementos
  revealItems.forEach((item) => {
    observer.observe(item);
  });

  // Activar elementos ya visibles en la carga inicial
  setTimeout(() => {
    revealItems.forEach((el) => {
      const rect = el.getBoundingClientRect();
      if (rect.top < window.innerHeight) {
        el.classList.add("revealed");
        observer.unobserve(el);
      }
    });
  }, 100);
});

/**
 * JavaScript para el selector de idiomas
 * Guarda este archivo como /assets/public/js/language-switcher.js
 */
document.addEventListener("DOMContentLoaded", function () {
  // Seleccionar todos los selectores de idioma
  const languageSwitchers = document.querySelectorAll(".language-switcher");

  languageSwitchers.forEach(function (switcher) {
    const button = switcher.querySelector("button");
    const dropdown = switcher.querySelector(".absolute");

    if (!button || !dropdown) return;

    // Variable para rastrear el estado del menú
    let isOpen = false;

    // Función para mostrar el menú
    const showDropdown = () => {
      dropdown.classList.remove("opacity-0", "invisible");
      dropdown.classList.add("opacity-100");
      dropdown.style.transform = "translateY(0)";
      isOpen = true;
    };

    // Función para ocultar el menú
    const hideDropdown = () => {
      dropdown.classList.remove("opacity-100");
      dropdown.classList.add("opacity-0", "invisible");
      dropdown.style.transform = "translateY(-5px)";
      isOpen = false;
    };

    // Mostrar/ocultar en click
    button.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();

      if (isOpen) {
        hideDropdown();
      } else {
        showDropdown();
      }
    });

    // Mostrar en hover (desktop)
    if (window.innerWidth >= 768) {
      switcher.addEventListener("mouseenter", showDropdown);
      switcher.addEventListener("mouseleave", hideDropdown);
    }

    // Cerrar al hacer clic fuera
    document.addEventListener("click", function (e) {
      if (isOpen && !switcher.contains(e.target)) {
        hideDropdown();
      }
    });

    // Cerrar al presionar Escape
    document.addEventListener("keydown", function (e) {
      if (isOpen && e.key === "Escape") {
        hideDropdown();
        button.focus();
      }
    });

    // Agregar aria-attributes para accesibilidad
    button.setAttribute("aria-haspopup", "true");
    button.setAttribute("aria-expanded", "false");

    // Actualizar atributos aria cuando cambia el estado
    const updateAriaAttributes = () => {
      button.setAttribute("aria-expanded", isOpen.toString());
    };

    // Observar cambios en el estado
    const observer = new MutationObserver(updateAriaAttributes);
    observer.observe(dropdown, {
      attributes: true,
      attributeFilter: ["class"],
    });
  });
});
