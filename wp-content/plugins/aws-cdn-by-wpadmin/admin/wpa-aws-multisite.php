<h1>Multi-site Setup</h1>
<?php
if(get_current_blog_id()  == 1 && is_super_admin())
{
require_once(wpawscdnbasedir . "admin/wpa-aws-deploy-multi.php");
}
else
{
echo "<div class='notice notice-error is-dismissible'><h2>Error!</h2><P>Only a <i>Super Admin</i> can access this page on the <i>Primary</i> site.</p></div>";	
}
?>
