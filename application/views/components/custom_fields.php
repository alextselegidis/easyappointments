<?php
/**
 * Local variables.
 *
 * @var bool $disabled (false)
 */

$disabled = $disabled ?? false; ?>

<?php for ($i = 1; $i <= 5; $i++): ?>
    <?php if (setting('display_custom_field_' . $i)): ?>
        <div class="mb-3">
            <label for="custom-field-<?= $i ?>" class="form-label">
                <?= setting('label_custom_field_' . $i) ?: lang('custom_field') . ' #' . $i ?>
                <?php if (vars('require_custom_field_' . $i)): ?>
                    <span class="text-danger" hidden>*</span>
                <?php endif; ?>
            </label>
            <input type="text" id="custom-field-<?= $i ?>"
                   class="<?= vars('require_custom_field_' . $i) ? 'required' : '' ?> form-control"
                   maxlength="120" <?= $disabled ? 'disabled' : '' ?>/>
        </div>
    <?php endif; ?>
<?php endfor; ?>
