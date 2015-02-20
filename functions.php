<?
	
	//all functions used for recap system
	
	
	//function getWeeklyHours
	//date created: 7 July 2014
	//purpose: to get the weekly hours of an employee
	//input parameters: name, date
	//output: current weekly hours
	function getWeeklyHours($fname, $fdate){
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
	function setHours($fname, $fdate, $fjob, $fhours, $fsuper){
		include("database.php");
		$now = date("F j, Y @ g:i a");
		$ffdate = $fdate;
		for($i=0;$i<7;$i++){
			if (strftime("%A",strtotime($fdate)) == "Sunday") {
				$sunday = $fdate;	//$sunday is a date yyyy-mm-dd
				//if fdate is a sunday
				if ($i == 0){
					if ($fname != "" && $fname != "---Select Employee---"){
						$esql = "INSERT INTO Hours (Submitted, Date, Name, Job, Hours, Submitter, WeeklyHours) VALUES ('$now', '$ffdate', '$fname', '$fjob', '$fhours', '$fsuper', '$fhours')";
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
	
?>