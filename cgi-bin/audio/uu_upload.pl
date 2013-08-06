#!/usr/bin/perl -w
#**********************************************************************************************************************************
#   ATTENTION: THIS FILE HEADER MUST REMAIN INTACT. DO NOT DELETE OR MODIFY THIS FILE HEADER.
#
#   Name: uu_upload.pl
#   Link: http://uber-uploader.sourceforge.net/
#   Revision: 5.0
#   Date: 17/03/2007 6:25PM
#   Initial Developer: Peter Schmandra
#   Description: Upload files to a temp dir based on Session-id, transfer files to upload dir and output results or redirect.
#
#   Credits:
#   I would like to thank the following people who helped create
#   and improve Uber-Uploader by providing code, ideas, insperation,
#   bug fixes and valuable feedback. If you feel you should be included
#   in this list, please post a message in the 'Open Discussion'
#   forum of the Uber-Uploader project page requesting a contributor credit.
#
#   Contributor: Art Bogdanov             www.sibsoft.net/xupload.html
#   Contributor: Bill                     www.rebootconcepts.com
#   Contributor: Cedric                   www.fsharp.fr
#   Contributor: Detlev Richter
#   Contributor: Erik Guilfoyle
#   Contributor: Feyyaz Oezdemir
#   Contributor: Jeroen Soeters
#   Contributor: Kim Steinhaug
#   Contributor: Klaus Karcher
#   Contributor: Nico Hawley-Weld
#   Contributor: Raditha Dissanyake       www.raditha.com/megaupload/
#   Contributor: Tolriq
#   Contributor: Tore B. Krudtaa
#
#   Licence:
#   The contents of this file are subject to the Mozilla Public
#   License Version 1.1 (the "License"); you may not use this file
#   except in compliance with the License. You may obtain a copy of
#   the License at http://www.mozilla.org/MPL/
#
#   Software distributed under the License is distributed on an "AS
#   IS" basis, WITHOUT WARRANTY OF ANY KIND, either express or
#   implied. See the License for the specific language governing
#   rights and limitations under the License.
#
#**********************************************************************************************************************************
my $THIS_VERSION = "5.0";                                                    # Version of this driver
my $start_time = time();                                                     # Timestamp of the start of the upload

# Makes %ENV safer
$ENV{'PATH'} = '/bin:/usr/bin:/usr/local/bin';
delete @ENV{'IFS', 'CDPATH', 'ENV', 'BASH_ENV'};

use strict;
use lib qw(.);                              # Add current directory to list of valid paths
use CGI::Carp 'fatalsToBrowser';           # Dump fatal errors to screen
use CGI qw(:cgi);                           # Load the CGI.pm module
use uu_lib;                                 # Load the uu_lib.pm module


###############################################################
# The following possible query string formats are assumed
#
# 1. ?tmp_sid=some_sid_number&config_file=some_config_file_name
# 2  ?tmp_sid=some_sid_number
# 3. ?cmd=about
# 4. ?cmd=debug&config_file=some_config_file_name
# 5. ?cmd=debug
###############################################################
my %query_string = parse_query_string($ENV{'QUERY_STRING'});

# Check for tainted sid
if(exists($query_string{'tmp_sid'})){
	if($query_string{'tmp_sid'} !~ m/(\w{32})/){ &kak("<font color='red'>ERROR<\/font>: Invalid session-id<br>\n", 1, __LINE__); }
	else{ $query_string{'tmp_sid'} = $1; }
}

# Check for tainted config file name
if(exists($query_string{'config_file'})){
	if($query_string{'config_file'} !~ m/(\w{5,32})/){ &kak("<font color='red'>ERROR<\/font>: Invalid config file name<br>\n", 1, __LINE__); }
	else{ $query_string{'config_file'} = $1; }
}

# Check for tainted command
if(exists($query_string{'cmd'})){
	if($query_string{'cmd'} ne 'about' && $query_string{'cmd'} ne 'debug'){ &kak("<font color='red'>ERROR<\/font>: Invalid command<br>\n", 1, __LINE__); }
}

# Make sure cmd or tmp_sid was passed but not both
if(!exists($query_string{'cmd'}) && !exists($query_string{'tmp_sid'})){ &kak("<font color='red'>ERROR<\/font>: Invalid parameters<br>\n", 1, __LINE__); }
if(exists($query_string{'cmd'}) && exists($query_string{'tmp_sid'})){ &kak("<font color='red'>ERROR<\/font>: Conflicting parameters<br>\n", 1, __LINE__); }


#######################################################################################################
# Attempt to load the config file that was passed to the script if multi configs is enabled. If no
# config file name was passed to the script, load the default config file 'uu_default_config.pm'
#######################################################################################################
if(exists($query_string{'config_file'}) && $MULTI_CONFIGS_ENABLED){
	my $config_file = $query_string{'config_file'};

	unless(eval "use $config_file"){
		if($@){ &kak("<font color='red'>ERROR<\/font>: Failed to load config file $config_file.pm<br>\n", 1, __LINE__); }
	}
}
elsif(exists($query_string{'config_file'}) && !$MULTI_CONFIGS_ENABLED){
	 &kak("<font color='red'>ERROR<\/font>: Multi Config files disabled<br>\n", 1, __LINE__);
}
else{
	unless(eval "use uu_default_config"){
		if($@){ &kak("<font color='red'>ERROR<\/font>: Failed to load config file uu_default_config.pm<br>\n", 1, __LINE__); }
	}
}

# Process 'about' or 'debug' command
if(exists($query_string{'cmd'}) && $query_string{'cmd'} eq 'about'){ &kak("<u><b>UBER UPLOADER VERSION</b><\/u><br> UBER UPLOADER VERSION = <b>" . $UBER_VERSION . "<\/b><br> UU_UPLOAD = <b>" . $THIS_VERSION . "<\/b><br>\n", 1, __LINE__); }
elsif(exists($query_string{'cmd'}) && $query_string{'cmd'} eq 'debug' && !$DEBUG_ENABLED){ &kak("<u><b>UBER UPLOADER CGI SETTINGS<\/b><\/u><br> DEBUG = <b><font color='red'>disabled</font><\/b><br>\n", 1, __LINE__); }
elsif(exists($query_string{'cmd'}) && $query_string{'cmd'} eq 'debug' && $DEBUG_ENABLED){ &debug(); }

my $tmp_sid = $query_string{'tmp_sid'};                                             # Assign session-id
my $sleep_time = 1;                                                                 # Seconds to wait before upload proceeds (for small file uploads)
my %uploaded_files = ();                                                            # Hash with all the uploaded file names
my $temp_dir_sid = $main::config->{temp_dir} . $tmp_sid;                            # Append Session-id to upload temp directory
my $flength_file = $temp_dir_sid . '/flength';                                      # Flength file is used to store the size of the upload in bytes
my $unique_dir;                                                                     # Unique upload directory name

umask(0);
$|++;                                                                               # Force auto flush of output buffer
$SIG{HUP} = 'IGNORE';                                                               # Ignore sig hup
local $SIG{__DIE__} = \&cleanup;                                                    # User has pressed stop during upload so deal with it
$CGI::POST_MAX = $main::config->{max_upload};                                       # Set the max post value

# Create temp directory if it does not exist
if(!-d $main::config->{temp_dir}){ mkdir($main::config->{temp_dir}, 0777) or &kak("<font color='red'>ERROR</font>: Failed to mkdir $main::config->{temp_dir}: $!", 1, __LINE__); }

# Create a temp directory based on Session-id
if(!-d $temp_dir_sid){ mkdir($temp_dir_sid, 0777) or &kak("<font color='red'>ERROR</font>: Failed to mkdir $temp_dir_sid: $!", 1, __LINE__); }
else{
	&deldir($temp_dir_sid);
	mkdir($temp_dir_sid, 0777) or &kak("<font color='red'>ERROR</font>: Failed to mkdir $temp_dir_sid: $!", 1, __LINE__);
}

# Prepare the flength file for writing
open(FLENGTH, ">" , "$flength_file") or &kak("<font color='red'>ERROR</font>: Failed to open $temp_dir_sid/flength: $!", 1, __LINE__);

if($ENV{'CONTENT_LENGTH'} > $main::config->{max_upload}){
	# If file size exceeds maximum write error to flength file and exit
	my $max_size = &format_bytes($main::config->{max_upload}, 99);

	print FLENGTH "ERROR: Maximum upload size of $max_size exceeded";
	close(FLENGTH);
	chmod 0666, $flength_file;

	&kak("<font color='red'>ERROR</font>: Maximum upload size of $max_size exceeded.<br><br>Your upload has failed.<br>", 1, __LINE__);
}
else{
	# Write total upload size in bytes to flength file
	print FLENGTH $ENV{'CONTENT_LENGTH'};
	close(FLENGTH);
	chmod 0666, $flength_file;
}

# Let progress bar get some info (for small file uploads)
sleep($sleep_time);

# Tell CGI.pm to use our directory based on Session-id
if($TempFile::TMPDIRECTORY){ $TempFile::TMPDIRECTORY = $temp_dir_sid; }
elsif($CGITempFile::TMPDIRECTORY){ $CGITempFile::TMPDIRECTORY = $temp_dir_sid; }
else{ &kak("<font color='red'>ERROR</font>: Failed to assign CGI temp directory", 1, __LINE__); }

my $query = new CGI;

####################################################################################################################
# The upload is complete at this point, so you can now access any post values. eg. $query->param("some_post_value");
####################################################################################################################

###################################################################################################################
# IF you are modifying the upload directory with a post value, it may be done here.
#
# Note: Making modifications based on posted input may be unsafe. Make sure your posted input is safe!
#
# You must override the $main::config->{upload_dir} value
# If you are linking to the file you must also override the $main::config->{path_to_upload} value
#
# eg. $main::config->{upload_dir} .= $query->param("employee_num") . '/';
# eg. $main::config->{path_to_upload} .= $query->param("employee_num") . '/';
###################################################################################################################

# Create a unique directory inside the upload directory if config setting 'unique_upload_dir' is enabled
if($main::config->{unique_upload_dir}){
	$unique_dir = generate_random_string($main::config->{unique_upload_dir_length});
	$main::config->{upload_dir} .= $unique_dir . '/';

	if($main::config->{link_to_upload}){ $main::config->{path_to_upload} .= $unique_dir . '/'; }
}

# Create upload directory if it does not exist
if(!-d $main::config->{upload_dir}){ mkdir($main::config->{upload_dir}, 0777) or &kak("<font color='red'>ERROR</font>: Failed to mkdir $main::config->{upload_dir}: $!", 1, __LINE__); }

# If we are using rename, make sure it's the same disk or mount
if($main::config->{create_files_by_rename}){
	my $dev_temp_dir = (stat($main::config->{temp_dir}))[0];
	my $dev_upload_dir = (stat($main::config->{upload_dir}))[0];

	# We have have two disks so use copy instead (can't rename across disks/mounts)
	if($dev_temp_dir != $dev_upload_dir){ $main::config->{create_files_by_rename} = 0; }
}

# Start processing the uploaded files
for my $upload_key (keys %{$query->{'.tmpfiles'}}){
	# Get uploaded file name
	$query->{'.tmpfiles'}->{$upload_key}->{info}->{'Content-Disposition'} =~ / filename="([^"]*)"/;
	my $file_name = $1;

	# Get the filed name eg. 'upfile_0'
	$query->{'.tmpfiles'}->{$upload_key}->{info}->{'Content-Disposition'} =~ / name="([^"]*)"/;
	my $field_name = $1;

	# Get the upload file handle
	my $upload_filehandle = $query->upload($field_name);

        # Get the CGI temp file name
        my $tmp_filename = $query->tmpFileName($upload_filehandle);

        # Strip extra path info from the file (IE). Note: Will likely cause problems with foreign languages like chinese
        $file_name =~ s/.*[\/\\](.*)/$1/;

        # Normalize file name  Note: Will cause problems with foreign languages like chinese
        if($main::config->{normalize_file_names}){ $file_name = &normalize_filename($file_name, $main::config->{normalize_file_delimiter}, $main::config->{normalize_file_length}); }

        # Get the file extention
        my ($f_name, $file_extension) = ($file_name =~ /(.*)\.(.+)/);

        ########################################################################################################
	# IF you are modifying the file name with a post value, it may be done here.
	#
	# Note: Making modifications based on posted input may be unsafe. Make sure your posted input is safe!
	#
	# eg. $file_name = $f_name . "_" . $query->param("employee_num") . $file_extension;
	########################################################################################################

        #################################################################################################################################################
	# IF you want to filter file uploads by disallowed AND allowed extensions, change the following line to use the allow extensions config setting.
	# eg. if((-s $tmp_filename) && ($file_extention !~ m/^$main::config->{disallow_extensions}$/i) && ($file_extention =~ m/^$main::config->{allowed_extensions}$/i)){
	#################################################################################################################################################################

        # Do not process zero length files or files with illegal extensions
        if((-s $tmp_filename) && ($file_extension !~ m/^$main::config->{disallow_extensions}$/i)){
		# Create a unique filename if config setting 'unique_filename' is enabled
		if($main::config->{unique_file_name}){
			my $unique_file_name = generate_random_string($main::config->{unique_file_name_length});

			$unique_file_name = $unique_file_name . "." . $file_extension;
			$file_name = $unique_file_name;
		}

		# Check for an existing file and rename if it already exists
		if(!$main::config->{overwrite_existing_files}){ $file_name = &rename_filename($file_name, 1); }

		my $upload_file_path = $main::config->{upload_dir} . $file_name;

		if($main::config->{create_files_by_rename}){
			# Create uploaded files by rename (fast)
			close($upload_filehandle);
			rename($tmp_filename, $upload_file_path) or warn("Cannot rename from $tmp_filename to $upload_file_path: $!");
		}
		else{
			# Create uploaded files by copy (slow but works across disks and mounts)
			open(UPLOADFILE, ">" , "$upload_file_path");
			binmode UPLOADFILE;

			while(<$upload_filehandle>){ print UPLOADFILE; }

			close(UPLOADFILE);
			close($upload_filehandle);
		}

		chmod 0666, $upload_file_path;
	}

	# Record the upload file info
	$uploaded_files{$file_name}{$field_name} = 0;
}

# Delete the temp directory based on session-id and everything in it
&deldir($temp_dir_sid);

# Redirect to php page if redirect enabled else display results
if($main::config->{redirect_after_upload}){
	my $param_file_path = $main::config->{temp_dir} . $tmp_sid . ".params";
	my @names = $query->param;

	# We are re-directing so write a session-id.param file with all the post values
	open PARAMS, ">$param_file_path" or &display_results_through_cgi();
	binmode PARAMS;

	# Write post values to param file (we do not write the files names at this point)
	foreach my $key (@names){
		my $post_value = $query->param($key);
		$post_value =~ s/(\r\n|\n|\r)/~NWLN~/g;
		$post_value =~ s/=/~EQLS~/g;

		if($post_value ne "" && $key !~ m/^upfile_/){ print PARAMS "$key=$post_value\n"; }
	}

	# Write some config settings to the param file
	print PARAMS "upload_dir=$main::config->{upload_dir}\n";
	print PARAMS "link_to_upload=$main::config->{link_to_upload}\n";
	print PARAMS "delete_param_file=$main::config->{delete_param_file}\n";

	# If we want a direct link and or an email link, we need to pass the path_to_upload setting
	if($main::config->{link_to_upload} || ($main::config->{send_email_on_upload} && $main::config->{link_to_upload_in_email})){
		print PARAMS "path_to_upload=$main::config->{path_to_upload}\n";
	}

	# If 'unique_upload_dir' config setting is enabled pass the info
	if($main::config->{unique_upload_dir}){
		print PARAMS "unique_upload_dir=$main::config->{unique_upload_dir}\n";
		print PARAMS "unique_dir=$unique_dir\n";
	}

	# If email on upload, write all the email info to param file
	if($main::config->{send_email_on_upload}){
		print PARAMS "send_email_on_upload=1\n";
		print PARAMS "link_to_upload_in_email=$main::config->{link_to_upload_in_email}\n";
		print PARAMS "email_subject=$main::config->{email_subject}\n";
		print PARAMS "html_email_support=$main::config->{html_email_support}\n";
		print PARAMS "to_email_address=$main::config->{to_email_address}\n";
		print PARAMS "from_email_address=$main::config->{from_email_address}\n";
	}
	else{ print PARAMS "send_email_on_upload=0\n"; }

	# Write upload file names to param file
	for my $file_name (keys %uploaded_files){
		for my $upload_slot (keys %{$uploaded_files{$file_name}}){
			print PARAMS "$upload_slot=$file_name\n";
		}
	}

	# Write start and end upload time to param file
	print PARAMS "start_time=$start_time\n";
	print PARAMS "end_time=" . time() . "\n";

	close(PARAMS);

	chmod 0666, $param_file_path;
############################################################################################
################################# REDIRECT AND POST AUDIO FILE NAME ########################

	# Append random string so finished page does not cache
	my $redirect_url = $main::config->{redirect_url};

##########################################################################
############# POSTED FILE NAME HERE ######################################

	$redirect_url .= "?rnd_id=" . &generate_random_string(8);
	#$redirect_url .= "?audio_id=" . $unique_file_name;

	# Force 'redirect_using_location' if user does not have a javascript capable browser or using embedded_upload_results
	if($query->param('no_script') || $query->param('embedded_upload_results') == 1){
		$main::config->{redirect_using_js_html} = 0;
		$main::config->{redirect_using_location} = 1;
	}

	# Perform Redirect
	if($main::config->{redirect_using_js_html}){
		&kak("<form name='redirect' method='post' action=\"$redirect_url\"><input type='hidden' name='tmp_sid' value=\"$tmp_sid\"><input type='hidden' name='temp_dir' value=\"$main::config->{temp_dir}\"></form><script language='javascript' type='text/javascript'>document.redirect.submit()</script>", 1, __LINE__);
	}
	else{
		##############################################################################################################
		# If you are using one of the following redirect methods, the 'tmp_sid' and 'temp_dir' setting will be passed
		# in the address bar. If you do not want the path to the temp dir passed in the address bar simply remove it
		# from the redirect and hard-code the $temp_dir value in the uploader_finished.php page.
		#
		# eg. $redirect_url .= "&tmp_sid=$tmp_sid";
		###############################################################################################################

		# Append the session-id and path to the temp dir to the redirect url.
		$redirect_url .= "&tmp_sid=$tmp_sid&temp_dir=$main::config->{temp_dir}";

		if($main::config->{redirect_using_html}){
			print "content-type:text/html; charset=utf-8\n\n";
			print "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><meta http-equiv=\"refresh\" content=\"0; url='$redirect_url'\"></head><body></body></html>";
		}
		elsif($main::config->{redirect_using_js}){
			&kak("<script language=\"javascript\" type=\"text/javascript\">location.href='$redirect_url';</script>", 1, __LINE__);
		}
		elsif($main::config->{redirect_using_location}){
			# Uncomment next line if using Webstar V
			# print "HTTP/1.1 302 Redirection\n";
			print "Location: $redirect_url\n\n";
		}
	}
}
else{
	# We are not redirecting so dump upload results to screen using CGI
	&display_results_through_cgi();
}

exit;

######################################################### START SUBROUTINES ###################################################

########################################
# Delete the temp dir based on tmp_sid
########################################
sub cleanup{ deldir($temp_dir_sid); }

#####################################
# Display upload results through cgi
#####################################
sub display_results_through_cgi{
	&confirm_upload();
	&display_results();
}

#####################################################
# Confirm uploaded file exist and get size file size
#####################################################
sub confirm_upload{
	for my $file_name (keys %uploaded_files){
		for my $upload_slot ( keys %{ $uploaded_files{$file_name} } ){
			my $path_to_file = $main::config->{upload_dir} . $file_name;

			if(-e $path_to_file && -f $path_to_file){ $uploaded_files{$file_name}{$upload_slot} = -s $path_to_file; }
		}
	}
}

####################################
# Format the upload result and exit
####################################
sub display_results{
	my ($upload_result, $email_file_list, $bg_col, $buffer, $js_code) = ();
	my $i = 0;

	if(defined($query->param('embedded_upload_results')) && $query->param('embedded_upload_results') == 1){
		$upload_result .= "<script language=\"javascript\" type=\"text/javascript\">\n";
		$upload_result .= "  parent.document.getElementById('upload_div').style.display = \"\";\n";
		$upload_result .= "  parent.iniFilePage();\n";
		$upload_result .= "</script>\n";
	}

	$upload_result .= "<table cellpadding='1' cellspacing='1' width='70%'>\n";
	$upload_result .= "  <tr>\n";
        $upload_result .= "    <td align='center' bgcolor='bbbbbb'>&nbsp;&nbsp;<b>UPLOADED FILE NAME</b>&nbsp;&nbsp;</td><td align='center' bgcolor='bbbbbb'>&nbsp;&nbsp;<b>UPLOADED FILE SIZE</b>&nbsp;&nbsp;</td>\n";
        $upload_result .= "  </tr>\n";

     	# Loop over the file names creating table elements
	for my $file_name (keys %uploaded_files){
		if($i%2){ $bg_col = 'cccccc'; }
    		else{ $bg_col = 'dddddd'; }

		for my $upload_slot ( keys %{ $uploaded_files{$file_name} } ){
			if($uploaded_files{$file_name}{$upload_slot} > 0){
				my $file_size = &format_bytes($uploaded_files{$file_name}{$upload_slot}, 99);

				if($main::config->{link_to_upload}){
					$upload_result .= "<tr><td align='center' bgcolor='$bg_col'><a href=\"$main::config->{path_to_upload}$file_name\" TARGET=\"_blank\">$file_name</a><\/td><td align='center' bgcolor='$bg_col'>$file_size<\/td><\/tr>\n";
				}
				else{
					$upload_result .= "<tr><td align='center' bgcolor='$bg_col'>&nbsp;$file_name&nbsp;<\/td><td align='center' bgcolor='$bg_col'>$file_size<\/td><\/tr>\n";
				}

				if($main::config->{link_to_upload_in_email}){
					$email_file_list .= "File Name: $main::config->{path_to_upload}$file_name     File Size: $file_size\n";
				}
				else{
					if($main::config->{unique_upload_dir}){ $email_file_list .= "File Name: $unique_dir/$file_name     File Size: $file_size\n"; }
					else{ $email_file_list .= "File Name: $file_name     File Size: $file_size\n"; }
				}
			}
			else{
				$upload_result .= "<tr><td align='center' bgcolor='$bg_col'>&nbsp;$file_name&nbsp;<\/td><td align='center' bgcolor='$bg_col'><font color='red'>Failed To Upload<\/font><\/td><\/tr>\n";
				$email_file_list .= "File Name: $file_name     File Size: Failed To Upload !\n";
			}
		}

		$i++;
	}

	$upload_result .= "</table>\n";
	$upload_result .= "<br>\n";

	if($main::config->{send_email_on_upload}){ &email_upload_results($email_file_list); }
	&kak($upload_result, 1, __LINE__);
}

##########################################
#  Send an email with the upload results.
##########################################
sub email_upload_results{
	my $file_list = shift;
	my $path_to_sendmail = "/usr/sbin/sendmail -t";
	my $message;
	my ($ssec, $smin, $shour, $smday, $smon, $syear, $swday, $syday, $sisdst) = localtime($start_time);
	my $end_time = time();
	my ($esec, $emin, $ehour, $emday, $emon, $eyear, $ewday, $eyday, $eisdst) = localtime($end_time);
	my @abbr = ('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');

	$eyear += 1900;
	$syear += 1900;

	if(open(SENDMAIL, "|$path_to_sendmail")){
		print SENDMAIL "From:" . $main::config->{from_email_address} . "\n";
		print SENDMAIL "To:" . $main::config->{to_email_address} . "\n";
		print SENDMAIL "Subject:" . $main::config->{email_subject} . "\n";

		if($main::config->{html_email_support}){ print SENDMAIL 'Content-type: text/html; charset=utf-8; format=flowed' . "\r\n"; }
		else{ print SENDMAIL 'Content-type: text/plain; charset=utf-8; format=flowed' . "\r\n"; }

		$message = "\nStart Upload: " . $abbr[$smon] . " " . $smday . ", " . $syear . ", " .  $shour . ":" . $smin . ":" . $ssec . "\n";
		$message .= "End Upload: " . $abbr[$emon] . " " . $emday . ", " . $eyear . ", " .  $ehour . ":" . $emin . ":" . $esec . "\n";
		$message .= "SID: ". $tmp_sid . "\n";
		$message .= "Remote IP: " . $ENV{'REMOTE_ADDR'}  . "\n";
		$message .= "Browser: " . $ENV{'HTTP_USER_AGENT'} . "\n\n";
		$message .= $file_list;

		print SENDMAIL $message;
		close(SENDMAIL);
	}
	else{ warn("Failed to open sendmail $!"); }
}

####################################################
#  formatBytes($file_size, 99) mixed file sizes
#  formatBytes($file_size, 0) KB file sizes
#  formatBytes($file_size, 1) MB file sizes etc
####################################################
sub format_bytes{
	my $bytes = shift;
	my $byte_format = shift;
	my $byte_size = 1024;
	my $i = 0;
	my @byte_type = (" KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");

	$bytes /= $byte_size;

	if($byte_format == 99 || $byte_format > 7){
		while($bytes > $byte_size){
			$bytes /= $byte_size;
			$i++;
		}
	}
	else{
		while($i < $byte_format){
			$bytes /= $byte_size;
			$i++;
		}
	}

	$bytes = sprintf("%1.2f", $bytes);
	$bytes .= $byte_type[$i];

	return $bytes;
}

#####################################################################
# Print config, driver settings and 'Environment Variables' to screen
#####################################################################
sub debug{
	my ($msg, $temp_dir_state, $upload_dir_state) = ();

	if(!-d $main::config->{temp_dir}){ $temp_dir_state = "<font color='red'>$main::config->{temp_dir}<\/font>"; }
	else{ $temp_dir_state = "<font color='green'>$main::config->{temp_dir}<\/font>"; }

	if(!-d $main::config->{upload_dir}){ $upload_dir_state = "<font color='red'>$main::config->{upload_dir}<\/font>"; }
	else{ $upload_dir_state = "<font color='green'>$main::config->{upload_dir}<\/font>"; }

	$msg .= "<u><b>UBER UPLOADER CONFIG SETTINGS<\/b><\/u><br>\n";
	$msg .= "CONFIG_FILE = <b>$main::config->{config_file_name}<\/b><br>\n";
	$msg .= "UBER UPLOADER VERSION = <b>$UBER_VERSION<\/b><br>\n";
	$msg .= "UU_UPLOAD = <b>$THIS_VERSION<\/b><br>\n";

	if($MULTI_CONFIGS_ENABLED){ $msg .= "MULTI_CONFIGS_ENABLED = <b><font color='green'>enabled</font><\/b><br>\n"; }
	else{ $msg .= "MULTI_CONFIGS_ENABLED = <b><font color='red'>disabled</font><\/b><br>\n"; }

	$msg .= "TEMP_DIR = <b>$temp_dir_state<\/b><br>\n";
	$msg .= "UPLOAD_DIR = <b>$upload_dir_state<\/b><br>\n";
	$msg .= "MAX_UPLOAD = <b>" . format_bytes($main::config->{max_upload}, 99) . "<\/b><br>\n";
	$msg .= "GET_DATA_SPEED = <b>$main::config->{get_data_speed}<\/b><br>\n";

	if($main::config->{cedric_progress_bar}){ $msg .= "CEDRIC_PROGRESS_BAR = <b><font color='green'>enabled</font><\/b><br>\n"; }
	else{ $msg .= "CEDRIC_PROGRESS_BAR = <b><font color='red'>disabled</font><\/b><br>\n"; }

	if($main::config->{unique_upload_dir}){
		$msg .= "UNIQUE_UPLOAD_DIR = <b><font color='green'>enabled</font><\/b><br>\n";
		$msg .= "UNIQUE_UPLOAD_DIR_LENGTH = <b>$main::config->{unique_upload_dir_length} chars<\/b><br>\n";
	}
	else{ $msg .= "UNIQUE_UPLOAD_DIR = <b><font color='red'>disabled</font><\/b><br>\n"; }

	if($main::config->{unique_file_name}){
		$msg .= "UNIQUE_FILE_NAME = <b><font color='green'>enabled</font><\/b><br>\n";
		$msg .= "UNIQUE_FILE_NAME_LENGTH = <b>$main::config->{unique_file_name_length} chars<\/b><br>\n";
	}
	else{ $msg .= "UNIQUE_FILE_NAME = <b><font color='red'>disabled</font><\/b><br>\n"; }

	if($main::config->{create_files_by_rename}){ $msg .= "CREATE_FILES_BY_RENAME = <b><font color='green'>enabled</font><\/b><br>\n"; }
	else{ $msg .= "CREATE_FILES_BY_RENAME = <b><font color='red'>disabled</font><\/b><br>\n"; }

	if($main::config->{overwrite_existing_files}){ $msg .= "OVERWRITE_EXISTING_FILES = <b><font color='green'>enabled</font><\/b><br>\n"; }
	else{ $msg .= "OVERWRITE_EXISTING_FILES = <b><font color='red'>disabled</font><\/b><br>\n"; }

	if($main::config->{normalize_file_names}){ $msg .= "NORMALIZE_FILE_NAMES = <b><font color='green'>enabled</font><\/b><br>\n"; }
	else{ $msg .= "NORMALIZE_FILE_NAMES = <b><font color='red'>disabled</font><\/b><br>\n"; }

	if($main::config->{normalize_file_names}){ $msg .= "NORMALIZE_FILE_LENGTH = <b>$main::config->{normalize_file_length} chars<\/b><br>\n"; }

	if($main::config->{normalize_file_names}){ $msg .= "NORMALIZE_FILE_DELIMITER = <b>$main::config->{normalize_file_delimiter}<\/b><br>\n"; }

	if($main::config->{delete_param_file}){ $msg .= "DELETE_PARAM_FILE = <b><font color='green'>enabled</font><\/b><br>\n"; }
	else{ $msg .= "DELETE_PARAM_FILE = <b><font color='red'>disabled</font><\/b><br>\n"; }

	if($main::config->{link_to_upload}){
		$msg .= "LINK_TO_UPLOAD = <b><font color='green'>enabled</font><\/b><br>\n";
		$msg .= "PATH_TO_UPLOAD = <a href=\"$main::config->{path_to_upload}\">$main::config->{path_to_upload}</a><br>\n";
	}
	else{ $msg .= "LINK_TO_UPLOAD = <b><font color='red'>disabled</font><\/b><br>\n"; }

	if($main::config->{send_email_on_upload}){
		$msg .= "SEND_EMAIL_ON_UPLOAD = <b><font color='green'><a href=\"mailto:$main::config->{to_email_address}?subject=Uber Uploader Email Test\">enabled</a></font><\/b><br>\n";
		$msg .= "EMAIL_SUBJECT = <b>$main::config->{email_subject}<\/b><br>\n";

		if($main::config->{html_email_support}){ $msg .= "HTML_EMAIL_SUPPORT = <b><font color='green'>enabled</font><\/b><br>\n"; }
		else{ $msg .= "HTML_EMAIL_SUPPORT = <b><font color='red'>disabled</font><\/b><br>\n"; }

		if($main::config->{link_to_upload_in_email}){ $msg .= "LINK_TO_UPLOAD_IN_EMAIL = <b><font color='green'>enabled</font><\/b><br>\n"; }
		else{ $msg .= "LINK_TO_UPLOAD_IN_EMAIL = <b><font color='red'>disabled</font><\/b><br>\n"; }
	}
	else{ $msg .= "SEND_EMAIL_ON_UPLOAD = <b><font color='red'>disabled</font><\/b><br>\n"; }

	if($main::config->{redirect_after_upload}){
		$msg .= "REDIRECT_AFTER_UPLOAD = <b><font color='green'>enabled</font><\/b><br>\n";

		if($main::config->{redirect_using_js_html}){ $msg .= "REDIRECT_USING_JS_HTML = <b><font color='green'>enabled</font><\/b><br>\n"; }
		else{ $msg .= "REDIRECT_USING_JS_HTML = <b><font color='red'>disabled</font><\/b><br>\n"; }

		if($main::config->{redirect_using_html}){ $msg .= "REDIRECT_USING_HTML = <b><font color='green'>enabled</font><\/b><br>\n"; }
		else{ $msg .= "REDIRECT_USING_HTML = <b><font color='red'>disabled</font><\/b><br>\n"; }

		if($main::config->{redirect_using_js}){ $msg .= "REDIRECT_USING_JS = <b><font color='green'>enabled</font><\/b><br>\n"; }
		else{ $msg .= "REDIRECT_USING_JS = <b><font color='red'>disabled</font><\/b><br>\n"; }

		if($main::config->{redirect_using_location}){ $msg .= "REDIRECT_USING_LOCATION = <b><font color='green'>enabled</font><\/b><br>\n"; }
		else{ $msg .= "REDIRECT_USING_LOCATION = <b><font color='red'>disabled</font><\/b><br>\n"; }

		$msg .= "REDIRECT_URL = <a href=\"$main::config->{redirect_url}?cmd=about\">$main::config->{redirect_url}<\/a><br>\n";
	}
	else{ $msg .= "REDIRECT_AFTER_UPLOAD = <b><font color='red'>disabled</font><\/b><br>\n"; }

	$msg .= "<br><u><b>ENVIRONMENT VARIABLES<\/b><\/u><br>\n";

	foreach my $key (sort keys(%ENV)){ $msg .= "$key = <b>$ENV{$key}<\/b><br>\n"; }

	&kak($msg, 1, __LINE__);
}


############################################
# Rename uploaded file if it already exists
############################################
sub rename_filename{
	my $file_name = shift;
	my $count = shift;
	my $path_to_file = $main::config->{upload_dir} . $file_name;

	if(-e $path_to_file && -f $path_to_file){
		if($file_name =~ /(.*)_(\d*)\.(.*)/){
			# Already renamed so count on
			$count = $2 + 1 ;
			$file_name =~ s/(.*)_(\d*)\.(.*)/$1_$count\.$3/;
		}
		else{
			# not renamed so start counting
			$file_name =~ s/(.*)\.(.*)/$1_$count\.$2/;
		}
		&rename_filename($file_name, $count);
	}
	else{ return $file_name; }
}

#######################
# Normalize file name
######################
sub normalize_filename{
	my $file_name = shift;
	my $delimiter = shift;
	my $max_file_length = shift;

	#$file_name =~ s/\s+//g;

	if(length($file_name) > $max_file_length){ $file_name = substr($file_name, length($file_name) - $max_file_length); }

	#$file_name =~ s/[^a-zA-Z0-9\_\.\-]/$delimiter/g;
	$file_name =~ s/[^a-zA-Z0-9\_\ \.\-]/$delimiter/g;

	return $file_name;
}

#########################
# Generate Randon String
#########################
sub generate_random_string{
	my $length_of_randomstring = shift;
	my @chars=('a'..'z','A'..'Z','0'..'9');
	my $random_string;

	for(my $i = 0; $i < $length_of_randomstring; $i++){ $random_string .= $chars[int(rand(58))]; }

	return $random_string;
}