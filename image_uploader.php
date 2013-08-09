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

$load_ajax			= 1;
$load_javascript		= '';
$page_title			= $lang_upload_image;
$albums_proceed		= true;
$user_id 			= (int) mysql_real_escape_string($user_id);
$allowed_ext		= $config['allowed_ext'];
$allowed_maxwidth		= $config['album_pic_maxwidth'];
$allowed_maxheight	= $config['album_pic_maxheight'];
$allowed_minwidth		= $config['album_pic_minwidth'];
$allowed_minheight	= $config['album_pic_minheight'];
$allowed_maxuploadsize	= $config['album_pic_maxsize'];
$member_max_albums	= $config['member_max_albums'];
$pictures_max_per_album	= $config['pictures_max_per_album'];
$show_maxuploadsize	= $allowed_maxuploadsize / 1000;
$thumb_width		= 120;
$img_file_tmp_count	= 0;
$album_names		= array();
$albums_all			= '';
$form_field			= '';
$count_albums		= 0;

// get user albums
$sql_albums = "SELECT gallery_id, gallery_name, public_private FROM image_galleries WHERE user_id = '$user_id' ORDER BY gallery_id";
$query_albums = @mysql_query($sql_albums);

while ($rows = @mysql_fetch_array($query_albums)) {
	$gallery_id		= (int) mysql_real_escape_string($rows['gallery_id']);
	$gallery_name	= mysql_real_escape_string($rows['gallery_name']);
	$count_albums 	= mysql_num_rows($query_albums);
	$form_field = '<option value="' . $gallery_id . '">' . $gallery_name . '</option>';
	$albums_all .= $form_field;
}

if ( mysql_real_escape_string($_POST['v3']) == 'upload_pictures') {
	$security_token_check	= $_SESSION['security_token'];
	$post_security_token	= mysql_real_escape_string( $_POST['security_token'] );
	if ( $post_security_token != $security_token_check ) {
		$albums_proceed	= false;
      	$message_type	= $lang_notification_error;
    		$error_message	= 'Hacking Attemp, your IP has been logged!';
      	$show_upload	= 1;
      	$show_finish	= 0;
    		$blk_notification = 1;
    		$template 		= "themes/$user_theme/templates/main_1.htm";
            $inner_template1 	= "themes/$user_theme/templates/inner_upload_image.htm";
            $TBS 			= new clsTinyButStrong;
            $TBS->NoErr 	= true;
            $TBS->LoadTemplate("$template");
            $TBS->Render 	= TBS_OUTPUT;
            $TBS->Show();
            @mysql_close();
            die();
	}

	$album_id			= (int) mysql_real_escape_string( $_POST['album_id'] );
	$new_photo_album		= mysql_real_escape_string( $_POST['new_photo_album'] );
	$album_desc			= mysql_real_escape_string( $_POST['photo_album_desc'] );
	$album_tags			= mysql_real_escape_string( $_POST['album_tags'] );
	$album_type			= mysql_real_escape_string( $_POST['albumtype'] );
	$allow_comments		= mysql_real_escape_string( $_POST['album_cmts'] );
	$allow_ratings		= mysql_real_escape_string( $_POST['album_ratings'] );

	if ( $new_photo_album == "" && $album_id == "" ) {

		unset($_SESSION['security_token']);
		$security_token			= randomcode();
		$_SESSION['security_token'] 	= $security_token;

		$albums_proceed	= false;
      	$message_type	= $lang_notification_error;
    		$error_message	= $config['select_an_album'];
      	$show_upload	= 1;
      	$show_finish	= 0;
    		$blk_notification = 1;
    		$template 		= "themes/$user_theme/templates/main_1.htm";
            $inner_template1 	= "themes/$user_theme/templates/inner_upload_image.htm";
            $TBS 			= new clsTinyButStrong;
            $TBS->NoErr 	= true;
            $TBS->LoadTemplate("$template");
            $TBS->Render 	= TBS_OUTPUT;
            $TBS->Show();
            @mysql_close();
            die();
      }

	$img_file_type	= $_FILES['img_file']['type'];
      $img_file_name	= $_FILES['img_file']['name'];
      $img_file_size	= $_FILES['img_file']['size'];
      $img_file_tmp	= $_FILES['img_file']['tmp_name'];

      $all_thumbs		= array();
      $img_uploaded	= sizeof($img_file_tmp);

      for ( $x=0; $x < $img_uploaded; $x++ ) {
      	if ( $img_file_tmp[$x] != '' ) $img_file_tmp_count++;
      }

      if ( $img_file_tmp_count == 0 ) $img_file_tmp_count=1;

      $count_bad = 0;

      for ( $x=0; $x < $img_file_tmp_count; $x++ ) {

      	if( !is_uploaded_file($img_file_tmp[$x]) ) {

      		unset($_SESSION['security_token']);
			$security_token			= randomcode();
			$_SESSION['security_token'] 	= $security_token;

      		$albums_proceed	= false;
      		$x			= $img_file_tmp_count;
      		$message_type	= $lang_notification_error;
    			$error_message	= $config['file_none_selected'];
      		$show_upload	= 1;
      		$show_finish	= 0;
    			$blk_notification = 1;
    			$template 		= "themes/$user_theme/templates/main_1.htm";
                	$inner_template1 	= "themes/$user_theme/templates/inner_upload_image.htm";
                	$TBS 			= new clsTinyButStrong;
                	$TBS->NoErr 	= true;
                	$TBS->LoadTemplate("$template");
                	$TBS->Render 	= TBS_OUTPUT;
                	$TBS->Show();
                	@mysql_close();
                	die();

      	}

      	$tmp_ext = explode ('.', $img_file_name[$x]);

      	if ( sizeof($tmp_ext) > 2 ) {

    			unset($_SESSION['security_token']);
			$security_token			= randomcode();
			$_SESSION['security_token'] 	= $security_token;

    			$albums_proceed	= false;
      		$x			= $img_file_tmp_count;
      		$show_upload	= 1;
      		$show_finish	= 0;
    			$message_type	= $lang_notification_error;
    			$error_message 	= 'Invalid file type !';
    			$blk_notification = 1;
    			$template 		= "themes/$user_theme/templates/main_1.htm";
                	$inner_template1 	= "themes/$user_theme/templates/inner_upload_image.htm";
                	$TBS 			= new clsTinyButStrong;
                	$TBS->NoErr 	= true;

                	$TBS->LoadTemplate("$template");
                	$TBS->Render 	= TBS_OUTPUT;
                	$TBS->Show();
                	@mysql_close();
                	die();
            }

      	$ext = strrchr($img_file_name[$x], '.');
      	$ext = strtolower($ext);

      	if ( !in_array( $ext, $allowed_ext ) ) {

      		unset($_SESSION['security_token']);
			$security_token			= randomcode();
			$_SESSION['security_token'] 	= $security_token;

      		$albums_proceed	= false;
      		$x			= $img_file_tmp_count;
      		$show_upload	= 1;
      		$show_finish	= 0;
    			$message_type	= $lang_notification_error;
    			$error_message 	= $config['file_type_error'];
    			$blk_notification = 1;
    			$template 		= "themes/$user_theme/templates/main_1.htm";
                	$inner_template1 	= "themes/$user_theme/templates/inner_upload_image.htm";
                	$TBS 			= new clsTinyButStrong;
                	$TBS->NoErr 	= true;

                	$TBS->LoadTemplate("$template");
                	$TBS->Render 	= TBS_OUTPUT;
                	$TBS->Show();
                	@mysql_close();
                	die();
      	}

      	if ( $img_file_size[$x] < 5000 ) {								// size in kilobytes

      		unset($_SESSION['security_token']);
			$security_token			= randomcode();
			$_SESSION['security_token'] 	= $security_token;

      		$albums_proceed	= false;
      		$x			= $img_file_tmp_count;
      		$show_upload	= 1;
      		$show_finish	= 0;
    			$message_type	= $lang_notification_error;
    			$error_message 	= $config['file_size_limit_error'];
    			$blk_notification = 1;
    			$template 		= "themes/$user_theme/templates/main_1.htm";
                	$inner_template1 	= "themes/$user_theme/templates/inner_upload_image.htm";
                	$TBS 			= new clsTinyButStrong;
                	$TBS->NoErr 	= true;

                	$TBS->LoadTemplate("$template");
                	$TBS->Render 	= TBS_OUTPUT;
                	$TBS->Show();
                	@mysql_close();
                	die();
      	}

      	if ( $img_file_size[$x] > $allowed_maxuploadsize ) {

      		unset($_SESSION['security_token']);
			$security_token			= randomcode();
			$_SESSION['security_token'] 	= $security_token;

      		$albums_proceed	= false;
      		$x			= $img_file_tmp_count;
      		$show_upload	= 1;
      		$show_finish	= 0;
    			$message_type	= $lang_notification_error;
    			$error_message 	= $config['file_size_limit_error'];
    			$blk_notification = 1;
    			$template 		= "themes/$user_theme/templates/main_1.htm";
                	$inner_template1 	= "themes/$user_theme/templates/inner_upload_image.htm";
                	$TBS 			= new clsTinyButStrong;
                	$TBS->NoErr 	= true;

                	$TBS->LoadTemplate("$template");
                	$TBS->Render 	= TBS_OUTPUT;
                	$TBS->Show();
                	@mysql_close();
                	die();
      	}

		$checked_file_ext		= $tmp_ext[count($tmp_ext)-1];
		$random_new_image		= md5(time());
		$random_new_image		= rand(0,999999999);
		$thumb_width		= $thumb_width;

		if( $albums_proceed == true && $img_file_size[$x] ) {

			$image_create = true;

			// lets keep same image types
			if( $img_file_type[$x] == "image/pjpeg" || $img_file_type[$x] == "image/jpeg" ) {
				$new_img = imagecreatefromjpeg($img_file_tmp[$x]);
           		}
           		elseif( $img_file_type[$x] == "image/x-png" || $img_file_type[$x] == "image/png" ) {
           			$new_img = imagecreatefrompng($img_file_tmp[$x]);
           		}
           		elseif( $img_file_type[$x] == "image/gif" ) {
           			$new_img = imagecreatefromgif($img_file_tmp[$x]);
           		}

           		list($width, $height)	= getimagesize($img_file_tmp[$x]);

           		// kick out too small of an image
           		if( $width < $allowed_minwidth || $height < $allowed_minheight ) {

           			$count_bad++;
           			ImageDestroy ($new_img);
           			$image_message		= "<font color=\"#ED0000\" size=\"2\"><b>$count_bad of your images was not processed, the image size must be at least $allowed_minwidth x $allowed_minheight</b></font><br />";
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

					$show_upload	= 1;
					$show_finish	= 0;
    					$message_type	= $lang_notification_error;
    					$error_message 	= $config['gd_library_missing'];
    					$blk_notification = 1;
    					$template 		= "themes/$user_theme/templates/main_1.htm";
                			$inner_template1 	= "themes/$user_theme/templates/inner_upload_image.htm";
                			$TBS 			= new clsTinyButStrong;
                			$TBS->NoErr 	= true;

                			$TBS->LoadTemplate("$template");
                			$TBS->Render 	= TBS_OUTPUT;
                			$TBS->Show();
                			@mysql_close();
                			die();
                 		}

            		imagecopyresized($resized_img, $new_img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

            		// save thumbnails

            		$checked_file_ext 	= strtolower($checked_file_ext);

            		// ob_start();ImageJpeg($resized_img);$imgvar=ob_get_contents();ob_end_clean();$img64=base64_encode($imgvar);echo('<img src="data:image/x-icon;base64,'.$img64.'"></img>');die();
            		// ImageJpeg ($resized_img, "addons/albums/thumbs/$random_new_image.$checked_file_ext");
            		// ImageJpeg, ImagePng, ImageGif
			if( $img_file_type[$x] == "image/pjpeg" || $img_file_type[$x] == "image/jpeg" ) {
				ImageJpeg ($resized_img, "addons/albums/thumbs/$random_new_image.$checked_file_ext");
           		}
           		elseif( $img_file_type[$x] == "image/x-png" || $img_file_type[$x] == "image/png" ) {
           			ImagePng ($resized_img, "addons/albums/thumbs/$random_new_image.$checked_file_ext");
           		}
           		elseif( $img_file_type[$x] == "image/gif" ) {
           			ImageGif ($resized_img, "addons/albums/thumbs/$random_new_image.$checked_file_ext");
           		}
            		ImageDestroy ($resized_img);
            		ImageDestroy ($new_img);

				$new_thumbs			= $random_new_image . '.' . $checked_file_ext;
				$all_thumbs_array 	= array('new_thumbs' => $new_thumbs);
				$thumb_name_array		= array('upload_slot' => ($x+1));
				$result_thumbs_array 	= @array_merge($all_thumbs_array, $thumb_name_array);
				$all_thumbs[] 		= $result_thumbs_array;

				if( $img_file_type[$x] == "image/pjpeg" || $img_file_type[$x] == "image/jpeg" ) {
					$new_large_img = imagecreatefromjpeg($img_file_tmp[$x]);
           			}
           			elseif( $img_file_type[$x] == "image/x-png" || $img_file_type[$x] == "image/png" ) {
           				$new_large_img = imagecreatefrompng($img_file_tmp[$x]);
           			}
           			elseif( $img_file_type[$x] == "image/gif" ) {
           				$new_large_img = imagecreatefromgif($img_file_tmp[$x]);
           			}

           			//############################################################################
           			// new to keep orginal image size if width < max and height < max
           			// resize large image and save

				$album_img_width		= $allowed_maxwidth;
				$album_img_height		= $allowed_maxheight;
           			list($width, $height)	= getimagesize($img_file_tmp[$x]);

           			/*
           			if ( $width < $album_img_width && $height < $album_img_height ) {
           				$new_width	= $width;
           				$new_height	= $height;
           			}
           			*/

           			if ( $width <= $album_img_width ) {
           				$new_width	= $width;
           				$new_height	= $height;
           			}


           			if ( $width > $album_img_width ) {
           				$large_img_ratio	= $width/$height;
           				if ( $large_img_ratio > 1 ) {
           					$new_width	= $album_img_width;
              				$new_height	= $album_img_width / $large_img_ratio;
            			} else {
            				$new_height	= $album_img_width;
                 				$new_width	= $album_img_width * $large_img_ratio;
					}
				}

				if ( $height > $album_img_height ) {
           				$large_img_ratio	= $width/$height;
           				if ( $large_img_ratio > 1 ) {
           					$new_width	= $album_img_width;
              				$new_height	= $album_img_width / $large_img_ratio;
            			} else {
            				$new_height	= $album_img_width;
                 				$new_width	= $album_img_width * $large_img_ratio;
					}
				}

				if ( function_exists(imagecreatetruecolor)) {
					$album_resized_img = imagecreatetruecolor($new_width, $new_height);
				}

            		imagecopyresized($album_resized_img, $new_large_img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

            		$checked_file_ext 	= strtolower($checked_file_ext);

            		// save new full size images
            		// ImageJpeg ($album_resized_img, "addons/albums/images/$random_new_image.$checked_file_ext");
			if( $img_file_type[$x] == "image/pjpeg" || $img_file_type[$x] == "image/jpeg" ) {
				ImageJpeg ($album_resized_img, "addons/albums/images/$random_new_image.$checked_file_ext");
           		}
           		elseif( $img_file_type[$x] == "image/x-png" || $img_file_type[$x] == "image/png" ) {
           			ImagePng ($album_resized_img, "addons/albums/images/$random_new_image.$checked_file_ext");
           		}
           		elseif( $img_file_type[$x] == "image/gif" ) {
           			ImageGif ($album_resized_img, "addons/albums/images/$random_new_image.$checked_file_ext");
           		}
            		ImageDestroy ($album_resized_img);
            		ImageDestroy ($new_large_img);

            	} //end if $image_create == true

            } // end if
      } //end for


      if ( $albums_proceed == true ) {

      	///////////////////////////////////////////////////////
      	// save album id OR album name to table

      	if ( $new_photo_album != "" ) {

      	     	$album_id		= "";
      		$gallery_name 	= $new_photo_album;
      		$gallery_name 	= ucwords($gallery_name);
      		$gallery_name_seo = seo_title($gallery_name);

      		$sql = "INSERT INTO image_galleries (gallery_id, user_id, gallery_name, gallery_name_seo, gallery_description, gallery_tags, date_created, public_private, allow_comments, allow_ratings, gallery_picture)
      					VALUES (NULL, '$user_id', '$gallery_name', '$gallery_name_seo', '$album_desc', '$album_tags', NOW(), '$album_type', '$allow_comments', '$allow_ratings', 'none.gif')";

      		$query = @mysql_query($sql);

      		$sql_new_album	= "SELECT gallery_id, gallery_name, FROM image_galleries WHERE user_id = '$user_id' AND gallery_name = '$new_photo_album'";
      		$new_album		= @mysql_query($sql_albums);

      		while ( $rows = @mysql_fetch_array($new_album) ) {
      			$new_gallery_id		= (int) mysql_real_escape_string($rows['gallery_id']);
			}

			$album_id = $new_gallery_id;

		} else {
			$new_photo_album = $gallery_name;
		}

		if ( $image_message != "" ) {
			$image_notice = $image_message;
			$img_file_tmp_count 	= $img_file_tmp_count - $count_bad;
		}

		// show thumbnails and finish form
      	$show_upload	= 0;
      	$show_finish	= 1;
      	$template		= "themes/$user_theme/templates/main_1.htm";
		$inner_template1	= "themes/$user_theme/templates/inner_upload_image.htm";
		$TBS			= new clsTinyButStrong;
		$TBS->NoErr		= true;
		$TBS->LoadTemplate("$template");
		$TBS->MergeBlock('blkfinished', $all_thumbs);
		$TBS->Render	= TBS_OUTPUT;
		$TBS->Show();

		die();
	}

} // end posted



if ( mysql_real_escape_string($_POST['v3']) == 'upload_pictures_finish') {

	$allow_embed	= 'yes';
	$img_uploaded	= (int) mysql_real_escape_string( $_POST['img_uploaded'] );
	$album_id 		= (int) mysql_real_escape_string( $_POST['album_id'] );
	$new_photo_album	= mysql_real_escape_string( $_POST['new_photo_album'] );
	$ablum_cover_img	= mysql_real_escape_string( $_POST['album_default'] );
	$album_type		= mysql_real_escape_string( $_POST['album_type'] );
	$allow_ratings	= mysql_real_escape_string( $_POST['album_ratings'] );
	$allow_comments	= mysql_real_escape_string( $_POST['album_cmts'] );
	$image_id[]		= mysql_real_escape_string( $_POST['img_name_1'] );
	$thumb_title[] 	= mysql_real_escape_string( $_POST['thumb_title_1'] );
	$thumb_desc[] 	= mysql_real_escape_string( $_POST['thumb_desc_1'] );
	$image_tags[] 	= mysql_real_escape_string( $_POST['image_tags_1'] );
	$image_id[]		= mysql_real_escape_string( $_POST['img_name_2'] );
	$thumb_title[] 	= mysql_real_escape_string( $_POST['thumb_title_2'] );
	$thumb_desc[] 	= mysql_real_escape_string( $_POST['thumb_desc_2'] );
	$image_tags[] 	= mysql_real_escape_string( $_POST['image_tags_2'] );
	$image_id[]		= mysql_real_escape_string( $_POST['img_name_3'] );
	$thumb_title[] 	= mysql_real_escape_string( $_POST['thumb_title_3'] );
	$thumb_desc[] 	= mysql_real_escape_string( $_POST['thumb_desc_3'] );
	$image_tags[] 	= mysql_real_escape_string( $_POST['image_tags_3'] );
	$new_photo_album	= ucwords($new_photo_album);

	// enter image into db
	for ( $x=0; $x < $img_uploaded; $x++ ) {

		$thumb_title[$x]	= ucwords($thumb_title[$x]);

		if ( is_numeric($thumb_title[$x]) ) {

			$thumb_title[$x] = $lang_edit_me;
		}

		$thumb_title[$x]	= str_replace('/', '-', $thumb_title[$x]);
		$thumb_title[$x]	= str_replace("'", '', $thumb_title[$x]);

     		$title_seo[$x]	= seo_title($thumb_title[$x]);

		$sql = "INSERT INTO images (indexer, image_id, gallery_id, user_id, viewtime, title, title_seo, description, tags, gallery_name, date_recorded, date_uploaded, image_size, allow_comments, allow_embedding, allow_ratings, rating_number_votes, rating_total_points, updated_rating, public_private, approved, number_of_views, featured, promoted, flag_counter)
				VALUES (NULL, '$image_id[$x]', '$album_id', '$user_id', '0000-00-00 00-00-00', '$thumb_title[$x]', '$title_seo[$x]', '$thumb_desc[$x]', '$image_tags[$x]', '$new_photo_album', '0000-00-00 00:00:00', NOW(), NULL, '$allow_comments', 'yes', '$allow_ratings', '0', '0', '0', '$album_type', 'yes', '0', 'no', 'no', '0')";

		$insert_query	= @mysql_query($sql);

		// update image gallery enter has images
		$sql_2 = "UPDATE image_galleries SET has_images = '1' WHERE gallery_id = $album_id";
		$query_update = @mysql_query($sql_2);


	}

	// update album cover image if an image is selected
	if ( $ablum_cover_img != "" ) {
		$sql = "UPDATE image_galleries SET gallery_picture = '$ablum_cover_img' WHERE gallery_id = $album_id";
		$cover_query = @mysql_query($sql);

     		if(!$cover_query) {
     			die($config['error_26']);
			@mysql_close();
			die();
		}

	}

	$template		= "themes/$user_theme/templates/main_1.htm";
	$inner_template1	= "themes/$user_theme/templates/inner_upload_images_complete.htm";
	$TBS			= new clsTinyButStrong;
	$TBS->NoErr		= true;

	$TBS->LoadTemplate("$template");

	$TBS->Render	= TBS_OUTPUT;

	$TBS->Show();
	@mysql_close();

	die();
}

///////////////////////////////////////////////////////////////////////////////////////////
// Show start page

if (!isset($form_submitted) || ($form_submitted == '')) {

	$albums_proceed 			= true;
	$_SESSION['security_token'] 	= NULL;
	unset($_SESSION['security_token']);
	$security_token			= randomcode();
	$_SESSION['security_token'] 	= $security_token;

	// show upload form
	if ( $albums_proceed == true ) {
		$show_upload	= 1;
		$show_finish	= 0;
		$template		= "themes/$user_theme/templates/main_1.htm";
		$inner_template1	= "themes/$user_theme/templates/inner_upload_image.htm";
		$TBS			= new clsTinyButStrong;
		$TBS->NoErr		= true;
		$TBS->LoadTemplate("$template");

		$TBS->Render	= TBS_OUTPUT;
		$TBS->Show();
		@mysql_close();
		die();
	}
}

?>
