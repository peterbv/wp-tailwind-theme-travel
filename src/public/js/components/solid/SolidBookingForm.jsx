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

  // Form states - CORRECT SOLID.JS SYNTAX - TOUR SPECIFIC
  const [getName, setName] = createSignal("");
  const [getEmail, setEmail] = createSignal("");
  const [getPhone, setPhone] = createSignal("");
  const [getService, setService] = createSignal(""); // Tour selection
  const [getDate, setDate] = createSignal(""); // Departure date
  const [getTime, setTime] = createSignal(""); // Departure time
  const [getMessage, setMessage] = createSignal("");
  const [getDuration, setDuration] = createSignal(""); // Tour package duration
  const [getVisitors, setVisitors] = createSignal(1);
  const [getRecipientEmail, setRecipientEmail] = createSignal(emailRecipient);
  
  // New tour-specific fields
  const [getAccommodation, setAccommodation] = createSignal("");
  const [getRoomConfig, setRoomConfig] = createSignal("");
  const [getPickupLocation, setPickupLocation] = createSignal("");
  const [getEmergencyContact, setEmergencyContact] = createSignal("");
  const [getDietaryRestrictions, setDietaryRestrictions] = createSignal([]);
  const [getSpecialRequests, setSpecialRequests] = createSignal("");
  const [getGuideLanguage, setGuideLanguage] = createSignal("");
  
  // Multiple travelers data storage
  const [getTravelersData, setTravelersData] = createSignal([]);
  
  // Countries data for nationality selector
  const [getCountries, setCountries] = createSignal([]);

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
  
  // NEW: Tour configuration state
  const [getCurrentTourConfig, setCurrentTourConfig] = createSignal(null);

  const [getShowServiceSelector, setShowServiceSelector] = createSignal(!useSingleService);

  // Create a derived signal to detect if selected date is today
  const [getShowTodayWarning, setShowTodayWarning] = createSignal(false);

  // SIMPLIFIED step configuration
  const totalSteps = 3;

  const getStepConfig = () => {
    if (useSingleService || !shouldShowServiceSelector()) {
      return [
        { step: 1, title: "Fecha y Salida", icon: "calendar", description: "Selecciona fecha y hora de partida" },
        { step: 2, title: "Detalles de Viajeros", icon: "travelers", description: "Información personal y preferencias" },
        { step: 3, title: "Confirmación de Tour", icon: "luggage", description: "Revisa tu reserva de viaje" }
      ];
    } else {
      return [
        { step: 1, title: "Selección de Tour", icon: "map", description: "Elige tu aventura perfecta" },
        { step: 2, title: "Detalles de Viajeros", icon: "travelers", description: "Información personal y preferencias" },
        { step: 3, title: "Confirmación de Tour", icon: "luggage", description: "Revisa tu reserva de viaje" }
      ];
    }
  };

  // Theme colors - Minimal design focused
  const colors = {
    primary: "#6B7280",      // Gray-500 - neutral and clean
    secondary: "#F9FAFB",    // Gray-50 - very light background
    accent: accentColor,     // Keep user's accent color
    light: "#F3F4F6",       // Gray-100 - subtle backgrounds
    border: "#E5E7EB",       // Gray-200 - clean borders
    text: "#374151",         // Gray-700 - readable text
    dark: "#1F2937",         // Gray-800 - strong contrast
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
        // Check if time is required based on tour configuration
        const timeRequired = shouldShowTimeSelector();
        
        // Auto-set time for non-flexible schedules
        if (!timeRequired && getAvailableTimes().length > 0 && !getTime()) {
          setTime(getAvailableTimes()[0]);
        }
        
        if (useSingleService || !shouldShowServiceSelector()) {
          return getDate() && (timeRequired ? getTime() : true);
        } else {
          return getService() && getDate() && (timeRequired ? getTime() : true);
        }
      case 2:
        // Dynamic validation based on tour configuration
        const config = getCurrentTourConfig();
        let isValid = getName() && getEmail() && getPhone() && getVisitors() > 0;
        
        // Add pickup validation if required
        if (config && config.pickup_required && !getPickupLocation()) {
          isValid = false;
        }
        
        // Add accommodation validation if required
        if (config && config.includes_accommodation && (!getAccommodation() || !getRoomConfig())) {
          isValid = false;
        }
        
        // Validate travelers data if required by configuration
        const travelersData = getTravelersData();
        const expectedTravelers = getVisitors();
        
        if (config && (config.require_traveler_details || config.require_documents)) {
          // Check if we have the right number of travelers
          if (travelersData.length !== expectedTravelers) {
            isValid = false;
          }
          
          // Check if each traveler has required fields
          for (const traveler of travelersData) {
            if (config.require_traveler_details) {
              if (!traveler.name || !traveler.age) {
                isValid = false;
                break;
              }
            }
            
            if (config.require_documents) {
              if (!traveler.documentType || !traveler.documentNumber) {
                isValid = false;
                break;
              }
            }

            // Check new required fields
            if (config.required_traveler_fields && config.required_traveler_fields.includes('nationality')) {
              if (!traveler.nationality) {
                isValid = false;
                break;
              }
            }

            if (config.required_traveler_fields && config.required_traveler_fields.includes('gender')) {
              if (!traveler.gender) {
                isValid = false;
                break;
              }
            }
          }
        }
        
        return isValid;
      case 3:
        return true;
      default:
        return false;
    }
  };

  // Validation effect - be specific about dependencies to avoid loops
  createEffect(() => {
    const step = getCurrentStep();
    const isValid = validateStep(step);
    setIsStepValid(isValid);
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

  // Function to load countries data
  const loadCountriesData = async () => {
    try {
      const response = await fetch('/wp-content/themes/wp-tailwind-theme-travel/src/public/js/json/paises-del-mundo.json');
      if (response.ok) {
        const countries = await response.json();
        setCountries(countries);
        console.log("[DEBUG] Loaded countries:", countries.length);
      } else {
        console.warn("[DEBUG] Failed to load countries data");
      }
    } catch (error) {
      console.error("[DEBUG] Error loading countries:", error);
    }
  };

  onMount(async () => {
    try {
      await getFreshNonce();
      // Load countries data for nationality selector
      await loadCountriesData();
    } catch (error) {
      console.error("[DEBUG] Failed to load initial nonce:", error);
      setResponse({
        type: "error",
        message: getTranslation("Error loading form. Please refresh the page."),
      });
    }

    console.log("[DEBUG] Services data:", services);
    console.log("[DEBUG] useSingleService:", useSingleService);

    if (Array.isArray(services) && services.length > 0) {
      const initialServiceId = services[0].id;
      console.log("[DEBUG] Setting initial service:", initialServiceId);

      const serviceIdString = String(initialServiceId);
      setService(serviceIdString);

      // Para single service (single-tours), establecer configuración inmediatamente
      if (useSingleService) {
        const serviceObj = services[0];
        console.log("[DEBUG] Single service - serviceObj:", serviceObj);
        if (serviceObj && serviceObj.booking_config) {
          console.log("[DEBUG] Single service - setting tour configuration immediately:", serviceObj.booking_config);
          setCurrentTourConfig(serviceObj.booking_config);
          console.log("[DEBUG] Single service - current config after set:", getCurrentTourConfig());
        } else {
          console.log("[DEBUG] Single service - no booking_config found in serviceObj");
        }
      }

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
      
      // NEW: Set tour configuration
      if (serviceObj.booking_config) {
        console.log("[DEBUG] updateServiceDetails - Setting tour configuration:", serviceObj.booking_config);
        setCurrentTourConfig(serviceObj.booking_config);
        console.log("[DEBUG] updateServiceDetails - current config after set:", getCurrentTourConfig());
      } else {
        console.log("[DEBUG] updateServiceDetails - no booking_config, using fallback");
        // Fallback configuration for older tours
        setCurrentTourConfig({
          duration_days: 1,
          includes_accommodation: false,
          requires_documents: false,
          has_flexible_schedule: true,
          pickup_required: false,
          emergency_contact_required: false,
          languages_available: ['es'],
          required_traveler_fields: []
        });
      }

      if (Array.isArray(serviceObj.hours) && serviceObj.hours.length > 0) {
        console.log("[DEBUG] Setting available times:", serviceObj.hours);
        console.log("[DEBUG] Tour ID:", serviceObj.id, "- Available Times:", serviceObj.hours, "- Hours from Service:", serviceObj.hours);
        setAvailableTimes([...serviceObj.hours]);
      } else {
        console.log("[DEBUG] No hours found for service - serviceObj.hours:", serviceObj.hours);
        console.log("[DEBUG] Tour ID:", serviceObj.id, "- Full service object:", serviceObj);
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
  
  // NEW: Helper functions to check if fields should be displayed based on tour configuration
  const shouldShowAccommodationFields = createMemo(() => {
    const config = getCurrentTourConfig();
    // Mostrar siempre como opcional, pero marcar como requerido si está configurado
    return true;
  });
  
  const shouldShowPickupField = createMemo(() => {
    const config = getCurrentTourConfig();
    // Mostrar siempre como opcional, pero marcar como requerido si está configurado
    return true;
  });
  
  const shouldShowTimeSelector = createMemo(() => {
    const availableTimes = getAvailableTimes();
    // Mostrar selector si hay más de una hora disponible, sin importar la configuración
    return Array.isArray(availableTimes) && availableTimes.length > 1;
  });
  
  const shouldShowDocumentFields = createMemo(() => {
    const config = getCurrentTourConfig();
    console.log("[DEBUG] shouldShowDocumentFields - config:", config);
    const result = config && config.requires_documents;
    console.log("[DEBUG] shouldShowDocumentFields - result:", result);
    return result;
  });
  
  const isFieldRequired = (fieldName) => {
    const config = getCurrentTourConfig();
    console.log(`[DEBUG] isFieldRequired('${fieldName}') - config:`, config);
    if (!config) {
      console.log(`[DEBUG] isFieldRequired('${fieldName}') - no config, returning false`);
      return false;
    }
    const result = config.required_traveler_fields && config.required_traveler_fields.includes(fieldName);
    console.log(`[DEBUG] isFieldRequired('${fieldName}') - result:`, result);
    return result;
  };
  
  const getTourDurationDays = createMemo(() => {
    const config = getCurrentTourConfig();
    return config ? config.duration_days : 1;
  });

  // Initialize travelers data when visitor count changes (no circular dependency)
  createEffect(() => {
    const visitorCount = getVisitors();
    const currentData = getTravelersData();
    
    // Only update if visitor count actually changed the array length
    if (currentData.length !== visitorCount) {
      const newTravelersData = [];
      for (let i = 0; i < visitorCount; i++) {
        const existingData = currentData[i] || {};
        newTravelersData.push({
          id: i,
          name: i === 0 ? getName() : (existingData.name || ''),
          email: i === 0 ? getEmail() : (existingData.email || ''),
          phone: i === 0 ? getPhone() : (existingData.phone || ''),
          birthDate: existingData.birthDate || '',
          documentType: existingData.documentType || '',
          documentNumber: existingData.documentNumber || '',
          nationality: existingData.nationality || '',
          gender: existingData.gender || '',
          isStudent: existingData.isStudent || false,
          dietaryRestrictions: existingData.dietaryRestrictions || '',
          medicalConditions: existingData.medicalConditions || ''
        });
      }
      
      setTravelersData(newTravelersData);
    }
  });

  // Auto-select duration when only one option is available
  createEffect(() => {
    const options = getDurationOptions();
    if (options.length === 1 && !getDuration()) {
      setDuration(options[0].value);
    }
  });

  // Function to update traveler data
  const updateTravelerData = (index, field, value) => {
    const travelers = getTravelersData();
    const updatedTravelers = [...travelers];
    if (updatedTravelers[index]) {
      // Only update if the value actually changed
      if (updatedTravelers[index][field] !== value) {
        updatedTravelers[index] = {
          ...updatedTravelers[index],
          [field]: value
        };
        
        // If updating the main traveler (index 0), also update main form fields
        if (index === 0) {
          if (field === 'name' && getName() !== value) setName(value);
          if (field === 'email' && getEmail() !== value) setEmail(value);
          if (field === 'phone' && getPhone() !== value) setPhone(value);
        }
        
        setTravelersData(updatedTravelers);
      }
    }
  };

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
      formData.append("phone", getPhone());
      formData.append("accommodation", getAccommodation());
      formData.append("room_config", getRoomConfig());
      formData.append("pickup_location", getPickupLocation());
      formData.append("emergency_contact", getEmergencyContact());
      formData.append("special_requests", getSpecialRequests());
      formData.append("guide_language", getGuideLanguage());

      // Add travelers data
      const travelersData = getTravelersData();
      formData.append("travelers_data", JSON.stringify(travelersData));

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
          setPhone("");
          setAccommodation("");
          setRoomConfig("");
          setPickupLocation("");
          setEmergencyContact("");
          setSpecialRequests("");

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
        case 'map':
          return (
            <svg class={iconClass} fill="none" stroke="currentColor" viewBox="0 0 24 24" role="img" aria-label="Tour selection">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
            </svg>
          );
        case 'calendar':
          return (
            <svg class={iconClass} fill="none" stroke="currentColor" viewBox="0 0 24 24" role="img" aria-label="Fecha de partida">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          );
        case 'travelers':
          return (
            <svg class={iconClass} fill="none" stroke="currentColor" viewBox="0 0 24 24" role="img" aria-label="Información de viajeros">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
            </svg>
          );
        case 'luggage':
          return (
            <svg class={iconClass} fill="none" stroke="currentColor" viewBox="0 0 24 24" role="img" aria-label="Confirmación de viaje">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
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
                    options={getServiceOptions}
                    value={getService}
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
                      <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

              {/* Duration selector - Only show if more than 1 option */}
              <Show when={getDurationOptions().length > 1}>
                <div class="mb-6">
                  <CustomSelect
                    id={`${formId}-duration`}
                    name="duration"
                    options={getDurationOptions}
                    value={getDuration}
                    onChange={setDuration}
                    placeholder="Select tour package"
                    labelText="Tour Package"
                    required={true}
                    darkMode={isDarkMode}
                    colors={colors}
                    aria-describedby="duration-help"
                  />
                  <div id="duration-help" class="sr-only">
                    Selecciona el paquete de tour que prefieras. El precio varía según la duración del viaje.
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
                  labelText="Departure Date"
                  placeholder="Select your departure date"
                  darkMode={isDarkMode}
                  colors={colors}
                  aria-describedby="date-help"
                />
                <div id="date-help" class="sr-only">
                  Elige tu fecha de partida preferida para el tour. Las salidas del mismo día requieren contacto por WhatsApp.
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
                        <strong class="block">¡Tour para HOY ({formatDateForDisplay(getDate())})!</strong>
                        Las salidas del mismo día solo se confirman por WhatsApp.
                      </p>
                      <a 
                        href="https://api.whatsapp.com/send/?phone=51906597850&amp;text&amp;type=phone_number&amp;app_absent=0" 
                        target="_blank" 
                        rel="noopener noreferrer"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-300 block mt-2 inline-flex items-center"
                        aria-label="Contactarnos vía WhatsApp para tour del mismo día"
                      >
                        Contactar por WhatsApp
                      </a>
                    </div>
                  </div>
                </aside>
              </Show>

              {/* Time selector - Dynamic based on tour configuration */}
              <Show when={shouldShowTimeSelector()}>
                <div>
                  <CustomSelect
                    id={`${formId}-time`}
                    name="time"
                    options={getTimeOptions}
                    value={getTime}
                    onChange={setTime}
                    placeholder="Select departure time"
                    labelText="Departure Time"
                    required={true}
                    darkMode={isDarkMode}
                    colors={colors}
                    aria-describedby="time-help"
                  />
                  <div id="time-help" class="sr-only">
                    Selecciona tu hora de salida preferida para el tour
                  </div>

                  <Show when={getTimeOptions().length === 0}>
                    <div class={`text-center py-4 text-sm ${isDarkMode ? 'text-gray-400' : 'text-gray-500'}`} role="status" aria-live="polite">
                      <p>No departure times available</p>
                      <div class="text-xs mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        {getSelectedService()
                          ? (
                            <div>
                              <p><strong>Tour:</strong> {getSelectedService().title}</p>
                              <p><strong>Tour ID:</strong> {getSelectedService().id}</p>
                              <p><strong>Hours configured:</strong> {getAvailableTimes().length}</p>
                              <p><strong>Hours from service:</strong> {JSON.stringify(getSelectedService().hours || [])}</p>
                              <p><strong>useSingleService:</strong> {props.useSingleService ? "true" : "false"}</p>
                              <p class="mt-2 text-yellow-700">
                                <strong>⚠️ To configure departure times:</strong><br/>
                                1. Go to WordPress Admin → Tours → Edit this tour<br/>
                                2. Click on <strong>"Availability"</strong> tab<br/>
                                3. In <strong>"Departure Times"</strong> section, add times (e.g., 09:00, 14:00)<br/>
                                4. Click <strong>"Update"</strong> to save
                              </p>
                            </div>
                          )
                          : 'Please select a tour first'
                        }
                      </div>
                    </div>
                  </Show>
                </div>
              </Show>
              
              {/* Fixed departure time for single-time tours */}
              <Show when={!shouldShowTimeSelector() && getSelectedService() && getAvailableTimes().length > 0}>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                  <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                      <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                    </div>
                    <div>
                      <h3 class="text-sm font-medium text-gray-700">
                        Hora de Salida
                      </h3>
                      <p class="text-sm text-gray-600">
                        Este tour sale a las <strong>{getAvailableTimes()[0]}</strong>
                      </p>
                    </div>
                  </div>
                </div>
              </Show>
            </article>
          </Show>

          {/* Step 2: Personal Details */}
          <Show when={getCurrentStep() === 2}>
            <article class="space-y-6" role="tabpanel" aria-labelledby="step2-heading">
              <header class="text-center mb-8">
                <h1 id="step2-heading" class={`text-2xl font-bold mb-2 ${isDarkMode ? 'text-white' : 'text-gray-800'}`}>
                  Detalles del Viajero
                </h1>
                <p class={`${isDarkMode ? 'text-gray-300' : 'text-gray-600'}`}>
                  Información necesaria para confirmar tu reserva de tour
                </p>
              </header>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {/* Name field */}
                <div>
                  <label for={`${formId}-name`} class={`block text-sm font-medium mb-2 ${isDarkMode ? 'text-white' : 'text-gray-700'}`}>
                    Nombre Completo *
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
                    placeholder="Nombre completo del viajero principal"
                  />
                  <div id={`${formId}-name-help`} class="sr-only">
                    Ingrese el nombre completo tal como aparecerá en la reserva del tour
                  </div>
                </div>

                {/* Email field */}
                <div>
                  <label for={`${formId}-email`} class={`block text-sm font-medium mb-2 ${isDarkMode ? 'text-white' : 'text-gray-700'}`}>
                    Email de Contacto *
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
                    placeholder="tu@email.com"
                  />
                  <div id={`${formId}-email-help`} class="sr-only">
                    Ingresa una dirección de email válida para recibir la confirmación del tour
                  </div>
                </div>

                {/* Phone field - CRUCIAL for tours */}
                <div class="md:col-span-2">
                  <label for={`${formId}-phone`} class={`block text-sm font-medium mb-2 ${isDarkMode ? 'text-white' : 'text-gray-700'}`}>
                    Teléfono / WhatsApp *
                  </label>
                  <input
                    id={`${formId}-phone`}
                    type="tel"
                    value={getPhone()}
                    onInput={(e) => setPhone(e.target.value)}
                    required
                    autocomplete="tel"
                    aria-describedby={`${formId}-phone-help`}
                    class={`w-full px-4 py-3 ${isDarkMode
                      ? "bg-gray-800/80 border-gray-700 text-white"
                      : "bg-gray-50 border-gray-300 text-gray-900"
                      } border rounded-lg focus:ring-2 focus:outline-none transition-colors duration-200`}
                    style={{ "border-color": colors.accent }}
                    placeholder="+51 999 888 777"
                  />
                  <div id={`${formId}-phone-help`} class="sr-only">
                    Número de teléfono para contactarte sobre el tour y en caso de emergencias
                  </div>
                  <p class="text-xs text-gray-500 mt-1">
                    <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Preferimos WhatsApp para comunicación durante el tour y actualizaciones
                  </p>
                </div>

                {/* Accommodation fields - Always show but mark as optional */}
                <Show when={true}>
                  {/* Accommodation type */}
                  <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center mb-3">
                      <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                      </svg>
                      <label for={`${formId}-accommodation`} class="text-sm font-semibold text-gray-700">
                        Tipo de Alojamiento {(() => {
                          const config = getCurrentTourConfig();
                          return config && config.includes_accommodation ? '*' : '(opcional)';
                        })()}
                      </label>
                    </div>
                    <select
                      id={`${formId}-accommodation`}
                      value={getAccommodation()}
                      onInput={(e) => setAccommodation(e.target.value)}
                      aria-describedby={`${formId}-accommodation-help`}
                      class="w-full px-4 py-3 bg-white border-amber-300 text-gray-900 border rounded-lg focus:ring-2 focus:ring-amber-300 focus:outline-none transition-colors duration-200"
                    >
                      <option value="">Select accommodation type...</option>
                      <option value="hotel-3star">3-Star Hotel</option>
                      <option value="hotel-4star">4-Star Hotel</option>
                      <option value="hotel-5star">5-Star Hotel (Luxury)</option>
                      <option value="hostel">Hostel/Backpacker</option>
                      <option value="airbnb">Airbnb/Apartment</option>
                      <option value="camping">Camping</option>
                      <option value="lodge">Lodge/Refuge</option>
                    </select>
                    <div id={`${formId}-accommodation-help`} class="sr-only">
                      Selecciona el tipo de alojamiento incluido en tu tour de {getTourDurationDays()} días
                    </div>
                    <p class="text-xs text-amber-700 mt-2 flex items-center">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      Alojamiento incluido para tour de {getTourDurationDays()} {getTourDurationDays() === 1 ? 'día' : 'días'}
                    </p>
                  </div>

                  {/* Room configuration */}
                  <Show when={getAccommodation() && getAccommodation() !== 'camping' && getAccommodation() !== 'none'}>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                      <div class="flex items-center mb-3">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <label for={`${formId}-room-config`} class="text-sm font-semibold text-gray-700">
                          Room Configuration *
                        </label>
                      </div>
                      <select
                        id={`${formId}-room-config`}
                        value={getRoomConfig()}
                        onInput={(e) => setRoomConfig(e.target.value)}
                        aria-describedby={`${formId}-room-config-help`}
                        class="w-full px-4 py-3 bg-white border-gray-300 text-gray-900 border rounded-lg focus:ring-2 focus:ring-gray-300 focus:outline-none transition-colors duration-200"
                      >
                        <option value="">Select room configuration...</option>
                        <option value="single">Single Room</option>
                        <option value="double">Double Room (2 beds)</option>
                        <option value="matrimonial">Double Room (1 bed)</option>
                        <option value="triple">Triple Room</option>
                        <option value="family">Family Room</option>
                        <option value="shared">Shared Room</option>
                      </select>
                      <div id={`${formId}-room-config-help`} class="sr-only">
                        Selecciona la configuración de habitación que necesitas para {getVisitors()} viajero{getVisitors() > 1 ? 's' : ''}
                      </div>
                      <p class="text-xs text-blue-700 mt-2">
                        Para {getVisitors()} viajero{getVisitors() > 1 ? 's' : ''} - elige la opción que mejor se adapte a tu grupo
                      </p>
                    </div>
                  </Show>
                </Show>

                {/* Pickup location - Always show as optional */}
                <Show when={true}>
                  <div class="md:col-span-2">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                      <div class="flex items-center mb-3">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <label for={`${formId}-pickup`} class="text-sm font-semibold text-gray-700">
                          Lugar de Recojo {(() => {
                            const config = getCurrentTourConfig();
                            return config && config.pickup_required ? '*' : '(opcional)';
                          })()}
                        </label>
                      </div>
                      <input
                        id={`${formId}-pickup`}
                        type="text"
                        value={getPickupLocation()}
                        onInput={(e) => setPickupLocation(e.target.value)}
                        aria-describedby={`${formId}-pickup-help`}
                        class="w-full px-4 py-3 bg-white border-gray-300 text-gray-900 border rounded-lg focus:ring-2 focus:ring-gray-300 focus:outline-none transition-colors duration-200"
                        placeholder="Hotel, dirección o punto de referencia donde te recogeremos"
                      />
                      <div id={`${formId}-pickup-help`} class="sr-only">
                        Indica dónde prefieres que te recojamos para el inicio del tour
                      </div>
                      <p class="text-xs text-green-700 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Te contactaremos 1 hora antes para confirmar la ubicación exacta
                      </p>
                    </div>
                  </div>
                </Show>
              </div>

              {/* Number of travelers */}
              <div role="group" aria-labelledby={`${formId}-travelers-label`} class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                <div class="flex items-center mb-4">
                  <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                  <h3 id={`${formId}-travelers-label`} class={`text-lg font-semibold ${isDarkMode ? 'text-white' : 'text-gray-800'}`}>
                    Número de Viajeros *
                  </h3>
                </div>
                <div class="flex items-center justify-center space-x-8" aria-describedby={`${formId}-travelers-help`}>
                  <button
                    type="button"
                    onClick={() => getVisitors() > 1 && setVisitors((prev) => prev - 1)}
                    disabled={getVisitors() <= 1}
                    aria-label="Disminuir número de viajeros"
                    class={`p-4 rounded-full transition-all duration-300 shadow-md ${getVisitors() <= 1
                      ? 'opacity-50 cursor-not-allowed bg-gray-200'
                      : 'bg-white hover:bg-teal-50 transform hover:scale-110 hover:shadow-lg border-2 border-teal-200'
                      }`}
                  >
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4" />
                    </svg>
                  </button>

                  <div class={`text-center min-w-32 ${isDarkMode ? 'text-white' : 'text-gray-800'}`} aria-live="polite">
                    <div class="bg-white rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-2 shadow-lg border-4 border-teal-100">
                      <span class="text-3xl font-bold text-gray-500" aria-label={`${getVisitors()} ${getVisitors() === 1 ? 'viajero' : 'viajeros'}`}>
                        {getVisitors()}
                      </span>
                    </div>
                    <p class="text-sm font-medium text-teal-700" aria-hidden="true">
                      {getVisitors() === 1 ? 'Viajero' : 'Viajeros'}
                    </p>
                  </div>

                  <button
                    type="button"
                    onClick={() => getVisitors() < 20 && setVisitors((prev) => prev + 1)}
                    disabled={getVisitors() >= 20}
                    aria-label="Aumentar número de viajeros"
                    class={`p-4 rounded-full transition-all duration-300 shadow-md ${getVisitors() >= 20
                      ? 'opacity-50 cursor-not-allowed bg-gray-200'
                      : 'bg-white hover:bg-teal-50 transform hover:scale-110 hover:shadow-lg border-2 border-teal-200'
                      }`}
                  >
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                    </svg>
                  </button>
                </div>
                <div id={`${formId}-travelers-help`} class="sr-only">
                  Usa los botones para ajustar el número de viajeros (1-20 personas)
                </div>
                <div class="flex items-center justify-center text-xs text-gray-500 mt-3 font-medium">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Máximo 20 viajeros por reserva
                </div>
              </div>

              {/* Individual Traveler Information Forms */}
              <div class="space-y-6">
                <For each={Array.from({length: getVisitors()}, (_, i) => i)}>
                  {(travelerIndex) => {
                    const travelerNumber = travelerIndex + 1;
                    const isMainTraveler = travelerIndex === 0;
                    
                    return (
                      <div class={`p-6 rounded-xl border-2 transition-all duration-300 ${
                        isMainTraveler 
                          ? (isDarkMode ? 'border-teal-500 bg-gray-800/70' : 'border-teal-400 bg-teal-50/50')
                          : (isDarkMode ? 'border-gray-600 bg-gray-800/40' : 'border-gray-200 bg-gray-50/50')
                      }`}>
                        <div class="flex items-center mb-4">
                          {isMainTraveler ? (
                            <>
                              <svg class="w-6 h-6 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                              </svg>
                              <h3 class={`text-lg font-semibold ${isDarkMode ? 'text-white' : 'text-gray-800'}`}>
                                Viajero Principal (Contacto)
                              </h3>
                            </>
                          ) : (
                            <>
                              <svg class="w-6 h-6 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m3 5.197H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                              </svg>
                              <h3 class={`text-lg font-semibold ${isDarkMode ? 'text-white' : 'text-gray-800'}`}>
                                Viajero {travelerNumber}
                              </h3>
                            </>
                          )}
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                          {/* Full Name */}
                          <div>
                            <label class={`block text-sm font-medium mb-2 ${isDarkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                              Nombre Completo *
                            </label>
                            <input
                              type="text"
                              value={getTravelersData()[travelerIndex]?.name || ''}
                              onInput={(e) => {
                                updateTravelerData(travelerIndex, 'name', e.target.value);
                              }}
                              required={true}
                              class={`w-full px-4 py-3 ${isDarkMode
                                ? "bg-gray-800/80 border-gray-700 text-white placeholder-gray-400"
                                : "bg-white border-gray-300 text-gray-900 placeholder-gray-500"
                              } border rounded-lg focus:ring-2 focus:ring-teal-300 focus:border-teal-400 focus:outline-none transition-all duration-200`}
                              placeholder="Ingresa el nombre completo"
                            />
                          </div>
                          
                          {/* Email (only for main traveler) */}
                          <Show when={isMainTraveler}>
                            <div>
                              <label class={`block text-sm font-medium mb-2 ${isDarkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                Email de Contacto *
                              </label>
                              <input
                                type="email"
                                value={getEmail()}
                                onInput={(e) => setEmail(e.target.value)}
                                required={true}
                                class={`w-full px-4 py-3 ${isDarkMode
                                  ? "bg-gray-800/80 border-gray-700 text-white placeholder-gray-400"
                                  : "bg-white border-gray-300 text-gray-900 placeholder-gray-500"
                                } border rounded-lg focus:ring-2 focus:ring-teal-300 focus:border-teal-400 focus:outline-none transition-all duration-200`}
                                placeholder="ejemplo@email.com"
                              />
                            </div>
                          </Show>
                          
                          {/* Phone/WhatsApp (only for main traveler) */}
                          <Show when={isMainTraveler}>
                            <div>
                              <label class={`block text-sm font-medium mb-2 ${isDarkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                Teléfono/WhatsApp *
                              </label>
                              <input
                                type="tel"
                                value={getPhone()}
                                onInput={(e) => setPhone(e.target.value)}
                                required={true}
                                class={`w-full px-4 py-3 ${isDarkMode
                                  ? "bg-gray-800/80 border-gray-700 text-white placeholder-gray-400"
                                  : "bg-white border-gray-300 text-gray-900 placeholder-gray-500"
                                } border rounded-lg focus:ring-2 focus:ring-teal-300 focus:border-teal-400 focus:outline-none transition-all duration-200`}
                                placeholder="+51 999 999 999"
                              />
                            </div>
                          </Show>
                          
                          {/* Birth Date - Show for all travelers if required */}
                          <Show when={isFieldRequired('birth_date')}>
                            <div>
                              <label class={`block text-sm font-medium mb-2 ${isDarkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                Birth Date *
                              </label>
                              <input
                                type="date"
                                value={getTravelersData()[travelerIndex]?.birthDate || ''}
                                onInput={(e) => updateTravelerData(travelerIndex, 'birthDate', e.target.value)}
                                class={`w-full px-4 py-3 ${isDarkMode
                                  ? "bg-gray-800/80 border-gray-700 text-white"
                                  : "bg-white border-gray-300 text-gray-900"
                                } border rounded-lg focus:ring-2 focus:ring-teal-300 focus:border-teal-400 focus:outline-none transition-all duration-200`}
                              />
                            </div>
                          </Show>
                          
                          {/* Document fields - Show if documents required */}
                          <Show when={shouldShowDocumentFields()}>
                            <div>
                              <label class={`block text-sm font-medium mb-2 ${isDarkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                Tipo de Documento *
                              </label>
                              <select
                                value={getTravelersData()[travelerIndex]?.documentType || ''}
                                onInput={(e) => updateTravelerData(travelerIndex, 'documentType', e.target.value)}
                                class={`w-full px-4 py-3 ${isDarkMode
                                  ? "bg-gray-800/80 border-gray-700 text-white"
                                  : "bg-white border-gray-300 text-gray-900"
                                } border rounded-lg focus:ring-2 focus:ring-teal-300 focus:border-teal-400 focus:outline-none transition-all duration-200`}
                              >
                                <option value="">Seleccionar tipo</option>
                                <option value="dni">DNI</option>
                                <option value="passport">Pasaporte</option>
                                <option value="ce">Carnet de Extranjería</option>
                              </select>
                            </div>
                            
                            <div>
                              <label class={`block text-sm font-medium mb-2 ${isDarkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                Número de Documento *
                              </label>
                              <input
                                type="text"
                                value={getTravelersData()[travelerIndex]?.documentNumber || ''}
                                onInput={(e) => updateTravelerData(travelerIndex, 'documentNumber', e.target.value)}
                                class={`w-full px-4 py-3 ${isDarkMode
                                  ? "bg-gray-800/80 border-gray-700 text-white placeholder-gray-400"
                                  : "bg-white border-gray-300 text-gray-900 placeholder-gray-500"
                                } border rounded-lg focus:ring-2 focus:ring-teal-300 focus:border-teal-400 focus:outline-none transition-all duration-200`}
                                placeholder="Número del documento"
                              />
                            </div>
                          </Show>
                          
                          {/* Nationality field - Show if required */}
                          <Show when={isFieldRequired('nationality')}>
                            <div>
                              <label class={`block text-sm font-medium mb-2 ${isDarkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                Nationality *
                              </label>
                              <select
                                value={getTravelersData()[travelerIndex]?.nationality || ''}
                                onInput={(e) => updateTravelerData(travelerIndex, 'nationality', e.target.value)}
                                class={`w-full px-4 py-3 ${isDarkMode
                                  ? "bg-gray-800/80 border-gray-700 text-white"
                                  : "bg-white border-gray-300 text-gray-900"
                                } border rounded-lg focus:ring-2 focus:ring-teal-300 focus:border-teal-400 focus:outline-none transition-all duration-200`}
                              >
                                <option value="">Select nationality</option>
                                <For each={getCountries()}>
                                  {(country) => (
                                    <option value={country.shortName}>
                                      {country.shortName}
                                    </option>
                                  )}
                                </For>
                              </select>
                            </div>
                          </Show>
                          
                          {/* Gender field - Show if required */}
                          <Show when={isFieldRequired('gender')}>
                            <div>
                              <label class={`block text-sm font-medium mb-2 ${isDarkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                Gender *
                              </label>
                              <select
                                value={getTravelersData()[travelerIndex]?.gender || ''}
                                onInput={(e) => updateTravelerData(travelerIndex, 'gender', e.target.value)}
                                class={`w-full px-4 py-3 ${isDarkMode
                                  ? "bg-gray-800/80 border-gray-700 text-white"
                                  : "bg-white border-gray-300 text-gray-900"
                                } border rounded-lg focus:ring-2 focus:ring-teal-300 focus:border-teal-400 focus:outline-none transition-all duration-200`}
                              >
                                <option value="">Select gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                                <option value="prefer-not-to-say">Prefer not to say</option>
                              </select>
                            </div>
                          </Show>
                          
                          {/* Student status field - Show if required */}
                          <Show when={isFieldRequired('is_student')}>
                            <div>
                              <label class={`block text-sm font-medium mb-2 ${isDarkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                Student Status
                              </label>
                              <div class="flex items-center space-x-3">
                                <label class="flex items-center">
                                  <input
                                    type="checkbox"
                                    checked={getTravelersData()[travelerIndex]?.isStudent || false}
                                    onInput={(e) => updateTravelerData(travelerIndex, 'isStudent', e.target.checked)}
                                    class="w-4 h-4 text-gray-500 bg-gray-100 border-gray-300 rounded focus:ring-teal-500 focus:ring-2"
                                  />
                                  <span class={`ml-2 text-sm ${isDarkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                    I am a student (discount may apply)
                                  </span>
                                </label>
                              </div>
                            </div>
                          </Show>
                        </div>
                        
                        {/* Dietary restrictions and medical conditions for all travelers */}
                        <div class="mt-4 space-y-4">
                          <Show when={isFieldRequired('dietary_restrictions')}>
                            <div>
                              <label class={`block text-sm font-medium mb-2 ${isDarkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                Restricciones Alimentarias
                              </label>
                              <textarea
                                rows="2"
                                value={getTravelersData()[travelerIndex]?.dietaryRestrictions || ''}
                                onInput={(e) => updateTravelerData(travelerIndex, 'dietaryRestrictions', e.target.value)}
                                class={`w-full px-4 py-3 ${isDarkMode
                                  ? "bg-gray-800/80 border-gray-700 text-white placeholder-gray-400"
                                  : "bg-white border-gray-300 text-gray-900 placeholder-gray-500"
                                } border rounded-lg focus:ring-2 focus:ring-teal-300 focus:border-teal-400 focus:outline-none transition-all duration-200 resize-none`}
                                placeholder="Vegetariano, vegano, alergias, etc..."
                              />
                            </div>
                          </Show>
                          
                          <Show when={isFieldRequired('medical_conditions')}>
                            <div>
                              <label class={`block text-sm font-medium mb-2 ${isDarkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                Condiciones Médicas Relevantes
                              </label>
                              <textarea
                                rows="2"
                                value={getTravelersData()[travelerIndex]?.medicalConditions || ''}
                                onInput={(e) => updateTravelerData(travelerIndex, 'medicalConditions', e.target.value)}
                                class={`w-full px-4 py-3 ${isDarkMode
                                  ? "bg-gray-800/80 border-gray-700 text-white placeholder-gray-400"
                                  : "bg-white border-gray-300 text-gray-900 placeholder-gray-500"
                                } border rounded-lg focus:ring-2 focus:ring-teal-300 focus:border-teal-400 focus:outline-none transition-all duration-200 resize-none`}
                                placeholder="Diabetes, hipertensión, movilidad reducida, etc..."
                              />
                            </div>
                          </Show>
                        </div>
                      </div>
                    );
                  }}
                </For>
              </div>
              
              {/* Additional Tour Configuration Sections */}
              
              {/* Pickup Location field - Dynamic based on tour configuration */}
              <Show when={shouldShowPickupField()}>
                <div class="p-6 rounded-lg bg-gray-50 border border-gray-200">
                  <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <label class="text-lg font-semibold text-green-900">
                      Lugar de Recojo
                    </label>
                  </div>
                  <input
                    type="text"
                    value={getPickupLocation()}
                    onInput={(e) => setPickupLocation(e.target.value)}
                    class="w-full px-4 py-3 bg-white border-green-300 text-green-900 placeholder-green-600 border rounded-lg focus:ring-2 focus:ring-green-300 focus:border-green-400 focus:outline-none transition-all duration-200"
                    placeholder="Hotel, aeropuerto, dirección específica..."
                  />
                  <p class="text-sm text-green-700 mt-2">
                    Indica dónde te gustaría que te recojan para iniciar el tour
                  </p>
                </div>
              </Show>

              {/* Tour Guide Language Preference */}
              <Show when={isFieldRequired('guide_language')}>
                <div class="p-6 rounded-lg bg-gray-50 border border-gray-200">
                  <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                    </svg>
                    <label class="text-lg font-semibold text-indigo-900">
                      Idioma del Guía Preferido
                    </label>
                  </div>
                  <select
                    value={getGuideLanguage()}
                    onInput={(e) => setGuideLanguage(e.target.value)}
                    class="w-full px-4 py-3 bg-white border-indigo-300 text-indigo-900 border rounded-lg focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 focus:outline-none transition-all duration-200"
                  >
                    <option value="">Selecciona idioma preferido</option>
                    <option value="spanish">Español</option>
                    <option value="english">Inglés</option>
                    <option value="portuguese">Portugués</option>
                    <option value="french">Francés</option>
                    <option value="german">Alemán</option>
                    <option value="italian">Italiano</option>
                  </select>
                  <p class="text-sm text-indigo-700 mt-2">
                    Indicamos tu preferencia al asignar el guía turístico
                  </p>
                </div>
              </Show>

              {/* Emergency Contact */}
              <div class="p-6 rounded-lg bg-gray-50 border border-gray-200">
                <div class="flex items-center mb-4">
                  <svg class="w-6 h-6 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2zM12 6v6" />
                  </svg>
                  <label class="text-lg font-semibold text-red-900">
                    Contacto de Emergencia (Opcional)
                  </label>
                </div>
                <input
                  type="text"
                  value={getEmergencyContact()}
                  onInput={(e) => setEmergencyContact(e.target.value)}
                  class="w-full px-4 py-3 bg-white border-red-300 text-red-900 placeholder-red-600 border rounded-lg focus:ring-2 focus:ring-red-300 focus:border-red-400 focus:outline-none transition-all duration-200"
                  placeholder="Nombre y teléfono de familiar/amigo"
                />
                <p class="text-sm text-red-700 mt-2">
                  Persona a contactar en caso de emergencia durante el tour
                </p>
              </div>

              {/* General Special Requests */}
              <div class="p-6 rounded-lg bg-gray-50 border border-gray-200">
                <div class="flex items-center mb-4">
                  <svg class="w-6 h-6 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                  </svg>
                  <label class="text-lg font-semibold text-orange-900">
                    Solicitudes Especiales (Opcional)
                  </label>
                </div>
                <textarea
                  value={getSpecialRequests()}
                  onInput={(e) => setSpecialRequests(e.target.value)}
                  rows="4"
                  class="w-full px-4 py-3 bg-white border-orange-300 text-orange-900 placeholder-orange-600 border rounded-lg focus:ring-2 focus:ring-orange-300 focus:border-orange-400 focus:outline-none transition-all duration-200 resize-none"
                  placeholder="Menciona cualquier solicitud especial para tu viaje: celebraciones, actividades específicas, necesidades especiales..."
                />
                <div class="flex flex-wrap gap-2 mt-3">
                  <div class="flex items-center text-xs bg-orange-200 text-orange-800 px-2 py-1 rounded-full">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    Celebración especial
                  </div>
                  <div class="flex items-center text-xs bg-orange-200 text-orange-800 px-2 py-1 rounded-full">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Sesión fotográfica
                  </div>
                  <div class="flex items-center text-xs bg-orange-200 text-orange-800 px-2 py-1 rounded-full">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Actividad especial
                  </div>
                </div>
              </div>
            </article>
          </Show>

          {/* Step 3: Confirmation */}
          <Show when={getCurrentStep() === 3}>
            <article class="space-y-6" role="tabpanel" aria-labelledby="step3-heading">
              <header class="text-center mb-8">
                <h1 id="step3-heading" class={`text-2xl font-bold mb-2 ${isDarkMode ? 'text-white' : 'text-gray-800'}`}>
                  Confirmar Reserva de Tour
                </h1>
                <p class={`${isDarkMode ? 'text-gray-300' : 'text-gray-600'}`}>
                  Revisa todos los detalles de tu viaje antes de confirmar
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
                      <svg class="w-5 h-5 inline mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                      </svg>
                      Detalles del Tour
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
                    <svg class="w-5 h-5 inline mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Fecha y Hora de Partida
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
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  {formatTime(getTime())}
                  </p>
                </div>

                {/* Contact details */}
                <div class="mb-4 pb-4 border-b border-gray-300">
                  <h3 class={`font-semibold mb-2 ${isDarkMode ? 'text-white' : 'text-gray-800'}`}>
                    <svg class="w-5 h-5 inline mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Información del Viajero
                  </h3>
                  <dl class={`space-y-2 ${isDarkMode ? 'text-gray-300' : 'text-gray-600'}`}>
                    <div>
                      <dt class="inline font-medium">Nombre:</dt>
                      <dd class="inline ml-1">{getName()}</dd>
                    </div>
                    <div>
                      <dt class="inline font-medium">Email:</dt>
                      <dd class="inline ml-1">{getEmail()}</dd>
                    </div>
                    <div>
                      <dt class="inline font-medium">Teléfono:</dt>
                      <dd class="inline ml-1">{getPhone()}</dd>
                    </div>
                    <div>
                      <dt class="inline font-medium">Viajeros:</dt>
                      <dd class="inline ml-1">{getVisitors()} {getVisitors() === 1 ? 'viajero' : 'viajeros'}</dd>
                    </div>
                    <Show when={getAccommodation()}>
                      <div>
                        <dt class="inline font-medium">Alojamiento:</dt>
                        <dd class="inline ml-1 capitalize">{getAccommodation().replace('-', ' ')}</dd>
                      </div>
                    </Show>
                    <Show when={getRoomConfig()}>
                      <div>
                        <dt class="inline font-medium">Habitación:</dt>
                        <dd class="inline ml-1 capitalize">{getRoomConfig()}</dd>
                      </div>
                    </Show>
                    <Show when={getPickupLocation()}>
                      <div>
                        <dt class="inline font-medium">Lugar de recojo:</dt>
                        <dd class="inline ml-1">{getPickupLocation()}</dd>
                      </div>
                    </Show>
                  </dl>
                </div>

                {/* Additional message */}
                <Show when={getMessage()}>
                  <div>
                    <h3 class={`font-semibold mb-2 ${isDarkMode ? 'text-white' : 'text-gray-800'}`}>
                      <svg class="w-5 h-5 inline mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10m0 0V6a2 2 0 00-2-2H9a2 2 0 00-2 2v2m0 0v10a2 2 0 002 2h8a2 2 0 002-2V8M9 12h6m-6 4h6" />
                      </svg>
                      Preferencias Especiales
                    </h3>
                    <p class={`${isDarkMode ? 'text-gray-300' : 'text-gray-600'}`}>
                      {getMessage()}
                    </p>
                  </div>
                </Show>
              </section>

              {/* Confirm reservation button */}
              <button
                type="submit"
                onClick={handleSubmit}
                disabled={getIsSubmitting()}
                aria-describedby="submit-help"
                class="w-full py-4 font-semibold text-lg rounded-lg transition-all duration-200 flex items-center justify-center hover:opacity-90"
                style={{
                  background: colors.accent,
                  color: "white",
                }}
              >
                {getIsSubmitting() ? (
                    <>
                      <svg class="w-6 h-6 mr-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                      </svg>
                      <span>Procesando Reserva...</span>
                    </>
                  ) : (
                    <>
                      <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                      </svg>
                      <span>CONFIRMAR RESERVA DE VIAJE</span>
                    </>
                  )}
              </button>
              <div id="submit-help" class="sr-only">
                Haz clic para enviar tu solicitud de reserva. Recibirás un email de confirmación del tour.
              </div>
            </article>
          </Show>
        </section>

        {/* Travel-themed Navigation */}
        <nav class="flex justify-between items-center pt-8" aria-label="Navegación del formulario">
          <button
            type="button"
            onClick={prevStep}
            disabled={getCurrentStep() === 1}
            aria-label="Volver al paso anterior"
            class={`flex items-center px-4 py-2 rounded-lg font-medium transition-all duration-200 ${getCurrentStep() === 1
              ? 'opacity-50 cursor-not-allowed text-gray-400'
              : 'bg-gray-100 hover:bg-gray-200 text-gray-700'
              }`}
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Anterior
          </button>

          <div class="flex items-center space-x-2">
            <span class={`text-sm font-medium ${isDarkMode ? 'text-gray-300' : 'text-gray-600'}`}>
              Paso {getCurrentStep()} de {totalSteps}
            </span>
            <div class="flex space-x-1">
              {Array.from({length: totalSteps}, (_, i) => (
                <div 
                  class={`w-2 h-2 rounded-full transition-all duration-200 ${
                    i + 1 <= getCurrentStep() ? 'bg-gray-600' : 'bg-gray-300'
                  }`}
                />
              ))}
            </div>
          </div>

          <Show when={getCurrentStep() < totalSteps}>
            <button
              type="button"
              onClick={nextStep}
              disabled={!getIsStepValid()}
              aria-label="Continuar al siguiente paso"
              class={`flex items-center px-4 py-2 rounded-lg font-medium transition-all duration-200 ${!getIsStepValid()
                ? 'opacity-50 cursor-not-allowed bg-gray-200 text-gray-400'
                : 'text-white hover:opacity-90'
                }`}
              style={!getIsStepValid() ? {} : { background: colors.accent }}
            >
              Continuar
              <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
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