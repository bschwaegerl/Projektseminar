	<html> 
	<head><title>InnovationTool</title></head> 

	<body>
		
		//TODO: BUTTON START!! + CHECKBOX WITH WEBSITES
		<?php
			$url = "http://www.spiegel.de/";
			
			$connection = buildDatabaseConnection();
			
			$supposedInnovations = getSupposedInnovations($url, $connection);
			
			function buildDatabaseConnection(){
				$hostname = "localhost"; $user = "root"; $password = ""; $db = "innovation";
				$connection = mysqli_connect($hostname, $user, $password, $db);
				mysqli_set_charset($connection,"utf8");
				
				return $connection;
			}
			
			function getSupposedInnovations($url, $connection){
			
			include_once('simple_html_dom.php');
			set_time_limit(0);
			
			//TODO: ARRAY URLS
			
			
			//komplettes html file
			$text = file_get_html($url)->plaintext;
		
			//alles Sonderzeichen entfernen außer "-"
			$text = preg_replace('/[^\p{Latin}\s-]/u', ' ', $text);
			
			//Bindestriche vor oder nach Leerzeichen weglöschen
			$text = preg_replace('/- /', ' ', $text);
			$text = preg_replace('/ -/', ' ', $text);

			//Mehrfachleerzeichen entfernen
			$text = mb_ereg_replace('\s+', ' ', $text);		

			
			//String in Array umwandeln anhand von Leerzeichen (trim um unnötige Leerzeichen zu entfernen)
			 $wordsOfWebsite = array_map('trim', explode(' ', $text));

			 echo "Wörter insgesamt: " . count($wordsOfWebsite) . '<br>'; 

			//Duplikate im Array entfernen
			$wordsOfWebsite = array_unique($wordsOfWebsite); 
			//leere array elemente entfernen
			$wordsOfWebsite = array_filter($wordsOfWebsite);
			
			
				
			
			$stopListGerman = array("ab", "aber", "als", "am", "an", "auch", "auf", "aus", "bei", "bin", "bis", "bist", "da","ca", "dadurch", "daher", "darum", "das", "dass", "daß", "dein", "deine", "dem", "den", "de", "der", "dessen", "deshalb", "die", "dies", "dieser", "dieses","doch", "dort", "du", "durch", "ein", "eine", "einem", "einen","einer", "eines", "er", "es", "euer", "für", "hatte", "hatten", "hattest","hattet", "hier", "hinter", "ich", "ihr", "ihre", "im", "in", "ist", "ja", "je" , "jede","jedem", "jeden", "jeder", "jedes", "jener", "jenes", "jetzt", "kann", "kannst", "können","könnt", "machen", "mein", "meine", "mit", "muß", "mußt", "musst", "müssen", "müßt","nach", "nachdem", "nein", "nicht", "nun", "oder", "ob", "seid", "sein", "seine", "sich", "sie","sind", "so", "soll", "sollen", "sollst", "sollt", "sonst", "soweit", "sowie", "und", "unser", "unsere","unter", "vom", "von", "vor", "wann", "warum", "was", "weiter", "weitere", "wenn", "wer", "werde", "werden","werdet", "weshalb", "wie", "wieder", "wieso", "wir", "wird", "wirst", "wo", "woher", "wohin","um", "zu", "zum", "zur", "über");
			$stopListEnglish = array("a" , "about" , "above" , "after" , "again" , "against" , "all" , "am" , "an" , "and" , "any" , "are" , "aren't" , "as" , "at" , "be" , "because" , "been" , "before" , "being" , "below" , "between" , "both" , "but" , "by" , "can't" , "cannot" , "could" , "couldn't" , "did" , "didn't" , "do" , "does" , "doesn't" , "doing" , "don't" , "down" , "during" , "each" , "few" , "for" , "from" , "further" ,"go", "had" , "hadn't" , "has" , "hasn't" , "have" , "haven't" , "having" , "he" , "he'd" , "he'll" , "he's" , "her" , "here" , "here's" , "hers" , "herself" , "him" , "himself" , "his" , "how" , "how's" , "i" , "i'd" , "i'll" , "i'm" , "i've" , "if" , "in" , "into" , "is" , "isn't" , "it" , "it's" , "its" , "itself" , "let's" , "me" , "more" , "most" , "mustn't" , "my" , "myself" , "no" , "nor" , "not" , "of" , "off" , "on" , "once" , "only" , "or" , "other" , "ought" , "our" , "ours	ourselves" , "out" , "over" , "own" , "same" , "shan't" , "she" , "she'd" , "she'll" , "she's" , "should" , "shouldn't" , "so" , "some" , "such" , "than" , "that" , "that's" , "the" , "their" , "theirs" , "them" , "themselves" , "then" , "there" , "there's" , "these" , "they" , "they'd" , "they'll" , "they're" , "they've" , "this" , "those" , "through" , "to" , "too" , "under" , "until" , "up" , "very" , "was" , "wasn't" , "we" , "we'd" , "we'll" , "we're" , "we've" , "were" , "weren't" , "what" , "what's" , "when" , "when's" , "where" , "where's" , "which" , "will", "while" , "who" , "who's" , "whom" , "why" , "why's" , "with" , "won't" , "would" , "wouldn't" , "you" , "you'd" , "you'll" , "you're" , "you've" , "your" , "yours" , "yourself" , "yourselves" );

			//alle Array Elemente entfernen, deren wortlänge 1 ist (A, B, C, ...), in englischer oder deutscher stopliste enthalten
			foreach($wordsOfWebsite as $key=>$arrayWord)
			{
				if(strlen($arrayWord) == 1  || strlen($arrayWord) == 2  || in_array(mb_strtolower($arrayWord), $stopListGerman) || in_array(mb_strtolower($arrayWord), $stopListEnglish))
				{
					unset($wordsOfWebsite[$key]);
				}
				
			}
			

			foreach($wordsOfWebsite as $key=>$searchedWord){
						
			$tableName = get_table_name($searchedWord);
						
				if($result = mysqli_query($connection, "SELECT word FROM `" .$tableName."`  where MATCH(word) AGAINST('".$searchedWord."' IN BOOLEAN MODE) " ))
				
				
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
			}

			return $wordsOfWebsiteWithUrls;
		
		
				
			}
			
			function get_table_name($word){
				
			$firstLetter = mb_substr($word,0,1,"UTF-8");
			$firstLetter = mb_strtolower($firstLetter, "UTF-8");
			
			return $firstLetter;
			
			}
		
			mysqli_close($connection);
		
		echo "Wörter nicht gefunden: " . count($supposedInnovations) . '<br>';
		
		
		echo "<form action=\"insertIntoDatabase.php\" method =\"post\">";
		foreach($supposedInnovations as $key=>$suppInno){
		
		echo "<input type='checkbox' id='chkbx' name='checkList[]' value=\"".$supposedInnovations[$key][0]. " ".$supposedInnovations[$key][1]. "\"/>".$supposedInnovations[$key][0]. " ".$supposedInnovations[$key][1]."<br>";
		echo "<input type='hidden' id='chkbxHidden' name='checkListHidden[]' value=\"".$supposedInnovations[$key][0]. "\"/>";

		}
		
		?>
		
			<input type="submit" id="btSend" value="Send Words!"/>
		</form>
		
	</body> 
	</html>