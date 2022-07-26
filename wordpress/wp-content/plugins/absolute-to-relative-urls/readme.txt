=== Absolute-to-Relative URLs ===
Author: Steven Vachon
URL: http://www.svachon.com/
Contact: contact@svachon.com
Contributors: prometh
Tags: absolute, function, link, links, plugin, relative, shorten, uri, url, urls
Requires at least: 3.2
Tested up to: 3.4.2
Stable tag: trunk

A **function()** for use in shortening URL links. This plugin is meant for dev work and does not automatically shorten URLs.


== Description ==

If you were to use this on a website like *http;//example.com/test/testing/*, you would get results like these:

1. 
	* **Before:** http;//example.com/test/another-test/#anchor
	* **After:** ../another-test/#anchor
2. 
	* **Before:** http;//example.com/wp-content/themes/twentyten/style.css
	* **After:** /wp-content/themes/twentyten/style.css
3. 
	* **Before:** http*s*;//example.com/wp-content/themes/twentyten/style.css
	* **After:** http*s*;//example.com/wp-content/themes/twentyten/style.css
4. 
	* **Before:** http;//google.com/test/
	* **After:** //google.com/test/
5. 
	* **Before:** ../../../../../../../../#anchor
	* **After:** /#anchor
	* **After** (`$output_type=1`)**:** ../../#anchor

**All string parsing. *No* directory browsing.**

If you're looking for a plugin that will *automatically* convert all URLs on your WordPress site, instead check out my other plugin, **[WP-HTML-Compression](http://wordpress.org/extend/plugins/wp-html-compression/)**.
**Before you copy this code and add it into your own**, keep in mind that there will probably be future updates. Keeping the code within an installed plugin will make sure that you're notified.


== Installation ==

1. Download the plugin (zip file).
2. Upload and activate the plugin through the "Plugins" menu in the WordPress admin.


== Frequently Asked Questions ==

= Why isn't this automatic? =

Check out **[WP-HTML-Compression](http://wordpress.org/extend/plugins/wp-html-compression/)** instead.

= How does this work? =

Just use `absolute_to_relative_url($url)`.

= Will this plugin work for WordPress version x.x.x? =

This plugin has only been tested with versions of WordPress as early as 3.2. For anything older, you'll have to see for yourself.


== Changelog ==

= 0.3.4 =
* Empty, hash-only anchors (`"#"`) are no longer invalidated to `"/"`

= 0.3.3 =
* JavaScript URIs (`"javascript:"`) are no longer invalidated as paths

= 0.3.2 =
* Data URIs (`"data:"`) are no longer invalidated as paths

= 0.3.1 =
* Domains with and without "www." are no longer considered to be identical by default, but can still be overridden

= 0.3 =
* Scheme-relative URLs (`//domain.com`) now supported on input and output
* Cleans up the ports and paths of external URLs
* Optionally, output only host-relative URLs (`/root-dir/`)
* Speed optimizations
* Minor bug fixes

= 0.2 =
* Path-relatve URLs (`../`), or parenting, now supported on input and output
* Differentiates schemes/protocols, usernames, passwords, hosts, ports, paths, resources/files, queries and fragments/hashes
* Considers domains with and without "www." to be identical, and can be overridden
* Outputs the shortest url (host- or path-relative) by default, and can be overridden
* Custom site URL support, but only with a separate `new Absolute_to_Relative_URLs()` instance

= 0.1 =
* Initial release