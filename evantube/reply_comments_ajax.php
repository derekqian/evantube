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

$color 	= $config['color_tellafriend'];

$referer	= mysql_real_escape_string($_SERVER['HTTP_REFERER']);

//if ( $referer == "" ) header("Location: " . "index.php");

$reply_type	= mysql_real_escape_string($_GET['type']);

$media_id	= (int) mysql_real_escape_string($_GET['media_id']);

switch ( $reply_type ) {

	case 'video':
		$table_replys 	= 'videocomments_replys';
		$table_cmts		= 'videocomments';
		$media_table	= 'videos';
	break;

	case 'audio':
		$table_replys 	= 'audiocomments_replys';
		$table_cmts		= 'audiocomments';
		$media_table	= 'audios';
	break;

	case 'image':
		$table_replys 	= 'imagecomments_replys';
		$table_cmts		= 'imagecomments';
		$media_table	= 'images';
	break;

	case 'profile':
		$table_replys	= 'profilecomments_replys';
		$table_cmts		= 'profilecomments';
		$media_table	= 'member_profile';
	break;
}

//CHECK IF FORM SUBMITTED
if($_POST['submitted'] == 'yes'){

	$comment_id		= $_POST['comment_id'];
	$comment_reply 	= $_POST['comment_reply'];
	$reply_type		= $_POST['reply_type'];
	$media_id		= $_POST['media_id'];


	switch ( $reply_type ) {

	case 'video':
		$table_replys 	= 'videocomments_replys';
		$table_cmts		= 'videocomments';
		$media_table	= 'videos';
	break;

	case 'audio':
		$table_replys 	= 'audiocomments_replys';
		$table_cmts		= 'audiocomments';
		$media_table	= 'audios';
	break;

	case 'image':
		$table_replys 	= 'imagecomments_replys';
		$table_cmts		= 'imagecomments';
		$media_table	= 'images';
	break;

	case 'profile':
		$table_replys	= 'profilecomments_replys';
		$table_cmts		= 'profilecomments';
		$media_table	= 'member_profile';
	break;
}


	//check if comment_reply have been filled in
	if ($comment_reply == "") {
		echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['video_comments_error_comment'].'</b></font>';
		die();
	}

	//check if user is logged in
	if ($user_id == "") {
		echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['video_comments_login'].'</b></font>';
		die();
	}

	//check if users are allowed multiple posts
	//if not, check if user has already posted

	$multiple_comments = $config['allow_multiple_video_comments'];

	if( $multiple_comments == 'no' ) {

		$user_id		= mysql_real_escape_string($user_id);
		$sql 			= "SELECT * from $table_replys WHERE $reply_type.'comment_id' = $media_id AND by_id = $user_id";
		$query		= mysql_query($sql);
		$result 		= mysql_num_rows($query);
		$comment_time	= $result['todays_date'];

		if ($result != 0) {
			echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['video_comments_error_already'].'</b></font>';
			die();
		}
	}

	// comment flood control
	$user_id		= mysql_real_escape_string($user_id);
	$user_name 		= mysql_real_escape_string($user_name);
	$comment_id		= mysql_real_escape_string($comment_id);
	$comment_reply 	= mysql_real_escape_string($comment_reply);
	$reply_type 	= mysql_real_escape_string($reply_type);
	$comment_table 	= $table_replys;
	$id_name 		= $reply_type . 'comment_id';

	$proceed = flood_check ( $user_id, $comment_table, $id_name, $comment_id );
	if ( $proceed[0] == 'false' ) {
		echo $proceed[1];
		die();
	}

	//check if user allows comments
	$sql1 = "SELECT * FROM $media_table WHERE indexer = $media_id AND allow_comments = 'no'";
	$result1 = @mysql_query($sql1);

	if(@mysql_num_rows($result1) != 0){
		echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['video_comments_not_allowed'].'</b></font>';
		die();
	}

	$sql = "INSERT into $table_replys (by_id, by_username, $id_name, comment_reply, todays_date)
					    VALUES('$user_id', '$user_name', '$comment_id', '$comment_reply', NOW() )";

	$query_add = @mysql_query($sql);

	if(!$query_add) {
		die($config['error_26']);
		@mysql_close();
		die();
	}


	// echo success message
	echo '<p align="center"><font color="#009933" face="Arial"><b>'.$config['video_comments_success'].'</b></font>';

	$comments = "";

	die();

} else {  //this is the get request

	// SHOW FORM
	//check if user is logged in

	if ($user_id == "") {
		echo '<p align="center"><font color="#FF0000" face="Arial"><b>'.$config['video_comments_login'].'</b></font>';
		die();

	} else {

		$comment_id = $_GET['id'];
		$comment_id = mysql_real_escape_string($comment_id);
		$indexer = $_GET['indexer'];
		$indexer = mysql_real_escape_string($indexer);


	echo '<div class="comment_reply" id="drop_block_'.$indexer.'">
		  <table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="'.$color.'" height="64">
		    <tr>
		      <td width="100%">
		        <span class="font5_14">'.$lang_post_reply_comment.'</span>
		        <br />
		        <form id="replycommentsform" action="javascript:ahahscript.likeSubmit(\'reply_comments_ajax.php\', \'post\', \'replycommentsform\', \'drop_block_'.$indexer.'\');">
		          <textarea rows="3" name="comment_reply" cols="44"></textarea>
		          <input type="hidden" value="'.$comment_id.'" name="comment_id" />
		          <input type="hidden" value="yes" name="submitted" />
		          <input type="hidden" value="'.$indexer.'" name="indexer" />
		          <input type="hidden" value="'.$reply_type.'" name="reply_type" />
		          <input type="hidden" value="'.$media_id.'" name="media_id" />
		          <br />
		          <input type="submit" value="'.$lang_submit.'" name="B3" />
		          &nbsp;&nbsp;
		          <button id="exit" name="cancel" value="'.$lang_cancel.'" onclick="javascript:closeBlock(\'drop_block_'.$indexer.'\');">'.$lang_cancel.'</button>
		        </form>
		      </td>
		    </tr>
		  </table>
		</div>';
	}
}


function close_form() {
	echo '<div class="comment_reply" id="drop_block_[blk3.indexer]"></div>';
}

?>