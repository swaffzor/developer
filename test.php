$summary = str_replace($illegals, $replacements, $summary);
	$planning = str_replace($illegals, $replacements, $_POST['planning']);
	$problems = str_replace($illegals, $replacements, $_POST['problems']);
	$discipline = str_replace($illegals, $replacements, $_POST['discipline']);
	$recognition = str_replace($illegals, $replacements, $_POST['recognition']);
	$technialDifficulties = str_replace($illegals, $replacements, $_POST['technicalDifficulties']);
	
	if ($planning != ""){
		$planning = "<hr><h4>Next Day Planning</h4>" . $planning;
	}
	if ($problems != ""){
		$problems = "<hr><h4>Problems</h4>" . $problems;
	}
	if ($discipline != ""){
		$discipline = "<hr><h4>Discipline</h4>" . $discipline;
	}
	if ($recognition != ""){
		$recognition = "<hr><h4>Recognition</h4>" . $recognition;
	}
	
	//! Technical Difficulties
	if($technialDifficulties != ""){
		//input into database
		$sql = "INSERT INTO Tickets (Submitter, Submitted, Status, Message) VALUES ('$empName[0]', '$now', 'New', '$technicalDifficulties')";
		mysqli_query($con, $sql);
		//email me and submitter
		//see if this email function will mess up the recap receipt email function
	}