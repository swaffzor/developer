<html>
	<head>
		<style>
			table { 
				table-layout: fixed; 
			}
			td { 
				width: 16.5%; 
				background-color: #8cf5f8;
			}
			
			.tahead{
				background-color: #77c5fc;
			}
		</style>
	</head>
	<body>

<?php
	
	require_once 'database.php';
	
	Class equipmentList{
		Public $eqID = array();
		Public $makemodel = array();
		Public $description = array();
		public $year = array();
		public $vin = array();
		public $ID = array();
	}
	
	$newList = new equipmentList();
	$oldList = new equipmentList();
	
	$equp = mysqli_query($eqcon,"SELECT * FROM EquipmentUpdate ORDER BY EquipmentID");
	while($row = mysqli_fetch_array($equp)) {
		$newList->eqID[] = $row['EquipmentID'];
		$newList->makemodel[] = $row['MakeModel'];
		$newList->description[] = $row['Description'];
		$newList->year[] = $row['Year'];
		$newList->vin[] = $row['VIN'];
		$newList->ID[] = $row['ID'];
	}
	
	$temp = mysqli_query($eqcon,"SELECT * FROM Equipment ORDER BY EquipmentID");
	while($row = mysqli_fetch_array($temp)) {
		$oldList->eqID[] = $row['EquipmentID'];
		$oldList->makemodel[] = $row['MakeModel'];
		$oldList->description[] = $row['Description'];
		$oldList->year[] = $row['Year'];
		$oldList->vin[] = $row['VIN'];
		$oldList->ID[] = $row['ID'];
	}
	
	echo "<form action='update_equipment_list.php' name='changeForm' method='post' enctype='multipart/form-data'><table><th>Type</th><th>Existing</th><th>New</th>";
	
	for($a=0; $a<sizeof($newList->makemodel); $a++){
		for($b=0; $b<sizeof($oldList->makemodel); $b++){
			if($newList->eqID[$a] != 0){	
				if($newList->eqID[$a] == $oldList->eqID[$b]){
					if($newList->makemodel[$a] != $oldList->makemodel[$b] || $newList->description[$a] != $oldList->description[$b] || $newList->year[$a] != $oldList->year[$b] || $newList->vin[$a] != $oldList->vin[$b]){
						echo "<tr><td class='tahead' colspan='3' align='center'>".$oldList->eqID[$b];
						//change checkbox
						echo "</td><td style='background-color:white'><input type='checkbox' id='cb".$oldList->ID[$b]."' name='cb".$oldList->ID[$b]."'";
						$theID = "cb".$oldList->ID[$b];
						if(isset($_POST[$theID])){
							echo " checked='".$_POST[$theID]."'";
						}
						echo "><label for='cb".$oldList->ID[$b];
						echo "'>change</label></td></tr>";
						
						if($newList->makemodel[$a] != $oldList->makemodel[$b]){
							echo "<tr><td>make/model</td><td>".$oldList->makemodel[$b]."</td><td>".$newList->makemodel[$a]."</td></tr>";
						}
						if($newList->description[$a] != $oldList->description[$b]){
							echo "<tr><td>description</td><td>".$oldList->description[$b]."</td><td>".$newList->description[$a]."</td></tr>";
						}
						if($newList->year[$a] != $oldList->year[$b]){
							echo "<tr><td>year</td><td>".$oldList->year[$b]."</td><td>".$newList->year[$a]."</td></tr>";
						}
						if($newList->vin[$a] != $oldList->vin[$b]){
							echo "<tr><td>VIN</td><td>".$oldList->vin[$b]."</td><td>".$newList->vin[$a]."</td></tr>";
						}
						
						
						//! backend
						if(isset($_POST[$theID])){
							if($newList->makemodel[$a] != $oldList->makemodel[$b]){
								$sqlMakemodel = " MakeModel='".$newList->makemodel[$a]."'";
								$p1 = true;
							}
							else{
								$sqlMakemodel = "";
								$p1 = false;
							}
							if($newList->description[$a] != $oldList->description[$b]){
								if($p1){
									$comma = ",";
								}
								else{
									$comma = "";
								}
								$sqlDescription = "$comma Description='".$newList->description[$a]."'";
								$p2 = true;
							}
							else{
								$sqlDescription = "";
								$p2 = false;
							}
							if($newList->year[$a] != $oldList->year[$b]){
								if($p1 == true || $p2 == true){
									$comma = ",";
								}
								else{
									$comma = "";
								}
								$sqlYear = "$comma Year='".$newList->year[$a]."'";
								$p3 = true;
							}
							else{
								$sqlYear = "";
								$p3 = false;
							}
							if($newList->vin[$a] != $oldList->vin[$b]){
								if($p1 == true || $p2 == true || $p3 == true){
									$comma = ",";
								}
								else{
									$comma = "";
								}
								$sqlVin = "$comma VIN='".$newList->vin[$a]."'";
							}
							else{
								$sqlVin = "";
							}
							
							$sql = "UPDATE Equipment SET $sqlMakemodel $sqlDescription $sqlVin $sqlYear WHERE ID= '".$oldList->ID[$b]."'";
							echo $sql."<BR>";
						}
					}
				}
			}
		}
	}
	
	echo "</table><input type='submit'></form";
	
	
	
	
	echo "<pre>";
	print_r($_POST);
	echo "</pre>";
	
	
	
?>

	</body>
</html>