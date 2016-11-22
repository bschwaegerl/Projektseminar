<html> 
<head><title>InnovationTool</title></head> 

<body>
	<?php
		include_once('simple_html_dom.php');
		set_time_limit(0);
		
		$url = $_POST['websitelink'];
		
		/* //$text befüllen und Sonderzeichen durch Leerzeichen ersetzen
		$text = preg_replace('/[^\p{Latin}\s]/u', ' ', (file_get_html($url)->plaintext));
		//Mehrfachleerzeichen zu einem umwandeln
		$text = preg_replace('!\s+!', ' ', $text);
		//Leerzeichen einfügen
		$text = preg_replace('/(?<!\ )[A-Z]/', ' $0', $text); */
		
		//String in Array umwandeln
		/* $wordsOfWebsite = explode(" ",$text);
		//Duplikate entfernen
		$wordsOfWebsite = array_unique(array_values(array_filter($wordsOfWebsite))); */
		$wordsOfWebsite = array('schwägerl', 'ähnlich', 'Test', 'groß', 'über','Ärger');
		
		//Datenbankverbindung aufbauen
		$hostname = "localhost"; $user = "root"; $password = ""; $db = "inno_blacklist";
		$connection = mysqli_connect($hostname, $user, $password, $db);
		
		foreach($wordsOfWebsite as $key=>$searchedWord){
			
		$tableName = get_table_name($searchedWord);
		echo $tableName;
		echo $searchedWord;
			if($result = mysqli_query($connection, "SELECT word FROM $tableName where word = '".$searchedWord."' " )){
			
			
				while($row = mysqli_fetch_array($result)){
				
					if (strcmp(strtolower($searchedWord),strtolower($row[0]))== 0){ 
					
					unset($wordsOfWebsite[$key]);
					}
				}
					mysqli_free_result($result); 
			}	
			
			
		}
		print_r(array_values($wordsOfWebsite));
		
		mysqli_close($connection); 
	
	
	function get_table_name($word){
		$firstLetter = mb_substr($word,0,1,"UTF-8");
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