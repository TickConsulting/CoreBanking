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
|	http://codeigniter.com/user_guide/general/routing.html
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
$route['default_controller'] = 'pages';
//$route['equity_bank'] = 'pages/equity_bank';
//$route['eazzyclub'] = 'pages/eazzyclub';
//$route['associations'] = 'pages/associations';
//$route['android'] = 'pages/android';
//$route['dismiss_android'] = 'pages/dismiss_android';
$route['terms_and_conditions'] = 'pages/terms_and_conditions';
$route['features'] = 'pages/features';
$route['join_group/(:any)'] = 'pages/join_group/$1';

$route['404_override'] = 'pages/error_404_page';
$route['translate_uri_dashes'] = FALSE;
/*$route['pricing'] = 'pages/pricing';
$route['technical-specs'] = 'pages/technical_specs';
$route['features/sacco-member-onboarding-and-enrollment'] = 'pages/member_management';
$route['features/sacco-loans'] = 'pages/loans';
$route['features/sacco-investments'] = 'pages/investments';
$route['features/sacco-income-and-expense-management'] = 'pages/income_and_expense_management';
$route['features/sacco-statements'] = 'pages/statements';
$route['features/sacco-reports'] = 'pages/reports';
$route['features/sacco-member-checkoffs'] = 'pages/checkoffs';
$route['features/sacco-ewallet'] = 'pages/ewallet';
$route['features/sacco-notifications'] = 'pages/notifications';
$route['features/sacco-member-fining'] = 'pages/fining';
$route['features/sacco-transactions'] = 'pages/transactions';
$route['features/sacco-communications'] = 'pages/communications';
$route['features/sacco-accounts'] = 'pages/sacco_accounts';
$route['features/sacco-member-deposits'] = 'pages/member_deposits';
$route['solutions/on-premise-sacco-solution'] = 'pages/on_premise';
$route['solutions/on-cloud-sacco-solution'] = 'pages/on_cloud';
$route['target-market/digital-lenders'] = 'pages/for_digital_lenders';
$route['target-market/saccos'] = 'pages/for_saccos';
$route['target-market/company-saccos'] = 'pages/for_company_saccos';
$route['target-market/shylock'] = 'pages/for_shylock';
$route['target-market/microfinance-organizations'] = 'pages/for_microfinance_organizations';
$route['demo'] = 'authentication/demo_login';*/


$route['admin/(reset_password)/(:any)'] = 'admin/$1/$2';
$route['admin/([a-zA-Z_-]+)/(:any)'] = '$1/admin/$2';
$route['admin/([a-zA-Z_-]+)/(:any)/(:any)'] = '$1/admin/$2/$3';
$route['admin/([a-zA-Z_-]+)/(:any)/(:any)/(:any)'] = '$1/admin/$2/$3/$4';
$route['admin/([a-zA-Z_-]+)/(:any)'] = '$1/admin/$2';
$route['admin/(login|logout|forgot_password|reset_password|confirm_code)'] = 'admin/$1';
$route['admin/([a-zA-Z_-]+)'] = '$1/admin/index';
$route['admin/([a-zA-Z_-]+)'] = '$admin/admin/$1';

//$route['bank/(checkin)/(:any)'] = 'bank/$1/$2';

$route['partner/(reset_password)/(:any)'] = 'partner/$1/$2';
$route['partner/([a-zA-Z_-]+)/(:any)'] = '$1/partner/$2';
$route['partner/([a-zA-Z_-]+)/(:any)/(:any)'] = '$1/partner/$2/$3';
$route['partner/([a-zA-Z_-]+)/(:any)/(:any)/(:any)'] = '$1/partner/$2/$3/$4';
$route['partner/([a-zA-Z_-]+)/(:any)'] = '$1/partner/$2';
$route['partner/checkin'] = 'partner/checkin';
$route['partner/([a-zA-Z_-]+)'] = '$1/partner/index';

$route['bank/(reset_password)/(:any)'] = 'bank/$1/$2';
$route['bank/([a-zA-Z_-]+)/(:any)'] = '$1/bank/$2';
$route['bank/([a-zA-Z_-]+)/(:any)/(:any)'] = '$1/bank/$2/$3';
$route['bank/([a-zA-Z_-]+)/(:any)/(:any)/(:any)'] = '$1/bank/$2/$3/$4';
$route['bank/([a-zA-Z_-]+)/(:any)'] = '$1/bank/$2';
$route['bank/checkin'] = 'bank/checkin';
$route['bank/([a-zA-Z_-]+)'] = '$1/bank/index';

$route['checkin'] = 'authentication/checkin';
$route['login'] = 'authentication/login';
$route['signup'] = 'authentication/signup';
$route['logout'] = 'authentication/logout';
$route['signup/(:any)'] = 'authentication/signup/$1';
$route['forgot_password'] = 'authentication/forgot_password';
$route['reset_password'] = 'authentication/reset_password';
$route['demo_login'] = 'authentication/demo_login';
$route['create_group'] = 'authentication/create_group';
$route['confirm_code'] = 'authentication/confirm_code';
$route['verify_otp'] = 'authentication/verify_otp';
$route['confirm_otp'] = 'authentication/confirm_otp';
$route['extend_session'] = 'authentication/extend_session';
$route['join/(:any)'] = 'authentication/join/$1';
$route['new_password'] = 'authentication/new_password';
$route['new_password/(:any)'] = 'authentication/new_password/$1';
$route['login_to_group/(:any)'] = 'authentication/login_to_group/$1';
$route['login_to_group/(:any)/(:any)'] = 'authentication/login_to_group/$1/$2';
$route['login_to_group'] = 'authentication/login_to_group';
$route['change_password'] = 'authentication/change_password';
$route['set_new_password'] = 'authentication/set_new_password';



$domains = explode(".", $_SERVER['HTTP_HOST']);

// $dots = 3;
// if(strlen($domains[(count($domains) - 1)]) == 2){
//    $dots = 4;
// }
// if(count($domains) == $dots && $domains[0] != "www"){
//    	$route['default_controller'] = "group";
//     $route['404_override'] = "group";
// }


$route['group/(reset_password)/(:any)'] = 'group/$1/$2';
$route['migrate/(group)/(:any)'] = 'migrate/$1/$2';
$route['group/([a-zA-Z_-]+)/(:any)'] = '$1/group/$2';
$route['group/([a-zA-Z_-]+)/(:any)/(:any)'] = '$1/group/$2/$3';
$route['group/([a-zA-Z_-]+)/(:any)/(:any)/(:any)'] = '$1/group/$2/$3/$4';
$route['group/([a-zA-Z_-]+)/(:any)'] = '$1/group/$2';
// $route['group/accounts'] = 'accounts';//there is an error with the accounts module and need to be checked later
$route['group/(activate|resend_activation_code|change_email_address|change_phone_number)'] = 'group/$1';
$route['group/([a-zA-Z_-]+)'] = '$1/group/index';


$route['member/(reset_password)/(:any)'] = 'member/$1/$2';
$route['member/([a-zA-Z_-]+)/(:any)'] = '$1/member/$2';
$route['member/([a-zA-Z_-]+)/(:any)/(:any)'] = '$1/member/$2/$3';
$route['member/([a-zA-Z_-]+)/(:any)/(:any)/(:any)'] = '$1/member/$2/$3/$4';
$route['member/([a-zA-Z_-]+)/(:any)'] = '$1/member/$2';
$route['member/(login|logout|forgot_password|reset_password|confirm_code)'] = 'member/$1';
$route['member/([a-zA-Z_-]+)'] = '$1/member/index';
$route['eazzy_club_loan_application/([a-zA-Z_-]+)/(:any)'] = '$1/eazzy_club_loan_application/$2';



$route['mobile/(reset_password)/(:any)'] = 'mobile/$1/$2';
$route['mobile/(get_user_group_data)/(:any)/(:any)/(:any)'] = 'mobile/$1/$2/$3/$4';
$route['mobile/(get_user_group_data)/(:any)'] = 'mobile/$1/$2';
$route['mobile/(get_user_group_data_information)/(:any)'] = 'mobile/$1/$2';
$route['mobile/(get_test_data)/(:any)'] = 'mobile/$1/$2';
$route['mobile/([a-zA-Z_-]+)/(:any)'] = '$1/mobile/$2';
$route['mobile/([a-zA-Z_-]+)/(:any)/(:any)'] = '$1/mobile/$2/$3';
$route['mobile/([a-zA-Z_-]+)/(:any)/(:any)/(:any)'] = '$1/mobile/$2/$3/$4';
$route['mobile/([a-zA-Z_-]+)/(:any)'] = '$1/mobile/$2';

$route['mobile/(:any)'] = 'mobile/$1';

$route['api/([a-zA-Z_-]+)/(:any)'] = '$1/api/$2';

$route['mobile/(login|logout|forgot_password|reset_password|confirm_code)'] = 'mobile/$1';
$route['mobile/([a-zA-Z_-]+)'] = '$1/mobile/index';


$route['ajax/(reset_password)/(:any)'] = 'ajax/$1/$2';
$route['ajax/(get_user_group_data)/(:any)/(:any)/(:any)'] = 'ajax/$1/$2/$3/$4';
$route['ajax/(get_user_group_data)/(:any)'] = 'ajax/$1/$2';
$route['ajax/(get_user_group_data_information)/(:any)'] = 'ajax/$1/$2';
$route['ajax/([a-zA-Z_-]+)/(:any)'] = '$1/ajax/$2';
$route['ajax/([a-zA-Z_-]+)/(:any)/(:any)'] = '$1/ajax/$2/$3';
$route['ajax/([a-zA-Z_-]+)/(:any)/(:any)/(:any)'] = '$1/ajax/$2/$3/$4';
$route['ajax/([a-zA-Z_-]+)/(:any)'] = '$1/ajax/$2';

$route['terms_of_use'] = 'pages/terms_of_use';