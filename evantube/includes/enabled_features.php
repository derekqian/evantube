<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////


//Check the name of the file calling this include
/////////////////////////////////////////////////
$calling_file 		= end(explode('/', $_SERVER['PHP_SELF']));


$sql_public_private	= '';
$sql_pagination 		= '';

//___________________________________________________________
//__START HERE_______________________________________________

switch ($calling_file)
{
	//___Play.php___________________________________________________________________________________________
    	case 'play.php':

      	//Get all video details
        	$sql = "SELECT * FROM videos WHERE indexer = $vid AND approved ='yes'";
        	$query = @mysql_query($sql);
        	$result = @mysql_fetch_array($query);
        	$active = $result['approved'];
        	$public_private = $result['public_private'];
        	$allow_embedding = $result['allow_embedding'];
        	$owners_id = $result['user_id'];

        	// Is video Active/Found etc
        	(mysql_num_rows($query) > 0) ? null:generic_error($config['error_11']);

        	//check if its my friend viewing
        	////////////////////////////////
        	if ($user_id != '' && $user_id != $owners_id){
            	$are_we_friends = false;
            	$sql = "SELECT * FROM friends WHERE user_id = $user_id AND friends_id = $owners_id AND invitation_status = 'accepted' OR user_id = $owners_id AND friends_id = $user_id AND invitation_status = 'accepted'";
            	$query = @mysql_query($sql);
            	if (mysql_num_rows($query) > 0){
                		$are_we_friends = true;
                		$show_private_content = true;
            	}
        	}

        	//if not my friend, check if admin/mod
        	if ($public_private == 'private' && $owners_id != $user_id && $are_we_friends == false) {
        		($user_group == 'admin' || $user_group == 'global_mod' || $user_group == 'standard_mod') ? $show_private_content = true : generic_error($config['error_12']);
        	}

        	//check if allow download
        	$allow_download = (($config['allow_download'] == 'yes') ? 1 : 0);

        	//check allow embed
        	$show_embed = (($allow_embedding == 'yes') ? 1 : 0);

        	// video comments
        	$show_comments = (($config["enabled_features_video_comments"] == 'yes') ? 1 : 0);

	break;

	//___Album_view.php___________________________________________________________________________________________
    	case 'album_view.php':

      	//Get all video or Album details
        	if ($image_id){
            	$sql = "SELECT * FROM images WHERE indexer = $image_id AND approved ='yes'";
        	}

        	if ($album){
            	$sql = "SELECT * FROM image_galleries WHERE gallery_id = '$album'";
        	}

        	$query = @mysql_query($sql);
        	$result = @mysql_fetch_array($query);
        	$active = $result['approved'];
        	$public_private = $result['public_private'];
        	$allow_embedding = $result['allow_embedding'];
        	$owners_id = $result['user_id'];

        	// Is video Active/Found etc
        	(mysql_num_rows($query) > 0) ? null:generic_error($config['error_11']);

        	//check if its my friend viewing
        	if ($user_id != '' && $user_id != $owners_id){
            	$are_we_friends = false;
            	$sql = "SELECT * FROM friends WHERE user_id = $user_id AND friends_id = $owners_id AND invitation_status = 'accepted' OR user_id = $owners_id AND friends_id = $user_id AND invitation_status = 'accepted'";
            	$query = @mysql_query($sql);
            	if (mysql_num_rows($query) > 0){
                		$are_we_friends = true;
                		$show_private_content = true; //used in album_view
            	}
        	}

        	//if not my friend, check if admin/mod
        	if ($public_private == 'private' && $owners_id != $user_id && $are_we_friends == false) {
        		($user_group == 'admin' || $user_group == 'global_mod' || $user_group == 'standard_mod') ? $show_private_content = true : generic_error($config['error_12']);
        	}

        	//check if allow download
        	$allow_download = (($config['allow_download'] == 'yes') ? 1 : 0);

	      //check allow embed
	      $show_embed = (($allow_embedding == 'yes') ? 1 : 0);

	      // video comments
        	$show_comments = (($config['enabled_features_video_comments'] == 'yes') ? 1 : 0);

	break;

    	case 'albums.php':
    	case 'albums_ajax.php':
    	case 'seemore.php':
    	case 'category_home.php':

      	//Check if admin/mod
      	if ($user_group == 'admin' || $user_group == 'global_mod' || $user_group == 'standard_mod') {
      		$sql_public_private = '';
      	} else {
      		$sql_public_private = "AND public_private = 'public'";
      	}
	break;

	case 'group_home.php':

    		$sql			= "SELECT * FROM group_profile WHERE indexer = $group_id";
      	$query 		= @mysql_query($sql);
        	$result 		= @mysql_fetch_array($query);
        	$public_private	= $result['public_private'];
        	$owners_id 		= $result['admin_id'];

        	// is it found
        	(mysql_num_rows($query) > 0) ? null: generic_error($config['error_11']);


        	if ($user_id != '' && $user_id != $owners_id){
      		$are_we_friends = false;
            	$sql = "SELECT * FROM friends WHERE user_id = '$user_id' AND friends_id = '$owners_id' AND invitation_status = 'accepted' OR user_id = '$owners_id' AND friends_id = '$user_id' AND invitation_status = 'accepted'";
            	$query = @mysql_query($sql);

            	if (mysql_num_rows($query) > 0) $are_we_friends = true;
            }

        	if ($public_private == 'private' && $owners_id != $user_id) {
            	($are_we_friends == true || $user_group == 'admin' || $user_group == 'global_mod' || $user_group == 'standard_mod') ? null : generic_error($config['error_12']);
        	}

	break;


	case 'read_blog.php':

		// Get blog details and blog story/article
		$result	= array();
		$sql		= "SELECT * FROM blogs WHERE indexer = $blog_id";
		$query 	= @mysql_query($sql);

		// is blog active found etc
		(mysql_num_rows($query) > 0) ? null:generic_error($config['error_11']);

		while ( $result = @mysql_fetch_array($query) ) {
			$owners_id		= $result['user_id'];
			$public_private 	= $result['public_private'];
		}

		if ($user_id != '' && $user_id != $owners_id) {
			$are_we_friends = false;
			$sql = "SELECT * FROM friends WHERE user_id = '$user_id' AND friends_id = '$owners_id' AND invitation_status = 'accepted' OR user_id = '$owners_id' AND friends_id = '$user_id' AND invitation_status = 'accepted'";
      		$query = @mysql_query($sql);

      		if (mysql_num_rows($query) > 0){
          			$are_we_friends = true;
           			$show_private_content = true;
			}
		} else {
			$show_private_content = true;
		}

		// if not my friend, check if admin or mod
		if ($public_private == 'private' && $owners_id != $user_id && $are_we_friends == false) {
			($user_group == 'admin' || $user_group == 'global_mod' || $user_group == 'standard_mod') ? $show_private_content = true : generic_error($config['error_12']);
		}

		// return correct sql
		if ($user_group == 'admin' || $user_group == 'global_mod' || $user_group == 'standard_mod' || $show_private_content == true){
    			$sql_public_private = '';
     			$sql_pagination = "public_private = 'public' OR public_private = 'private'";
		} else {
    			$sql_public_private = "AND public_private = 'public'";
     			$sql_pagination = "public_private = 'public'";
		}

		//echo "show_private_content " . $show_private_content . " user_id " . $user_id . " owner id " . $owners_id . " blog id " . $blog_id . " sql " . $public_private;

	break;
}// end switch


//______________________________________________________________________________________________
//___Audio______________________________________________________________________________________

$enable_audio = (($config["enabled_features_audio"] == 'yes') ? 1 : 0);
$files_array = array('audio.php', 'audio_selector.php', 'audio_uploader.php', 'audio.php', 'play_audio.php', 'comments_audio.php', 'comments_audio_ajax.php');

if (in_array($calling_file, $files_array) && $enable_audio == 0) {
	$error_messages = $config['error_26'];
    	generic_error($error_messages);
}

//______________________________________________________________________________________________
//___Images______________________________________________________________________________________

$enable_images = (($config["enabled_features_images"] == 'yes') ? 1 : 0);
$files_array = array('albums.php', 'album_view.php', 'album_ajax.php', 'comments_image_ajax.php', 'image_uploader.php');

if (in_array($calling_file, $files_array) && $enable_images == 0) {
	$error_messages = $config['error_26'];
	generic_error($error_messages);
}

//______________________________________________________________________________________________
//___Blogs______________________________________________________________________________________

$enable_blogs = (($config["enabled_features_blogs"] == 'yes') ? 1 : 0);
$files_array = array('blogs.php', 'blog_reply_ajax.php', 'read_blog.php');

if (in_array($calling_file, $files_array) && $enable_blogs == 0) {
	$error_messages = $config['error_26'];
    	generic_error($error_messages);
}


?>