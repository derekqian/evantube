<?php
include_once ("../classes/config.php");
include_once ("includes/inc.stats.php");
include_once ("includes/functions.php");
include_once ("includes/login_check.php");
include_once ("thirdparty/lastRSS.php");
include_once ("includes/menuloader.php");
$rss_url = 'http://motionmods.com';
$rss = new lastRSS;
$rs = $rss->get("$rss_url/feed");
$rss_mods = '';
foreach ($rs['items'] as $item){
	$link = "$rss_url/".strtolower(str_replace(' ','-',$item['title']));
	$desc = strip_tags(html_entity_decode($item['description']),'<b><strong><span><br>');
	if(preg_match( '/src="([^"]*)"/',html_entity_decode($item['description']),$imgs)){
		$res = explode('"', $imgs[0]);
		$img_url = parse_url($res[1], PHP_URL_PATH);
		$dsc = '<a href="'.$link.'" target="_blank">';
		$dsc.= '<img style="width:90px;float:left;margin:0 10px" src="'.$rss_url.$img_url.'" style="" /></a>';
		$desc= $dsc.str_replace('productPrice','productPrice" style="line-height:26px;font-size:11px"',$desc);
	}
	$rss_mods.='<li><a href="'.$link.'" target="_blank"><h4>'.$item['title'].'</h4></a><span>(Published: '.$item['pubDate'].')</span></li>'.
	'<li>&nbsp;</li><li>'.$desc.'</li><li><a href="'.$link.'" target="_blank">Read More...</a></li>'.
	'<li class="separator" style="clear:both">&nbsp;</li>';
}
$show_hide = 1;
$show_notification = 0;
$todays_date = $config["date_format"];
$base_url = $config['site_base_url'];
$inner_template1 = "templates/eclipse-mm-mods.html";
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;
$TBS->LoadTemplate("templates/main.html");
$TBS->Render = TBS_OUTPUT;
$TBS->Show();
die();
?>