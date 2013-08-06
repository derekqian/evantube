<?php

include_once('../classes/config.php');
include_once('../classes/permissions.php');

$id = mysql_real_escape_string($_GET['id']);

if (!empty($_POST))
{
    $id = mysql_real_escape_string($_POST['id']);
}

// check if uid is in the requesting url
if ($id == "") {
    ErrorDisplay1($config["invalid_request"]);
    die();
}

/*
//check permissions again
/////////////////////////
$usercheck = new LoadPermissions('',$id,'audio');  //($user_id, $content_id, $content_type);
$usercheck->CheckPermissions();
$usercheck->ActionsCheck('edit'); //Can be ( edit, delete, both ) this returns error and dies if user does not have permissions
*/


// Some basic presets
$show_notification = 0;


///////////////////////
//update mysql database
///////////////////////

if ($_POST["submitted"] == "yes"){

    //get form post (no mysql_real_escapE) so that it can be checked for full completion
    $group_name = $_POST["group_name"];
    $group_description = $_POST["group_description"];



    //check if form filled in fully
    ////////////////////////////////

    if ($group_name == "" || $group_description == ""){
    //error notification
    $show_notification =1;
    $message = $config["fill_all_fields"]; //fill all fields

    }else{

    //Update Database
    /////////////////

    //get same form as above but process ready for mysql
    $group_name = @mysql_real_escape_string($group_name);
    $group_name_seo = seo_title($group_name);
    $group_description = @mysql_real_escape_string($group_description);
    $public_private = mysql_real_escape_string($_POST["public_private"]);
    $sql = "UPDATE group_profile SET
	group_name ='$group_name',
	group_name_seo ='$group_name_seo',
	group_description ='$group_description',
	public_private ='$public_private'
	WHERE indexer =$id";
    @mysql_query($sql);

    if(mysql_error()){
    $show_notification =1;
    $message = $config["error_26"]; //error
    }else{
    $show_notification =1;
    $message = $config["error_25"]; //request success
    }

}
}



//////////////////
//get Group details
//////////////////

$sql = "SELECT * FROM group_profile WHERE indexer = $id";
$query = @mysql_query($sql);
$result = @mysql_fetch_array($query);
$group_name = $result["group_name"];
$group_description = $result["group_description"];
$public_private = 'public_private_' . $result["public_private"];

    //seting "selected" value for HTML pull down lists
    $$public_private = "selected";

if(isset($_POST['submit'])){
    //get form post (no mysql_real_escapE) so that it can be checked for full completion
    $group_name = $_POST["group_name"];
    $group_description = $_POST["group_description"];
}

////////////
//disply page
////////////
$template = "templates/inner_edit_groups.htm"; //middle of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true; // no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->Render = TBS_OUTPUT;
$TBS->tbs_show();
@mysql_close();
die();


?>