<?php

//-----------------------------------------------------------------//
//
// PHPmotion Installation Wizard - Version 2
//
//-----------------------------------------------------------------//

include_once (dirname(__file__) . '/includes/config.php');
include_once (dirname(__file__) . '/includes/view.php');
include_once (dirname(__file__) . '/includes/mysql.php');
include_once (BASE_PATH . '/classes/mysql.inc.php');

$visibility = visibility('details',$_SESSION['prescan_error']);


//connect to database
$dbase = new QuickMysql($config["dbname"],$config["dbusername"],$config["dbpassword"],
    $config["hostname"]);

//set the ffmpeg as found earlier
$ffmpeg = ($_SESSION['ffmpeg'] == '')?'' : $_SESSION['ffmpeg'];
$flvtool2 = ($_SESSION['flvtool2'] == '')?'' : $_SESSION['flvtool2'];
$mencoder = ($_SESSION['mencoder'] == '')?'' : $_SESSION['mencoder'];

if($dbase->obj_status) {

    if($_POST['forms'] == 'details') {

        //get all vars
        foreach($_POST as $key => $value) {
            $$key = $value;
        }

        $result = array($_POST);

        $proceed = true;

        //_________check if form filled in________
        $process = new FormProcessor();
        if(!$process->RequiredFields('all')) {
            $notifications = 1;
            $notice = "Error! - Fill in all form fields";
            $proceed = false;
        }


        //_________check domain name is not domain.com________
        if($proceed && $site_base_url == 'http://www.yourdomain.com') {
            $notifications = 1;
            $notice = "Error! - Enter your correct domain name";
            $proceed = false;
        }


        //_________check admin email________
        if($proceed && !$process->CheckFormatEmail('admin_email')) {
            $notifications = 1;
            $notice = "Error! - Invalid admin email address";
            $proceed = false;
        }

        //_________check system email________
        if($proceed && !$process->CheckFormatEmail('notifications_from_email')) {
            $notifications = 1;
            $notice = "Error! - Invalid system email address";
            $proceed = false;
        }

        //_________check admin email________
        if($proceed && !$process->CheckFieldMatch('admin_password, admin_password_confirm')) {
            $notifications = 1;
            $notice = "Error! - Passwords do not match";
            $proceed = false;
        }


        //_________everything is fine - finish________
        if($proceed) {
            $site_name = mysql_real_escape_string($_POST['site_name']);
            $site_base_url = mysql_real_escape_string($_POST['site_base_url']);
            //remove starting /
            if(substr($site_base_url,0,1) == '/') {
                $site_base_url = substr($site_base_url,1);
            }
            $date_format = mysql_real_escape_string($_POST['date_format']);
            $path_to_ffmpeg = mysql_real_escape_string($_POST['path_to_ffmpeg']);
            $path_to_flvtool2 = mysql_real_escape_string($_POST['path_to_flvtool2']);
            $path_to_mencoder = mysql_real_escape_string($_POST['path_to_mencoder']);
            $notifications_from_email = mysql_real_escape_string($_POST['notifications_from_email']);
            $from_system_name = mysql_real_escape_string($_POST['from_system_name']);



            //Update Database
            $dbase->UpdateRecord("UPDATE general_settings SET
                                    site_name	='$site_name',
									site_base_url	='$site_base_url',
									date_format	='$date_format',
									path_to_ffmpeg	='$path_to_ffmpeg',
									path_to_flvtool2	='$path_to_flvtool2',
									path_to_mencoder	='$path_to_mencoder',
									notifications_from_email	='$notifications_from_email',
									from_system_name	='$from_system_name'");


            //set password etc
            $random_code = random_code();
            $password = md5($admin_password);
            $passwordSalt = substr(md5(rand()),0,4);


            //add the admin user
            $dbase->InsertRecord("INSERT into member_profile (
		                      user_name, password, passwordSalt, email_address, account_status, account_type, date_created, random_code, user_group
				              ) VALUES (
				              '$admin_username', '$password', '$passwordSalt', '$admin_email', 'active', 'standard', NOW(), '$random_code', 'admin')");

            //create admin privacy
            $dbase->InsertRecord("INSERT INTO privacy (
		                      videocomments, profilecomments, privatemessage, friendsinvite, newsletter, user_id, publicfavorites, publicplaylists
							  ) VALUES (
							  'yes', 'yes', 'yes', 'yes', 'yes', 1, 'yes', 'yes')");


            //check mysql errors
            if(!$dbase->obj_status) {
                $notifications = 1;
                $notice = "Error! - Mysql error: " . $dbase->last_error;
                $proceed = false;
            }


            //write to file
            if($proceed) {

                include_once (BASE_PATH . '/siteadmin/configbuilder.php');
                $myFile = BASE_PATH . '/classes/settings.php';
                @unlink($myFile);
                $fh = @fopen($myFile,'wb'); //or $file = false;
                @fwrite($fh,$final) or $file = false;
                @fclose($fh);

                if(!is_file($myFile)) {
                    $notifications = 1;
                    $notice = "Error! - Unable to create settings file:  $myFile";
                }
            }


            //______________________________________________We have finished__________________________________________
            if($proceed) {
            	setupRec(1);
                $visibility = visibility('finished',$_SESSION['prescan_error']);
            }




        }




    } else {

        //__________Show initial page_____________
        $result = $dbase->SelectRecordLoop("SELECT * FROM general_settings");
        $admin_username = 'admin';
        $admin_email = '';

        if(!$dbase->result_status) {
            $notifications = 1;
            $notice = "Mysql Error! - $mysql_error";
        }


    }
} else {


    //__________unable to connect to database_____________
    $notifications = 1;
    $notice = "Error! - $mysql_error";

}


//____visibility items put in vars)_____
foreach($visibility as $key => $value) {
    $$key = $value;
}




//____Display View________________________
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;
$TBS->LoadTemplate(MAIN_TEMPLATE);
$TBS->MergeBlock('adminblk',$result);
$TBS->Render = TBS_OUTPUT;
$TBS->Show();

?>