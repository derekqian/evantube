<?php


include_once ('../classes/config.php');
include_once ('includes/inc.stats.php');
include_once ('includes/functions.php');
include_once ('includes/login_check.php');


/////////////////////
//defaults settings /
/////////////////////
$show_notification = 0;
$newsid = @mysql_real_escape_string($_GET['id']);


//UPDATE or CREATE
//////////////////

if(isset($_POST['submit'])){

$news_flash = @mysql_real_escape_string($_POST['FCKeditor1']);
$news_headline = @mysql_real_escape_string($_POST['news_headline']);
$newsid = $_POST['newsid']; //this will be set if its an edit request

if ($news_flash == '' || $news_headline == ''){
$show_notification = 1;
$message = $config['fill_all_fields'];
}else{

if($newsid == ''){
//create new article
$sql = "INSERT INTO news_flash (publish, news_flash, date_created, news_picture, news_headline) VALUES ('yes', '$news_flash', NOW(), 'none.gif', '$news_headline')";
}else{
//update existing article
$sql = "UPDATE news_flash SET news_flash = '$news_flash', news_headline = '$news_headline' WHERE news_id = $newsid";
}
@mysql_query($sql);
//display notification
if(@mysql_error()){
$show_notification =1;
$message = $config["error_26"]; //error
}else{
$show_notification =1;
$message = $config["error_25"]; //success
}
}
}




//Get All news items
////////////////////

if($newsid != ''){
$sql = "SELECT * FROM news_flash WHERE news_id = $newsid";
$query = @mysql_query($sql);
$result = @mysql_fetch_array($query);
$news_flash = $result['news_flash'];
$news_headline = $result['news_headline'];
}

if(isset($_POST['submit'])){
    //get form post (no mysql_real_escapE)
$news_flash = $_POST['FCKeditor1'];
$news_headline = $_POST['news_headline'];
}

///FCK EDITOR________________________________________________________________________
    	include('../fckeditor/fckeditor.php');

    	$sBasePath 						= "../fckeditor/";
    	$oFCKeditor 					= new FCKeditor('FCKeditor1');
    	$oFCKeditor->BasePath 				= $sBasePath;
    	$oFCKeditor->CustomConfigurationsPath 	= "fckconfig.js";
    	$oFCKeditor->ToolbarSet 			= 'Basic';
    	$oFCKeditor->SkinPath 				= $sBasePath + 'editor/skins/silver/';

    	$oFCKeditor->Value  				= "$news_flash";
    	$oFCKeditor->Width  				= '600';
    	$oFCKeditor->Height 				= '220';
    	$FCKeditor1 					= $oFCKeditor->CreateHtml();
    	$my_edit 						= $FCKeditor1;
//____________________________________________________________________________________

////////////////////////////////
//display form with error message
////////////////////////////////
$template = "templates/inner_edit_news.htm";//middle of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->Render = TBS_OUTPUT;
$TBS->Show();
@mysql_close();
die();

?>