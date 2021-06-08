<?
	
	//all functions used for recap system
	
	
	//function getWeeklyHours
	//date created: 7 July 2014
	//purpose: to get the weekly hours of an employee
	//input parameters: name, date
	//output: current weekly hours
	function getWeeklyHours($fname, $fdate){
		include("globals.php");
		include("database.php");
		$now = date("F j, Y @ g:i a");
		$ddate = $fdate;
		$running = true;
		do {
			$qresult = mysqli_query($con, "SELECT * FROM Hours WHERE Date = '$ddate' AND Name = '$fname' ORDER BY WeeklyHours ASC");
			while($row = mysqli_fetch_array($qresult)) {
				$fwkh = $row['WeeklyHours'];
				$running = false;
			}
			$ddate = date("Y-m-d", strtotime("-1 days", strtotime($ddate)));
			if (strftime("%A",strtotime($ddate)) == "Sunday"){
				$running = false;
				$qresult = mysqli_query($con, "SELECT * FROM Hours WHERE Date = '$ddate' AND Name = '$fname' ORDER BY WeeklyHours ASC");
				while($row = mysqli_fetch_array($qresult)) {
					$fwkh = $row['WeeklyHours'];
				}
			}
		} while($running);
		if ($fwkh){
			return $fwkh;
		}
		else{
			return 0;
		}
	}
	
	//function setHours
	//date created: 7 July 2014
	//purpose: to set the daily hours and weekly hours for an employee
	//input parameters: name, date
	//output: none (inputs data into hours database)
	function setHours($fname, $fdate, $fjob, $fhours, $fsuper, $unit){
		include("globals.php");
		include("database.php");
		$now = date("F j, Y @ g:i a");
		$ffdate = $fdate;
		for($i=0;$i<7;$i++){
			if (strftime("%A",strtotime($fdate)) == "Sunday") {
				$sunday = $fdate;	//$sunday is a date yyyy-mm-dd
				//if fdate is a sunday
				if ($i == 0){
					if ($fname != "" && $fname != "---Select Employee---"){
						$esql = "INSERT INTO Hours (Submitted, Date, Name, Job, Hours, Submitter, WeeklyHours, Unit) VALUES ('$now', '$ffdate', '$fname', '$fjob', '$fhours', '$fsuper', '$fhours', '$unit')";
						mysqli_query($con, $esql);
						mysqli_query($con, "UPDATE employees SET daysMissing='0' WHERE Name = '$fname'");
					}
				}
				else{
					//$start = "Not Sunday";
					if ($fname != "" && $fname != "---Select Employee---"){
						//loop back until sunday
						$phours = $fhours + getWeeklyHours($fname, $ffdate);
						if (strftime("%A",strtotime($fdate)) == "Sunday") {
							//insert into database here
							$esql = "INSERT INTO Hours (Submitted, Date, Name, Job, Hours, Submitter, WeeklyHours, Unit) VALUES ('$now', '$ffdate', '$fname', '$fjob', '$fhours', '$fsuper', '$phours', '$unit')";
							mysqli_query($con, $esql);
							mysqli_query($con, "UPDATE employees SET daysMissing='0' WHERE Name = '$fname'");
						}
					}
				}
			}
			$fdate = date("Y-m-d", strtotime("-1 days", strtotime($fdate))); //subtract a day from the date and repeat
		}
		
	}
	
	
	function updateHours($fname, $fdate, $fjob, $fhours, $fsuper){
		include("database.php");
		$now = date("F j, Y @ g:i a");
		$ffdate = $fdate;
		for($i=0;$i<7;$i++){
			if (strftime("%A",strtotime($fdate)) == "Sunday") {
				$sunday = $fdate;	//$sunday is a date yyyy-mm-dd
				//if fdate is a sunday
				if ($i == 0){
					if ($fname != "" && $fname != "---Select Employee---"){
						$esql = "UPDATE Hours SET Submitted='$now', Name='$fname', Job='$fjob', Hours='$fhours', Submitter='$fsuper', WeeklyHours='$fhours' WHERE ...";
						mysqli_query($con, $esql);
					}
				}
				else{
					//$start = "Not Sunday";
					if ($fname != "" && $fname != "---Select Employee---"){
						//loop back until sunday
						$phours = $fhours + getWeeklyHours($fname, $ffdate);
						if (strftime("%A",strtotime($fdate)) == "Sunday") {
							//insert into database here
							$esql = "INSERT INTO Hours (Submitted, Date, Name, Job, Hours, Submitter, WeeklyHours) VALUES ('$now', '$ffdate', '$fname', '$fjob', '$fhours', '$fsuper', '$phours')";
							mysqli_query($con, $esql);
						}
					}
				}
			}
			$fdate = date("Y-m-d", strtotime("-1 days", strtotime($fdate))); //subtract a day from the date and repeat
		}
		
	}
	
	
	//test whether the two dates are in the same week
	//date2 is the later date
	function weekTest($date, $date2){
		//determine which day was Sunday
		for($i=0;$i<7;$i++){
			if (strftime("%A",strtotime($date)) == "Sunday") {
				$sunday = $date;	//$sunday is a date yyyy-mm-dd
			}
			$date = date("Y-m-d", strtotime("-1 days", strtotime($date))); //subtract a day from the date and repeat
		}
		$count = 0;
		$stop = false;
		//determine if date2 was during the same week as date
		while($stop == false){
			if($date2 == $sunday && $count <= 6){
				$stop = true;
				return true;	//returns true since less than 7 days between dates
			}
			elseif($count > 6){
				$stop = true;
			}
			$count++;
			$date2 = date("Y-m-d", strtotime("-1 days", strtotime($date2)));
		}
		return false;
	}
	
	//finds and returns the date of sunday of that week
	function getSundayDate($date){
		//determine which day was Sunday
		for($i=0;$i<7;$i++){
			if (strftime("%A",strtotime($date)) == "Sunday") {
				return $date;	//$sunday is a date yyyy-mm-dd
			}
			$date = date("Y-m-d", strtotime("-1 days", strtotime($date))); //subtract a day from the date and repeat
		}
	}
	
	//determines if a value is within 24 hours of now
	function SubmittedRecently($strDate){
		  
		$submitted_time = trim(substr($strDate, strpos($strDate, "@") + 1)); 
		$submitted_time = date("G:i", strtotime($submitted_time));
		
		$submitted_date = trim(substr($strDate, 0, strpos($strDate, "@")));
		$submitted_date = date("Y-m-d", strtotime($submitted_date));
		
		$combined_date = date("Y-m-d G:i", strtotime("$submitted_date $submitted_time"));
		
		$nowt = date("Y-m-d G:i");
		
		$diff = strtotime($nowt) - strtotime($combined_date);
		$diff = round($diff/3600, 1);
		
		if($diff <= 24){
			return true;
		}
		else{
			return false;
		}
	}
	
	function StrToHex($string){
	    $hex='';
	    for ($i=0; $i < strlen($string); $i++)
	    {
	        $hex .= dechex(ord($string[$i]));
	    }
	    return $hex;
	}
	
	function HexToStr($hex){
	    $string='';
	    for ($i=0; $i < strlen($hex)-1; $i+=2)
	    {
	        $string .= chr(hexdec($hex[$i].$hex[$i+1]));
	    }
	    return $string;
	}
	
	//function GetHoursForDate
	//date created: 3 August 2016
	//purpose: to get the total hours of an employee for a specific date
	//input parameters: sql connection, name, date
	//output: total hours for a date
	function GetHoursForDate($con, $name, $date){
		$hours = 0;
		$queryresults = mysqli_query($con, "SELECT Hours FROM Hours WHERE Name = '$name' AND Date = '$date' ");
		while($row = mysqli_fetch_array($queryresults)) {
			$hours += $row['Hours'];
		}
		return $hours;
	}
	
	
	//function GetIDForDate
	//date created: 8 August 2016
	//purpose: to get the SQL ID of an employee for a specific date
	//input parameters: sql connection, name, date
	//output: the ID of the table in the Data table
	function GetIDForDate($con, $name, $date){
		$now = time();
		if(strtotime($date) < $now){
			$queryresults = mysqli_query($con, "SELECT id FROM Data WHERE Name = '$name' AND Date = '$date' ");
			while($row = mysqli_fetch_array($queryresults)) {
				$theID = $row['id'];
			}
		}
		return $theID;
	}
	
	
	//function GetIDForDate
	//date created: 8 August 2016
	//purpose: to get the SQL ID of an employee for a specific date
	//input parameters: sql connection, name, date
	//output: the ID of the table in the Data table
	/*
	permission 	|	description	| pages 
	============================================================================
		NULL	|	(default)	| index.php, eif.php, photo.php, exception.php 
		1		|	viewer		| default + last.php, selectdate.php, old/2014.html
		2		|	personnel	| default + personnel.php
		3		|	edit emps	| employee.php
	*/
	
	function GetNavLinks($con, $userID){
		
		$queryresults = mysqli_query($con, "SELECT Permissions FROM users WHERE UserID = '$userID' ");
		while($row = mysqli_fetch_array($queryresults)) {
			$permissions = $row['Permissions'];
		}
		$permArray = explode(",", $permissions);
// 		$navBar = '<img src="http://tsidisaster.net/developer/old/images/square.png" width="30">&nbsp;&nbsp;';
		$queryresults = mysqli_query($con, "SELECT * FROM Permissions ORDER BY AuthLevel, ID");
		while($row = mysqli_fetch_array($queryresults)) {
			if($row['AuthLevel'] == null && $userID == ""){
				$navBar .= "<a class='button' href='".$row['Page']."' >".$row['Link']."</a>&nbsp;&nbsp;";
			}
			else{
				for($i = 0; $i<count($permArray); $i++){
					if($permArray[$i] == $row['AuthLevel'] || $permArray[$i] == "all"){
						$navBar .= "<a class='button' href='".$row['Page']."' >".$row['Link']."</a>&nbsp;&nbsp;";
					}
				}
			}
		}
		
		return $navBar;
		
	}
	
	
	function ParseSummary($name, $summary){
		$text = "";
		$info[] = null;
		//check first character, if '<', then old style
		if(substr($summary, 0, 1) == "<"){
			//modern style with fieldsets
			echo "<h1>yup</h1>";
			//get job number
			$text = substr($summary, 18);
			$info["job"] = substr($text, 0, 3);
			
			//get that summary
			$pos = strpos($text, ">");
			$text = substr($text, $pos);
			$end = strpos($text, "</fieldset>") - 1;
			$info["summary"] = substr($text, 1, $end);
			
			$loop = true;
			while(true){
				//next day planning and problems etc.
				$pos = strpos($text, "<h4>");
				if(!$pos){
					break;
				}
				
				$text = substr($text, $pos+4);
				$end = strpos($text, "</h4>");
				$temp = substr($text, 0, $end);
				if(strpos($temp, "Planning")){
					$temp = "planning";
				}
				if($temp == "Problems"){
					$temp = "problems";
				}
				if($temp == "Discipline"){
					$temp = "discipline";
				}
				if($temp == "Recognition"){
					$temp = "recognition";
				}
				
				$pos = strpos($text, "</h4>");
				$text = substr($text, $pos+5);
				$end = strpos($text, "<hr>");
				$info[$temp] = substr($text, 0, $end);
			}
			
			//get submitter hours
			$pos = strpos($text, "center");
			$text = substr($text, $pos+8);
			$pos = strpos($text, "center");
			$text = substr($text, $pos+8);
			$end = strpos($text, "</td");
			$info["hours"] = substr($text, 0, $end);
			
			//get the weekly hours out of the way
			$pos = strpos($text, "<tr");
			$text = substr($text, $pos+4);
			
			//employee hours
			$i = 1;
			$reset = true;
			while(true){
				$pos = strpos($text, "center");
				$text = substr($text, $pos+8);
				$end = strpos($text, "</td");
				$ename = substr($text, 0, $end);	//should be the name
				$pos = strpos($text, "center");
				$text = substr($text, $pos+8);
				$end = strpos($text, "</td");
				$ehours = substr($text, 0, $end);	//hours
				$pos = strpos($text, "center");
				$text = substr($text, $pos+8);
				$end = strpos($text, "</td");
				$ejob = substr($text, 1, $end-1);		//should be the job
				
				//check for employees
				$pos = strpos($text, "(s)");
				$check = substr($text, $pos-11, 3);
				
				if($ename == $name){
					$desc = "hoursm$i";
					$info[$desc] = $ehours;
				}
				if($check != " 1 "){
					if($reset){
						$i = 1;
						$reset = false;
					}
					$mult = false;
					$desc = "employee$i";
					$info[$desc] = $ename;
					$desc = "hours$i";
					$info[$desc] = $ehours;
					$desc = "job$i";
					$info[$desc] = $ejob;
				}
				else break;
				
				//get the weekly hours out of the way
				$pos = strpos($text, "<tr");
				$text = substr($text, $pos+4);
				$end = strpos($text, "</tr>");
				$check = substr($text, 0, $end);
				echo "check$i<textarea>$check</textarea><br>";
				if(strpos($check, "<b>")){
					break;
				}
				
				$i++;
				if($i > 10) break;	//insurance
			}
			
			
			
			$pos = strpos($text, "Vehicle");
			if($pos){
				$text = substr($text, $pos);
				$pos = strpos($text, ":");
				$text = substr($text, $pos+1);
				$info["vid"] = substr($text, 1, 3);
				$pos = strpos($text, ":");
				$text = substr($text, $pos+1);
				$end = strpos($text, " -");
				$info["startodo"] = substr($text, 1, $end-1);
				$pos = strpos($text, "-");
				$text = substr($text, $pos+1);
				$end = strpos($text, "<br>");
				$info["endodo"] = substr($text, 1, $end-1);
				
			}
		}
		else{
			//before fieldsets
			echo "<h1>nope</h1>";
		}
		
		return $info;
	}
	
	
	
	
	
	
	
	
	
?>