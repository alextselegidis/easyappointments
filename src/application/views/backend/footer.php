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
        Licensed Under GPLv3
    </div>
    
    <div id="footer-user-display-name">
        Hello, <?php echo $user_display_name; ?>!
    </div>
</div>
</body>
</html>