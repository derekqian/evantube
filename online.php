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

//Default time out for inactive user in minutes
define ('TIMEOUT', 20);

//the value for checking if user has exceded timeout
$idle		= time()-(60*TIMEOUT);
$user_ip 	= mysql_escape_string($_SERVER['REMOTE_ADDR']);

// Adding user to online table run only while we have an IP address to work with
if ($user_ip != "") {

	$last_active = time();

	//check if I am logged in or just guest

	if ($user_id == "") {
		$logged_in_username = 'guest';
		$logged_in_id = 0;
	} else {
		$logged_in_username = $user_name;
		$logged_in_id = $user_id;
	}

	//check if user is already recorded in online table
	$sql			= "SELECT * FROM online WHERE user_ip = '$user_ip'";
	$query		= mysql_query($sql);
	$result		= mysql_fetch_array($query);
	$db_date_seen	= $result['last_active'];
	$rows			= mysql_num_rows(mysql_query($sql));

	if ($rows == 0) {

		//create new logged in user record

	 	$sql = "INSERT INTO online (user_ip, last_active, last_seen, logged_in_username, logged_in_id) VALUES ('$user_ip', '$last_active', NOW(), '$logged_in_username', '$logged_in_id')";
		$query = @mysql_query($sql);

		// update last seen only on create new login
		$sql2 = "UPDATE member_profile SET user_ip = '$user_ip', last_seen = NOW() WHERE user_id = '$user_id'";
		@mysql_query($sql2);

	} else {

		// update if more than 15 minutes => should maybe set to an admin setting?

		if ( $db_date_seen == '' ) {
			$sql = "UPDATE online SET last_seen = NOW(), last_active = '$last_active', logged_in_username='$logged_in_username', logged_in_id='$logged_in_id' WHERE user_ip='$user_ip'";
			@mysql_query($sql);

			$sql2 = "UPDATE member_profile SET last_seen = NOW(), user_ip = '$user_ip' WHERE user_id = '$user_id'";
			@mysql_query($sql2);
		}

		if ( $db_date_seen < ( time()- 900 ) ) {

			$sql = "UPDATE online SET last_seen = NOW(), last_active = '$last_active', logged_in_username='$logged_in_username', logged_in_id='$logged_in_id' WHERE user_ip='$user_ip'";
			@mysql_query($sql);

			$sql2 = "UPDATE member_profile SET last_seen = NOW(), user_ip = '$user_ip' WHERE user_id = '$user_id'";
			@mysql_query($sql2);
		}
	}

}

//Use this opportunity to delete inactive records
$sql = "DELETE FROM online WHERE last_active < $idle";
@mysql_query($sql);


//count number of guests
$sql = "SELECT * FROM online WHERE logged_in_username = 'guest'";
$guests_online = @mysql_num_rows(mysql_query($sql));

//get array of all user who are online and within the idle time set
$sql = "SELECT * FROM online";
$onlinequery = @mysql_query($sql);
$count_online_users = @mysql_num_rows($onlinequery);



//NOTES >> You can call run through this array in any other script such as showing members page with their online status
// simply include this whole script


?>