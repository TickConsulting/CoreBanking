<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$key = md5('chamasoft_version_three');
if (!defined('SALT')) define('SALT', $key);

if (defined('CALLING_CODE')) {
} else {
    $this->load->model('countries/countries_m');
    if ($code = calling_code_prefix()) {
        define('CALLING_CODE', $code);
    } else {
        $country = $this->countries_m->get_default_country();
        if ($country) {
            if ($country->calling_code) {
                define('CALLING_CODE', $country->calling_code);
                define('COUNTRY_CODE', $country->code);
            } else {
                define('CALLING_CODE', "255");
                define('COUNTRY_CODE', "TZ");
            }
        } else {
            define('CALLING_CODE', "255");
            define('COUNTRY_CODE', "TZ");
        }
    }
}


$ci = &get_instance();
$domain_name = $ci->config->item('cookie_domain');
if(preg_match('/154\.152\.227\.83/',$_SERVER['REMOTE_ADDR'])){
    // echo $domain_name; die;
}

$code = str_replace("+", "", CALLING_CODE);
if (is_numeric($code)) {
    setcookie('CALLING_CODE', CALLING_CODE, time() + (86400 * 90), '/', $domain_name, TRUE, TRUE);
} else {
    setcookie('CALLING_CODE', "255", time() + (86400 * 90), '/', $domain_name, TRUE, TRUE);
}
setcookie('COUNTRY_CODE', filter_var(COUNTRY_CODE, FILTER_SANITIZE_STRING), time() + (86400 * 90), '/', $domain_name, TRUE, TRUE);

if ($this->router->fetch_method() == 'signup') {
    $refferral_code = $this->input->get('referral_code');
    $cookie = array(
        'name'   => 'REFFERAL_CODE',
        'value'  => $refferral_code,
        'expire' => 86400 * 30,
        'secure' => TRUE
    );
    setcookie('REFFERAL_CODE', $refferral_code, time() + (86400 * 30), '/', $domain_name, TRUE, TRUE);
}

function ordinal($number)
{
    $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
    if ((($number % 100) >= 11) && (($number % 100) <= 13))
        return $number . 'th';
    else
        return $number . $ends[$number % 10];
}

function number_to_currency($int = 0)
{
    if (abs(round($int, 3)) == 0) {
        $int = abs($int);
    }
    if (floatval(str_replace(',', '', $int))) {
        return number_format(floatval(str_replace(',', '', $int)), 2);
    } else {
        return number_format(0, 2);
    }
}
function timestamp_to_datetime_from_timestamp($timestamp = 0,$value=FALSE){
    if(is_numeric($timestamp)){
        $date = DateTime::createFromFormat('YmdHis',$timestamp);
        if($date){
            return  '<span class="tooltips" data-original-title="'.$date->format('l, jS F Y, g:i A').'" >'.$date->format('d-m-Y , g:i A').'</span>'; 
        }else{
            return '<span class="tooltips" data-original-title="'.date('l, jS F Y, g:i A',$timestamp?$timestamp:0).'" >'.date('d-m-Y , g:i A',$timestamp?$timestamp:0).'</span>'; 
        }  
    }
}
function raw_phone_number($phone = 0)
{
    return str_replace('254', '', $phone);
}


function timestamp_to_datetime($timestamp = 0)
{
    if (is_numeric($timestamp)) {
        return '<span class="tooltips" data-original-title="' . date('l, jS F Y, g:i A', $timestamp ? $timestamp : 0) . '" >' . date('d-m-Y , g:i A', $timestamp ? $timestamp : 0) . '</span>';
    }
}

function timestamp_to_date($timestamp = 0, $value = FALSE)
{
    if (is_numeric($timestamp)) {
        if ($value) {
            return date('d-m-Y', $timestamp ? $timestamp : 0);
        } else {
            return '<span class="tooltips" data-original-title="' . date('l, jS F Y', $timestamp ? $timestamp : 0) . '" >' . date('d-m-Y', $timestamp ? $timestamp : 0) . '</span>';
        }
    }
}

function timestamp_to_mobile_shorttime($timestamp = 0)
{
    if (is_numeric($timestamp)) {
        return date('D, jS M Y', $timestamp ? $timestamp : 0);
    } else {
        return '';
    }
}


function timestamp_to_mobile_time($timestamp = 0)
{
    return date('l, jS F Y', $timestamp ? $timestamp : 0);
}

function timestamp_to_mobile_report_time($timestamp = 0)
{
    return date("jS M, Y ", $timestamp ? $timestamp : 0);
}

function _valid_identity()
{
    $identity = $this->input->post('identity');

    if (!valid_email($identity)) {
        if (!valid_phone($identity)) {
            $this->form_validation->set_message('_valid_identity', 'Enter a valid Email or Phone Number');
            return FALSE;
        }
        return TRUE;
    } else {
        return TRUE;
    }
}

function timestamp_to_daytime($timestamp = 0)
{
    return date('l, d-m-Y', $timestamp ? $timestamp : 0);
}

function timestamp_to_message_time($timestamp = 0)
{
    return date('H:iA, d-m-Y', $timestamp ?: 0);
}

function timestamp_to_monthtime($timestamp = 0)
{
    return date('M Y', $timestamp ? $timestamp : 0);
}

function timestamp_to_datemonth($timestamp = 0)
{
    return date('D, M', $timestamp ? $timestamp : 0);
}

function timestamp_to_date_and_time($timestamp = 0)
{
    return date('d-m-Y, g:ia', $timestamp ? $timestamp : 0);
}

function timestamp_to_receipt($timestamp = 0)
{
    return date('D, M d, Y', $timestamp ? $timestamp : 0);
}

function timestamp_to_report_time($timestamp)
{
    return date("jS F,Y  ", $timestamp ? $timestamp : 0);
}

function timestamp_to_short_mobile_report_time($timestamp)
{
    return date("jS M, y", $timestamp ? $timestamp : 0);
}

function mobile_timestamp_to_month($timestamp = 0)
{
    return date('M', $timestamp ? $timestamp : 0);
}

function timestamp_to_datepicker($timestamp = 0)
{
    if (is_numeric($timestamp)) {
        return date('d-m-Y', $timestamp ? $timestamp : time());
    } else {
        $times = strtotime($timestamp);
        if (is_numeric($times)) {
            return date('d-m-Y', $times ? $times : time());
        } else {
            return date('d-m-Y', time());
        }
    }
}

function timestamp_to_monthpicker($timestamp = 0)
{
    if (is_numeric($timestamp)) {
        return date('M-Y', $timestamp ? $timestamp : time());
    } else {
        $times = strtotime($timestamp);
        if (is_numeric($times)) {
            return date('M-Y', $times ? $times : time());
        } else {
            return date('M-Y', time());
        }
    }
}

function timestamp_to_datemonth_and_time($timestamp = 0)
{
    return date('d-M-Y g:iA', $timestamp ? $timestamp : 0);
}

function remove_zero($number = 0)
{
    if (strpos($number, "0") == 0) {
        return substr($number, 1);
    } else {
        return $number;
    }
}
function calculate_days_in_arrears($disbursement_date=0, $repayment_period=0) {
    // Calculate the expected repayment date
    $expected_repayment_date = strtotime("+" . $repayment_period . " months", $disbursement_date);
    // Get the current date
    $current_date = time();
    // Calculate the number of days in arrears if the repayment date has passed
    if ($current_date > $expected_repayment_date) {
        $days_in_arrears = ($current_date - $expected_repayment_date) / (60 * 60 * 24); // Convert seconds to days
        return floor($days_in_arrears); // Return the number of days as an integer
    } else {
        return 0; // No arrears if the repayment date has not passed
    }
}




function valid_phone($phone = 0, $strlen = TRUE, $set_calling_code_prefix = FALSE)
{

    $phone = preg_replace('/[\s\s+\-\(|\)]/', '', $phone);
    $ci = &get_instance();

    if ($phone) {
        //checks whether its a valid phone number
        if (preg_match("/^[\+0-9\-\(\)\s]*$/", $phone)) {
            if (preg_match("/[\+]/", $phone)) {
                //phone has a plus at the beginning of the string e.g. +254721106625
                if ($strlen == TRUE && $strlen >= 10) {
                    return $phone;
                } else {
                    if ($strlen == FALSE) {
                        if (strlen($phone) <= 10) {
                            if ($set_calling_code_prefix) {
                                $code = calling_code_prefix();
                                if ($code) {
                                    return $code . remove_zero($phone);
                                } else {
                                    if (isset($ci->group_calling_code) && !empty($ci->group_calling_code)) {
                                        return  $phone = $ci->group_calling_code . substr($phone, -9);
                                    } else {
                                        return  $phone = CALLING_CODE . remove_zero($phone);
                                    }
                                }
                            } else {
                                if (isset($ci->group_calling_code) && !empty($ci->group_calling_code)) {
                                    return  $phone = $ci->group_calling_code . substr($phone, -9);
                                } else {
                                    return  $phone = CALLING_CODE . remove_zero($phone);
                                }
                            }
                        } else {
                            return $phone;
                        }
                    } else {
                        return FALSE;
                    }
                }
            } else {
                if (strlen($phone) < 14 && strlen($phone) > 8) {
                    if (strlen($phone) < 10) {
                        if ($set_calling_code_prefix) {
                            $code = calling_code_prefix();
                            if ($code) {
                                return $code . remove_zero($phone);
                            } else {
                                if (isset($ci->group_calling_code) && !empty($ci->group_calling_code)) {
                                    return  $phone = $ci->group_calling_code . substr($phone, -9);
                                } else {
                                    return  $phone = CALLING_CODE . remove_zero($phone);
                                }
                            }
                        } else {
                            if (isset($ci->group_calling_code) && !empty($ci->group_calling_code)) {
                                return  $phone = $ci->group_calling_code . substr($phone, -9);
                            } else {
                                return  $phone = CALLING_CODE . remove_zero($phone);
                            }
                        }
                    } else {
                        if (substr($phone, 0, 1) == 0) {
                            if ($set_calling_code_prefix) {
                                $code = calling_code_prefix();
                                if ($code) {
                                    return $code . remove_zero($phone);
                                } else {
                                    if (isset($ci->group_calling_code) && !empty($ci->group_calling_code)) {
                                        return  $phone = $ci->group_calling_code . remove_zero($phone);
                                    } else {
                                        return  $phone = CALLING_CODE . remove_zero($phone);
                                    }
                                }
                            } else {
                                if (isset($ci->group_calling_code) && !empty($ci->group_calling_code)) {
                                    return  $phone = $ci->group_calling_code . remove_zero($phone);
                                } else {
                                    return  $phone = CALLING_CODE . remove_zero($phone);
                                }
                            }
                        } else {
                            return $phone;
                        }
                    }
                } else {
                    if ($strlen == FALSE) {
                        if (strlen($phone) <= 10) {
                            if ($set_calling_code_prefix) {
                                $code = calling_code_prefix();
                                if ($code) {
                                    return $code . remove_zero($phone);
                                } else {
                                    if (isset($ci->group_calling_code) && !empty($ci->group_calling_code)) {
                                        return  $phone = $ci->group_calling_code . substr($phone, -9);
                                    } else {
                                        return  $phone = CALLING_CODE . remove_zero($phone);
                                    }
                                }
                            } else {
                                if (isset($ci->group_calling_code) && !empty($ci->group_calling_code)) {
                                    return  $phone = $ci->group_calling_code . substr($phone, -9);
                                } else {
                                    return  $phone = CALLING_CODE . remove_zero($phone);
                                }
                            }
                        } else {
                            return $phone;
                        }
                    } else {
                        if (substr($phone, 0, 1) == 0) {
                            if ($set_calling_code_prefix) {
                                $code = calling_code_prefix();
                                if ($code) {
                                    return $code . remove_zero($phone);
                                } else {
                                    if (isset($ci->group_calling_code) && !empty($ci->group_calling_code)) {
                                        return  $phone = $ci->group_calling_code . substr($phone, -9);
                                    } else {
                                        return  $phone = CALLING_CODE . remove_zero($phone);
                                    }
                                }
                            } else {
                                if (isset($ci->group_calling_code) && !empty($ci->group_calling_code)) {
                                    return  $phone = $ci->group_calling_code . substr($phone, -9);
                                } else {
                                    return  $phone = CALLING_CODE . remove_zero($phone);
                                }
                            }
                        } else if (strlen($phone) > 9) {
                            return $phone;
                        } else {
                            return FALSE;
                        }
                    }
                }
            }
        } else {
            return FALSE;
        }
    } else {
        return FALSE;
    }
}

function invalid_phone($phone = 0)
{
    if ($phone) {
        $phone = substr($phone, -9);
        return '0' . $phone;
    }
}

function calling_code_prefix()
{
    $ci = &get_instance();
    $segment = $ci->uri->segment(1);
    if (preg_match('/login/', $segment) || preg_match('/signup/', $segment) || preg_match('/group/', $segment) || preg_match('/member/', $segment)) {
    } else {
        // define('COUNTRY_CODE',"TZ");
        // return '255';
    }
    if (preg_match('/\.local/', $_SERVER['HTTP_HOST'])) {
        define('COUNTRY_CODE', "KE");
        return '254';
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_COOKIE['COUNTRY_CODE']) && isset($_COOKIE['CALLING_CODE'])) {
            $details = new StdClass();
            $details = new StdClass();
            $details->calling_code = $_COOKIE['CALLING_CODE'];
            $details->country_code2 = $_COOKIE['COUNTRY_CODE'];
        } else {
            $details = getCountry($ip);
        }
        if ($details && (isset($details->calling_code) || isset($details->country_name))) {
            if (isset($details->calling_code) && $details->calling_code) {
                $country_code = $details->country_code2;
                $calling_code =  $details->calling_code;
            } elseif (isset($details->country_name) && $details->country_name) {
                $code = $details->country_name;
                $current_country = $ci->countries_m->get_country_by_calling_code($code);
                if ($current_country) {
                    $country_code = $details->country;
                    $calling_code =  $current_country->calling_code;
                } else {
                    $calling_code = "255";
                }
            } else {
                $calling_code = "255";
            }
            $code = str_replace("+", "", $calling_code);
            if (is_numeric($code)) {
                $calling_code = $code;
            } else {
                $calling_code = intval($code);
            }
            define('COUNTRY_CODE', filter_var($country_code, FILTER_SANITIZE_STRING));
            return $calling_code;
        } else {
            return FALSE;
        }
    }
}

function getCountry($ipaddress = '')
{
    $apiKey = '742bc5968adf476ca5ca2eb90e31d4da';
    $lang = "en";
    $fields = "*";
    $excludes = "";
    $url = "https://api.ipgeolocation.io/ipgeo?apiKey=" . $apiKey . "&ip=" . $ipaddress . "&lang=" . $lang . "&fields=" . $fields . "&excludes=" . $excludes;
    $cURL = curl_init();
    curl_setopt($cURL, CURLOPT_URL, $url);
    curl_setopt($cURL, CURLOPT_HTTPGET, true);
    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Accept: application/json'
    ));
    $result = curl_exec($cURL);
    return json_decode($result);
}


function page_get_contents($url = '')
{
    $curl_handle = curl_init();
    curl_setopt($curl_handle, CURLOPT_URL, $url);
    curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Your application name');
    $query = curl_exec($curl_handle);
    curl_close($curl_handle);
    print_r($query);
    die;
}

function url_get_contents($url, $useragent = 'cURL', $headers = false, $follow_redirects = true, $debug = false)
{

    // initialise the CURL library
    $ch = curl_init();

    // specify the URL to be retrieved
    curl_setopt($ch, CURLOPT_URL, $url);

    // we want to get the contents of the URL and store it in a variable
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // specify the useragent: this is a required courtesy to site owners
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);

    // ignore SSL errors
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // return headers as requested
    if ($headers == true) {
        curl_setopt($ch, CURLOPT_HEADER, 1);
    }

    // only return headers
    if ($headers == 'headers only') {
        curl_setopt($ch, CURLOPT_NOBODY, 1);
    }

    // follow redirects - note this is disabled by default in most PHP installs from 4.4.4 up
    if ($follow_redirects == true) {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    }

    // if debugging, return an array with CURL's debug info and the URL contents
    if ($debug == true) {
        $result['contents'] = curl_exec($ch);
        $result['info'] = curl_getinfo($ch);
    }

    // otherwise just return the contents as a variable
    else $result = curl_exec($ch);

    // free resources
    curl_close($ch);

    // send back the data
    return $result;
}


function currency($str)
{
    if (preg_match('/^[0-9,.]+$/', $str)) {
        $amount = floatval(str_replace(',', '', $str));
        if ($amount > 0) {
            return $amount;
        } else {
            return FALSE;
        }
    } else {
        return FALSE;
    }
}
function currency_convert($str)
{
    if (preg_match('/^[0-9,.]+$/', $str)) {
        $amount = floatval(str_replace(',', '', $str));
        if ($amount > 0) {
            return $amount;
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}



function number_to_words($number)
{
    if ($number == number_format($number, 0)) {
        $number = number_format($number, 0);
    }
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'fourty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );

    if (!is_numeric($number)) {
        $number = str_replace(',', '', $number);
        if (!is_numeric($number)) {
            return false;
        }
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . number_to_words(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= number_to_words($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}

function remove_subdomain_from_url($url = 'chamasoft.com', $protocol = "http://")
{
    $domains = explode(".", $_SERVER['HTTP_HOST']);
    $dots = 3;
    if (strlen($domains[(count($domains) - 1)]) == 2) {
        $dots = 4;
    }
    if (isset($_SERVER['REQUEST_URI'])) {
        $request_uri = $_SERVER['REQUEST_URI'];
    } else {
        $request_uri = '';
    }
    if (count($domains) == $dots && $domains[0] != "www") {
        if (preg_match('/(http)/', $url)) {
            redirect($url . $request_uri, 'refresh');
        } else {
            redirect($protocol . $url . $request_uri, 'refresh');
        }
    }
}

function timestamp_to_time_elapsed($ptime)
{
    $etime = time() - $ptime;

    if ($etime < 1) {
        return '0 seconds';
    }

    $a = array(
        365 * 24 * 60 * 60  =>  'year',
        30 * 24 * 60 * 60  =>  'month',
        24 * 60 * 60  =>  'day',
        60 * 60  =>  'hour',
        60  =>  'minute',
        1  =>  'second'
    );
    $a_plural = array(
        'year'   => 'years',
        'month'  => 'months',
        'day'    => 'days',
        'hour'   => 'hours',
        'minute' => 'minutes',
        'second' => 'seconds'
    );

    foreach ($a as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
        }
    }
}

function elapsed_time($elapsed)
{
    $etime = time() - $elapsed;

    if ($etime < 1) {
        return '0 seconds';
    }

    $a = array(
        365 * 24 * 60 * 60  =>  'year',
        30 * 24 * 60 * 60  =>  'month',
        24 * 60 * 60  =>  'day',
        60 * 60  =>  'hour',
        60  =>  'minute',
        1  =>  'second'
    );
    $a_plural = array(
        'year'   => 'years',
        'month'  => 'months',
        'day'    => 'days',
        'hour'   => 'hours',
        'minute' => 'minutes',
        'second' => 'seconds'
    );

    foreach ($a as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . '';
        }
    }
}

function separate_account($account_id_type = 0)
{
    if ($account_id_type) {
        $type = '';
        $account_id = '';
        if (preg_match('/bank/', $account_id_type)) {
            $exploded_account = explode('-', $account_id_type);
            $type = 1;
            $account_id = trim($exploded_account[1]);
        } else if (preg_match('/sacco/', $account_id_type)) {
            $exploded_account = explode('-', $account_id_type);
            $type = 2;
            $account_id = trim($exploded_account[1]);
        } else if (preg_match('/mobile/', $account_id_type)) {
            $exploded_account = explode('-', $account_id_type);
            $type = 3;
            $account_id = trim($exploded_account[1]);
        } else if (preg_match('/petty/', $account_id_type)) {
            $exploded_account = explode('-', $account_id_type);
            $type = 4;
            $account_id = trim($exploded_account[1]);
        }

        return (object)array('account_type' => $type, 'account_id' => $account_id);
    } else {
        return FALSE;
    }
}

function reconstruct_account($account_type = 0, $account_id = 0)
{
    if ($account_id && $account_type) {
        $account_id = trim($account_id);
        if ($account_type == 1) {
            return 'bank-' . $account_id;
        } else if ($account_type == 2) {
            return 'sacco-' . $account_id;
        } else if ($account_type == 3) {
            return 'mobile-' . $account_id;
        } else if ($account_type == 4) {
            return 'petty-' . $account_id;
        }
    } else {
        return FALSE;
    }
}

function valid_currency($str = '')
{
    if (preg_match('/^[0-9,.]+$/', $str)) {
        if ((is_numeric($str) || is_float($str)) && (round($str) == 0)) {
            if ($str > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return str_replace(',', '', $str);
        }
    } else {
        if ((is_numeric($str) || is_float($str)) && (round($str) == 0)) {
            if ($str > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
}


function group_account($account_id, $accounts = array())
{
    $account = '';
    if (empty($accounts[$account_id])) {
        foreach ($accounts as $key => $value) {
            if (is_array($value)) {
                if (array_key_exists($account_id, $value)) {
                    $account = $value[$account_id];
                }
            }
        }
    } else {
        $account = $accounts[$account_id];
    }

    return $account;
}


function is_character_allowed($character = '')
{
    $allowed_special_characters = array(
        '\'',
        '`',
        ' ',
        '-',
    );

    $character = str_replace(' ', '', trim($character));
    if ($character) {
        if (preg_match('/[\W]+/', $character)) {
            if (in_array($character, $allowed_special_characters)) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return TRUE;
        }
    } else {
        return TRUE;
    }
}
function month_name_to_name($month_number = 1)
{
    $dateObj   = DateTime::createFromFormat('!m', $month_number);
    return $dateObj->format('F');
}


function days_ago($timestamp = 0)
{
    $timeago = time() - $timestamp;
    if ($timeago > 0) {
        return ($timeago / 86400);
    }
}

function daysAgo($timestamp = 0, $markupdate = 0)
{
    $markupdate = $markupdate ?: time();
    $daysAgo = '0 days';
    $elapsedTime = abs(round($markupdate - $timestamp));
    $secondsArray = array(
        365 * 24 * 60 * 60,
        30 * 24 * 60 * 60,
        // 7 * 24 * 60 * 60,
        24 * 60 * 60,
        60 * 60,
        60,
        1
    );
    $timeDescriptions = array(
        "year",
        "month",
        //"week",
        "day",
        "hour",
        "minute",
        "second"
    );
    for ($i = 0; $i < count($secondsArray); $i++) {
        $convertedTime = $elapsedTime / $secondsArray[$i];
        if ($convertedTime >= 1) {
            $time = round($convertedTime);
            $daysAgo = $time . " " . (($time > 1) ? $timeDescriptions[$i] . "s" : $timeDescriptions[$i]);
            break;
        } else {
            continue;
        }
    }

    return $daysAgo;
}

function generate_slug($name = '')
{
    if ($name) {
        $name = str_replace(' ', '-', $name);
        $name = str_replace('.', '-', $name);
        return strtolower(trim($name));
    } else {
        return 'false';
    }
}

function generate_menu_slug($name = '')
{
    if ($name) {
        //$name = delete_all_between('[',']',$name);
        $name = trim($name);
        $name = str_replace(' ', '_', $name);
        $name = str_replace('.', '_', $name);
        $name = str_replace('<br>', '', $name);
        $file = strtolower(trim($name));
        return $file;
    } else {
        return 'false';
    }
}

function translate($loops = array())
{
    $ci = &get_instance();
    return $ci->languages_m->generate_loop_slug($loops);
}


function escape_single_qoutes($string = "")
{
    //return preg_replace_all("/([^\])'/","$1\'",$string);
    return addslashes($string);
}

function explode_str_to_array($str = '')
{
    if ($str) {
        if (preg_match('/,/', $str)) {
            return explode(',', $str);
        } else {
            return $str;
        }
    }
}

function limit_text($text, $limit)
{
    if (str_word_count($text, 0) > $limit) {
        $words = str_word_count($text, 2);
        $pos = array_keys($words);
        $text = substr($text, 0, $pos[$limit]) . '...';
    }
    return $text;
}

function remove_special_characters($string = '')
{
    $string = str_replace(' ', '', strtolower($string)); // Replaces all spaces with hyphens.
    return preg_replace('/[^A-Za-z0-9]/', '', $string); // Removes special chars.
}


function printDebug($str)
{
    echo date('Ymd H:i:s ') . $str . "\r\n";
}


if (!function_exists('apache_request_headers')) {
    function apache_request_headers()
    {
        $arrCasedHeaders = array(
            // HTTP
            'Dasl'             => 'DASL',
            'Dav'              => 'DAV',
            'Etag'             => 'ETag',
            'Mime-Version'     => 'MIME-Version',
            'Slug'             => 'SLUG',
            'Te'               => 'TE',
            'Www-Authenticate' => 'WWW-Authenticate',
            // MIME
            'Content-Md5'      => 'Content-MD5',
            'Content-Id'       => 'Content-ID',
            'Content-Features' => 'Content-features',
        );
        $arrHttpHeaders = array();
        foreach ($_SERVER as $strKey => $mixValue) {
            if ('HTTP_' !== substr($strKey, 0, 5)) {
                continue;
            }

            $strHeaderKey = strtolower(substr($strKey, 5));

            if (0 < substr_count($strHeaderKey, '_')) {
                $arrHeaderKey = explode('_', $strHeaderKey);
                $arrHeaderKey = array_map('ucfirst', $arrHeaderKey);
                $strHeaderKey = implode('-', $arrHeaderKey);
            } else {
                $strHeaderKey = ucfirst($strHeaderKey);
            }

            if (array_key_exists($strHeaderKey, $arrCasedHeaders)) {
                $strHeaderKey = $arrCasedHeaders[$strHeaderKey];
            }

            $arrHttpHeaders[$strHeaderKey] = $mixValue;
        }

        return $arrHttpHeaders;
    }
}

if (!function_exists('random_string')) {
    function random_string($type = '', $length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

function openssl_key_encrypt($security_pass = '')
{
    if ($security_pass) {
        $fp = fopen("./assets/certificates/encryptioncer.crt", "r");
        $pub_key_string = fread($fp, 8192);
        fclose($fp);
        $PK = openssl_get_publickey($pub_key_string);
        if (!$PK) {
            echo "Cannot get public key";
            die;
        }
        openssl_public_encrypt($security_pass, $crypttext, $pub_key_string);
        return (base64_encode($crypttext));
    } else {
        return FALSE;
    }
}

function unique_request($request_id = 0)
{
    if ($request_id) {
        $file = "./logs/request_ids.txt";
        if (!file_exists($file)) {
            file_put_contents($file, "\n", FILE_APPEND);
        }
        $contents = file_get_contents($file);
        $pattern = preg_quote($request_id, '/');
        $pattern = "/^.*$pattern.*\$/m";
        if (preg_match_all($pattern, $contents, $matches)) {
            return FALSE;
        } else {
            file_put_contents($file, $request_id . "\n", FILE_APPEND);
            return TRUE;
        }
    } else {
        return FALSE;
    }
}

function migrate_group_member_balances($group_id = 0)
{
    if ($group_id) {
        $file = "./logs/group_member_balances_request.txt";
        if (!file_exists($file)) {
            file_put_contents($file, "\n", FILE_APPEND);
        }
        file_put_contents($file, $group_id . "\n", FILE_APPEND);
    }
}

function get_requested_group_ids()
{
    $file = "./logs/group_member_balances_request.txt";
    if (!file_exists($file)) {
        file_put_contents($file, "\n", FILE_APPEND);
    }
    $contents = file_get_contents($file);
    return array_filter(preg_split("/\\r\\n|\\r|\\n/", $contents));
}

function update_group_id_request($group_id = 0)
{
    if ($group_id) {
        $file = "./logs/group_member_balances_request.txt";
        if (!file_exists($file)) {
            file_put_contents($file, "\n", FILE_APPEND);
        }
        $contents = file_get_contents($file);
        $str = str_replace($group_id, '', $contents);
        file_put_contents($file, $str);
    } else {
        return FALSE;
    }
}

function update_request_id($request_id = 0)
{
    if ($request_id) {
        $file = "./logs/request_ids.txt";
        if (!file_exists($file)) {
            file_put_contents($file, "\n", FILE_APPEND);
        }
        $contents = file_get_contents($file);
        $str = str_replace($request_id, '', $contents);
        file_put_contents($file, $str);
    } else {
        return FALSE;
    }
}


//1@MkNGuCf!#)iOx
function clean_notification($string = '')
{
    return strip_tags($string);
}


function convert_currency($amount = 0, $currency = 'KES', $destination = 'USD', $charge_fee = TRUE)
{
    if ($amount && $currency) {
        $destination = $destination ?: 'USD';
        if ($destination == $currency) {
            return $amount;
        }
        $ci = &get_instance();
        $url = 'http://data.fixer.io/api/latest?access_key=8832f730978153d1814d34f62371faa5&format=1';
        $result = $ci->curl->get_request($url);
        if ($result) {
            $res = json_decode($result);
            if ($res) {
                $rates = isset($res->rates) ? $res->rates : '';
                if ($rates) {
                    $amount = currency($amount);
                    $usd = $rates->$destination;
                    $kes = $rates->$currency;
                    $amount = ($amount * ($usd / $kes));
                    if ($charge_fee) {
                        return (1.03 * $amount);
                    } else {
                        return ($amount / 1.03);
                    }
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    } else {
        return FALSE;
    }
}


function gen_uuid()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),

        // 16 bits for "time_mid"
        mt_rand(0, 0xffff),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand(0, 0x0fff) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand(0, 0x3fff) | 0x8000,

        // 48 bits for "node"
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}

function hashed_user_id($user_id = 0)
{
    return hash_hmac(
        'sha256', // hash function
        $user_id,
        'vng3EKZQY6vGblKZOFeE7EE_V0k3eWWNHYfZ-P3V' // secret key (keep safe!)
    );
}

//returns ip information from 'www.geoplugin.net'
function ip_meta($ip = NULL, $purpose = "location", $deep_detect = TRUE)
{
    $output = NULL;
    if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
        $ip = $_SERVER["REMOTE_ADDR"];
        if ($deep_detect) {
            if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
    }
    //return $ip;
    $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
    $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
    $continents = array(
        "AF" => "Africa",
        "AN" => "Antarctica",
        "AS" => "Asia",
        "EU" => "Europe",
        "OC" => "Australia (Oceania)",
        "NA" => "North America",
        "SA" => "South America"
    );
    if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
        if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
            switch ($purpose) {
                case "location":
                    $output = array(
                        "city"           => @$ipdat->geoplugin_city,
                        "state"          => @$ipdat->geoplugin_regionName,
                        "country"        => @$ipdat->geoplugin_countryName,
                        "country_code"   => @$ipdat->geoplugin_countryCode,
                        "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                        "continent_code" => @$ipdat->geoplugin_continentCode
                    );
                    break;
                case "address":
                    $address = array($ipdat->geoplugin_countryName);
                    if (@strlen($ipdat->geoplugin_regionName) >= 1)
                        $address[] = $ipdat->geoplugin_regionName;
                    if (@strlen($ipdat->geoplugin_city) >= 1)
                        $address[] = $ipdat->geoplugin_city;
                    $output = implode(", ", array_reverse($address));
                    break;
                case "city":
                    $output = @$ipdat->geoplugin_city;
                    break;
                case "state":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "region":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "country":
                    $output = @$ipdat->geoplugin_countryName;
                    break;
                case "countrycode":
                    $output = @$ipdat->geoplugin_countryCode;
                    break;
            }
        }
    }
    print_r($output);
    die;
    return $output;

    //********** GET VISITOR IP ADDRESS THIS WAY ***********//
    //echo ip_info("Visitor", "Country"); // India
    //echo ip_info("Visitor", "Country Code"); // IN
    //echo ip_info("Visitor", "State"); // Andhra Pradesh
    //echo ip_info("Visitor", "City"); // Proddatur
    //echo ip_info("Visitor", "Address"); // Proddatur, Andhra Pradesh, India
    //print_r(ip_info("Visitor", "Location")); // Array ( [city] => Proddatur [state] => Andhra Pradesh [country] => India [country_code] => IN [continent] => Asia [continent_code] => AS )

    //********** GET ANY IP ADDRESS THIS WAY ***********//
    //echo ip_info("173.252.110.27", "Country"); // United States
    //echo ip_info("173.252.110.27", "Country Code"); // US
    //echo ip_info("173.252.110.27", "State"); // California
    //echo ip_info("173.252.110.27", "City"); // Menlo Park
    //echo ip_info("173.252.110.27", "Address"); // Menlo Park, California, United States
    //print_r(ip_info("173.252.110.27", "Location")); // Array ( [city] => Menlo Park [state] => California [country] => United States [country_code] => US [continent] => North America [continent_code] => NA )

}


function delete_all_between($beginning, $end, $string)
{
    $beginningPos = strpos($string, $beginning);
    $endPos = strpos($string, $end);
    if ($beginningPos === false || $endPos === false) {
        return $string;
    }

    $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);

    return delete_all_between($beginning, $end, str_replace($textToDelete, '', $string)); // recursion to ensure all occurrences are replaced
}

function generate_months_array($start, $end)
{
    $current = $start;
    $ret = array();
    while ($current <= $end) {
        $next = @date('Y-M-01', $current) . "+1 month";
        $current = @strtotime($next);
        $ret[] = date('M Y', $current);
    }
    return ($ret);
}

function generate_previous_months_array($months = 12)
{
    for ($i = 1; $i <= $months; $i++) {
        $year = date("Y", strtotime(date('Y-m-01') . " -$i months"));
        $month = date("M", strtotime("$i months"));
        $arr[] = $month;
    }
    return $arr;
}

function generate_previous_year_months_array($months = 12)
{
    for ($i = 1; $i <= $months; $i++) {
        $year = date("Ym", strtotime(date('Y-m-01') . " -$i months"));
        $month = date("Ym", strtotime("$i months"));
        $arr[] = $month;
    }
    return $arr;
}


function create_zip($files = array(), $destination = '', $overwrite = false)
{
    if (file_exists($destination) && !$overwrite) {
        return false;
    }
    //vars
    $valid_files = array();
    //if files were passed in...
    if (is_array($files)) {
        //cycle through each file
        foreach ($files as $file) {
            //make sure the file exists
            if (file_exists($file)) {
                $valid_files[] = $file;
            }
        }
    }
    //if we have good files...
    if (count($valid_files)) {
        //create the archive
        $path =  realpath($destination);
        $zip = new ZipArchive();
        //$overwrite ? ZIPARCHIVE::OVERWRITE : 
        $zip_file = $path . '.zip';
        if ($zip->open($zip_file, ZIPARCHIVE::CREATE) !== true) {
            return false;
        }
        $file2 = '';
        foreach ($valid_files as $file) {
            $values = array_pad(explode('/', $file), 2, $file);
            if (is_array($values)) {
                $num = count($values) - 1;
                if ($num > 0) {
                    $file2 = $values[$num];
                    if ($num - 1 >= 0) {
                        $file2 = $values[$num];
                    }
                }
            }
            if ($file) {
                if ($file2) {
                    $zip->addFile($file, $file2);
                } else {
                    $zip->addFile($file, $file);
                }
            }
        }
        $zip->close();
        if (file_exists($zip_file)) {
            foreach ($files as $name => $file_path) {
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            return $destination . '.zip';
        } else {
            return FALSE;
        }
    } else {
        return false;
    }
}

function unzip_file($zip_file = '')
{
    $zip = new ZipArchive;
    $res = $zip->open($zip_file);
    $directory = './logs/deleted_groups/unzipped' . time();
    if ($res === TRUE) {
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        $zip->extractTo($directory);
        $zip->close();
        return $directory;
    } else {
        echo 'doh!';
    }
}

function generate_password($length = 8, $complexity = 1)
{
    $alpha = "abcdefghjkmnpqrtuvwxyz";
    $alpha_upper = strtoupper($alpha);
    $numeric = "123456789";
    $special = "+=!@$#*%[]{}";
    $chars = "";

    if ($complexity == 1) {
        $chars = $alpha . $numeric; //. $alpha_upper
    } else {
        $chars = $alpha . $alpha_upper . $numeric . $special;
    }

    $len = strlen($chars);
    $pw = '';

    for ($i = 0; $i < $length; $i++)
        $pw .= substr($chars, rand(0, $len - 1), 1);

    // the finished password
    $pw = str_shuffle($pw);

    return $pw;
}

function valid_date($date = '', $n_year = 2)
{
    if ($date) {
        $timestamp = strtotime($date);
        if (date('Ymd', $timestamp) <= date('Ymd', time())) {
            return TRUE;
        } else {
            return FALSE;
        }






        // $last_timestamp = strtotime('-2 years', time());
        // $today = time();
        // $tomorrow_timestamp = strtotime('+1 day', time());
        // $date_check = strtotime($date);
        // if($date_check > $last_timestamp){
        //     //print_r(date('y',$tomorrow_timestamp));
        //     //echo "<br>";
        //     //print_r(date('y',$date_check));echo "<br>";
        //     if(date('dm',$date_check) < date('dm',$tomorrow_timestamp)){
        //         if(date('y',$date_check) <= date('y',$tomorrow_timestamp)){
        //             return TRUE;
        //         }else{
        //             return FALSE;
        //         }
        //     }else{
        //        return FALSE;  
        //     }
        // }else{
        //     return FALSE;
        // }
    } else {
        return FALSE;
    }
}

function xss_clean_input($data)
{
    $data = trim($data);
    //remove slashes
    $data = stripslashes($data);
    $data = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $data);
    $data = (filter_var($data, FILTER_SANITIZE_STRING));
    // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
    $data = str_replace(array('[\', \']'), '', $data);
    $data = preg_replace('/\[.*\]/U', '', $data);
    $data = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $data);
    $data = htmlentities($data, ENT_COMPAT, 'utf-8');
    $data = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $data);
    $data = preg_replace(array('/[^a-z0-9]/i', '/[-]+/'), '-', $data);
    $data = utf8_decode($data);;
    return $data;
}

function checkPassword($password)
{
    $strength = ['Excellent', 'Strong', 'Good', 'Week'];

    if (isEnoughLength($password, 12) && containsMixedCase($password) && containsDigits($password) && containsSpecialChars($password)) {
        return $strength[0];
    } elseif (isEnoughLength($password, 10) && containsMixedCase($password) && containsDigits($password)) {
        return $strength[1];
    } elseif (isEnoughLength($password, 8) && containsMixedCase($password)) {
        return $strength[2];
    } elseif (isEnoughLength($password, 8) && containsDigits($password)) {
        return $strength[2];
    } elseif (isEnoughLength($password, 8) && containsSpecialChars($password)) {
        return $strength[2];
    } else {
        return $strength[3];
    }
}

function isEnoughLength($password, $length)
{
    if (empty($password)) {
        return false;
    } elseif (strlen($password) < $length) {
        return false;
    } else {
        return true;
    }
}

function containsMixedCase($password)
{
    if (preg_match('/[a-z]+/', $password) && preg_match('/[A-Z]+/', $password)) {
        return true;
    } else {
        return false;
    }
}

function containsDigits($password)
{
    if (preg_match("/\d/", $password)) {
        return true;
    } else {
        return false;
    }
}

function containsSpecialChars($password)
{
    $pattern = '/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z]).{8,20}$/';
    if (preg_match($pattern, $password)) {
        return true;
    } else {
        return false;
    }

    /*if (preg_match("/[^\da-z]/", $password)) {
        return true;
    } else {
        return false;
    }*/
}


function encrypt_json_encode($data = '')
{
    $ci = &get_instance();
    if (defined('NEWAPPLICATION')) {
        if (array_key_exists('response', $data)) {
            $data = $data['response'];
        }
    }
    if(preg_match('/websacco\.com/', $_SERVER['HTTP_HOST']) || preg_match('/eazzykikundidemo\.com/', $_SERVER['HTTP_HOST'])){
        return json_encode($data);
    }
    $ci->load->library('Encryptdecrypt');
    $key = random_string('alnum', 16);
    $encrypted_key = $ci->encryptdecrypt->encryptPublicCertMobile($key);
    $body = $ci->encryptdecrypt->encryptPrivate($key, json_encode($data));
    if (defined('LOCALENVIRONMENT')) {
        //echo 'response '.$ci->encryptdecrypt->decryptMobile($encrypted_key,$body);
        return $ci->encryptdecrypt->decryptMobile($encrypted_key, $body);
    } else {
        return json_encode(array(  
            'secret' => $encrypted_key,
            'body' => $body,
        ));
    }
}


function generate_previous_years_array($start = '', $end = '')
{
    $first_day_year = date("Y-m-d", $start);
    $start    = (new DateTime($first_day_year))->modify('first day of this year');
    $end      = (new DateTime(date('Y-m-d', $end)))->modify('first day of next month');
    $interval = DateInterval::createFromDateString('1 month');
    $period   = new DatePeriod($start, $interval, $end);
    foreach ($period as $dt) {
        $arr[] =  (object)array(
            'year' => $dt->format("Y"),
            'month' => $dt->format('m')
        );
    }
    return $arr;
}

function generate_years_from_dates($start, $end)
{
    $begin = new DateTime(date('Y-m-d', $start));
    $end = new DateTime(date('Y-m-d', $end));
    $arr = array();
    while ($begin <= $end) {
        $arr[] =  $begin->format('Y');
        $begin->modify('first day of next month');
    }
    return array_unique($arr);
}


function generate_years_months_from_dates($start_date, $end_date)
{
    $first_day_year = date("Y-01-01", $start_date);
    $current_date = strtotime($first_day_year);
    $end_date = $end_date;
    $out = array();
    while ($current_date <= $end_date) {
        $out[date("Y", $current_date)][date("m", $current_date)] = (int)date("m", $current_date);
        $current_date = strtotime("+1 days", $current_date);
    }
    return $out;
}

function short_month_number_to_name($month_number = 1)
{
    $dateObj   = DateTime::createFromFormat('!m', $month_number);
    return $dateObj->format('M');
}
