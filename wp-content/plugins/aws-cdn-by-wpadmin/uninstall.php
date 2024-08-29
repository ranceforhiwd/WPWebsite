<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option('wpawscdndata');
delete_option('wpawscdndomain');
delete_option('WPAdmin_CDN_Ignorelist');
delete_option('WPAdmin_CDN_Ignorepages');
delete_option('wpawscdnarn');
delete_option('WPAdmin_CDN_KEY');
