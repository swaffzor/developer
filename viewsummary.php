<?
	
	session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

	<head>
		<title>Last Recap Report</title>
		<link rel="apple-touch-icon-precomposed" href="http://tsidisaster.net/report-touch-icon-114.png">
		<style>
			Body{
				Background-color: ;
				Font-family: sans-serif;
				Color: black;
				padding: 5px 50px 10px 50px;
			}
				
			h1, h2{
				Color: black;
			}
					
			h3{
				Font-style: italic;
			}
				
			#pics{
			    width: 50%;
			    height: auto;
			}
		</style>
	</head>
	<body>

		<?php
			include("functions.php");
			date_default_timezone_set ("America/New_York");
			
			include("database.php");
			include("nav.php");

			
			$sql = "SELECT * FROM Data WHERE Date = '".$_GET['Date']."'";
			
			if($_GET['Name'] != ""){
				$sql .= "AND Name = '".$_GET['Name']."'";
			}
			
			$day = strftime("%A",strtotime($_GET['Date']));
			$now = date("F j, Y @ g:i a");
						
			echo "<b>Recent Submissions not on this page</b><br>";
			echo HexToStr($_SESSION['linkblock']);
			
			echo "Report Generated on " . $now . "<br><h1>Recap for " .$day ." " . $_GET['Date'] . "</h1>";
			$result = mysqli_query($con, $sql);
			while($row = mysqli_fetch_array($result)) {
				echo "<h3>" . $row['Name'] . "</h3> <i>Submitted: " . $row['Submitted'] . "</i><br>" . $row['Summary'];
			}
			
			
		?>
	</body>
	
</html>