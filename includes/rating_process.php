<?
header("Cache-Control: no-cache");
header("Pragma: nocache");
$expire = time() + 99999999;
$domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false; // make cookies work with localhost

$ip = $_SERVER['REMOTE_ADDR'];

include_once ('../classes/config.php');
include_once ('../classes/sessions.php');

if ( $user_id == "" ) {
	$msg = " Please Login to vote";
} else {
	$user = $user_id;
}

if($_POST) {
	$id 		= (int) $_POST['id'];
	$rating 	= (int) $_POST['rating'];
	$media 	= $_POST['media'];

	if ( $media == 'member_profile' ) {
		$indexer_name = 'user_id';
	} else {
		$indexer_name = 'indexer';
	}

	$flag_sql	= "SELECT * FROM media_rating WHERE media_type = $media AND media_id = $id AND user_id = $user";
	$flag_query = @mysql_query($flag_sql);
	$flag_count = @mysql_num_rows($flag_query);
	if ( $flag_count != 0 ) {
		@mysql_close();
		echo 'Voted Already';
	} else {
		setcookie('has_voted_'.$media.'_'.$id, $id, $expire, '/', $domain, false);
		$flag_sql	= "INSERT INTO media_rating (user_id, IP, media_id, media_type) VALUES ('$user', '$ip', '$id', '$media')";
		$flag_query = @mysql_query($flag_sql);

		$sql		= "SELECT rating_number_votes, rating_total_points FROM $media WHERE $indexer_name = $id"; // AND approved='yes'";
		$query 	= @mysql_query($sql);
		$count 	= @mysql_num_rows($query);
		if ( $count == 0 ) {
			@mysql_close();
			echo 'Your request could not be completed';
		} else {
			$result			= @mysql_fetch_array($query);
			$rating_number_votes 	= $result['rating_number_votes'] + 1;
			$rating_total_points 	= $result['rating_total_points'] + $rating; //$rate_video_rating;
			$updated_rating 		= $rating_total_points / $rating_number_votes;
			$sql 				= "UPDATE $media SET rating_number_votes = $rating_number_votes, rating_total_points = $rating_total_points, updated_rating = $updated_rating WHERE $indexer_name = $id";
			@mysql_query($sql);
		}

		$sel = mysql_query("SELECT * FROM $media WHERE $indexer_name = '$id'");
		while( $data = mysql_fetch_assoc($sel) ) {
			$stars			= $data['updated_rating'];
			$num_votes			= $data['rating_number_votes'];
			$total_points		= $data['rating_total_points'];
		}
		$perc = ($total_points/$num_votes) * 20;
		echo round($perc,2);
	}
}
?>
