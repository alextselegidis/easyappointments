<div class="modal" id="working-plan-exceptions-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= lang('working_plan_exception') ?></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="working-plan-exceptions-date"><?= lang('date') ?></label>
                    <input class="form-control" id="working-plan-exceptions-date">
                </div>

                <div class="form-group">
                    <label for="working-plan-exceptions-start"><?= lang('start') ?></label>
                    <input class="form-control" id="working-plan-exceptions-start">
                </div>

                <div class="form-group">
                    <label for="working-plan-exceptions-end"><?= lang('end') ?></label>
                    <input class="form-control" id="working-plan-exceptions-end">
                </div>

                <h3><?= lang('breaks') ?></h3>

                <p>
                    <?= lang('add_breaks_during_each_day') ?>
                </p>

                <div>
                    <button type="button" class="btn btn-primary working-plan-exceptions-add-break">
                        <i class="fas fa-plus-square mr-2"></i>
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= lang('cancel') ?></button>
                <button type="button" class="btn btn-primary"
                        id="working-plan-exceptions-save"><?= lang('save') ?></button>
            </div>
        </div>
    </div>
</div>
