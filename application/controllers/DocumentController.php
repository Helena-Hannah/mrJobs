<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 11-07-2018
 * Time: 15:31
 */
defined('BASEPATH') OR exit('No direct script access allowed');
include_once 'application/libraries/REST_Controller.php';

class DocumentController extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("UserModel");
        $this->load->model("CategoryModel");
        $this->load->model("ApplicationModel");
    }


    public function termsConditions_post()
    {
        $requestParams = json_decode(file_get_contents('php://input'), true);
        if (!empty($requestParams)) {

            $validateStatus = $this->validatetermsConditions($requestParams);
            if (!is_object($validateStatus)) {
                $getapplicationdetails = $this->ApplicationModel->getApplicationetail();
                if ($requestParams['language_id'] == '1') {
                    $data['terms_url'] = $getapplicationdetails[0]['url'];
                    $data['disclaimer'] = $getapplicationdetails[4]['url'];
                    $data['delivery'] = $getapplicationdetails[6]['url'];
                    $data['privacy'] = $getapplicationdetails[8]['url'];
                    $data['refund'] = $getapplicationdetails[10]['url'];
                } else {
                    $data['terms_url'] = $getapplicationdetails[3]['url'];
                    $data['disclaimer'] = $getapplicationdetails[5]['url'];
                    $data['delivery'] = $getapplicationdetails[7]['url'];
                    $data['privacy'] = $getapplicationdetails[9]['url'];
                    $data['refund'] = $getapplicationdetails[11]['url'];
                }
                $applicationsettings['applicationdetails'] = $data;
                if (!empty($getapplicationdetails)) {
                    $responseData = responseBuilder(LISTED_APPLICATION_SETTINGS, 200, REST_Controller::HTTP_OK, $applicationsettings);
                    $response = $this->set_response($responseData['data'], $responseData['code']);
                    return $response;
                } else {
                    $responseData = responseBuilder(SERVER_ERROR, 500, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                    $response = $this->set_response($responseData['data'], $responseData['code']);
                    return $response;
                }
            } else {
                return $validateStatus;
            }

        } else {
            $responseData = responseBuilder(MISSING_PARAMETER, 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }
    }

    //Submit Feedback API
    public function submitMyFeedback_post()
    {
        $requestParams = json_decode(file_get_contents('php://input'), true);
        $headerData = $this->input->get_request_header("Session-Token", true);

        if (!empty($requestParams)) {

            $validateStatus = $this->validatesubmitMyFeedback($requestParams);
            if (!is_object($validateStatus)) {

                if (sessionCheck($headerData, $requestParams['user_id'])) {

                    // get user info
                    $user_info = $this->UserModel->getUserInfo(null, $requestParams['user_id'], null);

                    //send Mail
                    $from_mail = $user_info['u_email'];
                    //$recipient = "fetch@support.in";
                    $recipient = "sruthy.m@codelynks.com";
                    $mailContent = "Subject" . ':' . $requestParams['title'];
                    $mailContent .= "<br/>";
                    $mailContent .= "Description" . ':' . $requestParams['description'];
                    $mailContent .= "<br/>";
                    $mailContent .= "<br/>";
                    $subject = "fetch Feedback";
                    send_fetch_feedback_mail($recipient, $subject, $mailContent, $from_mail, null);
                    if ($requestParams['language_id'] == 1 ? $msg = FEEDBACK_SUBMISSION : $msg = FEEDBACK_SUBMISSION_AR) ;
                    $responseData = responseBuilder($msg, 200, REST_Controller::HTTP_OK);
                    $response = $this->set_response($responseData['data'], $responseData['code']);
                    return $this->response;

                } else {
                    if ($requestParams['language_id'] == 1 ? $msg = INVALID_SESSION : $msg = INVALID_SESSION_AR) ;
                    $responseData = responseBuilder($msg, 403, REST_Controller::HTTP_FORBIDDEN);
                    $response = $this->set_response($responseData['data'], $responseData['code']);
                    return $this->response;
                }
            } else {
                return $validateStatus;
            }

        } else {
            $responseData = responseBuilder(MISSING_PARAMETER, 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }
    }

    //Submit Contct API
    public function submitContactForm_post()
    {
        $requestParams = json_decode(file_get_contents('php://input'), true);

        if (!empty($requestParams)) {

            $validateStatus = $this->validatesubmitContactForm($requestParams);
            if (!is_object($validateStatus)) {

                    // get user info
                    $user_info = $this->UserModel->getUserInfo(null, $requestParams['user_id'], null);

                    //send Mail
                    $from_mail = $user_info['u_email'];
                    //$recipient = "fetch@support.in";
                    $recipient = "helena.g@codelynks.com";
                    $mailContent = "You have a new contact request from " . ':' . $requestParams['u_name'].". Follow them through  " . ':' . $requestParams['u_email'];
                    $mailContent .= "<br/>";
                   // $mailContent = ". Follow them through  " . ':' . $requestParams['u_email'];
                   // $mailContent .= "<br/>";
                    $mailContent .= "Message" . ':' . $requestParams['u_message'];
                    $mailContent .= "<br/>";
                    $mailContent .= "<br/>";
                    $subject = "fetch Contact Request";
                    send_fetch_feedback_mail($recipient, $subject, $mailContent, $from_mail, null);
                    if ($requestParams['language_id'] == 1 ? $msg = CONTACT_SUBMISSION : $msg = CONTACT_SUBMISSION_AR) ;
                    $responseData = responseBuilder($msg, 200, REST_Controller::HTTP_OK);
                    $response = $this->set_response($responseData['data'], $responseData['code']);
                    return $this->response;
            } else {
                return $validateStatus;
            }

        } else {
            $responseData = responseBuilder(MISSING_PARAMETER, 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }
    }

    public function validatetermsConditions($requestParams)
    {
        if (array_key_exists("language_id", $requestParams)) {
            if ($requestParams['language_id'] == "") {
                $responseData = responseBuilder(EMPTY_REQUEST . "language id.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
            $languageDetails = $this->UserModel->getLanguageInfo($requestParams['language_id']);
            if (empty($languageDetails)) {
                $responseData = responseBuilder(INVALID_LANGUAGE_ID, 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "language id.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }
        return true;
    }


    public function validatesubmitMyFeedback($requestParams)
    {
        if (array_key_exists("user_id", $requestParams)) {
            if ($requestParams['user_id'] == "") {
                $responseData = responseBuilder(EMPTY_REQUEST . "user id.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
            $userDetails = $this->UserModel->getUserInfo(null, $requestParams['user_id'], null);
            if (empty($userDetails)) {
                $responseData = responseBuilder(INVALID_USER_ID, 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "user id.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }
        if (array_key_exists("language_id", $requestParams)) {
            if ($requestParams['language_id'] == "") {
                $responseData = responseBuilder(EMPTY_REQUEST . "language id.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
            $languageDetails = $this->UserModel->getLanguageInfo($requestParams['language_id']);
            if (empty($languageDetails)) {
                $responseData = responseBuilder(INVALID_LANGUAGE_ID, 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "language id.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }
        return true;
    }

    public function validatesubmitContactForm($requestParams)
    {
        if (array_key_exists("language_id", $requestParams)) {
            if ($requestParams['language_id'] == "") {
                $responseData = responseBuilder(EMPTY_REQUEST . "language id.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
            $languageDetails = $this->UserModel->getLanguageInfo($requestParams['language_id']);
            if (empty($languageDetails)) {
                $responseData = responseBuilder(INVALID_LANGUAGE_ID, 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "language id.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }
        if (array_key_exists("u_email", $requestParams)) {
            if ($requestParams['u_email'] == "") {
                $responseData = responseBuilder(EMPTY_REQUEST . "u_email.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "u_email.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }
        if (array_key_exists("u_name", $requestParams)) {
            if ($requestParams['u_name'] == "") {
                $responseData = responseBuilder(EMPTY_REQUEST . "u_name.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "u_name.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }
        if (array_key_exists("u_message", $requestParams)) {
            if ($requestParams['u_message'] == "") {
                $responseData = responseBuilder(EMPTY_REQUEST . "u_message.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "u_message.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }
        return true;
    }
}