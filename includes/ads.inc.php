<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

//get all ads information from mysql

$sqlads = "SELECT * FROM adverts WHERE preloaded = 'yes'";
$queryads = @mysql_query($sqlads);
while ($resultads = @mysql_fetch_array($queryads)) {
    $ads_left = $resultads['ads_left'];
    $ads_right = $resultads['ads_right'];
    $ads_top = $resultads['ads_top'];
    $ads_bottom = $resultads['ads_bottom'];
    $ads_home_right = $resultads['ads_home_right'];
}

?>