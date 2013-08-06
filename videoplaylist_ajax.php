<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Main Author: Brian Shawa -  bshawa@gmail.com                                      //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once ('classes/config.php');
include_once ('classes/sessions.php');
include_once ('classes/menu.php');


$vid = mysql_real_escape_string($_GET['vid']);


//////////////////////////////
//check if user is logged in
//////////////////////////////

if ($user_id == ""){
echo '<p align="center"><font color="#FF4242" face="Arial" size="2"><b>'.$config["must_login"].'</b></font>';
die();
}


/////////////////////////////////////////////////////////////
//Check if a form to add new video to list has been submitted
/////////////////////////////////////////////////////////////
if ($_POST['submitted_playlist_action'] == 'yes') {
$vid = ''; //reset variable
$vid = mysql_real_escape_string($_POST['vid']);
$playlists_id = mysql_real_escape_string($_POST['playlists_id']);


//check if video id has been sent
if ( $vid == '' || $playlists_id ==''){
echo '<p align="center"><font color="#FF4242" face="Arial" size="2"><b>'.$config["error_26"].'</b></font>';	//error
	die();
}


//check if this is my playlist ( extra safety check)
$sql = "SELECT * FROM video_playlist WHERE list_id =  $playlists_id AND user_id  = $user_id";
if (@mysql_num_rows(@mysql_query($sql)) == 0){
echo '<p align="center"><font color="#FF4242" face="Arial" size="2"><b>'.$config["error_26"].'</b></font>';	//error
	die();
}

//check if this video is already in my playlist
$sql = "SELECT * FROM video_playlist_lists WHERE list_id = $playlists_id AND video_id  = $vid AND user_id  = $user_id";
if (@mysql_num_rows(@mysql_query($sql)) > 0){
echo '<p align="center"><font color="#FF4242" face="Arial" size="2"><b>'.$config['Playlists_duplicate_item'].'</b></font>';	//duplicate error
	die();
}

/////////////////////////
//add video to play list
/////////////////////////


//get the actual videos file name
$sql = "SELECT * FROM videos WHERE indexer = $vid";
$query = @mysql_query($sql);
$result = @mysql_fetch_array($query);
$vid_file_name = $result['video_id'];


$sql = "SELECT * FROM video_playlist_lists WHERE list_id = $playlists_id";
$total_in_list = @mysql_num_rows(@mysql_query($sql));
$new_position = $total_in_list + 1;


$sql = "INSERT INTO video_playlist_lists (list_id, user_id, video_id, video_file_name, video_position) VALUES ($playlists_id, $user_id, $vid, '$vid_file_name', $new_position)";
@mysql_query($sql);

//check if any mysql error occurred
if (mysql_error()){
echo '<p align="center"><font color="#FF4242" face="Arial" size="2"><b>'.$config["error_26"].'</b></font>';	//error
	die();
}else{

//success//
echo '<p align="center"><font color="#009933" face="Arial" size="2"><b>'.$config["error_25"].'</b></font>';	//error
die();
}

}//end of form actions




///////////////////////////////
//Check if vid is there
///////////////////////////////
if ($vid == ''){
echo '<p align="center"><font color="#FF4242" face="Arial" size="2"><b>'.$config["error_26"].'</b></font>';	//error
die();
}



///////////////////////////////
//Check if video is valid
///////////////////////////////
$sql = "SELECT * FROM videos WHERE indexer = $vid AND approved ='yes'";
if (@mysql_num_rows(mysql_query($sql)) == 0) {
echo '<p align="center"><font color="#FF4242" face="Arial" size="2"><b>'.$config["error_7"].'</b></font>';	//error
die();
}


///////////////////////////////
//Check if member has playlists
///////////////////////////////
$sql = "SELECT * FROM video_playlist WHERE user_id = $user_id ORDER BY list_name ASC";
if (@mysql_num_rows(mysql_query($sql)) == 0) {
//echo create new list
echo '<p align="center"><b><font color="#FF4242" face="Arial" size="2">'.$config['Playlists_none'].'</font></b>';
}



//////////////////////////////////
//else show my list and add button
//////////////////////////////////


//will trim the long play list names and add ...
function ShortenText($text) {
$chars = 60; //maximum to show
if (strlen($text) > $chars){
$dot_dot = '...';
}else{
$dot_dot = '';
}
$text = $text." ";
$text = substr($text,0,$chars);
$text = substr($text,0,strrpos($text,' '));
$text = $text.$dot_dot;
return $text;
}



//Get list for pull down menu
$sql = "SELECT * FROM video_playlist WHERE user_id = $user_id ORDER BY list_name ASC";
    $query = mysql_query($sql);
    while ($result = mysql_fetch_array($query)) {

    	$shortened_name = ShortenText($result['list_name']);
        $pulldown_list = $pulldown_list.'<option value="'.$result['list_id'].'">'.$shortened_name.'</option>';
    }


//show list and add button
echo'<form id="playlistform" action="javascript:ahahscript.likeSubmit(\'videoplaylist_ajax.php\', \'post\', \'playlistform\', \'playlist_ajaxdiv\');">
    <br />
    <br />
    <b>
    '.$config['word_my_playlists'].'
    </b>
    &nbsp;<select name="playlists_id" id="playlists_id" style="font-size: 10pt; width:180px; font-weight:bold; color:#666666; letter-spacing: 1; border: 1px solid #DFDCDC; background-color: #FDFDFD">'.$pulldown_list.'</select>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="submit" name="playlist_action" value="'.$config['word_add'].'" id="playlist_action" style="font-size: 10pt; color:#666666; letter-spacing: 1; border: 1px solid #DFDCDC; background-color: #FDFDFD" />
    <input type="hidden" name="vid" value="'.$vid.'" />
    <input type="hidden" name="submitted_playlist_action" value="yes" />
    <br /><br /></form>';

?>