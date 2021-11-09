<div id="manage-unavailable" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><?= lang('new_unavailable_title') ?></h3>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-message alert d-none"></div>

                <form>
                    <fieldset>
                        <input id="unavailable-id" type="hidden">

                        <div class="form-group">
                            <label for="unavailable-provider" class="control-label">
                                <?= lang('provider') ?>
                            </label>
                            <select id="unavailable-provider" class="form-control"></select>
                        </div>

                        <div class="form-group">
                            <label for="unavailable-start" class="control-label">
                                <?= lang('start') ?>
                                <span class="text-danger">*</span>
                            </label>
                            <input id="unavailable-start" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="unavailable-end" class="control-label">
                                <?= lang('end') ?>
                                <span class="text-danger">*</span>
                            </label>
                            <input id="unavailable-end" class="form-control">
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= lang('timezone') ?></label>

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

                        <div class="form-group">
                            <label for="unavailable-notes" class="control-label">
                                <?= lang('notes') ?>
                            </label>
                            <textarea id="unavailable-notes" rows="3" class="form-control"></textarea>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-dismiss="modal">
                    <i class="fas fa-ban"></i>
                    <?= lang('cancel') ?>
                </button>
                <button id="save-unavailable" class="btn btn-primary">
                    <i class="fas fa-check-square mr-2"></i>
                    <?= lang('save') ?>
                </button>
            </div>
        </div>
    </div>
</div>
