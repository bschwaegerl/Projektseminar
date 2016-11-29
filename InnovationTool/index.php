<html> 
<head><title>InnovationTool</title></head> 

<body>
	<?php
		include_once('simple_html_dom.php');
		set_time_limit(0);
		
		$url = $_POST['websitelink'];
		
		//komplettes html file
		$text = file_get_html($url)->plaintext;
				
		//Sonderzeichen umwandeln
		$text = preg_replace('~&auml;~', 'ä', $text);
		$text = preg_replace('~&ouml;~', 'ö', $text);
		$text = preg_replace('~&uuml;~', 'ü', $text);
		$text = preg_replace('~&szlig;~', 'ß', $text);
		$text = preg_replace('~&Auml;~', 'Ä', $text);
		$text = preg_replace('~&Ouml;~', 'Ö', $text);
		$text = preg_replace('~&Uuml;~', 'Ü', $text);
		
		$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
fwrite($myfile, $text);
fclose($myfile);

		 //$text befüllen und Sonderzeichen durch Leerzeichen ersetzen
		$text = preg_replace('/[^\p{Latin}\s]/u', ' ', $text);
		//Leerzeichen einfügen
		$text = preg_replace('/(?<!\ )[A-Z]/', ' $0', $text); 
		//Mehrfachleerzeichen zu einem umwandeln
		$text = preg_replace('!\s+!', ' ', $text);
		

		//String in Array umwandeln
		 $wordsOfWebsite = explode(" ",$text);
		//Duplikate entfernen
		$wordsOfWebsite = array_unique(array_values($wordsOfWebsite)); 
		
		echo "Wörter insgesamt: " . count($wordsOfWebsite) . '<br>'; 
		
		//Datenbankverbindung aufbauen
		$hostname = "localhost"; $user = "root"; $password = ""; $db = "inno_blacklist";
		$connection = mysqli_connect($hostname, $user, $password, $db);
		
		foreach($wordsOfWebsite as $key=>$searchedWord){
					
		$tableName = get_table_name($searchedWord);
		
			$searchedWord = utf8_decode($searchedWord);
			
			if($result = mysqli_query($connection, "SELECT word FROM $tableName where word = '".$searchedWord."' " )){
			

				while($row = mysqli_fetch_array($result)){
				
					if (strcmp(strtolower($searchedWord),strtolower($row[0]))== 0){ 
					
					unset($wordsOfWebsite[$key]);
					}
				}
					mysqli_free_result($result); 
			}	
			
			
		}
		
		mysqli_close($connection); 
		
		echo "Wörter nicht gefunden: " . count($wordsOfWebsite) . '<br>';
		
		$count = 1;
		
		foreach($wordsOfWebsite as $notExistingWord){
		
		echo $count . " " . $notExistingWord . '<br>';
		$count++;

		}
	
	
	function get_table_name($word){
		$firstLetter = mb_substr($word,0,1,"UTF-8");
		$firstLetter = mb_strtolower($firstLetter, "UTF-8");
		
		switch($firstLetter){
			case 'ü': 
				$firstLetter = 'u';
				break;
			case 'ä': 
				$firstLetter = 'a';
				break;
			case 'ö': 
				$firstLetter = 'o';
				break;
			default:
				break;
		}
		return $firstLetter;
		
	}
	
	?>
</body> 
</html>