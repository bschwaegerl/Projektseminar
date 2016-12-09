<html>
<head><title>Edit Websites</title></head> 
<body>
	
	<?php
		if(isset($_POST['website'])){
			
			$newWebsite = $_POST['website'];
			$connection = buildDatabaseConnection();
			
			//saves the url in the table _websites IF NOT EXISTS
			if(mysqli_query($connection, "INSERT INTO _websites (url) SELECT * FROM (SELECT '".$newWebsite."') as tmp WHERE NOT EXISTS
			(SELECT * FROM _websites where url = '".$newWebsite."') LIMIT 1")){
				
				echo $newWebsite." was added to table _websites!";
				
				mysqli_close($connection);
			}
		}elseif(isset($_POST['deleteCheckList'])){
			
			$websitesToDelete = $_POST['deleteCheckList'];
			$connection = buildDatabaseConnection();
			
			foreach($websitesToDelete as $websiteDel){
				
				if(mysqli_query($connection, "DELETE FROM _websites WHERE url = '".$websiteDel."'")){
				
					echo $websiteDel." was deleted from table _websites! <br>";
				
					
				}
			}
			
			mysqli_close($connection);
				
		}else{
	?>
			<form action="editWebsites.php" method="POST">
				Insert the new website:<br>
					<input type="url" name="website">
					<input type="submit" name="btInsert" value="Save website">
			</form>
			
			<br><br><br><br>
			
			<form action="editWebsites.php" method="POST">
	<?php
			// build form with chechboxes to erase url if wanted
			$connection = buildDatabaseConnection();
			$allMainUrls = getAllMainUrls($connection);
			
			//create table
			echo "<table border=1>
					<tr>
					<th></th>
					<th>Select URLs to delete</th>
					</tr>";
					
				foreach($allMainUrls as $key=>$mainUrl){
		
					//Tabellentupel pro MainURL
					echo "<tr>";
					echo "<td> <input type='checkbox' id='chkbx' name='deleteCheckList[]' value=\"".$mainUrl."\"/> </td>";
					echo "<td>" .$mainUrl. "</td>";
					echo "</tr>";
				
				}
	?>
			</table>
				<input type="submit" name="btDelete" value="Delete website">
			</form>
	
	<?php
	
		}
		
		function getAllMainUrls($connection){
			
			$allMainUrls = array();
			
			$result = mysqli_query($connection,"SELECT url FROM _websites");

			
			while($row = mysqli_fetch_array($result))
			{
				$allMainUrls[] = $row['url'];
			}
			
			return $allMainUrls;
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