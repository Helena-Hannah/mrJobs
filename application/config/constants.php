<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE') OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE') OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE') OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ') OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE') OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE') OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE') OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE') OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE') OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT') OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT') OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS') OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR') OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG') OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE') OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS') OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT') OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE') OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN') OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX') OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

//Domain
define("Domain", "http://34.214.67.129/");
define('stakeholder', 'fetch_Stakeholder');
define('service', 'kpFetch');
// server stakeholder path
define("StakeholderPath", Domain . stakeholder . "/index.php/");
// service path
define("ServicePath", Domain . service . "/index.php/");

// Default Image Path
define("defaultImage", "assets/img/profile_place.png");

//Image paths
define("ProductImage", Domain . stakeholder . "/project_img/product_img/");                // category images
define("CategoryImage", Domain . stakeholder . "/project_img/category_img/");
define("SubCategoryImage", Domain . stakeholder . "/project_img/category_img/");


//Image paths
/*define("ProductImage", "http://localhost/fetch_Stakeholder/project_img/category_img/");                // category images
define("CategoryImage", "http://localhost/fetch_Stakeholder/project_img/category_img/");
define("SubCategoryImage", "http://localhost/fetch_Stakeholder/project_img/category_img/");*/


// Project info

define("FromName", "fetchApp");
define("FromEmail", "fetch@support.in");

// Forgot password Exp. Time

define('Password_Reset_Time', "24H");

// Gust User

define('GUST_SESSION_TOKEN', "379ccd17-150f-11e7-8142-2c600cf8021b");
define('GUST_USER_ID', "64");

// common validations

define('MISSING_PARAMETER', 'service failed due to missing parameters');                                                         // empty parameter
define('EMPTY_REQUEST', 'Please enter your ');                                                                                   // empty request
define('ENTER_YOUR_TITLE', 'Please enter a title');
define('ENTER_YOUR_DESCRIPTION', 'Please fill the description');
define('MISSING_ID', 'Request failed due to empty ');                                                                            // missing id
define('REQUEST_MISSING', 'Request failed due to missing ');                                                                     // missing request
define('FAILED', 'Operation Failed.');                                                                                           // Server Error
define('INVALID_SESSION', 'Your current session is invalid. Please login to continue.');
define('INVALID_SESSION_AR', 'لقد انتهت صلاحية الدخول. يرجى إعادة تسجيل الدخول للإستمرار ');  // invalid current session
define('INVALID_USER_ID', 'Invalid user_id');                                                                                    // invalid user_id
define('EMAIL_EXISTS', 'This email Id is already used for registration. Please login to continue.');                             //user already EXISTS
define('EMAIL_EXISTS_AR', 'البريد الإلكتروني الذي أدخلته تم إستخدامه مسبقاً وقت التسجيل. الرجاء تسجيل الدخول للمتابعة');                             //user already EXISTS arabic
define('EMAIL_NOT_EXISTS', "The mail id you entered doesn't belong to an account. Please check your mail id and try again.");    //user already EXISTS
define('EMAIL_NOT_EXISTS_AR', "البريد الالكتروني الذي أدخلته لا ينتمي إلى حساب  يرجى التحقق من البريد الالكتروني الخاص بك وحاول مرة أخرى.");    //user already EXISTS
define('MOBILE_EXISTS', 'This mobile number is already used for registration. Try with a new number.');                          //user already EXISTS
define('MOBILE_EXISTS_AR', 'رقم الهاتف الذي أدخلته تم إستخدامه مسبقاً وقت التسجيل. الرجاء إدخال رقم هاتف آخر');                          //user already EXISTS
define('SIGN_UP_SUCCESS', 'Congrats!! You have successfully registered to the app.');                                            // signUp success
define('SIGN_UP_SUCCESS_AR', 'مبروك!! لقد قمت بالتسجيل في التطبيق بنجاح.');
define('SUCCESS', 'Success');                                                                                                    // signIn success
//define('INVALID_REFRESH_TOKEN', 'Invalid refresh token');                                                                        // invalid refresh token
define('EDIT_PROFILE', 'Your profile has been updated successfully');
define('EDIT_PROFILE_AR', 'تم تحديث كلمة السر الخاصه بك بنجاح');  // edit Profile
define('CURRENT_PASSWORD_WRONG', 'Sorry, your current password is wrong. Please double-check your password.');                   // change password
define('CURRENT_PASSWORD_WRONG_AR', 'عذرًا، كلمة السر الحالية خاطئة. يرجى التحقق من كلمة السر مرة أخرى');                   // change password
define('CURRENT_PASSWORD_CHANGED', 'Your password has been updated successfully.');                                              // changed password
define('CURRENT_PASSWORD_CHANGED_AR', 'تم تحديث كلمة السر الخاصه بك بنجاح');                                              // changed password
define('RESET_PASSWORD_SENT', 'Your request for password change has been received. Please check your mail to update the password.');    // changed password
define('RESET_PASSWORD_SENT_AR', 'تم استلام طلبك لتغيير كلمة السر يرجى التحقق من بريدك الالكتروني لتحديث كلمة السر');    // changed password
define('ADDED_IMAGES', "Your profile has been updated successfully.");
define('ADDED_IMAGES_AR', "تم تحديث الملف الشخصي الخاص بك بنجاح");// upload images
define('FEEDBACK_SUBMISSION', "Thank you for your feedback. We will use your comments as we strive to improve your experience."); // Feedback submission
define('FEEDBACK_SUBMISSION_AR', "شكرا على ملاحظتك. سنأخذ الملاحظة بعين الاعتبار حيث نسعى دائمًا لتحسين تجربتك."); // Feedback submission in Arabic
define('CONTACT_SUBMISSION', "Thanks for your message."); // Feedback submission
define('CONTACT_SUBMISSION_AR', "شكراً على رسالتك."); // Feedback submission
define('UPDATED_USER_SETTINGS', "Your settings has been updated.");
define('UPDATED_USER_SETTINGS_AR', "تم تحديث إعداداتك");
define('LISTED', 'listed');
define('LOGGEDOUT', "You've successfully logged out");
define('LOGGEDOUT_AR', "تم تسجيل الخروج بنجاح");
define('EMAIL_WITH_ANOTHER_ACCOUNT', 'This Email is not matching with your account');
//define('REFRESHTOKEN_DOES_NOT_MATCH', "Refresh token & member_id does not match");


//Category, Subcategory, Product Type & Product constants
define('INVALID_CATEGORY_ID', 'Invalid Category Id');
define('PRODUCT_DOES_NOT_EXISTS', ' is no longer available');
define('PRODUCT_DOES_NOT_EXISTS_AR', ' is no longer available');
define('INVALID_LOCATION_ID', 'Invalid location id');
define('INVALID_SUBCATEGORY_ID', 'Invalid Subcategory Id');
define('INVALID_PRODUCT_TYPE', 'Invalid Product Type');
define('INVALID_PRODUCT_ID', 'Invalid Product Id ');
define('INVALID_LANGUAGE_ID', 'Invalid language id');

// Order & delivery constants
define('CART_NOT_EXISTS', 'You have no items in your shopping cart.');
define('DELIVERY_NOT_EXISTS', 'No Delivery available for this location. Please change your location');
define('DELIVERY_NOT_EXISTS_AR', 'التوصيل الى هذا الموقع غير متوفر. الرجاء تغيير الموقع');

//Offer & Discounts constants
define('COUPON_MINIMUM_AMOUNT', 'This coupon is valid for a minimum purchase of ');
define('COUPON_MINIMUM_AMOUNT_AR', 'هذا الكوبون صالح للمشتريات بقيمة ( ) كحد أدنى ');
define('INVALID_COUPON_CODE', 'Invalid Coupon code');
define('INVALID_COUPON_CODE_AR', 'رمز الكوبون غير صحيح');
define('COUPON_EXPIRED', 'This coupon is no longer valid');
define('COUPON_EXPIRED_AR', 'الكوبون منتهي الصلاحية');
define('COUPON_LIMIT_EXCEEDED', 'This coupon has exceeded maximum usage count');
define('COUPON_LIMIT_EXCEEDED_AR', 'لقد تجاوز هذا الكوبون حد الاستخدام الخاص به');
define('COUPON_ALREADY_USED', 'You have already used this coupon');
define('COUPON_ALREADY_USED_AR', 'لقد استخدمت هذا الكوبون مسبقاً');
define('COUPON_APPLIED', 'Coupon Applied successfully');
define('COUPON_APPLIED_AR', 'تم تفعيل رمز الكوبون');

//application based
define('LISTED_APPLICATION_SETTINGS', "Listed Application settings"); // listed user settings
define('SERVER_ERROR', 'Some error occured'); //server error

define('returnUrl', ServicePath . 'payment/paymentResponse');

// Push Notification constants
define('NOTIFICATION_SENT', "Notification send successfully.");  // Notification successfully sent message.
define('NEWPROMCODE', 'New Promotional Code'); //new prom code
define('NEWCATEGORY', 'New Category'); //new category
define('NEWMESSAGE', 'New Message'); //new message
define('STOCKUPDATION', 'Stock Updation'); //new message
define('NOTIFICATION_REMOVED', 'The Notification Moved into Trash'); //Remove a notification
define('NOTIFICATION_REMOVED_AR', 'تم نقل الإشعار للمهملات'); //Remove a notification

define('PRODUCT_OUT_STOCK', '  is out of stock'); //prodcut out of stock
define('PRODUCT_OUT_STOCK_AR', '  غير متوفر حاليا '); //prodcut out of stock


define('MINIMUM_AMOUNT_EXCEEDED', 'To checkout, Your purchase amount must be greater than  '); //prodcut out of stock
define('MINIMUM_AMOUNT_EXCEEDED_AR', 'للإستمرار، قيمة المشتريات يجب ان تكون أكثر من  '); //prodcut out of stock


define('ENABLED_STOCK_NOTIFICATION', 'You will notified on stock updation of the product'); //prodcut out of stock
define('ENABLED_STOCK_NOTIFICATION_AR', 'ابلاغي عند توفر المنتجً');//prodcut out of stock


define('ALREADY_ENABLED', 'This notification is already enabled for this product'); //prodcut out of stock
define('ALREADY_ENABLED_AR', 'تم تفعيل هذا الإشعار مسبقاًً'); //prodcut out of stock
