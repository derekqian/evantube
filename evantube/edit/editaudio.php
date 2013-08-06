<?php

include_once('../classes/config.php');
include_once('../classes/permissions.php');
include_once('../siteadmin/includes/functions.php');

$id = mysql_real_escape_string($_GET['id']);

if (!empty($_POST)){
$id = mysql_real_escape_string($_POST['id']);
}

// check if uid is in the requesting url
if ($id == "") {
    ErrorDisplay1($config["invalid_request"]);
    die();
}

//_____________________________________________________________________________________________________
//______PERMISSIONS CHECK _____________________________________________________________________________
$usercheck = new LoadPermissions('',$id,'audios');  //($user_id, $content_id, $content_type);
$usercheck->CheckPermissions();
$usercheck->ActionsCheck('edit'); //Can be ( edit, delete, both ) dies if user does not have permissions
//_____________________________________________________________________________________________________


// Some basic presets
$show_notification =0;
$aid = $id;


//get audio to play
  $sql = "SELECT * FROM audios WHERE indexer = $aid";
  $query = @mysql_query($sql);
  $result = @mysql_fetch_array($query);
  $audio_play = $result['audio_id'];
//////////////////
//get audio details
//////////////////
if ($_POST["submitted"] != "yes") {

    $sql = "SELECT * FROM audios WHERE indexer = $aid";
    $query = @mysql_query($sql);

    $result = @mysql_fetch_array($query);

    $title = $result["title"];
    $description = $result["description"];
    $tags = $result["tags"];
    $channel = $result["channel"];
    $location_recorded = $result["location_recorded"];
    $allow_comments = 'allow_comments_' . $result["allow_comments"];
    $allow_embedding = 'allow_embedding_' . $result["allow_embedding"];
    $public_private = 'public_private_' . $result["public_private"];

    //get channel data, create "select" form fields to load into form
    $sql = "SELECT * FROM genre";
    $result1 = @mysql_query($sql);
    $fileds_all = "";
    while ($result = @mysql_fetch_array($result1)) {
        if ($result['channel_name'] == $channel) {
            $selected = "selected";
        }
        else {
            $selected = "";
        }
        $field = '<option value="' . $result['channel_id'] . '" ' . $selected . ' >' .
            $result['channel_name'] . '</option>';
        $fields_all = $fields_all . $field;
    }

    //seting "selected" value for HTML pull down lists
    //////////////////////////////////////////////////

    $$allow_comments = "selected";
    $$allow_embedding = "selected";
    $$public_private = "selected";

    ///////////////////
    //disply first page
    ///////////////////

    $template = "templates/inner_edit_audio.htm";//middle of page
    $TBS = new clsTinyButStrong;
    $TBS->NoErr = true;// no more error message displayed.
    $TBS->LoadTemplate("$template");
    $TBS->Render = TBS_OUTPUT;
    $TBS->tbs_show();

    @mysql_close();
    die();
}

///////////////////////
//update mysql database
///////////////////////

if ($_POST["submitted"] == "yes") {
    //get form post (no mysql_real_escapE) so that it can be checked for full completion

    $title = $_POST["title"];
    $description = $_POST["description"];
    $tags = $_POST["tags"];
    $channel = $_POST["channel"];

    //check if I am owner
    $sql = "SELECT * FROM audios WHERE indexer = $aid";
    if (mysql_num_rows(mysql_query($sql)) == 0) {
        $show_notification =1;
    	$message = $config["error_25"]; //request success

        die();
    }

    //check if form filled in fully
    if ($title == "" || $description == "" || $tags == "") {

        //get channel data, create "select" form fields to load into form
        $sql = "SELECT * FROM genre";
        $result1 = @mysql_query($sql);
        $fileds_all = "";
        while ($result = @mysql_fetch_array($result1)) {
            if ($result['channel_name'] == $channel) {
                $selected = "selected";
            }
            else {
                $selected = "";
            }
            $field = '<option value="' . $result['channel_id'] . '" ' . $selected . ' >' .
                $result['channel_name'] . '</option>';
            $fields_all = $fields_all . $field;
        }

        //error fill in all fields
        $show_notification =1;
    	$message = $config["fill_all_fields"]; //fill all fields

        $template = "templates/inner_edit_audio.htm";//middle of page
        $TBS = new clsTinyButStrong;
        $TBS->NoErr = true;// no more error message displayed.
        $TBS->LoadTemplate("$template");
        $TBS->Render = TBS_OUTPUT;
        $TBS->tbs_show();

        @mysql_close();
        die();
    }

    //get same form as above but process ready for mysql
    $title = mysql_real_escape_string($_POST["title"]);
    $title_seo = seo_title($title);
    $description = mysql_real_escape_string($_POST["description"]);
    $tags = mysql_real_escape_string($_POST["tags"]);
    $channel_id = mysql_real_escape_string($_POST["channel"]);
    $location_recorded = mysql_real_escape_string($_POST["location_recorded"]);
    $allow_comments = mysql_real_escape_string($_POST["allow_comments"]);
    $allow_embedding = mysql_real_escape_string($_POST["allow_embedding"]);
    $public_private = mysql_real_escape_string($_POST["public_private"]);


    //get new channel name
    $sql2 = "SELECT channel_name FROM genre WHERE channel_id = $channel_id";
    $result2 = @mysql_fetch_array(@mysql_query($sql2));
    $channel_name = $result2['channel_name'];

    $sql = "UPDATE audios SET title ='$title', title_seo ='$title_seo', description ='$description', tags ='$tags', cat_id =$channel_id,
location_recorded ='$location_recorded', allow_comments = '$allow_comments',
allow_embedding ='$allow_embedding', public_private ='$public_private', channel ='$channel_name' WHERE indexer = $aid";
    @mysql_query($sql);

    //also remove from people who have made this their favorite movie
    if ($public_private == 'private') {
        $sql2 = "DELETE FROM favorites WHERE audio_id = $aid";
        @mysql_query($sql2);
    }

    //display updated audio details
    ////////////////////////////////

    $sql = "SELECT * FROM audios WHERE indexer = $aid";
    $query = @mysql_query($sql);
    $result = @mysql_fetch_array($query);

    $title = $result["title"];
    $description = $result["description"];
    $tags = $result["tags"];
    $channel = $result["channel"];
    $location_recorded = $result["location_recorded"];
    $allow_comments = 'allow_comments_' . $result["allow_comments"];
    $allow_embedding = 'allow_embedding_' . $result["allow_embedding"];
    $public_private = 'public_private_' . $result["public_private"];

    //get channel data, create "select" form fields to load into form
    $sql = "SELECT * FROM genre";
    $result1 = @mysql_query($sql);
    $fileds_all = "";
    while ($result = @mysql_fetch_array($result1)) {
        if ($result['channel_name'] == $channel) {
            $selected = "selected";
        }
        else {
            $selected = "";
        }
        $field = '<option value="' . $result['channel_id'] . '" ' . $selected . ' >' .
            $result['channel_name'] . '</option>';
        $fields_all = $fields_all . $field;
    }

    //seting "selected" value for HTML pull down lists
    //////////////////////////////////////////////////

    $$allow_comments = "selected";
    $$allow_embedding = "selected";
    $$public_private = "selected";


if(isset($_POST['submit'])){
    //get form post (no mysql_real_escapE)
    $title = $_POST["title"];
    $description = $_POST["description"];
    $tags = $_POST["tags"];
    $channel = $_POST["channel"];
}

    //////////////////////////////////
    //displaying page with new updates

        $show_notification =1;
    	$message = $config["error_25"]; //request success

    $template = "templates/inner_edit_audio.htm";//middle of page
    $TBS = new clsTinyButStrong;
    $TBS->NoErr = true;// no more error message displayed.
    $TBS->LoadTemplate("$template");
    $TBS->Render = TBS_OUTPUT;
    $TBS->tbs_show();
    @mysql_close();
    die();
}


?>