<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once('../classes/config.php');


$admin_id = $_SESSION['user_id'];

/////////////////////////////////////
//if not logged in load register.php
/////////////////////////////////////

if ($_SESSION["admin_logged"] !="ok"){
    $admin_logout = '';
    $show_hide = 2;//show only login table section
    $template = "templates/main.html";
    $template_inner = '';
    $TBS = new clsTinyButStrong;
    $TBS->NoErr = true;
    $TBS->LoadTemplate("$template");
    $TBS->Render = TBS_OUTPUT;
    $TBS->Show();
    @mysql_close();
    die();

}

?>