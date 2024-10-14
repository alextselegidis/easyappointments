<?php
/**
 * Local variables.
 *
 * @var array $roles
 */
?>

<div id="ldap-import-modal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><?= lang('import') ?></h3>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label" for="ldap-import-ldap-dn">
                        <?= lang('ldap_dn') ?>
                        <span class="text-danger">*</span>
                    </label>
                    <input id="ldap-import-ldap-dn" class="form-control required" maxlength="256">
                </div>
                
                <div class="mb-3">
                    <label class="form-label" for="ldap-import-role-slug">
                        <?= lang('role') ?>
                        <span class="text-danger">*</span>
                    </label>
                    <select id="ldap-import-role-slug" class="form-select required">
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['slug'] ?>">
                                <?= $role['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label" for="ldap-import-first-name">
                        <?= lang('first_name') ?>
                        <span class="text-danger">*</span>
                    </label>
                    <input id="ldap-import-first-name" class="form-control required" maxlength="256">
                </div>

                <div class="mb-3">
                    <label class="form-label" for="ldap-import-last-name">
                        <?= lang('last_name') ?>
                        <span class="text-danger">*</span>
                    </label>
                    <input id="ldap-import-last-name" class="form-control required" maxlength="256">
                </div>

                <div class="mb-3">
                    <label class="form-label" for="ldap-import-email">
                        <?= lang('email') ?>
                        <span class="text-danger">*</span>
                    </label>
                    <input id="ldap-import-email" class="form-control required" max="512">
                </div>

                <div class="mb-3">
                    <label class="form-label" for="ldap-import-phone-number">
                        <?= lang('phone_number') ?>
                        <span class="text-danger">*</span>
                    </label>
                    <input id="ldap-import-phone-number" class="form-control required" max="128">
                </div>

                <div class="mb-3">
                    <label class="form-label" for="ldap-import-username">
                        <?= lang('username') ?>
                    </label>
                    <input id="ldap-import-username" class="form-control" maxlength="256">
                </div>

                <div class="mb-3">
                    <label class="form-label" for="ldap-import-password">
                        <?= lang('password') ?>
                    </label>
                    <input type="password" id="ldap-import-password" class="form-control"
                           maxlength="512" autocomplete="new-password">
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <?= lang('cancel') ?>
                </button>
                <button id="ldap-import-save" class="btn btn-primary">
                    <i class="fas fa-check-square me-2"></i>
                    <?= lang('save') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/js/components/ldap_import_modal.js') ?>"></script>

<?php end_section('scripts'); ?>
