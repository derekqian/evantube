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


$referer		= mysql_real_escape_string($_SERVER['HTTP_REFERER']);
$flag_type 		= mysql_real_escape_string($_GET['flag_type']);
$flag_content_id 	= (int) mysql_real_escape_string($_GET['abuse']);

if ( $referer == "" ) {
	 header("Location: " . "index.php");
}

//$flag_type MUST be same as table name (see array) created an array for checking posted/get $type

$type_array = array('audiocomments',
			  'audios',
			  'group_comments',
			  'images',
			  'imagecomments',
			  'imagecomments_replys',
			  'videos',
			  'member_profile',
			  'profilecomments',
			  'videocomments',
			  'videocomments_replys',
			  'blogs',
			  'blogcomments',
			  'blogcomments_replys',
			  'group_profile'
			  );

//is member logged in
if ($user_id == "") {
	echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config["error_13"].'</b></font>';			//login error
	die();
}

//check post/get values
if ( $flag_content_id == "" || $flag_type == "" || !in_array($flag_type, $type_array) ) {
	@mysql_close();
    	echo '<p align="center"><font color="#DD0000" face="Arial"><b>'.$config['error_26'].'</b></font>';			//general error
    	die();
}

//has user flagged this before
$flag_sql 	= "SELECT * FROM flagging WHERE content_id = $flag_content_id AND user_id = $user_id AND flag_type = '$flag_type'";
$flag_query = @mysql_query($flag_sql);
$flag_count = @mysql_num_rows($flag_query);

if ( $flag_count != 0 ) {
	@mysql_close();
    	echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config["error_14"].'</b></font>';			//already flagged this error
    	die();

} else {

	$flag_sql = "INSERT INTO flagging (user_id, content_id, today_date, flag_type) VALUES ($user_id, $flag_content_id, NOW(), '$flag_type' )";
    	$flag_query = @mysql_query($flag_sql);

    	if ($flag_type == 'member_profile') {													//because member profiles does not user indexer
      	$sql_key = 'user_id';

    	} else {
    		$sql_key = 'indexer';
	}

	//get medias current flag count
	$sql 		= "SELECT flag_counter from $flag_type WHERE $sql_key = $flag_content_id";
	$query 	= @mysql_fetch_array(@mysql_query($sql));
	$result 	= $query['flag_counter'];

	if ($result != '') {
     		$flag_counter = $result + 1;
	}

	//update counter
	$sql		= "UPDATE $flag_type SET flag_counter = $flag_counter WHERE $sql_key = $flag_content_id";
	@mysql_query($sql);

	if( !mysql_error() ) {
		echo '<p align="center"><font color="#00EE00" face="Arial"><b>'.$config["error_15"].'</b></font>';
    		die();
	} else {
		echo '<p align="center"><font color="#00EE00" face="Arial"><b>'.$config["error_15"].'</b></font>';
		die();
	}
}

?>

