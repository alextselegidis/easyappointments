<!DOCTYPE html>
<html>
<head>
    <title><?php echo $this->lang->line('appointment_registered') . ' - ' . $company_name; ?></title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    
    <?php // INCLUDE JS FILES ?>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/libs/jquery/jquery.min.js"></script>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/libs/bootstrap/bootstrap.min.js"></script>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/libs/date.js"></script>
        
    <?php // INCLUDE CSS FILES ?>
    <link 
        rel="stylesheet" 
        type="text/css" 
        href="<?php echo $this->config->base_url(); ?>assets/css/libs/bootstrap/bootstrap.css">
    <link 
        rel="stylesheet" 
        type="text/css" 
        href="<?php echo $this->config->base_url(); ?>assets/css/libs/bootstrap/bootstrap-responsive.css">
    
    <?php // SET FAVICON FOR PAGE ?>
    <link 
        rel="icon" 
        type="image/x-icon" 
        href="<?php echo $this->config->base_url(); ?>assets/images/favicon.ico">
    
    <style>
        body {
            background-color: #CAEDF3;
        }
        
        #success-frame {
            width: 630px;
            margin: 150px auto 0 auto;
            background: #FFF;
            border: 1px solid #DDDADA;
            padding: 70px;
        }
        
        #success-icon {
            float: right;
            margin-top: 17px;
        }
    </style>
    
    <script src="https://apis.google.com/js/client.js"></script>
    <script type="text/javascript">
        var GlobalVariables = {
            'appointmentData'   : <?php echo json_encode($appointment_data); ?>,
            'providerData'      : <?php echo json_encode($provider_data); ?>,
            'serviceData'       : <?php echo json_encode($service_data); ?>,
            'companyName'       : <?php echo '"' . $company_name . '"'; ?>,  
            'googleApiKey'      : <?php echo '"' . SystemConfiguration::$google_api_key . '"'; ?>,
            'googleClientId'    : <?php echo '"' . SystemConfiguration::$google_client_id . '"'; ?>,
            'googleApiScope'    : 'https://www.googleapis.com/auth/calendar'
        };
        
        var EALang = <?php echo json_encode($this->lang->language); ?>;
        
        $(document).ready(function() {
            /**
             * Event: Add Appointment to Google Calendar "Click"
             * 
             * This event handler adds the appointment to the users Google
             * Calendar Account. The event is going to be added to the "primary"
             * calendar. In order to use the API the javascript client library 
             * provided by Google is necessary.
             */
            $('#add-to-google-calendar').click(function() {
                gapi.client.setApiKey(GlobalVariables.googleApiKey);
                gapi.auth.authorize({
                    'client_id'     : GlobalVariables.googleClientId,
                    'scope'         : GlobalVariables.googleApiScope,
                    'immediate'     : false
                }, handleAuthResult);
            });
            
            /**
             * This method handles the authorization result. If the user granted access
             * to his data, then the appointment is going to be added to his calendar.
             * 
             * @param {bool} authResult The user's auth result.
             */
            function handleAuthResult(authResult) {
                try {
                    ///////////////////////////////////////////////////////////
                    console.log('Google Authorization Result: ', authResult);
                    ///////////////////////////////////////////////////////////
                    
                    if (authResult.error) {
                        throw 'Could not authorize user.';
                    }
                    
                    // The user has granted access, add the appointment to his calendar.
                    // Before making the event.insert request the the event resource data
                    // must be prepared.
                    var appointmentData = GlobalVariables.appointmentData;
                    
                    appointmentData['start_datetime'] = GeneralFunctions.ISODateString(
                            Date.parseExact(appointmentData['start_datetime'], 
                            'yyyy-MM-dd HH:mm:ss'));
                    appointmentData['end_datetime'] = GeneralFunctions.ISODateString(
                            Date.parseExact(appointmentData['end_datetime'], 
                            'yyyy-MM-dd HH:mm:ss'));

                    // Create a valid Google Calendar API resource for the new event.
                    var resource = {
                        'summary'   : GlobalVariables.serviceData['name'],
                        'location'  : GlobalVariables.companyName,
                        'start'     : {
                            'dateTime': appointmentData['start_datetime']
                        },
                        'end'       : {
                            'dateTime': appointmentData['end_datetime']
                        },
                        'attendees' : [
                            {
                                'email'         : GlobalVariables.providerData['email'],
                                'displayName'   : GlobalVariables.providerData['first_name'] + ' '
                                                    + GlobalVariables.providerData['last_name']
                            }
                        ]
                    };

                    gapi.client.load('calendar', 'v3', function() {
                        var request = gapi.client.calendar.events.insert({
                            'calendarId'    : 'primary',
                            'resource'      : resource
                        });

                        request.execute(function(response) {
                            /////////////////////////////////////////////////
                            console.log('Google API Response:', response);
                            /////////////////////////////////////////////////
                            
                            if (!response.error) {
                                $('#success-frame').append(
                                    '<br><br>' +
                                    '<div class="alert alert-success">' +
                                        '<h4>' + EALang['success'] + '</h4>' +
                                        '<p>' +
                                            EALang['appointment_added_to_google_calendar'] + 
                                            '<br>' + 
                                            '<a href="' + response.htmlLink + '" target="_blank">' + 
                                                EALang['view_appointment_in_google_calendar'] +
                                            '</a>' + 
                                        '</p>' +
                                    '</div>'
                                );
                                $('#add-to-google-calendar').hide();
                            } else {
                                throw 'Could not add the event to Google Calendar.';
                            }
                        });
                    });
                } catch(exc) {
                    // The user denied access or something else happened, display 
                    // corresponding message on the screen.
                    $('#success-frame').append(
                        '<br><br>' +
                        '<div class="alert alert-error">' + 
                            '<h4>' + EALang['oops_something_went_wrong'] + '</h4>' + 
                            '<p>' + 
                                EALang['could_not_add_to_google_calendar'] +
                                '<pre>' + exc + '</pre>' +
                            '</p>' +
                        '</div>');
                    console.log('Add To Google Calendar Exception', exc);
                }
            }   
        });
    </script>
</head>
<body>
    <div id="success-frame" class="frame-container">
        <img id="success-icon" src="<?php echo $this->config->base_url(); ?>assets/images/success.png" />

        <h3><?php echo $this->lang->line('appointment_registered'); ?></h3>
        <p><?php echo $this->lang->line('appointment_details_was_sent_to_you'); ?></p>

        <?php if ($this->config->item('ea_google_sync_feature')) { ?>
        <button id="add-to-google-calendar" class="btn btn-primary">
            <i class="icon-plus icon-white"></i>
            <?php echo $this->lang->line('add_to_google_calendar'); ?>
        </button>
        <?php } ?>
        
        <?php 
            // Display exceptions (if any).
            if (isset($exceptions)) {
                echo '<div style="margin: 10px">';
                echo '<h4>Unexpected Errors</h4>';
                foreach($exceptions as $exc) {
                    echo exceptionToHtml($exc);
                }
                echo '</div>';
            }
        ?>
    </div>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/general_functions.js"></script>
</body>
</html>