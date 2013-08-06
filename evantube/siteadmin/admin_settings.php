<?php

/**
* @author PHPmotion.com
* @copyright 2008
*/

include_once ("../classes/config.php");
include_once ("includes/inc.stats.php");
include_once ("includes/functions.php");
include_once ('includes/login_check.php');


/////////////////////
//defaults settings /
/////////////////////
$show_hide = 1;
$show_notification = 0;
$todays_date = $config["date_format"];
$base_url = $config['site_base_url'];


//GENERAL SETTINGS
//////////////////

$sql = "SELECT * FROM member_profile WHERE user_id = $admin_id";
$result = @mysql_query($sql);
$result = @mysql_fetch_array($result);

$admin_user_name = $result['user_name'];
$admin_email = $result['email_address'];
$admin_password_original = $result['password'];
$admin_user_name_original = $result['user_name'];


//check if form has been posted

if(isset($_POST['update'])){
$proceed = true;
$admin_user_name = $_POST['admin_user_name'];
$admin_email = $_POST['admin_email'];
$admin_password = $_POST['admin_password'];
$current_password = $_POST['current_password'];

if($admin_user_name == '' || $admin_email=='' || $current_password==''){
$show_notification = 1;
$message = $config['fill_all_fields'];
$proceed = false;
}

if($proceed == true && !eregi("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-z]{2,3})$", $admin_email)){
$show_notification = 1;
$message = $config['invalid_email_format'];
$proceed = false;
}

if($proceed == true && md5($current_password) != $admin_password_original ){
$show_notification = 1;
$message = $config["incorrect_password"];
$proceed = false;
}

//UPDATE ADMIN DETAILS AND REST OF CONTENT
//////////////////////////////////////////

if($proceed == true){
	
if($admin_password == ''){
$sql = "UPDATE member_profile SET user_name ='$admin_user_name', email_address ='$admin_email' WHERE user_id = $admin_id";
@mysql_query($sql);
}else{
$new_password = md5($admin_password);
$sql = "UPDATE member_profile SET user_name ='$admin_user_name', email_address ='$admin_email', password = '$new_password' WHERE user_id = $admin_id";
@mysql_query($sql);
}

//______________________________________________________________________________________________________________________
//___Mass update on change of UserName reusable for future changes______________________________________________________

//Update audiocomments Comments
$sql = "UPDATE audiocomments SET by_username ='$admin_user_name' WHERE by_id = $admin_id";
@mysql_query($sql);

//Update audiocomments_replys Comments
$sql = "UPDATE audiocomments_replys SET by_username ='$admin_user_name' WHERE by_id = $admin_id";
@mysql_query($sql);

//Update Blogs
$sql = "UPDATE blogs SET blog_owner ='$admin_user_name' WHERE user_id = $admin_id";
@mysql_query($sql);

//Update Blog_replys Comments
$sql = "UPDATE blog_replys SET by_username ='$admin_user_name' WHERE by_id = $admin_id";
@mysql_query($sql);

//Update friends Comments
$sql = "UPDATE friends SET my_username ='$admin_user_name' WHERE user_id = $admin_id OR friends_id = $admin_id";
@mysql_query($sql);

//Update group_comments Comments
$sql = "UPDATE group_comments SET by_username ='$admin_user_name' WHERE by_id = $admin_id";
@mysql_query($sql);

//Update group_membership Comments
$sql = "UPDATE group_membership SET member_username ='$admin_user_name' WHERE member_id = $admin_id";
@mysql_query($sql);

//Update imagecomments Comments
$sql = "UPDATE imagecomments SET by_username ='$admin_user_name' WHERE by_id = $admin_id";
@mysql_query($sql);

//Update imagecomments_replys Comments
$sql = "UPDATE imagecomments_replys SET by_username ='$admin_user_name' WHERE by_id = $admin_id";
@mysql_query($sql);

//Update messages Comments
$sql = "UPDATE messages SET from_username ='$admin_user_name' WHERE from_username = $admin_user_name_original"; //update for general usage
@mysql_query($sql);

//Update imagecomments Comments
$sql = "UPDATE messages_sent SET to_username ='$admin_user_name' WHERE to_username = $admin_user_name_original"; //update for general usage
@mysql_query($sql);

//Update profilecomments Comments
$sql = "UPDATE profilecomments SET by_username ='$admin_user_name' WHERE by_id = $admin_id";
@mysql_query($sql);

//Update profilecomments_replys Comments
$sql = "UPDATE profilecomments_replys SET by_username ='$admin_user_name' WHERE by_id = $admin_id";
@mysql_query($sql);

//Update videocomments Comments
$sql = "UPDATE videocomments SET by_username ='$admin_user_name' WHERE by_id = $admin_id";
@mysql_query($sql);

//Update videocomments Comments
$sql = "UPDATE videocomments SET by_username ='$admin_user_name' WHERE by_id = $admin_id";
@mysql_query($sql);

//______________________________________________________________________________________________________________________
//___Mass update on change of UserName END______________________________________________________________________________

$show_notification = 1;
$message = $config['error_25']; //request completed
}

}

////////////////////////////////
//display form with error message
////////////////////////////////
$show_content_menu = 0;//display top tabs as set in /includes/menuloader
$template = "templates/main.html";
$inner_template1 = "templates/inner_admin_settings.html";//middle of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->Render = TBS_OUTPUT;
$TBS->Show();
@mysql_close();
die();




?>