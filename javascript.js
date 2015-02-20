<script type="text/javascript">

			var FIELD_COUNT = 30;
			var clicks = 0;

			function checkChecks(){
				showHide('expenses', 'box');
				showHide('cHours', 'cHoursb');
				showHide('cHours2', 'cHoursb2');
				showHide('odo', 'odoc');
				showHide('expenses2', 'box2');
				showHide('moreHours', 'multhours')
			}
			
			function insertEmail(senderName){
				var names = [
					<?
					$count=0;
					$tmp = mysqli_query($con,"SELECT * FROM employees where recap != '' ORDER BY Name");
					while($row = mysqli_fetch_array($tmp)) {
						echo "'".$row['Name'] . "',";
						$count++;
					}
					?>
				''];
				var emails = [
					<?
					$tmp = mysqli_query($con,"SELECT * FROM employees where recap != '' ORDER BY Name");
					while($row = mysqli_fetch_array($tmp)) {
						echo "'".$row['email'] . "',";
					}
					?>
				''];
				var COUNT = <? echo $count; ?>;
				
				for(i=0; i<COUNT; i++){
					if (document.getElementById("nameDrop").value == names[i]){
						document.getElementById("email").value = emails[i];
					}
				}
			}
			
			function showHideName(){
				if (document.getElementById("noList").checked){
					document.getElementById("nameDrop").style.display = 'none';
					document.getElementById("name").style.display = 'inline';
					document.getElementById("email").disabled = false;
				}
				else{
					document.getElementById("nameDrop").style.display = 'inline';
					document.getElementById("name").style.display = 'none';	
					document.getElementById("email").disabled = true;				
				}
			}
			
			function showHide(div_id, sender) {
				if(document.getElementById(sender).checked) {
					//document.getElementById(div_id).style.display = 'block';
					document.getElementById(div_id).style.position = 'relative';
					document.getElementById(div_id).style.left = '0px';
				}
				else {
					//document.getElementById(div_id).style.display='none';
					document.getElementById(div_id).style.position='absolute;';
					document.getElementById(div_id).style.left = '-9999px';
				}
			}
			
			function putToDay(){
				var toDay = new Date();
				var dd = toDay.getDate();
				var mm = toDay.getMonth() + 1; //January is 0!
				var yyyy = toDay.getFullYear();
				
				if(mm < 10){
					document.getElementById("Month").value = "0" + mm;
				}
				else{
					document.getElementById("Month").value = mm;
				}
				
				if(dd < 10){
					document.getElementById("Day").value = "0" + dd;
				}
				else{
					document.getElementById("Day").value = dd;
				}
				
				document.getElementById("Year").value = yyyy;
				
				var ddd = document.getElementById("Day").value;
				var dmm = document.getElementById("Month").value;
				var dyyyy = document.getElementById("Year").value;
				
				dateCheck();
				//*/
			}
			
			function dateCheck(){
				var toDay = new Date();
				var dd = toDay.getDate();
				var mm = toDay.getMonth() + 1; //January is 0!
				var yyyy = toDay.getFullYear();
				var ddd = document.getElementById("Day").value;
				var dmm = document.getElementById("Month").value;
				var dyyyy = document.getElementById("Year").value;
				
				//disable future dates
				//Day
				var op = document.getElementById("Day").getElementsByTagName("option");
				for (var i = 0; i < op.length; i++) {
					if (op[i].value > dd && dmm == mm) {
						op[i].disabled = true;
					}
					else{
						op[i].disabled = false;
					}
				}
				//Month
				var op = document.getElementById("Month").getElementsByTagName("option");
				for (var i = 0; i < op.length; i++) {
					if (op[i].value > mm && dyyyy == yyyy) {
						op[i].disabled = true;
					}
					else{
						op[i].disabled = false;
					}
				}
				//Year
				var op = document.getElementById("Year").getElementsByTagName("option");
				for (var i = 0; i < op.length; i++) {
					if (op[i].value > yyyy) {
						op[i].disabled = true;
					}
					else{
						op[i].disabled = false;
					}
				}
			}
			
			function validate(){
				var success = 1;	//true/false replacement for return false
				var message;		//message to show in alert when validation fails
				clicks++;
				//document.getElementById("upload").disabled = true;	//disable button to reduce duplicates
			
				if(document.getElementById("name").value == "" && document.getElementById("noList").checked){
					message = "Fill out your name";
					success = 0;
				}
				else if(document.getElementById("noList").checked == false && document.getElementById("nameDrop").value == "---Select Name---"){
					message = "Select your name";
					document.getElementById("nameDrop").focus();
					success = 0;
				}
				else if(document.getElementById("email").value == ""){
					message = "Fill out your email";
					document.getElementById("email").focus();
					success = 0;
				}
				else if(document.getElementById("hours").value == ""){
					message = "Fill out your hours";
					document.getElementById("hours").focus();	
					success = 0;
				}
				else if(document.getElementById("hours").value > 24){
					message = "Too many hours, there just isn't enough time in the Day. Let's fix that.";
					document.getElementById("hours").focus();	
					success = 0;
				}
				else if(document.getElementById("job").value == 0){
					message = "Fill out your job";
					document.getElementById("job").focus();				
					success = 0;
				}
				else if(document.getElementById("summary").value == ""){
					message = "Fill out the summary";
					document.getElementById("summary").focus();	
					success = 0;
				}
				for (i=1; i<=FIELD_COUNT; i++) {
					if(document.getElementById("hours" + i).value > 24){
						message = "Too many hours, there just isn't enough time in the Day. Let's fix that.";
						document.getElementById("hours" + i).focus();	
						success = 0;
					}
					if (i<5){
						if(document.getElementById("hoursm" + i).value > 24){
							message = "Too many hours, there just isn't enough time in the Day. Let's fix that.";
							document.getElementById("hoursm" + i).focus();	
							success = 0;
						}
					}
				}
				
				for (i=1; i<=FIELD_COUNT; i++) {
					if(document.getElementById("employee" + i).value != "---Select Employee---" && document.getElementById("hours" + i).value == ""){
						message = "Please enter the number of hours for " + document.getElementById("employee" + i).value + " in the space next to his name.";
						document.getElementById("hours" + i).focus();
						success = 0;
					}
				}
				
				
				if (document.getElementById("startodo").value != "" && document.getElementById("endodo").value == ""){
					message = "Fill out the ending odometer";
					document.getElementById("endodo").focus();	
					success = 0;
				}
				else if (document.getElementById("endodo").value != "" && document.getElementById("startodo").value == ""){
					message = "Fill out the starting odometer";	
					document.getElementById("startodo").focus();
					success = 0;
				}
				if (document.getElementById("endodo").value == document.getElementById("startodo").value && document.getElementById("startodo").value != 0 && document.getElementById("endodo").value != 0){
					message = "What spacecraft did you drive today? Your starting and end odometer are the same.";
					document.getElementById("startodo").focus();
					success = 0;
				}
				
				//check for full name
				var spaceCount = 0;
				var x = document.getElementById("name").value;
				var boolSwitch = true;
				for(i = 0; i < x.length; i++){
					if(x[i] == ' '){
						boolSwitch = false;
						spaceCount++;
					}
				}
				
				
				if(boolSwitch == true && document.getElementById("noList").checked){
					message = "Please enter full name";
					document.getElementById("name").focus();
					success = 0;
				}
				
				//if validation fails, show the message, return false and enable the button for retry
				if(success == 0){
					alert(message);
					document.getElementById("upload").disabled = false;
					document.getElementById("theButton").disabled = false;
					return false;
				}
				else{
					//submit the form
					document.getElementById("email").disabled = false;
					document.forms["recapForm"].submit();
				}
				
				//document.getElementById("upload").disabled = false;
				//document.getElementById("theButton").disabled = true;
				//document.getElementById("theButton").style.position='absolute;';	//hide on success to reduce duplicates
				//document.getElementById("theButton").style.left = '-9999px';
							
			}
			//Capitalize 1st letter in name
			function nameFix(){
				var eName = document.getElementById("name").value;
				document.getElementById("name").value = toTitleCase(eName);
				function toTitleCase(str){
				    return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
				}
			}
			function makeSameJob(){
				//if(document.getElementById("sameJob").checked){
					var theJob = document.getElementById("job").value;
					for(i=1; i<31; i++){
						document.getElementById("job" + i).value = theJob;
					}
					for(i=1; i<31; i++){
						document.getElementById("sjob" + i).value = theJob;
					}
					for(i=1; i<11; i++){
						document.getElementById("ejob" + i).value = theJob;
					}
				//}
			}
			
			
			function showStyle(){
				var toDay = new Date();
				var dd = toDay.getDate();
				var mm = toDay.getMonth() + 1; //January is 0!
				var yyyy = toDay.getFullYear();
				var ddd = document.getElementById("Day").value;
				var dmm = document.getElementById("Month").value;
				var dyyyy = document.getElementById("Year").value;
				var elems = document.getElementsByTagName('input');
				var len = elems.length;
				var elemst = document.getElementsByTagName('textarea');
				var lent = elems.length;
				var elemss = document.getElementsByTagName('select');
				var lens = elems.length;
				
				if(dd != ddd || mm != dmm || yyyy != dyyyy){
					//document.getElementById("messageDiv").style.display = 'block';
					document.getElementById(div_id).style.position = 'relative';
					document.getElementById("messageDiv").style.left = '0px';
					//disable form, show buttons
					document.recapForm.action = "past.php";
					document.getElementById("dateHere").innerHTML = dmm+"-"+ddd+"-"+dyyyy;
					
					for (var i = 0; i < len; i++) {
					    elems[i].disabled = true;
					}
					for (var i = 0; i < lent; i++) {
					    elemst[i].disabled = true;
					}
				}
				else{
					//document.getElementById("messageDiv").style.display='none';
					document.getElementById(div_id).style.position='absolute;';
					document.getElementById("messageDiv").style.left = '-9999px';
					for (var i = 0; i < len; i++) {
					    elems[i].disabled = false;
					}
					for (var i = 0; i < lent; i++) {
					    elemst[i].disabled = false;
					}
				}
			}
			
		</script>