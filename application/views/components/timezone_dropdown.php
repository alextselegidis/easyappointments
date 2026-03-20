<?php
/**
 * Local variables.
 *
 * @var string $attributes
 * @var array $grouped_timezones
 * @var string|null $selected_value
 */
?>

<select <?= $attributes ?>>
    <?php foreach ($grouped_timezones as $continent => $entries): ?>
        <optgroup label="<?= $continent ?>">
            <?php foreach ($entries as $value => $name): ?>
                <option value="<?= $value ?>" <?= isset($selected_value) && $selected_value === $value ? 'selected' : '' ?>><?= $name ?></option>
            <?php endforeach; ?>
        </optgroup>
    <?php endforeach; ?>
</select>
