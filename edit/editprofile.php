<?php
///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once('../classes/config.php');
include_once('../siteadmin/includes/inc.stats.php');
include_once('../siteadmin/includes/functions.php');
include_once('../classes/permissions.php');

$referer = mysql_real_escape_string($_SERVER['HTTP_REFERER']);
if ( $referer == "" ) header("Location: " . "../index.php");
if ( !ereg ( $_SERVER[HTTP_HOST], $referer . '/greybox/' ) ) header("Location: " . "../index.php");


//Get ID
$id = mysql_real_escape_string($_GET['id']);

if (!empty($_POST)) {
	$id = mysql_real_escape_string($_POST['id']);
}

// check if uid is in the requesting url
if ($id == '') {
	ErrorDisplay1($config["invalid_request"]);
    	die();
}

//check permissions again
/////////////////////////
$usercheck = new LoadPermissions('',$id,'member_profile');  //($user_id, $content_id, $content_type);
$usercheck->CheckPermissions();
$usercheck->ActionsCheck('edit'); //Can be ( edit, delete, both ) this returns error and dies if user does not have permissions


// Some basic presets
$show_notification =0;
$member_id = $id;
$base_path = installation_paths();


// retrieve members details
////////////////////////////
$sql = "SELECT * FROM member_profile WHERE user_id = $member_id";
$query = mysql_query($sql);

if (mysql_num_rows($query) == 0) {
	$config["error_21"];//error
}

//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> PROCESS SUBMITTED FORM >>>>>>>>>>>>>>>>>>

if (isset($_POST['submit'])) {

	//run checks if form was fully filled in
	foreach($_POST as $key => $value ) {

		$value = trim(mysql_real_escape_string($value));


		//######################################################################### TODO #######################################
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//
		// we need valid email check - and also check if email is used by another !!!
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//remmove approve picture (its not in same table)
		if($key !='approved') {

			//MD5 password
			if($key =='password' && $value !='') {
				$value = md5($value);
			}

			$sql = "$key = '$value',";
			if ($key =='uid'|| $key =='submit' || $key =='id' || $key =='delete_avatar'){
				$sql ='';
			}

			//ignore blank password
			if($key =='password' && $value ==''){
				$sql ='';
			}

			$sql1 = $sql1.$sql;

		} else {
			$sql2 = "$key = '$value'";
		}
	}


	//Update mysql database (members profile)

	$sql1 = substr($sql1,0,-1);
	$sql ="UPDATE member_profile SET $sql1 WHERE user_id = $member_id";
	@mysql_query($sql);

	//notifications
	$show_notification =1;
	$message = notifications(1);

	//Delete members image
	if ($_POST['delete_avatar'] == 'yes'){

		//get image name
		$sql = "SELECT * FROM pictures WHERE user_id = $member_id";
		$result = @mysql_fetch_array(@mysql_query($sql));
		$existing_file = $result['file_name'];

		//delete image from dbase
		$sql ="DELETE FROM pictures WHERE user_id = $member_id";
		@mysql_query($sql);

		//delete image from server
		$mypicture = $config['site_base_url'].'/pictures/'.$existing_file;
		@unlink($mypicture);

		//notifications
		$show_notification =1;
		$message = notifications(1);
	}
}

//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> DISPLAY RESULT >>>>>>>>>>>>>>>>>>>>>>>>>

///////////////////
// get user details
///////////////////
$sql = "SELECT * FROM member_profile WHERE user_id = $member_id";
$result1 = @mysql_query($sql);
$result_active = @mysql_fetch_array($result1);


//basic user details
foreach ($result_active as $key => $value) {
    	$$key = $value;
}

//pull down menus (may not work with non english lang files)

$current_country_1 = $current_country . '1';
$year_of_birth_x = 'x' . $year_of_birth;
$home_country = "selected";
$current_country_1 = "selected";
$gender = "selected";
$relationship_status = "selected";
$year_of_birth_x = "selected";


//get members picture
$sql = "SELECT * FROM pictures WHERE user_id = $member_id";
$result = @mysql_fetch_array(@mysql_query($sql));
$existing_file = $result['file_name'];
$approved = $result['approved'];

if (@mysql_num_rows(mysql_query($sql)) == 0) {
	$mypicture = "../themes/$user_theme/images/placeholder.gif";;
    	$picture_status = $lang_no_pictures_uploaded;

} else {

	$mypicture = $config['site_base_url'].'/pictures/'.$existing_file;
      //create pull down under picture to allow deleting image
    	$picture_status = '</font><font size="2">&nbsp;|&nbsp;<b>'.$lang_del_avatar.'</b></font>&nbsp;
				<select size="1" name="delete_avatar">
            			<option value="no" >'.$lang_no.'</option>
					<option value="yes" >'.$lang_yes.'</option>
            		</select>';
}

//rescale thumbs
$page_display_width = $config['members_profile_own_thumb_width'];
$display_thumbs = show_thumb($mypicture, $page_display_width);
$image_width_profile = $display_thumbs[0];
$image_height_profile = $display_thumbs[1];

if ($image_width_profile == 0){
	$image_width_profile =100;
}

if ($image_height_profile == 0){
	$image_height_profile =100;
}

////////////////////
// get users stats
////////////////////

//get number of videos per member
/////////////////////////////////
$sql = "SELECT indexer FROM videos WHERE approved='yes' AND user_id = $member_id";
$query = @mysql_query($sql);
$video_count = @mysql_num_rows($query);

//get number of audios per member
/////////////////////////////////////////////////////////
$sql = "SELECT indexer FROM audios WHERE approved='yes' AND user_id = $member_id";
$query = @mysql_query($sql);
$audio_count = @mysql_num_rows($query);


//get number of images per member
/////////////////////////////////////////////////////////
$sql = "SELECT indexer FROM images WHERE approved='yes' AND user_id = $member_id";//TODO change to pictures SQL
$query = @mysql_query($sql);
$pictures_count = @mysql_num_rows($query);

//get TOTAL number of friends
/////////////////////////////////
$sql = "SELECT * FROM friends WHERE user_id = $member_id AND invitation_status = 'accepted' OR friends_id = $member_id AND invitation_status = 'accepted'";
$query = @mysql_query($sql);
$friends_count = @mysql_num_rows($query);

//get number of blogs per member
/////////////////////////////////
$sql = "SELECT indexer FROM blogs WHERE approved='yes' AND user_id = $member_id"; //TODO change to blog count
$query = @mysql_query($sql);
$blog_count = @mysql_num_rows($query);

// show disabled messages
//TO DO - check settings.php if picture etc are disabled
//if yes set $pictures_count = $lang_disabled , etc etc


// disply page
$template = "templates/inner_edit_member.htm";
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->Render = TBS_OUTPUT;
$TBS->tbs_show();
@mysql_close();
die();

//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>display end>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

?>
