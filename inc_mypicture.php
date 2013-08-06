<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once('classes/login_check.php');

 $a1_sql = "SELECT * FROM pictures WHERE user_id = $user_id";
 $a1_result = @mysql_query($a1_sql);
 if (@mysql_num_rows($a1_result) != 0) {
    $a1_result = @mysql_fetch_array($a1_result);
     $a1_existing_file = $a1_result['file_name'];
     $a1_approved = $a1_result['approved'];
     if ($a1_approved == "yes") {
        // show picture and "change picture link"
        $a1_mypicture = $config['site_base_url'] . '/pictures/' . $a1_existing_file;
         $a1_show = 1;
         } else {
        // show place holder image and "awaiting approval link"
        $a1_mypicture = $config['site_base_url'] . "/themes/$user_theme/images/placeholder.gif";
         $a1_show = 2;
         }
    } else {
    // show place holder with "upload image link"
    $a1_mypicture = $config['site_base_url'] . "/themes/$user_theme/images/placeholder.gif";
     $a1_show = 3;
     }

?>

