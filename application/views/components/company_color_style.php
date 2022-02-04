<?php
/**
 * @var string $company_color
 */
?>

<style>
    a {
        color: <?= $company_color ?>;
    }

    a:hover {
        color: <?= $company_color ?>;
        filter: brightness(120%);
    }

    .btn-primary {
        background-color: <?= $company_color ?>;
        border-color: <?= $company_color ?>;
    }

    .btn-primary:hover {
        background-color: <?= $company_color ?>;
        border-color: <?= $company_color ?>;
        filter: brightness(120%);
    }
    
    #book-appointment-wizard #header {
        background: <?= $company_color ?>;
    }

    #book-appointment-wizard #company-name .display-selected-service, 
    #book-appointment-wizard #company-name .display-selected-provider {
        color: <?= $company_color ?>;
        border-color: <?= $company_color ?> !important;
        filter: brightness(50%);
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
        background-color: <?= $company_color ?> ;
        border-color: <?= $company_color ?> ;
    }
        
    #frame-footer .backend-link {
        background-color: <?= $company_color ?> !important;
    }

    #frame-footer .backend-link:hover {
        color: #fff;
    }
</style>
