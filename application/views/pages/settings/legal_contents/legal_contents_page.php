<?php
/**
 * @var array $system_settings
 * @var array $privileges
 */
?>

<?php extend('layouts/backend/backend_layout') ?>

<?php section('content') ?>

<script src="<?= asset_url('assets/js/backend_settings_legal_contents_helper.js') ?>"></script>
<script src="<?= asset_url('assets/js/backend_settings_legal_contents.js') ?>"></script>
<script>
    var GlobalVariables = {
        csrfToken: <?= json_encode($this->security->get_csrf_hash()) ?>,
        baseUrl: <?= json_encode(config('base_url')) ?>,
        dateFormat: <?= json_encode(setting('date_format')) ?>,
        timeFormat: <?= json_encode(setting('time_format')) ?>,
        firstWeekday: <?= json_encode(setting('first_weekday')) ?>,
        settings: {
            system: <?= json_encode($system_settings) ?>,
        },
        user: {
            id: <?= session('user_id') ?>,
            email: <?= json_encode(session('user_email')) ?>,
            timezone: <?= json_encode(session('timezone')) ?>,
            role_slug: <?= json_encode(session('role_slug')) ?>,
            privileges: <?= json_encode($privileges) ?>
        }
    };

    $(function () {
        BackendSettingsLegalContents.initialize(true);
    });
</script>

<div id="legal-contents-page" class="container-fluid backend-page">
    <div id="legal-contents">
        <form>
            <fieldset>
                <legend class="border-bottom mb-4">
                    <?= lang('legal_contents') ?>
                    <?php if ($privileges[PRIV_SYSTEM_SETTINGS]['edit'] == TRUE): ?>
                        <button type="button" class="save-settings btn btn-primary btn-sm mb-2"
                                data-tippy-content="<?= lang('save') ?>">
                            <i class="fas fa-check-square me-2"></i>
                            <?= lang('save') ?>
                        </button>
                    <?php endif ?>
                </legend>

                <div class="row">
                    <div class="col-12 col-sm-11 col-md-10 col-lg-9">
                        <h4><?= lang('cookie_notice') ?></h4>

                        <div class="mb-3">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="display-cookie-notice">
                                <label class="custom-form-label" for="display-cookie-notice">
                                    <?= lang('display_cookie_notice') ?>
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label><?= lang('cookie_notice_content') ?></label>
                            <textarea id="cookie-notice-content" cols="30" rows="10" class="mb-3"></textarea>
                        </div>

                        <br>

                        <h4><?= lang('terms_and_conditions') ?></h4>

                        <div class="mb-3">
                            <div class="mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox"
                                           id="display-terms-and-conditions">
                                    <label class="custom-form-label" for="display-terms-and-conditions">
                                        <?= lang('display_terms_and_conditions') ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label><?= lang('terms_and_conditions_content') ?></label>
                            <textarea id="terms-and-conditions-content" cols="30" rows="10"
                                      class="mb-3"></textarea>
                        </div>

                        <h4><?= lang('privacy_policy') ?></h4>

                        <div class="mb-3">
                            <div class="mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" id="display-privacy-policy">
                                    <label class="custom-form-label" for="display-privacy-policy">
                                        <?= lang('display_privacy_policy') ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label><?= lang('privacy_policy_content') ?></label>
                            <textarea id="privacy-policy-content" cols="30" rows="10" class="mb-3"></textarea>
                        </div>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>

<?php section('content') ?>
