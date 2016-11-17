<html> 
<head><title>InnovationTool</title></head> 

<body>
	<?php
		$searchedWord = "Auto";
		$tableName = get_table_name($searchedWord);
		
		
		$hostname = "localhost"; $user = "root"; $password = ""; $db = "inno_blacklist";
		
		$connection = mysqli_connect($hostname, $user, $password, $db);
			if ($result = mysqli_query($connection, "SELECT * FROM $tableName where word = '".$searchedWord."' ")){ 
				while ($row = mysqli_fetch_array($result)){
					echo utf8_encode(("ID: ".$row[0].", Name: ".$row[1]."<br />"));
					}
			mysqli_free_result($result); 
			}
			else {
				echo "0 results";
			}
	mysqli_close($connection); 
	
	
	function get_table_name($word){
		
		return substr($word,0,1);
		
	}
	
	?>
</body> 
</html>