<?php

//-----------------------------------------------------------------//
//
// PHPmotion Installation Wizard - Version 2
//
//-----------------------------------------------------------------//

include_once (dirname(__file__) . '/includes/config.php');
include_once (dirname(__file__) . '/includes/view.php');


//__predefined____
$host = '';
$username = '';
$path = '';


//________________________________________________________CHMOD - VIA FTP_______________________________________________________

if($_POST['forms'] == 'chmod') {

    $visibility = visibility('chmod_ftp',$_SESSION['prescan_error']);
    $proceed = true;

    foreach($_POST as $key => $value) {
        $$key = $value;
    }

    //_________check if form filled in________
    $process = new FormProcessor();
    if(!$process->RequiredFields('all')) {
        $notifications = 1;
        $notice = "Error! - Fill in all form fields";
        $proceed = false;

    }else{


    //__________________________________________________FTP CHMODING______________________________________________

    //clean up host
    $remove = array('http://www.','http://','www.');
    $host = trim(str_replace($remove,'',$_POST['host']));


    //____check ftp connection______
    $conn_id = @ftp_connect($host) or $connect = 'failed';

    if($connect == 'failed') {
        $notifications = 1;
        $notice = "Error! - Unable to connect to your FTP";

    } else {


        if(!@ftp_login($conn_id,$username,$password)) {
            $notifications = 1;
            $notice = "Error! - Unable to connect - check your details and try again";
            $proceed = false;
        } else {

            //_____get ready to start chmoding_____

            //remove starting /
            if(substr($path,0,1) == '/') {
                $path = substr($path,1);
            }

            //remove any trailing /
            if(substr($path,-1) == '/') {
                $path = substr($path,0,-1);
            }

            $path = trim($path);

            //cpanel users who may put 'www'
            if($path == 'www') {
                $path = 'public_html';
            }
            

            //start chmoding 1 by 1
            $pass_count = 0;
            foreach($dir_paths as $key => $value) {
                $file_path = './'.$path . $value;
                $chmod = '0777';
                if(ftp_chmod($conn_id,eval("return({$chmod});"),$file_path) !== false) {
                    $pass_count += 1;
                }
            }

            if($pass_count != 17) {
                $notifications = 1;
                $notice = "Error! - Unable to Chmod - check your BASE DIRECTORY - try again";
            }


        }

    }

}
}

//check if dir's are already chmod
$errors = 0;
foreach($dir_paths as $key => $value) {
    if(!is_writable(BASE_PATH.$value)) {
        $errors += 1;
        $$key = 'Var-Check-Fail'; //set css
    } else {
        $$key = 'Var-Check-Pass'; //set css
    }
}

if($errors > 0) {
	if($notifications != 1){
    $notifications = 1;
    $notice = "Error! - Some files and folders are not CHMOD correctly";
    }
    $visibility = visibility('chmod_ftp',$_SESSION['prescan_error']);
} else {
    $visibility = visibility('chmod_passed',$_SESSION['prescan_error']);
}








//____visibility items put in vars)_____
foreach($visibility as $key => $value) {
    $$key = $value;
}



//____Display View________________________
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;
$TBS->LoadTemplate(MAIN_TEMPLATE);
$TBS->Render = TBS_OUTPUT;
$TBS->Show();

?>