<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://alexander.heimbu.ch
 * @since      1.0.0
 *
 * @package    Podlove_Player_Aggregator
 * @subpackage Podlove_Player_Aggregator/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Podlove_Player_Aggregator
 * @subpackage Podlove_Player_Aggregator/admin
 * @author     Alexander Heimbuch <kontakt@alexander.heimbu.ch>
 */
class Podlove_Player_Aggregator_Admin {

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
		$this->api = new Podlove_Player_Aggregator_Admin_API($plugin_name, $version);
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles($hook) {
		if ($hook != 'settings_page_' . $this->plugin_name . '-settings') {
			return null;
		}

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Podlove_Player_Aggregator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Podlove_Player_Aggregator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'dist/settings.css', array('wp-components'), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook) {
		if ($hook != 'settings_page_' . $this->plugin_name . '-settings') {
			return null;
		}

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Podlove_Player_Aggregator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Podlove_Player_Aggregator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'dist/settings.js', array( 'wp-api', 'wp-i18n', 'wp-components', 'wp-element' ), $this->version, true );
		wp_localize_script( $this->plugin_name, 'PODLOVE_PLAYER_AGGREGATOR', array(
			'api' => $this->api->routes(),
			'nonce' => wp_create_nonce('wp_rest')
		  )
		);
	}

	public function page_settings() {
		include( plugin_dir_path( __FILE__ ) . 'partials/podlove-player-aggregator-admin-display.php' );
  	}

	public function register_menu_page() {
		return add_submenu_page(
			'options-general.php',
			'Podlove Player Aggregator',
			'Podlove Aggregator',
			'manage_options',
			$this->plugin_name . '-settings',
			array( $this, 'page_settings' )
		);
	  }

	/**
	 * Register api routes
	 *
	 * @since    1.0.0
	 */
	public function add_routes() {
		$this->api->registerRoutes();
	}
}
