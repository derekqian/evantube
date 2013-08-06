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


$member_id = mysql_real_escape_string($_GET['uid']);

// check if uid is in the requesting url
if ($member_id == "") {
    @mysql_close();
    header("Location: " . "index.php");
    die();
}

// check if uid in request url is same as logged in members user_id. If so load update profile
// showing a green error that user must logout to view their own profile
if ($member_id == $user_id) {
    @mysql_close();
    header("Location: " . "myaccount.php?code=103");
    die();
}

// otherwise proceed to retrieve members details - starting with profile informaton
$sql = "SELECT * FROM member_profile WHERE user_id = $member_id";
$query = @mysql_query($sql);
// if no member found redirect to home page with error code
if (@mysql_num_rows($query) == 0) {
    header("Location: " . "index.php?code=102");
    die();
}
else {
    $result = @mysql_fetch_array($query);
    // get all values for display on members profile section
    $members_username = $result["user_name"];
    $year_of_birth = $result["year_of_birth"];
    $gender = $result["gender"];
    $relationship_satus = $result["relationship_status"];
    $about_me = $result["about_me"];
    $personal_website = $result["personal_website"];
    $home_town = $result["home_town"];
    $home_country = $result["home_country"];
    $current_country = $result["current_country"];
    $high_school = $result["high_school"];
    $college = $result["college"];
    $work_places = $result["work_places"];
    $interests = $result["interests"];
    $fav_movies = $result["fav_movies"];
    $fav_music = $result["fav_music"];
    $current_city = $result["current_city"];
}

// get members picture
// check friends picture
$sql = "SELECT * FROM pictures WHERE user_id = $member_id";
$result1 = @mysql_query($sql);
if (@mysql_num_rows($result1) != 0) {
    $result1 = mysql_fetch_array($result1);
    $result1_existing_file = $result1['file_name'];
    $result1_approved = $result1['approved'];
    if ($result1_approved == "yes") {
        // show current picture
        $result1_mypicture = $config['site_base_url'] . '/pictures/' . $result1_existing_file;
    }
    else {
        // show place holder image  for image "awaiting approval"
        $result1_mypicture = $config['site_base_url'] . "/themes/$user_theme/images/placeholder.gif";
    }
}
else {
    // show place holder for no image uploaded by user at all
    $result1_mypicture = $config['site_base_url'] . "/themes/$user_theme/images/placeholder.gif";

}
/*----------------------------resuable video display code----------------------------*/
//Pagination

$limit = 1;
$pagination = pagination("SELECT * FROM favorites WHERE user_id = $member_id", $limit);
$set_limit = $pagination[0]['set_limit'];
$total_pages = $pagination[0]['total_pages'];
$current_page = $pagination[0]['current_page'];
$total_records = $pagination[0]['total_records'];
$next_page = $pagination[0]['next_page'];//use in html navigation (src)
$prev_page = $pagination[0]['prev_page'];//use in html navigation (src)
$nl = $pagination[0]['nl'];//use in html navigation: next>>
$pl = $pagination[0]['pl'];//use in html navigation: <<previous

//Select video to play
include_once ('video_selector.php');

/*
* -----------------------------reusable favorites code -------------------------------
* display members snaphot video thumbs (3 total)
*/
$result = array();
$sql = "SELECT * FROM favorites WHERE user_id = $member_id LIMIT $set_limit, $limit";
$query = @mysql_query($sql);
if (@mysql_num_rows($query) == 0) {
    $show_v = 1;
}
else {
    while ($result_members_favorites = @mysql_fetch_array($query)) {
        $fav_id = mysql_real_escape_string($result_members_favorites['video_id']);

        // get more details about video i.e titles etc
        $sql2 = "SELECT * FROM videos WHERE indexer = $fav_id ";
        $query2 = @mysql_query($sql2);
        $result_members_fav_videos = @mysql_fetch_array($query2);

        //get rating for each vide
        $video_id = $result_members_fav_videos['indexer'];
        $stars_array = array();
        $stars_array = stars_array($video_id);

        //merge comments array and video array
        $result2 = @array_merge($result_members_fav_videos, $stars_array);
        $result[] = $result2;
    }
}
/*
* END
*/

//set condition for hidding certain blocks (e.g "no emails to list")
if (empty($result)) {
    $show_v = 1;
}
else {
    $show_v = 2;
}
/*END*/

// display page
// checking for any error codes
$codes = $_GET['code'];
$error_code = errorcodes($codes);
if (!empty($error_code)) {
    $blk_notification = $error_code['error_display'];
    $message_type = $error_code['error_type'];
    $error_message =$error_code['error_message'];
}
$template = "themes/$user_theme/templates/main_1.htm";
$inner_template1 = "themes/$user_theme/templates/inner_members_favorites.htm";//middle of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->MergeBlock('blkfeatured', $result);
$TBS->Render = TBS_OUTPUT;
$TBS->Show();

@mysql_close();
die();


?>