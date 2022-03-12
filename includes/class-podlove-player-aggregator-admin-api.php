<?php

/**
 * REST interface for the podlove web player configurator
 *
 *
 * @since      1.0.0
 * @package    Podlove_Player_Aggregator
 * @subpackage Podlove_Player_Aggregator/includes
 * @author     Alexander Heimbuch <kontakt@alexander.heimbu.ch>
 */
class Podlove_Player_Aggregator_Admin_API
{
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
     * Plugin options
     */
    private $options;


    /**
     * API Client
     */
    private $api;


    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->options = new Podlove_Player_Aggregator_Options($plugin_name);
        $this->api = new Podlove_Player_Aggregator_Rest_Client();
    }

    /**
     * Define API paths
     *
     * @since    1.0.0
     */
    public function routes()
    {
        return array(
            'sites' => esc_url_raw(rest_url($this->plugin_name . '/' . $this->version . '/' . 'sites')),
            'verify' => esc_url_raw(rest_url($this->plugin_name . '/' . $this->version . '/' . 'verify')),
            'episodes' => esc_url_raw(rest_url($this->plugin_name . '/' . $this->version . '/' . 'episodes')),
            'details' => esc_url_raw(rest_url($this->plugin_name . '/' . $this->version . '/' . 'details'))
        );
    }

    /**
     * Register the API routes
     *
     * @since    1.0.0
     */
    public function registerRoutes()
    {
        register_rest_route($this->plugin_name . '/' . $this->version, 'sites',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'getSites'),
                'permission_callback' => array($this, 'api_permissions'),
            )
        );

        register_rest_route($this->plugin_name . '/' . $this->version, 'sites',
            array(
                'methods' => 'POST',
                'callback' => array($this, 'updateSites'),
                'args' => array(
                    'sites' => array(
                        'required' => true

                    ),
                ),
                'permission_callback' => array($this, 'api_permissions'),
            )
        );

        register_rest_route($this->plugin_name . '/' . $this->version, 'verify',
            array(
                'methods' => 'POST',
                'callback' => array($this, 'verifySite'),
                'args' => array(
                    'site' => array(
                        'required' => true

                    ),
                ),
                'permission_callback' => array($this, 'api_permissions'),
            )
        );

        register_rest_route($this->plugin_name . '/' . $this->version, 'episodes',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'getEpisodes'),
                'permission_callback' => array($this, 'api_permissions'),
            )
        );

        register_rest_route($this->plugin_name . '/' . $this->version, 'details',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'getDetails'),
                'args' => array(
                    'site' => array(
                        'required' => true
                    ),
                    'id' => array(
                        'required' => true
                    )
                ),
                'permission_callback' => array($this, 'api_permissions'),
            )
        );
    }

    /**
     * Check API permissions
     *
     * @since    1.0.0
     */
    public function api_permissions()
    {
      return current_user_can('edit_posts');
    }

    /**
     * Load initial data
     *
     * @since    1.0.0
     */
    public function getSites()
    {
        $data = $this->options->read();

        return rest_ensure_response($data['sites']);
    }

    /**
     * Load initial data
     *
     * @since    1.0.0
     */
    public function updateSites(WP_REST_Request $request)
    {
        $options = $this->options->read();

        $options['sites'] = $request->get_param('sites');

        $this->options->update($options);
        $options = $this->options->read();

        return rest_ensure_response($options['sites']);
    }

    /**
     * Verify Sites
     *
     * @since    1.0.0
     */
    public function verifySite(WP_REST_Request $request)
    {
        $valid = false;
        
        try {
            $valid = array_key_exists('_version', $this->api->get($request->get_param('site') . '/wp-json/podlove/v2/episodes')) !== null;
        } finally {

        }

        return rest_ensure_response(array('valid' => $valid));
    }

    /**
     * get list of episodes
     *
     * @since    1.0.0
     */
    public function getEpisodes(WP_REST_Request $request)
    {
        $options = $this->options->read();
        $results = array();
        $sites = $options['sites'] ?? [];

        foreach ($sites as $site) {
            try {
                $result = $this->api->get($site['url'] . '/wp-json/podlove/v2/episodes');
                $episodes = $result->results ?? array();

                foreach ($episodes as $episode) {
                    array_push($results, array(
                        'episode' => $episode->id,
                        'title' => $episode->title,
                        'site' => $site['name']
                    ));
                }
            } finally {
                
            }
    
        }

        return rest_ensure_response(array('results' => $results));
    }

    /**
     * get list of episodes
     *
     * @since    1.0.0
     */
    public function getDetails(WP_REST_Request $request)
    {
        $options = $this->options->read();
        $site = null;

        foreach ($options['sites'] as $value) {
            if ($value['name'] == $request->get_param('site')) {
                $site = $value;
                break;
            }
        }

        if ($site === null) {
            return new WP_Error( 'no_site', __( "Couldn't find a matching site", "podlove-player-aggregator" ) );
        }
        

        try {
            $details = $this->api->get($site['url'] . '/wp-json/podlove/v2/episodes/' . $request->get_param('id'));
        } finally {
            
        }

        return rest_ensure_response(array('result' => $details));
    }
}
