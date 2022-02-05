<?php

/**
 * Modify podlove web player settings
 *
 *
 * @since      1.0.0
 * @package    Podlove_Player_Aggregator
 * @subpackage Podlove_Player_Aggregator/includes
 * @author     Alexander Heimbuch <kontakt@alexander.heimbu.ch>
 */
class Podlove_Player_Aggregator_Options
{

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    public function __construct($plugin_name)
    {
        $this->plugin_name = $plugin_name;
    }

    /**
     * Serializes values with defaults
     *
     * @since     1.0.0
     */
    private function serializer($value = [])
    {
        return json_encode($value);
    }

    /**
     * Creates the plugin options
     *
     * @since     1.0.0
     */
    public function create()
    {
        add_option($this->plugin_name, $this->serializer());
    }

    /**
     * Reads the plugin options
     *
     * @since     1.0.0
     */
    public function read()
    {

        $options = json_decode(get_option($this->plugin_name), true);

        $options = $options ?? array();

        return array_replace_recursive($options ?? [], array(
            'sites' => $options['sites'] ?? []
        ));
    }

    /**
     * Updates the plugin options
     *
     * @since     1.0.0
     */
    public function update($value = array())
    {
        update_option($this->plugin_name, $this->serializer($value));
    }

    /**
     * Updates the plugin options
     *
     * @since     1.0.0
     */
    public function delete()
    {
        delete_option($this->plugin_name);
    }
}
