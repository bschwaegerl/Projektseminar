<html> 
<head><title>InnovationTool</title></head> 
	<body>
		<?php
		
			set_time_limit(0);
			
			//database connection
			$connection = buildDatabaseConnection();
			
			if(isset($_POST['löschen'])){
			$updateInnoFoundUrls = "DELETE FROM _innovation_found_urls WHERE id = $_POST[hidden]";

			mysqli_query($connection, $updateInnoFoundUrls);
			}			
			//get table innovation_found
			$innovationFoundTable = getInnoFoundTable($connection);
			
			//get table innovation_found_urls
			$innovationFoundUrlsTable = getInnoFoundUrlsTable($connection);
			
			//build combined table
			buildCombinedTable($innovationFoundTable, $innovationFoundUrlsTable);
			
			//close connection
			mysqli_close($connection);
			
			//build the database connection
			function buildDatabaseConnection(){
				$hostname = "localhost"; $user = "root"; $password = ""; $db = "innovation";
				$connection = mysqli_connect($hostname, $user, $password, $db);
				mysqli_set_charset($connection,"utf8");
				
				return $connection;
			}
			
			//method for getting innovation_found table
			function getInnoFoundTable($connection){
			
			$innovationFound = array();
			$result = mysqli_query($connection,"SELECT * FROM _innovation_found");
			
			while($row = mysqli_fetch_array($result))
			{
				$innovationFound[] = $row;
			}
			return $innovationFound;
			}

			//method for getting innovation_found_urls table
			function getInnoFoundUrlsTable($connection){
			
			$innovationFoundUrls = array();
			$result = mysqli_query($connection,"SELECT * FROM _innovation_found_urls");
			
			while($row = mysqli_fetch_array($result))
			{
				$innovationFoundUrls[] = $row;
			}
			return $innovationFoundUrls;
			}	

			//function for combining two tables and create one big
			function buildCombinedTable($innovationFoundTable, $innovationFoundUrlsTable){
			
			//table initialization 
			echo "<table border='1'>
			<tr>
			<th>Wort</th>
			<th>Url</th>
			<th>Datum</th>
			</tr>";
			
			$count = 0;
			foreach($innovationFoundTable as $innovationFound){
			
				foreach($innovationFoundUrlsTable as $innovationFoundUrls){
					
					//if foreign key equals primary key than draw record
					if($innovationFound[0] == $innovationFoundUrls[1]){
						
						echo "<form acion=showInnovations.php method=post>";
						echo "<tr>";
						echo "<td>" . $innovationFound[1]. "</td>";
						echo "<td> <a href="  . $innovationFoundUrls[2] . "</a>"   . $innovationFoundUrls[2] . "</td>";
						echo "<td>"  . $innovationFoundUrls[3]. "</td>";
						echo "<td> <input type=hidden name=hidden value=" . $innovationFoundUrls[0]. "></td>";
						echo "<td> <input type=submit name=löschen value=löschen></td>";
						echo "</tr>";
						echo "</form>";
					}
				}	
			}
			
			echo "</table>";
			
			}
?>
	<form action="index.html" method ="post">
	<br>
	<input type="submit" name="hauptseite" value="Back to main page">
	</form>
	</body> 
</html>			