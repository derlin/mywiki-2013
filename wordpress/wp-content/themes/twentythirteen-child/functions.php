<?php
define( 'CHILD_DIR', get_stylesheet_directory_uri() );
/**
 * Proper way to enqueue scripts and styles
 */
function twentythirteen_child_scripts() {
    wp_enqueue_style( 'bootstrap', CHILD_DIR . '/css/bootstrap.css' );
    wp_enqueue_script( 'bootstrap', CHILD_DIR . '/js/bootstrap.min.js', array('jquery'), '1.0.0', true );
    wp_enqueue_script('myutils', CHILD_DIR. '/js/myutils.js', array( 'jquery', 'bootstrap' ));
}

add_action( 'wp_enqueue_scripts', 'twentythirteen_child_scripts' );

//add_filter( 'get_the_excerpt', 'shortcodetext_in_excerpt' );
function shortcodetext_in_excerpt( $excerpt ) {
    $more = ' <a href="'. get_permalink( get_the_ID() ) . '">more...</a>';
    $content = do_shortcode(get_the_content());
    //$content = wp_trim_words( $content, 50, $more );
    // $content = apply_filters('excerpt_length', 50);
    //$content = preg_replace('|<pre.*>.*</pre>|', '[code]', $content);
	// $content = preg_replace("#<pre(.+?)/pre>#", "||k||", $content);
    $content = preg_replace('/<(pre)(?:(?!<\/\1).)*?<\/\1>/s','<i>[code]</i>',$content);
    $content = substr($content, 0, 340);
    return preg_replace( '/\[[^\]]+\]/', '', $content ) . $more;
}




function custom_wp_trim_excerpt($text) {
	$raw_excerpt = $text;
	if ( '' == $text ) {
	    //Retrieve the post content.
	    $text = get_the_content();
	 
	    // resolve shortcodes
	    $text = do_shortcode( $text );
    	$text = preg_replace('/<(pre)(?:(?!<\/\1).)*?<\/\1>/s','@@code@@',$text);
	 
	    $text = apply_filters('the_content', $text);
	    $text = str_replace(']]>', ']]&gt;', $text);
	 
	    $text = strip_tags($text, '<em><br><i><strong><tt><ul><ol><li>');
	 
	    $excerpt_word_count = 130; /*** MODIFY THIS. change the excerpt word count to any integer you like.***/
	    $excerpt_length = apply_filters('excerpt_length', $excerpt_word_count);
	 
	    $excerpt_end = '<div class="excerpt-readmore"><a href="'. get_permalink($post->ID) . '">view article</a></div>'; /*** MODIFY THIS. change the excerpt endind to something else.***/
	    $excerpt_more = apply_filters('excerpt_more', ' ' . $excerpt_end);

	    $words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
	    if ( count($words) > $excerpt_length ) {
	        array_pop($words);
	        $text = implode(' ', $words);
	        $text = force_balance_tags($text);
	    } else {
	        $text = implode(' ', $words);
	    }

	    // replace the <pre>...</pre> by [code] 
    	$text = preg_replace('/@@code@@/','<i>[code]</i>',$text);
	}

	return $text  . $excerpt_more;
}
remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'custom_wp_trim_excerpt');


add_filter( 'wp_nav_menu_items','wpsites_loginout_menu_link' );

function wpsites_loginout_menu_link( $menu ) {
    $loginout = wp_loginout($_SERVER['REQUEST_URI'], false );
    //$loginout = '<li class="nav-menu" class="menu-item">' . wp_loginout($_SERVER['REQUEST_URI'], false ) . '</li>';
    //$menu .= '<li>' . $loginout . '</li>';
    $menu .= $loginout;
    return $menu;
}

?>
