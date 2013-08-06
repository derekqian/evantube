<?php

/********************************************************************************************
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
+	PHPMotion - Dynamic Search						   				  +
+     for PHPMotion Video Script Ver 3.5									  +
+     By PHPMotion Sept - 2009 * Copyright 2009 all rights reserved				  +
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
********************************************************************************************/


include_once ('classes/config.php');

$media_type		= mysql_real_escape_string( $_GET['type'] );
$search_input	= mysql_real_escape_string( $_GET['keyword'] );
$suggest		= mysql_real_escape_string( $_GET['find_words'] );
$action		= mysql_real_escape_string( $_GET['action'] );
$text_limit		= 30;

if ( $action == 'suggest_words' ) {
	$words_choice = mysql_real_escape_string( $_GET['check'] );
	if ( strlen($words_choice) > 1 ) $js_form_words = make_search_words($media_type, $words_choice);
}

$search_input = str_replace("%20", " ", $search_input);

if ( $js_form_words ) echo $js_form_words;

function make_search_words($media_type, $search_input) {
	global $text_limit;

	$order_by 		= 'viewtime';
	$limit 		= 20;

	if ( $media_type == 'audios' ) $order_by = 'playtime';

																									/*
	=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
						START TAGS SEARCHES
	=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
																									*/

	$sql		= "SELECT tags FROM $media_type WHERE public_private = 'public' AND approved ='yes' AND tags LIKE '%$search_input%'";
	$query	= @mysql_query($sql);

	while ($result = @mysql_fetch_array($query)) $tag_keywords[] = $result['tags'];

	for ( $x=0; $x < sizeof($tag_keywords); $x++ ) $each_word[] = split(" ", $tag_keywords[$x]);

	for ( $y=0; $y < sizeof($each_word); $y++ ) for ( $t=0; $t < sizeof($each_word[$y]); $t++ ) $words[] = $each_word[$y][$t];

	foreach($words as $find_word) $found = (preg_match_all("/($search_input)+/i", $find_word, $matches) ? $tag_return_word[] = $find_word : '');

																									/*
	=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
						END TAGS SEARCHES
	=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
																									*/
																									/*
	=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
						START TITLE SEARCHES
	=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
																									*/

	$sql		= "SELECT title FROM $media_type WHERE public_private = 'public' AND approved ='yes' AND title LIKE '%$search_input%'";
	$query	= @mysql_query($sql);

	while ($result = @mysql_fetch_array($query)) $title_return_word[] = str_replace('"', '', $result['title']);
																									/*
	=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
						END TITLE SEARCHES
	=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
																									*/

	if ( sizeof($tag_return_word) == 0 )
		$return_word[0] = 'No Suggestions'; //$title_return_word;
	else
		$return_word = array_merge($tag_return_word,$title_return_word);

	$google_returned = 'window.google.ac.Suggest_apply(frameElement, "'.$search_input.'", new Array(2, "'.$return_word[0].'", "", "'.$return_word[1].'", "", "'.$return_word[2].'", "", "'.$return_word[3].'", "", "'.$return_word[4].'", "", "'.$return_word[5].'", "", "'.$return_word[6].'", "", "'.$return_word[7].'", "", "'.$return_word[8].'", "", "'.$return_word[9].'", ""), new Array(""));';

	//echo $google_returned;

	return($google_returned);
}

// end function



?>