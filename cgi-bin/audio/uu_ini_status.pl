#!/usr/bin/perl -w

#******************************************************************************************************
#   ATTENTION: THIS FILE HEADER MUST REMAIN INTACT. DO NOT DELETE OR MODIFY THIS FILE HEADER.
#
#   Name: uu_ini_status.pl
#   Link: http://uber-uploader.sourceforge.net/
#   Revision: 2.0
#   Date: 17/03/2007 6:25PM
#   Initial Developer: Peter Schmandra
#   Description: Initialize the progress bar and exit
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
#************************************************************************************************************************************
my $THIS_VERSION = "2.0";    # Version of this driver  

# Makes %ENV safer
$ENV{'PATH'} = '/bin:/usr/bin:/usr/local/bin';
delete @ENV{'IFS', 'CDPATH', 'ENV', 'BASH_ENV'};

use strict;
use lib qw(.);                         # Add current directory to list of valid paths
#use CGI::Carp 'fatalsToBrowser';      # Dump fatal errors to screen
use uu_lib;                            # Load the uu_lib.pm module
                                  
#####################################################################################################################
# The following possible query string formats are assumed
#
# 1. ?tmp_sid=some_sid_number&config_file=some_config_name&rnd_id=some_random_number
# 2. ?tmp_sid=some_sid_number&rnd_id=some_random_number
# 3. ?cmd=about
#####################################################################################################################
my %query_string = &parse_query_string($ENV{'QUERY_STRING'});

# Check for tainted sid
if(exists($query_string{'tmp_sid'})){
	if($query_string{'tmp_sid'} !~ m/(\w{32})/){ &xml_kak("<ini_status><error_status>1</error_status><error_msg>ERROR: Invalid session-id</error_msg></ini_status>"); }
	else{ $query_string{'tmp_sid'} = $1; }
} 

# Check for tainted config file name
if(exists($query_string{'config_file'})){
	if($query_string{'config_file'} !~ m/(\w{5,32})/){ &xml_kak("<ini_status><error_status>1</error_status><error_msg>ERROR: Invalid config file name</error_msg></ini_status>"); }
	else{ $query_string{'config_file'} = $1; }
}

# Check for tainted command
if(exists($query_string{'cmd'})){
	if($query_string{'cmd'} eq 'about'){ &kak("<u><b>UBER UPLOADER AJAX PROGRESS PAGE</b><\/u><br> UBER UPLOADER VERSION = <b>" . $UBER_VERSION . "<\/b><br>UU_INI_STATUS = <b>" . $THIS_VERSION . "<\/b><br>\n", 1, __LINE__); }
	else{ &kak("<font color='red'>ERROR<\/font>: Invalid command<br>\n", 1, __LINE__); }
}

# Make sure cmd or tmp_sid was passed but not both
if(!exists($query_string{'cmd'}) && !exists($query_string{'tmp_sid'})){ &kak("<font color='red'>ERROR<\/font>: Invalid parameters<br>\n", 1, __LINE__); }
if(exists($query_string{'cmd'}) && exists($query_string{'tmp_sid'})){ &kak("<font color='red'>ERROR<\/font>: Conflicting parameters<br>\n", 1, __LINE__); }

#######################################################################
# Attempt to load the config file that was passed to the script. If
# no config file name was passed then load the default config file. 
#######################################################################
if(exists($query_string{'config_file'}) && $MULTI_CONFIGS_ENABLED){ 
	my $config_file = $query_string{'config_file'};
	
	unless(eval "use $config_file"){
		if($@){ &xml_kak("<ini_status><error_status>1</error_status><error_msg>ERROR: Failed to load config file " . $config_file . ".pm</error_msg><stop_upload>1</stop_upload></ini_status>"); }
	}
}
elsif(exists($query_string{'config_file'}) && !$MULTI_CONFIGS_ENABLED){
	if(!$MULTI_CONFIGS_ENABLED){ &xml_kak("<ini_status><error_status>1</error_status><error_msg>ERROR: Multi config files disabled</error_msg><stop_upload>1</stop_upload></ini_status>"); }
	elsif($query_string{'config_file'} !~ m/(\w{5,32})/){ &xml_kak("<ini_status><error_status>1</error_status><error_msg>ERROR: Invalid config file name</error_msg><stop_upload>1</stop_upload></ini_status>"); }
}
else{
	unless(eval "use uu_default_config"){
		if($@){ &xml_kak("<ini_status><error_status>1</error_status><error_msg>ERROR: Failed to load default config file uu_default_config.pm</error_msg><stop_upload>1</stop_upload></ini_status>"); }
	}
}

my $tmp_sid = $query_string{'tmp_sid'};                               # Assign session-id                                      
my $temp_dir_sid = $main::config->{temp_dir} . $tmp_sid;              # temp_dir/session-id                      
my $flength_file = $temp_dir_sid . "/flength";                        # temp_dir/session-id/flength     
my $flength_file_exists = 0;                                          # track the flength file
my $total_bytes = 0;                                                  # size of the upload in bytes
      
# Keep trying to find the flength file for 10 secs
for(my $i = 0; $i < 10; $i++){
	if(-e $flength_file && -r $flength_file){
		# We found the flength file
		$flength_file_exists = 1;
		
		open(FLENGTH, $flength_file);
		$total_bytes = <FLENGTH>;
		close(FLENGTH);
		
		last;
	}
	else{ sleep(1); }
}

#####################################################################################
# Ok, we couldn't find the flength file after 10 seconds. This means
#
# a. The upload was so fast the flength file was deleted before it could be read.
# b. The flength file does not exist because the script is not set up properly.
# c. The flength file does not exist due to some kind of server caching.
#
# More info can be found at http://uber-uploader.sourceforge.net/?section=flength 
#
# So, issue "Failed to find flength file" and exit. Upload may succeed anyway.
#####################################################################################
if(!$flength_file_exists){ 
	&xml_kak("<ini_status><error_status>1</error_status><error_msg>Failed to find flength file - See http://wiki.phpmotion.com/HelpFileUploadingErrors</error_msg></ini_status>"); 
}

######################################################################
# Found the flength file but it contains an error. So, clean up the 
# temp directories and issue error
######################################################################
if($total_bytes =~ m/ERROR/g){
	&deldir($temp_dir_sid);
	&xml_kak("<ini_status><error_status>1</error_status><error_msg>$total_bytes</error_msg><stop_upload>1</stop_upload></ini_status>");
}

# Everything looks good so build the xml output and dump it to screen (initialize progress bar) 
my $xml_msg;
$xml_msg .= "<ini_status>";
$xml_msg .= "<error_status>0</error_status>";
$xml_msg .= "<start_time>".time()."</start_time>";
$xml_msg .= "<temp_dir_sid>".$temp_dir_sid."</temp_dir_sid>";
$xml_msg .= "<total_bytes>".$total_bytes."</total_bytes>";
$xml_msg .= "<get_data_speed>".$main::config->{get_data_speed}."</get_data_speed>";
$xml_msg .= "<cedric_progress_bar>".$main::config->{cedric_progress_bar}."</cedric_progress_bar>";
$xml_msg .= "</ini_status>";              
&xml_kak($xml_msg);

# Dump xml to screen
sub xml_kak{
	my $xml = shift;
	
	print "Content-type: text/xml\n\n"; 
	print "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
	print $xml;
	exit;
}