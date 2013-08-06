<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Version: PHPmotion V3.0 beta                                                      //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once ('classes/config.php');

// settings config in config.inc.php
$show_limit = $config['popular_show_limit'];
$text_limit = $config['popular_text_limit'];

$popular_videos		= '';
$popular_audios		= '';
$popular_blogs		= '';
$popular_images		= '';
$popular_members		= '';
$popular_categories	= '';

// videos
$sql = "SELECT * FROM videos WHERE public_private = 'public' AND approved ='yes' AND viewtime > '0000-00-00 00:00:00' ORDER BY number_of_views desc LIMIT $show_limit ";
$query = @mysql_query($sql);
while ($result = @mysql_fetch_array($query)) {

	$vid_play		= $result['indexer'];
	$viewnumber		= $result['number_of_views'];
	$viewnumber 	= number_format( $viewnumber, 0 );
	$video_title	= $result['title'];
	$vid_title_seo	= $result['title_seo'];
	$video_title	= trim($video_title);
	$video_title	= str_replace('"', "", $video_title);
	$video_title	= str_replace("'", "", $video_title);

	if (strlen($video_title) > $text_limit)
	$video_title = substr($video_title, 0, strrpos(substr($video_title, 0, $text_limit), ' ')) . '...';

	$popular_videos .= "
				<div>
				  <div style=\"margin-left:20px; float:left;\">
				    <a href=\"videos/$vid_play/$vid_title_seo\">$video_title</a>
				  </div>
				  <div style=\"margin-right:20px; float:right;\">
				    $viewnumber&nbsp;Views
				  </div>
				</div>
				<br>";
}

// audios
$sql2 = "SELECT * FROM audios WHERE public_private = 'public' AND approved ='yes' AND playtime > '0000-00-00 00:00:00' ORDER BY number_of_views desc LIMIT $show_limit ";
$query2 = @mysql_query($sql2);
while ($result2 = @mysql_fetch_array($query2)) {

	$audio_id		= $result2['indexer'];
	$viewnumber		= $result2['number_of_views'];
	$viewnumber 	= number_format( $viewnumber, 0 );
	$audio_title	= $result2['title'];
	$audio_title_seo	= $result2['title_seo'];
	$audio_title	= trim($audio_title);
	$audio_title	= str_replace('"', "", $audio_title);
	$audio_title	= str_replace("'", "", $audio_title);

	if (strlen($audio_title) > $text_limit)
	$audio_title = substr($audio_title, 0, strrpos(substr($audio_title, 0, $text_limit), ' ')) . '...';

	$popular_audios .= "
				<div>
				  <div style=\"margin-left:20px; float:left;\">
				    <a href=\"audio/$audio_id/$audio_title_seo\">$audio_title</a>
				  </div>
				  <div style=\"margin-right:20px; float:right;\">
				    $viewnumber&nbsp;Plays
				  </div>
				</div>
				<br>";
}

// blogs
$sql3 = "SELECT * FROM blogs WHERE public_private = 'public' AND approved ='yes' AND viewtime > '0000-00-00 00:00:00' ORDER BY number_of_views desc LIMIT $show_limit ";
$query3 = @mysql_query($sql3);
while ($result3 = @mysql_fetch_array($query3)) {

	$blog_id		= $result3['indexer'];
	$viewnumber		= $result3['number_of_views'];
	$viewnumber 	= number_format( $viewnumber, 0 );
	$blog_title		= $result3['title'];
	$blog_title_seo	= $result3['title_seo'];
	$blog_title		= trim($blog_title);
	$blog_title		= str_replace('"', "", $blog_title);
	$blog_title		= str_replace("'", "", $blog_title);

	if (strlen($blog_title) > $text_limit)
	$blog_title = substr($blog_title, 0, strrpos(substr($blog_title, 0, $text_limit), ' ')) . '...';

	$popular_blogs .= "
				<div>
				  <div style=\"margin-left:20px; float:left;\">
				    <a href=\"read_blog/$blog_id/$blog_title_seo\">$blog_title</a>
				  </div>
				  <div style=\"margin-right:20px; float:right;\">
				    $viewnumber&nbsp;Views
				  </div>
				</div>
				<br>";
}

// images
$sql3 = "SELECT * FROM image_galleries WHERE public_private = 'public' AND approved ='yes' AND viewtime > '0000-00-00 00:00:00' ORDER BY number_of_views desc LIMIT $show_limit ";
$query3 = @mysql_query($sql3);
while ($result3 = @mysql_fetch_array($query3)) {

	$gallery_id		= $result3['gallery_id'];
	$viewnumber		= $result3['number_of_views'];
	$viewnumber 	= number_format( $viewnumber, 0 );
	$gallery_name	= $result3['gallery_name'];
	$gallery_name_seo	= $result3['gallery_name_seo'];

	if (strlen($gallery_name) > $text_limit)
		$gallery_name = substr($gallery_name, 0, strrpos(substr($gallery_name, 0, $text_limit), ' ')) . '...';
	$gallery_id		= $result3['gallery_id'];

	$popular_images .= "
				<div>
				  <div style=\"margin-left:20px; float:left;\">
				    <a href=\"view-album/$gallery_id/$gallery_name_seo/\">$gallery_name</a>
				  </div>
				  <div style=\"margin-right:20px; float:right;\">
				    $viewnumber&nbsp;Views
				  </div>
				</div>
				<br>";
}

// members

$sql4 = "SELECT * FROM member_profile WHERE account_status ='active' ORDER BY number_of_views desc LIMIT $show_limit ";
$query4 = @mysql_query($sql4);
while ($result4 = @mysql_fetch_array($query4)) {
	$viewnumber		= $result4['number_of_views'];
	$viewnumber 	= number_format( $viewnumber, 0 );
	$popular_users	= $result4['user_name'];

	$popular_members .= "
				<div>
				  <div style=\"margin-left:20px; float:left;\">
				    <a href=\"/members/$popular_users\">$popular_users</a>
				  </div>
				  <div style=\"margin-right:20px; float:right;\">
				    $viewnumber&nbsp;Views
				  </div>
				</div>
				<br>";
}

// all categories

$all_categories	= array();

$show_empty		= $config['show_categories_if_empty'];

$sql 			= "SELECT channel_name, channel_name_seo, channel_id FROM channels ORDER BY channel_name ASC";
$query 		= @mysql_query($sql);
while ($result = @mysql_fetch_array($query)) {

	$channel_name_seo	= $result['channel_name_seo'];
	$channel_name	= $result['channel_name'];
	$channel_id		= $result['channel_id'];

    	$sql0 		= "SELECT indexer FROM videos WHERE channel_id = '$channel_id' AND approved = 'yes' $sql_public_private ORDER BY indexer DESC";
    	$query0 		= @mysql_query($sql0);
  	$result0 		= @mysql_fetch_array($query0);
  	$count_videos 	= @mysql_num_rows($query0);

    	// dont push array if empty and show is turned off
    	if ( $show_empty == 'yes' ) {

    		if ( $count_videos == '' ) {
    			$count_videos = 0;
    			$popular_categories .= "
				<div>
				  <div style=\"margin-left:3px; float:left;\">
			    	    <a href=\"category/$channel_name_seo\">$channel_name</a>
			    	  </div>
			    	  <div style=\"margin-right:22px; float:right;\">
			    	    $count_videos&nbsp;Videos
			    	  </div>
			    	</div><br>";
    		} else {
    			$popular_categories .= "
				<div>
				  <div style=\"margin-left:3px; float:left;\">
			    	    <a href=\"category/$channel_name_seo\">$channel_name</a>
			    	  </div>
			    	  <div style=\"margin-right:22px; float:right;\">
			    	    $count_videos&nbsp;Videos
			    	  </div>
			    	</div><br>";
		}

	} else {

    		if ( $count_videos == '' ) {
    			$count_videos 	= '';
    			$channel_name_seo = '';
    			$channel_name	= '';

    		} else {

    			$popular_categories .= "
				<div>
				  <div style=\"margin-left:3px; float:left;\">
			    	    <a href=\"category/$channel_name_seo\">$channel_name</a>
			    	  </div>
			    	  <div style=\"margin-right:22px; float:right;\">
			    	    $count_videos&nbsp;Videos
			    	  </div>
			    	</div><br>";
		}
    	}
}

?>