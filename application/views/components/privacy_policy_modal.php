<?php
/**
 * Local variables.
 * 
 * @var string $privacy_policy_content
 */
?>

<div id="privacy-policy-modal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?= lang('privacy_policy') ?></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"><span aria-hidden="true"></span>
                </button>
            </div>
            <div class="modal-body">
                <p><?= e($privacy_policy_content) ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <?= lang('close') ?>
                </button>
            </div>
        </div>
    </div>
</div>
