<?php

/**
 * @package    Device token settings
 * @author     Rarish K
 * @copyright  (c) 2016-Codelynks
 * @version    1.0
 */
class DeviceToken extends CI_Model
{

    public function deviceType($userId)
    {
        try {
            $this->db->select("u_platform");
            $this->db->from("user_settings");
            $this->db->where(array("user_id" => $userId));
            $res = $this->db->get()->row('u_platform');
            return $res;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function getDeviceTokens($userId)
    {
        try {
            $this->db->select("device_token");
            if (is_array($userId)) {
                $this->db->where_in("user_id", $userId);
            } else {
                $this->db->where(array("user_id" => $userId));
            }
            $query = $this->db->get("user_settings");
            $regId = $query->result_array();
            if (!empty($regId)) {
                return $regId;
            } else {
                return false;
            }
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function getRegId($userId)
    {
        try {
            $this->db->select("device_token");
            if (is_array($userId)) {
                $this->db->where_in("user_id", $userId);
            } else {
                $this->db->where("user_id", $userId);
            }

            $query = $this->db->get("user_settings");
            $regId = $query->result_array();
            if (!empty($regId)) {
                return $regId;
            } else {
                return false;
            }
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    //update device token
    public function addDevice($userId, $data)
    {
        try {
            $this->db->where('user_id', $userId);
            $result = $this->db->update('user_settings', $data);
            if ($result) {
                return $result;
            }
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

}
