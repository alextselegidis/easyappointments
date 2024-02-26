<?php
/**
 * @var string $attributes
 */
?>

<?php section('styles'); ?>

<link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/components/color_selection.css') ?>">

<?php end_section('styles'); ?>

<label class="form-label"><?= lang('color') ?></label>

<div <?= $attributes ?? '' ?> class="color-selection d-flex justify-content-between mb-4">
    <button type="button" class="color-selection-option selected" data-value="#7cbae8">
        <i class="fas fa-check"></i>
    </button>

    <button type="button" class="color-selection-option" data-value="#acbefb">
        <i class="fas fa-check"></i>
    </button>

    <button type="button" class="color-selection-option" data-value="#82e4ec">
        <i class="fas fa-check"></i>
    </button>

    <button type="button" class="color-selection-option" data-value="#7cebc1">
        <i class="fas fa-check"></i>
    </button>

    <button type="button" class="color-selection-option" data-value="#abe9a4">
        <i class="fas fa-check"></i>
    </button>

    <button type="button" class="color-selection-option" data-value="#ebe07c">
        <i class="fas fa-check"></i>
    </button>

    <button type="button" class="color-selection-option" data-value="#f3bc7d">
        <i class="fas fa-check"></i>
    </button>

    <button type="button" class="color-selection-option" data-value="#f3aea6">
        <i class="fas fa-check"></i>
    </button>

    <button type="button" class="color-selection-option" data-value="#eb8687">
        <i class="fas fa-check"></i>
    </button>

    <button type="button" class="color-selection-option" data-value="#dfaffe">
        <i class="fas fa-check"></i>
    </button>

    <button type="button" class="color-selection-option" data-value="#e3e3e3">
        <i class="fas fa-check"></i>
    </button>
</div>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/js/components/color_selection.js') ?>"></script>

<?php end_section('scripts'); ?>
