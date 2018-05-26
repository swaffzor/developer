<?
	session_start();
	include("database.php");
	include("functions.php");
	include("nav2.php");
	//! TODO: 
	//reset password link
	
/*
	echo "<pre>POST ";
	print_r($_GET);
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
	
	
	?>
	<head>
		<link rel='stylesheet' href='mystyle.css'>
	</head>
	<?
	if(!isset($_GET['forgotpw'])){
		echo "<h1>Login</h1>";
		$form = '<form name="test" action="login.php" method="post">
			<input type="email" name="email" placeholder="email">
			<input type="password" name="pw" placeholder="password">
			<input type="submit" value="Login"><br>
			<a style="Font-size:small;" href="login.php?forgotpw=1">Forgot Password?</a>
		</form>';
	}
	else if($_GET['forgotpw'] == 1){
		echo "<h1>Forgot PW</h1>";
		$form = '<form name="test" action="login.php" method="get">
			<input type="text" name="email" placeholder="email">
			<input type="hidden" name="forgotpw" value="2">
			<input type="submit" value="Send PW Reset">
			</form>';
	}
	else if($_GET['forgotpw'] == 2){
		$legit = 0;
		$check = mysqli_query($con,"SELECT * FROM users WHERE EmailAddress = '". $_GET['email'] ."' Limit 1");
		while($row = mysqli_fetch_array($check)) {
			if($row['EmailAddress'] == $_GET['email']){
				$legit = 1;
				$userName = $row['Username'];
			}
		}
		
		if($legit){		
			$ranNum = rand();
			$msg = 'To reset your password <a href="http://tsidisaster.net/beta/resetpw.php?resetpw='.$ranNum.'">Click here</a>';
			$sql = "UPDATE `users` SET `ResetPW`= '".$ranNum."' WHERE `Username`= '".$userName."' AND `EmailAddress` = '".$_GET['email']."'";
			if(mysqli_query($con, $sql)){
				echo "<h1>Reset Password Process Started</h1>";
			}
			else{
				echo "<h1>Failed to reset password</h1>";
			}
			$headers2 = "MIME-Version: 1.0" . "\r\n";
			$headers2 .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
			$headers2 .= "From: robot@tsidisaster.net" . "\r\n" . "bcc: jeremy@tsidisaster.com";
			if(mail($_GET['email'], "Reset Recap Password", $msg, $headers2)){
				echo "<h1>recovery email sent to ".$_GET['email']."</h1>";
			}
			else{
				echo "<h1>ERROR</h1>";
			}
		}
		else{
			echo "<h2>No user account found with email: ".$_GET['email'] ."</h2>";
		}

	}
	
	if(isset($_POST['pw'])){
		//attempted login
		$userExists = 0;
		$results = mysqli_query($con,"SELECT * FROM users WHERE EmailAddress = '". $_POST['email'] ."' Limit 1");
		while($row = mysqli_fetch_array($results)) {
			$userExists = 1;
			if (hash_equals(crypt($_POST['pw'], $row['Salt']), $row['Hash'])){
				//successfully logged in
				$_SESSION['User'] = $row['Username'];
				$_SESSION['LoggedIn'] = 1;
				$_SESSION['userID'] = $row['UserID'];
				$_SESSION['Permissions'] = $row['Permissions'];
				//meta refresh to sender page 
				echo "Welcome back " . $row['Username'] . "<br>";
				echo "You should be automatically re-directed, if not click <a href='".$_SESSION['sender']."'>here</a><br>";
				echo "<meta http-equiv='refresh' content='1;URL=$sender'>";
			}
			else{
				//password did not match
				echo "Login credentials did not match, try again<br>" . $form;
			}
		}
		if(!$userExists){
			echo "Login credentials did not match, try again<br>" . $form;
		}
		
	}
	else{
		// not logged in, show the login form
		echo $form;
	}
	
	
	
?>