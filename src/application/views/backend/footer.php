<div id="footer">
    <div id="footer-content" class="col-xs-12 col-sm-6">
        Powered by
        <a href="http://easyappointments.org">Easy!Appointments
            <?php
                echo 'v' . $this->config->item('version');

                $release_title = $this->config->item('release_label');
                if ($release_title != '') {
                    echo ' - ' . $release_title;
                }
            ?></a> |
        <?php echo lang('licensed_under'); ?> GPLv3 |
        <span id="select-language" class="label label-success">
        	<?php echo ucfirst($this->config->item('language')); ?>
        </span>
        |
        <a href="<?php echo site_url('appointments'); ?>">
            <?php echo lang('go_to_booking_page') ?>
        </a>
    </div>

    <div id="footer-user-display-name" class="col-xs-12 col-sm-6">
        <?php echo lang('hello') . ', ' . $user_display_name; ?>!
    </div>
</div>

<script src="<?php echo base_url('assets/js/backend.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/general_functions.js'); ?>"></script>
</body>
</html>
