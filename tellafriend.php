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

// define access for loading non display php files - e.g. postage.php, inc_my_picture.php etc...
define('access',true);

$referer = mysql_real_escape_string($_SERVER['HTTP_REFERER']);
if ( $referer == '' ) header("Location: " . "index.php");

$color 	= $config['color_tellafriend'];
$word_send	= $config['word_send'];
$vid		= (int) mysql_real_escape_string( $_GET['vid'] );
$audio	= (int) mysql_real_escape_string( $_GET['audio'] );
$image 	= (int) mysql_real_escape_string( $_GET['image'] );
$media_type = mysql_real_escape_string( $_GET['type'] );
$title_seo	= mysql_real_escape_string( $_GET['title_seo'] );


switch ( $media_type ) {
	case 'video':
		if ( $vid == '' ) {
			echo "<center><h2>".$config['error_24']."</h2></center>";
			die();
		} else {
			$media_id = $vid;
			$media_link = "/videos/$vid/$title_seo";
		}
	break;
	case 'audio':
		if ( $audio == '' ) {
			echo "<center><h2>".$config['error_24']."</h2></center>";
			die();
		} else {
			$media_id = $audio;
			$media_link = "/audio/$audio/$title_seo";
		}
	break;
	case 'image':
		if ( $image == '' ) {
			echo "<center><h2>".$config['error_24']."</h2></center>";
			die();
		} else {
			$media_id = $image;
			$media_link = "/view-image/$image/$title_seo";
		}
	break;
}

// check if user is logged in
if ( $user_id == "" ) {
	echo '<p align="center"><font color="#FF4242" face="Arial" size="2"><b>'.$config['must_login'].'</b></font>';
	die();
}

// CHECK IF FORM SUBMITTED

if ( $_POST['submitted'] == 'yes' ) {

	$friends_email 	= mysql_real_escape_string( $_POST['friends_email'] );
	$media_link		= mysql_real_escape_string( $_POST['media_link'] );

	//check if email is filled in
	if ( $friends_email == '' ) {
		echo '&nbsp;';
		$form_error = '<p align="center"><font color="#FF0000">'.$config["invite_send_error"].'</font></p>';
		show_form();
	}

	//check if email is valid
	if (!eregi("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-z]{2,3})$", $friends_email)) {

		//SHOW FORM WITH ERRROR
		echo '&nbsp;';
		$form_error = '<p align="center"><font color="#FF0000">'.$config['invalid_email_format'].'</font></p>';
		show_form();
	}

	//get members (me) real name for use in email
      $sql_1 		= "SELECT * FROM member_profile WHERE user_id = $user_id";
      $result_1 		= @mysql_fetch_array(@mysql_query($sql_1));
      $my_real_name	= $result_1['first_name'];

      //if the member has not yet updated their profile to show real name then use their username and email instead
      if ($my_real_name == "") $my_real_name = $user_name . ' (' . $result_1['email_address'] . ')';

      // MAKE THIS A DYNAMIC MEDIA TYPE / LINK

      //$video_link = $config["site_base_url"].'/play.php?vid='.$vid;

      $video_link = $config["site_base_url"].$media_link;

      // send email
      $email_template	= 'email_templates/tellafriend.htm';
      $subject 		= $config["tellafriend_email_subject"];
      $to 			= $friends_email;
      $from 		= $config['notifications_from_email'];

      //send email template to TBS for rendering of variable inside
      $template	= $email_template;
      $TBS 		= new clsTinyButStrong;
      $TBS->NoErr = true;// no more error message displayed.

      $TBS->LoadTemplate("$template");
      $TBS->tbs_show(TBS_NOTHING);

      $message	= $TBS->Source;

      //load postage.php
      include ('includes/postage.php');

      echo '<p align="center"><font color="#009933" face="Arial" size="2"><b>'.$config["error_25"].'</b></font>';
      die();

}

// SHOW FORM
echo '&nbsp;'; //seems ajax function wont work without some ourput first ??

show_form();

//Functions
function show_form() {

	global $color, $word_send, $form_error, $media_link, $media_id, $lang_friends_email;

	echo '<div align="center">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="'.$color.'" height="64">
    		<tr>
      	<td width="100%" height="64">
  		<form id="tellafriend" action="javascript:ahahscript.likeSubmit(\'tellafriend.php\', \'post\', \'tellafriend\', \'playlist_ajaxdiv\');">
  		'.$form_error.'

  		<p align="center">'.$lang_friends_email.':&nbsp;<input type="text" name="friends_email" size="38">
  		&nbsp;
  		<input type="hidden" name="submitted" value="yes" />
  		<input type="hidden" name="media_link" value="'.$media_link.'" />
  		<input type="hidden" name="media_id" value="'.$media_id.'" />
  		<input type="submit" value="'.$word_send.'" name="submit"></p>
  		</form>
  		</td>
  		</tr>
  		</table>
  		</div>';

	return true;
	die();
}

?>