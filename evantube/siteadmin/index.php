<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once ('../classes/config.php');
include_once ('includes/functions.php');
include_once ('includes/inc.stats.php');
include_once ('includes/menuloader.php');
include_once ('thirdparty/lastRSS.php');

//Menu Loaders
///////////////
$side_menu = 'index';
$dashboard_header = $lang_welcome;

$user_name 		= mysql_real_escape_string($_POST['username']);
$password		= mysql_real_escape_string($_POST['password']);
$smf_password	= mysql_real_escape_string($_POST['password']);

/////////////////
//default setting
/////////////////
$show_right_top_menu = 0;//disable top right hand sub menu
$show_news = 1;

//check if setup folder has been deleted
////////////////////////////////////////////////
$base_path = installation_paths();
$setup_file = $base_path.'/setup/index.php';
if (file_exists($setup_file)) {
    echo '
<div align="center">
	&nbsp;<p>&nbsp;</p>
	<table width="556" id="table1" style="border: 1px solid #FF3300; " cellspacing="0" cellpadding="0" bgcolor="#FFDDDD">
		<tr>
			<td bgcolor="#FFECEC">
			<table border="0" width="100%" id="table2" cellspacing="0" cellpadding="0">
				<tr>
					<td width="30">
			<img border="0" src="images/icon_info.gif"></td>
					<td height="20">
					<p align="center"><font face="Arial" size="2">'.$lang_important.'!! - '.$lang_you_must_delete_setup.
        '</font></td>
				</tr>
			</table>
			</td>
		</tr>
	</table>
</div>
<p align="center"><font color="#FF0000"><a href="index.php">
<font color="#000080" face="Arial">'.$lang_retry.'</font></a></font></p>';
    die();
}

//Display login page
////////////////////
if ($_POST['submitted'] != "yes" && $_SESSION["admin_logged"] == "") {//done

    $admin_logout = '';
    $show_hide = 2;//show only login table section
    $template = "templates/main.html";
    $template_inner = '';
    $TBS = new clsTinyButStrong;
    $TBS->NoErr = true;
    $TBS->LoadTemplate("$template");
    $TBS->Render = TBS_OUTPUT;
    $TBS->Show();
    @mysql_close();
    die();

}

if ($_POST['submitted'] != "yes" && $_SESSION["admin_logged"] == "ok") {

    ///////////////////
    //disply home page
    ///////////////////

    include_once ('forum_rss_feed.php');

    $show_hide = 1;
    $show_notification = 0;
    $template = "templates/main.html";
    $TBS = new clsTinyButStrong;
    $TBS->NoErr = true;// no more error message displayed.
    $TBS->LoadTemplate("$template");
    $TBS->Render = TBS_OUTPUT;
    $TBS->Show();

    @mysql_close();
    die();
}

if ($_POST["submitted"] == "yes") {//done

    if ($_POST['username'] == "" || $_POST['password'] == "") {

        ////////////////////////////////
        //display form with error message
        ////////////////////////////////
        $message = "Incorrect Login";

        $message_type = "fail";

        $show_hide = 2;//show only login table section
        $show_notification = 1;
        $template = "templates/main.html";
        $TBS = new clsTinyButStrong;
        $TBS->NoErr = true;// no more error message displayed.
        $TBS->LoadTemplate("$template");
        $TBS->Render = TBS_OUTPUT;
        $TBS->Show();

        @mysql_close();
        die();
    }
}

//////////////////////////////////////////////////
//GET LOGIN INFORMATION AND CHECK AGAINST DATABASE
//////////////////////////////////////////////////

$password	= md5($password);
$sql 		= "SELECT * FROM member_profile WHERE user_name = '$user_name' AND password = '$password' AND user_group ='admin'";
$query 	= @mysql_query($sql);

if (mysql_num_rows($query) != 0) {

	//get user id for admin (should be 1 most times...but we will still get it

    	$results = @mysql_fetch_array($query);
    	$user_id = $results['user_id'];
    	$random_code = $results['random_code'];

    	//register admin side sessions
    	@session_start();
    	@session_register("admin_logged");
    	$_SESSION["admin_logged"] = "ok";

    	//register user side sessions
    	@session_register('user_id');
    	@session_register('random_code');
    	@session_register('user_name');
    	$_SESSION['random_code'] = $random_code;
    	$_SESSION['user_id'] = $user_id;
    	$_SESSION['user_name'] = $user_name;

    	include_once ('forum_rss_feed.php');

    	//account OK - Load main
    	/////////////////////////
    	$show_hide = 1;
    	$show_notification = 0;
    	$template = "templates/main.html";
    	$TBS = new clsTinyButStrong;
    	$TBS->NoErr = true;// no more error message displayed.
    	$TBS->LoadTemplate("$template");
    	$TBS->Render = TBS_OUTPUT;
   	$TBS->Show();

    	@mysql_close();
    	die();

} else {


	if ( $enable_forum == 1 ) {

		$smf_password	= sha1(strtolower($user_name) . $smf_password);
		$smf_password	= substr($smf_password, 0, 32);
		$sql 			= "SELECT * FROM member_profile WHERE user_name = '$user_name' AND password = '$smf_password' AND user_group ='admin'";
		$query 		= @mysql_query($sql);

		if (mysql_num_rows($query) != 0) {

			//get user id for admin (should be 1 most times...but we will still get it
    			$results = @mysql_fetch_array($query);
    			$user_id = $results['user_id'];
    			$random_code = $results['random_code'];

    	    		@session_start();
    			@session_register("admin_logged");
    			$_SESSION["admin_logged"] = "ok";
    			@session_register('user_id');
    			@session_register('random_code');
    			@session_register('user_name');
    			$_SESSION['random_code'] = $random_code;
    			$_SESSION['user_id'] = $user_id;
   	 		$_SESSION['user_name'] = $user_name;

    			include_once ('forum_rss_feed.php');

    			$show_hide = 1;
    			$show_notification = 0;
    			$template = "templates/main.html";
    			$TBS = new clsTinyButStrong;
    			$TBS->NoErr = true;
    			$TBS->LoadTemplate("$template");
    			$TBS->Render = TBS_OUTPUT;
   			$TBS->Show();

    			@mysql_close();
    			die();

    		} else {
			$message = "Incorrect Login";
    			$message_type = "fail";
    			$show_hide = 2;
    			$show_notification = 1;
    			$template = "templates/main.html";
    			$TBS = new clsTinyButStrong;
    			$TBS->NoErr = true;
    			$TBS->LoadTemplate("$template");
    			$TBS->Render = TBS_OUTPUT;
    			$TBS->Show();
    			die();
    		}

    	} else {
		$message = "Incorrect Login";
    		$message_type = "fail";
    		$show_hide = 2;
    		$show_notification = 1;
    		$template = "templates/main.html";
    		$TBS = new clsTinyButStrong;
    		$TBS->NoErr = true;
    		$TBS->LoadTemplate("$template");
    		$TBS->Render = TBS_OUTPUT;
    		$TBS->Show();
    		die();
    	}
}
?>