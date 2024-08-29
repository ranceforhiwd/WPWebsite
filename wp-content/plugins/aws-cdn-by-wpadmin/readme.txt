=== WPAdmin AWS CDN ===
Contributors: luckychingi
Tags: CDN, Free, Amazon, AWS, Cloudfront, Multisite
Donate link: https://wpadmin.ca/donation/
Requires at least: 4.4.2
Tested up to:  6.4.2
Requires PHP: 7.0
Stable tag: 3.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The new & improved Amazon Cloudfront Distribution Plugin by WPAdmin. Setup Amazon Cloudfront CDN for your website. Now with intuitive layout and more flexibility.


== Description ==
The new & improved Amazon Cloudfront Distribution Plugin by WPAdmin. Setup Amazon Cloudfront CDN for your website. Now with intuitive layout and more flexibility. Also supports WordPress Multisite installation.


== Installation ==
= Using the WordPress Plugin Search =



1. Navigate to the `Add New` sub-page under the Plugins admin page.

2. Search for `WPAdmin AWS CDN`.

3. The plugin should be listed first in the search results.

4. Click the `Install Now` link.

5. Lastly click the `Activate Plugin` link to activate the plugin.



= Uploading in WordPress Admin =



1. [Download the plugin zip file](https://downloads.wordpress.org/plugin/aws-cdn-by-wpadmin.3.0.1.zip) and save it to your computer.

2. Navigate to the `Add New` sub-page under the Plugins admin page.

3. Click the `Upload` link.

4. Select `wpadmin-aws-cdn` zip file from where you saved the zip file on your computer.

5. Click the `Install Now` button.

6. Lastly click the `Activate Plugin` link to activate the plugin.



= Using FTP =



1. [Download the plugin zip file](https://downloads.wordpress.org/plugin/aws-cdn-by-wpadmin.3.0.1.zip) and save it to your computer.

2. Extract the `wpadmin-aws-cdn` zip file.

3. Create a new directory named `wpadmin-aws-cdn` directory in the `../wp-content/plugins/` directory.

4. Upload the files from the folder extracted in Step 2.

4. Activate the plugin on the Plugins admin page.

== Frequently Asked Questions ==
 = CORS Error: No Access-Control-Allow-Origin header is present on the requested resource =
<h3>Apache</h3>
Add the following in your .htaccess file, immediately under '# END WordPress'
<code>
<FilesMatch "\.(ttf|ttc|otf|eot|woff|woff2|font.css)$">
<IfModule mod_headers.c>
Header add Access-Control-Allow-Origin "*"
Header set Access-Control-Allow-Origin "*"
</IfModule>
</FilesMatch>
</code>
<h3>Nginx</h3>
Add something like this to your vhost config
<code>
location ~* \.(eot|otf|ttf|woff|woff2)$ {
    add_header Access-Control-Allow-Origin *;
}
</code>
Refer to this article for more info: https://github.com/fontello/fontello/wiki/How-to-setup-server-to-serve-fonts
= How To Create An AWS User =
[Follow the steps in this article](https://wpadmin.ca/how-to-create-an-aws-user-with-limited-permissions-to-access-cloudfront-only/)


= Paid Support Available =
[Send me an email](http://wpadmin.ca/contact-us/)

== Screenshots ==
1. screenshot-1.png
2. screenshot-2.png
3. screenshot-3.png
4. screenshot-4.png
5. screenshot-5.png

== Changelog ==
V.3.0.1
Enhancements

== Upgrade Notice ==
Bugs & Improvements
