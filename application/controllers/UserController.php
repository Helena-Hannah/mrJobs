<?php
/**
 * Created by PhpStorm.
 * User: Hannah Helena George
 * Date: 22-06-2018
 * Time: 12:19
 */
defined('BASEPATH') OR exit('No direct script access allowed');
include_once 'application/libraries/REST_Controller.php';

class UserController extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("UserModel");
        $this->load->model("JobCategoryModel");
    }


    /* -------- User SignUp --------- */

    public function signUp_post()
    {
        $requestParams = json_decode(file_get_contents('php://input'), true);

        if (!empty($requestParams)) {

            $validateStatus = $this->validateSignUp($requestParams);
            if (!is_object($validateStatus)) {


            } else {
                return $validateStatus;
            }
        } else {
            $responseData = responseBuilder(MISSING_PARAMETER, 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }
    }

    /* -------- User Login --------- */

    public function signIn_post()
    {
        $requestParams = json_decode(file_get_contents('php://input'), true);
        //   print_r($requestParams);
        //   die;
        if (!empty($requestParams)) {

            $validateStatus = $this->validateSignIn($requestParams);
            if (!is_object($validateStatus)) {

                $get_userID = $this->UserModel->loginCheck($requestParams);

                if (!is_array($get_userID)) {
                    $resultnot['u_platform'] = $requestParams['u_platform'];
                    $resultnot['device_token'] = $requestParams['device_token'];
                    $this->UserModel->editUserSettings($resultnot, $get_userID);
                    $userDetails = $this->UserModel->getUserInfo(null, $get_userID, null);
                    $user_settings = $this->UserModel->getMySettings($userDetails['user_id']);
                    $result = (array)$userDetails;

                    //Getting category details
                    $categories = $this->UserModel->getMyCategoryDetails($userDetails['user_id']);
                    $category_ids = explode(",", $categories['category_id']);
                    $category = array();
                    foreach ($category_ids as $category_id) {
                        $categories = $this->CategoryModel->getCategorysInfo($category_id);
                        array_push($category, $categories[0]);
                        $result['categories'] = $category;
                    }
                    $categories = $this->UserModel->getMyLocationyDetails($userDetails['user_id']);
                    $result['location_id'] = $categories['location_id'];
                    $result['location_name'] = $categories['location_name'];
                    !empty($user_settings['language_id']) ? $result['language_id'] = $user_settings['language_id'] : "";
                    unset($userDetails['password']);

                    $sessionToken = $this->UserModel->getAccessToken($result);
                    $token_info = $sessionToken;
                    $token_info['device_token'] = $user_settings['device_token'];;
                    unset($sessionToken['u_platform'], $resultnot['device_token']);

                    $userInfo = $token_info;
                    $userInfo['user'] = $result;

                    $responseData = responseBuilder(SUCCESS, 200, REST_Controller::HTTP_OK, $userInfo);
                    $response = $this->set_response($responseData['data'], $responseData['code']);
                    return $response;

                } else {
                    $responseData = responseBuilder($get_userID[0], 400, REST_Controller::HTTP_BAD_REQUEST);
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


    /* -------- Forgot Password --------- */

    public function forgotPassword_post()
    {
        $requestParams = json_decode(file_get_contents('php://input'), true);
        if (!empty($requestParams)) {
            $validateStatus = $this->validateforgotPassword($requestParams);
            if (!is_object($validateStatus)) {
                $this->load->library('email');

                $user_status = $this->UserModel->getUserInfo($requestParams['u_email'], NULL, NULL);

                if (is_array($user_status)) {

                    $linkId = hash("md5", $requestParams['u_email'] . time(), false);
                    $data['pass_val_string'] = $linkId;
                    $data['expiry_date'] = date("Y-m-d H:i:s");

                    $this->UserModel->addForgotPwdLink($data, $user_status['user_id']);
                    $base_Url = $this->config->base_url();

                    $sendTo = $requestParams['u_email'];
                    $subject = "Password Change";
                    $headers = "From: " . 'fetch - Password Change';
                    $headers .= "<" . FromEmail . ">\r\n";
                    $headers .= "Reply-To: " . FromEmail . "\r\n";
                    $headers .= "Return-Path: " . FromEmail;

                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

                    $message = "<p>Hi " . $requestParams['u_email'] . ",</p>";
                    $message .= "<p>You recently requested to reset your password for fetch. <p>
 Please " . "<a href='" . $base_Url . "index.php/UserController/resetUserPassword/" .
                        $linkId . "/" . base64_encode($user_status['user_id']) . "'>click here </a> 
 to create new password.</p>";

                    $message .= "<br/><p>If you did not request this, please ignore this email. 
The link is valid for only 24 hr.</p>";

                    $message .= "<br/><p>Thanks</p>";
                    $message .= "<br/><br/><p>fetch Team</p>";

                    mail($sendTo, $subject, $message, $headers);
                }
                if ($requestParams['language_id'] == 1 ? $msg = RESET_PASSWORD_SENT : $msg = RESET_PASSWORD_SENT_AR) ;
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

    /* --------reset password--------- */

    public function resetUserPassword_get()
    {
        $key = $this->uri->segment(3);
        $userId = base64_decode($this->uri->segment(4));
        //check for the values
        $res = $this->UserModel->checkTempPassword($key, $userId);
        $data = array("res" => $res, "id" => $userId, "msg" => "");

        if ($res)
            $this->load->view('resetpwd', $data);
        else
            $this->load->view('resetpwd', $data);
    }

    public function resetPasswordAction_post()
    {
        $userDetails = $this->input->post();
        $res = $this->UserModel->resetPassword($userDetails);
        $data = array("res" => 0, "id" => $userDetails['id'], "msg" => "Updated the password");
        if ($res) {
            $set_view = $this->load->view('forgot_success', $data, TRUE);
            echo json_encode($set_view);
        } else {
            $this->load->view('resetpwd', $data);
        }
    }

    /* --------- Edit User Profile --------- */

    public function editProfile_post()
    {
        $requestParams = json_decode(file_get_contents('php://input'), true);
        $headerData = $this->input->get_request_header("Session-Token", true);

        if (!empty($requestParams)) {

            $validateStatus = $this->validateEditProfile($requestParams);
            if (!is_object($validateStatus)) {

                if (sessionCheck($headerData, $requestParams['user_id'])) {
                    $data = array();
                    //if (!empty($requestParams['u_Name']))
                    // $data['u_Name'] = $requestParams['u_Name'];
                    if (!empty($requestParams['u_address1']))
                        $data['u_address1'] = $requestParams['u_address1'];
                    if (!empty($requestParams['u_address2']))
                        $data['u_address2'] = $requestParams['u_address2'];
                    if (!empty($requestParams['u_dob']))
                        $data['u_dob'] = $requestParams['u_dob'];
                    if (!empty($requestParams['u_mobile']))
                        $data['u_mobile'] = $requestParams['u_mobile'];
                    $category_id = array();
                    foreach ($requestParams['category_id'] as $value)
                        array_push($category_id, $value);
                    $data['category_id'] = implode(",", $category_id);
                    if (!empty($requestParams['location_id']))
                        $data['location_id'] = $requestParams['location_id'];
                    $edit_status = $this->UserModel->editUserProfile($data, $requestParams['user_id']);

                    if (!empty($edit_status)) {

                        $userDetails = $this->UserModel->getUserInfo(null, $requestParams['user_id'], null);
                        $user_settings = $this->UserModel->getMySettings($userDetails['user_id']);
                        $result['user_id'] = $userDetails['user_id'];
                        $result['u_email'] = $userDetails['u_email'];
                        // $result['u_Name'] = $userDetails['u_Name'];
                        $result['u_address1'] = $userDetails['u_address1'];
                        $result['u_address2'] = $userDetails['u_address2'];
                        $result['u_dob'] = $userDetails['u_dob'];
                        $result['u_mobile'] = $userDetails['u_mobile'];
                        $result['u_platform'] = $user_settings['u_platform'];
                        $result['language_id'] = $user_settings['language_id'];
                        //Getting category details
                        $categories = $this->UserModel->getMyCategoryDetails($userDetails['user_id']);
                        $category_ids = explode(",", $categories['category_id']);
                        $category = array();
                        foreach ($category_ids as $key => $category_id) {
                            $categories = $this->CategoryModel->getCategorysInfo($category_id);
                            array_push($category, $categories[0]);
                            $result['categories'] = $category;
                        }
                        $categories = $this->UserModel->getMyLocationyDetails($userDetails['user_id']);
                        $result['location_id'] = $categories['location_id'];
                        $result['location_name'] = $categories['location_name'];
                        unset($userDetails['password']);
                        unset($result['device_token']);

                        $userInfo['user'] = $result;
                        if ($requestParams['language_id'] == 1 ? $msg = EDIT_PROFILE : $msg = EDIT_PROFILE_AR) ;
                        $responseData = responseBuilder($msg, 200, REST_Controller::HTTP_OK, $userInfo);
                        $response = $this->set_response($responseData['data'], $responseData['code']);
                        return $this->response;

                    } else {
                        $responseData = responseBuilder(FAILED, 400, REST_Controller::HTTP_BAD_REQUEST);
                        $response = $this->set_response($responseData['data'], $responseData['code']);
                        return $this->response;
                    }


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

    /* --------- Update User Session token --------- */


    /* ------- Get My Notification Settings ------- */

    public function getMySettings_post()
    {
        $requestParams = json_decode(file_get_contents('php://input'), true);
        $headerData = $this->input->get_request_header("Session-Token", true);

        if (!empty($requestParams)) {

            $validateStatus = $this->validateMyNotificationSettings($requestParams);
            if (!is_object($validateStatus)) {

                if (sessionCheck($headerData, $requestParams['user_id'])) {

                    $user_status = $this->UserModel->getUserInfo(null, $requestParams['user_id'], null);

                    if (!empty($user_status)) {

                        $result['notification_status'] = $user_status['notification_settings'];

                        $responseData = responseBuilder(SUCCESS, 200, REST_Controller::HTTP_OK, $result);
                        $response = $this->set_response($responseData['data'], $responseData['code']);
                        return $this->response;
                    }
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


    /* ------- Update Notification Settings ------- */

    public function changeMySettings_post()
    {
        $requestParams = json_decode(file_get_contents('php://input'), true);
        $headerData = $this->input->get_request_header("Session-Token", true);

        if (!empty($requestParams)) {

            $validateStatus = $this->validatechangeMySettings($requestParams);
            if (!is_object($validateStatus)) {

                if (sessionCheck($headerData, $requestParams['user_id'])) {

                    $requestParams['notification'] == "Y" ? $data['notification_settings'] = "Y" : $data['notification_settings'] = "N";
                    if (!empty($requestParams['device_token']))
                        $data['device_token'] = $requestParams['device_token'];
                    $notification_status = $this->UserModel->editUserSettings($data, $requestParams['user_id']);
                    if (!empty($notification_status)) {
                        if ($requestParams['language_id'] == 1 ? $msg = UPDATED_USER_SETTINGS : $msg = UPDATED_USER_SETTINGS_AR) ;
                        $responseData = responseBuilder($msg, 200, REST_Controller::HTTP_OK);
                        $response = $this->set_response($responseData['data'], $responseData['code']);
                        return $this->response;
                    }

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


    public function signOut_post()
    {
        $requestParams = json_decode(file_get_contents('php://input'), true);
        $headerData = $this->input->get_request_header("Session-Token", true);

        if (!empty($requestParams)) {

            $validateStatus = $this->validateSignOut($requestParams);
            if (!is_object($validateStatus)) {

                if (sessionCheck($headerData, $requestParams['user_id'])) {

                    $data['session_token'] = "";
                    $data['refresh_token'] = "";

                    $signOut_status = $this->UserModel->editUserSettings($data, $requestParams['user_id']);

                    if (!empty($signOut_status)) {
                        if ($requestParams['language_id'] == 1 ? $msg = LOGGEDOUT : $msg = LOGGEDOUT_AR) ;
                        $responseData = responseBuilder($msg, 200, REST_Controller::HTTP_OK);
                        $response = $this->set_response($responseData['data'], $responseData['code']);
                        return $response;

                    } else {
                        $responseData = responseBuilder(FAILED, 400, REST_Controller::HTTP_BAD_REQUEST);
                        $response = $this->set_response($responseData['data'], $responseData['code']);
                        return $response;
                    }

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

    // ---------- Validations ---------- //

    public function validateSignUp($requestParams)
    {

        if (array_key_exists("user_type", $requestParams)) {
            if ($requestParams['user_type'] == "") {
                $responseData = responseBuilder(EMPTY_REQUEST . "user_type.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            } elseif (!in_array($requestParams['user_type'], array("Employee", "Employer"))) {
                $responseData = responseBuilder(VALID_OPTIONS . "user_type.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "user_type.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }
        if (array_key_exists("u_email", $requestParams)) {
            if ($requestParams['u_email'] == "") {
                $responseData = responseBuilder(EMPTY_REQUEST . "email address.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "u_email.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }
        if (!empty($requestParams['u_email'])) {

            $email_status = $this->UserModel->getUserEmailStatus($requestParams['u_email'], null, null);

            if (!empty($email_status)) {
                $responseData = responseBuilder(EMAIL_EXISTS, 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        }
        if (array_key_exists("u_password", $requestParams)) {
            if ($requestParams['u_password'] == "") {
                $responseData = responseBuilder(EMPTY_REQUEST . "password.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "u_password.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }

        if (!empty($requestParams['u_mobile'])) {
            $mobile_status = $this->UserModel->getUserInfo(null, null, $requestParams['u_mobile']);
            if (!empty($mobile_status)) {
                $responseData = responseBuilder(MOBILE_EXISTS, 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        }
        if (array_key_exists("u_mobile", $requestParams)) {
            if ($requestParams['u_mobile'] == "") {
                $responseData = responseBuilder(EMPTY_REQUEST . "mobile number.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "u_mobile.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }

        if (array_key_exists("u_platform", $requestParams)) {
            if ($requestParams['u_platform'] == "") {
                $responseData = responseBuilder(MISSING_ID . "u_platform.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "u_platform.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }
        if (array_key_exists("u_address1", $requestParams)) {
            if ($requestParams['u_address1'] == "") {
                $responseData = responseBuilder(MISSING_ID . "u_address1.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "u_address1.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }
        if ($requestParams['user_type'] == 'Employer') {
            if (array_key_exists("vacancy_count", $requestParams)) {
                if ($requestParams['u_dob'] == "") {
                    $responseData = responseBuilder(EMPTY_REQUEST . "vacancy_count.", 400, REST_Controller::HTTP_BAD_REQUEST);
                    $response = $this->set_response($responseData['data'], $responseData['code']);
                    return $this->response;
                }
            } else {
                $responseData = responseBuilder(REQUEST_MISSING . "vacancy_count.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
            if (array_key_exists("salary_range", $requestParams)) {
                if ($requestParams['salary_range'] == "") {
                    $responseData = responseBuilder(EMPTY_REQUEST . "vacancy_count.", 400, REST_Controller::HTTP_BAD_REQUEST);
                    $response = $this->set_response($responseData['data'], $responseData['code']);
                    return $this->response;
                }
            } else {
                $responseData = responseBuilder(REQUEST_MISSING . "vacancy_count.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
            if (array_key_exists("job_post", $requestParams)) {
                if ($requestParams['job_post'] == "") {
                    $responseData = responseBuilder(EMPTY_REQUEST . "job_post.", 400, REST_Controller::HTTP_BAD_REQUEST);
                    $response = $this->set_response($responseData['data'], $responseData['code']);
                    return $this->response;
                }
            } else {
                $responseData = responseBuilder(REQUEST_MISSING . "job_post.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        }
        if ($requestParams['user_type'] == 'Employee') {
            if (array_key_exists("u_dob", $requestParams)) {
                if ($requestParams['u_dob'] == "") {
                    $responseData = responseBuilder(EMPTY_REQUEST . "date of birth.", 400, REST_Controller::HTTP_BAD_REQUEST);
                    $response = $this->set_response($responseData['data'], $responseData['code']);
                    return $this->response;
                }
            } else {
                $responseData = responseBuilder(REQUEST_MISSING . "u_dob.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
            if (array_key_exists("adhar_number", $requestParams)) {
                if ($requestParams['adhar_number'] == "") {
                    $responseData = responseBuilder(EMPTY_REQUEST . "adhar_number.", 400, REST_Controller::HTTP_BAD_REQUEST);
                    $response = $this->set_response($responseData['data'], $responseData['code']);
                    return $this->response;
                }
            } else {
                $responseData = responseBuilder(REQUEST_MISSING . "adhar_number.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        }
        if (array_key_exists("job_post", $requestParams)) {
            if ($requestParams['job_post'] == "") {
                $responseData = responseBuilder(EMPTY_REQUEST . "job_post.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "job_post.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }
        if (array_key_exists("sex", $requestParams)) {
            if ($requestParams['sex'] == "") {
                $responseData = responseBuilder(EMPTY_REQUEST . "sex.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "sex.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }
        if (array_key_exists("highest_qualification", $requestParams)) {
            if ($requestParams['highest_qualification'] == "") {
                $responseData = responseBuilder(EMPTY_REQUEST . "highest_qualification.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "highest_qualification.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }
        if (array_key_exists("passout_year", $requestParams)) {
            if ($requestParams['passout_year'] == "") {
                $responseData = responseBuilder(EMPTY_REQUEST . "passout_year.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "passout_year.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }
        if (array_key_exists("reference_number", $requestParams)) {
            if ($requestParams['reference_number'] == "") {
                $responseData = responseBuilder(EMPTY_REQUEST . "reference_number.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "reference_number.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }
        return true;
    }

    public function validateSignIn($requestParams)
    {

        if (array_key_exists("u_email", $requestParams)) {
            if ($requestParams['u_email'] == "") {
                $responseData = responseBuilder(EMPTY_REQUEST . "name/email address.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "u_email.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }

        if (array_key_exists("u_password", $requestParams)) {
            if ($requestParams['u_password'] == "") {
                $responseData = responseBuilder(EMPTY_REQUEST . "password.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "u_password.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }

        if (array_key_exists("u_platform", $requestParams)) {
            if ($requestParams['u_platform'] == "") {
                $responseData = responseBuilder(MISSING_ID . "u_platform.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "u_platform.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }

        return true;
    }


    public function validateEditProfile($requestParams)
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
        /*   if (array_key_exists("category_id", $requestParams)) {
               if ($requestParams['category_id'] == "") {
                   $responseData = responseBuilder(EMPTY_REQUEST . "category id.", 400, REST_Controller::HTTP_BAD_REQUEST);
                   $response = $this->set_response($responseData['data'], $responseData['code']);
                   return $this->response;
               }
               $productDetails = $this->CategoryModel->getCategorysInfo($requestParams['category_id']);
               if (empty($productDetails)) {
                   $responseData = responseBuilder(INVALID_CATEGORY_ID, 400, REST_Controller::HTTP_BAD_REQUEST);
                   $response = $this->set_response($responseData['data'], $responseData['code']);
                   return $this->response;
               }
           } else {
               $responseData = responseBuilder(REQUEST_MISSING . "category_id.", 400, REST_Controller::HTTP_BAD_REQUEST);
               $response = $this->set_response($responseData['data'], $responseData['code']);
               return $this->response;
           }*/
        if (array_key_exists("location_id", $requestParams)) {
            if ($requestParams['location_id'] == "") {
                $responseData = responseBuilder(EMPTY_REQUEST . "location id.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
            $productDetails = $this->LocationModel->getLocationInfo($requestParams['location_id']);
            if (empty($productDetails)) {
                $responseData = responseBuilder(INVALID_LOCATION_ID, 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "location_id.", 400, REST_Controller::HTTP_BAD_REQUEST);
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


    public function validateforgotPassword($requestParams)
    {
        if (array_key_exists("u_email", $requestParams)) {
            if ($requestParams['u_email'] == "") {
                $responseData = responseBuilder(EMPTY_REQUEST . "user email.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
            $userDetails = $this->UserModel->getUserInfo($requestParams['u_email'], null, null);
            if (empty($userDetails)) {
                if ($requestParams['language_id'] == 1 ? $msg = EMAIL_NOT_EXISTS : $msg = EMAIL_NOT_EXISTS_AR) ;
                $responseData = responseBuilder($msg, 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "u_email.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }

        return true;
    }

    public function validateMyNotificationSettings($requestParams)
    {
        // User status
        $user_status = $this->UserModel->getUserInfo(null, $requestParams['user_id'], null);
        if (empty($user_status)) {
            $responseData = responseBuilder(INVALID_USER_ID, 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }

        return true;
    }

    public function validatechangeMySettings($requestParams)
    {
        // User status
        $user_status = $this->UserModel->getUserInfo(null, $requestParams['user_id'], null);
        if (empty($user_status)) {
            $responseData = responseBuilder(INVALID_USER_ID, 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }

        if (array_key_exists("notification", $requestParams)) {
            if ($requestParams['notification'] == "") {
                $responseData = responseBuilder(EMPTY_REQUEST . "notification", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "notification.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }

        return true;
    }

    public function validateSignOut($requestParams)
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
            $responseData = responseBuilder(REQUEST_MISSING . "user_id.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }

        return true;
    }
}