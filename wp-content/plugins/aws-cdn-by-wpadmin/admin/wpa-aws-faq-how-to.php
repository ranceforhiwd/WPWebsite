<h1>FAQ & How To</h1>

<div class=gscontainer-fluid>
<div class=gsrow>
<div class=gscols6>
<h3>Howto</h3>
<h4>Setup CloudFront</h4>
<ol>
<li>Setup your AWS Account @ <a href='http://aws.amazon.com/' target=_BLANK>aws.amazon.com</a></li>
<li>Refer to <a href='https://wpadmin.ca/how-to-create-an-aws-user-with-limited-permissions-to-access-cloudfront-only/' target=_BLANK>this article</a> to setup the correct permissions</li>
<li>Get the <i>Access Key ID</i> & <i>Secret Key</i></li>
<li>Enter the <i>Access Key ID</i> & <i>Secret Key</i> in the respective input boxes</li>
<li>The domain name is automatically added (you may not need to change it)</li>
<li>Select the <u>Price Class</u> (AWS charges may vary depending on your selection)</li>
<li>Click the <u>Create Distribution</u> button</li>
<li>Wait for AWS to setup the Distribtuion (usually about 5 minutes)</li>
<li>Click the  <i>Activate rAnd0mChA6s.cloudfront.net</i> button to activate CDN</li>
</ol>
<h4>Disable Cloudfront Temporarily</h4>
<ol>
<li>If the CDN is active, click the <u>Disable CDN</u> button</li>
<li>Clear cache if you are using any caching plugin</li>
</ol>
<h4>Re-enable Cloudfront</h4>
<ol>
<li>Click the <u>Activate rAnd0mChA6s.cloudfront.net</u> OR <u>Activate cdn.<?php echo esc_html($this->wpawscdnbaredomain); ?></u> button</li>
<li>Clear cache if you are using any caching plugin</li>
</ol>
<h4>Modify Distribution</h4>
<ol>
<li>On the <a href='/wp-admin/admin.php?page=wpa-aws-setup'> AWS CDN Setup</a> page:</li>
<li>If the CDN is active, click the <u>Disable CDN</u> button</li>
<li>Click the <u>Reset CDN</u> button</li>
<li>Reload the page</li>
<li>Enter the <em>Secret Key</em> and click <strong>List Distribution</strong> button</li>
<li>Click the <strong>Modify CDN</strong> icon</li>
<li>Update the details in the form</li>
<li>Click <strong>Modify Distribution</strong> button</li>
</ol>
<h4>Delete Cloudfront Setup</h4>
<ol>
<li>If the CDN is active, click the <u>Disable CDN</u> button</li>
<li>Click the <u>Reset CDN</u> button</li>
</ol>
<h4>Exclusions</h4>
<ol>
<li>Disable CDN on specific pages</li>
<li>Disbale CDN by specific words.</li>
</ol>
</div>

<div name=faq class="gscols6">
<h3>FAQ</h3>
<p>
<dl>
<dt>Does the plugin support variables in wp-config.php?</dt>
<dd>Since version 2.0.11, the plugin supports variables defined in wp-config.php. You can add the Access ID and Secret Key in wp-config.php as under
<pre>
define( 'AWS_Access_ID', 'AWSAccessID' );
define( 'AWS_Secret_Key', 'AWSSecretKey' );
</pre></dd>	
<dt>Custom CDN button grayed out after update</dt>
<dd>
The button is grayed out because the plugin does not have the custom domain name. Here are the steps to re-enable the custom CDN button 
<p>Visit <a href='/wp-admin/admin.php?page=wpa-aws-ssl'>SSL Manager</a></p>
<p>Enter Access ID and Secret Key</p>
<p>Click <strong>List Certificate</strong></p>
If an active certificate is found, you should see message <strong>SSL Certificate can now be used with Cloudfront CDN. Proceed to Cloudfront Setup</strong>
<p>Return to the <a href='/wp-admin/admin.php?page=wpa-aws-setup'> AWS CDN Setup</a> page</p>
<p>If the CDN is active, click <strong>Disable CDN</strong> button</p>
<p>Click <strong>Reset CDN</strong> button</p>
<p>Reload the Page</p>
You will be back at the initial setup page.
<p>Enter the missing details in the form</p>
<p>Select <strong>I would like to use a custom CDN domain.</strong> The certificate ARN field should be visible and have the ARN details listed</p>
<p>Click <strong>Create Distribution</strong>. The existing distribution details will be added to the website, no new distribution will be created.</p>
You should now be able to use custom domain with the Cloudfront CDN
</dd>
<dt>How does the plugin work?</dt>
<dd>The plugin replaces the domain name on all static assets (images, scripts, stylesheets,etc) in the wp-content & wp-includes folder.</dd>
<dt>Does this plugin support WordPress Multisite Setup?</dt>
<dd>Yes, it does. If you have setup the multisite correctly and the <b>Domain Name:</b> field in STEP 1 shows a FQDN (Fully Qualified Domain Name), the plugin should work just fine.</dd>
<dt>Where are the AWS Access Key ID and Secret Key Stored?</dt>
<dd>Only the AWS Access Key ID is stored in the database. You will need to provide the Secret Key every time.</dd>
<dt>What does the <u>Reset CDN</u> button do?</dt>
<dd>When you click the Reset CDN button, the reference to the cloudfront domain is removed from the website.</dd>
<dt>Can I use any other CDN?</dt>
<dd>No, you cannot. We have made the plugin exclusively for use with AWS</dd>
<dt> What content is moved to AWS CDN</dt>
<dd> All Static files and images are moved to AWS CDN. There have been cases where some contents failed, please send me an email to report such issues</dd>
<dt> Can I  edit what goes and what does not?</dt>
<dd> Unfortunately, the plugin does not support granular control over contents that can be moved to AWS CDN; howver, you can use wildcard to control to an extent</dd>
<dt>Is there a way to flush the CDN</dt>
<dd>Amazon refers this to '<b>Invalidation</b>' and charges for any invalidation requests. The easiest way is to rename the file or add a version tag</dd>
<dt>What if I have a few Questions?</dt>
<dd>Request help on  <a href='https://wordpress.org/support/plugin/aws-cdn-by-wpadmin/' target=_BLANK>the WordPress support forum</a>. Premium support is also available for a fee.</dd>
<dt>I don't get a response while trying to setup CDN</dt>
<dd>The plugin needs <em>php-xml</em> to process requests. This module is enabled by most hosting serivce providers. If you are using your own cloud server, please ensure the module is enabled on your server. </dd>
<dt>I want to buy you a coffee?</dt>
We are thankful to our generous donors who in the spirit of supporting future development of this plugin wants to <a href='https://wpadmin.ca/donation/' target=_BLANK>buy us a coffee</a></dd>
</dl>
</p>
</div>
</div>
</div>
