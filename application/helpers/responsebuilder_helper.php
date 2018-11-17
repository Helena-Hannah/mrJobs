<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function responseBuilder($message, $responseCode, $httpResponse, $data = NULL, $error = NULL, $error_message = NULL) {
    $responseDataArray = array();
    if ($error) {
        $responseDataArray['data'] = array(
            'error_code' => $responseCode,
            'error' => $error,
            'error_description' => $error_message
        );
    } else {
        $responseArray = array();
        if ($data) {
            $responseArray = $data;
            $responseArray['statusCode'] = $responseCode;
            $responseArray['statusMessage'] = $message;
        } else {
            $responseArray['statusCode'] = $responseCode;
            $responseArray['statusMessage'] = $message;
        }
        $responseDataArray['data'] = $responseArray;
    }
    $responseDataArray['code'] = $httpResponse;
    return $responseDataArray;
}
