<?php
	for($i = 1; $i<13; $i++){
		$monthValues .= "<option ";
		if($i == $_POST['Month']){
			$monthValues .= "selected ";
		}
		$monthValues .= "value='";
		if($i<10){
			$monthValues .= "0";
		}
		$monthValues .= "$i'>";
		if($i<10){
			$monthValues .= "0";
		}
		$monthValues .= "$i</option>\n\t\t\t\t";
	}
	for($i = 1; $i<32; $i++){
		$dayValues .= "<option";
		if($i == $_POST['Day']) {
			$dayValues .= " selected";
		}
		$dayValues .= " value='";
		if($i<10){
			$dayValues .= "0";
		}
		$dayValues .= "$i'>";
		if($i<10) {$dayValues .= "0";}
		$dayValues .= "$i</option>\n\t\t\t\t";
	}
	for($i = 2014; $i<2021; $i++){
		$yearValues .= "<option";
		if($i == $_POST['Year']) {
			$yearValues .= " selected";
		}
		$yearValues .= " value='$i'>$i</option>\n\t\t\t\t";
	}

	 echo "<p>Select the day that you want to view</p>
		<table>
		<form action='report2.php' method='post' name='dateForm'>
		<th>Month</th><th>Day</th><th>Year</th>
		<tr><td><select name='Month' id='month'>
					". $monthValues ."
				</select></td>
			
				<td><select name='Day' id='day'>
					". $dayValues ."
				</select>
				</td>
				
				<td><select name='Year' id='year'>
					". $yearValues ."
				</select></td>
				
				<td id='blank'></td></tr>
					
		<tr><td><input type='submit' name='submit'></td>
		<td colspan='2' align='center'
	</form></table>";
?>