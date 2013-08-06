<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////


$selector_video_id = mysql_real_escape_string($_GET['vid']);

if ($selector_video_id == ""){ //if no video is set

	$video_play = "default.flv";

}else{
$selector_sql = "SELECT * FROM videos WHERE indexer = $selector_video_id AND approved ='yes'";
$selector_query = @mysql_query($selector_sql);
while ($selector_result = @mysql_fetch_array($selector_query)){
	
//get more information about the video
$selector_video = $selector_result['video_id'];
$video_play =$selector_video.'.flv';  //used in embedded flvplayer
$selector_title = $selector_result['title'];
$selector_show = 1; //show the table with video information below play screen
$selector_views = $selector_result['number_of_views'];
$selector_rating = $selector_result['updated_rating'];
$selector_vid = $selector_video_id;
}

//update video counter
if ($selector_video != "" || !empty($selector_video)){

    $selector_views_counter = $selector_views +1;
	$selector_sql = "UPDATE videos SET number_of_views = $selector_views_counter WHERE indexer = $selector_video_id";
    $selector_query = @mysql_query($selector_sql);
    }
}	
	?>