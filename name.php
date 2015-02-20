<?
	require_once 'database.php';
	require_once 'javascript.php';	
?>
	<select onchange="insertEmail(this, 'email')" id="nameDrop" name="nameDrop" style="display: inline">
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
		<input placeholder="Name" name="name" id="name" type="text" onchange="nameFix()" required value="<?php echo $_POST['name'] ?>" style="display: none"/>
		<input type="email" name="email" id="email" placeholder="email" disabled="true" required value="<?php echo $_COOKIE['email'] ?>">
		
		<input type="checkbox" id="noList" name="noList" onchange="showHideName()"><label for="noList">Name Not Listed</label><br />