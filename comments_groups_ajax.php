<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once('classes/config.php');
include_once('classes/sessions.php');

$wrap_limit = 80; //for wrapping comments
$group_id = mysql_real_escape_string($_GET['gid']);
$action = mysql_real_escape_string($_GET['action']);
$msg_id = mysql_real_escape_string($_GET['msg_id']);
$admin_delete_messages = ""; //only admins can see the "delete" option for messages
$message_deleted = "";

//check if I am deleting messages
if ($action == 'delete' && $msg_id !=""){

//check if I am group admin to load admin features
$sql = "SELECT * FROM group_membership WHERE group_id = $group_id AND member_id = $user_id AND group_admin = 'yes'";
$query = @mysql_query($sql);
$am_i_admin = @mysql_num_rows($query);
if ($am_i_admin != 0) {
//delete message
		$sql = "DELETE from group_comments WHERE indexer = $msg_id";
		@mysql_query($sql);
$message_deleted = '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['comment_has_been_deleted'].'</b></font>';
$admin_delete_messages = $config["delete_general"];
}
}


//get group comments
$limit2 = $config["comment_page_limits"];
$pagination2 = pagination("SELECT * FROM group_comments WHERE group_id = $group_id ORDER BY indexer DESC", $limit2);
$set_limit2 = $pagination2[0]['set_limit'];
$total_pages2 = $pagination2[0]['total_pages'];
$current_page2 = $pagination2[0]['current_page'];
$total_records2 = $pagination2[0]['total_records'];
$next_page2 = $pagination2[0]['next_page'];//use in html navigation (src)
$prev_page2 = $pagination2[0]['prev_page'];//use in html navigation (src)
$nl2 = $pagination2[0]['nl'];//use in html navigation: next>>
$pl2 = $pagination2[0]['pl'];//use in html navigation: <<previous

$result_search3 = array();
$sql12 = "SELECT * FROM group_comments WHERE group_id = $group_id ORDER BY indexer DESC LIMIT $set_limit2, $limit2";
$query12 = @mysql_query($sql12);
    //create new array with "wrapped" comments
    while ($result1 = @mysql_fetch_array($query12)) {

        $text = $result1['comments'];
        $wrap = wordwrap($text,$wrap_limit," ",true);
        $each_comment = array('by_id' => $result1['by_id'],'indexer' => $result1['indexer'],
            'comments' => $wrap,'todays_date' => $result1['todays_date'],
            'by_username' => $result1['by_username']);

        $result_search3[] = $each_comment;
    }
    //end of comments and "wrap"
//set condition for hidding certain blocks (e.g "no emails to list")
if (empty($result_search3)) {
    $show_c1 = 1;
}
else {
    $show_c1 = 2;
}
echo $message_deleted;
$template = "themes/$user_theme/templates/inner_comments_groups_ajax.htm";
$TBS = new clsTinyButStrong ;
$TBS->NoErr = true; // no more error message displayed.
$TBS->LoadTemplate("$template") ;
$TBS->MergeBlock('blk3',$result_search3) ;
$TBS->Render = TBS_OUTPUT;
$TBS->tbs_show();
@mysql_close;
die();

?>