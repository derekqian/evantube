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
include_once ('classes/login_check.php');
include_once ('online.php');


// load required javascripts used in main_1.htm
$thickbox		= 1;
$greybox		= 1;

//___________Check for Powertools___________________

//check for InviteMyContacts
if(file_exists('addons/getmycontacts/index.php')){
$powertool_invitemycontacts = 1;
}
//___________Check for Powertools___________________


//check if any error messages are being passed to this page before it loads
$codes = mysql_real_escape_string($_GET['code']);
if ($codes != "") {
    $error_code = errorcodes($codes);
    if (!empty($error_code)) {
        $blk_notification = $error_code['error_display'];
        $message_type = $error_code['error_type'];
        $error_message =$error_code['error_message'];
        $display_error = 1;
    }
}

$sql = "SELECT * FROM member_profile WHERE user_id = $user_id AND random_code = '$user_random_code'";
$query = @mysql_query($sql);
$outcome = @mysql_fetch_array($query);
$result = $outcome["account_status"];
@mysql_close();

//display page
///////////////////////
$template = "themes/$user_theme/templates/main_1.htm";
$inner_template1 = "themes/$user_theme/templates/inner_welcome.htm";//middle of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->Render = TBS_OUTPUT;
$TBS->Show();

@mysql_close();
die();

?>
