<?
	session_start();
	include_once 'nav.html';
	require_once 'database.php';
	
	date_default_timezone_set ("America/New_York");
	
	$tmp = mysqli_query($eqcon,"SELECT * FROM EquipmentInspections WHERE ID =".$_GET['ID']);
	while($row = mysqli_fetch_array($tmp)) {
		$rawHTML.= "<h2>Submitted: " . $row['Submitted'] . "</h2>";
		$rawHTML.= $row['rawHTML'];
	}	

	$illegals = array("'",'"');
	$replacements = array("&#39", "&#34");
	$rawHTML = str_replace($replacements, $illegals, $rawHTML);

?>
<html>
	<head>
		<style>
			Body{
				Background-color: ;
				Font-family: sans-serif;
				Color: black;}
		</style>
		<title>Equipment Inpection Report</title>
		
		<script type="text/javascript">
			
		</script>
		
	</head>
	<body>
		<table cellspacing="20px">
			<tr>
				<td valign="top" align="center">
					<a href='equipment_landing.php'><button>Reset Links</button></a>
				</td>
				<td rowspan="2">
					<?
						if($_GET['ID'] != ""){							
							echo $rawHTML;
						}		
					?>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<?
						echo $_SESSION['links'];
					?>
				</td>
			</tr>
		</table>
</body>
</html>
