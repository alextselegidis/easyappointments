<h1>EasyBlue on 1.3.2</h1>
<p>Thanks Alex for sharing Easy!Appointments with us. I have been using it from 1.0. It just keeps getting better. I am sharing what I have developed for my business. I am a psychotherapist and need to comply with HIPAA privacy laws in the US. So, I have made modifications to help in that direction.</p>
<p>WARNING: I did not develop this process with ease of installation in mind. I just developed it little by little on my site until it worked. I did not really think about how others will install it and how involved it would be. Nevertheless, after trying to explain the process to another Easy!Appointments user, Felipe, I have tried to make some clear instructions. This is my attempt. Every change I have made is documented in the Github history. So if you want to just take parts of my modification away you can. But search through all the mods to be sure that you get all my corrections as well.</p>
<p>If you are using a plain vanilla build of Easy!Appointments, this build will layer on top of that and will run just fine if you do the update sequence. However, if you are using one of my old EasyBlue builds unfortunately you CANNOT just load this on top with an update. Easy!Appointments 1.3.2 incorporates some of what was included in my older builds but using different methods and field names. So you will need to start over again. What I did was quit the sync to google calendar and backed up the fallowing tables:</p>
<ul>
	<li>ea_service_categories</li>
	<li>ea_services</li>
	<li>ea_users</li>
	<li>ea_user_settings</li>
	<li>ea_services_providers</li>
	<li>ea_cellcarrier //I deleted the default table and loaded the table I prefer for my area, you may not want or need to do this one.</li>
</ul>

<p>I loaded the new build and then restored the back ups of the tables. I then reconnected to google calendar and ran the sync to restore all my appointments.</p>
<h2>WHAT IS ADDED IN THIS BUILD</h2>
<ul>
	<li>Theme picker, for red, blue, and green themes</li>
	<li>Wordpress hooks to fully integrate with Wordpress functions and plugins</li>
	<li>Customer information automatically fills in when sitting behind a Wordpress login.</li>
	<li>Loading spinner on the calendar (for slow loading servers like mine)</li>
	<li>Privacy setting on front end, clients can refuse notifications (increase compliance with US HIPAA requirements)</li>
	<li>Option to show minimal details in notifications (increase compliance with US HIPAA requirements)</li>
	<li>Include a setting for maximum days out for sync</li>
	<li>SMS reminders in addition to email triggered by a cronjob</li>
	<li>Added option for appointments to post on the hour or on the quarter. This is a hacky way to allow for padding between appointments. If you select on the quarter, a 10 min appointment will always have 5 min padding before the next appointment. If set on the half, that 10 min appointment will now have a 20 min pad and so on. Not perfect but it works for me. It is best to use one setting for all appointments. So all are on the half or on the quarter rather than mixing up.</li>
	<li>A waiting list option that sends out notices by cronjob regarding any openings in the future.</li>
	<li>Enhanced return to booking routine and landing page. Return to book loads the customer data that they used in the last booking. The landing page presents options to delete the appointment, modify the existing appointment, or make a new appointment. There is also a button to delete all customer data.</li>
	<li>Add information to the confirm appointment panel at step 4</li>
	<li>Added Paypal integration when used with Wordpress and WP-Invoice with the Single Page Checkout addon.</li>
	<li>Added option to hide the provider option from front end. If you are a solo provider this drop down menu is not needed on the front end.</li>
	<li>Modified the google syc routine to reproduce recurring appointments from google calendar so that the appointment and client are reflected back to EA accurately.</li>
	<li>Added a script to automatically sync google calendar with EA (necessary to update any recurring appontments)</li>
	<li>Added an option to limit information within the ICS file.</li>
	<li>Added an announcement script. This is a hacky script that I use to send out announcements that I will be away from the office.</li>
	<li>Added a preview page for the front end of Wordpress. Clients can see availability but cannot book unless they log in to the site.</li>
	<li>Special category of billing for specific clients. This allows for me to have special rates for some clients. For example, all clients with copays of $40 can book appoints at that rate where other clients with copays of $20 will see that as their rate.</li>
</ul>
<p>My build works inside and outside of WordPress. However, this build was designed to sit behind a Wordpress login and some features will only work within Wordpress: the auto filling of customer data, Paypal, and special billing category will only work behind a Wordpress login.</p>
<p>This build was designed to work with Linux Cronjob or similar application. The reminders and waiting list features will only work if you set up Cronjob.</p>
<p>This build was also designed to work with Google calendar. The recurring appointments feature is dependent on Google calendar to work.</p>
<h2>HOW TO LOAD THIS BUILD INTO WORDPRESS</h2>
<p>Within WordPress you will need to follow my instructions to integrate it. It is not designed to work with Alex's WP plug in. One day I will get around to making it a plugin.</p>

<h3>Making it all work in WordPress:</h3>
<p>I install my Easy!Appointments directory within my WordPress directory and install it normally per the normal EA process by navigating to www.yourdomain.com/wordpress/youreasyappointments .</p>

<p>In the Easy!Appointments config.php file I use the WordPress database credentials for the installation. I keep the WP_HEADER_FOOTER variable in the config.php file set to false.</p>
<p>Then when all is installed and running I set the WP_HEADER_FOOTER to true. Now when you navigate to www.yourdomain.com/wordpress/youreasyappointments you will now see easy appointments with your wordpress theme formatting. And all WP functions will run in your EA file.</p>
<h3>Adding a WP login</h3>
<p>To get the auto filling of customer data, Paypal, and special billing category, you need to have a login procedure for your wordpress site and you need to have easy appointments sitting behind the login.</p>

<p>For a patient log in, minimally you will need to install and activate the following:</p>
<ul>
	<li>Postman SMTP (or similar this handles email for log in registration process) https://wordpress.org/plugins/post-smtp/</li>
	<li>Nav Menu Roles https://wordpress.org/plugins/nav-menu-roles/</li>
	<li>Peter's Login Redirect https://wordpress.org/plugins/peters-login-redirect/</li>
	<li>User Role Editor https://wordpress.org/plugins/user-role-editor/</li>
	<li>A plugin to modify the registration routine to include some extra fields. I use Gravity Forms Registration addon. I use Gravity Forms for a lot of what I do on my site. It is a great piece of software with very good tech support. It is worth the price of the pro licence. It has the feature, so I use it. But you can use any registration plugin that will access user_meta fields will do. Some will do it for free. I will only describe what I do with Gravity Forms here. You will have to figure out how to do it with other applications. https://www.gravityforms.com</li>
	<li>It is also nice to use a plugin that can modify the look of the login screen. There are many out there. I use Theme My Login. This is useful but not necessary. https://wordpress.org/plugins/theme-my-login/</li>
	<li>Redirection https://wordpress.org/plugins/redirection/</li>
</ul>
<p>Your WordPress server should be configured with SSL. For this you will need a domain name and a SSL certificate. There are many commercial sources for these -- if you don't already have one you are comfortable with, try namecheap.com and their "RapidSSL" certificates. If you are planning on only allowing patients to schedule on the site this should be enough. But if you plan to exchange patient information beyond this you would likely need your own server to increase security. It is also best to have all protected patient information stored in a different location that can only be reached by a VPN.
</p>
<p>The first plug-in to configure is "User Role Editor". In the administrative area go to Users -> User Role Editor. Click "Add Role" and create a new role with an ID of "patient" (this specific ID is required, all lower case) and a display name of "Patient". Make it a copy of the Subscriber role so that its only capability is "read". This is the role that will be assigned to your patients. Also if patients will self-register for a portal account, be sure to set the primary default role to "patient" or "client".</p>

<p>Then it would be good to review and customize all of your system settings. In the administrative area you'll see that "Settings" is broken down into about 9 sections: General, Writing, Reading, etc. The WP instructions will help you with these, but here are a some special notes:</p>

<p>In Reading, you probably want your front page to display a static page.</p>
<p>In Login/logout redirects, you will want the "patient" role to redirect to a suitable page upon login. Plan to set that up after you have created some initial pages.</p>

<p>Be sure to configure Postman SMTP or whatever you use to manage outgoing mail. This is to make sure that you and your users get any appropriate mail that may be generated.</p>

<p>You probably want most or all of your pages with forms to be available only to logged-in patients. The "Nav Menu Roles" plug-in will make that easy.</p>

<p>In the Redirection plugin, add two new redirects using the advanced options:</p>

<p>Replace the following below with your system information:</p>
<ul>
	<li>wordpress= your wordpress directory</li>
	<li>easyappointments=your Easy!Appointments directory</li>
</ul>

<b>First:</b><br/>
Source URL: /wordpress/easyappointments/?<br/>
Title:<br/>
Match: URL and login status<br/>
When matched: Redirect URL to HTTP code 301-Moved Perminantly<br/>
Logged in:<br/>
Logged out: https://www.yourwebsite.com/wordpress/login/<br/>
Group: Redirections<br/><br/>

<b>Second:</b><br/>
Source URL: /wordpress/easyappointments/<br/>
Title:<br/>
Match: URL and login status<br/>
When matched: Redirect URL to HTTP code 301-Moved Perminantly<br/>
Logged in:<br/>
Logged out: https://www.yourwebsite.com/wordpress/login/<br/>
Group: Redirections<br/>
Adding An Enhanced WP Registration Page and Profile Fields<br/>

<p>For the user profile you need to add the following function to your themes Functions.php file:</p>

		<?php
		//Functions to add user_meta keys for registration to Wordpress
		add_action( 'show_user_profile', 'tml_edit_user_profile' );
		function tml_edit_user_profile( $profileuser ) {
		?>
			<p>
				<label for="first_name">First Name</label>
				<input id="first_name" type="text" name="first_name" value="<?php echo $profileuser->first_name; ?>" />
				
				<label for="last_name">Last Name</label>
				<input id="last_name" type="text" name="last_name" value="<?php echo $profileuser->last_name; ?>" />

				<label for="phone_number">Phone Number</label>
				<input id="phone_number" type="text" name="phone_number" value="<?php echo $profileuser->phone_number; ?>" />

				<?php 
				global $wpdb;
				$cellco_options=$wpdb->get_results("SELECT `id`, `cellco` FROM `ea_cellcarrier` WHERE 1"); 
				$cell_carrier = $profileuser->cell_carrier;
				?>
				
				<label for="cell_carrier">Cell Carrier</label>
				<select id="cell_carrier" type="text" name="cell_carrier"/> 			
					<option value="" > -- select -- </option>
						<?php foreach ($cellco_options as $option):?>
							<option <?php if ($cell_carrier == $option->id) { ?>selected<?php }?> value="<?php echo $option->id; ?>" >
							<?php echo $option->cellco; ?></option>
						<?php endforeach; ?>
				</select>	

				<h4>Email/SMS Notice/Reminder Authorization</h4>
				<label><input type="radio" name="notification" id="notification" class="input" <?php if ($profileuser->notification == 1) { ?>checked="checked"<?php }?> value="1" />
				I want to recieve email/sms notices and appointment reminders</label><br><br>
				<label><input type="radio" name="notification" id="notification" class="input" <?php if ($profileuser->notification == 0) { ?>checked="checked"<?php }?> value="0"/>
				Do not send me sms/email notices and appointment reminders</label><br><br>		
				
			</p>
		<?php
		}

		add_action( 'personal_options_update', 'my_save_custom_user_profile_fields' );
		function my_save_custom_user_profile_fields( $user_id ) {
			if ( !current_user_can( 'edit_user', $user_id ) )
				return FALSE;

				update_user_meta( $user_id, 'first_name', $_POST['first_name'] );
				update_user_meta( $user_id, 'last_name', $_POST['last_name'] );
				update_user_meta( $user_id, 'phone_number', $_POST['phone_number'] );
				update_user_meta( $user_id, 'cell_carrier', $_POST['cell_carrier'] );
				update_user_meta( $user_id, 'notification', $_POST['notification'] );
				
			}
		add_action( 'edit_user_profile', 'tml_edit_user_profile' );
		add_action( 'edit_user_profile_update', 'my_save_custom_user_profile_fields' ); 
		?>

<h2>Make a custom registration page</h2>
<p>You will need to add some fields for the registration sequence. I use Gravity Forms and it comes with a Registration Addon. So I use it. In addition to the user, password, and email fields you need the following:</p>
<ul>
	<li>First Name mapped to first_name</li>
	<li>Last Name mapped to last_name</li>
</ul>

<b>User Meta Fields:</b>
<ul>
	<li>Phone Number mapped to phone_number</li>
	<li>Cell Carrier mapped to cell_carrier //as a drop down menu</li>
</ul>

<p>To autofill the cell_carrier field I add the following function to functions.php</p>

		  add_filter("gform_pre_render_29", populate_dropdown); //29 is the GF Form ID replace it with your own
		  add_filter("gform_admin_pre_render_29", populate_dropdown);

		  function populate_dropdown($form){
			global $wpdb; //Accessing WP Database (non-WP Table) use code below.
			$cellco_options = $wpdb->get_results("SELECT `id`, `cellco` FROM `ea_cellcarrier` WHERE 1");

			$choices = array();
			$choices[] = array("text" => "--Select--", "value" => ""); //adding a array option with no value, this will make the user select and option.

			foreach ($cellco_options as $option) {
			  $choices[] = array("text" => $option->cellco, "value" => $option->id);
			}
						   
			foreach($form["fields"] as &$field){
			  if($field["id"] == 9){//9 is the fielf ID number, replace with your own
				$field["choices"] = $choices;
			  }
			}
			return $form;
		  }

<h2>GETTING PAYPAL TO WORK WITH EASY!APPOINTMENTS</h2>
<p>Install and Activate WP-invoice with Single Page Checkout Addon https://wordpress.org/plugins/wp-invoice/ ; https://www.usabilitydynamics.com/product/wp-invoice-single-page-checkout</p>
To use my PayPal option you need to install and activate the WP-Invoice plugin with the Single Page Checkout addon activated.
<p>You need to get a PayPal IPN URL from your paypal account: https://www.usabilitydynamics.com/product/wp-invoice/docs/paypal-payment-settings-in-wp-invoice-plugin</p>
<p>You need your paypal account activated within WP-Invoice.</p>
<h3>Easy!Appointments Settings Page</h3>
<p>On the settings set:</p>
<ul>
	<li>WP-Invoice Integration (In WP with WP-Invoice Plugin installed only) = Yes</li>
	<li>PayPal Invoice Item suffix = any suffix you want to appear in your paypal invoice (no spaces). I use "-MyBusinessName"</li>
</ul>

<h3>Add Some Wordpress Pages</h3>
<p>You need to make a page in Wordpress titled:</p>
<ul>
	<li>processingpayment</li>
	<li>add this short code to the page: [paypalpay]</li>
</ul>

<p>You need to make another Wordpress page titled "payment-success". After replacing the text "pathtowordpresssite" with the path to your wordpress site, add the following in the text tab for the content (do not past this in the Visual tab):</p>

		  <h1 style="text-align: center;">Your Payment Was Successful</h1>
		  <h2 style="text-align: center;">Thank you...</h2>
		  <p style="text-align: center;">A receipt for transaction has been emailed to you. Complete details of this transaction are found at this link:</p>

		  <div id="fep-menu" style="text-align: center;"><a class="fep-button" title="Invoice Dashboard" href="https://pathtowordpresssite/paymentdashboard/">Payment Dashboard</a></div>
		  &nbsp;
		  <p style="text-align: center;">You may also log into your account at www.paypal.com to view financial details of this transaction.</p>

<p>You need to run a cronjob that will launch the Paypaltimer.php script once every 60 seconds. This deletes all pending appointments with payments not completed within 10 minutes.</p>

<p>Add a Function to Functions.php in Your Wordpress Theme
Within the functions.php file of your theme you need to add the following two functions</p>

		//Shortcode for PayPal Payments with Easy!Appointments. Place this in your Functions.php file.
		//Replace the following with url addresses that corespond to your site: 
		//  https://PATHTOYOUREASY!APPOINTMENTS.COM  (three instances)
		//  https://PATHTOYOURWORDPRESS.COM (one instance)
		//Make a page in wordpress titled "processingpayment" and place in it the shortcode: [paypalpay]
		function paypalpayment() {
			global $wpdb;
			$user = wp_get_current_user();
			$user_ID = $user->ID;
			$shortcode = $wpdb->get_row("SELECT MAX(ap.pending) AS pending, ap.book_datetime, ap.id, ap.hash FROM ea_appointments AS ap "
					."INNER JOIN ea_users AS us ON ap.id_users_customer = us.id "
					."WHERE us.wp_id ='".$user_ID."'");
			$html = '';
			
			if ($shortcode->pending == ''){
				$html .= '<h1>Processing Error: Appointment has been deleted. </h1>';
				$html .= '<p align="center"><a class="fep-button" style="width: 195px; text-align: center;" href="https://PATHTOYOUREASY!APPOINTMENTS.COM"><img height="18" width="18"/> Schedule an appointment</a></p>';
			} else {
				$html .= '<h2>Fee Policy</h2>';
				$html .= '<p>You may reschedule an appointment without charge within 24 hours of your appointment.  Cancelation of an appointment within 24 hours can either result in a refund or a credit for your next appointment per your request. You will need to inform Mr. Tucker on the discussion board about how you would like your cancelation to be handled. If you would like a refund, refunds will be the full amount of the cost of your session minus the PayPal processing fees. There are no refunds for cancelations later than 24 hours in advance.</p>';
				date_default_timezone_set('America/Los_Angeles');
				$refreshtime = strtotime($shortcode->book_datetime) - strtotime("-10 minutes");

				$html .= '<meta http-equiv="refresh" content="';
				$html .=   $refreshtime;
				$html .=  '">';
			if(strtotime($shortcode->book_datetime) > (strtotime("-10 minutes"))){
				$html .= '<style>
					ul.wpi_checkout_block.wpi_checkout_billing_address {
						display: none;
					} 
					ul.wpi_checkout_block.wpi_checkout_customer_information {
						display: none;
					}	
					ul.wpi_checkout_block.wpi_checkout_billing_information {
						display: none;
					}
					.wpi_checkout_submit_btn.btn.btn-success.wpi_checkout_process_payment.wpi_paypal {
						margin: -1px;
					}
					input {
						margin-top: 10px;
						width: 130px;
					}
					form.wpi_checkout .total_price {
						top: 1px;
					}
					.loader {
					  border: 4px solid #f3f3f3;
					  border-radius: 50%;
					  border-top: 4px solid #3498db;
					  width: 30px;
					  height: 30px;
					  -webkit-animation: spin 2s linear infinite; /* Safari */
					  animation: spin 2s linear infinite;
					}

					/* Safari */
					@-webkit-keyframes spin {
					  0% { -webkit-transform: rotate(0deg); }
					  100% { -webkit-transform: rotate(360deg); }
					}

					@keyframes spin {
					  0% { transform: rotate(0deg); }
					  100% { transform: rotate(360deg); }
					} 

					div#spinner {
						margin-left: 160px;
						position: absolute;
					}
					
					input#stepone {
						position: absolute;
						margin-top: -2px;
						padding: 0px;
					}
				</style>';
				
				$html .= '<input type="button" id="stepone" onclick="processpaypal()" value="Process Payment">';
				$html .= '<div id="spinner" class="loader" style="display:none"></div>';
				$html .= do_shortcode($shortcode->pending);
				$html .= '<input type="button" onclick="deletapt()" value="Refuse/Delete">';
				$html .= '<script>
					cancelurl = "https://PATHTOYOUREASY!APPOINTMENTS.COM/index.php/appointments/cancel/' . $shortcode->hash . '";
					function deletapt(){
						window.location = cancelurl;
					}
					$(document).ready(function() {
						$("input[name=return]").val("https://PATHTOYOURWORDPRESS.COM/payment-success/");
						$("input[name=cancel_return]").val(cancelurl);
					});  
					
					function processpaypal(){
						$("#spinner").css("display","block");
						$("#stepone").css("display","none");
						setTimeout(
						function() 
						{
							$(".wpi_checkout_submit_btn").click();
						}, 250);				
					}
				
				</script>';
				}else{
				$html .= '<h4 style="text-align: center;">Time has expired</h4>';
				$html .= '<p align="center"><a class="fep-button" style="width: 195px; text-align: center;" href="[url]/?p=369"><img height="18" width="18" src="https://PATHTOYOUREASY!APPOINTMENTS.COM" /> Schedule an appointment</a></p>';
				}
			}	
			return $html;
		}
		add_shortcode("paypalpay", "paypalpayment");

		//PayPal Callback function that replaces the cryptic suffix with actual service names in patients the WP-Invoice file.
		function ea_paypalcallback($transaction_data){
			global $wpdb;
			//Grab Service Name and Date 
			$session_id = $transaction_data[post_data][created_by][0];
			$posturl = $transaction_data[post_data][guid][0];
			$urlArray = explode('=',$posturl);
			$postid = $urlArray[sizeof($urlArray)-1];
			$dummyname = $transaction_data[items][1][name];
			$query = "SELECT concat(eas.name, '  ', DATE_FORMAT(eaa.start_datetime, '%m/%d/%Y') ) FROM ea_services AS eas  " .
			"LEFT JOIN  ea_appointments AS eaa ON eaa.id_services =  eas.id WHERE eaa.pending LIKE '%" . $session_id . "%'";
			$servicename = $wpdb->get_var($query);	
			$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}posts SET post_content = REPLACE(post_content, 'Items: $dummyname', '$dummyname: $servicename') WHERE ID = $postid"));
			$wpdb->query($wpdb->prepare("UPDATE $wpdb->postmeta SET meta_value = REPLACE(meta_value, '$session_id', '') WHERE meta_key = 'post_title' AND post_id = $postid"));
			$wpdb->query($wpdb->prepare("UPDATE ea_appointments SET pending = '' WHERE pending LIKE '%$session_id%'"));
			
			// the message
			$var = $postid;
		}
    
<h3>Add Items to the Wp-invoice Line Items Tab</h3>
<p>The last step:</p>
<p>In WP-Invoice settings> line items, there need to be items listed there. This is the system that I used:</p>

<p>I went into phpMyAdmin and to the Easy!Appointments table: ea_services and used the service ID number attached to the suffix you assigned in the previous section. This creates each item in the line items table of WP-Invoices such as this:</p>

<table align="left" border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr align="left" valign="top">
		<th>Name & Description</th>
		<th>Qty.</th>
		<th>Price</th>
	</tr>
	<tr align="left" valign="top">
		<td>13Invoice-Service</td>
		<td>1</td>
		<td>140</td>
	</tr>
	<tr align="left" valign="top">
		<td>17Invoice-Service</td>
		<td>1</td>
		<td>252</td>
	</tr>
	<tr align="left" valign="top">
		<td>18Invoice-Service </td>
		<td>1</td>
		<td>110</td>
	</tr>
</table>
<p>And so on . . .</p>

<p>The cryptic 13Invoice-Service is replaced in WP-Invoice with the full information about the service as you entered in Easy!Appointments. So, the client can get more complete information by looking at the invoice securely stored on your site.</p>

<p>This is not the most efficient method and it means that when you change your price for a service you need to enter it into two places. But it works for now. If others want to add code to automatically do this -- feel free to do so and share.</p>

<h1>SETTING UP CRON JOBS</h1>
<p>In order to send out reminders, waiting list notices, clear orphan paypal transactions, and auto sync with Google Calendar, Cron Tab or similar program needs to be set up to run the scripts found in 

    /application/controllers/cli/

<p>To test the scripts by commandline you can use syntax similar to this (different for every server):</p>

	use a space as a separator
	php56 /volume1/web/PathToE!A/index.php cli/[script]

	php56 /volume1/web/PathToE!A/index.php cli/waitinglist
	php56 /volume1/web/PathToE!A/index.php cli/reminders
	php56 /volume1/web/PathToE!A/index.php cli/paypaltimer
	php56 /volume1/web/PathToE!A/index.php google/sync3

<p>From crontab you will use syntax similar to this:</p>

	Use tab as separator.  There must be a blank CR at the end of the file.
	0	22	*	*	*	root	/usr/local/bin/php56	/volume1/web/PathToE!A/index.php cli/waitinglist
	30	7	*	*	*	root	/usr/local/bin/php56	/volume1/web/PathToE!A/index.php cli/reminders
	1	*	*	*	*	root	/usr/local/bin/php56	/volume1/web/PathToE!A/index.php cli/paypaltimer
	30	21	*	*	*	root	/usr/local/bin/php56	/volume1/web/PathToE!A/index.php google/sync3

Remember that different Linux installations handle crontab a little different.  Be sure to look up your system.
    
<p>It is very important that you clean up PayPal payments that were not completed and then open up the appointment back as availble.  To do this you need to set up a cron job to run paypaltimer script every minute.</p>

<p>For sync of recurring appointments with google calendar the google/sync3 routine must run at least once a day.  My server is very slow and it often takes about a minute or more to sync a full calendar of appointments.  So I do not run the sync more than once a day automatically.  I use the manual sync on the back end if I make changes in the Google Calendar (like a recurring appointment) that I need to update into EA.  Then I have an auto sync nightly. That has worked well for me.</p>

<p>You can set the reminders and waiging list notices to go out at any time you think is nice.</p>
