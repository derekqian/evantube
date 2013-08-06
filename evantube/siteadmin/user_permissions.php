<?php

 
include_once ("../classes/config.php");
include_once ("includes/inc.stats.php");
include_once ("includes/functions.php");
include_once ('includes/login_check.php');

//Menu Loaders
///////////////
$top_menu = 'permissions';
$side_menu = 'permissions';
$dashboard_header = $lang_user_permissions;

/////////////////////
//defaults settings /
/////////////////////
$show_hide = 1;
$show_notification = 0;
$todays_date = $config["date_format"];
$base_url = $config['site_base_url'];
$limit = $config["admin_maximum_display"];
$result_active = array();
$show_content_menu = 1;//top tabs on content box (disabled)
$proceed = true;

////////////////
//check for type
////////////////

if (isset($_POST['type'])) {

    $type = $_POST['type'];
}
else {
    $type = $_GET['type'];
}

if ($type == 1 | $type == 2 || $type == 3) {
    $show_t = $type;
}
else {
    $show_t = 1;
}

switch ($show_t) {

case 1:
    $user_type = 'member';
    break;

case 2:
    $user_type = 'standard_mod';
    break;

case 3:
    $user_type = 'global_mod';
    break;
}

//Check Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

if (isset($_POST['update'])) {

foreach($_POST as $key=>$value){

if ($key == 'update' || $key == 'type'){
//do nothing
}else{

//build sql query

$sql_part = $sql_part.$key." = '".$value."',";

}
}
$sql_part = substr($sql_part,0,-1);

//update dbase
$sql = "UPDATE permissions SET $sql_part WHERE user_group = '$user_type'";
@mysql_query($sql);
//notifications
$show_notification =1;
$message = notifications(1);

}

//Display Results >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

//MENU LOADER
/////////////

include_once ("includes/menuloader.php");

if ($proceed == true) {

    $sql = "SELECT * FROM permissions WHERE user_group = '$user_type'";
    $query = @mysql_query($sql);
    $result = @mysql_fetch_array($query);
   
   foreach($result as $key=>$value){
   $$key = $value;
   
   if($value == 1){
   $select = $key.'_yes';
   $$select = 'selected';
   }else{
   $select = $key.'_no';
   $$select = 'selected';     
   }    
}
}

////////////////////////////////
//display form with error message
////////////////////////////////
$template = "templates/main.html";
$inner_template1 = "templates/inner_permissions.htm";//middle of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
//$TBS->MergeBlock('adminblk', $result);
$TBS->Render = TBS_OUTPUT;
$TBS->Show();
@mysql_close();
die();

?>