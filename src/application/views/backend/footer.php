<div id="footer">
    <div id="footer-content">
        Powered by
        <a href="http://easyappointments.org">Easy!Appointments
            <?php
                echo 'v' . $this->config->item('ea_version');

                $release_title = $this->config->item('ea_release_title');
                if ($release_title != '') {
                    echo ' - ' . $release_title;
                }
            ?></a> |
        <?php echo $this->lang->line('licensed_under'); ?> GPLv3 |
        <span id="select-language" class="label label-success">
        	<?php echo ucfirst($this->config->item('language')); ?>
        </span>
        |
        <a href="<?php echo $base_url; ?>/index.php/appointments">
            <?php echo $this->lang->line('go_to_booking_page') ?>
        </a>
    </div>

    <div id="footer-user-display-name">
        <?php echo $this->lang->line('hello') . ', ' . $user_display_name; ?>!
    </div>
</div>

<script
    type="text/javascript"
    src="<?php echo $base_url; ?>/assets/js/backend.js"></script>
<script
    type="text/javascript"
    src="<?php echo $base_url; ?>/assets/js/general_functions.js"></script>
</body>
</html>
