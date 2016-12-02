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
	
	cleanupUpEnvironment($connection);
	
	mysqli_close($connection);	

	
	function buildDatabaseConnection(){
				$hostname = "localhost"; $user = "root"; $password = ""; $db = "innovation";
				$connection = mysqli_connect($hostname, $user, $password, $db);
				mysqli_set_charset($connection,"utf8");
				
				return $connection;
			}
	
	function insertIntoDatabase($connection, $uncheckedWords, $checkedWords){
		
		
		//add words to blacklist
		foreach($uncheckedWords as $key=>$uncheckedWord){
			$tableName = get_table_name($uncheckedWord);
			if(mysqli_query($connection, "INSERT IGNORE INTO ".$tableName."(word) VALUES ('".$uncheckedWord."')" )){
			}else{
				echo "failed";
			}
		}
		//add checked words to innovation_found and innovation_found_urls
		foreach($checkedWords as $key=>$checkedWord) {
						 
			if(mysqli_query($connection, "INSERT INTO _innovation_found (word) 
				SELECT * FROM (SELECT '".$checkedWord."') as tmp WHERE NOT EXISTS
			(SELECT * FROM _innovation_found where word = '".$checkedWord."') LIMIT 1" )){
			
			//current system date
			date_default_timezone_set('Europe/Berlin');
			$date = date('d/m/Y H:i:s', time());
			
				if(mysqli_query($connection, "INSERT INTO _innovation_found_urls (innovation_found_id, url, date) 
					SELECT * FROM (SELECT
				(SELECT id from _innovation_found where word = '".$checkedWord."'),
				(SELECT url from innovation_check where word = '".$checkedWord."'),
				'".date("Y-m-d H:i:s", time())."') as tmp
				WHERE NOT EXISTS
				(SELECT * FROM _innovation_found_urls where url = 
				(SELECT url from innovation_check where word = '".$checkedWord."')) LIMIT 1" )){
					
					echo $checkedWord ." added to _innovation_found_urls!";
			}else{
				echo "FOREIGNKEY FAILURE";
			}
			echo $checkedWord. " added to _innovation_found!";
			}else{
				echo "PRIMARYKEY FAILURE";
			}
			
		}
	}
	
	function getUncheckedWords($allWords, $checkedWords){
		
		foreach($allWords as $key=>$word)
		{
			if(in_array($word, $checkedWords)){
				unset($allWords[$key]);
			}
				
		}
		return $allWords;
	}
	
	function get_table_name($word){
				
			$firstLetter = mb_substr($word,0,1,"UTF-8");
			$firstLetter = mb_strtolower($firstLetter, "UTF-8");
			
			return $firstLetter;
			
			}
			
	function cleanupUpEnvironment($connection){
		
		mysqli_query($connection, "DELETE FROM innovation_check");

		mysqli_query($connection, "DELETE FROM _websites_searched");

	}
		
	
	?>