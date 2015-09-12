<?
	
	include_once("globals.php");
	
	
	if($DEBUG == true){
		$con = mysqli_connect("192.254.232.54", "swafford_jeremy", "cloud999", "swafford_debug");
	}
	else{
		$con = mysqli_connect("192.254.232.54", "swafford_jeremy", "cloud999", "swafford_recap2");
	}
	
	$eqcon = mysqli_connect("192.254.232.54", "swafford_jeremy", "cloud999", "swafford_inspection");
	
	
	//the following code is for analytic purposes
	// Function to get the client IP address
	    $ipaddress = '';
	    if ($_SERVER['HTTP_CLIENT_IP']){
	        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        }
	    else if($_SERVER['HTTP_X_forWARDED_for']){
	        $ipaddress = $_SERVER['HTTP_X_forWARDED_for'];
        }
	    else if($_SERVER['HTTP_X_forWARDED']){
	        $ipaddress = $_SERVER['HTTP_X_forWARDED'];
        }
	    else if($_SERVER['HTTP_forWARDED_for']){
	        $ipaddress = $_SERVER['HTTP_forWARDED_for'];
        }
	    else if($_SERVER['HTTP_forWARDED']){
	        $ipaddress = $_SERVER['HTTP_forWARDED'];
        }
	    else if($_SERVER['REMOTE_ADDR']){
	        $ipaddress = $_SERVER['REMOTE_ADDR'];
        }
	    else{
	        $ipaddress = 'UNKNOWN';
        }
	
	date_default_timezone_set ("America/New_York");
	$now = date("Y-m-d H:i:s");
	
	$sssql = "INSERT INTO Analytics (
						Page, 
						User, 
						Submitted, 
						IP_Address
					) 
					VALUES (
						'".$_SERVER['REQUEST_URI']."', 
						'".$_COOKIE['name']."', 
						'$now',
						'$ipaddress'
					)";
					
	mysqli_query($con, $sssql);
	
?>