<?php

//******************************************************************************************************
//   ATTENTION: THIS FILE HEADER MUST REMAIN INTACT. DO NOT DELETE OR MODIFY THIS FILE HEADER.
//
//   Name: uploader_status.php
//   Revision: 1.2
//   Date: 21/01/2007 3:52PM
//   Link: http://uber-uploader.sourceforge.net  
//   Initial Developer: Peter Schmandra
//   Description: Gather stats on an existing upload
//
//   Licence:
//   The contents of this file are subject to the Mozilla Public
//   License Version 1.1 (the "License"); you may not use this file
//   except in compliance with the License. You may obtain a copy of
//   the License at http://www.mozilla.org/MPL/
// 
//   Software distributed under the License is distributed on an "AS
//   IS" basis, WITHOUT WARRANTY OF ANY KIND, either express or
//   implied. See the License for the specific language governing
//   rights and limitations under the License.
//
//***************************************************************************************************************

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// The following possible query string formats are assumed
//
// 1. ?temp_dir_sid=path_to_temp_dir&start_time=upload_start_time&total_upload=total_upload_size&cedric_progress_bar=1or0&rnd_id=some_random_number 
// 2. ?cmd=about
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//load the needed javascripts in inner_main.htm
$which_java1 = "phphomtion_uploader.js";
$which_java2= "uploader_bar.js";

require "uploader_conlib.php";

$THIS_VERSION = "1.2";

if(isset($_GET['cmd']) && $_GET['cmd'] == 'about'){ kak("<u><b>UBER UPLOADER GET PROGRESS BAR STATUS</b></u><br>UBER UPLOADER VERSION =  <b>" . $UBER_VERSION . "</b><br>UU_GET_STATUS = <b>" . $THIS_VERSION . "<b><br>\n"); }
elseif(!isset($_GET['temp_dir_sid']) || !isset($_GET['start_time']) || !isset($_GET['total_upload_size'])){ kak("<font color='red'>ERROR</font>: Invalid parameters passed<br>"); }

$status = GetBytesRead($_GET['temp_dir_sid']);
$uploaded_files = $status[0];
$bRead = $status[1];
$flength_file = $_GET['temp_dir_sid'] . "/flength";
$lapsed = time() - $_GET['start_time'];
$bSpeed = 0; 
$remaining = 0;

if($lapsed > 0){ $bSpeed = $bRead / $lapsed; }
if($bSpeed > 0){ $remaining = round(($_GET['total_upload_size'] - $bRead) / $bSpeed); }

//If Cedric progress bar is enabled, calculate smooth speeds
if($_GET['cedric_progress_bar']){
	if($bSpeed != 0){    
		$info_time_width = round($_GET['total_upload_size'] * 1000 /($bSpeed * $progress_bar_width));
		$info_time_bytes = round(1024000 / $bSpeed);
	}
	else{ 
		$info_time_width = 200;    
		$info_time_bytes = 15;
	}
}

// Calculate percent finished
$percent_float = $bRead / $_GET['total_upload_size'];
$info_width = round($percent_float * $progress_bar_width);
$percent = round(100 * $percent_float);

// Calculate time remaining
$remaining_sec = ($remaining % 60); 
$remaining_min = ((($remaining - $remaining_sec) % 3600) / 60); 
$remaining_hours = (((($remaining - $remaining_sec) - ($remaining_min * 60)) % 86400) / 3600); 

if($remaining_sec < 10){ $remaining_sec = "0$remaining_sec"; }
if($remaining_min < 10){ $remaining_min = "0$remaining_min"; }
if($remaining_hours < 10){ $remaining_hours = "0$remaining_hours"; }

$remainingf = "$remaining_hours:$remaining_min:$remaining_sec"; 

if(is_dir($_GET['temp_dir_sid']) && is_file($flength_file) && $bRead < $_GET['total_upload_size']){	
	$speed = $lapsed ? round($bRead / $lapsed) : 0;
	$speed = round($speed / 1024);
	$bRead = round($bRead /= 1024);	
}
else{
	echo "get_data_loop = false;";
	echo "hideProgressBar();";
	exit;
}
?>

<? if($_GET['cedric_progress_bar']){ ?>
updateCedricStatus(<? echo $info_width; ?>,<? echo $bRead; ?>);
info_time_width = <? echo $info_time_width; ?>;
info_time_bytes = <? echo $info_time_bytes; ?>;
<? }else{ ?>
document.getElementById('upload_status').style.width = <? echo $info_width; ?>+'px';
document.getElementById('current').innerHTML = <? echo $bRead; ?>;
<? } ?>
document.getElementById('percent').innerHTML = <? echo $percent; ?>+'%';
document.getElementById('uploaded_files').innerHTML = <? echo $uploaded_files; ?>;
document.getElementById('remain').innerHTML = "<? echo $remainingf; ?>";
document.getElementById('speed').innerHTML = <? echo $speed; ?>;