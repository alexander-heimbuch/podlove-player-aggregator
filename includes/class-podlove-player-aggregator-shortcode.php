<?php

/**
 * Podlove Player Aggregator Shortcode
 *
 *
 * @since      1.0.0
 * @package    Podlove_Player_Aggregator
 * @subpackage Podlove_Player_Aggregator/includes
 * @author     Alexander Heimbuch <github@heimbu.ch>
 */
class Podlove_Player_Aggregator_Shortcode
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

    private $options;
    private $api;

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->options = new Podlove_Player_Aggregator_Options($plugin_name);
        $this->api = new Podlove_Player_Aggregator_Rest_Client();
    }

    /**
     * Shortcode Renderer
     *
     * @since    1.0.0
     * @param    array    $atts          Shortcode attributes.
     * @param    array    $content       Shortcode content.
     */
    public function render($atts)
    {
        if (!is_array($atts)) {
            $atts = [];
        }

        $props = array_change_key_case($atts, CASE_LOWER);

        return $this->html($props);
    }

    private function api($site, $path) {
        return get_site_url() . '/' . 'wp-json' . '/' . $this->plugin_name . '/' . 'site' . '/' . $site . '/' . $path;
    }

    private function configUrl($props) {
        if (!array_key_exists('site', $props)) {
            return '';
        }

        if (!array_key_exists('config', $props)) {
            return '';
        }

        if (!array_key_exists('theme', $props)) {
            return '';
        }

        return $this->api($props['site'], 'config' . '/' . $props['config'] . '/' . 'theme' . '/' . $props['theme']);
    }

    private function templateString($props) {
        if (!array_key_exists('site', $props)) {
            return '';
        }

        if (!array_key_exists('template', $props)) {
            return '';
        }

        $site = null;
        $template = $props['template'];
        $options = $this->options->read();
        $playerOptions = null;

        foreach ($options['sites'] as $value) {
            if ($value['name'] == $props['site']) {
                $site = $value;
                break;
            }
        }

        if ($site === null) {
            return '';
        }

        try {
            $playerOptions = $this->api->get($site['url'] . '/wp-json/podlove-web-player/options');
        } 
        finally {

        }

        if ($playerOptions === null) {
            return '';
        }

        if(isset($playerOptions->templates->$template)) {
            return $playerOptions->templates->$template;
        }

        return '';
    }

    private function episodeUrl($props) {
        if (!array_key_exists('site', $props)) {
            return '';
        }

        if (!array_key_exists('post', $props)) {
            return '';
        }

        return $this->api($props['site'], 'episode' . '/' . $props['post']);
    }

    /**
     * Template string generator
     *
     * @since    1.0.0
     * @param    array         $props        array of properties
     */
    private function html($props)
    {
        $embed = '';

        if (array_key_exists('embed', $props) && $props['embed'] === 'player') {
            $embed = '
                <script src="https://cdn.podlove.org/web-player/5.x/embed.js"></script>
                <div data-episode-id="$episode" data-post-id="$post" data-site="$site" data-title="$title" id="player-$episode">$template_string</div>
                <script>
                    podlovePlayer("#player-$episode", "$episode_url", "$config_url");
                </script>
            ';
        } else {
            $embed = '
                <audio data-title="$title" data-episode-id="$episode" data-post-id="$post" data-site="$site" data-title="$title" src="$audio" controls></audio>
            ';
        }

        return strtr($embed, array(
            '$episode' =>  array_key_exists('episode', $props) ? $props['episode'] : '',
            '$post' => array_key_exists('post', $props) ? $props['post'] : '',
            '$audio' => array_key_exists('audio', $props) ? $props['audio'] : '',
            '$site' =>  array_key_exists('site', $props) ? $props['site'] : '',
            '$title' =>  array_key_exists('title', $props) ? $props['title'] : '',
            '$config_url' =>  $this->configUrl($props),
            '$template_string' =>  $this->templateString($props),
            '$episode_url' =>  $this->episodeUrl($props),
        ));
    }
}
