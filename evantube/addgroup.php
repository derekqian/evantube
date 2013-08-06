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

$referer	= $_SERVER['HTTP_REFERER'];
$submitted	= $_POST['submitted'];
$result 	= array();
$which_type = $config['create_new_group'];

if ($user_id == '') {
	@mysql_close();
    	header("Location: $login_out_link");;
	die();
}

if ($user_id != '') {

	if ($submitted == 'yes') {

      	//check if form is complete
        	$group_name			= mysql_real_escape_string($_POST['group_name']);
        	$group_description 	= mysql_real_escape_string($_POST['group_description']);
        	$public_private 		= mysql_real_escape_string($_POST['public_private']);

        	if ( $group_name == '' || $group_description == '' ) {
            	$show_form		= 1;

            	$blk_notification = 1;
            	$message_type 	= $config['notification_error'];
            	$error_message 	= $config['fill_all_fields'];

            	$template 		= "themes/$user_theme/templates/main_1.htm";
            	$inner_template1 	= "themes/$user_theme/templates/inner_groups_create.htm";

            	$TBS			= new clsTinyButStrong;
            	$TBS->NoErr 	= true;

            	$TBS->LoadTemplate("$template");
            	$TBS->MergeBlock('blkfeatured',$result);
            	$TBS->Render 	= TBS_OUTPUT;
            	$TBS->Show();

			@mysql_close();
            	die();
      	}

		// check if group name exists
      	$sql		= "SELECT indexer FROM group_profile WHERE group_name = '$group_name'";
        	$query 	= @mysql_query($sql);

        	if (@mysql_num_rows($query) != 0) {

            	//display error page
	            $show_form		= 1;
	            $blk_notification = 1;
	            $message_type	= $config['notification_error'];
            	$error_message 	= $config['group_name_exits'];

            	$template		= "themes/$user_theme/templates/main_1.htm";
            	$inner_template1 	= "themes/$user_theme/templates/inner_groups_create.htm";//middle of page
            	$TBS 			= new clsTinyButStrong;
            	$TBS->NoErr 	= true;

            	$TBS->LoadTemplate("$template");
            	$TBS->MergeBlock('blkfeatured',$result);
            	$TBS->Render 	= TBS_OUTPUT;
            	$TBS->Show();

            	@mysql_close();
            	die();
        	}

        	// else add new group
        	$sql = "INSERT into group_profile (group_name, public_private, todays_date, group_description, admin_id) VALUES
        						    ('$group_name', '$public_private', NOW(), '$group_description', $user_id)";

        	$query = @mysql_query($sql);

        	// get new groups unique id
        	$sql		= "SELECT indexer FROM group_profile WHERE group_name = '$group_name'";
        	$query 	= mysql_query($sql);
        	$result1 	= mysql_fetch_array($query);
        	$group_id 	= $result1['indexer'];

        	// add this member into group membership as group admin
        	$user_id	= (int) mysql_real_escape_string($user_id);
        	$user_name 	= mysql_real_escape_string($user_name);

        	$sql		= "INSERT into group_membership (member_id, group_admin, group_id, today_date, member_username, approved) VALUES
        								  ('$user_id', 'yes', $group_id, NOW(), '$user_name', 'yes')";

        	@mysql_query($sql);

        	// display sucess page
        	$show_form		= 0;
        	$blk_notification = 1;
        	$message_type 	= $config['notification_success'];
        	$error_message 	= $config['new_group_created'];

        	$template 		= "themes/$user_theme/templates/main_1.htm";
        	$inner_template1 	= "themes/$user_theme/templates/inner_groups_create.htm";

        	$TBS 			= new clsTinyButStrong;
        	$TBS->NoErr 	= true;
        	$TBS->LoadTemplate("$template");
        	$TBS->MergeBlock('blkfeatured',$result);
        	$TBS->Render 	= TBS_OUTPUT;
        	$TBS->Show();

        	@mysql_close();
        	die();

	} else {

		// load main page with form to add new group
		$show_form		= 1;
		$template 		= "themes/$user_theme/templates/main_1.htm";
        	$inner_template1 	= "themes/$user_theme/templates/inner_groups_create.htm";

        	$TBS 			= new clsTinyButStrong;
        	$TBS->NoErr 	= true;

        	$TBS->LoadTemplate("$template");
        	$TBS->MergeBlock('blkfeatured',$result);
        	$TBS->Render 	= TBS_OUTPUT;
        	$TBS->Show();

        	@mysql_close();
        	die();
	}

}


?>

