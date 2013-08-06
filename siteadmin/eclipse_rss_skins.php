<?php

    //get RSS - Skins
    /////////////////////////

    $rss = new lastRSS;
    $rss->cache_dir = '';
    $rss->cache_time = 3600;// one hour
    $rs = $rss->get('http://phpmotionwiz.com/index.php?option=com_ose_rss&view=feed&feedID=2;limit=10');
    $rss_skins = '';
    foreach ($rs['items'] as $item)
    {
        $rss_skins1 = '
	<li><a href="' . $item[link] . '" target="_blank"><h4>' . $item['title'] . '
	</h4></a><span>(Published: ' . $item['pubDate'] . ')</span>
	</li>
	<li>&nbsp;</li>
	<li>' . $item['description'] . '</li>
	<li class="separator">&nbsp;</li>';

        $rss_skins = $rss_skins . $rss_skins1;
    }

?>