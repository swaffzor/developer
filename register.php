<?
	session_start();
	include("database.php");
	include("functions.php");
	include("nav2.php");
	//! TODO: 
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
	
	$form = '<form name="register" action="register.php" method="post">
		<input type="text" name="fname" id="first_name" placeholder="First Name">
		<input type="text" name="lname" id="last_name" placeholder="Last Name"><br>
		<div style="color:gray; Font-size:small;">
		Password must be:<br>
		* At least 8 characters long<br>
		* Contain at least 1 upper case letter, 1 lower case letter<br>
		* At least 1 special charachter (!@#$%^&*)<br>
		* At least 1 number 0-9
		</div>
		<input type="password" name="pw" id="password" placeholder="password">
		<input type="password" name="repw" id="repassword" placeholder="retype password"><br>
		<input type="text" name="email" id="email" placeholder="email"><br>
		<input type="button" value="Register" onclick="validate()">
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
				
				aField[0] = document.getElementById("first_name");
				aField[1] = document.getElementById("last_name");
				aField[2] = document.getElementById("password");
				aField[3] = document.getElementById("repassword");
				aField[4] = document.getElementById("email");

				if(aField[2].value != aField[3].value){
					valPassed = false;
					msg = "Password did not match";
					aField[2].focus();
				}
				
				for(i=0; i<aField.length; i++){
					if(aField[i].value == ""){
						valPassed = false;
						msg = aField[i].id + " is empty, all fields are required";
						aField[i].focus();
						break;
					}
				}
				
				if(aField[2].value.length < 8){
					valPassed = false;
					msg = "Password length is too short";
					aField[2].focus();
				}
				stuff = /[a-z]/;
				if(!stuff.test(aField[2].value)){
					valPassed = false;
					msg = "Password must contain at least one lowercase letter (a-z)!";
					aField[2].focus();
				}
				stuff = /[A-Z]/;
				if(!stuff.test(aField[2].value)){
					valPassed = false;
					msg = "Password must contain at least one uppercase letter (A-Z)!";
					aField[2].focus();
				}
				stuff = /[0-9]/;
				if(!stuff.test(aField[2].value)){
					valPassed = false;
					msg = "Password must contain at least one Number (0-9)!";
					aField[2].focus();
				}
				stuff = /[!@#$%^&*]/;
				if(!stuff.test(aField[2].value)){
					valPassed = false;
					msg = "Password must contain at least one special character (!@#$%^&*)!";
					aField[2].focus();
				}
				
				stuff = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				if(!stuff.test(aField[4].value)){
					valPassed = false;
					msg = "email is invalid, please enter a valid email address";
					aField[4].focus();
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
			$sql = "INSERT INTO users (EmailAddress, Salt, Hash, Permissions, Username, Verified) VALUES ('".trim($_POST['email'])."', '$salt', '$hash', '0', '". ucfirst(trim($_POST['fname'])) . " " . ucfirst(trim($_POST['lname']))."', '0')";
			if(mysqli_query($con, $sql)){
				$msg = ucfirst(trim($_POST['fname'])) . " " . ucfirst(trim($_POST['lname'])) . " has registered to the Recap site with the email ". trim($_POST['email']);
				$headers2 = "MIME-Version: 1.0" . "\r\n";
				$headers2 .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
				$headers2 .= "From: robot@tsidisaster.net";
				mail("jeremy@tsidisaster.com", "New User Registered", $msg, $headers2);
				echo "<h1>Registered Successfully</h1>";
				echo "<meta http-equiv='refresh' content='1;URL=login.php'>";
			}
			else{
				echo "<h1>Failed Registration</h1>";
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