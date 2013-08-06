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

$small_block_background = $config['color_tellafriend'];

$referer	= mysql_real_escape_string($_SERVER['HTTP_REFERER']);

if ( $referer == "" ) header("Location: " . "index.php");

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

$comments	= $postedValue[0];
$image_id 	= $postedValue[1];

//check if being loaded from play.php
if ( $image_id == "" ) die();

//check if comments have been filled in
if ( $comments == "" ) {
	echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['video_comments_error_comment'].'</b></font>';
	die();
}

//check if user is logged in
if ( $user_id == "" ) {
	echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['video_comments_login'].'</b></font>';
	die();
}

// check if users are allowed multiple posts if not, check if user has laready posted
$multiple_comments = $config["allow_multiple_video_comments"];

if( $multiple_comments == "no" ) {
	$user_id	= mysql_real_escape_string($user_id);
	$image_id	= mysql_real_escape_string($image_id);
	$sql		= "SELECT image_id, by_id from imagecomments WHERE image_id = $image_id AND by_id = $user_id";
	$query	= mysql_query($sql);
	$result 	= mysql_num_rows($query);

	if ( $result != 0 ) {
		echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['video_comments_error_already'].'</b></font>';
		die();
	}
}

// comment flood control
$user_id 		= mysql_real_escape_string($user_id);
$comment_table 	= 'imagecomments';
$image_id 		= mysql_real_escape_string($image_id);
$item_id 		= 'image_id';
$proceed 		= flood_check ( $user_id, $comment_table, $item_id, $image_id );

if ( $proceed[0] == 'false' ) {
	echo $proceed[1];
	die();
}

//check if user allows image comments to their image
$sql1		= "SELECT indexer FROM images WHERE indexer = $image_id AND allow_comments = 'no'";
$result1 	= @mysql_query($sql1);

if( @mysql_num_rows($result1) != 0 ) {
	echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['audio_comments_not_allowed'].'</b></font>';
	die();
}

$user_id	= mysql_real_escape_string($user_id);
$user_name	= mysql_real_escape_string($user_name);
$image_id 	= mysql_real_escape_string($image_id);
$comments 	= mysql_real_escape_string($comments);

$sql 		= "INSERT into imagecomments(
					by_id,
					by_username,
					image_id,
					comments,
					todays_date)

				VALUES(
					'$user_id',
					'$user_name',
					'$image_id',
					'$comments',
					NOW()
					)";
mysql_query($sql);

echo '<p align="center"><font color="#009933" face="Arial"><b>'.$config['video_comments_success'].'</b></font>';

$comments = "";

// call javascript ajax refresh we need to call comments ajax but page == 1 to view new posted comment and image_id == page image_id
// comments_ajax.php?page=1&vid=2

echo "<script type='text/javascript'>ahahscript.ahah('comments_image_ajax.php?page=1&image=$image_id', 'commentajax', '', 'GET', '', this);</script>";

die();


?>