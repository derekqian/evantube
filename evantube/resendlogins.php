<?php
///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once ('classes/config.php');

$referer	= mysql_real_escape_string($_SERVER['HTTP_REFERER']);

if ( $referer == '' ) header("Location: $base_url/index.php");

// define access for loading non display php files - e.g. postage.php, inc_my_picture.php etc...
define('access',true);

$show_login = 1;
$email_address_lost = mysql_real_escape_string($_POST['email_address_lost']);

if ($email_address_lost != "") {

    // check if users email is in dbase
    $sql = "SELECT * FROM member_profile WHERE email_address = '$email_address_lost'";
    $result = @mysql_query($sql);

    if (@mysql_num_rows($result) == 0) {
        $error_message = "No such user exists. Please check your email address and try again";
        $message_type = $config["notification_error"];
        $blk_notification = 1;//ready to show a result (error)
    }
    else {
        $result = @mysql_fetch_array($result);

        $status = $result['account_status'];
        // check if account is active
        if ($status != 'active') {
            $error_message = "That account is not currently active or has been suspended/delete. An email would have been sent out to confirm";
            $message_type = $config["notification_error"];
            $blk_notification = 1;//ready to show a result (error)
            // if everything is fine, resend email details (username and password)
        }
        elseif($status == 'active') {
            $lost_username = $result['user_name'];
		$newpass = pw_gen();
            $lost_password = md5($newpass);
		$sql = "update member_profile SET password = '$lost_password' WHERE email_address = '$email_address_lost'";
		$result = @mysql_query($sql);

            //send email --------resuable------------------------------------->>
            //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
            $email_template = 'email_templates/loginreminder.htm';
            $subject = $config["email_login_reminder"];
            $to = $email_address_lost;
            $from = $config['notifications_from_email'];

            //send email template to TBS for rendering of variable inside
            $template = $email_template;
            $TBS = new clsTinyButStrong;
            $TBS->NoErr = true;// no more error message displayed.
            $TBS->LoadTemplate("$template");
            $TBS->tbs_show(TBS_NOTHING);
            $message = $TBS->Source;

            //load postage.php
            include ('includes/postage.php');
            //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>


            $error_message = $config["request_completed"];
            $message_type = $config["notification_success"];
            $blk_notification = 1;//ready to show a result (error/success)
        }
    }
}
if ($email_address_lost == "") {
$error_message = "No such user exists. Please check your email address and try again";
$message_type = $config["notification_error"];
$blk_notification = 1;//ready to show a result (error/success)
}
// display login page with results
// //////////////////////////////
$template = "themes/$user_theme/templates/main_1.htm";
$inner_template1 = "themes/$user_theme/templates/inner_signup_form.htm";//middle of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->Render = TBS_OUTPUT;
$TBS->Show();

@mysql_close();
die();

function pw_gen() {
    $gen_password = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','P','Q','R','S','T','U','V','W','X','Y','Z','1','2','3','4','5','6','7','8','9','0');
    $pw_gen = '';

    for($i=1; $i<=6; $i++) {
        mt_srand ((double)microtime()*1000000);
        $tmp=mt_rand(0,count($gen_password)-1);
        $pw_gen.=$gen_password[$tmp];
    }

    return $pw_gen;
}
?>