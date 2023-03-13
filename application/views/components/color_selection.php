<?php
/**
 * @var string $attributes
 */
?>

<?php section('styles') ?>

<link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/components/color_selection.css') ?>">

<?php end_section('styles') ?>

<label class="form-label"><?= lang('color') ?></label>

<div <?= $attributes ?? '' ?> class="color-selection d-flex justify-content-between mb-4">
    <button type="button" class="color-selection-option selected" data-value="#4c96cc">
        <i class="fas fa-check"></i>
    </button>

    <button type="button" class="color-selection-option" data-value="#acbefb">
        <i class="fas fa-check"></i>
    </button>

    <button type="button" class="color-selection-option" data-value="#65d8e1">
        <i class="fas fa-check"></i>
    </button>

    <button type="button" class="color-selection-option" data-value="#7cebc1">
        <i class="fas fa-check"></i>
    </button>

    <button type="button" class="color-selection-option" data-value="#54be49">
        <i class="fas fa-check"></i>
    </button>

    <button type="button" class="color-selection-option" data-value="#eddf60">
        <i class="fas fa-check"></i>
    </button>

    <button type="button" class="color-selection-option" data-value="#f3bc7d">
        <i class="fas fa-check"></i>
    </button>

    <button type="button" class="color-selection-option" data-value="#ef8c80">
        <i class="fas fa-check"></i>
    </button>

    <button type="button" class="color-selection-option" data-value="#e0292b">
        <i class="fas fa-check"></i>
    </button>

    <button type="button" class="color-selection-option" data-value="#dfaffe">
        <i class="fas fa-check"></i>
    </button>

    <button type="button" class="color-selection-option" data-value="#e3e3e3">
        <i class="fas fa-check"></i>
    </button>
</div>

<?php section('scripts') ?>

<script src="<?= asset_url('assets/js/components/color_selection.js') ?>"></script>

<?php end_section('scripts') ?>
