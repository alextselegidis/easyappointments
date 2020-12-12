# Google Calendar Sync

> This page will guide you through the activation of the Google Calendar synchronization feature.

Since version 1.0 Easy!Appointments supports two way synchronization with the Google Calendar API. Every provider can link his Google Calendar account with the application and make sure that any modification made on the schedule is synced between both systems. Easy!Appointments will add extra information (if available) to Google Calendar events so that users can check event participants, reply whether they will come or not etc. Once the events are on Google Calendar they can then be synced and used by other services that support Google’s API. 

So, these are the required steps for enabling the Google Calendar Sync feature of Easy!Appointments.

## Working Easy!Appointments Installation

The installation must be fully functional and configured and needs to have some services and providers.

## Google OAuth2 Credentials

The synchronization feature requires valid Google API credentials in order to reach data from Google accounts. Some services have a maximum quota for their free use (fortunately the Calendar API provides 1.000.000 requests/day).

- Go to the Google Cloud Console and create a new project.
- Click on **Use Google APIs** box and enable the Google Calendar API.
- Then click on the **Credentials** menu item (on the left) and create an **OAuth client ID** for your installation. You will need to fill the consent screen information and then select **Web Application** in the next frame. Give it a client name and add your installation domain as the **Authorised JavaScript origins** (important give only the domain not the complete URL e.g. `http://mywebsite.com`). The **Authorised redirect URIs** field will need the following value `https://url/to/easyappointments/folder/index.php/google/oauth_callback` (replace `url/to/easyappointments/installation` with your real domain).
- Click on **Create** button to complete the wizard. Afterwards Google Cloud will show you two key strings that you need to mark for the following step.

## Google API Key

Customers will be able to sync their appointments with their Google Calendars but beforehand you will need an API key. Click again on the **Create credentials** button while being in the Credentials overview page and select **API Key**. A modal dialog will be shown asking you the type of key to create. Select the **Browser Key** option and fill the following form with your installation data.

## Easy!Appointments Syncing Feature

To enable the synchronization edit your root config.php file and update the Google Calendar Sync section with your API credentials.

  - `GOOGLE_SYNC_FEATURE` needs to be set to `TRUE`.
  - `GOOGLE_PRODUCT_NAME` needs to have the same name as the Google Cloud Console project. 
  - `GOOGLE_CLIENT_ID` needs the client ID from the OAuth2 credentials.
  - `GOOGLE_CLIENT_SECRET` needs the client secret from the OAuth2 credentials.
  - `GOOGLE_API_KEY` needs the API key created in the previous section.
  
4. **Link Google Calendar and Easy!Appointments**: Go to backend/calendar page, select a provider and click on the "Enable Sync" button. A new window will pop up asking you to grant concern. Enter the user's credentials and the sync will be activated!

#### Note that ...

* Currently synchronization can only be triggered from the Easy!Appointments backend or whenever there are changes in the appointment plan.

* Every provider user can be synced with only one Google Calendar account.

* Recursive events are not supported yet.

#### Useful Links ...

Google Developers – https://developers.google.com/google-apps/calendar

E!A Support Group – https://groups.google.com/forum/#!forum/easy-appointments

*This document applies to Easy!Appointments v1.4.1.*

[Back](readme.md)
