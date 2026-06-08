<?php
/**
 * Local variables.
 *
 * @var array $grouped_timezones
 */
?>

<?php $embedded = vars('embedded'); ?>

<div id="wizard-frame-2" class="wizard-frame p-3 p-md-4" style="display:none;">
    <div class="frame-container py-3" style="min-height: 500px;">

        <?php if (!$embedded): ?>
            <h2 class="frame-title fw-light text-center mb-4 text-muted"><?= lang('appointment_date_and_time') ?></h2>
        <?php endif; ?>

        <div class="row frame-content<?= $embedded ? ' booking-embed-columns' : '' ?>">
            <div class="<?= $embedded ? 'booking-embed-column' : 'col-12 col-lg-6' ?>">
                <?php if ($embedded): ?>
                    <h3 id="embed-service-name" class="booking-embed-service-name fw-semibold mb-2 text-center"></h3>
                <?php endif; ?>
                <div id="select-date" class="mx-auto<?= $embedded ? ' my-2' : ' my-4' ?>"></div>

            </div>

            <div class="<?= $embedded ? 'booking-embed-column' : 'col-12 col-lg-6' ?>">
                <div id="select-time" class="mx-auto py-3"<?= $embedded ? '' : ' style="max-width: 288px;"' ?>>
                    <div class="mb-3">
                        <label for="select-timezone" class="form-label">
                            <?= lang('timezone') ?>
                        </label>
                        <?php component('timezone_dropdown', [
                            'attributes' => 'id="select-timezone" class="form-select mb-3" value="UTC"',
                            'grouped_timezones' => $grouped_timezones,
                        ]); ?>
                    </div>

                    <div id="available-hours" class="overflow-auto my-3 pe-2" style="max-height: 250px;"></div>

                </div>
            </div>
        </div>
    </div>

    <div class="command-buttons text-center my-3 mx-auto d-md-flex justify-content-md-between">
        <button type="button" id="button-back-2" class="btn button-back btn-outline-secondary" style="min-width: 120px; margin-right: 10px;"
                data-step_index="2">
            <i class="fas fa-chevron-left me-2"></i>
            <?= lang('back') ?>
        </button>
        <button type="button" id="button-next-2" class="btn button-next btn-dark" style="min-width: 120px; margin-right: 10px;"
                data-step_index="2">
            <?= lang('next') ?>
            <i class="fas fa-chevron-right ms-2"></i>
        </button>
    </div>
</div>
