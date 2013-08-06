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
include_once ('includes/news.inc.php');
include_once ('popular.php');

$page_title = $lang_upload;

//load javascript
$thickbox = 1;
$greybox = 1;

//Login check
if ($_SESSION['user_id'] == "") {
	header("Location: $login_out_link");;
	die();
}

//___________Check for Powertools___________________

//check for VideoEmbedder
if(file_exists('addons/videoembedder/upload.php')){
$powertool_videoembedder = 1;
}



//check for VideoGrabber
if(file_exists('addons/videograbber/upload.php')){
$powertool_videograbber = 1;
}

//___________Check for Powertools___________________


//START-----------------------

$template = "themes/$user_theme/templates/main_1.htm";
$inner_template1 = "themes/$user_theme/templates/inner_upload_menu.htm";

$TBS = new clsTinyButStrong;
$TBS->NoErr = true;

$TBS->LoadTemplate("$template");
$TBS->Render = TBS_OUTPUT;
$TBS->Show();
@mysql_close();
die();


?>