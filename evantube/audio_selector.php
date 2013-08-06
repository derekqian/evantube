<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////


$selector_audio_id = mysql_real_escape_string($_GET['vid']);

if ($selector_audio_id == ""){ //if no audio is set

	$audio_play = "default.mp3";

}else{
$selector_sql = "SELECT * FROM audios WHERE indexer = $selector_audio_id AND approved ='yes'";
$selector_query = @mysql_query($selector_sql);
while ($selector_result = @mysql_fetch_array($selector_query)){

//get more information about the audio
$selector_audio = $selector_result['audio_id'];
$audio_play =$selector_audio.'.mp3';  //used in embedded flvplayer
$selector_title = $selector_result['title'];
$selector_show = 1; //show the table with audio information below play screen
$selector_views = $selector_result['number_of_views'];
$selector_rating = $selector_result['updated_rating'];
$selector_vid = $selector_audio_id;
}

//update audio counter
if ($selector_audio != "" || !empty($selector_audio)){

    $selector_views_counter = $selector_views +1;
	$selector_sql = "UPDATE audios SET number_of_views = $selector_views_counter WHERE indexer = $selector_audio_id";
    $selector_query = @mysql_query($selector_sql);
    }
}
	?>