<?php


include_once ("../classes/config.php");
include_once ("includes/inc.stats.php");
include_once ("includes/functions.php");
include_once ('includes/login_check.php');

//Menu Loaders
///////////////
$top_menu = '';
$side_menu = 'settings';
$dashboard_header = $lang_themes;

/////////////////////
//defaults settings /
/////////////////////
$show_hide = 1;
$show_notification = 0;
$todays_date = $config["date_format"];
$base_url = $config['site_base_url'];
$result_active = array();
$base_path = installation_paths();
$_SESSION['statsxml'] = '';//reset this session

////////////////
//check for type
////////////////


//check for posted year
///////////////////////
if ($_POST['year'] != '')
{
    $year = $_POST['year'];
}
else
{
    $year = date("Y");
}

//Build XMl data for comparing stats>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
$stats_title = "$lang_statistics - $lang_compare";
$selectedyear = $year;
for ($count = 1; $count < 13; $count++) { //12 months of the year


//videos	
$sql = "SELECT indexer FROM videos WHERE YEAR(date_uploaded)= $selectedyear AND MONTH(date_uploaded)= $count";
$query = mysql_query($sql);
$counter = mysql_num_rows($query);
//build part of XML file
$xml_counter_videos = $xml_counter_videos . "<number>$counter</number>\n";


//audio
$sql = "SELECT indexer FROM audios WHERE YEAR(date_uploaded)= $selectedyear AND MONTH(date_uploaded)= $count";
$query = mysql_query($sql);
$counter = mysql_num_rows($query);
//build part of XML file
$xml_counter_audio = $xml_counter_audio . "<number>$counter</number>\n";


//blogs
$sql = "SELECT indexer FROM blogs WHERE YEAR(date_created)= $selectedyear AND MONTH(date_created)= $count";
$query = mysql_query($sql);
$counter = mysql_num_rows($query);
//build part of XML file
$xml_counter_blogs = $xml_counter_blogs . "<number>$counter</number>\n";


//images
$sql = "SELECT indexer FROM images WHERE YEAR(date_uploaded)= $selectedyear AND MONTH(date_uploaded)= $count";
$query = mysql_query($sql);
$counter = mysql_num_rows($query);
//build part of XML file
$xml_counter_images = $xml_counter_images . "<number>$counter</number>\n";


//members
$sql = "SELECT user_id FROM member_profile WHERE YEAR(date_created)= $selectedyear AND MONTH(date_created)= $count";
$query = mysql_query($sql);
$counter = mysql_num_rows($query);
//build part of XML file
$xml_counter_members = $xml_counter_members . "<number>$counter</number>\n";

}





//Write full XML data to a session(
//Used /siteadmin/includes/stats_xml.php
////////////////////////////////////////
$xml_counter1 = "<row>\n<string>$lang_word_videos</string>\n$xml_counter_videos</row>\n"; //videos
$xml_counter2 = "<row>\n<string>$lang_word_Audio</string>\n$xml_counter_audio</row>\n"; //audio
$xml_counter3 = "<row>\n<string>$lang_blogs</string>\n$xml_counter_blogs</row>\n"; //blogs
$xml_counter4 = "<row>\n<string>$lang_word_images</string>\n$xml_counter_images</row>\n"; //images
$xml_counter5 = "<row>\n<string>$lang_members</string>\n$xml_counter_members</row>\n"; //members

//merge it all together
$xml_counter_compare = "$xml_counter1\n$xml_counter2\n$xml_counter3\n$xml_counter4\n$xml_counter5\n";


//start session to store xml data
@session_start();
@session_register('statsxml_compare');//used to hold stats xml
$_SESSION['statsxml_compare'] = $xml_counter_compare;




//Generate year drop down
/////////////////////////
for ($yearcount = 2001; $yearcount < 2050; $yearcount++)
{
    $yearcount_as_string = $yearcount;
    settype($yearcount_as_string, "string");
    if ($yearcount_as_string == $year)
    {
        $yearselect = $yearselect . '<option value="' . $yearcount . '" selected>&nbsp;' .
            $yearcount . '&nbsp;</option>';
    }
    else
    {
        $yearselect = $yearselect . '<option value="' . $yearcount . '" ' . $selected .
            '>&nbsp;  ' . $yearcount . '&nbsp;  </option>';
    }
}



//Handle errors that may crash browser
//////////////////////////////////////
$errors = 0;
if (mysql_error())
{
    $errors = 1;//this stops flash from loading
    $show_notification = 1;
    $message = $config["error_26"];
}




////////////////////////////////
//display form with error message
////////////////////////////////
$template = "templates/main.html";
$inner_template1 = "templates/inner_statistics_compare.htm";//middle of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
//$TBS->MergeBlock('adminblk', $result);
$TBS->Render = TBS_OUTPUT;
$TBS->Show();
@mysql_close();
die();

?>