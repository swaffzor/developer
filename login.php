<?
	session_start();
	include("database.php");
	include("nav.php");
	
	echo "<pre>POST ";
	print_r($_GET);
	echo "<br>SESSION ";
	print_r($_SESSION);
	echo "</pre>";
	
	if(empty($_GET['sender'])){
		$sender = "index.php";
	}
	else{
		$sender = $_GET['sender'];
	}
	
	$form = '<form name="test" action="login.php" method="post">
		<input type="text" name="email" placeholder="email">
		<input type="password" name="pw" placeholder="password">
		<input type="submit" value="Login">
	</form>';
	
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
	
	/*
	///////////////for registration purposes////////////////////
	echo "<fieldset>";
	$cost = 10;
	$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
	$salt = sprintf("$2a$%02d$", $cost) . $salt;
	echo "salt: " . $salt . "<br>";
	$hash = crypt('test01', $salt);
	echo "hash: " . $hash . "</fieldset>";
	////////////////////////////////////////////////////////////
	*/
	
?>