<?php
	
	//Get all checked Words
	$checkedWords = $_POST['checkList'];
	
	print_r(array_values($checkedWords));
	
	//Get all Words
	$allWords = $_POST['checkListHidden'];
	
	$uncheckedWords = getAllUncheckedWords($allWords, $checkedWords);
	
	$connection = buildDatabaseConnection();
	
	insertIntoDatabase($connection, $uncheckedWords, $checkedWords);
	
	mysqli_close($connection);
	
	echo "Die W&ouml;rter wurden in die Datenbank gespeichert! <br>";
	
	function getAllUncheckedWords($allWords, $words){
		print_r(array_values($words));
		if(isset($allWords)){
			foreach($allWords as $key=>$word){
				if(in_array($word, $words)){
					unset($allWords[$key]);
				}
	
			}
		}
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
		
	
	
	?>