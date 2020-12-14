/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

$(document).ready(function () {
    /**
     * Event: Add Appointment to Google Calendar "Click"
     *
     * This event handler adds the appointment to the users Google Calendar Account. The event is going to
     * be added to the "primary" calendar. In order to use the API the javascript client library provided by
     * Google is necessary.
     */
    $('#add-to-google-calendar').on('click', function () {
        gapi.client.setApiKey(GlobalVariables.googleApiKey);
        gapi.auth.authorize({
            client_id: GlobalVariables.googleClientId,
            scope: GlobalVariables.googleApiScope,
            immediate: false
        }, handleAuthResult);
    });

    /**
     * Handle Authorization Result
     *
     * This method handles the authorization result. If the user granted access to his data, then the
     * appointment is going to be added to his calendar.
     *
     * @param {Boolean} authResult The user's authorization result.
     */
    function handleAuthResult(authResult) {
        try {
            if (authResult.error) {
                throw new Error('Could not authorize user.');
            }

            // The user has granted access, add the appointment to his calendar. Before making the event.insert request
            // the the event resource data must be prepared.
            var providerData = GlobalVariables.providerData;

            var appointmentData = GlobalVariables.appointmentData;

            // Create a valid Google Calendar API resource for the new event.
            var resource = {
                summary: GlobalVariables.serviceData.name,
                location: GlobalVariables.companyName,
                start: {
                    dateTime: moment.tz(appointmentData.start_datetime, providerData.timezone).format()
                },
                end: {
                    dateTime: moment.tz(appointmentData.end_datetime, providerData.timezone).format()
                },
                attendees: [
                    {
                        email: GlobalVariables.providerData.email,
                        displayName: GlobalVariables.providerData.first_name + ' '
                            + GlobalVariables.providerData.last_name
                    }
                ]
            };

            gapi.client.load('calendar', 'v3', function () {
                var request = gapi.client.calendar.events.insert({
                    calendarId: 'primary',
                    resource: resource
                });

                request.execute(function (response) {
                    if (response.error) {
                        throw new Error('Could not add the event to Google Calendar.');
                    }

                    $('#success-frame').append(
                        $('<br/>'),
                        $('<div/>', {
                            'class': 'alert alert-success col-xs-12',
                            'html': [
                                $('<h4/>', {
                                    'text': EALang.success
                                }),
                                $('<p/>', {
                                    'text': EALang.appointment_added_to_google_calendar
                                }),
                                $('<a/>', {
                                    'href': response.htmlLink,
                                    'text': EALang.view_appointment_in_google_calendar
                                })
                            ]
                        })
                    );
                    $('#add-to-google-calendar').hide();
                });
            });
        } catch (error) {
            // The user denied access or something else happened, display corresponding message on the screen.
            $('#success-frame').append(
                $('<br/>'),
                $('<div/>', {
                    'class': 'alert alert-danger col-xs-12',
                    'html': [
                        $('<h4/>', {
                            'text': EALang.oops_something_went_wrong
                        }),
                        $('<p/>', {
                            'text': EALang.could_not_add_to_google_calendar
                        }),
                        $('<pre/>', {
                            'text': error.message
                        }),
                    ]
                })
            );
        }
    }
});
