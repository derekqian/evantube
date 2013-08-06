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


//check permissions again
/////////////////////////
$usercheck = new LoadPermissions('',$id,'image_album');  //($user_id, $content_id, $content_type);
$usercheck->CheckPermissions();
$usercheck->ActionsCheck('edit'); //Can be ( edit, delete, both ) this returns error and dies if user does not have permissions



// Some basic presets
$show_notification = 0;


///////////////////////
//update mysql database
///////////////////////

if ($_POST["submitted"] == "yes"){

    //get form post (no mysql_real_escapE) so that it can be checked for full completion
    $title = $_POST["title"];
    $description = $_POST["description"];
    $tags = $_POST["tags"];


    //check if form filled in fully
    ////////////////////////////////

    if ($title == "" || $description == "" || $tags == ""){
    //error notification
    $show_notification =1;
    $message = $config["fill_all_fields"]; //fill all fields

    }else{

    //Update Database
    /////////////////

    //get same form as above but process ready for mysql
    $title = @mysql_real_escape_string($title);
    $title_seo = seo_title($title);
    $description = @mysql_real_escape_string($description);
    $tags = @mysql_real_escape_string($tags);
    $allow_comments = mysql_real_escape_string($_POST["allow_comments"]);
    $allow_ratings = mysql_real_escape_string($_POST["allow_ratings"]);
    $public_private = mysql_real_escape_string($_POST["public_private"]);
    $sql = "UPDATE image_galleries SET
	gallery_name ='$title',
	gallery_name_seo ='$title_seo',
	gallery_description ='$description',
	public_private ='$public_private',
	allow_comments ='$allow_comments',
	allow_ratings ='$allow_ratings',
	gallery_tags ='$tags' WHERE	gallery_id =$id";
    @mysql_query($sql);


    //Update images table also
    //////////////////////////
    $sql = "UPDATE images SET gallery_name = '$title',
	public_private ='$public_private',
	allow_ratings ='$allow_ratings',
	allow_comments ='$allow_comments' WHERE gallery_id = $id";
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
//get image details
//////////////////

$sql = "SELECT * FROM image_galleries WHERE gallery_id = $id";
$query = @mysql_query($sql);
$result = @mysql_fetch_array($query);
$title = $result["gallery_name"];
$description = $result["gallery_description"];
$tags = $result["gallery_tags"];
$allow_comments = 'allow_comments_' . $result["allow_comments"];
$allow_ratings = 'allow_ratings_' . $result["allow_ratings"];
$public_private = 'public_private_' . $result["public_private"];

    //seting "selected" value for HTML pull down lists
    $$allow_comments = "selected";
    $$allow_ratings  = "selected";
    $$public_private = "selected";


if(isset($_POST['submit'])){
    //get form post (no mysql_real_escapE)
    $title = $_POST["title"];
    $description = $_POST["description"];
    $tags = $_POST["tags"];
}

////////////
//disply page
////////////
$template = "templates/inner_edit_album.htm"; //middle of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true; // no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->Render = TBS_OUTPUT;
$TBS->tbs_show();
@mysql_close();
die();


?>