<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://ptheme.com/
 * @since      1.0.0
 *
 * @package    Wp_Frontend_Submit
 * @subpackage Wp_Frontend_Submit/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wp_Frontend_Submit
 * @subpackage Wp_Frontend_Submit/includes
 * @author     Leo <newbiesup@gmail.com>
 */
class Wp_Frontend_Submit_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-frontend-submit',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
