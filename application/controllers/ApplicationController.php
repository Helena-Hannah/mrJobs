<?php

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 12-07-2018
 * Time: 13:38
 */
class ApplicationController extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
       // $this->load->model('UserModel');
        //$this->load->model('PermissionModel');
    }


    // Load english terms  page

    public function termsConditionsEn()
    {
        $this->load->view("terms_conditions_en");

    }

    // Load arabic terms  page

    public function termsConditionsAr()
    {
        $this->load->view("terms_conditions_ar");

    }

    // Load english disclaimer  page

    public function disclaimerEn()
    {
        $this->load->view("disclaimer_en");

    }

    // Load arabic disclaimer  page

    public function disclaimerAr()
    {
        $this->load->view("discalimer_ar");

    }

    // Load english delivery  page

    public function deliveryEn()
    {
        $this->load->view("delivery_en");

    }

    // Load arabic delivery  page

    public function deliveryAr()
    {
        $this->load->view("delivery_policy_ar");

    }

    // Load english privacy  page

    public function privacyEn()
    {
        $this->load->view("privacy_policy_en");

    }

    // Load arabic privacy  page

    public function privacyAr()
    {
        $this->load->view("privacy_policy_ar");

    }

    // Load english refund  page

    public function refundEn()
    {
        $this->load->view("refund_exchange_en");

    }

    // Load arabic refund  page

    public function refundAr()
    {
        $this->load->view("refund_exchange_ar");

    }

}