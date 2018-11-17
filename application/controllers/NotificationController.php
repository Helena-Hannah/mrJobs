<?php
/**
 * Created by PhpStorm.
 * User: Helena Hannah George
 * Date: 17-07-2018
 * Time: 10:01
 */
defined('BASEPATH') OR exit('No direct script access allowed');
include_once 'application/libraries/REST_Controller.php';
include_once 'application/libraries/GCM.php';
include_once 'application/libraries/APNS.php';
include_once 'application/libraries/Notification.php';

class NotificationController extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("PurchaseModel");
        $this->load->model("UserModel");
        $this->load->model("CheckoutModel");
        $this->load->model("NotificationModel");
    }

    // send notification on a new discount arrival
    public function PromCodeNotification_post()
    {
        $requestParams = json_decode(file_get_contents('php://input'), true);
        if (!empty($requestParams)) {
            $user_lists = $this->UserModel->getAllActiveNotificUsers();
            // notification status
            $notification['subject'] = NEWPROMCODE;
            $msg = "You have been credited a new Promotional Code. Use " . $requestParams['discount_name'] . " for acheive this offer." . $requestParams['discount_desc'];
            $notification['discount_id'] = $requestParams['discount_id'];
            $message = $msg;
            $msgData['msg_content'] = $message;
            $msgId = $this->NotificationModel->addMsgDetails($msgData);
            foreach ($user_lists as $user_list) {
                $data['user_id'] = $user_list['user_id'];
                $data['type_id'] = $notification['discount_id'];
                $data['type'] = 'COUPON';
                $data['msg_content'] = $msgId;
                $data['status'] = 'Y';
                $this->NotificationModel->addLogDetails($data);
            }
            $notification_data = new Notification();
            $notification_data->sendNotification($user_lists, $message, $notification);
            $responseData = responseBuilder(NOTIFICATION_SENT, 200, REST_Controller::HTTP_OK);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $response;
        }
    }

    // send notification on a new category arrival
    public function ProductNotification_post()
    {
        $requestParams = json_decode(file_get_contents('php://input'), true);
        if (!empty($requestParams)) {
            $user_lists = $this->UserModel->getAllActiveNotificUsers();
            // notification status
            $notification['subject'] = NEWCATEGORY;
            $msg = "A new product " . $requestParams['product_name'] . " added to the " . $requestParams['category_name'] . " section.";
            $message = $msg;
            $msgData['msg_content'] = $message;
            $msgId = $this->NotificationModel->addMsgDetails($msgData);
            foreach ($user_lists as $user_list) {
                $data['user_id'] = $user_list['user_id'];
                $data['type_id'] = $requestParams['product_id'];
                $data['type'] = 'PRODUCT';
                $data['msg_content'] = $msgId;
                $data['status'] = 'Y';
                $this->NotificationModel->addLogDetails($data);
            }
            $notification_data = new Notification();
            $notification_data->sendNotification($user_lists, $message, $notification);
            $responseData = responseBuilder(NOTIFICATION_SENT, 200, REST_Controller::HTTP_OK);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $response;
        }
    }

    public function SendCustomMessage_post()
    {
        $requestParams = json_decode(file_get_contents('php://input'), true);

        if (!empty($requestParams)) {
            $notification['subject'] = NEWMESSAGE;
            $msg = $requestParams['message_desc'];
            $message = $msg;
            $users = $requestParams['user_lists'];
            $ids = array();
            foreach ($users as $user) {
                $user_lists = $this->UserModel->getAllSelectedUsers($user)[0];
                array_push($ids, $user_lists);
            }
            if (!empty($ids)) {
                $msgData['msg_content'] = $requestParams['message_desc'];
                $msgId = $this->NotificationModel->addMsgDetails($msgData);
                foreach ($ids as $id) {
                    $data['user_id'] = $id['user_id'];
                    $data['type_id'] = $requestParams['message_id'];
                    $data['type'] = 'COUSTOM';
                    $data['msg_content'] = $msgId;
                    $data['status'] = 'Y';
                    $this->NotificationModel->addLogDetails($data);
                }
                $notification_data = new Notification();
                $notification_data->sendNotification($ids, $message, $notification);
                $responseData = responseBuilder(NOTIFICATION_SENT, 200, REST_Controller::HTTP_OK);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $response;
            }
            $responseData = responseBuilder(FAILED, 400, REST_Controller::HTTP_OK);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $response;
        }
    }

    //Notification history of a user
    public function getMyNotificationHistory_post()
    {
        $requestParams = json_decode(file_get_contents('php://input'), true);
        $headerData = $this->input->get_request_header("Session-Token", true);

        if (!empty($requestParams)) {
            if (sessionCheck($headerData, $requestParams['user_id'])) {
                $validateStatus = $this->validateMyNotificationHistory($requestParams);
                if (!is_object($validateStatus)) {
                    $history = $this->NotificationModel->notficationHistoryofUser($requestParams['user_id'], $requestParams['start_index'], $requestParams['count']);
                    $historyCount = $this->NotificationModel->notificationCount($requestParams['user_id']);
                    $results['total'] = count($historyCount);
                    if (!empty($history) ? $results['data'] = $history : $results['data'] = array()) ;
                    $responseData = responseBuilder(LISTED, 200, REST_Controller::HTTP_OK, $results);
                    $response = $this->set_response($responseData['data'], $responseData['code']);
                    return $response;
                } else {
                    return $validateStatus;
                }

            } else {
                if ($requestParams['language_id'] == 1 ? $msg = INVALID_SESSION : $msg = INVALID_SESSION_AR) ;
                $responseData = responseBuilder($msg, 403, REST_Controller::HTTP_FORBIDDEN);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(MISSING_PARAMETER, 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }
    }

    //Remove notification of a user
    public function myNotificationRemove_post()
    {

        $requestParams = json_decode(file_get_contents('php://input'), true);
        $headerData = $this->input->get_request_header("Session-Token", true);
        if (!empty($requestParams)) {

            $validateStatus = $this->validatemyNotificationRemove($requestParams);
            if (!is_object($validateStatus)) {

                if (sessionCheck($headerData, $requestParams['user_id'])) {
                    $data['status'] = 'D';
                    $removed = $this->NotificationModel->removeNotification($requestParams['user_id'], $requestParams['notification_id'], $data);
                    if (!empty($removed)) {
                        if ($requestParams['language_id'] == 1 ? $msg = NOTIFICATION_REMOVED : $msg = NOTIFICATION_REMOVED_AR) ;
                        $responseData = responseBuilder($msg, 200, REST_Controller::HTTP_OK);
                        $response = $this->set_response($responseData['data'], $responseData['code']);
                        return $response;
                    } else {
                        $responseData = responseBuilder(FAILED, 400, REST_Controller::HTTP_OK);
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
        }
    }


    // send notification on stock updation of a product
    public function StockNotification_post()
    {
        $requestParams = json_decode(file_get_contents('php://input'), true);
       // print_r($requestParams);
        if (!empty($requestParams)) {
            $user_lists = $this->UserModel->getAllActiveStockUsers($requestParams['product_id']);
            // notification status
            $notification['subject'] = STOCKUPDATION;
            $msg = $requestParams['product_name'] . " is now available";
            $message = $msg;
            $msgData['msg_content'] = $message;
            $msgId = $this->NotificationModel->addMsgDetails($msgData);
            foreach ($user_lists as $user_list) {
                $data['user_id'] = $user_list['user_id'];
                $data['type_id'] = $requestParams['product_id'];
                $data['type'] = 'STOCK';
                $data['msg_content'] = $msgId;
                $data['status'] = 'Y';
                $this->NotificationModel->addLogDetails($data);
            }
            $notification_data = new Notification();
            $notification_data->sendNotification($user_lists, $message, $notification);
            $responseData = responseBuilder(NOTIFICATION_SENT, 200, REST_Controller::HTTP_OK);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $response;
        }
    }


    public function validatemyNotificationRemove($requestParams)
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

        if (array_key_exists("notification_id", $requestParams)) {
            if ($requestParams['notification_id'] == "") {
                $responseData = responseBuilder(EMPTY_REQUEST . "notification_id", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "notification_id.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }

        return true;
    }

    public function validateMyNotificationHistory($requestParams)
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
        if (array_key_exists("start_index", $requestParams)) {
            if ($requestParams['start_index'] == "") {
                $responseData = responseBuilder(EMPTY_REQUEST . "start index.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "start index.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }
        if (array_key_exists("count", $requestParams)) {
            if ($requestParams['count'] == "") {
                $responseData = responseBuilder(EMPTY_REQUEST . "count.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "count.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }
        return true;
    }
}