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
class Podlove_Player_Aggregator_Public_API
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
     * Register the API routes
     *
     * @since    1.0.0
     */
    public function registerRoutes()
    {
        register_rest_route(
            $this->plugin_name . '/' . 'site',
            '/(?P<site>[a-z0-9-]+)/config/(?P<config>[a-z0-9-]+)/theme/(?P<theme>[a-z0-9-]+)',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'config'),
                'args' => array(
                    'config' => array(
                        'required' => true,
                    ),
                    'theme' => array(
                        'required' => true,
                    ),
                    'site' => array(
                        'required' => true,
                    ),
                ),
                'permission_callback' => '__return_true',
            )
        );

        register_rest_route(
            $this->plugin_name . '/' . 'site',
            '/(?P<site>[a-z0-9-]+)/episode/(?P<episode>\d+)',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'episode'),
                'args' => array(
                    'site' => array(
                        'required' => true,
                    ),
                    'episode' => array(
                        'required' => true,
                    ),
                ),
                'permission_callback' => '__return_true',
            )
        );
    }

    public function config(WP_REST_Request $request)
    {
        $config = $request->get_param('config');
        $theme = $request->get_param('theme');
        $options = $this->options->read();

        $site = null;
        $result = null;

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
            $result = $this->api->get($site['url'] . '/wp-json/podlove-web-player/shortcode/config/' . $config . '/'. 'theme' . '/' . $theme);
        } 
        finally {

        }


        if (isset($result->playlist)) {
            $playlist = array();

            try {
                $playlist = $this->api->get($result->playlist);
            } 
            finally {
                if (is_iterable($playlist)) {
                    foreach ($playlist as $value) {
                        $value->config = get_site_url() . '/' . 'wp-json' . '/' . $this->plugin_name . '/' . 'site' . '/' . $site['name'] . '/' . 'episode' . '/' . $value->episode;
                    }
                    $result->playlist = $playlist;
                }
            }
        }

        if ($result === null) {
            return new WP_Error( 'no_config', __( "Couldn't find a config for this request", "podlove-player-aggregator" ) );
        }


        return rest_ensure_response($result);
    }

    public function episode(WP_REST_Request $request)
    {
        $episode = $request->get_param('episode');
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
            $result = $this->api->get($site['url'] . '/wp-json/podlove-web-player/shortcode/publisher/' . $episode);
        } 
        finally {

        }
        
        if ($result === null) {
            return new WP_Error( 'no_episode', __( "Couldn't find an episode for this request", "podlove-player-aggregator" ) );
        }

        if (isset($result->transcripts)) {
            try {
                $result->transcripts = $this->api->get($result->transcripts);
            } 
            finally {
            }
        }

        return rest_ensure_response($result);
    }
}
