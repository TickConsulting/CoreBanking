<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| SendGrid Setup
|--------------------------------------------------------------------------
*/
$config['protocol']	= 'smtp';
$config['smtp_port']	= '587';
$config['smtp_host']	= 'smtp.sendgrid.net';
$config['smtp_user']	= '';
$config['smtp_pass']	= '';
$config['newline']	= '\r\n';
$config['mailtype']	= 'html';
$config['charset']	= 'iso-8859-1';
$config['crlf']	= '\r\n';

// postmark token
$config['api_token']	= 'c2200479-5391-4cb5-b53c-91e5e04f2c79';

$config['eazzyclub_token']	= '1948f33d-2860-4162-8d36-3157fc71f130';
//$config['eazzychama_token']	= '7316e31c-9545-426d-bf70-e8e2726e0110';
$config['eazzychama_token']	= 'cb833b41-2415-4f81-95a5-a1178f94ed96';