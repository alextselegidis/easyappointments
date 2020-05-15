<div id="footer">
    <div id="footer-content" class="col-xs-12 col-sm-8">
        Powered by
        <a href="https://easyappointments.org">Easy!Appointments
            <?php
                echo 'v' . config('version');

                $release_title = config('release_label');
                if ($release_title != '') {
                    echo ' - ' . $release_title;
                }
            ?></a> |
        <?= lang('licensed_under') ?> GPLv3 |
        <span id="select-language" class="label label-success">
        	<?= ucfirst(config('language')) ?>
        </span>
        |
        <a href="<?= site_url('appointments') ?>">
            <?= lang('go_to_booking_page') ?>
        </a>
    </div>

    <div id="footer-user-display-name" class="col-xs-12 col-sm-4">
        <?= lang('hello') . ', ' . $user_display_name ?>!
    </div>
</div>

<script src="<?= asset_url('assets/js/backend.js') ?>"></script>
<script src="<?= asset_url('assets/js/polyfill.js') ?>"></script>
<script src="<?= asset_url('assets/js/general_functions.js') ?>"></script>
</body>
</html>
