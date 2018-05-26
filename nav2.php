<?	
	session_start();
	
	echo '<div name="links" style="font-family:sans-serif">
		<img src="http://tsidisaster.net/images/logo_simple.jpg"><span style="color:red">BETA</span> ';
		if($_SESSION['LoggedIn'] == 1){
			$extra .= '<li><a class="button" href="account.php">'. $_SESSION['User'] .'</a></li><li><a class="button"  href="logout.php">Log out</a></li>';
			//getnavlinks	
			$nav = GetNavLinks($con, $_SESSION['userID']);
			echo '<nav class="row"><div class="large-12 columns">'; 
			echo '<ul>
					<li class="dropdown">
						<a href="#" class="dropbtn">Menu</a>
						<div class="dropdown-content">	
							'.$nav.'
						</div>
					</li>
					<li>
					'.$extra.'
					</li>
				</ul>';
		    echo '</div></nav>';	
			
		}
		else{
			echo '<br>
			<ul>
				<li><img src="http://tsidisaster.net/developer/old/images/square.png" width="30">&nbsp;&nbsp;</li>
				<li class="dropdown">
						<a href="login.php">Login</a>&nbsp;&nbsp;
						<a href="register.php">Register</a>&nbsp;&nbsp;
				</li>
			</ul>';
		}
	echo '</div><br>';
	
?>

