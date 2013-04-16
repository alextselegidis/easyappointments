<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    
    <?php // INCLUDE CSS FILES ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config->base_url(); ?>assets/css/libs/bootstrap/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config->base_url(); ?>assets/css/libs/bootstrap/bootstrap-responsive.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config->base_url(); ?>assets/css/libs/jquery/jquery-ui.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config->base_url(); ?>assets/css/libs/jquery/jquery.qtip.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config->base_url(); ?>assets/css/style.css">

    <?php // INCLUDE JS FILES ?>
    <script type="text/javascript" src="<?php echo $this->config->base_url(); ?>assets/js/libs/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->config->base_url(); ?>assets/js/libs/jquery/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->config->base_url(); ?>assets/js/libs/jquery/jquery.qtip.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->config->base_url(); ?>assets/js/libs/bootstrap/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->config->base_url(); ?>assets/js/libs/date.js"></script>
    
    <?php // SET FAVICON FOR PAGE ?>
    <link rel="icon" type="image/x-icon" href="<?php echo $this->config->base_url(); ?>assets/images/favicon.ico">
    
    <script type="text/javascript">
        $(document).ready(function() {
            var services = <?php echo json_encode($availableServices); ?>;
            var providers = <?php echo json_encode($availableProviders); ?>;
            
            // When the user clicks on a service, its available providers should 
            // become visible.
            $('#select-service').change(function() {
                var currServiceId = $('#select-service').val();
                $('#select-provider').empty();
                
                $.each(providers, function(index, provider) {
                    $.each(provider['services'], function(index, serviceId) {
                        if (serviceId == currServiceId) { 
                            // This provider can provide the selected service.
                            // Add him to the list box.
                            var option = new Option(provider['last_name'] + ' ' + provider['first_name'], provider['id']);
                            $('#select-provider').append(option);
                        }
                    });
                });
                
                refreshAvailableHours(Date.today().toString('MM/dd/yyyy'));
            });
            
            /**
             * Selected Provider Changed Event Handler
             */
            $('#select-provider').change(function() {
                refreshAvailableHours(Date.today().toString('MM/dd/yyyy'));
            });
            
            $('#select-service').trigger('change');
            
            $('.book-step').qtip({
                position: {
                    my: 'top center',
                    at: 'bottom center'
                },
                style: {
                    classes: 'qtip-green qtip-shadow custom-qtip'
                }
            });
            
            $('#select-date').datepicker({
                //dateFormat  : 'dd/mm/yy',
                onSelect    : function(dateText, inst) {
                    refreshAvailableHours(dateText);
                }
            });
            
            refreshAvailableHours(Date.today().toString('MM/dd/yyyy'));
            
            /**
             * Next Step Button Clicked
             */
            $('.button-next').click(function() {
                var nextTabIndex = parseInt($(this).attr('data-step_index')) + 1;
                
                $(this).parents().eq(1).hide('fade', function() {    
                    $('.active-step').removeClass('active-step');
                    $('#step-' + nextTabIndex).addClass('active-step');
                    $('#book-appointment-' + nextTabIndex).show('fade');
                });
            });
            
            /**
             * Back Step Button Clicked
             */
            $('.button-back').click(function() {
                var prevTabIndex = parseInt($(this).attr('data-step_index')) - 1;
                
                $(this).parents().eq(1).hide('fade', function() {    
                    $('.active-step').removeClass('active-step');
                    $('#step-' + prevTabIndex).addClass('active-step');
                    $('#book-appointment-' + prevTabIndex).show('fade');
                });
            });
            
            /**
             * Available Hour Click Event Handler 
             */
            $('#available-hours').on('click', '.available-hour', function() {
                $('.selected-hour').removeClass('selected-hour');
                $(this).addClass('selected-hour');
            });
            
            /**
             * This function makes an ajax call and returns the available 
             * hours for the selected service, provider and date.
             */
            function refreshAvailableHours(selDate) {
                // Fetch the available hours of the current date 
                // for the chosen service and provider.
                var selServiceDuration = 15; // Default duration.
                $.each(services, function(index, service) {
                    if (service['id'] == $('#select-service').val()) {
                        selServiceDuration = service['duration'];
                    }
                })

                var postData = {
                    'serviceId'         : $('#select-service').val(),
                    'providerId'        : $('#select-provider').val(),
                    'selectedDate'      : selDate,
                    'serviceDuration'   : selServiceDuration
                };

                console.log('\n\n Get Available Hours Post Data:', postData, '\n\n');

                // Make ajax post request and get the available hours.
                var ajaxurl = '<?php echo $this->config->base_url(); ?>index.php/appointments/getAvailableHours';
                jQuery.post(ajaxurl, postData, function(postResponse) {
                    ////////////////////////////////////////////////////////////////////////////////
                    console.log('\n\n Get Available Hours Post Response :', postResponse, '\n\n');
                    ////////////////////////////////////////////////////////////////////////////////

                    try {
                        var jsonResponse = jQuery.parseJSON(postResponse);
                        ////////////////////////////////////////////////////////////////////////////////
                        console.log('\n\n Get Available Hours Json Response :', jsonResponse, '\n\n');
                        ////////////////////////////////////////////////////////////////////////////////

                        // Fill the available time div
                        var currColumn = 1;
                        $('#available-hours').html('<div style="width:50px; float:left;"></div>');
                        $.each(jsonResponse, function(index, availableHour) {
                            if ((currColumn * 10) < (index + 1)) {
                                currColumn++;
                                $('#available-hours').append('<div style="width:50px; float:left;"></div>');
                            }
                            $('#available-hours div:eq(' + (currColumn - 1) + ')')
                                .append('<span class="available-hour">' + availableHour + '</span><br/>');
                        });

                    } catch(exception) {
                        // @task Display message to the user.
                    };
                });
            }
        });
    </script>
</head>

<body>
    <div id="main" class="container">
        <div id="book-appointment">
            <div id="top-bar">
                <span id="business-name"><?php echo $businessName; ?></span>
                <div id="book-steps">
                    <div id="step-1" class="book-step active-step" title="Select Service & Provider">
                        <strong>1</strong>
                    </div>
                    
                    <div id="step-2" class="book-step" title="Select Appointment Date">
                        <strong>2</strong>
                    </div>
                    <div id="step-3" class="book-step" title="Your Information">
                        <strong>3</strong>
                    </div>
                    <div id="step-4" class="book-step" title="Confirm Appointment">
                        <strong>4</strong>
                    </div>
                </div>
            </div>

            <?php // SELECT SERVICE AND PROVIDER ?>
            <div id="book-appointment-1" class="book-appoinment-step">
                <h2>Select Service & Provider</h2>
                <label for="select-service">Select Service</label>
                <select id="select-service">
                    <?php 
                        foreach($availableServices as $service) {
                            echo '<option value="' . $service['id'] . '">' . $service['name'] . '</option>';
                        }
                    ?>
                </select>

                <label for="select-provider">Select Provider</label>
                <select id="select-provider"></select>
                
                <div class="command-buttons">
                    <button type="button" id="button-next-1" class="btn button-next" data-step_index="1">Next</button>
                </div>
            </div>

            <?php // APPOINTMENT DATE ?>
            <div id="book-appointment-2" class="book-appoinment-step" style="display:none;">
                <h2>Select Appointment Date And Time</h2>
                <div class="span3">
                    <div id="select-date"></div>
                </div>
                
                <div class="span3">
                    <?php // Available hours are going to be fetched via ajax call. ?>
                    <div id="available-hours"></div>
                </div>
                
                <div class="command-buttons">
                    <button type="button" id="button-back-2" class="btn button-back" data-step_index="2">Back</button>
                    <button type="button" id="button-next-2" class="btn button-next" data-step_index="2">Next</button>
                </div>
            </div>

            <?php // CUSTOMER'S INFO ?>
            <div id="book-appointment-3" class="book-appoinment-step" style="display:none;">
                <h2>Fill In Your Information</h2>
                
                <div class="span3">
                    <label for="last-name">Last Name *</label>
                    <input type="text" id="last-name" maxlength="250" />

                    <label for="first-name">First Name</label>
                    <input type="text" id="first-name" maxlength="100" />

                    <label for="email">Email *</label>
                    <input type="text" id="email" maxlength="250" />

                    <label for="phone-number">Phone Number *</label>
                    <input type="text" id="phone-number" maxlength="60" />
                </div>
                
                <div class="span3">
                    <label for="address">Address</label>
                    <input type="text" id="address" maxlength="250" />

                    <label for="city">City</label>
                    <input type="text" id="city" maxlength="120" />

                    <label for="zip-code">Zip Code</label>
                    <input type="text" id="zip-code" maxlength="120" />

                    <label for="notes">Notes</label>
                    <textarea id="notes" maxlength="500"></textarea>
                </div>
                
                <div class="command-buttons">
                    <button type="button" id="button-back-3" class="btn button-back" data-step_index="3">Back</button>
                    <button type="button" id="button-next-3" class="btn button-next" data-step_index="3">Next</button>
                </div>
            </div>

            <?php // CONFIRMATION STEP ?>
            <div id="book-appointment-4" class="book-appoinment-step" style="display:none;">
                <h2>Confirm Appointment</h2>
                
                <div id="appointment-info">
                    
                    
                </div>
                
                <div id="customer-info">
                    
                </div>
                
                <div class="command-buttons">
                    <button type="button" id="button-back-4" class="btn button-back" data-step_index="4">Back</button>
                    <form>
                        <button type="submit" class="btn-success">Confirm Appointment</button>
                    </form>
                </div>
            </div>
            
            <div id="frame-footer">
                <em>Powered By <a href="https://code.google.com/p/easy-appointments/">Easy!Appointments</a></em>
            </div>
        </div>
    </div>
</body>
</html>
