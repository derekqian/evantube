<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once ('classes/config.php');
include_once ('classes/sessions.php');

$referer	= mysql_real_escape_string($_SERVER['HTTP_REFERER']);

if ( $referer == "" ) header("Location: " . "index.php");

$IP 				= $_SERVER['REMOTE_ADDR'];
$comment_old_rating	= mysql_real_escape_string($_GET['value']);

// this is either +1 or -1
$comment_rating = mysql_real_escape_string($_GET['rate']);
if ( $comment_rating == 'up' ) {
	$comment_rating = 1;
}
if ( $comment_rating == 'down' ) {
	$comment_rating = -1;
}

$rate_comment_id	= mysql_real_escape_string($_GET['comment_id']);
$rate_type		= mysql_real_escape_string($_GET['type']);

switch ( $rate_type ) {

	case 'video':
		$table_rating 	= 'videocomments_rating';
		$table_cmts		= 'videocomments';
	break;

	case 'audio':
		$table_rating 	= 'audiocomments_rating';
		$table_cmts		= 'audiocomments';
	break;

	case 'image':
		$table_rating 	= 'imagecomments_rating';
		$table_cmts		= 'imagecomments';
	break;

	case 'profile':
		$table_rating	= 'profilecomments_rating';
		$table_cmts		= 'profilecomments';
	break;
}

//check if viewer is logged in as a member
if ($user_id == "") {
	@mysql_close();
    	echo '<p align="center"><font color="#DD0000" face="Arial" size="2"><b>'.$config["must_login"].'</b></font>';
	die();
}

//if no comment id in url
if ($rate_comment_id == "") {
	@mysql_close();
	echo '<p align="center"><font color="#DD0000" face="Arial" size="2"><b>'.$config["error_26"].'</b></font>';	//error
	die();
}

//if no rating in url
if ($comment_rating == "") {
	@mysql_close();
	echo '<p align="center"><font color="#DD0000" face="Arial" size="2"><b>'.$config["error_26"].'</b></font>';	//error
	die();
}

//check if user has not rated this before
$flag_sql = "SELECT * FROM $table_rating WHERE comment_id = $rate_comment_id AND user_id = $user_id";
$flag_query = @mysql_query($flag_sql);
$flag_count = @mysql_num_rows($flag_query);

if ( $flag_count != 0 ) {
	@mysql_close();
	echo '<p align="center"><font color="#DD0000" face="Arial" size="2"><b>'.$config["error_40"].'</b></font>';
	//"You have already rated this comment."

	// call javascript ajax refresh
	echo "<script type='text/javascript'>ajax_refresh('drop_block_$rate_comment_id', 'rating_vote_$rate_comment_id', $comment_old_rating, 0);</script>";

	die();

} else {

	// register user rating this comment
    	$flag_sql = "INSERT INTO $table_rating (user_id, IP, comment_id) VALUES ('$user_id', '$IP', '$rate_comment_id')";
    	$flag_query = @mysql_query($flag_sql);

	if(!$flag_query) {
		die($config['error_26']);
		@mysql_close();
		die();
	}

    	// Update Rating
    	$sql = "SELECT * FROM $table_cmts WHERE indexer = $rate_comment_id";
    	$query = @mysql_query($sql);
    	$count = @mysql_num_rows($query);

    	if ($count == 0) {
      	@mysql_close();
        	//"You request could not be completed. "
        	echo '<p align="center"><font color="#DD0000" face="Arial" size="2"><b>'.$config["error_18"].'</b></font>';
        	die();

    	} else {
      	$result = @mysql_fetch_array($query);
        	$rating_number_votes = $result['rating_number_votes'] + 1;
        	$rating_total_points = $result['rating_total_points'] + $comment_rating;

        	$updated_rating = $rating_total_points;

        	//updated rating
        	$sql = "UPDATE $table_cmts SET rating_number_votes = $rating_number_votes, rating_total_points = $rating_total_points, updated_rating = $updated_rating WHERE indexer = $rate_comment_id";
        	@mysql_query($sql);

        	echo '<p align="center"><font color="#00DD00" face="Arial" size="2"><b>'.$config["error_19"].'</b></font>';

        	// call javascript ajax refresh
        	echo "<script type='text/javascript'>ajax_refresh('drop_block_$rate_comment_id', 'rating_vote_$rate_comment_id', $comment_old_rating, $comment_rating);</script>";

        	die();
    	}
}

?>

