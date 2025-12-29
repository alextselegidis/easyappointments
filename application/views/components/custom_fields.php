<?php
/**
 * Local variables.
 *
 * @var bool $disabled (false)
 */

$disabled = $disabled ?? false;

// Get CodeIgniter instance
$CI =& get_instance();

// Load custom fields model if not already loaded
if (!isset($CI->custom_fields_model)) {
    $CI->load->model('custom_fields_model');
    $CI->load->model('custom_field_options_model');
}

// Get all active custom fields grouped by column
$grouped_fields = $CI->custom_fields_model->get_grouped_by_column();

?>

<div class="row">
    <?php foreach (['column_1', 'column_2'] as $column_key): ?>
        <div class="col-md-6">
            <?php if (!empty($grouped_fields[$column_key])): ?>
                <?php foreach ($grouped_fields[$column_key] as $field): ?>
                    <div class="mb-3">
                        <label for="custom-field-<?= $field['id'] ?>" class="form-label">
                            <?= e($field['label']) ?>
                            <?php if ($field['required']): ?>
                                <span class="text-danger" <?= $disabled ? 'hidden' : '' ?>>*</span>
                            <?php endif; ?>
                        </label>

                        <?php if ($field['type'] === 'text'): ?>
                            <input type="text"
                                   id="custom-field-<?= $field['id'] ?>"
                                   data-field-id="<?= $field['id'] ?>"
                                   data-field-name="<?= e($field['name']) ?>"
                                   class="custom-field-input <?= $field['required'] ? 'required' : '' ?> form-control"
                                   maxlength="255"
                                   <?= $disabled ? 'disabled' : '' ?>/>

                        <?php elseif ($field['type'] === 'textarea'): ?>
                            <textarea id="custom-field-<?= $field['id'] ?>"
                                      data-field-id="<?= $field['id'] ?>"
                                      data-field-name="<?= e($field['name']) ?>"
                                      class="custom-field-input <?= $field['required'] ? 'required' : '' ?> form-control"
                                      rows="3"
                                      maxlength="1000"
                                      <?= $disabled ? 'disabled' : '' ?>></textarea>

                        <?php elseif ($field['type'] === 'select'): ?>
                            <?php
                            $options = $CI->custom_field_options_model->get_by_field($field['id']);
                            ?>
                            <select id="custom-field-<?= $field['id'] ?>"
                                    data-field-id="<?= $field['id'] ?>"
                                    data-field-name="<?= e($field['name']) ?>"
                                    class="custom-field-input <?= $field['required'] ? 'required' : '' ?> form-select"
                                    <?= $disabled ? 'disabled' : '' ?>>
                                <option value=""><?= lang('please_select') ?></option>
                                <?php foreach ($options as $option): ?>
                                    <option value="<?= e($option['option_value']) ?>">
                                        <?= e($option['option_label']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
