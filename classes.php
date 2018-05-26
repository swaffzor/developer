<?
	include_once 'functions.php';
	
	//include("database.php");
	//printf("Error: %s\n", mysqli_error($con));
	/**
	* Employee
	*/
	class TSIemployee {
	
		public $name;
		public $status;
		public $company;
		public $recap;
		public $email;
		public $exempt;
		public $daysMissing;
		public $reportsTo;
		public $firstName;
		public $sqlId;
		public $idNumber;
		
		//sets the class variables to the passed username
		//TODO: test if username is invalid
		public function SetEmployeeData($username, $con){
			$queryresults = mysqli_query($con, "SELECT * FROM employees WHERE Name = '$username'");
			while($row = mysqli_fetch_array($queryresults)) {
				$this->name = $row['Name'];
				$this->status = $row['Status'];
				$this->company = $row['Company'];
				$this->recap = $row['recap'];
				$this->email = $row['email'];
				$this->exempt = $row['exempt'];
				$this->daysMissing = $row['daysMissing'];
				$this->reportsTo = $row['ReportingTo'];
				$this->firstName = $row['Firstname'];
				$this->sqlId = $row['id'];
				$this->idNumber = $row['id'];
			}
		}
		
		public function test($word){
			echo $this->name . "<BR>";
			echo $this->status . "<BR>";
			echo $this->company . "<BR>";
			echo $this->recap . "<BR>";
			echo $this->email . "<BR>";
			echo $this->exempt . "<BR>";
			echo $this->daysMissing. "<BR>";
			echo $this->reportsTo . "<BR>";
			echo $this->firstName . "<BR>";
			echo $this->idNumber . "<BR>";
		}
	}	
	
	
	/**
	* week of hours
	*/
	class WeekHours {
		//dates
		public $sun;
		public $mon;
		public $tue;
		public $wed;
		public $thu;
		public $fri;
		public $sat;
		
		//hours for corresponding days
		public $uhr;
		public $mhr;
		public $thr;
		public $whr;
		public $hhr;
		public $fhr;
		public $shr;
		
		//sql id for the hours table
		public $uid;
		public $mid;
		public $tid;
		public $wid;
		public $hid;
		public $fid;
		public $sid;
		
		public function SetDays($sun){
			
			$this->sun = date("Y-m-d", strtotime($sun));
			$this->mon = date("Y-m-d", strtotime("+1 days", strtotime($sun)));
			$this->tue = date("Y-m-d", strtotime("+2 days", strtotime($sun)));
			$this->wed = date("Y-m-d", strtotime("+3 days", strtotime($sun)));
			$this->thu = date("Y-m-d", strtotime("+4 days", strtotime($sun)));
			$this->fri = date("Y-m-d", strtotime("+5 days", strtotime($sun)));
			$this->sat = date("Y-m-d", strtotime("+6 days", strtotime($sun)));
		}
		
		public function SetWeekHours($con, $name){
			
			$this->uhr = GetHoursForDate($con, $name, $this->sun);
			$this->mhr = GetHoursForDate($con, $name, $this->mon);
			$this->thr = GetHoursForDate($con, $name, $this->tue);
			$this->whr = GetHoursForDate($con, $name, $this->wed);
			$this->hhr = GetHoursForDate($con, $name, $this->thu);
			$this->fhr = GetHoursForDate($con, $name, $this->fri);
			$this->shr = GetHoursForDate($con, $name, $this->sat);
			
			$this->uid = GetIDForDate($con, $name, $this->sun);
			$this->mid = GetIDForDate($con, $name, $this->mon);
			$this->tid = GetIDForDate($con, $name, $this->tue);
			$this->wid = GetIDForDate($con, $name, $this->wed);
			$this->hid = GetIDForDate($con, $name, $this->thu);
			$this->fid = GetIDForDate($con, $name, $this->fri);
			$this->sid = GetIDForDate($con, $name, $this->sat);
			
		}
		
		public function GetTotalHoursForWeek(){
			$total = $this->uhr;
			$total += $this->mhr;
			$total += $this->thr;
			$total += $this->whr;
			$total += $this->hhr;
			$total += $this->fhr;
			$total += $this->shr;
			return $total;
		}
		
		
	
	}
	
	
?>