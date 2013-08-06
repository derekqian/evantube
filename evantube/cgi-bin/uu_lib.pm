package uu_lib;
use strict; 
use Exporter;
 
@uu_lib::ISA = qw( Exporter ); 
@uu_lib::EXPORT = qw($UBER_VERSION $DEBUG_ENABLED $MULTI_CONFIGS_ENABLED deldir kak parse_query_string); 
 
use vars qw($UBER_VERSION $DEBUG_ENABLED $MULTI_CONFIGS_ENABLED);
use subs qw(deldir kak parse_query_string);

$UBER_VERSION = "4.6";       # Version of Uber-Uploader
$DEBUG_ENABLED = 1;          # WARNING! Make sure to set DEBUG_ENABLED back to 0 when you go live.
$MULTI_CONFIGS_ENABLED = 0;  # Set this value to 1 if you are using multiple config files

my $print_issued = 0;

#########################################
# Delete a directory and everthing in it
#########################################
sub deldir{
	my $del_dir = shift;
	
	if(-d $del_dir){
		if(opendir(DIRHANDLE, $del_dir)){
			my @file_list = readdir(DIRHANDLE);
			
			closedir(DIRHANDLE);
			
			foreach my $file (@file_list){
				unless(($file eq ".") || ($file eq "..")){
					if($file =~ m/(\w{5,15})/){
						my $file_name = $1; 
					
						for(my $i = 0; $i < 5; $i++){ 
							if(unlink($del_dir . "/" . $file_name)){ last; }
							else{ sleep(1); }
						} 
					}
				}
			}
			
			for(my $i = 0; $i < 5; $i++){
				if(rmdir($del_dir)){ last; }
				else{ sleep(1); }
			}
		}
		else{ warn("Cannont open $del_dir: $!"); }
	}
}

########################################################################
# Output a message to the screen 
#
# You can use this function to debug your script. 
#
# eg. &kak("The value of blarg is: " . $blarg . "<br>", 1, __LINE__);
# This will print the value of blarg and exit the script.
#
# eg. &kak("The value of blarg is: " . $blarg . "<br>", 0, __LINE__);
# This will print the value of blarg and continue the script.
########################################################################
sub kak{
	my $msg = shift;
	my $kak_exit = shift;
	my $line  = shift;
	
	if(!$print_issued){ 
		print "Content-type: text/html\n\n";
		$print_issued = 1; 
	}
	
	print "<!DOCTYPE HTML PUBLIC \"-\/\/W3C\/\/DTD HTML 4.01 Transitional\/\/EN\">\n";
	print "<html>\n";
	print "  <head>\n";
	print "    <title>Uber-Uploader - Free File Upload Progress Bar<\/title>\n";
	print "      <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">\n";
	print "      <meta http-equiv=\"Pragma\" content=\"no-cache\">\n";
	print "      <meta http-equiv=\"CACHE-CONTROL\" content=\"no-cache\">\n";
	print "      <meta http-equiv=\"expires\" content=\"-1\">\n";
	print "      <meta name=\"robots\" content=\"none\">\n";
	print "  <\/head>\n";
	print "  <body style=\"background-color: #EEEEEE; color: #000000; font-family: arial, helvetica, sans_serif;\">\n";
	print "    <br>\n";
	print "    <div align='center'>\n";
	print "    $msg\n";
	print "    <br>\n";
	print "    <!-- kak on line $line -->\n";
	print "    </div>\n";
	print "  </body>\n";
	print "</html>\n";
	
	if($kak_exit){ exit; }
}

##########################
# Parse the query string
##########################
sub parse_query_string{
	my $buffer = shift;
	my @pairs = split(/&/, $buffer);
	my %query_string = ();
	
	foreach my $pair (@pairs){
		my ($name, $value) = split(/=/, $pair);
		
		$value =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;
		$query_string{$name} = $value;
	}
	
	return %query_string;
}

1;
