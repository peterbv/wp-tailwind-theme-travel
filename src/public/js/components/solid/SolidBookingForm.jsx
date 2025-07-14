// src/public/js/components/solid/SolidBookingForm.jsx
// SEO OPTIMIZED: Estructura semántica, accesibilidad y schema markup mejorados
// Internationalization (i18n) support

import { createSignal, createEffect, Show, onMount, For, createMemo } from "solid-js";
import { __ } from "../../solid-core";
import SimpleDatePicker from "./SimpleDatePicker";
import CustomSelect from "./CustomSelect";

/**
 * Booking form optimized with Solid.js
 * SEO OPTIMIZED: Semantic structure, accessibility, and schema markup
 */
const SolidBookingForm = (props) => {
  // Properties
  const {
    formId = "booking-form",
    services = [],
    isDarkMode = false,
    accentColor = "#D4B254",
    ajaxUrl = "",
    useSingleService = false,
    emailRecipient = "",
    businessName = "Business Name", // SEO: Para schema markup
    businessUrl = "", // SEO: Para schema markup
    serviceType = "Service", // SEO: Para schema markup
  } = props;

  // Form states - CORRECT SOLID.JS SYNTAX
  const [getName, setName] = createSignal("");
  const [getEmail, setEmail] = createSignal("");
  const [getService, setService] = createSignal("");
  const [getDate, setDate] = createSignal("");
  const [getTime, setTime] = createSignal("");
  const [getMessage, setMessage] = createSignal("");
  const [getDuration, setDuration] = createSignal("");
  const [getVisitors, setVisitors] = createSignal(1);
  const [getRecipientEmail, setRecipientEmail] = createSignal(emailRecipient);

  // Step flow states - CORRECT SYNTAX
  const [getCurrentStep, setCurrentStep] = createSignal(1);
  const [getCompletedSteps, setCompletedSteps] = createSignal(new Set());
  const [getIsStepValid, setIsStepValid] = createSignal(false);

  // Additional states - CORRECT SYNTAX
  const [getIsSubmitting, setIsSubmitting] = createSignal(false);
  const [getResponse, setResponse] = createSignal({ type: "", message: "" });
  const [getAvailableTimes, setAvailableTimes] = createSignal([]);
  const [getAvailableDurations, setAvailableDurations] = createSignal([]);
  const [getSelectedService, setSelectedService] = createSignal(null);
  const [getCurrentNonce, setCurrentNonce] = createSignal("");

  const [getShowServiceSelector, setShowServiceSelector] = createSignal(!useSingleService);

  // Create a derived signal to detect if selected date is today
  const [getShowTodayWarning, setShowTodayWarning] = createSignal(false);

  // SIMPLIFIED step configuration
  const totalSteps = 3;

  const getStepConfig = () => {
    if (useSingleService || !shouldShowServiceSelector()) {
      return [
        { step: 1, title: "Date & Time", icon: "calendar", description: "Choose your preferred appointment time" },
        { step: 2, title: "Personal Details", icon: "user", description: "Provide your contact information" },
        { step: 3, title: "Confirmation", icon: "check", description: "Review and confirm your booking" }
      ];
    } else {
      return [
        { step: 1, title: "Service & Schedule", icon: "calendar", description: "Select service and schedule" },
        { step: 2, title: "Personal Details", icon: "user", description: "Provide your contact information" },
        { step: 3, title: "Confirmation", icon: "check", description: "Review and confirm your booking" }
      ];
    }
  };

  // Theme colors
  const colors = {
    primary: "#4F8A8B",
    secondary: "#F7EDE2",
    accent: accentColor,
    sage: "#8BAB8D",
    rose: "#D9ADB7",
    dark: "#424242",
  };

  // SEO: Generate structured data for booking
  const generateStructuredData = () => {
    if (!getSelectedService() || !getDate() || !getTime()) return null;

    const service = getSelectedService();
    const bookingData = {
      "@context": "https://schema.org",
      "@type": "Service",
      "name": service.title,
      "provider": {
        "@type": "Organization",
        "name": businessName,
        "url": businessUrl
      },
      "serviceType": serviceType,
      "availableChannel": {
        "@type": "ServiceChannel",
        "serviceUrl": businessUrl,
        "availableLanguage": "es"
      }
    };

    return JSON.stringify(bookingData);
  };

  // Function to get fresh nonce
  const getFreshNonce = async () => {
    try {
      const ajaxURL = ajaxUrl || window.wptbtBooking?.ajaxurl || "/wp-admin/admin-ajax.php";
      const response = await fetch(ajaxURL, {
        method: "POST",
        credentials: "same-origin",
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=wptbt_get_fresh_nonce'
      });

      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
      const result = await response.json();

      if (result.success && result.data.nonce) {
        setCurrentNonce(result.data.nonce);
        return result.data.nonce;
      } else {
        throw new Error("Invalid nonce response");
      }
    } catch (error) {
      console.error("[DEBUG] Error getting fresh nonce:", error);
      throw error;
    }
  };

  // Function to get translations
  const getTranslation = (text, domain = "wptbt-booking-form-block") => {
    const componentTranslations = window.wptbtI18n_booking_form || {};
    if (componentTranslations[text]) {
      return componentTranslations[text];
    }
    return __(text, domain);
  };

  function getTodayInLimaFormat() {
    const today = new Date().toLocaleDateString('es-PE', {
      timeZone: 'America/Lima',
      year: 'numeric',
      month: '2-digit',
      day: '2-digit'
    });

    const [day, month, year] = today.split('/');
    return `${year}-${month}-${day}`;
  }

  const isToday = (dateString) => {
    if (!dateString) return false;
    const today = getTodayInLimaFormat();
    return dateString === today;
  };

  const validateStep = (step) => {
    switch (step) {
      case 1:
        if (isToday(getDate())) {
          return false;
        }
        if (useSingleService || !shouldShowServiceSelector()) {
          return getDate() && getTime();
        } else {
          return getService() && getDate() && getTime();
        }
      case 2:
        return getName() && getEmail() && getVisitors() > 0;
      case 3:
        return true;
      default:
        return false;
    }
  };

  createEffect(() => {
    setIsStepValid(validateStep(getCurrentStep()));
  });

  createEffect(() => {
    const currentDate = getDate();
    setShowTodayWarning(isToday(currentDate));
  });

  createEffect(() => {
    const currentStep = getCurrentStep();
    if (currentStep > 1) {
      setTimeout(() => {
        const formContainer = document.querySelector(`#${formId}`) ||
          document.querySelector('[data-booking-form]') ||
          document.querySelector('.space-y-6');

        if (formContainer) {
          formContainer.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
            inline: 'nearest'
          });
        } else {
          window.scrollTo({
            top: 0,
            behavior: 'smooth'
          });
        }
      }, 100);
    }
  });

  onMount(async () => {
    try {
      await getFreshNonce();
    } catch (error) {
      console.error("[DEBUG] Failed to load initial nonce:", error);
      setResponse({
        type: "error",
        message: getTranslation("Error loading form. Please refresh the page."),
      });
    }

    console.log("[DEBUG] Services data:", services);

    if (Array.isArray(services) && services.length > 0) {
      const initialServiceId = services[0].id;
      console.log("[DEBUG] Setting initial service:", initialServiceId);

      const serviceIdString = String(initialServiceId);
      setService(serviceIdString);

      setTimeout(() => {
        updateServiceDetails(serviceIdString);
      }, 0);
    } else {
      console.warn("[DEBUG] No valid services found:", services);
    }
  });

  // Auto-select service when only one is available
  createEffect(() => {
    const availableServices = getServiceOptions();
    if (availableServices.length === 1 && !getService()) {
      const singleServiceId = availableServices[0].value;
      console.log("[DEBUG] Auto-selecting single available service:", singleServiceId);
      setService(String(singleServiceId));
      updateServiceDetails(String(singleServiceId));
    }
  });

  const updateServiceDetails = (serviceId) => {
    if (!serviceId) return;

    console.log("[DEBUG] Updating service details for:", serviceId);

    const serviceObj = services.find((s) => s.id === serviceId);
    if (serviceObj) {
      console.log("[DEBUG] Found service object:", serviceObj);
      setSelectedService(serviceObj);

      if (Array.isArray(serviceObj.hours) && serviceObj.hours.length > 0) {
        console.log("[DEBUG] Setting available times:", serviceObj.hours);
        setAvailableTimes([...serviceObj.hours]);
      } else {
        console.log("[DEBUG] No hours found for service");
        setAvailableTimes([]);
      }

      if (Array.isArray(serviceObj.durations) && serviceObj.durations.length > 0) {
        console.log("[DEBUG] Setting available durations:", serviceObj.durations);
        setAvailableDurations([...serviceObj.durations]);

        const firstDuration = serviceObj.durations[0];
        if (firstDuration && firstDuration.value) {
          setDuration(firstDuration.value);
        }
      } else {
        const durations = [];
        if (serviceObj.duration1 && serviceObj.price1) {
          const days = parseInt(serviceObj.duration1.replace(/[^0-9]/g, '')) || 0;
          durations.push({
            minutes: days * 24 * 60, // Convert days to minutes for compatibility
            price: serviceObj.price1,
            text: `${serviceObj.duration1} días - $${serviceObj.price1.replace('$', '')}`,
            duration: serviceObj.duration1,
            value: `${days}days-${serviceObj.price1}`
          });
        }
        if (serviceObj.duration2 && serviceObj.price2) {
          const days = parseInt(serviceObj.duration2.replace(/[^0-9]/g, '')) || 0;
          durations.push({
            minutes: days * 24 * 60, // Convert days to minutes for compatibility
            price: serviceObj.price2,
            text: `${serviceObj.duration2} días - $${serviceObj.price2.replace('$', '')}`,
            duration: serviceObj.duration2,
            value: `${days}days-${serviceObj.price2}`
          });
        }
        console.log("[DEBUG] Setting fallback durations:", durations);
        setAvailableDurations(durations);
        if (durations.length > 0 && durations[0].value) {
          setDuration(durations[0].value);
        }
      }

      setTime("");
    } else {
      console.log("[DEBUG] Service not found for ID:", serviceId);
      setAvailableTimes([]);
      setAvailableDurations([]);
      setSelectedService(null);
    }
  };

  const nextStep = () => {
    if (getIsStepValid() && getCurrentStep() < totalSteps) {
      setCompletedSteps((prev) => new Set([...prev, getCurrentStep()]));
      setCurrentStep((prev) => prev + 1);
    }
  };

  const prevStep = () => {
    if (getCurrentStep() > 1) {
      setCurrentStep((prev) => prev - 1);
    }
  };

  const goToStep = (step) => {
    if (step <= getCurrentStep() || getCompletedSteps().has(step - 1)) {
      setCurrentStep(step);
    }
  };

  const handleServiceChange = (serviceId) => {
    console.log("[DEBUG] Service changed to:", serviceId);

    const serviceIdString = String(serviceId);
    setService(serviceIdString);

    setTimeout(() => {
      updateServiceDetails(serviceIdString);
    }, 0);
  };

  const formatTime = (timeStr) => {
    if (!timeStr) return "";
    try {
      const [hours, minutes] = timeStr.split(":");
      const hour = parseInt(hours, 10);
      const suffix = hour >= 12 ? "PM" : "AM";
      const displayHour = hour % 12 === 0 ? 12 : hour % 12;
      return `${displayHour}:${minutes} ${suffix}`;
    } catch (e) {
      return timeStr;
    }
  };

  const handleDateChange = (e) => {
    const newDate = e.target.value;
    setDate(newDate);
  };

  const formatDateForDisplay = (dateStr) => {
    if (!dateStr) return "";
    try {
      const date = new Date(Date.parse(`${dateStr}T00:00:00`));
      return date.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    } catch (e) {
      return dateStr;
    }
  };

  const getServiceOptions = createMemo(() => {
    console.log("[DEBUG] Creating service options from:", services);
    return services.map(service => ({
      value: service.id,
      label: service.title,
      subtitle: service.subtitle || `${service.durations?.length || 0} duration options`,
      disabled: false
    }));
  });

  // Flag to hide service selector in single-service mode or when only one service is available
  const shouldShowServiceSelector = createMemo(() => {
    if (useSingleService) return false;
    const availableServices = getServiceOptions();
    return availableServices.length > 1;
  });

  const getDurationOptions = createMemo(() => {
    const durations = getAvailableDurations();
    console.log("[DEBUG] Creating duration options from:", durations);

    if (!Array.isArray(durations)) {
      console.warn("[DEBUG] Durations is not an array:", durations);
      return [];
    }

    return durations.map(duration => ({
      value: duration.value || `${duration.minutes}days-${duration.price}`,
      label: `${duration.duration || duration.minutes} días`,
      subtitle: `$${duration.price.replace('$', '')}`,
      disabled: false
    }));
  });

  const getTimeOptions = createMemo(() => {
    const times = getAvailableTimes();
    console.log("[DEBUG] Creating time options from:", times);

    if (!Array.isArray(times)) {
      console.warn("[DEBUG] Times is not an array:", times);
      return [];
    }

    return times.map(time => ({
      value: time,
      label: formatTime(time),
      disabled: false
    }));
  });

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsSubmitting(true);
    setResponse({
      type: "info",
      message: getTranslation("Processing your booking..."),
    });

    try {
      let nonceToUse = getCurrentNonce();
      if (!nonceToUse) {
        nonceToUse = await getFreshNonce();
      }

      const formData = new FormData();
      formData.append("action", "wptbt_submit_booking");
      formData.append("booking_nonce", nonceToUse);
      formData.append("recipient_email", getRecipientEmail());
      formData.append("name", getName());
      formData.append("email", getEmail());
      formData.append("service", getService());
      formData.append("date", getDate());
      formData.append("time", getTime());
      formData.append("message", getMessage());
      formData.append("visitors", getVisitors());

      if (getDuration()) {
        formData.append("duration", getDuration());
      }

      const serviceObj = services.find((s) => s.id === getService());
      if (serviceObj) {
        formData.append("service_title", serviceObj.title);
      }

      const ajaxURL = ajaxUrl || window.wptbtBooking?.ajaxurl || "/wp-admin/admin-ajax.php";
      const response = await fetch(ajaxURL, {
        method: "POST",
        credentials: "same-origin",
        body: formData,
      });

      if (!response.ok) {
        throw new Error(`Network response error: ${response.status} ${response.statusText}`);
      }

      const result = await response.json();

      if (result.success) {
        setResponse({
          type: "success",
          message: result.data || getTranslation("Booking successfully made"),
        });

        setTimeout(() => {
          setCurrentStep(1);
          setCompletedSteps(new Set());
          setName("");
          setEmail("");
          setMessage("");
          setDate("");
          setTime("");
          setVisitors(1);

          if (services.length > 0) {
            setService(services[0].id);
            updateServiceDetails(services[0].id);
          }
        }, 3000);

      } else {
        setResponse({
          type: "error",
          message: result.data || getTranslation("An error occurred. Please try again."),
        });
      }
    } catch (error) {
      console.error("[DEBUG] Error submitting form:", error);
      setResponse({
        type: "error",
        message: getTranslation("Connection error. Please try again later."),
      });
    } finally {
      setIsSubmitting(false);
    }
  };

  const renderStepIcon = (iconType, isActive, isCompleted) => {
    const iconClass = `w-6 h-6 ${isActive ? 'text-white' : isCompleted ? 'text-white' : isDarkMode ? 'text-gray-400' : 'text-gray-500'}`;
    if (!isCompleted) {
      switch (iconType) {
        case 'service':
          return (
            <svg class={iconClass} fill="none" stroke="currentColor" viewBox="0 0 24 24" role="img" aria-label="Service selection">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
          );
        case 'calendar':
          return (
            <svg class={iconClass} fill="none" stroke="currentColor" viewBox="0 0 24 24" role="img" aria-label="Date and time selection">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          );
        case 'user':
          return (
            <svg class={iconClass} fill="none" stroke="currentColor" viewBox="0 0 24 24" role="img" aria-label="Personal information">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          );
        case 'check':
          return (
            <svg class={iconClass} fill="none" stroke="currentColor" viewBox="0 0 24 24" role="img" aria-label="Confirmation">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          );
        default:
          return (
            <svg class={iconClass} fill="none" stroke="currentColor" viewBox="0 0 24 24" role="img" aria-label="Step">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          );
      }
    } else {
      return (
        <svg xmlns="http://www.w3.org/2000/svg" class={iconClass} stroke="currentColor" fill="none" viewBox="0 0 24 24" role="img" aria-label="Completed step">
          <path d="M4 12.6111L8.92308 17.5L20 6.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      )
    }
  };

  return (
    <>
      {/* SEO: Structured data for booking service */}
      <Show when={generateStructuredData()}>
        <script type="application/ld+json">
          {generateStructuredData()}
        </script>
      </Show>

      {/* SEO: Main form container with semantic HTML */}
      <main class="space-y-6" id={formId} data-booking-form="true" role="main" aria-label="Booking appointment form">

        {/* SEO: Progress navigation with semantic structure */}
        <nav aria-label="Booking form progress" class="mb-8">
          <h2 class="sr-only">Booking Steps Progress</h2>
          <div class={`flex justify-between items-center mb-4 ${isDarkMode ? 'text-white' : 'text-gray-700'}`}>
            <For each={getStepConfig()}>
              {(stepConfig) => {
                const isActive = () => getCurrentStep() === stepConfig.step;
                const isCompleted = () => getCompletedSteps().has(stepConfig.step);
                const isAccessible = () => stepConfig.step <= getCurrentStep() || isCompleted;

                return (
                  <div
                    class={`flex flex-col items-center cursor-pointer transition-all duration-300 ${isAccessible() ? 'hover:scale-105' : 'cursor-not-allowed'
                      }`}
                    onClick={() => isAccessible() && goToStep(stepConfig.step)}
                    role="button"
                    tabindex={isAccessible() ? 0 : -1}
                    aria-label={`Step ${stepConfig.step}: ${stepConfig.title}. ${stepConfig.description}. ${isCompleted() ? 'Completed' : isActive() ? 'Current step' : 'Not completed'}`}
                    onKeyDown={(e) => {
                      if ((e.key === 'Enter' || e.key === ' ') && isAccessible()) {
                        e.preventDefault();
                        goToStep(stepConfig.step);
                      }
                    }}
                  >
                    <div class={`
                      w-12 h-12 rounded-full flex items-center justify-center mb-2 transition-all duration-300
                      ${isActive()
                        ? `bg-opacity-100 shadow-lg transform scale-110  bg-teal-500`
                        : isCompleted()
                          ? `bg-opacity-80 shadow-md bg-teal-500`
                          : isDarkMode
                            ? 'bg-gray-700 border-2 border-gray-600'
                            : 'bg-gray-200 border-2 border-gray-300'
                      }
                    `}
                    aria-hidden="true"
                    >
                      {renderStepIcon(stepConfig.icon, isActive(), isCompleted())}
                    </div>
                    <span class={`text-xs text-center font-medium transition-colors duration-300 ${isActive() ? 'text-current' : isDarkMode ? 'text-gray-400' : 'text-gray-500'
                      }`}>
                      {stepConfig.title}
                    </span>
                  </div>
                );
              }}
            </For>
          </div>

          {/* Progress bar */}
          <div class={`w-full h-2 rounded-full ${isDarkMode ? 'bg-gray-700' : 'bg-gray-200'}`} role="progressbar" aria-valuenow={getCurrentStep()} aria-valuemin={1} aria-valuemax={totalSteps} aria-label={`Step ${getCurrentStep()} of ${totalSteps}`}>
            <div
              class="h-2 rounded-full transition-all duration-500 ease-out bg-teal-500"
              style={{
                backgroundColor: colors.accent,
                width: `${(getCurrentStep() / totalSteps) * 100}%`
              }}
            />
          </div>
        </nav>

        {/* SEO: Form content with semantic sections */}
        <section class={`min-h-96 p-6 rounded-lg transition-all z-10 relative duration-300 ${isDarkMode ? 'bg-gray-800/50' : 'bg-white/50'
          } backdrop-blur-sm border ${isDarkMode ? 'border-gray-700' : 'border-gray-200'}`}
          data-step-content="true"
          aria-live="polite"
          aria-atomic="true">

          {/* Step 1: Service and Schedule */}
          <Show when={getCurrentStep() === 1}>
            <article class="space-y-6" role="tabpanel" aria-labelledby="step1-heading">
              <header class="text-center mb-8">
                <h1 id="step1-heading" class={`text-2xl font-bold mb-2 ${isDarkMode ? 'text-white' : 'text-gray-800'}`}>
                  {useSingleService || !shouldShowServiceSelector() ? "Date & Time Selection" : "Service & Schedule Selection"}
                </h1>
                <p class={`${isDarkMode ? 'text-gray-300' : 'text-gray-600'}`}>
                  {useSingleService || !shouldShowServiceSelector() ? "Select when you'd like your appointment" : "Choose your preferred service and schedule"}
                </p>
              </header>

              {/* Service selector */}
              <Show when={shouldShowServiceSelector()}>
                <div class="mb-8">
                  <CustomSelect
                    id={`${formId}-service`}
                    name="service"
                    options={() => getServiceOptions()}
                    value={() => getService()}
                    onChange={handleServiceChange}
                    placeholder={getTranslation("Select a service")}
                    labelText={getTranslation("Select Service")}
                    required={true}
                    darkMode={isDarkMode}
                    colors={colors}
                    aria-describedby="service-help"
                  />
                  <div id="service-help" class="sr-only">
                    Choose the service you want to book from the available options
                  </div>
                </div>
              </Show>

              {/* Show selected tour info when only one is available */}
              <Show when={!shouldShowServiceSelector() && getSelectedService()}>
                <div class="mb-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                  <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                      <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      </svg>
                    </div>
                    <div>
                      <h3 class="text-lg font-medium text-blue-900">
                        {getSelectedService()?.title}
                      </h3>
                      <Show when={getSelectedService()?.subtitle}>
                        <p class="text-sm text-blue-700">
                          {getSelectedService()?.subtitle}
                        </p>
                      </Show>
                    </div>
                  </div>
                </div>
              </Show>

              {/* Duration selector */}
              <Show when={getDurationOptions().length > 0}>
                <div class="mb-6">
                  <CustomSelect
                    id={`${formId}-duration`}
                    name="duration"
                    options={() => getDurationOptions()}
                    value={() => getDuration()}
                    onChange={setDuration}
                    placeholder={getTranslation("Select duration")}
                    labelText={getTranslation("Select Duration")}
                    required={true}
                    darkMode={isDarkMode}
                    colors={colors}
                    aria-describedby="duration-help"
                  />
                  <div id="duration-help" class="sr-only">
                    Select the duration for your appointment. Price varies by duration.
                  </div>
                </div>
              </Show>

              {/* Date selector */}
              <div class="mb-6">
                <SimpleDatePicker
                  id={`${formId}-date`}
                  name="date"
                  value={getDate()}
                  onChange={handleDateChange}
                  required={true}
                  labelText={getTranslation("Select Date")}
                  placeholder={getTranslation("Select date")}
                  darkMode={isDarkMode}
                  colors={colors}
                  aria-describedby="date-help"
                />
                <div id="date-help" class="sr-only">
                  Choose your preferred date for the appointment. Same-day bookings require WhatsApp contact.
                </div>
              </div>

              {/* Alert for same-day reservations */}
              <Show when={isToday(getDate())}>
                <aside class="bg-green-50 border-l-4 border-green-500 rounded-lg shadow-md p-4 flex items-center justify-between" role="alert" aria-live="assertive">
                  <div class="flex items-center">
                    <svg class="w-10 h-10 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"></path>
                    </svg>
                    <div>
                      <p class="text-sm text-green-700">
                        <strong class="block">Reserve for TODAY ({formatDateForDisplay(getDate())}):</strong>
                        Only via WhatsApp for same-day appointments.
                      </p>
                      <a 
                        href="https://api.whatsapp.com/send/?phone=51906597850&amp;text&amp;type=phone_number&amp;app_absent=0" 
                        target="_blank" 
                        rel="noopener noreferrer"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-300 block mt-2 inline-flex items-center"
                        aria-label="Contact us via WhatsApp for same-day booking"
                      >
                        Contact by WhatsApp
                      </a>
                    </div>
                  </div>
                </aside>
              </Show>

              {/* Time selector */}
              <div>
                <CustomSelect
                  id={`${formId}-time`}
                  name="time"
                  options={() => getTimeOptions()}
                  value={() => getTime()}
                  onChange={setTime}
                  placeholder={getTranslation("Select available time")}
                  labelText={getTranslation("Select Time")}
                  required={true}
                  darkMode={isDarkMode}
                  colors={colors}
                  aria-describedby="time-help"
                />
                <div id="time-help" class="sr-only">
                  Select your preferred appointment time from available slots
                </div>

                <Show when={getTimeOptions().length === 0}>
                  <p class={`text-center py-4 text-sm ${isDarkMode ? 'text-gray-400' : 'text-gray-500'}`} role="status" aria-live="polite">
                    {getTranslation("No available times")}
                    <br />
                    <span class="text-xs">
                      {getSelectedService()
                        ? `Service: ${getSelectedService().title}, Hours configured: ${getAvailableTimes().length}`
                        : 'Please select a service first'}
                    </span>
                  </p>
                </Show>
              </div>
            </article>
          </Show>

          {/* Step 2: Personal Details */}
          <Show when={getCurrentStep() === 2}>
            <article class="space-y-6" role="tabpanel" aria-labelledby="step2-heading">
              <header class="text-center mb-8">
                <h1 id="step2-heading" class={`text-2xl font-bold mb-2 ${isDarkMode ? 'text-white' : 'text-gray-800'}`}>
                  Personal Details
                </h1>
                <p class={`${isDarkMode ? 'text-gray-300' : 'text-gray-600'}`}>
                  Information to confirm your booking
                </p>
              </header>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {/* Name field */}
                <div>
                  <label for={`${formId}-name`} class={`block text-sm font-medium mb-2 ${isDarkMode ? 'text-white' : 'text-gray-700'}`}>
                    {getTranslation("Name")} *
                  </label>
                  <input
                    id={`${formId}-name`}
                    type="text"
                    value={getName()}
                    onInput={(e) => setName(e.target.value)}
                    required
                    autocomplete="name"
                    aria-describedby={`${formId}-name-help`}
                    class={`w-full px-4 py-3 ${isDarkMode
                      ? "bg-gray-800/80 border-gray-700 text-white"
                      : "bg-gray-50 border-gray-300 text-gray-900"
                      } border rounded-lg focus:ring-2 focus:outline-none transition-colors duration-200`}
                    style={{ "border-color": colors.accent }}
                    placeholder={getTranslation("Full name")}
                  />
                  <div id={`${formId}-name-help`} class="sr-only">
                    Enter your full name as it should appear on the booking
                  </div>
                </div>

                {/* Email field */}
                <div>
                  <label for={`${formId}-email`} class={`block text-sm font-medium mb-2 ${isDarkMode ? 'text-white' : 'text-gray-700'}`}>
                    {getTranslation("Email")} *
                  </label>
                  <input
                    id={`${formId}-email`}
                    type="email"
                    value={getEmail()}
                    onInput={(e) => setEmail(e.target.value)}
                    required
                    autocomplete="email"
                    aria-describedby={`${formId}-email-help`}
                    class={`w-full px-4 py-3 ${isDarkMode
                      ? "bg-gray-800/80 border-gray-700 text-white"
                      : "bg-gray-50 border-gray-300 text-gray-900"
                      } border rounded-lg focus:ring-2 focus:outline-none transition-colors duration-200`}
                    style={{ "border-color": colors.accent }}
                    placeholder="you@email.com"
                  />
                  <div id={`${formId}-email-help`} class="sr-only">
                    Enter a valid email address to receive booking confirmation
                  </div>
                </div>
              </div>

              {/* Number of visitors */}
              <div role="group" aria-labelledby={`${formId}-visitors-label`}>
                <h3 id={`${formId}-visitors-label`} class={`block text-sm font-medium mb-4 ${isDarkMode ? 'text-white' : 'text-gray-700'}`}>
                  {getTranslation("Number of Visitors")} *
                </h3>
                <div class="flex items-center justify-center space-x-6" aria-describedby={`${formId}-visitors-help`}>
                  <button
                    type="button"
                    onClick={() => getVisitors() > 1 && setVisitors((prev) => prev - 1)}
                    disabled={getVisitors() <= 1}
                    aria-label="Decrease number of visitors"
                    class={`p-3 rounded-lg transition-all duration-200 ${getVisitors() <= 1
                      ? 'opacity-50 cursor-not-allowed'
                      : 'hover:bg-gray-200/20 transform hover:scale-105'
                      }`}
                    style={{ backgroundColor: `${colors.accent}20` }}
                  >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                  </button>

                  <div class={`text-center min-w-24 ${isDarkMode ? 'text-white' : 'text-gray-800'}`} aria-live="polite">
                    <span class="text-4xl font-bold" aria-label={`${getVisitors()} ${getVisitors() === 1 ? 'person' : 'people'}`}>
                      {getVisitors()}
                    </span>
                    <p class="text-sm text-gray-500 mt-1" aria-hidden="true">
                      {getVisitors() === 1 ? 'person' : 'people'}
                    </p>
                  </div>

                  <button
                    type="button"
                    onClick={() => getVisitors() < 20 && setVisitors((prev) => prev + 1)}
                    disabled={getVisitors() >= 20}
                    aria-label="Increase number of visitors"
                    class={`p-3 rounded-lg transition-all duration-200 ${getVisitors() >= 20
                      ? 'opacity-50 cursor-not-allowed'
                      : 'hover:bg-gray-200/20 transform hover:scale-105'
                      }`}
                    style={{ backgroundColor: `${colors.accent}20` }}
                  >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                  </button>
                </div>
                <div id={`${formId}-visitors-help`} class="sr-only">
                  Use the minus and plus buttons to adjust the number of visitors (1-20)
                </div>
              </div>

              {/* Additional information */}
              <div>
                <label for={`${formId}-message`} class={`block text-sm font-medium mb-2 ${isDarkMode ? 'text-white' : 'text-gray-700'}`}>
                  {getTranslation("Additional Information (Optional)")}
                </label>
                <textarea
                  id={`${formId}-message`}
                  value={getMessage()}
                  onInput={(e) => setMessage(e.target.value)}
                  rows="4"
                  aria-describedby={`${formId}-message-help`}
                  class={`w-full px-4 py-3 ${isDarkMode
                    ? "bg-gray-800/80 border-gray-700 text-white"
                    : "bg-gray-50 border-gray-300 text-gray-900"
                    } border rounded-lg focus:ring-2 focus:outline-none transition-colors duration-200 resize-none`}
                  style={{ "border-color": colors.accent }}
                  placeholder="Any special requests or additional information..."
                />
                <div id={`${formId}-message-help`} class="sr-only">
                  Optional field for any special requests or additional information about your booking
                </div>
              </div>
            </article>
          </Show>

          {/* Step 3: Confirmation */}
          <Show when={getCurrentStep() === 3}>
            <article class="space-y-6" role="tabpanel" aria-labelledby="step3-heading">
              <header class="text-center mb-8">
                <h1 id="step3-heading" class={`text-2xl font-bold mb-2 ${isDarkMode ? 'text-white' : 'text-gray-800'}`}>
                  Confirm Booking
                </h1>
                <p class={`${isDarkMode ? 'text-gray-300' : 'text-gray-600'}`}>
                  Review all details before confirming
                </p>
              </header>

              {/* Booking summary */}
              <section class={`p-6 rounded-lg ${isDarkMode ? 'bg-gray-700/50' : 'bg-gray-50'
                } border ${isDarkMode ? 'border-gray-600' : 'border-gray-200'}`}
                aria-labelledby="booking-summary-heading">

                <h2 id="booking-summary-heading" class="sr-only">Booking Summary</h2>

                {/* Service details */}
                <Show when={getSelectedService()}>
                  <div class="mb-4 pb-4 border-b border-gray-300">
                    <h3 class={`font-semibold mb-2 ${isDarkMode ? 'text-white' : 'text-gray-800'}`}>
                      📋 Service Details
                    </h3>
                    <p class={`${isDarkMode ? 'text-gray-300' : 'text-gray-600'}`}>
                      {getSelectedService()?.title}
                    </p>
                    <Show when={getDuration()}>
                      <p class={`text-sm ${isDarkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                        {getDuration().replace('days-', ' días - $').replace('min-', ' días - $')}
                      </p>
                    </Show>
                  </div>
                </Show>

                {/* Date and time */}
                <div class="mb-4 pb-4 border-b border-gray-300">
                  <h3 class={`font-semibold mb-2 ${isDarkMode ? 'text-white' : 'text-gray-800'}`}>
                    📅 Appointment Date & Time
                  </h3>
                  <div class="flex items-center space-x-2">
                    <p class={`font-medium ${isDarkMode ? 'text-gray-200' : 'text-gray-700'}`}>
                      {formatDateForDisplay(getDate())}
                    </p>
                    <Show when={isToday(getDate())}>
                      <span class="bg-amber-100 text-amber-800 px-2 py-1 rounded-full text-xs font-medium">
                        TODAY
                      </span>
                    </Show>
                  </div>
                  <p class={`text-lg font-semibold ${isDarkMode ? 'text-gray-200' : 'text-gray-700'}`}>
                    🕐 {formatTime(getTime())}
                  </p>
                </div>

                {/* Contact details */}
                <div class="mb-4 pb-4 border-b border-gray-300">
                  <h3 class={`font-semibold mb-2 ${isDarkMode ? 'text-white' : 'text-gray-800'}`}>
                    👤 Contact Information
                  </h3>
                  <dl class={`space-y-1 ${isDarkMode ? 'text-gray-300' : 'text-gray-600'}`}>
                    <div>
                      <dt class="inline font-medium">Name:</dt>
                      <dd class="inline ml-1">{getName()}</dd>
                    </div>
                    <div>
                      <dt class="inline font-medium">Email:</dt>
                      <dd class="inline ml-1">{getEmail()}</dd>
                    </div>
                    <div>
                      <dt class="inline font-medium">Visitors:</dt>
                      <dd class="inline ml-1">{getVisitors()} {getVisitors() === 1 ? 'person' : 'people'}</dd>
                    </div>
                  </dl>
                </div>

                {/* Additional message */}
                <Show when={getMessage()}>
                  <div>
                    <h3 class={`font-semibold mb-2 ${isDarkMode ? 'text-white' : 'text-gray-800'}`}>
                      💬 Additional Information
                    </h3>
                    <p class={`${isDarkMode ? 'text-gray-300' : 'text-gray-600'}`}>
                      {getMessage()}
                    </p>
                  </div>
                </Show>
              </section>

              {/* Confirm button */}
              <button
                type="submit"
                onClick={handleSubmit}
                disabled={getIsSubmitting()}
                aria-describedby="submit-help"
                class="w-full py-4 font-medium tracking-wider rounded-lg transition-all duration-300 relative overflow-hidden flex items-center justify-center group"
                style={{
                  "background-color": colors.accent,
                  color: "white",
                  "text-shadow": "0px 1px 2px rgba(0, 0, 0, 0.3)",
                }}
              >
                <span class="absolute inset-0 w-1/4 h-full transition-all duration-700 ease-out transform translate-x-[-100%] bg-white opacity-20 group-hover:translate-x-[400%] skew-x-12" aria-hidden="true"></span>
                <span class="relative flex items-center z-10">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span class="font-semibold">
                    {getIsSubmitting() ? getTranslation("Processing...") : getTranslation("CONFIRM BOOKING")}
                  </span>
                </span>
              </button>
              <div id="submit-help" class="sr-only">
                Click to submit your booking request. You will receive a confirmation email.
              </div>
            </article>
          </Show>
        </section>

        {/* SEO: Navigation with semantic structure */}
        <nav class="flex justify-between pt-6" aria-label="Form navigation">
          <button
            type="button"
            onClick={prevStep}
            disabled={getCurrentStep() === 1}
            aria-label="Go to previous step"
            class={`px-6 py-3 rounded-lg font-medium transition-all duration-300 ${getCurrentStep() === 1
              ? 'opacity-50 cursor-not-allowed'
              : isDarkMode
                ? 'bg-gray-700 hover:bg-gray-600 text-white'
                : 'bg-gray-200 hover:bg-gray-300 text-gray-700'
              }`}
          >
            ← Previous
          </button>

          <Show when={getCurrentStep() < totalSteps}>
            <button
              type="button"
              onClick={nextStep}
              disabled={!getIsStepValid()}
              aria-label="Go to next step"
              class={`px-6 py-3 rounded-lg font-medium transition-all duration-300 ${!getIsStepValid()
                ? 'opacity-50 cursor-not-allowed'
                : 'hover:shadow-lg transform hover:scale-105 bg-teal-500'
                }`}
              style={{
                backgroundColor: getIsStepValid() ? colors.accent : undefined,
                color: getIsStepValid() ? 'white' : undefined
              }}
            >
              Next →
            </button>
          </Show>
        </nav>

        {/* SEO: Response message with proper ARIA */}
        <Show when={getResponse().message}>
          <aside
            role="alert"
            aria-live="assertive"
            class={`p-4 rounded-lg text-center transition-all duration-300 ${getResponse().type === "success"
              ? "bg-green-500 text-white"
              : getResponse().type === "error"
                ? "bg-red-500 text-white"
                : "bg-blue-500 text-white"
              }`}
          >
            {getResponse().message}
          </aside>
        </Show>

        {/* Hidden fields for compatibility */}
        <div class="sr-only" aria-hidden="true">
          <input type="hidden" name="date" value={getDate()} />
          <input type="hidden" name="recipient_email" value={emailRecipient} />
          <Show when={!getShowServiceSelector() && getService()}>
            <input type="hidden" name="service" value={getService()} />
            <input type="hidden" name="service_title" value={getSelectedService()?.title || ""} />
          </Show>
        </div>
      </main>
    </>
  );
};

export default SolidBookingForm;