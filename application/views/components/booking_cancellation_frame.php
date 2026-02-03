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
    <div id="cancel-appointment-frame" class="row booking-header-bar py-2 px-3 m-0 align-items-center" style="background: #ffeeba;">
        <div class="col-lg-9">
            <small><?= lang('cancel_appointment_hint') ?></small>
        </div>
        <div class="col-lg-3 text-end">
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
        <div class="booking-header-bar row py-2 px-3 m-0 align-items-center" style="background: #f8d7da;">
            <div class="col-lg-9">
                <small><?= lang('delete_personal_information_hint') ?></small>
            </div>
            <div class="col-lg-3 text-end">
                <button id="delete-personal-information" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash me-2"></i>
                    <?= lang('delete') ?>
                </button>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
