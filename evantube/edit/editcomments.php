<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once('../classes/config.php');
include_once('../siteadmin/includes/inc.stats.php');
include_once('../siteadmin/includes/functions.php');
include_once('../classes/permissions.php');

// odd bug - for some reason this form comments length is shorter than comments.php
$config['comments_length'] = 11; // ajusted for ascii chrs == posted comment - letters - chars length == 4

//__________________________________________________________________________________________________
/* Edit Code "Type" (as used in HTML Links)_________________________________________________________

1 = videocomments              //tested 29 Oct 2008
2 = videocomments_replys       //tested 29 Oct 2008
3 = audiocomments              //tested 29 Oct 2008
4 = audiocomments_replys       //tested 29 Oct 2008
5 = blog_replys (blog_replys)  //tested 29 Oct 2008
6 = imagecomments              //tested 29 Oct 2008
7 = imagecomments_replys       //tested 29 Oct 2008
8 = profilecomments
9 = group_comments
*/


// Some basic presets
$show_notification =0;

//Get query
$commentid = (int) mysql_real_escape_string($_GET['id']);
$type = mysql_real_escape_string($_GET['type']);

if( isset($_POST['submit']) ) {
	$commentid 	= (int) mysql_real_escape_string($_POST['id']);
	$type		= (int)mysql_real_escape_string($_POST['type']);
	$comment 	= @mysql_real_escape_string($_POST['FCKeditor1']);
}

// check if ID's are in the requesting url
if ($commentid == '' || $type =='') {
    ErrorDisplay1($config["invalid_request"]);
    die();
}


//____________________________________________________________________________________________________________
//____________Build SQL QUERY_________________________________________________________________________________


switch($type){

case 1:
$sql = "UPDATE videocomments SET comments = '$comment' WHERE indexer = $commentid";
$sql2= "SELECT * FROM videocomments WHERE indexer = $commentid";
$field = 'comments';
$checktype = 'video_comments';
break;

case 2:
$sql = "UPDATE videocomments_replys SET comment_reply = '$comment' WHERE indexer = $commentid";
$sql2= "SELECT * FROM videocomments_replys WHERE indexer = $commentid";
$field = 'comment_reply';
$checktype = 'videocomments_replys';
break;

case 3:
$sql = "UPDATE audiocomments SET comments = '$comment' WHERE indexer = $commentid";
$sql2= "SELECT * FROM audiocomments WHERE indexer = $commentid";
$field = 'comments';
$checktype = 'audio_comments';
break;

case 4:
$sql = "UPDATE audiocomments_replys SET comment_reply = '$comment' WHERE indexer = $commentid";
$sql2= "SELECT * FROM audiocomments_replys WHERE indexer = $commentid";
$field = 'comment_reply';
$checktype = 'audio_comments_replies';
break;

case 5:
$sql = "UPDATE blog_replys SET reply_body = '$comment' WHERE indexer = $commentid"; //blog replies
$sql2= "SELECT * FROM blog_replys WHERE indexer = $commentid";
$field = 'reply_body';
$checktype = 'blog_comments';
break;

case 6:
$sql = "UPDATE imagecomments SET comments = '$comment' WHERE indexer = $commentid";
$sql2= "SELECT * FROM imagecomments WHERE indexer = $commentid";
$field = 'comments';
$checktype = 'imagecomments';
break;

case 7:
$sql = "UPDATE imagecomments_replys SET comment_reply = '$comment' WHERE indexer = $commentid";
$sql2= "SELECT * FROM imagecomments_replys WHERE indexer = $commentid";
$field = 'comment_reply';
$checktype = 'imagecomments_replys';
break;

case 8:
$sql = "UPDATE profilecomments SET comments = '$comment' WHERE indexer = $commentid";
$sql2= "SELECT * FROM profilecomments WHERE indexer = $commentid";
$field = 'comments';
$checktype = 'profile_comments';
break;


case 9:
$sql = "UPDATE group_comments SET comments = '$comment' WHERE indexer = $commentid";
$sql2= "SELECT * FROM group_comments WHERE indexer = $commentid";
$field = 'comments';
$checktype = 'group_comments';
break;

}


//______PERMISSIONS CHECK ________________________________________________________________________________
$usercheck = new LoadPermissions('',$commentid,$checktype);
$usercheck->CheckPermissions();
$usercheck->ActionsCheck('edit');
//________________________________________________________________________________________________________



//____________________________________________________________________________________________________________
//____________UPDATE COMMENTS_________________________________________________________________________________

$proceed = true;

if(isset($_POST['submit'])){

	//Fill in all fields
	if ($comment == '') {
		$show_notification = 1;
		$message = $config['fill_all_fields'];
		$proceed = false;
	}

	if ( strlen($comment) < $config['comments_length'] ) {
		//echo "posted length " . strlen($comment) . " config length setting " . $config['comments_length'];
		$show_notification = 1;
		$message = "
		<font color=\"#FF4242\" face=\"Arial\" size=\"3\"><b>".$config['video_comments_error_length']."! - Comment minimum length 4 chars.</b></font>";

		$proceed = false;

	} else {

		//update existing article
		@mysql_query($sql);
	}

	//display notification
	if(@mysql_error()) {

		$show_notification = 1;
		$message = $config["error_26"]; //error

	} else {

		if ( $proceed != false ) {
			$show_notification = 1;
			$message = $config["error_25"]; //success
		}
	}
}

//____________________________________________________________________________________________________________
//____________LOAD COMMENT____________________________________________________________________________________


if($commentid != '') {
$query = @mysql_query($sql2);
$result = @mysql_fetch_array($query);
$comment = $result["$field"];
$comment = htmlspecialchars_decode($comment);

}

if(isset($_POST['submit'])){
//get form post (no mysql_real_escapE)
$comment = $_POST['FCKeditor1'];
}

///FCK EDITOR________________________________________________________________________
    	include('../fckeditor/fckeditor.php');

    	$sBasePath 						= "$base_url/fckeditor/";
    	$oFCKeditor 					= new FCKeditor('FCKeditor1');
    	$oFCKeditor->BasePath 				= $sBasePath;
    	$oFCKeditor->CustomConfigurationsPath 	= "fckconfig.js";
    	$oFCKeditor->ToolbarSet 			= 'Basic';
    	$oFCKeditor->SkinPath 				= $sBasePath + 'editor/skins/silver/';

    	$oFCKeditor->Value  				= $comment;
    	$oFCKeditor->Width  				= '600';
    	$oFCKeditor->Height 				= '220';
    	$FCKeditor1 					= $oFCKeditor->CreateHtml();
    	$my_edit 						= $FCKeditor1;
//____________________________________________________________________________________

////////////////////////////////
//display form with error message
////////////////////////////////
$template = "templates/inner_edit_comments.htm";//middle of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->Render = TBS_OUTPUT;
$TBS->tbs_show();
@mysql_close();
die();

?>