<?php
//-----------------------------------------------------------------//
//
// PHPmotion Installation Wizard - Version 2
//
//-----------------------------------------------------------------//

include_once (dirname(__file__) . '/includes/config.php');
include_once (dirname(__file__) . '/includes/view.php');

$config_output = '532'; //the echo for testing phpshield (change this to anything else to see typical help output)

//___Set Welcome Visibility_____
$visibility = visibility('welcome');


if($_GET['step'] == 2) {

    //___Get Referrer url & add it to sessions____
    if($_SERVER['HTTP_REFERER'] == '') {
        $install_domain = siteBaseUrl();
        $install_domain = str_replace('index.php?step=2','',$install_domain); //just incase page refreshed
    } else {
        $install_domain = str_replace('setup/','',$_SERVER['HTTP_REFERER']);
        $install_domain = str_replace('index.php?step=2','',$install_domain); //just incase page refreshed
    }
    $_SESSION['install_url'] = $install_domain;


    //error array
    $error_array = array();


    //___Set Welcome Visibility_____
    $visibility = visibility('prechecks');


    //____________________________________________________Check PHP Version_________________________________________________
    $check_php = checksPHP();

    //default
    if($check_php['failed']) {
        $display_icon_php = 'Var-Check-Unknown';
        $display_notes_php = 'Unable to check this item';

    } else {
        $display_icon_php = 'Var-Check-Pass';
        $display_notes_php = $check_php['results'];
    }

    //v5.3 now supported
    if($check_php['is_53_above']) {
        $php_5_3 = true;
    }

    //ready warbing for 5.2.5
    if($check_php['needs_dynamic']) {
        $php_5_2_5 = true;
    }

    //________________________________________________Check FFMPEG/MENCODER/FLVTOOL2_________________________________________

    $modules = array('ffmpeg','mencoder','flvtool2');

    //__lets check each one____
    foreach($modules as $value) {

        $check_this = checkMODULES($value);

        //create a name for each var
        $display_icon_what = "display_icon_$value";
        $display_notes_what = "display_notes_$value";


        //default
        if($check_this['failed']) {
        	$error_array[] = 1; //log error
        	$display_server_help = 1; //show server help
            $$display_icon_what = 'Var-Check-Fail';
            $$display_notes_what = "$value could not be found/verified on this server";
            $_SESSION[$value] = 'Unknown - Ask your webhost';
        } else {
            $$display_icon_what = 'Var-Check-Pass';
            $$display_notes_what = $check_this['results'];
            $_SESSION[$value] = $check_this['results'];
        }


        //construct
        switch($value) {

            case 'ffmpeg':
                $f=$check_this['failed'];
                break;

            case 'mencoder':
                $m=$check_this['failed'];
                break;

            case 'flvtool2':
                $fl=$check_this['failed'];
                break;
        }

    }


    	//________________________________________________________PHPShield_______________________________________________________

    	$phpshield_check_url = $install_domain.'/classes/config.php?setup=1';

	if(checkingINI('allow_url_fopen')) {
    		$check_phpshield = file_get_contents($phpshield_check_url);
		$phpshield_proceed = true;
    	}

	if(!$phpshield_proceed){
    		if  (in_array  ('curl', get_loaded_extensions())) {
    			$check_phpshield = curl_get($phpshield_check_url);
			$phpshield_proceed = true;
		}
    	}
        
        
    	if ( file_get_contents($phpshield_check_url) != 532 ) $check_phpshield = curl_get_file_contents($phpshield_check_url);

    	//____________if we managed to load url - but cant get output________________

    	if($phpshield_proceed && !ereg($config_output, $check_phpshield)){ //output produced by config.php for testing

    		$pse = 1; //error log

    		//error for php5.3
		if($php_5_3){
    			$display_phpshield_help = 1;
			$phpshield_error_list_1 = 1; //php5.3 needs dynamic loader
    			$phpshield_result_css = 'Install-Progress-Warning';
    			$phpshield_result = 'Error! - See detailed explanation below';
		}

    		//error for php5.2.5+
		if($php_5_2_5 && !$php_5_3){
        		$display_phpshield_help = 1;
			$phpshield_error_list_4 = 1; //php5.2.5 needs dynamic loader
    			$phpshield_result_css = 'Install-Progress-Warning';
   	 		$phpshield_result = 'Error! - See detailed explanation below';
		}


    		//neither 5.3.5 not 5.2.5
		if(!$php_5_2_5 && !$php_5_3){
    			$display_phpshield_help = 1;
			$phpshield_error_list_3 = 1; //probably did not upload in binary
    			$phpshield_result_css = 'Install-Progress-Warning';
    			$phpshield_result = 'Error! - See detailed explanation below';
		}

		//check 64 bit
		if(is64bit()){
    			$display_phpshield_help = 1;
			$phpshield_error_list_5 = 1; //64 bit server
			$phpshield_error_list_3 = 0; //dont show this one
    			$phpshield_result_css = 'Install-Progress-Warning';
    			$phpshield_result = 'Error! - See detailed explanation below';
		}

	} else {

		$pse = 0; //error log
		$skip_dl_safemode = 1; //no need to run these tests further below
    	$phpshield_result_css = 'Install-Progress-Passed';
    	$phpshield_result = 'Everything looks ok';
	}



	if(!$phpshield_proceed){
    $pse = 1; //error log
    $display_phpshield_help = 1;
	$phpshield_error_list_2 = 1; //unable to check
    $phpshield_result_css = 'Install-Progress-Warning';
    $phpshield_result = 'Unable to test/verify this item';
    $phpshield_display_output .= '';
   }


    //____________________________________________________Check PHP.INI Items (ini_get)______________________________________________

    $modules = array('safe_mode','enable_dl');

    //__lets check each one____
    foreach($modules as $value) {


        //what are we expecting php.ini to be set to
        switch($value) {

            case 'safe_mode':
                $expected = 'Off'; //"Off" will also auto-check for "0" | "On" will also auto-check for "1"
                break;

            case 'enable_dl':
                $expected = 'On'; //"Off" will also auto-check for "0" | "On" will also auto-check for "1"
                break;


        }
        $check_this = checkINI($value,$expected);

        //create a name for each var
        $display_icon_what = "display_icon_$value";
        $display_notes_what = "display_notes_$value";


        //default
        if($check_this['failed'] && $skip_dl_safemode != 1) { //only display error if phpshielf failed also
            $error_array[] = 1; //log error
            $$display_icon_what = 'Var-Check-Fail';
            $$display_notes_what = "$value is not set correct. It should be set to: <b>$expected</>";

        } else {
            $$display_icon_what = 'Var-Check-Pass';
            $$display_notes_what = $check_this['results'];
        }

        //construct
        switch($value) {
            case 'safe_mode':
                $sm= $check_this['failed'];
                break;

            case 'enable_dl':
                $dl = $check_this['failed'];
                break;
        }

    }


    //____________________Check for rror___________________
    if(count($error_array) > 0) {

        $_SESSION['prescan_error'] = 1;
    }else{
      $_SESSION['prescan_error'] = 0;
    }

setupRec(0);
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



function curl_get_file_contents($URL)
{
	$c = curl_init();
      curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($c, CURLOPT_URL, $URL);
      $contents = curl_exec($c);
      curl_close($c);

      //echo "CONTENTS <br /> " . $contents . "<br />";

      if ($contents) return $contents;
      else return FALSE;

}

?>