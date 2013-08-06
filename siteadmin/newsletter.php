<?php
ini_set('display_errors', 1);
error_reporting( E_ALL );


include_once ("../classes/config.php");
include_once ("includes/inc.stats.php");
include_once ("includes/functions.php");
include_once ('includes/login_check.php');

//Menu Loaders
///////////////
$top_menu = '';
$side_menu = 'settings';
$dashboard_header = $lang_themes;

/////////////////////
//defaults settings /
/////////////////////
$show_hide = 1;
$show_form = 1;
$show_notification = 0;

$todays_date = $config["date_format"];
$base_url = $config['site_base_url'];
$base_path = installation_paths();


$securityvar = 'xTg5S3455fd'; //matches that sent by newsletter_cli.php  ****USE ONLY TEXT and NUMBERS****



if( isset($_POST['send']) ) {

	//Get all post Data
	$seconds 		= $_POST['seconds'];
	$mailgroup 		= $_POST['mailgroup'];
	$message_type 	= $_POST['message_type'];
	$message_body	= $_POST['message_body'];
	$subject 		= $_POST['subject'];

	if ( $message_body == '' || $subject == '' ) {

		echo "POSTED FAILED </br> ";

		$show_notification = 1;
		$message = $config['fill_all_fields'];
		$proceed = false;

	} else {

		//Send Form to CLI
		//////////////////

		$emailer = $base_path . '/siteadmin/newsletter_cli.php';

		//Delet Dbase and then insert into Database
		$sql = "DELETE FROM newsletter";
		//mysql_query($sql);

		$query = mysql_query($sql);
		if(!$query) {
			die("Error while during sql_query. Error Output: <br/>". mysql_errno() . ": " . mysql_error(). "<br/>"."Query follows:<br/>".$query_f);
			@mysql_close();
			die();
		}

		$email_message = mysql_real_escape_string($message_body);
		$email_subject = mysql_real_escape_string($subject);

		$sql = "INSERT INTO newsletter (message, subject) VALUES ('$email_message', '$email_subject')";
		//mysql_query($sql);

		$query = mysql_query($sql);
		if(!$query) {
			die("Error while during sql_query. Error Output: <br/>". mysql_errno() . ": " . mysql_error(). "<br/>"."Query follows:<br/>".$query_f);
			@mysql_close();
			die();
		}


		//check mysql error
		if( mysql_error() ) {
			$show_notification =1;
			$message = $config["error_26"]; //error

		} else {

		//Send to emailer (newsletter_cli.php) using CLI call

		exec("$path_to_php $emailer $securityvar $mailgroup $seconds $message_type> /dev/null &");//

		$show_form = 2;
		$show_notification =1;
		$message = $config["error_25"]; //request success
		$message2 = $config['news_letter_ok'];

		}
	}
}


////////////////////////////////
//display form with error message
////////////////////////////////

$template		= "templates/main.html";
$inner_template1 	= "templates/inner_newsletter.html";

$TBS 			= new clsTinyButStrong;
$TBS->NoErr 	= true;

$TBS->LoadTemplate("$template");
$TBS->Render 	= TBS_OUTPUT;
$TBS->Show();

mysql_close();

die();

?>