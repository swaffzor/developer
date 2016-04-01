<html>
	<head>
		<style>
			#pics{
			    width: 50%;
			    height: auto;
			}
		</style>
	</head>
<?php
	
date_default_timezone_set ("America/New_York");
$date = date("Y-m-d");
$now = date("Y-m-d_g:i:s_");

	/* if 'Name Not Listed' is checked then empName is what was manually entered */
	if(isset($_POST['noList']) != "on"){
		$eList->name[0] = $_POST['nameDrop'];
		$empName[0] = $eList->name[0];
	}
	else{
		$eList->name[0] = $_POST['name'];
		$empName[0] = $eList->name[0];
	}
	
	if ($empName[0] == "" || $empName[0] == "---Select Name---"){
		exit("Please press the back button and fill out your name");
	}
	
$empName[1] = str_replace(" ", "_", $empName[0]);

include("database.php");
include("nav2.php");

$target_dir = "uploads/";
$target_dir = $target_dir . basename( $_FILES["uploadFile"]["name"]);
$fileName = $now . $empName[1];


echo "Thanks $empName[0]";



if (move_uploaded_file($_FILES["uploadFile"]["tmp_name"], "uploads/$fileName")) {
    echo "The file ". basename( $_FILES["uploadFile"]["name"]). " has been uploaded as $fileName";
    mysqli_query($con, "INSERT INTO photos(Name, Date, link) VALUES ('$empName[0]', '$date', '$fileName')");
} else {
    echo "Sorry, there was an error uploading your file. Please try again.";
}
echo "<br><BR><img src='http://tsidisaster.net/jeremy/uploads/" . $fileName . "' id='pics'>";
echo "<BR>".$fileName;


?>