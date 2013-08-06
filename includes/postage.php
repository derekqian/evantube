<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

if ( !defined('access') ) {
	header("Location: " . "index.php");
}

// IMPORTANT: change this to 'yes'  ONLY if you are sending html format email
$html_format	= 'yes';

$from			= $config['site_name'].'<'.$config['notifications_from_email'].'>';


if ( $html_format == 'yes' )
{
	$headers = "From: $from\n";
	$headers .= "Reply-To: $from\n";
	$headers .= "Return-Path: $from\n";
	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\n";

	mail($to,$subject,$message,$headers);

} else {

	mail($to, $subject, $message, "From: $from");
}

?>
