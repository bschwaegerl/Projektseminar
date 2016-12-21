	<html> 
	<head><title>InnovationTool</title></head> 

	<body>
		<?php
		
			set_time_limit(0);
			$time = microtime(true)/60;
			//gets the checked websites from the index.php
			if (isset($_POST['websiteList'])){
			$websitesForSearch = $_POST['websiteList'];
			}
			else
			{
				$websitesForSearch = NULL;
				exit("Bitte mindestens eine Website zum Durchsuchen auswählen!");
			}
			
			//database connection
			$connection = buildDatabaseConnection();
			
			//saves all innovations already found in an array
			
			$innovationsFound = array();
			$result = mysqli_query($connection,"SELECT word FROM _innovation_found");
			while($row = mysqli_fetch_array($result))
			{
				$innovationsFound[] = $row[0];
			}
			
			//variable to count all words 
			$counterForAllWords = 0;
			
			//function to find for every main url the sub urls
			foreach($websitesForSearch as $url)
			{	

			//get for example google.com 
			crawlPage($url,parse_url($url, PHP_URL_HOST) , $connection);
			}
			
			//get all urls of main url
			$allURLsForSearch = getAllURLsForSearch($connection);
			
			//TODO alle Links, die bereits durchsucht worden sind selektieren und mit $allURLsForSearch vergleichen
			//all supposed innvations
			$supposedInnovations = array();
			
			//saves all word which havent be found in an tmp array and after that in an main array for all urls
			foreach($allURLsForSearch as $urlForSearch)
			 {
			  $supposedInnovationsTemp = getSupposedInnovations($urlForSearch, $connection);
			  foreach($supposedInnovationsTemp as $suppInnoTemp)
				{
				 $supposedInnovations[] = $suppInnoTemp;
				}
			} 
		
		//analysis of the run
		foreach($websitesForSearch as $key=>$url)
			{
		echo "MAIN-URL " .($key+1). ": " . $url	. '<br>';
			}
		echo "URLs durchsucht insgesamt: " . mysqli_num_rows(mysqli_query($connection, "SELECT * FROM _tmp_websites_actual_run" )). '<br>';
		echo "Wörter insgesamt untersucht: " .$counterForAllWords . '<br>';
		echo "Wörter nicht gefunden: " . count($supposedInnovations) . '<br>';
		echo "Benötigte Zeit: " . round(((microtime(true)/60) - $time),2) . " Minuten";
		echo "<br><br>Grün-markierte Wörter sind bereits gefundene Innvoavtionen.<br>";
		//Tabelle 
		echo "<table border='1'>
		<tr>
		<th></th>
		<th>Wort</th>
		<th>URL</th>
		</tr>";
		
		//form action for button click
		echo "<form action=\"insertIntoDatabase.php\" method =\"post\">";
		foreach($supposedInnovations as $key=>$suppInno){
		
			$rowColorAndChecked = getRowColor($innovationsFound, $supposedInnovations[$key][0]);
			
			//Tabellentupel pro Innovation
			echo "<tr bgcolor='".$rowColorAndChecked[0]."'>";
			echo "<td> <input type='checkbox' id='chkbx' name='checkList[]' value=\"".$supposedInnovations[$key][0]."\"/".$rowColorAndChecked[1]."> </td>";
			echo "<td>" .$supposedInnovations[$key][0]. "</td>";
			echo "<td> <a href=\"".$supposedInnovations[$key][1]."\" </a>" .$supposedInnovations[$key][1]. "</td>";
			echo "</tr>";
			
			//post array with all words not found
			echo "<input type='hidden' id='chkbxHidden' name='checkListHidden[]' value=\"".$supposedInnovations[$key][0]. "\"/>";

		}
		echo "</table>";

			//build the database connection
			function buildDatabaseConnection(){
				$hostname = "localhost"; $user = "root"; $password = ""; $db = "innovation";
				$connection = mysqli_connect($hostname, $user, $password, $db);
				mysqli_set_charset($connection,"utf8");
				
				return $connection;
			}
			
			//get html file, divide into array, compares  every word of array with the database
			function getSupposedInnovations($url, $connection){
			
			include_once('simple_html_dom.php');
			set_time_limit(0);			
			
			//komplettes html file
			$text = @file_get_html($url)->plaintext;
			
			//Umlaute zurück erstellen
			$text = preg_replace('/&auml;/','ä',$text);
			$text = preg_replace('/&ouml;/','ö',$text);
			$text = preg_replace('/&uuml;/','ü',$text);
			$text = preg_replace('/&Auml;/','Ä',$text);
			$text = preg_replace('/&Ouml;/','Ö',$text);
			$text = preg_replace('/&Uuml;/','Ü',$text);
			$text = preg_replace('/&szlig;/','ß',$text);
			
			//Mehrfachleerzeichen entfernen
			$text = mb_ereg_replace('\s+', ' ', $text);
			
			//alles Sonderzeichen entfernen außer "-"
			$text = preg_replace('/[^\p{Latin}\s-]/u', ' ', $text);
			
			
			//Bindestriche vor oder nach Leerzeichen weglöschen
			$text = preg_replace('/- /', ' ', $text);
			$text = preg_replace('/ -/', ' ', $text);
			

					

			//String in Array umwandeln anhand von Leerzeichen (trim um unnötige Leerzeichen zu entfernen)
			 $wordsOfWebsite = array_map('trim', explode(' ', $text));
			 
			//count every word
			global $counterForAllWords;
			$counterForAllWords += count($wordsOfWebsite);
			
			//Duplikate im Array entfernen
			$wordsOfWebsite = array_unique($wordsOfWebsite); 
			//leere array elemente entfernen
			$wordsOfWebsite = array_filter($wordsOfWebsite);
			
			
				
			
			$stopListGerman = array("ab", "aber", "als", "am", "an", "auch", "auf", "aus", "bei", "bin", "bis", "bist", "da","ca", "dadurch", "daher", "darum", "das", "dass", "daß", "dein", "deine", "dem", "den", "de", "der", "dessen", "deshalb", "die", "dies", "dieser", "dieses","doch", "dort", "du", "durch", "ein", "eine", "einem", "einen","einer", "eines", "er", "es", "euer", "für", "hatte", "hatten", "hattest","hattet", "hier", "hinter", "ich", "ihr", "ihre", "im", "in", "ist", "ja", "je" , "jede","jedem", "jeden", "jeder", "jedes", "jener", "jenes", "jetzt", "kann", "kannst", "können","könnt", "machen", "mein", "meine", "mit", "muß", "mußt", "musst", "müssen", "müßt","nach", "nachdem", "nein", "nicht", "nochmal", "nun", "oder", "ob", "seid", "sein", "seine", "sich", "sie","sind", "so", "soll", "sollen", "sollst", "sollt", "sonst", "soweit", "sowie", "und", "unser", "unsere","unter", "vom", "von", "vor", "wann", "warum", "was", "weiter", "weitere", "wenn", "wer", "werde", "werden","werdet", "weshalb", "wie", "wieder", "wieso", "wir", "wird", "wirst", "wo", "woher", "wohin", "www","um", "zu", "zum", "zur", "über");
			$stopListEnglish = array("a" , "about" , "above" , "after" , "again" , "against" , "all" , "am" , "an" , "and" , "any" , "are" , "aren't" , "as" , "at" , "be" , "because" , "been" , "before" , "being" , "below" , "between" , "both" , "but" , "by" , "can't" , "cannot" , "could" , "couldn't" , "did" , "didn't" , "do" , "does" , "doesn't" , "doing" , "don't" , "down" , "during" , "each" , "few" , "for" , "from" , "further" ,"go", "had" , "hadn't" , "has" , "hasn't" , "have" , "haven't" , "having" , "he" , "he'd" , "he'll" , "he's" , "her" , "here" , "here's" , "hers" , "herself" , "him" , "himself" , "his" , "how" , "how's" , "i" , "i'd" , "i'll" , "i'm" , "i've" , "if" , "in" , "into" , "is" , "isn't" , "it" , "it's" , "its" , "itself" , "let's" , "me" , "more" , "most" , "mustn't" , "my" , "myself" , "no" , "nor" , "not" , "of" , "off" , "on" , "once" , "only" , "or" , "other" , "ought" , "our" , "ours	ourselves" , "out" , "over" , "own" , "same" , "shan't" , "she" , "she'd" , "she'll" , "she's" , "should" , "shouldn't" , "so" , "some" , "such" , "than" , "that" , "that's" , "the" , "their" , "theirs" , "them" , "themselves" , "then" , "there" , "there's" , "these" , "they" , "they'd" , "they'll" , "they're" , "they've" , "this" , "those" , "through" , "to" , "too" , "under" , "until" , "up" , "very" , "was" , "wasn't" , "we" , "we'd" , "we'll" , "we're" , "we've" , "were" , "weren't" , "what" , "what's" , "when" , "when's" , "where" , "where's" , "which" , "will", "while" , "who" , "who's" , "whom" , "why" , "why's" , "with" , "won't" , "would" , "wouldn't" , "you" , "you'd" , "you'll" , "you're" , "you've" , "your" , "yours" , "yourself" , "yourselves" );

			//alle Array Elemente entfernen, deren wortlänge 1 ist (A, B, C, ...), in englischer oder deutscher stopliste enthalten
			foreach($wordsOfWebsite as $key=>$arrayWord)
			{
				if(strlen($arrayWord) == 1  || in_array(mb_strtolower($arrayWord), $stopListGerman) || in_array(mb_strtolower($arrayWord), $stopListEnglish))
				{
					unset($wordsOfWebsite[$key]);
				}
				
			}
			

			foreach($wordsOfWebsite as $key=>$searchedWord){
						
			$tableName = get_table_name($searchedWord);
					
				//check string lenght, if string is shorter than 4 chars were handleing it with the like operator. otherwise we re using the match against
				
				$lenghtOfWord = strlen($searchedWord);
				
				if(($result = mysqli_query($connection, "SELECT word FROM `" .$tableName."`  WHERE MATCH(word) AGAINST('".$searchedWord."' IN BOOLEAN MODE) " )) && $lenghtOfWord > 3)
				
				
				{				
					while($row = mysqli_fetch_array($result)){
					
						if (strcmp(strtolower($searchedWord),strtolower($row[0]))== 0){ 
						
						unset($wordsOfWebsite[$key]);
						}
			
					}
						mysqli_free_result($result); 
				}
				 else if(($result = mysqli_query($connection, "SELECT word FROM `" .$tableName."`  WHERE word LIKE '".$searchedWord."'" )) && $lenghtOfWord < 4)
				{
					while($row = mysqli_fetch_array($result)){
					
						if (strcmp(strtolower($searchedWord),strtolower($row[0]))== 0){ 
						
						unset($wordsOfWebsite[$key]);
						}
			
					}
					mysqli_free_result($result); 

				} 
			}
			
			$wordsOfWebsiteWithUrls = array();
		
			foreach($wordsOfWebsite as $key=>$searchedWord){	
				$wordsOfWebsiteWithUrls[] = array($searchedWord,$url);	
				if(mysqli_query($connection, "INSERT INTO _innovation_check (word, url) VALUES ('" . $searchedWord . "', '" . $url . "')" )){
			}else{
				echo "failed to insert ".$searchedWord." into _innovation_check";
			}
			}
			
			mysqli_query($connection, "INSERT INTO _websites_searched (url) VALUES ('" . $url . "')");
			return $wordsOfWebsiteWithUrls;
		
		
				
			}

			//get the table name for the search in the database
			function get_table_name($word){
				
			$firstLetter = mb_substr($word,0,1,"UTF-8");
			$firstLetter = mb_strtolower($firstLetter, "UTF-8");
			
			return $firstLetter;
			
			}
			
			//get for every link the suburls and removes all suburls, which does not contain the origin host and writes it into table _tmp_websites_actual_run
		function crawlPage($url, $host, $connection){
			
			// writes into table _tmp_websites_actual_run MAIN URL
			mysqli_query($connection, "INSERT INTO _tmp_websites_actual_run (url) 
			SELECT * FROM(SELECT '" . $url . "') as tmp 
			WHERE NOT EXISTS (SELECT url from _tmp_websites_actual_run where url = '".$url."') LIMIT 1");
					
			/* $input = @file_get_contents($url);
			$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
				
			if(preg_match_all("/$regexp/siU", $input, $matches, PREG_SET_ORDER)) {
				foreach($matches as $match) {
					//$match[2] = link address
					//$match[3] = link text
					//trim all spaces from link
					$websiteLink = trim($match[2]);
					
					//if host is in match then write link into database - deletes all links to other websites
					if(strpos($websiteLink, $host)){
					
						// writes into table _tmp_websites_actual_run searched SUB URL
						mysqli_query($connection, "INSERT INTO _tmp_websites_actual_run (url) 
						SELECT * FROM(SELECT '" . $websiteLink . "') as tmp 
						WHERE NOT EXISTS (SELECT url from _tmp_websites_actual_run where url = '".$websiteLink."') LIMIT 1");
								
						
						//goes into depth 2
				
						$input2 = @file_get_contents($websiteLink) ;
						$regexp2 = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
				
						if(preg_match_all("/$regexp/siU", $input2, $matches2, PREG_SET_ORDER)) {
							foreach($matches2 as $match2) {
								// $match[2] = link address
								//$match[3] = link text
								//trim all spaces from link
								$websiteLink2 = trim($match2[2]);
							
								//if host is in match then write link into database - deletes all links to other websites
									if(strpos($websiteLink2, $host)){
					
										// writes into table _tmp_websites_actual_run SUBSUB URL
											if(mysqli_query($connection, "INSERT INTO _tmp_websites_actual_run (url) 
											SELECT * FROM(SELECT '" . $websiteLink2 . "') as tmp 
											WHERE NOT EXISTS (SELECT url from _tmp_websites_actual_run where url = '".$websiteLink2."') LIMIT 1")){
											} else {} 
									}
					 
							} 
						} 
						
					}
				} 
			 }*/
		}
		
		function getRowColor($innovationsFound, $word){
				$colorAndChecked = array();
			if(in_array($word,$innovationsFound)){
				$colorAndChecked[0] = "##4EEE94";
				$colorAndChecked[1] = "checked";
			}else{
				$colorAndChecked[0] = "#FFFFFF";
				$colorAndChecked[1] = "";
			}
		
			return $colorAndChecked;
		}
			
		//returns the result of all websites in table _tmp_websites_actual_run
		function getAllURLsForSearch($connection){
				
			$allURLsOfWebsite = array();
			$urlsSearched = array();
			$urlsMain = array();
			
			$result = mysqli_query($connection, "SELECT url FROM _tmp_websites_actual_run" );
			$result2 = mysqli_query($connection,"SELECT url FROM _websites_searched");
			$result3 = mysqli_query($connection,"SELECT url FROM _websites");

			
			while($row = mysqli_fetch_array($result))
			{
				$allURLsOfWebsite[] = $row['url'];
			}
			while($row = mysqli_fetch_array($result2))
			{
				$urlsSearched[] = $row['url'];
			}
			while($row = mysqli_fetch_array($result3))
			{
				$urlsMain[] = $row['url'];
			}
			
			foreach($allURLsOfWebsite as $key=>$url){
				if((in_array($url,$urlsSearched)) && (!in_array($url, $urlsMain))){
					unset($allURLsOfWebsite[$key]);
				}
				
			}
			
		return $allURLsOfWebsite;
		}	
		
?>
			<br>
			<input type="submit" id="btSend" value="Wörter in Datenbank speichern!"/>
		</form>
		
		
		<br><br>
		<form action="index.html" method="POST">
			<input type="submit" id="btCancel" value="Abbrechen und Bearbeitung aufschieben"/>
		</form>
		
	</body> 
	</html>