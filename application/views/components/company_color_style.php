<?php
/**
 * @var string $company_color
 */
?>

<?php if (!empty($company_color) && $company_color !== DEFAULT_COMPANY_COLOR): ?>
    <style>
        /* Generic Overrides */

        a {
            color: <?= $company_color ?>;
        }

        a:hover {
            color: <?= $company_color ?>;
        }

        .btn-primary {
            background-color: <?= $company_color ?>;
            border-color: <?= $company_color ?>;
        }

        .btn-primary:hover,
        .btn-primary:active,
        .btn-primary:focus {
            background-color: <?= $company_color ?>;
            border-color: <?= $company_color ?>;
            filter: brightness(120%);
            outline: none;
            box-shadow: none;
        }

        .btn-primary:disabled, .btn-primary.disabled {
            background-color: <?= $company_color ?>;
            border-color: <?= $company_color ?>;
            filter: brightness(70%);
            opacity: .75;
        }

        .dropdown-item.active,
        .dropdown-item:active {
            background-color: <?= $company_color ?> !important;
        }

        .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
            background-color: <?= $company_color ?> !important;
        }

        .nav .nav-link:not(.active) {
            color: <?= $company_color ?> !important;
        }

        .form-control:focus {
            border-color: <?= $company_color ?> !important;
            filter: brightness(120%);
            box-shadow: none;
        }

        .form-check-input:checked {
            background-color: <?= $company_color ?> !important;
            border-color: <?= $company_color ?> !important;
        }

        body .ui-datepicker .ui-slider-handle {
            border-color: <?= $company_color ?> !important;
            background-color: <?= $company_color ?> !important;
        }

        body .modal-header {
            background: <?= $company_color ?> !important;
        }

        .fc-daygrid-event {
            color: rgb(51, 51, 51) !important;
        }

        body .ui-draggable .ui-dialog-titlebar {
            background: <?= $company_color ?> !important;
        }

        /* Booking Layout */

        #book-appointment-wizard #header {
            background: <?= $company_color ?>;
        }

        #book-appointment-wizard #company-name .display-selected-service,
        #book-appointment-wizard #company-name .display-selected-provider {
            color: <?= $company_color ?>;
            border-right-color: <?= $company_color ?> !important;
            filter: brightness(35%);
        }

        #book-appointment-wizard .book-step {
            background: <?= $company_color ?>;
            filter: brightness(75%);
        }

        #book-appointment-wizard .book-step strong {
            color: <?= $company_color ?>;
            filter: brightness(200%);
        }

        body .ui-widget.ui-widget-content {
            border-color: <?= $company_color ?>;
        }

        body .ui-datepicker .ui-widget-header {
            background-color: <?= $company_color ?>;
        }

        body .ui-datepicker th {
            background-color: <?= $company_color ?>;
        }

        body .ui-datepicker .ui-datepicker-next-hover,
        body .ui-datepicker .ui-datepicker-prev-hover {
            background: <?= $company_color ?>;
            border-color: <?= $company_color ?>;
            filter: brightness(140%);
        }

        body .ui-datepicker td a, body .ui-datepicker td span {
            color: <?= $company_color ?> !important;
        }

        html body .ui-datepicker td a.ui-state-active {
            background: <?= $company_color ?> !important;
        }

        body .ui-datepicker td a.ui-state-highlight {
            background: <?= $company_color ?> !important;
            filter: brightness(140%);
        }

        #book-appointment-wizard #available-hours .selected-hour {
            background-color: <?= $company_color ?>;
            border-color: <?= $company_color ?>;
        }

        #frame-footer .backend-link {
            background-color: <?= $company_color ?> !important;
        }

        #frame-footer .backend-link:hover {
            color: #fff;
        }

        /* Backend Layout */

        #header {
            background-color: <?= $company_color ?> !important;
        }

        #header #header-menu .nav-item:hover,
        #header #header-menu .nav-item.active {
            background: <?= $company_color ?> !important;
        }

        #header #header-logo small {
            color: <?= $company_color ?> !important;
            filter: brightness(60%);
        }

        .backend-page .filter-records .results .entry.selected {
            border-right-color: <?= $company_color ?> !important;
        }

        .flatpickr-calendar .flatpickr-months,
        .flatpickr-calendar .flatpickr-months .flatpickr-month,
        .flatpickr-calendar .flatpickr-weekdays,
        .flatpickr-calendar .flatpickr-current-month .flatpickr-monthDropdown-months,
        .flatpickr-calendar span.flatpickr-weekday {
            background: <?= $company_color ?> !important;
        }

        .flatpickr-day.endRange, .flatpickr-day.endRange.inRange, .flatpickr-day.endRange.nextMonthDay, .flatpickr-day.endRange.prevMonthDay, .flatpickr-day.endRange:focus, .flatpickr-day.endRange:hover, .flatpickr-day.selected, .flatpickr-day.selected.inRange, .flatpickr-day.selected.nextMonthDay, .flatpickr-day.selected.prevMonthDay, .flatpickr-day.selected:focus, .flatpickr-day.selected:hover, .flatpickr-day.startRange, .flatpickr-day.startRange.inRange, .flatpickr-day.startRange.nextMonthDay, .flatpickr-day.startRange.prevMonthDay, .flatpickr-day.startRange:focus, .flatpickr-day.startRange:hover {
            background: <?= $company_color ?> !important;
            border-color: <?= $company_color ?> !important;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months .flatpickr-monthDropdown-month {
            background-color: <?= $company_color ?> !important;
        }

        #existing-customers-list div:hover {
            background: <?= $company_color ?> !important;
        }
    </style>
<?php endif; ?>
