<?php
/**
 * Created by PhpStorm.
 * User: Hannah Helena George
 * Date: 17-11-2018
 * Time: 11:50
 */
defined('BASEPATH') OR exit('No direct script access allowed');
include_once 'application/libraries/REST_Controller.php';

class JobCategoryController extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("JobCategoryModel");
    }

    public function jobCategories_get()
    {
        $CategoryList = $this->JobCategoryModel->getCategories();
        $result['categories'] = !empty($CategoryList) ? $CategoryList : array();
        $responseData = responseBuilder(LISTED, 200, REST_Controller::HTTP_OK, $result);
        $response = $this->set_response($responseData['data'], $responseData['code']);
        return $this->response;
    }
}