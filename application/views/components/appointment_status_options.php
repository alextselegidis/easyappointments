<?php
/**
 * @var string $attributes
 */
?>

<div class="appointment-status-options" <?= $attributes ?? '' ?>>
    <ul class="list-group">
        <!-- JS -->
    </ul>

    <button type="button" class="btn btn-outline-primary btn-sm add-appointment-status-option">
        <i class="fas fa-plus-square me-2"></i>
        <?= lang('add') ?>
    </button>
</div>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/js/components/appointment_status_options.js') ?>"></script>

<?php end_section('scripts'); ?>
