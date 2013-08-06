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

//get msg id or set to msg 0
if ($_GET['msg'] == "") {
    $indexer = 0;
}
else {
    $indexer = mysql_real_escape_string($_GET['msg']);
}

//checking if delete button has been pressed - delete - and display inbox
//////////////////////////////////////////////////////////////

if ($_POST['submitted'] == 'yes') {
    $sql = "DELETE from messages_sent WHERE indexer = $email_id and from_username = '$user_name'";
    @mysql_query($sql);

    $notification_type = $config["notification_success"];//the messsage displayed at the top coner
    $error_message = $config["request_completed"];
    $blk_notification = 1;//html table - error block
    $template = "themes/$user_theme/templates/main_1.htm";
    $inner_template1 = "themes/$user_theme/templates/inner_email_sent.htm";//middle of page
    $inner_template2 = "themes/$user_theme/templates/inner_blank.htm";// bottom of page
    $TBS = new clsTinyButStrong;
    $TBS->NoErr = true;// no more error message displayed.
    $TBS->LoadTemplate("$template");
    $TBS->Render = TBS_OUTPUT;
    $TBS->Show();

    @mysql_close();
    die();
}

///////////////////////////////////////////
//show message
///////////////////////////////////////////

$sql = "SELECT * FROM messages_sent WHERE from_id =$user_id AND indexer = $indexer";
$query = @mysql_query($sql);
$result = @mysql_fetch_array($query);
$message = $result['message'];
$subject = $result['subject'];
$email_id = $result['indexer'];

///////////////////////////////
//show inbox if error on msg id
///////////////////////////////

if ($result['indexer'] !== $indexer) {
    header("Location: " . "emailinbox.php");

}

$which_box = "emailsentitems.php";
$to_or_from = $lang_to;
$location = $lang_sent;
$from = $result['to_username'];
$subject = $result['subject'];
$message = $result['message'];

$template = "themes/$user_theme/templates/main_1.htm";
$inner_template1 = "themes/$user_theme/templates/inner_email_read.htm";//middle of page
$inner_template2 = "themes/$user_theme/templates/inner_blank.htm";// bottom of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->Render = TBS_OUTPUT;
$TBS->Show();

@mysql_close();
die();

?>