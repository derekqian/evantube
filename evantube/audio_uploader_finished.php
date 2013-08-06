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
include_once ('classes/login_check.php');
include_once ('includes/enabled_features.php');


$file_size_limit_error	= $config['file_size_limit_error'];
$file_type_error		= $config['file_type_error'];
$file_dimensions_error	= $config['file_dimensions_error'];
$file_chmod_error		= $config['file_chmod_error'];
$error_25			= $config['error_25'];
$error_18			= $config['error_18'];
$gd_library_missing	= $config['gd_library_missing'];
$allowed_max		= $config['member_pic_maxsize'];
$allowed_min		= '5000';
$upload_maxwidth		= $config['member_pic_maxwidth'];
$upload_maxheight		= $config['member_pic_maxheight'];
$upload_minwidth		= $config['member_pic_minwidth'];
$upload_minheight		= $config['member_pic_minheight'];


// Check if Feature is enabled
DisabledFeatureRedirect($audio); //for usage see /includes/menus.inc.php

// catch new album image upload first
$audio_image_upload = @mysql_real_escape_string( $_POST['audio_image_upload'] );

if ($_POST['audio_image_upload'] == 'yes') {

	$upload_album_cover		= @mysql_real_escape_string( $_POST['upload_album_cover'] );

	$album_id				= (int) @mysql_real_escape_string( $_POST['album_id'] );
	$display_mem_pic_width		= $config['display_member_picture_width'];
      $upload_allowed_maxwidth	= $config['member_pic_maxwidth'];
      $upload_allowed_maxheight	= $config['member_pic_maxheight'];
      $upload_allowed_minwidth	= $config['member_pic_minwidth'];
      $upload_allowed_minheight	= $config['member_pic_minheight'];
      $allowed_maxuploadsize		= $config['member_pic_maxsize'];
      $allowed_ext			= $config['allowed_ext'];
	$show_image_done			= 1;
     	$show_audio_done			= 0;
	$upload_dir 			= 'addons/audio/images/album_images/';

    	if ($_FILES['userfile']) {
    		// do_upload function moved to sub_function below - maybe move to functions.inc.php ?

    		$album_cover_change	= do_upload( $upload_dir );
    		$album_image_message	= $album_cover_change[0];
    		$album_new_pic		= $album_cover_change[1];
    		$upload_proceed		= $album_cover_change[2];
    	}

   	if ( $upload_proceed == true ) {

   		if ( $upload_album_cover == 'no' ) {
   			$show_image_done	= 0;
			$show_audio_done	= 1;

		} else {
			// update audio_albums table album cover image
			$sql_image			= "UPDATE audio_albums SET album_picture = '$album_new_pic' WHERE album_id = '$album_id'";
   			$query_image		= @mysql_query($sql_image);
   			$show_image_done		= 1;
   			$album_image_message	= $config['album_image_message'];
   		}

   		// display success
   		$template 		= "themes/$user_theme/templates/main_1.htm";
		$inner_template1 	= "themes/$user_theme/templates/inner_upload_audio_complete.htm";
		$TBS 			= new clsTinyButStrong;
		$TBS->NoErr 	= true;
		$TBS->LoadTemplate("$template");
		$TBS->Render 	= TBS_OUTPUT;
		$TBS->Show();
		@mysql_close();
		die();

	} else {

		// we have errors with album image upload

		// we need posted info to re-poplulate form to start image upload again

		$use_album_id		= (int) @mysql_real_escape_string( $_POST['album_id'] );

		$change_album_cover	= 1;
		$show_audio_done		= 0;
		$show_image_done		= 0;

		// in main_1 this is the order of messages
		//[var.message_type] - [var.error_message]

		$error_message 		= $album_image_message;

		$message_type 		= $config['notification_error'];

		$blk_notification 	= 1;

		$template 			= "themes/$user_theme/templates/main_1.htm";
		$inner_template1 		= "themes/$user_theme/templates/inner_upload_audio_complete.htm";
		$TBS 				= new clsTinyButStrong;
		$TBS->NoErr 		= true;

		$TBS->LoadTemplate("$template");

		$TBS->Render 		= TBS_OUTPUT;
		$TBS->Show();
		@mysql_close();
		die();
	}


} else {

$show_image_done	= 0;
$show_audio_done	= 1;
$album_names	= array();
$albums_all		= '';
$form_field		= '';

// get user albums
$sql_albums		= "SELECT user_id, album_id FROM audios WHERE user_id = '$user_id' GROUP BY album_id";
$query_albums 	= @mysql_query($sql_albums);
while ($rows = @mysql_fetch_array($query_albums)) {
	$album_id		= (int) mysql_real_escape_string($rows['album_id']);

	$sql	 		= "SELECT album_name FROM audio_albums WHERE album_id = '$album_id' ORDER BY album_id";
	$result 		= @mysql_query($sql);
	$row			= @mysql_fetch_array( $result );
	$album_names	= mysql_real_escape_string($row['album_name']);

	if ( $album_names != '' ) {
		$form_field = '<option value="' . $album_names . '">' . $album_names . '</option>';
		$albums_all .= $form_field;
	}
}

$finish 			= @mysql_real_escape_string( $_POST['B3'] );
$category 			= @mysql_real_escape_string( $_POST['category'] );
$album_type			= @mysql_real_escape_string( $_POST['album_type'] );
$use_album			= @mysql_real_escape_string( $_POST['use_album'] );

$create_user_album	= false;
$use_album_id		= 0;
$change_album_cover	= 0;

if ( $finish ) {

	if ( $use_album == 'yes' ) {
		$audio_album		= @mysql_real_escape_string( $_POST['audio_album'] );
		$new_audio_album		= @mysql_real_escape_string( $_POST['new_audio_album'] );
		$create_user_album 	= true;
		$change_album_cover	= 1;
	}

	if ( $new_audio_album != "" ) {
		$create_audio_album	= $new_audio_album;
		$create_audio_album 	= ucwords($create_audio_album);
      	$create_audio_album_seo = seo_title($create_audio_album);

      } else {

      	// user chooses one of their albums

      	$create_user_album 	= false;
		$members_album_choice	= 'member_album';
		$create_audio_album 	= $audio_album;
	}

	if ( $members_album_choice == 'member_album' ) {
		$get_album_id	= "SELECT album_id FROM audio_albums WHERE album_name = '$create_audio_album' LIMIT 1";
      	$n_album_id		= @mysql_query($get_album_id);
      	$row_id		= @mysql_fetch_array( $n_album_id );
    		$use_album_id	= $row_id['album_id'];
	}

	if ( $create_user_album == true ) {
		// get genre id and add new audio album to audio_albums table
		$sql_id	= "SELECT channel_id FROM genre WHERE channel_name = $category LIMIT 1";
    		$result_id 	= @mysql_query($sql_id);
    		$row 		= @mysql_fetch_array( $result_id );
    		$genre_id	= $row['channel_id'];

		$sql = "INSERT INTO audio_albums (album_name, album_name_seo, album_description, date_created, public_private, active, album_id, genre_id, has_audio, album_picture)
      	  			  VALUES ('$create_audio_album', '$create_audio_album_seo', '$create_audio_album', NOW(), '$album_type', 'yes', '', '$genre_id', 'yes', '')";

		$query = @mysql_query($sql);

		if( !$query ) {
			die("Error while during sql_query. Error Output: <br/>". mysql_errno() . ": " . mysql_error(). "<br/>"."Query follows:<br/>".$query);
			@mysql_close();
			die();
		}



     		$get_album_id	= "SELECT album_id FROM audio_albums WHERE album_name = '$create_audio_album' LIMIT 1";
      	$n_album_id		= @mysql_query($get_album_id);
      	$row_id		= @mysql_fetch_array( $n_album_id );
    		$use_album_id	= $row_id['album_id'];
    	}

	$file_name 		= $_POST['uploaded_file_name'];
	$title		= $_POST['title'];
	$artist 		= $_POST['artist'];
	$description	= $_POST['description'];
	$tags 		= $_POST['tags'];
	$length		= $_POST['length'];
	$album 		= $_POST['album'];
	$album_year 	= $_POST['year'];

	$location_recorded= $_POST['location_recorded'];
	$allow_comments	= $_POST['allow_comments'];
	$allow_embedding	= $_POST['allow_embedding'];
	$public_private	= $_POST['public_private'];

	// IF PROCEDE INSERT ALL DATA INTO AUDIO DATABASE
	list($split_name, $extension) = split('.', $file_name);
	$file_name			= @mysql_real_escape_string($file_name);
	$title 			= @mysql_real_escape_string($title);
	$artist 			= @mysql_real_escape_string($artist);
	$description 		= @mysql_real_escape_string($description);
	$tags 			= @mysql_real_escape_string($tags);
	$length 			= @mysql_real_escape_string($length);
	$cat_id 			= @mysql_real_escape_string($cat_id);
	$category 			= @mysql_real_escape_string($category);

	$album 			= @mysql_real_escape_string($album);
	$album_year 		= @mysql_real_escape_string($year);

	$location_recorded	= @mysql_real_escape_string($location_recorded);
	$allow_comments 		= @mysql_real_escape_string($allow_comments);
	$allow_embedding 		= @mysql_real_escape_string($allow_embedding);
	$public_private 		= @mysql_real_escape_string($public_private);

	$title = str_replace ('"', "", $title);
	$title_seo = seo_title($title);

	$sql = "INSERT INTO audios(audio_id, album_id, title, title_seo, artist, description, tags, audio_length, cat_id, channel, album, album_year,
				   location_recorded, allow_comments, allow_embedding, public_private, date_uploaded, allow_ratings,
				   rating_number_votes, rating_total_points, updated_rating, approved, number_of_views, user_id, featured)
			  VALUES('$file_name', '$use_album_id', '$title', '$title_seo', '$artist', '$description', '$tags', '$length', '$cat_id', '$category',
			  	   '$album', '$album_year', '$location_recorded', '$allow_comments', '$allow_embedding', '$public_private',
			  	   NOW(), 'yes', 0, 0, 0, 'yes', 0, $user_id, 'no')";

	$query = @mysql_query($sql);

	// UPDATE GENRE TABLE
	$sql2 = "UPDATE genre SET has_audio = 'yes' WHERE channel_name = '$category'";
	$query = @mysql_query($sql2);

	$template 		= "themes/$user_theme/templates/main_1.htm";
	$inner_template1 	= "themes/$user_theme/templates/inner_upload_audio_complete.htm";
	$TBS 			= new clsTinyButStrong;
	$TBS->NoErr 	= true;
	$TBS->LoadTemplate("$template");
	$TBS->Render 	= TBS_OUTPUT;
	$TBS->Show();
	die();

} else { // else we have a post from uu_upload start

	require 'audio_uploader_conlib.php';
	$temp_dir	= $_REQUEST['temp_dir'];
	$_POST_DATA = getPostData($temp_dir, $_REQUEST['tmp_sid']);

	foreach ($_POST_DATA as $post_key => $post_value) {
		if (preg_match("/^upfile_/i", $post_key)) {
        		$uploaded_file_name = $post_value;
        		$uploaded_file_path = $_POST_DATA['upload_dir'] . $uploaded_file_name;

        		if (is_file($uploaded_file_path)) {
            		$file_size = @filesize($uploaded_file_path);
            		$file_size = formatBytes($file_size);
            		clearstatcache();
        		}
    		}
	}

	$procede = true;

	$use_album		= $_POST['use_ablum'];
	$audio_album	= $_POST['audio_album'];
	$new_audio_album	= $_POST['new_audio_album'];
	$album_type		= $_POST['album_type'];
	$new_album_id 	= $_POST['new_audio_id'];


	if ($form_submitted == 'yes') {
    		foreach ( $_POST as $key => $value ) {
        		if ($key == 'title' || $key == 'description' || $key == 'tags') {
            		if (!isset($value) || ($value == "")) {
                			$display_key = @str_replace('_', " ", $key);
                			$message_type = $config['notification_error'];
                			$blk_id = 2;
                			$error_message = $error_message . ' - ' . $display_key . '  '.$lang_required;
                			$procede = false;
            		}
        		}
    		}
	} // end submitted

	// audio id3 tags
	if ($procede == true){
    		require('includes/mp3_id3_utils.php');
    		$mp3 = "uploads/audio/$uploaded_file_name";
    		$id1 = mp3_id($mp3);
    		if ( $id1==-1 ) {
        		//echo "File not exists or invalid header.<br>";
    		} else {
        		reset($id1);
    			while (list ($key, $val) = each ($id1)) {
      			switch ($key){
        				case 'title':
      	    				$title=$val;
                			break;

            			case 'artist':
            	   			$artist=$val;
            	   		break;

            			case 'lenght':
            	   			$length=$val;
            	   		break;

            			case 'genreid':
            	   			$cat_id=$val;
            	   		break;

            			case 'genre':
            	   			$category=$val;
            	   		break;

            			case 'album':
            	   			$album=$val;
            	   		break;

            			case 'year':
            	   			$year=$val;
            	   		break;
            		}
	        	}
		}

		if ($title == $uploaded_file_name ) $id3_error = 'tags';
		if ($cat_id == "") {
		    $cat_id = 12;
		    $category = 'Other';
		}

		// if id3 tag errors show edit form

		if ($id3_error) {
			$sql = "SELECT channel_name FROM genre WHERE active = 'yes' ORDER BY channel_name";
		    	$result1 = @mysql_query($sql);
		    	$allowed_genre = array();

		    	while ($result = @mysql_fetch_array($result1)) {
                  	$allowed_genre[] = $result['channel_name'];
                	}

                	if (in_array($category, $allowed_genre)) {
                  	$category = $category;

                	} else {
                    $category = 'Other';
                	}

		    	$audio_form = 'inner_upload_audio_form_edit.htm';

		    	$title = '';
		    	$artist = '';
		    	$description = '';
		    	$tags = '';

		} else {

			// we have info back from mp3 class - check if mp3 genre is allowed
		    	$sql = "SELECT channel_name FROM genre WHERE active = 'yes' ORDER BY channel_name";
		    	$result1 = @mysql_query($sql);
		    	$allowed_genre = array();

		    	while ($result = @mysql_fetch_array($result1)) {
                  	$allowed_genre[] = $result['channel_name'];
                	}

                	if (in_array($category, $allowed_genre)) {
                  	$category = $category; // GENRE/CATEGORY IS ALLOWED

                	} else {
                  	$category = 'Other'; // NOT ALLOWED SET TO =>OTHER
                	}

                	$audio_form = 'inner_upload_audio_form.htm';
		}

		// display audio upload finish form and exit
		$template 		= "themes/$user_theme/templates/main_1.htm";
		$inner_template1 	= "themes/$user_theme/templates/" . $audio_form;
		$TBS 			= new clsTinyButStrong;
		$TBS->NoErr 	= true;

		$TBS->LoadTemplate("$template");

		$TBS->Render 	= TBS_OUTPUT;

		$TBS->Show();

		@mysql_close();
		die();
	}
}  // end finish

} // end album image stop

//----------------------------------------------------------------- sub functions -----------------------------------------------------------------------//
//-------------------------------------------------------------------------------------------------------------------------------------------------------//

function getPostData($up_dir, $tmp_sid) {

	$param_array = array();
    	$buffer = "";
    	$key = "";
    	$value = "";
    	$paramFileName = $up_dir . $tmp_sid . ".params";
    	$fh = @fopen($paramFileName, 'r');

    	if (!is_resource($fh)) {
      	kak("<font color='red'>$lang_error</font>: ".$config['file_opening_error']." $paramFileName");
    	}

    	while (!feof($fh)) {
      	$buffer = fgets($fh, 4096);
        	list($key, $value) = explode('=', trim($buffer));
        	$value = str_replace("~EQLS~", "=", $value);
        	$value = str_replace("~NWLN~", "\r\n", $value);

        	if (isset($key) && isset($value) && strlen($key) > 0 && strlen($value) > 0) {
            	if (preg_match('/(.*)\[(.*)\]/i', $key, $match)) {
                		$param_array[$match[1]][$match[2]] = $value;

            	} else {
            		$param_array[$key] = $value;
            	}
        	}
    	}

    	fclose($fh);

    	if (isset($param_array['delete_param_file']) && $param_array['delete_param_file'] == 1) {

    		for ($i = 0; $i < 5; $i++) {
            	if (@unlink($paramFileName)) {
               		 break;

            	} else {
            		sleep(1);
            	}
        	}
    	}

    	return $param_array;
}
//-------------------------------------------------------------------------------------------------------------------------------------------------------//

function formatBytes($bytes, $format = 99)
{

	$byte_size = 1024;
    	$byte_type = array(" KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
    	$bytes /= $byte_size;
    	$i = 0;

    	if ($format == 99 || $format > 7) {
      	while ($bytes > $byte_size) {
            	$bytes /= $byte_size;
            	$i++;
        	}
    	} else {
    		while ($i < $format) {
            	$bytes /= $byte_size;
            	$i++;
        	}
    	}

    	$bytes = sprintf("%1.2f", $bytes);
    	$bytes .= $byte_type[$i];
    	return $bytes;
}
//-------------------------------------------------------------------------------------------------------------------------------------------------------//

function do_upload( $upload_dir )
{

	global $display_mem_pic_width, $upload_allowed_maxwidth, $upload_allowed_maxheight, $upload_allowed_minwidth, $upload_allowed_minheight, $allowed_maxuploadsize, $allowed_ext,
	$lang_limit, $allowed_min, $file_size_limit_error, $file_type_error, $file_dimensions_error, $file_chmod_error, $error_25, $error_18, $gd_library_missing;

	$upload_proceed 	= true;
	$file_name		= $_FILES['userfile']['name'];
      $file_name 		= str_replace("\\","",$file_name);
      $file_name 		= str_replace("'","",$file_name);
      $file_type		= $_FILES['userfile']['type'];
	$file_size		= $_FILES['userfile']['size'];
      $file_tmp		= $_FILES['userfile']['tmp_name'];
      $file_mimes 	= array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');
      $file_ext 		= explode( '.', $file_name );
      $ext_use		= strrchr($file_name, '.');
      $ext_use		= strtolower($ext_use);


      if ( $file_name == "" || $file_ext[1] == "" )	$album_image_message = $file_name_error;

      // block some exploit attempts - only alert a generic message
      if ( sizeof( $file_ext ) > 2 ) {
      	$album_image_message 	= $file_type_error;
      	$upload_proceed		= false;
      }

      if ( !in_array( $ext_use, $allowed_ext ) ) {
      	$album_image_message = $file_type_error;
      	$upload_proceed		= false;
      }

      if ( !in_array( $file_type, $file_mimes) ) {
      	$album_image_message = $file_type_error;
      	$upload_proceed		= false;
      }

      if ( $file_size < 5000 ) {
      	$album_image_message = $file_size_limit_error.": $lang_file_size_to_small $lang_limit $allowed_min bytes";
      	$upload_proceed		= false;
      }

      if ( $file_size > $allowed_maxuploadsize ) {
      	$album_image_message = $file_size_limit_error.": $lang_limit  $allowed_maxuploadsize bytes";
      	$upload_proceed		= false;
      }

      $checked_file_ext		= $file_ext[count($file_ext)-1];
     	$random_new_image		= md5(time());
	$random_new_image		= rand(0,999999999);
	$thumb_width		= $display_mem_pic_width;

      if ( $upload_proceed == true ) {

      	$image_create = true;

      	if( $file_type == 'image/pjpeg' || $file_type == 'image/jpeg' ) {
			$new_img = imagecreatefromjpeg($file_tmp);
           	}
           	elseif( $file_type == 'image/x-png' || $file_type == 'image/png' ) {
           		$new_img = imagecreatefrompng($file_tmp);
           	}
           	elseif( $file_type == 'image/gif' ) {
           		$new_img = imagecreatefromgif($file_tmp);
           	}

           	list($width, $height)	= getimagesize( $file_tmp );

           	if( $width < $upload_allowed_minwidth || $height < $upload_allowed_minheight ) {

           		ImageDestroy ($new_img);
           		$album_image_message	= $file_dimensions_error;
           		$image_create 		= false;
           	}

           	if ( $image_create == true ) {

           		$img_ratio		= $width/$height;

           		if ( $img_ratio > 1 ) {
           			$new_width	= $thumb_width;
              		$new_height	= $thumb_width / $img_ratio;
           		} else {
           			$new_height	= $thumb_width;
           			$new_width	= $thumb_width * $img_ratio;
			}

			if ( function_exists(imagecreatetruecolor)) {
				$resized_img = imagecreatetruecolor($new_width, $new_height);
			} else {
				$album_image_message	= $gd_library_missing;
			}

			imagecopyresized($resized_img, $new_img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

           		ImageJpeg ($resized_img, $upload_dir.$random_new_image.'.'.$checked_file_ext);
           		ImageDestroy ($resized_img);
           		ImageDestroy ($new_img);

           		$album_new_pic = $random_new_image.'.'.$checked_file_ext;

			$file_path	= $upload_dir.$random_new_image.'.'.$checked_file_ext;
			$result	= file_exists($file_path);

			if ( !chmod( $file_path, 0644 ) ) {
      			$album_image_message = $file_chmod_error;
      		} else {
      			$album_image_message = ($result)?$error_25 : $error_18;
     			}

     		} // end create member picture
     	} // end upload proceed


return array( $album_image_message, $album_new_pic, $upload_proceed );

}

?>