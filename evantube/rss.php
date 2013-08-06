<?php

include_once ('classes/config.php');

header ("Content-type: text/xml; charset=utf-8");

?>

<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">

<channel>
  <title><![CDATA[<?=$config["site_name"]; ?>]]></title>

  <link><?=$config["site_base_url"]; ?></link>
  <image>
    <title><![CDATA[<?=$config["site_name"]; ?>]]></title>
    <description><?=$config["site_name"]; ?></description>
    <url><?=$config["site_base_url"]; ?>/themes/default/images/logo-main.gif</url>
    <link><?=$config["site_base_url"]; ?></link>
  </image>

  <description><?=$config["site_name"]; ?></description>

<?

$sql = "SELECT * FROM videos WHERE approved = 'yes' AND public_private = 'public' ORDER BY number_of_views DESC limit 100" ;
$result = mysql_query($sql);
while($row = mysql_fetch_assoc($result)){

	$title_seo = $row['title_seo'];

	$title = $row['title'];
	$title = str_replace("&", "and", $title);
	$title = str_replace("?", "", $title);
?>

	<item>

		<title><![CDATA[<?=$title;?>]]></title>
		<link><![CDATA[<?=$config["site_base_url"]; ?>/videos/<?=$row['indexer']; ?>/<?=$title_seo; ?>]]></link>

		<description>
			<![CDATA[<img src="<?=$config["site_base_url"]; ?>/uploads/thumbs/<?=$row['video_id']; ?>.jpg" height="90" width="120" alt="Riled ThumbNail" align="left" hspace="3" vspace="3">
			<p><b>Description:</b></p>
			<p><?=$row['description']; ?></p>
			<p><b>Duration:</b> <?=$row['video_length']; ?> / <b>Uploaded:</b> <?=$row['date_uploaded']; ?></p>]]>
		</description>
		<category><![CDATA[Tests]]></category>
		<guid isPermaLink="true"><?=$config["site_base_url"]; ?>/videos/<?=$row['indexer']; ?>/<?=$title_seo; ?></guid>
	</item>
<?
}
?>

<atom:link href="<?=$config["site_base_url"]; ?>/rss.php" rel="self" type="application/rss+xml" />
</channel>
</rss>