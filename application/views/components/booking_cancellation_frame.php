<?php
/**
 * Local variables.
 *
 * @var bool $manage_mode
 * @var array $appointment_data
 * @var bool $display_delete_personal_information
 */
?>

<?php if ($manage_mode): ?>
    <div id="cancel-appointment-frame" class="booking-header-bar d-flex gap-3 justify-content-between align-items-center py-2 px-3 m-0 bg-warning-subtle">
        <div class="flex-fill">
            <small><?= lang('cancel_appointment_hint') ?></small>
        </div>
        <div class="text-end">
            <form id="cancel-appointment-form" method="post"
                  action="<?= site_url('booking_cancellation/of/' . $appointment_data['hash']) ?>">

                <input id="hidden-cancellation-reason" name="cancellation_reason" type="hidden">

                <button id="cancel-appointment" class="btn btn-warning btn-sm text-nowrap">
                    <i class="fas fa-trash me-2"></i>
                    <?= lang('cancel_appointment_title') ?>
                </button>
            </form>
        </div>
    </div>
    <?php if ($display_delete_personal_information): ?>
        <div class="booking-header-bar d-flex gap-3 justify-content-between align-items-center py-2 px-3 m-0 bg-danger-subtle">
            <div class="flex-fill">
                <small><?= lang('delete_personal_information_hint') ?></small>
            </div>
            <div class="text-end">
                <button id="delete-personal-information" class="btn btn-danger btn-sm text-nowrap">
                    <i class="fas fa-trash me-2"></i>
                    <?= lang('delete') ?>
                </button>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
