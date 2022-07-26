=== Relative URL ===
Contributors: Sparanoid
Donate link: http://sparanoid.com/donate/
Tags: admin, administration, comment, comments, content, contents, excerpt, excerpts, feed, feeds, html, multisite, page, pages, plugin, plugins, post, posts, template, templates, text, title, wp_make_link_relative, widget, widgets, wpmu, writing
Requires at least: 2.1.0
Tested up to: 3.9
Stable tag: 0.0.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Relative URL applies wp_make_link_relative function to links to convert them to relative URLs.

== Description ==

Relative URL applies `wp_make_link_relative` function to links (posts, categories, pages and etc.) to convert them to relative URLs. Useful for developers when debugging local WordPress instance on a mobile device (iPad. iPhone, etc.).

More information please visit my [site](hhttp://sparanoid.com/work/relative-url/).

For example:

`http://localhost:8080/wp/`

Will be converted to:

`/wp/`

And..

`http://localhost:8080/wp/2012/09/01/hello-world/`

Will be converted to:

`/wp/2012/09/01/hello-world/`

And..

`http://localhost:8080/wp/wp-content/themes/twentyeleven/style.css`

Will be converted to:

`/wp/wp-content/themes/twentyeleven/style.css`

Then after activating this plugin, you can simply access your local instance using `http://192.168.0.1:8080/wp/` on your iPad or other mobile devices without having styles and navigation issue.

**Notice**: This plugin is mainly used for local development. Haven't tested on a production environment, but it should work, no harm to your sever.

== Installation ==

WordPress (Also works on multisite enabled instance):

1. Upload the extracted files to the `/wp-content/plugins/` directory, or just install this plugin from your WordPress backend.
2. In 'Plugins' page, choose 'Activate'

== Upgrade Notice ==

= 0.0.9 =
* Readme

= 0.0.8 =
* Compatibility check for 3.7, 3.8, and 3.9 alpha, nothing new, just bump version to tell everyone this plugin still works.

= 0.0.7 =
* Compatibility check for 3.6, nothing new, just bump version to tell everyone this plugin still works.

= 0.0.6 =
* Update donate link, If you like this plugin, please consider buying me a cup of coffee.

= 0.0.5 =
* Update plugin description and page design on sparanoid.com, check it out: http://sparanoid.com/work/relative-url/ props @lianghai

= 0.0.4 =
* Update description, props @lianghai

= 0.0.3 =
* Compatibility check for 3.5, nothing new, just bump version to tell everyone this plugin still works.

= 0.0.2 =
* Moving plugin development repo from WordPress.org to GitHub. You can find the new repo at https://github.com/sparanoid/relative-url

= 0.0.1 =
* First release

== Changelog ==

= 0.0.8 =
* Compatibility check for 3.7, 3.8, and 3.9 alpha, nothing new, just bump version to tell everyone this plugin still works.

= 0.0.7 =
* Compatibility check for 3.6, nothing new, just bump version to tell everyone this plugin still works.

= 0.0.6 =
* Update donate link, If you like this plugin, please consider buying me a cup of coffee.

= 0.0.5 =
* Update plugin description and page design on sparanoid.com, check it out: http://sparanoid.com/work/relative-url/ props @lianghai

= 0.0.4 =
* Update description, props @lianghai

= 0.0.3 =
* Compatibility check for 3.5, nothing new, just bump version to tell everyone this plugin still works.

= 0.0.2 =
* Moving plugin development repo from WordPress.org to GitHub. You can find the new repo at https://github.com/sparanoid/relative-url

= 0.0.1 =
* First release