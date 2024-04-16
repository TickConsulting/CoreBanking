<?php
	if(preg_match('/local/', $_SERVER['HTTP_HOST'])){
		redirect(site_url('login'));
	}else{
		// echo 'No landing page on local please go to login.';
    	echo file_get_contents('https://tickconsulting.co.ke');

		
	}
?>