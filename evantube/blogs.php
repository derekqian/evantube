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
include_once ('classes/permissions.php');

$page_title			= $lang_browse_blogs . ' ' . $lang_on .  ' ' . $site_name;

$tag_cloud 			= make_tag_cloud('blogs');
$tag_cloud_block 		= $tag_cloud[1];
$procede 			= true;
$codes_internal		= '';
$limit 			= (int) mysql_real_escape_string($config['see_more_limits']);

// set some global vars for tbs
$progress_bar_width	= 0;
$blk_notification 	= '';
$blog_max_limits		= $config['blog_max_limits'];
$word_length		= $config['max_tag_word_length'];
$theme_blog_width		= $config['theme_blog_width'];
$blog_success		= $lang_blog_created;

//load fckeditor js
$load_fckeditor 		= 1;

//$ahah			= 1;
$load_ajax 			= 1;

if ( isset($_POST['article_submit']) )	$blog_submitted	= mysql_real_escape_string($_POST['article_submit']);	else $blog_submitted	= '';
if ( isset($_GET['cat_id']) ) 		$blog_category	= (int) mysql_real_escape_string($_GET['cat_id']); 	else $blog_category	= '';
if ( isset($_GET['load']) ) 			$which_one		= mysql_real_escape_string($_GET['load']); 		else $which_one	= 'recent';
if ( isset($_GET['code']) ) 			$codes 		= (int) mysql_real_escape_string($_GET['code']); 	else $codes = '';
if ( isset($_GET['action']) )			$action		= mysql_real_escape_string($_GET['action']); 		else $action	= '';

// this is per each template from config.inc.php advanced config settings
$page_display_small_width	= $config['general_medium_thumb_width']; 					// 80

$which_query_rows = 'indexer, blog_owner, user_id, title, title_seo, description, tags, category, date_created, approved, number_of_views';

// get blog categories
$all_categories	= array();
$sql			= "SELECT category_id, category_name_seo, category_name FROM blog_categories WHERE has_blogs = 'yes' ORDER BY category_id ASC";
$query		= @mysql_query($sql);

while ( $result = @mysql_fetch_array($query) ) {
	$category_id	= $result['category_id'];
    	$sql_cnt 		= "SELECT indexer FROM blogs WHERE category_id = '$category_id' AND approved = 'yes' ORDER BY indexer DESC";
    	$query_cnt 		= @mysql_query($sql_cnt);
    	$count_blogs	= mysql_num_rows($query_cnt);
    	$blog_count		= array('category_count' => $count_blogs);
	$new_array		= @array_merge($result, $blog_count);
	$all_categories[] = $new_array;
}

// start
if ( $blog_submitted == 'yes' ) {

	foreach ($_POST as $key => $value) {

		if ( $key == 'title' || $key == 'description' || $key == 'tags' ) {
			if (!isset($value) || ($value == '')) {

           			$procede 		= false;
           			$message_type	= $lang_error;
                		$message 		= $message_type . " - " . $display_key . "  - $lang_required ";

                		$error_message 	='<p align="center"><font color="#EE0000" face="Arial"><b>'.$user_name.'&nbsp;&nbsp;'.$config['fill_all_fields'].'</b></font>';


	           	} else {
           			if ( $key == 'tags' )		$tag_input		= $value;
           			if ( $key == 'title' ) 		$title_input	= $value;
           			if ( $key == 'description' )	$desc_input		= $value;
           		}
     	   	}
     	}

	$tags_returned = make_tag_words( $tag_input, $word_length );

	if ( $tags_returned[0] == 'false' ) {

		$procede 		= false;
		$message_type	= $lang_error;
		$message		= $tags_returned[1];
		$error_message 	= '<p align="center"><font color="#EE0000" face="Arial"><b>'.$message_type.'&nbsp;&nbsp;'.$message.'</b></font><br />';

	} else {
		$safe_tags		= $tags_returned[1];
	}
}

// display page with form error
if ($procede == false && $blog_submitted == "yes") {

	// we populate create new blog category select box here
	$sql			= "SELECT category_id, category_name_seo, category_name FROM blog_categories ORDER BY category_id ASC";
	$query		= @mysql_query($sql);

	$fields_all 	= "";
	$sub_fields_all	= "";
	$show_fields	= "";

	while ( $result = @mysql_fetch_array($query) ) {
		$fields_all .= '<option value="'.$result['category_id'].'">'.$result['category_name'].'</option>';
	}

	$form_title		= $_POST['title'];
	$form_description	= $_POST['description'];
	$form_tags		= $safe_tags;

	//$blog_story		= strip_tags($_POST['FCKeditor1']);

	$blog_story		= $_POST['FCKeditor1'];


	$show_block 	= 2;
	$blog_errors 	= 1;


	///FCK EDITOR________________________________________________________________________
	include('fckeditor/fckeditor.php');

	$sBasePath 						= "$base_url/fckeditor/";
	$oFCKeditor 					= new FCKeditor('FCKeditor1');
	$oFCKeditor->BasePath 				= $sBasePath;
	$oFCKeditor->CustomConfigurationsPath 	= "fckconfig.js";
	$oFCKeditor->ToolbarSet 			= 'Basic';
	$oFCKeditor->SkinPath 				= $sBasePath + 'editor/skins/silver/';
	$oFCKeditor->Value  				= "$blog_story";
	$oFCKeditor->Width  				= "$theme_blog_width"; //'700';
	$oFCKeditor->Height 				= '150';
	$FCKeditor1 					= $oFCKeditor->CreateHtml();
	$my_edit 						= $FCKeditor1;
	//____________________________________________________________________________________



	$template 		= "themes/$user_theme/templates/main_1.htm";
    	$inner_template1 	= "themes/$user_theme/templates/inner_see_blogs.htm";
    	$TBS 			= new clsTinyButStrong;
    	$TBS->NoErr 	= true;
    	$TBS->LoadTemplate("$template");

    	$TBS->MergeBlock('blk1', $all_categories);

    	$TBS->Render 	= TBS_OUTPUT;
    	$TBS->Show();
	@mysql_close();
    	die();
}



// create new blog - add blog info and story to DB and call refresh
if ( $procede == true && $blog_submitted == 'yes' ) {

	$title		= $_POST['title'];
	$description	= $_POST['description'];
	$tags			= $safe_tags;
	$category_id	= $_POST['category'];
	$allow_replies	= $_POST['allow_replies'];
	$allow_rating	= $_POST['allow_rating'];
	$public_private	= $_POST['type'];

	$blog_story		= $_POST['FCKeditor1'];
	$blog_story		= @mysql_real_escape_string($blog_story);

	$title		= @mysql_real_escape_string($title);
	$description	= @mysql_real_escape_string($description);
	$tags			= @mysql_real_escape_string($tags);
	$category_id	= @mysql_real_escape_string($category_id);
	$allow_replies	= @mysql_real_escape_string($allow_replies);
	$allow_rating	= @mysql_real_escape_string($allow_rating);
	$public_private	= @mysql_real_escape_string($public_private);

	$blog_owner		= $_SESSION['user_name'];
	$viewtime		= '0000-00-00 00:00:00';
	$title_seo		= seo_title($title);

	// get category name
	$sql_c		= "SELECT category_id, category_name FROM blog_categories WHERE category_id = '$category_id'";
	$query_c		= @mysql_query($sql_c);

	while ( $result_c = @mysql_fetch_array($query_c) ) {
		$insert_category_id	= $result_c['category_id'];
		$insert_category_name	= $result_c['category_name'];
	}

	// enter new blog info into blog table
	$sql = "INSERT INTO blogs(blog_owner, user_id, viewtime, title, title_seo, description, tags, category, category_id, blog_story,
					  date_created, allow_replies, allow_ratings, rating_number_votes, rating_total_points,
					  updated_rating, public_private, approved, number_of_views, featured, promoted)
				 VALUES('$blog_owner', '$user_id', '$viewtime', '$title', '$title_seo', '$description', '$tags', '$insert_category_name',
				 	  '$insert_category_id', '$blog_story', NOW(), '$allow_replies', '$allow_rating', 0, 0, 0,'$public_private', 'yes', 0, 'no', 'no')";
	@mysql_query($sql);

	// update blog categories has_blogs
	$update_blogs_sql	= "UPDATE blog_categories SET has_blogs = 'yes' WHERE category_name = $insert_category_name";
	@mysql_query($update_blogs_sql);

	$new_sql		= "SELECT indexer, title_seo FROM blogs WHERE user_id = $user_id ORDER BY indexer DESC LIMIT 1";
	$new_query		= @mysql_query($new_sql);
	$row			= @mysql_fetch_array($new_query);
	$new_id		= $row['indexer'];
	$new_title		= $row['title_seo'];

	$message_type	= '<p align="center"><font color="#009900" size="3" face="Arial"><b>'.$blog_success.'</b></font><br />';

	$message		= "<a href=\"$base_url/read_blog/$new_id/$new_title\"><font color=\"#009900\" size=\"3\" face=\"Arial\"><b>Click here to view new blog</b></font></a></p>";

	$error_message 	= '<p align="center">' .$message_type. '&nbsp;&nbsp;' .$message.'</p>';

	$blog_errors 	= 1;
	$template 		= "themes/$user_theme/templates/main_1.htm";
    	$inner_template1 	= "themes/$user_theme/templates/inner_see_blogs.htm";
    	$TBS 			= new clsTinyButStrong;
    	$TBS->NoErr 	= true;

    	$TBS->LoadTemplate("$template");
    	$TBS->MergeBlock('blk1', $all_categories);

    	$TBS->Render 	= TBS_OUTPUT;
    	$TBS->Show();
	@mysql_close();
    	die();
}

if ( $action != '' ) {

	if ($_SESSION['user_id'] == "") {
		header("Location: $base_url/login.php");
		die();
	}

	if ( $action == 'write' ) {

		$result_featured	= '';

		// we populate create new blog category select box here
		$sql			= "SELECT category_id, category_name_seo, category_name FROM blog_categories ORDER BY category_id ASC";
		$query		= @mysql_query($sql);

		$fields_all 	= "";
		$sub_fields_all	= "";
		$show_fields	= "";

		while ( $result = @mysql_fetch_array($query) ) {
			$fields_all .= '<option value="'.$result['category_id'].'">'.$result['category_name'].'</option>';
		}

		// check admin limit settings
		$sql = "SELECT indexer FROM blogs WHERE user_id = '$user_id' AND approved = 'yes'";
		$member_blog_total = @mysql_num_rows(mysql_query($sql));

		if ( $member_blog_total == $config['blog_max_limits'] ) {
			$show_block = 3;

		} else {
			$member_blog_total++;
			$show_block = 2;
		}


		///FCK EDITOR________________________________________________________________________
		include('fckeditor/fckeditor.php');

		$sBasePath 						= "$base_url/fckeditor/";
		$oFCKeditor 					= new FCKeditor('FCKeditor1');
		$oFCKeditor->BasePath 				= $sBasePath;
		$oFCKeditor->CustomConfigurationsPath 	= "fckconfig.js";
		$oFCKeditor->ToolbarSet 			= 'Basic';
		$oFCKeditor->SkinPath 				= $sBasePath + 'editor/skins/silver/';
		$oFCKeditor->Value  				= "$blog_story";
		$oFCKeditor->Width  				= '700';
		$oFCKeditor->Height 				= '150';
		$FCKeditor1 					= $oFCKeditor->CreateHtml();
		$my_edit 						= $FCKeditor1;
		//____________________________________________________________________________________


	}

} else {

// get members own blogs---------------------------------------------------------------------------------------------------------
if ( $which_one == 'myblogs' ) {

     	define('blogs', true);

	if ($_SESSION['user_id'] == "") {
		header("Location: $base_url/login.php");
		die();
	}

	$pagination 	= pagination("SELECT indexer FROM blogs WHERE user_id=$user_id AND approved='yes' ORDER BY indexer DESC", $limit);
	$set_limit		= $pagination[0]['set_limit'];
	$total_pages	= $pagination[0]['total_pages'];
	$current_page	= $pagination[0]['current_page'];
	$total_records	= $pagination[0]['total_records'];
	$next_page		= $pagination[0]['next_page'];
	$prev_page		= $pagination[0]['prev_page'];
	$nl			= $pagination[0]['nl'];
	$pl			= $pagination[0]['pl'];

	$result_featured = array();
	$sql = "SELECT $which_query_rows FROM blogs WHERE user_id=$user_id AND approved='yes' ORDER BY indexer DESC LIMIT $set_limit, $limit";
	$query = @mysql_query($sql);

	$member_blog_total = 0;

	while ( $result1 = @mysql_fetch_array($query) ) {

		// change db time date to 2 days ago 12 mins ago etc
	  	$change_date			= dateTimeDiff($result1['date_created']);
	  	$result1['date_created'] 	= $change_date;
        	$blog_id 				= mysql_real_escape_string($result1['indexer']);

		// select any replies
	  	$sql2 = "SELECT indexer FROM blog_replys WHERE blog_id = $blog_id";
        	$query2 = @mysql_query($sql2);
        	$reply_number = @mysql_num_rows($query2);
        	$reply_array = array('replies' => $reply_number);

        	// get rating stars
        	$id = mysql_real_escape_string($result1['indexer']);
        	include ('stars_include.php');
        	$stars_array = array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

		// get blog member photo
        	$a1_sql = "SELECT file_name, approved FROM pictures WHERE user_id = $user_id";
        	$a1_result = @mysql_query($a1_sql);

        	if (@mysql_num_rows($a1_result) != 0) {
        		$a1_result = @mysql_fetch_array($a1_result);
			$a1_existing_file = $a1_result['file_name'];
			$a1_approved = $a1_result['approved'];

			if ($a1_approved == 'yes') {
				$poster_picture = "pictures/$a1_existing_file";
			} else {
				$poster_picture = "themes/$user_theme/images/placeholder.gif";
			}
		} else {
			$poster_picture = "themes/$user_theme/images/placeholder.gif";
		}

		$display_thumbs		= show_thumb( $poster_picture, $page_display_small_width );
		$new_smallwidth		= $display_thumbs[0];
		$new_smallheight		= $display_thumbs[1];
		$thumb_new_width_array	= array('thumb_new_width' => $new_smallwidth);
		$thumb_new_height_array	= array('thumb_new_height' => $new_smallheight);
		$poster_picture 		= array('poster_picture' => $poster_picture);

		$result2 = @array_merge($result1, $reply_array, $poster_picture, $stars_array, $thumb_new_width_array, $thumb_new_height_array);
      	$result_featured[] = $result2;

		$member_blog_total++;
    	}

	if ( empty($result_featured) ) {
    		$show_m = 0;
    	} else {
    		$show_v = 2;
    	}

	$see_more_title = 'My'; //$config['myblogs'];
    	$show_block = 1;

	//PAGINATION PLUS >> start  -- reusable code

    	$url = 'blogs/load';							//the url to be put in links - EDIT ME
    	$additional_url_variable = '/myblogs/';				//add addtions information that goes in query string here , e.g. '&load=groups&friends=all' - EDIT ME

    	@include_once ($include_base . '/includes/pagination.inc.php');
    	//PAGINATION PLUS >> end

} // end my blogs



//get most recent  -----------------------------------

if ( $which_one == 'recent' ) {

	//$pagination 	= pagination("SELECT indexer FROM blogs WHERE approved='yes' AND public_private = 'public' ORDER BY indexer DESC", $limit);
	$pagination 	= pagination("SELECT indexer FROM blogs WHERE approved='yes' ORDER BY indexer DESC", $limit);

	$set_limit		= $pagination[0]['set_limit'];
	$total_pages	= $pagination[0]['total_pages'];
	$current_page	= $pagination[0]['current_page'];
	$total_records	= $pagination[0]['total_records'];
	$next_page		= $pagination[0]['next_page'];
	$prev_page		= $pagination[0]['prev_page'];
	$nl			= $pagination[0]['nl'];
	$pl			= $pagination[0]['pl'];

	define('blogs', true);

	$result_featured = array();
	$sql = "SELECT $which_query_rows FROM blogs WHERE approved='yes' ORDER BY indexer DESC LIMIT $set_limit, $limit";
	$query = @mysql_query($sql);

	while ( $result1 = @mysql_fetch_array($query) ) {

    		$blog_owner		= mysql_real_escape_string($result1['blog_owner']);
    		$user_id		= mysql_real_escape_string($result1['user_id']);
        	$blog_id 		= mysql_real_escape_string($result1['indexer']);

        	// change db time date to 2 days ago 12 mins ago etc
	  	$change_date	= dateTimeDiff($result1['date_created']);
	  	$result1['date_created'] = $change_date;

	  	// select any replies to blog article IF any
	  	$sql2 = "SELECT indexer FROM blog_replys WHERE blog_id = $blog_id";
        	$query2 = @mysql_query($sql2);
        	$reply_number = @mysql_num_rows($query2);
        	$reply_array = array('replies' => $reply_number);

        	// get rating stars
        	$id = mysql_real_escape_string($result1['indexer']);
        	include ('stars_include.php');
        	$stars_array = array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

		// get blog member photo
        	$a1_sql = "SELECT file_name, approved FROM pictures WHERE user_id = $user_id";
        	$a1_result = @mysql_query($a1_sql);

        	if (@mysql_num_rows($a1_result) != 0) {
        		$a1_result = @mysql_fetch_array($a1_result);
			$a1_existing_file = $a1_result['file_name'];
			$a1_approved = $a1_result['approved'];

			if ($a1_approved == 'yes') {
				$poster_picture = "pictures/$a1_existing_file";
			} else {
				$poster_picture = "themes/$user_theme/images/placeholder.gif";
			}
		} else {
			$poster_picture = "themes/$user_theme/images/placeholder.gif";
		}

		$display_thumbs		= show_thumb( $poster_picture, $page_display_small_width );
		$new_smallwidth		= $display_thumbs[0];
		$new_smallheight		= $display_thumbs[1];
		$thumb_new_width_array	= array('thumb_new_width' => $new_smallwidth);
		$thumb_new_height_array	= array('thumb_new_height' => $new_smallheight);
		$poster_picture 		= array('poster_picture' => $poster_picture);

		$result2 = @array_merge($result1, $reply_array, $poster_picture, $stars_array, $thumb_new_width_array, $thumb_new_height_array);
      	$result_featured[] = $result2;
    	}

    	$see_more_title = $config['most_recent'];
    	$show_block = 1;

    	if ( empty($result_featured) ) {
    		$show_v = 1;
    	} else {
    		$show_v = 2;
    	}

    	//PAGINATION PLUS >> start  -- reusable code

    	$url = 'blogs/load';

    	$additional_url_variable = '/recent/';

    	include_once ($include_base . '/includes/pagination.inc.php');

    	//PAGINATION PLUS >> end
}

// end recent--------------------------------------------------------------------------------------------------------------------

// get featured----------------------------------------------------------------------------------------------------------------------

if ( $which_one == 'featured' ) {
	define('blogs', true);
	$pagination 	= pagination("SELECT indexer FROM blogs WHERE featured = 'yes' AND approved='yes' ORDER BY indexer DESC", $limit);
	$set_limit		= $pagination[0]['set_limit'];
	$total_pages	= $pagination[0]['total_pages'];
	$current_page	= $pagination[0]['current_page'];
	$total_records	= $pagination[0]['total_records'];
	$next_page		= $pagination[0]['next_page'];
	$prev_page		= $pagination[0]['prev_page'];
	$nl			= $pagination[0]['nl'];
	$pl			= $pagination[0]['pl'];

	$result_featured = array();
	$sql = "SELECT $which_query_rows FROM blogs WHERE featured = 'yes' AND approved='yes' ORDER BY indexer DESC LIMIT $set_limit, $limit";
	$query = @mysql_query($sql);

	while ( $result1 = @mysql_fetch_array($query) ) {

		$blog_owner		= mysql_real_escape_string($result1['blog_owner']);
		$user_id		= mysql_real_escape_string($result1['user_id']);
		$blog_id 		= mysql_real_escape_string($result1['indexer']);

		// change db time date to 2 days ago 12 mins ago etc
	  	$change_date	= dateTimeDiff($result1['date_created']);
	  	$result1['date_created'] = $change_date;

	  	// select any replies to blog article
	  	$sql2 = "SELECT indexer FROM blog_replys WHERE blog_id = $blog_id";
        	$query2 = @mysql_query($sql2);
        	$reply_number = @mysql_num_rows($query2);
        	$reply_array = array('replies' => $reply_number);

        	// get rating stars
        	$id = mysql_real_escape_string($result1['indexer']);
        	include ('stars_include.php');
        	$stars_array = array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

		// get blog member photo
		$a1_sql = "SELECT file_name, approved FROM pictures WHERE user_id = $user_id";
		$a1_result = @mysql_query($a1_sql);

		if (@mysql_num_rows($a1_result) != 0) {
        		$a1_result = @mysql_fetch_array($a1_result);
			$a1_existing_file = $a1_result['file_name'];
			$a1_approved = $a1_result['approved'];

			if ($a1_approved == 'yes') {
				$poster_picture = "pictures/$a1_existing_file";
			} else {
				$poster_picture = "themes/$user_theme/images/placeholder.gif";
			}
		} else {
			$poster_picture = "themes/$user_theme/images/placeholder.gif";
		}

		$display_thumbs		= show_thumb( $poster_picture, $page_display_small_width );
		$new_smallwidth		= $display_thumbs[0];
		$new_smallheight		= $display_thumbs[1];
		$thumb_new_width_array	= array('thumb_new_width' => $new_smallwidth);
		$thumb_new_height_array	= array('thumb_new_height' => $new_smallheight);
		$poster_picture 		= array('poster_picture' => $poster_picture);

		$result2 = @array_merge($result1, $reply_array, $poster_picture, $stars_array, $thumb_new_width_array, $thumb_new_height_array);
      	$result_featured[] = $result2;

    	}

    	$see_more_title = $config['featured'];
    	$show_block = 1;

	if ( empty($result_featured) ) {
    		$show_v = 1;
    	} else {
    		$show_v = 2;
    	}

	//PAGINATION PLUS >> start  -- reusable code

    	$url = 'blogs/load';							//the url to be put in links - EDIT ME
    	$additional_url_variable = '/featured/';				//add addtions information that goes in query string here , e.g. '&load=groups&friends=all' - EDIT ME

    	@include_once ($include_base . '/includes/pagination.inc.php');
    	//PAGINATION PLUS >> end
}

// end featured--------------------------------------------------------------------------------------------------------------------


// get most viewed----------------------------------------------------------------------------------------------------------------------

if ( $which_one == 'viewed' ) {
	define('blogs', true);
	$pagination 	= pagination("SELECT indexer FROM blogs WHERE approved='yes' ORDER BY number_of_views DESC", $limit);
	$set_limit		= $pagination[0]['set_limit'];
	$total_pages	= $pagination[0]['total_pages'];
	$current_page	= $pagination[0]['current_page'];
	$total_records	= $pagination[0]['total_records'];
	$next_page		= $pagination[0]['next_page'];
	$prev_page		= $pagination[0]['prev_page'];
	$nl			= $pagination[0]['nl'];
	$pl			= $pagination[0]['pl'];

	$result_featured = array();
	$sql = "SELECT $which_query_rows FROM blogs WHERE approved='yes' ORDER BY number_of_views DESC LIMIT $set_limit, $limit";
	$query = @mysql_query($sql);

	while ( $result1 = @mysql_fetch_array($query) ) {

		$blog_owner		= mysql_real_escape_string($result1['blog_owner']);
		$user_id		= mysql_real_escape_string($result1['user_id']);
		$blog_id 		= mysql_real_escape_string($result1['indexer']);

		// change db time date to 2 days ago 12 mins ago etc
	  	$change_date	= dateTimeDiff($result1['date_created']);
	  	$result1['date_created'] = $change_date;

	  	// select any replies to blog article
	  	$sql2 = "SELECT indexer FROM blog_replys WHERE blog_id = $blog_id";
        	$query2 = @mysql_query($sql2);
        	$reply_number = @mysql_num_rows($query2);
        	$reply_array = array('replies' => $reply_number);

        	// get rating stars
        	$id = mysql_real_escape_string($result1['indexer']);
        	include ('stars_include.php');
        	$stars_array = array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

		// get blog member photo
		$a1_sql = "SELECT file_name, approved FROM pictures WHERE user_id = $user_id";
		$a1_result = @mysql_query($a1_sql);

		if (@mysql_num_rows($a1_result) != 0) {
        		$a1_result = @mysql_fetch_array($a1_result);
			$a1_existing_file = $a1_result['file_name'];
			$a1_approved = $a1_result['approved'];

			if ($a1_approved == 'yes') {
				$poster_picture = "pictures/$a1_existing_file";
			} else {
				$poster_picture = "themes/$user_theme/images/placeholder.gif";
			}
		} else {
			$poster_picture = "themes/$user_theme/images/placeholder.gif";
		}

		$display_thumbs		= show_thumb( $poster_picture, $page_display_small_width );
		$new_smallwidth		= $display_thumbs[0];
		$new_smallheight		= $display_thumbs[1];
		$thumb_new_width_array	= array('thumb_new_width' => $new_smallwidth);
		$thumb_new_height_array	= array('thumb_new_height' => $new_smallheight);
		$poster_picture 		= array('poster_picture' => $poster_picture);

		$result2 = @array_merge($result1, $reply_array, $poster_picture, $stars_array, $thumb_new_width_array, $thumb_new_height_array);
      	$result_featured[] = $result2;
    	}

    	$see_more_title = $config['most_viewed'];
    	$show_block = 1;

	if ( empty($result_featured) ) {
    		$show_v = 1;
    	} else {
    		$show_v = 2;
    	}

	//PAGINATION PLUS >> start  -- reusable code

    	$url = 'blogs/load';							//the url to be put in links - EDIT ME
    	$additional_url_variable = '/viewed/';				//add addtions information that goes in query string here , e.g. '&load=groups&friends=all' - EDIT ME

    	@include_once ($include_base . '/includes/pagination.inc.php');
    	//PAGINATION PLUS >> end
}

// end views--------------------------------------------------------------------------------------------------------------------

if ($which_one == 'comments') {
	define('blogs', true);


	$pagination 	= pagination("SELECT indexer FROM blogs WHERE approved='yes' ORDER BY indexer DESC", $limit);
    	$set_limit 		= $pagination[0]['set_limit'];
    	$total_pages 	= $pagination[0]['total_pages'];
    	$current_page 	= $pagination[0]['current_page'];
    	$total_records 	= $pagination[0]['total_records'];
    	$next_page 		= $pagination[0]['next_page'];//use in html navigation (src)
    	$prev_page 		= $pagination[0]['prev_page'];//use in html navigation (src)
    	$nl 			= $pagination[0]['nl'];//use in html navigation: next>>
    	$pl 			= $pagination[0]['pl'];//use in html navigation: <<previous

    	$result_featured 	= array();

    	$browse_blogs = array();

    	$sql = "SELECT $which_query_rows FROM blogs WHERE approved='yes' ORDER BY indexer DESC"; // LIMIT $limit";
    	$query = @mysql_query($sql);

    	$blog_limit_count 	= @mysql_num_rows($query);

    	while ( $result1 = @mysql_fetch_array($query) ) {

		$blog_owner		= mysql_real_escape_string($result1['blog_owner']);
		$user_id		= mysql_real_escape_string($result1['user_id']);
		$blog_id 		= mysql_real_escape_string($result1['indexer']);

		// change db time date to 2 days ago 12 mins ago etc
	  	$change_date	= dateTimeDiff($result1['date_created']);
	  	$result1['date_created'] = $change_date;

	  	// select any replies to blog article
	  	$sql2 = "SELECT indexer FROM blog_replys WHERE blog_id = $blog_id";
        	$query2 = @mysql_query($sql2);
        	$reply_number = @mysql_num_rows($query2);
        	$reply_array = array('replies' => $reply_number);


		// get rating stars
        	$id = mysql_real_escape_string($result1['indexer']);
        	include ('stars_include.php');
        	$stars_array = array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

		// get blog member photo
        	$a1_sql = "SELECT file_name, approved FROM pictures WHERE user_id = $user_id";
        	$a1_result = @mysql_query($a1_sql);

        	if (@mysql_num_rows($a1_result) != 0) {
        		$a1_result = @mysql_fetch_array($a1_result);
			$a1_existing_file = $a1_result['file_name'];
			$a1_approved = $a1_result['approved'];

			if ($a1_approved == 'yes') {
				$poster_picture = "pictures/$a1_existing_file";
			} else {
				$poster_picture = "themes/$user_theme/images/placeholder.gif";
			}
		} else {
			$poster_picture = "themes/$user_theme/images/placeholder.gif";
		}

		$display_thumbs		= show_thumb( $poster_picture, $page_display_small_width );
		$new_smallwidth		= $display_thumbs[0];
		$new_smallheight		= $display_thumbs[1];
		$thumb_new_width_array	= array('thumb_new_width' => $new_smallwidth);
		$thumb_new_height_array	= array('thumb_new_height' => $new_smallheight);
		$poster_picture 		= array('poster_picture' => $poster_picture);

		$result2 = @array_merge($result1, $reply_array, $poster_picture, $stars_array, $thumb_new_width_array, $thumb_new_height_array);
      	$result_featured[] = $result2;
      }

      //sort the final array by order of number of comments
    	function arr_keys_multisort($arr, $my_key, $sort_type) {

    		foreach ($arr as $key => $row) {
            	$arr_tmp[$key] = $row["$my_key"];
		}
	     	if ($sort_type == 'desc')
            	@array_multisort($arr_tmp, SORT_DESC, $arr);
        	else
            	@array_multisort($arr_tmp, SORT_ASC, $arr);

        	return $arr;
    	}

      // return only config limit
      $result_featured_all = arr_keys_multisort($result_featured, 'replies', 'desc');

     	$limit_result_featured = array();

     	if ( sizeof($result_featured) < $limit ) $limit = sizeof($result_featured);

	$show_count = $limit + $set_limit;

	if ( $show_count > $blog_limit_count ) $show_count = $blog_limit_count;

	for ( $x = $set_limit; $x < $show_count; $x++ ) {
		$limit_result_featured[$x] = $result_featured_all[$x];
	}

    	$result_featured = $limit_result_featured;

	$see_more_title = $config['most_commented'];
    	$show_block = 1;

	if ( empty($result_featured) ) {
    		$show_v = 1;
    	} else {
    		$show_v = 2;
    	}

    	//PAGINATION PLUS >> start  -- reusable code

    	$url = 'blogs/load';							//the url to be put in links - EDIT ME
    	$additional_url_variable = '/comments/';				//add addtions information that goes in query string here , e.g. '&load=groups&friends=all' - EDIT ME

    	@include_once ($include_base . '/includes/pagination.inc.php');
    	//PAGINATION PLUS >> end

}

// end most comments / replies-----------------------------------------------------------------------------------------------------------

// get blog categories-------------------------------------------------------------------------------------------------------------------

if ( $blog_category > 0 ) {
	$pagination 	= pagination("SELECT indexer FROM blogs WHERE category_id = '$blog_category' AND approved='yes' ORDER BY indexer DESC", $limit);
	$set_limit		= $pagination[0]['set_limit'];
	$total_pages	= $pagination[0]['total_pages'];
	$current_page	= $pagination[0]['current_page'];
	$total_records	= $pagination[0]['total_records'];
	$next_page		= $pagination[0]['next_page'];
	$prev_page		= $pagination[0]['prev_page'];
	$nl			= $pagination[0]['nl'];
	$pl			= $pagination[0]['pl'];

	$result_featured = array();
	$sql = "SELECT $which_query_rows FROM blogs WHERE category_id = '$blog_category' AND approved='yes' ORDER BY indexer DESC LIMIT $set_limit, $limit";
	$query = @mysql_query($sql);

	while ( $result1 = @mysql_fetch_array($query) ) {

    		$blog_owner		= mysql_real_escape_string($result1['blog_owner']);
    		$user_id		= mysql_real_escape_string($result1['user_id']);
        	$blog_id 		= mysql_real_escape_string($result1['indexer']);
        	$pagi_link		= mysql_real_escape_string($result1['category']);

        	// change db time date to 2 days ago 12 mins ago etc
	  	$change_date	= dateTimeDiff($result1['date_created']);
	  	$result1['date_created'] = $change_date;

	  	// select any replies to blog article IF any
	  	$sql2 = "SELECT indexer FROM blog_replys WHERE blog_id = $blog_id";
        	$query2 = @mysql_query($sql2);
        	$reply_number = @mysql_num_rows($query2);
        	$reply_array = array('replies' => $reply_number);

        	// get rating stars
        	$id = mysql_real_escape_string($result1['indexer']);
        	include ('stars_include.php');
        	$stars_array = array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

		// get blog member photo
        	$a1_sql = "SELECT file_name, approved FROM pictures WHERE user_id = $user_id";
        	$a1_result = @mysql_query($a1_sql);

        	if (@mysql_num_rows($a1_result) != 0) {
        		$a1_result = @mysql_fetch_array($a1_result);
			$a1_existing_file = $a1_result['file_name'];
			$a1_approved = $a1_result['approved'];

			if ($a1_approved == 'yes') {
				$poster_picture = "pictures/$a1_existing_file";
			} else {
				$poster_picture = "themes/$user_theme/images/placeholder.gif";
			}
		} else {
			$poster_picture = "themes/$user_theme/images/placeholder.gif";
		}

		$display_thumbs		= show_thumb( $poster_picture, $page_display_small_width );
		$new_smallwidth		= $display_thumbs[0];
		$new_smallheight		= $display_thumbs[1];
		$thumb_new_width_array	= array('thumb_new_width' => $new_smallwidth);
		$thumb_new_height_array	= array('thumb_new_height' => $new_smallheight);
		$poster_picture 		= array('poster_picture' => $poster_picture);

		$result2 = @array_merge($result1, $reply_array, $poster_picture, $stars_array, $thumb_new_width_array, $thumb_new_height_array);
      	$result_featured[] = $result2;
    	}

    	$see_more_title = $pagi_link;
    	$show_block = 1;

    	if ( empty($result_featured) ) {
    		$show_v = 1;
    	} else {
    		$show_v = 2;
    	}

    	//PAGINATION PLUS >> start  -- reusable code

    	$url = 'blogs/category/'.$blog_category;

    	$additional_url_variable = '/'.$pagi_link.'/';

    	@include_once ($include_base . '/includes/pagination.inc.php');

    	//PAGINATION PLUS >> end
}

// blog categories end----------------------------------------------------------------------------------------------------------------------------

} // end action

//show any errors/notifications
if ($codes == "") $codes = $codes_internal;

$error_code = errorcodes($codes);

if (!empty($error_code)) {
	//$blk_notification	= $error_code['error_display'];
    	$blk_notification = 1;
    	$message_type 	= $error_code['error_type'];
    	$error_message 	= $error_code['error_message'];


    	// display form with error message

	$message_type	= $lang_error;
    	$blk_notification = 1;

    	$template 		= "themes/$user_theme/templates/main_1.htm";
    	$inner_template1 	= "themes/$user_theme/templates/inner_see_blogs.htm";
    	$TBS 			= new clsTinyButStrong;
    	$TBS->NoErr 	= true;

    	$TBS->LoadTemplate("$template");

    	$TBS->Render 	= TBS_OUTPUT;
    	$TBS->Show();

    	@mysql_close();
    	die();
}


$template 		= "themes/$user_theme/templates/main_1.htm";
$inner_template1 	= "themes/$user_theme/templates/inner_see_blogs.htm";
$TBS 			= new clsTinyButStrong;
$TBS->NoErr 	= true;

$TBS->LoadTemplate("$template");

$TBS->MergeBlock('blkfeatured', $result_featured);
$TBS->MergeBlock('blk1', $all_categories);

$TBS->Render 	= TBS_OUTPUT;

$TBS->Show();
@mysql_close();
die();

?>