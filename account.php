<?
	session_start();
	include_once 'database.php';
	include_once 'nav.php';	
	
	echo "<form name='data' action='account.php' method='post'><table cellspacing='15px'><tr><td align='center'>";
	
	?>
	
	<div id="history" ><?
		echo "<h3>Recap History</h3><p>yyyy-mm-dd</p>";
		
		$results = mysqli_query($con,"SELECT * FROM Data WHERE Name = '". $_SESSION['User'] ."' ORDER BY Date DESC");
		while($row = mysqli_fetch_array($results)) {
			echo "<a href=account.php?param=".$row['Date'].">".$row['Date']."<BR>";
		}
		?>
		</td>
	</div>
	<td valign='top'>
	<?
	$single = mysqli_query($con,"SELECT * FROM Data WHERE Name = '". $_SESSION['User'] ."' AND Date = '".$_GET['param']."' ");
	while($row2 = mysqli_fetch_array($single)) {
		echo "<h1>Recap for ".$row2['Date'] . "</h1>";
		echo "<h2>Submitted on " . $row2['Submitted'] . "</h2>";
		echo $row2['Summary'];
	}
	
	echo "</td></tr>";
	?>

	</table></form>