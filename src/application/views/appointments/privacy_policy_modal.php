<div id="privacy-policy-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= lang('privacy_policy') ?></h4>
            </div>
            <div class="modal-body">
                <p><?= $privacy_policy_content ?></p>
            </div>
            <div class="modal-footer">
				<?php if ($conf_notice == '1'): ?>
				<div class="form-group">
					<label for="conf-notice" style="float:left"><strong><?= lang('notice_auth'); ?> - 
						<font color="red"><?= lang('select'); ?></font></strong></label>
					<select id="conf-notice" class="form-control" ?>
						<option value="1" <?php if ((Config::WP_HEADER_FOOTER== TRUE) && ($current_user->notification == 1)):?> selected <?php endif; ?> >
						<?= lang('notice_auth_y'); ?></option>
						<option value="0" <?php if ((Config::WP_HEADER_FOOTER== TRUE) && ($current_user->notification == 0)):?> selected <?php endif; ?> >
						<?= lang('notice_auth_n'); ?></option>
					</select><br><br>
				</div>	
				<?php endif; ?>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
