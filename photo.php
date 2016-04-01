<?
	include("database.php");
	include("nav2.php");
?>
<html><head>
	
<script type="text/javascript">
	
	function showHideName(){
		if (document.getElementById("noList").checked){
			document.getElementById("nameDrop").style.display = 'none';
			document.getElementById("name").style.display = 'inline';
		}
		else{
			document.getElementById("nameDrop").style.display = 'inline';
			document.getElementById("name").style.display = 'none';		
		}
	}
			
	function putToDay(){
				var toDay = new Date();
				var dd = toDay.getDate();
				var mm = toDay.getMonth() + 1; //January is 0!
				var yyyy = toDay.getFullYear();
				
				if(mm < 10){
					document.getElementById("Month").value = "0" + mm;
				}
				else{
					document.getElementById("Month").value = mm;
				}
				
				if(dd < 10){
					document.getElementById("Day").value = "0" + dd;
				}
				else{
					document.getElementById("Day").value = dd;
				}
				
				document.getElementById("Year").value = yyyy;
				
				var ddd = document.getElementById("Day").value;
				var dmm = document.getElementById("Month").value;
				var dyyyy = document.getElementById("Year").value;
				
				dateCheck();
				//*/
	}
			
			function dateCheck(){
				var toDay = new Date();
				var dd = toDay.getDate();
				var mm = toDay.getMonth() + 1; //January is 0!
				var yyyy = toDay.getFullYear();
				var ddd = document.getElementById("Day").value;
				var dmm = document.getElementById("Month").value;
				var dyyyy = document.getElementById("Year").value;
				
				//disable future dates
				//Day
				var op = document.getElementById("Day").getElementsByTagName("option");
				for (var i = 0; i < op.length; i++) {
					if (op[i].value > dd && dmm == mm) {
						op[i].disabled = true;
					}
					else{
						op[i].disabled = false;
					}
				}
				//Month
				var op = document.getElementById("Month").getElementsByTagName("option");
				for (var i = 0; i < op.length; i++) {
					if (op[i].value > mm && dyyyy == yyyy) {
						op[i].disabled = true;
					}
					else{
						op[i].disabled = false;
					}
				}
				//Year
				var op = document.getElementById("Year").getElementsByTagName("option");
				for (var i = 0; i < op.length; i++) {
					if (op[i].value > yyyy) {
						op[i].disabled = true;
					}
					else{
						op[i].disabled = false;
					}
				}
			}
</script>
			
</head>
<body onload="putToDay()">

	<table><th>Month</th><th>Day</th><th>Year</th><th></th>
		<tr><td><select name='Month' id="Month" onchange="dateCheck()" selected="<?php echo $_POST['Month']?>">
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
	
		<td><select name='Day' id="Day" onchange="dateCheck()" selected="<?php echo $_POST['Day']?>">
			<option value='01' id="1">01</option>
			<option value='02' id="2">02</option>
			<option value='03' id="3">03</option>
			<option value='04' id="4">04</option>
			<option value='05' id="5">05</option>
			<option value='06' id="6">06</option>
			<option value='07' id="7">07</option>
			<option value='08' id="8">08</option>
			<option value='09' id="9">09</option>
			<option value='10' id="10">10</option>
			<option value='11' id="11">11</option>
			<option value='12' id="12">12</option>
			<option value='13' id="13">13</option>
			<option value='14' id="14">14</option>
			<option value='15' id="15">15</option>
			<option value='16' id="16">16</option>
			<option value='17' id="17">17</option>
			<option value='18' id="18">18</option>
			<option value='19' id="19">19</option>
			<option value='20' id="20">20</option>
			<option value='21' id="21">21</option>
			<option value='22' id="22">22</option>
			<option value='23' id="23">23</option>
			<option value='24' id="24">24</option>
			<option value='25' id="25">25</option>
			<option value='26' id="26">26</option>
			<option value='27' id="27">27</option>
			<option value='28' id="28">28</option>
			<option value='29' id="29">29</option>
			<option value='30' id="30">30</option>
			<option value='31' id="31">31</option>
		</select>
		</td>
		
		<td colspan="1"><select name='Year' id="Year" onchange="dateCheck()" selected="<?php echo $_POST['Year']?>">
			<option value='2014'>2014</option>
			<option value='2015'>2015</option>
			<option value='2016'>2016</option>
			<option value='2017'>2017</option>
			<option value='2018'>2018</option>
			<option value='2019'>2019</option>
			<option value='2020'>2020</option>
		</select></td>
		<tr>
			<td colspan="3" align="center">
				<div id="messageDiv" style="display: none;"><p id="message" style="color: red; font-size: 20;">Not Today's Date</p>
				</div>
			</td>
		</tr>
		</table>
<h2>Upload a photo</h2>
<form action="testupload.php" method="post" enctype="multipart/form-data">
	
	<?//!name
			?>
			<select onchange="insertEmail(this.value)" id="nameDrop" name="nameDrop" style="display: inline">
				<option>---Select Name---</option>
				<?php
					$tmp = mysqli_query($con,"SELECT Name FROM employees where recap != '' ORDER BY Name");
					while($row = mysqli_fetch_array($tmp)) {
						echo "<option value ='" . $row['Name'] . "'>" . $row['Name'];
					}
				?>
			</select>
			<input placeholder="Name" name="name" id="name" type="text" onchange="nameFix()" required value="<?php echo $_POST['name'] ?>" style="display: none"/>
			
			<input type="checkbox" id="noList" name="noList" onchange="showHideName()">Name Not Listed<br />
			
	Please choose a file: <input type="file" name="uploadFile"><br>
	<input type="submit" value="Upload File">
</form></body></html>