<?php

class wpaawscdnadmin extends wpawscdn
{
private $autoload, $checkcdn, $wpadbval, $wpawscdnblogid, $wpawscdndomain, $wpawscdnbaredomain, $wpawscloudfront;

	public function __construct()
	{
	$wpawscfdomain = null;
	$domain = $_SERVER['HTTP_HOST'];
	$this->wpawscdndomain  = $domain;
	$this->wpawscdnbaredomain = str_replace("www.","",$domain);
	if(get_option('wpawscdndomain') && strpos(get_option('wpawscdndomain'),'cloudfront') )
	{
	$this->wpawscloudfront = get_option('wpawscdndomain');
	}

	if(get_option('wpawscdndata'))
	{
		$wpawscdndata = get_option('wpawscdndata');
		if(array_key_exists('custom',$wpawscdndata) && $wpawscdndata['custom'] == "Yes" && $wpawscdndata['customdomain'] != "")
		{
		$this->wpadbval['customdomain'] = $wpawscdndata['customdomain'];
		}	
	}

	$this->autoload = 'yes';
	
		function wpawscdn_load_scripts()
		{
		$wpawscdn_JS = wpawscdnurl."admin/asset/js/script.min.js";
		$wpawscdn_CSS = wpawscdnurl."admin/asset/css/style.min.css";
		wp_enqueue_script('wpawscdn',$wpawscdn_JS);
		wp_enqueue_style('wpawscdn',$wpawscdn_CSS);
		}
		add_action('admin_enqueue_scripts', 'wpawscdn_load_scripts');
	$this->checkcdn = false;
	}

	public function load()
	{
	add_action("admin_menu", array(&$this,'wpawscdnmenus'));
	$wpawscdn_uploadsdir = wp_upload_dir();
	$wpawscdn_txtfile = $wpawscdn_uploadsdir['basedir'] . "/" . $this->wpawscdnbaredomain . ".txt";
	if(!file_exists($wpawscdn_txtfile )) $wpawscdn_txtfile = $wpawscdn_uploadsdir['basedir'] . "/" . $this->wpawscdndomain . ".txt";
	
	if(file_exists($wpawscdn_txtfile))
	{
	$wpawscdnlegacyfile = file_get_contents($wpawscdn_txtfile);
	preg_match_all('#<awsid[^>]*>(.*?)</awsid>#', $wpawscdnlegacyfile, $wpawscdn_legacyid);
	preg_match_all('#<awsdomain[^>]*>(.*?)</awsdomain>#', $wpawscdnlegacyfile, $wpawscdn_legacydomain);
	$wpawscdn_xid = trim(strip_tags($wpawscdn_legacyid[0][0]));
	$wpawscdn_xdomain = trim(strip_tags($wpawscdn_legacydomain[0][0]));
		if(!get_option('wpawscdnisactive'))
		{
		add_option('wpawscdnisactive', $wpawscdn_xdomain, '', $this->autoload);
		}
		if(!get_option('wpawscdndomain'))
		{
		add_option('wpawscdndomain', $wpawscdn_xdomain, '', $this->autoload);
		unlink($wpawscdn_txtfile);
		}
		
	}

	}
	
	public function wpawscdnget($which){
	return $which;
	}
	
	public function wpawscdnmenus()
	{
	if(current_user_can('administrator'))
	{
	add_menu_page("WPAdmin CDN", "WPAdmin CDN", 'edit_pages', "wpa-aws-setup", array(&$this,'wpawscdn_pagesetup'),'dashicons-performance');
		if(is_multisite())
		{
		add_submenu_page("wpa-aws-setup","Multi-Site Setup", "Multi-Site Setup",'edit_pages',"wpa-aws-multisite",array(&$this,'wpawscdn_pagemulti'));
		}
	add_submenu_page("wpa-aws-setup","SSL Manager", "SSL Manager",'edit_pages',"wpa-aws-ssl",array(&$this,'wpawscdn_pagessl'));
	add_submenu_page("wpa-aws-setup","Faq", "FAQ / How To",'edit_pages',"wpa-aws-faq-how-to",array(&$this,'wpawscdn_pagefaq'));	
	add_submenu_page("wpa-aws-setup","Support Us", "Support Us",'edit_pages',"wpa-aws-donate",array(&$this,'wpawscdn_pagedonate'));
	}
	}

	public function wpawscdn_pagesetup() {
	require_once(wpawscdnbasedir . "admin/wpa-aws-setup.php");
	}

	public function wpawscdn_pagefaq() {
	require_once(wpawscdnbasedir . "admin/wpa-aws-faq-how-to.php");
	}
	
	public function wpawscdn_pagessl() {
	require_once(wpawscdnbasedir . "admin/wpa-aws-ssl.php");
	}
	
	public function wpawscdn_pagedonate() {
	require_once(wpawscdnbasedir . "admin/wpa-aws-donate.php");
	}	

	public function wpawscdn_pagemulti() {
	require_once(wpawscdnbasedir . "admin/wpa-aws-multisite.php");
	}
	
	/*AWS authentication code*/
	public function wpawscdn_auth($what = null)
	{
	if($what == "ACM")
	{
	if( !class_exists( 'Aws\Acm\AcmClient' ) ) { require  wpawscdnbasedir . 'admin/aws/aws-autoloader.php'; }
	$wpawscdn_cert = new Aws\Acm\AcmClient([
	'version'     => 'latest',
	'region'  => 'us-east-1',
	'credentials' => [
	'key'    => $this->wpadbval['accessid'],
	'secret' => $this->wpadbval['secretkey']
	],
	'http'    => [
	'verify' => wpawscdnbasedir .'admin/cacert.pem'
	]
	]);
	}
	else
	{
	if( !class_exists( 'Aws\CloudFront\CloudFrontClient' ) ) { require  wpawscdnbasedir . 'admin/aws/aws-autoloader.php'; }
	$wpawscdn_cert = new Aws\CloudFront\CloudFrontClient([
	'version'     => 'latest',
	'region'  => 'us-east-1',
	'credentials' => [
	'key'    => $this->wpadbval['accessid'],
	'secret' => $this->wpadbval['secretkey']
	],
	'http'    => [
	'verify' => wpawscdnbasedir.'admin/cacert.pem'
	]
	]);
	}
	
	return $wpawscdn_cert;
	}
	
	public function wpaac_add_pageexclusion()
	{
	if ( isset($_REQUEST) )
		{
		$this->wpaws_sanitize($_REQUEST,'nocheck');
        
         $retval = $this->validateNonce($_REQUEST['nonce']);
       if( $retval != "success")
       {
       echo $retval;
       wp_die();        
       }
        
		if(is_multisite())
		{
			$this->switchMultisiteBlogOn( $this->wpadbval["domain"]);
		}
		$data = $this->wpadbval['pages'];
		$data = str_replace("none","",$data);
		$data = explode(",",$data);
		$data = array_filter($data);
		$data = implode(",",$data);

		if($data == "") $data = "NA";
		if(get_option('WPAdmin_CDN_Ignorepages'))
		{
		update_option('WPAdmin_CDN_Ignorepages', $data, '', $this->autoload);
		}
		else
		{
		add_option('WPAdmin_CDN_Ignorepages', $data, '', $this->autoload);
		}
		echo  "<div class='gscols12 alert alert-success'>Page Exclusion List Updated<br><a href=''>Reload the page</a> to continue</div>";
		}
	
	wp_die();
	}
	
	public function wpaac_add_exclusion()
	{
	if ( isset($_REQUEST) )
		{
		$this->wpaws_sanitize($_REQUEST,'nocheck');
        
          $retval = $this->validateNonce($_REQUEST['nonce']);
       if( $retval != "success")
       {
       echo $retval;
       wp_die();        
       }
        
        
		if(is_multisite())
		{
			$this->switchMultisiteBlogOn( $this->wpadbval["domain"]);
		}
		$data = $this->wpadbval['terms'];
		$data = explode("\n",$data);
		$data = array_filter($data);
		$data = implode(",",$data);
			if($data == "") $data = "NA";
		
			if(get_option('WPAdmin_CDN_Ignorelist'))
			{
			update_option('WPAdmin_CDN_Ignorelist', $data, '', $this->autoload);
			}
			else
			{
			add_option('WPAdmin_CDN_Ignorelist', $data, '', $this->autoload);
			$tmpval = get_option('WPAdmin_CDN_Ignorelist');
			if($tmpval == NULL) update_option('WPAdmin_CDN_Ignorelist', $data ,$this->autoload);	
			}
		echo  "<div class='gscols12 alert alert-success'>Exclusion List Updated<br><a href=''>Reload the page</a> to continue</div>";
		}
	
	wp_die();
	}
	
	public function wpaac_activate_fsdomain()
	{
	$this->wpaws_sanitize($_REQUEST,'nocheck');	
    
     $retval = $this->validateNonce($_REQUEST['nonce']);
       if( $retval != "success")
       {
       echo $retval;
       wp_die();        
       }
        
		if(is_multisite())
		{
			if ( isset($_REQUEST) )
			{
			$this->switchMultisiteBlogOn( $this->wpadbval["cdndomain"]);
			}
		}
		
		if($this->wpawscloudfront == NULL) 
		{
			$wpconfig = get_home_path() . "wp-config.php";
			include_once($wpconfig);
			if (defined('AWS_CDN_Domain') && $this->wpawscloudfront == NULL ) $this->wpawscloudfront = AWS_CDN_Domain;
		}
	$wpawscdn_ipaddress = gethostbyname($this->wpawscloudfront);
	if($wpawscdn_ipaddress !=  $this->wpawscloudfront)
	{

	/*add to wp-config*/
		
		if(is_multisite() && get_current_blog_id()  == 1 && is_super_admin() )
		{
			$this->wpaws_wpconfig('activate','AWS_CDN_Isactive',$this->wpawscloudfront);
		}
		elseif(!is_multisite() )
		{
			$this->wpaws_wpconfig('activate','AWS_CDN_Isactive',$this->wpawscloudfront);
		}
		
	
		if(get_option('wpawscdnisactive'))
		{
			update_option('wpawscdnisactive', $this->wpawscloudfront, '', $this->autoload);
		}
		else
		{
			add_option('wpawscdnisactive', $this->wpawscloudfront, '', $this->autoload);
		}
		
		echo  "<div class='gscols12 alert alert-success'>CDN " . esc_html($this->wpawscloudfront) . " activated<br><a href=''>Reload the page</a> to continue</div>";
	}
	else
	{
	echo  "<div class='gscols12 alert alert-warning'>CDN " . esc_html($this->wpawscloudfront) . " is being deployed, try to activate after some time.</div>";
	}
	$this->switchMultisiteBlogOff();
	wp_die();
	}
	
	public function wpaac_activate_csdomain()
	{
	$this->wpaws_sanitize($_REQUEST,'nocheck');			
    
      $retval = $this->validateNonce($_REQUEST['nonce']);
       if( $retval != "success")
       {
       echo $retval;
       wp_die();        
       }
        
        
		if(is_multisite())
		{
			if ( isset($_REQUEST) )
			{
			$this->switchMultisiteBlogOn( $this->wpadbval["cdndomain"]);
			}
		}
	$wpawscdn_ipaddress = gethostbyname($this->wpadbval['cdndomain']);
	if($wpawscdn_ipaddress !=  $this->wpadbval['cdndomain'])
	{
		$this->wpadbval = get_option('wpawscdndata');
		
			/*add to wp-config*/
		if(is_multisite() && get_current_blog_id()  == 1 && is_super_admin() )
		{
			$this->wpaws_wpconfig('activate','AWS_CDN_Isactive',$this->wpadbval['customdomain']);
		}
		elseif(!is_multisite() )
		{
			$this->wpaws_wpconfig('activate','AWS_CDN_Isactive',$this->wpadbval['customdomain']);
		}
		
		if(get_option('wpawscdnisactive'))
		{
			update_option('wpawscdnisactive', $this->wpadbval['customdomain'], '', $this->autoload);
		}
		else
		{
			add_option('wpawscdnisactive', $this->wpadbval['customdomain'], '', $this->autoload);
		}
		echo  "<div class='gscols12 alert alert-success'>CDN " . esc_html($this->wpadbval['customdomain']) . " activated<br><a href=''>Reload the page</a> to continue</div>";
	}
	else
	{
	echo  "<div class='gscols12 alert alert-warning'>CDN " . esc_html($this->wpadbval['customdomain']) . " is being deployed, try to activate after some time.</div>";
	}
	$this->switchMultisiteBlogOff();
	wp_die();
	}
	
	public function wpaac_disable_fsdomain()
	{
	$this->wpaws_sanitize($_REQUEST,'nocheck');	
    
     $retval = $this->validateNonce($_REQUEST['nonce']);
       if( $retval != "success")
       {
       echo $retval;
       wp_die();        
       }
        
		if(is_multisite())
		{
			if ( isset($_REQUEST) )
			{
			$this->switchMultisiteBlogOn( $this->wpadbval['cdndomain']);
			}
		}
	
		if(is_multisite() && get_current_blog_id()  == 1 && is_super_admin() )
		{
			$this->wpaws_wpconfig('disable','AWS_CDN_Isactive','NA');
		}
		elseif(!is_multisite() )
		{
			$this->wpaws_wpconfig('disable','AWS_CDN_Isactive','NA');
		}
		
		
	update_option('wpawscdnisactive', 'NA', '', $this->autoload);
	echo  "<div class='gscols12 alert alert-warning'>CDN " . esc_html($this->wpawscloudfront) . " de-activated<br><a href=''>Reload the page</a> to continue</div>";
	$this->switchMultisiteBlogOff();
	wp_die();
	}
	
	public function wpaac_reset_fsdomain()
	{
		if(is_multisite())
		{
			$this->wpaws_sanitize($_REQUEST,'nocheck');
			if ( isset($_REQUEST) )
			{
			$this->switchMultisiteBlogOn( $this->wpadbval["domain"]);
			}
		}

  $retval = $this->validateNonce($_REQUEST['nonce']);
       if( $retval != "success")
       {
       echo $retval;
       wp_die();        
       }
        
		if(is_multisite() && get_current_blog_id()  == 1 && is_super_admin() )
		{
			$this->wpaws_wpconfig('disable','AWS_CDN_Domain','NA');
			$this->wpaws_wpconfig('disable','AWS_CDN_Isactive','NA');
		}
		elseif(!is_multisite() )
		{
			$this->wpaws_wpconfig('disable','AWS_CDN_Domain','NA');
			$this->wpaws_wpconfig('disable','AWS_CDN_Isactive','NA');
		}
		

		
	update_option('wpawscdndomain', 'NA', '', $this->autoload);
	update_option('wpawscdnisactive', 'NA', '', $this->autoload);
	echo  "<div class='gscols12 alert alert-danger'>CDN has been reset<br><a href=''>Reload the page</a> to continue</div>";
	$this->switchMultisiteBlogOff();
	wp_die();
	}
	
	public function wpaac_reactivate_cdn()
	{
		if ( isset($_REQUEST) )
		{
		$this->wpaws_sanitize($_REQUEST,'nocheck');
        
         $retval = $this->validateNonce($_REQUEST['nonce']);
       if( $retval != "success")
       {
       echo $retval;
       wp_die();        
       }
        

$this->wpaws_wpconfig('add','AWS_CDN_Domain',$this->wpadbval['id']);
			
		if(get_option('wpawscdndomain'))
			{
			update_option('wpawscdndomain', $this->wpadbval['id'], $this->autoload);
			$tmpval = get_option('wpawscdndomain');
			if($tmpval == NULL) add_option('wpawscdndomain', $this->wpadbval['id'], $this->autoload);
			}
			else
			{
			add_option('wpawscdndomain', $this->wpadbval['id'], $this->autoload);
			$tmpval = get_option('wpawscdndomain');
			if($tmpval == "") update_option('wpawscdndomain', $this->wpadbval['id'], $this->autoload);	
			}

	echo  "<div class='gscols12 alert alert-success'>CDN <strong>".esc_html($this->wpadbval['id'])."</strong> has been re-added to <strong>".esc_html($this->wpadbval['domain'])."</strong><br><a href=''>Reload the page</a> to continue</div>";
	
		}
	$this->switchMultisiteBlogOff();
	wp_die();
	}
	
	public function wpaac_list_cdn($type = NULL)
	{
  
     
        if ( isset($_REQUEST) )
		{
		$this->wpaws_sanitize($_REQUEST,'checkaws');
		}
        
        $retval = $this->validateNonce($_REQUEST['nonce']);
       if( $retval != "success")
       {
       echo $retval;
       wp_die();        
       }


	$wpaval['secretkey'] = false;
	try{
	$this->wpadbval['foundcdn'] = false;
	$wpawscdn_cert = $this->wpawscdn_auth();
	$result = $wpawscdn_cert->listDistributions();
	if(count($result['DistributionList']['Items']) > 0)
	{
	$dls = $result['DistributionList']['Items'];
		foreach($dls as $dl)
		{
			if($dl['Origins']['Items'][0]['DomainName'] == $this->wpadbval['domain'])
			{
				if($type == NULL)
				{
					echo "<div class='gscols12'>Found CDN <strong>" . esc_html($dl['DomainName']) . "</strong>, click <em>Create Distribution</em> button to update the plugin</p>";
				}
				else
				{
			echo "<div class='gscols12'><button alt='Modify CDN' title='Modify CDN' data-id=".esc_html($dl['Id'])." class='gsformbtnmini alert-danger wpawscdnedit'><span class='dashicons dashicons-edit'></span></button> <button alt='Select CDN' title='Select CDN' data-domain=". esc_html($this->wpadbval['domain']) ." data-id=".esc_html($dl['DomainName'])." class='gsformbtnmini alert-success wpawscdnactivate'><span class='dashicons dashicons-yes'></span></button> <strong>" . esc_html($dl['DomainName']) . "</strong> Modified on: " . esc_html($dl['LastModifiedTime']) . "</p>";
				
			$this->wpadbval['foundcdn'] = true;
			
			$this->wpadbval['secretkey'] = false;
			if(get_option('wpawscdndata'))
			{
				update_option('wpawscdndata', $this->wpadbval, '', $this->autoload);
			}
			else
			{
				add_option('wpawscdndata', $this->wpadbval, '', $this->autoload);
			}
			}
			wp_die();
			}
		}
		
		if($this->wpadbval['foundcdn'] == false && $this->checkcdn != true)
		{
		echo "<div class='alert alert-warning'>No distribution Found on Amazon Cloudfront for " . esc_html($this->wpadbval['domain']) . ".</div>";
		wp_die();
		}

	}
	else
	{
	if($this->checkcdn != true)
	{
	echo "<div class='alert alert-warning'>No distribution Found on Amazon Cloudfront for " . esc_html($this->wpadbval['domain']) . ".</div>";
	wp_die();
	}
	}
	}
	catch (Exception $e) {
	$err = $e->getMessage();
	$er = explode("response",$err);
	echo "<div class='alert alert-danger'>List CDN: " . esc_html($err) . "</div>";
		wp_die();
	}
	$this->switchMultisiteBlogOff();
	return;	
	wp_die();
	}
	
	public function wpaac_modify_cdn()
	{
		if ( isset($_REQUEST) )
		{
		$this->wpaws_sanitize($_REQUEST);
        
         $retval = $this->validateNonce($_REQUEST['nonce']);
       if( $retval != "success")
       {
       echo $retval;
       wp_die();        
       }
			
		$this->wpawscdn_modifyCDN();
		$this->switchMultisiteBlogOff();
		
			$this->wpadbval['secretkey'] = false;
			if(get_option('wpawscdndata'))
			{
				update_option('wpawscdndata', $this->wpadbval, '', $this->autoload);
			}
			else
			{
				add_option('wpawscdndata', $this->wpadbval, '', $this->autoload);
			}
		}
		

	wp_die();
	}

	public function wpawscdn_modifyCDN()
	{
	$wpawscdnpreset = ['Id' => $this->wpadbval['awsid'] ];

		try{
	$wpawscdn_cert = $this->wpawscdn_auth();
	$result = $wpawscdn_cert->getDistribution($wpawscdnpreset);

	$result = $result->toArray();
	
	$wpawscdn_etag = $result["ETag"];
	$result = $result["Distribution"]["DistributionConfig"];
	$wpawscdn_calleref = $result["CallerReference"];
	unset($result['ETag']);
	unset($result["DefaultCacheBehavior"]["MinTTL"]);
	unset($result["DefaultCacheBehavior"]["DefaultTTL"]);			
	unset($result["DefaultCacheBehavior"]["MaxTTL"]);
	unset($result["DefaultCacheBehavior"]["ForwardedValues"]);		
	unset($result["Origins"]["Items"]["0"]["CustomOriginConfig"]["OriginSslProtocols"]["Quantity"]);	
	unset($result["Origins"]["Items"]["0"]["CustomOriginConfig"]["OriginSslProtocols"]["Items"]);		
	$result['CallerReference'] = $wpawscdn_calleref;
	$result['Comment'] = 'Configured using AWS CDN plugin by WPAdmin';
	$result['Enabled'] = true;
	$result['WebACLId'] = "";
	$result["HttpVersion"] = "http2";
	$result['PriceClass'] = $this->wpadbval['priceclass'];
	$result["DefaultCacheBehavior"]["CachePolicyId"] = $this->wpadbval['cachepolicy'];
	$result["DefaultCacheBehavior"]["Compress"] = $this->wpadbval['compressobject'];
	$result["DefaultCacheBehavior"]["OriginRequestPolicyId"] = '59781a5b-3903-41f3-afcb-af62929ccde1';
	$result["DefaultCacheBehavior"]["ResponseHeadersPolicyId"] = 'eaab4381-ed33-4a86-88ca-d9558dc6cd63';
	$result["DefaultCacheBehavior"]["Compress"] = $this->wpadbval['compressobject'];
	$result["DefaultCacheBehavior"]["TargetOriginId"] = 'WPAdminOrigin';
	$result["DefaultCacheBehavior"]["AllowedMethods"]["Quantity"] = 3;
	$result["DefaultCacheBehavior"]["ViewerProtocolPolicy"] = 'allow-all';
	$result["DefaultCacheBehavior"]["AllowedMethods"]["Items"][0] = 'HEAD';
	$result["DefaultCacheBehavior"]["AllowedMethods"]["Items"][1] = 'GET';
	$result["DefaultCacheBehavior"]["AllowedMethods"]["Items"][2] = 'OPTIONS';
	$result["DefaultCacheBehavior"]["AllowedMethods"]["CachedMethods"]["Quantity"] = 3;
	$result["DefaultCacheBehavior"]["AllowedMethods"]["CachedMethods"]["Items"][0] = 'HEAD';
	$result["DefaultCacheBehavior"]["AllowedMethods"]["CachedMethods"]["Items"][1] = 'GET';
	$result["DefaultCacheBehavior"]["AllowedMethods"]["CachedMethods"]["Items"][2] = 'OPTIONS';
	$result["Origins"]["Quantity"] = 1;
	$result["Origins"]["Items"]["0"]["Id"] = 'WPAdminOrigin';
	$result["Origins"]["Items"]["0"]["DomainName"] = $this->wpadbval['domain'];	
		if($this->wpadbval['subfolder'] == "")
		{
		$result["Origins"]["Items"]["0"]["OriginPath"] = "";	
		}
		else
		{
		$result["Origins"]["Items"]["0"]["OriginPath"] = "/" .  $this->wpadbval['subfolder'];
		}
	$result["Origins"]["Items"][0]["CustomHeaders"]["Quantity"] = 1;
	$result["Origins"]["Items"][0]["CustomHeaders"]["Items"][0]["HeaderName"] = "Access-Control-Allow-Origin";
	$result["Origins"]["Items"][0]["CustomHeaders"]["Items"][0]["HeaderValue"] = $this->wpadbval['domain'];
	$result["Origins"]["Items"]["0"]["CustomOriginConfig"]["OriginSslProtocols"]["Quantity"] = 1;		
	$result["Origins"]["Items"]["0"]["CustomOriginConfig"]["OriginSslProtocols"]["Items"][0] = $this->wpadbval['tlsver'];
	$result['DefaultRootObject'] = '';
	$result['Logging'] = [
	'Enabled' => false,
	'Bucket' => '',
	'Prefix' => '',
	'IncludeCookies' => true,
	];
		if($this->wpadbval['custom'] == "Yes")
		{
		$wpaac_certarnx = explode("/",$this->wpadbval['certarn']);
		$wpaac_certid = "*." . $this->wpawscdnbaredomain; 
		$result["Aliases"]['Quantity'] = 1;
		$result["Aliases"]['Items'] = [$this->wpadbval['customdomain']];
		$result["ViewerCertificate"] =  [
		'ACMCertificateArn' => $this->wpadbval['certarn'],
		'SSLSupportMethod' => 'sni-only',
		'MinimumProtocolVersion' => 'TLSv1.2_2021',
		];
		}
		else
		{
		$result["ViewerCertificate"] =  [
		'CloudFrontDefaultCertificate' => true,
		'CertificateSource' => 'cloudfront',
		'MinimumProtocolVersion' => 'TLSv1.2_2021',
		];
		}
	$modresult = [
	"DistributionConfig"=>$result,
	"Id" => $this->wpadbval['awsid'],
	"IfMatch" => $wpawscdn_etag
	];

	$modresult = $wpawscdn_cert->updateDistribution($modresult);
	if($modresult['Distribution']['Status'] == "InProgress")
	{
	$awsdomainid = $modresult['Distribution']['Id'];
	$awsdomain = $modresult['Distribution']['DomainName'];
	
	if(get_option('wpawscdndomain'))
	{
		update_option('wpawscdndomain', $awsdomain, '', $this->autoload);
	}
	else
	{
		add_option('wpawscdndomain', $awsdomain, '', $this->autoload);
	}
	echo "<div class='gscols12 alert alert-success'>Successfully modified Cloudfront Distribution with ID # ". esc_html($awsdomainid) . "<br><a href=''>Reload the page</a> to continue</div>";
	}
	
	}
	catch (Exception $e) {
	$err = $e->getMessage();
	$er = explode("response",$err);
	echo "<div class='alert alert-danger'>Modify CDN: " . esc_html($err) . "</div>";
	}

	}
	
	public function wpaac_delete_certificate()
	{
		if ( isset($_REQUEST) )
		{
		$this->wpaws_sanitize($_REQUEST,'nocheck');
        
         $retval = $this->validateNonce($_REQUEST['nonce']);
       if( $retval != "success")
       {
       echo $retval;
       wp_die();        
       }
        
		$wpawscdn_cert = $this->wpawscdn_auth('ACM');	
		$params =[
		'CertificateArn' => $this->wpadbval['id']
		];
		$result = $wpawscdn_cert->deleteCertificate($params);
		
		if(get_option('wpawscdnarn'))
		{
			update_option('wpawscdnarn', 'NA', '', $this->autoload);
		}
		else
		{
			add_option('wpawscdnarn', 'NA', '', $this->autoload);
		}
				
		echo "<div class='gscols12 alert alert-warning'>SSL Certificate has been deleted</div>";
		}
	wp_die();
	}
	
	public function wpaac_list_certificate()
	{
		if ( isset($_REQUEST) )
		{
		$this->wpaws_sanitize($_REQUEST,'checkaws');
        
        $retval = $this->validateNonce($_REQUEST['nonce']);
       if( $retval != "success")
       {
       echo $retval;
       wp_die();        
       }
        
		$this->switchMultisiteBlogOn($this->wpadbval['domain']);
		$wpawscdn_cert = $this->wpawscdn_auth('ACM');	

		$result = $wpawscdn_cert->listCertificates();
		$wpawscdn_allcerts =  $result["CertificateSummaryList"];
		
		$found = false;
		foreach($wpawscdn_allcerts as $certificate)
		{
		
		$descresult = $wpawscdn_cert->describeCertificate([ "CertificateArn" => $certificate['CertificateArn'] ]);
		if($certificate['DomainName'] == "*." . $this->wpawscdnbaredomain)
		{
		$found = true;
		
		$descresult = $wpawscdn_cert->describeCertificate([ "CertificateArn" => $certificate['CertificateArn'] ]);
			if($descresult["Certificate"]["Status"] == "ISSUED")
			{
				if(get_option('wpawscdnarn'))
				{
					update_option('wpawscdnarn', $certificate['CertificateArn'], '', $this->autoload);
				}
				else
				{
					add_option('wpawscdnarn', $certificate['CertificateArn'], '', $this->autoload);
				}
			echo "<div class='alert alert-success'>SSL Certificate can now be used with Cloudfront CDN. <a href='/wp-admin/admin.php?page=wpa-aws-setup'>Proceed to Cloudfront Setup</a> </div>";	
			wp_die();
			}
		
		echo "<div class='gscols12 alert alert-warning'><button alt='Delete Certificate' title='Delete Certificate' data-id=".esc_html($certificate['CertificateArn'])." class='gsformbtnmini alert-danger wpawsssldelete'><span class='dashicons dashicons-trash'></span></button> &nbsp; &nbsp; Inactive certificate found for <strong>".esc_html($certificate['DomainName'])."</strong>. Please verify & click <strong>List Certificate</strong> again</div>";
		wp_die();
		}
		}
		
		if($found == false) echo "<div class='alert alert-warning'>No SSL certificate found for " . esc_html($this->wpawscdndomain) ." </div>";
				
		}
	$this->switchMultisiteBlogOff();
	wp_die();
	}
	
		
	public function wpaac_create_certificate()
	{
		if ( isset($_REQUEST) )
		{
		$this->wpaws_sanitize($_REQUEST,'nocheck');
		$this->switchMultisiteBlogOn($this->wpadbval['domain']);
        
         $retval = $this->validateNonce($_REQUEST['nonce']);
       if( $retval != "success")
       {
       echo $retval;
       wp_die();        
       }
        
		$wpaval['secretkey'] = false;
		$this->wpawscdn_checkcertificate();
		if($this->wpadbval['certexists'] == false)
		{
		$this->wpawscdn_requestcertificate();
		}
		
		if($this->wpadbval['certexists'] == true)
		{
		$this->wpawscdn_certificatestatus();
		}
		
		}
	$this->switchMultisiteBlogOff();
	wp_die();
	}
		
	public function wpaac_create_cdn()
	{

        
        
		if ( isset($_REQUEST) )
		{
		$this->wpaws_sanitize($_REQUEST);
        
       $retval = $this->validateNonce($_REQUEST['nonce']);
       if( $retval != "success")
       {
       echo $retval;
       wp_die();        
       }
        
        
		$this->switchMultisiteBlogOn($this->wpadbval['domain']);
			$this->wpadbval = $wpaval;
			$wpaval['secretkey'] = false;
			if(get_option('wpawscdndata'))
			{
				update_option('wpawscdndata', $wpaval, '', $this->autoload);
			}
			else
			{
				add_option('wpawscdndata', $wpaval, '', $this->autoload);
			}
			
		if($wpaval['custom'] == "Yes" && $wpaval['certarn'] == 'NA')
		{
		echo "<div class='alert alert-danger'>Cannot setup <strong>Custom CDN domain</strong><br>Please un-check <strong>I would like to use a custom CDN domain</strong> or <strong>Request a Certificate</strong> button</div>";
		wp_die();
		}
				
		}
		$this->checkcdn = true;
		$this->wpaac_list_cdn('create');
		if($this->wpadbval['foundcdn'] == false) 
		{
		$this->wpawscdn_setupCDN();
		}
		else
		{
		echo "<div class='alert alert-warning'>Distribution exists, not creating a new one</div>";
		}

	$this->switchMultisiteBlogOff();
	
	wp_die();
	}
	
	public function wpawscdn_setupCDN()
	{
	$this->switchMultisiteBlogOn($this->wpawscdndomain);
	$wpawscdnpreset = [
	"DistributionConfig" => [
	'CacheBehaviors' => ['Quantity' => 0],
	'Comment' => 'Configured using AWS CDN plugin by WPAdmin',
	'Enabled' => true,
	'CallerReference' => 'WPAdmin-' . time(),
	'DefaultCacheBehavior' => [
		'AllowedMethods' => [
			'CachedMethods' => [
			'Items' => ['GET','HEAD','OPTIONS'], 
			'Quantity' => 3,
			],
			'Items' => ['GET','HEAD','OPTIONS'], 
		'Quantity' => 3,
		],
		'CachePolicyId' => $this->wpadbval['cachepolicy'],
		'OriginRequestPolicyId' => '59781a5b-3903-41f3-afcb-af62929ccde1',
		'ResponseHeadersPolicyId' => 'eaab4381-ed33-4a86-88ca-d9558dc6cd63',
		'Compress' => $this->wpadbval['compressobject'],
		'ViewerProtocolPolicy' => 'allow-all',
		'TargetOriginId' => 'WPAdminOrigin',
		'TrustedSigners' => [
		'Enabled'  => false,
		'Quantity' => 0,
		],
	],
	'DefaultRootObject' => '',
	'Logging' => [
	'Enabled' => false,
	'Bucket' => '',
	'Prefix' => '',
	'IncludeCookies' => true,
	],
	'Origins' => [
	'Quantity' => 1,
		'Items' => [
			[
			'CustomHeaders' => [
			'Items' => [
			[
			'HeaderName' => 'Access-Control-Allow-Origin',
			'HeaderValue' => $this->wpadbval['domain'],
			],
			],
			'Quantity' => 1,
			],
			'Id' => 'WPAdminOrigin',
			'DomainName' => $this->wpadbval['domain'],
			'OriginPath' => $this->wpadbval['subfolder'],
			'CustomOriginConfig' => [
				'HTTPPort' => 80,
				'HTTPSPort' => 443,
				'OriginProtocolPolicy' => 'match-viewer',
				'OriginSslProtocols' => [
					'Quantity' => 1,
					'Items' => [$this->wpadbval['tlsver'] ],
					]
			]
			]
		]
	],
	'PriceClass' => $this->wpadbval['priceclass'],
	]
	];
	

	if($this->wpadbval['custom'] == "Yes")
	{

	$wpawscdnpreset["DistributionConfig"]["Aliases"]= [
	'Quantity' => 1,
	'Items' => [$this->wpadbval['customdomain']],
	];

	$wpaac_certarnx = explode("/",$this->wpadbval['certarn']);
	$wpaac_certid = "*." . $this->wpawscdnbaredomain; 
	
	$wpawscdnpreset["DistributionConfig"]["ViewerCertificate"] =  [
	'ACMCertificateArn' => $this->wpadbval['certarn'],
	'SSLSupportMethod' => 'sni-only',
	];
	}

	try{
	$wpawscdn_cert = $this->wpawscdn_auth();
	$result = $wpawscdn_cert->createDistribution($wpawscdnpreset);	
	$awsdomain = $result['Distribution']['DomainName'];
	$awsdomainid = $result['Distribution']['Id'];
	
	$wpconfig = get_home_path() . "wp-config.php";
	
/* add to wp-config*/
		if(is_multisite() && get_current_blog_id()  == 1 && is_super_admin() )
		{
			$this->wpaws_wpconfig('add','AWS_CDN_Domain',$awsdomain);
		}
		elseif(!is_multisite() )
		{
			$this->wpaws_wpconfig('add','AWS_CDN_Domain',$awsdomain);
		}
		
	if(get_option('wpawscdndomain'))
	{
		update_option('wpawscdndomain', $awsdomain, '', $this->autoload);
	}
	else
	{
		add_option('wpawscdndomain', $awsdomain, '', $this->autoload);
	}

	echo  "<div class='gscols12 alert alert-success'>Successfully created Cloudfront Distribution with ID # ".esc_html($awsdomainid)."<br><a href=''>Reload the page</a> to continue</div>";
	}
	catch (Exception $e) {
	$err = $e->getMessage();
	$er = explode("response",$err);
	echo "<div class='alert alert-danger'>Create CDN: " . esc_html($err) . "</div>";
	}
	
	$this->switchMultisiteBlogOff();
	wp_die();
	}
	
	public function wpawscdn_checkcertificate()
	{
	$wpawscdn_cert = $this->wpawscdn_auth('ACM');	
	$result = $wpawscdn_cert->listCertificates();
	$wpawscdn_allcerts =  $result["CertificateSummaryList"];	
	$this->wpadbval['certexists'] = false;
	$this->wpadbval['certarn'] = "NA";
		foreach($wpawscdn_allcerts as $wpawscdn_allcert)
		{
			if("*." . $this->wpawscdnbaredomain == $wpawscdn_allcert["DomainName"])
			{
			$this->wpadbval['certexists'] = true;
			$this->wpadbval['certarn'] = $wpawscdn_allcert["CertificateArn"];
			}
		}
	return;	
	}
	
	public function wpawscdn_requestcertificate()
	{
	$wpawscdn_cert = $this->wpawscdn_auth('ACM');	
	$cert_preset = [
	'DomainName' => "*." . $this->wpawscdnbaredomain,
	'ValidationMethod' => $this->wpadbval['verifymethod'],
	];
		try
		{
			$result = $wpawscdn_cert->requestCertificate($cert_preset);
			$this->wpadbval['certarn'] =  $result['CertificateArn'];
			$this->wpadbval['certexists'] =  true;
		}
		catch (Exception $e)
		{
			$err = $e->getMessage();
			return $err;
		}
	return;			
	}
	
	public function wpawscdn_certificatestatus()
	{
	$wpawscdn_cert = $this->wpawscdn_auth('ACM');	
	sleep(5);
	
	try
	{
		$descresult = $wpawscdn_cert->describeCertificate(array('CertificateArn' => $this->wpadbval['certarn']));
		
		if($descresult["Certificate"]["DomainValidationOptions"][0]["ValidationMethod"] != $this->wpadbval['verifymethod'])
		{
		echo "<div class='alert alert-warning gscols12'>SSL certificate already exists. Delete the existing certificate if you wish to change verification method.</div>";
		return;
		}
		
		
		if($descresult['Certificate']['Status'] <> "SUCCESS")
		{
		echo "<div class='alert alert-success gscols12'>SSL certificate has been issued and is curently pending verification.</div>";
			if($this->wpadbval['verifymethod'] == "DNS")
			{
			echo  "<div class='alert alert-success gscols12'>Please add a <strong>CNAME</strong> entry in the DNS with the following details:<br>Name: <strong>" . esc_html($descresult["Certificate"]["DomainValidationOptions"][0]["ResourceRecord"]["Name"]) . "</strong><br>pointing to<br>Value: <strong>" . esc_html($descresult["Certificate"]["DomainValidationOptions"][0]["ResourceRecord"]["Value"]) ."</strong><p><div class='alert alert-warning'>Once the certificate is verified, return to this page and click <strong>List Certificate</strong>.</div></p></div>";
			}
			if($this->wpadbval['verifymethod'] == "EMAIL")
			{
			echo  "<div class='alert alert-success gscols12'>An email has been sent to one of these addresses:<ul>"; 
				$emails = ['admin','administrator','hostmaster','postmaster','webmaster'];
				foreach($emails as $email)
				{
				echo "<li class=gscols3><strong>". esc_html($email) . "@Your.Domain</strong></li>";
				}
				echo "</ul><br>Please check & approve the email from Amazon.<p><div class='alert alert-warning'>Once the certificate is verified, return to this page and click <strong>List Certificate</strong>.</div></p><button id=wpawscdn_resend class='gsformbtn hidden'>Re-send Validation Email</button></div>";
			}
		}
		
		
	}
	catch (Exception $e)
	{
		$err = $e->getMessage();
		return $err;
	}
	wp_die();	
	}
	
	public function switchMultisiteBlogOn($wpawsblogurl)
	{
		if(is_multisite() && get_current_blog_id()  == 1 && is_super_admin() )
		{
		$this->wpawscdndomain = $wpawsblogurl;
		$this->wpawscdnbaredomain = str_replace("www.","",$wpawsblogurl);
		$this->wpawscdnblogid = get_blog_id_from_url($wpawsblogurl);
		
		switch_to_blog( get_blog_id_from_url($wpawsblogurl) );
		
			if(get_option('wpawscdndomain') && strpos(get_option('wpawscdndomain'),'cloudfront') )
			{
			$this->wpawscloudfront = get_option('wpawscdndomain');
			}
			
		if(get_option('wpawscdndata'))
		{
			$wpawscdndata = get_option('wpawscdndata');
			if($wpawscdndata['custom'] == "Yes" && $wpawscdndata['customdomain'] != "")
			{
			$this->wpadbval['customdomain'] = $wpawscdndata['customdomain'];
			}	
		}
	
		
		}
	}
	
	public function switchMultisiteBlogOff()
	{
	if(is_multisite()) restore_current_blog();
	}
	
	public function wpaws_wpconfig($case,$field, $awsdomain)
	{

		/*add to wp-config*/
		$wpconfig = get_home_path() . "wp-config.php";		
		$wpcontent = file_get_contents($wpconfig);
		$wpx = explode(PHP_EOL,$wpcontent);
		$newdata = "";
		
		foreach($wpx as $wp)
		{
		
		if($case == "add" || $case == "activate")
		{
				if(strpos($wp,'table_prefix') > 0 )
				{
				$newdata .= "define( '$field', '$awsdomain' );" . PHP_EOL;
				}
				if(strpos($wp,$field) > 0 )
				{
				$wp = "";
				}

				if($wp <> "") $newdata .= $wp . PHP_EOL;
		}
			
		if($case == "disable")
		{
				if(strpos($wp,$field) > 0 )
				{
				$wp = "";
				}

				if($wp <> "") $newdata .= $wp . PHP_EOL;
		}	
			
		}
		file_put_contents($wpconfig,$newdata);
		
	}
	
	public function wpaws_sanitize($REQUEST,$check = NULL)
	{
		$id = sanitize_text_field($REQUEST['id']);
		$domain = sanitize_text_field($REQUEST['domain']);
		$accessid = sanitize_text_field($REQUEST['accessid']);
		$secretkey = sanitize_text_field($REQUEST['secretkey']);
		$cachepolicy = sanitize_text_field($REQUEST['cachepolicy']);
		$tlsver = sanitize_text_field($REQUEST['tlsver']);
		$compressobject = sanitize_text_field($REQUEST['compressobject']);
		$priceclass = sanitize_text_field($REQUEST['priceclass']);
		$cdndomain = sanitize_text_field($REQUEST['cdndomain']);
		$subfolder = sanitize_text_field($REQUEST['subfolder']);
		$awsid = sanitize_text_field($REQUEST['awsid']);
		$certarn = sanitize_text_field($REQUEST['certarn']);
		$custom = sanitize_text_field($REQUEST['custom']);
		$customdomain = sanitize_text_field($REQUEST['customdomain']);	
		$pages = sanitize_text_field($REQUEST['pages']);
		$verifymethod = sanitize_text_field($REQUEST['verifymethod']);
		$terms = wp_kses($REQUEST['terms'],"\n");
		$compressobject = ($compressobject == "true" ? true : false);
		$this->switchMultisiteBlogOn($domain);
		
		if($check == NULL)
		{
		if($domain != $this->wpawscdndomain)
		{
		echo "<div class='alert alert-danger'>Domain name does not match the actual domain</div>";
		wp_die();
		}
			
		if(($accessid == "" || $secretkey == ""))
		{
		echo "<div class='alert alert-warning'>Please provide a valid Access ID / Secret Key</div>";
		wp_die();
		}
			
	
		if($wpaval['custom'] == "Yes" && $wpaval['certarn'] == 'NA')
		{
		echo "<div class='alert alert-danger'>Cannot setup <strong>Custom CDN domain</strong><br>Please un-check <strong>I would like to use a custom CDN domain</strong> or <strong>Request a Certificate</strong> button</div>";
		wp_die();
		}
		}

		if($check == "checkaws")
		{
		if($domain != $this->wpawscdndomain)
		{
		echo "<div class='alert alert-danger'>Domain name does not match the actual domain</div>";
		wp_die();
		}
			
		if(($accessid == "" || $secretkey == ""))
		{
		echo "<div class='alert alert-warning'>Please provide a valid Access ID / Secret Key</div>";
		wp_die();
		}	
		}
		
		$wpaval = [
		'id' => $id,	
		'domain' => $domain,
		'accessid' => $accessid,
		'secretkey' => $secretkey,
		'cachepolicy' => $cachepolicy,
		'tlsver' => $tlsver,	
		'compressobject' => $compressobject,
		'priceclass' => $priceclass,
		'cdndomain' => $cdndomain,	
		'subfolder' => $subfolder,
		'awsid' => $awsid,
		'certarn' => $certarn,
		'custom' => $custom,
		'customdomain' => $customdomain	,
		'pages' => $pages,
		'verifymethod' => $verifymethod,
		'terms' => $terms	
		];		
		
	$this->wpadbval = $wpaval;
	}
    
    public function validateNonce($form_nonce)
    {
        if(!$form_nonce)
        {
        return "<div class='alert alert-danger'>Nonce not found</div>";
        }
       
        if( !check_ajax_referer(get_current_user(), 'nonce')  )
        {
        return "<div class='alert alert-danger'>Nonce Check Failed</div>";
        }
return "success";
}

}

$wpaawscdnadmin = new wpaawscdnadmin;
$wpaawscdnadmin->load();

add_action( 'wp_ajax_wpaac_create_certificate', array($wpaawscdnadmin,'wpaac_create_certificate') );
add_action( 'wp_ajax_wpaac_list_certificate', array($wpaawscdnadmin,'wpaac_list_certificate') );
add_action( 'wp_ajax_wpaac_delete_certificate', array($wpaawscdnadmin,'wpaac_delete_certificate') );
add_action( 'wp_ajax_wpaac_get_certificate', array($wpaawscdnadmin,'wpaac_get_certificate') );
add_action( 'wp_ajax_wpaac_create_cdn', array($wpaawscdnadmin,'wpaac_create_cdn') );
add_action( 'wp_ajax_wpaac_list_cdn', array($wpaawscdnadmin,'wpaac_list_cdn') );
add_action( 'wp_ajax_wpaac_modify_cdn', array($wpaawscdnadmin,'wpaac_modify_cdn') );
add_action( 'wp_ajax_wpaac_reactivate_cdn', array($wpaawscdnadmin,'wpaac_reactivate_cdn') );
add_action( 'wp_ajax_wpaac_activate_fsdomain', array($wpaawscdnadmin,'wpaac_activate_fsdomain') );
add_action( 'wp_ajax_wpaac_activate_csdomain', array($wpaawscdnadmin,'wpaac_activate_csdomain') );
add_action( 'wp_ajax_wpaac_disable_fsdomain', array($wpaawscdnadmin,'wpaac_disable_fsdomain') );
add_action( 'wp_ajax_wpaac_reset_fsdomain', array($wpaawscdnadmin,'wpaac_reset_fsdomain') );
add_action( 'wp_ajax_wpaac_add_exclusion', array($wpaawscdnadmin,'wpaac_add_exclusion') );
add_action( 'wp_ajax_wpaac_add_pageexclusion', array($wpaawscdnadmin,'wpaac_add_pageexclusion') );
add_action( 'wp_ajax_wpaac_add_donation', array($wpaawscdnadmin,'wpaac_add_donation') );
