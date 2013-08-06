<?php

    //get RSS feed from forum
    /////////////////////////

    $rss = new lastRSS;
    $rss->cache_dir = '';
    $rss->cache_time = 3600;// one hour
    $rs = $rss->get('http://phpmotion.com/forum/index.php?board=2.0&type=rss;action=.xml;limit=3');
    $rss_output = '';
    foreach ($rs['items'] as $item)
    {
        $rss_output1 = '
	<li><a href="' . $item[link] . '"><b>' . $item['title'] . '
	</b></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (Published: ' . $item['pubDate'] .
            ')
	</li>
	<li>&nbsp;</li>
	<li>' . $item['description'] . '</li>
	<li><p align="center">---------------------------------------------------------------------------------------------------------------------</li>';

        $rss_output = $rss_output . $rss_output1;
    }

?>