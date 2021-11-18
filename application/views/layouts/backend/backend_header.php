<?php
/**
 * @var array $privileges
 * @var string $active_menu
 */
?>

<nav id="header" class="navbar navbar-expand-md navbar-dark">
    <div id="header-logo" class="navbar-brand">
        <img src="<?= base_url('assets/img/logo.png') ?>">
        <h6>EASY!APPOINTMENTS</h6>
        <small>Open Source Appointment Scheduler</small>
    </div>

    <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#header-menu">
        <span class="sr-only">Toggle navigation</span>
        <span class="navbar-toggler-icon"></span>
    </button>

    <div id="header-menu" class="collapse navbar-collapse flex-row-reverse">
        <ul class="navbar-nav">
            <?php $hidden = ($privileges[PRIV_APPOINTMENTS]['view'] == TRUE) ? '' : 'd-none' ?>
            <?php $active = ($active_menu == PRIV_APPOINTMENTS) ? 'active' : '' ?>
            <li class="nav-item <?= $active . $hidden ?>">
                <a href="<?= site_url('backend') ?>" class="nav-link"
                   data-tippy-content="<?= lang('manage_appointment_record_hint') ?>">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    <?= lang('calendar') ?>
                </a>
            </li>

            <?php $hidden = ($privileges[PRIV_CUSTOMERS]['view'] == TRUE) ? '' : 'd-none' ?>
            <?php $active = ($active_menu == PRIV_CUSTOMERS) ? 'active' : '' ?>
            <li class="nav-item <?= $active . $hidden ?>">
                <a href="<?= site_url('backend/customers') ?>" class="nav-link"
                   data-tippy-content="<?= lang('manage_customers_hint') ?>">
                    <i class="fas fa-user-friends mr-2"></i>
                    <?= lang('customers') ?>
                </a>
            </li>

            <?php $hidden = ($privileges[PRIV_SERVICES]['view'] == TRUE) ? '' : 'd-none' ?>
            <?php $active = ($active_menu == PRIV_SERVICES) ? 'active' : '' ?>
            <li class="nav-item <?= $active . $hidden ?>">
                <a href="<?= site_url('services') ?>" class="nav-link"
                   data-tippy-content="<?= lang('manage_services_hint') ?>">
                    <i class="fas fa-business-time mr-2"></i>
                    <?= lang('services') ?>
                </a>
            </li>

            <?php $hidden = ($privileges[PRIV_USERS]['view'] == TRUE) ? '' : 'd-none' ?>
            <?php $active = ($active_menu == PRIV_USERS) ? 'active' : '' ?>
            <li class="nav-item <?= $active . $hidden ?>">
                <a href="<?= site_url('backend/users') ?>" class="nav-link"
                   data-tippy-content="<?= lang('manage_users_hint') ?>">
                    <i class="fas fa-users-cog mr-2"></i>
                    <?= lang('users') ?>
                </a>
            </li>

            <?php $hidden = ($privileges[PRIV_SYSTEM_SETTINGS]['view'] == TRUE
                || $privileges[PRIV_USER_SETTINGS]['view'] == TRUE) ? '' : 'd-none' ?>
            <?php $active = ($active_menu == PRIV_SYSTEM_SETTINGS) ? 'active' : '' ?>
            <li class="nav-item <?= $active . $hidden ?>">
                <a href="<?= site_url('backend/settings') ?>" class="nav-link"
                   data-tippy-content="<?= lang('settings_hint') ?>">
                    <i class="fas fa-cogs mr-2"></i>
                    <?= lang('settings') ?>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= site_url('user/logout') ?>" class="nav-link"
                   data-tippy-content="<?= lang('log_out_hint') ?>">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    <?= lang('log_out') ?>
                </a>
            </li>
        </ul>
    </div>
</nav>

<div id="notification" style="display: none;"></div>

<div id="loading" style="display: none;">
    <div class="any-element animation is-loading">
        &nbsp;
    </div>
</div>
