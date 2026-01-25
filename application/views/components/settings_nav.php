<h4 class=" text-black-50 py-3 mb-3 fw-light">
    <?= lang('settings') ?>
</h4>

<ul id="settings-nav" class="nav flex-column">
    <li class="nav-item mb-3">
        <a class="nav-link px-0 py-2" href="<?= site_url('general_settings') ?>">
            <?= lang('general_settings') ?>
        </a>
    </li>

    <li class="nav-item mb-3">
        <a class="nav-link px-0 py-2" href="<?= site_url('booking_settings') ?>">
            <?= lang('booking_settings') ?>
        </a>
    </li>

    <li class="nav-item mb-3">
        <a class="nav-link px-0 py-2" href="<?= site_url('business_settings') ?>">
            <?= lang('business_logic') ?>
        </a>
    </li>

    <li class="nav-item mb-3">
        <a class="nav-link px-0 py-2" href="<?= site_url('legal_settings') ?>">
            <?= lang('legal_contents') ?>
        </a>
    </li>

    <li class="nav-item mb-3">
        <a class="nav-link px-0 py-2" href="<?= site_url('configuration_settings') ?>">
            <?= lang('configuration') ?>
        </a>
    </li>

    <li class="nav-item mb-3">
        <a class="nav-link px-0 py-2" href="<?= site_url('integrations') ?>">
            <?= lang('integrations') ?>
        </a>
    </li>
</ul>
