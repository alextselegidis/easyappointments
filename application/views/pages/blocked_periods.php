<?php extend('layouts/backend_layout'); ?>

<?php section('content'); ?>

<div class="container-fluid backend-page" id="blocked-periods-page">

    <div class="row" id="blocked-periods">
        <div id="filter-blocked-periods" class="filter-records column col-12 col-md-5">
            <form class="input-append mb-4">
                <div class="input-group">
                    <input type="text" class="key form-control" aria-label="keyword">

                    <button class="filter btn btn-outline-secondary" type="submit"
                            data-tippy-content="<?= lang('filter') ?>">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <h4 class="text-black-50 mb-3 fw-light">
                <?= lang('blocked_periods') ?>
            </h4>

            <?php slot('after_page_title'); ?>

            <div class="results">
                <!-- JS -->
            </div>
        </div>

        <div class="record-details col-12 col-md-5">
            <div class="btn-toolbar mb-4">
                <a href="<?= site_url('business_settings') ?>" class="btn btn-outline-primary me-2">
                    <i class="fas fa-chevron-left me-2"></i>
                    <?= lang('back') ?>
                </a>
                
                <div class="add-edit-delete-group btn-group">
                    <button id="add-blocked-period" class="btn btn-primary">
                        <i class="fas fa-plus-square me-2"></i>
                        <?= lang('add') ?>
                    </button>
                    <button id="edit-blocked-period" class="btn btn-outline-secondary" disabled="disabled">
                        <i class="fas fa-edit me-2"></i>
                        <?= lang('edit') ?>
                    </button>
                    <button id="delete-blocked-period" class="btn btn-outline-secondary" disabled="disabled">
                        <i class="fas fa-trash-alt me-2"></i>
                        <?= lang('delete') ?>
                    </button>
                </div>

                <div class="save-cancel-group" style="display:none;">
                    <button id="save-blocked-period" class="btn btn-primary">
                        <i class="fas fa-check-square me-2"></i>
                        <?= lang('save') ?>
                    </button>
                    <button id="cancel-blocked-period" class="btn btn-secondary">
                        <?= lang('cancel') ?>
                    </button>
                </div>

                <?php slot('after_page_actions'); ?>
            </div>

            <h4 class="text-black-50 mb-3 fw-light">
                <?= lang('details') ?>
            </h4>

            <div class="form-message alert" style="display:none;"></div>

            <input type="hidden" id="id">

            <div class="mb-3">
                <label class="form-label" for="name">
                    <?= lang('name') ?>
                    <span class="text-danger" hidden>*</span>
                </label>
                <input id="name" class="form-control required" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label" for="start-date-time">
                    <?= lang('start') ?>
                    <span class="text-danger" hidden>*</span>
                </label>
                <input id="start-date-time" class="form-control required" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label" for="end-date-time">
                    <?= lang('end') ?>
                    <span class="text-danger" hidden>*</span>
                </label>
                <input id="end-date-time" class="form-control required" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label" for="notes">
                    <?= lang('notes') ?>
                </label>
                <textarea id="notes" rows="4" class="form-control" disabled></textarea>
            </div>

            <?php slot('after_primary_fields'); ?>
        </div>
    </div>

</div>

<?php end_section('content'); ?>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/js/utils/message.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/validation.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/url.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/ui.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/blocked_periods_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/blocked_periods.js') ?>"></script>

<?php end_section('scripts'); ?>
