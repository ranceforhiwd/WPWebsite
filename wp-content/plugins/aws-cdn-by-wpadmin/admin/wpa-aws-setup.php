<p>
<a class='gsformbtn' href='https://wpadmin.ca/donation/' target=_BLANK>Help us stay focussed - Donate a Coffee</a>
</p>
<?php 
$wpawscdnsubfolder = content_url();
$wpa_these = array("http:","https:","/","wp-content",$this->wpawscdndomain);
$wpawscdnsubfolder = str_replace($wpa_these,"",$wpawscdnsubfolder);
if($wpawscdnsubfolder == null) $subfolder = false;

$accessid = $secretkey = $priceclass = $subfolder = $custom = $customdomain = $selected1 = $selected2 = $selected3 = '';
$minttl = 3600;
$maxttl = 86400;
$type = 'hidden';
$dnsselected = "checked";
$emailselected = "";
$wpawscdndomain = 'cdn.' . str_replace('www.','',$this->wpawscdndomain);

/*check wp-config file for details*/
$wpconfig = get_home_path() . "wp-config.php";
include($wpconfig);
if (defined('AWS_CDN_Domain') && $this->wpawscloudfront == NULL ) $this->wpawscloudfront = AWS_CDN_Domain;

if($this->wpawscloudfront)
{
require(wpawscdnbasedir . 'admin/wpa-aws-configure.php');
}
else
{
require(wpawscdnbasedir . 'admin/wpa-aws-deploy.php');
}

