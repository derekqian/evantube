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

if ( $referer == "" ) {
	echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['video_comments_error_comment'].'</b></font>';
	die();
}

if ( isset( $_POST ) )
   $postArray = &$_POST;
else
   $postArray = &$HTTP_POST_VARS;

foreach ( $postArray as $sForm => $value ) {
	if ( get_magic_quotes_gpc() )
		$postedValue[] = htmlspecialchars( stripslashes( $value ) ) ;
	else
		$postedValue[] = htmlspecialchars( $value ) ;
}

$post_check = sizeof($postedValue);
if ( $post_check > 2) {
	echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['video_comments_error_comment'].'</b></font>';
	die();
}

$comments	= $postedValue[0];
$vid 		= $postedValue[1];

if ( $vid == '' ) {
	echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['video_comments_error_comment'].'</b></font>';
	die();
}

//check if comments have been filled in || now checks for length
if ( $comments == '' ) {
	echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['video_comments_error_comment'].'</b></font>';
	die();
}

if ( strlen($comments) < $config['comments_length'] ) {
	echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['video_comments_error_length'].'</b></font>';
	die();
}

//check if user is logged in
if ($user_id == ''){
	echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['video_comments_login'].'</b></font>';
	die();
}

//check if users are allowed multiple posts if not, check if user has already posted
$multiple_comments = $config["allow_multiple_video_comments"];

if( $multiple_comments == 'no' ) {

	$user_id = mysql_real_escape_string($user_id);
	$vid = mysql_real_escape_string($vid);
	$sql = "SELECT * from videocomments WHERE video_id = $vid AND by_id = $user_id";

	$query = mysql_query($sql);
	$result = mysql_num_rows($query);
	$comment_time = $result['todays_date'];

	if ($result != 0){
		echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['video_comments_error_already'].'</b></font>';
		die();
	}
}

// comment flood control
$user_id 		= mysql_real_escape_string($user_id);
$comment_table 	= 'videocomments';
$vid 			= mysql_real_escape_string($vid);
$item_id 		= 'video_id';
$proceed 		= flood_check ( $user_id, $comment_table, $item_id, $vid );

if ( $proceed[0] == 'false' ) {
	echo $proceed[1];
	die();
}

//check if user allows video comments to their video
$sql1		= "SELECT * FROM videos WHERE indexer = $vid AND allow_comments = 'no'";
$result1 	= @mysql_query($sql1);

if(@mysql_num_rows($result1) != 0){
	echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['video_comments_not_allowed'].'</b></font>';
	die();
}

$user_id	= mysql_real_escape_string($user_id);
$user_name 	= mysql_real_escape_string($user_name);
$vid 		= mysql_real_escape_string($vid);
$comments 	= mysql_real_escape_string($comments);

$sql = "INSERT into videocomments (by_id, by_username, video_id, comments, todays_date) VALUES
                                  ($user_id, '$user_name', $vid, '$comments', NOW())";
mysql_query($sql);

	echo '<p align="center"><font color="#009933" face="Arial"><b>'.$config['video_comments_success'].'</b></font>';

	$comments = '';

	// call javascript ajax refresh, need comments ajax but page == 1 to view new posted comment and vid == page vid comments_ajax.php?page=1&vid=2

      echo "<script type='text/javascript'>ahahscript.ahah('comments_ajax.php?page=1&vid=$vid', 'commentajax', '', 'GET', '', this);</script>";
	die();
?>