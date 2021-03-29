<div class="modal" id="working-plan-periods-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= lang('working_plan_period') ?></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="working-plan-periods-startdate"><?= lang('startdate') ?></label>
                    <input class="form-control" id="working-plan-periods-startdate">
                </div>

                <div class="form-group">
                    <label for="working-plan-periods-enddate"><?= lang('enddate') ?></label>
                    <input class="form-control" id="working-plan-periods-enddate">
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= lang('cancel') ?></button>
                <button type="button" class="btn btn-primary"
                        id="working-plan-periods-save"><?= lang('save') ?></button>
            </div>
        </div>
    </div>
</div>
