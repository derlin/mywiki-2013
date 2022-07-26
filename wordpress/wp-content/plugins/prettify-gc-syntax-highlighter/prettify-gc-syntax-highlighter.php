<?php
/*
Plugin Name: Prettify GC Syntax Highlighter
Plugin URI: http://www.simplex3d.org/uncategorized/prettify-gc-syntax-highlighter/
Description: A WordPress Plugin to highlight syntax in source code. This plugin has prettify.js copied (unmodified) directly from google-code source browser. So your code will look exactly like it does on google-code.
Author: Aleksey Nikiforov (lex)
Author URI: http://www.simplex3d.org
Version: 1.6.0
License: See prettify.js for license details (Apache License 2.0). Other files are available under GPLv2.
*/

function prettify_init() {
  wp_register_style('prettify-gc-syntax-highlighter', WP_PLUGIN_URL . '/'.basename(dirname(__FILE__)).'/prettify.css');
  wp_enqueue_style('prettify-gc-syntax-highlighter');

  wp_register_script('prettify-gc-syntax-highlighter', WP_PLUGIN_URL . '/'.basename(dirname(__FILE__)).'/prettify.js', array(), false, true);
  wp_enqueue_script('prettify-gc-syntax-highlighter');

  wp_register_script('prettify-gc-syntax-highlighter-launcher', WP_PLUGIN_URL . '/'.basename(dirname(__FILE__)).'/launch.js', array(), false, true);
  wp_enqueue_script('prettify-gc-syntax-highlighter-launcher');

  // addition: latex support
  add_latex_support();
}

add_action('init', 'prettify_init');

/* my addition: basic latex support */
function add_latex_support(){
    // register script
    wp_register_script('prettify-gc-syntax-highlighter-tex', 
        WP_PLUGIN_URL . '/'.basename(dirname(__FILE__)).'/lang-tex.js', 
        array('prettify-gc-syntax-highlighter-launcher', 'prettify-gc-syntax-highlighter'), false, true);
    wp_enqueue_script('prettify-gc-syntax-highlighter-tex');

    // register style
    wp_register_style('prettify-gc-syntax-highlighter-tex', 
        WP_PLUGIN_URL . '/'.basename(dirname(__FILE__)).'/lang-tex.css');
    wp_enqueue_style('prettify-gc-syntax-highlighter-tex');
}

function strip_autoformat($text) {
  $no_br = preg_replace("/<br.?\/>/is", "", $text);
  $no_p = preg_replace("/<p>/is", "", $no_br);
  return preg_replace("/<\/p>/is", "\n", $no_p);
}

// [code lang="scala" start="1" highlight="1-3,6,10-14" background="#C0FFB3"]
// [code lang="scala" quote="false"]
function codetag_handler($atts, $content) {
  extract(shortcode_atts(array(
    'lang' => 'default-markup',
    'quote' => 'true',
    'start' => null,
    'highlight' => null,
    'background' => null,
  ), $atts));

  return "<pre class='" .
    ($quote == 'true' ? "" : "dontquote ") .
    "prettyprint lang-$lang" .
    ($start != null ? " linenums:$start linenumstrigger" : "") .
    ($highlight != null ? " highlight:$highlight" : "") .
    "'" .
    ($background != null ? " style='background-color: $background;'" : "") .
    ">" . strip_autoformat($content) . "</pre>";
}
add_shortcode('code', 'codetag_handler');


/*
// Uncomment this filter to mass convert <code> tags to <pre> tags.
function tags_code_to_pre($text) {
  return preg_replace("/<code>(.*?)<\/code>/ise", "'<pre class=\"'.'prettyprint linenums'.'\">'.stripslashes('$1').'</pre>'", $text);
}
add_filter('the_content', 'tags_code_to_pre', 0);
*/

// This filter makes the code html-friendly, add 'dontquote' as a first class to disable this filter.
function prettify_gc_clean_code($text) {
  return preg_replace("/<pre\\s*class\\s*=\\s*([\"'])\\s*prettyprint\\s*([^>]*?)\\s*>(.*?)<\/pre>/ise", "'<pre class='.stripslashes('$1').'prettyprint '.stripslashes('$2').'>'.htmlentities(stripslashes('$3')).'</pre>'", $text);
}
add_filter('the_content', 'prettify_gc_clean_code', 1);

// This filter adds an extra css class when 'linenums' is present.
function prettify_gc_style_trigger($text) {
  return preg_replace("/<pre\\s*class\\s*=([^>]*?)(\\blinenums\\b)([^>]*?)\\s*>/ise", "'<pre class='.stripslashes('$1').stripslashes('$2').'trigger '.stripslashes('$2').stripslashes('$3').'>'", $text);
}
add_filter('the_content', 'prettify_gc_style_trigger', 2);
?>
