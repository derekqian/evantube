<?php

/**
 * -------------------------USING THE PLAYLIST----------------------
 * You can set in the url query
 * -limit: ( number of videos returned) <optional>
 * -search: ('a seach query useful for related videos') <otional>
 * -type: ('featured'; 'promoted')
 *
 * --By default the playlist will be for latest videos.
 * --Specifying a search query will overide any 'type' setting
 * --Output is an xml file
 * -----------------------------------------------------------------
 */

include_once ('classes/config.php');


//____get video limit________
function GetVideoLimit() {
    $limit = (is_numeric($_GET['limit']))?$_GET['limit']:12;
    return $limit;
}


//____get the type $sql________
function GetVideoType() {
    switch($_GET['type']) {
        case 'featured':
            $add_sql = " AND featured = 'yes'";
            break;

        case 'promoted':
            $add_sql = " AND promoted = 'yes'";
            break;
    }
    return $add_sql;
}


//____get search query_________
function GetVideoSearch() {

    if($_GET['search'] != '') {
        $keywords = str_replace('+',' ',$_GET['search']);
        $keywords = str_replace('%20',' ',$keywords);
        $keywords = str_replace('%26','&',$keywords);

        $safety = array('UNION','=',"'",'?');
        $keywords = str_replace($safety,'',$keywords);
        $search = " AND (title LIKE '%$keywords%' OR tags LIKE '%$keywords%' OR description LIKE '%$keywords%')";
        return $search;
    } else {

        return;
    }


}

//____some cleanup___________
function CleanTitle($txt = '') {
    $txt = trim($txt);
    $txt = str_replace('"',"",$txt);
    $txt = str_replace("'","",$txt);
    $txt = str_replace('?','',$txt);
    return $txt;
}


//____run sql query__________
$limit = GetVideoLimit();
$search = GetVideoSearch();
$type = ($search == '')?GetVideoType():''; //set type only if search is blank

$sql = "SELECT indexer, video_id, title, description
        FROM videos
		WHERE approved = 'yes'
		AND public_private = 'public'
		AND video_type = 'uploaded'
		$type $search
		ORDER BY RAND()
		LIMIT $limit";
$query = @mysql_query($sql);


//____start xml file headers____________
header("Content-Type: text/xml; charset=UTF-8");
header("Expires: 0");
print "<?xml version=\"1.0\"?>\n";
print "<playlist>\n";



//_____loop through all results____________
while($result = @mysql_fetch_array($query)) {

    $title = CleanTitle($result['title']);
    $title_seo = CleanTitle($result['title_seo']);
    $vid = $result['indexer'];
    $file_name = $result['video_id'];
    $external_url = $config["site_base_url"].'/videos/'.$vid.'/'.$title_seo;
    $source_url = $config["site_base_url"].'/uploads/'.$file_name.'.flv';
    $thumb_path = $base_path.'/uploads/player_thumbs/'.$file_name.'.jpg';
    $source_path = $base_path.'/uploads/'.$file_name.'.flv';

    //cant find big thumb file
    if(file_exists($thumb_path)) {
        $thumb_url = $config["site_base_url"].'/uploads/player_thumbs/'.$file_name.'.jpg';
    } else {
        $thumb_url = $config["site_base_url"].'/uploads/thumbs/'.$file_name.'.jpg';
        $thumb_path = $base_path.'/uploads/thumbs/'.$file_name.'.jpg';
    }

    //only list if the video file and imagae exist
    if(file_exists($source_path) && file_exists($thumb_path)) {
        print "<video>\n";
        print "<title>$title</title>\n";
        print "<source>$source_url</source>\n";
        print "<thumb>$thumb_url</thumb>\n";
        print "<external_url>$external_url</external_url>\n";
        print "<duration>0.00</duration>\n";
        print "</video>\n";
    }
}
print "</playlist>";


?>

