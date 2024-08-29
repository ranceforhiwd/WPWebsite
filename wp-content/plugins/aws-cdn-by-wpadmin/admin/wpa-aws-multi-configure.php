<?php
if(get_current_blog_id()  != 1 && !is_super_admin())
{
echo "<div class='notice notice-error is-dismissible'><h2>Error!</h2><P>Only a <i>Super Admin</i> can access this page on the <i>Primary</i> site.</p></div>";
exit;
}
/*switch to the blog*/
switch_to_blog( get_blog_id_from_url($domain) );

$this->wpawscdndomain = "cdn." . str_replace("www.","",$domain);

if(get_option('wpawscdndata'))
{
$wpawscdndata = get_option('wpawscdndata');
$custom = $wpawscdndata['custom'];
}

if(get_option('wpawscdndomain'))
{
$this->wpawscloudfront = get_option('wpawscdndomain');
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
<div class='gscontainer'>
<div class='gsrow'>

<?php
if($this->wpawscloudfront == "NA")
{
echo "<div class='gscols12 alert alert-danger gstextcenter'>Cloudfront CDN not found for <strong>".esc_html($domain)."</strong><br><a href='/wp-admin/admin.php?page=wpa-aws-multisite'>Return to Multisite Setup</a></div>";
exit;
}
?>

<div class='gscols12'>
<div id='wpawscdnresult'></div>
</div>


<div class='gscols12'>
<p>

<input type=text class='gsforminput <?php echo esc_textarea($class); ?>' value='<?php echo $wpawscdnactive; ?>'>
</p>
</div>

<div class='gscols6'>
<input id=wpawsactivatemcfd type='Button' data-domain=<?php echo esc_textarea($domain);?> Value='Activate <?php echo esc_textarea($this->wpawscloudfront);?>' class='gsformbtn'>
</div>

<?php
if($custom == 'Yes')
{
echo "<div class='gscols6'>
<input id=wpawsactivatecsd  type='Button' Value='Activate " . esc_textarea($this->wpawscdndomain) . "' class='gsformbtn'>
</div>";
}
else
{
echo "<div class='gscols6'>
<input id=wpawsactivatecsd type='Button' Value='Activate ". esc_textarea($this->wpawscdndomain) . "' DISABLED class='gsformbtndefault'>
</div>";
}
?>

<div class='gscols6'>
<input id=wpawsdisablemcdn  data-domain=<?php echo esc_textarea($domain);?> type='Button' Value='Disable CDN' class='gsformbtn alert-warning'>
</div>

<div class='gscols6'>
<input id=wpawsresetmcdn  data-domain=<?php echo esc_textarea($domain);?> type='Button' Value='Reset CDN' class='gsformbtn alert-danger'>
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
if( !strstr($pageexclusionlist,$postname) ) echo "<option value='".esc_textarea($postname)."'>".esc_textarea($postname) ."</option>";
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
<input type=button id=wpawscdnpageexclusionbtn class='gsformbtn' data-id=<?php echo esc_textarea($domain); ?>  value='Update Pages List'>
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
<input type=button id=wpawscdnwildcardexclusionbtn class='gsformbtn' data-id=<?php echo esc_textarea($domain); ?>  value='Update Wildcard Exclusion'>
</div>

</div>

</div>
