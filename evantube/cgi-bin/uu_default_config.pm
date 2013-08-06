package uu_default_config;
use strict;
use Exporter;

@uu_default_config::ISA = qw( Exporter );
@uu_default_config::EXPORT = qw($config);

use vars qw($config);

$config = {
	config_file_name         => 'uu_default_config',
	temp_dir                 => $ENV{'DOCUMENT_ROOT'} . '/temp/',
	upload_dir               => $ENV{'DOCUMENT_ROOT'} . '/uploads/avi/',
	redirect_url             => '/uploader_finished.php',
	path_to_upload           => '/uploads/avi/',
	unique_upload_dir        => 0,
	unique_upload_dir_length => 20,
	unique_file_name         => 1,
	unique_file_name_length  => 20,
	create_files_by_rename   => 1,
	max_upload               => 209715200,
	overwrite_existing_files => 0,
	redirect_after_upload    => 1,
	redirect_using_js_html   => 1,
	redirect_using_html      => 0,
	redirect_using_js        => 0,
	redirect_using_location  => 0,
	delete_param_file        => 1,
	get_data_speed           => 1000,
	cedric_progress_bar      => 1,
	disallow_extensions      => '(3gp|vob|part|swf|divx|sh|php|php3|php4|php5|py|shtml|phtml|cgi|pl|plx|htaccess|htpasswd|zip|tar|txt|doc|xls|jpg|gif|htm|html|xml|exe|csv|bmp)',
	allow_extensions         => '(wmv|mpg|mpeg|avi|mp4|flv|mov|moov|divx)',
	normalize_file_names     => 1,
	normalize_file_delimiter => '_',
	normalize_file_length    => 64,
	link_to_upload           => 0,
	send_email_on_upload     => 0,
	html_email_support       => 0,
	link_to_upload_in_email  => 0,
	email_subject            => 'Video File Upload',
	to_email_address         => 'email_1@somewhere.com,email_2@somewhere.com',
	from_email_address       => 'admin@yoursite.com',
};

1;
