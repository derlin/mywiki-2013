<?php
/**
 * @package EasyIncludes
 * @version 0.1
 */

/*
    Plugin Name: Easy Includes
    Plugin URI:
    Description: simply add code or posts from files
    Author: Lucy Linder
    Version: 0.1
*/

include( dirname(__FILE__) . DIRECTORY_SEPARATOR . 'EasyIncludesSettingsPage.php' );

//[display_file path="local/path/to/file"]
function sc_display_file( $attrs ){
    extract( shortcode_atts( array(
        'path' => '',
    ), $attrs ) );

    $options = get_option('easyincludes_settings');
    $path = $options['root_fs'] . DIRECTORY_SEPARATOR . $path; 
    if( $path && file_exists($path)  ){
        return file_get_contents($path);
    }
}

add_shortcode( 'display_file', 'sc_display_file' );

function easyincludes_admin_notices() {
         settings_errors( 'EasyIncludesSettingsPage_notice' );
}


 if( is_admin() ){
     $ei_settings_page = new EasyIncludesSettingsPage();
     add_action( 'admin_notices', 'EasyIncludesSettingsPage_notice_admin_notice' );
 }


?>
