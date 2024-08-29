<?php
$domain  ="";
$accessid = $secretkey = $priceclass = $subfolder = $custom = $customdomain = $selected1 = $selected2 = $selected3 = '';
$minttl = 3600;
$maxttl = 86400;
$type = 'hidden';
$dnsselected = "checked";
$emailselected = "";

if(isset($_REQUEST['domain'])) $domain = sanitize_text_field($_REQUEST['domain']);

if($domain == "")
{
require_once(wpawscdnbasedir . "admin/wpa-aws-multi-deploy.php");
}
else
{
require_once(wpawscdnbasedir . "admin/wpa-aws-multi-configure.php");
}

