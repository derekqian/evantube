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

$referer = mysql_real_escape_string($_SERVER['HTTP_REFERER']);
if ( $referer == "" ) header("Location: " . "index.php");

$add_vid_id 	= (int) mysql_real_escape_string( $_GET['vid'] );
$add_audio_id 	= (int) mysql_real_escape_string( $_GET['audio'] );
$add_image_id 	= (int) mysql_real_escape_string( $_GET['image'] );


//check if user is logged in
if ($user_id == "") {
	echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['video_comments_login'].'</b></font>';
	die();
}

if ($add_vid_id > ""){

	//check if this video exists

	$addfav_sql = "SELECT * FROM videos WHERE indexer = $add_vid_id AND approved ='yes' AND public_private = 'public'";
	$addfav_query = @mysql_query($addfav_sql);
	$addfav_count = @mysql_num_rows($addfav_query);

	if ($addfav_count == 0) {
    		@mysql_close();
    		//error_redirect(107);//"An error has occured. the video could not be added"
    		echo '<p align="center"><font color="#FF0000" face="Arial"><b>'.$config["error_7"].'</b></font>';
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
        		//error_redirect(108);//"The video has now been added to your favorites"
        		echo '<p align="center"><font color="#00EE00" face="Arial"><b>'.$config["error_8"].'</b></font>';

		}
		else {
        		@mysql_close();
        		//error_redirect(109);//"The video is already in your favorites"
        		echo '<p align="center"><font color="#FF0000" face="Arial"><b>'.$config["error_9"].'</b></font>';
    		}
	}
}

elseif ($add_audio_id > ""){

	$addfav_sql = "SELECT * FROM audios WHERE indexer = $add_audio_id AND approved ='yes' AND public_private = 'public'";
	$addfav_query = @mysql_query($addfav_sql);
	$addfav_count = @mysql_num_rows($addfav_query);
	if ($addfav_count == 0) {
    		@mysql_close();
    		//error_redirect(107);//"An error has occured. the audio could not be added"
     		echo '<p align="center"><font color="#FF0000" face="Arial"><b>'.$config["error_7"].'</b></font>';
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
        		//error_redirect(108);//"The audio has now been added to your favorites"
        		echo '<p align="center"><font color="#00EE00" face="Arial"><b>'.$config["error_8"].'</b></font>';
		}
		else {
        		@mysql_close();
        		//error_redirect(109);//"The audio is already in your favorites"
        		echo '<p align="center"><font color="#FF0000" face="Arial"><b>'.$config["error_9"].'</b></font>';
        	}
	}

}

elseif ($add_image_id > ""){

	$addfav_sql = "SELECT * FROM images WHERE indexer = $add_image_id AND approved ='yes' AND public_private = 'public'";
	$addfav_query = @mysql_query($addfav_sql);
	$addfav_count = @mysql_num_rows($addfav_query);
	if ($addfav_count == 0) {
    		@mysql_close();
    		//error_redirect(107);//"An error has occured. the item could not be added"
     		echo '<p align="center"><font color="#FF0000" face="Arial"><b>'.$config["error_7"].'</b></font>';
	}
	else {
		//get image owners details
		$owner_details = mysql_fetch_array($addfav_query);
		$owner_id = $owner_details['user_id'];
		//check if its not in my fav already
    		$add_image_id = mysql_real_escape_string($add_image_id);
    		$user_id = mysql_real_escape_string($user_id);
    		$addfav_sql = "SELECT * FROM image_favorites WHERE image_id = $add_image_id and user_id = $user_id";
    		$addfav_query = @mysql_query($addfav_sql);
    		$addfav_count = @mysql_num_rows($addfav_query);

		//procede with adding and redirect
    		if ($addfav_count == 0) {
        		$addfav_sql = "INSERT INTO image_favorites (user_id, image_id, owner_id) VALUES ($user_id, $add_image_id, $owner_id)";
       	 	$addfav_query = @mysql_query($addfav_sql);
        		@mysql_close();
        		//error_redirect(108);//"The image has now been added to your favorites"
        		echo '<p align="center"><font color="#00EE00" face="Arial"><b>'.$config["error_8"].'</b></font>';
		}
		else {
        		@mysql_close();
        		//error_redirect(109);//"The image is already in your favorites"
        		echo '<p align="center"><font color="#FF0000" face="Arial"><b>'.$config["error_9"].'</b></font>';
        	}
	}

}

elseif ( $add_vid_id == "" ) {
	echo '<p align="center"><font color="#FF0000" face="Arial"><b>'.$config["error_7"].'</b></font>';
}

elseif ( $add_audio_id == "" ) {
	echo '<p align="center"><font color="#FF0000" face="Arial"><b>'.$config["error_7"].'</b></font>';
}

elseif ( $add_iamge_id == "" ) {
	echo '<p align="center"><font color="#FF0000" face="Arial"><b>'.$config["error_7"].'</b></font>';
}


?>

