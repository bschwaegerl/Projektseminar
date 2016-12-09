<html>
<head><title>Edit Websites</title></head> 
<body>
	
	<?php
		if(isset($_POST['website'])){
			
			$connection = buildDatabaseConnection();
			mysqli_query($connection, "INSERT INTO _websites");
			echo $_POST['website']." was added to table _websites!";
		}else{
	?>
			<form action="editWebsites.php" method="POST">
				Insert the new website:<br>
					<input type="url" name="website">
					<input type="submit" name="btInsert" value="Save website">
			</form>
	<?php
		}
		
		function buildDatabaseConnection(){
			$hostname = "localhost"; $user = "root"; $password = ""; $db = "innovation";
			$connection = mysqli_connect($hostname, $user, $password, $db);
			mysqli_set_charset($connection,"utf8");
				
			return $connection;
		}
	?>
</body>
</html>