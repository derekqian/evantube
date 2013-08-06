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
include_once ('classes/login_check.php');

//upload include
require 'uploader_conlib.php';

$config_video_title_length	= $config['video_title_length'];

//===================================START OF UPLOADER (ubber)================================

$THIS_VERSION = "4.0";

/////////////////////////////////////////////////////////////////
// The following possible query string formats are assumed
//
// 1. ?upload_dir=tmp_sid&path_to_upload_dir
// 2. ?cmd=about
/////////////////////////////////////////////////////////////////

// Hard-code the 'temp_dir' value here instead of passing it in the address bar.

$temp_dir	= $_REQUEST['temp_dir'];
$_POST_DATA = getPostData($temp_dir, $_REQUEST['tmp_sid']);

$title 			= $_POST_DATA['title'];
$description 		= $_POST_DATA['description'];
$tags 			= $_POST_DATA['tags'];
$location_recorded	= $_POST_DATA['location_recorded'];
$allow_comments 		= $_POST_DATA['allow_comments'];
$allow_embedding 		= $_POST_DATA['allow_embedding'];
$public_private 		= $_POST_DATA['public_private'];

$channel_id			= $_POST_DATA['channel'];
$channel_name 		= $_POST_DATA['channel_name'];
$sub_cat			= $_POST_DATA['sub_cat'];

$vid_response 		= $_POST_DATA['vid_response'];
$response_id 		= $_POST_DATA['response_id'];
$of_channel_id 		= $_POST_DATA['of_channel_id'];
$of_channel			= $_POST_DATA['of_channel'];

/////////////////////////////////////////////////////////////////////
// run posted tags thru tag word function

////////////////////////////////////////////////////////////////////////////////////////////
// You can now access all the post values from the .param file. eg. $_POST_DATA['email']; //
////////////////////////////////////////////////////////////////////////////////////////////
# Loop over the post data looking for files and create table elements

foreach ($_POST_DATA as $post_key => $post_value) {

	if (preg_match("/^upfile_/i", $post_key)) {
      	$uploaded_file_name = $post_value;
        	$uploaded_file_path = $_POST_DATA['upload_dir'] . $uploaded_file_name;

        	if (is_file($uploaded_file_path)) {
            	$file_size = @filesize($uploaded_file_path);
            	$file_size = formatBytes($file_size);
            	clearstatcache();
        	}
    	}
}

/////////////////////////////////////////////////////////
//  Get the post data from the param file (tmp_sid.param)
/////////////////////////////////////////////////////////

function getPostData($up_dir, $tmp_sid) {
	$param_array = array();
    	$buffer = "";
    	$key = "";
    	$value = "";
    	$paramFileName = $up_dir . $tmp_sid . ".params";
    	$fh = @fopen($paramFileName, 'r');

    	if (!is_resource($fh)) {
      	kak("<font color='red'>ERROR</font>: Failed to open $paramFileName");
    	}

    	while (!feof($fh)) {
      	$buffer = fgets($fh, 4096);
        	list($key, $value) = explode('=', trim($buffer));
        	$value = str_replace("~EQLS~", "=", $value);
        	$value = str_replace("~NWLN~", "\r\n", $value);

        	if (isset($key) && isset($value) && strlen($key) > 0 && strlen($value) > 0) {
            	if (preg_match('/(.*)\[(.*)\]/i', $key, $match)) {
                		$param_array[$match[1]][$match[2]] = $value;
            } else {
            	$param_array[$key] = $value;
            }
	}
}

fclose($fh);

if (isset($param_array['delete_param_file']) && $param_array['delete_param_file'] == 1) {
	for ($i = 0; $i < 5; $i++) {
      	if (@unlink($paramFileName)) {
            	break;

            } else {
            	sleep(1);
            }
	}
}
return $param_array;
}

//////////////////////////////////////////////////
//  formatBytes($file_size) mixed file sizes
//  formatBytes($file_size, 0) KB file sizes
//  formatBytes($file_size, 1) MB file sizes etc
//////////////////////////////////////////////////
function formatBytes($bytes, $format = 99) {
	$byte_size = 1024;
    	$byte_type = array(" KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");

    	$bytes /= $byte_size;
    	$i = 0;

    	if ($format == 99 || $format > 7) {
      	while ($bytes > $byte_size) {
            	$bytes /= $byte_size;
            	$i++;
        	}
    	} else {
      	while ($i < $format) {
            	$bytes /= $byte_size;
            	$i++;
        	}
    	}

    	$bytes = sprintf("%1.2f", $bytes);
    	$bytes .= $byte_type[$i];

    	return $bytes;
}

//===============================START OF DATABASE ACTION & DISPLAY =========================
//Recording the video files info into dbase

list($filename, $extension) = split('\.', $uploaded_file_name);

$filename 			= @mysql_real_escape_string($filename);
$title 			= @mysql_real_escape_string($title);
$description		= @mysql_real_escape_string($description);
$tags 			= @mysql_real_escape_string($tags);
$main_channel		= (int) @mysql_real_escape_string($channel_id);
$allow_comments 		= @mysql_real_escape_string($allow_comments);
$allow_embed 		= @mysql_real_escape_string($allow_embed);
$public_private		= @mysql_real_escape_string($public_private);
$channel_name		= @mysql_real_escape_string($channel_name);
$sub_cat_id			= @mysql_real_escape_string($sub_cat);
$title 			= substr($title, 0, $config_video_title_length);
$title_seo 			= seo_title($title);

// from vid reponse form
$vid_response 		= @mysql_real_escape_string($vid_response);
$response_id 		= @mysql_real_escape_string($response_id);
$of_channel_id 		= @mysql_real_escape_string($of_channel_id);
$of_channel			= @mysql_real_escape_string($of_channel);
// end vid response form

if ( $of_channel_id == '' ) $of_channel_id = $main_channel;
if ( $channel_name == '' ) $channel_name = $of_channel;
if ( $response_id == '' ) $response_id = 0;

$sql = "INSERT INTO videos (video_id,
				    response_id,
				    channel_id,
				    sub_channel_id,
				    user_id,
				    viewtime,
				    title,
				    title_seo,
				    description,
				    tags,
				    channel,
				    date_recorded,
				    date_uploaded,
				    location_recorded,
				    video_length,
				    allow_comments,
				    allow_embedding,
				    allow_ratings,
				    rating_number_votes,
				    rating_total_points,
				    updated_rating,
				    public_private,
				    approved,
				    number_of_views,
				    featured,
				    promoted,
				    flag_counter)
			  VALUES ('$uploaded_file_name',
			  	    '$response_id',
			  	    '$of_channel_id',
			  	    '$sub_cat_id',
			  	    '$user_id',
			  	    '0000-00-00 00:00:00',
			  	    '$title',
			  	    '$title_seo',
			  	    '$description',
			  	    '$tags',
			  	    '$channel_name',
			  	    '0000-00-00 00:00:00',
			  	    NOW(),
			  	    '',
			  	    '00:00:00',
			  	    '$allow_comments',
			  	    '$allow_embedding',
			  	    'yes',
			  	    '0',
			  	    '0',
			  	    '0',
			  	    '$public_private',
			  	    'pending_conversion',
			  	    '0',
			  	    'no',
			  	    'no',
			  	    '0'
			  	    )";

@mysql_query($sql);


// update sub-category table

if ( $sub_cat_id > 0 ) {

	$sql_subs	= "UPDATE sub_channels SET has_vids = 'yes' WHERE sub_channel_id = '$sub_cat_id'";
	$query_subs	= @mysql_query($sql_subs);
}


//============================START OF FFMPEG ACTIONS ==================================
// sending command to convertor.php tp convert the movie named : uploaded_file_name ..
// conversion happens in background so user is not delayed browser window can even be closed.

$base_path = installation_paths();
$convertor = $base_path . '/convertor.php';

//check if we are debugging so the convertor is called as url
if ($debugmodex == 1){ //yes debug mode
	header("Location: " . "convertor.php?id=$uploaded_file_name&debug=1");
	die;
}

//else load convertor in background
exec("$path_to_php $convertor $uploaded_file_name> /dev/null &");// (>/dev/null & part is what sends to background)

//=================================== START OF TBS DISPLAY ====================================

/////////////////////////////////////////
// V3 video response done via thickbox
// don't fire up main_1 or site complete

if ( $vid_response == 'vid_response' ) {
	$template = "themes/$user_theme/templates/response_upload_complete.htm";
} else {
	$template = "themes/$user_theme/templates/main_1.htm";
	$inner_template1 = "themes/$user_theme/templates/inner_upload_complete.htm";
}

$covertor_url = $config["site_base_url"].'/convertor.php';
@exec("curl -G $covertor_url > /dev/null &");// (required where background process does not work)

$TBS = new clsTinyButStrong;
$TBS->NoErr = true;
$TBS->LoadTemplate("$template");
$TBS->Render = TBS_OUTPUT;
$TBS->Show();


@exec("$path_to_php $convertor $uploaded_file_name> /dev/null &");// (>/dev/null & part is what sends to background)

@mysql_close();

die();

?>