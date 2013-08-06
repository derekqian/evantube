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
include_once ('includes/enabled_features.php');

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
$audio 	= $postedValue[1];

//check if being loaded from play.php

if ( $audio == '' ) die();


//check if comments have been filled in

if ( $comments == '' ) {
	echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['video_comments_error_comment'].'</b></font>';
	die();
}

//check if user is logged in
if ( $user_id == '' ) {
	echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['video_comments_login'].'</b></font>';
	die();
}

// check if users are allowed multiple posts if not, check if user has laready posted

$multiple_comments = $config["allow_multiple_video_comments"];

if( $multiple_comments == 'no' ) {

	$user_id	= mysql_real_escape_string($user_id);
	$audio	= mysql_real_escape_string($audio);
	$sql		= "SELECT * from audiocomments WHERE audio_id = $audio AND by_id = $user_id";
	$query	= mysql_query($sql);
	$result 	= mysql_num_rows($query);

	if ( $result != 0 ) {
		echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['video_comments_error_already'].'</b></font>';
		die();
	}
}

// comment flood control
$user_id 		= mysql_real_escape_string($user_id);
$comment_table 	= 'audiocomments';
$audio 		= mysql_real_escape_string($audio);
$item_id 		= 'audio_id';
$proceed 		= flood_check ( $user_id, $comment_table, $item_id, $audio );

if ( $proceed[0] == 'false' ) {
	echo $proceed[1];
	die();
}

//check if user allows audio comments to their audio
$sql1		= "SELECT * FROM audios WHERE indexer = $audio AND allow_comments = 'no'";
$result1 	= @mysql_query($sql1);

if( @mysql_num_rows($result1) != 0 ) {
	echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['audio_comments_not_allowed'].'</b></font>';
	die();
}


$user_id		= mysql_real_escape_string($user_id);
$user_name		= mysql_real_escape_string($user_name);
$audio 		= mysql_real_escape_string($audio);
$comments 		= mysql_real_escape_string($comments);

$sql 			= "INSERT into audiocomments(
						by_id,
						by_username,
						audio_id,
						comments,
						todays_date)

					VALUES(
						'$user_id',
						'$user_name',
						'$audio',
						'$comments',
						NOW()
						)";

mysql_query($sql);

echo '<p align="center"><font color="#009933" face="Arial"><b>'.$config['video_comments_success'].'</b></font>';

$comments = '';

// call javascript ajax refresh we need to call comments ajax but page == 1 to view new posted comment and audio == page audio
// comments_ajax.php?page=1&vid=2

echo "<script type='text/javascript'>ahahscript.ahah('comments_audio_ajax.php?page=1&audio=$audio', 'commentajax', '', 'GET', '', this);</script>";

die();


?>