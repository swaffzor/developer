<?
	require_once 'database.php';
	require_once 'globals.php';
	
	session_start();
	$_SESSION['sender'] = $URL;
	//old version: PHP Version 5.4.45
	echo "this is the test page<Br>";
	
	?>
	
	<a href="login.php">Login</a>
	
	<?	
	
	///////////////for registration purposes////////////////////
	echo "<fieldset>";
	$cost = 10;
	$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
	$salt = sprintf("$2a$%02d$", $cost) . $salt;
	echo "salt: " . $salt . "<br>";
	$hash = crypt('test01', $salt);
	echo "hash: " . $hash . "</fieldset>";
	////////////////////////////////////////////////////////////
	
	$form = '<form name="test" action="test.php" method="post">
		<input type="text" name="email" placeholder="email">
		<input type="password" name="pw" placeholder="password">
		<input type="submit">
	</form>';
	
	if(isset($_POST['pw'])){
		//attempted login
		$results = mysqli_query($con,"SELECT * FROM users WHERE EmailAddress = '". $_POST['email'] ."' Limit 1");
		while($row = mysqli_fetch_array($results)) {
			if (hash_equals(crypt($_POST['pw'], $row['Salt']), $row['Hash'])){
				//successfully logged in
				echo "worked<Br>";
			}
			else{
				echo "did not work<br>";
				//password did not match
			}
		}
		
	}
	else{
		// not logged in, show the login form
		echo $form;
	}
?>