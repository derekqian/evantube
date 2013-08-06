<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////


//use this script to load various generic pages  (e.g. about us, terms etc)

include_once ('classes/config.php');
include_once ('classes/sessions.php');

$page 	= (int) mysql_real_escape_string( $_GET['page'] );

switch ($page)

{

	case 1:
		$which_page = 'generic_aboutus.htm';
	break;

	case 2:
    		$which_page = 'generic_contactus.htm';
    	break;

    	case 3:
    		$which_page = 'generic_terms.htm';
    	break;

    	case 4:
    		$which_page = 'generic_advertise.htm';
    	break;

    	// new pages v3 => start at >= 10

    	case 10:
    		$which_page = 'site_ranking_info.htm';
    	break;


    	default:
    		header( 'Location: index.php' ) ;
    	break;
}

if ( $which_page == 'site_ranking_info.htm' ) {

	$template	= "themes/$user_theme/templates/$which_page";

	$TBS 		= new clsTinyButStrong;
	$TBS->NoErr = true;
	$TBS->LoadTemplate("$template");
	$TBS->Render	= TBS_OUTPUT;
	$TBS->Show();
	die();

} else {

	$template		= "themes/$user_theme/templates/main_1.htm";
	$inner_template1 	= "themes/$user_theme/templates/$which_page";//middle of page
	$TBS 			= new clsTinyButStrong;
	$TBS->NoErr 	= true;

	$TBS->LoadTemplate("$template");

	//$TBS->MergeBlock('mp', $recent);
	//$TBS->MergeBlock('blkfeatured', $result_featured);

	$TBS->Render	= TBS_OUTPUT;

	$TBS->Show();

	@mysql_close();
	die();
}

?>