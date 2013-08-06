<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once('classes/config.php');
include_once('classes/login_check.php');
include_once('classes/sessions.php');

//check if accept lick has been clicked

if ($_GET['act'] == 1) {
    $indexer = mysql_real_escape_string($_GET['id']);
    if ($indexer != "") {
        $sql = "SELECT * FROM friends WHERE indexer =$indexer AND friends_id = $user_id AND invitation_status ='pending'";
        $result = @mysql_query($sql);
        if (@mysql_num_rows($result) == 0) {
            $notification_type = $config["notification_error"];//the messsage displayed at the top coner
            $error_message = $config["v_friends_invite_error"];
            $blk_notification = 1;//html table - error block
            $template = "themes/$user_theme/templates/main_1.htm";
            $inner_template1 = "themes/$user_theme/templates/inner_friends_invites_received.htm";//middle of page
            $inner_template2 = "themes/$user_theme/templates/inner_blank.htm";// bottom of page
            $TBS = new clsTinyButStrong;
            $TBS->NoErr = true;// no more error message displayed.
            $TBS->LoadTemplate("$template");
            $TBS->MergeBlock('blk1', $result);
            $TBS->Render = TBS_OUTPUT;
            $TBS->Show();

            @mysql_close();
            die();
        }
        else {
            $sql = "UPDATE friends SET invitation_status = 'accepted' WHERE indexer =$indexer AND friends_id = $user_id";
            @mysql_query($sql);
        }
    }
}

///////////////////////////////////
//pagination and displaying results
///////////////////////////////////

$limit = 20;//maximum records per page
$pagination = pagination("SELECT * FROM friends WHERE friends_id =$user_id AND invitation_status ='pending'",
    $limit);//run pagination function

$set_limit = $pagination[0]['set_limit'];
$total_pages = $pagination[0]['total_pages'];
$current_page = $pagination[0]['current_page'];
$next_page = $pagination[0]['next_page'];
$prev_page = $pagination[0]['prev_page'];
$nl = $pagination[0]['nl'];
$pl = $pagination[0]['pl'];

//checking if delete button has been pressed and that atleast one check box selected
////////////////////////////////////////////////////////////////////////////////////

if ($_POST['submitted'] == 'yes' && !empty($_POST['list'])) {

    foreach ($_POST['list'] as $indexer) {
        $indexer = mysql_real_escape_string($indexer);
        $sql = "DELETE from friends WHERE indexer = $indexer and friends_id = $user_id";
        @mysql_query($sql);
    }
    $delete_confirmation = 1;//show green box to confirm deletion
}

///////////////////////////////////////////
//run sql again this time getting set pages
///////////////////////////////////////////

$result = array();
$sql = "SELECT * FROM friends WHERE friends_id =$user_id AND invitation_status ='pending' LIMIT $set_limit, $limit";
$query = @mysql_query($sql);
while ($result1 = @mysql_fetch_array($query)) {
    $result[] = $result1;
}

//set condition for hidding certain blocks (e.g "no emails to list")
if (empty($result)) {
    $show = 1;
}
else {
    $show = 2;
}


    ////////////////////////////////////////////
	//PAGINATION PLUS >> start  -- reusable code
    ////////////////////////////////////////////
	$url = 'inviteread.php'; //the url to be put in links - EDIT ME
	$additional_url_variable = "?page=";//additional string info here , e.g. '&load=groups&friends=all'

     include_once('includes/pagination.inc.php');
 	//PAGINATION PLUS >> end

//$error_color = $config["error_color_green"];
$notification_type = $config["notification_success"];//the messsage displayed at the top coner
$error_message = $config["request_completed"];
$blk_notification = $delete_confirmation;//html table - error block
$template = "themes/$user_theme/templates/main_1.htm";
$inner_template1 = "themes/$user_theme/templates/inner_friends_invites_received.htm";//middle of page
$inner_template2 = "themes/$user_theme/templates/inner_blank.htm";// bottom of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->MergeBlock('blk1', $result);
$TBS->Render = TBS_OUTPUT;
$TBS->Show();

@mysql_close();
die();

?>
