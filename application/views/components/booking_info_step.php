<div id="wizard-frame-3" class="wizard-frame" style="display:none;">
    <div class="frame-container">

        <h2 class="frame-title"><?= lang('customer_information') ?></h2>

        <div class="row frame-content">
            <div class="col-12 col-md-6">
                <?php if (vars('display_first_name')): ?>
                    <div class="mb-3">
                        <label for="first-name" class="form-label">
                            <?= lang('first_name') ?>
                            <?php if (vars('require_first_name')): ?>
                                <span class="text-danger">*</span>
                            <?php endif ?>
                        </label>
                        <input type="text" id="first-name"
                               class="<?= vars('require_first_name') ? 'required' : '' ?> form-control" maxlength="100"/>
                    </div>
                <?php endif ?>
                
                <?php if (vars('display_last_name')): ?>
                    <div class="mb-3">
                        <label for="last-name" class="form-label">
                            <?= lang('last_name') ?>
                            <?php if (vars('require_last_name')): ?>
                                <span class="text-danger">*</span>
                            <?php endif ?>
                        </label>
                        <input type="text" id="last-name"
                               class="<?= vars('require_last_name') ? 'required' : '' ?> form-control" maxlength="120"/>
                    </div>
                <?php endif ?>
                
                <?php if (vars('display_email')): ?>
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <?= lang('email') ?>
                            <?php if (vars('require_email')): ?>
                                <span class="text-danger">*</span>
                            <?php endif ?>
                        </label>
                        <input type="text" id="email"
                               class="<?= vars('require_email') ? 'required' : '' ?> form-control" maxlength="120"/>
                    </div>
                <?php endif ?>
                
                <?php if (vars('display_phone_number')): ?>
                    <div class="mb-3">
                        <label for="phone-number" class="form-label">
                            <?= lang('phone_number') ?>
                            <?php if (vars('require_phone_number')): ?>
                                <span class="text-danger">*</span>
                            <?php endif ?>
                        </label>
                        <input type="text" id="phone-number" maxlength="60"
                               class="<?= vars('require_phone_number') ? 'required' : '' ?> form-control"/>
                    </div>
                <?php endif ?>
            </div>

            <div class="col-12 col-md-6">
                <?php if (vars('display_address')) : ?>
                    <div class="mb-3">
                        <label for="address" class="form-label">
                            <?= lang('address') ?>
                            <?php if (vars('require_address')): ?>
                                <span class="text-danger">*</span>
                            <?php endif ?>
                        </label>
                        <input type="text" id="address" class="<?= vars('require_address') ? 'required' : '' ?> form-control" maxlength="120"/>
                    </div>
                <?php endif ?>
                <?php if (vars('display_city')): ?>
                    <div class="mb-3">
                        <label for="city" class="form-label">
                            <?= lang('city') ?>
                            <?php if (vars('require_city')): ?>
                                <span class="text-danger">*</span>
                            <?php endif ?>
                        </label>
                        <input type="text" id="city" class="<?= vars('require_city') ? 'required' : '' ?> form-control" maxlength="120"/>
                    </div>
                <?php endif ?>
                <?php if (vars('display_zip_code')): ?>
                    <div class="mb-3">
                        <label for="zip-code" class="form-label">
                            <?= lang('zip_code') ?>
                            <?php if (vars('require_zip_code')): ?>
                                <span class="text-danger">*</span>
                            <?php endif ?>
                        </label>
                        <input type="text" id="zip-code" class="<?= vars('require_zip_code') ? 'required' : '' ?> form-control" maxlength="120"/>
                    </div>
                <?php endif ?>
                <?php if (vars('display_notes')): ?>
                    <div class="mb-3">
                        <label for="notes" class="form-label">
                            <?= lang('notes') ?>
                            <?php if (vars('require_notes')): ?>
                                <span class="text-danger">*</span>
                            <?php endif ?>
                        </label>
                        <textarea id="notes" maxlength="500" class="<?= vars('require_notes') ? 'required' : '' ?> form-control" rows="1"></textarea>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>

    <?php if (vars('display_terms_and_conditions')): ?>
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

    <?php if (vars('display_privacy_policy')): ?>
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
