<?
	/*include_once 'nav.php';
	session_start();
	if($_SESSION['LoggedIn'] != 1){
		echo '<meta http-equiv="refresh" content="0;login.php?sender=index.php">';
		exit();
	}*/
	
	include_once 'nav.php';
?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>
<html>

	<head>
		<title>Recap Report</title>
		<link rel="apple-touch-icon-precomposed" href="http://tsidisaster.net/report-touch-icon-114.png">
		<script type="text/javascript">
			function putToday(){
				var today = new Date();
				var dd = today.getDate();
				var mm = today.getMonth() + 1; //January is 0!
				var yyyy = today.getFullYear();
				var ddd = document.getElementById("day").value;
				var dmm = document.getElementById("month").value;
				var dyyyy = document.getElementById("year").value;
				
				if(mm < 10){
					document.getElementById("month").value = "0" + mm;
				}
				else{
					document.getElementById("month").value = mm;
				}
				
				if(dd < 10){
					document.getElementById("day").value = "0" + dd;
				}
				else{
					document.getElementById("day").value = dd;
				}
				
				document.getElementById("year").value = yyyy;
				
			}
			
			function yesterday(){
				var today = new Date();
				var dd = today.getDate();
				var mm = today.getMonth() + 1; //January is 0!
				var yyyy = today.getFullYear();
				var ddd = document.getElementById("day").value;
				var dmm = document.getElementById("month").value;
				var dyyyy = document.getElementById("year").value;
				
				if(dd > 10){
					document.getElementById("day").value = "0" + (dd-1);
				}
				//adjust for months with 31 days
				else if(dd == 1){
					if(mm == 1 || mm == 2 || mm == 4 || mm == 6 || mm == 8 || mm == 9 || mm == 11){
						document.getElementById("day").value = 31;
					}
					else if(mm == 3){
						document.getElementById("day".value = 28);
					}
					else{
						document.getElementById("day").value = 30;
					}
					if (mm > 9){
						document.getElementById("month").value = mm - 1;
					}
					else{
						document.getElementById("month").value = "0" + (mm - 1);
					}
				}
				else{
					document.getElementById("day").value = (dd-1);
				}
			}
		</script>
		
	</head>
	<body onload="putToday()">
	
	
	
	<h1>Recap Report</h1>
	<p>Select the day that you want to view</p>
	<table>
	<form action='report2.php' method='post' name='dateForm'>
	<th>Month</th><th>Day</th><th>Year</th>
	<tr><td><select name='Month' id="month">
				<option value='01'>01</option>
				<option value='02'>02</option>
				<option value='03'>03</option>
				<option value='04'>04</option>
				<option value='05'>05</option>
				<option value='06'>06</option>
				<option value='07'>07</option>
				<option value='08'>08</option>
				<option value='09'>09</option>
				<option value='10'>10</option>
				<option value='11'>11</option>
				<option value='12'>12</option>
			</select></td>
		
			<td><select name='Day' id="day">
				<option value='01'>01</option>
				<option value='02'>02</option>
				<option value='03'>03</option>
				<option value='04'>04</option>
				<option value='05'>05</option>
				<option value='06'>06</option>
				<option value='07'>07</option>
				<option value='08'>08</option>
				<option value='09'>09</option>
				<option value='10'>10</option>
				<option value='11'>11</option>
				<option value='12'>12</option>
				<option value='13'>13</option>
				<option value='14'>14</option>
				<option value='15'>15</option>
				<option value='16'>16</option>
				<option value='17'>17</option>
				<option value='18'>18</option>
				<option value='19'>19</option>
				<option value='20'>20</option>
				<option value='21'>21</option>
				<option value='22'>22</option>
				<option value='23'>23</option>
				<option value='24'>24</option>
				<option value='25'>25</option>
				<option value='26'>26</option>
				<option value='27'>27</option>
				<option value='28'>28</option>
				<option value='29'>29</option>
				<option value='30'>30</option>
				<option value='31'>31</option>
			</select>
			</td>
			
			<td><select name='Year' id="year">
				<option value='2013'>2013</option>
				<option value='2014'>2014</option>
				<option value='2015'>2015</option>
				<option value='2016'>2016</option>
				<option value='2017'>2017</option>
				<option value='2018'>2018</option>
				<option value='2019'>2019</option>
				<option value='2020'>2020</option>
			</select></td>
			
			<td id="blank"></td></tr>
				
	<tr><td><input type='submit' name='submit'></td>
	<td colspan="2" align="center"><!--button style="background-color: lightblue;" onclick="yesterday()">Yesterday<--></td></tr>
</form>
</table>
</body>
</html>