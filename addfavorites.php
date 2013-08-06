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

$referer = $_SERVER['HTTP_REFERER'];
$add_vid_id = $_GET['vid'];

$add_audio_id = $_GET['audio'];

//check if viewer is logged in as a member
if ($user_id == "") {
    error_redirect(110);//"You need to be logged in. Please login or register"
}

/*
//if no id in url
if ($add_vid_id == "") {
    @mysql_close();
    header("Location: " . "$referer");
    die();
}
elseif ($add_audio_id == ""){
	@mysql_close();
    	header("Location: " . "$referer");
    	die();
}
*/

if ($add_vid_id > ""){
	//check if this video exists
	$add_vid_id = mysql_real_escape_string($add_vid_id);
	$addfav_sql = "SELECT * FROM videos WHERE indexer = $add_vid_id AND approved ='yes' AND public_private = 'public'";
	$addfav_query = @mysql_query($addfav_sql);
	$addfav_count = @mysql_num_rows($addfav_query);
	if ($addfav_count == 0) {
    		@mysql_close();
    		error_redirect(107);//"An error has occured. the video could not be added"
	}
	else {
		//get video owners details
		$owner_details = mysql_fetch_array($addfav_query);
		$owner_id = $owner_details['user_id'];
		//check if its not in my fav already
    		$add_vid_id = mysql_real_escape_string($add_vid_id);
    		$user_id = mysql_real_escape_string($user_id);
    		$addfav_sql = "SELECT * FROM favorites WHERE video_id = $add_vid_id and user_id = $user_id";
    		$addfav_query = @mysql_query($addfav_sql);
    		$addfav_count = @mysql_num_rows($addfav_query);

		//procede with adding and redirect
    		if ($addfav_count == 0) {
        		$addfav_sql = "INSERT INTO favorites (user_id, video_id, owner_id) VALUES ($user_id, $add_vid_id, $owner_id)";
       	 	$addfav_query = @mysql_query($addfav_sql);
        		@mysql_close();
        		error_redirect(108);//"The video has now been added to your favorites"
		}
		else {
        		@mysql_close();
        		error_redirect(109);//"The video is already in your favorites"
    		}
	}
}
elseif ($add_audio_id > ""){
	$add_audio_id = mysql_real_escape_string($add_audio_id);
	$addfav_sql = "SELECT * FROM audios WHERE indexer = $add_audio_id AND approved ='yes' AND public_private = 'public'";
	$addfav_query = @mysql_query($addfav_sql);
	$addfav_count = @mysql_num_rows($addfav_query);
	if ($addfav_count == 0) {
    		@mysql_close();
    		error_redirect(107);//"An error has occured. the audio could not be added"
	}
	else {
		//get audio owners details
		$owner_details = mysql_fetch_array($addfav_query);
		$owner_id = $owner_details['user_id'];
		//check if its not in my fav already
    		$add_audio_id = mysql_real_escape_string($add_audio_id);
    		$user_id = mysql_real_escape_string($user_id);
    		$addfav_sql = "SELECT * FROM audio_favorites WHERE audio_id = $add_audio_id and user_id = $user_id";
    		$addfav_query = @mysql_query($addfav_sql);
    		$addfav_count = @mysql_num_rows($addfav_query);

		//procede with adding and redirect
    		if ($addfav_count == 0) {
        		$addfav_sql = "INSERT INTO audio_favorites (user_id, audio_id, owner_id) VALUES ($user_id, $add_audio_id, $owner_id)";
       	 	$addfav_query = @mysql_query($addfav_sql);
        		@mysql_close();
        		error_redirect(108);//"The audio has now been added to your favorites"
		}
		else {
        		@mysql_close();
        		error_redirect(109);//"The audio is already in your favorites"
        	}
	}

}

elseif ($add_audio_id == ""){
    		error_redirect(107);//"An error has occured. the audio could not be added"
	}
elseif ($add_vid_id == ""){
    		error_redirect(107);//"An error has occured. the video could not be added"
	}

?>

