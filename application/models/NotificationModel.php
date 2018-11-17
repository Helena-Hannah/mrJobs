<?php

/**
 * Created by PhpStorm.
 * User: Helena Hannah George
 * Date: 19-07-2018
 * Time: 14:12
 */
class NotificationModel extends CI_Model
{
    // get  notification history of a user
    public function notficationHistoryofUser($user_id, $start_index, $count)
    {
        $query = $this->db->query("SELECT notification_log.id, user_id, type_id,`type`,notification_msg_content.msg_content as msg_content, `status`, created_at, updated_at,
 case when `type`='PRODUCT' then (select case when (prod_pic IS NULL OR prod_pic = 'null') 
            THEN CONCAT('" . ProductImage . "',prod_pic)
                 END as img_url from products where product_id=notification_log.type_id)
 end as img_url
	FROM notification_log 
	join notification_msg_content on notification_log.msg_content=notification_msg_content.id
	where  status = 'Y' AND user_id= $user_id  order by created_at  desc limit $start_index,$count");

        $result = $query->result_array();
        if (!empty($result)) {
            return $result;
        }
    }

    public function notificationCount($user_id)
    {

        $query = $this->db->query("SELECT notification_log.id
	FROM notification_log 
	join notification_msg_content on notification_log.msg_content=notification_msg_content.id
	where  status = 'Y' AND user_id= $user_id");

        $result = $query->result_array();
        if (!empty($result)) {
            return $result;
        }
    }
    // remove  notification from history of a user
    public function removeNotification($user_id, $notification_id, $data)
    {
        try {
            $this->db->where(array('user_id' => $user_id, 'id' => $notification_id));
            $result = $this->db->update('notification_log', $data);
            if ($result) {
                return $result;
            }
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    //Add product/discount log details
    public function addLogDetails($data)
    {
        try {
            $result = $this->db->insert("notification_log", $data);
            if ($result) {
                return $this->db->insert_id();
            } else {
                return FALSE;
            }
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function addMsgDetails($msgData)
    {
        try {
            $result = $this->db->insert("notification_msg_content", $msgData);
            if ($result) {
                return $this->db->insert_id();
            } else {
                return FALSE;
            }
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
}