<?
	session_start();
 	echo '<div name="links" style="font-family:sans-serif;">
		<img src="http://tsidisaster.net/images/logo_simple.jpg">';
		if($_SESSION['LoggedIn'] == 1){
			echo '<a href="account.php">'. $_SESSION['User'] .'</a>&nbsp;&nbsp;<a href="logout.php">Log out</a>';
		}
		echo '<br>
		<a href="http://tsidisaster.com"><img src="http://tsidisaster.net/developer/old/images/square.png" width="30"></a>&nbsp;&nbsp;
		<a href="last.php">Most Recent Recap</a>&nbsp;&nbsp;
		<a href="selectdate.php">Recap Report</a>&nbsp;&nbsp;
		<a href="personnel.php">Personnel Report</a>&nbsp;&nbsp;
		<a href="http://tsidisaster.net/developer/old/2014.html" target="new">Old</a>&nbsp;&nbsp;
		<a href="index.php">Submit</a>&nbsp;&nbsp;
		<a href="eif.php">Equipment Inspection Form</a>&nbsp;&nbsp;
		<a href="equipment_landing.php">Equipment Report</a>
	</div><br>';
?>