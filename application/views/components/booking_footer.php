<?php
/**
 * Local variables.
 *
 * @var bool $display_login_button
 */
?>

<div id="frame-footer" class="p-3 text-center border-top">
    <small class="d-block d-md-flex">
        <span class="footer-powered-by small d-block w-100 w-md-50 text-center text-md-start p-1 pe-md-0">
            Powered By
            <a href="https://easyappointments.org" target="_blank">Easy!Appointments</a>
        </span>

        <span class="footer-options d-block w-100 w-md-50 text-center text-md-end">
            <span id="select-language" class="badge bg-secondary d-inline-block my-1 my-md-0 p-1" style="min-width: 100px;">
                <i class="fas fa-language me-2"></i>
                <?= ucfirst(config('language')) ?>
            </span>
    
            <?php if ($display_login_button): ?>
                <a class="backend-link badge bg-primary text-decoration-none px-2 d-inline-block my-1 my-md-0 p-1"
                   href="<?= session('user_id') ? site_url('calendar') : site_url('login') ?>"
                   style="min-width: 120px;">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    <?= session('user_id') ? lang('backend_section') : lang('login') ?>
                </a>
            <?php endif; ?>
        </span>
    </small>
</div>
