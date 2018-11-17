<?php

/**
 * Created by PhpStorm.
 * User: Hannah Helena George
 * Date: 17-11-2018
 * Time: 11:59
 */
class JobCategoryModel extends CI_Model
{
    public function getCategories()
    {
        try {
            $this->db->select(array("id as category_id", "category_name"));
            $this->db->from("job_category");
            $this->db->where(array('status' => 'Y'));
            $this->db->order_by("category_name");
            $res = $this->db->get()->result_array();
            if (count($res)) {
                return $res;
            } else {
                return false;
            }
        } catch (Exception $exec) {
            throw new Exception("Database error occured");
        }
    }
}