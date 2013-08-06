<?php

include_once('../classes/config.php');
include_once('../classes/permissions.php');

$id = mysql_real_escape_string($_GET['id']);

if (!empty($_POST))
{
    $id = mysql_real_escape_string($_POST['id']);
}

// check if uid is in the requesting url
if ($id == '') {
    ErrorDisplay1($config['invalid_request']);
    die();
}

//check permissions again
/////////////////////////
$usercheck = new LoadPermissions('',$id,'images');  //($user_id, $content_id, $content_type);
$usercheck->CheckPermissions();
$usercheck->ActionsCheck('edit'); //Can be ( edit, delete, both ) this returns error and dies if user does not have permissions



// Some basic presets
$show_notification = 0;


///////////////////////
//update mysql database
///////////////////////

if ($_POST['submitted'] == 'yes') {

	//get form post (no mysql_real_escapE) so that it can be checked for full completion
    	$title = $_POST['title'];
    	$description = $_POST['description'];
    	$tags = $_POST['tags'];

	//check if form filled in fully
    	////////////////////////////////

    	if ($title == '' || $description == '' || $tags == ''){
    		//error notification
    		$show_notification =1;
    		$message = $config['fill_all_fields']; //fill all fields

    	} else {

    		//Update Database
    		/////////////////

    		$gallery_id = mysql_real_escape_string($_POST['gallery']);

    		//get gallery name from sql
    		$sql = "SELECT gallery_name FROM image_galleries where gallery_id = $gallery_id";
    		$result1 = @mysql_fetch_array(mysql_query($sql));
    		$gallery_name = $result1['gallery_name'];

    		//get same form as above but process ready for mysql
    		$title	= @mysql_real_escape_string($title);
    		$title	= str_replace('/', '-', $title);
		$title	= str_replace("'", '', $title);

    		$title_seo = seo_title($title);

    		$description = @mysql_real_escape_string($description);

    		$tags = @mysql_real_escape_string($tags);

    		$sql = "UPDATE images SET title ='$title', title_seo ='$title_seo', description ='$description', tags ='$tags', gallery_name ='$gallery_name', gallery_id =$gallery_id WHERE indexer = $id";
    		@mysql_query($sql);

    		if(mysql_error()){
    			$show_notification =1;
    			$message = $config['error_26'];
    		} else {
    			$show_notification =1;
    			$message = $config['error_25'];
    		}

	}
}


//////////////////
//get image details
//////////////////

$sql = "SELECT * FROM images WHERE indexer = $id";
$query = @mysql_query($sql);
$result = @mysql_fetch_array($query);
$title = $result['title'];
$description = $result['description'];
$tags = $result['tags'];
$album_name = $result['gallery_name'];
$owners_id = $result['user_id'];
$public_private = $result['public_private'];
$allow_comments = $result['allow_comments'];
$image_id = $result['image_id'];
$gallery_name = $result['gallery_name'];
$gallery_id = $result['gallery_id'];

//get users other galleries/albums and createe select form
$sql = "SELECT gallery_id, gallery_name FROM image_galleries where user_id = $owners_id";
$result1 = @mysql_query($sql);
$fileds_all = '';
while ($result = @mysql_fetch_array($result1))
{
    if ($result['gallery_id'] == $gallery_id)
    {
        $selected = 'selected';
    } else
    {
        $selected = '';
    }
            $field = '<option value="' . $result['gallery_id'] . '" ' . $selected . ' >' . $result['gallery_name'] . '</option>';
            $fields_all = $fields_all . $field;
}


if(isset($_POST['submit'])){
    //get form post (no mysql_real_escapE)
    $title = $_POST['title'];
    $description = $_POST['description'];
    $tags = $_POST['tags'];
}



////////////
//disply page
////////////
$template = "templates/inner_edit_image.htm"; //middle of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true; // no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->Render = TBS_OUTPUT;
$TBS->tbs_show();
@mysql_close();
die();


?>