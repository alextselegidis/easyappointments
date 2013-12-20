/**
 * This namespace handles the js functionality of the users backend page. It uses three other
 * classes (defined below) in order to handle the admin, provider and secretary record types. 
 * 
 * @namespace BackendUsers
 */
var BackendUsers = {
    MIN_PASSWORD_LENGTH: 7, 
    
    /**
     * Contains the current tab record methods for the page.
     * 
     * @type AdminsHelper|ProvidersHelper|SecretariesHelper
     */
    helper: {},
            
    /**
     * Use this class instance for performing actions on the working plan.
     * 
     * @type {object}
     */
    wp: {},
        
    /**
     * Initialize the backend users page.
     * 
     * @param {bool} defaultEventHandlers (OPTIONAL) Whether to bind the default event handlers 
     * (default: true).
     */
    initialize: function(defaultEventHandlers) {
        if (defaultEventHandlers == undefined) defaultEventHandlers = true;
        
        // Initialize jScrollPane Scrollbars
        $('#filter-admins .results').jScrollPane();
        $('#filter-providers .results').jScrollPane();
        $('#filter-secretaries .results').jScrollPane();
        
        // Instanciate default helper object (admin).
        BackendUsers.helper = new AdminsHelper();
        BackendUsers.helper.resetForm();
        BackendUsers.helper.filter('');
        
        BackendUsers.wp = new WorkingPlan();
        BackendUsers.wp.bindEventHandlers();
        
        // Fill the services and providers list boxes.
        $.each(GlobalVariables.services, function(index, service) {
            var html = '<label class="checkbox"><input type="checkbox" data-id="' + service.id + '" />' 
                    + service.name + '</label>'; 
            $('#provider-services').append(html);
        });
        $('#provider-services').jScrollPane({ mouseWheelSpeed: 70 });
                
        $.each(GlobalVariables.providers, function(index, provider) {
           var html = '<label class="checkbox"><input type="checkbox" data-id="' + provider.id + '" />' 
                   + provider.first_name + ' ' + provider.last_name + '</label>';
            $('#secretary-providers').append(html);
        });
        $('#secretary-providers').jScrollPane({ mouseWheelSpeed: 70 });
        
        $('#reset-working-plan').qtip({
            position: {
                my: 'top center',
                at: 'bottom center'
            },
            style: {
                classes: 'qtip-green qtip-shadow custom-qtip'
            }
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
            $(this).parent().find('.active').removeClass('active');
            $(this).addClass('active');
            $('.tab-content').hide();
            
            if ($(this).hasClass('admins-tab')) { // display admins tab
                $('#admins').show();
                BackendUsers.helper = new AdminsHelper();
            } else if ($(this).hasClass('providers-tab')) { // display providers tab
                $('#providers').show();
                $('#provider-services').data('jsp').destroy();
                $('#provider-services').jScrollPane({ mouseWheelSpeed: 70 });
                BackendUsers.helper = new ProvidersHelper();
            } else if ($(this).hasClass('secretaries-tab')) { // display secretaries tab
                $('#secretaries').show();
                BackendUsers.helper = new SecretariesHelper();
                
                // Update the list with the all the available providers.
                var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_filter_providers';
                var postData = { 'key': '' };
                $.post(postUrl, postData, function(response) {
                    //////////////////////////////////////////////////////////
                    //console.log('Get all db providers response:', response);
                    //////////////////////////////////////////////////////////
                    
                    if (!GeneralFunctions.handleAjaxExceptions(response)) return;
                    
                    GlobalVariables.providers = response;
                
                    $('#secretary-providers').data('jsp').destroy();
                    $('#secretary-providers').html('');
                    $.each(GlobalVariables.providers, function(index, provider) {
                        var html = '<label class="checkbox"><input type="checkbox" data-id="' + provider.id + '" />' 
                                + provider.first_name + ' ' + provider.last_name + '</label>';
                         $('#secretary-providers').append(html);
                     });
                     $('#secretary-providers input[type="checkbox"]').prop('disabled', true);
                     $('#secretary-providers').jScrollPane({ mouseWheelSpeed: 70 });
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
                'user_id': $input.parents().eq(2).find('.record-id').val()
            };
            
            $.post(postUrl, postData, function(response) {
                ///////////////////////////////////////////////////////
                console.log('Validate Username Response:', response);
                ///////////////////////////////////////////////////////
                if (!GeneralFunctions.handleAjaxExceptions(response)) return;
                if (response == false) {
                    $input.css('border', '2px solid red');
                    $input.attr('already-exists', 'true');
                    $input.parents().eq(3).find('.form-message').text(EALang['username_already_exists']);
                    $input.parents().eq(3).find('.form-message').show();
                } else {
                    $input.css('border', '');
                    $input.attr('already-exists', 'false');
                    if ($input.parents().eq(3).find('.form-message').text() == EALang['username_already_exists']) {
                        $input.parents().eq(3).find('.form-message').hide();
                    }
                }
            }, 'json');
        });
        
        // ------------------------------------------------------------------------
        
        AdminsHelper.prototype.bindEventHandlers();
        
        // ------------------------------------------------------------------------
        
        ProvidersHelper.prototype.bindEventHandlers();
        
        // ------------------------------------------------------------------------
        
        SecretariesHelper.prototype.bindEventHandlers();
        
        // ------------------------------------------------------------------------
    }
};