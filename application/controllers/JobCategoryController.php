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
        $JobsList = $this->JobCategoryModel->getJobs();
        $result['jobs'] = !empty($JobsList) ? $JobsList : array();
        $LocationsList = $this->JobCategoryModel->getLocations();
        $result['locations'] = !empty($LocationsList) ? $LocationsList : array();
        $responseData = responseBuilder(LISTED, 200, REST_Controller::HTTP_OK, $result);
        $response = $this->set_response($responseData['data'], $responseData['code']);
        return $this->response;
    }

    public function getJobsByCategory_post()
    {
        $requestParams = json_decode(file_get_contents('php://input'), true);
        if (!empty($requestParams)) {
            $validateStatus = $this->validategetJobsByCategory($requestParams);
            if (!is_object($validateStatus)) {
                $category_id = $requestParams['category_id'];
                $jobs = $this->JobCategoryModel->getJobsByCategory($category_id);
                $result['jobs'] = !empty($jobs) ? $jobs : array();
                $responseData = responseBuilder(LISTED, 200, REST_Controller::HTTP_OK, $result);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            } else {
                return $validateStatus;
            }
        } else {
            $responseData = responseBuilder(MISSING_PARAMETER, 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }
    }

    public function validategetJobsByCategory($requestParams)
    {
        if (array_key_exists("category_id", $requestParams)) {
            if ($requestParams['category_id'] == "") {
                $responseData = responseBuilder(EMPTY_REQUEST . "category_id.", 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
            $userDetails = $this->JobCategoryModel->getCategoryStatus($requestParams['category_id']);
            if (!empty($userDetails)) {
                $responseData = responseBuilder(INVALID_CATEGORY_ID, 400, REST_Controller::HTTP_BAD_REQUEST);
                $response = $this->set_response($responseData['data'], $responseData['code']);
                return $this->response;
            }
        } else {
            $responseData = responseBuilder(REQUEST_MISSING . "category_id.", 400, REST_Controller::HTTP_BAD_REQUEST);
            $response = $this->set_response($responseData['data'], $responseData['code']);
            return $this->response;
        }
    }
}