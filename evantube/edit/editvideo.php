<?php
///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once('../classes/config.php');
include_once('../classes/permissions.php');
include_once('../siteadmin/includes/functions.php');

$id = mysql_real_escape_string($_GET['id']);

if (!empty($_POST)) $id = mysql_real_escape_string($_POST['id']);

// check if uid is in the requesting url
if ($id == "") {
    ErrorDisplay1($config["invalid_request"]);
    die();
}

//check permissions again
/////////////////////////
$usercheck = new LoadPermissions('',$id,'videos');  //($user_id, $content_id, $content_type);
$usercheck->CheckPermissions();
$usercheck->ActionsCheck('edit'); //Can be ( edit, delete, both ) this returns error and dies if user does not have permissions

//____________V3____Categories___________________________________________________________________________
if($phpmotion_main_version == 3 || $phpmotion_main_version == 3.5){
$sql			= "SELECT channel_id, channel_name FROM channels";
$result1 		= @mysql_query($sql);
$count_cats 	= @mysql_num_rows($result1);
$fields_all 	= "";
$sub_fields_all	= "";
$show_fields	= "";

$fields_all .= '<option value="99999">Select One</option>';

while ($result = @mysql_fetch_array($result1)) {
    	$fields_all .= '<option value="'.$result['channel_id'].'">'.$result['channel_name'].'</option>';
}
}
//________________________________________________________________________________________________________

// Some basic presets
$show_notification =0;
$vid = $id;

//get video to play
$sql = "SELECT * FROM videos WHERE indexer = $vid";
$query = @mysql_query($sql);
$result = @mysql_fetch_array($query);

$video_play = $result['video_id'].'.flv';
$video_thumb = $result['video_id'].'.jpg';

//////////////////
//get video details
//////////////////
if ($_POST['submitted'] != 'yes') {

	$sql				= "SELECT * FROM videos WHERE indexer = $vid";
	$query 			= @mysql_query($sql);
	$result 			= @mysql_fetch_array($query);

	$title 			= $result['title'];
    	$description		= $result['description'];
    	$tags 			= $result['tags'];

    	$channel 			= $result['channel'];

    	$type				= $result['type'];
    	$response_id 		= $result['response_id'];
    	$channel_id			= $result['channel_id'];
    	$sub_channel_id 		= $result['sub_channel_id'];

    	$location_recorded	= $result['location_recorded'];
    	$allow_comments 		= 'allow_comments_' . $result['allow_comments'];
    	$allow_embedding 		= 'allow_embedding_' . $result['allow_embedding'];
    	$public_private 		= 'public_private_' . $result['public_private'];
    	
     
      //------------------VIDEOEMBEDER---------------------------------
      
          $video_type = $result['video_type'];
          $embed_id	= $result['embed_id'];
          $embed_width_edit = 245;
          $embed_height_edit = 192;
          if($video_type == 'embedded'){
          include('../addons/videoembedder/embed.php');
          }
          if($video_type == 'mass_embedded'){
          include('../addons/massembedder/embed.php');
          $video_type = 'embedded'; //rest to just 'embedded' - for inner_play.htm
          }
      //---------------------------------------------------------------
      

    	//seting "selected" value for HTML pull down lists
    	//////////////////////////////////////////////////

    	$$allow_comments	= 'selected';
    	$$allow_embedding = 'selected';
    	$$public_private 	= 'selected';

    	///////////////////
    	//disply first page
    	///////////////////

    	$input_checks	= 1;
    	$template 		= "templates/inner_edit_video.htm";
    	$TBS 			= new clsTinyButStrong;
    	$TBS->NoErr 	= true;
    	$TBS->LoadTemplate("$template");
    	$TBS->Render 	= TBS_OUTPUT;
    	$TBS->tbs_show();

    	@mysql_close();
    	die();
}

// update mysql database

if ($_POST['submitted'] == 'yes') {

    	$title		= $_POST['title'];
    	$description 	= $_POST['description'];
    	$tags 		= $_POST['tags'];
    	$tags			= make_tag_words( $tags, $config['max_tag_word_length']);

	if ( $tags[0] == 'false' ) {

		$error_message = "<p align=\"center\"><h3><font color=\"#DD0000\"><b>$tags[1]</b></font></h3></p>";
		$input_checks	= 1;
		$show_notification= 1;
    		$tags 		= '';
    		$message 		= $error_message;
    		$template		= "templates/inner_edit_video.htm";
    		$TBS 			= new clsTinyButStrong;
        	$TBS->NoErr 	= true;

        	$TBS->LoadTemplate("$template");
        	$TBS->Render	= TBS_OUTPUT;
        	$TBS->tbs_show();

        	@mysql_close();
        	die();
      } else {
      	$tags = $tags[1];
      }


    	// IF WE ALL UTF-8 THE POST WOULD ALLOW / USE UTF-8 INPUT FROM USERS KEYBOARD
    	// NOT SOMEONE POSTING UNICODE HTML DECIMAL ENTITIES LIKE &#4102 ETC
    	// SO WE SHOULD KICK ALL OF THAT OUT?

    	$proceed_1 = utf_check($title);
    	$proceed_2 = utf_check($description);
    	$proceed_3 = utf_check($tags);


    	if ( $proceed_1 == 'false' || $proceed_2 == 'false' || $proceed_3 == 'false'  ) {
    		$title		= '';
    		$description 	= '';
    		$tags 		= '';
		$error_message = "<p align=\"center\"><h3><font color=\"#DD0000\"><b>Invalid Characters</b></font></h3></p>";

		$input_checks	= 0;
		$show_notification= 1;
    		$message 		= $error_message;
    		$template		= "templates/inner_edit_video.htm";
    		$TBS 			= new clsTinyButStrong;
        	$TBS->NoErr 	= true;

        	$TBS->LoadTemplate("$template");
        	$TBS->Render	= TBS_OUTPUT;
        	$TBS->tbs_show();

        	@mysql_close();
        	die();

    	}

    	// what does this do??
    	//check if I am owner
    	$sql = "SELECT * FROM videos WHERE indexer = $vid";
    	if (@mysql_num_rows(mysql_query($sql)) == 0) {
      	echo '<p align="center"><font color="#FF4242" face="Arial"><b>' . $config["invalid_request"] . '</b></font>';
      	die();
      }

    	//check if form filled in fully
    	if ($title == '' || $description == '' || $tags == '') {

      	// error messages
      	$input_checks	= 1;
      	$show_notification= 1;
    		$message 		= $config['fill_all_fields'];
    		$template		= "templates/inner_edit_video.htm";
    		$TBS 			= new clsTinyButStrong;
        	$TBS->NoErr 	= true;

        	$TBS->LoadTemplate("$template");
        	$TBS->Render	= TBS_OUTPUT;
        	$TBS->tbs_show();

        	@mysql_close();
        	die();
    	}

    	$title			= mysql_real_escape_string($title);
    	$description 		= mysql_real_escape_string($description);
    	$tags 			= mysql_real_escape_string($tags);

    	$title_seo 			= seo_title($title);

    	$type				= mysql_real_escape_string($_POST['type']); 			// flv, mp4 needed if we need to change video format for addons

    	$channel 			= (int)mysql_real_escape_string($_POST['channel']);
    	$response_id 		= (int)mysql_real_escape_string($_POST['response_id']);
    	$channel_id			= (int)mysql_real_escape_string($_POST['channel_id']);
    	$sub_channel_id 		= (int)mysql_real_escape_string($_POST['sub_channel_id']);

    	$location_recorded	= mysql_real_escape_string($_POST['location_recorded']);
    	$allow_comments		= mysql_real_escape_string($_POST['allow_comments']);
    	$allow_embedding		= mysql_real_escape_string($_POST['allow_embedding']);
    	$public_private		= mysql_real_escape_string($_POST['public_private']);


      //------------------VIDEOEMBEDER---------------------------------
      
          $video_type = $result['video_type'];
          $embed_id	= $result['embed_id'];
          $embed_width_edit = 245;
          $embed_height_edit = 192;
          if($video_type == 'embedded'){
          include('../addons/videoembedder/embed.php');
          }
          if($video_type == 'mass_embedded'){
          include('../addons/massembedder/embed.php');
          $video_type = 'embedded'; //rest to just 'embedded' - for inner_play.htm
          }
      //---------------------------------------------------------------
      
    	// get new channel name
    	// V3 => channel_id 	channel_name
    	$sql2				= "SELECT channel_name FROM channels WHERE channel_id = $channel";
    	$result2 			= @mysql_fetch_array(@mysql_query($sql2));
    	$channel_name 		= $result2['channel_name'];

    	// if video moved to new category RESET SUB category for now
    	if ( $channel != $channel_id ) $sub_channel_id = '99999';
    	if ( $type == '' ) $type = 'flv';

    	$description	= str_replace("<br>", " ", $description);
    	$description	= str_replace("<br />", " ", $description);
    	$title		= str_replace("<br>", " ", $title);
    	$title		= str_replace("<br />", " ", $title);

    	$sql = "UPDATE videos SET
    					type			= '$type',
    					response_id		= '$response_id',
    					channel_id		= '$channel',
				    	sub_channel_id	= '$sub_channel_id',
    					title 		= '$title',
    					title_seo 		= '$title_seo',
    					description 	= '$description',
    					tags 			= '$tags',
    					channel 		= '$channel_name',
    					location_recorded = '$location_recorded',
    					allow_comments 	= '$allow_comments',
    					allow_embedding 	= '$allow_embedding',
    					public_private 	= '$public_private'
    		WHERE indexer = $vid";

    	@mysql_query($sql);

    	//also remove from people who have made this their favorite movie
    	if ($public_private == 'private') {

      	$sql2 = "DELETE FROM favorites WHERE video_id = $vid";
      	@mysql_query($sql2);
    	}

    	//display updated video details
    	////////////////////////////////

    	$sql = "SELECT * FROM videos WHERE indexer = $vid";
    	$query = @mysql_query($sql);
    	$result = @mysql_fetch_array($query);

    	$title = $result['title'];
    	$description = $result['description'];
    	$tags = $result['tags'];
    	$channel = $result['channel'];
    	$location_recorded = $result['location_recorded'];
    	$allow_comments = 'allow_comments_' . $result['allow_comments'];
    	$allow_embedding = 'allow_embedding_' . $result['allow_embedding'];
    	$public_private = 'public_private_' . $result['public_private'];

    	//seting "selected" value for HTML pull down lists
    	//////////////////////////////////////////////////

    	$allow_comments 	= 'selected';
    	$allow_embedding	= 'selected';
    	$public_private 	= 'selected';

    	//////////////////////////////////
    	//displaying page with new updates
      $input_checks	= 1;
      $show_notification =1;
      
    	$message = $config['error_25']; //request success
    	$template = "templates/inner_edit_video.htm";//middle of page
    	$TBS = new clsTinyButStrong;
    	$TBS->NoErr = true;// no more error message displayed.
    	$TBS->LoadTemplate("$template");
    	$TBS->Render = TBS_OUTPUT;
    	$TBS->tbs_show();
    	@mysql_close();
    	die();
}


?>