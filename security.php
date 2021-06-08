<?
	session_start();
	
/*
	echo "<pre>POST ";
	print_r($_GET);
	echo "<br>SESSION ";
	print_r($_SESSION);
	echo "</pre>";
*/
	
	$perms = explode(",", $_SESSION['Permissions']);
	$allowed = 0;
	foreach($perms as $key => $value){
		if($value == $thisPagePermission){
			$allowed = 1;
			break;
		}
	}
	
	if($allowed != 1){
		echo "<h1>You do not have the security clearance to access this page</h1>";
		echo '<meta http-equiv="refresh" content="3;index.php">';
		exit();
	}	
?>