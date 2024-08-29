<?php
if(get_option('wpawscdndata'))
{
	$wpawscdndata = get_option('wpawscdndata');
	if(array_key_exists('accessid',$wpawscdndata) && $wpawscdndata['accessid']) $accessid = $wpawscdndata['accessid'];
	if(array_key_exists('cachepolicy',$wpawscdndata) && $wpawscdndata['cachepolicy']) $cachepolicy = $wpawscdndata['cachepolicy'];
	if(array_key_exists('compressobject',$wpawscdndata) && $wpawscdndata['compressobject']) $compressobject = $wpawscdndata['compressobject'];
	if(array_key_exists('secretkey',$wpawscdndata) && $wpawscdndata['secretkey']) $secretkey = $wpawscdndata['secretkey'];
	if(array_key_exists('subfolder',$wpawscdndata) && $wpawscdndata['subfolder']) $subfolder = $wpawscdndata['subfolder'];
/*	if($wpawscdndata['verifymethod'] == "EMAIL")
	{
	$dnsselected = "";
	$emailselected = "checked";	
	}*/
	if(array_key_exists('custom',$wpawscdndata) && $wpawscdndata['custom'] == "Yes")	
	{
		$custom = "Checked";
		$wpawscdndomain = $wpawscdndata['customdomain'];
		$type='text';
	}
	if(array_key_exists('customdomain',$wpawscdndata) ) $customdomain = $wpawscdndata['customdomain'];
	if(array_key_exists('priceclass',$wpawscdndata) && $wpawscdndata['priceclass'] == "PriceClass_100") $selected1 = "Selected";
	if(array_key_exists('priceclass',$wpawscdndata) && $wpawscdndata['priceclass'] == "PriceClass_200") $selected2 = "Selected";
	if(array_key_exists('priceclass',$wpawscdndata) && $wpawscdndata['priceclass'] == "PriceClass_All") $selected3 = "Selected";
	
	if(array_key_exists('cachepolicy',$wpawscdndata) && $wpawscdndata['cachepolicy'] == "4135ea2d-6df8-44a3-9df3-4b5a84be39ad") $cpselected1 = "Selected";
	if(array_key_exists('cachepolicy',$wpawscdndata) && $wpawscdndata['cachepolicy'] == "658327ea-f89d-4fab-a63d-7e88639e58f6") $cpselected2 = "Selected";
	if(array_key_exists('cachepolicy',$wpawscdndata) && $wpawscdndata['cachepolicy'] == "b2884449-e4de-46a7-ac36-70bc7f1ddd6d") $cpselected3 = "Selected";
	
	if(array_key_exists('compressobject',$wpawscdndata) && $wpawscdndata['compressobject'] == true) $coselected1 = "Selected";	else $coselected2 = "Selected";	
}

$protocols = [
    'TLSv1' => ['protocol' => CURL_SSLVERSION_TLSv1_0, 'sec' => false],
    'TLSv1.1' => ['protocol' => CURL_SSLVERSION_TLSv1_1, 'sec' => false],
    'TLSv1.2' => ['protocol' => CURL_SSLVERSION_TLSv1_2, 'sec' => true],
    'SSLv3' => ['protocol' => CURL_SSLVERSION_SSLv3, 'sec' => true],
];
$supported = "";
$checlked = "";
$url = 'https://www.howsmyssl.com/a/check';
foreach ($protocols as $name => $value) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSLVERSION, $value['protocol']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch) !== false;

    if ($value['sec'] && $response) {$supported =  $name;}
	elseif (!$value['sec'] && $response) {$supported =  $name;}
}

	if($supported == "SSLv3") $tlsselected1 = "Selected";
	if($supported == "TLSv1.2") $tlsselected2 = "Selected";
	if($supported == "TLSv1.1") $selected3 = "Selected";
	if($supported == "TLSv1") $selected4 = "Selected";

/*check wp-config file for details*/
$wpconfig = get_home_path() . "wp-config.php";
include_once($wpconfig);
if (defined('AWS_Access_ID') && $accessid == NULL ) $accessid = AWS_Access_ID;
if (defined('AWS_Secret_Key') && $secretkey == NULL ) $secretkey = AWS_Secret_Key;
?>

<h1>Setup Amazon Cloudfront CDN</h1>
<p>Now supports variables defined in wp-config.php. Read the <a href='/wp-admin/admin.php?page=wpa-aws-faq-how-to#faq'>FAQ section</a></p>
<div class=gscontainer>
<div class=gsrow>

<div class='gscols12'>
<div id='wpawscdnresult'></div>
</div>

<div class='gscols3'>
<label>Domain Name<br>
<?php
global $wp;  
$current_url = home_url(add_query_arg(array($_GET), $wp->request));
?>
<input type=text id=wpawscdndomain value="<?php echo esc_textarea( $this->wpawscdndomain);?>" class=gsforminput>
<input type=hidden id=wpawscdnnonce value="<?php echo wp_create_nonce(get_current_user());?>" class=gsforminput>
<input type=hidden id=wpawscdnreferrer value="<?php echo $current_url;?>" class=gsforminput>
</label>
</div>

<div class='gscols3'>
<label>Access ID<br>
<input type=text id=wpawscdnaccessid value="<?php echo esc_textarea( $accessid);?>" class=gsforminput>
</label>
</div>

<div class='gscols3'>
<label>Secret Key<br>

<input type=text id=wpawscdnsecretkey value="<?php echo esc_textarea( $secretkey);?>" class=gsforminput>
</label>
</div>

<div class='gscols4'>
<label>Cache Policy<br>
<select id="wpawscdncachepolicy" class="gsforminput" placeholder="Cache Policy">	
<option <?php echo esc_textarea( $cpselected1);?> value="4135ea2d-6df8-44a3-9df3-4b5a84be39ad">Disabled</option>
<option <?php echo esc_textarea( $cpselected2);?> value="658327ea-f89d-4fab-a63d-7e88639e58f6">Enabled with Compression</option>
<option <?php echo esc_textarea( $cpselected3);?> value="b2884449-e4de-46a7-ac36-70bc7f1ddd6d">Enabled without Compression</option>
</select>
</label>
</div>

<div class='gscols4'>
	<label title='Auto selected based on your server response'>TLS Version <sup>i</sup><br>
<select id="wpawscdntlsver" class="gsforminput" placeholder="TLS Version">	
<option <?php echo esc_textarea( $tlsselected1);?> value="SSLv3">SSLv3</option>	
<option <?php echo esc_textarea( $tlsselected2);?> value="TLSv1.2">TLSv1.2</option>
<option <?php echo esc_textarea( $tlsselected3);?> value="TLSv1.1">TLSv1.1</option>
<option <?php echo esc_textarea( $tlsselected4);?> value="TLSv1">TLSv1</option>

</select>
</label>
</div>
	
<div class='gscols4'>
<label>Compress Objects<br>
<select id="wpawscdncompressobject" class="gsforminput" placeholder="Compress Objects Automatically">	
<option <?php echo esc_textarea( $coselected1);?> value="true">Yes</option>
<option <?php echo esc_textarea( $coselected2);?> value="false">No</option>
</select>
</label>
</div>

<div class='gscols4'>
<label>Price Class<br>
<select id="wpawscdnpriceclass" class="gsforminput" placeholder="Price Class">	
<option <?php echo esc_textarea( $selected1);?> value="PriceClass_100">US, Canada and Europe</option>
<option <?php echo esc_textarea( $selected2);?> value="PriceClass_200">US, Canada, Europe &amp; Asia</option>
<option <?php echo esc_textarea( $selected3);?> value="PriceClass_All">All Locations</option>
</select>
</label>
</div>

<div class='gscols6'>
<label>Domain hosted in a sub-folder<br>
<input type=text id=wpawscdnsubfolder value="<?php echo esc_textarea( $subfolder);?>" class=gsforminput>
</label>
</div>

<div class='gscols6'>
&nbsp;<br>
<label><input type=checkbox <?php echo esc_textarea( $custom);?> id=wpawscdnusecdn data-cdn='<?php echo esc_textarea( $wpawscdndomain);?>' name=wpawscdnusecdn > &nbsp; I would like to use a custom CDN domain
</label>
</div>

<div class='gscols3 certblock'>
<label>Custom CDN domain<br>
<input type=<?php echo esc_textarea( $type);?> id=wpawscdncustomdomain value="<?php echo esc_textarea( $wpawscdndomain);?>" class=gsforminput>
</label>
</div>

<div class='gscols6 certblock'>
<?php
if(get_option('wpawscdnarn') && get_option('wpawscdnarn') != "NA")
{
echo "<label>Certificate ARN<br>";
echo "<input type=text READONLY id=wpawscdncertarn value=" . get_option('wpawscdnarn') . " class=gsforminput>";
echo "</label>";
}
else
{
echo "<p class=gstextcenter>&nbsp;<br><a href='/wp-admin/admin.php?page=wpa-aws-ssl' class='gsformbtn'>Request a Certificate First</a></p>";
}
?>
</div>


<div id=wpawscdnnote class='gscols12 gshidden'>
<input type=hidden id=wpawscdnAWSID value=''>
<p><b>NOTE</b>: This feature needs an SSL certificate. The plugin will request a <b>Free</b> certificate from <a href='https://aws.amazon.com/certificate-manager/pricing/' target=_BLANK>Amazon Certificate Manager (ACM)</a>.</p></div>

<div class='gscols3'>
<input type=button id=wpawscdncreate value="Create Distribution" class=gsformbtn>
</div>

<div class='gscols3'>
<input type=button id=wpawscdnlist value="List Distribution" class=gsformbtn>
</div>


<div class='gscols3'>
<input type=button id=wpawscdnmodify value="Modify Distribution" class=gsformbtn>
</div>

<div class='gscols12'>
<p>
<a target=_BLANK href='https://wpadmin.ca/how-to-create-an-aws-user-with-limited-permissions-to-access-cloudfront-only/'>How to create an Amazon user with the correct permission</a>
</p>

	<div class='cachepol cpewc'>
		<strong>Cache Policy With Compression</strong><br>		
This policy is designed to optimize cache efficiency by minimizing the values that CloudFront includes in the cache key. CloudFront doesn’t include any query strings or cookies in the cache key, and only includes the normalized Accept-Encoding header. This enables CloudFront to separately cache objects in the Gzip and Brotli compressions formats when the origin returns them or when CloudFront edge compression is enabled.	
</div>
<div class='cachepol cpewoc'>
	<strong>Cache Policy Without Compression</strong><br>	
This policy is designed to optimize cache efficiency by minimizing the values included in the cache key. No query strings, headers, or cookies are included. This policy is identical to the previous one, but it disables the cache compressed objects setting.	
</div>
<div class='cachepol cpd'>
	<strong>Cache Policy Disabled</strong><br>
This policy disables caching. This policy is useful for dynamic content and for requests that are not cacheable.
</div>
	
	
</div>

</div>
</div>
