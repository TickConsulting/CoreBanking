<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Curl_post_data{

	protected $ci;

	public function __construct(){
		$this->ci= & get_instance();
	}

	public function curl_post_xml($xml_file='',$url=''){
		if($xml_file && $url){
			$ch = curl_init();  
	        curl_setopt($ch,CURLOPT_URL,$url);
	        curl_setopt( $ch, CURLOPT_POST, true );
	        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_file);    
	        $output=curl_exec($ch);
	        curl_close($ch);
	        return  $output;
		}else{
			$this->ci->session->set_flashdata('error','Ensure all parameters are passed');
			return FALSE;
		}
	}

	public function curl_post_json_pdf($json_file='',$url='',$filename='Chamasoft Report',$prompt_download = TRUE){
    	if($url && $json_file){
    		$filename = trim($filename);
    		set_time_limit(0);
	        ini_set('memory_limit','512M');
	        ini_set('max_execution_time', 300);
    		$ch = curl_init();  
	        curl_setopt($ch,CURLOPT_URL,$url);
	        curl_setopt($ch,CURLOPT_POST, true );
	        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
	        curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	        curl_setopt($ch,CURLOPT_POSTFIELDS, $json_file);    
	        $output=curl_exec($ch);
	        if($prompt_download){
	        	header('Cache-Control: public'); 
				header('Content-type: application/pdf');
				header('Content-Disposition: attachment; filename="'.$filename.'".pdf"');
				header('Content-Length: '.strlen($output));
	        }
	        curl_close($ch);
	        if($prompt_download){
	        	return($output);
	        }else{
	        	if($output){
	        		$directory = '/uploads/downloads';
	        		if(!is_dir('./'.$directory)){
	        			mkdir('./'.$directory,0777,TRUE);
	        		}
	        		$file = str_replace(' ','',$directory."/".$filename.".pdf");
		        	file_put_contents('./'.$file, $output);
		        	return $file;
		        }else{
		        	return FALSE;
		        }
	        }
	        
    	}
    	else{
    		$this->ci->session->set_flashdata('error','Ensure all parameters are passed');
			return FALSE;
    	}
    }


    public function curl_post_json_excel($json_file='',$url='',$filename='Chamasoft Report'){
    	if($url && $json_file){
    		$filename = trim($filename);
    		set_time_limit(0);
	        ini_set('memory_limit','2048M');
	        ini_set('max_execution_time', 1200);
    		$ch = curl_init();  
	        curl_setopt($ch,CURLOPT_URL,$url);
	        curl_setopt($ch,CURLOPT_POST, true );
	        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
	        curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	        curl_setopt($ch,CURLOPT_POSTFIELDS, $json_file);    
	        $output=curl_exec($ch);
	        header('Cache-Control: public'); 
			header('Content-type: application/excel');
			header('Content-Disposition: attachment; filename="'.$filename.'.xlsx"');
			header('Content-Length: '.strlen($output));
	        curl_close($ch);
	        return($output);
    	}
    	else{
    		$this->ci->session->set_flashdata('error','Ensure all parameters are passed');
			return FALSE;
    	}
    }

    public function post_json($json_file = '',$url = ''){
    	if($url && $json_file){
    		$ch = curl_init();  
	        curl_setopt($ch,CURLOPT_URL,$url);
	        curl_setopt($ch,CURLOPT_POST, true );
	        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
	        curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	        curl_setopt($ch,CURLOPT_POSTFIELDS, $json_file);  
	        $output = curl_exec($ch);
	        curl_close($ch);
	        return($output);
    	}
    	else{
    		$this->ci->session->set_flashdata('error','Ensure all parameters are passed');
			return FALSE;
    	}
    }

    public function post($post_data = '',$url = ''){
    	if($url && $post_data){
    		$ch = curl_init();  
	        curl_setopt($ch,CURLOPT_URL,$url);
	        curl_setopt($ch,CURLOPT_POST, true );
	        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
	        //curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	        curl_setopt($ch,CURLOPT_POSTFIELDS, $post_data);    
	        $output=curl_exec($ch);
	        curl_close($ch);
	        return($output);
    	}
    	else{
    		$this->ci->session->set_flashdata('error','Ensure all parameters are passed');
			return FALSE;
    	}
    }

}
?>