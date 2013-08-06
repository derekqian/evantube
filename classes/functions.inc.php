<?php
@error_reporting(0);
//////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

$flood_stop 	= $config['comments_flood_time'];
$flood_msg 		= $config['comments_flood_msg'];
$most_popular	= $config['Tags_most_popular'];
$more_tags 		= $config['Tags_more_tags'];
$age_limit		= $config['age_limit'];

/*_______________________________________________________________________________________________________________________________________________*/

function member_site_ranking( $member_id ) {

	$sql		= "SELECT user_group FROM member_profile WHERE user_id = '$member_id'";
	$result 	= mysql_query($sql);
	$row 		= mysql_fetch_array($result);
      $user_group = $row['user_group'];

	// get videos by member
      $sql = "SELECT indexer FROM videos WHERE user_id = $member_id AND approved='yes'";
      $member_videos = (int) mysql_num_rows(mysql_query($sql));

      // get audios by member
      $sql = "SELECT indexer FROM audios WHERE user_id = $member_id AND approved='yes'";
      $member_audios = (int) mysql_num_rows(mysql_query($sql));

      // get albums by member
      $sql = "SELECT gallery_id FROM image_galleries  WHERE user_id = $member_id AND approved='yes'";
      $member_albums = (int) mysql_num_rows(mysql_query($sql));

      // get blogs by member
      $sql = "SELECT indexer FROM blogs WHERE user_id = $member_id AND approved='yes'";
      $member_blogs = (int) mysql_num_rows(mysql_query($sql));

      $member_site_total = ( $member_videos + $member_audios + $member_albums + $member_blogs );

	switch ( $member_site_total ) {

		case $member_site_total > 150:
			$badge = 'badge_4.png';
		break;

		case $member_site_total > 50:
			$badge = 'badge_2.png';
		break;

		case $member_site_total > 25:
			$badge = 'badge_3.png';
		break;

		case $member_site_total > 0:
			$badge = 'badge_1.png';
		break;
	}

	// over ride site points and give admin highest badge
	// also could be done for mod's etc...

	if ($user_group == 'admin') $badge = 'badge_5.png';

	//mysql_free_result($result);

return $badge;

}

/*_______________________________________________________________________________________________________________________________________________*/


/*_______________________________________________________________________________________________________________________________________________*/

function birthday ($db_birth_date) {
	global $age_limit, $lang_private;

	list($year,$month,$day) = explode('-', $db_birth_date);

    	$year_remainder  = date('Y') - $year;
    	$month_remainder = date('m') - $month;
    	$day_remainder   = date('d') - $day;

    	//if ( $day_remainder < 0 || $month_remainder < 0 ) $year_remainder--;

    	if ( $month_remainder < 0 ) $year_remainder--;

    	if ( $year_remainder <= $age_limit ) $year_remainder = $lang_private;

    	return $year_remainder;
}
/*_______________________________________________________________________________________________________________________________________________*/



/*_______________________________________________________________________________________________________________________________________________*/

function check_moderator($user_id) {
	$sql = "SELECT user_group, user_name FROM member_profile WHERE user_id = '$user_id'";
	$query = @mysql_query($sql);

	while ($result = @mysql_fetch_assoc($query)){
		$is_mod = $result['user_group'];
		$mod_user_name = $result['user_name'];
	}

	if ( $is_mod == 'global_mod' || $is_mod == 'admin') {
		return array(true, $mod_user_name);
	} else {
		return false;
	}
	mysql_free_result($result);
}
/*_______________________________________________________________________________________________________________________________________________*/

/*_______________________________________________________________________________________________________________________________________________*/

function seo_title($title) {
	// change to lower case
	$title_seo = strtolower(trim($title));

	// Remove odd chrs
	//$title_seo = html_entity_decode(preg_replace ('/&([a-zA-Z])(uml|acute|grave|circ|tilde|cedil|ring);/', '$1', htmlentities($title_seo, ENT_COMPAT)));

	// substitute any date forward slash with dashes
	$title_seo = str_replace('/', '-', $title_seo);
	$title_seo = str_replace('%', '', $title_seo);


	// remove all non-alphanumeric characters except for spaces and dashes
	//$title_seo = preg_replace('/[^a-zA-Z0-9-% ]/', '', $title_seo);

	// substitute the spaces with dashes
	$title_seo = str_replace(' ', '-', $title_seo);

	// Clean leftover dashes
	$title_seo = str_replace(array('---', '--'), '-', $title_seo);

return $title_seo;
}
/*_______________________________________________________________________________________________________________________________________________*/

/*_______________________________________________________________________________________________________________________________________________*/

function flood_check ( $user_id, $comment_table, $id_name, $id ) {
	global $flood_stop, $flood_msg;

	$sql = "SELECT * from $comment_table WHERE $id_name = $id AND by_id = $user_id";
	$query = mysql_query($sql);

	while ($result = @mysql_fetch_array($query)) {
		$comment_time = strtotime($result['todays_date']);
	}
	if ( time() < ($comment_time + $flood_stop) ) {
		$proceed = 'false';
		$flood_msg = '<p align="center"><font color="#FF4242" face="Arial"><b>'.$flood_msg.'</b></font>';
	}
	mysql_free_result($result);
	return array($proceed, $flood_msg);
}
/*_______________________________________________________________________________________________________________________________________________*/

/*_______________________________________________________________________________________________________________________________________________*/

if (!function_exists('htmlspecialchars_decode')) {
	function htmlspecialchars_decode($str, $options="") {
      	$trans = get_html_translation_table(HTML_SPECIALCHARS, $options);
          	$decode = ARRAY();
          	foreach ($trans AS $char=>$entity) {
            	$decode[$entity] = $char;
          	}
          	$str = strtr($str, $decode);
          	return $str;
      }
}
/*_______________________________________________________________________________________________________________________________________________*/

/*_______________________________________________________________________________________________________________________________________________*/

function to_bbc_code ($string) {
	$search = array(
    			'#\[b\](.*?)\[/b\]#',
    			'#\[i\](.*?)\[/i\]#',
    			'#\[u\](.*?)\[/u\]#',
    			'#\[img\](.*?)\[/img\]#',
    			'#\[url=(.*?)\](.*?)\[/url\]#',
    			'#\[code\](.*?)\[/code\]#'
			);

	$replace = array(
    			'<b>\\1</b>',
    			'<i>\\1</i>',
    			'<u>\\1</u>',
    			'<img src="\\1">',
    			'<a href="\\1">\\2</a>',
    			'<code>\\1</code>'
			);

return preg_replace($search , $replace, $string);

}
/*_______________________________________________________________________________________________________________________________________________*/

/*_______________________________________________________________________________________________________________________________________________*/

function refresh_me($url) {

	echo '<script type="text/javascript">
		<!--
		window.location = "'.$url.'"
		//-->
		</script>';
}
/*_______________________________________________________________________________________________________________________________________________*/

/*_______________________________________________________________________________________________________________________________________________*/

function dateTimeDiff($db_date) {

	if (!function_exists('gregoriantojd')) {
	 	function gregoriantojd() {
	 		$msg = "The PHP calendar function is disabled\n
	 		Please ask your host to do a normal php install";
	 		$fo = @fopen('phpmotion_errors.txt', 'w');
	 		@fwrite($fo, $msg);
	 		@fclose($fo);
	 	}
	 }

	$h_r			= '';
	$m_r 			= '';
	$s_r 			= '';

	// from V3 tables
	// 2008-07-14 20:34:03

	$c_date		= date('Y-m-d H:i:s');
	$c_year 		= substr($c_date,0,4);
	$c_month 		= substr($c_date,5,2);
	$c_day 		= substr($c_date,8,2);
	$r_year 		= substr($db_date,0,4);
	$r_month 		= substr($db_date,5,2);
	$r_day 		= substr($db_date,8,2);
	$tmp_m_dates	= $c_year . $c_month . $c_day;
	$tmp_r_use 		= $r_year . $r_month . $r_day;
	$tmp_dif 		= $tmp_m_dates-$tmp_r_use;
	$use_diff 		= $tmp_dif;
	$c_hour 		= substr($c_date,11,2);
	$c_min 		= substr($c_date,14,2);
	$c_seconds 		= substr($c_date,17,2);
	$r_hour 		= substr($db_date,11,2);
	$r_min 		= substr($db_date,14,2);
	$r_seconds 		= substr($db_date,17,2);
	$h_r 			= $c_hour-$r_hour;
	$m_r 			= $c_min-$r_min;
	$s_r 			= $c_seconds-$r_seconds;

	if( $use_diff < 1 ) {
		if( $h_r > 0 ) {
			if( $m_r < 0 ) {
				$m_r	= 60 + $m_r;
				$h_r 	= $h_r - 1;
				return $m_r . " Mins ago";
			} else {
				return $h_r. " Hrs " . $m_r . " Mins ago";
			}
		} else {
			if( $m_r > 0 ){
				return $m_r . " Mins ago";
			} else {
				return $s_r . " Secs ago";
			}
		}
	} else {
		$c_date		= date('m/d/Y');
		$date_str 		= strtotime($db_date);
		$db_date 		= date('m/d/Y', $date_str);
		$dformat 		= '/';
		$date_part_1	= explode($dformat, $db_date);
		$date_part_2  	= explode($dformat, $c_date);
		$db_date	  	= gregoriantojd($date_part_1[0], $date_part_1[1], $date_part_1[2]);
		$c_date 		= gregoriantojd($date_part_2[0], $date_part_2[1], $date_part_2[2]);
		$days_ago 		= $c_date - $db_date;

		if ( $days_ago == 1 ) {
			$day_word = 'day ago';
		} else {
			$day_word = 'days ago';
		}

		return $days_ago . " " . $day_word;
	}

}
/*_______________________________________________________________________________________________________________________________________________*/

/*_______________________________________________________________________________________________________________________________________________*/

// TAG CLOUDS
// usage call function arg = media type
// $tag_cloud = make_tag_cloud('videos');
// $tag_cloud_block = $tag_cloud[0];
// returns array large or small
// large =>$tag_cloud[0]
// small =>$tag_cloud[1]

function make_tag_cloud($media_type) {

	global $most_popular, $more_tags;

	////////////////////////////////////////////////////////////////////////////
	// lets check media_type in case
	//
	$media_type		= mysql_real_escape_string($media_type);
	$allowed_media 	= array('videos', 'audios', 'images', 'blogs');
	if ( !in_array( $media_type, $allowed_media ) ) $media_type = 'videos';
	////////////////////////////////////////////////////////////////////////////

	// #1
	//////////////////////////////////////////////
	// SET UP VARIABLES LOOK and FEEL
	//Font color =>Most popular & Least popular Headings only
	$font_color = "000099";
	//////////////////////////////////////////////

	// #2
	//////////////////////////////////////////////
	// number of vids to poll to for tag_cloud array
	// increase number for larger tag cloud
	// decrease if tag cloud size is too large
	$limit = 15;
	/////////////////////////////////////////////

	// #3
	/////////////////////////////////////////////
	//order tags by last viewed
	//uncomment to use this sort
	//requires video db viewtime mod
	$order_by = 'viewtime';
	/////////////////////////////////////////////

	// #4
	/////////////////////////////////////////////
	//order tags by most popular =>default setting
	//comment out if above sort is used
	//$order_by = 'number_of_views';
	//
	/////////////////////////////////////////////

	// #5
	/////////////////////////////////////////////
	//font setup this should work for most sites
	$font_size = 2;
	$adjust_font = 0;
	//
	/////////////////////////////////////////////

	// nothing below to edit

	$tag_cloud_block = "";
	$tag_cloud_small_block = "";

	$recent_tags = array();
	$viewkey = array();

	if ( $media_type == 'audios' ) {
		$order_by = 'playtime';
	}

	$sql = "SELECT * FROM $media_type WHERE public_private = 'public' AND approved ='yes' ORDER BY $order_by desc LIMIT $limit";
	$query = @mysql_query($sql);

	/* debug test
	if(!$query) {
		die("Error while during sql_query. Error Output: <br/>". mysql_errno() . ": " . mysql_error(). "<br/>"."Query follows:<br/>".$query);
		@mysql_close();
		die();
	}
	*/


	while ($result = @mysql_fetch_array($query)) {
		$recent_tags[] = $result['tags'];
		$viewkey[] = $result['indexer'];
	}

	$tag_count = sizeof($recent_tags);
	$tag_cloud = array();
	for ($x=0;$x<$tag_count;$x++){
  		$tag_cloud[] = split(" ", $recent_tags[$x]);
	}

	//first half - most popular
	$tag_cloud_block .="<font color=\"$font_color\"><b>$most_popular:</b></font>\n";
	$tag_cloud_block .="&nbsp;";
	$first_half = sizeof($tag_cloud);

	for ($y=0;$y<floor($first_half / 2);$y++){
  		$tags = sizeof($tag_cloud[$y]);
  		$font_size = floor($first_half / 3);
  		$font_size = $font_size - $adjust_font;

  		for ($t=0;$t<sizeof($tag_cloud[$y]);$t++){

    			$tag_word=$tag_cloud[$y][$t];

    			$tag_word = str_replace('"', "", $tag_word);
    			//$tag_word = str_replace("'", "", $tag_word);
    			$tag_word = str_replace('[', "", $tag_word);
    			$tag_word = str_replace(']', "", $tag_word);
    			$tag_word = str_replace('!', "", $tag_word);
    			$tag_word = str_replace('&', "", $tag_word);

    			$tag_cloud_block .= "<a href=\"search.php?keyword=$tag_word&amp;type=$media_type\" class=\"tag_cloud\"><font size=\"$font_size\">$tag_word</font></a>\n";
    			$tag_cloud_block .=" ";

    			$tag_cloud_small_block .= "<a href=\"search.php?keyword=$tag_word&amp;type=$media_type\" class=\"tag_cloud\"><font size=\"$font_size\">$tag_word</font></a>\n";
    			$tag_cloud_small_block .=" ";
  		}
  	$font_size--;
  	$adjust_font++;
	}

	$tag_cloud_block .="<br>";
	$tag_cloud_block .="<font color=\"$font_color\"><b>$more_tags:</b></font>\n";
	$tag_cloud_block .="&nbsp;";

	$font_size = (floor($first_half / 4)-1);

	//last half - least popular
	for ($y=floor($tag_count/2);$y<sizeof($tag_cloud);$y++){
  		$tags = sizeof($tag_cloud[$y]);
  		for ($t=0;$t<sizeof($tag_cloud[$y]);$t++){
    			$tag_word = $tag_cloud[$y][$t];
    			$tag_word = str_replace('"', "", $tag_word);
    			//$tag_word = str_replace("'", "", $tag_word);
    			$tag_word = str_replace('[', "", $tag_word);
    			$tag_word = str_replace(']', "", $tag_word);
    			$tag_word = str_replace('!', "", $tag_word);
    			$tag_word = str_replace('&', "", $tag_word);

    			$tag_cloud_block .= "<a href=\"search.php?keyword=$tag_word&amp;type=$media_type\" class=\"tag_cloud\"><font size=\"$font_size\">$tag_word</font></a>\n";
    			$tag_cloud_block .=" ";

    			$tag_cloud_small_block .= "<a href=\"search.php?keyword=$tag_word&amp;type=$media_type\" class=\"tag_cloud\"><font size=\"$font_size\">$tag_word</font></a>\n";
   			$tag_cloud_small_block .=" ";

			//$font_size--;
    			//$adjust_font++;
  		}
  	$font_size--;
  	$adjust_font++;
	}

return array($tag_cloud_block, $tag_cloud_small_block);

mysql_free_result($result);


} // end function

/*_______________________________________________________________________________________________________________________________________________*/

/*_______________________________________________________________________________________________________________________________________________*/
// ALL MEDIA AJAX RATING FUNCTION

// changed to work with all media => call function with media type [AS =>table name] as first arg => pullRating('videos', 56, true, false, true);

function getRating($media, $id)
{
	if ( $media == 'member_profile' ) {
		$indexer_name = 'user_id';
	} else {
		$indexer_name = 'indexer';
	}

	$total	= 0;
	$rows		= 0;
	$sel		= mysql_query("SELECT * FROM $media WHERE $indexer_name = '$id'");

	while( $data = mysql_fetch_assoc($sel) ) {
		$stars		= $data['updated_rating'];
		$num_votes		= $data['rating_number_votes'];
		$total_points	= $data['rating_total_points'];
	}
	if ($num_votes == 0){
		return '0';
	}
	else{
		$perc		= ($total_points/$num_votes) * 20;
		$newPerc	= round($perc,2);
		return $newPerc.'%';
	}
}

function outOfFive($media, $id)
{
	if ( $media == 'member_profile' ) {
		$indexer_name = 'user_id';
	} else {
		$indexer_name = 'indexer';
	}

	$total	= 0;
	$rows		= 0;
	$sel		= mysql_query("SELECT * FROM $media WHERE $indexer_name = '$id'");

	while( $data = mysql_fetch_assoc($sel) ) {
		$stars		= $data['updated_rating'];
		$num_votes		= $data['rating_number_votes'];
		$total_points 	= $data['rating_total_points'];
	}
	if ($num_votes == 0){
		return '0';
	}
	else{
		$perc = ($total_points/$num_votes);
		return round($perc,2);
	}
}

function getVotes($media, $id)
{
	if ( $media == 'member_profile' ) {
		$indexer_name = 'user_id';
	} else {
		$indexer_name = 'indexer';
	}

	$sel = mysql_query("SELECT rating_number_votes FROM $media WHERE $indexer_name = '$id'");
	while($data = mysql_fetch_assoc($sel)) {
		$num_votes = $data['rating_number_votes'];
	}
	if($num_votes == 0) {
		$votes = '0 Votes';
	}
	else if($num_votes == 1) {
		$votes = '1 Vote';
	} else {
		$votes = $num_votes . ' Votes';
	}
	return $votes;
}

function pullRating($media, $id, $show5 = false, $showPerc = false, $showVotes = false, $static = NULL)
{
	$sql		= "SELECT * FROM media_rating WHERE media_id = '$id' AND media_type = '$media' AND user_id = '$user'";
	$query	= @mysql_query($sql);
	$count	= @mysql_num_rows($query);

	if ( $count > 0 ) {
		$msg = " --  You have voted all ready!";
		exit;
		$text = '';
		if($show5 || $showPerc || $showVotes){
			$text .= '<div class="rated_text">';
		}
		if($show5){
			$text .= '<b>Rated:&nbsp;</b><span id="outOfFive_'.$id.'" class="out5Class">'.outOfFive($id).'</span>/5';
		}
		if($showPerc){
			$text .= '(<span id="percentage_'.$id.'" class="percentClass">'.getRating($media, $id).'</span>)';
		}
		if($showVotes){
			$text .= '(<span id="showvotes_'.$id.'" class="votesClass">'.getVotes($media, $id).'</span>)';
		}
		if($show5 || $showPerc || $showVotes){
			$text .= '';
		}
		return $text.'
		  <span class="inline-rating">
			<ul class="star-rating2" id="rater_'.$id.'">
			<li class="current-rating" style="width:'.getRating($media, $id).';" id="ul_'.$id.'"></li>
			<li><a onclick="return false;" title="1 star out of 5 '.$msg.'" class="one-star">1</a></li>
			<li><a onclick="return false;" title="2 stars out of 5 '.$msg.'" class="two-stars">2</a></li>
			<li><a onclick="return false;" title="3 stars out of 5 '.$msg.'" class="three-stars">3</a></li>
			<li><a onclick="return false;" title="4 stars out of 5 '.$msg.'" class="four-stars">4</a></li>
			<li><a onclick="return false;" title="5 stars out of 5 '.$msg.'" class="five-stars">5</a></li>
			</ul>
		  </span>
			</div>
			<div id="loading_'.$id.'"></div>';
	exit;
	}
	else{

		$sel = mysql_query("SELECT user_id FROM media_rating WHERE IP = '".$_SERVER['REMOTE_ADDR']."' AND media_type = '$media' AND media_id = '$id'");

		if(mysql_num_rows($sel) > 0 || $static == 'novote' || $_COOKIE['has_voted_'.$media.'_'.$id]) {
			if ($static == 'novote'){
				$msg = " Please Login to vote";
				$text = '';
			}else{
				$msg = " --  You have voted for this item";
				$text = '';
			}
			if($show5 || $showPerc || $showVotes){
				$text .= '<div class="rated_text">';
			}
			if($show5){
				$text .= '<b>Rated:&nbsp;</b> <span id="outOfFive_'.$id.'" class="out5Class">'.outOfFive($media, $id).'</span>/5';
			}
			if($showPerc){
				$text .= ' (<span id="percentage_'.$id.'" class="percentClass">'.getRating($media, $id).'</span>)';
			}
			if($showVotes){
				$text .= ' (<span id="showvotes_'.$id.'" class="votesClass">'.getVotes($media, $id).'</span>)';
			}
			if($show5 || $showPerc || $showVotes){
				$text .= '';
			}
			return $text.'
			<span class="inline-rating">
			<ul class="star-rating2" id="rater_'.$id.'">
				<li class="current-rating" style="width:'.getRating($media, $id).';" id="ul_'.$id.'"></li>
				<li><a onclick="return false;" title="1 star out of 5 '.$msg.'" class="one-star">1</a></li>
				<li><a onclick="return false;" title="2 stars out of 5 '.$msg.'" class="two-stars">2</a></li>
				<li><a onclick="return false;" title="3 stars out of 5 '.$msg.'" class="three-stars">3</a></li>
				<li><a onclick="return false;" title="4 stars out of 5 '.$msg.'" class="four-stars">4</a></li>
				<li><a onclick="return false;" title="5 stars out of 5 '.$msg.'" class="five-stars">5</a></li>
			</ul>
			</span>
			</div>
			<div id="loading_'.$id.'"></div>';
		}
		else {
			if($show5 || $showPerc || $showVotes){
				$text .= '<div class="rated_text">';
			}
			if($show5){
				$show5bool = 'true';
				$text .= '<b>Rated:&nbsp;</b><span id="outOfFive_'.$id.'" class="out5Class">'.outOfFive($media, $id).'</span>/5';
			} else {
				$show5bool = 'false';
			}
			if($showPerc){
				$showPercbool = 'true';
				$text .= ' (<span id="percentage_'.$id.'" class="percentClass">'.getRating($media, $id).'</span>)';
			} else {
				$showPercbool = 'false';
			}
			if($showVotes){
				$showVotesbool = 'true';
				$text .= ' (<span id="showvotes_'.$id.'" class="votesClass">'.getVotes($media, $id).'</span>)';
			} else {
				$showVotesbool = 'false';
			}
			if($show5 || $showPerc || $showVotes){
				$text .= '';
			}
			return $text.'
			<span class="inline-rating">
			<ul class="star-rating" id="rater_'.$id.'">
				<li class="current-rating" style="width:'.getRating($media, $id).';" id="ul_'.$id.'"></li>
				<li><a onclick="rate(\''.$media.'\',\'1\',\''.$id.'\','.$show5bool.','.$showPercbool.','.$showVotesbool.'); return false;" href="includes/rating_process.php?media='.$media.'&amp;id='.$id.'&amp;rating=1" title="1 star out of 5" class="one-star" >1</a></li>
				<li><a onclick="rate(\''.$media.'\',\'2\',\''.$id.'\','.$show5bool.','.$showPercbool.','.$showVotesbool.'); return false;" href="includes/rating_process.php?media='.$media.'&amp;id='.$id.'&amp;rating=2" title="2 stars out of 5" class="two-stars">2</a></li>
				<li><a onclick="rate(\''.$media.'\',\'3\',\''.$id.'\','.$show5bool.','.$showPercbool.','.$showVotesbool.'); return false;" href="includes/rating_process.php?media='.$media.'&amp;id='.$id.'&amp;rating=3" title="3 stars out of 5" class="three-stars">3</a></li>
				<li><a onclick="rate(\''.$media.'\',\'4\',\''.$id.'\','.$show5bool.','.$showPercbool.','.$showVotesbool.'); return false;" href="includes/rating_process.php?media='.$media.'&amp;id='.$id.'&amp;rating=4" title="4 stars out of 5" class="four-stars">4</a></li>
				<li><a onclick="rate(\''.$media.'\',\'5\',\''.$id.'\','.$show5bool.','.$showPercbool.','.$showVotesbool.'); return false;" href="includes/rating_process.php?media='.$media.'&amp;id='.$id.'&amp;rating=5" title="5 stars out of 5" class="five-stars">5</a></li>
			</ul>
			</span>
			</div>
			<div id="loading_'.$id.'"></div>';
		}
	}
}

/*_______________________________________________________________________________________________________________________________________________*/

/*_______________________________________________________________________________________________________________________________________________*/


///////////////////////////////////////////////////////////
//Error Display GENERIC ERROR - example user not logged in
///////////////////////////////////////////////////////////

function ErrorDisplay1($message){

GLOBAL $base_url;
    echo '
<div align="center">
	&nbsp;<p>&nbsp;</p>
	<table width="556" id="table1" style="border: 1px solid #FF3300; " cellspacing="0" cellpadding="0" bgcolor="#FFDDDD">
		<tr>
			<td bgcolor="#FFECEC">
			<table border="0" width="100%" id="table2" cellspacing="0" cellpadding="0">
				<tr>
					<td width="30">
			<img border="0" src="'.$base_url.'/images/icon_info.gif"></td>
					<td height="20">
					<p align="center"><font face="Arial" size="2">'.$message.'</font></td>
				</tr>
			</table>
			</td>
		</tr>
	</table>
</div>';
	}

/*__________________________________________________________________________________________________________________________________________*/
//Enabled/Disabled Features Redirect
/*__________________________________________________________________________________________________________________________________________*/

function DisabledFeatureRedirect($feature){
    GLOBAL $config;

//For Audio
if ($feature =='audio' && $config["enabled_features_audio"] != 'yes'){
		header('Location: '.$config["site_base_url"]);
		die();
	}

//For Blogs
if ($feature =='blogs' && $config["enabled_features_blogs"] != 'yes'){
		header('Location: '.$config["site_base_url"]);
		die();
	}

//For Images
if ($feature =='images' && $config["enabled_features_images"] != 'yes'){
		header('Location: '.$config["site_base_url"]);
		die();
	}
}


/*__________________________________________________________________________________________________________________________________________*/
// Random code generator
/*__________________________________________________________________________________________________________________________________________*/

function randomcode() {
	$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    	srand((double)microtime() * 1000000);
    	$i = 0;
    	$pass = '';

    	while ($i <= 31) {
    		$num = rand() % 62;
        	$tmp = substr($chars,$num,1);
        	$pass = $pass.$tmp;
        	$i++;
    	}
return $pass;
}

/*__________________________________________________________________________________________________________________________________________*/
// Error redirect
/*__________________________________________________________________________________________________________________________________________*/

function error_redirect($code) {

	if ( isset($_SERVER['HTTP_REFERER']) ) {
		$url = $_SERVER['HTTP_REFERER'];

	} else {
		$url= $config['site_base_url'].'/index.php';
	}

	if ( preg_match('/\?/',$url,$matches) ) {
		$referer = $url.'&code='.$code;				//"The member does not accept private messages."
        	@mysql_close();
        	header("Location: "."$referer");
        	die();

    	} else {
    		$referer = $url.'?code='.$code;				//"The member does not accept private messages."
        	@mysql_close();
        	header("Location: "."$referer");
    	}
}


/*__________________________________________________________________________________________________________________________________________*/
// Start generator
/*__________________________________________________________________________________________________________________________________________*/

function stars_array($vid) {

	$sql_stars		= "SELECT * FROM videos WHERE indexer = $vid";
    	$query_stars	= @mysql_query($sql_stars);
    	$result_stars	= @mysql_fetch_array($query_stars);
    	$stars 		= $result_stars['updated_rating'];

    	//default stars
    	if ($stars == 0) {
    		$star1 		= 'star_grey.gif';
        	$star2 		= 'star_grey.gif';
        	$star3 		= 'star_grey.gif';
        	$star4 		= 'star_grey.gif';
        	$star5 		= 'star_grey.gif';
        	$stars_array	= array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);
        	return $stars_array;
    	}
    	if ($stars == 1) {
        	$star1 		= 'star_red.gif';
        	$star2 		= 'star_grey.gif';
        	$star3 		= 'star_grey.gif';
        	$star4 		= 'star_grey.gif';
        	$star5 		= 'star_grey.gif';
        	$stars_array	= array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);
        	return $stars_array;
    	}
    	if ($stars == 2) {
      	$star1 		= 'star_red.gif';
        	$star2 		= 'star_red.gif';
        	$star3 		= 'star_grey.gif';
        	$star4 		= 'star_grey.gif';
        	$star5 		= 'star_grey.gif';
        	$stars_array	= array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);
        	return $stars_array;
    	}
    	if ($stars == 3) {
      	$star1 		= 'star_red.gif';
        	$star2 		= 'star_red.gif';
        	$star3 		= 'star_red.gif';
        	$star4 		= 'star_grey.gif';
        	$star5 		= 'star_grey.gif';
        	$stars_array	= array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);
        	return $stars_array;
    	}
    	if ($stars == 4) {
      	$star1 		= 'star_red.gif';
        	$star2 		= 'star_red.gif';
        	$star3 		= 'star_red.gif';
        	$star4 		= 'star_red.gif';
        	$star5 		= 'star_grey.gif';
        	$stars_array 	= array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);
        	return $stars_array;
    	}
    	if ($stars == 5) {
      	$star1 		= 'star_red.gif';
        	$star2 		= 'star_red.gif';
        	$star3 		= 'star_red.gif';
        	$star4 		= 'star_red.gif';
        	$star5 		= 'star_red.gif';
        	$stars_array 	= array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);
        	return $stars_array;
    	}

    	mysql_free_result($result_stars);
}


/*__________________________________________________________________________________________________________________________________________*/
// Media View Tracker - record views, usage, IP etc for media
/*__________________________________________________________________________________________________________________________________________*/

function media_views_tracker($id, $type) {  //type should be name of media tables in dbase

	if($id !='' && $type !='') {
		if ($type =='videos' || $type =='audios' || $type =='blogs' || $type =='member_profile' || $type =='images') {
			$users_ip = $ip=$_SERVER['REMOTE_ADDR'];
			$sql = "INSERT into views_tracker (ipaddress, location, media_type, media_id, date_viewed) VALUES ('$users_ip', 'unknown', '$type', '$id', NOW() )";
			@mysql_query($sql);
		}
	}
}

/*__________________________________________________________________________________________________________________________________________*/
// dynamic thumb sizes
/*__________________________________________________________________________________________________________________________________________*/

function show_thumb($thumb_used, $page_display_small_width) {

	list($width, $height)	= getimagesize( "$thumb_used" );

	if ( $width > $page_display_small_width ) {
		$large_img_ratio	= $width/$height;

		if ( $large_img_ratio > 1 ) {
           		$new_smallwidth	= $page_display_small_width;
            	$new_smallheight	= $page_display_small_width / $large_img_ratio;
            } else {
            	$new_smallheight	= $page_display_small_width;
            	$new_smallwidth	= $page_display_small_width * $large_img_ratio;
		}
	}

	if ( $height > $page_display_small_height ) {
		$large_img_ratio	= $width/$height;

     		if ( $large_img_ratio > 1 ) {
			$new_smallwidth	= $page_display_small_width;
			$new_smallheight	= $page_display_small_width / $large_img_ratio;
		} else {
			$new_smallheight	= $page_display_small_width;
			$new_smallwidth	= $page_display_small_width * $large_img_ratio;
		}
	}


	$new_smallwidth		= floor($new_smallwidth);
	$new_smallheight		= floor($new_smallheight);

	return array($new_smallwidth, $new_smallheight);
}

/*__________________________________________________________________________________________________________________________________________*/
// Generic Error - Redirect to main page with just a message (same as whats seen when a member joins)
/*__________________________________________________________________________________________________________________________________________*/

function generic_error($notification) { //do not change back to $message

global $user_theme, $inner_template1, $message, $error_height;

	$error_height 		= '288px;';
	$message 			= $notification; //do not delete
    	$template 			= "themes/$user_theme/templates/main_1.htm";
    	$inner_template1 		= "themes/$user_theme/templates/inner_notification.htm";
    	$TBS 				= new clsTinyButStrong;
    	$TBS->NoErr 		= true;

    	$TBS->LoadTemplate("$template");
    	$TBS->Render 		= TBS_OUTPUT;
    	$TBS->Show();
    	@mysql_close();
    	die();
}

/*__________________________________________________________________________________________________________________________________________*/
// safe tag words
/*__________________________________________________________________________________________________________________________________________*/

function make_tag_words( $tag_input, $word_length ) {

	$theme_max_tag_word_length = $word_length;

	// comma error check
	$tag_check_1 = strrchr($tag_input, ',');

	// space error check
	$tag_check_2 = strstr($tag_input, ' ');

	if ( $tag_check_1 > '' || $tag_check_2 == '' ) {

     		$error_message = $error_message . 'Please separate tag words with a space, and do not use commas';

		return array('false', $error_message );
		die();
	}

	// if ok check tag keyword lengths

	$keyword_array	= explode( ' ', $tag_input );
	$keyword_array	= array_unique($keyword_array);
	$safe_tags 		= '';

	foreach ( $keyword_array as $tag ) {

		if ( strlen($tag) <= $theme_max_tag_word_length ) {

			$tag = trim($tag);
			$tag = str_replace('"', "", $tag);
			$tag = str_replace("'", "", $tag);
			$tag = str_replace(".", "", $tag);

			// uncommnet this to use capitilized first letter tag word
			//$safe_tags .= ' ' . ucfirst($tag);

			$safe_tags .= ' ' . trim($tag);
		}
	}

	$safe_tags = clean($safe_tags, 68);
	return array( 'true', $safe_tags );
}

/*__________________________________________________________________________________________________________________________________________*/
// safe word wrap
/*__________________________________________________________________________________________________________________________________________*/

function safe_word_wrap($check_utf8) {

	// simple utf8 char match
	if ( !ereg ('&#', $check_utf8) ) {
   		$return_check_utf8 = wordwrap($check_utf8, 68, " ", true);
   		$return_check_utf8 = clean($return_check_utf8, 68);
   	} else {
   		$return_check_utf8 = $check_utf8;
   	}

   	// TODO match other chars



   	return ($return_check_utf8);

}

/*__________________________________________________________________________________________________________________________________________*/
// clean up some chars for word wrap
/*__________________________________________________________________________________________________________________________________________*/

function clean($text_in, $length) {

	$text_in		= trim($text_in);
	$text_in 		= strip_tags($text_in);
	$delete_char 	= array('title', 'description', 'tags', '=', '&', '\'', '"', '>', '<');
	$delete_replace 	= '';
	$text_in 		= str_replace($delete_char, $delete_replace, $text_in);
	$text_in		= wordwrap($text_in, $length, ":\n", TRUE);
	$cleaned_text	= explode(':', $text_in);

	foreach ( $cleaned_text as $cleaned_words ) $text_out .= $cleaned_words;

	return $text_out;
}
/*__________________________________________________________________________________________________________________________________________*/


/*__________________________________________________________________________________________________________________________________________*/
// clean up all bad input spaces
/*__________________________________________________________________________________________________________________________________________*/


////////////////////////////////////////////////////////////////////////////////////////////
// USAGE
//
// [if second argument is false the word length is not truncated
// [if second argument is numeric that it the max length of the returned word
// examples:
// $word_string = clean_spaces('  string of      words with  different number    of  spaces    ', false);
// $word_string = clean_spaces('  string of      words with  different number    of  spaces    ', 3);
//

function clean_spaces($words_in, $length)
{
	if ( $length ) {
		$words_check = preg_replace('/\s\s+/', ' ', trim($words_in));
		$words_len = explode( ' ', $words_check );
		foreach ( $words_len as $word => $value) {
			if ( strlen( $value ) > $length ) {
				// un comment this line to show the dots of truncated words
				//$word_fixed = substr( $value, 0, $length) . '...';
				$word_fixed = substr( $value, 0, $length);
				$words_out = $words_out . ' ' . $word_fixed;
			}
		}

	} else {
		$words_out = preg_replace('/\s\s+/', ' ', trim($words_in));
	}

return $words_out;

}



function utf_check($string_in) {

	//$string_in = htmlentities($string_in, ENT_NOQUOTES, 'UTF-8');
	//echo $string_in;

	$uft_count = 0;

	$string_in = htmlentities($string_in);

	$utf_1 = strstr($string_in, 'amp');

	if ( $utf_1 ) $proceed = 'false';

	return $proceed;
}



?>