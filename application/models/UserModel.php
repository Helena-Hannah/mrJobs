<?php

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 22-06-2018
 * Time: 14:38
 */
class UserModel extends CI_Model
{
    /*public $id;
    public $u_email;checkUserSession
    public $location_id;
    public $category_id;
    public $u_mobile;
    public $u_password;
    public $u_profilePicURL;
    public $u_dob;
    public $u_Name;
    public $u_address1;
    public $u_address2;
    public $created_date;
    public $last_updated;

    public function setUserId($userId)
    {
        $this->id = $userId;
    }

    public function getUserId()
    {
        $this->id;
    }

    public function setUserEmail($userEmail)
    {
        $this->u_email = $userEmail;
    }

    public function getUserEmail()
    {
        $this->u_email;
    }

    public function setUserMobile($userMobile)
    {
        $this->u_mobile = $userMobile;
    }

    public function getUserMobile()
    {
        $this->u_mobile;
    }

    public function setUserPassword($userPassword)
    {
        $this->u_password = $userPassword;
    }

    public function getUserPassword()
    {
        $this->u_password;
    }

    public function setUserProfilePic($userPassword)
    {
        $this->u_profilePicURL = $userPassword;
    }

    public function getUserProfilePic()
    {
        $this->u_profilePicURL;
    }

    public function setUserDob($userDob)
    {
        $this->u_dob = $userDob;
    }

    public function getUserDob()
    {
        $this->u_dob;
    }

    public function setUserName($userName)
    {
        $this->u_Name = $userName;
    }

    public function getUserName()
    {
        $this->u_Name;
    }

    public function setUserAddress1($UserAddress1)
    {
        $this->u_address1 = $UserAddress1;
    }

    public function getUserAddress1()
    {
        $this->u_address1;
    }

    public function setUserAddress2($UserAddress2)
    {
        $this->u_address2 = $UserAddress2;
    }

    public function getUserAddress2()
    {
        $this->u_address2;
    }

    public function setUserCategory($CategoryID)
    {
        $this->category_id = $CategoryID;
    }

    public function getUserCategory()
    {
        $this->category_id;
    }

    public function setUserLocation($LocationID)
    {
        $this->location_id = $LocationID;
    }

    public function getUserLocation()
    {
        $this->location_id;
    }

    public function setUserCreatedDate($userCreatedDate)
    {
        $this->created_date = $userCreatedDate;
    }

    public function getUserCreatedDate()
    {
        $this->created_date;
    }

    public function setUserUpdatedDate($userUpdatedDate)
    {
        $this->last_updated = $userUpdatedDate;
    }

    public function getUserUpdatedDate()
    {
        $this->last_updated;
    }*/

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
        // print_r($userDetails); die;
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
            $data['language_id'] = $userDetails['language_id'];
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
            "IFNULL(user_profile.u_dob,'')as u_dob", "IFNULL(user_profile.u_address1,'')as u_address1",
            "user_profile.u_address2",
            "case when (user_profile.u_profilePicURL IS NULL) THEN '" . base_url() . defaultImage . "'
	             when (user_profile.u_profilePicURL!='') THEN CONCAT('" . base_url() . "project_img/user_profile/',u_profilePicURL)
                 END as u_profilePicURL", 'user_profile.created_date', 'user_profile.last_updated',
            'user_settings.u_platform', 'user_settings.device_token',
            'user_settings.language_id', 'languages.language',
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
        $this->db->join('languages', 'languages.id = user_settings.language_id', 'left');
        $res = $this->db->get()->result_array();
        if (count($res)) {
            return $res[0];
        } else {
            return false;
        }
    }


    //get user status
    public function getUserEmailStatus($email, $userId,$phone)
    {
        $this->db->select(array('id','u_email'));
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
                        if($userDetails['language_id']=='1')
                        return array("Sorry, your password was incorrect. Please double-check your password.");
                   else
                       return array("عذرًا، كلمة السر الحالية خاطئة. يرجى التحقق من كلمة السر مرة أخرى");
                    }
                }
            } else {
                if($userDetails['language_id']=='1')
                return array("The mail id you entered doesn't belong to an account. Please check your mail id and try again.");
           else
               return array("البريد الالكتروني الذي أدخلته لا ينتمي إلى حساب  يرجى التحقق من البريد الالكتروني الخاص بك وحاول مرة أخرى.");
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

    public function getLanguageInfo($language_id)
    {
        try {
            $this->db->select("");
            $this->db->from("languages");
            $this->db->where(array("id" => $language_id));
            $res = $this->db->get()->result_array();
            if ($res) {
                return $res;
            } else {
                return false;
            }
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function myDeliveryDetails($user_id)
    {
        try {
            $this->db->select(array("locations.location_id", "locations.location_name", "locations.delivery_charge", "locations.delivery_time"
            ,"latitude","longitude"));
            $this->db->from("locations");
            $this->db->join("user_profile", "locations.location_id = user_profile.location_id");
            $this->db->where(array("user_profile.id" => $user_id, "locations.status" => 'Y'));
            $res = $this->db->get()->result_array();
            if ($res) {
                return $res[0];
            } else {
                return "";
            }
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
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

    //get refreshtoken of a user id
    /*public function getRefreshTokenWithUserID($user_id,$refreshToken){

        $this->db->select('refresh_token');
        $this->db->from('user_settings');
        $this->db->where(array('user_id'=> $user_id,'refresh_token LIKE' => $refreshToken));
        $res = $this->db->get()->result_array();
        if (!empty($res)) {
            return $res[0];
        } else {
            return false;
        }
    }*/

    // Get Refresh Token

    /* public function getRefreshToken($refreshToken)
     {
         $this->db->select();
         $this->db->from('user_settings');
         $this->db->where(array('refresh_token LIKE' => $refreshToken));
         $query = $this->db->get();
         $res = $query->result();
         if (!empty($res)) {
             $userDetails = json_decode(json_encode($res[0]), true);
             return $userDetails;
         } else {
             return false;
         }
     }*/

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

    public function getMyCategoryDetails($userId)
    {
        try {
            /*$this->db->select(array("categories.category_id","categories.catname_english",
                "categories.category_pic","categories.created_date"));
            $this->db->from("user_profile");
            $this->db->join("categories","user_profile.category_id=categories.category_id");
            $this->db->where(array("user_profile.id" => $userId),
                "categories.status","Y");*/
            $this->db->select("category_id");
            $this->db->from("user_profile");
            $this->db->where(array("id" => $userId));
            $res = $this->db->get()->result_array();
            if ($res) {
                return $res[0];
            } else {
                return false;
            }
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function getMyLocationyDetails($userId)
    {
        try {
            $this->db->select(array("locations.location_id", "locations.location_name",
                "locations.delivery_charge", "locations.delivery_time"));
            $this->db->from("user_profile");
            $this->db->join("locations", "user_profile.location_id=locations.location_id");
            $this->db->where(array("user_profile.id" => $userId),
                "locations.status", "Y");
            $res = $this->db->get()->result_array();
            if ($res) {
                return $res[0];
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
        $this->db->select(array('user_profile.u_email', 'user_profile.id as user_id','user_profile.u_Name'));
        $this->db->from('user_profile');
        $this->db->join('user_settings', 'user_settings.user_id = user_profile.id');
        $this->db->where(array('user_settings.notification_settings'=>'Y'));
        $res = $this->db->get()->result_array();
        if (!empty($res)) {
            return $res;
        } else {
            return false;
        }
    }

    public function  getAllSelectedUsers($userID){
        $this->db->select(array('user_profile.u_email', 'user_profile.id as user_id','user_profile.u_Name'));
        $this->db->from('user_profile');
        $this->db->join('user_settings', 'user_settings.user_id = user_profile.id');
        $this->db->where(array('user_settings.notification_settings'=>'Y','user_profile.id'=>$userID));
        $res = $this->db->get()->result_array();
        if (!empty($res)) {
            return $res;
        } else {
            return false;
        }
    }

    public function addStockNotification($data){


        try {
            $res = $this->db->insert('stock_notifications', $data);
            if ($res) {
                return $this->db->insert_id();
            } else {
                return false;
            }
        } catch (Exception $exc) {
            throw new Exception("Database error occured");
        }
    }

    //Get all active notification users list

    public function getAllActiveStockUsers($product_id)
    {
        $this->db->select(array('user_profile.u_email', 'user_profile.id as user_id','user_profile.u_Name'));
        $this->db->from('user_profile');
        $this->db->join('stock_notifications', 'stock_notifications.user_id = user_profile.id');
        $this->db->where(array('stock_notifications.status'=>'Y',
            "stock_notifications.product_id"=>$product_id));
        $res = $this->db->get()->result_array();
        if (!empty($res)) {
            return $res;
        } else {
            return false;
        }
    }

    public function checkStockNotifaication($userId,$productId){
        try {
            $this->db->select("id");
            $this->db->from("stock_notifications");
            $this->db->where(array("user_id" => $userId,
                "product_id"=>$productId));
            $res = $this->db->get()->result_array();
            if ($res) {
                return $res[0];
            } else {
                return "";
            }
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
}