<?php
	
	$employee = array(
		"4074638518@txt.att.net" => "Jeremy",
		"4079139745@txt.att.net" => "Delmar Rager",
		"4073615167@txt.att.net" => "Jon Fischer",
		"4073615133@txt.att.net" => "Leigh Elliott",
		"9413746299@txt.att.net" => "John Cullen",
		"4073611523@txt.att.net" => "Brandon Minder",
		"4077090203@txt.att.net" => "Stephen Lee",
		"9414568387@txt.att.net" => "Leigh Elliott",
		"8102881011@messaging.sprintpcs.com" => "Dane"
	);

	foreach($employee as $number=>$name){
		echo "$name";
		if (mail($number, "", "This is a friendly reminder to perform equipment inspections today http://tsidisaster.net/eif.php", "From:robot@tsidisaster.net")){
			echo " was successfully sent a message!";
		}
		else{
			echo " was NOT sent a message";
		}
		echo "\n";
	}
	
	
?>