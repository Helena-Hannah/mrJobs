<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


// user modules

$route['user/signUp'] = 'UserController/signUp';                                      // user signUp
$route['user/signIn'] = 'UserController/signIn';                                      // user signUp
$route['user/editProfile'] = 'UserController/editProfile';                            // user Edit profile
$route['user/forgotPassword'] = 'UserController/forgotPassword';                      // forgot password page
$route['resetUserPassword'] = 'UserController/resetUserPassword';                     // reset forgot password
$route['user/signOut'] = 'UserController/signOut';                                    // user signOut
//$route['user/editRefreshToken'] = 'UserController/editRefreshToken';                                    // user signOut
$route['user/getMySettings'] = 'UserController/getMySettings';                          // get my Notification Settings
$route['user/changeMySettings'] = 'UserController/changeMySettings';                    // submit user Notification Settings


//Category Modules
$route['home/AllCategories'] = 'JobCategoryController/jobCategories';                    // category list for sign up


//Web view pages
$route['documents/termsConditions'] = 'DocumentController/termsConditions';             // create delivery
$route['documents/termsConditions_en'] = 'ApplicationController/termsConditionsEn';        // create delivery
$route['documents/termsConditions_ar'] = 'ApplicationController/termsConditionsAr';        // create delivery
$route['documents/disclaimer_en'] = 'ApplicationController/disclaimerEn';                   // create delivery
$route['documents/disclaimer_ar'] = 'ApplicationController/disclaimerAr';                    // create delivery
$route['documents/delivery_en'] = 'ApplicationController/deliveryEn';                    // create delivery
$route['documents/delivery_ar'] = 'ApplicationController/deliveryAr';                    // create delivery
$route['documents/privacy_en'] = 'ApplicationController/privacyEn';                    // create delivery
$route['documents/privacy_ar'] = 'ApplicationController/privacyAr';                    // create delivery
$route['documents/refund_en'] = 'ApplicationController/refundEn';                    // create delivery
$route['documents/refund_ar'] = 'ApplicationController/refundAr';                    // create delivery


//Push Notifications
$route['notification/SendCustomMessage'] = 'NotificationController/SendCustomMessage';   // notification on new discount


//Notification history
$route['notification/getMyNotificationHistory'] = 'NotificationController/getMyNotificationHistory';   // notification history of a user
$route['notification/myNotificationRemove'] = 'NotificationController/myNotificationRemove';   // notification history of a user


//Filter Products
$route['category/filterProducts'] = 'CategoryController/filterProducts';                      // filter products

//Feedback Submit
$route['application/submitMyFeedback'] = 'DocumentController/submitMyFeedback';   // submit app feedback of a user
$route['application/submitContactForm'] = 'DocumentController/submitContactForm';   // add contact form of a user
