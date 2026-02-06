<?php
/**
 * Local variables.
 *
 * @var string $active_menu
 * @var string $company_logo
 */
?>

<nav id="header" class="navbar navbar-expand-md navbar-dark bg-primary p-0">
    <div id="header-logo" class="navbar-brand p-1 lh-1">
        <img src="<?= base_url(
            'assets/img/logo.png',
        ) ?>" alt="logo" class="float-start me-2" style="width: 45px; height: 45px;">
        <h6 class="mb-1 mt-1 fw-bold text-white" style="font-size: 15px;">EASY!APPOINTMENTS</h6>
        <small class="d-block text-white-50" style="font-size: 12px;">Online Appointment Scheduler</small>
    </div>

    <button type="button" class="navbar-toggler me-1" data-bs-toggle="collapse" data-bs-target="#header-menu">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div id="header-menu" class="collapse navbar-collapse flex-row-reverse px-2">
        <ul class="navbar-nav">
            <?php $hidden = can('view', PRIV_APPOINTMENTS) ? '' : 'd-none'; ?>
            <?php $active = $active_menu == PRIV_APPOINTMENTS ? 'active' : ''; ?>
            <li class="nav-item text-center <?= $active . $hidden ?>" style="min-width: 100px;">
                <a href="<?= site_url(
                    'calendar' . (vars('calendar_view') === CALENDAR_VIEW_TABLE ? '?view=table' : ''),
                ) ?>"
                   class="nav-link text-white fw-light py-3 px-3"
                   data-tippy-content="<?= lang('manage_appointment_record_hint') ?>">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <?= lang('calendar') ?>
                </a>
            </li>

            <?php $hidden = can('view', PRIV_CUSTOMERS) ? '' : 'd-none'; ?>
            <?php $active = $active_menu == PRIV_CUSTOMERS ? 'active' : ''; ?>
            <li class="nav-item text-center <?= $active . $hidden ?>" style="min-width: 100px;">
                <a href="<?= site_url('customers') ?>" class="nav-link text-white fw-light py-3 px-3"
                   data-tippy-content="<?= lang('manage_customers_hint') ?>">
                    <i class="fas fa-user-friends me-2"></i>
                    <?= lang('customers') ?>
                </a>
            </li>

            <?php $hidden = can('view', PRIV_SERVICES) ? '' : 'd-none'; ?>
            <?php $active = $active_menu == PRIV_SERVICES ? 'active' : ''; ?>
            <li class="nav-item dropdown text-center <?= $active . $hidden ?>" style="min-width: 100px;">
                <a class="nav-link dropdown-toggle text-white fw-light py-3 px-3" href="#" data-bs-toggle="dropdown"
                   data-tippy-content="<?= lang('manage_services_hint') ?>">
                    <i class="fas fa-business-time me-2"></i>
                    <?= lang('services') ?>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="<?= site_url('services') ?>">
                        <?= lang('services') ?>
                    </a>
                    <a class="dropdown-item" href="<?= site_url('service_categories') ?>">
                        <?= lang('categories') ?>
                    </a>
                </div>
            </li>

            <?php $hidden = can('view', PRIV_USERS) ? '' : 'd-none'; ?>
            <?php $active = $active_menu == PRIV_USERS ? 'active' : ''; ?>
            <li class="nav-item dropdown text-center <?= $active . $hidden ?>" style="min-width: 100px;">
                <a class="nav-link dropdown-toggle text-white fw-light py-3 px-3" href="#" data-bs-toggle="dropdown"
                   data-tippy-content="<?= lang('manage_users_hint') ?>">
                    <i class="fas fa-users me-2"></i>
                    <?= lang('users') ?>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="<?= site_url('providers') ?>">
                        <?= lang('providers') ?>
                    </a>
                    <a class="dropdown-item" href="<?= site_url('secretaries') ?>">
                        <?= lang('secretaries') ?>
                    </a>
                    <a class="dropdown-item" href="<?= site_url('admins') ?>">
                        <?= lang('admins') ?>
                    </a>
                </div>
            </li>

            <?php $hidden = can('view', PRIV_SYSTEM_SETTINGS) || can('view', PRIV_USER_SETTINGS) ? '' : 'd-none'; ?>
            <?php $active = $active_menu == PRIV_SYSTEM_SETTINGS ? 'active' : ''; ?>
            <li class="nav-item dropdown text-center <?= $active . $hidden ?>" style="min-width: 100px;">
                <a class="nav-link dropdown-toggle text-white fw-light py-3 px-3" href="#" data-bs-toggle="dropdown"
                   data-tippy-content="<?= lang('settings_hint') ?>">
                    <i class="fas fa-user me-2"></i>
                    <?= e(vars('user_display_name')) ?>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <?php if (can('view', PRIV_SYSTEM_SETTINGS)): ?>
                        <a class="dropdown-item" href="<?= site_url('general_settings') ?>">
                            <?= lang('settings') ?>
                        </a>
                    <?php endif; ?>

                    <a class="dropdown-item" href="<?= site_url('account') ?>">
                        <?= lang('account') ?>
                    </a>
                    <a class="dropdown-item" href="<?= site_url('about') ?>">
                        <?= lang('about') ?>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?= site_url('logout') ?>">
                        <?= lang('log_out') ?>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>

<div id="notification" style="display: none;"></div>

<div id="loading" class="position-fixed top-0 start-0 w-100 h-100" style="display: none; z-index: 999999; background: rgba(255, 255, 255, 0.75);">
    <div class="any-element animation is-loading d-block mx-auto">
        &nbsp;
    </div>
</div>
