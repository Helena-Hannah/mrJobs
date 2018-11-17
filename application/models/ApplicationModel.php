<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 12-07-2018
 * Time: 12:46
 */
class ApplicationModel extends CI_Model
{

    //get ApplicationDetails
    public function getApplicationetail()
    {
        try {
            $this->db->select(array("IFNULL(item,'')as content", "IFNULL(url,'')as url"));
            $this->db->from("app_settings");
            $res = $this->db->get()->result_array();
            if (count($res)) {
                return $res;
            } else {
                return false;
            }
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

}