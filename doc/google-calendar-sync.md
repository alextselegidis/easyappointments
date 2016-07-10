> This page will guide you through the activation of the Google Calendar synchronization feature.

Since version 1.0 Easy!Appointments supports two way synchronization with the Google Calendar API. Every provider can link his Google Calendar account with the application and make sure that any modification made on the schedule is synced between both systems. Easy!Appointments will add extra information (if available) to Google Calendar events so that users can check event participants, reply whether they will come or not etc. Once the events are on Google Calendar they can then be synced and used by other services that support Google’s API. 

So, these are the required steps for enabling the Google Calendar Sync feature of Easy!Appointments:

1. **Working Easy!Appointments Installation**: the installation must be fully functional and configured and needs to have some services and providers.
2. **Google Calendar API Credentials**: The synchronization feature requires valid Google API credentials in order to be handle data with Google accounts. Some services have a maximum quota for their free use (fortunately the Calendar API provides 1.000.000 requests/day).
  - Go to the Google Developers Console and create a new project.
  - Select the "APIs & auth – APIs" menu and enable the Calendar API.
  - Click on the "Credentials" menu item and create a client ID for your installation and a public API access key ("Create new Client ID" and "Create new Key" buttons). When editing the client ID the redirect URI to http://url/to/easyappointments/index.php/google/oauth_callback.
3. **Enable the Sync Feature**: Go to your config.php file and update the Google Calendar Sync section with your API credentials.
4. **Link Google Calendar and Easy!Appointments**: Go to backend/calendar page, select a provider and click on the "Enable Sync" button. A new window will pop up asking you to grant concern.
 

#### Note that ...

* Currently synchronization can be triggered only from the Easy!Appointments backend or when there are changes in the appointment plan.

* Every provider user can be synced with only one Google Calendar account.

* Recursive events are not supported yet.

 

#### Useful Links ...

Google Developers – https://developers.google.com/google-apps/calendar

E!A Support Group – https://groups.google.com/forum/#!forum/easy-appointments
