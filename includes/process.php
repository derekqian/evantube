<?php

/*
 * Captcha
 *
 * Created on February 3, 2007
 * Developer: Maria Erlandson
 * http://www.teasso.com
 *
 */
session_start();

if (strtolower($_POST['captext']) == strtolower($_SESSION['security_code']))
	echo "good";
else
	echo "bad";
?>
