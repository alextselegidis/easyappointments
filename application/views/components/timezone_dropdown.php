<?php
/**
 * Local variables.
 *
 * @var string $attributes
 * @var array $grouped_timezones
 */
?>

<select <?= $attributes ?>>
    <?php foreach ($grouped_timezones as $continent => $entries): ?>
        <optgroup label="<?= $continent ?>">
            <?php foreach ($entries as $value => $name): ?>
                <option value="<?= $value ?>"><?= $name ?></option>
            <?php endforeach; ?>
        </optgroup>
    <?php endforeach; ?>
</select>
