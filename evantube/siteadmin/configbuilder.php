<?php
//general sql
$sql = "SELECT * FROM general_settings";
$query = @mysql_query($sql);
$result = @mysql_fetch_array($query);

//videos sql
$sql = "SELECT * FROM video_settings";
$query = @mysql_query($sql);
$result2 = @mysql_fetch_array($query);

//pictures sql
$sql = "SELECT * FROM image_settings";
$query = @mysql_query($sql);
$result3 = @mysql_fetch_array($query);

//enabled features and themes/language sql
$sql = "SELECT * FROM features_settings";
$query = @mysql_query($sql);
$result4 = @mysql_fetch_array($query);


//headers and stuff
$comment1 = '//General settings';
$comment2 = '//Video settings';
$comment3 = '//Theme setting';
$comment5 ='//This file is auto generated in the Site Adim panel';
$comment6 = '//Picture settings section';
$comment7 = '//Enabled features section';
$a0       ='<?php';
$a101     = '?>';


//General Settings
$a5 = '$config["from_system_name"] = "'.$result['from_system_name'].'";';
$a6 = '$config["notifications_from_email"] = "'.$result['notifications_from_email'].'";';
$a7 ='$config["site_name"] = "'.$result['site_name'].'";';
$a8 ='$config["date_format"] = "'.$result['date_format'].'";';
$a9 ='$config["site_base_url"] = "'.$result['site_base_url'].'";';
$a10 ='$config["delete_original"] = "'.$result['delete_original'].'";';
$a11 ='$config["delete_avi"] = "'.$result['delete_avi'].'";';
$a12 ='$config["path_to_mencoder"] = "'.$result['path_to_mencoder'].'";';
$a13 ='$config["path_to_ffmpeg"] = "'.$result['path_to_ffmpeg'].'";';
$a14 ='$config["path_to_flvtool2"] = "'.$result['path_to_flvtool2'].'";';
$a16 ='$config["auto_approve_videos"] = "'.$result['auto_approve_videos'].'";';
$a17 ='$config["maximum_size_human_readale"] = "'.$result['maximum_size_human_readale'].'";';
$a18 ='$config["auto_approve_profile_photo"] = "'.$result['auto_approve_profile_photo'].'";';
$a19 ='$config["debug_mode"] = "'.$result['debug_mode'].'";';
$a20 ='$config["allow_multiple_video_comments"] = "'.$result['allow_multiple_video_comments'].'";';
$a211 ='$config["auto_play_index"] = "'.$result['auto_play_index'].'";';
$a21 ='$config["auto_play"] = "'.$result['auto_play'].'";';
$a22 ='$config["video_buffer_time"] = '.$result['video_buffer_time'].';';
$a23 ='$config["maximum_size"] = '.$result['maximum_size'].';';
$a24 ='$config["search_page_limits"] = '.$result['search_page_limits'].';';
$a25 ='$config["groups_main_limit"] = '.$result['groups_main_limit'].';';
$a26 ='$config["groups_home_video_limit"] = '.$result['groups_home_video_limit'].';';
$a27 ='$config["comment_page_limits"] = '.$result['comment_page_limits'].';';
$a28 ='$config["see_more_limits"] = '.$result['see_more_limits'].';';
$a29 ='$config["admin_maximum_display"] = '.$result['admin_maximum_display'].';';
$a30 ='$config["flagging_threshold_limits"] = '.$result['flagging_threshold_limits'].';';
$a31 ='$config["seemore_limits_wide"] = '.$result['seemore_limits_wide'].';';
$a32 ='$config["allow_download"] = "'.$result['allow_download'].'";';
$a33 ='$enable_audio = "'.$result['enable_audio'].'";';
$a34 ='$path_to_php = "'.$result['path_to_php'].'";';
$a35 ='$log_encoder = "'.$result['log_encoder'].'";';
$a36 ='$config_recent_title_length = "'.$result['config_recent_title_length'].'";';

//video settings section
$a37 ='$config["video_watermark"] = "'.$result2['video_watermark'].'";';
$a38 ='$config["video_watermark_place"] = "'.$result2['video_watermark_place'].'";';
$a39 ='$config["video_resize"] = "'.$result2['video_resize'].'";';
$a40 ='$config["video_convert_pass"] = "'.$result2['video_convert_pass'].'";';
$a41 ='$config["video_ffmpeg_size"] = "'.$result2['video_ffmpeg_size'].'";';
$a42 ='$config["video_ffmpeg_bit_rate"] = "'.$result2['video_ffmpeg_bit_rate'].'";';
$a43 ='$config["video_ffmpeg_audio_rate"] = "'.$result2['video_ffmpeg_audio_rate'].'";';
$a44 ='$config["video_ffmpeg_high_quality"] = "'.$result2['video_ffmpeg_high_quality'].'";';
$a45 ='$config["video_ffmpeg_hq"] = "'.$result2['video_ffmpeg_hq'].'";';
$a46 ='$config["video_ffmpeg_hq_size"] = "'.$result2['video_ffmpeg_hq_size'].'";';
$a47 ='$config["video_ffmpeg_qmax"] = "'.$result2['video_ffmpeg_qmax'].'";';
$a48 ='$config["video_mencoder_vbitrate"] = "'.$result2['video_mencoder_vbitrate'].'";';
$a49 ='$config["video_mencoder_scale"] = "'.$result2['video_mencoder_scale'].'";';
$a50 ='$config["video_mencoder_srate"] = "'.$result2['video_mencoder_srate'].'";';
$a51 ='$config["video_mencoder_audio_rate"] = "'.$result2['video_mencoder_audio_rate'].'";';


//Picture settings section
$a52 ='$config["album_pic_maxwidth"] = "'.$result3['album_pic_maxwidth'].'";';
$a53 ='$config["album_pic_maxheight"] = "'.$result3['album_pic_maxheight'].'";';
$a54 ='$config["album_pic_minwidth"] = "'.$result3['album_pic_minwidth'].'";';
$a55 ='$config["album_pic_minheight"] = "'.$result3['album_pic_minheight'].'";';
$a56 ='$config["album_pic_maxsize"] = "'.$result3['album_pic_maxsize'].'";';
$a57 ='$config["member_max_albums"] = "'.$result3['member_max_albums'].'";';
$a58 ='$config["pictures_max_per_album"] = "'.$result3['pictures_max_per_album'].'";';

//Enabled Features section
$a60 ='$config["enabled_features_audio"] = "'.$result4['audio'].'";';
$a61 ='$config["enabled_features_images"] = "'.$result4['images'].'";';
$a62 ='$config["enabled_features_blogs"] = "'.$result4['blogs'].'";';
$a63 ='$config["enabled_features_video_comments"] = "'.$result4['video_comments'].'";';
$a64 ='$config["enabled_features_blog_comments"] = "'.$result4['blog_comments'].'";';
$a65 ='$config["enabled_features_audio_comments"] = "'.$result4['audio_comments'].'";';
$a66 ='$config["enabled_features_image_comments"] = "'.$result4['image_comments'].'";';
$a67 ='$config["enabled_features_profile_comments"] = "'.$result4['profile_comments'].'";';
$a68 ='$config["enabled_features_stats"] = "'.$result4['stats'].'";';
$a69 ='$config["enabled_features_confirmation_email"] = "'.$result4['confirmation_email'].'";';
$a70 ='$config["enabled_features_custome_profile"] = "'.$result4['custome_profile'].'";';


//theme
$a71 ='$config["user_theme"] = "'.$result4['theme'].'";';
$a72 ='$config["language"] = "'.$result4['language'].'";';


$final = $a0."\n"."\n".$comment5."\n".$a2."\n"."\n" .$comment1."\n".$a5 ."\n".$a6 ."\n".$a7 ."\n".$a8 ."\n".$a9 ."\n".$a10
."\n".$a11 ."\n".$a12 ."\n".$a13 ."\n".$a14 ."\n".$a16 ."\n".$a17 ."\n".$a18 ."\n".$a19 ."\n".$a20 ."\n".$a211 ."\n".$a21 ."\n".$a22
."\n".$a23 ."\n".$a24 ."\n".$a25 ."\n".$a26 ."\n".$a27 ."\n".$a28 ."\n".$a29 ."\n".$a30 ."\n".$a31 ."\n".$a32 ."\n".$a33
."\n".$a34 ."\n".$a35."\n".$a36."\n"."\n".$comment2."\n".$a37."\n".$a38."\n".$a39."\n".$a40."\n".$a41."\n".$a42."\n".$a43
."\n".$a44."\n".$a45."\n".$a46."\n".$a47."\n".$a48."\n".$a49."\n".$a50."\n".$a51."\n"."\n"
.$comment6."\n".$a52."\n".$a53."\n".$a54."\n".$a55."\n".$a56."\n".$a57."\n".$a58."\n"."\n" .$comment7."\n".$a60."\n"
.$a61."\n".$a62."\n".$a63."\n".$a64."\n".$a65."\n".$a66."\n".$a67."\n".$a68."\n".$a69."\n".$a70."\n"."\n"
.$comment3."\n".$a71."\n".$a72."\n"."\n"."\n".$a101;

?>