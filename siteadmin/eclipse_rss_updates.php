<?php

    //get RSS - Updates
    /////////////////////////

    $rss = new lastRSS;
    $rss->cache_dir = '';
    $rss->cache_time = 3600;// one hour
    $rs = $rss->get('http://phpmotionwiz.com/forum/index.php?action=.xml;board=23.0;type=rss;limit=10');
    $rss_updates = '';
    foreach ($rs['items'] as $item)
    {
        $rss_updates1 = '
	<li><a href="' . $item[link] . '" target="_blank"><h4>' . $item['title'] . '
	</h4></a><span>(Published: ' . $item['pubDate'] . ')</span>
	</li>
	<li>&nbsp;</li>
	<li>' . $item['description'] . '</li>
	<li><a href="' . $item[link] . '" target="_blank">Read More...</a></li>
	<li class="separator">&nbsp;</li>';

        $rss_updates = $rss_updates . $rss_updates1;
    }

?>