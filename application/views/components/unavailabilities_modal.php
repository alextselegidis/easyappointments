<?php
/**
 * Local variables.
 *
 * @var array $timezones
 * @var string $timezone
 */
?>

<div id="unavailabilities-modal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><?= lang('new_unavailability_title') ?></h3>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="modal-message alert d-none"></div>

                <form>
                    <fieldset>
                        <input id="unavailability-id" type="hidden">

                        <div class="mb-3">
                            <label for="unavailability-provider" class="form-label">
                                <?= lang('provider') ?>
                            </label>
                            <select id="unavailability-provider" class="form-select"></select>
                        </div>

                        <?php slot('after_select_appointment_provider'); ?>
                     
                        <div class="mb-3">
                            <label for="unavailability-start" class="form-label">
                                <?= lang('start') ?>
                                <span class="text-danger">*</span>
                            </label>
                            <input id="unavailability-start" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="unavailability-end" class="form-label">
                                <?= lang('end') ?>
                                <span class="text-danger">*</span>
                            </label>
                            <input id="unavailability-end" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <?= lang('timezone') ?>
                            </label>

                            <div
                                class="border rounded d-flex justify-content-between align-items-center bg-light timezone-info">
                                <div class="border-end w-50 p-1 text-center">
                                    <small>
                                        <?= lang('provider') ?>:
                                        <span class="provider-timezone">
                                            -
                                        </span>
                                    </small>
                                </div>
                                <div class="w-50 p-1 text-center">
                                    <small>
                                        <?= lang('current_user') ?>:
                                        <span>
                                            <?= $timezones[session('timezone', 'UTC')] ?>
                                        </span>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="unavailability-notes" class="form-label">
                                <?= lang('notes') ?>
                            </label>
                            <textarea id="unavailability-notes" rows="3" class="form-control"></textarea>
                        </div>

                        <?php slot('after_primary_unavailability_fields'); ?>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <?php slot('after_unavailability_actions'); ?>
                
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <?= lang('cancel') ?>
                </button>
                <button id="save-unavailability" class="btn btn-primary">
                    <i class="fas fa-check-square me-2"></i>
                    <?= lang('save') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/js/components/unavailabilities_modal.js') ?>"></script>

<?php end_section('scripts'); ?> 
