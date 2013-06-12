<link rel="stylesheet" type="text/css"
        href="<?php echo $base_url; ?>assets/css/libs/jquery/fullcalendar.css" />

<script type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/libs/jquery/fullcalendar.min.js"></script>
        
<script type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/backend_calendar.js"></script>
        
<script type="text/javascript">    
    var GlobalVariables = {
        'availableProviders'    : <?php echo json_encode($available_providers); ?>,
        'availableServices'     : <?php echo json_encode($available_services); ?>,
        'baseUrl'               : <?php echo '"' . $base_url . '"'; ?>
    };
    
    $(document).ready(function() {
        BackendCalendar.initialize(true);
    });
</script>

<div id="calendar-page">
    <div id="calendar-toolbar">
        <div id="calendar-filter">
            <label for="select-provider">Provider Calendar</label>
            <select id="select-provider"></select>
            <br>
            <label for="select-service">Service Calendar</label>
            <select id="select-service"></select>
        </div>
        
        <div id="calendar-actions">
            <button id="google-sync" class="btn btn-danger btn-large">
                <i class="icon-refresh icon-white"></i>
                Synchronize
            </button>
            
            <button id="enable-sync" class="btn btn-primary btn-large">
                <i class="icon-calendar icon-white"></i>
                Enable Sync
            </button>
        </div>
    </div>
    
    <div id="calendar"></div>
</div>