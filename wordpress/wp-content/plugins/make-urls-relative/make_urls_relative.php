<?php
/*
    Plugin Name: Make URLs Relative
    Plugin URI:
    Description: replaces absolute urls to relative ones (inspired from the relative url plugin:        http://sparanoid.com/work/relative-url/)
    Author: Lucy Linder
    Version: 0.1


  Copyright 2014 Tunghsiao Liu, aka. Sparanoid (info@sparanoid.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

  // Inspired from  http://www.deluxeblogtips.com/2012/06/relative-urls.html
// make internal links relative to the root of the site, removing the protocol + host + port
// part. 
// It works for dynamic link generation (through the wp built-in methods) and for 
// post content. 

  // get the host + port part
  $HOME = preg_replace( '|https?://([^/]*).*|', '$1', home_url() );

  // filter wp built-ins
  add_action( 'template_redirect', 'relative_url' );
  // filter post content
  add_filter( 'the_content', 'make_link_relative' );


  // wipe out the host+port part of any link which is in the domain
  function make_link_relative( $link ) {
      global $HOME;
      return preg_replace( '|https?://' . $HOME . '|', '', $link);
  }


  // add filters to wp built-ins
  function relative_url() {
    // Don't do anything if:
    // - In feed
    // - In sitemap by WordPress SEO plugin
    if ( is_feed() || get_query_var( 'sitemap' ) )
      return;


    // the list of functions to filter:
    $filters = array(
      'post_link',                  // Normal post link
      'post_type_link',             // Custom post type link
      'page_link',                  // Page link
      'attachment_link',            // Attachment link
      'get_shortlink',              // Shortlink
      'post_type_archive_link',     // Post type archive link
      'get_pagenum_link',           // Paginated link
      'get_comments_pagenum_link',  // Paginated comment link
      'term_link',                  // Term link, including category, tag
      'search_link',                // Search link
      'day_link',                   // Date archive link
      'month_link',
      'year_link',

      // site location
      'option_siteurl',
      'blog_option_siteurl',
      'option_home',
      'admin_url',
      'home_url',
      'includes_url',
      'site_url',
      'site_option_siteurl',
      'network_home_url',
      'network_site_url',

      // debug only filters
      'get_the_author_url',
      'get_comment_link',
      'wp_get_attachment_image_src',
      'wp_get_attachment_thumb_url',
      'wp_get_attachment_url',
      'wp_login_url',
      'wp_logout_url',
      'wp_lostpassword_url',
      'get_stylesheet_uri',
      'get_stylesheet_directory_uri',
      // 'plugins_url',
      // 'plugin_dir_url',
      // 'stylesheet_directory_uri',
      // 'get_template_directory_uri',
      // 'template_directory_uri',
      'get_locale_stylesheet_uri',
      'script_loader_src', // plugin scripts url
      'style_loader_src', // plugin styles url
      'get_theme_root_uri'
      // 'home_url' NEVER FILTER HOME_URL !!
    );

    // actually add the filter
    foreach ( $filters as $filter ) {
      add_filter( $filter, 'make_link_relative' );
    }
    //home_url($path = '', $scheme = null);
  }

?>
