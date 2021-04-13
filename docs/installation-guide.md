# Installation Guide

> This page will guide you through the installation and configuration of Easy!Appointments.

### Introduction
Easy!Appointments is a web appointment scheduler that can be installed and run in web servers. Users will be able to reach the application through their web browsers by using an active internet connection, just like visiting a normal website. The installation process is very similar to other popular web systems like WordPress and Joomla, so it is very likely that you will be familiar with the next steps. Follow this article strictly in order to complete the installation with no problems. After that, read the "Configuration" section for adjusting the system to fit your needs.

### Installation
There are 6 steps you must follow during the installation process.

  1. **Make sure that your server has at least the following applications/tools installed: Apache(v2.4), PHP(v7.3) and MySQL(v5.7).** Easy!Appointments needs these programs to run. Most of the web hosting companies provide these tools within their Linux hosting plans. If you want to install Easy!Appointments on your local server use one of the pre-made bundles available on the web (XAMPP, MAMP, WAMP ...), all of which are free to use. If you plan to use the Google Calendar synchronization you will need the **php_curl** extension installed and enabled as well.
  2. **Create a new database (or use an existing one).** The database is necessary for storing the system data. Therefore your hosting plan must include at least one MySQL database. You must also get the database administration credentials because they will be needed later on.
  3. **Upload the Easy!Appointments source files to your server.** You can place the files into a directory with named "easyappointments" or "appointments" or "book" etc. Make sure that you mark the Easy!Appointments folder URL because it will be needed in the following step. For example if the system files are placed in the this directory ".../httpdocs/easyappointments/" then the URL to this folder will be "http://your-domain.com/easyappointments". This URL will be needed in the following steps.
  4. **Ensure that the "storage" directory is writable.** Session information, logs and any other kind of files will land into the "storage" directory so make sure that it has the correct permissions and that is writable. 
  5. **Edit the "config.php" file and set your server properties.** Like other web systems Easy!Appointments needs to know how to connect to the database and the base URL of the installation. You can also provide the Google Calendar API keys in this file, if you want to use the Google Calendar Sync feature. NOTE that you will need to create an API key before that in the Google Cloud Console.
  6. **Open a web browser and head to the Easy!Appointments installation folder URL.** The first time you open this page an installation guide will be shown. Fill in the administrator user and company settings and press the "Install" button. That's it! You can now use the application at your own will.


### Configuration
When you finish the installation, Easy!Appointments will only contain an administrator user, a test service, a test provider and some default settings. You will need configure the system with your own business logic, but before that you can try adding a test appointment before proceeding with the following steps.

  1. Head to backend section by entering the Easy!Appointments URL `http://installation-url/index.php/backend` in your browser. When logged in, click on the "Settings" menu item on top of the page. Click on the "Business Logic" tab and apply the working plan of your company. Press the "Save" button when you are finished. This will be the default working plan for all new providers (not the existing ones).
  2. Next you will need to add the services that your customers will book appointments for. You can optionally organize these services by using custom categories. Add some records and make sure you fill the required fields.
  3. Once you are ready with the services you will need to add the service providers of your company. By doing so, customers will be able to select the employee they want to provide their preferred service, during the booking procedure. Hit the "Users" menu item and then the "Providers" tab. Add a new provider user and set his working plan and services. If you wish to assign the appointments without using Easy!Appointments just create a single general provider user named with your company name. It is not necessary for the provider to know the login credentials, but if he does, he will be able to view his appointment plan and sync it with his Google Calendar account.
  4. If you have a secretary handling all the appointments for your company, hit the "Secretaries" tab and add a new user. Select the providers that she can manage and save the record. The secretary user will be able to manage the appointments only for the providers she is responsible.
  5. That's it! Click on the "Go To Booking Page" link on the footer of the page and create a new appointment. The new appointment will be now be visible at the "Calendar" page of the backend section. The customer's data have also been saved and can be found in the "Customers" page. 

Finally just add a link in your website that points to your Easy!Appointments installation with a caption similar to "Book Appointment". Whenever a customer clicks on that link he will be redirected the booking page.

Happy Bookin'!

*This document applies to Easy!Appointments v1.4.1.*

[Back](readme.md)
