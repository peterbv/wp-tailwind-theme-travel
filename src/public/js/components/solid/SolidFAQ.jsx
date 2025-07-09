// src/public/js/components/solid/SolidFAQ.jsx
import { createSignal, createEffect, onMount, For, Show } from "solid-js";
import { __ } from "../../solid-core";

/**
 * Elegant FAQ (Frequently Asked Questions) component for spa and wellness sites
 * Enhanced design with focus on elegance and usability
 * With full internationalization support
 *
 * @param {Object} props Component properties
 * @returns {JSX.Element} Solid Component
 */
const SolidFAQ = (props) => {
  // Properties with default values
  const {
    title = __("Frequently Asked Questions", "wp-tailwind-blocks"),
    subtitle = __("We answer your questions", "wp-tailwind-blocks"),
    faqs = [],
    backgroundColor = "#F7EDE2", // Warm background color
    textColor = "#424242", // Dark text color
    accentColor = "#D4B254", // Elegant gold
    secondaryColor = "#8BAB8D", // Sage green for secondary elements
    layout = "full", // 'full' or 'boxed'
    showTopWave = true,
    showBottomWave = true,
    contactText = __("Do you have more questions?", "wp-tailwind-blocks"),
    contactUrl = "#contact",
    showContactButton = true,
    openFirst = false,
    singleOpen = false, // Only one question open at a time
    animateEntrance = true, // Animate entrance of FAQs
  } = props;

  // Function to get specific translations for this component
  const getTranslation = (text, domain = "wptbt-faq-block") => {
    // First look in component-specific translations
    const componentTranslations = window.wptbtI18n_faq || {};
    if (componentTranslations[text]) {
      return componentTranslations[text];
    }

    // If not found, use the general __ function
    return __(text, domain);
  };

  // State for open questions
  const [openQuestions, setOpenQuestions] = createSignal(new Set());
  const [hasAnimated, setHasAnimated] = createSignal(false);
  const [activeQuestion, setActiveQuestion] = createSignal(null);

  // Handle opening and closing of questions
  const toggleQuestion = (index) => {
    setOpenQuestions((prev) => {
      const newSet = new Set(prev);

      // If the question is already open, close it
      if (newSet.has(index)) {
        newSet.delete(index);
      } else {
        // If only one question is allowed to be open at a time, close the others
        if (singleOpen) {
          newSet.clear();
        }
        newSet.add(index);
      }

      return newSet;
    });
  };

  // Check if a question is open
  const isQuestionOpen = (index) => {
    return openQuestions().has(index);
  };

  // Initialize the first question as open if specified
  onMount(() => {
    if (openFirst && faqs.length > 0) {
      toggleQuestion(0);
    }

    // Animate entrance
    if (animateEntrance) {
      setTimeout(() => {
        setHasAnimated(true);
      }, 100);
    } else {
      setHasAnimated(true);
    }
  });

  // Set up intersection observer for animations
  let containerRef;
  onMount(() => {
    if (typeof IntersectionObserver === "undefined") return;

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting && !hasAnimated()) {
            setHasAnimated(true);
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.2 }
    );

    if (containerRef) {
      observer.observe(containerRef);
    }

    return () => {
      if (containerRef) observer.unobserve(containerRef);
    };
  });

  return (
    <div
      class={`wptbt-faq-wrapper w-full ${
        layout === "full" ? "full-width" : ""
      }`}
      style={{
        margin: layout === "full" ? "0 calc(50% - 50vw)" : "0",
        width: layout === "full" ? "100vw" : "100%",
        "max-width": layout === "full" ? "100vw" : "100%",
      }}
    >
      <div
        ref={containerRef}
        class={`wptbt-faq-container py-16 md:py-20 lg:py-24 relative ${
          hasAnimated()
            ? "opacity-100 translate-y-0"
            : "opacity-0 translate-y-8"
        }`}
        style={{
          "background-color": backgroundColor,
          color: textColor,
          transition: "opacity 0.8s ease-out, transform 0.8s ease-out",
          "background-image": `
            radial-gradient(circle at 10% 20%, rgba(${hexToRgb(
              secondaryColor
            )}, 0.05) 0%, rgba(${hexToRgb(secondaryColor)}, 0) 20%),
            radial-gradient(circle at 90% 80%, rgba(${hexToRgb(
              accentColor
            )}, 0.07) 0%, rgba(${hexToRgb(accentColor)}, 0) 20%)
          `,
          overflow: "hidden",
          position: "relative",
        }}
      >
        {/* Floating decorative elements */}
        <div
          class="absolute -left-16 top-1/4 w-64 h-64 opacity-10 pointer-events-none rounded-full"
          style={{ "background-color": secondaryColor }}
        ></div>
        <div
          class="absolute -right-16 bottom-1/4 w-48 h-48 opacity-10 pointer-events-none rounded-full"
          style={{ "background-color": accentColor }}
        ></div>

        {/* Decorative top wave (for full layout) */}
        <Show when={layout === "full" && showTopWave}>
          <div class="absolute top-0 left-0 right-0 z-10 transform rotate-180">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 1440 100"
              preserveAspectRatio="none"
              class="w-full h-16 md:h-20"
            >
              <path
                fill="white"
                d="M0,85.7L20,75.4C40,65.1,80,44.6,120,33.9C160,23.2,200,22.4,240,28.6C280,34.8,320,48.1,360,50C400,51.9,440,42.5,480,39.8C520,37.2,560,41.3,600,48.3C640,55.2,680,65,720,62.3C760,59.5,800,44.2,840,41.9C880,39.7,920,50.5,960,54.3C1000,58.2,1040,55.2,1080,58.2C1120,61.1,1160,69.9,1200,73.3C1240,76.8,1280,74.9,1320,70.9C1360,67,1400,61,1420,58L1440,55L1440,100L1420,100C1400,100,1360,100,1320,100C1280,100,1240,100,1200,100C1160,100,1120,100,1080,100C1040,100,1000,100,960,100C920,100,880,100,840,100C800,100,760,100,720,100C680,100,640,100,600,100C560,100,520,100,480,100C440,100,400,100,360,100C320,100,280,100,240,100C200,100,160,100,120,100C80,100,40,100,20,100L0,100Z"
              ></path>
            </svg>
          </div>
        </Show>

        <div class="container mx-auto px-4 relative">
          {/* Section header with elegant decorations */}
          <div class="text-center mb-16 relative">
            {/* Decorative icon */}
            <div class="absolute left-1/2 top-0 -translate-x-1/2 -translate-y-1/2 opacity-10 pointer-events-none">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="120"
                height="120"
                viewBox="0 0 24 24"
                fill={secondaryColor}
                class="transform rotate-12"
              >
                <path d="M12 2c5.514 0 10 4.486 10 10s-4.486 10-10 10-10-4.486-10-10 4.486-10 10-10zm0-2c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm1.25 17c0 .69-.559 1.25-1.25 1.25-.689 0-1.25-.56-1.25-1.25s.561-1.25 1.25-1.25c.691 0 1.25.56 1.25 1.25zm1.393-9.998c-.608-.616-1.515-.955-2.551-.955-2.18 0-3.59 1.55-3.59 3.95h2.011c0-1.486.829-2.013 1.538-2.013.634 0 1.307.421 1.364 1.226.062.847-.39 1.277-.962 1.821-1.412 1.343-1.438 1.993-1.432 3.468h2.005c-.013-.664.03-1.203.935-2.178.677-.73 1.519-1.638 1.536-3.022.011-.924-.284-1.719-.854-2.297z" />
              </svg>
            </div>

            <span
              class="block text-lg italic font-medium mb-2"
              style={{ color: accentColor }}
            >
              {subtitle}
            </span>

            <div class="relative inline-block">
              <h2 class="text-3xl md:text-4xl lg:text-5xl fancy-text font-medium mb-4">
                {title}
              </h2>
              <div
                class="absolute -bottom-2 left-1/2 w-24 h-0.5 transform -translate-x-1/2"
                style={{ "background-color": accentColor }}
              >
                <div
                  class="absolute left-1/2 top-1/2 w-2 h-2 rounded-full -translate-x-1/2 -translate-y-1/2"
                  style={{ "background-color": accentColor }}
                ></div>
              </div>
            </div>

            {/* Decorative element */}
            <div class="absolute left-1/2 bottom-0 transform -translate-x-1/2 translate-y-full">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke={accentColor}
                stroke-width="1"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path
                  d="M12 22V2M2 12h20M17 7l-5 5-5-5M17 17l-5-5-5 5"
                  class="opacity-40"
                />
              </svg>
            </div>
          </div>

          {/* FAQ accordion with improved styling */}
          <div class="max-w-3xl mx-auto relative">
            {/* Large decorative quotes */}
            <div class="absolute -left-8 top-0 opacity-10 pointer-events-none transform -translate-y-1/2">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="120"
                height="120"
                viewBox="0 0 24 24"
                fill={accentColor}
              >
                <path d="M13 14.725c0-5.141 3.892-10.519 10-11.725l.984 2.126c-2.215.835-4.163 3.742-4.38 5.746 2.491.392 4.396 2.547 4.396 5.149 0 3.182-2.584 4.979-5.199 4.979-3.015 0-5.801-2.305-5.801-6.275zm-13 0c0-5.141 3.892-10.519 10-11.725l.984 2.126c-2.215.835-4.163 3.742-4.38 5.746 2.491.392 4.396 2.547 4.396 5.149 0 3.182-2.584 4.979-5.199 4.979-3.015 0-5.801-2.305-5.801-6.275z" />
              </svg>
            </div>

            <For each={faqs}>
              {(faq, index) => (
                <div
                  class="faq-item mb-5 rounded-xl overflow-hidden shadow-sm transition-all duration-500 group"
                  style={{
                    transform: hasAnimated()
                      ? "translateY(0)"
                      : "translateY(20px)",
                    opacity: hasAnimated() ? "1" : "0",
                    transition: `transform 0.5s ease ${
                      index() * 0.1
                    }s, opacity 0.5s ease ${
                      index() * 0.1
                    }s, box-shadow 0.3s ease`,
                    "box-shadow": isQuestionOpen(index())
                      ? "0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.05)"
                      : "0 1px 3px rgba(0,0,0,0.1)",
                    background: "rgba(255,255,255,0.9)",
                    "backdrop-filter": "blur(5px)",
                    border: isQuestionOpen(index())
                      ? `1px solid ${accentColor}30`
                      : "1px solid rgba(0,0,0,0.05)",
                  }}
                  onMouseEnter={() => setActiveQuestion(index())}
                  onMouseLeave={() => setActiveQuestion(null)}
                >
                  <div
                    class="faq-question-container relative cursor-pointer"
                    onClick={() => toggleQuestion(index())}
                  >
                    {/* Decorative background */}
                    <div
                      class="absolute inset-0 opacity-5 pointer-events-none"
                      style={{
                        background: `radial-gradient(circle at top right, ${accentColor}, transparent 70%)`,
                        "z-index": "0",
                      }}
                    ></div>

                    <h3 class="relative z-10">
                      <button
                        class="w-full text-left p-5 relative font-medium text-lg flex items-center transition-all duration-300"
                        style={{
                          color: isQuestionOpen(index())
                            ? accentColor
                            : textColor,
                        }}
                        aria-expanded={
                          isQuestionOpen(index()) ? "true" : "false"
                        }
                        aria-controls={`faq-answer-${index()}`}
                      >
                        <span
                          class="flex-shrink-0 mr-4 w-8 h-8 rounded-full grid place-items-center transition-all duration-300"
                          style={{
                            "background-color": isQuestionOpen(index())
                              ? accentColor
                              : secondaryColor,
                            transform:
                              activeQuestion() === index()
                                ? "scale(1.1)"
                                : "scale(1)",
                          }}
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 text-white transition-transform duration-500"
                            style={{
                              transform: isQuestionOpen(index())
                                ? "rotate(180deg)"
                                : "rotate(0deg)",
                            }}
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                              clip-rule="evenodd"
                            />
                          </svg>
                        </span>
                        <span
                          class="flex-1 transition-all duration-300"
                          style={{
                            "font-weight": isQuestionOpen(index())
                              ? "600"
                              : "500",
                            "text-shadow": isQuestionOpen(index())
                              ? `0 0 1px ${accentColor}33`
                              : "none",
                          }}
                        >
                          {faq.question}
                        </span>
                      </button>
                    </h3>
                  </div>
                  <div
                    id={`faq-answer-${index()}`}
                    role="region"
                    class="faq-answer relative z-10 overflow-hidden transition-all duration-500"
                    style={{
                      "max-height": isQuestionOpen(index()) ? "1000px" : "0px",
                      opacity: isQuestionOpen(index()) ? "1" : "0",
                      "padding-left": "60px",
                      "padding-right": "24px",
                      "padding-bottom": isQuestionOpen(index())
                        ? "24px"
                        : "0px",
                    }}
                  >
                    {/* Elegant separator line */}
                    <div class="w-full h-px mb-4 relative overflow-hidden">
                      <div
                        class="absolute inset-0 opacity-20"
                        style={{
                          "background-color": accentColor,
                        }}
                      ></div>
                      <div
                        class="absolute left-0 top-0 h-full w-1/3 transition-all duration-500"
                        style={{
                          "background-color": accentColor,
                          transform:
                            activeQuestion() === index()
                              ? "translateX(200%)"
                              : "translateX(0)",
                        }}
                      ></div>
                    </div>

                    <div
                      class="prose max-w-none font-light"
                      innerHTML={faq.answer}
                      style={{
                        "line-height": "1.7",
                        color: isQuestionOpen(index())
                          ? textColor
                          : `${textColor}dd`,
                      }}
                    ></div>
                  </div>
                </div>
              )}
            </For>
          </div>

          {/* Optional button for more information */}
          <Show when={showContactButton}>
            <div class="text-center mt-12">
              <a
                href={contactUrl}
                class="inline-flex items-center px-8 py-3 rounded-full text-white font-medium transition-all duration-300 group"
                style={{
                  "background-color": accentColor,
                  "box-shadow": "0 4px 6px rgba(0, 0, 0, 0.1)",
                  transform: "translateY(0)",
                  ":hover": {
                    transform: "translateY(-3px)",
                    "box-shadow":
                      "0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)",
                  },
                }}
                onMouseEnter={(e) => {
                  e.currentTarget.style.transform = "translateY(-3px)";
                  e.currentTarget.style.boxShadow =
                    "0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)";
                }}
                onMouseLeave={(e) => {
                  e.currentTarget.style.transform = "translateY(0)";
                  e.currentTarget.style.boxShadow =
                    "0 4px 6px rgba(0, 0, 0, 0.1)";
                }}
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-2 transition-transform duration-300 group-hover:-rotate-12"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                    clip-rule="evenodd"
                  />
                </svg>
                {contactText}
              </a>
            </div>
          </Show>
        </div>

        {/* Decorative bottom wave (for full layout) */}
        <Show when={layout === "full" && showBottomWave}>
          <div class="absolute bottom-0 left-0 right-0 z-10">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 1440 100"
              preserveAspectRatio="none"
              class="w-full h-16 md:h-20"
            >
              <path
                fill="white"
                d="M0,85.7L20,75.4C40,65.1,80,44.6,120,33.9C160,23.2,200,22.4,240,28.6C280,34.8,320,48.1,360,50C400,51.9,440,42.5,480,39.8C520,37.2,560,41.3,600,48.3C640,55.2,680,65,720,62.3C760,59.5,800,44.2,840,41.9C880,39.7,920,50.5,960,54.3C1000,58.2,1040,55.2,1080,58.2C1120,61.1,1160,69.9,1200,73.3C1240,76.8,1280,74.9,1320,70.9C1360,67,1400,61,1420,58L1440,55L1440,100L1420,100C1400,100,1360,100,1320,100C1280,100,1240,100,1200,100C1160,100,1120,100,1080,100C1040,100,1000,100,960,100C920,100,880,100,840,100C800,100,760,100,720,100C680,100,640,100,600,100C560,100,520,100,480,100C440,100,400,100,360,100C320,100,280,100,240,100C200,100,160,100,120,100C80,100,40,100,20,100L0,100Z"
              ></path>
            </svg>
          </div>
        </Show>
      </div>
    </div>
  );
};

// Helper function to convert hex to rgb
function hexToRgb(hex) {
  // Remove # if it exists
  hex = hex.replace(/^#/, "");

  // Convert to RGB
  let bigint = parseInt(hex, 16);
  let r = (bigint >> 16) & 255;
  let g = (bigint >> 8) & 255;
  let b = bigint & 255;

  return `${r}, ${g}, ${b}`;
}

export default SolidFAQ;
