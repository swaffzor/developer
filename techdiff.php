<?php
	include_once("functions.php");
	include_once("globals.php");
	include_once("database.php");
	
?>

<html>
	<head>
	</head>
	<body>
		<? include_once("nav2.php"); ?>
		
		<form name="options" action="techdiff.php" method="post">
			
			<select name="statusOption">
				<?php
				if($_POST['statusOption'] == "na"){
					echo "<option selected value='na'>---Select Status---</option>";
				}
				else{
					echo "<option value='na'>---Select Status---</option>";
				}
				
				if($_POST['statusOption'] == "new"){
					echo "<option selected value='new'>New</option>";
				}
				else{
					echo "<option value='new'>New</option>";
				}
				
				if($_POST['statusOption'] == "complete"){
					echo "<option selected value='complete'>Complete</option>";
				}
				else{
					echo "<option value='complete'>Complete</option>";
				}
				
				if($_POST['statusOption'] == "open"){
					echo "<option selected value='open'>Open</option>";
				}
				else{
					echo "<option value='open'>Open</option>";
				}
				?>
			</select>
			
			<input type="submit">
		</form>
		
		<table cellspacing="10">
			<th>Submitted</th><th>Submitter</th><th>Message</th><th>Status</th>
			<?php
				$sql = "SELECT * FROM Tickets";
				if($_POST['statusOption'] != "na" && $_POST['statusOption'] != ""){
					$sql .= " WHERE Status = '".$_POST['statusOption']."'";
				}
				
				$techDiffRows = mysqli_query($con, $sql);
				while($row = mysqli_fetch_array($techDiffRows)) {
					echo "<tr><td>".$row['Submitted']."</td>";
					echo "<td>".$row['Submitter']."</td>";
					echo "<td>".$row['Message']."</td>";
					echo "<td>".$row['Status']."</td></tr>";
					echo "<tr><td colspan=4><hr></td></tr>";
				}
			?>
		</table>
	</body>
</html>