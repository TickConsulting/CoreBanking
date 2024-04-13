<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$config['grant_type'] = 'client_credentials';
$config['token_username'] = 'githaiga.geoffrey@gmail.com';
$config['token_password'] = '31784253Kk!';
$config['token_client_id'] = '37B7C217CE3C46E';
$config['token_grant_type'] = 'password';


/***************URLs*********************/

if(preg_match('/(eazzykikundi\.com)/',$_SERVER['HTTP_HOST']) ){
	// $config['prod_url'] = 'https://api-omnichannel-preprod.azure-api.net/';
	$config['prod_url'] = 'https://api.equitygroupholdings.com/';
	$config['client_id'] = '9F4B27B0A8414BA';
	$config['client_secret'] = 'NEUwQ0RENkEtRkQ1NC00RUQ4LUEzRkEtREU4NjNBMjk1Mzk2';
	$config['token_grant_type'] = 'client_credentials';
}else if(preg_match('/(eazzychama\.co)/',$_SERVER['HTTP_HOST']) ){
	// $config['prod_url'] = 'https://api-omnichannel-preprod.azure-api.net/';
	$config['prod_url'] = 'https://equityconnect.equitygroupholdings.com/';
	$config['client_id'] = '9F4B27B0A8414BA';
	$config['client_secret'] = 'NEUwQ0RENkEtRkQ1NC00RUQ4LUEzRkEtREU4NjNBMjk1Mzk2';
	$config['token_grant_type'] = 'client_credentials';
}else if(preg_match('/(chamasoft\-we\-001\-dev\-ke\.azurewebsites\.net)/',$_SERVER['HTTP_HOST']) || preg_match('/(chamasoft\-we\-001\-dev\.azurewebsites\.net)/',$_SERVER['HTTP_HOST'])){
	//$config['prod_url'] = 'https://api-omnichannel-uat.azure-api.net/';
	//$config['prod_url'] = 'https://equityconnect-uat.equitygroupholdings.com/';
	$config['prod_url'] = 'https://api-uat.equitygroupholdings.com/';
	$config['client_id'] = 'E44EF1C7427A4C2';
	$config['client_secret'] = 'NTMyMjVCODMtMUVGMi00QTY2LTk1N0QtNDI2NzhGM0M1NzU5';
	$config['token_grant_type'] = 'client_credentials';
}
else{
	//$config['prod_url'] = 'https://api-omnichannel-uat.azure-api.net/';
	$config['prod_url'] = 'https://equityconnect-uat.equitygroupholdings.com/';
	$config['client_id'] = 'E44EF1C7427A4C2';
	$config['client_secret'] = 'NTMyMjVCODMtMUVGMi00QTY2LTk1N0QtNDI2NzhGM0M1NzU5';
	$config['token_grant_type'] = 'client_credentials';
}

$config['dev_url'] = 'https://api-omnichannel-dev.azure-api.net/';
$config['uat_url'] = 'https://api-omnichannel-uat.azure-api.net/';
$config['token_url'] = 'v2/oauth/token';
$config['notification_url'] = 'v1/notification';
$config['generate_otp_url'] = 'v1/otp';
$config['verify_otp_url'] = 'v1/otp/verify';
$config['funds_transfer_url'] = 'v2/bridge';
$config['internal_funds_transfer_url'] = 'v1/transfer/intra';
$config['mobile_money_url'] = 'v1/mobilemoney';
$config['account_lookup'] = 'v1/account/lookup/';
$config['telco_lookup'] = 'v1/mobilemoney/namecheck';
$config['account_balance'] = 'v1/account/balance/';
$config['initiate_account_linkage'] = 'v1/equityconnect/';
$config['account_linkage_send_otp'] = 'v1/equityconnect/otp/';
$config['account_linkage_verify_otp'] = 'v1/equityconnect/';
$config['check_linked_account'] = 'v1/equityconnect/linked';