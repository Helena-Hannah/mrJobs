<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function sessionCheck($header, $memberId)
{
    $CI = &get_instance();
    $CI->load->model("UserModel");
        $sessionStatus = $CI->UserModel->checkUserSession($header, $memberId);

    return $sessionStatus;
}
