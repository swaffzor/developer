<?php
/*
	//comment out when using as a module block
	date_default_timezone_set ("America/New_York");
	session_start();
	
	require_once("database.php");
	include_once("functions.php");
	include_once("globals.php");
	include_once("classes.php");
	include_once("nav.php");	
	
*/
	if(isset($_GET['lastwk'])){
		$sun = getSundayDate(date("Y-m-d"));
		$sun = date("Y-m-d", strtotime("-1 week", strtotime($sun)));
		$link = "<a href='?thiswk=1'>This Week</a><Br>";
	}
	else{
		$sun = getSundayDate(date("Y-m-d"));
		$link = "<a href='?lastwk=1'>Last Week</a><Br>";
	}
	//$sun = date("Y-m-d", strtotime("-1 week", strtotime($sun)));  //TODO: remove this when done debugging
	$week = new WeekHours();
	$week->SetDays($sun); //$this->sun = date("Y-m-d", strtotime("-1 week", strtotime($sun)));
	$week->SetWeekHours($con, $_SESSION['User']);
	
	echo "<table border><tr>";
	echo "<td align='center' colspan='8'> Hours for week starting " . $week->sun . "</td></tr><tr>"; 
	echo "<td align='center'>Sunday<br><a href='account.php?param=". 	$week->uid ."'>".date("m-d", strtotime($week->sun))."</a></td>";
	echo "<td align='center'>Monday<br><a href='account.php?param=". 	$week->mid ."'>".date("m-d", strtotime($week->mon))."</a></td>";
	echo "<td align='center'>Tuesday<br><a href='account.php?param=". 	$week->tid ."'>".date("m-d", strtotime($week->tue))."</a></td>";
	echo "<td align='center'>Wednesday<br><a href='account.php?param=". $week->wid ."'>".date("m-d", strtotime($week->wed))."</a></td>";
	echo "<td align='center'>Thursday<br><a href='account.php?param=". 	$week->hid ."'>".date("m-d", strtotime($week->thu))."</a></td>";
	echo "<td align='center'>Friday<br><a href='account.php?param=". 	$week->fid ."'>".date("m-d", strtotime($week->fri))."</a></td>";
	echo "<td align='center'>Saturday<br><a href='account.php?param=". 	$week->sid ."'>".date("m-d", strtotime($week->sat))."</a></td>";
	echo "<td align='center'>Total</td>";
	
	echo "</tr><tr>";
	
	echo "<td align='center'>". $week->uhr ."</td>"; //sun
	echo "<td align='center'>". $week->mhr ."</td>"; //mon
	echo "<td align='center'>". $week->thr ."</td>"; //tue
	echo "<td align='center'>". $week->whr ."</td>"; //wed
	echo "<td align='center'>". $week->hhr ."</td>"; //thur
	echo "<td align='center'>". $week->fhr ."</td>"; //fri
	echo "<td align='center'>". $week->shr ."</td>"; //sat
	echo "<td align='center'>". $week->GetTotalHoursForWeek() ."</td>"; //total
	
	echo "</tr></table>";
	echo $link;
	
?>