<?php
///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

//Dynamic Audio Menus
//////////////////////
if ($config['enabled_features_audio'] == 'yes') {

$audio_main_menu1					= '<li><a href="audios/load/recent">'.$lang_word_audios.'</a></li>';
$audio_main_menu_default         		= '<li><a href="myfriends.php">'.$config["word_friends"].'</a></li>';
$audio_sub_menu1                 		= '&nbsp;<a href="audio.php?load=mymusic">'.$config["word_my_music"].'</a>&nbsp;|';
$audio_sub_menu2	             		= '&nbsp;<a href="audio.php?load=myfavs">'.$config["favorites_audio"].'</a>&nbsp;&nbsp;|';
$audio_sub_menu3	             		= '&nbsp;|&nbsp;&nbsp;<a href="audio_uploader.php">'.$config["word_upload_audio"].'</a>';
$audio_inner_upload_video_form1  		= '<li><a href="audio_uploader.php">'.$config["word_upload_audio"].'</a></li>';
$audio_inner_upload_video_form2  		= '<li><a href="audio.php?load=mymusic">'.$config["word_my_music"].'</a></li>';
$image_inner_upload_form			= '<li><a href="image_uploader.php">'.$config["word_upload_image"].'</a></li>';
$audio_inner_welcome             		= '<li><span class="font4_16"><a href="audio.php?load=mymusic">'.$config["listen_to_my_music"].'</a></span></li>                                        <li><span class="font4_16"><a href="audio_uploader.php">'.$config["upload_manage_audio"].'</a></span></li>';

} else {

$audio_main_menu1					= $audio_main_menu_default; //"My Friends"

}

// Dynamic Images Menus
if ( $config['enabled_features_images'] == 'yes' ) {
	$images_main_menu1 = '<li><a href="albums/load/recent">'.$lang_word_images.'</a></li>';
}

// Dynamic Blogs Menus
if ( $config['enabled_features_blogs'] == 'yes' ){
	$blogs_main_menu1 = '<li><a href="blogs/load/recent">'.$lang_word_images.'</a></li>';
}


?>
