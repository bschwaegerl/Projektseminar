<?php
	
	//Get all checked Words
	if (isset($_POST['checkList'])){
		$checkedWords = $_POST['checkList'];
	}else{
		$checkedWords = NULL;
	}
	
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
	cleanupUpEnvironment($connection);
	
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
				echo $uncheckedWord."failed adding word to Blacklist";
			}
		}
		//add checked words to innovation_found and innovation_found_urls
	if(!empty($checkedWords)){
		foreach($checkedWords as $key=>$checkedWord) {
			
			//insert into _innovation_found if not exists			
			if(mysqli_query($connection, "INSERT INTO _innovation_found (word) 
				SELECT * FROM (SELECT '".$checkedWord."') as tmp WHERE NOT EXISTS
			(SELECT * FROM _innovation_found where word = '".$checkedWord."') LIMIT 1" )){
				
				echo $checkedWord. " added to _innovation_found!";
			}else{
				echo $checkedWord. " existiert bereits in der Tabelle _innovation_found";
			}
			
			//current system date
			date_default_timezone_set('Europe/Berlin');
			$date = date('d/m/Y H:i:s', time());
			echo $date;
			
			//insert into _innovation_found_url with foreign key _innovation_found_id if not exists
			if(mysqli_query($connection, "INSERT INTO _innovation_found_urls (innovation_found_id, url, date) 
					SELECT * FROM (SELECT
				(SELECT id from _innovation_found where word = '".$checkedWord."'),
				(SELECT url from _innovation_check where word = '".$checkedWord."'),
				'".date("Y-m-d H:i:s", time())."') as tmp LIMIT 1" )){
					
					echo $checkedWord ." added to _innovation_found_urls!";
					
			}else{
					echo "FOREIGNKEY FAILURE";
			}
			
		
		}
	}
	}
	
	
	//compares  every word of _innovation_check with the checkedWord array . if word of all words is in checkedWOrdArray it will be deleted
	function getUncheckedWords($allWords, $checkedWords){
		
		foreach($allWords as $key=>$word)
		{
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
	function cleanupUpEnvironment($connection){
		
		mysqli_query($connection, "DELETE FROM _innovation_check");
		mysqli_query($connection, "ALTER TABLE _innovation_check AUTO_INCREMENT=1");

		mysqli_query($connection, "DELETE FROM _tmp_websites_actual_run");
		mysqli_query($connection, "ALTER TABLE _tmp_websites_actual_run AUTO_INCREMENT=1");

	}
		
	
	?>