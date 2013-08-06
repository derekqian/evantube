<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once ('../classes/config.php');
include_once ('../classes/sessions.php');


////////////////////////////////////////////////
// just my info not for release
$use_date = date("D - M d, Y @ h:i A");
$log_file = '../logs/avatar_log.txt';
$capture = false;
////////////////////////////////////////////////

if($_SESSION['user_id'] == '') {
    $show_notification = 1;
    $message = $config['login_first'];
    die();
}

$allowed_maxuploadsize = $config['member_pic_maxsize'];
$allowed_minuploadsize = $config['member_pic_minsize'];
$show_maxuploadsize = $allowed_maxuploadsize / 1000;
$show_minuploadsize = $allowed_minuploadsize / 1000;
$show_max_width = $config['member_pic_maxwidth'];
$show_min_width = $config['member_pic_minwidth'];
$show_max_height = $config['member_pic_maxheight'];
$show_min_height = $config['member_pic_minheight'];
$show = 1;
$allowed_ext = array('.jpg','.png','.gif');
$allowed_file_types = array('image/gif','image/jpg','image/x-png','image/png',);
$pictures_folder = $base_path.'/pictures';
$proceed = true;


if(isset($_POST['upload'])) {

    $security_token_check = $_SESSION['security_token'];
    $post_security_token = mysql_real_escape_string($_POST['security_token']);

    if($post_security_token != $security_token_check) {

        $proceed = false;
        $color = '#DD0000';
        $show_notification = 1;
        $message = 'Hacking Attemp, your IP has been logged!';

        unset($_SESSION['security_token']);
        $security_token = randomcode();
        $_SESSION['security_token'] = $security_token;

        ///////////////////////////////////////////////////////////////
        $capture = true;
        admin_msg($_SESSION['user_id']);
        $file_contents = 'Avatar hacking attempt - bad security token - check 1'."\n".
            'USER_ID '.$_SESSION['user_id']."\n".$use_date."\n".
            '================================================================='."\n";
        write_debug_log($file_contents);
        ///////////////////////////////////////////////////////////////

        $template = "templates/inner_upload_avatar.htm";
        $TBS = new clsTinyButStrong;
        $TBS->NoErr = true;

        $TBS->LoadTemplate("$template");
        $TBS->Render = TBS_OUTPUT;
        $TBS->tbs_show();
        @mysql_close();
        die();
    }


    $img_file_type = $_FILES['img_file']['type'];
    $img_file_name = $_FILES['img_file']['name'];
    $img_file_size = $_FILES['img_file']['size'];
    $img_file_tmp = $_FILES['img_file']['tmp_name'];


    if($img_file_size == 0 || $img_file_size > $allowed_maxuploadsize || $img_file_size < $allowed_minuploadsize) {
        $proceed = false;
        $color = '#DD0000';
        $show_notification = 1;
        $message = $config['file_size_limit_error'];
        unset($_SESSION['security_token']);
        $security_token = randomcode();
        $_SESSION['security_token'] = $security_token;

        ////////////////////////////////////////////////////////////////
        $capture = true;
        admin_msg($_SESSION['user_id']);
        $file_contents = 'Avatar upload file size error - check 2'."\n".'USER_ID '.$_SESSION['user_id']."\n".$use_date."\n".
            '================================================================='."\n";
        write_debug_log($file_contents);
        ////////////////////////////////////////////////////////////////

        die_with_msg($message);
    }

    $tmp_ext = explode('.',$img_file_name);
    if(sizeof($tmp_ext) > 2) {
        $proceed = false;
        $color = '#DD0000';
        $show_notification = 1;
        $message = 'Hacking Attemp, '.$config['file_type_error'];
        unset($_SESSION['security_token']);
        $security_token = randomcode();
        $_SESSION['security_token'] = $security_token;

        ////////////////////////////////////////////////////////////////
        $capture = true;
        admin_msg($_SESSION['user_id']);
        $file_contents = 'Avatar hacking attempt - more than 1 extension - check 3'."\n".
            'USER_ID '.$_SESSION['user_id']."\n".$use_date."\n".
            '================================================================='."\n";
        write_debug_log($file_contents);
        ////////////////////////////////////////////////////////////////

        die_with_msg($message);
    }

    $ext = strrchr($img_file_name,'.');
    $ext = strtolower($ext);
    $new_file_ext = $ext;

    if(!in_array($ext,$allowed_ext) || $ext = '') {
        $proceed = false;
        $color = '#DD0000';
        $show_notification = 1;
        $message = $config['file_type_error'];
        unset($_SESSION['security_token']);
        $security_token = randomcode();
        $_SESSION['security_token'] = $security_token;

        ////////////////////////////////////////////////////////////////
        $capture = true;
        admin_msg($_SESSION['user_id']);
        $file_contents = 'Avatar hacking attempt - extension error - check 4'."\n".
            'USER_ID '.$_SESSION['user_id']."\n".$use_date."\n"."\n".
            '================================================================='."\n";
        write_debug_log($file_contents);
        ////////////////////////////////////////////////////////////////

        die_with_msg($message);
    }

    if(!is_writeable($pictures_folder)) {
        $proceed = false;
        $color = '#DD0000';
        $show_notification = 1;
        $message = $config['error_26'];
        unset($_SESSION['security_token']);
        $security_token = randomcode();
        $_SESSION['security_token'] = $security_token;
        die_with_msg($message);
    }

} else { // show form

    $_SESSION['security_token'] = null;
    unset($_SESSION['security_token']);
    $security_token = randomcode();
    $_SESSION['security_token'] = $security_token;
    $template = "templates/inner_upload_avatar.htm";
    $TBS = new clsTinyButStrong;
    $TBS->NoErr = true;
    $TBS->LoadTemplate("$template");
    $TBS->Render = TBS_OUTPUT;
    $TBS->tbs_show();
    @mysql_close();
    die();
}


if($proceed == true) {
    $filename_random_code = randomcode();
    $new_file_name = $filename_random_code.$new_file_ext;
    $new_file_path = $base_path.'/pictures/'.$new_file_name;

    if(!move_uploaded_file($img_file_tmp,$new_file_path)) {
        $proceed = false;
        $color = '#DD0000';
        $show_notification = 1;
        $message = $config['error_26'];
        unset($_SESSION['security_token']);
        $security_token = randomcode();
        $_SESSION['security_token'] = $security_token;
        die_with_msg($message);

    } else {

        list($width,$height) = getimagesize($new_file_path);
        if($width > $config['member_pic_maxwidth'] || $width < $config['member_pic_minwidth'] ||
            $height > $config['member_pic_maxheight'] || $height < $config['member_pic_minheight']) {
            $proceed = false;
            $color = '#DD0000';
            $show_notification = 1;
            $message = $config['file_dimensions_error'];

            unset($_SESSION['security_token']);
            $security_token = randomcode();
            $_SESSION['security_token'] = $security_token;

            ////////////////////////////////////////////////////////////////
            $capture = true;
            admin_msg($_SESSION['user_id']);
            $file_contents = 'Avatar upload error - width or height error - check 5'."\n".
                'USER_ID '.$_SESSION['user_id']."\n".$use_date."\n".
                '================================================================='."\n";
            write_debug_log($file_contents);
            ////////////////////////////////////////////////////////////////

            // keep image to look at
            //@unlink($new_file_path);
            die_with_msg($message);
        }
    }

    $sql = "SELECT * FROM pictures WHERE user_id = $user_id";
    $result = @mysql_query($sql);

    // delete member avatar if they have one already
    if(@mysql_num_rows($result) != 0) {
        $result = @mysql_fetch_array($result);
        $existing_file = $result['file_name'];
        $filepath = installation_paths();
        $filepath = $filepath.'/pictures/'.$existing_file;

        @unlink($filepath);

        $sql = "DELETE FROM pictures WHERE user_id = $user_id";
        @mysql_query($sql);

        unset($_SESSION['security_token']);
        $security_token = randomcode();
        $_SESSION['security_token'] = $security_token;
        $color = '#DD0000';
    }

    $sql = "INSERT INTO pictures (file_name, user_id, todays_date, approved) VALUES ('$new_file_name', $user_id, NOW(), 'yes')";
    @mysql_query($sql);

    if(mysql_error()) {
        $show_notification = 1;
        $color = '#DD0000';
        $message = $config["error_26"];
        unset($_SESSION['security_token']);
        $security_token = randomcode();
        $_SESSION['security_token'] = $security_token;
        die_with_msg($message);
    } else {
        $color = '#009900';
        $show_notification = 1;
        $message = $config["error_25"];
        $show = 2;
        unset($_SESSION['security_token']);
        $security_token = randomcode();
        $_SESSION['security_token'] = $security_token;
    }

    // show success and message
    unset($_SESSION['security_token']);
    $security_token = randomcode();
    $_SESSION['security_token'] = $security_token;

    $template = "templates/inner_upload_avatar.htm";
    $TBS = new clsTinyButStrong;
    $TBS->NoErr = true;

    $TBS->LoadTemplate("$template");
    $TBS->Render = TBS_OUTPUT;
    $TBS->tbs_show();

    @mysql_close();
    die();

} else { // proceed is false - show error msg

    $show_notification = 1;
    unset($_SESSION['security_token']);
    $security_token = randomcode();
    $_SESSION['security_token'] = $security_token;

    $template = "templates/inner_upload_avatar.htm";
    $TBS = new clsTinyButStrong;
    $TBS->NoErr = true;

    $TBS->LoadTemplate("$template");
    $TBS->Render = TBS_OUTPUT;
    $TBS->tbs_show();
    @mysql_close();
    die();
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////
// sub functions

function die_with_msg($message) {
    $show_notification = 1;
    $template = "templates/inner_upload_avatar.htm";
    $TBS = new clsTinyButStrong;
    $TBS->NoErr = true;
    $TBS->LoadTemplate("$template");
    $TBS->Render = TBS_OUTPUT;
    $TBS->tbs_show();
    @mysql_close();
    die();

}


function admin_msg($user_id = '') {

    global $config, $site_name,$admin_email,$base_url,$base_path,$img_file_tmp,$img_file_name,$capture;

    $ext = strrchr($img_file_name,'.');
    $ext = strtolower($ext);
    $new_file_ext = $ext;
    $filename_random_code = randomcode();
    $new_file_name = 'BAD_'.$filename_random_code.$new_file_ext;
    $new_file_path = $base_path.'/pictures/'.$new_file_name;

    if($capture == true) {
        move_uploaded_file($img_file_tmp,$new_file_path);
    }

$message = 
"An avatar upload failed.
---------------------------------------------------------------------------------------------------------------------\n

The user id was: $user_id\n
You MAY be able to view the file here:".$config["site_base_url"]."/pictures/$new_file_name\n

[TIP]: Make sure that your FTP folder '/pictures' is chmod correctly (777 or on some servers 755)

---------------------------------------------------------------------------------------------------------------------\n\n\n";
	
//__________Admins email address for report________________
$sql = "SELECT email_address
        FROM member_profile
        WHERE user_group = 'admin'
        LIMIT 1";
$query = mysql_query($sql);
$result = mysql_fetch_array($query);
$admin_email = $result[0];


//Send the email to admin
$to = $admin_email;
$subject = "Avatar Upload Error";
$from = $config['site_name'].'<'.$config['notifications_from_email'].'>';
mail($to, $subject, $message, "From: $from");


$sql = "DELETE FROM videos WHERE video_id = '$raw_video'";
$query = @mysql_query($sql);
return true;

}

// write a log for each upload -- use new_flv as name ??
function write_debug_log($file_contents) {
    global $log_file;

    if(@file_exists($log_file)) {
        $fo = @fopen($debug_log_file,'a');
        @fwrite($fo,$file_contents);
        @fclose($fo);

    } else {
        $fo = @fopen($log_file,'w');
        @fwrite($fo,$file_contents);
        @fclose($fo);
    }
    return true;
}


?>