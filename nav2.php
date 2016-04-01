<?	
	session_start();
	echo '<div name="links" style="font-family:sans-serif">
		<img src="http://tsidisaster.net/images/logo_simple.jpg">';
		if($_SESSION['LoggedIn'] == 1){
			echo '<a href="account.php">'. $_SESSION['User'] .'</a>&nbsp;&nbsp;<a href="logout.php">Log out</a>';			
		}
		echo '<br>
		<img src="http://tsidisaster.net/developer/old/images/square.png" width="30">&nbsp;&nbsp;
		<a href="index.php">Submit Recap</a>&nbsp;&nbsp;
		<a href="eif.php">Equipment Inspection Form</a>&nbsp;&nbsp;
		<a href="photo.php">Upload Photo</a>&nbsp;&nbsp;
		<a href="exception.php">Email Exception</a>
	</div><br>';
	
?>