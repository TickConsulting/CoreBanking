<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$config['consumer_secret'] = 'client_credentials';
$config['consumer_key'] = '';
$config['initiator_password'] = '';
$config['short_code'] = '600996';
$config['prod_url'] = 'https://sandbox.safaricom.co.ke/';



/***************URLs*********************/

if(preg_match('/(tickconsulting\.co\.ke)/',$_SERVER['HTTP_HOST']) ){
	$config['prod_url'] = 'https://sandbox.safaricom.co.ke/';
	$config['consumer_secret'] = 'client_credentials';
    $config['consumer_key'] = '';
}

$config['token_url'] = 'oauth/v1/generate?grant_type=client_credentials';
$config['b2c_url'] = 'mpesa/b2c/v3/paymentrequest';

?>