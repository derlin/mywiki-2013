<?php
class EasyIncludesSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $notice_id = "EasyIncludesSettingsPage_notice";

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'EasyIncludes Settings', 
            'manage_options', 
            'ei-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'easyincludes_settings' );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>EasyIncludes Settings</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'easyincludes_option_group' );   
                do_settings_sections( 'ei-setting-admin' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'easyincludes_option_group', // Option group
            'easyincludes_settings', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'ei_path', // ID
            'File System', // Title
            array( $this, 'print_section_info' ), // Callback
            'ei-setting-admin' // Page
        );  

        add_settings_field(
            'root_fs', // ID
            'Base dir', // Title 
            array( $this, 'root_fs_callback' ), // Callback
            'ei-setting-admin', // Page
            'ei_path' // Section           
        );      

    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['root_fs'] ) ){

            if( !is_dir( $input['root_fs'] ) || !is_readable( $input['root_fs'] ) )
                add_settings_error(
                    $this->notice_id,
                    'invalid_path',
                    __('The path "' . $input['root_fs'] . '" does not exist or is not readable'), 
                    'error'

                ); 

            else 
                add_settings_error(
                    $this->notice_id,
                    'ok',
                    __( 'Settings updated' ),
                    'updated'

                ); 
                $new_input['root_fs'] = realpath( $input['root_fs'] );
        }

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function root_fs_callback()
    {
        printf(
            '<input type="text" id="root_fs" name="easyincludes_settings[root_fs]" 
                value="%s" /> Specify the base directory for included files',
                isset( $this->options['root_fs'] ) ? 
                    esc_attr( $this->options['root_fs']) : ''
        );
    }

}

