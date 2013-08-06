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

if ($user_id == ""){
echo '<p align="center"><font color="#FF4242" face="Arial" size="2"><b>'.$config["must_login"].'</b></font>';
die();
}



$blk_notification = '';//reset	notifications
$show_create_new = 1 ;//show create new list
$show_main_list_actions = 1;//show list tables etc (only if user has a created list, else hide)

///////////////////////////////
//Check if member has playlists
///////////////////////////////

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


$sql = "SELECT * FROM video_playlist WHERE user_id = $user_id ORDER BY list_name ASC";
if (@mysql_num_rows(mysql_query($sql)) == 0) {
    $show_main_list_actions = '';//show list menu / actions
    $show_main_list_table = '';//do now show list table
    $blk_notification = 1;
    $message_type = $config['word_notice'];
    $error_message = $config['Playlists_none'];
}
else {
    //create pull down menu for list
    $query = mysql_query($sql);
    while ($result = mysql_fetch_array($query)) {
      	$shortened_name = ShortenText($result['list_name']);
        $pulldown_list = $pulldown_list.'<option value="'.$result['list_id'].'">'.$shortened_name.
            '</option>';
    }
}

//////////////////////
//Create new play list
//////////////////////
if ($_POST['submitted_new_list'] == 'yes') {
    $blk_notification = 0;//rest

    $list_name = @mysql_real_escape_string($_POST['list_name']);

    //check if  list name is empty
    if ($list_name == '') {
        $blk_notification = 1;
        $message_type = $config["notification_error"];
        $error_message = $config["fill_all_fields"];
        tbs_render_page(1,1);//options: mysql_close and die()
    }

    //check if I already have list with exact same name
    $sql = "SELECT * FROM video_playlist WHERE user_id = $user_id AND list_name = '$list_name'";
    if (@mysql_num_rows(mysql_query($sql)) > 0) {
        $blk_notification = 1;
        $message_type = $config["notification_error"];
        $error_message = $config['Playlists_duplicate'];
        tbs_render_page(1,1);//options: mysql_close and die()
    }

    //create new list
    $sql = "INSERT INTO video_playlist (list_name, user_id) VALUES ('$list_name', $user_id)";
    @mysql_query($sql);

    //check if created ok
    $sql = "SELECT * FROM video_playlist WHERE user_id = $user_id AND list_name = '$list_name'";
    if (@mysql_num_rows(mysql_query($sql)) == 0) {
        $blk_notification = 1;
        $message_type = $config["notification_error"];
        $error_message = $config["error_26"];
        tbs_render_page(1,1);//options: mysql_close and die()
    }
    else {
        $blk_notification = 1;
        $message_type = $config["notification_success"];
        $error_message = $config["error_25"];//success
        $show_main_list_actions = 1;

        //get updated pull down list
        $pulldown_list ="";
        $sql = "SELECT * FROM video_playlist WHERE user_id = $user_id ORDER BY list_name ASC";
        $query = mysql_query($sql);
    while ($result = mysql_fetch_array($query)) {
      	$shortened_name = ShortenText($result['list_name']);
        $pulldown_list = $pulldown_list.'<option value="'.$result['list_id'].'">'.$shortened_name.
            '</option>';
    }

        tbs_render_page(1,1);//options: mysql_close and die()
    }
}

//////////////////////////////
//POST ACTIONS ON PLAY LISTS
//////////////////////////////
if ($_POST['submitted_playlist_action'] == 'yes') {
$playlists_id = mysql_real_escape_string($_POST['my_playlists']);



//Delete List and also videos from lists table
///////////////////////////////////////////////
if ($_POST['selected_box'] == 'delete'){

$sql = "DELETE FROM video_playlist WHERE list_id = $playlists_id AND user_id  = $user_id";
@mysql_query($sql);

$sql = "DELETE FROM video_playlist_lists WHERE list_id = $playlists_id AND user_id  = $user_id";
@mysql_query($sql);

        $blk_notification = 1;
        $message_type = $config["notification_success"];
        $error_message = $config["error_25"];//success
        $show_main_list_actions = 1;

                //get updated pull down list
                $pulldown_list ="";
        $sql = "SELECT * FROM video_playlist WHERE user_id = $user_id ORDER BY list_name ASC";
        $query = mysql_query($sql);
    while ($result = mysql_fetch_array($query)) {
      	$shortened_name = ShortenText($result['list_name']);
        $pulldown_list = $pulldown_list.'<option value="'.$result['list_id'].'">'.$shortened_name.
            '</option>';
    }
}




//display all videos
////////////////////
if ($_POST['selected_box'] == 'show' || $_POST['selected_box'] == 'delete'){ //this just shows results on list
$show_main_list_table = 1;
$result =array();
$sql = "SELECT * FROM video_playlist_lists WHERE list_id = $playlists_id AND user_id  = $user_id";
$query = @mysql_query($sql);
while ($result1 = @mysql_fetch_array($query)){

$play_list_video_id = $result1['video_id'];

//get video details
$sql2 = "SELECT * FROM videos WHERE indexer = $play_list_video_id";
$query2 = mysql_query($sql2);
$results2 = mysql_fetch_array($query2);

//join the 2 arrays

$merged_array = array_merge($results2, $result1);

$result[] = $merged_array;
}


//get list title
$sql = "SELECT * FROM video_playlist WHERE list_id = $playlists_id";
$query = mysql_query($sql);
$result3 = mysql_fetch_array($query);
$shortened_name = ShortenText($result3['list_name']);

//set condition for hidding (position, delete from list) to <tr>
if (empty($result)){
$show = 2;
}else{
$show = 1;
}



//show header of table with list name
$show_title = 1;

if ($_POST['selected_box'] == 'delete'){ //do not show anything if action was delete a list
	$show_title = '';
	$show = '';
	}

//show results
tbs_render_page2(1,1,$result); //options: mysql_close and die()
}




/////////////////////////////////////////////////
//show play list (as generated by myplaylist.php)
/////////////////////////////////////////////////
if ($_POST['selected_box'] == 'play'){

//check if list has videos
$sql = "SELECT * FROM video_playlist_lists WHERE list_id = $playlists_id AND user_id  = $user_id";
$query = @mysql_query($sql);
if(mysql_num_rows($query) < 0){

	//show nothing found
	$show = 2;
	$show_player = '';
	}else{

//load player table
$count_videos = mysql_num_rows($query);
$show_player = 1;
$show = '';
$show_title = 2;
$show_create_new = '';

		}


	}


}





//////////////////////////////
//GET ACTIONS ON PLAY LISTS
//////////////////////////////

if ($_GET['act']=='del' && $_GET['vid'] != "" && $_GET['id'] != ""){


$playlists_id = mysql_real_escape_string($_GET['id']);
$del_vid = mysql_real_escape_string($_GET['vid']);

//Delete Video >>
$sql = "DELETE FROM video_playlist_lists WHERE list_id = $playlists_id AND video_id = $del_vid AND user_id = $user_id";
@mysql_query($sql);
        $blk_notification = 1;
        $message_type = $config["notification_success"];
        $error_message = $config["error_25"];//success
        $show_main_list_actions = 1;
//del video end<<


$show_main_list_table = 1;
$result =array();
$sql = "SELECT * FROM video_playlist_lists WHERE list_id = $playlists_id AND user_id  = $user_id";
$query = @mysql_query($sql);
while ($result1 = @mysql_fetch_array($query)){

$play_list_video_id = $result1['video_id'];

//get video details
$sql2 = "SELECT * FROM videos WHERE indexer = $play_list_video_id";
$query2 = mysql_query($sql2);
$results2 = mysql_fetch_array($query2);

//join the 2 arrays

$merged_array = array_merge($results2, $result1);

$result[] = $merged_array;
}

//set condition for hidding (position, delete from list) to <tr>
if (empty($result)){
$show = 2;
}else{
$show = 1;
}

//show header of table with list name
$show_title = 1;

//show results
tbs_render_page2(1,1,$result); //options: mysql_close and die()
}

//////////////////
//Load default page
//////////////////

//show default page
tbs_render_page(1,1);//options: mysql_close and die()

////////////////////////////
//TBS PAGE DISPLAY FUNCTIONS
////////////////////////////

function tbs_render_page($closemysql,$die) {
	global $user_theme;

    	$template = "themes/$user_theme/templates/inner_playlist_main.htm";//middle of page
    	$TBS = new clsTinyButStrong;
    	$TBS->NoErr = true;// no more error message displayed.
    	$TBS->LoadTemplate("$template");
    	$TBS->Render = TBS_OUTPUT;
    	$TBS->Show();

    	//close mysql?
    	if ($closemysql == 1) @mysql_close();

    	//die() ?
    	if ($die == 1) die();
}


function tbs_render_page2($closemysql,$die,$result) {
	global $user_theme;

	$template = "themes/$user_theme/templates/inner_playlist_main.htm";//middle of page
    	$TBS = new clsTinyButStrong;
    	$TBS->NoErr = true;// no more error message displayed.
    	$TBS->LoadTemplate("$template");
    	$TBS->MergeBlock('blk1',$result);
    	$TBS->Render = TBS_OUTPUT;
    	$TBS->Show();

	//close mysql?
    	if ($closemysql == 1) @mysql_close();

    	//die()
    	if ($die == 1) die();
}


?>