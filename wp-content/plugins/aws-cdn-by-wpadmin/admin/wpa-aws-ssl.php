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


if(get_option('wpawscdndata'))
{
	$wpawscdndata = get_option('wpawscdndata');
	$accessid = $wpawscdndata['accessid'];
	$minttl = $wpawscdndata['minttl'];
	$maxttl = $wpawscdndata['maxttl'];
	$secretkey = $wpawscdndata['secretkey'];
	$subfolder = $wpawscdndata['subfolder'];
	/*if($wpawscdndata['verifymethod'] == "EMAIL")
	{
	$dnsselected = "";
	$emailselected = "checked";	
	}
	if($wpawscdndata['custom'] == "Yes")
	{
		$custom = "Checked";
		$wpawscdndomain = $wpawscdndata['customdomain'];
		$type='text';
	}*/
	$customdomain = $wpawscdndata['customdomain'];
	if($wpawscdndata['priceclass'] == "PriceClass_100") $selected1 = "Selected";
	if($wpawscdndata['priceclass'] == "PriceClass_200") $selected2 = "Selected";
	if($wpawscdndata['priceclass'] == "PriceClass_All") $selected3 = "Selected";
}

/*check wp-config file for details*/
$wpconfig = get_home_path() . "wp-config.php";
include_once($wpconfig);
if (defined('AWS_Access_ID') && $accessid == NULL ) $accessid = AWS_Access_ID;
if (defined('AWS_Secret_Key') && $secretkey == NULL ) $secretkey = AWS_Secret_Key;
?>

<h1>SSL Manager</h1>

<div class=gscontainer>
<div class=gsrow>

<div class='gscols12'>
<div id='wpawscdnresult'></div>
</div>

<div class='gscols12'>
<p>If you plan to use custom domain name with Cloudfront, you will need an SSL certificate. Amazon offers FREE SSL certificate that can be used with cloudfront distributions.</p>
</div>


<div class='gscols6'>
<label>Access ID<br>
<?php
global $wp;  
$current_url = home_url(add_query_arg(array($_GET), $wp->request));
?>
<input type=text id=wpawscdnaccessid value="<?php echo esc_textarea( $accessid); ?>" class=gsforminput>
<input type=hidden id=wpawscdnnonce value="<?php echo wp_create_nonce(get_current_user());?>" class=gsforminput>
<input type=hidden id=wpawscdnreferrer value="<?php echo $current_url;?>" class=gsforminput>
</label>
</div>

<div class='gscols6'>
<label>Secret Key<br>

<input type=text id=wpawscdnsecretkey value="<?php echo esc_textarea ( $secretkey); ?>" class=gsforminput>
</label>
</div>

<div class='gscols6'>
<label>Domain Name<br>
<?php 
if ( is_multisite()  && is_super_admin()) 
{
$wpawscdnsites = get_sites();
$selected = "";
?>
<select id=wpawscdndomain class="gsforminput" >
<?php	
foreach($wpawscdnsites as $key => $wpasite)
{
$domainname = $wpasite->domain;
if($domainname == $_SERVER['HTTP_HOST']) $selected = "selected";
if($wpasite->path != "/" ) $domainname .= $wpasite->path;
echo "<option $selected value=\"".esc_textarea($domainname)."\">".esc_textarea($domainname)."</option>";
$selected = "";
}
?>
</select>
<?php 
}
else
{
?>
<input type=text id=wpawscdndomain value="<?php echo esc_textarea( $this->wpawscdndomain);?>" class=gsforminput>
<?php 
}
?>
</label>
</div>

<div class='gscols6'>
Certificate Verification Method:<br>
<input type=radio <?php echo esc_textarea( $dnsselected); ?> id=wpawscdnbydns name=wpawscdnverifymethod value='DNS'>DNS <input type=radio <?php echo esc_textarea( $emailselected); ?> id=wpawscdnbyemail name=wpawscdnverifymethod value='EMAIL'>Email
</div>

<div class='gscols6'>
<input type=button id=wpawssslcreate value="Request Certificate" class=gsformbtn>
</div>

<div class='gscols6'>
<input type=button id=wpawsssllist value="List Certificate" class=gsformbtn>
</div>




</div>
</div>
