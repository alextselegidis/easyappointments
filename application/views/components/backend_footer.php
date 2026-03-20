<?php
/**
 * Local variables.
 *
 * @var string $user_display_name
 */
?>
<div id="footer" class="d-lg-flex justify-content-lg-start align-items-lg-center p-2 text-center text-lg-left mt-auto">
    <!-- Company/Brand info (customizable) -->
    <div class="mb-3 me-lg-5 mb-lg-0">
        <?php if (setting('company_name')): ?>
            <?= e(setting('company_name')) ?>
        <?php endif; ?>

        <?php if (setting('company_name') && config('version')): ?>
            <span class="text-muted">|</span>
        <?php endif; ?>

        <?php if (config('version')): ?>
            <span class="text-muted">v<?= config('version') ?></span>
        <?php endif; ?>
    </div>

    <!-- Language selector -->
    <div class="mb-3 me-lg-5 mb-lg-0">
        <span id="select-language" class="badge bg-dark">
            <i class="fas fa-language me-2"></i>
        	<?= ucfirst(config('language')) ?>
        </span>
    </div>

    <!-- Link to booking page -->
    <div class="mb-3 me-lg-5 mb-lg-0">
        <a href="<?= site_url('appointments') ?>">
            <?= lang('go_to_booking_page') ?>
        </a>
    </div>

    <!-- User greeting -->
    <div class="ms-lg-auto">
        <strong id="footer-user-display-name">
            <?= lang('hello') . ', ' . e($user_display_name) ?>!
        </strong>
    </div>
</div>


