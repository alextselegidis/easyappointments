<?php
/**
 * Company Color Style Component
 *
 * Overrides Bootstrap CSS variables with the company's custom primary color.
 * This allows the entire application to automatically adopt the custom branding.
 *
 * @var string $company_color
 */

if (!function_exists('hexToRgb')) {
    /**
     * Convert hex color to RGB components
     */
    function hexToRgb(string $hex): string
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return sprintf(
            '%d, %d, %d',
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        );
    }
}

if (!function_exists('adjustBrightness')) {
    /**
     * Adjust color brightness
     * @param string $hex Hex color code
     * @param int $percent Positive = lighter, negative = darker
     */
    function adjustBrightness(string $hex, int $percent): string
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return sprintf(
            '#%02x%02x%02x',
            max(0, min(255, $r + ($r * $percent) / 100)),
            max(0, min(255, $g + ($g * $percent) / 100)),
            max(0, min(255, $b + ($b * $percent) / 100)),
        );
    }
}

if (!empty($company_color) && $company_color !== DEFAULT_COMPANY_COLOR):
    // Generate color variations


    $rgb = hexToRgb($company_color);
    $hover = adjustBrightness($company_color, 15);
    $active = adjustBrightness($company_color, -10);
    $dark = adjustBrightness($company_color, -25);
    $darker = adjustBrightness($company_color, -40);
    $subtle = adjustBrightness($company_color, 80);
    $border_subtle = adjustBrightness($company_color, 40);
    ?>
<style>
    :root,
    [data-bs-theme="light"],
    [data-bs-theme="dark"] {
        /* Bootstrap primary color overrides */
        --bs-primary: <?= $company_color ?>;
        --bs-primary-rgb: <?= $rgb ?>;
        --bs-primary-bg-subtle: <?= $subtle ?>;
        --bs-primary-bg-subtle-rgb: <?= hexToRgb($subtle) ?>;
        --bs-primary-border-subtle: <?= $border_subtle ?>;
        --bs-primary-text-emphasis: <?= $darker ?>;
        
        /* Link colors */
        --bs-link-color: <?= $company_color ?>;
        --bs-link-color-rgb: <?= $rgb ?>;
        --bs-link-hover-color: <?= $hover ?>;
        --bs-link-hover-color-rgb: <?= hexToRgb($hover) ?>;
        
        /* Focus ring */
        --bs-focus-ring-color: rgba(<?= $rgb ?>, 0.25);
        
        /* Custom app color shades */
        --ea-primary-hover: <?= $hover ?>;
        --ea-primary-active: <?= $active ?>;
        --ea-primary-dark: <?= $dark ?>;
        --ea-primary-darker: <?= $darker ?>;
        --ea-primary-subtle: <?= $subtle ?>;
    }

    /* Button component overrides */
    .btn-primary {
        --bs-btn-bg: var(--bs-primary);
        --bs-btn-border-color: var(--bs-primary);
        --bs-btn-hover-bg: var(--ea-primary-hover);
        --bs-btn-hover-border-color: var(--ea-primary-hover);
        --bs-btn-active-bg: var(--ea-primary-active);
        --bs-btn-active-border-color: var(--ea-primary-active);
        --bs-btn-disabled-bg: var(--bs-primary);
        --bs-btn-disabled-border-color: var(--bs-primary);
        background: linear-gradient(180deg, var(--ea-primary-hover), var(--bs-primary));
        border: none;
        box-shadow: 0 1px 3px rgba(var(--bs-primary-rgb), 0.3);
    }

    .btn-primary:hover {
        background: linear-gradient(180deg, var(--bs-primary), var(--ea-primary-active));
        box-shadow: 0 4px 12px rgba(var(--bs-primary-rgb), 0.35);
    }

    .btn-primary:active,
    .btn-primary.active {
        background: var(--ea-primary-active);
    }

    .btn-outline-primary {
        --bs-btn-color: var(--bs-primary);
        --bs-btn-border-color: var(--bs-primary);
        --bs-btn-hover-color: var(--bs-white, #fff);
        --bs-btn-hover-bg: var(--bs-primary);
        --bs-btn-hover-border-color: var(--bs-primary);
        --bs-btn-active-color: var(--bs-white, #fff);
        --bs-btn-active-bg: var(--ea-primary-active);
        --bs-btn-active-border-color: var(--ea-primary-active);
        --bs-btn-disabled-color: var(--bs-primary);
        --bs-btn-disabled-border-color: var(--bs-primary);
        color: var(--bs-primary);
        border-color: var(--bs-primary);
    }

    .btn-outline-primary:hover {
        color: var(--bs-white, #fff);
        background: linear-gradient(180deg, var(--ea-primary-hover), var(--bs-primary));
        border-color: var(--bs-primary);
        box-shadow: 0 4px 12px rgba(var(--bs-primary-rgb), 0.35);
    }

    .btn-outline-primary:active,
    .btn-outline-primary.active {
        color: var(--bs-white, #fff);
        background: var(--ea-primary-active);
        border-color: var(--ea-primary-active);
    }

    .btn-secondary {
        --bs-btn-color: var(--bs-primary);
        --bs-btn-hover-color: var(--ea-primary-active);
        color: var(--bs-primary);
    }

    .btn-secondary:hover {
        color: var(--ea-primary-active);
    }

    .btn-outline-secondary {
        --bs-btn-color: var(--bs-secondary);
        --bs-btn-hover-color: var(--bs-dark);
        --bs-btn-active-color: var(--bs-dark);
    }

    .btn-outline-secondary:focus {
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-secondary-rgb), 0.2);
    }

    /* Form component overrides */
    .form-control:focus,
    .form-select:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.25);
    }

    .form-check-input:checked {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
    }

    .form-check-input:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.25);
    }

    /* Navigation component overrides */
    .nav-pills {
        --bs-nav-pills-link-active-bg: var(--bs-primary);
    }

    .nav-pills .nav-link {
        color: var(--bs-primary);
    }

    .nav-pills .nav-link:hover:not(.active) {
        background: rgba(var(--bs-primary-rgb), 0.08);
        color: var(--ea-primary-active);
    }

    .nav-pills .nav-link.active {
        background: var(--bs-primary);
        color: var(--bs-white, #fff);
    }

    .nav-pills .nav-link:focus {
        outline: none;
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.25);
    }

    .nav-tabs .nav-link:hover {
        background: rgba(var(--bs-primary-rgb), 0.05);
        border-color: var(--bs-border-color) var(--bs-border-color) transparent;
    }

    .nav-tabs .nav-link.active {
        color: var(--bs-primary);
        border-color: var(--bs-border-color) var(--bs-border-color) var(--bs-white);
    }

    .nav-tabs .nav-link:focus {
        outline: none;
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.15);
    }

    .nav-link {
        --bs-nav-link-color: var(--bs-primary);
        --bs-nav-link-hover-color: var(--ea-primary-hover);
    }

    .dropdown-menu {
        --bs-dropdown-link-active-bg: var(--bs-primary);
    }

    .dropdown-menu .dropdown-item:hover,
    .dropdown-menu .dropdown-item:focus {
        background: rgba(var(--bs-primary-rgb), 0.08);
    }

    .pagination {
        --bs-pagination-active-bg: var(--bs-primary);
        --bs-pagination-active-border-color: var(--bs-primary);
    }

    /* Alert component overrides */
    .alert-primary {
        background: rgba(var(--bs-primary-rgb), 0.1);
        color: var(--ea-primary-darker);
    }

    /* Other component overrides */
    .progress {
        --bs-progress-bar-bg: var(--bs-primary);
    }

    .list-group {
        --bs-list-group-active-bg: var(--bs-primary);
        --bs-list-group-active-border-color: var(--bs-primary);
    }

    .list-group-item:hover {
        background: rgba(var(--bs-primary-rgb), 0.03);
    }

    .accordion {
        --bs-accordion-active-color: var(--bs-primary);
        --bs-accordion-btn-focus-box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.25);
    }

    /* Selection color */
    ::selection {
        background: rgba(var(--bs-primary-rgb), 0.2);
    }

    /* Body background gradient */
    body {
        background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.03) 0%, rgba(var(--bs-primary-rgb), 0.01) 100%);
    }

    /* Application-specific overrides */
    #header,
    #book-appointment-wizard #header,
    #frame-footer .backend-link {
        background-color: var(--bs-primary) !important;
    }

    #book-appointment-wizard .book-step {
        background: var(--ea-primary-dark);
    }

    #book-appointment-wizard .book-step strong {
        color: var(--ea-primary-subtle);
    }

    #book-appointment-wizard #available-hours .selected-hour {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
    }

    #book-appointment-wizard #company-name .display-selected-service,
    #book-appointment-wizard #company-name .display-selected-provider,
    #book-appointment-wizard #company-name .display-booking-selection {
        color: var(--ea-primary-darker);
        border-right-color: var(--ea-primary-darker);
    }

    #header #header-logo small {
        color: var(--ea-primary-darker) !important;
    }

    .backend-page .filter-records .results .entry.selected {
        border-right-color: var(--bs-primary) !important;
    }

    #existing-customers-list div:hover {
        background: var(--bs-primary) !important;
    }
</style>
<?php
endif; ?>
