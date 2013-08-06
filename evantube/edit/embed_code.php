<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once ('../classes/config.php');

$flag_stop	= 0;
$referer	= mysql_real_escape_string($_SERVER['HTTP_REFERER']);

if ( $referer == '' ) header("Location: $base_url/index.php");
if ( !ereg ($_SERVER['SERVER_NAME'], $referer) ) $flag_stop++;
if ( !ereg ($base_url, $referer) ) $flag_stop++;
if ( $flag_stop == 2 ) header("Location: $base_url/index.php");

$vid			= (int) mysql_real_escape_string( $_GET['vid'] );
$form_submitted 	= $_POST['submitted'];
$embed_skin		= $config['jw_player_default_skin'];

if ( $form_submitted == 'yes' ) {

	$size 		= mysql_real_escape_string( $_POST['size'] );
	$wmode		= mysql_real_escape_string( $_POST['wmode'] );
	$bgcolor		= mysql_real_escape_string( $_POST['bgcolor'] );
	$player		= mysql_real_escape_string( $_POST['player'] );
	$fullscreen		= mysql_real_escape_string( $_POST['fullscreen'] );
	$overstretch	= mysql_real_escape_string( $_POST['overstretch'] );
	$autoplay		= mysql_real_escape_string( $_POST['autoplay'] );
	$showlogo		= mysql_real_escape_string( $_POST['showlogo'] );

	$video_id		= (int) mysql_real_escape_string( $_POST['vid'] );
	$custom_w		= (int) mysql_real_escape_string( $_POST['custom_width'] );
	$custom_h		= (int) mysql_real_escape_string( $_POST['custom_height'] );

	$sql			= "SELECT indexer, video_id, public_private, title_seo FROM videos WHERE indexer = $video_id AND approved = 'yes'";
	$result 		= @mysql_query($sql);
	$row			= @mysql_fetch_array($result);

	$public_private	= $row['public_private'];
	$video_play		= $row['video_id'] . '.flv';
  	$thumb_file		= $base_url . '/uploads/player_thumbs/' . $row['video_id'] . '.jpg';
  	$click_link		= $base_url . '/videos/' . $row['indexer'] . '/' . $row['title_seo'];

  	// perhaps this could be a config setting
  	$linktarget		= '_self';

	$logopath		= $base_url . '/images/playerlogos/logo-player.png';
	$player_skin	= $base_url . '/skins/' . $embed_skin;
   	$procede		= true;
	$show_form		= 0;
	$show_code		= 1;

	if ( $size == 'custom' ) {
		$width	= $custom_w;
		$height	= $custom_h;

	} else {
		list($width, $height) = split('x', $size);
	}

	if ( $public_private == 'private' ) $procede = false;

	if ( $fullscreen == 'true' ) $show_f_s = 'true';


	if ( $overstretch == 'true' ) $overstretch = 'stretch'; else $overstretch = 'fit';

	if ( $procede == false ) {
		echo '<br /><br /><center><h2><font color="#EE0000">Sorry, private videos can not be embeded !</font></h2></center>';

	} else {

		$embed_code	='<embed src="'.$base_url.'/player.swf" width="'.$width.'" height="'.$height.'" bgcolor="'.$bgcolor.'" allowscriptaccess="always" allowfullscreen="'.$fullscreen.'" flashvars="flvsource='.$base_url.'/uploads/'.$video_play.'&amp;preview_image='.$thumb_file.'&amp;backgcolor='.$bgcolor.'&amp;autoplay='.$autoplay.'&amp;url_logo='.$logopath.'&amp;logo=top_left&amp;floating_navbar=false&amp;color_nav_bar_top=0x478dc2&amp;color_nav_bar_bottom=0xE7EBEC&amp;ads_background_color=0x00CCFF&amp;ads_border_color=0xCCCCCC&amp;scrubber_position_color=0x6AA1CE&amp;scrubber_load_color=0x888888&amp;scrubber_background_color=0xBBBBBB&amp;volume_bar_color=0xBBBBBB&amp;aspect_ratio='.$overstretch.'"></embed>';

		$template	= "templates/inner_embed_code.htm";
		$TBS 		= new clsTinyButStrong;
		$TBS->NoErr	= true;
		$TBS->LoadTemplate("$template");
		$TBS->Render = TBS_OUTPUT;
		$TBS->Show();
		die();
	}

} else {

	$embed_code	= '';
	$show_form	= 1;
	$show_code	= 0;

	$template		= "templates/inner_embed_code.htm";
	$TBS			= new clsTinyButStrong;
	$TBS->NoErr 	= true;

	$TBS->LoadTemplate("$template");
	$TBS->Render	= TBS_OUTPUT;
	$TBS->Show();

	die();
}


?>