<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Format class
 * Help convert between various formats such as XML, JSON, CSV, etc.
 *
 * @author    Phil Sturgeon, Chris Kacerguis, @softwarespot
 * @license   http://www.dbad-license.org/
 */
class GCM
{

    var $url = 'https://fcm.googleapis.com/fcm/send';
    var $serverApiKey = "AAAAAt4-Gyw:APA91bFtb2RgiPFDgrUh1vVBYkUyguMVYxe-1bctgAy1_478s0swFqdkHFWxzR1QGE-pyhNncvvq6cTgMo8-9JF1d6DsuPSRyFUCx2K4x08bCkfapTuse0ug9H0JPeYWgFGKOiJTqDTg";
    var $devices = array();

    /*
      Set the devices to send to
      @param $deviceIds array of device tokens to send to
     */

    function setDevices($deviceIds)
    {
        if (is_array($deviceIds)) {
            $this->devices = $deviceIds;
        } else {
            $this->devices = array($deviceIds);
        }
    }

    /*
      Send the message to the device
      @param $message The message to send
      @param $data Array of data to accompany the message
     */

    function send($message, $data = false)
    {

        if (!is_array($this->devices) || count($this->devices) == 0) {
            $this->error("No devices set");
        }

        if (strlen($this->serverApiKey) < 8) {
            $this->error("Server API Key not set");
        }
        $fields = array(
            'registration_ids' => $this->devices,
            'data' => array("message" => $message),
        );


        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $fields['data'][$key] = $value;
            }
        }

        $headers = array(
            'Authorization: key=' . $this->serverApiKey,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $this->url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Avoids problem with https certificate
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Execute post
        $result = curl_exec($ch);

        // Close connection
        curl_close($ch);
        return $result;
    }

    function error($msg)
    {
        print_r($msg);
        exit;
        echo "Android send notification failed with error:";
        echo "\t" . $msg;
        exit(1);
    }

}
