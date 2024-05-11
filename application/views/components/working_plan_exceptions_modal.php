<div class="modal" id="working-plan-exceptions-modal">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= lang('working_plan_exception') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label" for="working-plan-exceptions-date"><?= lang('date') ?></label>
                    <input class="form-control" id="working-plan-exceptions-date">
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="working-plan-exceptions-is-non-working-day">
                    <label class="form-check-label" for="working-plan-exceptions-is-non-working-day">
                        <?= lang('make_non_working_day') ?>
                    </label>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label class="form-label" for="working-plan-exceptions-start"><?= lang('start') ?></label>
                            <input class="form-control" id="working-plan-exceptions-start">
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label class="form-label" for="working-plan-exceptions-end"><?= lang('end') ?></label>
                            <input class="form-control" id="working-plan-exceptions-end">
                        </div>
                    </div>
                </div>

                <h3><?= lang('breaks') ?></h3>

                <p>
                    <?= lang('add_breaks_during_each_day') ?>
                </p>

                <div>
                    <button type="button" class="btn btn-outline-primary btn-sm working-plan-exceptions-add-break">
                        <i class="fas fa-plus-square me-2"></i>
                        <?= lang('add_break') ?>
                    </button>
                </div>

                <br>

                <table class="table table-striped" id="working-plan-exceptions-breaks">
                    <thead>
                    <tr>
                        <th><?= lang('start') ?></th>
                        <th><?= lang('end') ?></th>
                        <th><?= lang('actions') ?></th>
                    </tr>
                    </thead>
                    <tbody><!-- Dynamic Content --></tbody>
                </table>

                <?php slot('after_primary_working_plan_exception_fields'); ?>
            </div>
            <div class="modal-footer">
                <?php slot('before_working_plan_exception_actions'); ?>
                
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <?= lang('cancel') ?>
                </button>
                <button type="button" class="btn btn-primary" id="working-plan-exceptions-save">
                    <i class="fas fa-check-square me-2"></i>
                    <?= lang('save') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/js/components/working_plan_exceptions_modal.js') ?>"></script>

<?php end_section('scripts'); ?>
