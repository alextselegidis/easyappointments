<?php
/**
 * Local variables.
 *
 * @var bool $disabled (false)
 * @var array $custom_fields
 */

$disabled = $disabled ?? false;
$custom_fields = $custom_fields ?? []; ?>

<?php foreach ($custom_fields as $custom_field): ?>
    <?php if ($custom_field['active']): ?>
        <div class="mb-3">
            <label for="custom-field-<?= $custom_field['id'] ?>" class="form-label">
                <?= e($custom_field['label']) ?>
                <?php if ($custom_field['required']): ?>
                    <span class="text-danger" <?= $disabled ? 'hidden' : '' ?>>*</span>
                <?php endif; ?>
            </label>

            <?php if ($custom_field['type'] === 'text'): ?>
                <input type="text"
                       id="custom-field-<?= $custom_field['id'] ?>"
                       data-field-name="<?= e($custom_field['name']) ?>"
                       class="custom-field-input <?= $custom_field['required'] ? 'required' : '' ?> form-control"
                       maxlength="255"
                       <?= $disabled ? 'disabled' : '' ?>/>

            <?php elseif ($custom_field['type'] === 'textarea'): ?>
                <textarea
                    id="custom-field-<?= $custom_field['id'] ?>"
                    data-field-name="<?= e($custom_field['name']) ?>"
                    class="custom-field-input <?= $custom_field['required'] ? 'required' : '' ?> form-control"
                    rows="3"
                    maxlength="500"
                    <?= $disabled ? 'disabled' : '' ?>></textarea>

            <?php elseif ($custom_field['type'] === 'select' && !empty($custom_field['options'])): ?>
                <select
                    id="custom-field-<?= $custom_field['id'] ?>"
                    data-field-name="<?= e($custom_field['name']) ?>"
                    class="custom-field-input <?= $custom_field['required'] ? 'required' : '' ?> form-control"
                    <?= $disabled ? 'disabled' : '' ?>>
                    <option value=""><?= lang('select') ?></option>
                    <?php foreach ($custom_field['options'] as $option): ?>
                        <option value="<?= e($option['option_value']) ?>">
                            <?= e($option['option_label']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php // Legacy custom fields for backwards compatibility ?>
<?php for ($i = 1; $i <= 5; $i++): ?>
    <?php if (setting('display_custom_field_' . $i)): ?>
        <div class="mb-3">
            <label for="legacy-custom-field-<?= $i ?>" class="form-label">
                <?= setting('label_custom_field_' . $i) ?: lang('custom_field') . ' #' . $i ?>
                <?php if (setting('require_custom_field_' . $i)): ?>
                    <span class="text-danger" <?= $disabled ? 'hidden' : '' ?>>*</span>
                <?php endif; ?>
            </label>
            <input type="text" id="legacy-custom-field-<?= $i ?>"
                   class="<?= setting('require_custom_field_' . $i) ? 'required' : '' ?> form-control"
                   maxlength="120" <?= $disabled ? 'disabled' : '' ?>/>
        </div>
    <?php endif; ?>
<?php endfor; ?>
