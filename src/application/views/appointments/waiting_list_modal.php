<div id="waiting-list-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
		<div class="modal-content">
            <div class="modal-body">
                <p><?= $waiting_list_content ?></p>
            </div>
			<div class="modal-body">
				<div class="modal-message alert" style="display: none;"></div>
				<div class="container-fluid" style= "margin-left:30px; margin-right:30px;">
					<div class="form-group">
						<label for="email2" class="control-label">*<?= lang('email'); ?></label>
						<input type="text" id="email2" class="form-control" maxlength="250" />
					</div>
					<div class="form-group">
						<label for="cell-carrier2">
							<strong><?= lang('cell_carrier'); ?></strong>
						</label>
						<select id="cell-carrier2" class="col-md-4 form-control">
							<option disabled selected> <?= lang('select'); ?> </option>	 
							<?php foreach($cell_services as $carrier) {
									echo '<option value="' . $carrier['cellurl'] . '">' . $carrier['cellco'] . '</option>';
							}?>
						</select><br><br>
						<div class="form-group">
							<label for="phone-number2" class="control-label"><?= lang('phone_number'); ?></label>
							<input type="text" id="phone-number2" class="form-control" maxlength="60" />
						</div>
						<br><br>
					</div>					
				</div>
			</div>
			<div class="modal-footer">
				<form id="save-waitinglist-form" style="display:inline-block" method="post">
					<button id="save-waitinglist" type="button" name="submit2" class="btn btn-success" >
						<span class="glyphicon glyphicon-ok"></span>
						<?= lang('save'); ?>
					</button>
					<input type="hidden" name="csrfToken" />
					<input type="hidden" name="post_waiting" />
				</form>
				<button id="cancel-waitinglist" class="btn" data-dismiss="modal">
					<?= lang('cancel'); ?>
				</button>
			</div>				
			<em id="form-message" class="text-danger"><?= lang('fields_are_required'); ?></em>
		</div>
	</div>								
</div>	
