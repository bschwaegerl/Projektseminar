<html>
<head><title>InnovationTool</title></head> 
<body>
<?php

	$hostname = "localhost"; $user = "root"; $password = ""; $db = "innovation";
	$connection = mysqli_connect($hostname, $user, $password, $db);
	mysqli_set_charset($connection,"utf8");
	
	//clean up websites for the run
	mysqli_query($connection, "DELETE FROM _tmp_websites_actual_run");
	mysqli_query($connection, "ALTER TABLE _tmp_websites_actual_run AUTO_INCREMENT=1");
				
	echo "<form action=\"searchForWords.php\" method =\"post\">";

	if($result = mysqli_query($connection, "SELECT * FROM _websites" ))
				
				
				{				
					while($row = mysqli_fetch_assoc($result)){
					
					echo "<input type='checkbox' id='website' name='websiteList[]' value=\"" . $row['url'] . "\"/>". $row['url'] . "<br>";
			
					}
						mysqli_free_result($result); 
				}
				
				mysqli_close($connection);		
?>

	<br></br>
	<input type="submit" id="btStartSearch" value="Suche beginnen!"/>

</body>
</html>
	