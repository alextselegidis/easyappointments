<h4 class="mt-4 text-black-50 pb-2 border-bottom mb-2">
    <?= lang('settings') ?>
</h4>

<ul id="settings-nav" class="nav flex-column">
    <li class="nav-item mb-2">
        <a class="nav-link" href="<?= site_url('general_settings') ?>">
            <?= lang('general_settings') ?>
        </a>
    </li>
    
    <li class="nav-item mb-2">
        <a class="nav-link" href="<?= site_url('booking_settings') ?>">
            <?= lang('booking_settings') ?>
        </a>
    </li>
    
    <li class="nav-item mb-2">
        <a class="nav-link" href="<?= site_url('business_settings') ?>">
            <?= lang('business_logic') ?>
        </a>
    </li>
    
    <li class="nav-item mb-2">
        <a class="nav-link" href="<?= site_url('legal_settings') ?>">
            <?= lang('legal_contents') ?>
        </a>
    </li>
    
    <li class="nav-item mb-2">
        <a class="nav-link" href="<?= site_url('integrations') ?>">
            <?= lang('integrations') ?>
        </a>
    </li>
</ul>
