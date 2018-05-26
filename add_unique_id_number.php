<?
	include("database.php");	
	$i = 1;
	
	$query = mysqli_query($con, "SELECT * FROM Hours WHERE job='666'");
	while($row = mysqli_fetch_array($query)) {
		$test = $row['Name'];
		$newQuery = "UPDATE Hours SET
			Submitted='".$row['Submitted']."',
			Date='".$row['Date']."',
			Name='".$row['Name']."',
			Job='".$row['Job']."',
			Hours='".$row['Hours']."',
			WeeklyHours='".$row['WeeklyHours']."',
			Submitter='".$row['Submitter']."',
			OT='".$row['OT']."',
			id=[$i] 
			WHERE 
			Submitted='".$row['Submitted']."',
			Date='".$row['Date']."',
			Name='".$row['Name']."',
			Job='".$row['Job']."',
			Hours='".$row['Hours']."',
			WeeklyHours='".$row['WeeklyHours']."',
			Submitter='".$row['Submitter']."',
			OT='".$row['OT']."'";
			
			echo "$newQuery<BR><BR>";
			mysqli_query($con, $newQuery);
			$i++;
	}
?>