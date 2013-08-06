<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

validate_user($_COOKIE['user']);

// set user vars for passing around each php page
$user_id	= $_SESSION['user_id'];
$user_name	= $_SESSION['user_name'];
$user_group = $_SESSION['user_group'];


//get some basic user things like email for logged in members
if ( $user_id != '' ) {

	if ( defined('SMF_INSTALLED') ) {

		//include_once($_SERVER["DOCUMENT_ROOT"] . "/forum/SSI.php");
		//global $sc;

		/*
		$loggedin 			= 1;
		$login_out			= $config['logout_text'];
		$login_out_link 		= $smf_bridge_log_out;
		$login_out_link 		= $login_out_link.$sc;
		*/

		$loggedin 			= 1;
		$login_out 			= $config['logout_text'];
		$login_out_link 		= 'logout.php';
		$register_menu		= $config['fav_menu'];


	} else {

		$loggedin 			= 1;
		$login_out 			= $config['logout_text'];
		$login_out_link 		= 'logout.php';
		$register_menu		= $config['fav_menu'];

		//$register_menu_link	= 'myfavorites.php';
	}

	//email inbox
	$sql				= "SELECT indexer FROM messages WHERE to_id = '$user_id' AND email_read = 'no'";
	$number_of_emails 	= @mysql_num_rows(mysql_query($sql));
	$show_email_count 	= 1;

	//Friend Invites
	$sql 				= "SELECT * FROM friends WHERE friends_id = '$user_id' AND invitation_status ='pending'";
	$number_of_invites	= @mysql_num_rows(mysql_query($sql));
	$show_invites_count 	= 1;

	//show My favs link
	$show_my_favs 		= 1;

	//show My account link
	$show_my_account 		= 1;

	//do not show signup link
	$show_register 		= '';

} else {

	$show_register 		= 1;
}

?>