<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once ('classes/config.php');
include_once ('classes/permissions.php');
include_once ('includes/enabled_features.php');

$small_block_background = $config['color_tellafriend'];

$referer	= mysql_real_escape_string($_SERVER['HTTP_REFERER']);
if ( $referer == "" ) header("Location: " . "index.php");

$wrap_limit 		= 80;
$audio 			= mysql_real_escape_string( $_GET['audio'] );
$limit 			= $config['comment_page_limits'];

//we need a total of text comments before LIMIT query!!
$text_comments	= 0;
$sql_count		= "SELECT audio_id FROM audiocomments WHERE audio_id = $audio";
$num_query 		= @mysql_query($sql_count);
$text_comments 	= mysql_num_rows($num_query);


// ajax pagination reuseable
$page = $_GET['page'];
$page = mysql_real_escape_string($page);

if(empty($page)) {
	$page = 1;
}

$limit				= $config['comment_page_limits'];
$limit_value 			= $page * $limit - ($limit);
$totalrows				= $text_comments;
$config['ajax_page_previous']	= $config['pagination_previous'];
$config['ajax_page_next'] 	= $config['pagination_next'];

// PREVIOUS
if($page != 1) {
	$pageprev = ($page - 1);
	$ajax_page_previous = $config['ajax_page_previous'];

} else {
	// no previous link
	$ajax_page_previous = ''; //$config['ajax_page_previous'];
}

// NEXT
$next_page = $totalrows - ($limit * $page);

if( ($totalrows - ($limit * $page) ) > 0) {
	$pagenext = ($page + 1);
	$ajax_page_next = "&nbsp;&nbsp;" . $config['ajax_page_next'];

} else {
	// no link
	$ajax_page_next = '';
}

$result	= array();
$sql 		= "SELECT * FROM audiocomments WHERE audio_id = $audio ORDER BY indexer DESC LIMIT $limit_value, $limit";
$query 	= @mysql_query($sql);

$see_limit 	= ($limit * $page);

if ( $see_limit > $totalrows ) $see_limit = $totalrows;


//create new array with "wrapped" comments
while ($result = @mysql_fetch_array($query)) {

	//get commentors picture (added 01-01-2008)
	$poster_id	= $result['by_id'];
	$a1_sql 	= "SELECT * FROM pictures WHERE user_id = $poster_id";
	$a1_result 	= @mysql_query($a1_sql);

	if (@mysql_num_rows($a1_result) != 0) {
		$a1_result = @mysql_fetch_array($a1_result);
		$a1_existing_file = $a1_result['file_name'];
		$a1_approved = $a1_result['approved'];

		if ($a1_approved == "yes") {
			// show picture and "change picture link"
			$poster_picture = $config['site_base_url'] . '/pictures/' . $a1_existing_file;
		} else {
			// show place holder or blank (default is blank)- uncomment or comment the option you want
			$poster_picture = $config['site_base_url'] . "/themes/$user_theme/images/placeholder.gif";;
		}
	} else {
		// show place holder or blank (default is blank)- uncomment or comment the option you want
		$poster_picture = $config['site_base_url'] . "/themes/$user_theme/images/placeholder.gif";;
	}


	$text		= $result['comments'];
	$wrap 	= wordwrap($text, $wrap_limit, " ", true);
	$wrap 	= htmlspecialchars_decode($wrap);
	$indexer_id = $result['indexer'];

	$drop_block = "<div id=\"drop_block_$indexer_id\"></div>";

      //______________________________________________________________________________________________
	//______PERMISSIONS CHECK COMMENTS______________________________________________________________
    	$usercheck = new LoadPermissions('', $indexer_id, 'audio_comments');
    	$usercheck->CheckPermissions();
    	$audio_comment_edit_on = $usercheck->ReturnVars('edit_on');
    	$audio_comment_delete_on = $usercheck->ReturnVars('delete_on');

    	//Edit link
    	if($audio_comment_edit_on == 1){
    	$audio_comment_edit_on = '<a href="edit/editcomments.php?type=3&id='.$indexer_id.'" title="Edit" onclick="return parent.GB_show(\'Edit Comment\', this.href, 400, 700)">
        <img src="'.$base_url.'/themes/'.$user_theme.'/images/icons/edit_small.png" alt="'.$lang_edit.'" width="14" height="14" border="0" />
         '.$lang_edit.'</a>';
    	}else{
    	$audio_comment_edit_on = '';
    	}

    	//Delet link
    	if($audio_comment_delete_on ==1){
    	$audio_comment_delete_on = '<a href="edit/delete.php?type=8&id='.$indexer_id.'" title="Delete" onclick="return parent.GB_show(\'Delete Comment\', this.href, 300, 700)">
        <img src="'.$base_url.'/themes/'.$user_theme.'/images/icons/delete_small.png" alt="'.$lang_delete_general.'" width="14" height="14"
		border="0" /> '.$lang_delete_general.'</a>';
    	}else{
    	$audio_comment_delete_on = '';
    	}

    	//_______________________________________________________________________________________________


	// we need to query audiocomments_replys to find any REPLIES

	$each_reply = array();

	$sql_replies = "SELECT * FROM audiocomments_replys WHERE audiocomment_id = $indexer_id ORDER BY indexer DESC"; // LIMIT $limit_value, $limit";

	$replies = @mysql_query($sql_replies);
	$replies_count = mysql_num_rows($replies);
	$reply_wrap = array();
	$by_username = array();
	$reply_date = array();

	$reply_block = "";


	while ($result_replies = @mysql_fetch_array($replies)) {
		$reply_text 	= $result_replies['comment_reply'];

		/* future use
		$reply_text 	= to_bbc_code($reply_text);
		$reply_text		= htmlspecialchars_decode($reply_text);
		*/

		$reply_wrap 	= wordwrap($reply_text, $wrap_limit, " ", true);
		$by_username 	= $result_replies['by_username'];
		$db_date		= $result_replies['todays_date'];
		$reply_date		= date($config["date_format"], strtotime($db_date));
		$reply_id		= $result_replies['indexer'];

      //______________________________________________________________________________________________
	//______PERMISSIONS CHECK COMMENTS______________________________________________________________
    	$usercheck = new LoadPermissions('', $reply_id, 'audio_comments_replies');
    	$usercheck->CheckPermissions();
    	$reply_edit_on = $usercheck->ReturnVars('edit_on');
    	$reply_delete_on = $usercheck->ReturnVars('delete_on');

		if ( $reply_delete_on == 1 ) {
			$delete_reply_on = "<div style=\"margin-right:12px; float:right;\">
			  <a href=\"edit/delete.php?type=15&id=$reply_id\" title=\"$lang_delete_general\" onclick=\"return parent.GB_show('Delete Reply', this.href, 350, 550)\">
              <img src=\"$base_url/themes/$user_theme/images/icons/delete_small.png\" alt=\"$lang_delete_general\" width=\"14\" height=\"14\" border=\"0\" />
			  $lang_delete_general</a>
			</div>\n";
		} else {
			$delete_reply_on = "<div style=\"margin-right:12px; float:right;\">&nbsp;</div>\n";;
		}

		if ( $reply_edit_on == 1 ) {
			$edit_reply_on = "<div style=\"margin-right:12px; float:right;\">
			  <a href=\"edit/editcomments.php?type=4&id=$reply_id\" title=\"$lang_edit\" onclick=\"return parent.GB_show('Edit Reply', this.href, 350, 550)\">
              <img src=\"$base_url/themes/$user_theme/images/icons/edit_small.png\" alt=\"$lang_delete_general\" width=\"14\" height=\"14\" border=\"0\" />
			  $lang_edit</a>
			</div>\n";
		} else {
			$edit_reply_on = "<div style=\"margin-right:12px; float:right;\">&nbsp;</div>\n";
		}
        //_______________________________________________________________________________________________________________


		$reply_block .= "<hr>
					<div id=\"reply_block_$indexer_id\" style=\"background: $small_block_background\">
					  <div>
					    <div style=\"margin-left:12px; float:left;\">$lang_reply_by:&nbsp;<b>$by_username</b></div>
					    <div style=\"margin-left:42px; float:left;\"><b>$lang_date:&nbsp;</b>$reply_date</div>
					  </div>
					  <div>";

		$reply_block .= $delete_reply_on . $edit_reply_on;

		$reply_block .="</div>
					  <br />
					  <br />
					  <div style=\"margin-left:32px; float:left;\">$reply_wrap</div>
					  <br />
					  <br />
					</div>";
		}


		$each_comment = array('by_id'				=>$result['by_id'],
					    'indexer'			=>$result['indexer'],
					    'comments'			=>$wrap,
					    'todays_date'			=>$result['todays_date'],
					    'by_username'			=>$result['by_username'],
					    'poster_picture'		=>$poster_picture,
					    'rating'			=>$result['updated_rating'],
					    'audio_comment_edit'	=>$audio_comment_edit_on,
					    'audio_comment_delete'	=>$audio_comment_delete_on,
					    'drop_block'			=>$drop_block,
					    'reply_block'			=>$reply_block);

      	$result_search3[] = $each_comment;
}

//print_r($result_search3);

//set condition for hidding certain blocks (e.g "no emails to list")
if (empty($result)) {
    $show_c = 1;
}
else {
    $show_c = 2;
}

//$div_style_height = ($limit + $replies_count) * 200;

$template = "themes/$user_theme/templates/inner_comments_audio_ajax.htm";

$TBS = new clsTinyButStrong;
$TBS->NoErr = true;

$TBS->LoadTemplate("$template");
$TBS->MergeBlock('blk3', $result_search3);
$TBS->Render = TBS_OUTPUT;
$TBS->tbs_show();
die();

?>