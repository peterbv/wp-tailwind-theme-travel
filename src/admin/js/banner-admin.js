/**
 * JavaScript para el metabox del banner personalizado con soporte para videos de WordPress
 * Este archivo debe guardarse como assets/js/banner-admin.js
 */
(function ($) {
  "use strict";

  // Cuando el DOM esté listo
  $(document).ready(function () {
    // Variables
    var mediaUploader;
    var videoUploader;
    var slidesContainer = $("#banner_slides_container");
    var slideIndex = slidesContainer.find(".banner-slide-item").length;

    // Función para actualizar los índices de los slides después de reordenar o eliminar
    function updateSlideIndexes() {
      slidesContainer.find(".banner-slide-item").each(function (idx) {
        var currentIndex = $(this).data("index");
        var newIndex = idx;

        // Actualizar el atributo data-index
        $(this).attr("data-index", newIndex);

        // Actualizar todos los inputs dentro del slide
        $(this)
          .find("input, textarea")
          .each(function () {
            var name = $(this).attr("name");
            if (name) {
              var newName = name.replace(
                /wptbt_banner_slides\[\d+\]/g,
                "wptbt_banner_slides[" + newIndex + "]"
              );
              $(this).attr("name", newName);
            }
          });
      });
    }

    // Función para añadir un nuevo slide de imagen
    function addImageSlide(attachment) {
      var template = $("#slide_template_image").html();
      var bannerMode = $('input[name="wptbt_banner_mode"]:checked').val();
      var contentStyle = bannerMode === "global" ? "display: none;" : "";

      template = template
        .replace(/\{\{index\}\}/g, slideIndex)
        .replace(/\{\{media_id\}\}/g, attachment.id)
        .replace(
          /\{\{image_url\}\}/g,
          attachment.sizes && attachment.sizes.thumbnail
            ? attachment.sizes.thumbnail.url
            : attachment.url
        )
        .replace(/\{\{content_style\}\}/g, contentStyle);

      // Eliminar el mensaje de "no hay slides" si existe
      slidesContainer.find(".banner-no-slides").remove();

      // Añadir el nuevo slide
      slidesContainer.append(template);
      slideIndex++;
    }

    // Función para añadir un nuevo slide de video por URL
    function addVideoUrlSlide(videoUrl) {
      var template = $("#slide_template_video_url").html();
      var bannerMode = $('input[name="wptbt_banner_mode"]:checked').val();
      var contentStyle = bannerMode === "global" ? "display: none;" : "";

      template = template
        .replace(/\{\{index\}\}/g, slideIndex)
        .replace(/\{\{video_url\}\}/g, videoUrl)
        .replace(/\{\{content_style\}\}/g, contentStyle);

      // Eliminar el mensaje de "no hay slides" si existe
      slidesContainer.find(".banner-no-slides").remove();

      // Añadir el nuevo slide
      slidesContainer.append(template);
      slideIndex++;
    }

    // Función para añadir un nuevo slide de video de WordPress
    function addVideoWpSlide(attachment) {
      var template = $("#slide_template_video_wp").html();
      var bannerMode = $('input[name="wptbt_banner_mode"]:checked').val();
      var contentStyle = bannerMode === "global" ? "display: none;" : "";
      var videoFilename =
        attachment.filename || attachment.url.split("/").pop();

      template = template
        .replace(/\{\{index\}\}/g, slideIndex)
        .replace(/\{\{media_id\}\}/g, attachment.id)
        .replace(/\{\{video_filename\}\}/g, videoFilename)
        .replace(/\{\{content_style\}\}/g, contentStyle);

      // Eliminar el mensaje de "no hay slides" si existe
      slidesContainer.find(".banner-no-slides").remove();

      // Añadir el nuevo slide
      slidesContainer.append(template);
      slideIndex++;
    }

    // Manejador para el botón "Añadir Imagen"
    $("#wptbt_add_image").on("click", function (e) {
      e.preventDefault();

      // Crear el uploader de medios para imágenes
      mediaUploader = wp.media({
        title: "Seleccionar imagen para el banner",
        button: {
          text: "Usar esta imagen",
        },
        multiple: false,
        library: {
          type: "image",
        },
      });

      // Cuando se seleccione la imagen
      mediaUploader.on("select", function () {
        var attachment = mediaUploader
          .state()
          .get("selection")
          .first()
          .toJSON();
        if (attachment.type === "image") {
          addImageSlide(attachment);
        }
      });

      // Abrir el selector de medios
      mediaUploader.open();
    });

    // Manejador para el botón "Añadir Video de Galería"
    $("#wptbt_add_video_wp").on("click", function (e) {
      e.preventDefault();

      // Crear el uploader de medios para videos
      videoUploader = wp.media({
        title: "Seleccionar video para el banner",
        button: {
          text: "Usar este video",
        },
        multiple: false,
        library: {
          type: "video",
        },
      });

      // Cuando se seleccione el video
      videoUploader.on("select", function () {
        var attachment = videoUploader
          .state()
          .get("selection")
          .first()
          .toJSON();
        if (attachment.type === "video") {
          addVideoWpSlide(attachment);
        }
      });

      // Abrir el selector de medios
      videoUploader.open();
    });

    // Manejador para el botón "Añadir Video por URL"
    $("#wptbt_add_video_url").on("click", function (e) {
      e.preventDefault();

      // Solicitar URL de video mediante prompt
      var videoUrl = prompt(
        "Introduce la URL del video (YouTube, Vimeo, etc.):",
        ""
      );
      if (videoUrl && videoUrl.trim() !== "") {
        addVideoUrlSlide(videoUrl.trim());
      }
    });

    // Eliminar slide
    slidesContainer.on("click", ".slide-remove", function () {
      $(this).closest(".banner-slide-item").remove();
      updateSlideIndexes();

      // Si no quedan slides, mostrar mensaje
      if (slidesContainer.find(".banner-slide-item").length === 0) {
        slidesContainer.html(
          '<div class="banner-no-slides"><p>No hay slides configurados. Añade una imagen o video.</p></div>'
        );
      }
    });

    // Editar slide (mostrar/ocultar la sección de contenido)
    slidesContainer.on("click", ".slide-edit", function () {
      var slideItem = $(this).closest(".banner-slide-item");
      var slideContent = slideItem.find(".slide-content");

      // Solo permitir edición si estamos en modo individual
      if ($('input[name="wptbt_banner_mode"]:checked').val() === "individual") {
        slideContent.slideToggle();
      } else {
        alert(
          "Para editar el contenido de slides individuales, cambia al modo 'Configurar texto individual para cada imagen/video'"
        );
      }
    });

    // Permitir reordenar slides con arrastrar y soltar
    if ($.fn.sortable) {
      slidesContainer.sortable({
        items: ".banner-slide-item",
        handle: ".slide-move",
        cursor: "move",
        opacity: 0.7,
        update: function () {
          updateSlideIndexes();
        },
      });
    }

    // Actualizar la visualización al cambiar de modo
    $('input[name="wptbt_banner_mode"]').on("change", function () {
      var mode = $(this).val();
      if (mode === "global") {
        slidesContainer.find(".slide-content").slideUp();
      } else {
        slidesContainer.find(".slide-content").slideDown();
      }
    });
  });
})(jQuery);
