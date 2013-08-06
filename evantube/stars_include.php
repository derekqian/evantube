<?php
///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

if ( defined('blogs') ) {
	$media_table = 'blogs';
	$row = 'indexer';
}

if ( defined('audios') ) {
	$media_table = 'audios';
	$row = 'indexer';
}

if ( defined('images') ) {
	$media_table = 'images';
	$row = 'indexer';
}

if ( defined('members') ) {
	$media_table = 'member_profile';
	$row = 'user_id';
}

//$sql_stars	= "SELECT updated_rating FROM $media_table WHERE indexer = $id";

$sql_stars 		= "SELECT updated_rating FROM $media_table WHERE $row = $id";

$query_stars 	= @mysql_query($sql_stars);
$result_stars	= @mysql_fetch_array($query_stars);
$stars 		= $result_stars['updated_rating'];

//default stars
$star1 		= 'star_grey.gif';
$star2 		= 'star_grey.gif';
$star3 		= 'star_grey.gif';
$star4 		= 'star_grey.gif';
$star5 		= 'star_grey.gif';

if ($stars == 0) {
	$star1 	= 'star_grey.gif';
    	$star2 	= 'star_grey.gif';
    	$star3 	= 'star_grey.gif';
    	$star4 	= 'star_grey.gif';
    	$star5 	= 'star_grey.gif';
}
if ($stars == 1) {
    	$star1 	= 'star_red.gif';
    	$star2 	= 'star_grey.gif';
    	$star3 	= 'star_grey.gif';
    	$star4 	= 'star_grey.gif';
    	$star5 	= 'star_grey.gif';
}
if ($stars == 2) {
    	$star1 	= 'star_red.gif';
    	$star2 	= 'star_red.gif';
    	$star3 	= 'star_grey.gif';
    	$star4 	= 'star_grey.gif';
    	$star5 	= 'star_grey.gif';
}
if ($stars == 3) {
    	$star1 	= 'star_red.gif';
    	$star2 	= 'star_red.gif';
    	$star3 	= 'star_red.gif';
    	$star4 	= 'star_grey.gif';
    	$star5 	= 'star_grey.gif';
}
if ($stars == 4) {
    	$star1 	= 'star_red.gif';
    	$star2 	= 'star_red.gif';
    	$star3 	= 'star_red.gif';
    	$star4 	= 'star_red.gif';
    	$star5 	= 'star_grey.gif';
}
if ($stars == 5) {
    	$star1 	= 'star_red.gif';
    	$star2 	= 'star_red.gif';
    	$star3 	= 'star_red.gif';
    	$star4 	= 'star_red.gif';
    	$star5 	= 'star_red.gif';
}


?>