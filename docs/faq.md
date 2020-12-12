# FAQ 

## How do I check that my server has Apache, Php and MySQL already installed?

Easy!Appointments is a php application and needs an apache server with php and mysql installed. Apart from that, the php "curl" extension and the apache module "mod_rewrite" need to be enabled. To check if your server fullfils the needed prerequisites you will need to either contact the web hosting company or create a php file on your web root directory with the content <?php phpinfo(); ?> and then access it from the web (eg "phpinfo.php" >> "http://domain-name.com/phpinfo.php"). This url will display all the server details.


## How do I create a Google Calendar API key?

Google needs to authorize the usage of her services, so you need to create an API key for your Easy!Appointments installation. In order to do that you will need a google account. When logged in, go to the Google Developers Console and create a new project. Enable the Calendar API service and then head to the "APIs & Auth >> Credentials" menu item. There, you will need to create a new OAuth client id. The last step is to enter a valid redirect url for the authrorization process. This is very important because if the redirect url is wrong, you will not be able to use the google synchronization feature on your E!A installation. For your redirect url enter the following value: "http://domain-name/folder-to-ea-installation/google/oauth_callback" (replace the domain name and the path to the Easy!Appointments installation folder with your server values). For example if E!A is installed on the "ea" folder on the web root directory the valid redirect url would be "http://my-domain/ea/google/callback". 


## Installation Page Is Not Working

Various users have reported on the support group that they cannot complete the installation successfully. This is primarily because of two reasons (a) either the configuration.php file was set incorrectly, or the server settings won't allow AJAX calls to be completed successfully and thus let the installation finish. For the first situation you will have to check that you have properly set the $base_url parameter to "https://url/to/easyappointments/folder/" (last slash is necessary!), for example "http://easyappointments.org/ea-installation/". Then ensure that your database connection credentials is correct. In the second situation the things are more complex. Users have stated that in some web hosts you need to change the .htaccess file in order for the application to work (htacces snippet provided below). If this doesn't work then check the apache error log and open the browser javascript console after you press the "Install E!A" button. There you will find information about what is going wrong with your installation. Contact your hosting company and ask them to resolve these issues.

.htaccess

```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?/$1 [L]
```

##Booking Wizard Won't Display Any Hours

This issue comes when the customer is on the appointment book wizard but he is not seeing any available appointment hours and he cannot continue either. If your installation has this problem check the apache error log and open the browser's javascript console to find any issues. This is definetely a server issue and can only fixed by contacting your hosting company. It has to do with your server settings not letting Easy!Appointments run correctly. Normally you or the web hosting company will be able to find what needs to be changed in order to solve the issue.


Booking Wizard Displays "There are no available appointment hours for the selected date. Please choose another date." 
 ... Even though there are available time periods in my plan.

This is not actually an issue but it happened to a user and it was indeed confusing to resolve. A clean installation of E!A has some default settings such as the default working plan that contains some breaks too during the working days. This was set this way in order for you to see how can a working plan be like and set. So if you add a service that lasts for several hours (eg. 3~4 hours) then because of the default breaks new appointments will not fit in any empty time period of your calendar and thus the customer will not be able to book appointments with you. So if your services last for long you will need to change the working plan of your providers too so new appointments will fit into their schedule.


##Installing E!A on Subdomain Won't Load Appointment Hours

If you want to install Easy!Appointments on a subdomain you will have to use the subdomain URL in your "configuration.php" file and not the initial URL directory. For example if you have the subdomain "http://book.mysite.com" where E!A resides and this subdomain is mapping on "http://mysite.com/book", you will have to set $base_url = 'http://book.mysite.com' in your "configuration.php" file, otherwise you will get a No 'Access-Control-Allow-Origin' error and you won't get any available appointment hours on frontend.


##Change the gap of the available hours of the booking wizard to 60 minutes (or similar). 

The following link points to a common question that many users ask. The default gap between the available appointment hours is 15 minutes. In the following thread there is a file attached which will change this gap to 60 minutes. 

(https://groups.google.com/d/msg/easy-appointments/Mdt98fbF8hE/9CEjOvW7FAAJ)

## DateTime::__construct(): It is not safe to rely on the system's timezone settings...

You get this warning because PHP is not configured with a timezone setting. This is a very important setting especially for Easy!Appointments, cause otherwise you might get into trouble with the appointment hours. After
 installing the application, make sure that the php.ini "date.timezone" setting has the correct value depending 
 your location. You can find a list of the [available timezone setting on php.net](http://www.google.com/url?q=http%3A%2F%2Fphp.net%2Fmanual%2Fen%2Ftimezones.php&sa=D&sntz=1&usg=AFQjCNFtFw3O6UQXAKFKWCXqhSd9Z0UwgQ). If you cannot modify your php.ini file try to add the following command at the top of `index.php`. 
 
 `date_default_timezone_set('America/Los_Angeles'); // Use your own timezone string.`


*This document applies to Easy!Appointments v1.4.1.*

[Back](readme.md)
