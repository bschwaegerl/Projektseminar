<?php
	
	//Get all checked Words
	if (isset($_POST['checkList'])){
		$checkedWords = $_POST['checkList'];
	}else{
		$checkedWords = NULL;
	}

		print_r($checkedWords);	
	//Get all Words
	if (isset($_POST['checkListHidden'])){
		$allWords = $_POST['checkListHidden'];
	}else{
		$allWords = NULL;
	}
	$uncheckedWords = getAllUncheckedWords($allWords, $checkedWords);
	
	$connection = buildDatabaseConnection();
	
	//insertIntoDatabase($connection, $uncheckedWords, $checkedWords);
	
	mysqli_close($connection);
	
	echo "Die W&ouml;rter wurden in die Datenbank gespeichert! <br>";
	
	function getAllUncheckedWords($allWords, $words){
		
		$words = deleteUrls($words);
		print_r($words);
			foreach($allWords as $key=>$word){
				echo $word;
				if(in_array($word, $words, true)){
					
					unset($allWords[$key]);
				}
	
			}
		
		print_r($allWords);
	return $allWords;
	}

	function buildDatabaseConnection(){
				$hostname = "localhost"; $user = "root"; $password = ""; $db = "innovation";
				$connection = mysqli_connect($hostname, $user, $password, $db);
				mysqli_set_charset($connection,"utf8");
				
				return $connection;
			}
	
	function insertIntoDatabase($connection, $uncheckedWords, $checkedWords){
		foreach($uncheckedWords as $key=>$word){
			$tableName = get_table_name($word);
			if(mysqli_query($connection, "INSERT IGNORE INTO ".$tableName."(word) VALUES ('".$word."')" )){
				echo $word;
			}else{
				echo "failed";
			}
		}
	}
	
	function get_table_name($word){
				
			$firstLetter = mb_substr($word,0,1,"UTF-8");
			$firstLetter = mb_strtolower($firstLetter, "UTF-8");
			
			return $firstLetter;
			
			}
		
	function deleteUrls($words){
		foreach($words as $key=>$singleWord){
			$singleWord = substr($singleWord,0,strpos($singleWord,' ')+1);
		
			$words[$key] = $singleWord;
		}
	
		return $words;
	}
	
	?>