=== Plugin Name ===
Prettify GC Syntax Highlighter
Contributors: see prettify.js; WP plugin by Aleksey Nikiforov (lex).
Donate link:
Tags: Syntax Highlighter, Google Code, Prettify, Prettify.js
Requires at least: 3.0.5
Tested up to: 3.4.1
Stable tag: 1.6.0

Your code will look exactly like it does on google-code.


== License ==

See prettify.js for license details (Apache License 2.0). Other files are available under GPLv2.


== Description ==

This plugin has prettify.js copied directly from google-code source browser. So your code will look exactly like it does on google-code.
CSS is slightly modified to embolden keywords and types.


== Installation ==

You can use the WordPress plugin installer:

1. Go to: WpAdim -> Plugins -> Add New -> Upload.
2. Simply upload the zip file and then click 'Activate'.

Or you can install the plugin manually:

1. Upload the content of the zip file to the `/wp-content/plugins/` directory.
2. Then activate the plugin through the 'Plugins' menu in WordPress.


== Screenshots ==

1. How to use and a short example.
2. A larger example with line numbers.


== Changelog ==

= 1.6.0 =
* Fixed "wp_register_style was called incorrectly" warnings.

= 1.5.1 =
* Fixed line highlight indices when using linenums.
* Added shortcodes.

= 1.4 =
* Added an option to highlight one or serveral lines of code.

= 1.3.1 =
* Updated the language list.
* Improved documentation.

= 1.2 =
* Improved htlm-friendly quote filter.
* Added line numbering support.
* Added overflow support.

= 1.1 =
* The first public version.


== Upgrade Notice ==

= 1.6.0 =
Fixed WP warning messages.

= 1.5.1 =
Easier to use with shortcodes. Minor bug fixes.

= 1.4 =
A new option to highlight one or serveral lines of code.

= 1.3.1 =
Improved documentation.

= 1.2 =
Upgrade to the new version for overflow support and line numbering.


== Usage ==

&lt;pre class=&quot;prettyprint&quot;&gt;
  // Put your code here.
&lt;/pre&gt;

Or with shortcode:

[code]
  // Put your code here.
[/code]


== Making Code HTML Safe ==

The plugin automatically makes highlighted code html-safe:

&lt;pre class=&quot;prettyprint&quot;&gt;
  Anything that goes here will be quoted to appear on the page as-is.
  For example: &lt;head&gt;&lt;/head&gt;&lt;body&gt;&lt;/body&gt;
&lt;/pre&gt;

To avoid quoting your code, add <strong>dontquote</strong> as the first class.

&lt;pre class=&quot;dontquote prettyprint&quot;&gt;
  Remember to quote manually.
  For example: &amp;lt;head&amp;gt;&amp;lt;/head&amp;gt;&amp;lt;body&amp;gt;&amp;lt;/body&amp;gt;
&lt;/pre&gt;


== Line Numbering ==

Line numbdering can be enabled as follows:

&lt;pre class=&quot;prettyprint linenums&quot;&gt;
  // Put your code here.
&lt;/pre&gt;

If you want to start with a line number 42, you can do the following:

&lt;pre class=&quot;prettyprint linenums:42&quot;&gt;
  // Put your code here.
&lt;/pre&gt;


== Highlighting Lines ==

When line numbering is enabled you can highlight one or several lines of code using the highlight class:

&lt;pre class=&quot;prettyprint lang-YOURLANG linenums highlight:2,4&quot;&gt;
  // line 2
  // line 3
  // line 4
&lt;/pre&gt;

Alternatively you can highlight one or several code segments:

&lt;pre class=&quot;prettyprint lang-YOURLANG linenums highlight:1-3,5-6&quot;&gt;
  // line 2
  // line 3
  // line 4
  // line 5
&lt;/pre&gt;

Note: This feature is not available when line numbering is disabled.


== Specifying Language ==

Most of the time the highlighter will do a good job guessing how to highlight the code. However, to achieve the best result, you should specify the language:

&lt;pre class=&quot;prettyprint lang-YOURLANG&quot;&gt;
  // Put your code here.
&lt;/pre&gt;

Replace YOURLANG with one of the following:

  * aea
  * agc
  * apollo
  * bsh
  * c
  * cc
  * cl
  * cpp
  * cs
  * csh
  * css-str
  * cv
  * cxx
  * cyc
  * default-markup
  * el
  * fs
  * go
  * hs
  * htm
  * html
  * java
  * js
  * json
  * lisp
  * lua
  * m
  * ml
  * mxml
  * perl
  * pl
  * pm
  * proto
  * py
  * rb
  * scala
  * scm
  * sh
  * sql
  * vhd
  * vhdl
  * wiki
  * xhtml
  * xml
  * xsl
  * yaml
  * yml


== Shortcode ==

Here is a quick example. All the parameters are optional.

[code lang="scala" start="1" highlight="1-3,6,10-14" background="#C0FFB3"]
  // Put your code here.
[/code]

Html quoting can be disabled as follows:

[code quote="false"]
  // Put your code here.
[/code]

The contents of shortcodes is auto-formatted by WP. As a result &lt;p&gt; and &lt;br/&gt; tags along
with some formatting will be lost. If you need to preseve the original formatting use
html notation instead of the shortcode.


== Please Support ==

If you like Prettify GC Syntax Highlighter plugin, please donate a minute of your time and vote for it. Thank you!