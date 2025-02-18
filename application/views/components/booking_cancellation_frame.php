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
    <div id="cancel-appointment-frame" class="row booking-header-bar">
        <div class="col-md-10">
            <small><?= lang('cancel_appointment_hint') ?></small>
        </div>
        <div class="col-md-2">
            <form id="cancel-appointment-form" method="post"
                  action="<?= site_url('booking_cancellation/of/' . $appointment_data['hash']) ?>">

                <input id="hidden-cancellation-reason" name="cancellation_reason" type="hidden">

                <button id="cancel-appointment" class="btn btn-warning btn-sm">
                    <i class="fas fa-trash me-2"></i>
                    <?= lang('cancel') ?>
                </button>
            </form>
        </div>
    </div>
    <?php if ($display_delete_personal_information): ?>
        <div class="booking-header-bar row">
            <div class="col-md-10">
                <small><?= lang('delete_personal_information_hint') ?></small>
            </div>
            <div class="col-md-2">
                <button id="delete-personal-information" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash me-2"></i>
                    <?= lang('delete') ?>
                </button>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
