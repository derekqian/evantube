<?php

////////////////
//Active Button
////////////////
for($count=1; $count<20; $count++){
$var = 'active_button'.$count;
$$var = '';
}

$active_button = 'active_button'.$type;
$$active_button = 'id="active_submenu_button"';

///////////////////
// TOP SUB MENUS //
///////////////////


//Videos main page
if ($top_menu == 'videos'){
$submenu_content = '
<li><div align="center"><a href="videos.php?type=1&action=0&sort=1"'.$active_button1.'>'.$lang_active.'</a></div></li>
<li><div align="center"><a href="videos.php?type=2&action=0&sort=1"'.$active_button2.'>'.$lang_pending.'</a></div></li>
<li><div align="center"><a href="videos.php?type=3&action=0&sort=1"'.$active_button3.'>'.$lang_featured.'</a></div></li>
<li><div align="center"><a href="videos.php?type=7&action=0&sort=1"'.$active_button7.'>'.$lang_promoted.'</a></div></li>
<li><div align="center"><a href="videos.php?type=5&action=0&sort=1"'.$active_button5.'>'.$lang_flagged.'</a></div></li>
<li><div align="center"><a href="videos.php?type=4&action=99&sort=1"'.$active_button4.'>'.$lang_search.'</a></div></li>';
}

//Videos main page
if ($top_menu == 'images'){
$submenu_content = '
<li><div align="center"><a href="images.php?type=1&action=0&sort=1"'.$active_button1.'>'.$lang_active.'</a></div></li>
<li><div align="center"><a href="images.php?type=2&action=0&sort=1"'.$active_button2.'>'.$lang_pending.'</a></div></li>
<li><div align="center"><a href="images.php?type=3&action=0&sort=1"'.$active_button3.'>'.$lang_featured.'</a></div></li>
<li><div align="center"><a href="images.php?type=5&action=0&sort=1"'.$active_button5.'>'.$lang_flagged.'</a></div></li>
<li><div align="center"><a href="images.php?type=4&action=99&sort=1"'.$active_button4.'>'.$lang_search.'</a></div></li>';
}

//Videos main page
if ($top_menu == 'audio'){
$submenu_content = '
<li><div align="center"><a href="audio.php?type=1&action=0&sort=1"'.$active_button1.'>'.$lang_active.'</a></div></li>
<li><div align="center"><a href="audio.php?type=2&action=0&sort=1"'.$active_button2.'>'.$lang_pending.'</a></div></li>
<li><div align="center"><a href="audio.php?type=3&action=0&sort=1"'.$active_button3.'>'.$lang_featured.'</a></div></li>
<li><div align="center"><a href="audio.php?type=5&action=0&sort=1"'.$active_button5.'>'.$lang_flagged.'</a></div></li>
<li><div align="center"><a href="audio.php?type=4&action=99&sort=1"'.$active_button4.'>'.$lang_search.'</a></div></li>';
}

if ($top_menu == 'blogs'){
$submenu_content = '
<li><div align="center"><a href="blogs.php?type=1&action=0&sort=1"'.$active_button1.'>'.$lang_active.'</a></div></li>
<li><div align="center"><a href="blogs.php?type=2&action=0&sort=1"'.$active_button2.'>'.$lang_pending.'</a></div></li>
<li><div align="center"><a href="blogs.php?type=3&action=0&sort=1"'.$active_button3.'>'.$lang_featured.'</a></div></li>
<li><div align="center"><a href="blogs.php?type=5&action=0&sort=1"'.$active_button5.'>'.$lang_flagged.'</a></div></li>
<li><div align="center"><a href="blogs.php?type=4&action=99&sort=1"'.$active_button4.'>'.$lang_search.'</a></div></li>';
}


if ($top_menu == 'flagged'){
$submenu_content = '
<li><div align="center"><a href="flagged_comments.php?type=1&action=0&sort=1"'.$active_button1.'>'.$lang_word_videos.'</a></div></li>
<li><div align="center"><a href="flagged_comments.php?type=2&action=0&sort=1"'.$active_button2.'>'.$lang_word_Audio.'</a></div></li>
<li><div align="center"><a href="flagged_comments.php?type=3&action=0&sort=1"'.$active_button3.'>'.$lang_blogs.'</a></div></li>
<li><div align="center"><a href="flagged_comments.php?type=4&action=0&sort=1"'.$active_button4.'>'.$lang_pictures.'</a></div></li>
<li><div align="center"><a href="flagged_comments.php?type=5&action=0&sort=1"'.$active_button5.'>'.$lang_groups.'</a></div></li>
<li><div align="center"><a href="flagged_comments.php?type=6&action=0&sort=1"'.$active_button6.'>'.$lang_profiles.'</a></div></li>';
}


//members menu
if ($top_menu == 'members'){
$submenu_content = '
<li><div align="center"><a href="members.php?type=1&action=0&sort="'.$active_button1.'>'.$lang_active.'</a></div></li>
<li><div align="center"><a href="members.php?type=2&action=0&sort="'.$active_button2.'>'.$lang_pending.'</a></div></li>
<li><div align="center"><a href="members.php?type=3&action=0&sort="'.$active_button3.'>'.$lang_suspended.'</a></div></li>
<li><div align="center"><a href="members.php?type=4&action=0&sort="'.$active_button4.'>'.$lang_all.'</a></div></li>
<li><div align="center"><a href="members.php?type=5&action=0&sort="'.$active_button5.'>'.$lang_moderators.'</a></div></li>
<li><div align="center"><a href="members.php?type=6&action=99&sort="'.$active_button6.'>'.$lang_search.'</a></div></li>';
}


//Permissions
if ($top_menu == 'permissions'){
$submenu_content = '
<li><div align="center"><a href="user_permissions.php?type=1"'.$active_button1.'>'.$lang_regular_member.'</a></div></li>
<li><div align="center"><a href="user_permissions.php?type=2"'.$active_button2.'>'.$lang_stand_mod.'</a></div></li>
<li><div align="center"><a href="user_permissions.php?type=3"'.$active_button3.'>'.$lang_global_mod.'</a></div></li>';
}


//Videos main page
if ($top_menu == 'groups'){
$submenu_content = '
<li><div align="center"><a href="groups.php?type=1&action=0&sort=1"'.$active_button1.'>'.$lang_active.'</a></div></li>
<li><div align="center"><a href="groups.php?type=3&action=0&sort=1"'.$active_button3.'>'.$lang_featured.'</a></div></li>
<li><div align="center"><a href="groups.php?type=5&action=0&sort=1"'.$active_button5.'>'.$lang_flagged.'</a></div></li>
<li><div align="center"><a href="groups.php?type=4&action=99&sort=1"'.$active_button4.'>'.$lang_search.'</a></div></li>';
}



///////////////////
// SIDE MENUS
//////////////////

//Media Menus
if ($side_menu == 'media'){
$submenu_left = '
<li> &nbsp;- <a href="videos.php">'.$lang_word_videos.'</a></li>
<li> &nbsp;- <a href="audio.php">'.$lang_word_Audio.'</a></li>
<li> &nbsp;- <a href="blogs.php">'.$lang_blogs.'</a></li>
<li> &nbsp;- <a href="pictures.php">'.$lang_pictures.'</a></li>';
}


if ($side_menu == 'flagged'){
$submenu_left = '
<li> &nbsp;- <a href="flagged_comments.php?type=1&action=0&sort=1">'.$lang_word_videos.'</a></li>
<li> &nbsp;- <a href="flagged_comments.php?type=2&action=0&sort=1">'.$lang_word_Audio.'</a></li>
<li> &nbsp;- <a href="flagged_comments.php?type=3&action=0&sort=1">'.$lang_blogs.'</a></li>
<li> &nbsp;- <a href="flagged_comments.php?type=4&action=0&sort=1">'.$lang_pictures.'</a></li>
<li> &nbsp;- <a href="flagged_comments.php?type=5&action=99&sort=1">'.$lang_groups.'</a></li>
<li> &nbsp;- <a href="flagged_comments.php?type=6&action=99&sort=1">'.$lang_profiles.'</a></li>';
}


//___Check for PowerTools______________

//check for Massuploader
if(file_exists($base_path.'/addons/massuploader/index.php')){
$menu_powertool .=  '<li> &nbsp;- <a href="../addons/massuploader/index.php?keepThis=true&TB_iframe=true&height=500&width=850" class="thickbox" title="MassUploader">MassUploader</a></li>';
}

//check for RemoteServer
if(file_exists($base_path.'/addons/remoteserver/index.php')){
$menu_powertool .=  '<li> &nbsp;- <a href="../addons/remoteserver/index.php?keepThis=true&TB_iframe=true&height=500&width=850" class="thickbox" title="MassUploader">Remote Server(s)</a></li>';
}

//check for Systembackup
if(file_exists($base_path.'/addons/systembackup/index.php')){
$menu_powertool .=  '<li> &nbsp;- <a href="../addons/systembackup/index.php?keepThis=true&TB_iframe=true&height=500&width=850" class="thickbox" title="MassUploader">Remote Server(s)</a></li>';
}

//check for MassEmbedder
if(file_exists($base_path.'/addons/massembedder/index.php')){
$menu_powertool .=  '<li> &nbsp;- <a href="../addons/massembedder/index.php?keepThis=true&TB_iframe=true&height=600&width=950" class="thickbox" title="MassEmbedder">Mass Video Embedder</a></li>';
}

//check for video ads
if(file_exists($base_path.'/addons/videoads/ads.php')){
$menu_powertool .=  '<li> &nbsp;- <a href="../addons/videoads/ads.php?keepThis=true&TB_iframe=true&height=600&width=950" class="thickbox" title="VideoAds">VideoAds Manager</a></li>';
}


//______________________________________

//Index Menus
if ($side_menu == 'index'){
$submenu_left = '
<li> &nbsp;- <a href="videos.php">'.$lang_word_videos.'</a></li>
<li> &nbsp;- <a href="audio.php">'.$lang_word_Audio.'</a></li>
<li> &nbsp;- <a href="blogs.php">'.$lang_blogs.'</a></li>
<li> &nbsp;- <a href="pictures.php">'.$lang_pictures.'</a></li>'.$menu_powertool;
}

//Settings Menus
if ($side_menu == 'settings'){
$submenu_left = '
<li> &nbsp;- <a href="settings_general.php">'.$lang_general_settings.'</a></li>
<li> &nbsp;- <a href="settings_video.php">'.$lang_video_settings.'</a></li>
<li> &nbsp;- <a href="settings_pictures.php">'.$lang_picture_settings.'</a></li>
<li> &nbsp;- <a href="settings_features.php">'.$lang_enabled_features.'</a></li>'.$menu_powertool;
}

//Settings Menus
if ($side_menu == 'permissions'){
$submenu_left = '
<li> &nbsp;- <a href="user_permissions.php?type=1">'.$lang_regular_member.'</a></li>
<li> &nbsp;- <a href="user_permissions.php?type=2">'.$lang_stand_mod.'</a></li>
<li> &nbsp;- <a href="user_permissions.php?type=3">'.$lang_global_mod.'</a></li>'.$menu_powertool;
}


//Index Second Menu
if ($side_menu2 == 'enabled'){
$submenu_left2 = '
<li> &nbsp;- <a href="settings_general.php">'.$lang_general_settings.'</a></li>
<li> &nbsp;- <a href="settings_video.php">'.$lang_video_settings.'</a></li>
<li> &nbsp;- <a href="settings_pictures.php">'.$lang_picture_settings.'</a></li>
<li> &nbsp;- <a href="settings_features.php">'.$lang_enabled_features.'</a></li>'.$menu_powertool;
}


?>