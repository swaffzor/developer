<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

	<head>
		<title>Recap Report</title>
		
		<style>
			Body{
				Background-color: ;
				Font-family: sans-serif;
				Color: black;}
				
			h1, h2{
				Color: black;}
					
			h3{
				Font-style: italic;}
		</style>
		
		<?php
					
			$date = $_POST['Year'] . "-" . $_POST['Month'] . "-" . $_POST['Day'];
			$con = mysqli_connect("50.87.144.29", "swafford_jeremy", "cloud999", "swafford_recap");
			// Check connection
			if (mysqli_connect_errno()) {
			  echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}
			date_default_timezone_set ("America/New_York");
			$now = date("Y-m-d");
			$result = mysqli_query($con,"SELECT * FROM Hours WHERE Date ='$now'");
			while($row = mysqli_fetch_array($result)) {
				$jobList[] = $row['Job'];
				$hoursList[] = $row['Hours'];
			}
			$result2 = mysqli_query($con,"SELECT * FROM Jobs");
			while($row2 = mysqli_fetch_array($result2)) {
				$job[] = $row2['Number'];
				$jobName[] = $row2['Name'];
			}
			
			$c2 = count($job);
			$c1 = count($jobList);
			for ($i=1; $i<=$c2; $i++){
				for ($j=0; $j<=$c1; $j++) {
					if ($job[$i] == $jobList[$j]) {
						$hours[$i] += $hoursList[$j];
					}
					else{
						$hours[$i] += 0;
					}
				}
			}
			
		?>
		
		<script type='text/javascript' src='https://www.google.com/jsapi'></script>
	    <script type='text/javascript'>
	      google.load('visualization', '1', {packages:['corechart']});
	      google.setOnLoadCallback(drawChart);
	      function drawChart() {
	        var data = google.visualization.arrayToDataTable([
	          	['Job', 'Hours per Day'],
	          	<?php 
				for($i=1; $i<$c2; $i++){
					if ($hours[$i] != ""){
						echo "['" . $jobName[$i] . "', " . $hours[$i] . "],";
					}
				}
				?>
	        ]);
	
	        var options = {
	          title: 'Total Hours by Job <?php echo date("F") . " " . date("d") . " " . date("Y"); ?>',
				backgroundColor: 'white',
				pieSliceText: 'label',
				is3D:'true',
				chartArea:{left:20,top:20,width:'50%',height:'60%'}
				
				//legend.textStyle: {color: <'white'>}
	
				
	        };
	
	        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
	        chart.draw(data, options);
	      }
	
		
	
	    </script>
	</head>
	<body>
		<?php
		// check for empty submissions & nix them
		
			$date = $_POST['Year'] . "-" . $_POST['Month'] . "-" . $_POST['Day'];
		
			$con = mysqli_connect("50.87.144.29", "swafford_jeremy", "cloud999", "swafford_recap");
			// Check connection
			if (mysqli_connect_errno()) {
			  echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}
		?>
			<div id='chart_div' style='width: 800px; height: 400px; display: block;'></div>
	</body>
</html>
