<div id="cookie-notice-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= lang('cookie_notice') ?></h4>
            </div>
            <div class="modal-body">
                <p><?= $cookie_notice_content ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal"><?= lang('close') ?></button>
            </div>
        </div>
    </div>
</div>
