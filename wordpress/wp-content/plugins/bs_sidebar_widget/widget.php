<?php
// Creating the widget 
class boostrap_sidebar_widget extends WP_Widget {

//--------------------------------------------------------
    /**
     * constructor
     */
    function __construct() {
        parent::__construct(
            'bootstrap_sidebar_widget', 

            // Widget name as it will appear in UI
            __('Bootstrap Sidebar Widget', 'boostrap_sidebar_widget_domain'), 

            // Widget description
            array( 'description' => __( 'Display a side menu like in bootstrap\'s site with h1 and h2 present in the page'))
        );

    }

//--------------------------------------------------------

    /**
     * widget front-end, the content of the widget
     * when activated
     */
    public function widget( $args, $instance ) {
        // get title
        $title = apply_filters( 'widget_title', $instance['title'] );
        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if ( ! empty( $title ) )
            echo $args['before_title'] . $title . $args['after_title'];

        // enqueue the js
        wp_dequeue_script('bootstrap');
        wp_enqueue_script( 'bootstrap_fix', plugins_url( 'bootstrap_fix.js', __FILE__), 
            array( 'jquery' ));
        wp_enqueue_script('bs_sidebar_js', plugins_url('/bs_sidebar_widget.js', __FILE__ ), 
            array( 'jquery', 'bootstrap' ));


        // add the skeleton of the side menu
        // it will be populated by javascript on document ready
?>
        <div class=" the-sidebar-container">
             <div id="widgbs_sidebar" class="bs-sidebar">
                <ul class="nav bs-sidebar"></ul>
            </div>
        </div>
<?php
        echo $args['after_widget'];
        // insert the css, customised with the settings
        $this->css( $instance );
    }// end widget 

//--------------------------------------------------------

    /**
     *  Widget Backend 
     *  settings are: 
     *   - the title
     *   - the color of the active link
     *   - additional css for the active links,
     *     like font-weight: bold; and such
     */
    public function form( $instance ) {

        wp_enqueue_style( 'wp-color-picker' );        
        wp_enqueue_script( 'wp-color-picker' );    

        $defaults = array( 
            'title'                  => '',
            'active_links_color'     => '#563d7c',
            'active_links_extra_css' => 'font-style: italic;'
        );
        // merge the defaults args with user-defined ones
        $instance = wp_parse_args( (array) $instance, $defaults );
        $title = $instance[ 'title' ];
?>
        <script type='text/javascript'> // script for the color picker
                    jQuery(document).ready(function($) {
                $('.my-color-picker').wpColorPicker();
            });
        </script>

<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>">
    <?php _e( 'Title:' ); ?>
</label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" 
       name="<?php echo $this->get_field_name( 'title' ); ?>" 
       type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<p>
<div>
    <label for="<?php echo $this->get_field_id( 'active_links_color' ); ?>">
    <?php _e( 'Active links color', $this->textdomain ); ?>
    </label>
</div>
<input class="my-color-picker" id="<?php echo $this->get_field_id( 'active_links_color' ); ?>" 
       name="<?php echo $this->get_field_name( 'active_links_color' ); ?>" 
       type="text" value="<?php echo esc_attr( $instance['active_links_color'] ); ?>"
       style="width: 100%" />
</p>

<label>extra css for active links:</label>
<textarea class="widefat" id="<?php echo $this->get_field_id( 'active_links_extra_css' ); ?>" 
       name="<?php echo $this->get_field_name( 'active_links_extra_css' ); ?>" 
       type="text" rows=15 cols="10" >
        <?php echo esc_attr( $instance['active_links_extra_css'] ); ?>
</textarea> 

</p>
<?php 
    }

//--------------------------------------------------------
    
    /**
     * update the widget settings
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array_map( "strip_tags", $new_instance );
        return $instance;
    }

//--------------------------------------------------------

    /**
     * prints the css, using the colors specified in the 
     * widget's settings
     */
    function css( $instance ){
?>
<style type="text/css">
.anchored:before {
    content:"";
    display:block;
    height:50px;
    margin:-30px 0 0;
}

.bs-sidebar.affix {
    position: static;
}
.bs-sidenav {
    background-color: #F7F5FA;
    border-radius: 5px;
    margin-bottom: 30px;
    margin-top: 30px;
    padding-bottom: 10px;
    padding-top: 10px;
    text-shadow: 0 1px 0 #FFFFFF;
}
.bs-sidebar .nav > li > a {
    color: black;
    display: block;
    text-align: left;
    padding: 2px 12px 2px 2px;
}
.bs-sidebar .nav > li > a:hover, .bs-sidebar .nav > li > a:focus {
    background-color: <?php echo $this->hex2rgb( $instance['active_links_color'], '0.1' ) ?>;/* rgba(242, 191, 114, 0.1);*/
    border-right: 1px solid <?php echo $this->hex2rgb( $instance['active_links_color'], '0.1' ) ?>;
    text-decoration: none;
}
.bs-sidebar .nav > .active > a, .bs-sidebar .nav > .active:hover > a, .bs-sidebar .nav > .active:focus > a {
    background-color: rgba(0, 0, 0, 0);
    border-right: 1px solid <?php echo $instance['active_links_color'] ?>;
    color: <?php echo $instance['active_links_color'] ?>;
    /*font-weight: bold; */
    <?php if( $instance['active_links_extra_css'] ) echo $instance['active_links_extra_css'] ?>
}
.bs-sidebar .nav .nav {
    display: none;
    margin-bottom: 8px;
}
.bs-sidebar .nav .nav > li > a {
    font-size: 0.9em;
}
@media (min-width: 992px) {
.bs-sidebar .nav > .active > ul {
    display: block;
}
.bs-sidebar.affix, .bs-sidebar.affix-bottom {
    width: inherit;
}
.bs-sidebar.affix {
    position: fixed;
    top: 80px;
}
.bs-sidebar.affix-bottom {
    position: absolute;
}
.bs-sidebar.affix-bottom .bs-sidenav, .bs-sidebar.affix .bs-sidenav {
    margin-bottom: 0;
    margin-top: 0;
}
}
@media (min-width: 1200px) {
.bs-sidebar.affix-bottom, .bs-sidebar.affix, .bs-sidebar.affix-top {
    /* width: 500px; */
}
}
</style>

<?php    
    }// end css function

//--------------------------------------------------------
    
    /**
     * takes a color in hexadecimal and converts it to the 
     * corresponding css string.
     * If an alpha value is provided, the string will be
     * of the form "rgba(r,g,b,alpha)"
     */
    function hex2rgb($hex, $alpha='') {
        $hex = str_replace("#", "", $hex);

        if(strlen($hex) == 3) {
            $int = hexdec($hex);
            $r = 0xF & ($int >> 0x8);
            $g = 0xF & ($int >> 0x4);
            $b = 0xF & $int; 
        } else {
            $int = hexdec($hex);
            $r = 0xFF & ($int >> 0x10);
            $g = 0xFF & ($int >> 0x8);
            $b = 0xFF & $int; 

        }

        if( $alpha )
            $rgb = "rgba(" . $r . ", " . $g . ", " . $b . ", " . $alpha . ")";
        else
            $rgb = "rgb(" . $r . ", " . $g . ", " . $b . ")";

        return $rgb; 
    }
} // Class boostrap_sidebar_widget ends here

//--------------------------------------------------------

// Register and load the widget
function wpb_load_widget() {
    register_widget( 'boostrap_sidebar_widget' );
}

add_action( 'widgets_init', 'wpb_load_widget' ); // register the widget
