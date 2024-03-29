<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://alexander.heimbu.ch
 * @since      1.0.0
 *
 * @package    Podlove_Player_Aggregator
 * @subpackage Podlove_Player_Aggregator/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Podlove_Player_Aggregator
 * @subpackage Podlove_Player_Aggregator/includes
 * @author     Alexander Heimbuch <kontakt@alexander.heimbu.ch>
 */
class Podlove_Player_Aggregator {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Podlove_Player_Aggregator_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PODLOVE_PLAYER_AGGREGATOR_VERSION' ) ) {
			$this->version = PODLOVE_PLAYER_AGGREGATOR_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'podlove-player-aggregator';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_block_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Podlove_Player_Aggregator_Loader. Orchestrates the hooks of the plugin.
	 * - Podlove_Player_Aggregator_i18n. Defines internationalization functionality.
	 * - Podlove_Player_Aggregator_Admin. Defines all hooks for the admin area.
	 * - Podlove_Player_Aggregator_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-podlove-player-aggregator-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-podlove-player-aggregator-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-podlove-player-aggregator-admin.php';

    	/**
		 * The class responsible for loading and saving plugin options
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-podlove-player-aggregator-options.php';

		/**
		 * The class responsible for defining the Admin REST API
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-podlove-player-aggregator-admin-api.php';

		/**
		 * The class responsible for API calls
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-podlove-player-aggregator-rest-client.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-podlove-player-aggregator-public.php';

    	/**
		 * The class responsible for rednering player shortcode
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-podlove-player-aggregator-shortcode.php';
    	
		/**
		 * The class responsible for the public api
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-podlove-player-aggregator-public-api.php';

		/**
		 * The class responsible for defining the block
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'block/class-podlove-player-aggregator-block.php';


		$this->loader = new Podlove_Player_Aggregator_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Podlove_Player_Aggregator_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Podlove_Player_Aggregator_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Podlove_Player_Aggregator_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'register_menu_page' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin, 'add_routes' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Podlove_Player_Aggregator_Public( $this->get_plugin_name(), $this->get_version() );
		$plugin_block = new Podlove_Player_Aggregator_Block( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_block, 'register_block' );
		$this->loader->add_action( 'rest_api_init', $plugin_public, 'add_routes' );
	}

	/**
	 * Register all of the hooks related to the block functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_block_hooks() {
		$plugin_block = new Podlove_Player_Aggregator_Block( $this->get_plugin_name(), $this->get_version() );
	
		$this->loader->add_action( 'enqueue_block_editor_assets', $plugin_block, 'enqueue_scripts' );
	  }

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Podlove_Player_Aggregator_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
