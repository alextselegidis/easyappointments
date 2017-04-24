<!DOCTYPE html>

<html>

	<head>
		<title>ChartJS - BarGraph</title>
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		 	<!-- javascript -->
	
		<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js "></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.bundle.min.js"></script>
		<!-- javascript -->	
		<script type="text/javascript" src="js/app.js"></script>
			
		
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		
		<style type="text/css">
			#chart-container {
				width: 640px;
				height: 320px;
				margin: auto;
			}
			
			.dropdown{
				margin-top: 10px;
				margin-bottom: 10px;
				margin-right: 10px;
				margin-left: 10px;
			}
			
			body {
				background-color: rgb(202,237,243);
			}
			
			#centreblock{
				width: 760px;
				height: 675px;
				margin: auto;
				margin-top: 30px;
				border-style: solid;
				border-width: 1px;
				border-color: rgb(200,219,221);
				background-color: white;
			}
			
			#topblock{
				width: 758px;
				height: 80px;
				background-color: rgb(60,212,129);
	
				
				
			}
			
			#midblock{
				width: 758px;
				height: 5px;
				background-color: rgb(162, 233,193);
			}
			
			p{
			margin-top: 20px;
			text-align: center;
			color: rgb(123,123,125);
			font-size: 250%;
			}
				</style>
				
		
	</head>
	<body>
	
	         <div id="centreblock">
			 <div id="topblock">
			 
			  </div>
			  <div id="midblock">
			  </div>
			  <p id="bargraphtitle"> Select Day of Week</p>
            <div class="dropdown">
    <button class="btn btn-primary dropdown-toggle"  type="button" data-toggle="dropdown">Choose a day
    <span class="caret"></span></button>
    <ul class="dropdown-menu">
      <li> <a id="Monday" >Monday</a></li>
      <li><a id="Tuesday" >Tuesday</a></li> 
      <li><a id="Wednesday" >Wednesday</a></li>
	  <li><a id="Thursday">Thursday</a></li>
      <li><a id="Friday" >Friday</a></li>
    </ul>
  </div>

 <div id="chart-container">
			<canvas id="mycanvas"></canvas>
		</div>
</div>
  <?php

 ?>
  <script>


   
   var  bargraph = null; 

$(document).ready(function(){
   
		// Edited
	var location = window.location.href;
	var day = parseInt(location.match("day=([0-9]+)")[0].split("=")[1]);
	var month = parseInt(location.match("month=([0-9]+)")[0].split("=")[1]);
	var year = parseInt(location.match("year=([0-9]+)")[0].split("=")[1]);
	
	var useDay = new Date(year, month - 1, day).getDay(); // 
	// 1 = Monday, 2 = Tuesday, 3 = Wednesday, 4 = Thursday, 5 = Friday
	
	var startDate = new Date(year, month - 1, day - (useDay - 1));
	// todayDay = 4 (thursday), so startDate = day - 3
	
	var endDate = new Date(year, month - 1, day + (5 - useDay));
	$("#bargraphtitle").text("Select Day of Week (" + startDate.getDate() + "/" + (startDate.getMonth() + 1) + "/" + (startDate.getFullYear()) + "  -  "  + endDate.getDate() + "/" + (endDate.getMonth() + 1) + "/" + (endDate.getFullYear()) + ")");
	//Sets the date its between in the title

	 
	function displayGraph(day){
		
		// gets appointments on specific day chosen from url parameters
		$.ajax({
			url: "http://localhost/easyappointments/data2.php",
			data: {todayDay: day, startD: startDate.getDate(), startM: startDate.getMonth(), startY: startDate.getFullYear()},
			method: "POST",
			success: function(data) {
				var appointmentsOnDay = JSON.parse(data);
				
				// get appointments on a day
				
				// array of appointments within a particular hour (8am - 6pm)
				var appointmentCountsPerHour = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
				
				// Gets all times and puts them into an array
				for(var i = 0; i < appointmentsOnDay.length; i++){
					appointmentCountsPerHour[new Date(appointmentsOnDay[i][0]).getHours() - 9] += 25; // changed from 8
					 // maximum of 4 appointments per hour, so add 25% for every appointment within an hour
					 // take 8 away from the hours to fit them into array (8am is position 0 in appointmentCountsPerHour, so take away 8 from 8am to leave position 0).
				}
				
				
				
				// get all appointments
				
				$.ajax({
					url: "http://localhost/easyappointments/data3.php",
					method: "POST",
					success: function(allData) {
						var allAppointments = JSON.parse(allData);
						
						// Find averages of all appointments on a particular day of the week
						
						var sameDayOfWeekAppointments = [ 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]; // keeps track of total appointments on a particular day of the week at a specific hour (9am - 6pm)
						var differentDates = [ 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]; // keeps track of how many different dates have appointments at a particular hour
						
						var previousDates = []; // keeps track of unique dates and hours 
						
						
						var tempDate, find, found;
						for(var i = 0; i < allAppointments.length; i++){ // iterate through ever appointment
							tempDate = new Date(allAppointments[i]);
							if (tempDate.getDay() == day + 1){ // if the day of the week of the appointment is the same as the day of the week selected in the drop-down box
								sameDayOfWeekAppointments[tempDate.getHours() - 9]++; // the total number of appointments that take place at a specific time is increased by 1   changed from 8
								
								// checks if the date of the appointment is a date that hasn't been found yet or not
								found = false;
								find = tempDate.getDate() + "/" + tempDate.getMonth() + "/" + tempDate.getYear() + "/" + tempDate.getHours();
								for(var d = 0; d < previousDates.length; d++){
									if(previousDates[d] == find){ // the date has been found in the list, and is not a new date
										found = true;
										break;
									}
								}
								
								// if the date of the appointment is completely new, then the number of different dates with the same appointment time is increased by 1
								if(!found){
									previousDates.push(find);
									differentDates[tempDate.getHours() - 9]++; // changed from 8
								}
							}
						}
						
						/*
							example (2pm on a friday)
						
							2 appointments at 2pm friday 21st april
							1 appointment at 2pm friday 24th march
							1 appointment at 8am friday 31st march
						
							total number of appointments on a friday: 3
							total number of seperate dates that have an appointment on a friday: 2
							8am appointment is ignored for 2pm section of the line graph
							
							Average appointments on any day = Total number of appointments on a particular day of the week / total number of seperate dates that have appointments
							Average appointments on a friday at 2pm = 3 / 2 = 1.5 appointments
							Percentage = 1.5 * 25 = 37.5%
						*/
						
						// If there are 2 or more seperate dates&hours with appointments, the average number of appointments at a specific hour has to be calculated
						for(var i = 0; i < sameDayOfWeekAppointments.length; i++){
							console.log(sameDayOfWeekAppointments[i] + ", " + differentDates[i]);
							if (differentDates[i] > 1){
								sameDayOfWeekAppointments[i] /= differentDates[i];
							}
							sameDayOfWeekAppointments[i] *= 25;
						}
						
						// line graph
						chartdata = {
							labels: ['9am', '10am', '11am', '12pm', '1pm', '2pm', '3pm', '4pm', '5pm', '6pm'],
							datasets : [
								{
									label: "Average Patients",
									type:'line',
									data: sameDayOfWeekAppointments,
									fill: false,
									borderColor: '#EC932F',
									backgroundColor: '#EC932F',
									pointBorderColor: '#EC932F',
									pointBackgroundColor: '#EC932F',
									pointHoverBackgroundColor: '#EC932F',
									pointHoverBorderColor: '#EC932F'
									
								},
								
								{
									label: 'Amount of Patients',
									backgroundColor: 'rgb(142, 142, 255)',
									borderColor: 'rgba(200, 200, 200, 0.75)',
									hoverBackgroundColor: 'rgb(40, 40, 255)',
									hoverBorderColor: 'rgba(200, 200, 200, 1)',
									data: appointmentCountsPerHour,
								}
							]				
						};
								
								
						$('#mycanvas').remove(); 
						$('iframe.chartjs-hidden-iframe').remove(); 
						$('#chart-container').append('<canvas id="mycanvas"><canvas>');
						var ctx = document.getElementById("mycanvas");
						// bar graph
						bargraph = new Chart(ctx, {
						
							type: 'bar',
							data: chartdata,
							options: {
								responsive:true,
								maintainAspectRatio: false,
								scales: {
									xAxes: [
										{ 
											time: {
												unit: 'hour'
											},
													
											barThickness : 51 
										}
									],
									yAxes: [{
										ticks: {
											min:0,
											max:100,
											callback: function(value){
											return value + "%"}
										}
									}]
								}
							},
						});	
					},
					error: function(data) {
						console.log(data);
					}
				});
				
			},
			error: function(data) {
				console.log(data);
			}
		});
		
		
		
	}

	 
	 
		$( "#Monday" ).click(function() {
			displayGraph(0);
		});
	 
		$( "#Tuesday" ).click(function() {
			displayGraph(1);
		});
		
		$( "#Wednesday" ).click(function() {
			displayGraph(2);
		});
		
		$( "#Thursday" ).click(function() {
			displayGraph(3);
		});
		
		$( "#Friday" ).click(function() {
			displayGraph(4);
		});
		
	displayGraph(useDay - 1);

});
  
  </script>
  						 
	</body>
	<foot>
		

	</foot>
</html>



