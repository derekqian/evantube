<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

//this script closes all sessions, reset user_id and closes any open mysql
@session_start();
@session_destroy();
@mysql_close();

//just to be extra sure - set sessions to NULL
$_SESSION['random_code'] = NULL;
$_SESSION['user_id'] = NULL;
$_SESSION['user_name'] = NULL;
$_SESSION["admin_logged"] = NULL;

header("Location: " . "index.php");

?>