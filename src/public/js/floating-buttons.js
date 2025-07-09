/**
 * Script optimizado para los botones flotantes del tema
 */
document.addEventListener("DOMContentLoaded", function () {
  const floatingButtonsContainer = document.querySelector(".floating-buttons");

  // Si no hay botones flotantes en la página, salir
  if (!floatingButtonsContainer) return;

  // Obtener todos los botones flotantes
  const floatingButtons =
    floatingButtonsContainer.querySelectorAll(".floating-button");

  // Configuración de la animación para la aparición de los botones
  const buttonAppearClass = "scale-100";
  const buttonDisappearClass = "scale-0";
  const buttonAppearDelay = 100; // ms entre cada botón

  /**
   * Función para mostrar los botones con efecto secuencial
   */
  function animateButtonsAppearance() {
    // Inicialmente ocultar todos los botones
    floatingButtons.forEach((button) => {
      button.classList.add(buttonDisappearClass);
      button.classList.remove(buttonAppearClass);
    });

    // Mostrar cada botón con retraso secuencial
    floatingButtons.forEach((button, index) => {
      setTimeout(
        () => {
          button.classList.remove(buttonDisappearClass);
          button.classList.add(buttonAppearClass);
        },
        buttonAppearDelay * (index + 1)
      );
    });
  }

  /**
   * Mejora la interacción con los tooltips de los botones
   */
  function setupButtonInteractions() {
    floatingButtons.forEach((button) => {
      const tooltip = button.querySelector(".floating-button-text");

      if (tooltip) {
        // Eventos de ratón
        button.addEventListener("mouseenter", () => {
          tooltip.classList.remove("opacity-0");
          tooltip.classList.add("opacity-100");
        });

        button.addEventListener("mouseleave", () => {
          tooltip.classList.remove("opacity-100");
          tooltip.classList.add("opacity-0");
        });

        // Eventos de teclado para accesibilidad
        button.addEventListener("focus", () => {
          tooltip.classList.remove("opacity-0");
          tooltip.classList.add("opacity-100");
        });

        button.addEventListener("blur", () => {
          tooltip.classList.remove("opacity-100");
          tooltip.classList.add("opacity-0");
        });
      }
    });
  }

  /**
   * Controla la visibilidad de los botones según el scroll
   */
  function setupScrollBehavior() {
    let lastScrollTop = 0;
    let isVisible = true;
    const scrollThreshold = 1000; // px - umbral para ocultar/mostrar

    window.addEventListener("scroll", () => {
      const scrollTop =
        window.pageYOffset || document.documentElement.scrollTop;

      // Mostrar botones después de cierto scroll desde el inicio
      if (scrollTop > 300 && !isVisible) {
        floatingButtonsContainer.classList.remove(
          "translate-y-20",
          "opacity-0"
        );
        isVisible = true;
        animateButtonsAppearance();
      }
      // Ocultar botones cuando se está cerca del inicio de la página
      else if (scrollTop <= 300 && isVisible) {
        floatingButtonsContainer.classList.add("translate-y-20", "opacity-0");
        isVisible = false;
      }

      // Opcional: ocultar al hacer scroll rápido hacia abajo
      if (scrollTop > scrollThreshold && scrollTop > lastScrollTop + 50) {
        floatingButtonsContainer.classList.add("translate-y-20", "opacity-0");
        isVisible = false;
      } else if (
        scrollTop > scrollThreshold &&
        scrollTop < lastScrollTop - 50
      ) {
        floatingButtonsContainer.classList.remove(
          "translate-y-20",
          "opacity-0"
        );
        isVisible = true;
        animateButtonsAppearance();
      }

      lastScrollTop = scrollTop;
    });
  }

  // Inicializar funcionalidades
  animateButtonsAppearance();
  setupButtonInteractions();

  // Solo activar comportamiento de scroll si está habilitado
  if (floatingButtonsContainer.hasAttribute("data-scroll-behavior")) {
    setupScrollBehavior();
  }

  // Añadir capacidad de ocultar todos los botones con un clic (opcional)
  const closeButton = floatingButtonsContainer.querySelector(
    ".floating-buttons-close"
  );
  if (closeButton) {
    closeButton.addEventListener("click", () => {
      floatingButtonsContainer.classList.add("translate-y-20", "opacity-0");

      // Guardar preferencia del usuario con sessionStorage
      sessionStorage.setItem("floatingButtonsHidden", "true");

      // Prevenir que vuelvan a aparecer en esta sesión
      window.removeEventListener("scroll", setupScrollBehavior);
    });

    // Verificar si el usuario los ocultó previamente
    if (sessionStorage.getItem("floatingButtonsHidden") === "true") {
      floatingButtonsContainer.classList.add("translate-y-20", "opacity-0");
    }
  }
});
