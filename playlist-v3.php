<?php
///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once ('classes/config.php');

$limit	= 12;
$sql		= "SELECT indexer, video_id, title, description FROM videos WHERE approved = 'yes' AND public_private = 'public' AND video_type = 'uploaded' ORDER BY indexer DESC LIMIT $limit";
$query 	= @mysql_query($sql);

header("Content-Type: text/xml; charset=UTF-8");
header("Expires: 0");
print "<?xml version=\"1.0\"?>\n";

echo "<asx version=\"3.0\">\n";
echo "<title>$base_url Playlist</title>\n";
echo "<info>$base_url</info>\n";

while ($result = @mysql_fetch_array($query)) {

	$title		= $result['title'];
	$description	= $result['description'];
	$vid			= $result['video_id'];
	$title		= trim($title);
	$title		= str_replace('"', "", $title);
	$title		= str_replace("'", "", $title);
	$title		= str_replace("&", "", $title);
	$description	= trim($description);
	$description	= str_replace('"', "", $description);
	$description	= str_replace("'", "", $description);
	$description	= str_replace("&", "", $description);


	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// THIS IS TMP AS we now have small player thumbs so the old vids, the player wont find large thumbs player large thumb

    	$player_thumb_dir		= '/player-thumbs/';
    	$large_player_thumb	= $vid.'.jpg';
    	$thumb_file			= "uploads/player_thumbs/$large_player_thumb";

    	if ( !file_exists($thumb_file) ) {
	    	$thumb_file = $base_url . '/uploads/thumbs/' . $vid.'.jpg';
    	}

    	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	print "<entry>\n";
		print "<title>$title</title>\n";
		print "<abstract>$description</abstract>\n";
		print "<ref href=\"$base_url/uploads/$vid.flv\" />\n";
		print "<param name=\"image\" value=\"$thumb_file\" />\n";
	print "</entry>\n";
}

@mysql_close();

print "</asx>\n";

exit();

?>

