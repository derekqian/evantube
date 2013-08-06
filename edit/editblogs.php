<?php
// GOOD EDIT EMBED WORKS
///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once('../classes/config.php');
include_once('../classes/permissions.php');
include_once('../siteadmin/includes/functions.php');

$word_length	= $config['max_tag_word_length'];
$id 			= (int) mysql_real_escape_string($_GET['id']);

if ( !empty($_POST) ) $id = (int) mysql_real_escape_string($_POST['id']);

// check if uid is in the requesting url
if ($id == '') {
	ErrorDisplay1($config['invalid_request']);
    	die();
}

//______PERMISSIONS CHECK _____________________________________________________________________________
$usercheck = new LoadPermissions('',$id,'blogs');  //($user_id, $content_id, $content_type);
$usercheck->CheckPermissions();
$usercheck->ActionsCheck('edit'); //Can be ( edit, delete, both ) dies if user does not have permissions
//_____________________________________________________________________________________________________

// Some basic presets
$show_notification	= 0;
$show_success		= 0;
$show_warning		= 0;

// update mysql database
if( isset($_POST['submit']) ) {
	$proceed		= true;
	$title		= $_POST['title'];
    	$description 	= $_POST['description'];
    	$tags 		= $_POST['tags'];
    	$channel 		= $_POST['channel'];
    	$category_id 	= $_POST['category_id'];
   	$allow_replies 	= $_POST['allow_replies'];
    	$allow_ratings 	= $_POST['allow_ratings'];
    	$public_private 	= $_POST['public_private'];

	$title_seo		= seo_title($title);
	$blog_story 	= $_POST['FCKeditor1'];

    	$tags_returned 	= make_tag_words($tags, $word_length);

     	if ( $tags_returned[0] == 'false' ) {
		$proceed			= false;
		$error_message		= $tags_returned[1];
		$message			= $lang_error . " " . $error_message;
		$show_notification 	= 1;

	} else {
		$safe_tags		= $tags_returned[1];
	}

    	//  check if form is completed should never get here unless some messing around ??
    	// is show message and die!

    	if ($title == '' || $description == '' || $tags == '' || $blog_story == '') {

      	$warning_close		= 'There was some problem with your input, please close this window and start over';
      	$show_warning		= 1;
      	$show_notification	= 1;
    		$message			= $config['fill_all_fields'];
    		$show_success		= 1;  // 1 = do not show fields or editor this show also say "Close this window and start over

		$template		= "templates/inner_edit_blog.htm";
		$TBS 			= new clsTinyButStrong;
		$TBS->NoErr 	= true;
		$TBS->LoadTemplate("$template");
		$TBS->Render 	= TBS_OUTPUT;
		$TBS->tbs_show();
		@mysql_close();
		die();

    	} else {

		if ( $proceed == true ) {
			$show_notification	= 1;
			$message			= $config['error_25'];				//success
			$tags 			= $safe_tags;
			$title			= mysql_real_escape_string($title);
			$description 		= mysql_real_escape_string($description);
			$channel 			= mysql_real_escape_string($channel);
			$category_id 		= mysql_real_escape_string($category_id);
   			$allow_replies 		= mysql_real_escape_string($allow_replies);
    			$allow_ratings 		= mysql_real_escape_string($allow_ratings);
    			$public_private 		= mysql_real_escape_string($public_private);

    			$sql = "UPDATE blogs SET
    							title 		= '$title',
    							title_seo 		= '$title_seo',
    							description 	= '$description',
    							blog_story 		= '$blog_story',
    							tags 			= '$tags',
    							category_id 	= '$category_id',
    							allow_replies 	= '$allow_replies',
    							allow_ratings 	= '$allow_ratings',
    							public_private	= '$public_private'
    							WHERE indexer = $id";

    			@mysql_query($sql);

			//display notification
			if( @mysql_error() ) {
				$show_notification	= 1;
				$message 			= $config['error_26']; //error
			} else {
				$show_notification	= 1;
				$message			= $config['error_25']; //success
				$show_success		= 1;
			}

		} else { // proceed = false

			// else => show they have an error
	      	$warning_close		= 'There was some problem with your input, please close this window and start over';
			$show_warning		= 1;
			$show_success		= 1;  // 1 = do not show fields or editor this show also say "Close this window and start over
			$error_message		= $tags_returned[1];
			$message			= $lang_error . " " . $error_message;
			$show_notification 	= 1;

			//$template		= "templates/inner_edit_blog.htm";
			//$TBS 			= new clsTinyButStrong;
			//$TBS->NoErr 	= true;
			//$TBS->LoadTemplate("$template");
			//$TBS->Render 	= TBS_OUTPUT;
			//$TBS->tbs_show();
			//@mysql_close();
			//die();
		}


	}


	// show success
	$template		= "templates/inner_edit_blog.htm";
	$TBS 			= new clsTinyButStrong;
	$TBS->NoErr 	= true;
	$TBS->LoadTemplate("$template");
	$TBS->Render 	= TBS_OUTPUT;
	$TBS->tbs_show();

	@mysql_close();
	die();

} else {

	//LOAD BLOG
	$sql			= "SELECT * FROM blogs WHERE indexer = $id";
	$query 		= @mysql_query($sql);
	$result 		= @mysql_fetch_array($query);
	$title 		= $result['title'];
	$description	= $result['description'];
	$blog_story 	= $result['blog_story'];
	$blog_story 	= htmlspecialchars_decode($blog_story);

	$tags 		= $result['tags'];
	$category 		= $result['category'];
	$allow_replies 	= 'allow_replies_' . $result['allow_replies'];
	$allow_ratings 	= 'allow_ratings_' . $result['allow_ratings'];
	$public_private 	= 'public_private_' . $result['public_private'];

	//get channel data, create "select" form fields to load into form
	$sql		= "SELECT * FROM blog_categories";
	$result1	= @mysql_query($sql);
	$fileds_all = '';

	while ($result = @mysql_fetch_array($result1)) {

		if ($result['category_name'] == $category) {
      		$selected = 'selected';
      	} else {
      		$selected = '';
		}

		$field = '<option value="' . $result['category_id'] . '" ' . $selected . ' >' . $result['category_name'] . '</option>';
		$fields_all = $fields_all . $field;

	}

	// set "selected" value for HTML pull down lists

	$allow_replies	= 'selected';
	$allow_ratings 	= 'selected';
	$public_private 	= 'selected';

	///FCK EDITOR________________________________________________________________________
	include('../fckeditor/fckeditor.php');

	$sBasePath 						= "$base_url/fckeditor/";
	$oFCKeditor 					= new FCKeditor('FCKeditor1');
	$oFCKeditor->BasePath 				= $sBasePath;
	$oFCKeditor->CustomConfigurationsPath 	= "fckconfig.js";
	$oFCKeditor->ToolbarSet 			= 'Basic';
	$oFCKeditor->SkinPath 				= $sBasePath + 'editor/skins/silver/';
	$oFCKeditor->Value  				= "$blog_story";
	$oFCKeditor->Width  				= '775';
	$oFCKeditor->Height 				= '250';
	$FCKeditor1 					= $oFCKeditor->CreateHtml();
	$my_edit 						= $FCKeditor1;
	//____________________________________________________________________________________

	//disply edit
	$template = "templates/inner_edit_blog.htm";//middle of page
	$TBS = new clsTinyButStrong;
	$TBS->NoErr = true;// no more error message displayed.
	$TBS->LoadTemplate("$template");
	$TBS->Render = TBS_OUTPUT;
	$TBS->tbs_show();

	@mysql_close();
	die();

}


?>