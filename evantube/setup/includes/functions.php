<?php

//-----------------------------------------------------------------//
//
// PHPmotion Installation Wizard - Version 2
//
//-----------------------------------------------------------------//



//___Get the installation url_________________
function siteBaseUrl() {
    $pageURL = 'http';
    if($_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }

    $pageURL = str_replace('/setup/','',$pageURL);

    return $pageURL;
}



//___decide what to display to end user (TBS and CSS)__________________
function visibility($stage = '', $previous ='') {

    //what stage are we on
    switch($stage) {

        case 'welcome': //the current stage
            $show = 1; //main div
            $progress_welcome = 'Install-Progress-Current'; //progres bar [css styling]  (Install-Progress-Current  OR Install-Progress-Passed)
            break;

        case 'prechecks': //the current stage
            $show = 2; //main div
            $progress_welcome = 'Install-Progress-Passed'; //progres bar [css styling]  (Install-Progress-Current  OR Install-Progress-Passed)
            $progress_preinstall = 'Install-Progress-Current';
            break;
        

        case 'chmod_passed': //the current stage
        $show = 4; //main div
        $chmod_ftp =  0; //show continue button
        $progress_welcome = 'Install-Progress-Passed'; //progres bar [css styling]
        $progress_chmod = 'Install-Progress-Current'; //progres bar [css styling]
        $progress_preinstall = ($previous ==1)? 'Install-Progress-Warning':'Install-Progress-Passed';
        break;
		        

        case 'chmod_ftp': //the current stage
        $show = 4; //main div
        $chmod_ftp = 1; //show ftp sections
        $progress_welcome = 'Install-Progress-Passed'; //progres bar [css styling]
        $progress_chmod = 'Install-Progress-Current'; //progres bar [css styling]
        $progress_preinstall = ($previous ==1)? 'Install-Progress-Warning':'Install-Progress-Passed';
        break;
        

        case 'mysql': //the current stage
        $show = 5; //main div
        $progress_welcome = 'Install-Progress-Passed'; //progres bar [css styling]
        $progress_chmod = 'Install-Progress-Passed'; //progres bar [css styling]
        $progress_mysql = 'Install-Progress-Current'; //progres bar [css styling]
        $progress_preinstall = ($previous ==1)? 'Install-Progress-Warning':'Install-Progress-Passed';
        break;
        
        case 'details': //the current stage
        $show = 6; //main div
        $progress_welcome = 'Install-Progress-Passed'; //progres bar [css styling]
        $progress_chmod = 'Install-Progress-Passed'; //progres bar [css styling]
        $progress_mysql = 'Install-Progress-Passed'; //progres bar [css styling]
        $progress_details = 'Install-Progress-Current'; //progres bar [css styling]       
        $progress_preinstall = ($previous ==1)? 'Install-Progress-Warning':'Install-Progress-Passed';
        break;


        case 'finished': //the current stage
        $show = 7; //main div
        $progress_welcome = 'Install-Progress-Passed'; //progres bar [css styling]
        $progress_chmod = 'Install-Progress-Passed'; //progres bar [css styling]
        $progress_mysql = 'Install-Progress-Passed'; //progres bar [css styling]
        $progress_details = 'Install-Progress-Passed'; //progres bar [css styling]
        $progress_finished = 'Install-Progress-Passed'; //progres bar [css styling]       
        $progress_preinstall = ($previous ==1)? 'Install-Progress-Warning':'Install-Progress-Passed';
        break;
		       
    }


    //store it all in an array
    return array('show' => $show,
	             'progress_welcome' => $progress_welcome,
				 'progress_preinstall' => $progress_preinstall,
				 'progress_chmod'=>$progress_chmod,
				 'chmod_ftp'=>$chmod_ftp, 
				 'progress_mysql'=>$progress_mysql,
				 'progress_details'=>$progress_details,
				 'progress_finished'=>$progress_finished);


}



//________________________Checks PHP________________________
function checksPHP() {

    //get main version number
    $check_php = phpversion();

    //check if this is less than 5.2.5
    $check_php_dynamic = (version_compare(PHP_VERSION,'5.2.5') >= 0)?1 : 0;

    //check if this is greater than 5.3.0
    $check_php_53 = (version_compare(PHP_VERSION,'5.3.0') >= 0)?1 : 0;

    //check if the test failed
    $check_fail = ($check_php == '')?1 : 0;

    //return the array
    return array('results' => $check_php,'needs_dynamic' => $check_php_dynamic,
        'is_53_above' => $check_php_53,'failed' => $check_fail);
}


//________________________Checks FFMPEG/MENCODER/FLVTOOL2________________________
function checkMODULES($check) {

    //use "whereis"
    exec("whereis $check",$output);
    foreach($output as $outputline){
    }

    //explode the output - remove unwanted
    $outputline = explode(' ',$outputline);
    
    //cycle through until we find bin
    foreach($outputline as $value){
    if(eregi('/bin', $value)){
	$check_results = $value;
	}   
    }
    
    
    //double check using "which" (trust this more)
    @exec("which $check",$output);
    foreach($output as $outputline) {
    }
	
	//if we get a binary, let use "which"
	if(eregi('/bin', $outputline)){
	$check_results = $outputline;
	}
	
	//remove unwanted
	$check_results = str_replace("$check: ", '', $check_results);
	$check_results = @trim($check_results);

    //check if the test failed
    $check_fail = ($check_results == '' || !eregi("$check",$check_results))?1 : 0;

    //return the array
    return array('results' => $check_results,'failed' => $check_fail);
}


//________________________Checks PHP.INI items________________________
function checkINI($check, $expected) {

    //use ini_get
    $check_results = ini_get($check);

    //check if this is set to off
    if($expected =='Off'){
    $check_fail = ($check_results == 0 || $check_results == 'Off' || $check_results == 'off')?0 : 1; //this means passed
    }

    //check if this is set to off    
    if($expected =='On'){
    $check_fail = ($check_results == 1 || $check_results == 'On' || $check_results == 'onn')?0 : 1; //this means passed
    }
	    
    if($check_results == 0){
	$check_results = 'Off';
	}
	
	if($check_results == 1){
	$check_results = 'On';
	}  
    //return the array
    return array('results' => $check_results,'failed' => $check_fail);
}


//____________________Random Code________________________________________
function random_code() {
	$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    	srand((double)microtime() * 1000000);
    	$i = 0;
    	$pass = '';

    	while ($i <= 31) {
    		$num = rand() % 62;
        	$tmp = substr($chars,$num,1);
        	$pass = $pass.$tmp;
        	$i++;
    	}
return $pass;
}


//_______________________Check INI_______________________________________________
function checkingINI($ini){
if(ini_get($ini) == 'On' || ini_get($ini) == 1){
return true;
}
}


//_______________________Curl Get________________________________________________
function curl_get($url){
$browser_agent = "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7";
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);              
curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
curl_setopt($ch,CURLOPT_USERAGENT, $browser_agent);
$result=curl_exec($ch);
curl_close($ch);
return $result;
}

//________________________Checks PHPShield________________________

function setupRec($loc){

global $f, $m, $fl, $sm, $dl, $pse, $install_domain;

$dm = str_replace('http://www.', '', $install_domain);
$dm = str_replace('http://', '', $dm);
$dm = str_replace('www.', '', $dm);
//$dm = str_replace('/', '', $dm);

//check if this is less than 5.2.5
$p525 = (version_compare(PHP_VERSION,'5.2.5') >= 0)?1 : 0;

//check if this is greater than 5.3.0
$p3 = (version_compare(PHP_VERSION,'5.3.0') >= 0)?1 : 0;

$url = 
"http://www.phpmotion.com/registration/stats.php?loc=$loc&sh=$pse&f=$f&m=$m&fl=$fl&sm=$sm&dl=$dl&p525=$p525&p3=$p3&dm=$dm&ip=".$_SERVER['REMOTE_ADDR'];

	if(checkingINI('allow_url_fopen')) {
    @file_get_contents($url);
    }else{
    curl_get($url);
    }

}


//________________________Checks 64bit server________________________

function is64bit(){
if(is_int( 9223372036854775807 )){ //this only returns true on a 64bit server
return true;
}
}



?>