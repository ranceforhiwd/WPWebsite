<?php
if(get_option('wpawscdndata'))
{
$wpawscdndata = get_option('wpawscdndata');
$custom = "";	
if(array_key_exists('custom',$wpawscdndata) ) $custom = $wpawscdndata['custom'];
if(array_key_exists('customdomain',$wpawscdndata) ) $customdomain = $wpawscdndata['customdomain'];
}

if(get_option('wpawscdnisactive') && (strpos(get_option('wpawscdnisactive'),'cloudfront') || strpos(get_option('wpawscdnisactive'), $this->wpawscdnbaredomain)) )
{
$wpawscdnactive = "CDN " . get_option('wpawscdnisactive') . " is Currently Active";
$class = 'roactive';
}
else
{
$wpawscdnactive = "CDN is Currently Inactive";
$class = 'roinactive';
}
?>
<h1>Configure Amazon Cloudfront CDN</h1>
<div class='gscontainer'>
<div class='gsrow'>

<div class='gscols12'>
<div id='wpawscdnresult'></div>
</div>


<div class='gscols12'>
<p>
<?php
global $wp;  
$current_url = home_url(add_query_arg(array($_GET), $wp->request));
?>
<input type=hidden id=wpawscdnnonce value="<?php echo wp_create_nonce(get_current_user());?>" class=gsforminput>
<input type=hidden id=wpawscdnreferrer value="<?php echo $current_url;?>" class=gsforminput>
<input type=text class='gsforminput <?php echo esc_textarea($class); ?>' value='<?php echo esc_textarea($wpawscdnactive); ?>'>
</p>
</div>

<div class='gscols6'>
<input id=wpawsactivatecfd type='Button' Value='Activate <?php echo esc_textarea($this->wpawscloudfront);?>' class='gsformbtn'>
</div>

<?php
if($custom == 'Yes')
{
echo "<div class='gscols6'>
<input id=wpawsactivatecsd  type='Button' data-domain=".esc_textarea($customdomain)." Value='Activate ".esc_textarea($customdomain )."' class='gsformbtn'>
</div>";
}
else
{
echo "<div class='gscols6'>
<input id=wpawsactivatecsd type='Button' Value='Activate ".esc_textarea($customdomain)."' DISABLED class='gsformbtndefault'>
</div>";
}
?>

<div class='gscols6'>
<input id=wpawsdisablecdn  type='Button' Value='Disable CDN' class='gsformbtn alert-warning'>
</div>

<div class='gscols6'>
<input id=wpawsresetcdn  type='Button' Value='Reset CDN' class='gsformbtn alert-danger'>
</div>

</div>


<div class=gsrow>
<div class='gscols12'>
<h2>Exclusions</h2>
</div>
<div class='gscols6'>
<h4>Exclude Pages</h4>
<div class='gsrow'>
<div class='gscols6'>
<?php
$pageexclusionlist = "";
if(get_option('WPAdmin_CDN_Ignorepages') && get_option('WPAdmin_CDN_Ignorepages') != "")
{
$pageexclusionlist = get_option('WPAdmin_CDN_Ignorepages');
}
?>

Existing Pages
<select id=wpawscdnallpages class=gsformlist size=15>
<?php
$defaults = array(
'post_type' => 'page',
'posts_per_page' => -1,
'orderby' => 'post_title',
'order' => 'ASC',
);
$query = new WP_Query;
$allposts = ($query->query( $defaults ));
foreach($allposts as $allposts)
{
$postname = $allposts->post_name;
if( !strstr($pageexclusionlist,$postname) ) echo "<option value='".esc_textarea($postname)."'>".esc_textarea($postname)."</option>";
}
?>
</select>
</div>
<div class='gscols6'>
Excluded Pages
<?php
$pageexclusionlist = explode(",",$pageexclusionlist);
$pageexclusionlist = array_filter($pageexclusionlist);
echo "<select id=wpawscdnexclusionpages class=gsformlist size=15>";
foreach($pageexclusionlist as $post)
{
if($post != "NA") echo "<option value='".esc_textarea($post)."'>".esc_textarea($post)."</option>";
}
?>
</select>
<input type=button id=wpawscdnpageexclusionbtn class='gsformbtn' value='Update Pages List'>
</div>
</div>

</div>

<div class='gscols6'>
<h4>Wildcard</h4>
Exclude if the link contains the follow words (1 on each line)
<?php
$exclusionlist = "";
if(get_option('WPAdmin_CDN_Ignorelist'))
{
$exclusionlist = get_option('WPAdmin_CDN_Ignorelist');
$exclusionlist = str_replace(" ","",$exclusionlist);
$exclusionlist = str_replace("NA","",$exclusionlist);
$exclusionlist = str_replace(",","\r\n",$exclusionlist);
if(is_array($exclusionlist)) $exclusionlist = array_filter($exclusionlist);
}
?>
<textarea id=wpawscdnwildcardexclusion class='gsforminput'><?php echo esc_textarea($exclusionlist); ?></textarea>
<input type=button id=wpawscdnwildcardexclusionbtn class='gsformbtn' value='Update Wildcard Exclusion'>
</div>

</div>

</div>
