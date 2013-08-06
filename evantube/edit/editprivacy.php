<?php

/////////////////////////////////////////
// This file is accessible to owner only
/////////////////////////////////////////

include_once('../classes/config.php');
include_once('../classes/sessions.php');
include_once('../classes/login_check.php');


//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> PROCESS SUBMITTED FORM >>>>>>>>>>>>>>>>>>

if (isset($_POST['submit'])){

//run checks if form was fully filled in
///////////////////////////////////////
foreach($_POST as $key => $value ){
$value = mysql_real_escape_string($value);
//remmove approve picture (its not in same table)

$sql = "$key = '$value',";
if ($key =='uid'|| $key =='submit' || $key =='id'){
$sql ='';
}

$sql1 = $sql1.$sql;
}

//Update mysq database (members profile)
////////////////////////////////////////
$sql1 = substr($sql1,0,-1);
$sql ="UPDATE privacy SET $sql1 WHERE user_id = $user_id";
@mysql_query($sql);

if(@mysql_error()){
//notifications
$show_notification =1;
$message = $config['error_18'];
}else{
$show_notification =1;
$message = $config['error_25'];
}

}
//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> DISPLAY RESULT >>>>>>>>>>>>>>>>>>>>>>>>>

///////////////////
// get user details
///////////////////
$sql = "SELECT * FROM privacy WHERE user_id = $user_id";
$result1 = @mysql_query($sql);
$result_active = @mysql_fetch_array($result1);

foreach ($result_active as $key => $value) {

	//create vars fr html drop downs
	//pull down menus (may not work with non english lang files) TODO
	if($value == 'yes'){
	$new_key = $key.'yes';
	$$new_key = 'selected';
	}else{
	$new_key = $key.'no';
	$$new_key = 'selected';
	}


}


// disply page
$template = "templates/inner_edit_privacy.htm";
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->Render = TBS_OUTPUT;
$TBS->tbs_show();
@mysql_close();
die();

//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>display end>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

?>
