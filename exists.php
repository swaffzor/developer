<html>
	<head>
		
		<link rel='stylesheet' href='mystyle.css'>
	</head>
	<body>
		
		<?
			include_once("database.php");
			include_once("functions.php");
			include_once("nav2.php");
		?>
		<h1>A Recap for this date has already been submitted</h1>
		<?	
			$ssql = "SELECT * FROM Data WHERE Name = '".$empName[0]."' AND Date = '".$date."'";
			$duplicateCheck = mysqli_query($con, $ssql);
			
			while($row = mysqli_fetch_array($duplicateCheck)) {
				echo "Submitted: " . $row['Submitted']. "<br><br>";
				echo $row['Date']."<br><BR>";
				echo $row['Summary'];
			}
			
		?>
	</body>
</html>