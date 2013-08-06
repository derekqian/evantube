<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////


$user_id = $_SESSION['user_id'];

if ( defined('SMF_INSTALLED') ) {

     	$smf_sql = "DELETE FROM smf_log_online WHERE ID_MEMBER = $user_id";
     	@mysql_query($smf_sql);

     	$vid_sql = "DELETE FROM online WHERE logged_in_id = $user_id";
	@mysql_query($vid_sql);

     	foreach ( $_COOKIE as $key => $value ) {
     		$logout = '';
    		setcookie($key, $logout);
    	}
    	foreach ($_SESSION as $key => $value) {
   		$_SESSION[$key] 	= NULL;
		unset($_SESSION[$key]);
	}

} else {

	$sql = "DELETE FROM online WHERE logged_in_id = $user_id";
	@mysql_query($sql);
	@mysql_close();


	$user_id				= '';
	$user_name				= '';
	$_SESSION['user_id'] 		= NULL;
	$_SESSION['user_name'] 		= NULL;
	$_SESSION['user_group'] 	= NULL;
	$_SESSION['admin_logged']	= NULL;

	@session_start();
	@session_destroy();

	foreach ( $_COOKIE as $key => $value ) {
     		$logout = '';
    		setcookie($key, $logout);
    	}
    	foreach ($_SESSION as $key => $value) {
   		$_SESSION[$key] 	= NULL;
		unset($_SESSION[$key]);
	}



}

header("Location: " . "index.php");

?>