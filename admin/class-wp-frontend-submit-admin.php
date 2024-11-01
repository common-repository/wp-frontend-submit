<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://ptheme.com/
 * @since      1.0.0
 *
 * @package    Wp_Frontend_Submit
 * @subpackage Wp_Frontend_Submit/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Frontend_Submit
 * @subpackage Wp_Frontend_Submit/admin
 * @author     Leo <newbiesup@gmail.com>
 */
class Wp_Frontend_Submit_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Frontend_Submit_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Frontend_Submit_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-frontend-submit-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Frontend_Submit_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Frontend_Submit_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-frontend-submit-admin.js', array( 'jquery' ), $this->version, true );

	}

	/**
	 * Register the plugin settings in Customizer.
	 *
	 * @since    1.0.0
	 */
	public function wfs_customize_register( $wp_customize ) {

		// Add post setting and control.
		$wp_customize->add_section( 'wfs_settings_section' , array(
		    'title'      => __( 'WP Frontend Submit Settings', 'wp-frontend-submit' ),
		    'priority'   => 10,
		) );

		$wp_customize->add_setting( 'wfs_guest_post', array(
			'default'           => '0',
			'type'				=> 'option',
			'transport'			=> 'postMessage',
			// 'sanitize_callback' => 'wfs_sanitize_post_post',
		) );

		$wp_customize->add_control( 'wfs_guest_post', array(
			'label'    		=> __( 'Post Submission Access', 'wp-frontend-submit' ),
			'description'   => __( 'Select whether logging in is required to submit a post.', 'wp-frontend-submit' ),
			'section'  		=> 'wfs_settings_section',
			'type'     		=> 'select',
			'choices'  		=> array(
				'0' => __( 'Only logged in users', 'wp-frontend-submit' ),
				'1' => __( 'All users', 'wp-frontend-submit' ),
				),
			'priority' => 1,
		) );

		$wp_customize->add_setting( 'wfs_post_status', array(
			'default'           => 'pending',
			'type'				=> 'option',
			'transport'			=> 'postMessage',
			// 'sanitize_callback' => 'wfs_sanitize_post_status',
		) );

		$wp_customize->add_control( 'wfs_post_status', array(
			'label'    		=> __( 'Submitted Post Status', 'wp-frontend-submit' ),
			'description'   => __( 'Choose the default status of posts submitted by users.', 'wp-frontend-submit' ),
			'section'  		=> 'wfs_settings_section',
			'type'     		=> 'select',
			'choices'  		=> array(
				'pending' 		=> __( 'Pending for admin review', 'wp-frontend-submit' ),
				'publish' 		=> __( 'Published immediately', 'wp-frontend-submit' ),
			),
			'priority' => 2,
		) );

		$wp_customize->add_setting( 'wfs_images_limit', array(
			'default'           => '3',
			'type'				=> 'option',
			'transport'			=> 'postMessage',
			// 'sanitize_callback' => 'wfs_sanitize_images_limit',
		) );

		$wp_customize->add_control( 'wfs_images_limit', array(
			'label'    		=> __( 'Number of Images users can upload', 'wp-frontend-submit' ),
			'description'   => __( 'Enter the number of images users can upload for each submission. Default: 3', 'wp-frontend-submit' ),
			'section'  		=> 'wfs_settings_section',
			'type'     		=> 'text',
			'priority' => 3,
		) );
	}

	/*public function wfs_sanitize_post_post( $input ) {
	    $valid = array(
	        '0' => __( 'Only logged in users', 'wp-frontend-submit' ),
			'1' => __( 'All users', 'wp-frontend-submit' ),
	    );
	 
	    if ( array_key_exists( $input, $valid ) ) {
	        return $input;
	    } else {
	        return '0';
	    }
	}

	public function wfs_sanitize_post_status( $input ) {
	    $valid = array(
	        'pending' 		=> __( 'Pending for admin review', 'wp-frontend-submit' ),
			'publish' 		=> __( 'Published immediately', 'wp-frontend-submit' ),
	    );
	 
	    if ( array_key_exists( $input, $valid ) ) {
	        return $input;
	    } else {
	        return 'pending';
	    }
	}

	public function wfs_images_limit( $input ) {
	 
	    if ( is_numeric( $input ) ) {
	        return $input;
	    } else {
	        return '3';
	    }
	}*/


}
