<?php
/**
 * Local variables.
 *
 * @var string $company_name
 * @var array $loggedin_user
 */
?>

<div id="header">
    <div id="company-name">
        <img src="<?= vars('company_logo') ?: base_url('assets/img/logo.png') ?>" alt="logo" id="company-logo">

        <span>
            <?= e($company_name) ?>
        </span>

        <div class="d-flex justify-content-center justify-content-md-start">
            <span class="display-booking-selection">
                <?= lang('service') ?> │ <?= lang('provider') ?>
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

<div class="welcome-header">
    <div class="logged-in <?= $loggedin_user?'':'display-none' ?>">
        <h2 class="frame-title welkom mt-3"><?= lang('welcome_user') ?>  <b><?= $loggedin_user['first_name'] ?></b></h2>
        <h6 class="frame-title welkom subtitle"><a href="#" id="click-if-not-user"><?= lang('click_if_not'); ?> <?= $loggedin_user['first_name'] ?></a></h6>
    </div>
    <div class="not-logged-in <?= $loggedin_user?'display-none':'' ?>">
        <h2 class="frame-title welkom mt-3 mb-4"><?= lang('welcome_new') ?> <?= e($company_name) ?></h2>
    </div>
</div>

