<?php
@error_reporting(E_ALL);

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once ('classes/config.php');


//_____Set Player Defaults______
$player_defaults_ads = 'no';
$player_defaults_ads_xml = '';
$video_buffer_time = (is_numeric($config["video_buffer_time"]))? $config["video_buffer_time"]: 2;
$auto_play_vid = $config['auto_play'];

//____include video ads powertool______
if(is_file('addons/videoads/ads.php')) {
    include_once ('addons/videoads/ads.php');
}


$procede = true;
$referer = mysql_real_escape_string($_SERVER['HTTP_REFERER']);

if($referer == "") {
    $procede = false;
    echo "<br />";
    echo "<br />";
    echo "<br />";
    echo "<center><h2><font color=\"DD0000\">".$config['error_11']."</font></h2>"; //could not be found
    echo "<br />";
    echo "<br />";
    echo "<br />";
    echo "<br />";
    echo "<a href=\"$base_url\">$lang_continue</a>";
    echo "</center>";
    exit();
}

if(!ereg($_SERVER[HTTP_HOST],$referer)) {
    $procede = false;
    echo "<br />";
    echo "<br />";
    echo "<br />";
    echo "<center><h2><font color=\"DD0000\">".$config['error_11']."</font></h2>"; //could not be found
    echo "<br />";
    echo "<br />";
    echo "<br />";
    echo "<br />";
    echo "<a href=\"$base_url\">$lang_continue</a>";
    echo "</center>";
    exit();
}

if($procede == true) {
    $vid = (int)mysql_real_escape_string($_GET['vid']);
    $sql = "SELECT video_id FROM videos WHERE indexer = '$vid'";
    $result = mysql_query($sql);

    while($row = mysql_fetch_assoc($result)) {
        $video_id = $row['video_id'];
    }

    @mysql_close();

    $video_play = $video_id.'.flv';

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // THIS IS TMP AS we now have small player thumbs so the old vids, the player wont find large thumbs player large thumb

    $large_player_thumb = $video_id.'.jpg';
    $thumb_file = "uploads/player_thumbs/$large_player_thumb";

    if(!file_exists($thumb_file)) $thumb_file = 'uploads/thumbs/'.$video_id.'.jpg';

    $template = "themes/$user_theme/templates/make_player.html";
    $TBS = new clsTinyButStrong;
    $TBS->NoErr = true;

    $TBS->LoadTemplate("$template");

    $TBS->Render = TBS_OUTPUT;
    $TBS->Show();
    die();
}

?>