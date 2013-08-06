<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Version: PHPmotion V2.0 beta                                                      //
// This file last modified:  07 Jan 2008                                             //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

$site_news = "";
$Newsflash = "";

$sql_news = "SELECT * FROM news_flash WHERE publish = 'yes'";
$query_news = @mysql_query($sql_news);

while ($result_news = @mysql_fetch_array($query_news)) {
	$news_headlines = $result_news['news_headline'];
	$news_id = $result_news['news_id'];
    	$news_picture = $result_news['news_picture'];
    	$newsflash .= '<li><a href="news.php?id='.$news_id.'">'. $news_headlines . '</a></li>';
}

?>