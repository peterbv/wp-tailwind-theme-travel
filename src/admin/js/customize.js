/**
 * Archivo JavaScript para la vista previa del Customizer
 *
 * Este archivo maneja las actualizaciones en tiempo real en el Customizer
 * cuando el usuario cambia las opciones del tema.
 */
(function ($) {
  "use strict";

  // Actualizar el color principal en tiempo real
  wp.customize("cta_button_color", function (value) {
    value.bind(function (newColor) {
      // Calcular un color más oscuro para hover
      const darkerColor = adjustBrightness(newColor, -20);

      // Actualizar las variables CSS
      document.documentElement.style.setProperty("--color-primary", newColor);
      document.documentElement.style.setProperty(
        "--color-primary-dark",
        darkerColor
      );

      // Actualizar elementos específicos si es necesario
      $(".bg-primary").css("background-color", newColor);
      $(".text-primary").css("color", newColor);
      $(".border-primary").css("border-color", newColor);

      // Actualizar elementos del menú
      $(
        ".main-navigation .menu-item > a:hover, .main-navigation .menu-item > a:focus, .main-navigation .current-menu-item > a"
      ).css("color", newColor);
      $(".main-navigation .menu-item > a::after").css(
        "background-color",
        newColor
      );
    });
  });

  // Mostrar/ocultar barra superior
  wp.customize("show_topbar", function (value) {
    value.bind(function (show) {
      if (show) {
        $(".topbar").removeClass("hidden");
      } else {
        $(".topbar").addClass("hidden");
      }
    });
  });

  // Email de contacto
  wp.customize("contact_email", function (value) {
    value.bind(function (newEmail) {
      $(".topbar-left a")
        .text(newEmail)
        .attr("href", "mailto:" + newEmail);
    });
  });

  // Horario de negocio
  wp.customize("business_hours", function (value) {
    value.bind(function (newHours) {
      $(".topbar-right .mr-4 span").text(newHours);
    });
  });

  // Texto del logo
  wp.customize("logo_text", function (value) {
    value.bind(function (newText) {
      if (newText) {
        $(".site-title a span:first-child").text(newText);
      } else {
        // Restaurar al nombre del sitio
        $(".site-title a span:first-child").text(wpData.siteName);
      }
    });
  });

  // Texto de eslogan
  wp.customize("tagline_text", function (value) {
    value.bind(function (newText) {
      if ($(".site-title a span.ml-2").length > 0) {
        if (newText) {
          $(".site-title a span.ml-2").text(newText).removeClass("hidden");
        } else {
          $(".site-title a span.ml-2").addClass("hidden");
        }
      } else if (newText) {
        $(".site-title a").append(
          '<span class="ml-2 text-sm text-gray-500">' + newText + "</span>"
        );
      }
    });
  });

  // Mostrar/ocultar botón CTA
  wp.customize("show_cta_button", function (value) {
    value.bind(function (show) {
      if (show) {
        $(".main-navigation .ml-8").removeClass("hidden");
        $("#mobile-menu .mt-4").removeClass("hidden");
      } else {
        $(".main-navigation .ml-8").addClass("hidden");
        $("#mobile-menu .mt-4").addClass("hidden");
      }
    });
  });

  // Texto del botón CTA
  wp.customize("cta_button_text", function (value) {
    value.bind(function (newText) {
      $(".main-navigation .ml-8 a, #mobile-menu .mt-4 a").text(newText);
    });
  });

  // URL del botón CTA
  wp.customize("cta_button_url", function (value) {
    value.bind(function (newUrl) {
      $(".main-navigation .ml-8 a, #mobile-menu .mt-4 a").attr("href", newUrl);
    });
  });

  // Actualizar redes sociales
  const updateSocialIcon = function (network, newUrl) {
    const $icon = $(`.social-icons a[aria-label="${network}"]`);

    if (newUrl) {
      if ($icon.length > 0) {
        $icon.attr("href", newUrl);
      } else {
        // Crear el ícono si no existe
        const svgIcon = getSocialSvg(network);
        if (svgIcon) {
          $(".social-icons").append(
            `<a href="${newUrl}" target="_blank" rel="noopener noreferrer" class="text-gray-600 hover:text-primary" aria-label="${network}">${svgIcon}</a>`
          );
        }
      }
    } else {
      $icon.remove();
    }
  };

  // Facebook
  wp.customize("social_facebook", function (value) {
    value.bind(function (newUrl) {
      updateSocialIcon("Facebook", newUrl);
    });
  });

  // Pinterest
  wp.customize("social_pinterest", function (value) {
    value.bind(function (newUrl) {
      updateSocialIcon("Pinterest", newUrl);
    });
  });

  // Vimeo
  wp.customize("social_vimeo", function (value) {
    value.bind(function (newUrl) {
      updateSocialIcon("Vimeo", newUrl);
    });
  });

  // Funciones auxiliares

  /**
   * Ajusta el brillo de un color hexadecimal
   * @param {string} hex Color en formato hexadecimal
   * @param {number} percent Porcentaje de ajuste (-100 a 100)
   * @return {string} Color hexadecimal ajustado
   */
  function adjustBrightness(hex, percent) {
    // Eliminar # si está presente
    hex = hex.replace(/^\s*#|\s*$/g, "");

    // Convertir valores abreviados (p.ej., "e0f" a "ee00ff")
    if (hex.length === 3) {
      hex = hex.replace(/(.)/g, "$1$1");
    }

    // Convertir a RGB
    var r = parseInt(hex.substring(0, 2), 16);
    var g = parseInt(hex.substring(2, 4), 16);
    var b = parseInt(hex.substring(4, 6), 16);

    // Ajustar brillo
    r = Math.max(0, Math.min(255, r + Math.floor((percent / 100) * 255)));
    g = Math.max(0, Math.min(255, g + Math.floor((percent / 100) * 255)));
    b = Math.max(0, Math.min(255, b + Math.floor((percent / 100) * 255)));

    // Convertir de nuevo a hexadecimal
    return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
  }

  /**
   * Obtiene el SVG para un ícono de red social
   * @param {string} network Nombre de la red social
   * @return {string} Código SVG
   */
  function getSocialSvg(network) {
    const icons = {
      Facebook:
        '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>',
      Pinterest:
        '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 0a12 12 0 00-4.373 23.182c-.017-.319-.031-.878.068-1.257.091-.345.601-2.202.601-2.202s-.152-.305-.152-.756c0-.709.411-1.238.926-1.238.436 0 .646.327.646.72 0 .437-.276 1.092-.421 1.7-.12.508.254.921.752.921.902 0 1.596-.958 1.596-2.344 0-1.223-.871-2.191-2.109-2.191-1.439 0-2.281 1.088-2.281 2.211 0 .438.138.903.358 1.157.033.045.042.083.033.121l-.141.583c-.021.09-.071.109-.164.066-.615-.292-.997-1.199-.997-1.925 0-1.53 1.098-2.957 3.148-2.957 1.656 0 2.946 1.25 2.946 2.915 0 1.748-1.09 3.129-2.6 3.129-.522 0-.941-.269-1.099-.63l-.299 1.136c-.109.42-.399.943-.594 1.263.447.132.932.205 1.429.205 2.762 0 5.222-1.283 6.826-3.284C20.719 15.221 22 12.754 22 10c0-5.514-4.486-10-10-10z" /></svg>',
      Vimeo:
        '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M23.977 6.416c-.105 2.338-1.739 5.543-4.894 9.609-3.268 4.247-6.026 6.37-8.29 6.37-1.409 0-2.578-1.294-3.553-3.881L5.322 11.4C4.603 8.816 3.834 7.522 3.01 7.522c-.179 0-.806.378-1.881 1.132L0 7.197c1.185-1.044 2.351-2.084 3.501-3.128C5.08 2.701 6.266 1.984 7.055 1.91c1.867-.18 3.016 1.1 3.447 3.838.465 2.953.789 4.789.971 5.507.539 2.45 1.131 3.674 1.776 3.674.502 0 1.256-.796 2.265-2.385 1.004-1.589 1.54-2.797 1.612-3.628.144-1.371-.395-2.061-1.614-2.061-.574 0-1.167.121-1.777.391 1.186-3.868 3.434-5.757 6.762-5.637 2.473.06 3.628 1.664 3.493 4.797l-.013 0z"/></svg>',
    };

    return icons[network] || "";
  }
  // Título del CTA en página de servicios
  wp.customize("services_archive_cta_title", function (value) {
    value.bind(function (newTitle) {
      $(".cta-banner .max-w-2xl h2").text(newTitle);
    });
  });

  // Texto del CTA en página de servicios
  wp.customize("services_archive_cta_text", function (value) {
    value.bind(function (newText) {
      $(".cta-banner .max-w-2xl p").text(newText);
    });
  });

  // Texto del botón CTA en página de servicios
  wp.customize("services_archive_cta_button_text", function (value) {
    value.bind(function (newButtonText) {
      $(".cta-banner .max-w-2xl a").text(newButtonText);
    });
  });

  // Mostrar/ocultar formulario de reserva
  wp.customize("show_services_booking_form", function (value) {
    value.bind(function (show) {
      if (show) {
        $(".wptbt-booking-wrapper").removeClass("hidden");
      } else {
        $(".wptbt-booking-wrapper").addClass("hidden");
      }
    });
  });

  // Título del formulario de reserva
  wp.customize("services_booking_form_title", function (value) {
    value.bind(function (newTitle) {
      $(".wptbt-booking-container h2").text(newTitle);
    });
  });

  // Subtítulo del formulario de reserva
  wp.customize("services_booking_form_subtitle", function (value) {
    value.bind(function (newSubtitle) {
      $(".wptbt-booking-container .font-medium.mb-2").text(newSubtitle);
    });
  });

  // Descripción del formulario de reserva
  wp.customize("services_booking_form_description", function (value) {
    value.bind(function (newDescription) {
      $(".wptbt-booking-container .text-gray-200.text-lg").text(newDescription);
    });
  });

  // Color de acento del formulario
  wp.customize("services_booking_form_accent_color", function (value) {
    value.bind(function (newColor) {
      // Botones y elementos con el color de acento
      $(".wptbt-booking-container .bg-accent").css(
        "background-color",
        newColor
      );
      $(".wptbt-booking-container .text-accent").css("color", newColor);
      $(".wptbt-booking-container .border-accent").css(
        "border-color",
        newColor
      );

      // Actualizar el estilo inline
      let style = $(".wptbt-booking-container").attr("style");
      if (style) {
        style = style.replace(
          /--color-accent:[^;]+;/,
          `--color-accent: ${newColor};`
        );
        $(".wptbt-booking-container").attr("style", style);
      }
    });
  });

  // Mostrar/ocultar CTA banner
  wp.customize("show_services_cta", function (value) {
    value.bind(function (show) {
      if (show) {
        $(".cta-banner").removeClass("hidden");
      } else {
        $(".cta-banner").addClass("hidden");
      }
    });
  });

  // Título de la página de servicios
  wp.customize("services_archive_title", function (value) {
    value.bind(function (newTitle) {
      $(".page-header .text-center h1").text(newTitle);
    });
  });

  // Subtítulo de la página de servicios
  wp.customize("services_archive_subtitle", function (value) {
    value.bind(function (newSubtitle) {
      $(".page-header .text-center p.text-xl").text(newSubtitle);
    });
  });

  // Descripción de la página de servicios
  wp.customize("services_archive_description", function (value) {
    value.bind(function (newDescription) {
      $(".page-header .prose p").text(newDescription);
    });
  });

  // Mostrar/ocultar filtros de categoría
  wp.customize("show_service_filters", function (value) {
    value.bind(function (show) {
      if (show) {
        $(".service-filters").removeClass("hidden");
      } else {
        $(".service-filters").addClass("hidden");
      }
    });
  });

  // Mostrar/ocultar onda superior
  wp.customize("services_booking_form_show_top_wave", function (value) {
    value.bind(function (show) {
      const $topWave = $(".wptbt-booking-wrapper .absolute.top-0");
      if (show) {
        $topWave.removeClass("hidden");
      } else {
        $topWave.addClass("hidden");
      }
    });
  });

  // Mostrar/ocultar onda inferior
  wp.customize("services_booking_form_show_bottom_wave", function (value) {
    value.bind(function (show) {
      const $bottomWave = $(".wptbt-booking-wrapper .absolute.bottom-0");
      if (show) {
        $bottomWave.removeClass("hidden");
      } else {
        $bottomWave.addClass("hidden");
      }
    });
  });
})(jQuery);
