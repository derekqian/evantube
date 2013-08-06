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
include_once ('includes/enabled_features.php');

$page_title			= $lang_browse_images . ' ' . $lang_on .  ' ' . $site_name;

$ahah				= 1;

$progress_bar_width	= 0;
$blk_notification 	= '';
$load_ajax			= '';
$load_javascript		= '';
$show_v			= '';
$tag_cloud 			= make_tag_cloud('images');
$tag_cloud_block		= $tag_cloud[1];											//$tag_cloud[1]= small $tag_cloud[0]=large
$limit 			= mysql_real_escape_string($config['see_more_limits']);
$all_albums			= '';
$albums_list		= '';

// get left menu listing--------------------------------

$albums_display_limit = 7;
$temp_page_holder = $_GET['page']; //Bug fix: pagination() already calls $_GET['page'] so have to reset to prevent pagination on side albums
$_GET['page'] = '';

$pagination 	= pagination("SELECT * FROM image_galleries WHERE has_images = '1' AND approved='yes' $sql_public_private ORDER BY gallery_id DESC", $albums_display_limit);
$set_limit 		= $pagination[0]['set_limit'];
$total_pages 	= $pagination[0]['total_pages'];
$current_page 	= $pagination[0]['current_page'];
$total_records 	= $pagination[0]['total_records'];
$next_page 		= $pagination[0]['next_page'];//use in html navigation (src)
$prev_page 		= $pagination[0]['prev_page'];//use in html navigation (src)
$nl 			= $pagination[0]['nl'];//use in html navigation: next>>
$pl 			= $pagination[0]['pl'];//use in html navigation: <<previous

$sql = "SELECT * FROM image_galleries WHERE has_images = '1' AND approved='yes' $sql_public_private ORDER BY gallery_id DESC LIMIT $albums_display_limit";
$query = @mysql_query($sql);

while ($result = @mysql_fetch_array($query)) {
	$gallery_id		= (int) mysql_real_escape_string($result['gallery_id']);
	$sql0			= "SELECT * FROM images WHERE gallery_id = '$gallery_id' AND approved = 'yes' $sql_public_private ORDER BY indexer DESC";
    	$query0		= @mysql_query($sql0);
    	$count_images	= mysql_num_rows($query0);
    	$img_count		= array('img_count' => $count_images);
	$new_array 		= @array_merge($result, $img_count);
    	$albums_list[] 	= $new_array;
}

if ( sizeof($albums_list) > 1 ) {
	$show_albums 	= 1;
	$show_height	= 'height:257px;';
}else{
	$show_albums	= 0;
	$show_height	= 'height:42px;';
}

	// PAGINATION PLUS >> start  -- reusable code
      $hide_numbering = true; //show only <<previous  Next>>>
      $url = 'javascript:void(0)" onClick="javascript:ahahscript.ahah(\'albums_ajax.php';//the url to be put in links - EDIT ME
      $ahah_pagination = "', 'Ajax-Albums', '', 'GET', '', this);";//for ajax pagination
      $additional_url_variable = '?page=';//add addtions information that goes in query string here
      include ('includes/pagination.inc.php');
	$ablums_pagination = $show_pages;
      $hide_numbering = false;//reset
      // PAGINATION PLUS >> end


$_GET['page']= $temp_page_holder;
if ( isset($_GET['page']) ) $page 		= (int) mysql_real_escape_string($_GET['page']); else $page = 1;
if ( isset($_GET['load']) ) $which_one	= mysql_real_escape_string($_GET['load']); else $which_one	= '';
if ( isset($_GET['code']) ) $codes 		= (int) mysql_real_escape_string($_GET['code']); else $codes = '';


//get most recent  -----------------------------------
if ( $which_one == 'recent' || $which_one == '' ) {
	$pagination 	= pagination("SELECT * FROM image_galleries WHERE has_images = '1' AND approved='yes' $sql_public_private ORDER BY gallery_id DESC", $limit);
    	$set_limit 		= $pagination[0]['set_limit'];
    	$total_pages 	= $pagination[0]['total_pages'];
    	$current_page 	= $pagination[0]['current_page'];
    	$total_records 	= $pagination[0]['total_records'];
    	$next_page 		= $pagination[0]['next_page'];//use in html navigation (src)
    	$prev_page 		= $pagination[0]['prev_page'];//use in html navigation (src)
    	$nl 			= $pagination[0]['nl'];//use in html navigation: next>>
    	$pl 			= $pagination[0]['pl'];//use in html navigation: <<previous

    	$sql = "SELECT * FROM image_galleries WHERE has_images = '1' AND approved='yes' $sql_public_private ORDER BY gallery_id DESC LIMIT $set_limit, $limit";
	$query = @mysql_query($sql);

	while ($result = @mysql_fetch_array($query)) {
		$all_albums[] 	= $result;
	}

    	$see_more_title = $config['most_recent'];

    	// PAGINATION PLUS >> start  -- reusable code
    	$pagination_html =''; //rest
    	$show_pages =''; //rest
    	$ahah_pagination =''; //rest
    	$url 					= 'albums';							//the url to be put in links - EDIT ME
    	$additional_url_variable 	= '/recent/';						//information that goes in query string e.g. '&load=groups&friends=all'
    	include ('includes/pagination.inc.php');
    	// PAGINATION PLUS >> end
}

//------------------------------------------------------

//get featured  -----------------------------------
if ($which_one == 'featured') {
	$pagination 	= pagination("SELECT * FROM image_galleries WHERE has_images = '1' AND approved='yes' $sql_public_private ORDER BY gallery_id DESC", $limit);
    	$set_limit 		= $pagination[0]['set_limit'];
    	$total_pages 	= $pagination[0]['total_pages'];
    	$current_page 	= $pagination[0]['current_page'];
    	$total_records 	= $pagination[0]['total_records'];
    	$next_page 		= $pagination[0]['next_page'];//use in html navigation (src)
    	$prev_page 		= $pagination[0]['prev_page'];//use in html navigation (src)
    	$nl 			= $pagination[0]['nl'];//use in html navigation: next>>
    	$pl 			= $pagination[0]['pl'];//use in html navigation: <<previous

	$sql = "SELECT DISTINCT images.gallery_id, image_galleries.* FROM image_galleries LEFT JOIN images ON images.gallery_id = image_galleries.gallery_id WHERE images.featured = 'yes' ORDER BY image_galleries.gallery_id DESC LIMIT $set_limit, $limit";

	$query = @mysql_query($sql);
	while ($result = @mysql_fetch_array($query)) {
		$all_albums[] 	= $result;
	}

	$see_more_title = $config['featured'];

    	// PAGINATION PLUS >> start  -- reusable code
    	$pagination_html =''; //rest
    	$show_pages =''; //rest
    	$ahah_pagination =''; //rest
    	$url 					= 'albums';							//the url to be put in links - EDIT ME
    	$additional_url_variable 	= '/featured/';						//information that goes in query string e.g. '&load=groups&friends=all'
    	include ('includes/pagination.inc.php');
    	// PAGINATION PLUS >> end
}

//------------------------------------------------------

//get most recent  -----------------------------------
if ($which_one == 'viewed') {
	$pagination 	= pagination("SELECT * FROM image_galleries WHERE has_images = '1' AND approved='yes' $sql_public_private ORDER BY number_of_views DESC", $limit);
    	$set_limit 		= $pagination[0]['set_limit'];
    	$total_pages 	= $pagination[0]['total_pages'];
    	$current_page 	= $pagination[0]['current_page'];
    	$total_records 	= $pagination[0]['total_records'];
    	$next_page 		= $pagination[0]['next_page'];//use in html navigation (src)
    	$prev_page 		= $pagination[0]['prev_page'];//use in html navigation (src)
    	$nl 			= $pagination[0]['nl'];//use in html navigation: next>>
    	$pl 			= $pagination[0]['pl'];//use in html navigation: <<previous

    	$sql = "SELECT * FROM image_galleries WHERE has_images = '1' AND approved='yes' $sql_public_private ORDER BY number_of_views DESC LIMIT $set_limit, $limit";
	$query = @mysql_query($sql);

	while ($result = @mysql_fetch_array($query)) {
		$all_albums[] 	= $result;
	}

	$see_more_title = $config['most_viewed'];

    	// PAGINATION PLUS >> start  -- reusable code
    	$pagination_html =''; //rest
    	$show_pages =''; //rest
    	$ahah_pagination =''; //rest
    	$url 					= 'albums';								//the url to be put in links - EDIT ME
    	$additional_url_variable 	= '/viewed/';						//information that goes in query string e.g. '&load=groups&friends=all'
    	include ('includes/pagination.inc.php');
    	// PAGINATION PLUS >> end
}

//------------------------------------------------------

// call template
if (empty($all_albums))
	$show_v = 1;
else
	$show_v = 2;

$error_code			= errorcodes($codes);

if (!empty($error_code)) {
	$blk_notification = $error_code['error_display'];
    	$message_type 	= $error_code['error_type'];
    	$error_message 	= $error_code['error_message'];
}

$template			= "themes/$user_theme/templates/main_1.htm";
$inner_template1		= "themes/$user_theme/templates/inner_albums.htm";
$TBS				= new clsTinyButStrong;
$TBS->NoErr			= true;

$TBS->LoadTemplate("$template");

$TBS->MergeBlock('mp', $all_albums);
$TBS->MergeBlock('blk1', $albums_list);

$TBS->Render = TBS_OUTPUT;
$TBS->Show();

@mysql_close();
die();

?>

