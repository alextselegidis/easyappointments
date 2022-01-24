<?php
/**
 * Local variables.
 * 
 * @var string $company_name
 */
?>

<div id="header">
    <div id="company-name">
        <?= $company_name ?>
        <div class="d-flex justify-content-between">
            <span class="display-selected-service me-2">
                &nbsp;
            </span>
            <span class="display-selected-provider">
                &nbsp;
            </span>
        </div>
    </div>

    <div id="steps">
        <div id="step-1" class="book-step active-step"
             data-tippy-content="<?= lang('service_and_provider') ?>">
            <strong>1</strong>
        </div>

        <div id="step-2" class="book-step" data-bs-toggle="tooltip"
             data-tippy-content="<?= lang('appointment_date_and_time') ?>">
            <strong>2</strong>
        </div>
        <div id="step-3" class="book-step" data-bs-toggle="tooltip"
             data-tippy-content="<?= lang('customer_information') ?>">
            <strong>3</strong>
        </div>
        <div id="step-4" class="book-step" data-bs-toggle="tooltip"
             data-tippy-content="<?= lang('appointment_confirmation') ?>">
            <strong>4</strong>
        </div>
    </div>
</div>
