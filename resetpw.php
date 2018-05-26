<?
	session_start();
	include("database.php");
	include("functions.php");
	
	$extra = "<li><a class='button' href = 'resetpw.php'>Password</a></li>";
	include("nav2.php");
	session_start();
	
	$validated = 0;
	if(isset($_GET['resetpw'])){
		$check = mysqli_query($con,"SELECT * FROM users WHERE ResetPW = '". $_GET['resetpw'] ."' Limit 1");
		while($row = mysqli_fetch_array($check)) {
			if($row['ResetPW'] == $_GET['resetpw']){
				$validated = 1;
				$user = $row['Username'];
				$userId = $row['UserID'];
			}
		}
	}
	else{
		$user = $_SESSION['User'];
		$userId = $_SESSION['userID'];
	}
	
	if($_SESSION['LoggedIn'] != 1 && $validated == 0){
		echo '<meta http-equiv="refresh" content="0;login.php?sender='.$URL.'">';
		exit();
	}
	//! TODO: 
	//set default permissions
	//email me
	
	///////////////for registration purposes////////////////////
	//echo "<fieldset>";
	$cost = 10;
	$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
	$salt = sprintf("$2a$%02d$", $cost) . $salt;
	//echo "salt: " . $salt . "<br>";
	//echo "hash: " . $hash . "</fieldset>";
	////////////////////////////////////////////////////////////
	
	
/*
	echo "<pre>POST ";
	print_r($_POST);
	echo "<br>SESSION ";
	print_r($_SESSION);
	echo "</pre>";
*/
	
	if(empty($_GET['sender'])){
		$sender = "index.php";
	}
	else{
		$sender = $_GET['sender'];
	}
	
	$form = '<form name="register" action="resetpw.php" method="post">
		<div style="color:gray; Font-size:small;">
		Password must be:<br>
		* At least 8 characters long<br>
		* Contain at least 1 upper case letter, 1 lower case letter<br>
		* At least 1 special charachter (!@#$%^&*)<br>
		* At least 1 number 0-9
		</div>
		<input type="password" name="pw" id="password" placeholder="password">
		<input type="password" name="repw" id="repassword" placeholder="retype password"><br>
		<input type="button" value="Reset Password" onclick="validate()">
	</form>';
	
	?> 
	<head>
		<link rel='stylesheet' href='mystyle.css'>
		<script type="text/javascript">
			
			function validate(){
				var valPassed = true;
				var msg = "";
				var aField = [];
				var i;
				
				aField[0] = document.getElementById("password");
				aField[1] = document.getElementById("repassword");

				if(aField[0].value != aField[1].value){
					valPassed = false;
					msg = "Password did not match";
					aField[0].focus();
				}
				
				for(i=0; i<aField.length; i++){
					if(aField[i].value == ""){
						valPassed = false;
						msg = aField[i].id + " is empty, all fields are required";
						aField[i].focus();
						break;
					}
				}
				
				if(aField[0].value.length < 8){
					valPassed = false;
					msg = "Password length is too short";
					aField[0].focus();
				}
				stuff = /[a-z]/;
				if(!stuff.test(aField[0].value)){
					valPassed = false;
					msg = "Password must contain at least one lowercase letter (a-z)!";
					aField[0].focus();
				}
				stuff = /[A-Z]/;
				if(!stuff.test(aField[0].value)){
					valPassed = false;
					msg = "Password must contain at least one uppercase letter (A-Z)!";
					aField[0].focus();
				}
				stuff = /[0-9]/;
				if(!stuff.test(aField[0].value)){
					valPassed = false;
					msg = "Password must contain at least one Number (0-9)!";
					aField[0].focus();
				}
				stuff = /[!@#$%^&*]/;
				if(!stuff.test(aField[0].value)){
					valPassed = false;
					msg = "Password must contain at least one special character (!@#$%^&*)!";
					aField[0].focus();
				}
				
				if(!valPassed){
					alert(msg);
					return false;
				}
				else{
					document.forms["register"].submit();
				}
			}
			
		</script>
	</head>
		
	<?
	if(isset($_POST['pw'])){
		if($_POST['pw'] == $_POST['repw']){
			$hash = crypt($_POST['pw'], $salt);
			//input into database
			$sql = "UPDATE `users` SET `Salt`= '$salt', `Hash` = '$hash' WHERE `Username`= '".$user."' AND `UserID` = '".$userId."'";
			if(mysqli_query($con, $sql)){
				echo "<h1>Reset Password Successfully</h1>";
			}
			else{
				echo "<h1>Failed to reset password</h1>";
			}
		}
		else{
			echo "Passwords did not match, try again<BR><BR>" . $form; 
		}
	}
	else{
		// no password entered, show the form
		echo $form;
	}
	
	
	
?>