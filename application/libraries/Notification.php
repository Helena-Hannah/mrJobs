<?php

/**
 * @package    Pushnotification settings
 * @author     Rarish K
 * @copyright  (c) 2016-Codelynks
 * @version    1.0
 */
class Notification
{

    var $notificationUserList;
    var $id;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    public function sendNotification($userIds, $message = NULL, $data = NULL)
    {
        $this->CI->load->model('DeviceToken');
        $gcmList = array();
        $apnsList = array();
        if (!empty($userIds) || isset($userIds)) {
            if (is_array($userIds)) {
                foreach ($userIds as $id) {
                    $deviceType = $this->CI->DeviceToken->deviceType($id['user_id']);
                    if ($deviceType == 2) {
                        $gcmList[] = $id['user_id'];

                    } elseif ($deviceType == 1) {
                        $apnsList[] = $id['user_id'];
                    }
                }
            } else {
                if (trim($userIds) != "") {
                    $deviceType = $this->CI->DeviceToken->deviceType($userIds);
                    if ($deviceType == 2) {
                        $gcmList[] = $userIds;
                    } elseif ($deviceType == 1) {
                        $apnsList[] = $userIds;
                    }
                }
            }
        }

        // For GCM
        if (!empty($gcmList)) {
            $gcm = new GCM();
            $regRejectId = $this->CI->DeviceToken->getRegId(array_values($gcmList));

            $rejRejId = array_map(function ($i) {
                return $i['device_token'];
            }, $regRejectId);
            $gcm->setDevices($rejRejId);
            $gcm->send($message, $data);
        }

        // For APNS
        if (!empty($apnsList)) {
            $apn = new APNS();
            $regId = $this->CI->DeviceToken->getRegId(array_values($apnsList));
            $deiceToken = array_map(function ($i) {
                return $i['device_token'];
            }, $regId);
            $apn->setDevices($deiceToken);
            $apn->send($message, $data);
        }
    }


}
