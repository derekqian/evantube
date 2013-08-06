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


////////////////////////
//groups top menu setter
if ($_SESSION['user_id'] != ""){
$loggedin = 2; //sets the top menu (groups)
}



$section = mysql_real_escape_string($_GET['section']);
$action = mysql_real_escape_string($_GET['action']);
$other = mysql_real_escape_string($_GET['other']);

//check if I am group admin to load admin features------------------------------------------------------------------------
$admin = 'no';
$delete_group_video = "";//show option to delete on each listed video
if ($user_id != "") {
    $sql = "SELECT * FROM group_membership WHERE group_id = $group_id AND member_id = $user_id AND group_admin = 'yes'";
    $query = @mysql_query($sql);
    $am_i_admin = @mysql_num_rows($query);
    if ($am_i_admin == 0) {
        $admin = 'no';
        $delete_group_video = "";//show option to delete on each listed video
    }
    else {
        $admin = 'yes';
        $delete_group_video = $config["delete_general"];//show option to delete on each listed video
    }
}
//------------------------------------------------------------------------------------------------------------------------

//pending member
if ($admin = 'yes') {//am i admin
    if ($section == 'pending' && $action = '') {

    }
}

$template = "themes/$user_theme/templates/inner_group_admin_ajax.htm";
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->MergeBlock('blk3', $result);

$TBS->Render = TBS_OUTPUT;
$TBS->Show();
@mysql_close();
die();

?>