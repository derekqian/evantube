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
include_once ('tag_cloud.php');
include_once ('online.php');
include_once ('popular.php');


$id = @mysql_real_escape_string($_GET['id']);

if ($id == '' || !is_numeric($id)){
//just shpw latest new item
$sql = "SELECT * FROM news_flash WHERE publish = 'yes'";
}else{
$sql = "SELECT * FROM news_flash WHERE publish = 'yes' AND news_id = $id";
}
$result = @mysql_fetch_array(@mysql_query($sql));
$main_headline = $result['news_headline'];
$news_content = $result['news_flash'];
$date_created = $result['date_created'];

//nothing found
if (mysql_num_rows(mysql_query($sql)) == 0){

$news_content = $config['error_11']; //item not found

}

/////////////////////////////////////////////
// show news headline as dynamic page title
$page_title = $site_name . ' ' . $lang_latest_news . ': ' . $main_headline;


    //display news page
    $template = "themes/$user_theme/templates/main_1.htm";
    $inner_template1 = "themes/$user_theme/templates/inner_news.htm";
    $TBS = new clsTinyButStrong;
    $TBS->NoErr = true;
    $TBS->LoadTemplate("$template");
    $TBS->Render = TBS_OUTPUT;
    $TBS->Show();
    @mysql_close();
    die();

?>

