<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stocks_m extends MY_Model {

	protected $_table = 'stocks';

	public function __construct(){
		parent::__construct();
		$this->load->dbforge();
		//$this->install();
	}

	public function install(){
		$this->db->query("
		create table if not exists stocks(
			id int not null auto_increment primary key,
			`name` blob,
			`symbol` blob,
			`purchase_date` blob,
			`purchase_price` blob,
			`number_of_shares` blob,
			`current_price` blob,
			`withdrawal_account_id` blob,
			`group_id` blob,
			`description` blob,
			`active` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");

		$this->db->query("
		create table if not exists stock_sales(
			id int not null auto_increment primary key,
			`sale_date` blob,
			`group_id` blob,
			`stock_id` blob,
			`number_of_shares_sold` blob,
			`sale_price_per_share` blob,
			`description` blob,
			`active` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");
	}

	function insert($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('stocks',$input);
	}

	function insert_stock_sale($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('stock_sales',$input);
	}

	function update($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'stocks',$input);
    }

 	function update_where($where = "",$input = array()){
        return $this->update_secure_where($where,'stocks',$input);
    }

    function update_stock_sale($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'stock_sales',$input);
    }

    function get_group_stock_sale($id = 0){
		$this->select_all_secure('stock_sales');
		$this->db->where('id',$id);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->get('stock_sales')->row();
    }

    function get_group_stock($id = 0,$group_id = 0){
		$this->select_all_secure('stocks');
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->get('stocks')->row();
	}

	function get_group_stocks($filter_parameters = array(),$group_id =0){
		$this->select_all_secure('stocks');
		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('purchase_date').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('purchase_date').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
			}
		}
		if(isset($filter_parameters['stocks']) && $filter_parameters['stocks']){
			$stocks = $filter_parameters['stocks'];
			if(empty($stocks)){
        		$this->db->where(' id IN (0)',NULL,FALSE);
			}else{
        		$this->db->where(' id IN ('.implode(',',$stocks).')',NULL,FALSE);
			}
		}
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->get('stocks')->result();
	}

	function get_group_stock_options($group_id=0){
		$arr = array();
		$this->db->select(
			array(
				'id',
				$this->dx('name').' as name '
			)
		);
		 
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$stocks =  $this->db->get('stocks')->result();
		foreach($stocks as $stock){
			$arr[$stock->id] = $stock->name;
		}
		return $arr;
	}

	function count_group_stocks($filter_parameters = array(),$group_id = 0){
		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('purchase_date').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('purchase_date').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
			}
		}
		if(isset($filter_parameters['stocks']) && $filter_parameters['stocks']){
			$stocks = $filter_parameters['stocks'];
			if(empty($stocks)){
        		$this->db->where(' id IN (0)',NULL,FALSE);
			}else{
        		$this->db->where(' id IN ('.implode(',',$stocks).')',NULL,FALSE);
			}
		}
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->count_all_results('stocks');
	}

	function get_group_stock_sales(){
		$this->select_all_secure('stock_sales');
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->get('stock_sales')->result();
	}

	function count_group_stock_sales(){
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->count_all_results('stock_sales');
	}

	function get_group_stock_sale_options($group_id = 0){
		$arr = array();
		$this->select_all_secure('stock_sales');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$stock_sales = $this->db->get('stock_sales')->result();
		$stock_options = $this->get_group_stock_options();
		foreach ($stock_sales as $stock_sale) {
			# code...
			$arr[$stock_sale->id] = $stock_sale->number_of_shares_sold.' '.$stock_options[$stock_sale->stock_id].' shares sold at '.$this->group_currency.' '.number_to_currency($stock_sale->sale_price_per_share);
		}
		return $arr;
	}

	function get_group_current_stocks_value($group_id = 0,$from = 0,$to = 0){
		$this->db->select(
			array(
				'stocks.id as id',
				$this->dx('stocks.number_of_shares').' as number_of_shares',
				$this->dx('stocks.purchase_price').' as purchase_price',
				$this->dx('stocks.current_price'). 'as current_price',
				$this->dx('stocks.number_of_shares').'-'.$this->dx('stocks.number_of_shares_sold').' as remaining_shares',
				'('.$this->dx('stocks.number_of_shares').'-'.$this->dx('stocks.number_of_shares_sold').') *'.$this->dx('stocks.current_price').' current_group_share_valuation',
			)
		);
		if($from){
			$this->db->where($this->dx('stocks.purchase_date').' <= "'.$from.'" ',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('stocks.purchase_date').' >= "'.$to.'" ',NULL,FALSE);
		}
		if($group_id){
			$this->db->where($this->dx('stocks.group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('stocks.group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('stocks.active').' = "1"',NULL,FALSE);
		$stocks = $this->db->get('stocks')->result();

		$total_value =0;
		foreach ($stocks as $stock){
			$total_value += $stock->current_group_share_valuation;
		}

		$result = array($stocks,'total_value'=>$total_value);
		return $result;
	}

	function get_group_stock_value(){
		$this->select_all_secure('stocks');
		$this->db->where($this->dx('stocks.group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('stocks.active').'="1"',NULL,FALSE);
		$group_stocks = $this->db->get('stocks')->result();

		$total_current_price=0;
		$total_initial_price = 0;

		foreach ($group_stocks as $stock) {
			$current_stock_price = (($stock->number_of_shares - $stock->number_of_shares_sold)*$stock->current_price); 
			$initial_stock_price = (($stock->number_of_shares - $stock->number_of_shares_sold)*$stock->purchase_price); 
			$total_current_price+=$current_stock_price;
			$total_initial_price+=$initial_stock_price;
		}

		return (object)array('total_current_price'=>$total_current_price,'total_initial_price'=>$total_initial_price);
	}

	function get_group_back_dating_stock_objects_array(){
		$this->select_all_secure('stocks');
		$this->db->where($this->dx('stocks.group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('stocks.active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('stocks.is_a_back_dating_record').'="1"',NULL,FALSE);
		$stocks = $this->db->get('stocks')->result();
		$arr = array();
		foreach($stocks as $stock):
			$arr[$stock->id] = $stock;
		endforeach;
		return $arr;
	}

	function update_group_back_dating_stocks_cut_off_date($group_id = 0,$input = array()){
        $where = " ".$this->dx('is_a_back_dating_record')." = '1' AND ".$this->dx('group_id')." = '".$group_id."' AND ".$this->dx('active')." = '1' ;";
        if($this->update_where($where,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

     function get_group_total_stocks_retained_per_month_array($group_id = 0){
    	//get stocks
        $this->db->select(
        	array(
        		'id',
        		$this->dx('purchase_date')." as purchase_date",
				" DATE_FORMAT(FROM_UNIXTIME(".$this->dx('purchase_date')." ),'%Y') as year ",	
				" DATE_FORMAT(FROM_UNIXTIME(".$this->dx('purchase_date')." ),'%b') as month ",	
        		$this->dx('purchase_price')." as purchase_price",
        		$this->dx('number_of_shares')." as number_of_shares",
        		$this->dx('number_of_shares_sold')." as number_of_shares_sold",
        	)
        );
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->order_by($this->dx('purchase_date'),'ASC',FALSE);
		$stocks = $this->db->get('stocks')->result();
		$stock_objects_array = array();

		foreach($stocks as $stock):
			$stock_objects_array[$stock->id] = $stock;
		endforeach;

		$arr = array();

		$first_month = date('M Y');
		foreach($stocks as $stock):
			$first_month = $stock->month.' '.$stock->year;
			break;
		endforeach;

		$current_month = date('M Y');
		$months_array = generate_months_array(strtotime($first_month),strtotime($current_month));

		foreach($months_array as $month):
			$arr[$month] = 0;
		endforeach;

		foreach($stocks as $stock):
			$price = $stock->number_of_shares * $stock->purchase_price;
			if(isset($arr[$stock->month.' '.$stock->year])){
				$arr[$stock->month.' '.$stock->year] += $price;
			}else{
				$arr[$stock->month.' '.$stock->year] = $price;
			}
		endforeach;

		$this->db->select(
			array(
				$this->dx('sale_price_per_share')." as sale_price_per_share ",
				" DATE_FORMAT(FROM_UNIXTIME(".$this->dx('sale_date')." ),'%Y') as year ",	
				" DATE_FORMAT(FROM_UNIXTIME(".$this->dx('sale_date')." ),'%b') as month ",	
				$this->dx('stock_id')." as stock_id ",
				$this->dx('number_of_shares_sold')." as number_of_shares_sold ",
			)
		);
		$this->db->where($this->dx('group_id').' ="'.$group_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
        $stock_sales = $this->db->get('stock_sales')->result();

		foreach($stock_sales as $stock_sale):
			$price = $stock_sale->number_of_shares_sold * $stock_objects_array[$stock_sale->stock_id]->purchase_price;
			$arr[$stock_sale->month.' '.$stock_sale->year] -= $price;
		endforeach;

		// for($i = $first_year; $i <= $current_year; $i++):
		// 	if($i == $first_year){
		// 		//echo "Am in <br/>";
		// 	}else{
		// 		//echo $current_year." | Am out <br/>";
		// 		$arr[$i] += $arr[($i - 1)];
		// 	}
		// endfor;
		foreach($months_array as $month):
			if($month == $first_month){

			}else{
				$previous_month = date('M Y',strtotime('-1 month',strtotime($month)));
				if(isset($arr[($previous_month)])){
					$arr[$month] += $arr[($previous_month)];
				}
			}
		endforeach;

		return $arr;

    }

    function get_group_total_stocks_sale_income_per_month_array($group_id = 0){

		$this->db->select(
			array(
				"SUM( ".$this->dx('amount')." ) as sale_amount ",
				" DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%Y') as year ",	
				" DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%b') as month ",	
				$this->dx('stock_id')." as stock_id "
			)
		);
		$this->db->where($this->dx('group_id').' ="'.$group_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('active').' ="1" ',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (25,26,27,28) ',NULL,FALSE);
		$this->db->group_by(
        	array(
        		'month',
        		'year',
        		$this->dx('stock_id')
        	)
        );
        $stock_sales = $this->db->get('deposits')->result();


        //get each stock purchase price
        $this->db->select(
        	array(
        		"id",
				" SUM( ".$this->dx('number_of_shares')." * ".$this->dx('purchase_price')." ) as purchase_amount ",
        	)
        );
		$this->db->where($this->dx('stocks.group_id').' ="'.$group_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('stocks.active').' ="1" ',NULL,FALSE);
        $this->db->group_by(
        	array(
        		'id'
        	)
        );
        $stock_purchases = $this->db->get('stocks')->result();

        $stock_purchases_array = array();

        foreach($stock_purchases as $stock_purchase):
        	$stock_purchases_array[$stock_purchase->id] = $stock_purchase->purchase_amount;
        endforeach;

        $stocks_sale_income_per_month_array = array();

        foreach($stock_sales as $stock_sale):

        	if($stock_sale->sale_amount > $stock_purchases_array[$stock_sale->stock_id]){
        		$income = $stock_sale->sale_amount - $stock_purchases_array[$stock_sale->stock_id];
        		if(isset($stocks_sale_income_per_month_array[$stock_sale->month.' '.$stock_sale->year])){
        			$stocks_sale_income_per_month_array[$stock_sale->month.' '.$stock_sale->year] += $income;
        		}else{
        			$stocks_sale_income_per_month_array[$stock_sale->month.' '.$stock_sale->year] = $income;
        		}
        	}else{
        		$stock_purchases_array[$stock_sale->stock_id] -= $stock_sale->sale_amount;
        	}

        endforeach;

        return $stocks_sale_income_per_month_array;

    }

    function get_group_total_stocks_sale_losses_per_month_array($group_id = 0){

		$this->db->select(
			array(
				$this->dx('sale_price_per_share')." as sale_price_per_share ",
				" DATE_FORMAT(FROM_UNIXTIME(".$this->dx('sale_date')." ),'%Y') as year ",	
				" DATE_FORMAT(FROM_UNIXTIME(".$this->dx('sale_date')." ),'%b') as month ",	
				$this->dx('stock_id')." as stock_id ",
				$this->dx('number_of_shares_sold')." as number_of_shares_sold ",
			)
		);
		$this->db->where($this->dx('group_id').' ="'.$group_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
        $stock_sales = $this->db->get('stock_sales')->result();

        //get stocks
        $this->db->select(
        	array(
        		'id',
        		$this->dx('purchase_date')." as purchase_date",
        		$this->dx('purchase_price')." as purchase_price",
        		$this->dx('number_of_shares')." as number_of_shares",
        		$this->dx('number_of_shares_sold')." as number_of_shares_sold",
        	)
        );
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$stocks = $this->db->get('stocks')->result();

		$stock_objects_array = array();

		foreach($stocks as $stock):
			$stock_objects_array[$stock->id] = $stock;
		endforeach;

        $stocks_sale_losses_per_month_array = array();

		foreach($stock_sales as $stock_sale):
			$sale_price = $stock_sale->sale_price_per_share * $stock_sale->number_of_shares_sold;
			$purchase_price = $stock_objects_array[$stock_sale->stock_id]->purchase_price * $stock_sale->number_of_shares_sold;
			$loss = 0;
			if($purchase_price > $sale_price){
				$loss = $purchase_price - $sale_price;
				if(isset($stocks_sale_losses_per_month_array[$stock_sale->month.' '.$stock_sale->year])){
					$stocks_sale_losses_per_month_array[$stock_sale->month.' '.$stock_sale->year] += $loss;
				}else{
					$stocks_sale_losses_per_month_array[$stock_sale->month.' '.$stock_sale->year] = $loss;
				}
			}
		endforeach;

		return $stocks_sale_losses_per_month_array;

    }


    function get_group_total_stocks_retained_per_year_array($group_id = 0){
    	//get stocks
        $this->db->select(
        	array(
        		'id',
        		$this->dx('purchase_date')." as purchase_date",
				" DATE_FORMAT(FROM_UNIXTIME(".$this->dx('purchase_date')." ),'%Y') as year ",	
        		$this->dx('purchase_price')." as purchase_price",
        		$this->dx('number_of_shares')." as number_of_shares",
        		$this->dx('number_of_shares_sold')." as number_of_shares_sold",
        	)
        );
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->order_by($this->dx('purchase_date'),'ASC',FALSE);
		$stocks = $this->db->get('stocks')->result();
		$stock_objects_array = array();

		foreach($stocks as $stock):
			$stock_objects_array[$stock->id] = $stock;
		endforeach;

		$arr = array();

		$first_year = date('Y');
		foreach($stocks as $stock):
			$first_year = $stock->year;
			break;
		endforeach;

		$current_year = date('Y');
		for($i = $first_year; $i <= $current_year; $i++):
			$arr[$i] = 0;
		endfor;

		foreach($stocks as $stock):
			$price = $stock->number_of_shares * $stock->purchase_price;
			$arr[$stock->year] += $price;
		endforeach;

		$this->db->select(
			array(
				$this->dx('sale_price_per_share')." as sale_price_per_share ",
				" DATE_FORMAT(FROM_UNIXTIME(".$this->dx('sale_date')." ),'%Y') as year ",	
				$this->dx('stock_id')." as stock_id ",
				$this->dx('number_of_shares_sold')." as number_of_shares_sold ",
			)
		);
		$this->db->where($this->dx('group_id').' ="'.$group_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
        $stock_sales = $this->db->get('stock_sales')->result();

		foreach($stock_sales as $stock_sale):
			$price = $stock_sale->number_of_shares_sold * $stock_objects_array[$stock_sale->stock_id]->purchase_price;
			$arr[$stock_sale->year] -= $price;
		endforeach;

		for($i = $first_year; $i <= $current_year; $i++):
			if($i == $first_year){
				//echo "Am in <br/>";
			}else{
				//echo $current_year." | Am out <br/>";
				$arr[$i] += $arr[($i - 1)];
			}
		endfor;

		return $arr;

    }


    function get_group_total_stocks_sale_income_per_year_array($group_id = 0){

		$this->db->select(
			array(
				"SUM( ".$this->dx('amount')." ) as sale_amount ",
				" DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%Y') as year ",	
				$this->dx('stock_id')." as stock_id "
			)
		);
		$this->db->where($this->dx('group_id').' ="'.$group_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('active').' ="1" ',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (25,26,27,28) ',NULL,FALSE);
		$this->db->group_by(
        	array(
        		'year',
        		$this->dx('stock_id')
        	)
        );
        $stock_sales = $this->db->get('deposits')->result();


        //get each stock purchase price
        $this->db->select(
        	array(
        		"id",
				" SUM( ".$this->dx('number_of_shares')." * ".$this->dx('purchase_price')." ) as purchase_amount ",
        	)
        );
		$this->db->where($this->dx('stocks.group_id').' ="'.$group_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('stocks.active').' ="1" ',NULL,FALSE);
        $this->db->group_by(
        	array(
        		'id'
        	)
        );
        $stock_purchases = $this->db->get('stocks')->result();

        $stock_purchases_array = array();

        foreach($stock_purchases as $stock_purchase):
        	$stock_purchases_array[$stock_purchase->id] = $stock_purchase->purchase_amount;
        endforeach;

        $stocks_sale_income_per_year_array = array();

        foreach($stock_sales as $stock_sale):

        	if($stock_sale->sale_amount > $stock_purchases_array[$stock_sale->stock_id]){
        		$income = $stock_sale->sale_amount - $stock_purchases_array[$stock_sale->stock_id];
        		if(isset($stocks_sale_income_per_year_array[$stock_sale->year])){
        			$stocks_sale_income_per_year_array[$stock_sale->year] += $income;
        		}else{
        			$stocks_sale_income_per_year_array[$stock_sale->year] = $income;
        		}
        	}else{
        		$stock_purchases_array[$stock_sale->stock_id] -= $stock_sale->sale_amount;
        	}

        endforeach;

        return $stocks_sale_income_per_year_array;

    }


    function get_group_total_stocks_sale_losses_per_year_array($group_id = 0){

		$this->db->select(
			array(
				$this->dx('sale_price_per_share')." as sale_price_per_share ",
				" DATE_FORMAT(FROM_UNIXTIME(".$this->dx('sale_date')." ),'%Y') as year ",	
				$this->dx('stock_id')." as stock_id ",
				$this->dx('number_of_shares_sold')." as number_of_shares_sold ",
			)
		);
		$this->db->where($this->dx('group_id').' ="'.$group_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
        $stock_sales = $this->db->get('stock_sales')->result();

        //get stocks
        $this->db->select(
        	array(
        		'id',
        		$this->dx('purchase_date')." as purchase_date",
        		$this->dx('purchase_price')." as purchase_price",
        		$this->dx('number_of_shares')." as number_of_shares",
        		$this->dx('number_of_shares_sold')." as number_of_shares_sold",
        	)
        );
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$stocks = $this->db->get('stocks')->result();

		$stock_objects_array = array();

		foreach($stocks as $stock):
			$stock_objects_array[$stock->id] = $stock;
		endforeach;

        $stocks_sale_losses_per_year_array = array();

		foreach($stock_sales as $stock_sale):
			$sale_price = $stock_sale->sale_price_per_share * $stock_sale->number_of_shares_sold;
			$purchase_price = $stock_objects_array[$stock_sale->stock_id]->purchase_price * $stock_sale->number_of_shares_sold;
			$loss = 0;
			if($purchase_price > $sale_price){
				$loss = $purchase_price - $sale_price;
				if(isset($stocks_sale_losses_per_year_array[$stock_sale->year])){
					$stocks_sale_losses_per_year_array[$stock_sale->year] += $loss;
				}else{
					$stocks_sale_losses_per_year_array[$stock_sale->year] = $loss;
				}
			}
		endforeach;

		return $stocks_sale_losses_per_year_array;

    }

}