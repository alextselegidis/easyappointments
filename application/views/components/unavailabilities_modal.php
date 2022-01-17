<?php
/**
 * @var array $timezones
 * @var string $timezone
 */
?>

<div id="unavailabilities-modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><?= lang('new_unavailable_title') ?></h3>
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
                            <select id="unavailability-provider" class="form-control"></select>
                        </div>

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
                            <label class="form-label"><?= lang('timezone') ?></label>

                            <ul>
                                <li>
                                    <?= lang('provider') ?>:
                                    <span class="provider-timezone">
                                        -
                                    </span>
                                </li>
                                <li>
                                    <?= lang('current_user') ?>:
                                    <span>
                                        <?= $timezones[$timezone] ?>
                                    </span>
                                </li>
                            </ul>
                        </div>

                        <div class="mb-3">
                            <label for="unavailability-notes" class="form-label">
                                <?= lang('notes') ?>
                            </label>
                            <textarea id="unavailability-notes" rows="3" class="form-control"></textarea>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-ban"></i>
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

<?php section('scripts') ?>

<script src="<?= asset_url('assets/js/components/unavailabilities_modal.js') ?>"></script>

<?php section('scripts') ?> 
