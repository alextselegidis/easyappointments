<?php
/**
 * @var array $system_settings
 * @var array $privileges
 */
?>

<?php extend('layouts/backend_layout') ?>

<?php section('content') ?>

<div id="legal-settings-page" class="container backend-page">
    <div id="legal-contents">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <form>
                    <fieldset>
                        <legend class="d-flex justify-content-between align-items-center border-bottom mb-4 py-2">
                            <?= lang('legal_contents') ?>

                            <?php if (can('edit', PRIV_SYSTEM_SETTINGS)): ?>
                                <button type="button" id="save-settings" class="btn btn-primary btn-sm">
                                    <i class="fas fa-check-square me-2"></i>
                                    <?= lang('save') ?>
                                </button>
                            <?php endif ?>
                        </legend>

                        <div class="row">
                            <div class="col-12">
                                <h4><?= lang('cookie_notice') ?></h4>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input display-switch" type="checkbox"
                                           id="display-cookie-notice">
                                    <label class="form-check-label" for="display-cookie-notice">
                                        <?= lang('display_cookie_notice') ?>
                                    </label>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><?= lang('cookie_notice_content') ?></label>
                                    <textarea id="cookie-notice-content" cols="30" rows="10" class="mb-3"></textarea>
                                </div>

                                <br>

                                <h4><?= lang('terms_and_conditions') ?></h4>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input display-switch" type="checkbox"
                                           id="display-terms-and-conditions">
                                    <label class="form-check-label" for="display-terms-and-conditions">
                                        <?= lang('display_terms_and_conditions') ?>
                                    </label>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label"><?= lang('terms_and_conditions_content') ?></label>
                                    <textarea id="terms-and-conditions-content" cols="30" rows="10"
                                              class="mb-3"></textarea>
                                </div>

                                <h4><?= lang('privacy_policy') ?></h4>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input display-switch" type="checkbox"
                                           id="display-privacy-policy">
                                    <label class="form-check-label" for="display-privacy-policy">
                                        <?= lang('display_privacy_policy') ?>
                                    </label>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><?= lang('privacy_policy_content') ?></label>
                                    <textarea id="privacy-policy-content" cols="30" rows="10" class="mb-3"></textarea>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>

    </div>
</div>

<?php section('content') ?>

<?php section('scripts') ?>

<script src="<?= asset_url('assets/js/utils/url.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/legal_settings_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/legal_settings.js') ?>"></script>


<?php section('scripts') ?>
