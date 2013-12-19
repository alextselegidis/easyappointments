<div id="footer">
    <div id="footer-content">
        Powered by 
        <a href="http://easyappointments.org">
            Easy!Appointments
            <?php 
                echo 'v' . $this->config->item('ea_version');
            
                $release_title = $this->config->item('ea_release_title');
                if ($release_title != '') {
                    echo ' - ' . $release_title;
                }
            ?>
        </a> | 
        <?php echo $this->lang->line('be_licensed_under'); ?> GPLv3
    </div>
    
    <div id="footer-user-display-name">
        <?php echo $this->lang->line('be_hello') . ', ' . $user_display_name; ?>!
    </div>
</div>

<script 
    type="text/javascript" 
    src="<?php echo $base_url; ?>assets/js/backend.js"></script>
<script 
    type="text/javascript" 
    src="<?php echo $base_url; ?>assets/js/general_functions.js"></script>
</body>
</html>