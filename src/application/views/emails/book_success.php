<html>
<head>
    <title>Appointment Book Success</title>
</head>
<body style="font: 13px arial, helvetica, tahoma;">
    <div class="email-container" style="width: 650px;border: 1px solid #eee;">
        <div id="header" style="background-color: #3DD481; border-bottom: 4px solid #1A865F; 
                height: 40px;padding: 10px 15px;">
            <strong id="logo" style="color: white; font-size: 31px; 
                    text-shadow: 1px 1px 1px #8F8888;">$company_name</strong>
        </div>

        <div id="content" style="padding: 10px 15px;">
            <h2>Your appointment has been successfully booked!</h2>
            <p>
                Thank you $customer_name for arranging an appointment with us. 
                Below you can see the appointment details. Click on the edit 
                link to make changes to your appointment.
            </p>    
            
            <h2>Appointment Details</h2>
            <table id="appointment-details">
                <tr>
                    <td class="label" style="padding: 3px;font-weight: bold;">Service</td>
                    <td style="padding: 3px;">$appointment_service</td>
                </tr>
                <tr>
                    <td class="label" style="padding: 3px;font-weight: bold;">Provider</td>
                    <td style="padding: 3px;">$appointment_provider</td>
                </tr>
                <tr>
                    <td class="label" style="padding: 3px;font-weight: bold;">Date</td>
                    <td style="padding: 3px;">$appointment_date</td>
                </tr>
                <tr>
                    <td class="label" style="padding: 3px;font-weight: bold;">Duration</td>
                    <td style="padding: 3px;">$appointment_duration</td>
                </tr>
            </table>

            <h2>Edit Link</h2>
            <p>
                Press the following link to make changes to your appointment reservation. 
                You are able to change the appointment details three hours before 
                the appointment.
            </p>
            <a href="$appointment_link">$appointment_link</a>
        </div>

        <div id="footer" style="padding: 10px; text-align: center; 
                border-top: 1px solid #EEE;background: #FAFAFA;">
            <a href="$company_link">$company_name</a> | Powered by Easy!Appointments        
        </div>
    </div>
</body>
</html>