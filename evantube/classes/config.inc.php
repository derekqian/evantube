<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Version: PHPmotion V3.0 beta                                                      //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////



//------------------------------Error Settings--------------------------------------------------------------------//
// PHP Error settings
// uncomment for further debugging
// ini_set('log_errors', 1);
// ini_set('error_log', dirname(__FILE__) . '/phpmotion_errors.txt');

error_reporting(0);
ini_set('display_errors', '0');

//-----------------------------------------------------------------------------------------------------------//


//------------------------------Admin Notifications--------------------------------------------------------------------//
// Various notifications that the admin can get.
//-----------------------------------------------------------------------------------------------------------//

$config['notify_failed_conversions'] = 'yes';
$config['notify_new_member_signup']  = 'no';

//-----------------------------------------------------------------------------------------------------------//


//------------------------------Meta Tags--------------------------------------------------------------------//
// Note: Title/descriptin meta tags are set dynamically
//-----------------------------------------------------------------------------------------------------------//

$description	= 'Video Sharing Site';
$tags			= 'video sharing, video uploads, phpmotion V3 video cms';

//-----------------------------------------------------------------------------------------------------------//


//------------------------------Dynamic Search Setting------------------------------------------------------------------//
$dynamic_search = "script.src = 'word-suggest.php?action=suggest_words&check='+ encodeURIComponent(tok)";
//-----------------------------------------------------------------------------------------------------------//


//------------------------------Show video categories on home page--------------------------------------------//
// 'show_home_page_categories' - [yes/no]	=>shows video categories on home page
// 'show_categories_if_empty' - [yes/no]	=>show
// 'refresh_to_last_page' - [yes/no]	=>sets the login redirect to last viewed page
//------------------------------------------------------------------------------------------------------------//

$config['show_home_page_categories']		= 'yes';
$config['show_categories_if_empty']			= 'no';
$config['refresh_to_last_page']			= 'yes';

//---------------------------------------Default template settings---------------------------------------------//
//Various Default template settings
//-------------------------------------------------------------------------------------------------------------//

//Thumbnail images
$config['theme_thumbnail_width']			= '120';			// default theme thumbnail width
$config['theme_thumbnail_height']			= '90';			// default theme thumbnail height

//Blog FCKEditor width
$config['theme_blog_width']				= '700';			// default V3 theme editor width

//Embedder THEME SIZE
$config['theme_embed_width']				= '560';			// default V3 theme width
$config['theme_embed_height']				= '420';			// default V3 theme height

// Thumbnails upload settings
$config['create_thumbnail_width']			= '150';			// create max thumbnail width
$config['create_video_thumb_width'] 		= '120';			// create max video thumbnail width
$config['create_album_thumb_width'] 		= '120';			// create max audio album thumbnail width

// Thumbnails display settings
$config['display_audio_album_thumb_width']	= '100';			// max audio album thumbnail display width
$config['index_display_video_thumb_width']	= '120';			// max width video thumb inner_index.htm
$config['connect_member_display_width']		= '100';			// max width of connect with member thumb
$config['member_blog_display_width']		= '80';			// max width of member thumb displayed on main blogs
$config['read_blog_member_thumb_width']		= '65';			// max width of member thumb displayed on read blog
$config['members_profile_thumb_width']		= '85';			// max width of thumbs on members profile for (videos, images etc)
$config['members_profile_own_thumb_width']	= '100';    		// max width of thumbs on members profile for (members main thumb)
$config['members_prof_friends_thumb_width']	= '55';     		// max width of thumbs on members profile for (members main thumb)
$config['general_large_thumb_width']		= '120';			// max display width of any general large thumbs, if needed
$config['general_medium_thumb_width']		= '80';			// max display width of any general medium thumbs, if needed
$config['general_small_thumb_width']		= '30';			// max display width of any general small thumbs, if needed
$config['display_member_picture_width']		= '80';			// max display width member picture

// Various
$config['show_member_videos']				= 12;				// member videos shown on play page right side
$config['color_tellafriend']				= '#F0F6F9';		// back color for reply boxes and tell a friend
$config['default_album_pic']				= 'featured_mp3.png';	// default audio image if album image is none
$config['popular_show_limit']				= 3;				// items shown on in popular boxes
$config['popular_text_limit']				= 38;				// length of title in popular boxes
$config['video_title_length']				= 68;				// video SEO title length NOTE THIS CAN BREAK UTF-8 words
$config['max_tag_word_length']			= 16;				// tag word max length to show



//---------------------------------------Members Avatar image settings-----------------------------------------//
//Avatar image settings
//-------------------------------------------------------------------------------------------------------------//
$config['member_pic_maxwidth'] 			= '600';
$config['member_pic_maxheight'] 			= '600';
$config['member_pic_minwidth'] 			= '100';
$config['member_pic_minheight'] 			= '100';
$config['member_pic_maxsize'] 			= '600000';
$config['member_pic_minsize'] 			= '10000';


//---------------------------------------Various Convertor Settings___-----------------------------------------//
// Various Settings used during the video conversion process
//-------------------------------------------------------------------------------------------------------------//

$config['convertor_aspect_ratio']           	= '4:3';
$config['convertor_frame_rate']             	= 29.97;

// Misc advanced config
$config['max_username_length']			= 18;			    				// usernames max length
$config['allowed_ext']				      = array(".gif", ".jpg", ".png", ".jpeg");	// allowed image upload types
$config['comments_flood_time']		      = 120;						// seconds to wait between posting comments
$config['comments_length']	     		    	= '20';						// minimum characters in video comments
$config['admin_order_by']			      = 'random';						// how to display videos on home page, random shows dif videos each time
$config['blog_max_limits']			      = '12';						// max number of blogs each member can have
$config['age_limit']				      = 14;							// under this age members birthday is shown as PRIVATE

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
							GENERAL ADVANCED SETTINGS - PHPMOTION ADDONS AND POWERTOOLS
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

//#####################################################################################################################################################
// SMF FORUM CONFIG

$forum_path						= 'forum';		// directory name of the smf forum installaltion
$smf_refresh					= 'off';		// off = after login refresh to video
										// on	 = after login refresh to forum
// SMF END
//#####################################################################################################################################################


//#####################################################################################################################################################
// DIVX CONFIG

// DIXV END
//#####################################################################################################################################################


//#####################################################################################################################################################
// MP4 CONFIG

// MP4 END
//#####################################################################################################################################################

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
							END GENERAL ADVANCED SETTINGS - PHPMOTION ADDONS AND POWERTOOLS
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//                 					!! IMPORTANT => DO NOT EDIT BELOW THIS LINE                                                   	     //
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// auto check for smf forum
//if ( defined('SMF_INSTALLED') ) {

if ( file_exists( $base_path.'/'.$forum_path.'/v3_smf_forum.inc.php' ) ) {

	////////////////////////////////////////////////////////////////////////////////////////////////
	// DO NOT EDIT BELOW

	$enable_forum		= 1;
	$smf_bridge_register	= $forum_path . '/index.php?action=register';
	$smf_bridge_login		= $forum_path . '/index.php?action=login2';
	$smf_bridge_link		= $forum_path . '/index.php?action=login';
	$smf_bridge_log_out	= $forum_path . '/index.php?action=logout;sesc=';
	$smf_reminder		= $forum_path . '/index.php?action=reminder';

	$login_out_link		= $smf_bridge_link; //$smf_bridge_login;

	//$login_out_link		= 'login.php';

	$register_menu_link     = $smf_bridge_register;
	$password_reminder	= $smf_reminder;

} else {

	$login_out_link		= 'login.php';
	$register_menu_link	= 'join.php';
	$password_reminder	= 'login.php';

}


?>