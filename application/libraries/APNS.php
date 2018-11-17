<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Format class
 * Help convert between various formats such as XML, JSON, CSV, etc.
 *
 * @author    Phil Sturgeon, Chris Kacerguis, @softwarespot
 * @license   http://www.dbad-license.org/
 */
class APNS
{

    //var $url='https://fcm.googleapis.com/fcm/send';
    //  var $url = 'ssl://gateway.push.apple.com:2195';
   /* var $url = 'ssl://gateway.sandbox.push.apple.com:2195';
    var $passPhrase = "12345";*/
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

    function send($message = NULL, $data = false)
    {
        $deviceToken = $this->devices;
        foreach ($deviceToken as $token) {

            /* We are using the sandbox version of the APNS for development. For production
                environments, change this to ssl://gateway.push.apple.com:2195 */
           $apnsServer = 'ssl://gateway.push.apple.com:2195';
            /* Make sure this is set to the password that you set for your private key
             when you exported it to the .pem file using openssl on your OS X */
            $privateKeyPassword = '12345';
            /* Put your own message here if you want to */
           // $message = 'Welcome to BNCHR DRIVER Push Notifications';
            /* Pur your device token here */
            $deviceToken =
                $token;
            /* Replace this with the name of the file that you have placed by your PHP
             script file, containing your private key and certificate that you generated
             earlier */
            $pushCertAndKeyPemFile = 'dis_fetch.pem';
           // $pushCertAndKeyPemFile = 'Certificates_fetch_push_dev.pem';
            $stream = stream_context_create();
            stream_context_set_option($stream,
                'ssl',
                'passphrase',
                $privateKeyPassword);
            stream_context_set_option($stream,
                'ssl',
                'local_cert',
                $pushCertAndKeyPemFile);

            $connectionTimeout = 60;
            $connectionType = STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT;
            $connection = stream_socket_client($apnsServer,
                $errorNumber,
                $errorString,
                $connectionTimeout,
                $connectionType,
                $stream);
            if (!$connection){
                echo "Failed to connect to the APNS server. Error no = $errorNumber<br/>";
                exit;
            } /*else {
                echo "Successfully connected to the APNS. Processing...</br>";
            }*/
        $messageBody['aps'] = array('alert' => $message,'data' => $data,
            'sound' => 'default','content-available'=> 1,
            'badge' => 2,
        );
            $payload = json_encode($messageBody);
            $notification = chr(0) .
                pack('n', 32) .
                pack('H*', $deviceToken) .
                pack('n', strlen($payload)) .
                $payload;
            $wroteSuccessfully = fwrite($connection, $notification, strlen($notification));
            if (!$wroteSuccessfully){
                echo "Could not send the message<br/>";
            }
           // else {
               // echo "Successfully sent the message<br/>";

           // }
            fclose($connection);
        }
    }
}