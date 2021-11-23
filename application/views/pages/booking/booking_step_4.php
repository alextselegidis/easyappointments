<div id="wizard-frame-4" class="wizard-frame" style="display:none;">
    <div class="frame-container">
        <h2 class="frame-title"><?= lang('appointment_confirmation') ?></h2>
        <div class="row frame-content">
            <div id="appointment-details" class="col-12 col-md-6"></div>
            <div id="customer-details" class="col-12 col-md-6"></div>
        </div>
        <?php if (setting('require_captcha')): ?>
            <div class="row frame-content">
                <div class="col-12 col-md-6">
                    <label class="captcha-title" for="captcha-text">
                        CAPTCHA
                        <button class="btn btn-link text-dark text-decoration-none py-0">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </label>
                    <img class="captcha-image" src="<?= site_url('captcha') ?>" alt="CAPTCHA">
                    <input id="captcha-text" class="captcha-text form-control" type="text" value=""/>
                    <span id="captcha-hint" class="help-block" style="opacity:0">&nbsp;</span>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="command-buttons">
        <button type="button" id="button-back-4" class="btn button-back btn-outline-secondary"
                data-step_index="4">
            <i class="fas fa-chevron-left me-2"></i>
            <?= lang('back') ?>
        </button>
        <form id="book-appointment-form" style="display:inline-block" method="post">
            <button id="book-appointment-submit" type="button" class="btn btn-success">
                <i class="fas fa-check-square me-2"></i>
                <?= ! $manage_mode ? lang('confirm') : lang('update') ?>
            </button>
            <input type="hidden" name="csrfToken"/>
            <input type="hidden" name="post_data"/>
        </form>
    </div>
</div>
