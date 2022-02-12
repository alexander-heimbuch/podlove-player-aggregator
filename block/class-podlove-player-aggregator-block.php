<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Podlove_Player_Aggregator_Block
 * @subpackage Podlove_Player_Aggregator_Block/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Podlove_Player_Aggregator_Block
 * @subpackage Podlove_Player_Aggregator_Block/admin
 * @author     Alexander Heimbuch <github@heimbu.ch>
 */
class Podlove_Player_Aggregator_Block {

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
   * Plugin api
   */
  private $api;

  /**
   * Plugin shortcode
   */
  private $shortcode;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    5.0.2
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->shortcode = new Podlove_Player_Aggregator_Shortcode( $this->plugin_name, $this->version );
        $this->api = new Podlove_Player_Aggregator_Admin_API( $this->plugin_name, $this->version );
  }

  public function enqueue_scripts() {
    wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'dist/block.js', array( 'wp-blocks', 'wp-i18n', 'wp-element' ), $this->version, true );
    wp_localize_script( $this->plugin_name, 'PODLOVE_PLAYER_AGGREGATOR', array(
        'api' => $this->api->routes(),
        'nonce' => wp_create_nonce('wp_rest')
      )
    );
  }

  public function register_block() {
    if ( ! function_exists( 'register_block_type' ) ) {
      return;
    }

    register_block_type( 'podlove-player-aggregator/shortcode', array(
      'render_callback' => array($this->shortcode, 'render')
    ));
  }
}
