<?php

//-----------------------------------------------------------------//
//
// PHPmotion Installation Wizard - Version 2
//
//-----------------------------------------------------------------//

include_once (dirname(__file__) . '/includes/config.php');
include_once (dirname(__file__) . '/includes/view.php');
include_once (dirname(__file__) . '/includes/mysql.php');

$visibility = visibility('mysql',$_SESSION['prescan_error']);


//pedefined
$database_host = '';
$database_name = '';
$database_username = '';
$database_password = '';

if($_POST['forms'] == 'mysql') {

    //get all vars
    foreach($_POST as $key => $value) {
        $$key = $value;
    }

    //_________check if form filled in________
    $process = new FormProcessor();
    if(!$process->RequiredFields('all')) {
        $notifications = 1;
        $notice = "Error! - Fill in all form fields";
        $proceed = false;

    } else {

        //______check connection_____
        $dbase = new QuickMysql($database_name,$database_username,$database_password,$database_host);
        if($dbase->obj_status) {


            //_______loadup mysql file_________
            $mysql_file = SETUP_PATH . '/mysql.sql';
            $file_content = file($mysql_file);
            $query = "";
            $error_count = 0;
            foreach($file_content as $sql_line) {
                if(trim($sql_line) != "" && strpos($sql_line,"--") === false) {
                    $query .= $sql_line;
                    if(preg_match("/;\s*$/",$sql_line)) {
                        $dbase->SqlQuery($query);
                        if(!$dbase->obj_status) {
                            $error_count++;
                            $mysql_error .= $dbase->last_error . '<br />';
                        }
                        $query = "";
                    }
                }
            }


            //_______Clech for errors_________
            if($error_count == 0) {


            $mysl_details = '<?php'."\n".'$config["hostname"] = "'.$database_host.'";'."\n".
                '$config["dbusername"] = "'.$database_username.'";'."\n".
                '$config["dbpassword"] = "'.$database_password.'";'."\n".
                '$config["dbname"] = "'.$database_name.'";'."\n".'?>';

            $myFile = BASE_PATH.'/classes/mysql.inc.php';
            @unlink($myFile);
            $fh = @fopen($myFile,'wb');
            @fwrite($fh,$mysl_details);
            @fclose($fh);

            if (!is_file($myFile)) {
            	$notifications = 1;
                $notice = 'Error! Unable to create file - /classes/mysql.inc.php';//error writing mysql.inc.php
            }else{

                //_________it worked - show details page___________
                $visibility = visibility('details',$_SESSION['prescan_error']);
                
                //get the default settings
                $result = $dbase->SelectRecordLoop("SELECT * FROM general_settings");
                
                //set the ffmpeg as found earlier
                $ffmpeg = ($_SESSION['ffmpeg'] == '')?'':$_SESSION['ffmpeg'];
                $flvtool2 = ($_SESSION['flvtool2'] == '')?'':$_SESSION['flvtool2'];
                $mencoder = ($_SESSION['mencoder'] == '')?'':$_SESSION['mencoder'];
				$admin_username =	'admin';
				$admin_email = '';			                
            }
			} else {
                $notifications = 1;
                $notice = "Error! - Unable to connect to your Mysql database";
            }

        } else {
            $error = $dbase->last_error;
            $notifications = 1;
            $notice = "Error! - $error";
            $proceed = false;

        }
    }
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