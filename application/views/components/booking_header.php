<?php
/**
 * Local variables.
 *
 * @var string $company_name
 */
?>

<div id="header" class="overflow-hidden p-3 p-md-4 d-flex flex-column flex-lg-row align-items-center bg-primary">
    <div id="company-name" class="d-block d-md-inline-block float-md-start text-center text-md-start text-white fs-4 fw-light my-3 my-md-0 mw-100 flex-grow-1" style="min-width: 0; line-height: 1.4;">
        <img src="<?= vars('company_logo') ?: base_url('assets/img/logo.png') ?>" alt="logo" id="company-logo" 
             class="d-block d-md-inline-block mx-auto mx-md-0 float-md-start me-md-3 mb-3 mb-md-0" style="max-height: 56px;">

        <span>
            <?= e($company_name) ?>
        </span>

        <div class="d-flex justify-content-center justify-content-md-start">
            <span class="display-booking-selection small fw-normal text-white-50">
                <?= lang('service') ?> │ <?= lang('provider') ?>
            </span>
        </div>
    </div>

    <div id="steps" class="d-block d-md-inline-block float-md-end overflow-hidden mx-auto my-3 my-md-1" style="width: 190px;">
        <div id="step-1" class="book-step active-step d-inline-block float-start rounded text-center bg-white"
             data-tippy-content="<?= lang('service_and_provider') ?>"
             style="height: 45px; width: 45px; padding: 7px; margin-right: 13px; transition: all 0.3s linear;">
            <strong class="d-block text-primary" style="font-size: 21px; cursor: default;">1</strong>
        </div>

        <div id="step-2" class="book-step d-inline-block float-start rounded" data-bs-toggle="tooltip"
             data-tippy-content="<?= lang('appointment_date_and_time') ?>"
             style="height: 35px; width: 35px; background: rgba(0,0,0,0.2); padding: 8px; margin-right: 12px; margin-top: 6px; transition: all 0.3s linear;">
            <strong class="d-block text-center text-white-50" style="font-size: 12px; cursor: default;">2</strong>
        </div>
        <div id="step-3" class="book-step d-inline-block float-start rounded" data-bs-toggle="tooltip"
             data-tippy-content="<?= lang('customer_information') ?>"
             style="height: 35px; width: 35px; background: rgba(0,0,0,0.2); padding: 8px; margin-right: 12px; margin-top: 6px; transition: all 0.3s linear;">
            <strong class="d-block text-center text-white-50" style="font-size: 12px; cursor: default;">3</strong>
        </div>
        <div id="step-4" class="book-step d-inline-block float-start rounded" data-bs-toggle="tooltip"
             data-tippy-content="<?= lang('appointment_confirmation') ?>"
             style="height: 35px; width: 35px; background: rgba(0,0,0,0.2); padding: 8px; margin-right: 0; margin-top: 6px; transition: all 0.3s linear;">
            <strong class="d-block text-center text-white-50" style="font-size: 12px; cursor: default;">4</strong>
        </div>
    </div>
</div>
