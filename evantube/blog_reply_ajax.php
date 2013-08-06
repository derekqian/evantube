<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once("classes/config.php");
include_once ('classes/sessions.php');

$referer	= mysql_real_escape_string($_SERVER['HTTP_REFERER']);

//CHECK IF FORM SUBMITTED

if($_POST['submitted'] == 'yes'){

	$blog_id = $_POST['blog_id'];
	$blog_reply = $_POST['blog_reply'];

	//check if blog_reply have been filled in
	if ($blog_reply == "") {
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

	$multiple_comments = $config["allow_multiple_video_comments"];

	if($multiple_comments == "no") {

		$user_id = mysql_real_escape_string($user_id);
		$blog_id = mysql_real_escape_string($blog_id);
		$sql = "SELECT * from blogs WHERE indexer = $blog_id AND by_id = $user_id";
		$query = mysql_query($sql);
		$result = mysql_num_rows($query);
		$comment_time = $result['todays_date'];

		if ($result != 0) {
			echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['video_comments_error_already'].'</b></font>';
			die();
		}
	}

	// comment flood control
	$user_id = mysql_real_escape_string($user_id);
	$user_name = mysql_real_escape_string($user_name);
	$blog_id = mysql_real_escape_string($blog_id);
	$blog_reply = mysql_real_escape_string($blog_reply);
	$blog_table = 'blog_replys';
	$id_name = 'blog_id';

	$proceed = flood_check ( $user_id, $comment_table, $id_name, $blog_id );
	if ( $proceed[0] == 'false' ) {
		echo $proceed[1];
		die();
	}

	//check if user allows blog replies
	$sql1 = "SELECT * FROM blogs WHERE indexer = $blog_id AND allow_comments = 'no'";
	$result1 = @mysql_query($sql1);
	if(@mysql_num_rows($result1) != 0){
		echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['video_comments_not_allowed'].'</b></font>';
		die();
	}

	// blog reply table  => by_id, by_username, blog_id, reply_body, todays_date

	$sql = "INSERT into blog_replys (by_id, by_username, blog_id, reply_body, todays_date) VALUES
                                  ($user_id, '$user_name', $blog_id, '$blog_reply', NOW())";

	//mysql_query($sql);
	$query = @mysql_query($sql);

	if(!$query) {
		die($config['error_26']);//mysql error
		@mysql_close();
		die();
	}

	// echo success message
	echo '<p align="center"><font color="#009933" face="Arial"><b>'.$config['video_comments_success'].'</b></font>';
	$comments = '';


      // sudo ajax refresh reply div
      usleep(500000);
	refresh_me( $referer );
	die();

} else {  //this is the get request

	// SHOW FORM

	//check if user is logged in
	if ($user_id == "") {
		echo '<div style="margin-left:12px; text-align:left;"><font color="#FF0000" face="Arial"><b>'.$config['video_comments_login'].'</b></font></div>';
		die();
	} else {

		$blog_id = $_GET['id'];
		$blog_id = mysql_real_escape_string($blog_id);
		$indexer = $_GET['indexer'];
		$indexer = mysql_real_escape_string($indexer);

	echo '<br /><div class="comment_reply" id="drop_block_'.$indexer.'">
		  <table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="'.$color.'">
		    <tr>
		      <td width="100%">
		        <span class="font5_14">Post a Reply</span>
		        <br />
		        <form id="blog_reply" action="javascript:ahahscript.likeSubmit(\'blog_reply_ajax.php\', \'post\', \'blog_reply\', \'drop_block_'.$indexer.'\');">
		          <textarea rows="3" name="blog_reply" cols="44"></textarea>
		          <input type="hidden" value="'.$blog_id.'" name="blog_id" />
		          <input type="hidden" value="yes" name="submitted" />
		          <input type="hidden" value="'.$indexer.'" name="indexer" />
		          <br />
		          <input type="submit" value="Post Reply" name="B3" />
		          &nbsp;&nbsp;
		          <button id="exit"  name="cancel" value="cancel" onclick="javascript:closeBlock(\'drop_block_'.$indexer.'\'); return false">Cancel</button>
		        </form>
		      </td>
		    </tr>
		  </table>
		</div><br />';

	}
}


//////////////////////////////////////////////////////////////////////////////////////////////
// functions - could move to functions.inc.php \\??//


function close_form() {
	echo '<div style="display:none;" class="comment_reply" id="drop_block_[blk3.indexer]"></div>';
}



?>