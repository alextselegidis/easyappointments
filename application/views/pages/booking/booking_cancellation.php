<?php
/**
 * @var bool $manage_mode
 * @var array $appointment_data
 */
?>

<?php if ($manage_mode): ?>
    <div id="cancel-appointment-frame" class="row booking-header-bar">
        <div class="col-12 col-md-10">
            <small><?= lang('cancel_appointment_hint') ?></small>
        </div>
        <div class="col-12 col-md-2">
            <form id="cancel-appointment-form" method="post"
                  action="<?= site_url('booking/cancel/' . $appointment_data['hash']) ?>">

                <input type="hidden" name="csrfToken" value="<?= $this->security->get_csrf_hash() ?>"/>

                <input id="cancel-reason" name="cancel_reason" type="hidden">

                <button id="cancel-appointment" class="btn btn-warning btn-sm">
                    <?= lang('cancel') ?>
                </button>
            </form>
        </div>
    </div>
    <div class="booking-header-bar row">
        <div class="col-12 col-md-10">
            <small><?= lang('delete_personal_information_hint') ?></small>
        </div>
        <div class="col-12 col-md-2">
            <button id="delete-personal-information"
                    class="btn btn-danger btn-sm"><?= lang('delete') ?></button>
        </div>
    </div>
<?php endif ?>
