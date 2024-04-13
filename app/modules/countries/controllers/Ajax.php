<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{

	function __construct(){
        parent::__construct();
        $this->load->model('countries_m');
    }

    function get_currency_option_by_code(){
        $currency_code = $this->input->post('currency_code');
        $currency = $this->countries_m->get_country_by_currency_code($currency_code);
        if($currency){
            echo $currency->id;
        }else{
            echo 0;
        }
    }

}