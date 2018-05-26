<?
	require_once 'database.php';
	require_once 'globals.php';
	
	session_start();
	$_SESSION['sender'] = $URL;
	//old version: PHP Version 5.4.45
	echo "this is the test page<Br>
	<table>	";
	
	$results = mysqli_query($con,"SELECT * FROM Jobs order by Number");
	while($row = mysqli_fetch_array($results)) {
		echo "<tr><td>" . $row['Name'] . "</td><td>" . $row['Number'] . "</td></tr>";
	}
	
	echo "</table>";
?>