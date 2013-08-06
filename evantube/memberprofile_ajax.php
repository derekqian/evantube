<?php ///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once ('classes/config.php');
include_once ('classes/sessions.php');
include_once ('online.php');

$type = @mysql_real_escape_string($_GET['type']);
$member_id = $_GET['uid'];
$sql_public_private = "AND public_private = 'public'"; //default show only public stuff

//__________________________________________________________________________________________________________________________________________
//______Check if viewer is friend____________________________________________________________________________________________________________

if($user_id != '' && $user_id != $member_id){
$sql = "SELECT * FROM friends WHERE user_id = $user_id AND friends_id = $member_id AND invitation_status = 'accepted' OR user_id = $member_id AND friends_id = $user_id AND invitation_status = 'accepted'";
$query = @mysql_query($sql);
if (mysql_num_rows($query) > 0){
$sql_public_private = '';
}
}

//__________________________________________________________________________________________________________________________________________
//______Check if viewer is owner____________________________________________________________________________________________________________

if($user_id == $member_id){
$show_edit_delete = 1; //allow owner to manage their content (edit/delete buttons)
$sql_public_private = '';
}


//__________________________________________________________________________________________________________________________________________
//______Check if viewer is Admin/Mod____________________________________________________________________________________________________________

if($user_group == 'adim' || $user_group == 'global_mod' || $user_group == 'standard_mod'){
$sql_public_private = '';
}

//__________________________________________________________________________________________________________________________________________
//______Members Videos - Audio - Blogs - Pictures - Fav Videos - Fav Images - Fav Audio_____________________________________________________
if ($type == 1 || $type == 5 || $type == 6 || $type == 3 || $type == 2 || $type == 4 || $type == 7) {

    //select table to query
    switch ($type) {

        //members videos
        case 1:
            $table = 'videos';
            $members_limit = 6;
            $load_media = 1; //used in html
            $delete_code = 1; //used in html & /edit/delete.php
            $extrasql = "AND approved = 'yes'$sql_public_private";
            $show_edit = 1; //html link
            $ahah_div = 'Ajax-Media';
            break;

        //members fav videos
        case 2:
            $table = 'favorites';
            $table2 = 'videos';
            $members_limit = 6;
            $load_media = 1; //used in html
            $delete_code = 9; //used in html & /edit/delete.php
            $extrasql = '';
            $ahah_div = 'Ajax-Aboutme';
            break;

        //members images
        case 3:
            $table = 'images';
            $members_limit = 6;
            $load_media = 2; //used in html
            $delete_code = 2; //used in html & /edit/delete.php
            $extrasql = "AND approved = 'yes'$sql_public_private";
            $show_edit = 1; //html link
            $ahah_div = 'Ajax-Media';
            break;

        //members fav images
        case 4:
            $table = 'image_favorites';
            $table2 = 'images';
            $members_limit = 6;
            $load_media = 2; //used in html
            $delete_code = 10; //used in html & /edit/delete.php
            $extrasql = '';
            $ahah_div = 'Ajax-Aboutme';
            break;

        //members blogs
        case 5:
            $table = 'blogs';
            $members_limit = 6;
            $load_media = 3; //used in html
            $delete_code = 3; //used in html & /edit/delete.php
            $extrasql = "AND approved = 'yes'$sql_public_private";
            $show_edit = 1; //html link
            $ahah_div = 'Ajax-Media';
            break;

        //members audio
        case 6:
            $table = 'audios';
            $members_limit = 6;
            $load_media = 4; //used in html
            $delete_code = 4; //used in html & /edit/delete.php
            $extrasql = "AND approved = 'yes'$sql_public_private";
            $show_edit = 1; //html link
            $ahah_div = 'Ajax-Media';
            break;

        //members fav audio
        case 7:
            $table = 'audio_favorites';
            $table2 = 'audios';
            $members_limit = 6;
            $load_media = 4; //used in html
            $delete_code = 12; //used in html & /edit/delete.php
            $extrasql = '';
            $ahah_div = 'Ajax-Aboutme';
            break;
    }


    //Pagination
    $pagination = pagination("SELECT * FROM $table WHERE user_id = $member_id $extrasql", $members_limit);
    $set_limit = $pagination[0]['set_limit'];
    $total_pages = $pagination[0]['total_pages'];
    $current_page = $pagination[0]['current_page'];
    $total_records = $pagination[0]['total_records'];
    $next_page = $pagination[0]['next_page']; //use in html navigation (src)
    $prev_page = $pagination[0]['prev_page']; //use in html navigation (src)
    $nl = $pagination[0]['nl']; //use in html navigation: next>>
    $pl = $pagination[0]['pl']; //use in html navigation: <<previous

    $member_media = array();
    $sql = "SELECT * FROM $table WHERE user_id = $member_id $extrasql LIMIT $set_limit, $members_limit";
    $query = @mysql_query($sql);
    if (@mysql_num_rows($query) == 0) {
        $show_v = 1;
    } else {
        while ($result_members_media = @mysql_fetch_array($query)) {

            //resize thumb for picture type media
            //////////////////////////////////////
            if ($type == 3) { // 3 = members picture
                //rescale thumbs
                $image_name = $result_members_media['image_id'];
                $page_display_width = $config['members_profile_thumb_width'];
                $image_thumb = $base_path . '/addons/albums/thumbs/' . $image_name;
                $display_thumbs = show_thumb($image_thumb, $page_display_width);
                $image_width = $display_thumbs[0];
                $image_height = $display_thumbs[1];
                $image_array = array('image_width' => $image_width, 'image_height' => $image_height);
                //merge arrays
                $result_members_media = @array_merge($result_members_media, $image_array);
            }

            //For favourites only
            //////////////////////
            if ($type == 2 || $type == 4 || $type == 7) {
                $fav_id = mysql_real_escape_string($result_members_media['video_id']);
                if($type == 4){
                $fav_id = mysql_real_escape_string($result_members_media['image_id']);
                }
                if($type == 7){
                $fav_id = mysql_real_escape_string($result_members_media['audio_id']);
                }
                // get more details about video i.e titles etc
                $sql2 = "SELECT * FROM $table2 WHERE indexer = $fav_id ";
                $query2 = @mysql_query($sql2);
                $result_members_media = @mysql_fetch_array($query2);


            if($type == 4){ //fav pictures resize thumb
            //rescale thumbs
            $image_id = $result_members_media['indexer'];
            $image_name = $result_members_media['image_id'];
            $page_display_width = $config['members_profile_thumb_width'];
            $image_thumb = $base_path . '/addons/albums/thumbs/' . $image_name;
            $display_thumbs = show_thumb($image_thumb, $page_display_width);
            $image_width = $display_thumbs[0];
            $image_height = $display_thumbs[1];
            $image_array = array('image_width' => $image_width, 'image_height' => $image_height);
            //merge arrays
            $result_members_media = @array_merge($result_members_media, $image_array);

            }
            }


            //Compile results
            /////////////////
            $member_media[] = $result_members_media;
            $show_v = 2;
        }

        ////////////////////////////////////////////
        //PAGINATION PLUS >> start  -- reusable code
        ////////////////////////////////////////////
        $show_pages = '';
        $url = 'javascript:void(0)" onClick="javascript:ahahscript.ahah(\'memberprofile_ajax.php'; //the url to be put in links - EDIT ME
        $ahah_pagination = "', '$ahah_div', '', 'GET', '', this);"; //for ajax pagination
        $additional_url_variable = '?type=' . $type . '&uid=' . $member_id . '&page='; //add addtions information that goes in query string here
        include ('includes/pagination.inc.php');
        $show_pages_media = $show_pages;
        //PAGINATION PLUS >> end
    }
}

//__________________________________________________________________________________________________________________________________________
//______General Profile_____________________________________________________________________________________________________________________

if ($type == 8 || $type == 9 || $type == 10) {

// Retrieve members Profile

$sql = "SELECT * FROM member_profile WHERE user_id = $member_id";
$query = mysql_query($sql);
// if no member found redirect to home page with error code
// get all values for display on members profile section
$result = @mysql_fetch_array($query);
if(@mysql_num_rows($query) >0){
foreach ($result as $key => $value) {
    //$key = preg_replace('/[0-9]/','',$key);
    $$key = $value;

}

//create my age
if ( $birthday == '0000-00-00' )$my_age = $lang_private;
else $my_age = birthday ($birthday);


}

}

//__________________________________________________________________________________________________________________________________________
//______Get my Privacy Setting____________________________________________________________________________________________________________

$template = "themes/$user_theme/templates/inner_members_profile_ajax.htm"; //middle of page

$sql = "SELECT * FROM privacy WHERE user_id = $member_id";
$result = @mysql_fetch_array(@mysql_query($sql));
//Public favorites
if( $result['publicfavorites'] == 'no' && $user_id != $member_id){
	if($type == 2 || $type == 4|| $type == 7){
$template = "themes/$user_theme/templates/inner_none_existant_file.htm"; //just to give blank results
}
}


//__________________________________________________________________________________________________________________________________________
//______Display Results_____________________________________________________________________________________________________________________

$TBS = new clsTinyButStrong;
$TBS->NoErr = true; // no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->MergeBlock('membersmedia', $member_media);
//$TBS->MergeBlock('membersfavs', $member_favorites);
//$TBS->MergeBlock('membersfavimages', $member_favorites_images);


$TBS->tbs_show(TBS_NOTHING);
echo $TBS->Source;
@mysql_close();
die(); ?>