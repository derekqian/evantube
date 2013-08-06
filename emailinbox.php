<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////


include_once ('classes/config.php');
include_once ('classes/login_check.php');
include_once ('classes/sessions.php');

///////////////////////////////////
//pagination and displaying results
///////////////////////////////////

$limit = 20; //maximum records per page
$pagination = pagination("SELECT * FROM messages WHERE to_id =$user_id", $limit); //run pagination function

$set_limit = $pagination[0]['set_limit'];
$total_pages = $pagination[0]['total_pages'];
$current_page = $pagination[0]['current_page'];
$next_page = $pagination[0]['next_page'];
$prev_page = $pagination[0]['prev_page'];
$nl = $pagination[0]['nl'];
$pl = $pagination[0]['pl'];




//checking if delete button has been pressed and that atleast one check box selected
////////////////////////////////////////////////////////////////////////////////////

if ($_POST['submitted']=='yes' && !empty($_POST['list'])){

	foreach($_POST['list'] as $email_id){
		$email_id = mysql_real_escape_string($email_id);
		$sql = "DELETE from messages WHERE indexer = $email_id and to_id = $user_id";
		@mysql_query($sql);
	}
$delete_confirmation = 1; //show green box to confirm deletion
}

///////////////////////////////////////////
//run sql again this time getting set pages
///////////////////////////////////////////

$result =array();
$sql = "SELECT * FROM messages WHERE to_id =$user_id LIMIT $set_limit, $limit";
$query = @mysql_query($sql);
while ($result1 = @mysql_fetch_array($query)){

//addition - to make unread email "bold"
if ($result1['email_read'] == 'no'){
$subject_font = 'bold'; //font style in html
}else{
$new_subject = 'normal';
}

//create new array with "bold" subject and insert into overall array
$additional_array = array('font_style'=>$subject_font);
$result1 = @array_merge($additional_array, $result1);

$result[] = $result1;
}
//set condition for hidding certain blocks (e.g "no emails to list")
if (empty($result)){
$show = 1;
}else{
$show = 2;
}



    //////////////////////////////////////////// 
	//PAGINATION PLUS >> start  -- reusable code
    ////////////////////////////////////////////
	$url = 'emailinbox.php'; //the url to be put in links - EDIT ME
	$additional_url_variable = "?page=";//additional string info here , e.g. '&load=groups&friends=all'
    
     include_once('includes/pagination.inc.php');
 	//PAGINATION PLUS >> end
 	

$error_color = $config["error_color_green"];
$notification_type = $config["notification_success"]; //the messsage displayed at the top coner
$error_message = $config["request_completed"];
$blk_notification = $delete_confirmation; //html table - error block
$template = "themes/$user_theme/templates/main_1.htm";
$inner_template1 = "themes/$user_theme/templates/inner_email_inbox.htm"; //middle of page
$TBS = new clsTinyButStrong ;
$TBS->NoErr = true; // no more error message displayed.
$TBS->LoadTemplate("$template") ;
$TBS->MergeBlock('blk1',$result) ;
$TBS->Render = TBS_OUTPUT;
$TBS->Show();

@mysql_close();
die();

?>