<?php

include_once ("../classes/config.php");
include_once ("includes/inc.stats.php");
include_once ("includes/functions.php");
include_once ("includes/login_check.php");
include_once ("includes/menuloader.php");

/////////////////////
//default settings //
/////////////////////
$show_hide = 1;
$show_notification = 0;
$notice = '';

function notice($notice, $success=0) {
	return '<br /><span style="font-weight:bold;color:' . (empty($success) ? 'red' : 'green' ) . '">' . $notice . '</span>';
}

foreach ($config as $ck=>$cv) $$ck = $cv;

if ($_POST['settings'] == 'valid') {
	$post_skin = mysql_real_escape_string($_POST['eclipse_skin']);
	$post_player = mysql_real_escape_string($_POST['eclipse_player']);
	$post_disqus = mysql_real_escape_string($_POST['dq_shortname']);
	$post_fburl = mysql_real_escape_string($_POST['soc_fburl']);
	$post_twurl = mysql_real_escape_string($_POST['soc_twurl']);
	$post_gpurl = mysql_real_escape_string($_POST['soc_gpurl']);
	$post_settings = '';
	foreach ($_POST as $k => $v) {
		if ($k != 'eclipse_skin' && $k != 'eclipse_player' && $k != 'dq_shortname' && $k != 'soc_fburl' && $k != 'soc_twurl' && $k != 'soc_gpurl' && $k != 'settings') {
			$k = mysql_real_escape_string($k);
			$post_settings .= '	$config['.'\''.$k.'\''.'] = '.(int)$v.';'."\n";
		}
	}
	$settings_content = 
<<<SETTINGS
<?php\n\n$post_settings\n\n
	\$eclipse_skin = '$post_skin';\n
	\$eclipse_player = '$post_player';\n
	\$dq_shortname = '$post_disqus';\n\n
	\$soc_fburl = '$post_fburl';\n
	\$soc_twurl = '$post_twurl';\n
	\$soc_gpurl = '$post_gpurl';\n	
SETTINGS;
	$post_path = "$base_path/addons/mw_eclipse/settings/";
	$post_file = $post_path.'eclipse_settings.php';
	if (!is_writable($post_path)) $notice = notice("Your $post_path directory is not writable. Please correct this and try again.");
	else {
		$updated = true;
		rename($post_file, $post_file.'.bak');
		$open_file = fopen($post_file,'wb') or $updated = false;
		fwrite($open_file, $settings_content) or $updated = false;
		fclose($open_file) or $updated = false;
		if ($updated == true) {
			unlink($post_file.'.bak');
			$notice = notice("Settings updated successfully!",1);
			$settings_array=explode("\n", file_get_contents($post_file));
			foreach ($settings_array as $k=>$v) {
				if (stripos($v, '$config[') !== false) {
					$cfg=explode("'",$v);
					$$cfg[1]=(int)str_replace('] =','',$cfg[2]);
				} elseif (stripos($v, '$') !== false) {
					$key = explode(" ",str_replace('$','',trim($v)));
					$cfg = explode("'",$v);
					$$key[0] = $cfg[1];
				}
			}
		} else {
			rename($post_file.'.bak', $post_file);
			$notice = notice("Update failed. Previous settings have been restored.");
		}
	}
}

$skins_directory = "$base_path/addons/mw_eclipse/skin/css/";
$open_skins = opendir($skins_directory);
$valid_skins = array();
while (($skin = readdir($open_skins)) !== false) {
	$file_path = $skins_directory.$skin;
	$file_type = filetype($file_path);
	$file_extension = end(explode('.', $skin));
	$file_parts = explode('.', $skin);
	$skin_name = $file_parts[0];
	$skin_name_view = ucwords(str_replace('_',' ',$skin_name));
	if ($skin != '.' && $skin != '..' && $file_type == 'file' && $file_extension =='css' && $skin_name != 'suckerfish') {
		$valid_skins["$skin_name"] = "$skin_name_view";
    }
}
closedir($skins_directory);
ksort($valid_skins);
$eclipse_skins = '<select name="eclipse_skin">';
foreach ($valid_skins as $name => $name_view) {
	$selected = $name == $eclipse_skin ? ' selected="selected"' : '';
	$eclipse_skins .= '<option value="' . $name . '"' . $selected . '>' . $name_view . '</option>';
}
$eclipse_skins .= '</select>';

$player_directory = "$base_path/addons/mw_eclipse/players/";
$open_players = opendir($player_directory);
$valid_players = array();
while (($player = readdir($open_players)) !== false) {
	$file_path = $player_directory.$player;
	$file_type = filetype($file_path);
	$player_name_view = ($player == 'pmplayer' ? 'Default Player' : ($player == 'jwplayer' ? 'JW Player' : ($player == 'flowplayer' ? 'Flowplayer' : $player)));
	if ($player != '.' && $player != '..' && $file_type == 'dir') {
		$valid_players["$player"] = "$player_name_view";
    }
}
closedir($player_directory);
ksort($valid_players);
$eclipse_players = '<select name="eclipse_player">';
foreach ($valid_players as $name => $name_view) {
	$selected = $name == $eclipse_player ? ' selected="selected"' : '';
	$eclipse_players .= '<option value="' . $name . '"' . $selected . '>' . $name_view . '</option>';
}
$eclipse_players .= '</select>';

////////////////////////////////
//display form with error message
////////////////////////////////
$inner_template1 = "templates/eclipse-admin_settings.html"; //middle of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true; // no more error message displayed.
$TBS->LoadTemplate("templates/main.html");
$TBS->Render = TBS_OUTPUT;
$TBS->Show();
@mysql_close();
die();
?>