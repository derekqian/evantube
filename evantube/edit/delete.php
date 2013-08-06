<?php


///////////////////////////////////////////////////////////////////////////////

// This file manages the deletion of content items (videos, images, friends etc)

///////////////////////////////////////////////////////////////////////////////

//____________________________________________________________________________________________________
/* Delete Code "Type" (as used in HTML Links)_________________________________________________________

1  = Videos                             //tested 29 Oct 2008
2  = Images                             //tested 29 Oct 2008
3  = Blogs (actual blog)                //tested 29 Oct 2008
4  = Audio
5  = Blog Comments (blog replies)       //tested 29 Oct 2008
6  = Profile Comments
7  = Image Comments                     //tested 29 Oct 2008
8  = Audio Comments                     //tested 29 Oct 2008
10 = Image favourites                   //tested 18 Nov 2008
9  = Videos Favourite                   //tested 18 Nov 2008
12 = Audio Favourite                   //tested 18 Nov 2008
13 = Friends  //for member use only
14 = Video Comment replys               //tested 29 Oct 2008
15 = Audio Comment replys               //tested 29 Oct 2008
16 = Image Comment replys               //tested 29 Oct 2008
18 = Group comments
19  = Video Comments                    //tested 29 Oct 2008
*/


include_once('../classes/config.php');
include_once('../classes/permissions.php');
include_once('../classes/sessions.php');
include_once('../siteadmin/includes/functions.php'); //use the admins function for deleting content

// Some basic presets
$show_notification =0;
$show_table = 1;


//Get query
$contentid = mysql_real_escape_string($_GET['id']);
$type = mysql_real_escape_string($_GET['type']);

if(isset($_POST['submit'])){
$contentid = mysql_real_escape_string($_POST['id']);
$type = mysql_real_escape_string($_POST['type']);
}

// check if ID's are in the requesting url
if ($contentid == '' || $type =='') {
    ErrorDisplay1($config["invalid_request"]);
    die();
}


//__________________________________________________________________________________________________________________
//________Check type and take action________________________________________________________________________________

if(isset($_POST['submit'])){
$show_table = ''; //so no table/submit form is shown after this process
switch($type){

//Videos
case 1:
$usercheck = new LoadPermissions('',$contentid,'videos');
$usercheck->CheckPermissions();
$usercheck->ActionsCheck('delete');
managevideo($contentid,'delete');
break;

//Images
case 2:
$usercheck = new LoadPermissions('',$contentid,'images');
$usercheck->CheckPermissions();
$usercheck->ActionsCheck('delete');
manageimages($contentid,'delete');
break;

//Blogs (actual blog)
case 3:
$usercheck = new LoadPermissions('',$contentid,'blogs');
$usercheck->CheckPermissions();
$usercheck->ActionsCheck('delete');
manageblogs($contentid,'delete');
break;

//Audio
case 4:
$usercheck = new LoadPermissions('',$contentid,'audios');
$usercheck->CheckPermissions();
$usercheck->ActionsCheck('delete');
manageaudio($contentid,'delete');
break;

//Blog replies
case 5:
$usercheck = new LoadPermissions('',$contentid,'blog_comments');
$usercheck->CheckPermissions();
$usercheck->ActionsCheck('delete');
$sql = "DELETE FROM blog_replys WHERE indexer = $contentid";
@mysql_query($sql);
break;

//profile comments
case 6:
$usercheck = new LoadPermissions('',$contentid,'profile_comments');
$usercheck->CheckPermissions();
$usercheck->ActionsCheck('delete');
manage_flagged_comments($contentid,'delete','profilecomments');
break;

//image comments
case 7:
$usercheck = new LoadPermissions('',$contentid,'imagecomments');
$usercheck->CheckPermissions();
$usercheck->ActionsCheck('delete');
manage_flagged_comments($contentid,'delete','imagecomments');
break;

//audio comments
case 8:
$usercheck = new LoadPermissions('',$contentid,'audio_comments');
$usercheck->CheckPermissions();
$usercheck->ActionsCheck('delete');
manage_flagged_comments($contentid,'delete','audiocomments');
break;


//Video favorites
case 9:
$sql = "DELETE FROM favorites WHERE video_id = $contentid AND user_id = $user_id";
@mysql_query($sql);
if(@mysql_error()){
ErrorDisplay1($config["invalid_request"]);
die();
}
break;

//Image favorites
case 10:
$sql = "DELETE FROM image_favorites WHERE image_id = $contentid AND user_id = $user_id";
@mysql_query($sql);
if(@mysql_error()){
ErrorDisplay1($config["invalid_request"]);
die();
}
break;

//Audio favorites
case 12:
$sql = "DELETE FROM audio_favorites WHERE audio_id = $contentid  AND user_id = $user_id";
@mysql_query($sql);
if(@mysql_error()){
ErrorDisplay1($config["invalid_request"]);
die();
}
break;



//Friends
case 13:
//check ownership
$sql = "SELECT indexer FROM friends WHERE user_id = $user_id AND friends_id = $contentid OR friends_id = $user_id AND user_id = $contentid";
if(@mysql_num_rows(@mysql_query($sql)) == 0){
ErrorDisplay1($config["invalid_request"]);
die();
}
//delete from friends part 1
$sql = "DELETE FROM friends WHERE user_id = $user_id AND friends_id = $contentid";
@mysql_query($sql);
//delete from friends part 2
$sql = "DELETE FROM friends WHERE friends_id = $user_id AND user_id = $contentid";
@mysql_query($sql);
break;


//video comment replys
case 14:
$usercheck = new LoadPermissions('',$contentid,'videocomments_replys');
$usercheck->CheckPermissions();
$usercheck->ActionsCheck('delete');
$sql = "DELETE FROM videocomments_replys WHERE indexer = $contentid";
@mysql_query($sql);
break;


//audio comment replys
case 15:
$usercheck = new LoadPermissions('',$contentid,'audio_comments_replies');
$usercheck->CheckPermissions();
$usercheck->ActionsCheck('delete');
$sql = "DELETE FROM audiocomments_replys WHERE indexer = $contentid";
@mysql_query($sql);
break;


//image comment replys
case 16:
$usercheck = new LoadPermissions('',$contentid,'imagecomments_replys');
$usercheck->CheckPermissions();
$usercheck->ActionsCheck('delete');
$sql = "DELETE FROM imagecomments_replys WHERE indexer = $contentid";
@mysql_query($sql);
break;

/* there is no group delete function for end users
//groups
case 17:
managegroups($contentid,'delete');
break;
*/

//group comments
case 18:
$usercheck = new LoadPermissions('',$contentid,'group_comments');
$usercheck->CheckPermissions();
$usercheck->ActionsCheck('delete');
manage_flagged_comments($contentid,'delete','group_comments');
break;


//Video comments
case 19:
$usercheck = new LoadPermissions('',$contentid,'video_comments');
$usercheck->CheckPermissions();
$usercheck->ActionsCheck('delete');
manage_flagged_comments($contentid,'delete','videocomments');
break;
}

//display notification
if(@mysql_error()){
$show_notification =1;
$message = $config["error_26"]; //error
}else{
$show_notification =1;
$message = $config["error_25"]; //success
}
}


////////////////////////////////
//display form with error message
////////////////////////////////
$template = "templates/inner_delete.htm";//middle of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->Render = TBS_OUTPUT;
$TBS->tbs_show();
@mysql_close();
die();

?>