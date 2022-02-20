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

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
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


    /**
     * Template string generator
     *
     * @since    1.0.0
     * @param    array         $props        array of properties
     */
    private function html($props)
    {
        $embed = '
            <audio data-title="$title" data-episode-id="$episode" data-post-id="$post" data-site="$site" data-title="$title" src="$audio" controls></audio>
        ';

        return strtr($embed, array(
            '$episode' =>  array_key_exists('episode', $props) ? $props['episode'] : '',
            '$post' => array_key_exists('post', $props) ? $props['post'] : '',
            '$audio' => array_key_exists('audio', $props) ? $props['audio'] : '',
            '$site' =>  array_key_exists('site', $props) ? $props['site'] : '',
            '$title' =>  array_key_exists('title', $props) ? $props['title'] : ''
        ));
    }
}
