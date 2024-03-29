<?php
/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Podlove_Player_Aggregator
 * @subpackage Podlove_Player_Aggregator/includes
 * @author     Alexander Heimbuch <kontakt@alexander.heimbu.ch>
 */
class Podlove_Player_Aggregator_Rest_Client {

    public function __construct() {}

    // This method will perform an action/method thru HTTP/API calls
    // Parameter description:
    // Method= POST, PUT, GET etc
    // Data= array("param" => "value") ==> index.php?param=value
    private function request($method, $url, $data = null)
    {
        $curl = curl_init();

        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // Optional Authentication:
        //curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        //curl_setopt($curl, CURLOPT_USERPWD, "username:password");

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        $contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);

        curl_close($curl);

        if (!str_contains($contentType, 'application/json')) {
            return array();
        }

        $result = json_decode($result, true);
        
        return $result;
    }


    public function get($url) {
        return $this->request('GET', $url);
    }
}