<?php
	$url = "http://www.spiegel.de/";
	$parse = parse_url($url);
	
	print_r($parse["host"]);
?>