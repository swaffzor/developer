<?		
	$pword = "brucewayne";
	$expire = time() + (60*60*24*90); // 3 months
	
	if($_POST['remember'] == "on" && $_POST['pword'] == $pword){
		setcookie("rememberme", $pword, $expire);
	}
		//setcookie("rememberme", "false");
	/*
	echo "<pre>";
	print_r($_POST);
	print_r($_COOKIE);
	echo "</pre>";
	*/
	
	$page_to_display = "
		<form name='test' action='last.php' method='post' >
			<input type='password' name='pword'>Password
			<br><input type='checkbox' id='remember' name='remember'><label for='remember'>Remember me for 3 months</label><br>
	";
			
		foreach($_POST as $key => $value){
			if($key != "submit" && $key != "pword"){
				$page_to_display.= "<input type='hidden' name='$key' value='$value'>";
			}
		}
		
		$page_to_display.= "<input type='submit'>
			</form><br>
			If you do not know the password, contact Marc Junker
		";
	
	if($_COOKIE['rememberme'] != $pword){	
		if($_POST['pword'] == ""){
			include("nav.html");
			echo $page_to_display;
		}
		elseif($_POST['pword'] == $pword){
			include("viewer.php");
		}
		else{
			include("nav.html");
			echo "<i>Incorrect Password</i>";
			echo $page_to_display;
		}
	}
	else{
		include("viewer.php");
	}
?>