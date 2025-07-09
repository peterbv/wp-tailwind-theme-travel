// src/public/js/components/solid/SimpleDatePicker.jsx
import { createSignal, onMount, onCleanup, Show } from "solid-js";

/**
 * DatePicker simplificado y optimizado con selector de mes y año
 * Versión corregida que permite seleccionar el día de HOY
 */
const SimpleDatePicker = (props) => {
  // Props con valores por defecto
  const {
    name = "date",
    id = `date-${Math.random().toString(36).substring(2, 10)}`,
    value = "",
    onChange = () => {},
    placeholder = "Seleccionar fecha",
    required = true,
    labelText = "Fecha",
    colors = {
      primary: "#4F8A8B",
      secondary: "#F7EDE2",
      accent: "#D4B254",
      dark: "#424242",
    },
    darkMode = false,
  } = props;

  // IMPORTANTE: Inicializar con estado predeterminado basado en hoy
  const today = new Date();
  today.setHours(0, 0, 0, 0); // Establecer horas a cero para comparaciones precisas
  const currentYearNum = today.getFullYear();

  // Estados locales
  const [isOpen, setIsOpen] = createSignal(false);
  const [currentMonth, setCurrentMonth] = createSignal(today.getMonth());
  const [currentYear, setCurrentYear] = createSignal(currentYearNum);
  const [selectedDate, setSelectedDate] = createSignal(
    value ? parseDate(value) : null
  );
  const [displayValue, setDisplayValue] = createSignal(value || "");

  // Estados para las vistas de selección
  const [viewMode, setViewMode] = createSignal("days"); // 'days', 'months', 'years'
  const [yearRange, setYearRange] = createSignal({
    start: currentYearNum - 6,
    end: currentYearNum + 5,
  });

  // Estados para transiciones avanzadas
  const [isTransitioning, setIsTransitioning] = createSignal(false);
  const [transitionDirection, setTransitionDirection] = createSignal("next"); // "next" o "prev" para la dirección de la animación
  const [previousView, setPreviousView] = createSignal(null); // Guarda la vista anterior durante transiciones
  const [previousViewContent, setPreviousViewContent] = createSignal(null); // Contenido de la vista anterior

  // Referencias a elementos DOM
  let containerRef;
  let calendarRef;

  // Parsear fecha de string a objeto Date
  function parseDate(dateStr) {
    try {
      if (!dateStr) return null;
      const [year, month, day] = dateStr.split("-").map(Number);
      if (isNaN(year) || isNaN(month) || isNaN(day)) return null;
      return new Date(year, month - 1, day);
    } catch (e) {
      console.error("Error al parsear fecha:", e);
      return null;
    }
  }

  // Nombres de días y meses en español
  const dayNames = ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"];
  const monthNames = [
    "Enero",
    "Febrero",
    "Marzo",
    "Abril",
    "Mayo",
    "Junio",
    "Julio",
    "Agosto",
    "Septiembre",
    "Octubre",
    "Noviembre",
    "Diciembre",
  ];

  // Nombres de meses abreviados para la vista de selección
  const shortMonthNames = [
    "Ene",
    "Feb",
    "Mar",
    "Abr",
    "May",
    "Jun",
    "Jul",
    "Ago",
    "Sep",
    "Oct",
    "Nov",
    "Dic",
  ];

  // Inicialización
  onMount(() => {
    // Si hay un valor inicial, establecerlo
    if (value) {
      try {
        const [year, month, day] = value.split("-").map(Number);
        if (!isNaN(year) && !isNaN(month) && !isNaN(day)) {
          const date = new Date(year, month - 1, day);
          setSelectedDate(date);
          setCurrentMonth(date.getMonth());
          setCurrentYear(date.getFullYear());
        }
      } catch (e) {
        console.error("Error parsing initial date:", e);
      }
    }

    // Registrar el event listener para clicks fuera
    setTimeout(() => {
      document.addEventListener("mousedown", handleOutsideClick);
    }, 100);
  });

  // Limpiar event listeners al desmontar
  onCleanup(() => {
    document.removeEventListener("mousedown", handleOutsideClick);
  });

  // Formatear fecha para display (YYYY-MM-DD)
  const formatDate = (date) => {
    if (!date) return "";
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");
    return `${year}-${month}-${day}`;
  };

  // CORREGIDO: Verificar si una fecha es pasada (permitir HOY)
  const isPastDate = (date) => {
    // Crear fechas sin horas para comparación precisa
    const dateOnly = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    const todayOnly = new Date(today.getFullYear(), today.getMonth(), today.getDate());
    
    // Solo bloquear fechas ANTERIORES a hoy (no hoy mismo)
    return dateOnly < todayOnly;
  };

  // Cerrar el calendario si se hace clic fuera
  const handleOutsideClick = (e) => {
    // Primero verificamos si el calendario está abierto, si no, no hay nada que hacer
    if (!isOpen()) return;

    // Si el clic fue dentro del calendario o el input, no hacemos nada
    if (
      (containerRef && containerRef.contains(e.target)) ||
      (calendarRef && calendarRef.contains(e.target))
    ) {
      return;
    }

    // Si llegamos aquí, el clic fue fuera - cerramos el calendario
    setIsOpen(false);

    // Resetear a la vista de días para la próxima vez que se abra
    setTimeout(() => {
      setViewMode("days");
    }, 300);
  };

  // Mostrar/ocultar el calendario
  const toggleCalendar = (e) => {
    if (e) {
      e.preventDefault();
      e.stopPropagation();
    }

    setIsOpen(!isOpen());
  };

  // Determinar la dirección de transición para navegación profunda
  const determineTransitionDirection = (currentView, newView) => {
    const viewHierarchy = { days: 0, months: 1, years: 2 };
    return viewHierarchy[newView] > viewHierarchy[currentView]
      ? "next"
      : "prev";
  };

  // Cambio de vista con transición suave y continua
  const changeViewMode = (newMode, e) => {
    if (e) {
      e.preventDefault();
      e.stopPropagation();
    }

    if (newMode === viewMode()) return;

    // Guardar la vista y contenido actuales antes de cambiar
    setPreviousView(viewMode());

    // Capturar el contenido actual según la vista
    if (viewMode() === "days") {
      setPreviousViewContent(generateDays());
    } else if (viewMode() === "months") {
      setPreviousViewContent(generateMonthSelector());
    } else if (viewMode() === "years") {
      setPreviousViewContent(generateYearSelector());
    }

    // Establecer la dirección de la transición
    setTransitionDirection(determineTransitionDirection(viewMode(), newMode));

    // Iniciar transición
    setIsTransitioning(true);

    // Cambiar inmediatamente la vista para que el nuevo contenido se prepare
    setViewMode(newMode);

    // Mantener el estado de transición por más tiempo para una animación más suave
    setTimeout(() => {
      setIsTransitioning(false);
      // Limpiar las referencias anteriores después de completar la transición
      setTimeout(() => {
        setPreviousView(null);
        setPreviousViewContent(null);
      }, 50);
    }, 300);
  };

  // Navegar al mes/año anterior o siguiente con transición continua
  const navigate = (step, e) => {
    if (e) {
      e.preventDefault();
      e.stopPropagation();
    }

    // Guardar contenido actual antes de la transición
    if (viewMode() === "days") {
      setPreviousViewContent(generateDays());
    } else if (viewMode() === "months") {
      setPreviousViewContent(generateMonthSelector());
    } else if (viewMode() === "years") {
      setPreviousViewContent(generateYearSelector());
    }

    setPreviousView(viewMode());

    // Establecer la dirección de la transición
    setTransitionDirection(step > 0 ? "next" : "prev");

    // Indicar que estamos en transición para animación
    setIsTransitioning(true);

    // Actualizamos los valores inmediatamente
    let newMonth = currentMonth();
    let newYear = currentYear();

    if (viewMode() === "days") {
      // Navegación por meses
      newMonth += step;

      if (newMonth > 11) {
        newMonth = 0;
        newYear += 1;
      } else if (newMonth < 0) {
        newMonth = 11;
        newYear -= 1;
      }

      setCurrentMonth(newMonth);
      setCurrentYear(newYear);
    } else if (viewMode() === "months") {
      // En vista de meses, navegar por años
      setCurrentYear(newYear + step);
    } else if (viewMode() === "years") {
      // En vista de años, navegar por rangos de años
      const range = yearRange();
      setYearRange({
        start: range.start + step * 12,
        end: range.end + step * 12,
      });
    }

    // Terminamos la transición después de un tiempo
    setTimeout(() => {
      setIsTransitioning(false);
      // Limpiar las referencias anteriores después de completar la transición
      setTimeout(() => {
        setPreviousView(null);
        setPreviousViewContent(null);
      }, 50);
    }, 300);
  };

  // Seleccionar un mes específico
  const selectMonth = (month, e) => {
    if (e) {
      e.preventDefault();
      e.stopPropagation();
    }

    setCurrentMonth(month);
    // Al seleccionar un mes, volvemos a la vista de días
    // Configurar transición a modo "prev" ya que vamos a un nivel menos profundo
    setTransitionDirection("prev");
    changeViewMode("days");
  };

  // Seleccionar un año específico
  const selectYear = (year, e) => {
    if (e) {
      e.preventDefault();
      e.stopPropagation();
    }

    setCurrentYear(year);
    // Al seleccionar un año, volvemos a la vista de meses
    // Configurar transición a modo "prev" ya que vamos a un nivel menos profundo
    setTransitionDirection("prev");
    changeViewMode("months");
  };

  // Cambiar a la vista de selección de mes
  const showMonthSelector = (e) => {
    if (e) {
      e.preventDefault();
      e.stopPropagation();
    }

    // Al ir a meses, usamos "next" porque vamos a un nivel más profundo
    setTransitionDirection("next");
    changeViewMode("months");
  };

  // Cambiar a la vista de selección de año
  const showYearSelector = (e) => {
    if (e) {
      e.preventDefault();
      e.stopPropagation();
    }

    // Ajustar el rango de años para que el año actual esté en el centro
    const year = currentYear();
    setYearRange({
      start: year - 6,
      end: year + 5,
    });

    // Al ir a años, usamos "next" porque vamos a un nivel más profundo
    // Si ya estamos en la vista de meses, es "next", si no, es una configuración específica
    setTransitionDirection(viewMode() === "months" ? "next" : "next");
    changeViewMode("years");
  };

  // Seleccionar una fecha
  const selectDate = (day, e) => {
    if (e) {
      e.preventDefault();
      e.stopPropagation();
    }

    try {
      // Crear objeto Date con el día seleccionado
      const date = new Date(currentYear(), currentMonth(), day);

      // Verificar si es una fecha pasada (ahora permite HOY)
      if (isPastDate(date)) {
        return; // Solo bloqueamos fechas ANTERIORES a hoy
      }

      // Formatear para display y envío
      const formattedDate = formatDate(date);

      // Actualizar estados internos
      setSelectedDate(date);
      setDisplayValue(formattedDate);

      // Notificar al componente padre
      if (typeof onChange === "function") {
        onChange({
          target: {
            name,
            value: formattedDate,
          },
        });
      }

      // Solo cerramos el calendario cuando se selecciona un día específico
      setIsOpen(false);

      // Resetear a la vista de días para la próxima vez
      setTimeout(() => {
        setViewMode("days");
      }, 300);
    } catch (error) {
      console.error("Error al seleccionar fecha:", error);
    }
  };

  // Generar la vista de meses para selección
  const generateMonthSelector = () => {
    const months = [];

    // Obtener el mes y año actuales para comparación
    const thisMonth = today.getMonth();
    const thisYear = today.getFullYear();

    for (let i = 0; i < 12; i++) {
      const isCurrentMonth = i === thisMonth && currentYear() === thisYear;
      const isSelected =
        selectedDate() &&
        i === selectedDate().getMonth() &&
        currentYear() === selectedDate().getFullYear();

      // CORREGIDO: Determinar si este mes es pasado (permitir mes actual)
      const isPastMonth = currentYear() === thisYear && i < thisMonth;

      months.push(
        <button
          type="button"
          disabled={isPastMonth}
          class={`p-3 rounded flex items-center justify-center transition-colors duration-150 
                 ${
                   isPastMonth
                     ? `${
                         darkMode ? "text-gray-600" : "text-gray-300"
                       } cursor-not-allowed`
                     : darkMode
                     ? "hover:bg-gray-700"
                     : "hover:bg-gray-100"
                 } 
                 ${
                   isSelected
                     ? `bg-${colors.accent.replace(
                         "#",
                         ""
                       )} text-white font-bold`
                     : ""
                 }
                 ${
                   isCurrentMonth && !isSelected && !isPastMonth
                     ? "font-semibold border border-gray-300"
                     : ""
                 }`}
          onClick={(e) => !isPastMonth && selectMonth(i, e)}
          onMouseDown={(e) => e.stopPropagation()}
          style={
            isSelected ? { backgroundColor: colors.accent, color: "white" } : {}
          }
        >
          {shortMonthNames[i]}
        </button>
      );
    }

    return <div class="grid grid-cols-3 gap-2 p-3">{months}</div>;
  };

  // Generar la vista de años para selección
  const generateYearSelector = () => {
    const years = [];
    const range = yearRange();
    const thisYear = today.getFullYear();

    for (let year = range.start; year <= range.end; year++) {
      const isCurrentYear = year === thisYear;
      const isSelected =
        selectedDate() && year === selectedDate().getFullYear();
      // CORREGIDO: Permitir el año actual
      const isPastYear = year < thisYear;

      years.push(
        <button
          type="button"
          disabled={isPastYear}
          class={`p-3 rounded flex items-center justify-center transition-colors duration-150
                 ${
                   isPastYear
                     ? `${
                         darkMode ? "text-gray-600" : "text-gray-300"
                       } cursor-not-allowed`
                     : darkMode
                     ? "hover:bg-gray-700"
                     : "hover:bg-gray-100"
                 }
                 ${
                   isSelected
                     ? `bg-${colors.accent.replace(
                         "#",
                         ""
                       )} text-white font-bold`
                     : ""
                 }
                 ${
                   isCurrentYear && !isSelected && !isPastYear
                     ? "font-semibold border border-gray-300"
                     : ""
                 }`}
          onClick={(e) => !isPastYear && selectYear(year, e)}
          onMouseDown={(e) => e.stopPropagation()}
          style={
            isSelected ? { backgroundColor: colors.accent, color: "white" } : {}
          }
        >
          {year}
        </button>
      );
    }

    return <div class="grid grid-cols-3 gap-2 p-3">{years}</div>;
  };

  // Generar la cuadrícula de días del mes actual
  const generateDays = () => {
    const days = [];
    const firstDay = new Date(currentYear(), currentMonth(), 1).getDay(); // 0 = Domingo
    const daysInMonth = new Date(
      currentYear(),
      currentMonth() + 1,
      0
    ).getDate();

    // Espacios en blanco para los días anteriores al 1
    for (let i = 0; i < firstDay; i++) {
      days.push(<div class="w-10 h-10"></div>);
    }

    // Días del mes
    for (let day = 1; day <= daysInMonth; day++) {
      const date = new Date(currentYear(), currentMonth(), day);

      // CORREGIDO: Verificar si es una fecha pasada (permite HOY)
      const isPast = isPastDate(date);

      const isToday = date.toDateString() === today.toDateString();
      const isSelected =
        selectedDate() &&
        date.getDate() === selectedDate().getDate() &&
        date.getMonth() === selectedDate().getMonth() &&
        date.getFullYear() === selectedDate().getFullYear();

      days.push(
        <button
          type="button"
          disabled={isPast}
          class={`w-10 h-10 rounded-full flex items-center justify-center transition-colors duration-150
                  ${
                    isPast
                      ? `${
                          darkMode ? "text-gray-600" : "text-gray-300"
                        } cursor-not-allowed`
                      : "cursor-pointer"
                  }
                  ${
                    isSelected
                      ? `bg-${colors.accent.replace(
                          "#",
                          ""
                        )} text-white font-bold`
                      : ""
                  }
                  ${
                    isToday && !isSelected
                      ? `border-2 border-${colors.primary.replace(
                          "#",
                          ""
                        )} font-semibold`
                      : ""
                  }
                  ${
                    !isPast && !isSelected
                      ? `hover:bg-${darkMode ? "gray-700" : "gray-100"}`
                      : ""
                  }`}
          onClick={(e) => !isPast && selectDate(day, e)}
          onMouseDown={(e) => e.stopPropagation()}
          style={{
            ...(isSelected
              ? { backgroundColor: colors.accent, color: "white" }
              : {}),
            ...(isToday && !isSelected ? { borderColor: colors.primary } : {}),
          }}
        >
          {day}
        </button>
      );
    }

    return days;
  };

  // Ir a la fecha actual
  const goToToday = (e) => {
    if (e) {
      e.preventDefault();
      e.stopPropagation();
    }

    const now = new Date();
    setIsTransitioning(true);

    setTimeout(() => {
      setCurrentMonth(now.getMonth());
      setCurrentYear(now.getFullYear());
      setViewMode("days");
      setIsTransitioning(false);
    }, 200);
  };

  // Generar CSS para el componente con transiciones avanzadas
  const datepickerStyles = `
    .datepicker-container {
      position: relative;
      width: 100%;
    }
    
    .calendar-container {
      position: absolute;
      z-index: 9999;
      width: 300px;
      left: 0;
      top: calc(100% + 0.5rem);
      animation: fadeIn 0.2s ease-out;
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .month-year-selector {
      cursor: pointer;
      user-select: none;
      padding: 0.25rem 0.5rem;
      border-radius: 0.25rem;
      transition: background-color 0.2s;
    }
    
    .month-year-selector:hover {
      background-color: rgba(0, 0, 0, 0.1);
    }
    
    .calendar-content {
      height: 300px; /* Altura fija para evitar saltos */
      position: relative;
      overflow: hidden;
      perspective: 1000px; /* Para efectos 3D más suaves */
    }
    
    /* Panel de vista actual */
    .view-panel {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      transition: transform 0.3s cubic-bezier(0.25, 0.1, 0.25, 1), opacity 0.3s ease;
      display: flex;
      flex-direction: column;
      justify-content: center;
      backface-visibility: hidden; /* Mejora rendimiento de animaciones */
      transform-style: preserve-3d;
      will-change: transform, opacity; /* Optimización de rendimiento */
    }
    
    /* Panel para la vista anterior (durante transiciones) */
    .previous-view-panel {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      z-index: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      transition: transform 0.3s cubic-bezier(0.25, 0.1, 0.25, 1), opacity 0.3s ease;
      backface-visibility: hidden;
      transform-style: preserve-3d;
      will-change: transform, opacity;
    }
    
    /* Animaciones para la vista actual durante transición */
    .transitioning.next .view-panel {
      transform: translateY(30px);
      opacity: 0;
      pointer-events: none;
    }
    
    .transitioning.prev .view-panel {
      transform: translateY(-30px);
      opacity: 0;
      pointer-events: none;
    }
    
    /* Animaciones para la vista anterior durante transición */
    .transitioning.next .previous-view-panel {
      transform: translateY(-30px);
      opacity: 0;
      pointer-events: none;
    }
    
    .transitioning.prev .previous-view-panel {
      transform: translateY(30px);
      opacity: 0;
      pointer-events: none;
    }
    
    /* Estados iniciales para animación de entrada */
    .view-enter-next {
      transform: translateY(30px);
      opacity: 0;
    }
    
    .view-enter-prev {
      transform: translateY(-30px);
      opacity: 0;
    }
    
    .view-enter-active {
      transform: translateY(0);
      opacity: 1;
      transition: transform 0.3s cubic-bezier(0.25, 0.1, 0.25, 1), opacity 0.3s ease;
    }
  `;

  // Renderizar el componente
  return (
    <div class="group datepicker-container" ref={containerRef}>
      <style>{datepickerStyles}</style>

      <label
        for={id}
        class={`block text-sm font-medium mb-1.5 ml-1 ${
          darkMode ? "text-white" : "text-gray-700"
        }`}
      >
        {labelText}
      </label>

      <div class="relative">
        <div
          class={`absolute inset-y-0 left-3 flex items-center pointer-events-none ${
            darkMode ? "text-gray-400" : "text-gray-500"
          }`}
        >
          <svg
            class="w-5 h-5 mr-2"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1.5"
              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
            ></path>
          </svg>
        </div>

        <input
          id={id}
          name={name}
          type="text"
          value={displayValue()}
          placeholder={placeholder}
          readonly
          required={required}
          onClick={toggleCalendar}
          class={`w-full pl-12 pr-4 py-3 ${
            darkMode
              ? "bg-gray-800/80 border-gray-700 text-white"
              : "bg-gray-50 border-gray-300 text-gray-900"
          } border rounded-md focus:ring-2 focus:outline-none cursor-pointer transition-colors duration-200`}
          style={{ "border-color": colors.accent }}
        />
      </div>

      {/* Calendario emergente */}
      <Show when={isOpen()}>
        <div
          ref={calendarRef}
          class={`calendar-container rounded-lg overflow-hidden ${
            darkMode
              ? "bg-gray-800 border border-gray-700"
              : "bg-white border border-gray-200"
          }`}
          onMouseDown={(e) => e.stopPropagation()}
          onClick={(e) => e.stopPropagation()}
        >
          {/* Encabezado con navegación */}
          <div
            class={`calendar-navigation flex justify-between items-center p-3 border-b ${
              darkMode ? "border-gray-700" : "border-gray-100"
            }`}
          >
            <button
              type="button"
              class={`p-1 rounded ${
                darkMode
                  ? "text-gray-300 hover:bg-gray-700"
                  : "text-gray-600 hover:bg-gray-100"
              }`}
              onClick={(e) => navigate(-1, e)}
              onMouseDown={(e) => e.stopPropagation()}
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>

            <div class="flex space-x-1">
              {/* Selector de mes/año - muestra diferente texto según la vista */}
              <span
                class={`month-year-selector font-medium ${
                  darkMode
                    ? "text-white hover:bg-gray-700"
                    : "text-gray-700 hover:bg-gray-100"
                }`}
                onClick={
                  viewMode() === "years"
                    ? null
                    : viewMode() === "months"
                    ? showYearSelector
                    : showMonthSelector
                }
                onMouseDown={(e) => e.stopPropagation()}
              >
                {viewMode() === "years"
                  ? `${yearRange().start} - ${yearRange().end}`
                  : viewMode() === "months"
                  ? currentYear()
                  : monthNames[currentMonth()]}
              </span>

              {/* Selector de año - solo visible en vista de días */}
              {viewMode() === "days" && (
                <>
                  <span class={darkMode ? "text-white" : "text-gray-700"}>
                    ,
                  </span>
                  <span
                    class={`month-year-selector font-medium ${
                      darkMode
                        ? "text-white hover:bg-gray-700"
                        : "text-gray-700 hover:bg-gray-100"
                    }`}
                    onClick={showYearSelector}
                    onMouseDown={(e) => e.stopPropagation()}
                  >
                    {currentYear()}
                  </span>
                </>
              )}
            </div>

            <button
              type="button"
              class={`p-1 rounded ${
                darkMode
                  ? "text-gray-300 hover:bg-gray-700"
                  : "text-gray-600 hover:bg-gray-100"
              }`}
              onClick={(e) => navigate(1, e)}
              onMouseDown={(e) => e.stopPropagation()}
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
          </div>

          {/* Contenido del calendario con sistema de capas para transiciones suaves */}
          <div
            class={`calendar-content ${
              isTransitioning() ? `transitioning ${transitionDirection()}` : ""
            }`}
            onMouseDown={(e) => e.stopPropagation()}
          >
            {/* Capa para la vista anterior - solo visible durante transiciones */}
            {previousView() && isTransitioning() && (
              <div class="previous-view-panel">{previousViewContent()}</div>
            )}

            {/* Capa para la vista actual */}
            <div
              class={`view-panel ${
                isTransitioning()
                  ? ""
                  : "view-enter-" + transitionDirection() + " view-enter-active"
              }`}
            >
              {viewMode() === "days" && (
                <div class="p-3">
                  <div
                    class={`grid grid-cols-7 mb-2 text-center text-xs ${
                      darkMode ? "text-gray-400" : "text-gray-500"
                    }`}
                  >
                    {dayNames.map((day) => (
                      <div>{day}</div>
                    ))}
                  </div>

                  {/* Días del mes */}
                  <div class="grid grid-cols-7 gap-1">{generateDays()}</div>
                </div>
              )}

              {/* Vista de selección de meses */}
              {viewMode() === "months" && generateMonthSelector()}

              {/* Vista de selección de años */}
              {viewMode() === "years" && generateYearSelector()}
            </div>
          </div>

          {/* Botón para volver a la fecha actual y botón de cerrar */}
          <div
            class={`p-2 border-t ${
              darkMode ? "border-gray-700" : "border-gray-100"
            } flex justify-between`}
          >
            <button
              type="button"
              class={`text-sm px-3 py-1 rounded ${
                darkMode
                  ? "text-gray-300 hover:bg-gray-700"
                  : "text-gray-600 hover:bg-gray-100"
              }`}
              onClick={goToToday}
              onMouseDown={(e) => e.stopPropagation()}
            >
              Hoy
            </button>

            <button
              type="button"
              class={`text-sm px-3 py-1 rounded ${
                darkMode
                  ? "text-gray-300 hover:bg-gray-700 bg-gray-700/50"
                  : "text-gray-600 hover:bg-gray-100 bg-gray-100/50"
              }`}
              onClick={(e) => {
                e.preventDefault();
                e.stopPropagation();
                setIsOpen(false);
                setTimeout(() => setViewMode("days"), 300);
              }}
              onMouseDown={(e) => e.stopPropagation()}
            >
              Cerrar
            </button>
          </div>
        </div>
      </Show>

      {/* Campo oculto para asegurar que el valor esté disponible para el formulario */}
      <input type="hidden" name={name} value={displayValue()} />
    </div>
  );
};

export default SimpleDatePicker;