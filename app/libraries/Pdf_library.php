<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* 

 *  ======================================= 

 *  Author     : Muhammad Surya Ikhsanudin 

 *  License    : Protected 

 *  Email      : mutofiyah@gmail.com 

 *   

 *  Dilarang merubah, mengganti dan mendistribusikan 

 *  ulang tanpa sepengetahuan Author 

 *  ======================================= 

 */  

//require_once "./assets/pdf_gen/mpdf.php"; 
require_once './assets/vendor/autoload.php';

class PDF_library { 

    public function __construct() { 

       // parent::__construct();
        $this->mpdf = new \Mpdf\Mpdf();
        $url = base_url().'assets/styling/style.css';
        $arrContextOptions=array(
			   "ssl"=>array(
			      "verify_peer"=>false,
			      "verify_peer_name"=>false,
			   ),
			);
        	$this->stylesheet = file_get_contents($url, false, stream_context_create($arrContextOptions));
        
    }  



    public function generate_full_view($html=''){
    	$this->mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
		$this->mpdf->mirrorMargins = 1;
		$this->mpdf->SetDisplayMode('fullpage');
		$this->footer = '{PAGENO} of {nbpg} pages||{PAGENO} of {nbpg} pages';
		$this->mpdf->setFooter($this->footer) ;
		$this->mpdf->setHeader(date("F j, Y, g:i a"));
		//print_r($html); die();
		$this->mpdf->WriteHTML($html);
		$this->mpdf->Output();
		exit;
    }
    
    public function generate_full_loan_view($html=''){
    	$mpdf=new mPDF(); 
		$mpdf->mirrorMargins = 1;
		$mpdf->SetDisplayMode('fullpage');
		$footer = '{PAGENO} of {nbpg} pages||{PAGENO} of {nbpg} pages';
		$mpdf->setFooter($footer) ;
		$mpdf->setHeader(date("F j, Y, g:i a"));
		$mpdf->WriteHTML($html);
		$mpdf->Output();
		exit;
    }

    function generate_small_view($html=''){
    	define('_MPDF_PATH','../');

		$mpdf=new mPDF('c','A4','','',20,15,48,25,10,10,L); 
		$mpdf->SetTitle("Acme Trading Co. - Invoice");
		$mpdf->SetAuthor("Acme Trading Co.");
		$mpdf->SetWatermarkText("Paid");
		$mpdf->showWatermarkText = false;
		$mpdf->watermark_font = 'DejaVuSansCondensed';
		$mpdf->watermarkTextAlpha = 0.1;
		$mpdf->setHeader(date("F j, Y, g:i a"));
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->WriteHTML($html);
		$mpdf->Output(); exit;
    }

    function paged_pdf($html=''){
    	$mpdf=new mPDF(); 
		$mpdf->mirrorMargins = 1;
		$mpdf->SetDisplayMode('fullpage','two');
		$stylesheet = file_get_contents('./assets/styling/paged.css');
		$mpdf->WriteHTML($stylesheet,1);
		$footer = '{PAGENO} of {nbpg} pages||{PAGENO} of {nbpg} pages';
		$mpdf->setFooter($footer) ;
		$mpdf->setHeader(date("F j, Y, g:i a"));
		$mpdf->WriteHTML($html);
		$mpdf->Output();
		exit;
    }

    function generate_landscape_report($html='',$save=FALSE,$filename='',$location=''){
    	$this->mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-L']);
		$this->mpdf->mirrorMargins = 1;
		$this->mpdf->SetDisplayMode('fullpage');
		$this->footer = '{PAGENO} of {nbpg} pages||{PAGENO} of {nbpg} pages';
		$this->mpdf->setFooter($this->footer) ;
		$this->mpdf->setHeader(date("F j, Y, g:i a"));
		//print_r($html); die();
		$this->mpdf->WriteHTML($html);
		if($save){
			$directory = './uploads/downloads';
     		if(!is_dir('./'.$directory)){
     			mkdir('./'.$directory,0777,TRUE);
     		}
     		$file = str_replace(' ','',$directory."/".$filename.".pdf");
			$this->mpdf->Output($file,'F');
			return $file;
		}else{
			$this->mpdf->Output();
			exit;
		}
    }

	function generate_loans_summary($html='',$save=FALSE,$filename='',$location=''){
		$this->mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-L']);
		$this->mpdf->mirrorMargins = 1;
		$this->mpdf->SetDisplayMode('fullpage');
		$this->footer = '{PAGENO} of {nbpg} pages||{PAGENO} of {nbpg} pages';
		$this->mpdf->setFooter($this->footer) ;
		$this->mpdf->setHeader(date("F j, Y, g:i a"));
		//print_r($html); die();
		$this->mpdf->WriteHTML($html);
		if($save){
			$directory = './uploads/downloads';
	 		if(!is_dir('./'.$directory)){
	 			mkdir('./'.$directory,0777,TRUE);
	 		}
	 		$file = str_replace(' ','',$directory."/".$filename.".pdf");
			$this->mpdf->Output($file,'F');
			return $file;
		}else{
			$this->mpdf->Output();
			exit;
		}
	}

}

?>