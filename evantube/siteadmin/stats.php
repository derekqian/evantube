<?php


include_once ("../classes/config.php");
include_once ("includes/inc.stats.php");
include_once ("includes/functions.php");
include_once ('includes/login_check.php');

//Menu Loaders
///////////////
$top_menu = '';
$side_menu = 'settings';
$dashboard_header = $lang_site_statistics ;

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

if (isset($_POST['type'])) {

    $type = $_POST['type'];
}
else {
    $type = $_GET['type'];
}

if ($type == 1 || $type == 2 || $type == 3 || $type == 4 || $type == 5 || $type == 6) {
    $show_t = $type;
}
else {
    $show_t = 1;
    $type = 1;
}

//check for posted year
///////////////////////
if($_POST['year'] != ''){
$year = $_POST['year'];
}else{
$year = date("Y");
}	
	

//Vidoes, images, blogs uploaded>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

$xml_counter = '';
$harddrive = 0;
$totals = 0;
$media_views = 0;
$media_count = 0;
$selectedyear = $year;

//Run thorugh 12 times for 12 months
for ($count = 1; $count < 13; $count++) {

    //select dbase to query
    ///////////////////////
    switch ($type) {
        case 1:
            //videos uploaded
            $sql = "SELECT indexer FROM videos WHERE YEAR(date_uploaded)= $selectedyear AND MONTH(date_uploaded)= $count";
            $sql2 = "SELECT indexer FROM videocomments";
            $sql3 = "SELECT indexer FROM views_tracker WHERE YEAR(date_viewed)= $selectedyear AND MONTH(date_viewed)= $count AND media_type ='videos'"; 
            $graph_title = "$lang_word_videos ($lang_uploaded)";
            $graph_title_viewed = "$lang_word_videos ($lang_viewed)";
            $stats_title = "$lang_statistics - $lang_word_videos";
            $harddrive = $videos_folder;
            $media_count = $video_total;
            $media_views = $total_views_videos;
 
             //videos watched           
            
            break;

        case 2:
            $sql = "SELECT indexer FROM audios WHERE YEAR(date_uploaded)= $selectedyear AND MONTH(date_uploaded)= $count";
            $sql2 = "SELECT indexer FROM audiocomments";
            $sql3 = "SELECT indexer FROM views_tracker WHERE YEAR(date_viewed)= $selectedyear AND MONTH(date_viewed)= $count AND media_type ='audios'"; 
            $graph_title = "$lang_word_Audio ($lang_uploaded)";
            $graph_title_viewed = "$lang_word_Audio ($lang_played)";            
            $stats_title = "$lang_statistics - $lang_word_Audio";
            $harddrive = $mp3_folder;
            $media_count = $audio_total;
            $media_views = $total_views_audio;
            break;

        case 3:
            $sql = "SELECT indexer FROM blogs WHERE YEAR(date_created)= $selectedyear AND MONTH(date_created)= $count";
            $sql2 = "SELECT indexer FROM blog_replys";
            $sql3 = "SELECT indexer FROM views_tracker WHERE YEAR(date_viewed)= $selectedyear AND MONTH(date_viewed)= $count AND media_type ='blogs'";
            $graph_title = "$lang_blogs ($lang_uploaded)";
            $graph_title_viewed = "$lang_blogs ($lang_read_past)";
            $stats_title = "$lang_statistics - $lang_blogs";
            $harddrive = 'n/a';
            $media_count = $blog_total;
            $media_views = $total_views_blogs;
            break;

        case 4:
            //images uploaded
            $sql = "SELECT indexer FROM images WHERE YEAR(date_uploaded)= $selectedyear AND MONTH(date_uploaded)= $count";
            $sql2 = "SELECT indexer FROM imagecomments";
            $sql3 = "SELECT indexer FROM views_tracker WHERE YEAR(date_viewed)= $selectedyear AND MONTH(date_viewed)= $count AND media_type ='images'"; 
            $stats_title = "$lang_statistics - $lang_word_images";
            $graph_title = "$lang_word_images ($lang_uploaded)";
            $graph_title_viewed = "$lang_word_images ($lang_viewed)";
            $harddrive = $pictures_folder;
            $media_count = $image_total;
            $media_views = $total_views_images;
            
            //images viewed
            
            break;

        case 5:
            $sql = "SELECT user_id FROM member_profile WHERE YEAR(date_created)= $selectedyear AND MONTH(date_created)= $count";
            $sql2 = "SELECT indexer FROM profilecomments";
            $sql3 = "SELECT indexer FROM views_tracker WHERE YEAR(date_viewed)= $selectedyear AND MONTH(date_viewed)= $count AND media_type ='member_profile'";
            $graph_title = $lang_members;
            $graph_title_viewed = "$lang_word_videos ($lang_viewed)";
            $graph_title_viewed = "$lang_profiles ($lang_viewed)";
            $stats_title = "$lang_statistics - $lang_members";
            $media_count = $members_total;
            $harddrive = 'n/a';
            $media_views = $total_views_profiles;
            break;
    }


    // Execute actual query (sql1) - uploaded
    //////////////////////////////////////
    $query = mysql_query($sql);
    $counter = mysql_num_rows($query);
    $totals = $totals + $counter; //total content




    //build part of XML file (sql1) - Uploaded
    ///////////////////////////////////////
    $xml_counter = $xml_counter . "<number>$counter</number>\n";



    // Execute count of comments (sql2)
    ////////////////////////////////////
    $query2 = mysql_query($sql2);
    $comments = mysql_num_rows($query2);



    // Execute actual query (sql3) - Viewed
    //////////////////////////////////////
    $query3 = mysql_query($sql3);
    $counter3 = mysql_num_rows($query3);


    //build part of XML file (sql3) - Viewed
    ///////////////////////////////////////
    $xml_counter_viewed = $xml_counter_viewed . "<number>$counter3</number>\n";



    //Allocate per month for yearly table
    /////////////////////////////////////
    switch ($count) {//where $count = 1->12

        case 1:
            $jan = $counter;
            break;

        case 2:
            $feb = $counter;
            break;

        case 3:
            $mar = $counter;
            break;

        case 4:
            $apr = $counter;
            break;

        case 5:
            $may = $counter;
            break;

        case 6:
            $jun = $counter;
            break;

        case 7:
            $jul = $counter;
            break;

        case 8:
            $aug = $counter;
            break;

        case 9:
            $sep = $counter;
            break;

        case 10:
            $oct = $counter;
            break;

        case 11:
            $nov = $counter;
            break;

        case 12:
            $dec = $counter;
            break;
    }
    
    


    //Allocate per month for yearly table for lastyear
    //////////////////////////////////////////////////
    switch ($count) {//where $count = 1->12

        case 1:
            $jan_view = $counter3;
            break;

        case 2:
            $feb_view = $counter3;
            break;

        case 3:
            $mar_view = $counter3;
            break;

        case 4:
            $apr_view = $counter3;
            break;

        case 5:
            $may_view = $counter3;
            break;

        case 6:
            $jun_view = $counter3;
            break;

        case 7:
            $jul_view = $counter3;
            break;

        case 8:
            $aug_view = $counter3;
            break;

        case 9:
            $sep_view = $counter3;
            break;

        case 10:
            $oct_view = $counter3;
            break;

        case 11:
            $nov_view = $counter3;
            break;

        case 12:
            $dec_view = $counter3;
            break;
    }

}



//write it all in xml format
$xml_counter = "<row>\n<string>$graph_title - $selectedyear</string>\n$xml_counter</row>\n";
$xml_counter_viewed = "<row>\n<string>$graph_title_viewed - $selectedyear</string>\n$xml_counter_viewed</row>\n";
//start session to store xml data
@session_start();
@session_register('statsxml');//used to hold stats xml
@session_register('statsxml_viewed');//used to hold stats xml
$_SESSION['statsxml'] = $xml_counter;
$_SESSION['statsxml_viewed'] = $xml_counter_viewed;

//(Previous Year) Vidoes, images, blogs uploaded>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

$lastyear = $year -1;;
$xml_counter = '';
$xml_counter_viewed ='';
for ($count = 1; $count < 13; $count++) { //12 months of the year

    //select dbase to query
    ///////////////////////
    switch ($type) {
        case 1:
            $sql = "SELECT indexer FROM videos WHERE YEAR(date_uploaded)= $lastyear AND MONTH(date_uploaded)= $count";
            $sql3 = "SELECT indexer FROM views_tracker WHERE YEAR(date_viewed)= $lastyear AND MONTH(date_viewed)= $count AND media_type ='videos'"; 
            $graph_title = "$lang_word_videos ($lang_uploaded)";
            $graph_title_viewed = "$lang_word_videos ($lang_viewed)";
            break;

        case 2:
            $sql = "SELECT indexer FROM audios WHERE YEAR(date_uploaded)= $lastyear AND MONTH(date_uploaded)= $count";
            $sql3 = "SELECT indexer FROM views_tracker WHERE YEAR(date_viewed)= $lastyear AND MONTH(date_viewed)= $count AND media_type ='audios'"; 
            $graph_title = "$lang_word_Audio ($lang_uploaded)";
            $graph_title_viewed = "$lang_word_Audio ($lang_played)";  
            break;

        case 3:
            $sql = "SELECT indexer FROM blogs WHERE YEAR(date_created)= $lastyear AND MONTH(date_created)= $count";
            $sql3 = "SELECT indexer FROM views_tracker WHERE YEAR(date_viewed)= $lastyear AND MONTH(date_viewed)= $count AND media_type ='blogs'";
            $graph_title = "$lang_blogs ($lang_uploaded)";
            $graph_title_viewed = "$lang_blogs ($lang_read_past)";
            $stats_title = "$lang_statistics - $lang_blogs";
            break;

        case 4:
            $sql = "SELECT indexer FROM images WHERE YEAR(date_uploaded)= $lastyear AND MONTH(date_uploaded)= $count";
            $sql3 = "SELECT indexer FROM views_tracker WHERE YEAR(date_viewed)= $lastyear AND MONTH(date_viewed)= $count AND media_type ='images'"; 
            $stats_title = "$lang_statistics - $lang_word_images";
            $graph_title = "$lang_word_images ($lang_uploaded)";
            $graph_title_viewed = "$lang_word_images ($lang_viewed)";
            break;

        case 5:
            $sql = "SELECT user_id FROM member_profile WHERE YEAR(date_created)= $lastyear AND MONTH(date_created)= $count";
            $sql3 = "SELECT indexer FROM views_tracker WHERE YEAR(date_viewed)= $lastyear AND MONTH(date_viewed)= $count AND media_type ='member_profile'";
            $graph_title = $lang_members;
            $graph_title_viewed = "$lang_word_videos ($lang_viewed)";
            $graph_title_viewed = "$lang_profiles ($lang_viewed)";
            $stats_title = "$lang_statistics - $lang_members";
            break;
    }

    // Execute actual query (sq1)-uploaded
    ////////////////////////////////////
    $query = mysql_query($sql);
    $counter = mysql_num_rows($query);

    //build part of XML file (sql1)
    ////////////////////////
    $xml_counter = $xml_counter . "<number>$counter</number>\n";
    
    
    // Execute actual query (sql3) - Viewed
    //////////////////////////////////////
    $query3 = mysql_query($sql3);
    $counter3 = mysql_num_rows($query3);

    //build part of XML file (sql3) - Viewed
    ///////////////////////////////////////
    $xml_counter_viewed = $xml_counter_viewed . "<number>$counter3</number>\n";

}


//write it all in xml format
$xml_counter = "<row>\n<string>$graph_title - $lastyear</string>\n$xml_counter</row>\n";
$xml_counter_viewed = "<row>\n<string>$graph_title_viewed - $lastyear</string>\n$xml_counter_viewed</row>\n";
//start session to store xml data
@session_start();
@session_register('statsxml_previous_year');//used to hold stats xml
@session_register('statsxml_previous_year_viewed');//used to hold stats xml
$_SESSION['statsxml_previous_year'] = $xml_counter;
$_SESSION['statsxml_previous_year_viewed'] = $xml_counter_viewed;


//Generate year drop down
/////////////////////////
for($yearcount=2001; $yearcount<2050; $yearcount ++){
	$yearcount_as_string = $yearcount;
settype($yearcount_as_string, "string");	
if($yearcount_as_string == $year){
$yearselect = $yearselect.'<option value="'.$yearcount.'" selected>&nbsp;'.$yearcount.'&nbsp;</option>';
}else{	
$yearselect = $yearselect.'<option value="'.$yearcount.'" '.$selected.'>&nbsp;  '.$yearcount.'&nbsp;  </option>';
}	
}



//Handle errors that may crash browser
$errors = 0;
if(mysql_error()){
$errors = 1; //this stops flash from loading
$show_notification =1;
$message = $config["error_26"];
}
////////////////////////////////
//display form with error message
////////////////////////////////
$template = "templates/main.html";
$inner_template1 = "templates/inner_statistics.htm";//middle of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
//$TBS->MergeBlock('adminblk', $result);
$TBS->Render = TBS_OUTPUT;
$TBS->Show();
@mysql_close();
die();

?>