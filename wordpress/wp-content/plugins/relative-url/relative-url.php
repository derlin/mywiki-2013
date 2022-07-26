<?php
/*
Plugin Name: Relative URL
Plugin URI: http://sparanoid.com/work/relative-url/
Description: Relative URL applies wp_make_link_relative function to links (posts, categories, pages and etc.) to convert them to relative URLs. Useful for developers when debugging local WordPress instance on a mobile device (iPad. iPhone, etc.).
Version: 0.0.9
Author: Tunghsiao Liu
Author URI: http://sparanoid.com/
Author Email: info@sparanoid.com
License: GPLv2 or later

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

  // Modified from https://github.com/retlehs/roots/issues/490

  // This makes paths realitive for NEW content.
  // For previously posted content, full urls are already loaded in to the database and will need to be updated.

  // for media and image paths - update wp admin: settings>media
  // set "Full URL path to files" feild to '/your-upload-folder-name/'
  // This would be '/assets/' for a default roots install.

  // http://www.deluxeblogtips.com/2012/06/relative-urls.html

  add_action( 'template_redirect', 'relative_url' );
  //add_filter( 'the_content', 'replace_relative_url' );

  
  function replace_relative_url($content='') {
      $content = preg_replace( '#https?://error418.no-ip.org:4321#', '', $content);
      return $content;
  }

  function make_link_relative( $link ) {
      //echo "link : " . $link . "<br />";
      return $link;
      //return preg_replace( '|https?://[^/]+(/.*)|i', '$1', $link );
  }


  function relative_url() {
    // Don't do anything if:
    // - In feed
    // - In sitemap by WordPress SEO plugin
    if ( is_feed() || get_query_var( 'sitemap' ) )
      return;
    $filters = array(
      'post_link',       // Normal post link
      'post_type_link',  // Custom post type link
      'page_link',       // Page link
      'attachment_link', // Attachment link
      'get_shortlink',   // Shortlink
      'post_type_archive_link',    // Post type archive link
      'get_pagenum_link',          // Paginated link
      'get_comments_pagenum_link', // Paginated comment link
      'term_link',   // Term link, including category, tag
      'search_link', // Search link
      'day_link',   // Date archive link
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
      // 'home_url'
    );

    foreach ( $filters as $filter ) {
      add_filter( $filter, 'make_link_relative' );
      echo '|' . $url . '?://[^/]+(/.*)|i';
    }
    home_url($path = '', $scheme = null);
  }
?>
