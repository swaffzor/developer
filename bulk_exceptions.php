<?
	date_default_timezone_set ("America/New_York");
	
	require_once 'database.php';
	require_once 'functions.php';
	
	$now = date("F j, Y @ g:i a");
	
	echo "<form action='bulk_exceptions.php' name='inspectionForm' method='post'>";
	echo "<h1>Bulk Exceptions for all employees on this date</h1>";
	echo "<input type='text' value='". $_POST['date']. "' name='date' placeholder='yyyy-mm-dd'><br>";
	echo "<input type='submit'><br><br>";
	
	echo $_POST['date'] . "<BR>";
	
	$temp = mysqli_query($con, "SELECT * FROM employees where recap != '' ORDER BY Name");
	while($row = mysqli_fetch_array($temp)) {
		$empNames[] = $row['Name'];
	}
	
	if(isset($_POST['date'])){
		foreach($empNames as $key => $value){
			if(mysqli_query($con, "INSERT INTO exception (Date, Name, Submitted) VALUES ('".$_POST['date']."', '$value', '$now')")){
				echo $value . " entered successfully<BR>";
			}
			else{
				echo $value . " ERROR<BR>";
			}
		}
	}
?>