<?php

//******************************************************************************************************
//   ATTENTION: THIS FILE HEADER MUST REMAIN INTACT. DO NOT DELETE OR MODIFY THIS FILE HEADER.
//
//   Name: uploader_conlib.php
//   Revision: 1.6
//   Date: 01/03/2007 7:17PM
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
include_once ('classes/config.php');
$UBER_VERSION               = "4.6";
$path_to_upload_script      = '/cgi-bin/uu_upload.pl';
$path_to_ini_status_script  = '/cgi-bin/uu_ini_status.pl';
$default_config_file        = 'uu_default_config';
$disallow_extensions        = '/(3gp|vob|part|swf|mvb|sh|php|php3|php4|php5|py|shtml|phtml|cgi|pl|plx|htaccess|htpasswd|htm|html|doc|txt|xml|js|zip|tar|rar|tgz|bzip|exe|jpg|jpeg|gif|png|bmp|xls|csv|sql|dat)$/i';
$allow_extensions           = '/(mpg|mpeg|avi|divx|mp4|flv|wmv|rm|mov|moov|asf)$/i';
$MULTI_CONFIGS_ENABLED      = 0;
$multi_upload_slots         = 0;
$embedded_upload_results    = 0;
$max_upload_slots           = 1;
$check_file_name_format     = 1;
$check_disallow_extensions  = 1;
$check_allow_extensions     = 1;
$check_null_file_count      = 1;
$check_duplicate_file_count = 1;
$progress_bar_width         = 400;

/////////////////////////////////////////
// Output a message to screen and exit.
////////////////////////////////////////
function kak($msg){
	print "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
	print "  <html>\n";
	print "    <head>\n";
	print "      <title>Uber-Uploader - Free File Upload Progress Bar</title>\n";
	print "      <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">\n";
	print "      <meta http-equiv=\"Pragma\" content=\"no-cache\">\n";
	print "      <meta http-equiv=\"cache-control\" content=\"no-cache\">\n";
	print "      <meta http-equiv=\"expires\" content=\"-1\">\n";
	print "      <meta name=\"robots\" content=\"none\">\n";
	print "    </head>\n";
	print "    <body style=\"background-color: #EEEEEE; color: #000000; font-family: arial, helvetica, sans_serif;\">\n";
	print "	     <br>\n";
	print "      <div align='center'>\n";
	print        $msg . "\n";
	print "      <br>\n";
	print "      <a href=\"http://sourceforge.net/projects/uber-uploader\"><font size='1'>Powered By Uber Uploader</font></a>\n";
	print "      </div>\n";
	print "    </body>\n";
	print "  </html>\n";
	exit;
}

///////////////////////////////////////////////////////////////////////////////
// Return the current size of the $_GET['temp_dir_sid'] - flength file size. //
///////////////////////////////////////////////////////////////////////////////
function GetBytesRead($tmp_dir){
	$bytesRead = 0;
	$files = 0;
	$report = array();

	if(is_dir($tmp_dir)){
		if($handle = opendir($tmp_dir)){
			while(false !== ($file = readdir($handle))){
				if($file != '.' && $file != '..' && $file != 'flength'){
					$bytesRead += filesize($tmp_dir . '/' . $file);
					$files++;
				}
			}
			closedir($handle);
		}
	}

	$files -= 1;

	if($files < 0){ $files = 0; }

	$report[0] = $files;
	$report[1] = $bytesRead;

	return $report;
}
?>