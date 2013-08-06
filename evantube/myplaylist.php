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

//////////////////////////////
//check if user is logged in
//////////////////////////////

if ($user_id == "") {
    echo '<p align="center"><font color="#FF4242" face="Arial" size="2"><b>'.$config["must_login"].
        '</b></font>';
    die();
}

$playlists_id = mysql_real_escape_string($_GET['id']);


//check if I am the owner, incase I am  looking at this via browser (privacy action)
//check if this is my playlist ( extra safety check)
$sql = "SELECT * FROM video_playlist WHERE list_id =  $playlists_id AND user_id  = $user_id";
if (@mysql_num_rows(@mysql_query($sql)) == 0){
echo '<p align="center"><font color="#FF4242" face="Arial" size="2"><b>'.$config["error_26"].'</b></font>';	//error 
	die();
}



/////////////////////////
//start building xml list
/////////////////////////


//HEADER>>
header("content-type:text/xml;charset=utf-8");
echo "<playlist version='1' xmlns='http://xspf.org/ns/0/'>\n";
echo "<trackList>\n";


//CONTENT>>
$sql = "SELECT * FROM video_playlist_lists WHERE list_id = $playlists_id AND user_id  = $user_id";
$query = mysql_query($sql);
while ($result = mysql_fetch_array($query)) {
	
$play_list_video_id = $result['video_id'];
//get video details
$sql2 = "SELECT * FROM videos WHERE indexer = $play_list_video_id";
$query2 = mysql_query($sql2);
$result2 = mysql_fetch_array($query2);
$video_title= $result2['title'];

//more details
	$video_file = $base_url.'/uploads/'.$result['video_file_name'].'.flv';
	$video_image = $base_url.'/uploads/thumbs/'.$result['video_file_name'].'.jpg';
	$video_link = '';
    echo "\t<track>\n";
    echo "\t\t<image>".$video_image."</image>\n";
    echo "\t\t<title>".$video_title."</title>\n";
    echo "\t\t<location>".$video_file."</location>\n";
    echo "\t\t<info>".$video_link."</info>\n";
    echo "\t</track>\n";
}

//FOOTER>>
echo "</trackList>\n";
echo "</playlist>\n";

?>