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

    public function getJobs()
    {
        try {
            $this->db->select(array("id as job_id", "position_name"));
            $this->db->from("jobs");
            $this->db->where(array('status' => 'Y'));
            $this->db->order_by("position_name");
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

    public function addJobDetails($data)
    {
        try {
            $res = $this->db->insert('jobs', $data);
            if ($res) {
                return $this->db->insert_id();
            } else {
                return false;
            }
        } catch (Exception $exc) {
            throw new Exception("Database error occured");
        }
    }

    public function getCategoryStatus($category_id)
    {
        try {
            $this->db->select(array("id"));
            $this->db->from("job_category");
            $this->db->where(array('status' => 'Y',
                'id' => $category_id));
            $res = $this->db->get()->result_array();
            //echo $this->db->last_query(); die;
            if (count($res)) {
                return $res[0];
            }
        } catch (Exception $exec) {
            throw new Exception("Database error occured");
        }
    }

    public function jobDataMapping($data)
    {
        try {
            $res = $this->db->insert('job_data_maping', $data);
            if ($res) {
                return $this->db->insert_id();
            } else {
                return false;
            }
        } catch (Exception $exc) {
            throw new Exception("Database error occured");
        }
    }

    public function getJobsByCategory($category_id)
    {
        try {
            $this->db->select(array("id as job_id", "position_name as job_name"));
            $this->db->from("jobs");
            $this->db->where(array('category_id' => $category_id));
            $this->db->order_by("position_name");
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

    public function getLocations()
    {
        try {
            $this->db->select(array("id as location_id", "location as location_name"));
            $this->db->from("locations");
            $this->db->order_by("location");
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