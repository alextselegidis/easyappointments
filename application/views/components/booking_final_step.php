<?php
/**
 * Local variables.
 *
 * @var bool $manage_mode
 * @var string $display_terms_and_conditions
 * @var string $display_privacy_policy
 */
?>

<div id="wizard-frame-4" class="wizard-frame p-3 p-md-4" style="display:none;">
    <div class="frame-container py-3" style="min-height: 500px;">
        <h2 class="frame-title fw-light text-center mb-4 text-muted"><?= lang('appointment_confirmation') ?></h2>

        <div class="row frame-content m-auto pt-md-4 mb-4" style="max-width: 630px;">
            <div id="appointment-details" class="col-12 col-lg-6 text-center text-md-start mb-2 mb-md-0 fs-5" style="line-height: 28px;">
                <!-- JS -->
            </div>

            <div id="customer-details" class="col-12 col-lg-6 text-center text-md-end fs-5" style="line-height: 28px;">
                <!-- JS -->
            </div>

        </div>

        <?php if (setting('require_captcha')): ?>
            <div class="row frame-content m-auto">
                <div class="col">
                    <label class="captcha-title float-start my-2 mb-2 me-md-4" for="captcha-text">
                        CAPTCHA
                        <button class="btn btn-link text-dark text-decoration-none py-0">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </label>
                    <img class="captcha-image float-start float-md-end mb-4 rounded" src="<?= site_url(
                        'captcha',
                    ) ?>" alt="CAPTCHA">
                    <input id="captcha-text" class="captcha-text form-control w-100 mb-4" type="text" value=""/>
                    <span id="captcha-hint" class="help-block" style="opacity:0">&nbsp;</span>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <div class="d-flex fs-6 justify-content-around">
        <?php if ($display_terms_and_conditions): ?>
            <div class="form-check mb-3">
                <input type="checkbox" class="required form-check-input" id="accept-to-terms-and-conditions">
                <label class="form-check-label" for="accept-to-terms-and-conditions">
                    <?= strtr(lang('read_and_agree_to_terms_and_conditions'), [
                        '{$link}' => '<a href="#" data-bs-toggle="modal" data-bs-target="#terms-and-conditions-modal">',
                        '{/$link}' => '</a>',
                    ]) ?>
                </label>
            </div>
        <?php endif; ?>

        <?php if ($display_privacy_policy): ?>
            <div class="form-check mb-3">
                <input type="checkbox" class="required form-check-input" id="accept-to-privacy-policy">
                <label class="form-check-label" for="accept-to-privacy-policy">
                    <?= strtr(lang('read_and_agree_to_privacy_policy'), [
                        '{$link}' => '<a href="#" data-bs-toggle="modal" data-bs-target="#privacy-policy-modal">',
                        '{/$link}' => '</a>',
                    ]) ?>
                </label>
            </div>
        <?php endif; ?>

    </div>

    <div class="command-buttons text-center my-3 mx-auto d-md-flex justify-content-md-between">
        <button type="button" id="button-back-4" class="btn button-back btn-outline-secondary" style="min-width: 120px; margin-right: 10px;"
                data-step_index="4">
            <i class="fas fa-chevron-left me-2"></i>
            <?= lang('back') ?>
        </button>
        <form id="book-appointment-form" class="d-inline-block" method="post">
            <button id="book-appointment-submit" type="button" class="btn btn-primary w-100">
                <i class="fas fa-check-square me-2"></i>
                <?= $manage_mode ? lang('update') : lang('confirm') ?>
            </button>
            <input type="hidden" name="csrfToken"/>
            <input type="hidden" name="post_data"/>
        </form>
    </div>
</div>
