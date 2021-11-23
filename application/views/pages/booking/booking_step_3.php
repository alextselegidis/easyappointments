<div id="wizard-frame-3" class="wizard-frame" style="display:none;">
    <div class="frame-container">

        <h2 class="frame-title"><?= lang('customer_information') ?></h2>

        <div class="row frame-content">
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label for="first-name" class="control-label">
                        <?= lang('first_name') ?>
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="first-name" class="required form-control" maxlength="100"/>
                </div>
                <div class="form-group">
                    <label for="last-name" class="control-label">
                        <?= lang('last_name') ?>
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="last-name" class="required form-control" maxlength="120"/>
                </div>
                <div class="form-group">
                    <label for="email" class="control-label">
                        <?= lang('email') ?>
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="email" class="required form-control" maxlength="120"/>
                </div>
                <?php if ($show_field['phone-number']) : ?>
                    <div class="form-group">
                        <label for="phone-number" class="control-label">
                            <?= lang('phone_number') ?>
                            <?= $require_phone_number === '1' ? '<span class="text-danger">*</span>' : '' ?>
                        </label>
                        <input type="text" id="phone-number" maxlength="60"
                               class="<?= $require_phone_number === '1' ? 'required' : '' ?> form-control"/>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-12 col-md-6">
                <?php if ($show_field['address']) : ?>
                    <div class="form-group">
                        <label for="address" class="control-label">
                            <?= lang('address') ?>
                        </label>
                        <input type="text" id="address" class="form-control" maxlength="120"/>
                    </div>
                <?php endif; ?>
                <?php if ($show_field['city']): ?>
                    <div class="form-group">
                        <label for="city" class="control-label">
                            <?= lang('city') ?>
                        </label>
                        <input type="text" id="city" class="form-control" maxlength="120"/>
                    </div>
                <?php endif; ?>
                <?php if ($show_field['zip-code']) : ?>
                    <div class="form-group">
                        <label for="zip-code" class="control-label">
                            <?= lang('zip_code') ?>
                        </label>
                        <input type="text" id="zip-code" class="form-control" maxlength="120"/>
                    </div>
                <?php endif; ?>
                <?php if ($show_field['notes']) : ?>
                    <div class="form-group">
                        <label for="notes" class="control-label">
                            <?= lang('notes') ?>
                        </label>
                        <textarea id="notes" maxlength="500" class="form-control" rows="1"></textarea>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($display_terms_and_conditions): ?>
        <div class="form-check mb-3">
            <input type="checkbox" class="required form-check-input" id="accept-to-terms-and-conditions">
            <label class="form-check-label" for="accept-to-terms-and-conditions">
                <?= strtr(lang('read_and_agree_to_terms_and_conditions'),
                    [
                        '{$link}' => '<a href="#" data-bs-toggle="modal" data-bs-target="#terms-and-conditions-modal">',
                        '{/$link}' => '</a>'
                    ])
                ?>
            </label>
        </div>
    <?php endif ?>

    <?php if ($display_privacy_policy): ?>
        <div class="form-check mb-3">
            <input type="checkbox" class="required form-check-input" id="accept-to-privacy-policy">
            <label class="form-check-label" for="accept-to-privacy-policy">
                <?= strtr(lang('read_and_agree_to_privacy_policy'),
                    [
                        '{$link}' => '<a href="#" data-bs-toggle="modal" data-bs-target="#privacy-policy-modal">',
                        '{/$link}' => '</a>'
                    ])
                ?>
            </label>
        </div>
    <?php endif ?>

    <div class="command-buttons">
        <button type="button" id="button-back-3" class="btn button-back btn-outline-secondary"
                data-step_index="3">
            <i class="fas fa-chevron-left me-2"></i>
            <?= lang('back') ?>
        </button>
        <button type="button" id="button-next-3" class="btn button-next btn-dark"
                data-step_index="3">
            <?= lang('next') ?>
            <i class="fas fa-chevron-right ms-2"></i>
        </button>
    </div>
</div>
