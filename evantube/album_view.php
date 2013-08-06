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
include_once ('online.php');

// load required javascripts used in main_1.htm
$rating_update	= 1;
$ahah			= 1;
$thickbox		= 1;
$greybox		= 1;
$load_ajax 		= 1;
$load_carousel	= 1;

$progress_bar_width	= 0;
$blk_notification 	= '';
$small_block_background = $config['color_tellafriend'];
$load_ajax 			= 1;
$wrap_limit 		= 80;
$codes			= mysql_real_escape_string($_GET['code']);
$proceed			= true;
$album			= (int) mysql_real_escape_string($_GET['album']);
$image_id			= (int) mysql_real_escape_string($_GET['image']);

//if($album == '' &&  $image_id =='') header('Location: '.$base_url);

//________________________________________________________________________________
//__Enabled/Disabled Features ++ also Private/Public Check________________________
include_once ('includes/enabled_features.php');

///////////////////////////////////////////////////// view album called /////////////////////////////////////////////////////////////////////
// album called
if ( $album ) {

	$greybox_sql	= "SELECT * FROM images WHERE gallery_id = $album AND approved ='yes' ORDER BY RAND()";
	$greybox_query	= @mysql_query($greybox_sql);
	$result_loader	= array();

	// load image array for full size greybox view
	while ($greybox_result = @mysql_fetch_array($greybox_query)) {

		$album_member_id			= $greybox_result['user_id'];
		$show_title[]			= $greybox_result['title'];
		$title_seo[]			= $greybox_result['title_seo'];
		$full_view				= $greybox_result['image_id'];
		$img_id[]				= $greybox_result['indexer'];
		$full_url				= $config['site_base_url'] . '/addons/albums/images/'.$full_view;
		$images_loader[]			= $full_url;

		$thunbs_full_url			= $config['site_base_url'] . '/addons/albums/thumbs/'.$full_view;
		$thumbs_loader[]			= $thunbs_full_url;

		list($width, $height)		= getimagesize( "addons/albums/images/$full_view" );
		$page_display_small_width	= 100;
		$page_display_small_height	= 100;

		if ( $width < $page_display_small_width && $height < $page_display_small_height ) {
			$new_smallwidth	= $width;
     			$new_smallheight	= $height;
		}

		if ( $width > $page_display_small_width ) {
			$large_img_ratio	= $width/$height;

     			if ( $large_img_ratio > 1 ) {
           			$new_smallwidth	= $page_display_small_width;
            		$new_smallheight	= $page_display_small_width / $large_img_ratio;
            	} else {
            		$new_smallheight	= $page_display_small_width;
            		$new_smallwidth	= $page_display_small_width * $large_img_ratio;
			}
		}

		$new_smallheight		= floor($new_smallheight);
		$thumb_new_width_array	= array('thumb_new_width' => $new_smallwidth);
		$thumb_new_height_array	= array('thumb_new_height' => $new_smallheight);

		// get rating stars
		define('images', true);
      	$id 				= mysql_real_escape_string($greybox_result['indexer']);
      	include ('stars_include.php');
      	$stars_array 		= array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

		$result_previews		= @array_merge( $greybox_result, $stars_array, $thumb_new_width_array, $thumb_new_height_array );
		$result_loader[]		= $result_previews;
	}

	$album_load_images	= "";
	$ajax_load			= "";
	$load_images 		= sizeof($images_loader);

	for ( $x=0; $x < $load_images; $x++ ) {
		$album_load_images .= "{'caption': '$show_title[$x]', 'url': '$images_loader[$x]'},";

		if ( $x == ($load_images - 1) ) {
			$ajax_load_last .= "{url: \"$thumbs_loader[$x]\", title: \"$img_id[$x]/$title_seo[$x]\"} ";
		} else {
			$ajax_load_cont .= "{url: \"$thumbs_loader[$x]\", title: \"$img_id[$x]/$title_seo[$x]\"}, ";
		}
	}

	$ajax_load = $ajax_load_cont . $ajax_load_last;

	// update album viewtime
	$update_album_sql	= "UPDATE image_galleries SET viewtime = NOW() WHERE gallery_id = $album";
	$query_album	= @mysql_query($update_album_sql);

	// update album number of views
	$views_sql		= "SELECT number_of_views FROM image_galleries WHERE has_images = '1' AND gallery_id = $album";
	$views_query	= @mysql_query($views_sql);
	$views_result	= @mysql_fetch_array($views_query);
	$number_of_views	= (int) mysql_real_escape_string($views_result['number_of_views']);
	$views_add		= $number_of_views + 1;
	$sql_update		= "UPDATE image_galleries SET number_of_views = $views_add WHERE gallery_id = $album";
	$query_upadte	= @mysql_query($sql_update);

	// page display query
	$sql = "SELECT * FROM images WHERE gallery_id = $album AND approved ='yes' LIMIT 1";

	// get other albums from same user
	($show_private_content)? $sql_public_private = '':$sql_public_private = "AND public_private = 'public'"; //from /includes/enabled_features.php
	$sql_other		= "SELECT * FROM image_galleries WHERE has_images = '1' AND user_id = '$album_member_id' AND gallery_id != '$album' AND approved ='yes' $sql_public_private ORDER BY gallery_id";
	$query_other	= @mysql_query($sql_other);
	$other_albums	= array();

	$other_display_small_width	= 80;
	$other_display_small_height	= 80;

	while ($result_other = @mysql_fetch_array($query_other)) {

		$album_cover_img		= $result_other['gallery_picture'];

		if ( $album_cover_img != 'none.gif' ) {

			list($width, $height)	= getimagesize( "addons/albums/images/$album_cover_img" );
		} else {

			list($width, $height)	= getimagesize( "addons/albums/thumbs/none.gif" );

		}

		if ( $width < $other_display_small_width && $height < $other_display_small_height ) {
			$new_smallwidth	= $width;
     			$new_smallheight	= $height;
		}

		if ( $width > $other_display_small_width ) {
			$large_img_ratio	= $width/$height;

     			if ( $large_img_ratio > 1 ) {
           			$new_smallwidth	= $other_display_small_width;
            		$new_smallheight	= $other_display_small_width / $large_img_ratio;
            	} else {
            		$new_smallheight	= $other_display_small_width;
            		$new_smallwidth	= $other_display_small_width * $large_img_ratio;
			}
		}

		$new_smallheight		= floor($new_smallheight);
		$other_new_width_array	= array('other_new_width' => $new_smallwidth);
		$other_new_height_array	= array('other_new_height' => $new_smallheight);

		$other_previews		= @array_merge( $result_other, $other_new_width_array, $other_new_height_array );
		$other_albums[] 		= $other_previews;
	}
}

//////////////////////////////////////////////////// end album //////////////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////// view image called ///////////////////////////////////////////////////////////////////////

// image called e.g index.htm or index_ajax.htm
if ( $image_id ) {

	// if image called called GET ALBUM ID
	$album_sql		= "SELECT gallery_id FROM images WHERE indexer = $image_id AND approved ='yes'";
	$album_query	= @mysql_query($album_sql);
	$album_result 	= @mysql_fetch_array($album_query);
	$image_gallery_id = $album_result['gallery_id'];

	$greybox_sql	= "SELECT * FROM images WHERE gallery_id = $image_gallery_id AND approved ='yes'";
	$greybox_query	= @mysql_query($greybox_sql);
	$result_loader	= array();

	// Is Active / Found
     	(mysql_num_rows($album_query) > 0) ? null:generic_error($config['error_11']);


	// load image array for full size greybox view
	while ($greybox_result = @mysql_fetch_array($greybox_query)) {

		$album_member_id	= $greybox_result['user_id'];
		$show_title[]	= $greybox_result['title'];
		$title_seo[]	= $greybox_result['title_seo'];
		$full_view		= $greybox_result['image_id'];
		$img_id[]		= $greybox_result['indexer'];
		$full_url		= $config['site_base_url'] . '/addons/albums/images/'.$full_view;
		$album_images[]	= $full_url;

		$thunbs_full_url			= $config['site_base_url'] . '/addons/albums/thumbs/'.$full_view;
		$thumbs_loader[]			= $thunbs_full_url;

		list($width, $height)		= getimagesize( "addons/albums/images/$full_view" );
		$page_display_small_width	= 100;
		$page_display_small_height	= 100;

		if ( $width < $page_display_small_width && $height < $page_display_small_height ) {
			$new_smallwidth	= $width;
     			$new_smallheight	= $height;
		}

		if ( $width > $page_display_small_width ) {
			$large_img_ratio	= $width/$height;

     			if ( $large_img_ratio > 1 ) {
           			$new_smallwidth	= $page_display_small_width;
            		$new_smallheight	= $page_display_small_width / $large_img_ratio;
            	} else {
            		$new_smallheight	= $page_display_small_width;
            		$new_smallwidth	= $page_display_small_width * $large_img_ratio;
			}
		}

		$new_smallheight		= floor($new_smallheight);
		$thumb_new_width_array	= array('thumb_new_width' => $new_smallwidth);
		$thumb_new_height_array	= array('thumb_new_height' => $new_smallheight);

		// get rating stars
		define('images', true);
      	$id 				= mysql_real_escape_string($greybox_result['indexer']);
      	include ('stars_include.php');
      	$stars_array 		= array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

		$result_previews		= @array_merge( $greybox_result, $stars_array, $thumb_new_width_array, $thumb_new_height_array );
		$result_loader[]		= $result_previews;
	}

	$album_load_images	= "";
	$load_images 		= sizeof($album_images);

	for ( $x=0; $x < $load_images; $x++ ) {
		$album_load_images .= "{'caption': '$show_title[$x]', 'url': '$album_images[$x]'},";
		$ajax_load .= "{url: \"$thumbs_loader[$x]\", title: \"$img_id[$x]/$title_seo[$x]\"},";
	}

	// update image viewtime
	$update_image_sql	= "UPDATE images SET viewtime = NOW() WHERE indexer = $image_id";
	$query_image	= @mysql_query($update_image_sql);

	// update image number of views
	$views_sql		= "SELECT number_of_views FROM images WHERE indexer = $image_id";
	$views_query	= @mysql_query($views_sql);
	$views_result	= @mysql_fetch_array($views_query);
	$number_of_views	= (int) mysql_real_escape_string($views_result['number_of_views']);
	$views_add		= $number_of_views + 1;
	$sql_update		= "UPDATE images SET number_of_views = $views_add WHERE indexer = $image_id";
	$query_upadte	= @mysql_query($sql_update);

	// page display query
	$sql = "SELECT * FROM images WHERE indexer = $image_id AND approved ='yes' ORDER BY RAND()";


	// get other albums from same user (public/private as set by /inc/enabled_features.php
	($show_private_content)? $sql_public_private = '':$sql_public_private = "AND public_private = 'public'"; //from /includes/enabled_features.php
	$sql_other		= "SELECT * FROM image_galleries WHERE has_images = '1' AND user_id = '$album_member_id' AND gallery_id != '$image_gallery_id' AND approved ='yes' $sql_public_private ORDER BY gallery_id";
	$query_other	= @mysql_query($sql_other);
	$other_albums	= array();

	$other_display_small_width	= 80;
	$other_display_small_height	= 80;

	while ($result_other = @mysql_fetch_array($query_other)) {

		$album_cover_img		= $result_other['gallery_picture'];

		if ( $album_cover_img != 'none.gif' ) {

			list($width, $height)	= getimagesize( "addons/albums/images/$album_cover_img" );
		} else {

			list($width, $height)	= getimagesize( "addons/albums/thumbs/none.gif" );

		}

		if ( $width < $other_display_small_width && $height < $other_display_small_height ) {
			$new_smallwidth	= $width;
     			$new_smallheight	= $height;
		}

		if ( $width > $other_display_small_width ) {
			$large_img_ratio	= $width/$height;

     			if ( $large_img_ratio > 1 ) {
           			$new_smallwidth	= $other_display_small_width;
            		$new_smallheight	= $other_display_small_width / $large_img_ratio;
            	} else {
            		$new_smallheight	= $other_display_small_width;
            		$new_smallwidth	= $other_display_small_width * $large_img_ratio;
			}
		}

		$new_smallheight		= floor($new_smallheight);
		$other_new_width_array	= array('other_new_width' => $new_smallwidth);
		$other_new_height_array	= array('other_new_height' => $new_smallheight);

		$other_previews		= @array_merge( $result_other, $other_new_width_array, $other_new_height_array );
		$other_albums[] 		= $other_previews;
	}


}


/////////////////////////////////////////////////// end image ////////////////////////////////////////////////////////////////////////////////

// show page with album called or page with image called

$query		= @mysql_query($sql);
$result		= @mysql_fetch_array($query);
$active		= $result['approved'];
$public_private	= $result['public_private'];
$allow_embedding 	= $result['allow_embedding'];
$member_id 		= $result['user_id'];

if ( !empty($result) ) {
	$showdetails = 1;
}

if ( $active != "yes" || empty($result) ) {
	$codes_internal = 111;
	$proceed = false;
}

// check if embedding is allowed
if ( $allow_embedding == 'no' ) {
	$show_embed = 0;
} else {
	$show_embed = 1;
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// if all checks out proceed.
if ($proceed == true) {

	$title			= $result['title'];
	$title_seo			= $result['title_seo'];
	$album_name			= $result['gallery_name'];

	$description 		= $result['description'];
   	$tags 			= $result['tags'];

   	$title 			= safe_word_wrap($title);
	$title_seo 			= safe_word_wrap($title_seo);
    	$description 		= safe_word_wrap($description);
   	$tags				= safe_word_wrap($tags);

   	$album_name			= $result['gallery_name'];
	$date_uploaded		= $result['date_uploaded'];
	$allow_comments		= $result['allow_comments'];
	$allow_embedding		= $result['allow_embedding'];
	$public_private		= $result['public_private'];
	$number_of_views		= mysql_real_escape_string($result['number_of_views']);
	$image_id			= $result['indexer'];
	$image_view			= $result['image_id'];
	$member_id			= $result['user_id'];
	$social_image_url		= $config['site_base_url'] . "/view-image/" . $image_id . "/" . $title_seo;

	$page_title			= $title;

	$media = 'images';
	if ($user_id == "") {
		$ajax_rating = pullRating($media,$image_id,false,true,true,'novote');
	} else {
		$ajax_rating = pullRating($media,$image_id,true,false,true,$user_id);
	}

	list($width, $height)	= getimagesize( "addons/albums/images/$image_view" );
	$page_display_width	= 400;
	$page_display_height	= 386; //308;

	if ( $width <= $page_display_width && $height <= $page_display_height ) {
		$new_width	= $width;
     		$new_height	= $height;
	}

	if ( $width > $page_display_width ) {
		$large_img_ratio	= $width/$height;

     		if ( $large_img_ratio > 1 ) {
           		$new_width	= $page_display_width;
            	$new_height	= $page_display_width / $large_img_ratio;
            } else {
            	$new_height	= $page_display_width;
            	$new_width	= $page_display_width * $large_img_ratio;
		}
	}

	if ( $height > $page_display_height ) {
		$large_img_ratio	= $width/$height;

     		if ( $large_img_ratio > 1 ) {
           		$new_width	= $page_display_width;
            	$new_height	= $page_display_width / $large_img_ratio;
            } else {
            	$new_height	= $page_display_width;
            	$new_width	= $page_display_width * $large_img_ratio;
		}
	}

	//$new_height	= floor($new_height);
	$new_height		= (int) $new_height;
	$new_width		= (int) $new_width;

	$image_url		= $config['site_base_url'] . '/addons/albums/images/'.$image_view;
	$album_images[]	= $image_url;

    	//get members personal information
    	$sql			= "SELECT user_name, last_seen, date_created FROM member_profile WHERE user_id = $member_id";
    	$query		= @mysql_query($sql);
    	$result 		= @mysql_fetch_array($query);
    	$member_username 	= $result['user_name'];

    	// last seen / active
    	$db_date		= $result['last_seen'];
    	if ( $db_date == '0000-00-00 00:00:00' ) $db_date = $result['date_created'];
    	$change_date	= dateTimeDiff($db_date);
    	$last_seen		= $change_date;

    	//record view time in views_tracker table
      @media_views_tracker($image_id, 'images');

    	//______PERMISSIONS CHECK AUDIO__________________________________________________________________________
    	$usercheck 		= new LoadPermissions('',$image_id,'images');  //($user_id, $content_id, $content_type);
    	$usercheck->CheckPermissions();
    	$edit_image 	= $usercheck->ReturnVars('edit_on');
    	$delete_image 	= $usercheck->ReturnVars('delete_on');
    	//________________________________________________________________________________________________________

    	if ( $tags != "" ) {
		$tags_exploded = explode(" ", $tags);
    		$i = 0;
    		while (isset($tags_exploded[$i])):
        		$tags_each = '<a href="search.php?type=images&keyword='.$tags_exploded[$i].'">'.$tags_exploded[$i].'</a>&nbsp;&nbsp;';
        		$tags_string = $tags_string . $tags_each;
        		$i++;
    		endwhile;
	}

	// V3 comments and replies
	///////////////////////////////////////////////////////////////////////////
	//we need a total of text comments before LIMIT query!!
	$text_comments = 0;
	$sql_count = "SELECT * FROM imagecomments WHERE image_id = $image_id";
	$num_query = @mysql_query($sql_count);
	$text_comments = mysql_num_rows($num_query);
	///////////////////////////////////////////////////////////////////////////

	// ajax pagination reuseable
	if(empty($page)) {
		$page = 1;
	}

	$limit = $config["comment_page_limits"];
	$limit_value = $page * $limit - ($limit);
	$totalrows = $text_comments;
	$config['ajax_page_previous'] = $config["pagination_previous"];
	$config['ajax_page_next'] = $config["pagination_next"];

	////////////////////////////////////////////////////////////////////////
	// PREVIOUS
	if($page != 1){
		$pageprev = ($page - 1);
		$ajax_page_previous = $config['ajax_page_previous'];
	}else{
		// no previous link
		$ajax_page_previous = '';
	}

	// NEXT
	$next_page = $totalrows - ($limit * $page);
	if(($totalrows - ($limit * $page)) > 0){
		$pagenext = ($page + 1);
		$ajax_page_next = "&nbsp;&nbsp;" . $config['ajax_page_next'];
		$refresh = 1;
	}else{
		// no link
		$ajax_page_next = '';
		$refresh = 0;
	}
	///////////////////////////////////////////////////////////////////////

	$result_search3 = array();
	$each_comment = array();

	$sql = "SELECT * FROM imagecomments WHERE image_id = $image_id ORDER BY indexer DESC LIMIT $limit_value, $limit";
	$query = @mysql_query($sql);

	$see_limit = 0;

	//create new array with "wrapped" comments
	while ($result1 = @mysql_fetch_array($query)) {
		$see_limit++;

		//get comentors picture (added 01-01-2008)
		$poster_id = $result1['by_id'];
		$a1_sql = "SELECT * FROM pictures WHERE user_id = $poster_id";
		$a1_result = @mysql_query($a1_sql);

		if (@mysql_num_rows($a1_result) != 0) {
			$a1_result = @mysql_fetch_array($a1_result);
			$a1_existing_file = $a1_result['file_name'];
			$a1_approved = $a1_result['approved'];

			if ($a1_approved == "yes") {
				// show picture and "change picture link"
				$poster_picture = $config['site_base_url'] . '/pictures/' . $a1_existing_file;
			} else {
				// show place holder or blank (default is blank)- uncomment or comment the option you want
				$poster_picture = $config['site_base_url'] . "/themes/$user_theme/images/placeholder.gif";
			}
		} else {
			// show place holder or blank (default is blank)- uncomment or comment the option you want
			$poster_picture = $config['site_base_url'] . "/themes/$user_theme/images/placeholder.gif";
		}

		//$text = $result1['comments'];
		//$wrap = wordwrap($text, $wrap_limit, " ", true);
		//$wrap = htmlspecialchars_decode($wrap);

		$text = $result1['comments'];
		$wrap = htmlspecialchars_decode($text);


		$indexer_id = $result1["indexer"];
		$drop_block = "<div id=\"drop_block_$indexer_id\"></div>";

        //______________________________________________________________________________________________
		//______PERMISSIONS CHECK COMMENTS______________________________________________________________
    	$usercheck = new LoadPermissions('', $indexer_id, 'imagecomments');
    	$usercheck->CheckPermissions();
    	$image_comment_edit_on = $usercheck->ReturnVars('edit_on');
    	$image_comment_delete_on = $usercheck->ReturnVars('delete_on');

    	//Edit link
    	if($image_comment_edit_on == 1){
    	$image_comment_edit_on = '<a href="edit/editcomments.php?type=6&id='.$indexer_id.'" title="Edit" rel="gb_page_center[800, 400]">
        <img src="'.$base_url.'/themes/'.$user_theme.'/images/icons/edit_small.png" alt="'.$lang_edit.'" width="14" height="14" border="0" />
         '.$lang_edit.'</a>';
    	}else{
    	$image_comment_edit_on = '';
    	}

    	//Delet link
    	if($image_comment_delete_on ==1){
    	$image_comment_delete_on = '<a href="edit/delete.php?type=7&id='.$indexer_id.'" title="Delete" rel="gb_page_center[800, 300]">
        <img src="'.$base_url.'/themes/'.$user_theme.'/images/icons/delete_small.png" alt="'.$lang_delete_general.'" width="14" height="14"
		border="0" /> '.$lang_delete_general.'</a>';
    	}else{
    	$image_comment_delete_on = '';
    	}

    	//_______________________________________________________________________________________________

		//print_r($comment_permissions);

		// reset html block to nothing
		$comment_actions_block = "";

		// query imagecomments_replys to find any REPLIES
		$each_reply = array();
		$sql_replies = "SELECT * FROM imagecomments_replys WHERE imagecomment_id = $indexer_id ORDER BY indexer DESC LIMIT $limit_value, $limit";
		$replies = @mysql_query($sql_replies);
		$replies_count = mysql_num_rows($replies);
		$reply_wrap = array();
		$by_username = array();
		$reply_date = array();

		$reply_block = "";

		while ($result_replies = @mysql_fetch_array($replies)) {
			$reply_text 	= $result_replies['comment_reply'];
			$reply_text		= htmlspecialchars_decode($reply_text);
			$reply_wrap 	= wordwrap($reply_text, $wrap_limit, " ", true);
			$by_username 	= $result_replies['by_username'];
			$db_date		= $result_replies['todays_date'];
			$reply_date		= date($config["date_format"], strtotime($db_date));
			$reply_id		= $result_replies['indexer'];


        //______________________________________________________________________________________________
		//______PERMISSIONS CHECK COMMENTS______________________________________________________________
    	$usercheck = new LoadPermissions('', $reply_id, 'imagecomments_replys');
    	$usercheck->CheckPermissions();
    	$reply_edit_on = $usercheck->ReturnVars('edit_on');
    	$reply_delete_on = $usercheck->ReturnVars('delete_on');

		if ( $reply_delete_on == 1 ) {
			$delete_reply_on = "<div style=\"margin-right:12px; float:right;\">
			  <a href=\"edit/delete.php?type=16&id=$reply_id\" title=\"$lang_delete_general\" rel=\"gb_page_center[800, 400]\">
              <img src=\"$base_url/themes/$user_theme/images/icons/delete_small.png\" alt=\"$lang_delete_general\" width=\"14\" height=\"14\" border=\"0\" />
			  $lang_delete_general</a>
			</div>\n";
		} else {
			$delete_reply_on = "<div style=\"margin-right:12px; float:right;\">&nbsp;</div>\n";;
		}

		if ( $reply_edit_on == 1 ) {
			$edit_reply_on = "<div style=\"margin-right:12px; float:right;\">
			  <a href=\"edit/editcomments.php?type=7&id=$reply_id\" title=\"$lang_edit\" rel=\"gb_page_center[800, 400]\">
              <img src=\"$base_url/themes/$user_theme/images/icons/edit_small.png\" alt=\"$lang_delete_general\" width=\"14\" height=\"14\" border=\"0\" />
			  $lang_edit</a>
			</div>\n";
		} else {
			$edit_reply_on = "<div style=\"margin-right:12px; float:right;\">&nbsp;</div>\n";
		}
        //_______________________________________________________________________________________________________________


			$reply_block .= "<hr>
			<div style=\"width: 100%; background-color: $small_block_background;\" id=\"reply_block\">
			  <div>
			    <div style=\"margin-left:12px; float:left;\">$lang_reply_by:&nbsp;<b>$by_username</b></div>
			    <div style=\"margin-left:42px; float:left;\"><b>$lang_date:&nbsp;</b>$reply_date</div>\n";
			  //</div>\n";
			  //</div>\n";

			$reply_block .= $delete_reply_on . $edit_reply_on;

			$reply_block .= "</div></div>\n";

			$reply_block .="<div style=\" background-color: $small_block_background;\"><!-- end div here //-->
		  	  <br>
		  	  <br>
		  	  <div style=\"margin-left:12px; float:left;\">$reply_wrap</div>
		  	  <br>
		  	  <br>
			</div>\n";
		}

		$each_comment = array('by_id'				=>$result1['by_id'],
					    'indexer'			=>$result1['indexer'],
					    'comments'			=>$wrap,
					    'todays_date'			=>$result1['todays_date'],
					    'by_username'			=>$result1['by_username'],
					    'poster_picture'		=>$poster_picture,
					    'rating'			=>$result1['updated_rating'],
					    'image_comment_edit'	=>$image_comment_edit_on,
					    'image_comment_delete'	=>$image_comment_delete_on,
					    'drop_block'			=>$drop_block,
					    'reply_block'			=>$reply_block);


		$result_search3[] = $each_comment;

	} // end comments while
	//print_r($result_search3);

	if ($text_comments == 0){
		$text_alert_msg = $config['image_comment_msg']; //'Be the first to comment on this image!';
	}

	///////////////////////////// END TEXT COMMENTS ///////////////////////////////////////////

	//set condition for hidding certain blocks (e.g "no emails to list")
	if (empty($result_search3)) {
		$show_c1 = 1;
	} else {
		$show_c1 = 2;
	}

	// get members picture
	$a1_sql = "SELECT * FROM pictures WHERE user_id = $member_id";
	$a1_result = @mysql_query($a1_sql);

	if (@mysql_num_rows($a1_result) != 0) {
    		$a1_result = @mysql_fetch_array($a1_result);
    		$a1_existing_file = $a1_result['file_name'];
    		$a1_approved = $a1_result['approved'];

    		if ($a1_approved == "yes") {
        		// show picture and "change picture link"
        		$a1_mypicture = $config['site_base_url'] . '/pictures/' . $a1_existing_file;
        		$a1_show = 1;
    		} else {
    			// show place holder image and "awaiting approval link"
        		$a1_mypicture = $config['site_base_url'] . "/themes/$user_theme/images/placeholder.gif";
        		$a1_show = 2;
    		}
	} else {
    		// show place holder with "upload image link"
    		$a1_mypicture = $config['site_base_url'] . "/themes/$user_theme/images/placeholder.gif";
    		$a1_show = 3;
	}

	// get members online status
	$sql = "SELECT * FROM online WHERE logged_in_id = $member_id";
	$my_online_status = @mysql_num_rows(mysql_query($sql));

	if ($my_online_status > 0){ // there is a multiple IP address issue hence use of '>'
    		$online_status = '<img src="images/icon_online.gif" alt="'.$lang_status.'" width="8" height="8" border="0" align="absmiddle" />';
	} else {
		$online_status = '<img src="images/icon_offline.gif" alt="'.$lang_status.'" width="8" height="8" border="0" align="absmiddle" />';
	}

	// get members overall site ranking for badge display
    	$member_badge	= member_site_ranking($member_id);

	if ( $codes == "" ) {
		$codes = $codes_internal;
	}

	$error_code = errorcodes($codes);

	if ( !empty($error_code) ) {
		$blk_notification = $error_code['error_display'];
    		$message_type = $error_code['error_type'];
    		$error_message = $error_code['error_message'];
	}

	if ( empty($other_albums) ) {
		$show_v = 1;
	} else {
		$show_v = 2;
	}

	$template = "themes/$user_theme/templates/main_1.htm";
	$inner_template1 = "themes/$user_theme/templates/inner_album_view.htm";

	$TBS = new clsTinyButStrong;
	$TBS->NoErr = true;

	$TBS->LoadTemplate("$template");

	$TBS->MergeBlock('blk1', $result_loader);
	$TBS->MergeBlock('blk2', $other_albums);
	$TBS->MergeBlock('blk3', $result_search3);

	$TBS->Render = TBS_OUTPUT;
	$TBS->Show();

	@mysql_close();
	die();
}

?>