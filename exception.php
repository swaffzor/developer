<?
	
	require_once 'database.php';
	include("javascript.php");
	date_default_timezone_set ("America/New_York");
	$now = date("F j, Y @ g:i a");
	$m = date("m");
	$y = date("Y");
	$d = date("d");
	include("nav2.html");
?>
	
	Turn off email notifications for the selected day <br>
	<form action="exception.php" method="post">
	<select onchange="insertEmail(this, 'exemail')" id="exnameDrop" name="exnameDrop" style="display: inline">
		<option>---Select Name---</option>
		<?php
			$tmp = mysqli_query($con,"SELECT Name FROM employees where recap != '' ORDER BY Name");
			while($row = mysqli_fetch_array($tmp)) {
				echo "<option value ='" . $row['Name']."'";
				
				if($row["Name"] == $_COOKIE["name"]){
					echo " selected";
				}
				
				echo ">" . $row['Name']."</option>";
			}
		?>
		</select>
		<input type="email" name="exemail" id="exemail" placeholder="email" disabled="true" required value="<?php echo $_COOKIE['email'] ?>">
		
		<table>
			<th>Month</th><th>Day</th><th>Year</th><th></th>
			<tr><td><select name='exMonth' id="exMonth">
				<?
					for($i = 1; $i<13; $i++){
						echo "<option";
						if($i == $m) {echo " selected";}
						echo " value='";
						if($i<10) {echo "0";}
						echo "$i'>";
						if($i<10) {echo "0";}
						echo "$i</option>\n\t\t\t\t";
					}
				?>
			</select></td>
		
			<td><select name='exDay' id="exDay">
				<?
					for($i = 1; $i<32; $i++){
						echo "<option";
						if($i == $d) {echo " selected";}
						echo " value='";
						if($i<10) {echo "0";}
						echo "$i'>";
						if($i<10) {echo "0";}
						echo "$i</option>\n\t\t\t\t";
					}
				?>
			</select>
			</td>
			
			<td colspan="1"><select name='exYear' id="exYear">
				<?
					for($i = 2014; $i<2021; $i++){
						echo "<option";
						if($i == $y) {echo " selected";}
						echo " value='$i'>$i</option>\n\t\t\t\t";
					}
				?>
			</select></td>
			</tr>
		</table>
		<input type="submit" value="Submit email exception">
	</form>
	
	<?
		if(isset($_POST["exnameDrop"])){
			$name = $_POST["exnameDrop"];
			$date = $_POST["exYear"]."-".$_POST["exMonth"]."-".$_POST["exDay"];
			$email = $_POST["exemail"];
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
			$headers .= "From: recap@TSIdisaster.com" . "\r\n" . "Reply-To: recap@tsidisaster.com" . "\r\n" . "Bcc: jeremy@tsidisaster.com";
			$message = "$name has elected to not receive email notifications on ";
			$message .= $_POST["exMonth"]."-".$_POST["exDay"]."-".$_POST["exYear"]."<BR>";
			$test = true;
			
			$results = mysqli_query($con, "SELECT * FROM exception WHERE Date = '$date' AND Name = '$name'");
			while($row = mysqli_fetch_array($results)) {
				$test = false;
				echo "This exception already exists<br>";
				echo "<table border><th>Name</th><th>Date</th><th>Submitted</th><tr><td>".$row["Date"]."</td><td>".$row["Name"]."</td><td>".$row["Submitted"]."</tr></table>";
				break;
			}
			
			if($test == true){
				if(mysqli_query($con, "INSERT INTO exception (Date, Name, Submitted) VALUES ('$date', '$name', '$now')")){
					echo $message;
					if(mail($email, "Email Exception Receipt", $message, $headers)){
						echo "<br>Your request has been successfully entered into the database and a receipt has been emailed to you.";
					}
				}
			
				else{
					if(mail("jeremy@tsidisaster.com", "Email Exception Error", $message, $headers)){
						echo "There was an error submitting your request, an email with your request has been sent to Jeremy for manual entry";
					}
				}
			}
		}
	?>

