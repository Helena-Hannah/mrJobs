<?php

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 22-06-2018
 * Time: 14:38
 */
class UserModel extends CI_Model
{
// SignUp User Profile

    public function signUp($userDetails)
    {
        try {
            $res = $this->db->insert('user_profile', $userDetails);
            if ($res) {
                return $this->db->insert_id();
            } else {
                return false;
            }
        } catch (Exception $exc) {
            throw new Exception("Database error occured");
        }
    }

    public function getAccessToken($userDetails)
    {
        $session_token = hash("md5", $userDetails['u_email'] . "-" . $userDetails['u_mobile'] . "-" . time(), false);

        try {
            //check user already exists
            $this->db->select(array("user_id", "refresh_token"));
            $this->db->from("user_settings");
            $this->db->where(array('user_id' => $userDetails['user_id']));
            $tokenCheckStatusRes = $this->db->get()->result_array();

            $data = array();
            $data['session_token'] = $session_token;
            if (isset($tokenCheckStatusRes[0]['refresh_token']) && $tokenCheckStatusRes[0]['refresh_token'] != '') {
                $data['refresh_token'] = $tokenCheckStatusRes[0]['refresh_token'];
            } else {
                $refresh_token = hash("md5", $userDetails['u_mobile'] . "-" . date("Y-M-D h:i:s"), false);
                $data['refresh_token'] = $refresh_token;
            }
            $data['u_platform'] = $userDetails['u_platform'];
            $data['device_token'] = $userDetails['device_token'];
            $data['user_id'] = $userDetails['user_id'];
            $data['last_updated_date'] = date("Y-m-d H:i:s");
            if (empty($tokenCheckStatusRes)) {
                $res = $this->db->insert('user_settings', $data);
                if ($res) {
                    return array("session_token" => $session_token, "refresh_token" => $data['refresh_token'],
                        "device_token" => $userDetails['device_token']);
                } else {
                    return false;
                }
            } else {
                $data['u_platform'] = $userDetails['u_platform'];
                $data['device_token'] = $userDetails['device_token'];
                // unset($data['member_id']);
                $this->db->where('user_id', $userDetails['user_id']);
                $updateStatus = $this->db->update('user_settings', $data);
                if ($updateStatus) {
                    return array("session_token" => $session_token, "refresh_token" => $data['refresh_token'], "device_token" => $userDetails['device_token']);
                } else {
                    return false;
                }

            }
        } catch (Exception $exc) {
            //Log error
            throw new Exception("Database error occured");
        }
    }

    // Get user info

    public function getUserInfo($email, $userId, $phone)
    {
        $this->db->select(array('user_profile.id as user_id', 'user_profile.u_email',
            'user_profile.u_Name', 'user_profile.u_mobile',
            "IFNULL(user_profile.u_dob,'')as u_dob", "IFNULL(user_profile.u_address,'')as u_address",
            "case when (user_profile.u_profilePicURL IS NULL) THEN '" . base_url() . defaultImage . "'
	             when (user_profile.u_profilePicURL!='') THEN CONCAT('" . base_url() . "project_img/user_profile/',u_profilePicURL)
                 END as u_profilePicURL", 'user_profile.created_date', 'user_profile.last_updated',
            'user_settings.u_platform', 'user_settings.device_token',

            'IFNULL(user_settings.notification_settings,"")as notification_settings'));
        $this->db->from("user_profile");
        if (!empty($email)) {
            $this->db->where(array('user_profile.u_email' => $email));
        } elseif (!empty($userId)) {
            $this->db->where(array('user_profile.id' => $userId));
        } elseif (!empty($phone)) {
            $this->db->where(array('user_profile.u_mobile' => $phone));
        }
        $this->db->join('user_settings', 'user_settings.user_id = user_profile.id');
        $res = $this->db->get()->result_array();
        if (count($res)) {
            return $res[0];
        } else {
            return false;
        }
    }


    //get user status
    public function getUserEmailStatus($email, $userId, $phone)
    {
        $this->db->select(array('id', 'u_email'));
        $this->db->from("user_profile");
        if (!empty($email)) {
            $this->db->where(array('u_email' => $email));
        } elseif (!empty($userId)) {
            $this->db->where(array('u_mobile' => $phone));
        } elseif (!empty($phone)) {
            $this->db->where(array('id' => $userId));
        }
        $res = $this->db->get()->result_array();
        if (count($res)) {
            return $res[0];
        } else {
            return false;
        }
    }

    // User Login

    public function loginCheck($userDetails)
    {
        try {
            $this->db->select("id");
            $this->db->from("user_profile");
            $this->db->where(array("u_email" => $userDetails['u_email']));
            $res = $this->db->get()->result_array();
            if ($res) {
                if (!empty($userDetails['u_password'])) {
                    $this->db->select("id");
                    $this->db->from("user_profile");
                    $this->db->where(array("u_email" => $userDetails['u_email'], "u_password" => md5($userDetails['u_password'])));
                    $res = $this->db->get()->result_array();
                    if ($res) {
                        return $res[0]['id'];
                    } else {

                        return array("Sorry, your password was incorrect. Please double-check your password.");
                    }
                }
            } else {

                return array("The mail id you entered doesn't belong to an account. Please check your mail id and try again.");
            }
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    // Check user Token

    public function checkUserSession($accessToken, $userId)
    {
        $this->db->select();
        $this->db->from('user_settings');
        $this->db->where(array('session_token' => $accessToken, 'user_id' => $userId));
        $res = $this->db->get()->result_array();
        if (count($res)) {
            return true;
        } else {
            return false;
        }
    }

    // get user settings details

    public function getMySettings($userId)
    {
        $this->db->select(array('device_token', 'notification_settings', 'IFNULL(u_platform,"")as u_platform', 'IFNULL(language_id,"")as language_id', 'temp_password', 'last_updated_date'));
        $this->db->from("user_settings");
        $this->db->where(array('user_id' => $userId));
        $res = $this->db->get()->result_array();
        if (count($res)) {
            return $res[0];
        } else {
            return false;
        }
    }

    // Get password

    public function getMyPassword($user_id, $password)
    {
        $this->db->select();
        $this->db->from('user_profile');
        $this->db->where(array('u_password LIKE' => $password, 'id' => $user_id));
        $query = $this->db->get();
        $res = $query->result();
        if (!empty($res)) {
            $userDetails = json_decode(json_encode($res[0]), true);
            return $userDetails;
        } else {
            return false;
        }
    }

    // update user profile

    public function editUserProfile($userDetails, $id)
    {
        try {
            $this->db->where(array('id' => $id));
            $res = $this->db->update('user_profile', $userDetails);
            return $res;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    // update user settings

    public function editUserSettings($userDetails, $id)
    {
        try {
            $this->db->where('user_id', $id);
            $res = $this->db->update('user_settings', $userDetails);
            return $res;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    // Set forgot password

    public function addForgotPwdLink($data, $userId)
    {
        try {
            $this->db->where('id', $userId);
            $result = $this->db->update('user_profile', $data);
            if ($result) {
                return $result;
            }
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function checkTempPassword($key, $userId)
    {
        $where = "id=" . $userId . " AND pass_val_string ='" . $key . "' and expiry_date >   now( ) - INTERVAL 2 HOUR AND pass_val_string !=''";
        $this->db->select('id');
        $this->db->where($where);
        $query = $this->db->get("user_profile");
        $res = $query->result();
        return $res;
    }

    // Reset Password

    public function resetPassword($userData)
    {
        try {
            $userData['password'] = $userData['password'];
            $data = array(
                "u_password" => md5($userData['password']),
            );
            $this->db->where('id', $userData['id']);
            $updateStatus = $this->db->update('user_profile', $data);
            if ($updateStatus) {
                $dataTempPassword = array(
                    "pass_val_string" => null,
                );
                $this->db->where('id', $userData['id']);
                $updateTempStatus = $this->db->update('user_profile', $dataTempPassword);
                return true;
            } else {
                return false;
            }
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    //Get all active notification users list

    public function getAllActiveNotificUsers()
    {
        $this->db->select(array('user_profile.u_email', 'user_profile.id as user_id', 'user_profile.u_Name'));
        $this->db->from('user_profile');
        $this->db->join('user_settings', 'user_settings.user_id = user_profile.id');
        $this->db->where(array('user_settings.notification_settings' => 'Y'));
        $res = $this->db->get()->result_array();
        if (!empty($res)) {
            return $res;
        } else {
            return false;
        }
    }

    public function getAllSelectedUsers($userID)
    {
        $this->db->select(array('user_profile.u_email', 'user_profile.id as user_id', 'user_profile.u_Name'));
        $this->db->from('user_profile');
        $this->db->join('user_settings', 'user_settings.user_id = user_profile.id');
        $this->db->where(array('user_settings.notification_settings' => 'Y', 'user_profile.id' => $userID));
        $res = $this->db->get()->result_array();
        if (!empty($res)) {
            return $res;
        } else {
            return false;
        }
    }

    public function getUserJobData($userID)
    {
        $this->db->select(array('job_data_maping.id as job_id', 'jobs.position_name'));
        $this->db->from('jobs');
        $this->db->join('job_data_maping', 'jobs.id = job_data_maping.job_id');
        $this->db->where(array('job_data_maping.user_id' => $userID));
        $res = $this->db->get()->result_array();
        if (!empty($res)) {
            return $res;
        } else {
            return false;
        }
    }

    public function getUserExperienceData($get_userID)
    {
        $this->db->select(array('id', 'current_experience', 'years', 'reference_number'));
        $this->db->from('user_experience');
        $this->db->where(array('user_id' => $get_userID));
        $res = $this->db->get()->result_array();
        if (!empty($res)) {
            return $res;
        } else {
            return false;
        }
    }

    public function getUserQualificationData($get_userID)
    {
        $this->db->select(array('id', 'qualification_name', 'passout_year '));
        $this->db->from('user_qualifications');
        $this->db->where(array('user_id' => $get_userID));
        $res = $this->db->get()->result_array();
        if (!empty($res)) {
            return $res;
        } else {
            return false;
        }
    }

    public function DeleteUserQualifications($userid)
    {
        $this->db->where('user_id', $userid);
        $del = $this->db->delete('user_qualifications');
        return $del;
    }

    public function DeleteUserExperiences($userid)
    {
        $this->db->where('user_id', $userid);
        $del = $this->db->delete('user_experience');
        return $del;
    }

    public function DeleteUserJobs($userid)
    {
        $this->db->where('user_id', $userid);
        $del = $this->db->delete('job_data_maping');
        return $del;
    }
}