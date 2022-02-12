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

        $attributes = array_change_key_case($atts, CASE_LOWER);

        return $this->html($attributes['episode'], $attributes['post'], $attributes['title'], $attributes['site'], $attributes['audio']);
    }


    /**
     * Template string generator
     *
     * @since    1.0.0
     * @param    string         $episode        episode id
     * @param    string         $post           post id
     * @param    string         $title          title
     * @param    string         $site          site
     * @param    string         $audio          audio url
     */
    private function html($episode, $post, $title, $site, $audio)
    {
        $embed = '
            <audio data-title="" data-episode-id="$episode" data-post-id="$post" data-site="$site" data-title="$title" src="$audio"></audio>
        ';

        return strtr($embed, array(
            '$episode' => $episode,
            '$post' => $post,
            '$audio' => $audio,
            '$site' => $site,
            '$title' => $title
        ));
    }
}
