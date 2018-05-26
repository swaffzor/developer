<?
	session_start();
	include_once 'database.php';
	include_once("functions.php");
	//$extra is additional links to put in the nav bar that needs to preceed nav2.php
	$extra = "<li>
			<a class='button' href='javascript:AlertIt()'>Equipment</a>
			</li><li><a class='button' href = 'resetpw.php'>Password</a></li>";
	include_once 'nav2.php';	

/*
	echo "<pre>POST ";
	print_r($_GET);
	echo "<br>SESSION ";
	print_r($_SESSION);
	echo "</pre>";
*/

	
	?>
	<html>
		<head>
			<link rel="stylesheet" href="mystyle.css">
			<script type="text/javascript">
				function AlertIt(){
					alert("Coming Soon...");
				}
			</script>
		</head>
		<body>
			
	<?
		
		echo "<form name='data' action='index.php' method='post'><table cellspacing='15px'><tr><td align='center'>";
	
	?>
	
	<div id="history" style="width: 100px"><?
		echo "<h3>Recap History</h3><p>yyyy-mm-dd</p>";
		
		$results = mysqli_query($con,"SELECT * FROM Data WHERE Name = '".$_SESSION['User']."' ORDER BY Date DESC");
		while($row = mysqli_fetch_array($results)) {
			echo "<a href=account.php?param=".$row['id'].">".$row['Date']."<BR>";
		}
		?>
		</td>
	</div>
	<td valign='top'>
	<?
	$single = mysqli_query($con,"SELECT * FROM Data WHERE Name = '".$_SESSION['User']."' AND id = '".$_GET['param']."' ");
	while($row2 = mysqli_fetch_array($single)) {
		echo "<h1>Recap for ". date('l Y-m-d', strtotime($row2['Date'])) . "</h1>";
		echo "<h2>Submitted on " . $row2['Submitted'] . "</h2>";
		echo $row2['Summary'] . "<BR>";
		
/*
		echo "<h1 style='color:red;'>Caution: Work in Progress</h1>";
		$test = ParseSummary($_SESSION['User'], $row2['Summary']);
		
		echo "<pre>";
		print_r($test);
		echo "</pre>";
		foreach($test as $key => $value){
			echo "<input type='hidden' name='$key' value='$value'>";
		}
		echo "<textarea cols='75' rows='10' visible='false' type='text' name='summary'>".$test["summary"]."</textarea>";
		echo "<button>Edit</button>";
*/
		
	}
	
	echo "</td></tr>";
	?>

	</table></form>
	</body>
	</html>
	