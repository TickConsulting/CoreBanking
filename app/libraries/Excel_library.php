<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
 
require_once './assets/vendor/autoload.php';
/*
require_once "./assets/PHPExcel.php"; */
class Excel_library{ 

	Protected $filename;
    Protected $title;
    Protected $header = array();
    Protected $data = array();
    Protected $ci;

    Protected $currencyFormat = '_(#,##0.00_);_((#,##0.00);_("-"??_);_(@_)';
    Protected $phoneFormat = '0000';

    Protected $reports_folder = './uploads/reports/';

    public function __construct() { 
    	$this->filename = 'WebSacco Report';
        $this->title = 'First Excel File';
        $this->ci= & get_instance();
        if(isset($this->user->id)){
            $this->reports_folder = './uploads/reports/';
        }else{
            $this->reports_folder = './uploads/reports/';
        }

        if(!is_dir($this->reports_folder)){
            mkdir($this->reports_folder,0777,TRUE);
        }
        //$this->ci->load->library('files_uploader');
    }
   

    public function create_spreadsheet_file($filename='',$title='',$header=array(),$data=array(),$selection_criterias=array(),$column_size=0){
    	$filename = $filename?:$this->filename;
        $title = $title?:$this->title;
        $header = $header?:$this->header;
        $data = $data?:$this->data;

        if(strlen($title)>=30){
            $title = substr($title, 0, 30); 
        }

		// Create new Spreadsheet object
		$spreadsheet = new Spreadsheet();
		 
		// Set workbook properties
		$spreadsheet->getProperties()->setCreator('WebSacco')
		        ->setLastModifiedBy('WebSacco')
		        ->setTitle("Reports :: ".$title)
		        ->setSubject('Reports')
		        ->setDescription("System Generated Report Exported from WebSacco Online ".$title)
		        ->setKeywords('WebSacco Spreadsheet')
		        ->setCategory($title);


		$spreadsheet->setActiveSheetIndex(0);
        $header_size = count($header);
        $cellChar  = 'A';
        $cellDigit  = '1';

        $styleArray = [
		    'font' => [
		        'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_DOUBLEACCOUNTING
		    ],
		    'alignment' => [
		        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
		    ],
		    'borders' => [
		        'top' => [
		            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
		        ],
		    ],
		    'fill' => [
		        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
		        'rotation' => 90,
		        'startColor' => [
		            'argb' => 'FFA0A0A0',
		        ],
		        'endColor' => [
		            'argb' => 'FFFFFFFF',
		        ],
		    ],
		];

		$double_underline_style = array(
            'font' => array(
                'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_DOUBLEACCOUNTING
            )
        );

		$spreadsheet->getActiveSheet()->getStyle('A3')->applyFromArray($styleArray);

        $spreadsheet
            ->getActiveSheet()
            ->getStyle($cellChar.'2')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('8BB4E7');
            $spreadsheet
            ->getActiveSheet()
            ->getStyle($cellChar.'2')
            ->getFont()->setBold(true);

        $currency_column=array();
        $phone_column=array();
        $description_column = array();
        $date_column = array();


        
        $criterias = '';
        $order_by ='';
        $order_by_result = 'ASC';
        if(is_array($selection_criterias)){
            if($selection_criterias){
                foreach ($selection_criterias as $name => $criteria) {
                    if($name == 'order_by'){
                        $order_by = $criteria;
                    }else if($name=='order_by_result'){
                        $order_by_result = $criteria;
                    }else{
                        if($criteria){
                            if($criterias){
                                $criterias.= $name.' ('.$criteria.') ';
                            }else{
                                $criterias= $name.' ('.$criteria.') ';
                            }
                        }
                    }
                }
            }


            if(!$criterias){
                $criterias = 'None';
            }

            $filename_title=$filename.' Filter Criteria ('.$criterias.')';
        }else{
            $filename_title = $filename;
        }

        $spreadsheet->getActiveSheet()->SetCellValue($cellChar.'1',$filename_title); 

        $cellChar++;

        foreach($header as $cell){
            $spreadsheet->getActiveSheet()->SetCellValue($cellChar.'2', $cell);          
            $spreadsheet
            ->getActiveSheet()
            ->getStyle($cellChar.'2')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('8BB4E7');

            if(preg_match('/amount/', strtolower($cell))){
                $currency_column = array_merge($currency_column,array($cellChar));
                if($currency_column){
                    if(in_array($cellChar, $currency_column)){
                        $spreadsheet->getActiveSheet()->getStyle($cellChar.'2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    }
                }
            }
            if(preg_match('/date/', strtolower($cell))){
                $currency_column = array_merge($currency_column,array($cellChar));
                if($currency_column){
                    if(in_array($cellChar, $currency_column)){
                        $spreadsheet->getActiveSheet()->getStyle($cellChar.'2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    }
                }
            }
            if(preg_match('/phone/',strtolower($cell))){
                $phone_column = array_merge($phone_column,array($cellChar));
            }
            if(preg_match('/description/', strtolower($cell)) || preg_match('/details/', strtolower($cell))){
                $description_column = array_merge($description_column,array($cellChar));
            }
            if(preg_match('/date/', strtolower($cell))){
                $date_column = array_merge($date_column,array($cellChar));
            }

           
            $spreadsheet
            ->getActiveSheet()
            ->getStyle($cellChar.'2')
            ->getFont()->setBold(true)->setSize(10);
            $spreadsheet->getActiveSheet()->getStyle($cellChar.'2')->getAlignment()->setWrapText(true); 
            $cellChar++;
        }

        $cellCharConstant = (--$cellChar);

        $underlineCell = '';
        
        $cellDigit  = '3';
        foreach($data as $row){
            $cellChar  = 'A';
            foreach($row as $cell){
                if(preg_match('/connected/', strtolower($cell)) || preg_match('/date/', strtolower($cell))){
                    $spreadsheet->getActiveSheet()->SetCellValue($cellChar.$cellDigit, $cell);
                }else{
                    $spreadsheet->getActiveSheet()->SetCellValue($cellChar.$cellDigit, $this->clean_data($cell));
                }
                $spreadsheet->getActiveSheet()->getStyle($cellChar.$cellDigit)->getFont()->setSize(9);
                $spreadsheet->getActiveSheet()->getStyle($cellChar.$cellDigit)->getAlignment()->setWrapText(true); 
                if($cellChar=='A'){
                    $spreadsheet->getActiveSheet()->getStyle("A".$cellDigit)->getFont()->setSize(10);
                    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
                }else{
                    $spreadsheet->getActiveSheet()->getColumnDimension($cellChar)->setWidth(20);
                    if($currency_column){
                        if(in_array($cellChar, $currency_column)){
                            $spreadsheet->getActiveSheet()->getStyle($cellChar.$cellDigit)->getFont()->setBold(true);
                            $spreadsheet->getActiveSheet()->getStyle($cellChar.$cellDigit)->getNumberFormat()->setFormatCode($this->currencyFormat);
                            $spreadsheet->getActiveSheet()->getColumnDimension($cellChar)->setAutoSize(true);
                        }
                    }
                }

                if($phone_column){
                    if(in_array($cellChar, $phone_column)){
                        $spreadsheet->getActiveSheet()->getStyle($cellChar.$cellDigit)->getNumberFormat()->setFormatCode($this->phoneFormat);
                        $spreadsheet->getActiveSheet()->getStyle($cellChar.$cellDigit)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    }
                }
                if($date_column){
                    if(in_array($cellChar, $date_column)){
                        $spreadsheet->getActiveSheet()->getStyle($cellChar.$cellDigit)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);;
                    }
                }

                if(preg_match('/total/', strtolower($cell))){
                    $underlineCell = $cellDigit;
                }

                if($underlineCell){
                    $spreadsheet->getActiveSheet()->getStyle($cellChar.$underlineCell)->applyFromArray($double_underline_style);
                    $spreadsheet->getActiveSheet()->getStyle($cellChar.$underlineCell)->getFont()->setBold(true);
                }
                if($column_size){
                    $spreadsheet->getActiveSheet()->getColumnDimension($cellChar)->setWidth(40);
                }


                $cellChar++;
            }
            $cellDigit++;
        }

        foreach ($description_column as $cellChar) {
            $spreadsheet->getActiveSheet()->getColumnDimension($cellChar)->setWidth(30);
        }

        $spreadsheet->getActiveSheet()->getRowDimension(1)->setRowHeight(25);
        $spreadsheet->getActiveSheet()->getStyle("A1")->getFont()->setSize(10)->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('8BB4E7');

        $cellCharConstant = 'A';
        foreach ($header as $head) {
            ++$cellCharConstant;
        }

        $cellsHeader = "A1:".$cellCharConstant."1";
        $spreadsheet->getActiveSheet()->mergeCells($cellsHeader);  


        $style = array(
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE
            )
        );

        $spreadsheet->getActiveSheet()->getStyle($cellsHeader)->applyFromArray($style);

        // Rename sheet
        $spreadsheet->getActiveSheet()->setTitle($title);
	        
        // Redirect output to a client's web browser (Xlsx)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');		 
		//new code:
		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
		exit;
    }

    function download_single_file($filename=''){
        $full_filename = $this->reports_folder.$filename;
        if(!file_exists($full_filename)){
            die('File Not Found');
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        readfile($full_filename);
        exit;
    }

    function download_zip($files = array(),$folder_name='WTF'){
        if(is_array($files)){
            $file_path = $this->reports_folder;
            $new_dir = $folder_name.'.zip';
            if(is_dir($new_dir)){
                unlink($new_dir);
            }
            $all_files = array();
            foreach ($files as $file) {
               $all_files[] = $file_path.$file;
            }
            $destination = $this->ci->files_uploader->create_zip($all_files,$new_dir);
            if (file_exists($new_dir)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($new_dir).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($new_dir));
                readfile($new_dir);
                unlink($new_dir);
                exit;
            }
        }
    }

    function delete_single_file($filename=''){
        if($filename){
            $file_path = $this->reports_folder;
            if(file_exists($file_path.$filename)){
                try{
                    unlink($file_path.$filename);
                    return TRUE;
                }catch(Exception $e) {
                  return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    function clean_data($data=''){
       return preg_replace("/[^a-zA-Z0-9\s\+\-\(\)\`\'\*\&\@\.\%\/\:\,]/", "", $data);
    } 

    function _generate_member_list($group_member_options=array(),$member_ids = array()){
        if($member_ids){
            if(!is_array($member_ids)){
                $member_ids = explode_str_to_array($member_ids);
            }
            $member_list = '';
            foreach ($member_ids as $member_id) {
                if($member_list){
                    $member_list.=', '.$group_member_options[$member_id];
                }else{
                    $member_list=$group_member_options[$member_id];
                }
            }
            return $member_list;
        }
    }

    function _generate_deposit_type_list($deposit_transaction_names=array(),$types = array()){
        if($types){
            if(!is_array($types)){
                $types = explode_str_to_array($types);
            }
            $type_list = '';
            foreach ($types as $type) {
                if($type_list){
                    $type_list=$deposit_transaction_names[$type];
                }else{
                    $type_list=$deposit_transaction_names[$type];
                }
            }
            return $type_list;
        }
    }

    function _generate_contribution_list($contribution_options=array(),$contribution_ids = array()){
        if($contribution_ids){
            if(!is_array($contribution_ids)){
                $contribution_ids = explode_str_to_array($contribution_ids);
            }
            $contribution_list = '';
            foreach ($contribution_ids as $contribution_id) {
                if($contribution_list){
                    $contribution_list.=', '.$contribution_options[$contribution_id];
                }else{
                    $contribution_list=$contribution_options[$contribution_id];
                }
            }
            return $contribution_list;
        }
    }

    function _generate_fine_list($fine_category_options=array(),$contribution_options=array(),$fine_ids=array()){
        if($fine_ids){
            if(!is_array($fine_ids)){
                $fine_ids = explode_str_to_array($fine_ids);
            }
            $fine_category_list = '';
            foreach ($fine_ids as $fine_id) {
                if($fine_category_list){
                    echo $fine_id;
                    $fine_category_list.=', '.$fine_category_options[$fine_id];
                }else{
                    $fine_category_list=$fine_category_options[$fine_id];
                }
            }
            return $fine_category_list;
        }
    }

    function _generate_income_category_list($income_category_options=array(),$income_ids=array()){
        if($income_ids){
            if(!is_array($income_ids)){
                $income_ids = explode_str_to_array($income_ids);
            }
            $income_category_list = '';
            foreach ($income_ids as $income_id) {
                if($income_category_list){
                    $income_category_list.=', '.$income_category_options[$income_id];
                }else{
                    $income_category_list=$income_category_options[$income_id];
                }
            }
            return $income_category_list;
        }
    }

    function _generate_stock_list($stock_options=array(),$stock_ids=array()){
        if($stock_ids){
            if(!is_array($stock_ids)){
                $stock_ids = explode_str_to_array($stock_ids);
            }
            $stock_list = '';
            foreach ($stock_ids as $stock_id) {
                if($stock_list){
                    $stock_list.=', '.$stock_options[$stock_id];
                }else{
                    $stock_list=$stock_options[$stock_id];
                }
            }
            return $stock_list;
        }
    }

    function _generate_money_market_investment_list($money_market_investment_options=array(),$money_market_investment_ids=array()){
        if($money_market_investment_ids){
            if(!is_array($money_market_investment_ids)){
                $money_market_investment_ids = explode_str_to_array($money_market_investment_ids);
            }
            $money_market_investment_list = '';
            foreach ($money_market_investment_ids as $money_market_investment_id) {
                if($money_market_investment_list){
                    $money_market_investment_list.=', '.$money_market_investment_options[$money_market_investment_id];
                }else{
                    $money_market_investment_list=$money_market_investment_options[$money_market_investment_id];
                }
            }
            return $money_market_investment_list;
        }
    }

    function _generate_asset_list($asset_options=array(),$asset_ids=array()){
        if($asset_ids){
            if(!is_array($asset_ids)){
                $asset_ids = explode_str_to_array($asset_ids);
            }
            $asset_list = '';
            foreach ($asset_ids as $asset_id) {
                if($asset_list){
                    $asset_list.=', '.$asset_options[$asset_id];
                }else{
                    $asset_list=$asset_options[$asset_id];
                }
            }
            return $asset_list;
        }
    }

    function _generate_account_list($account_options=array(),$account_ids=array()){
        if($account_ids){
            if(!is_array($account_ids)){
                $account_ids = explode_str_to_array($account_ids);
            }
            $account_list = '';
            foreach ($account_ids as $account_id) {
                if($account_list){
                    $account_list.=', '.$account_options[$account_id];
                }else{
                    $account_list=$account_options[$account_id];
                }
            }
            return $account_list;
        }
    }

    function generate_contribution_summary($contribution_summary = ''){
    	if($contribution_summary){
    		$result = json_decode($contribution_summary);
    		if($result){
				$this->group = $result->group;
				$filename = $this->group->name.' Contributions Summary';
				$title = $this->group->name.' Contributions Summary';
				$group_currency = $result->group_currency;

				$active_group_member_options = array();
				foreach ($result->active_group_member_options as $key => $value) {
					$active_group_member_options[$key] = $value;
				}

				$member_total_contributions_paid_per_contribution_array = array();
				foreach ($result->member_total_contributions_paid_per_contribution_array as $key => $values) {
					foreach ($values as $key2 => $value) {
						$member_total_contributions_paid_per_contribution_array[$key][$key2] = $value;
					}
					
				}

				$member_total_contribution_balances_per_contribution_array = array();
				foreach ($result->member_total_contribution_balances_per_contribution_array as $key => $values) {
					foreach ($values as $key2 => $value) {
						$member_total_contribution_balances_per_contribution_array[$key][$key2] = $value;
					}
				}

				$disabled_arrears_contribution_ids_array=array();
				foreach ($result->disabled_arrears_contribution_ids_array as $key => $value) {
					$disabled_arrears_contribution_ids_array[$key] = $value;
				}

				$contribution_options = array();
				foreach ($result->contribution_options as $key => $value) {
					$contribution_options[$key] = $value;
				}

				//print_r($result);
				$headers = array(
					'Contribution Name',
					'Member Name',
					'Paid Amount ('.$group_currency.')',
					'Arrears Amount ('.$group_currency.')',
				);

				$contributions = 0;
				$grand_total_amount_paid = 0;
				$grand_total_arrears = 0;
				foreach($contribution_options as $contribution_id => $contribution_name):
	                $total_amount_paid = 0;
	                $total_arrears = 0;
					$this->data[] = array(
						++$contributions,
						$contribution_name,
					);

					$count = 1;
					foreach($active_group_member_options  as $member_id => $member_name):
						$amount_paid = $member_total_contributions_paid_per_contribution_array[$contribution_id][$member_id];
						$total_amount_paid += $amount_paid;
						$arrears = $member_total_contribution_balances_per_contribution_array[$contribution_id][$member_id];
	                    $total_arrears += $arrears;
	                    if(in_array($contribution_id,$disabled_arrears_contribution_ids_array)){
	                    	$arrears_amount = number_to_currency();
                        }else{
                            $arrears_amount = number_to_currency($arrears);
                        }

	                    $this->data[] = array(
	                    	'',
	                    	$count++,
	                    	$member_name,
	                    	number_to_currency($amount_paid),
	                    	$arrears_amount,
	                    );
					endforeach;
					$this->data[] = array(
							'',
							$contribution_name.' Totals',
							'',
							$total_amount_paid,
							$total_arrears,
						);
					$this->data[] = array();
					$this->data[] = array();
					$grand_total_amount_paid+=$total_amount_paid; 
					$grand_total_arrears+=$total_arrears;
				endforeach;


				$this->data[] = array(
							'',
							'Grand Totals',
							'',
							$grand_total_amount_paid,
							$grand_total_arrears,
						);

				$filter_parameters = array(
					'From' => $result->from?timestamp_to_report_time($result->from):'',
					'To' => $result->to?timestamp_to_report_time($result->to):'',
				);

				$response = $this->create_spreadsheet_file($filename,$title,$headers,$this->data,$filter_parameters);
				/*if($response){
					$this->download_single_file($filename.'.xlsx');
					$this->delete_single_file($filename.'.xlsx');
				}*/
			}else{
				echo 'invalid file';
			}
    	}else{
    		echo 'Empty File';
    	}

    } 

    function generate_fines_summary($fines_summary = ''){
    	if($fines_summary){
    		$result = json_decode($fines_summary);
			if($result){
				$this->group = $result->group;
				$filename = $this->group->name.' FInes Summary';
				$title = $this->group->name.' Fines Summary';
				$group_currency = $result->group_currency;

				$member_total_contribution_transfers_to_fines_array = array();
				foreach ($result->member_total_contribution_transfers_to_fines_array as $key => $value) {
					$member_total_contribution_transfers_to_fines_array[$key] = $value;
 				}

 				$suspended_members_ids_array = array();
				foreach ($result->suspended_members_ids_array as $key => $value) {
					$suspended_members_ids_array[$key] = $value;
 				}

 				$group_member_fine_totals = array();
				foreach ($result->group_member_fine_totals as $key => $value) {
					$group_member_fine_totals[$key] = $value;
 				}

 				$group_member_fine_balance_totals = array();
				foreach ($result->group_member_fine_balance_totals as $key => $value) {
					$group_member_fine_balance_totals[$key] = $value;
 				}

 				$group_member_options = array();
				foreach ($result->group_member_options as $key => $value) {
					$group_member_options[$key] = $value;
 				}

 				$headers = array(
 					'Member Name',
 					'Amount Paid ('.$group_currency.')',
 					'Balance Amount ('.$group_currency.')',
 				);

 				$suspended_members_amount_paid = 0;
            	$suspended_members_arrears = 0;  
            	$total_amount_paid = 0; 
            	$total_arrears = 0; 
            	$count = 1;

            	foreach($group_member_options as $member_id => $member_name):
            		$total_amount_paid += $group_member_fine_totals[$member_id]+$member_total_contribution_transfers_to_fines_array[$member_id];
            		$total_arrears += $group_member_fine_balance_totals[$member_id];
            		if(in_array($member_id,$suspended_members_ids_array)){
                		$suspended_members_amount_paid += $group_member_fine_totals[$member_id]+$member_total_contribution_transfers_to_fines_array[$member_id];
                		$suspended_members_arrears += $group_member_fine_balance_totals[$member_id]; 
            		}else{
            			$this->data[] = array(
            				$count++,
            				$member_name,
            				number_to_currency($group_member_fine_totals[$member_id]+$member_total_contribution_transfers_to_fines_array[$member_id]),
            				number_to_currency($group_member_fine_balance_totals[$member_id]),
            			);
            		}
            	endforeach;

            	if(!empty($suspended_members_ids_array)){
            		if($suspended_members_amount_paid||$suspended_members_arrears){
            			$second_row[] = array(
            				$count++,
            				'Suspended members',
            				number_to_currency($suspended_members_amount_paid),
            				number_to_currency($suspended_members_arrears),
            			);
            			$this->data = array_merge($this->data,$second_row);
                	}
            	}

            	$totals = array(
            		'',
            		'Totals',
            		number_to_currency($total_amount_paid),
            		number_to_currency($total_arrears),
            	);
            	$this->data = array_merge($this->data,array($totals));

				$filter_parameters = array(
					
				);

				$response = $this->create_spreadsheet_file($filename,$title,$headers,$this->data,$filter_parameters);

			}else{
				echo 'Invalid file sent';
			}
    	}else{
    		echo 'Empty File';
    	}
    }

    function generate_loans_summary($loans_summary = ''){
    	if($loans_summary){
			$result = json_decode($loans_summary);
			if($result){
				$this->group = $result->group;
				$filename = $this->application_settings->application_name.' Loans in Arrears';
				$title = $this->application_settings->application_name.'Loans in Arrears';
				$group_currency = $result->group_currency;

				//print_r($result);die;
				$amount_paid = array();
				foreach ($result->amount_paid as $key => $value) {
					$amount_paid[$key] = $value;
				}

				$external_lending_amount_paid = array();
				foreach ($result->external_lending_amount_paid as $key => $value) {
					$external_lending_amount_paid[$key] = $value;
				}

				$projected_profit = array();
				foreach ($result->projected_profit as $key => $value) {
					$projected_profit[$key] = $value;
				}

				$external_lending_projected_profit = array();
				foreach ($result->external_lending_projected_profit as $key => $value) {
					$external_lending_projected_profit[$key] = $value;
				}

				$amount_payable_to_date = array();
				foreach ($result->amount_payable_to_date as $key => $value) {
					$amount_payable_to_date[$key] = $value;
				}

				$external_lending_amount_payable_to_date = array();
				foreach ($result->external_lending_amount_payable_to_date as $key => $value) {
					$external_lending_amount_payable_to_date[$key] = $value;
				}

				$members = array();
				foreach ($result->members as $key => $value) {
					$members[$key] = $value;
				}

				$debtors= array();
				if(isset($result->debtors)){
					foreach ($result->debtors as $key => $value) {
						$debtors[$key] = $value;
					}
				}
				


				$headers = array(
					'Applicant Name',
					'Loan Duration',
                  	'Loan Start Date',
                  	'Loan End Date',
                    'Amount Loaned ('.$group_currency.')',
                    'Interest Amount ('.$group_currency.')',
                    'Amount Paid ('.$group_currency.')',
                    'Amount Arrears ('.$group_currency.')',
                    'Profit Amount ('.$group_currency.')',
                    'Outstanding Profit Amount ('.$group_currency.')',
                    'Projected Profit Amount ('.$group_currency.')',
				);

				$total_loan=0;
                $total_interest=0;
                $total_paid=0;
                $total_balance=0;
                $total_projected=0;
                $total_outstanding_profit=0;
                $total_profits=0;
                $i=1;
				foreach ($result->posts as $key => $post) {
					if(isset($post->id)):
                        $total_amount_payable_to_date=$amount_payable_to_date[$post->id]->todate_amount_payable?:0;
                        $principle_payable_todate = $amount_payable_to_date[$post->id]->todate_principle_payable?:0;
                        if((round($total_amount_payable_to_date-$amount_paid[$post->id])) <= 0){
                            $intere = $total_amount_payable_to_date - $principle_payable_todate;
                            $overpayments = $amount_paid[$post->id] - $total_amount_payable_to_date;
                            if($overpayments<0) {
                                $overpayments = '';
                            }
                            $due_inter = '';
                            $pen = ($post->total_amount_payable) - ($post->total_interest_payable+$post->total_principle_payable);
                            if($pen>0) {
                                $penalty = $pen;
                            }
                            else {
                                $penalty = 0;
                            }
                        }else {
                            $intere = '';
                            $overpayments = '';
                            $penalty = ($post->total_amount_payable) - ($post->total_interest_payable+$post->total_principle_payable);
                        }
                        $profit = $projected_profit[$post->id];
	                    $outstanding_profit = round(($post->total_interest_payable+$penalty)-$profit);
	                    $projected_profits = $post->total_interest_payable+$penalty;

						$this->data[] = array(
							$i,
							$members[$post->member_id],
							$post->repayment_period.' Months',
	                        timestamp_to_mobile_time($post->disbursement_date),
	                        timestamp_to_mobile_time($post->loan_end_date),
	                        number_to_currency($loan = $post->loan_amount),
	                        number_to_currency($interest = $post->total_interest_payable),
	                        number_to_currency($paid = $amount_paid[$post->id]),
	                        number_to_currency($balance = $post->total_amount_payable - $paid),
	                        number_to_currency($profit),
	                        number_to_currency($outstanding_profit),
	                        number_to_currency($projected_profits),
						);
						$total_loan+=$loan; 
		                $total_interest+=$interest;
		                $total_paid+=$paid;
		                $total_balance+=$balance; 
		                $total_profits+=$profit; 
		                $total_projected+=$projected_profits; 
		                $total_outstanding_profit+=$outstanding_profit;
		                ++$i;
                	endif;
				}


				$totals = array(
					array(
						'',
						'',
						'',
	                  	'',
	                  	'Totals',
	                    number_to_currency($total_loan),
	                    number_to_currency($total_interest),
						number_to_currency($total_paid),
						number_to_currency($total_balance),
						number_to_currency($total_profits),
						number_to_currency($total_outstanding_profit),
						number_to_currency($total_projected),
					),
				);

				$this->data = array_merge($this->data,$totals);

				if($result->external_lending_post):
					$this->data = array_merge($this->data,array(array(),array(),array(),array(),array(
						'',
						'Debtor Name',
						'Loan Duration',
	                  	'Loan Start Date',
	                  	'Loan End Date',
	                    'Amount Loaned ('.$group_currency.')',
	                    'Interest Amount ('.$group_currency.')',
	                    'Amount Paid ('.$group_currency.')',
	                    'Amount Arrears ('.$group_currency.')',
	                    'Profit Amount ('.$group_currency.')',
	                    'Outstanding Profit Amount ('.$group_currency.')',
	                    'Projected Profit Amount ('.$group_currency.')',
					)));

					$external_lending_total_loan=0;
	                $external_lending_total_interest=0;
	                $external_lending_total_paid=0;
	                $external_lending_total_balance=0;
	                $external_lending_total_projected=0;
	                $external_lending_total_outstanding_profit=0;
	                $external_lending_total_profits=0;


	                $i=1;
	                foreach ($result->external_lending_post as $key => $post) {
	                	if(isset($post->id)):
	                        $total_amount_payable_to_date=$external_lending_amount_payable_to_date[$post->id]->todate_amount_payable?:0;
	                        $principle_payable_todate = $external_lending_amount_payable_to_date[$post->id]->todate_principle_payable?:0;
	                        if((round($total_amount_payable_to_date-$external_lending_amount_paid[$post->id])) <= 0){
	                            $intere = $total_amount_payable_to_date - $principle_payable_todate;
	                            $overpayments = $external_lending_amount_paid[$post->id] - $total_amount_payable_to_date;
	                            if($overpayments<0) {
	                                $overpayments = '';
	                            }
	                            $due_inter = '';
	                            $pen = ($post->total_amount_payable) - ($post->total_interest_payable+$post->total_principle_payable);
	                            if($pen>0) {
	                                $penalty = $pen;
	                            }
	                            else {
	                                $penalty = 0;
	                            }
	                        }else {
	                            $intere = '';
	                            $overpayments = '';
	                            $penalty = ($post->total_amount_payable) - ($post->total_interest_payable+$post->total_principle_payable);
	                        }
	                        $profit = $external_lending_projected_profit[$post->id];
		                    $outstanding_profit = round(($post->total_interest_payable+$penalty)-$profit);
		                    $projected_profits = $post->total_interest_payable+$penalty;

							$this->data[] = array(
								$i,
								$debtors[$post->debtor_id],
								$post->repayment_period.' Months',
		                        timestamp_to_mobile_time($post->disbursement_date),
		                        timestamp_to_mobile_time($post->loan_end_date),
		                        number_to_currency($loan = $post->loan_amount),
		                        number_to_currency($interest = $post->total_interest_payable),
		                        number_to_currency($paid = $external_lending_amount_paid[$post->id]),
		                        number_to_currency($balance = $post->total_amount_payable - $paid),
		                        number_to_currency($profit),
		                        number_to_currency($outstanding_profit),
		                        number_to_currency($projected_profits),
							);
							$external_lending_total_loan+=$loan; 
			                $external_lending_total_interest+=$interest;
			                $external_lending_total_paid+=$paid;
			                $external_lending_total_balance+=$balance; 
			                $external_lending_total_profits+=$profit; 
			                $external_lending_total_projected+=$projected_profits; 
			                $external_lending_total_outstanding_profit+=$outstanding_profit;
			                ++$i;
	                	endif;
	                }


					$totals_2 = array(
						array(
							'',
							'',
							'',
		                  	'',
		                  	'Totals',
		                    number_to_currency($external_lending_total_loan),
		                    number_to_currency($external_lending_total_interest),
							number_to_currency($external_lending_total_paid),
							number_to_currency($external_lending_total_balance),
							number_to_currency($external_lending_total_profits),
							number_to_currency($external_lending_total_outstanding_profit),
							number_to_currency($external_lending_total_projected),
						),
					);

					$this->data = array_merge($this->data,$totals_2);

				endif;

		

				$filter_parameters = array(
					
				);

				$response = $this->create_spreadsheet_file($filename,$title,$headers,$this->data,$filter_parameters);
				/*if($response){
					$this->excel_generator->download_single_file($filename.'.xlsx');
					$this->excel_generator->delete_single_file($filename.'.xlsx');
				}*/

			}else{
				echo 'Invalid file sent';
			}
		}else{
			echo 'No file Sent';
		}
    }
	function generate_loans_in_arrears_summary($loans_summary = ''){
    	if($loans_summary){
			$result = json_decode($loans_summary);
			if($result){
				$this->group = $result->group;
				$filename = 'Loans in Arrears';
				$title = 'Loans in Arrears';
				$group_currency = $result->group_currency;

				//print_r($result);die;
				$amount_paid = array();
				foreach ($result->amount_paid as $key => $value) {
					$amount_paid[$key] = $value;
				}

				$external_lending_amount_paid = array();
				foreach ($result->external_lending_amount_paid as $key => $value) {
					$external_lending_amount_paid[$key] = $value;
				}

				$projected_profit = array();
				foreach ($result->projected_profit as $key => $value) {
					$projected_profit[$key] = $value;
				}

				$external_lending_projected_profit = array();
				foreach ($result->external_lending_projected_profit as $key => $value) {
					$external_lending_projected_profit[$key] = $value;
				}

				$amount_payable_to_date = array();
				foreach ($result->amount_payable_to_date as $key => $value) {
					$amount_payable_to_date[$key] = $value;
				}

				$external_lending_amount_payable_to_date = array();
				foreach ($result->external_lending_amount_payable_to_date as $key => $value) {
					$external_lending_amount_payable_to_date[$key] = $value;
				}

				$members = array();
				foreach ($result->members as $key => $value) {
					$members[$key] = $value;
				}

				$debtors= array();
				if(isset($result->debtors)){
					foreach ($result->debtors as $key => $value) {
						$debtors[$key] = $value;
					}
				}
				


				$headers = array(
					'Applicant Name',
					'Loan Duration',
                  	'Loan Start Date',
                  	'Loan End Date',
                    'Amount Loaned ('.$group_currency.')',
                    'Interest Amount ('.$group_currency.')',
                    'Amount Paid ('.$group_currency.')',
                    'Amount Arrears ('.$group_currency.')',
                    'Days In Arrears',
                    'Profit Amount ('.$group_currency.')',
                    'Outstanding Profit Amount ('.$group_currency.')',
                    'Projected Profit Amount ('.$group_currency.')',
				);

				$total_loan=0;
                $total_interest=0;
                $total_paid=0;
                $total_balance=0;
                $total_projected=0;
                $total_outstanding_profit=0;
                $total_profits=0;
                $i=1;
				foreach ($result->posts as $key => $post) {
					if(isset($post->id)):
                        $total_amount_payable_to_date=$amount_payable_to_date[$post->id]->todate_amount_payable?:0;
                        $principle_payable_todate = $amount_payable_to_date[$post->id]->todate_principle_payable?:0;
                        if((round($total_amount_payable_to_date-$amount_paid[$post->id])) <= 0){
                            $intere = $total_amount_payable_to_date - $principle_payable_todate;
                            $overpayments = $amount_paid[$post->id] - $total_amount_payable_to_date;
                            if($overpayments<0) {
                                $overpayments = '';
                            }
                            $due_inter = '';
                            $pen = ($post->total_amount_payable) - ($post->total_interest_payable+$post->total_principle_payable);
                            if($pen>0) {
                                $penalty = $pen;
                            }
                            else {
                                $penalty = 0;
                            }
                        }else {
                            $intere = '';
                            $overpayments = '';
                            $penalty = ($post->total_amount_payable) - ($post->total_interest_payable+$post->total_principle_payable);
                        }
                        $profit = $projected_profit[$post->id];
	                    $outstanding_profit = round(($post->total_interest_payable+$penalty)-$profit);
	                    $projected_profits = $post->total_interest_payable+$penalty;

						$this->data[] = array(
							$i,
							$members[$post->member_id],
							$post->repayment_period.' Months',
	                        timestamp_to_mobile_time($post->disbursement_date),
	                        timestamp_to_mobile_time($post->loan_end_date),
	                        number_to_currency($loan = $post->loan_amount),
	                        number_to_currency($interest = $post->total_interest_payable),
	                        number_to_currency($paid = $amount_paid[$post->id]),
	                        number_to_currency($balance = $post->total_amount_payable - $paid),
	                         $post->days_in_arrears ,
	                        number_to_currency($profit),
	                        number_to_currency($outstanding_profit),
	                        number_to_currency($projected_profits),
						);
						$total_loan+=$loan; 
		                $total_interest+=$interest;
		                $total_paid+=$paid;
		                $total_balance+=$balance; 
		                $total_profits+=$profit; 
		                $total_projected+=$projected_profits; 
		                $total_outstanding_profit+=$outstanding_profit;
		                ++$i;
                	endif;
				}
	 

				$totals = array(
					array(
						'',
						'',
						'',
	                  	'',
	                  	'Totals',
	                    number_to_currency($total_loan),
	                    number_to_currency($total_interest),
						number_to_currency($total_paid),
						number_to_currency($total_balance),
						number_to_currency($total_profits),
						number_to_currency($total_outstanding_profit),
						number_to_currency($total_projected),
					),
				);

				$this->data = array_merge($this->data,$totals);

				if($result->external_lending_post):
					$this->data = array_merge($this->data,array(array(),array(),array(),array(),array(
						'',
						'Debtor Name',
						'Loan Duration',
	                  	'Loan Start Date',
	                  	'Loan End Date',
	                    'Amount Loaned ('.$group_currency.')',
	                    'Interest Amount ('.$group_currency.')',
	                    'Amount Paid ('.$group_currency.')',
	                    'Amount Arrears ('.$group_currency.')',
	                    'Profit Amount ('.$group_currency.')',
	                    'Outstanding Profit Amount ('.$group_currency.')',
	                    'Projected Profit Amount ('.$group_currency.')',
					)));

					$external_lending_total_loan=0;
	                $external_lending_total_interest=0;
	                $external_lending_total_paid=0;
	                $external_lending_total_balance=0;
	                $external_lending_total_projected=0;
	                $external_lending_total_outstanding_profit=0;
	                $external_lending_total_profits=0;


	                $i=1;
	                foreach ($result->external_lending_post as $key => $post) {
	                	if(isset($post->id)):
	                        $total_amount_payable_to_date=$external_lending_amount_payable_to_date[$post->id]->todate_amount_payable?:0;
	                        $principle_payable_todate = $external_lending_amount_payable_to_date[$post->id]->todate_principle_payable?:0;
	                        if((round($total_amount_payable_to_date-$external_lending_amount_paid[$post->id])) <= 0){
	                            $intere = $total_amount_payable_to_date - $principle_payable_todate;
	                            $overpayments = $external_lending_amount_paid[$post->id] - $total_amount_payable_to_date;
	                            if($overpayments<0) {
	                                $overpayments = '';
	                            }
	                            $due_inter = '';
	                            $pen = ($post->total_amount_payable) - ($post->total_interest_payable+$post->total_principle_payable);
	                            if($pen>0) {
	                                $penalty = $pen;
	                            }
	                            else {
	                                $penalty = 0;
	                            }
	                        }else {
	                            $intere = '';
	                            $overpayments = '';
	                            $penalty = ($post->total_amount_payable) - ($post->total_interest_payable+$post->total_principle_payable);
	                        }
	                        $profit = $external_lending_projected_profit[$post->id];
		                    $outstanding_profit = round(($post->total_interest_payable+$penalty)-$profit);
		                    $projected_profits = $post->total_interest_payable+$penalty;

							$this->data[] = array(
								$i,
								$debtors[$post->debtor_id],
								$post->repayment_period.' Months',
		                        timestamp_to_mobile_time($post->disbursement_date),
		                        timestamp_to_mobile_time($post->loan_end_date),
		                        number_to_currency($loan = $post->loan_amount),
		                        number_to_currency($interest = $post->total_interest_payable),
		                        number_to_currency($paid = $external_lending_amount_paid[$post->id]),
		                        number_to_currency($balance = $post->total_amount_payable - $paid),
		                        number_to_currency($profit),
		                        number_to_currency($outstanding_profit),
		                        number_to_currency($projected_profits),
							);
							$external_lending_total_loan+=$loan; 
			                $external_lending_total_interest+=$interest;
			                $external_lending_total_paid+=$paid;
			                $external_lending_total_balance+=$balance; 
			                $external_lending_total_profits+=$profit; 
			                $external_lending_total_projected+=$projected_profits; 
			                $external_lending_total_outstanding_profit+=$outstanding_profit;
			                ++$i;
	                	endif;
	                }


					$totals_2 = array(
						array(
							'',
							'',
							'',
		                  	'',
		                  	'Totals',
		                    number_to_currency($external_lending_total_loan),
		                    number_to_currency($external_lending_total_interest),
							number_to_currency($external_lending_total_paid),
							number_to_currency($external_lending_total_balance),
							number_to_currency($external_lending_total_profits),
							number_to_currency($external_lending_total_outstanding_profit),
							number_to_currency($external_lending_total_projected),
						),
					);

					$this->data = array_merge($this->data,$totals_2);

				endif;

		

				$filter_parameters = array(
					
				);

				$response = $this->create_spreadsheet_file($filename,$title,$headers,$this->data,$filter_parameters);
				/*if($response){
					$this->excel_generator->download_single_file($filename.'.xlsx');
					$this->excel_generator->delete_single_file($filename.'.xlsx');
				}*/

			}else{
				echo 'Invalid file sent';
			}
		}else{
			echo 'No file Sent';
		}
    }
    function generate_expense_summary($loans_summary = ''){
    	if($loans_summary){
			$result = json_decode($loans_summary);
			if($result){
				$this->group = $result->group;
				$filename = $this->group->name.' Expenses Summary';
				$title = $this->group->name.' Expenses Summary';
				$group_currency = $result->group_currency;

				$expense_category_options = array();
				foreach ($result->expense_category_options as $key => $value) {
					$expense_category_options[$key] = $value;
				}

				$group_expense_category_totals = array();
				foreach ($result->group_expense_category_totals as $key => $value) {
					$group_expense_category_totals[$key] = $value;
				}

				$headers = array(
					'Expense Category',
					'Amount Paid ('.$group_currency.')',
				);

				$total_expenses = 0; 
				$total_arrears = 0; 
				$count = 1; 
				foreach($group_expense_category_totals as $expense_category_id => $group_expense_category_total): 
            		$total_expenses += $group_expense_category_total;
            		$this->data[] = array(
            			$count++,
            			$expense_category_options[$expense_category_id],
            			number_to_currency($group_expense_category_total),
            		);
            	endforeach;

            	$totals = array(array(
            			'',
            			'Totals',
            			number_to_currency($total_expenses),
            		));

            	$this->data = array_merge($this->data,$totals);

				//print_r($this->data);die;

				$filter_parameters = array(
					
				);

				$response = $this->create_spreadsheet_file($filename,$title,$headers,$this->data,$filter_parameters);

			}else{
				echo 'Invalid file sent';
			}
		}else{
			echo 'No file Sent';
		}
    }

    function generate_account_balances($account_balances = ''){
    	if($account_balances){
			$result = json_decode($account_balances);
			if($result){
				$this->group = $result->group;
				$filename = $this->group->name.' Account Balances';
				$title = $this->group->name.' Account Balances';
				$group_currency = $result->group_currency;

				//print_r($result);die;

				$account_options = array();
				foreach ($result->account_options as $key => $value) {
					$account_options[$key] = $value;
				}

				
				$account_balances = array();
				foreach ($result->account_balances as $key => $value) {
					$account_balances[$key] = (array)$value;
				}

				
				$headers = array(
					'Account Type',
					'Account Name',
					'Account Balance Amount ('.$group_currency.')',
				);

				$data = array();

				if(!empty($account_options)){ 
				    $grand_total_balance = 0;
				    foreach($account_options as $account_category => $accounts):
				        if($accounts){
			                $accounts_name = array(array(
			                	'',
			                	$account_category,
			                ));
			                $total_balance = 0; 
			                $count=1; 
			                $first_row = array();
			                foreach($accounts as $account_id => $account_name):
			                	$total_balance += $account_balances[$account_category][$account_id];
                            	$grand_total_balance += $account_balances[$account_category][$account_id];
			                	$first_row[] = array(
			                		$count++,
			                		'',
			                		$account_name,
			                		 number_to_currency($account_balances[$account_category][$account_id]),
			                	);
			                endforeach;

			                $account_data = array_merge($accounts_name,$first_row);

			                $totals = array(array(
			                	'',
			                	'Totals',
			                	'',
			                	number_to_currency($total_balance),
			                ),array(),array());

			                $account_data = array_merge($account_data,$totals);
			                $data[] = $account_data;
			                unset($account_data);
				        }
        			endforeach;
        		}

        		//print_r($data);die;

        		foreach($data as $key_array=>$value_array){
        			if(is_array($value_array)){
        				foreach ($value_array as $key => $value) {
        					$this->data[] = $value;
        				}
        			}
        		}

        		//print_r($this->data);
        		$grand_totals = array(array(),array(
        				'',
        				'Grand Totals',
        				'',
        				$grand_total_balance,
        			));
        		$this->data = array_merge($this->data,$grand_totals);

				$filter_parameters = array(
					
				);
				$response = $this->create_spreadsheet_file($filename,$title,$headers,$this->data,0);
				/*if($response){
					$this->excel_generator->download_single_file($filename.'.xlsx');
					$this->excel_generator->delete_single_file($filename.'.xlsx');
				}*/

			}else{
				echo 'Invalid file sent';
			}
		}else{
			echo 'No file Sent';
		}
    }

    function generate_transaction_statement($transaction_statement = ''){    	
    	if($transaction_statement){
			$result = json_decode($transaction_statement);
			if($result){
				$this->group = $result->group;
				$filename = $this->group->name.' Transaction Statement';
				$title = $this->group->name.' Transaction Statement';
				$group_currency = $result->group_currency;

				//print_r($result);die;

				$transaction_names = array();
				foreach ($result->transaction_names as $key => $value) {
					$transaction_names[$key] = $value;
				}

				$account_options = array();
				foreach ($result->account_options as $key => $value) {
					$account_options[$key] = $value;
				}

				$contribution_options = array();
				foreach ($result->contribution_options as $key => $value) {
					$contribution_options[$key] = $value;
				}

				$fine_category_options = array();
				foreach ($result->fine_category_options as $key => $value) {
					$fine_category_options[$key] = $value;
				}

				$income_category_options = array();
				foreach ($result->income_category_options as $key => $value) {
					$income_category_options[$key] = $value;
				}
				
				$expense_category_options = array();
				foreach ($result->expense_category_options as $key => $value) {
					$expense_category_options[$key] = $value;
				}

				$stock_sale_options = array();
				foreach ($result->stock_sale_options as $key => $value) {
					$stock_sale_options[$key] = $value;
				}

				$depositor_options = array();
				foreach ($result->depositor_options as $key => $value) {
					$depositor_options[$key] = $value;
				}

				$bank_loan_options = array();
				foreach ($result->bank_loan_options as $key => $value) {
					$bank_loan_options[$key] = $value;
				}

				$loan_options = array();
				foreach ($result->loan_options as $key => $value) {
					$loan_options[$key] = $value;
				}

				$asset_options = array();
				foreach ($result->asset_options as $key => $value) {
					$asset_options[$key] = $value;
				}

				$stock_purchase_options = array();
				foreach ($result->stock_purchase_options as $key => $value) {
					$stock_purchase_options[$key] = $value;
				}

				$money_market_investment_options = array();
				foreach ($result->money_market_investment_options as $key => $value) {
					$money_market_investment_options[$key] = $value;
				}


				$transactions = array();
				foreach ($result->transactions as $key_array => $value_array) {
					/*if(is_array($key_array)){
						foreach ($value_array as $key => $value) {
							$transactions[$key_array][$key] = $value;
						}
					}*/
					$transactions[$key_array] = (array)$value_array;
				}
				$transactions = (object)$transactions;

				//print_r($transactions);die;

				$group_member_options = array();
				foreach ($result->group_member_options as $key => $value) {
					$group_member_options[$key] = $value;
				}

				$group_debtor_options = array();
				if(isset($result->group_debtor_options)){
					foreach ($result->group_debtor_options as $key => $value) {
						$group_debtor_options[$key] = $value;
					}
				}
				

				$headers = array(
					'Date',
					'Transaction Type',
					'Description',
					'Amount Withdrawn ('.$group_currency.')',
					'Amount Deposited ('.$group_currency.')',
					'Balance Amount ('.$group_currency.')',
				);

				$this->data = array(array(
					1,
					'Balance B/F',
					'',
					'',
					'',
					'',
					number_to_currency($result->starting_balance),
				));
				$balance = $result->starting_balance;
				foreach ($result->posts as $key => $post) {
					if(in_array($post->transaction_type,$transactions->deposit_transaction_types)){
                        $balance+=$post->amount;
                        $description = '';
                        if(in_array($post->transaction_type,$transactions->contribution_payment_transaction_types)){
	                        $description.=$transaction_names[$post->transaction_type].' from '.$group_member_options[$post->member_id].' for '.$contribution_options[$post->contribution_id].' to '.$account_options[$post->account_id]; 
                    		if($post->description){
                    			$description.=' : '.$post->description;
                    		}
	                    }else if(in_array($post->transaction_type,$transactions->fine_payment_transaction_types)){
	                        $for = isset($contribution_options[$post->contribution_id])?$contribution_options[$post->contribution_id]:
	                        $fine_category_options[$post->fine_category_id];
	                        $description.=$transaction_names[$post->transaction_type].' from '.$group_member_options[$post->member_id].' for '.$for.' to '.$account_options[$post->account_id]; 
                    		if($post->description){
                    			$description.= ' : '.$post->description;
                    		}
	                    }else if(in_array($post->transaction_type,$transactions->miscellaneous_payment_transaction_types)){
	                        $description.=$transaction_names[$post->transaction_type].' from '.$group_member_options[$post->member_id].' to '.$account_options[$post->account_id].' for '; 
                    		if($post->description){
                    			$description.= ' '.$post->description;
                    		}
	                    }else if(in_array($post->transaction_type,$transactions->income_deposit_transaction_types)){
	                        $description.=$transaction_names[$post->transaction_type].' from '.$depositor_options[$post->depositor_id].' to '.$account_options[$post->account_id].' for '.$income_category_options[$post->income_category_id]; 
                    		if($post->description){
                    			$description.=' : '.$post->description;
                    		}
                    	}else if(in_array($post->transaction_type,$transactions->stock_sale_deposit_transaction_types)){
                            $description.=$transaction_names[$post->transaction_type].' of '.$stock_sale_options[$post->stock_sale_id].', deposited to '.$account_options[$post->account_id]; 
                            if($post->description){
                                $description.=' : '.$post->description;
                            }
                        }else if(in_array($post->transaction_type,$transactions->bank_loan_disbursement_deposit_transaction_types)){
	                        $description.=$transaction_names[$post->transaction_type].' for '.$bank_loan_options[$post->bank_loan_id].', deposited to '.$account_options[$post->account_id]; 
	                        if($post->description){
	                            $description.=' : '.$post->description;
	                        }
	                    }else if(in_array($post->transaction_type,$transactions->loan_repayment_transaction_types)){
                            $description.= $transaction_names[$post->transaction_type].' by '.$group_member_options[$post->member_id].' for the loan of '.$loan_options[$post->loan_id].', deposited to '.$account_options[$post->account_id]; 
                            if($post->description){
                                $description.= ' : '.$post->description;
                            }
                        }else if(in_array($post->transaction_type,$transactions->money_market_investment_cash_in_deposit_transaction_types)){
                            $description.= $transaction_names[$post->transaction_type].' for '.$money_market_investment_options[$post->money_market_investment_id].', deposited to '.$account_options[$post->account_id]; 
                            if($post->description){
                                $description.= ' : '.$post->description;
                            }
                        }else if(in_array($post->transaction_type,$transactions->asset_sale_deposit_transaction_types)){
                            $description.= $transaction_names[$post->transaction_type].' of '.$asset_options[$post->asset_id].', deposited to '.$account_options[$post->account_id]; 
                            if($post->description){
                                $description.= ' : '.$post->description;
                            }
                        }else if(in_array($post->transaction_type,$transactions->incoming_account_transfer_withdrawal_transaction_types)){
                            $description.= $transaction_names[$post->transaction_type].' from '.$account_options[$post->from_account_id].' to '.$account_options[$post->to_account_id]; 
                            if($post->description){
                                $description.= ' : '.$post->description;
                            }
                        }else if(in_array($post->transaction_type,$transactions->statement_loan_processing_income_deposit_transaction_types)){
                            $description.= 'Charged on Loan disbursed to '.$group_member_options[$post->member_id];
                        }else if(in_array($post->transaction_type,$transactions->statement_external_lending_processing_income_transaction_types)){
                            $description.=  'Charged on Loan disbursed to '.$group_debtor_options[$post->debtor_id];
                        }else if(in_array($post->transaction_type,$transactions->statement_external_lending_loan_repayment_transaction_types)){
                            $description.=  $transaction_names[$post->transaction_type].' by '.$group_debtor_options[$post->debtor_id];
                            if(isset($external_lending_loan_options[$post->debtor_loan_id])){
                                $description.=  ' for the loan of '.$external_lending_loan_options[$post->debtor_loan_id];
                            }
                            $description.=  ', deposited to '.$account_options[$post->account_id]; 
                            if($post->description){
                                $description.=  ' : '.$post->description;
                            }
                        }

                        if($post->transaction_alert_id){
                            $description.= '- Reconciled ';
                        }

						$this->data[] = array(
							$key+2,
							timestamp_to_mobile_time($post->transaction_date),
							isset($transaction_names[$post->transaction_type])?$transaction_names[$post->transaction_type]:'',
							$description,
							number_to_currency(),
							number_to_currency($post->amount),
							number_to_currency($balance),
						);
					}else if(in_array($post->transaction_type,$transactions->withdrawal_transaction_types)){
                        $balance-=$post->amount;
                        $description = '';
                        if(in_array($post->transaction_type,$transactions->statement_expense_withdrawal_transaction_types)){
                            $description.= $transaction_names[$post->transaction_type].' for '.$expense_category_options[$post->expense_category_id].',withdrawn from '.$account_options[$post->account_id]; 
                            if($post->description){
                                $description.= ' : '.$post->description;
                            }
                        }else if(in_array($post->transaction_type,$transactions->statement_stock_purchase_withdrawal_transaction_types)){
                            $description.= $transaction_names[$post->transaction_type].' for '.$stock_purchase_options[$post->stock_id].', withdrawn from '.$account_options[$post->account_id]; 
                            if($post->description){
                                $description.= ' : '.$post->description;
                            }
                        }else if(in_array($post->transaction_type,$transactions->statement_loan_disbursement_withdrawal_transaction_types)){
                            $description.= $transaction_names[$post->transaction_type].' to '.$group_member_options[$post->member_id].', withdrawn from '.$account_options[$post->account_id]; 
                            if($post->description){
                                $description.= ' : '.$post->description;
                            }
                        }else if(in_array($post->transaction_type,$transactions->statement_money_market_investment_withdrawal_transaction_types)){
                            $description.= $transaction_names[$post->transaction_type].' for '.$money_market_investment_options[$post->money_market_investment_id].', withdrawn from '.$account_options[$post->account_id]; 
                            if($post->description){
                                $description.= ' : '.$post->description;
                            }
                        }else if(in_array($post->transaction_type,$transactions->statement_asset_purchase_withdrawal_transaction_types)){
                            $description.= $transaction_names[$post->transaction_type].' for '.$asset_options[$post->asset_id].', withdrawn from '.$account_options[$post->account_id]; 
                            if($post->description){
                                $description.= ' : '.$post->description;
                            }
                        }else if(in_array($post->transaction_type,$transactions->statement_contribution_refund_withdrawal_transaction_types)){
                            $description.= $transaction_names[$post->transaction_type].' to '.$group_member_options[$post->member_id].' from '.$contribution_options[$post->contribution_id].', withdrawn from '.$account_options[$post->account_id]; 
                            if($post->description){
                                $description.= ' : '.$post->description;
                            }
                        }else if(in_array($post->transaction_type,$transactions->statement_contribution_refund_withdrawal_transaction_types)){
                            $description.= $transaction_names[$post->transaction_type].' to '.$group_member_options[$post->member_id].' from '.$contribution_options[$post->contribution_id].', withdrawn from '.$account_options[$post->account_id]; 
                            if($post->description){
                                $description.= ' : '.$post->description;
                            }
                        }else if(in_array($post->transaction_type,$transactions->statement_bank_loan_repayment_withdrawal_transaction_types)){
                            $description.= $transaction_names[$post->transaction_type].' to '.$bank_loan_options[$post->bank_loan_id].', withdrawn from '.$account_options[$post->account_id]; 
                            if($post->description){
                                $description.= ' : '.$post->description;
                            }
                        }else if(in_array($post->transaction_type,$transactions->statement_outgoing_account_transfer_withdrawal_transaction_types)){
                            $description.= $transaction_names[$post->transaction_type].' from '.$account_options[$post->from_account_id].' to '.$account_options[$post->to_account_id]; 
                            if($post->description){
                                $description.= ' : '.$post->description;
                            }
                        }else if(in_array($post->transaction_type,$transactions->statement_external_lending_withdrawal_transaction_types)){
                            if($post->debtor_id){
                               $description.=  $transaction_names[$post->transaction_type].' to '.$group_debtor_options[$post->debtor_id].', withdrawn from '.$account_options[$post->account_id]; 
                                if($post->description){
                                    $description.=  ' : '.$post->description;
                                } 
                            }
                        }


                        if($post->transaction_alert_id){
                            $description.= ' - Reconciled  ';
                        }


                        $this->data [] = array(
                        	$key+2,
                        	timestamp_to_mobile_time($post->transaction_date),
                        	isset($transaction_names[$post->transaction_type])?$transaction_names[$post->transaction_type]:'',
                        	$description,
                        	number_to_currency($post->amount),
                        	number_to_currency(),
                        	number_to_currency($balance),
                        );
                    }
				}

				$totals = array(array(),array(
					'',
					'',
					'',
					'Totals',
					'',
					'',
					number_to_currency($balance),
				));

				$this->data = array_merge($this->data,$totals);

				//print_r($this->data);die;

				$filter_parameters = array(
					'From' => $result->from?timestamp_to_report_time($result->from):'',
					'To' => $result->to?timestamp_to_report_time($result->to):'',
				);

				$response = $this->create_spreadsheet_file($filename,$title,$headers,$this->data,$filter_parameters);
				/*if($response){
					$this->excel_generator->download_single_file($filename.'.xlsx');
					$this->excel_generator->delete_single_file($filename.'.xlsx');
				}*/

			}else{
				echo 'Invalid file sent';
			}
		}else{
			echo 'No file Sent';
		}
    }

    function generate_bank_loans_summary($bank_loan_summary = ''){
    	if($bank_loan_summary){    		
			$result = json_decode($bank_loan_summary);
			if($result){
				$this->group = $result->group;
				$filename = $this->group->name.' Bank Loans Summary';
				$title = $this->group->name.' Bank Loans Summary';
				$group_currency = $result->group_currency;

				$headers = array(
					'Description',
					'Loan Start Date',
					'Loan End Date',
					'Amount Received ('.$group_currency.')',
					'Bank Interest Rate ',
					'Amount Payable ('.$group_currency.')',
					'Amount Paid ('.$group_currency.')',
					'Balance Amount ('.$group_currency.')',
				);

				$total_loan=0;
                $total_interest=0;
                $total_paid=0;
                $total_balance=0;
                $total_payable=0;
				foreach ($result->posts as $key => $post) {
					$this->data[] = array(
						$key+1,
						$post->description,
						timestamp_to_date($post->loan_start_date),
						timestamp_to_date($post->loan_end_date),
						number_to_currency($loan = $post->amount_loaned),
						number_to_currency($interest = $post->total_loan_amount_payable-$loan),
						number_to_currency($payable =$post->total_loan_amount_payable),
						number_to_currency($paid =$post->amount_paid),
						number_to_currency($balance = $post->loan_balance),
					);
					$total_loan+=$loan; 
                    $total_interest+=$interest;
                    $total_paid+=$paid;
                    $total_balance+=$balance;
                    $total_payable+=$payable;
				}

				$totals = array(array(
					'',
					'Totals',
					'',
					'',
					number_to_currency($total_loan),
					number_to_currency($total_interest),
					number_to_currency($total_payable),
					number_to_currency($total_paid),
					number_to_currency($total_balance),
				));

				$this->data = array_merge($this->data,$totals);

				
				//print_r($this->data);die;

				$filter_parameters = array(
					
				);

				$response = $this->create_spreadsheet_file($filename,$title,$headers,$this->data,$filter_parameters);
				if($response){
					$this->excel_generator->download_single_file($filename.'.xlsx');
					$this->excel_generator->delete_single_file($filename.'.xlsx');
				}
			}else{
				echo 'Invalid file sent';
			}
    	}else{
    		echo 'No file Sent';
    	}
    }

	function generate_cash_flow_statement($cash_flow_statement = ''){
		if($cash_flow_statement){
			$result = json_decode($cash_flow_statement);
			if($result){
				$this->group = $result->group;
				$filename = $this->group->name.' List of Loans';
				$title = $this->group->name.' List of Loans';
				$group_currency = $result->group_currency;

				//print_r($result);die;
				$contribution_options = array();
				foreach ($result->contribution_options as $key => $value) {
					$contribution_options[$key] = $value;
				}

				$member_total_contributions_paid_per_contribution_array = array();
				foreach ($result->member_total_contributions_paid_per_contribution_array as $key1 => $values) {
					foreach ($values as $key2 => $value) {
						$member_total_contributions_paid_per_contribution_array[$key1][$key2] = $value;
					}
				}

				$member_total_contribution_transfers_to_fines_array = array();
				foreach ($result->member_total_contribution_transfers_to_fines_array as $key => $value) {
					$member_total_contribution_transfers_to_fines_array[$key] = $value;
				}

				$group_member_fine_totals = array();
				foreach ($result->group_member_fine_totals as $key => $value) {
					$group_member_fine_totals[$key] = $value;
				}

				$group_member_miscellaneous_totals = array();
				foreach ($result->group_member_miscellaneous_totals as $key => $value) {
					$group_member_miscellaneous_totals[$key] = $value;
				}

				$group_income_totals = array();
				foreach ($result->group_income_totals as $key1 => $values) {
					foreach ($values as $key2 => $value) {
						$group_income_totals[$key1][$key2] = $value;
					}
				}

				$income_categories = array();
				foreach ($result->income_categories as $key => $value) {
					$income_categories[$key] = $value;
				}

				$expense_category_options = array();
				foreach ($result->expense_category_options as $key => $value) {
					$expense_category_options[$key] = $value;
				}

				$group_expense_category_totals = array();
				foreach ($result->group_expense_category_totals as $key => $value) {
					$group_expense_category_totals[$key] = $value;
				}

				
				$loans = array();
				foreach ($result->loans as $key => $value) {
					$loans[$key] = $value;
				}

				$debtor_loans = array();
				foreach ($result->debtor_loans as $key => $value) {
					$debtor_loans[$key] = $value;
				}

				$members = array();
				foreach ($result->members as $key => $value) {
					$members[$key] = $value;
				}

				$debtors = array();
				foreach ($result->debtors as $key => $value) {
					$debtors[$key] = $value;
				}
				$loan_repayments = array();
				foreach ($result->loan_repayments as $key => $value) {
					$loan_repayments[$key] = $value;
				}

				$debtor_loan_repayments = array();
				foreach ($result->debtor_loan_repayments as $key => $value) {
					$debtor_loan_repayments[$key] = $value;
				}


				$headers = array(
					'CashIns',
					'',
					'Amounts ('.$group_currency.')',
					'Grand Total Amount('.$group_currency.')',
				);

				$total_amount_cash_flow_balance = 0;

				$total_contributions = 0;
				$this->data[] = array(
					'',
					'Contributions',
				);

				//contributions
				foreach ($contribution_options as $contribution_id => $contribution_name) {
					$this->data[] = array(
						'',
						$contribution_name,
						'',
						'',
						'',
					);
					$this->data[] = array(
						'#',
						'Member Name',
						'Member Code',
						'Contribution amount ('.$group_currency.')',
					);
					$total_amount_paid = 0;
                    $i = 0;
					foreach ($members as $member_id => $member_name) {
						if($member_total_contributions_paid_per_contribution_array[$member_id][$contribution_id]):

                            $amount_paid = $member_total_contributions_paid_per_contribution_array[$member_id][$contribution_id];
                            $total_amount_paid += $amount_paid;
                            $this->data[] = array(
                            	++$i,
                            	$member_name,
                            	'--',
                            	number_to_currency($amount_paid),
                            );
                        endif;
					}
					$this->data[] = array(
                    	'',
                    	'Totals',
                    	'',
                    	number_to_currency($total_amount_paid),
                    );

                    $this->data[] = array(
                    	'',
                    );
                    $total_contributions+=$total_amount_paid;
				}

				$total_amount_cash_flow_balance+=$total_contributions;

				$this->data[] = array(
					'',
					'Grand Total Contributions',
					'',
					number_to_currency($total_contributions),
					number_to_currency($total_amount_cash_flow_balance),
				);

				//member Fines

				$this->data[] = array(
                    	'',
                    );

				$this->data[] = array(
                    	'',
                    	'Member Fines'
                );
                $this->data[] = array(
                    	'#',
                    	'Member Name',
                    	'Member Code',
                    	'Fine Amount ('.$group_currency.')',
                );
				$total_member_fines = 0;
				$i = 0;
                foreach ($members as $member_id => $member_name) {
                	if($member_total_contribution_transfers_to_fines_array[$member_id] || $group_member_fine_totals[$member_id]):
                		$member_fine_amount = $member_total_contribution_transfers_to_fines_array[$member_id]+$group_member_fine_totals[$member_id];
                		$total_member_fines += $member_fine_amount;
                		$this->data[] = array(
                			++$i,
                			$member_name,
                			'--',
                			number_to_currency($member_fine_amount),
                		);
                	endif;
                }

                $total_amount_cash_flow_balance+=$total_member_fines;

                $this->data[] = array(
					'',
					'Grand Total Fines',
					'',
					number_to_currency($total_member_fines),
					number_to_currency($total_amount_cash_flow_balance),
				);

				//Member loan repayments
				$this->data[] = array(
                    	'',
                    );

				$this->data[] = array(
                    	'',
                    	'Member Loan Repayments'
                );
				$total_loan_repayment = 0;
				$i = 0;
				foreach ($members as $member_id => $member_name) {
					if($loan_repayments[$member_id]){
						$member_loan_repayment = $loan_repayments[$member_id];
						$total_loan_repayment+=$member_loan_repayment;
						$this->data[] = array(
							++$i,
							$member_name,
							'--',
							number_to_currency($member_loan_repayment),
						);
					}
				}
				$total_amount_cash_flow_balance+=$total_loan_repayment;
				$this->data[] = array(
					'',
					'Grand Total Member Loan Repayments',
					'',
					number_to_currency($total_loan_repayment),
					number_to_currency($total_amount_cash_flow_balance),
				);

				//Debtor loan repayments
				$this->data[] = array(
                    	'',
                    );

				$this->data[] = array(
                    	'',
                    	'Debtor Loan Repayments'
                );
				$total_debtor_loan_repayment = 0;
				$i = 0;
				foreach ($debtors as $debtor_id => $debtor_name) {
					if($debtor_loan_repayments[$debtor_id]){
						$debtor_loan_repayment = $debtor_loan_repayments[$debtor_id];
						$total_debtor_loan_repayment+=$debtor_loan_repayment;
						$this->data[] = array(
							++$i,
							$debtor_name,
							'--',
							number_to_currency($debtor_loan_repayment),
						);
					}
				}
				$total_amount_cash_flow_balance+=$total_debtor_loan_repayment;
				$this->data[] = array(
					'',
					'Grand Total Debtor Loan Repayments',
					'',
					number_to_currency($total_debtor_loan_repayment),
					number_to_currency($total_amount_cash_flow_balance),
				);


				//Miscellaneous repayments
				$this->data[] = array(
                    	'',
                    );

				$this->data[] = array(
                    	'',
                    	'Miscellaneous Payments'
                );
                $total_miscellaneous_payments = 0;
				$i = 0;
				foreach ($members as $member_id => $member_name) {
					if($group_member_miscellaneous_totals[$member_id]){
						$miscellaneous_paid = $group_member_miscellaneous_totals[$member_id];
						$total_miscellaneous_payments+=$miscellaneous_paid;
						$this->data[] = array(
							++$i,
							$member_name,
							'--',
							number_to_currency($miscellaneous_paid),
						);
					}
				}
				$total_amount_cash_flow_balance+=$total_miscellaneous_payments;
				$this->data[] = array(
					'',
					'Grand Total Miscellaneous Payments',
					'',
					number_to_currency($total_miscellaneous_payments),
					number_to_currency($total_amount_cash_flow_balance),
				);

				//incomes
				$this->data[] = array(
                    	'',
                    );

				$this->data[] = array(
                    	'',
                    	'Other Incomes'
                );
                $other_incomes = 0;
				$i = 0;
				foreach($group_income_totals as $extenal_income):
	                if($extenal_income['amount']):
	                	$extenal_income_amount = $extenal_income['amount'];
	                	$other_incomes+= $extenal_income_amount;
	                    $this->data[] = array(
	                    	++$i,
	                    	$income_categories[$extenal_income['income_category_id']],
	                    	'--',
	                    	number_to_currency($extenal_income_amount),
	                    );
	                endif;
	            endforeach; 


	            $bank_loan_amount = 0;
	            if($result->bank_loan_amount){
	            	$bank_loan_amount = $result->bank_loan_amount;
	            	$other_incomes+=$bank_loan_amount;
	            	$this->data[] = array(
	            		++$i,
	            		'Bank Loan Amount Received',
	            		'-',
	            		number_to_currency($bank_loan_amount)
	            	);
	            }

	            $total_asset_sale_amount = 0;
	            if($result->total_asset_sale_amount){
	            	$total_asset_sale_amount = $result->total_asset_sale_amount;
	            	$other_incomes+=$total_asset_sale_amount;
	            	$this->data[] = array(
	            		++$i,
	            		'Asset Sales Amount Received',
	            		'-',
	            		number_to_currency($total_asset_sale_amount)
	            	);
	            }

	            $total_stock_sale_amount = 0;
	            if($result->total_stock_sale_amount){
	            	$total_stock_sale_amount = $result->total_stock_sale_amount;
	            	$other_incomes+=$total_stock_sale_amount;
	            	$this->data[] = array(
	            		++$i,
	            		'Stock Sales Amount Received',
	            		'-',
	            		number_to_currency($total_stock_sale_amount)
	            	);
	            }

	            $total_money_market_cash_in_amount = 0;
	            if($result->total_money_market_cash_in_amount){
	            	$total_money_market_cash_in_amount = $result->total_money_market_cash_in_amount;
	            	$other_incomes+=$total_money_market_cash_in_amount;
	            	$this->data[] = array(
	            		++$i,
	            		'Money Market Cashins Amount Received',
	            		'--',
	            		number_to_currency($total_money_market_cash_in_amount)
	            	);
	            }



				$total_amount_cash_flow_balance+=$other_incomes;
				$this->data[] = array(
					'',
					'Grand Total From Income Payments',
					'',
					number_to_currency($other_incomes),
					number_to_currency($total_amount_cash_flow_balance),
				);
				

				$this->data[] = array(
					'',
				);
				$this->data[] = array(
					'',
					'Cash Out Amounts',
				);

				//expenses
				$this->data[] = array(
                    	'#',
                    	'Expense Name',
                    	'',
                    	'Expense Amount ('.$group_currency.')',
                );
				$total_expense_amount = 0;
				$i=0;
				foreach ($expense_category_options as $expense_category_id => $expense_category_name) {
					if(isset($group_expense_category_totals[$expense_category_id])  && $group_expense_category_totals[$expense_category_id]){
						$expensed_amount = $group_expense_category_totals[$expense_category_id];
						$total_expense_amount+=$expensed_amount;
						$this->data[] = array(
							++$i,
							$expense_category_name,
							'--',
							number_to_currency($expensed_amount),
						);
					}
				}

				$total_amount_cash_flow_balance-=$total_expense_amount;
				$this->data[] = array(
					'',
					'Grand Total Expenses',
					'',
					number_to_currency($total_expense_amount),
					number_to_currency($total_amount_cash_flow_balance),
				);


				

				//member loans
				$this->data[] = array('');
				$this->data[] = array(
					'',
					'Member Loans',
				);
				$this->data[] = array(
					'#',
					'Member Name',
					'',
					'Member Loan Amount ('.$group_currency.')'
				);
				$total_member_loans = 0;
				$i=0;
				foreach ($loans as $member_loan_id => $member_loan_amount) {
					if($member_loan_amount){
						$total_member_loans+=$member_loan_amount;
						$this->data[] = array(
							++$i,
							$members[$member_loan_id],
							'--',
							number_to_currency($member_loan_amount),
						);
					}
				}
				$total_amount_cash_flow_balance-=$total_member_loans;
				$this->data[] = array(
					'',
					'Grand Total Member Loans',
					'',
					number_to_currency($total_member_loans),
					number_to_currency($total_amount_cash_flow_balance),
				);
				
				//Debtor loans
				$this->data[] = array('');
				$this->data[] = array(
					'',
					'Debtor Loans',
				);
				$this->data[] = array(
					'#',
					'Debtor Name',
					'',
					'Debtor Loan Amount ('.$group_currency.')'
				);
				$total_debtor_loans = 0;
				$i=0;
				foreach ($debtor_loans as $debtor_loan_id => $debtor_loan_amount) {
					if($debtor_loan_amount){
						$total_member_loans+=$debtor_loan_amount;
						$this->data[] = array(
							++$i,
							$debtors[$debtor_loan_id],
							'--',
							number_to_currency($debtor_loan_amount),
						);
					}
				}
				$total_amount_cash_flow_balance-=$total_debtor_loans;
				$this->data[] = array(
					'',
					'Grand Total Debtor Loans',
					'',
					number_to_currency($total_debtor_loans),
					number_to_currency($total_amount_cash_flow_balance),
				);

				//Bank Loan Repayments
				$this->data[] = array('');
				$i = 0;
				$this->data[] = array(
					'',
					'Other Cashouts',
					'',
					'Amount ('.$group_currency.')'
				);
				$total_cash_outs = 0;
				if($result->bank_loan_repayment_amount){
					$total_cash_outs+=$result->bank_loan_repayment_amount;
					$this->data[] = array(
						++$i,
						'Bank Loan Repayments',
						'--',
						number_to_currency($result->bank_loan_repayment_amount),
					);
				}

				if($result->total_asset_purchase_amount){
					$total_cash_outs+=$result->total_asset_purchase_amount;
					$this->data[] = array(
						++$i,
						'Asset Purchase Payments',
						'--',
						number_to_currency($result->total_asset_purchase_amount),
					);
				}

				if($result->total_stock_purchase_amount){
					$total_cash_outs+=$result->total_stock_purchase_amount;
					$this->data[] = array(
						++$i,
						'Stock Purchase',
						'--',
						number_to_currency($result->total_stock_purchase_amount),
					);
				}

				if($result->money_market_investment_amount){
					$total_cash_outs+=$result->money_market_investment_amount;
					$this->data[] = array(
						++$i,
						'Money Market Investments',
						'--',
						number_to_currency($result->money_market_investment_amount),
					);
				}




				$total_amount_cash_flow_balance-=$total_cash_outs;
				$this->data[] = array(
					'',
					'Grand Total Cashouts',
					'',
					number_to_currency($total_cash_outs),
					number_to_currency($total_amount_cash_flow_balance),
				);

				$this->data[] = array('');
				$this->data[] = array(
						'',
						'Total Available Cash',
						'',
						'',
						number_to_currency($total_amount_cash_flow_balance),
					);




				/*print_r('<pre>');
				print_r($this->data);
				print_r('</pre>');die;*/

				$filter_parameters = array(
					
				);

				$response = $this->create_spreadsheet_file($filename,$title,$headers,$this->data,$filter_parameters);
				/*if($response){
					$this->excel_generator->download_single_file($filename.'.xlsx');
					$this->excel_generator->delete_single_file($filename.'.xlsx');
				}*/

			}else{
				echo 'Invalid file sent';
			}
		}else{
			echo 'No file Sent';
		}
	}

	function generate_income_statement($income_statement = ''){
		if($income_statement){
			$result = json_decode($income_statement);
			if($result){
				$this->group = $result->group;
				$filename = $this->group->name.' Income Statement Report';
				$title = $this->group->name.' Income Statement Report';
				$group_currency = $result->group_currency;

				//print_r($result);die;
				
				/*print_r('<pre>');
				print_r($this->data);
				print_r('</pre>');die;*/

				$headers = array(
					'Income statement from '.timestamp_to_report_time($result->date_from).' to '.timestamp_to_report_time($result->date_to),
				);

				$this->data[] = array(
					'',
					'Revenues'
				);
				$this->data[] = array(
					'',
					'Miscellaneous Income',$result->total_miscellaneous_income?$result->total_miscellaneous_income:0,
				);
				$this->data[] = array(
					'',
					'Revenue/Income',$result->total_income?$result->total_income:0,
				);
				$this->data[] = array(
					'',
					'Money Market Interest',$result->total_money_market_interest?$result->total_money_market_interest:0,
				);
				$this->data[] = array(
					'',
					'Loan Interest & Fines',$result->total_loan_interest_and_fines?$result->total_loan_interest_and_fines:0,
				);
				$this->data[] = array(
					'',
					'Loan Processing Income',$result->total_loan_processing_income?$result->total_loan_processing_income:0,
				);
				$this->data[] = array(
					'',
					'Total Revenues','',$result->total_revenue?$result->total_revenue:0,
				);

				$this->data[] = array(
					'',
					''
				);
				$this->data[] = array(
					'',
					'Expenses'
				);
				$this->data[] = array(
					'',
					'Group Expenses',$result->total_expenses?$result->total_expenses:0,
				);
				$this->data[] = array(
					'',
					'Bank Loan Interest',$result->total_bank_loan_interest?$result->total_bank_loan_interest:0,
				);
				$this->data[] = array(
					'',
					'Total Expenses','',$result->total_group_expenses?$result->total_group_expenses:0,
				);
				$net_profit_or_loss = $result->total_revenue-$result->total_group_expenses;
				if($net_profit_or_loss>=0){
                   $this->data[] = array(
						'',
						'','Net Profit',$net_profit_or_loss?abs($net_profit_or_loss):0,
					);       
                }else{
                   $this->data[] = array(
						'',
						'','Net Loss',$net_profit_or_loss?abs($net_profit_or_loss):0,
					);
                }
				
				$response = $this->create_income_statement($filename,$title,$headers,$this->data,0);
				/*if($response){
					$this->excel_generator->download_single_file($filename.'.xlsx');
					$this->excel_generator->delete_single_file($filename.'.xlsx');
				}*/

			}else{
				echo 'Invalid file sent';
			}
		}else{
			echo 'No file Sent';
		}
	}

    function generate_member_list($member_list = ''){        
        if($member_list){
            $result = json_decode($member_list);
            if($result){
                $this->group = $result->group;
                $filename = $this->group->application_name.' List of Users';
                $title = $this->group->application_name.' List of Users';

                $group_role_options = array();
                foreach ($result->group_role_options as $key => $value) {
                    $group_role_options[$key] = $value;
                }

                $headers = array(
                    'Full Name',
                    'Phone Number',
                    'Id Number',
                    'Email',
                    'Date of Birth',
                    'Last Login',
                    'Status',
                    'Physical Address',
                    'Postal Address',
                    // 'Place of Work,'
                );

                foreach ($result->posts as $key=>$member) {
                    $this->data[] = array(
                        $key+1,
                       
                        $member->first_name.' '.$member->last_name,
                        $member->phone,
						$member->id_number?$member->id_number:'Not set',
                        $member->email,
                        $member->date_of_birth?timestamp_to_report_time($member->date_of_birth):'',
                        ($member->last_login)?timestamp_to_report_time($member->last_login):'-',
                        $member->active?'Active':'Disabled',
                        isset($member->physical_address)?$member->physical_address:'',
                        isset($member->postal_address)?$member->postal_address:'',
                        // isset($member->place_of_work)?$member->place_of_work:'',
                    );
                }

                $filter_parameters = array();

                $response = $this->create_spreadsheet_file($filename,$title,$headers,$this->data,$filter_parameters);
               /* if($response){
                    $this->excel_generator->download_single_file($filename.'.xlsx');
                    $this->excel_generator->delete_single_file($filename.'.xlsx');
                }*/

            }else{
                echo 'Invalid file sent';
            }
        }else{
            echo 'No file Sent';
        }
    }

    function generate_deposits_listing($deposits_list){        
        if($deposits_list){
            $data = json_decode($deposits_list);
            if($data){
                if($data->posts){
                    $posts = $data->posts;
                    $pagination_record  = isset($data->pagination_record)?$data->pagination_record:0;

                    $this->group = $data->group;

                    $deposit_transaction_names = array();
                    foreach ($data->deposit_transaction_names as $key =>$value) {
                        $deposit_transaction_names[$key] = $value;
                    }

                    $group_member_options = array();
                    foreach ($data->group_member_options as $key => $value) {
                        $group_member_options[$key] = $value;
                    }

                    $group_debtor_options = array();
                    if(isset($data->group_debtor_options)){
                        foreach ($data->group_debtor_options as $key => $value) {
                            $group_debtor_options[$key] = $value;
                        }
                    }
                    
                    $depositor_options= array();
                    foreach ($data->depositor_options as $key => $value) {
                        $depositor_options[$key] = $value;
                    }

                    $deposit_method_options = array();
                    foreach ($data->deposit_method_options as $key => $value) {
                        $deposit_method_options[$key] = $value;
                    }

                    $fine_category_options = array();
                    foreach ($data->fine_category_options as $key => $value) {
                        $fine_category_options[$key] = $value;
                    }

                    $contribution_options = array();
                    foreach ($data->contribution_options as $key => $value) {
                        $contribution_options[$key] = $value;
                    }

                    $income_category_options = array();
                    foreach ($data->income_category_options as $key => $value) {
                        $income_category_options[$key] = $value;
                    }

                    $money_market_investment_options = array();
                    foreach ($data->money_market_investment_options as $key => $value) {
                        $money_market_investment_options[$key] = $value;
                    }
                    
                    $accounts=array();
                    foreach ($data->accounts as $key => $value) {
                        $accounts[$key] = $value;
                    }

                    $stock_options = array();
                    foreach ($data->stock_options as $key => $value) {
                        $stock_options[$key] = $value;
                    }

                    $asset_options = array();
                    foreach ($data->asset_options as $key => $value) {
                        $asset_options[$key] =$value;
                    }

                    $group_currency = $data->group_currency;

                    $headers = array(
                        'Deposit Date',
                        'Payment For',
                        'Amount ('.$group_currency.')',
                        'Depositor',
                        'Account',
                        'Description',
                        'Recorded On',
                    );

                    $filename = $this->group->name." List of all deposits";
                    $title = $this->group->name." List of all deposits";

                    $total_deposits=0;

                    foreach ($posts as $key=>$post) {
                        $first_row = array(
                            $key+1+$pagination_record,
                            timestamp_to_mobile_time($post->deposit_date),
                            $deposit_transaction_names[$post->type],
                            number_to_currency($post->amount),
                        );

                        $depositor = '';
                        if($post->type==13||$post->type==14||$post->type==15||$post->type==16){
                            $depositor = $depositor_options[$post->depositor_id];
                        }else if($post->type==17||$post->type==18||$post->type==19||$post->type==20){
                            $depositor =  $group_member_options[$post->member_id]; 
                        }else if($post->type==25||$post->type==26||$post->type==27||$post->type==28){

                        }else if($post->type==29||$post->type==30||$post->type==31||$post->type==32){
                            $depositor = $money_market_investment_options[$post->money_market_investment_id];
                        }else if($post->type==21||$post->type==22||$post->type==23||$post->type==24){

                        }else if($post->type==25||$post->type==26||$post->type==27||$post->type==28){

                        }else if($post->type==33||$post->type==34||$post->type==35||$post->type==36){

                        }else if($post->type==37||$post->type==38||$post->type==39||$post->type==40){

                        }else if($post->type==45||$post->type==46||$post->type==47||$post->type==48){

                        }else if($post->type==49||$post->type==50||$post->type==51||$post->type==52){

                        }else{
                            $depositor = $group_member_options[$post->member_id]; 
                        }

                        $description='';
                        if($post->type==1||$post->type==2||$post->type==3||$post->type==7){
                            $description.= $deposit_transaction_names[$post->type].' for "'.$contribution_options[$post->contribution_id].'" contribution via '.$deposit_method_options[$post->deposit_method];
                        }else if($post->type==4||$post->type==5||$post->type==6||$post->type==8){
                            if($post->contribution_id){
                                $for = $contribution_options[$post->contribution_id].' contribution late payment';
                            }else if($post->fine_category_id){
                                $for = $fine_category_options[$post->fine_category_id];
                            }else{
                                $for = '';
                            }
                           $description.=$deposit_transaction_names[$post->type].' for "'.$for.'" via '.$deposit_method_options[$post->deposit_method];
                        }else if($post->type==9||$post->type==10||$post->type==11||$post->type==12){
                            $description.=$deposit_transaction_names[$post->type];
                        }else if($post->type==13||$post->type==14||$post->type==15||$post->type==16){
                            $description.=$deposit_transaction_names[$post->type].' from '.$income_category_options[$post->income_category_id];
                        }else if($post->type==17||$post->type==18||$post->type==19||$post->type==20){
                            $description.=$deposit_transaction_names[$post->type].' via '.$deposit_method_options[$post->deposit_method].' to '.$accounts[$post->account_id];
                        }else if($post->type==25||$post->type==26||$post->type==27||$post->type==28){
                            $description.=$deposit_transaction_names[$post->type].' of '.$post->number_of_shares_sold.' "'.$stock_options[$post->stock_id].'" shares';
                        }else if($post->type==29||$post->type==30||$post->type==31||$post->type==32){
                            $description.=$deposit_transaction_names[$post->type];
                        }else if($post->type==21||$post->type==22||$post->type==23||$post->type==24){
                            $description.=$deposit_transaction_names[$post->type];
                        }else if($post->type==37||$post->type==38||$post->type==39||$post->type==40){
                            $description.=$deposit_transaction_names[$post->type];
                        }else if($post->type==45||$post->type==46||$post->type==47||$post->type==48){
                            $description.= 'External lending to Debtor: '.$group_debtor_options[$post->debtor_id];
                        }else if($post->type==49||$post->type==50||$post->type==51||$post->type==52){
                            $description.= 'External loan repayment by : '.$group_debtor_options[$post->debtor_id];
                        }

                        if($post->account_id){
                            $account = $accounts[$post->account_id];
                        }
                        if($post->description){
                            $description.= $post->description;
                        }
                        $reconciled="";
                        if($post->transaction_alert_id){
                            $reconciled = "Reconciled ";
                        }

                        $second_row = array(
                            $depositor,
                            $account,
                            $reconciled?$description.' - '.$reconciled:$description,
                            timestamp_to_mobile_time($post->created_on),
                        );
                        $total_deposits+=$post->amount;

                        $this->data[] = array_merge($first_row,$second_row);
                    }

                    $this->data = array_merge($this->data,array(
                        array(
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                        ),
                        array(
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                        ),
                        array(
                            '',
                            '',
                            'Total Deposits',
                            number_to_currency($total_deposits),
                            '',
                            '',
                            '',
                            '',
                        ),
                    ));


                    $parameters = array();
                    foreach ($data->filters as $key => $value) {
                        if(is_object($value)){
                            $parameters[$key] = (array)$value;
                        }else{
                            $parameters[$key] = $value;
                        }
                    }

                    $filter_parameters = array(
                        'From' => $parameters['from']?timestamp_to_report_time($parameters['from']):'',
                        'To' => $parameters['to']?timestamp_to_report_time($parameters['to']):'',
                        'Member'=> $this->_generate_member_list($group_member_options,$parameters['member_id']),
                        'Deposit Type' => $this->_generate_deposit_type_list($deposit_transaction_names,$parameters['type']),
                        'Contributions' => $this->_generate_contribution_list($contribution_options,$parameters['contributions']),
                        'Fines' => $this->_generate_fine_list($fine_category_options,$contribution_options,$parameters['fine_categories']),
                        'Income Categories' => $this->_generate_income_category_list($income_category_options,$parameters['income_categories']),
                        'Stocks' => $this->_generate_stock_list($stock_options,$parameters['stocks']),
                        'Money Markets Investments'=>$this->_generate_money_market_investment_list($money_market_investment_options,$parameters['money_market_investments']),
                        'Assets' => $this->_generate_asset_list($asset_options,$parameters['assets']),
                        'Accounts' => $this->_generate_account_list($accounts,$parameters['accounts']), 
                    );
                    //print_r($filter_parameters);die;
                    //print_r($filename);die;
                    $response = $this->create_spreadsheet_file($filename,$title,$headers,$this->data,$filter_parameters);
                    /*if($response){
                        $this->excel_generator->download_single_file($filename.'.xlsx');
                        $this->excel_generator->delete_single_file($filename.'.xlsx');
                    }*/

                }else{
                    echo 'Sorry, no records found';
                }
            }else{
                echo 'invalid file sent';
            }
        }else{
            echo 'No file Sent';
        }
    }

    function generate_withdrawals_listing($withdrawals_listing = ''){
        if($withdrawals_listing){
            $result = json_decode($withdrawals_listing);
            if($result){
                $this->group = $result->group;
                $filename = $this->group->name.' Withdrawal List';
                $title = $this->group->name.' Withdrawal List';

                $to = $result->to;
                $from = $result->from;
                $asset_options = array();
                foreach ($result->asset_options as $key => $value) {
                    $asset_options[$key] = $value;
                }

                $contribution_options=array();
                foreach ($result->contribution_options as $key => $value) {
                    $contribution_options[$key] = $value;
                }

                $withdrawal_transaction_names = array();
                foreach ($result->withdrawal_transaction_names as $key => $value) {
                    $withdrawal_transaction_names[$key] = $value;
                }

                $withdrawal_type_options = array();
                foreach ($result->withdrawal_type_options as $key => $value) {
                    $withdrawal_type_options[$key] = $value;
                }

                $expense_category_options = array();
                foreach ($result->expense_category_options as $key => $value) {
                    $expense_category_options[$key] = $value;
                }

                $group_member_options = array();
                foreach ($result->group_member_options as $key => $value) {
                    $group_member_options[$key] = $value;
                }

                $group_debtor_options = array();
                if(isset($result->group_debtor_options)){
                    foreach ($result->group_debtor_options as $key => $value) {
                        $group_debtor_options[$key] = $value;
                    }
                }
                

                $account_options = array();
                foreach ($result->account_options as $key => $value) {
                    $account_options[$key] = $value;
                }

                $headers = array(
                    'Withdrawal Date',
                    'Withdrawal Type',
                    'Amount ('.$result->group_currency.')',
                    'Description',
                    'Recorded On',
                );

                $total_withdrawals = 0;

                foreach ($result->posts as $key => $post) {
                    $first_row = array(
                        $key+1,
                        timestamp_to_mobile_time($post->withdrawal_date),
                        $withdrawal_transaction_names[$post->type],
                    );

                    $withdral_type = '';

                    if($post->type==1||$post->type==2||$post->type==3||$post->type==4){
                        $withdral_type = $withdrawal_transaction_names[$post->type].' for '.$expense_category_options[$post->expense_category_id];
                    }else if($post->type==5||$post->type==6||$post->type==7||$post->type==8){
                       $withdral_type =  $withdrawal_transaction_names[$post->type].' for '.$asset_options[$post->asset_id];
                    }else if($post->type==9||$post->type==10||$post->type==11||$post->type==12){
                       $withdral_type =  $withdrawal_transaction_names[$post->type];
                        if($post->member_id){ $withdral_type.=  ' to '.$group_member_options[$post->member_id];}
                    }else if($post->type==21||$post->type==22||$post->type==23||$post->type==24){
                        $withdral_type =  $withdrawal_transaction_names[$post->type].' to '.$group_member_options[$post->member_id].' for '.$contribution_options[$post->contribution_id];
                    }else if($post->type==13||$post->type==14||$post->type==15||$post->type==16){
                        $withdral_type =  $withdrawal_transaction_names[$post->type];
                    }else if($post->type==25||$post->type==26||$post->type==27||$post->type==28){
                        $withdral_type =  $withdrawal_transaction_names[$post->type];
                    }else if($post->type==29||$post->type==30||$post->type==31||$post->type==32){
                        $withdral_type =  $withdrawal_transaction_names[$post->type].' From '.$account_options[$post->from_account_id].' To '.$account_options[$post->to_account_id];
                    }else if($post->type==33||$post->type==34||$post->type==35||$post->type==36){
                        echo $withdrawal_transaction_names[$post->type];
                        if($post->debtor_id){ echo ' to '.$group_debtor_options[$post->debtor_id];}
                    }


                    $description= $withdral_type.' '.$post->description;
                    if($post->transaction_alert_id){
                        $description.=' - Reconcilled';
                    }

                    $total_withdrawals += $post->amount;

                    $second_row = array(
                        number_to_currency($post->amount),
                        $description,
                        timestamp_to_mobile_time($post->created_on),
                    );

                    $this->data[] = array_merge($first_row,$second_row);
                }

                $this->data = array_merge($this->data,array(
                        array(
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                        ),
                        array(
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                        ),
                        array(
                            '',
                            '',
                            'Total Amount',
                            number_to_currency($total_withdrawals),
                            '',
                            '',
                        ),
                    ));

                $filters =(array)$result->filters;

                $filter_parameters = array(
                    'From' => $from?timestamp_to_report_time($from):'',
                    'To' => $to?timestamp_to_report_time($to):'',
                    'Withdrawal Type' => $this->_generate_withdrawal_type($withdrawal_transaction_names,$filters['type']),
                );

                $response = $this->create_spreadsheet_file($filename,$title,$headers,$this->data,$filter_parameters);
                /*if($response){
                    $this->excel_generator->download_single_file($filename.'.xlsx');
                    $this->excel_generator->delete_single_file($filename.'.xlsx');
                }*/

            }else{
                echo 'Invalid file sent';
            }
        }else{
            echo 'No file Sent';
        }
    }
    
    function _generate_withdrawal_type($withdrawal_transaction_names=array(),$types=''){
        if($types){
            $types = explode_str_to_array($types);
            $type_list = '';
            foreach ($types as $type) {
                if($type_list){
                    $type_list=$withdrawal_transaction_names[$type];
                }else{
                    $type_list=$withdrawal_transaction_names[$type];
                }
            }
            return $type_list;
        }
    }

    function contribution_summary($file=''){
       $result = json_decode($file);
        if($result){
            $this->group = $result->group;
            $group_currency = $result->group_currency;
            $filename = $this->group->name.' Contributions Summary as at '.timestamp_to_report_time(time());
            $title = $this->group->name.'  Contributions Summary '.timestamp_to_report_time(time());

            $active_group_member_options = array();

            foreach ($result->active_group_member_options as $key => $value) {
                $active_group_member_options[$key] = $value;
            }

            $contribution_options = array();

            foreach ($result->contribution_options as $key => $value) {
                $contribution_options[$key] = $value;
            }

            $added_header = array();
            $total_contributions_array = array();

            foreach ($contribution_options as $contribution_id => $contribution_name) {
                $total_contributions_array[$contribution_id] = 0;
                $added_header[] = $contribution_name.' Amount ('.$group_currency.')';
            }

            $contribution_list = '';
            foreach ($contribution_options as $id=>$name) {
                if($contribution_list){
                    $contribution_list.=','.$id;
                }else{
                    $contribution_list=$id;
                }
            }
            $total_members_deposit_amounts = $this->ci->statements_m->get_group_members_total_paid_by_contribution_array($this->group->id,$contribution_list);

            $headers = array(
                "Member Name",
                "Membership Number",
                "Status",
            );

            $headers = array_merge($headers,$added_header,array('Total Amount ('.$group_currency.') '));
            $grand_total = 0;
            $i = 0;
            $row_count = 4;
            foreach ($active_group_member_options as $key => $member) {
                $member_name = $member->first_name .' '.$member->last_name;
                $member_id = $member->id;
                $total_amount = 0;
                $status = $member->active?'Active':'Suspended';
                $membership_number = $member->membership_number?$member->membership_number:'-';
                $total_member_amount_paid = 0;
                $this->data[$row_count] = array(
                    ++$i,
                    $member_name,
                    $membership_number,
                    $status,
                );
                $index = 4;
                foreach ($contribution_options as $contribution_id => $contribution_name) {
                    $amount_paid = isset($total_members_deposit_amounts[$member_id][$contribution_id])?$total_members_deposit_amounts[$member_id][$contribution_id]:0;
                    $grand_total+=$amount_paid;
                    $total_contributions_array[$contribution_id]+=$amount_paid;
                    $total_member_amount_paid += $amount_paid;
                    $this->data[$row_count][$index] = $amount_paid;
                    $index++;
                }
                $this->data[$row_count][++$index] = $total_member_amount_paid;
                $row_count++;
            }
            $this->data[$row_count] = array(
                '',                                
                'Totals',
                '',
                '',
            );

            $index = 4;
            foreach ($contribution_options as $contribution_id => $name) {
                $this->data[$row_count][$index] = $total_contributions_array[$contribution_id];
                $index++;
            }
            $this->data[$row_count][++$index] = $grand_total;
            $response = $this->create_spreadsheet_file($filename,$title,$headers,$this->data,FALSE);
        }else{
            echo 'invalid file sent';
        }
    }

    function generate_sms_templates($file = ''){
        $result = json_decode($file);
        if($result){
            $filename = ' Smses templates as at '.timestamp_to_report_time(time());
            $title = ' Smses templates  '.timestamp_to_report_time(time());
            $headers = array(
                "#",
                "Name",
                "Message",
                "Description",
            );
            $posts = $result->posts;
            $messages = [];
            $count = 0;
            foreach ($posts as $key => $post) {
                $this->data[] = [
                    '',
                    ++$count,
                    $post->title,
                    $post->sms_template,
                    $post->description

                ];
            }
            $response = $this->create_spreadsheet_file($filename,$title,$headers,$this->data,FALSE);
            print_r($response); die();
        }else{
            echo 'invalid file sent';
        }
    }

    function generate_group_listing_report($file=''){
        $data = json_decode($file);
        //print_r($data);die;
        if($data){
            $filename = 'Group Onboarding Summary';
            $title = 'Group summary';

            $headers = array(
                "#",
                "Group Name",
                "Group Size",
                "Admin Details",
                "Phone",
                "Email",
                "Onboarded By",
                "Onboarded On",
                "Account Name",
                "Account Number",
                "Branch",
                "Account Balance Amount",
                "Setup Status",
            );
            $posts = $data->posts;
            $setup_tasks = (array)($data->setup_tasks);
            $this->file = array();
            $admin_users = $data->admin_users;
            foreach ($posts as $key => $post) {
                $id = $post->id;
                $created_by = $post->created_by;
                $accounts = isset($data->bank_accounts->$id)?$data->bank_accounts->$id:'';
                $account_name = '';
                $account_number = '';
                $branch = '';
                $account_balance = number_to_currency(0);
                $setup_position = $post->group_setup_position;
                if($accounts){
                    foreach ($accounts as $key_account => $value) {
                        $currency_id = $value->account_currency_id;
                        $account_name = $value->account_name;
                        $account_number = $value->account_number;
                        $branch = $value->bank_branch;
                        $account_balance = number_to_currency($value->current_balance);
                    }
                }
                $this->file[] = array(
                    '',
                    ($key+1),
                    $post->name,
                    $post->active_size,
                    $post->username,
                    $post->phone,
                    $post->email,
                    isset($admin_users->$created_by)?$admin_users->$created_by:'',
                    timestamp_to_date($post->created_on,TRUE),
                    $account_name,
                    $account_number,
                    $branch,
                    $account_balance,
                    isset($setup_tasks[$setup_position])?$setup_tasks[$setup_position]:'Unknown',
                );
            }
            $response = $this->create_spreadsheet_file($filename,$title,$headers,$this->file,FALSE);
            print_r($response); die();
        }else{
            echo 'invalid file sent';
        }

        // $filename = 'Groups summary';
        //     $title = 'Groups summary';





        //     $headers = array(
        //         "#",
        //         "Group Name",
        //         "Size",
        //         "Join Date",
        //         "Onboarded Date",
        //         "Onboarded By",
        //         "Group Admin Name",
        //         "Group Admin Phone Number",
        //         "Group Admin Email Address",
        //         "Bank Details",
        //         "Account Balance",
        //     );
    }

}