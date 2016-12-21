<?php
	
	set_time_limit(0);			

	//Get all checked Words
	if (isset($_POST['checkList'])){
		$checkedWords = $_POST['checkList'];
	}else{
		$checkedWords = NULL;
	}
	
	//eliminate all duplicates
	$checkedWords = array_unique($checkedWords);
	
	//Get all Words
	if (isset($_POST['checkListHidden'])){
		$allWords = $_POST['checkListHidden'];
	}else{
		$allWords = NULL;
	}
	//build database connection
	$connection = buildDatabaseConnection();
	
	//get all unchecked words
	$uncheckedWords = getUncheckedWords($allWords, $checkedWords);
		
	//insert unchecked words into blacklist and mark all checked words as innovations
	insertIntoDatabase($connection, $uncheckedWords, $checkedWords);
	
	//deletes everything from table _wesbties_searched and table _innvation_check
	cleanupUpEnvironment($connection, $allWords);
	
	mysqli_close($connection);	

	//build database connection
	function buildDatabaseConnection(){
		
		$hostname = "localhost"; $user = "root"; $password = ""; $db = "innovation";
		$connection = mysqli_connect($hostname, $user, $password, $db);
		mysqli_set_charset($connection,"utf8");
				
		return $connection;
	}
	
	//insert words into blacklist ($uncheckedWord) and add words to _innovation_found / _innovation_found_urls ($checkedWords)
	function insertIntoDatabase($connection, $uncheckedWords, $checkedWords){

		//add words to blacklist
		foreach($uncheckedWords as $key=>$uncheckedWord){
			$tableName = get_table_name($uncheckedWord);
			if(mysqli_query($connection, "INSERT INTO ".$tableName."(word) VALUES ('".$uncheckedWord."')" )){
				
			
			}else{
				echo $uncheckedWord." konnte nicht in die Blacklist gespeichert werden. <br>";
			}
		}
		
		//add checked words to innovation_found and innovation_found_urls
		if(!empty($checkedWords)){
			foreach($checkedWords as $key=>$checkedWord) {
			
					$result = mysqli_query($connection, "SELECT url FROM _innovation_check where word = '" .$checkedWord."'");
				
					$allUrlsOfCheckedWords = array();
				
					while($row = mysqli_fetch_array($result)){
					$allUrlsOfCheckedWords[] = $row[0];
					}
			
					//insert into _innovation_found if not exists			
					if(mysqli_query($connection, "INSERT INTO _innovation_found (word) 
						SELECT * FROM (SELECT '".$checkedWord."') as tmp WHERE NOT EXISTS
					(SELECT * FROM _innovation_found where word = '".$checkedWord."') LIMIT 1" )){
				
					echo $checkedWord. " wurde als Innovation gespeichert. <br>";
					
					}else{
						echo $checkedWord. " existiert bereits als Innovation. <br>";
					}
			
					//current system date
					date_default_timezone_set('Europe/Berlin');
					$date = date("Y-m-d H:i:s", time());
			
					foreach($allUrlsOfCheckedWords as $url){

						//insert into _innovation_found_url with foreign key _innovation_found_id if not exists
						if(mysqli_query($connection, "INSERT INTO _innovation_found_urls (innovation_found_id, url, date) 
						SELECT 
						(SELECT id from _innovation_found where word = '".$checkedWord."'),
						'".$url."',
						'".$date."' WHERE NOT EXISTS
						(SELECT * from _innovation_found_urls where url = 
						(SELECT url FROM _innovation_check where word = '".$checkedWord."' AND url ='".$url."' LIMIT 1)				
						AND innovation_found_id =
						(SELECT id from _innovation_found where word = '".$checkedWord."'))" )){
					
							echo "Die Url zur Innovation ==> ".$checkedWord ." wurde gespeichert! <br>";
					
						}else{
					
							echo "Die Url zur Innovation ==> ".$checkedWord ." ist bereits vorhanden! <br>";
						}
					}	
				}	
		}
	}
	
	
	//compares  every word of _innovation_check with the checkedWord array . if word of all words is in checkedWOrdArray it will be deleted
	function getUncheckedWords($allWords, $checkedWords){
		
		foreach($allWords as $key=>$word){
			
			if(!empty($checkedWords)){
				if(in_array($word, $checkedWords)){
					unset($allWords[$key]);
				}
			}	
		}
		return $allWords;
	}
	
	function get_table_name($word){
				
		$firstLetter = mb_substr($word,0,1,"UTF-8");
		$firstLetter = mb_strtolower($firstLetter, "UTF-8");
			
		return $firstLetter;
			
	}
	
	//function for cleaning up the environment
	function cleanupUpEnvironment($connection, $allWords){
		foreach($allWords as $handledWord){
			mysqli_query($connection, "DELETE FROM _innovation_check where word = '".$handledWord."'");
			
		}
	}
		
	
	?>
<html>
	<head><title>Inserted into Database</title></head> 
	<body>
		<br><br>
		Die (restlichen) Wörter wurden der Blacklist hinzugefügt.
		
		<form action="index.html" method ="post">
			<br>
			<input type="submit" name="hauptseite" value="Zurück zur Startseite">
		</form>
	</body>
</html>