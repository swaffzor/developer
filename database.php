<?
	include_once("globals.php");
	if($DEBUG == true){
		$con = mysqli_connect("192.254.232.54", "swafford_jeremy", "cloud999", "swafford_debug");
	}
	else{
		$con = mysqli_connect("192.254.232.54", "swafford_jeremy", "cloud999", "swafford_recap2");
	}
	
	$eqcon = mysqli_connect("192.254.232.54", "swafford_jeremy", "cloud999", "swafford_inspection");
?>