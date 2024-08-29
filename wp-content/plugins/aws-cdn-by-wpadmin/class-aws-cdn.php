<?php
register_activation_hook( __FILE__, ['wpawscdn','wpaawscdn_activate'] );
register_deactivation_hook( __FILE__, ['wpawscdn','wpaawscdn_deactivate'] );

add_action( 'plugins_loaded', ['wpawscdn','init'] );

class wpawscdn
{

protected static $instance;

public function __construct()
{
if ( ! defined( 'wpawscdnbasedir' ) ) define( 'wpawscdnbasedir', plugin_dir_path( __FILE__ ) );
if ( ! defined( 'wpawscdnurl' ) ) define( 'wpawscdnurl', plugin_dir_url( __FILE__ ) );
if ( ! defined( 'wpawscdnver' ) ) define( 'wpawscdnver', '3.0.1');
if ( ! defined( 'wpawscdnname' ) ) define( 'wpawscdnname', 'wpadmin-aws-cdn');
if ( ! defined( 'wpawscdnfilename' ) ) define( 'wpawscdnfilename', 'wpadmin-aws-cdn.php');
if ( ! defined( 'wpawscdnbasename' ) ) define( 'wpawscdnbasename', 'wpadmin-aws-cdn/wpadmin-aws-cdn.php');
if ( ! defined( 'oldwpawscdnbasename' ) ) define( 'oldwpawscdnbasename', 'aws-cdn-by-wpadmin/wpadmin-aws-cdn.php');		

add_action( 'plugins_loaded', ['wpawscdn', 'init'] );

add_action('wp_footer', 'wpawscdn_comment_in_footer');	
function wpawscdn_comment_in_footer()
{
echo "<!--Amazon AWS CDN Plugin. Powered by WPAdmin.ca " . wpawscdnver ."-->";
}

function wpawscdn_add_settings_link( $links )
{
$donate_link = '<a target=_BLANK href="https://wpadmin.ca/free-wordpress-plugin-amazon-cloudfront-cdn/">' . __( '<b><i>Paid Support</i></b>' ) . '</a>';
array_unshift( $links, $donate_link );
$forum_link = '<a target=_BLANK href="https://wordpress.org/support/plugin/wpadmin-aws-cdn/">' . __( 'Support' ) . '</a>';
array_unshift( $links, $forum_link );
$review_link = '<a target=_BLANK href="https://wordpress.org/support/plugin/wpadmin-aws-cdn/reviews/#new-post">' . __( 'Review' ) . '</a>';
array_unshift( $links, $review_link );
$settings_link = '<a href="admin.php?page=wpa-aws-setup">' . __( 'Setup' ) . '</a>';
array_unshift( $links, $settings_link );
return $links;
}
add_filter( "plugin_action_links_" . oldwpawscdnbasename, 'wpawscdn_add_settings_link' );
}

public static function init()
{
}

public function load()
{
require wpawscdnbasedir. 'admin/class-aws-cdn-admin.php';
}

public static function wpaawscdn_activate()
{
add_option('wpawscdndata', 'NA', '', $this->autoload);
add_option('wpawscdndomain', 'NA', '', $this->autoload);
add_option('WPAdmin_CDN_Ignorelist', 'NA', '', $this->autoload);
add_option('WPAdmin_CDN_Ignorepages', 'NA', '', $this->autoload);
}

public static function wpaawscdn_deactivate()
{
delete_option('wpawscdndata');
delete_option('wpawscdndomain');
}

public function wpaws_cdn_prefetch()
{
if(get_option('wpawscdnisactive') && (stristr(get_option('wpawscdnisactive'),'cloudfront') || stristr(get_option('wpawscdnisactive'), str_replace("www.","",$_SERVER['HTTP_HOST']) )) )
{
$cdndomain = get_option('wpawscdnisactive');
echo "<link rel='dns-prefetch' href='//".esc_html($cdndomain)."' />";
}
}

public function check_ignored_pages()
{
	if(get_option('WPAdmin_CDN_Ignorepages'))
	{
	$pages = get_option('WPAdmin_CDN_Ignorepages');
	$currenturix = explode("/",$_SERVER['REQUEST_URI']);
	$currenturix = array_filter($currenturix);

		if( count($currenturix) > 0)
		{
		$currenturi = $currenturix[count($currenturix)];
		}
		else
		{
		$currenturi = "";
		}
		
		if( !empty($currenturi) && stristr($pages,$currenturi) !== false) return true;

		/*if currenturi is blank check if homepage*/
		if($currenturi == "")
		{
		$wpawscdn_homepageid = get_option( 'page_on_front' );
		$wpawscdn_homepageslug = get_post_field('post_name',$wpawscdn_homepageid);
		if(stristr($pages,$wpawscdn_homepageslug)) return true;
		}
	}
}

public function wpaws_cdn_stylescripts($content)
{
	/*check wp-config file for details*/
	$wparoot = ABSPATH;
    /*str_replace("wp-content/plugins/aws-cdn-by-wpadmin","", (__DIR__) );*/
    
$wpconfig = $wparoot . "wp-config.php";
include_once($wpconfig);
	if ( ( get_option('wpawscdnisactive') == NULL || get_option('wpawscdnisactive') == "NA" ) && !defined('AWS_CDN_Isactive') )
	{
	return $content;
	}

	if($this->check_ignored_pages() == true) return $content;

	$cdndomain = get_option('wpawscdnisactive');
	if($cdndomain == NULL && defined('AWS_CDN_Isactive')) $cdndomain = AWS_CDN_Isactive;
	$domain = $_SERVER['HTTP_HOST'];	
	$serverproto = "http";
	if(@$_SERVER['HTTPS'] == "on") $serverproto = "https";
	$localdomain = $serverproto . "://" . $domain;	
	$cdndomain = $serverproto . "://" . $cdndomain;
		if(substr($domain,0,3) == "www")
		{
		$wwwdomain = $serverproto . "://" . str_replace("www.","",$domain);
		}
		else
		{
		$wwwdomain = $serverproto ."://www." . $domain;
		}
	$nohttpdomain = "//" . $domain;


	/*check for keyword exclusion*/
	$found = 0;
	if(get_option('WPAdmin_CDN_Ignorelist'))
	{
	$exclusionlist = get_option('WPAdmin_CDN_Ignorelist');
	$exlist = explode(",",$exclusionlist);
	$exlist = array_filter($exlist);
		foreach($exlist as $exl)
		{
			if(stristr($content,$exl))
			{
			$found = 1; 
			break;
			}
		}
	}
		if($found == 0)
		{		
		$content = str_replace($localdomain,$cdndomain,$content);
		$content = str_replace($wwwdomain,$cdndomain,$content);
		$content = str_replace($nohttpdomain,$cdndomain,$content);
		}
	return $content;
}

public function wpaws_cdn_content($content)
{
	/*check wp-config file for details*/
	$wparoot = ABSPATH;
    /*str_replace("wp-content/plugins/aws-cdn-by-wpadmin","", (__DIR__) );*/
    
$wpconfig = $wparoot . "wp-config.php";
include_once($wpconfig);
	if ( (get_option('wpawscdnisactive') == NULL || get_option('wpawscdnisactive') == "NA") &&  !defined('AWS_CDN_Isactive') )
	{
	return $content;
	}

	if($this->check_ignored_pages() == true) return $content;

if( defined('AWS_CDN_Isactive')  || ( get_option('wpawscdnisactive') && stristr(get_option('wpawscdnisactive'),'cloudfront') || stristr(get_option('wpawscdnisactive'), str_replace("www.","",$_SERVER['HTTP_HOST']) )) )
{
$cdndomain = get_option('wpawscdnisactive');
if($cdndomain == NULL && defined('AWS_CDN_Isactive')) $cdndomain = AWS_CDN_Isactive;	
$domain = $_SERVER['HTTP_HOST'];	
$serverproto = "http";
if(@$_SERVER['HTTPS'] == "on") $serverproto = "https";
$localdomain = $serverproto . "://" . $domain;	
if(substr($domain,0,3) == "www")
{
$wwwdomain = $serverproto . "://" . str_replace("www.","",$domain);
}
else
{
$wwwdomain = $serverproto ."://www." . $domain;
}
$nohttpdomain = "//" . $domain;
$cdndomain = $serverproto . "://" . $cdndomain;	
	if(gettype($content) == "array")
	{
	if(array_key_exists('src',$content)) $content['src'] = strip_tags(str_replace($localdomain,$cdndomain,$content['src']));
	if(array_key_exists('data-src',$content)) $content['data-src'] = strip_tags(str_replace($localdomain,$cdndomain,$content['data-src']));	
	if(array_key_exists('srcset',$content)) $content['srcset'] = strip_tags(str_replace($localdomain,$cdndomain,$content['srcset']));
	if(array_key_exists('data-permalink',$content)) $content['data-permalink'] = strip_tags($content['data-permalink']);	
	if(array_key_exists('data-orig-file',$content)) $content['data-orig-file'] = strip_tags($content['data-orig-file']);	
	}
	elseif (filter_var($content, FILTER_VALIDATE_URL) ) 
	{
	$content = str_replace($localdomain,$cdndomain,$content);
	$content = str_replace($wwwdomain,$cdndomain,$content);
	$content = str_replace($nohttpdomain,$cdndomain,$content);
	}
	elseif(substr($content,0,4) =="<img")
	{
	$content = str_replace($localdomain,$cdndomain,$content);
	$content = str_replace($wwwdomain,$cdndomain,$content);
	$content = str_replace($nohttpdomain,$cdndomain,$content);
	}
	else
	{
	preg_match_all('/<img[^>]*>/', $content, $extracted);
	$cdnextract = NULL;
		if(is_array($extracted))
		{
			foreach ($extracted as $key => $extract)
			{
			if(count($extract) >0) 
				{
					foreach ($extract as $key => $imgs)
					{
						if(substr($imgs,0,4) == "<img")
						{
						if(stripos($imgs,'"'.$nohttpdomain)) $cdnextract = str_replace($nohttpdomain,$cdndomain,$imgs);
						if(stripos($imgs,$localdomain)) $cdnextract = str_replace($localdomain,$cdndomain,$imgs);
						if(stripos($imgs,$wwwdomain)) $cdnextract = str_replace($wwwdomain,$cdndomain,$imgs);

						if($cdnextract) $content = str_replace($imgs,$cdnextract,$content);

						}
					}
				}
			}
		}
	}
}
return $content;
}

}

$wpawscdn = new wpawscdn;
$wpawscdn->load();

add_action('wp_head',array($wpawscdn,'wpaws_cdn_prefetch'),-1);

if( !is_admin() ){
add_filter( 'style_loader_src', array($wpawscdn,'wpaws_cdn_stylescripts') ,PHP_INT_MAX,1);
add_filter( 'script_loader_src', array($wpawscdn,'wpaws_cdn_stylescripts') ,PHP_INT_MAX);
add_filter('the_content',array($wpawscdn,'wpaws_cdn_content'),PHP_INT_MAX);
add_filter( 'wp_get_attachment_thumb_url', array($wpawscdn,'wpaws_cdn_content'),PHP_INT_MAX);
add_filter( 'wp_get_attachment_url', array($wpawscdn,'wpaws_cdn_content'),PHP_INT_MAX);
add_filter( 'wp_get_attachment_link', array($wpawscdn,'wpaws_cdn_content'),PHP_INT_MAX);
add_filter( 'wp_get_attachment_image', array($wpawscdn,'wpaws_cdn_content'),PHP_INT_MAX);
add_filter( 'wp_get_attachment_image_attributes', array($wpawscdn,'wpaws_cdn_content'),PHP_INT_MAX);
add_filter( 'wp_get_attachment_image_srcset', array($wpawscdn,'wpaws_cdn_content'),PHP_INT_MAX);
add_filter( 'wp_get_attachment_thumb_file', array($wpawscdn,'wpaws_cdn_content'),PHP_INT_MAX);
add_filter( 'post_thumbnail_html', array($wpawscdn,'wpaws_cdn_content') ,PHP_INT_MAX);
add_filter( 'woocommerce_product_get_image', array($wpawscdn,'wpaws_cdn_content'),PHP_INT_MAX);	
add_filter( 'woocommerce_cart_item_thumbnail', array($wpawscdn,'wpaws_cdn_content'),PHP_INT_MAX);	
add_filter( 'woocommerce_order_item_thumbnail', array($wpawscdn,'wpaws_cdn_content'),PHP_INT_MAX);	
add_filter( 'woocommerce_product_thumbnails', array($wpawscdn,'wpaws_cdn_content'),PHP_INT_MAX);	
add_filter( 'woocommerce_product_thumbnails_columns', array($wpawscdn,'wpaws_cdn_content'),PHP_INT_MAX);		
add_filter( 'woocommerce_single_product_image_thumbnail_html', array($wpawscdn,'wpaws_cdn_content'),PHP_INT_MAX);	
add_filter('render_block',array($wpawscdn,'wpaws_cdn_content'),PHP_INT_MAX);
add_filter( 'widget_text', array($wpawscdn,'wpaws_cdn_content'),PHP_INT_MAX);
add_filter( 'post_gallery', array($wpawscdn,'wpaws_cdn_content'),PHP_INT_MAX);
add_filter( 'wp_calculate_image_srcset', array($wpawscdn,'wpaws_cdn_content'),PHP_INT_MAX);
add_filter( 'elementor/frontend/the_content', array($wpawscdn,'wpaws_cdn_content'),PHP_INT_MAX);
}