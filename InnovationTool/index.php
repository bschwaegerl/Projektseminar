<html> 
<head><title>InnovationTool</title></head> 

<body>
	<?php
		$hostname = "localhost"; $user = "root"; $password = ""; $db = "inno_blacklist";
		$connection = mysqli_connect($hostname, $user, $password, $db);
			if ($result = mysqli_query($connection, "SELECT * FROM a")){ while ($row = mysqli_fetch_array($result)){
				echo utf8_encode(("ID: ".$row[0].", Name: ".$row[1]."<br />")); }
		mysqli_free_result($result); }
	mysqli_close($connection); ?>
</body> 
</html>