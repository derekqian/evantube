<?php

//get some basic site stats
///////////////////////////

//total videos
$sql = "SELECT indexer FROM videos WHERE approved='yes'";
$video_total = @mysql_num_rows(mysql_query($sql));

//total Audios
$sql = "SELECT indexer FROM audios WHERE approved='yes'";
$audio_total = @mysql_num_rows(mysql_query($sql));

//total images
$sql = "SELECT indexer FROM images WHERE approved='yes'";
$image_total = @mysql_num_rows(mysql_query($sql));

//total blogs
$sql = "SELECT indexer FROM blogs WHERE approved='yes'";
$blog_total = @mysql_num_rows(mysql_query($sql));

//total members
$sql = "SELECT user_id FROM member_profile";
$members_total = @mysql_num_rows(mysql_query($sql));


//views vids
$sql = "SELECT indexer FROM views_tracker WHERE media_type ='videos'";
$total_views_videos = @mysql_num_rows(@mysql_query($sql));


//views images
$sql = "SELECT indexer FROM views_tracker WHERE media_type ='images'";
$total_views_images = @mysql_num_rows(@mysql_query($sql));


//views blogs
$sql = "SELECT indexer FROM views_tracker WHERE media_type ='blogs'";
$total_views_blogs = @mysql_num_rows(@mysql_query($sql));


//views audio
$sql = "SELECT indexer FROM views_tracker WHERE media_type ='audios'";
$result = @mysql_query($sql);
$total_views_audio = @mysql_num_rows(@mysql_query($sql));


//views profiles
$sql = "SELECT indexer FROM views_tracker WHERE media_type ='member_profile'";
$result = @mysql_query($sql);
$total_views_profiles = @mysql_num_rows(@mysql_query($sql));



//latest member
$sql = "select user_name, user_id from member_profile order by user_id desc";
$result = @mysql_query($sql);
$row = @mysql_fetch_row($result);
$newest_user_name = $row[0];
$newest_userid = $row[1];

//total comments
$sql = "SELECT indexer FROM videocomments";
$comments_total = @mysql_num_rows(mysql_query($sql));


//recent videos
$recent = array();
$sql = "SELECT * FROM videos WHERE approved='yes' AND public_private = 'public' ORDER BY indexer DESC LIMIT 10";
$query = @mysql_query($sql);
while ($result1 = mysql_fetch_array($query)) {
    $recent[] = $result1;
}


//Get Folder Usage (i.e folder sizez)
/////////////////////////////////////

//Get Total Folder Sizes
////////////////////////
function foldersize($path) {

	$total_size = 0;

	if (!function_exists('scandir')) {
    		function scandir($path) {
    			$dh  = opendir($path);
    			while (false !== ($filename = readdir($dh))) {
    				$files[] = $filename;
			}

			sort($files);
			//print_r($files);

			rsort($files);
			//print_r($files);

        		return($files);
    		}

    		$files = scandir($path);

    		foreach ($files as $t) {

        		if (is_dir($t)) {// In case of folder
            		if ($t <> "." && $t <> "..") {// Exclude self and parent folder
                			$size = foldersize($path."/".$t);
                			//print("Dir - $path/$t = $size<br>\n");
                			$total_size += $size;
            		}

        		} else {// In case of file
            		$size = @filesize($path."/".$t);
            		//print("File - $path/$t = $size<br>\n");
            		$total_size += $size;
        		}
    		}

    		$bytes = array('B','KB','MB','GB','TB');

    		foreach ($bytes as $val) {
      		if ($total_size > 1024) {
            		$total_size = $total_size / 1024;
        	     	} else {
            		break;
        		}
    		}

    		return @round($total_size,2)." ".$val;

	} else {

		$files = @scandir($path);

		foreach ($files as $t) {

			if (is_dir($t)) {// In case of folder
            		if ($t <> "." && $t <> "..") {// Exclude self and parent folder
                			$size = foldersize($path."/".$t);
                			$total_size += $size;
            		}

        		} else {
        			$size = @filesize($path."/".$t);
            		$total_size += $size;
        		}
    		}

    		$bytes = array('B','KB','MB','GB','TB');

    		foreach ($bytes as $val) {
        		if ($total_size > 1024) {
            		$total_size = $total_size / 1024;
        		} else {
            		break;
        		}
    		}
    		return @round($total_size,2)." ".$val;
	}
}

$videos_folder 	= foldersize($base_path . '/uploads');
if ($videos_folder == '') $videos_folder = 'None';

$mp3_folder		= foldersize($base_path . '/uploads/audio');
if ($mp3_folder == '') $mp3_folder = 'None';

$pictures_folder 	= foldersize($base_path . '/addons/albums/images');
if ($pictures_folder == '') $pictures_folder = 'None';

?>
