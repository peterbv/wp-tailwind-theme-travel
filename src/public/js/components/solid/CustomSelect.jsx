// src/public/js/components/solid/CustomSelect.jsx
import { createSignal, createEffect, Show, For, onMount, onCleanup } from "solid-js";

/**
 * Componente Select personalizado y bonito para Solid.js
 * @param {Object} props - Propiedades del componente
 * @param {string} props.id - ID del select
 * @param {string} props.name - Nombre del input
 * @param {Array} props.options - Array de opciones [{value, label, subtitle?, disabled?}]
 * @param {string} props.value - Valor seleccionado
 * @param {Function} props.onChange - Función callback cuando cambia el valor
 * @param {string} props.placeholder - Texto placeholder
 * @param {string} props.labelText - Texto de la etiqueta
 * @param {boolean} props.required - Si es requerido
 * @param {boolean} props.disabled - Si está deshabilitado
 * @param {boolean} props.darkMode - Modo oscuro
 * @param {Object} props.colors - Colores del tema
 * @param {string} props.icon - Tipo de icono a mostrar
 * @param {boolean} props.searchable - Si permite búsqueda
 */
const CustomSelect = (props) => {
    const {
        id = 'custom-select',
        name = 'select',
        options = () => [],
        value = '',
        onChange = () => { },
        placeholder = 'Select an option',
        labelText = '',
        required = false,
        disabled = false,
        darkMode = false,
        colors = { accent: '#D4B254' },
        icon = 'chevron',
        searchable = false
    } = props;
    console.log("CustomSelect props:", options());
    // Estados
    const [getIsOpen, setIsOpen] = createSignal(false);
    const [getSearchTerm, setSearchTerm] = createSignal('');
    const [getFocusedIndex, setFocusedIndex] = createSignal(-1);

    let selectRef;
    let dropdownRef;
    let searchInputRef;

    // Obtener la opción seleccionada
    const getSelectedOption = () => {
        return options().find(option => option.value === value()) || null;
    };

    // Filtrar opciones basado en búsqueda
    const getFilteredOptions = () => {
        if (!searchable || !getSearchTerm()) {
            return options();
        }

        return options().filter(option =>
            option.label.toLowerCase().includes(getSearchTerm().toLowerCase()) ||
            (option.subtitle && option.subtitle.toLowerCase().includes(getSearchTerm().toLowerCase()))
        );
    };

    // Abrir/cerrar dropdown
    const toggleDropdown = () => {
        if (disabled) return;
        setIsOpen(!getIsOpen());
        if (!getIsOpen()) {
            setSearchTerm('');
            setFocusedIndex(-1);
        }
    };

    // Seleccionar opción
    const selectOption = (option) => {
        if (option.disabled) return;
        onChange(option.value);
        setIsOpen(false);
        setSearchTerm('');
        setFocusedIndex(-1);
    };

    // Manejar clicks fuera del componente
    const handleClickOutside = (event) => {
        if (selectRef && !selectRef.contains(event.target)) {
            setIsOpen(false);
            setSearchTerm('');
            setFocusedIndex(-1);
        }
    };

    // Manejar navegación con teclado
    const handleKeyDown = (event) => {
        const filteredOptions = getFilteredOptions();

        switch (event.key) {
            case 'Enter':
                event.preventDefault();
                if (getIsOpen() && getFocusedIndex() >= 0) {
                    selectOption(filteredOptions[getFocusedIndex()]);
                } else {
                    toggleDropdown();
                }
                break;

            case 'Escape':
                setIsOpen(false);
                setSearchTerm('');
                setFocusedIndex(-1);
                break;

            case 'ArrowDown':
                event.preventDefault();
                if (!getIsOpen()) {
                    setIsOpen(true);
                } else {
                    setFocusedIndex(prev =>
                        prev < filteredoptions().length - 1 ? prev + 1 : 0
                    );
                }
                break;

            case 'ArrowUp':
                event.preventDefault();
                if (getIsOpen()) {
                    setFocusedIndex(prev =>
                        prev > 0 ? prev - 1 : filteredoptions().length - 1
                    );
                }
                break;

            case 'Tab':
                setIsOpen(false);
                setSearchTerm('');
                setFocusedIndex(-1);
                break;
        }
    };

    // Efectos
    onMount(() => {
        document.addEventListener('click', handleClickOutside);
        document.addEventListener('keydown', handleKeyDown);
    });

    onCleanup(() => {
        document.removeEventListener('click', handleClickOutside);
        document.removeEventListener('keydown', handleKeyDown);
    });

    // Focus en búsqueda cuando se abre
    createEffect(() => {
        if (getIsOpen() && searchable && searchInputRef) {
            setTimeout(() => searchInputRef.focus(), 50);
        }
    });

    // Iconos
    const renderIcon = (iconType, className = "w-5 h-5") => {
        switch (iconType) {
            case 'chevron':
                return (
                    <svg class={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d={getIsOpen() ? "M5 15l7-7 7 7" : "M19 9l-7 7-7-7"} />
                    </svg>
                );
            case 'check':
                return (
                    <svg class={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 13l4 4L19 7" />
                    </svg>
                );
            case 'search':
                return (
                    <svg class={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                );
            default:
                return null;
        }
    };

    return (
        <div class="relative" ref={selectRef}>
            {/* Label */}
            <Show when={labelText}>
                <label
                    for={id}
                    class={`block text-sm font-medium mb-2 ${darkMode ? 'text-white' : 'text-gray-700'
                        }`}
                >
                    {labelText}
                    <Show when={required}>
                        <span class="text-red-500 ml-1">*</span>
                    </Show>
                </label>
            </Show>

            {/* Select button */}
            <button
                type="button"
                id={id}
                name={name}
                onClick={toggleDropdown}
                disabled={disabled}
                class={`
          relative w-full max-w-80 sm:max-w-full px-4 py-3 text-left border rounded-lg 
          focus:ring-2 focus:outline-none transition-all duration-200
          ${disabled
                        ? 'opacity-50 cursor-not-allowed'
                        : 'cursor-pointer hover:shadow-md'
                    }
          ${darkMode
                        ? 'bg-gray-800/80 border-gray-700 text-white'
                        : 'bg-gray-50 border-gray-300 text-gray-900'
                    }
          ${getIsOpen() ? 'ring-2 shadow-lg' : ''}
        `}
                style={{
                    "border-color": getIsOpen() ? colors.accent : undefined,
                    "ring-color": colors.accent
                }}
                aria-haspopup="listbox"
                aria-expanded={getIsOpen()}
                aria-required={required}
            >
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <Show
                            when={getSelectedOption()}
                            fallback={
                                <span class={`block truncate ${darkMode ? 'text-gray-400' : 'text-gray-500'
                                    }`}>
                                    {placeholder}
                                </span>
                            }
                        >
                            <span class="block truncate font-medium">
                                {getSelectedOption().label}
                            </span>
                            <Show when={getSelectedOption().subtitle}>
                                <span class={`block text-sm truncate ${darkMode ? 'text-gray-400' : 'text-gray-500'
                                    }`}>
                                    {getSelectedOption().subtitle}
                                </span>
                            </Show>
                        </Show>
                    </div>

                    <div class={`ml-3 transition-transform duration-200 ${getIsOpen() ? 'transform rotate-180' : ''
                        } ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                        {renderIcon(icon)}
                    </div>
                </div>
            </button>

            {/* Dropdown */}
            <Show when={getIsOpen()}>
                <div
                    ref={dropdownRef}
                    class={`
            absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-lg shadow-xl
            ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'}
          `}
                    style={{ "max-height": "300px" }}
                >
                    {/* Búsqueda */}
                    <Show when={searchable}>
                        <div class="p-3 border-b border-gray-200">
                            <div class="relative">
                                <div class={`absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none ${darkMode ? 'text-gray-400' : 'text-gray-500'
                                    }`}>
                                    {renderIcon('search', 'w-4 h-4')}
                                </div>
                                <input
                                    ref={searchInputRef}
                                    type="text"
                                    value={getSearchTerm()}
                                    onInput={(e) => setSearchTerm(e.target.value)}
                                    placeholder="Search..."
                                    class={`
                    w-full pl-10 pr-3 py-2 text-sm border rounded-md focus:ring-2 focus:outline-none
                    ${darkMode
                                            ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400'
                                            : 'bg-gray-50 border-gray-300 text-gray-900 placeholder-gray-500'
                                        }
                  `}
                                    style={{ "ring-color": colors.accent }}
                                />
                            </div>
                        </div>
                    </Show>

                    {/* Options list */}
                    <div class={`max-h-60 overflow-y-auto ${darkMode ? 'bg-gray-800' : 'bg-white'
                        }`}>
                        <Show
                            when={getFilteredOptions().length > 0}
                            fallback={
                                <div class={`p-4 text-center text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'
                                    }`}>
                                    No options found
                                </div>
                            }
                        >
                            <For each={getFilteredOptions()}>
                                {(option, index) => (
                                    <button
                                        type="button"
                                        onClick={() => selectOption(option)}
                                        disabled={option.disabled}
                                        class={`
                      w-full px-4 py-3 text-left transition-colors duration-150
                      ${option.disabled
                                                ? 'opacity-50 cursor-not-allowed'
                                                : 'cursor-pointer'
                                            }
                      ${value() === option.value
                                                ? darkMode
                                                    ? 'bg-gray-700 text-white'
                                                    : 'bg-gray-100 text-gray-900'
                                                : darkMode
                                                    ? 'text-gray-200 hover:bg-gray-700'
                                                    : 'text-gray-900 hover:bg-gray-50'
                                            }
                      ${getFocusedIndex() === index()
                                                ? darkMode
                                                    ? 'bg-gray-600'
                                                    : 'bg-gray-100'
                                                : ''
                                            }
                    `}
                                    >
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center">
                                                    <span class="block truncate font-medium">
                                                        {option.label}
                                                    </span>
                                                    <Show when={value() === option.value}>
                                                        <div class="ml-2 text-current">
                                                            {renderIcon('check', 'w-4 h-4')}
                                                        </div>
                                                    </Show>
                                                </div>
                                                <Show when={option.subtitle}>
                                                    <span class={`block text-sm truncate ${darkMode ? 'text-gray-400' : 'text-gray-500'
                                                        }`}>
                                                        {option.subtitle}
                                                    </span>
                                                </Show>
                                            </div>
                                        </div>
                                    </button>
                                )}
                            </For>
                        </Show>
                    </div>
                </div>
            </Show>

            {/* Hidden input for form submission */}
            <input
                type="hidden"
                name={name}
                value={value()}
            />
        </div>
    );
};

export default CustomSelect;