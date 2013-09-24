/**
 * This namespace handles the js functionality of the users backend page. It uses three other
 * classes (defined below) in order to handle the admin, provider and secretary record types. 
 * 
 * @namespace BackendUsers
 */
var BackendUsers = {
    MIN_PASSWORD_LENGTH: 7, 
    
    /**
     * This flag is used when trying to cancel row editing. It is
     * true only whenever the user presses the cancel button.
     * 
     * @type {bool}
     */
    enableCancel: false,
            
    /**
     * This flag determines whether the jeditables are allowed to submit. It is  
     * true only whenever the user presses the save button.
     * 
     * @type {bool}
     */
    enableSubmit: false,
    
    /**
     * Contains the current tab record methods for the page.
     * 
     * @type AdminsHelper|ProvidersHelper|SecretariesHelper
     */
    helper: {},
    
    /**
     * Initialize the backend users page.
     * 
     * @param {bool} defaultEventHandlers (OPTIONAL) Whether to bind the default event handlers 
     * (default: true).
     */
    initialize: function(defaultEventHandlers) {
        if (defaultEventHandlers == undefined) defaultEventHandlers = true;
        
        // Instanciate default helper object (admin).
        BackendUsers.helper = new AdminsHelper();
        BackendUsers.helper.resetForm();
        BackendUsers.helper.filter('');
        
        // Fill the services and providers list boxes.
        $.each(GlobalVariables.services, function(index, service) {
            var html = '<label class="checkbox"><input type="checkbox" data-id="' + service.id + '" />' 
                    + service.name + '</label>'; 
            $('#provider-services').append(html);
        });
        
        $.each(GlobalVariables.providers, function(index, provider) {
           var html = '<label class="checkbox"><input type="checkbox" data-id="' + provider.id + '" />' 
                   + provider.first_name + ' ' + provider.last_name + '</label>';
            $('#secretary-providers').append(html);
        });
        
        // Bind event handlers.
        if (defaultEventHandlers) BackendUsers.bindEventHandlers();
    },
         
    /**
     * Binds the defauly backend users event handlers. Do not use this method on a different 
     * page because it needs the backend users page DOM.
     */
    bindEventHandlers: function() {
        /**
         * Event: Page Tab Button "Click"
         * 
         * Changes the displayed tab.
         */
        $('.tab').click(function() {
            $('.active').removeClass('active');
            $(this).addClass('active');
            $('.tab-content').hide();
            
            if ($(this).hasClass('admins-tab')) { // display admins tab
                $('#admins').show();
                BackendUsers.helper = new AdminsHelper();
            } else if ($(this).hasClass('providers-tab')) { // display providers tab
                $('#providers').show();
                BackendUsers.helper = new ProvidersHelper();
            } else if ($(this).hasClass('secretaries-tab')) { // display secretaries tab
                $('#secretaries').show();
                BackendUsers.helper = new SecretariesHelper();
                
                // Update the list with the all the available providers.
                var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_filter_providers';
                var postData = { 'key': '' };
                $.post(postUrl, postData, function(response) {
                    //////////////////////////////////////////////////////////
                    console.log('Get all db providers response:', response);
                    //////////////////////////////////////////////////////////
                    
                    if (!GeneralFunctions.handleAjaxExceptions(response)) return;
                    
                    GlobalVariables.providers = response;
                    
                    $('#secretary-providers').html('');
                    $.each(GlobalVariables.providers, function(index, provider) {
                        var html = '<label class="checkbox"><input type="checkbox" data-id="' + provider.id + '" />' 
                                + provider.first_name + ' ' + provider.last_name + '</label>';
                         $('#secretary-providers').append(html);
                     });
                     $('#secretary-providers input[type="checkbox"]').prop('disabled', true);
                }, 'json');
            }
            
            BackendUsers.helper.resetForm();
            BackendUsers.helper.filter('');
            $('.filter-key').val('');
        });
        
        /**
         * Event: Admin, Provider, Secretary Username "Focusout" 
         * 
         * When the user leaves the username input field we will need to check if the username 
         * is not taken by another record in the system. Usernames must be unique.
         */
        $('#admin-username, #provider-username, #secretary-username').focusout(function() {
            var $input = $(this);
            
            if ($input.prop('readonly') == true || $input.val() == '') {
                return;
            }
            
            var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_validate_username';
            var postData = { 
                'username': $input.val(), 
                'record_exists': ($input.parents().eq(2).find('.record-id').val() != '') ? true : false
            };
            
            $.post(postUrl, postData, function(response) {
                ///////////////////////////////////////////////////////
                console.log('Validate Username Response:', response);
                ///////////////////////////////////////////////////////
                if (!GeneralFunctions.handleAjaxExceptions(response)) return;
                if (response == false) {
                    $input.css('border', '2px solid red');
                    $input.parents().eq(3).find('.form-message').text('Username already exists.');
                    $input.parents().eq(3).find('.form-message').show();
                } else {
                    $input.css('border', '');
                    if ($input.parents().eq(3).find('.form-message').text() == 'Username already exists.') {
                        $input.parents().eq(3).find('.form-message').hide();
                    }
                }
            }, 'json');
        });
        
        // -----------------------------------------------------------------
        
        /**
         * Event: Filter Admins Button "Click"
         * 
         * Filter the admin records with the given key string.
         */
        $('.filter-admins').click(function() {
            var key = $('#admins .filter-key').val();
            $('.selected-row').removeClass('selected-row');
            BackendUsers.helper.resetForm();
            BackendUsers.helper.filter(key);
        });
        
        /**
         * Event: Filter Admin Row "Click"
         * 
         * Display the selected admin data to the user.
         */
        $(document).on('click', '.admin-row', function() {
            if ($('#admins .filter-admins').prop('disabled')) {
                $('#admins .filter-results').css('color', '#AAA');
                return; // exit because we are currently on edit mode
            }
            
            var admin = { 'id': $(this).attr('data-id') };
            $.each(BackendUsers.helper.filterResults, function(index, item) {
                if (item.id === admin.id) {
                    admin = item;
                    return;
                }
            });
            BackendUsers.helper.display(admin);
            $('.selected-row').removeClass('selected-row');
            $(this).addClass('selected-row');
            $('#edit-admin, #delete-admin').prop('disabled', false);
        });
        
        /**
         * Event: Add New Admin Button "Click"
         */
        $('#add-admin').click(function() {
            BackendUsers.helper.resetForm();
            $('#admins .add-edit-delete-group').hide();
            $('#admins .save-cancel-group').show();
            $('#admins .details').find('input, textarea').prop('readonly', false);
            $('#admins .filter-admins').prop('disabled', true);
            $('#admins .filter-results').css('color', '#AAA');
            $('#admin-password, #admin-password-confirm').addClass('required');
            $('#admin-notifications').prop('disabled', false);
        });
        
        /**
         * Event: Edit Admin Button "Click"
         */
        $('#edit-admin').click(function() {
            $('#admins .add-edit-delete-group').hide();
            $('#admins .save-cancel-group').show();
            $('.filter-admins').prop('disabled', true);
            $('#admins .filter-results').css('color', '#AAA');
            $('#admins .details').find('input, textarea').prop('readonly', false);
            $('#admin-password, #admin-password-confirm').removeClass('required');
            $('#admin-notifications').prop('disabled', false);
        });
        
        /**
         * Event: Delete Admin Button "Click"
         */
        $('#delete-admin').click(function() {
            var adminId = $('#admin-id').val();
            
            var messageBtns = {
                'Delete': function() {
                    BackendUsers.helper.delete(adminId);
                    $('#message_box').dialog('close');
                },
                'Cancel': function() {
                    $('#message_box').dialog('close');
                }
            };
            
            GeneralFunctions.displayMessageBox('Delete Admin', 'Are you sure that you want '
                    + 'to delete this record? This action cannot be undone.', messageBtns);
        });
        
        /**
         * Event: Save Admin Button "Click"
         */
        $('#save-admin').click(function() {
            var admin = {
                'first_name': $('#admin-first-name').val(),
                'last_name': $('#admin-last-name').val(),
                'email': $('#admin-email').val(),
                'mobile_number': $('#admin-mobile-number').val(),
                'phone_number': $('#admin-phone-number').val(),
                'address': $('#admin-address').val(),
                'city': $('#admin-city').val(),
                'state': $('#admin-state').val(),
                'zip_code': $('#admin-zip-code').val(),
                'notes': $('#admin-notes').val(),
                'settings': {
                    'username': $('#admin-username').val(),                    
                    'notifications': $('#admin-notifications').hasClass('active')
                }
            };
            
            // Include password if changed.
            if ($('#admin-password').val() !== '') {
                admin.settings.password = $('#admin-password').val();
            }
            
            // Include id if changed.
            if ($('#admin-id').val() !== '') {
                admin.id = $('#admin-id').val();
            }
            
            if (!BackendUsers.helper.validate(admin)) return;
            
            BackendUsers.helper.save(admin);
        });
        
        /**
         * Event: Cancel Admin Button "Click"
         * 
         * Cancel add or edit of an admin record.
         */
        $('#cancel-admin').click(function() {
            BackendUsers.helper.resetForm();
            
            var admin = { 'id': $('#admins .selected-row').attr('data-id') };
            
            $.each(BackendUsers.helper.filterResults, function(index, item) {
                if (item.id === admin.id) {
                    admin = item;
                    return;
                }
            });
            
            BackendUsers.helper.display(admin);
            
            $('#edit-admin, #delete-admin').prop('disabled', false);
        });
        
        // ------------------------------------------------------------------------
        
        /**
         * Event: Filter Providers Button "Click"
         * 
         * Filter the provider records with the given key string.
         */
        $('.filter-providers').click(function() {
            var key = $('#providers .filter-key').val();
            $('.selected-row').removeClass('selected-row');
            BackendUsers.helper.resetForm();
            BackendUsers.helper.filter(key);
        });
        
        /**
         * Event: Filter Provider Row "Click"
         * 
         * Display the selected provider data to the user.
         */
        $(document).on('click', '.provider-row', function() {
            if ($('#providers .filter-providers').prop('disabled')) {
                $('#providers .filter-results').css('color', '#AAA');
                return; // exit because we are currently on edit mode
            }
            
            var provider = { 'id': $(this).attr('data-id') };
            $.each(BackendUsers.helper.filterResults, function(index, item) {
                if (item.id === provider.id) {
                    provider = item;
                    return;
                }
            });
            BackendUsers.helper.display(provider);
            $('.selected-row').removeClass('selected-row');
            $(this).addClass('selected-row');
            $('#edit-provider, #delete-provider').prop('disabled', false);
        });
        
        /**
         * Event: Add New Provider Button "Click"
         */
        $('#add-provider').click(function() {
            BackendUsers.helper.resetForm();
            $('#providers .add-edit-delete-group').hide();
            $('#providers .save-cancel-group').show();
            $('#providers .details').find('input, textarea').prop('readonly', false);
            $('#providers .filter-providers').prop('disabled', true);
            $('#providers .filter-results').css('color', '#AAA');
            $('#provider-password, #provider-password-confirm').addClass('required');
            $('#provider-notifications').prop('disabled', false);
            $('#provider-services input[type="checkbox"]').prop('disabled', false);
            
            $('#providers .add-break').prop('disabled', false);
            $('.edit-break, .delete-break').prop('disabled', false);
            $('#providers input[type="checkbox"]').prop('disabled', false);
            
            // Apply default working plan
            $.each(GlobalVariables.workingPlan, function(index, workingDay) {
                if (workingDay != null) {
                    $('#' + index).prop('checked', true);
                    $('#' + index + '-start').val(workingDay.start);
                    $('#' + index + '-end').val(workingDay.end);

                    // Add the day's breaks on the breaks table.
                    $.each(workingDay.breaks, function(i, brk) {
                        var tr = 
                                '<tr>' + 
                                    '<td class="break-day editable">' + GeneralFunctions.ucaseFirstLetter(index) + '</td>' +
                                    '<td class="break-start editable">' + brk.start + '</td>' +
                                    '<td class="break-end editable">' + brk.end + '</td>' +
                                    '<td>' + 
                                        '<button type="button" class="btn edit-break" title="Edit Break">' +
                                            '<i class="icon-pencil"></i>' +
                                        '</button>' +
                                        '<button type="button" class="btn delete-break" title="Delete Break">' +
                                            '<i class="icon-remove"></i>' +
                                        '</button>' +
                                        '<button type="button" class="btn save-break hidden" title="Save Break">' +
                                            '<i class="icon-ok"></i>' +
                                        '</button>' +
                                        '<button type="button" class="btn cancel-break hidden" title="Cancel Break">' +
                                            '<i class="icon-ban-circle"></i>' +
                                        '</button>' +
                                    '</td>' +
                                '</tr>';
                        $('#breaks').append(tr);
                    });
                } else {
                    $('#' + index).prop('checked', false);
                    $('#' + index + '-start').prop('disabled', true);
                    $('#' + index + '-end').prop('disabled', true);
                }
            });
            
            // Make break cells editable.
            BackendUsers.editableBreakDay($('#breaks .break-day'));
            BackendUsers.editableBreakTime($('#breaks').find('.break-start, .break-end'));

            // Set timepickers where needed.
            $('.working-plan input').timepicker({
                'timeFormat': 'HH:mm',
                'onSelect': function(datetime, inst) {
                    // Start time must be earlier than end time. 
                    var start = Date.parse($(this).parent().parent().find('.work-start').val());
                    var end = Date.parse($(this).parent().parent().find('.work-end').val());

                    if (start > end) {
                        $(this).parent().parent().find('.work-end').val(start.addHours(1).toString('HH:mm'));
                    }
                }
            });
        });
        
        /**
         * Event: Day Checkbox "Click"
         * 
         * Enable or disable the time selection for each day.
         */
        $('.working-plan input[type="checkbox"]').click(function() {
            var id = $(this).attr('id');
            
            if ($(this).prop('checked') == true) {
                $('#' + id + '-start').prop('disabled', false).val('09:00');
                $('#' + id + '-end').prop('disabled', false).val('18:00');
            } else {
                $('#' + id + '-start').prop('disabled', true).val('');
                $('#' + id + '-end').prop('disabled', true).val('');
            }
        });
        
        /**
         * Event: Add Break Button "Click"
         * 
         * A new row is added on the table and the user can enter the new break 
         * data. After that he can either press the save or cancel button.
         */
        $('.add-break').click(function() {
            var tr = 
                    '<tr>' + 
                        '<td class="break-day editable">Monday</td>' +
                        '<td class="break-start editable">09:00</td>' +
                        '<td class="break-end editable">10:00</td>' +
                        '<td>' + 
                            '<button type="button" class="btn edit-break" title="Edit Break">' +
                                '<i class="icon-pencil"></i>' +
                            '</button>' +
                            '<button type="button" class="btn delete-break" title="Delete Break">' +
                                '<i class="icon-remove"></i>' +
                            '</button>' +
                            '<button type="button" class="btn save-break hidden" title="Save Break">' +
                                '<i class="icon-ok"></i>' +
                            '</button>' +
                            '<button type="button" class="btn cancel-break hidden" title="Cancel Break">' +
                                '<i class="icon-ban-circle"></i>' +
                            '</button>' +
                        '</td>' +
                    '</tr>';
            $('#breaks').prepend(tr);
            
            // Bind editable and event handlers.
            tr = $('#breaks tr').get()[1];
            BackendUsers.editableBreakDay($(tr).find('.break-day'));
            BackendUsers.editableBreakTime($(tr).find('.break-start, .break-end'));
            $(tr).find('.edit-break').trigger('click');
        });
        
        /**
         * Event: Edit Break Button "Click"
         * 
         * Enables the row editing for the "Breaks" table rows.
         */
        $(document).on('click', '.edit-break', function() {
            // Reset previous editable tds
            var $previousEdt = $(this).closest('table').find('.editable').get();
            $.each($previousEdt, function(index, edt) {
               edt.reset();
            });
            
            // Make all cells in current row editable.
            $(this).parent().parent().children().trigger('edit');
            $(this).parent().parent().find('.break-start input, .break-end input').timepicker();
            $(this).parent().parent().find('.break-day select').focus();
            
            // Show save - cancel buttons.
            $(this).closest('table').find('.edit-break, .delete-break').addClass('hidden');
            $(this).parent().find('.save-break, .cancel-break').removeClass('hidden');
            
        });
        
        /**
         * Event: Delete Break Button "Click"
         * 
         * Removes the current line from the "Breaks" table.
         */
        $(document).on('click', '.delete-break', function() {
           $(this).parent().parent().remove();
        });
        
        /**
         * Event: Cancel Break Button "Click"
         * 
         * Bring the "#breaks" table back to its initial state.
         */
        $(document).on('click', '.cancel-break', function() {
            BackendUsers.enableCancel = true;
            $(this).parent().parent().find('.cancel-editable').trigger('click');
            BackendUsers.enableCancel = false;
            
            $(this).closest('table').find('.edit-break, .delete-break').removeClass('hidden');
            $(this).parent().find('.save-break, .cancel-break').addClass('hidden');
        });
        
        /**
         * Event: Save Break Button "Click"
         * 
         * Save the editable values and restore the table to its initial state.
         */
        $(document).on('click', '.save-break', function() {
            // Break's start time must always be prior to break's end. 
            var start = Date.parse($(this).parent().parent().find('.break-start input').val());
            var end = Date.parse($(this).parent().parent().find('.break-end input').val());
            if (start > end) {
                $(this).parent().parent().find('.break-end  input').val(start.addHours(1).toString('HH:mm'));
            }
            
            BackendUsers.enableSubmit = true;
            $(this).parent().parent().find('.editable .submit-editable').trigger('click');
            BackendUsers.enableSubmit = false;
            
            $(this).closest('table').find('.edit-break, .delete-break').removeClass('hidden');
            $(this).parent().find('.save-break, .cancel-break').addClass('hidden');
        });
        
        /**
         * Event: Edit Provider Button "Click"
         */
        $('#edit-provider').click(function() {
            $('#providers .add-edit-delete-group').hide();
            $('#providers .save-cancel-group').show();
            $('.filter-providers').prop('disabled', true);
            $('#providers .filter-results').css('color', '#AAA');
            $('#providers .details').find('input, textarea').prop('readonly', false);
            $('#provider-password, #provider-password-confirm').removeClass('required');
            $('#provider-notifications').prop('disabled', false);
            $('#provider-services input[type="checkbox"]').prop('disabled', false);
            
            $('#providers .add-break').prop('disabled', false);
            $('.edit-break, .delete-break').prop('disabled', false);
            $('#providers input[type="checkbox"]').prop('disabled', false);
        });
        
        /**
         * Event: Delete Provider Button "Click"
         */
        $('#delete-provider').click(function() {
            var providerId = $('#provider-id').val();
            
            var messageBtns = {
                'Delete': function() {
                    BackendUsers.helper.delete(providerId);
                    $('#message_box').dialog('close');
                },
                'Cancel': function() {
                    $('#message_box').dialog('close');
                }
            };
            
            GeneralFunctions.displayMessageBox('Delete Provider', 'Are you sure that you want '
                    + 'to delete this record? This action cannot be undone.', messageBtns);
        });
        
        /**
         * Event: Save Provider Button "Click"
         */
        $('#save-provider').click(function() {
            var provider = {
                'first_name': $('#provider-first-name').val(),
                'last_name': $('#provider-last-name').val(),
                'email': $('#provider-email').val(),
                'mobile_number': $('#provider-mobile-number').val(),
                'phone_number': $('#provider-phone-number').val(),
                'address': $('#provider-address').val(),
                'city': $('#provider-city').val(),
                'state': $('#provider-state').val(),
                'zip_code': $('#provider-zip-code').val(),
                'notes': $('#provider-notes').val(),
                'settings': {
                    'username': $('#provider-username').val(),  
                    'working_plan': BackendUsers.helper.getWorkingPlan(),
                    'notifications': $('#provider-notifications').hasClass('active')
                }
            };
            
            // Include provider services.
            provider.services = [];
            $('#provider-services input[type="checkbox"]').each(function() {
                if ($(this).prop('checked')) {
                    provider.services.push($(this).attr('data-id'));
                }
            });
            
            // Include password if changed.
            if ($('#provider-password').val() !== '') {
                provider.settings.password = $('#provider-password').val();
            }
            
            // Include id if changed.
            if ($('#provider-id').val() !== '') {
                provider.id = $('#provider-id').val();
            }
            
            if (!BackendUsers.helper.validate(provider)) return;
            
            BackendUsers.helper.save(provider);
        });
        
        /**
         * Event: Cancel Provider Button "Click"
         * 
         * Cancel add or edit of an provider record.
         */
        $('#cancel-provider').click(function() {
            BackendUsers.helper.resetForm();
            
            var provider = { 'id': $('#providers .selected-row').attr('data-id') };
            
            $.each(BackendUsers.helper.filterResults, function(index, item) {
                if (item.id === provider.id) {
                    provider = item;
                    return;
                }
            });
            
            BackendUsers.helper.display(provider);
            
            $('#edit-provider, #delete-provider').prop('disabled', false);
        });
        
        /**
         * Event: Display Provider Details "Click"
         */
        $('#providers .display-details').click(function() {
            $('#providers .switch-view .current').removeClass('current');
            $(this).addClass('current');
            $('.working-plan-view').hide('fade', function() {
                $('.details-view').show('fade');
            });
            
        });
        
        /**
         * Event: Display Provider Working Plan "Click"
         */
        $('#providers .display-working-plan').click(function() {
            $('#providers .switch-view .current').removeClass('current');
            $(this).addClass('current');
            $('.details-view').hide('fade', function() {
                $('.working-plan-view').show('fade');
            });
        });
        
        // ------------------------------------------------------------------------
        
        /**
         * Event: Filter Secretaries Button "Click"
         * 
         * Filter the secretary records with the given key string.
         */
        $('.filter-secretaries').click(function() {
            var key = $('#secretaries .filter-key').val();
            $('.selected-row').removeClass('selected-row');
            BackendUsers.helper.resetForm();
            BackendUsers.helper.filter(key);
        });
        
        /**
         * Event: Filter Secretary Row "Click"
         * 
         * Display the selected secretary data to the user.
         */
        $(document).on('click', '.secretary-row', function() {
            if ($('#secretaries .filter-secretaries').prop('disabled')) {
                $('#secretaries .filter-results').css('color', '#AAA');
                return; // exit because we are currently on edit mode
            }
            
            var secretary = { 'id': $(this).attr('data-id') };
            $.each(BackendUsers.helper.filterResults, function(index, item) {
                if (item.id === secretary.id) {
                    secretary = item;
                    return;
                }
            });
            BackendUsers.helper.display(secretary);
            $('.selected-row').removeClass('selected-row');
            $(this).addClass('selected-row');
            $('#edit-secretary, #delete-secretary').prop('disabled', false);
        });
        
        /**
         * Event: Add New Secretary Button "Click"
         */
        $('#add-secretary').click(function() {
            BackendUsers.helper.resetForm();
            $('#secretaries .add-edit-delete-group').hide();
            $('#secretaries .save-cancel-group').show();
            $('#secretaries .details').find('input, textarea').prop('readonly', false);
            $('#secretaries .filter-secretaries').prop('disabled', true);
            $('#secretaries .filter-results').css('color', '#AAA');
            $('#secretary-password, #secretary-password-confirm').addClass('required');
            $('#secretary-notifications').prop('disabled', false);
            $('#secretary-providers input[type="checkbox"]').prop('disabled', false);
        });
        
        /**
         * Event: Edit Secretary Button "Click"
         */
        $('#edit-secretary').click(function() {
            $('#secretaries .add-edit-delete-group').hide();
            $('#secretaries .save-cancel-group').show();
            $('.filter-secretaries').prop('disabled', true);
            $('#secretaries .filter-results').css('color', '#AAA');
            $('#secretaries .details').find('input, textarea').prop('readonly', false);
            $('#secretary-password, #secretary-password-confirm').removeClass('required');
            $('#secretary-notifications').prop('disabled', false);
            $('#secretary-providers input[type="checkbox"]').prop('disabled', false);
        });
        
        /**
         * Event: Delete Secretary Button "Click"
         */
        $('#delete-secretary').click(function() {
            var providerId = $('#secretary-id').val();
            
            var messageBtns = {
                'Delete': function() {
                    BackendUsers.helper.delete(providerId);
                    $('#message_box').dialog('close');
                },
                'Cancel': function() {
                    $('#message_box').dialog('close');
                }
            };
            
            GeneralFunctions.displayMessageBox('Delete Secretary', 'Are you sure that you want '
                    + 'to delete this record? This action cannot be undone.', messageBtns);
        });
        
        /**
         * Event: Save Secretary Button "Click"
         */
        $('#save-secretary').click(function() {
            var secretary = {
                'first_name': $('#secretary-first-name').val(),
                'last_name': $('#secretary-last-name').val(),
                'email': $('#secretary-email').val(),
                'mobile_number': $('#secretary-mobile-number').val(),
                'phone_number': $('#secretary-phone-number').val(),
                'address': $('#secretary-address').val(),
                'city': $('#secretary-city').val(),
                'state': $('#secretary-state').val(),
                'zip_code': $('#secretary-zip-code').val(),
                'notes': $('#secretary-notes').val(),
                'settings': {
                    'username': $('#secretary-username').val(),                    
                    'notifications': $('#secretary-notifications').hasClass('active')
                }
            };
            
            // Include secretary services.
            secretary.providers = [];
            $('#secretary-providers input[type="checkbox"]').each(function() {
                if ($(this).prop('checked')) {
                    secretary.providers.push($(this).attr('data-id'));
                }
            });
            
            // Include password if changed.
            if ($('#secretary-password').val() !== '') {
                secretary.settings.password = $('#secretary-password').val();
            }
            
            // Include id if changed.
            if ($('#secretary-id').val() !== '') {
                secretary.id = $('#secretary-id').val();
            }
            
            if (!BackendUsers.helper.validate(secretary)) return;
            
            BackendUsers.helper.save(secretary);
        });
        
        /**
         * Event: Cancel Secretary Button "Click"
         * 
         * Cancel add or edit of an secretary record.
         */
        $('#cancel-secretary').click(function() {
            BackendUsers.helper.resetForm();
        });
    },
            
    /**
     * Initialize the editable functionality to the break day table cells.
     * 
     * @param {object} $selector The cells to be initialized.
     */
    editableBreakDay: function($selector) {
        $selector.editable(function(value, settings) {
            return value;
        }, {
            'type': 'select',
            'data': '{ "Monday": "Monday", "Tuesday": "Tuesday", "Wednesday": "Wednesday", '
                    + '"Thursday": "Thursday", "Friday": "Friday", "Saturday": "Saturday", '
                    + '"Sunday": "Sunday", "selected": "Monday"}',
            'event': 'edit',
            'height': '30px',
            'submit': '<button type="button" class="hidden submit-editable">Submit</button>',
            'cancel': '<button type="button" class="hidden cancel-editable">Cancel</button>',
            'onblur': 'ignore',
            'onreset': function(settings, td) {
                if (!BackendUsers.enableCancel) return false; // disable ESC button
            },
            'onsubmit': function(settings, td) {
                if (!BackendUsers.enableSubmit) return false; // disable Enter button
            }
        });
    },
    
    /**
     * Initialize the editable functionality to the break time table cells.
     * 
     * @param {object} $selector The cells to be initialized.
     */        
    editableBreakTime: function($selector) {
        $selector.editable(function(value, settings) {
            // Do not return the value because the user needs to press the "Save" button.
            return value;
        }, {
            'event': 'edit',
            'height': '25px',
            'submit': '<button type="button" class="hidden submit-editable">Submit</button>',
            'cancel': '<button type="button" class="hidden cancel-editable">Cancel</button>',
            'onblur': 'ignore',
            'onreset': function(settings, td) {
                if (!BackendUsers.enableCancel) return false; // disable ESC button
            },
            'onsubmit': function(settings, td) {
                if (!BackendUsers.enableSubmit) return false; // disable Enter button
            }
        });
    }
};

/**
 * This class contains the methods that will be used by the "Admins" tab of the page.
 * 
 * @class AdminsHelper
 */
var AdminsHelper = function() {
    this.filterResults = {}; // Store the results for later use.
};

/**
 * Save admin record to database.
 * 
 * @param {object} admin Contains the admin record data. If an 'id' value is provided
 * then the update operation is going to be executed.
 */
AdminsHelper.prototype.save = function(admin) {
    ////////////////////////////////////////////
    console.log('Admin data to save:', admin);
    ////////////////////////////////////////////
    
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_save_admin';
    var postData = { 'admin': JSON.stringify(admin) };
    
    $.post(postUrl, postData, function(response) {
        ////////////////////////////////////////////////
        console.log('Save Admin Response:', response);
        ////////////////////////////////////////////////
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        Backend.displayNotification('Admin saved successfully!');
        BackendUsers.helper.resetForm(true);
        // When adding a new record the "admin.id" will be undefined. In this situation
        // no record will be selected because we do not yet know the id of the new record,
        // but no error will occur either.
        BackendUsers.helper.filter($('#admins .filter-key').val(), admin.id); 
    }, 'json');
};

/**
 * Delete an admin record from database.
 * 
 * @param {int} id Record id to be deleted. 
 */
AdminsHelper.prototype.delete = function(id) {
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_delete_admin';
    var postData = { 'admin_id': id };
    
    $.post(postUrl, postData, function(response) {
        //////////////////////////////////////////////////
        console.log('Delete admin response:', response);
        //////////////////////////////////////////////////
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        Backend.displayNotification('Admin deleted successfully!');
        BackendUsers.helper.resetForm();
        BackendUsers.helper.filter($('#admins .filter-key').val());
    });
};

/**
 * Validates an admin record.
 * 
 * @param {object} admin Contains the admin data to be validated.
 * @returns {bool} Returns the validation result.
 */
AdminsHelper.prototype.validate = function(admin) {
    $('#admins .required').css('border', '');
    $('#admin-password, #admin-password-confirm').css('border', '');
    
    try {
        // Validate required fields.
        var missingRequired = false;
        $('#admins .required').each(function() {
            if ($(this).val() == '' || $(this).val() == undefined) {
                $(this).css('border', '2px solid red');
                missingRequired = true;
            }
        });
        if (missingRequired) {
            throw 'Fields with * are  required.';
        }
        
        // Validate passwords.
        if ($('#admin-password').val() != $('#admin-password-confirm').val()) {
            $('#admin-password, #admin-password-confirm').css('border', '2px solid red');
            throw 'Passwords mismatch!';
        }
        
        if ($('#admin-password').val().length < BackendUsers.MIN_PASSWORD_LENGTH
                && $('#admin-password').val() != '') {
            $('#admin-password, #admin-password-confirm').css('border', '2px solid red');
            throw 'Password must be at least ' + BackendUsers.MIN_PASSWORD_LENGTH 
                    + ' characters long.';
        }
        
        // Validate user email.
        if (!GeneralFunctions.validateEmail($('#admin-email').val())) {
            $('#admin-email').css('border', '2px solid red');
            throw 'Invalid email address!';
        }
        
        return true;
    } catch(exc) {
        $('#admins .form-message').text(exc);
        $('#admins .form-message').show();
        return false;
    }
};

/**
 * Resets the admin tab form back to its initial state. 
 * 
 * @param {bool} keepRecordData (OPTIONAL = false) If false then the current record data 
 * will remain on the form.
 */
AdminsHelper.prototype.resetForm = function(keepRecordData) {
    if (keepRecordData == undefined) keepRecordData = false;
    
    $('#admins .add-edit-delete-group').show();
    $('#admins .save-cancel-group').hide();
    $('#admins .details').find('input, textarea').prop('readonly', true);
    $('.filter-admins').prop('disabled', false);
    $('#admins .filter-results').css('color', '');
    $('#admins .form-message').hide();    
    $('#admin-notifications').prop('disabled', true);
    $('#admins .required').css('border', '');
    $('#admin-password, #admin-password-confirm').css('border', '');
    
    if (!keepRecordData) {
        $('#admins .details').find('input, textarea').val('');
        $('#admin-notifications').removeClass('active');
        $('#edit-admin, #delete-admin').prop('disabled', true);
    }
};

/**
 * Display a admin record into the admin form.
 * 
 * @param {object} admin Contains the admin record data.
 */
AdminsHelper.prototype.display = function(admin) {
    $('#admin-id').val(admin.id);
    $('#admin-first-name').val(admin.first_name);
    $('#admin-last-name').val(admin.last_name);
    $('#admin-email').val(admin.email);
    $('#admin-mobile-number').val(admin.mobile_number);
    $('#admin-phone-number').val(admin.phone_number);
    $('#admin-address').val(admin.address);
    $('#admin-city').val(admin.city);
    $('#admin-state').val(admin.state);
    $('#admin-zip-code').val(admin.zip_code);
    $('#admin-notes').val(admin.notes);
    
    $('#admin-username').val(admin.settings.username);
    if (admin.settings.notifications == true) {
        $('#admin-notifications').addClass('active');
    } else {
        $('#admin-notifications').removeClass('active');
    }
};

/**
 * Filters admin records depending a string key.
 * 
 * @param {string} key This is used to filter the admin records of the database.
 */
AdminsHelper.prototype.filter = function(key, selectRecordId) {
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_filter_admins';
    var postData = { 'key': key };
    
    $.post(postUrl, postData, function(response) {
        ///////////////////////////////////////////////////
        console.log('Filter admins response:', response);
        ///////////////////////////////////////////////////
        
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        
        BackendUsers.helper.filterResults = response;
        
        $('#admins .filter-results').html('');
        $.each(response, function(index, admin) {
            var html = AdminsHelper.prototype.getFilterHtml(admin);
            $('#admins .filter-results').append(html);
        });
        
        if (selectRecordId != undefined) {
            $('.admin-row').each(function() {
                if ($(this).attr('data-id') == selectRecordId) {
                    $(this).addClass('selected-row');
                    return false;
                }
            });
        }
    }, 'json');
};

/**
 * Get an admin row html code that is going to be displayed on the filter results list.
 * 
 * @param {object} admin Contains the admin record data.
 * @returns {string} The html code that represents the record on the filter results list.
 */
AdminsHelper.prototype.getFilterHtml = function(admin) {
    var html =
            '<div class="admin-row" data-id="' + admin.id + '">' + 
                '<strong>' + admin.first_name + ' ' + admin.last_name + '</strong><br>' +
                admin.email + ', ' + admin.mobile_number + ', ' + admin.phone_number + '<br>' + 
            '</div>';

    return html;
};

/**
 * This class contains the methods that will be used by the "Providers" tab of the page.
 * 
 * @class ProvidersHelper
 */
var ProvidersHelper = function() {
    this.filterResults = {}; // Store the results for later use.
};

/**
 * Save provider record to database.
 * 
 * @param {object} provider Contains the admin record data. If an 'id' value is provided
 * then the update operation is going to be executed.
 */
ProvidersHelper.prototype.save = function(provider) {
    //////////////////////////////////////////////////
    console.log('Provider data to save:', provider);
    //////////////////////////////////////////////////
    
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_save_provider';
    var postData = { 'provider': JSON.stringify(provider) };
    
    $.post(postUrl, postData, function(response) {
        ///////////////////////////////////////////////////
        console.log('Save Provider Response:', response);
        ///////////////////////////////////////////////////
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        Backend.displayNotification('Provider saved successfully!');
        BackendUsers.helper.resetForm(true);
        // If "id" is not defined then no record will be selected (applies when adding 
        // a new provider record).
        BackendUsers.helper.filter($('#providers .filter-key').val(), provider.id); 
    }, 'json');
};

/**
 * Delete a provider record from database.
 * 
 * @param {int} id Record id to be deleted. 
 */
ProvidersHelper.prototype.delete = function(id) {
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_delete_provider';
    var postData = { 'provider_id': id };
    
    $.post(postUrl, postData, function(response) {
        /////////////////////////////////////////////////////
        console.log('Delete provider response:', response);
        /////////////////////////////////////////////////////
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        Backend.displayNotification('Provider deleted successfully!');
        BackendUsers.helper.resetForm();
        BackendUsers.helper.filter($('#providers .filter-key').val());
    });
};

/**
 * Validates a provider record.
 * 
 * @param {object} provider Contains the admin data to be validated.
 * @returns {bool} Returns the validation result.
 */
ProvidersHelper.prototype.validate = function(provider) {
    $('#providers .required').css('border', '');
    $('#provider-password, #provider-password-confirm').css('border', '');
    
    try {
        // Validate required fields.
        var missingRequired = false;
        $('#providers .required').each(function() {
            if ($(this).val() == '' || $(this).val() == undefined) {
                $(this).css('border', '2px solid red');
                missingRequired = true;
            }
        });
        if (missingRequired) {
            throw 'Fields with * are  required.';
        }
        
        // Validate passwords.
        if ($('#provider-password').val() != $('#provider-password-confirm').val()) {
            $('#provider-password, #provider-password-confirm').css('border', '2px solid red');
            throw 'Passwords mismatch!';
        }
        
        if ($('#provider-password').val().length < BackendUsers.MIN_PASSWORD_LENGTH
                && $('#provider-password').val() != '') {
            $('#provider-password, #provider-password-confirm').css('border', '2px solid red');
            throw 'Password must be at least ' + BackendUsers.MIN_PASSWORD_LENGTH 
                    + ' characters long.';
        }
        
        // Validate user email.
        if (!GeneralFunctions.validateEmail($('#provider-email').val())) {
            $('#provider-email').css('border', '2px solid red');
            throw 'Invalid email address!';
        }
        
        return true;
    } catch(exc) {
        $('#providers .form-message').text(exc);
        $('#providers .form-message').show();
        return false;
    }
};

/**
 * Resets the admin tab form back to its initial state. 
 * 
 * @param {bool} keepRecordData (OPTIONAL = false) If true then the current record data will
 * remain on the form.
 */
ProvidersHelper.prototype.resetForm = function(keepRecordData) {
    if (keepRecordData == undefined) keepRecordData = false;
    
    $('#providers .add-edit-delete-group').show();
    $('#providers .save-cancel-group').hide();
    $('#providers .details').find('input, textarea').prop('readonly', true);
    $('.filter-providers').prop('disabled', false);
    $('#providers .filter-results').css('color', '');
    $('#providers .form-message').hide();    
    $('#provider-notifications').removeClass('active');
    $('#provider-notifications').prop('disabled', true);
    $('#provider-services input[type="checkbox"]').prop('disabled', true);
    $('#providers .required').css('border', '');
    $('#provider-password, #provider-password-confirm').css('border', '');
    $('#providers .add-break').prop('disabled', true);
    $('#providers input[type="checkbox"]').prop('disabled', true);
    $('#providers .working-plan input[type="text"]').timepicker('destroy');
    $('#breaks').find('.edit-break, .delete-break').prop('disabled', true);
    
    if (!keepRecordData) {
        $('#edit-provider, #delete-provider').prop('disabled', true);
        $('#providers .details').find('input, textarea').val('');
        $('#providers input[type="checkbox"]').prop('checked', false);
        $('#provider-services input[type="checkbox"]').prop('checked', false);
        $('#providers #breaks tbody').empty();
    }
};

/**
 * Display a provider record into the admin form.
 * 
 * @param {object} provider Contains the provider record data.
 */
ProvidersHelper.prototype.display = function(provider) {
    $('#provider-id').val(provider.id);
    $('#provider-first-name').val(provider.first_name);
    $('#provider-last-name').val(provider.last_name);
    $('#provider-email').val(provider.email);
    $('#provider-mobile-number').val(provider.mobile_number);
    $('#provider-phone-number').val(provider.phone_number);
    $('#provider-address').val(provider.address);
    $('#provider-city').val(provider.city);
    $('#provider-state').val(provider.state);
    $('#provider-zip-code').val(provider.zip_code);
    $('#provider-notes').val(provider.notes);
    
    $('#provider-username').val(provider.settings.username);
    if (provider.settings.notifications == true) {
        $('#provider-notifications').addClass('active');
    } else {
        $('#provider-notifications').removeClass('active');
    }
    
    $('#provider-services input[type="checkbox"]').prop('checked', false);
    $.each(provider.services, function(index, serviceId) {
        $('#provider-services input[type="checkbox"]').each(function() {
            if ($(this).attr('data-id') == serviceId) {
                $(this).prop('checked', true);
            }
        });
    });
    
    // Display working plan
    $('#providers #breaks tbody').empty();
    var workingPlan = $.parseJSON(provider.settings.working_plan);
    $.each(workingPlan, function(index, workingDay) {
        if (workingDay != null) {
            $('#' + index).prop('checked', true);
            $('#' + index + '-start').val(workingDay.start);
            $('#' + index + '-end').val(workingDay.end);

            // Add the day's breaks on the breaks table.
            $.each(workingDay.breaks, function(i, brk) {
                var tr = 
                        '<tr>' + 
                            '<td class="break-day editable">' + GeneralFunctions.ucaseFirstLetter(index) + '</td>' +
                            '<td class="break-start editable">' + brk.start + '</td>' +
                            '<td class="break-end editable">' + brk.end + '</td>' +
                            '<td>' + 
                                '<button type="button" class="btn edit-break" title="Edit Break">' +
                                    '<i class="icon-pencil"></i>' +
                                '</button>' +
                                '<button type="button" class="btn delete-break" title="Delete Break">' +
                                    '<i class="icon-remove"></i>' +
                                '</button>' +
                                '<button type="button" class="btn save-break hidden" title="Save Break">' +
                                    '<i class="icon-ok"></i>' +
                                '</button>' +
                                '<button type="button" class="btn cancel-break hidden" title="Cancel Break">' +
                                    '<i class="icon-ban-circle"></i>' +
                                '</button>' +
                            '</td>' +
                        '</tr>';
                $('#breaks').append(tr);
            });
        } else {
            $('#' + index).prop('checked', false);
            $('#' + index + '-start').prop('disabled', true);
            $('#' + index + '-end').prop('disabled', true);
        }
    });
    
    $('.edit-break, .delete-break').prop('disabled', true);

    // Make break cells editable.
    BackendUsers.editableBreakDay($('#breaks .break-day'));
    BackendUsers.editableBreakTime($('#breaks').find('.break-start, .break-end'));

    // Set timepickers where needed.
    $('.working-plan input').timepicker({
        'timeFormat': 'HH:mm',
        'onSelect': function(datetime, inst) {
            // Start time must be earlier than end time. 
            var start = Date.parse($(this).parent().parent().find('.work-start').val());
            var end = Date.parse($(this).parent().parent().find('.work-end').val());

            if (start > end) {
                $(this).parent().parent().find('.work-end').val(start.addHours(1).toString('HH:mm'));
            }
        }
    });
};

/**
 * Filters provider records depending a string key.
 * 
 * @param {string} key This is used to filter the provider records of the database.
 * @param {numeric} selectRecordId (OPTIONAL) If set, when the function is complete
 * a result row can be set as selected. 
 */
ProvidersHelper.prototype.filter = function(key, selectRecordId) {
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_filter_providers';
    var postData = { 'key': key };
    
    $.post(postUrl, postData, function(response) {
        //////////////////////////////////////////////////////
        console.log('Filter providers response:', response);
        //////////////////////////////////////////////////////
        
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        
        BackendUsers.helper.filterResults = response;
        
        $('#providers .filter-results').html('');
        $.each(response, function(index, provider) {
            var html = ProvidersHelper.prototype.getFilterHtml(provider);
            $('#providers .filter-results').append(html);
        });
        
        if (selectRecordId != undefined) {
            $('.provider-row').each(function() {
                if ($(this).attr('data-id') == selectRecordId) {
                    $(this).addClass('selected-row');
                    return false;
                }
            });
        }
    }, 'json');
};

/**
 * Get an provider row html code that is going to be displayed on the filter results list.
 * 
 * @param {object} provider Contains the provider record data.
 * @returns {string} The html code that represents the record on the filter results list.
 */
ProvidersHelper.prototype.getFilterHtml = function(provider) {
    var html =
            '<div class="provider-row" data-id="' + provider.id + '">' + 
                '<strong>' + provider.first_name + ' ' + provider.last_name + '</strong><br>' +
                provider.email + ', ' + provider.mobile_number + ', ' + provider.phone_number + '<br>' + 
            '</div>';

    return html;
};

/**
 * Get the current working plan.
 * 
 * @return {string} Returns the working plan (already stringified).
 */
ProvidersHelper.prototype.getWorkingPlan = function() {
    var workingPlan = {};
    $('.working-plan input[type="checkbox"').each(function() {
        var id = $(this).attr('id');
        if ($(this).prop('checked') == true) {
            workingPlan[id] = {}
            workingPlan[id].start = $('#' + id + '-start').val();
            workingPlan[id].end = $('#' + id + '-end').val();
            workingPlan[id].breaks = [];
            
            $('#breaks tr').each(function(index, tr) {
                var day = $(tr).find('.break-day').text().toLowerCase();
                if (day == id) {
                    var start = $(tr).find('.break-start').text();
                    var end = $(tr).find('.break-end').text();
                    
                    workingPlan[id].breaks.push({
                        'start': start,
                        'end': end
                    });
                    
                }
            });
            
        } else {
            workingPlan[id] = null;
        }
    });
    
    return JSON.stringify(workingPlan);
};

/**
 * This class contains the methods that will be used by the "Secretaries" tab of the page.
 * 
 * @class SecretariesHelper
 */
var SecretariesHelper = function() {
    this.filterResults = {}; // Store the results for later use.
};

/**
 * Save secretary record to database.
 * 
 * @param {object} secretary Contains the admin record data. If an 'id' value is provided
 * then the update operation is going to be executed.
 */
SecretariesHelper.prototype.save = function(secretary) {
    ////////////////////////////////////////////////////
    console.log('Secretary data to save:', secretary);
    ////////////////////////////////////////////////////
    
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_save_secretary';
    var postData = { 'secretary': JSON.stringify(secretary) };
    
    $.post(postUrl, postData, function(response) {
        ////////////////////////////////////////////////////
        console.log('Save Secretary Response:', response);
        ////////////////////////////////////////////////////
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        Backend.displayNotification('Secretary saved successfully!');
        BackendUsers.helper.resetForm();
        BackendUsers.helper.filter($('#secretaries .filter-key').val());
    }, 'json');
};

/**
 * Delete a secretary record from database.
 * 
 * @param {int} id Record id to be deleted. 
 */
SecretariesHelper.prototype.delete = function(id) {
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_delete_secretary';
    var postData = { 'secretary_id': id };
    
    $.post(postUrl, postData, function(response) {
        //////////////////////////////////////////////////////
        console.log('Delete secretary response:', response);
        //////////////////////////////////////////////////////
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        Backend.displayNotification('Secretary deleted successfully!');
        BackendUsers.helper.resetForm();
        BackendUsers.helper.filter($('#secretaries .filter-key').val());
    });
};

/**
 * Validates a secretary record.
 * 
 * @param {object} secretary Contains the admin data to be validated.
 * @returns {bool} Returns the validation result.
 */
SecretariesHelper.prototype.validate = function(secretary) {
    $('#secretaries .required').css('border', '');
    $('#secretary-password, #secretary-password-confirm').css('border', '');
    
    try {
        // Validate required fields.
        var missingRequired = false;
        $('#secretaries .required').each(function() {
            if ($(this).val() == '' || $(this).val() == undefined) {
                $(this).css('border', '2px solid red');
                missingRequired = true;
            }
        });
        if (missingRequired) {
            throw 'Fields with * are  required.';
        }
        
        // Validate passwords.
        if ($('#secretary-password').val() != $('#secretary-password-confirm').val()) {
            $('#secretary-password, #secretary-password-confirm').css('border', '2px solid red');
            throw 'Passwords mismatch!';
        }
        
        if ($('#secretary-password').val().length < BackendUsers.MIN_PASSWORD_LENGTH
                && $('#secretary-password').val() != '') {
            $('#secretary-password, #secretary-password-confirm').css('border', '2px solid red');
            throw 'Password must be at least ' + BackendUsers.MIN_PASSWORD_LENGTH 
                    + ' characters long.';
        }
        
        // Validate user email.
        if (!GeneralFunctions.validateEmail($('#secretary-email').val())) {
            $('#secretary-email').css('border', '2px solid red');
            throw 'Invalid email address!';
        }
        
        return true;
    } catch(exc) {
        $('#secretaries .form-message').text(exc);
        $('#secretaries .form-message').show();
        return false;
    }
};

/**
 * Resets the admin tab form back to its initial state. 
 */
SecretariesHelper.prototype.resetForm = function() {
    $('#secretaries .details').find('input, textarea').val('');
    $('#secretaries .add-edit-delete-group').show();
    $('#secretaries .save-cancel-group').hide();
    $('#edit-secretary, #delete-secretary').prop('disabled', true);
    $('#secretaries .details').find('input, textarea').prop('readonly', true);
    $('.filter-secretaries').prop('disabled', false);
    $('#secretaries .filter-results').css('color', '');
    $('#secretaries .form-message').hide();    
    $('#secretary-notifications').removeClass('active');
    $('#secretary-notifications').prop('disabled', true);
    $('#secretary-providers input[type="checkbox"]').prop('checked', false);
    $('#secretary-providers input[type="checkbox"]').prop('disabled', true);
    $('#secretaries .required').css('border', '');
    $('#secretary-password, #secretary-password-confirm').css('border', '');
};

/**
 * Display a secretary record into the admin form.
 * 
 * @param {object} secretary Contains the secretary record data.
 */
SecretariesHelper.prototype.display = function(secretary) {
    $('#secretary-id').val(secretary.id);
    $('#secretary-first-name').val(secretary.first_name);
    $('#secretary-last-name').val(secretary.last_name);
    $('#secretary-email').val(secretary.email);
    $('#secretary-mobile-number').val(secretary.mobile_number);
    $('#secretary-phone-number').val(secretary.phone_number);
    $('#secretary-address').val(secretary.address);
    $('#secretary-city').val(secretary.city);
    $('#secretary-state').val(secretary.state);
    $('#secretary-zip-code').val(secretary.zip_code);
    $('#secretary-notes').val(secretary.notes);
    
    $('#secretary-username').val(secretary.settings.username);
    if (secretary.settings.notifications == true) {
        $('#secretary-notifications').addClass('active');
    } else {
        $('#secretary-notifications').removeClass('active');
    }
    
    $('#secretary-providers input[type="checkbox"]').prop('checked', false);
    $.each(secretary.providers, function(index, providerId) {
        $('#secretary-providers input[type="checkbox"]').each(function() {
            if ($(this).attr('data-id') == providerId) {
                $(this).prop('checked', true);
            }
        });
    });
};

/**
 * Filters secretary records depending a string key.
 * 
 * @param {string} key This is used to filter the secretary records of the database.
 * @param {numeric} selectRecordId (OPTIONAL) If provided then the given id will be 
 * selected in the filter results (only selected, not displayed).
 */
SecretariesHelper.prototype.filter = function(key, selectRecordId) {
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_filter_secretaries';
    var postData = { 'key': key };
    
    $.post(postUrl, postData, function(response) {
        ////////////////////////////////////////////////////////
        console.log('Filter secretaries response:', response);
        ////////////////////////////////////////////////////////
        
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        
        BackendUsers.helper.filterResults = response;
        
        $('#secretaries .filter-results').html('');
        $.each(response, function(index, secretary) {
            var html = SecretariesHelper.prototype.getFilterHtml(secretary);
            $('#secretaries .filter-results').append(html);
        });
        
        if (selectRecordId != undefined) {
            $('.secretary-row').each(function() {
                if ($(this).attr('data-id') == selectRecordId) {
                    $(this).addClass('selected-row');
                    return false;
                }
            });
        }
    }, 'json');
};

/**
 * Get an secretary row html code that is going to be displayed on the filter results list.
 * 
 * @param {object} secretary Contains the secretary record data.
 * @returns {string} The html code that represents the record on the filter results list.
 */
SecretariesHelper.prototype.getFilterHtml = function(secretary) {
    var html =
            '<div class="secretary-row" data-id="' + secretary.id + '">' + 
                '<strong>' + secretary.first_name + ' ' + secretary.last_name + '</strong><br>' +
                secretary.email + ', ' + secretary.mobile_number + ', ' + secretary.phone_number + '<br>' + 
            '</div>';

    return html;
};
