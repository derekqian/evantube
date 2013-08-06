<?php ///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once ('classes/config.php');
include_once ('classes/sessions.php');
include_once ('includes/enabled_features.php');

if (isset($_GET['page']))
    $page = (int)mysql_real_escape_string($_GET['page']);
else
    $page = 1;

// get left menu listing--------------------------------
$albums_display_limit = 7;
$pagination = pagination("SELECT * FROM image_galleries WHERE has_images = '1' AND approved='yes' $sql_public_private ORDER BY gallery_id DESC",
    $albums_display_limit);
$set_limit = $pagination[0]['set_limit'];
$total_pages = $pagination[0]['total_pages'];
$current_page = $pagination[0]['current_page'];
$total_records = $pagination[0]['total_records'];
$next_page = $pagination[0]['next_page'];//use in html navigation (src)
$prev_page = $pagination[0]['prev_page'];//use in html navigation (src)
$nl = $pagination[0]['nl'];//use in html navigation: next>>
$pl = $pagination[0]['pl'];//use in html navigation: <<previous

$sql = "SELECT * FROM image_galleries WHERE has_images = '1' AND approved='yes' $sql_public_private ORDER BY gallery_id DESC  LIMIT $set_limit, $albums_display_limit";
$query = @mysql_query($sql);

while ($result = @mysql_fetch_array($query)) {
    $gallery_id = (int)mysql_real_escape_string($result['gallery_id']);
    $sql0 = "SELECT * FROM images WHERE gallery_id = '$gallery_id' AND approved = 'yes' $sql_public_private ORDER BY indexer DESC";
    $query0 = @mysql_query($sql0);
    $count_images = mysql_num_rows($query0);
    $img_count = array('img_count' => $count_images);
    $new_array = @array_merge($result, $img_count);
    $albums_list[] = $new_array;
}

if (sizeof($albums_list) > 0)
    $show_albums = 1;
else
    $show_albums = 0;

    	// PAGINATION PLUS >> start  -- reusable code
       $hide_numbering = true; //show only <<previous  Next>>>
       $url = 'javascript:void(0)" onClick="javascript:ahahscript.ahah(\'albums_ajax.php';//the url to be put in links - EDIT ME
       $ahah_pagination = "', 'Ajax-Albums', '', 'GET', '', this);";//for ajax pagination
       $additional_url_variable = '?page=';//add addtions information that goes in query string here
       include ('includes/pagination.inc.php');
	   $ablums_pagination = $show_pages; 	
       // PAGINATION PLUS >> end

$template = "themes/$user_theme/templates/inner_albums_ajax.htm";
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;
$TBS->LoadTemplate("$template");
$TBS->MergeBlock('mp', $all_albums);
$TBS->MergeBlock('blk1', $albums_list);
$TBS->Render = TBS_OUTPUT;
$TBS->tbs_show();
@mysql_close();
die();
?>

