<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Contribution_invoices{

    /*
        1. Contribution invoice
        2. Contribution fine invoice
        3. Fine invoice
        4. General group invoice for special purposes defined by the group admin 
        5. Back dated contribution invoice
        6. Back dated contribution fine invoice
        7. Back dated fine invoice
        8. Back dated general group invoice for special purposes defined by the group admin 
    */
    public $sms_template_default = 'Hi [FIRST_NAME], you have been invoiced [GROUP_CURRENCY] [INVOICED_AMOUNT], for your [CONTRIBUTION_NAME], new balance is [GROUP_CURRENCY] [CONTRIBUTION_BALANCE].';

	protected $ci;

    public $contribution_type_options = array(
        1 => 'Regular Contribution',
        2 => 'One Time Contribution',
        3 => 'Non Scheduled Contribution',
    );

    public $contribution_category_options = array(
        1 => 'Share Capital',
        2 => 'Savings Contribution',
        3 => 'Welfare Contribution / Community Contribution',
        4 => 'Membership / Entry fee contribution',
        5 => 'Investment Contribution',
        6 => 'Other Contributions',
    );

    public $contribution_frequency_options=array(
        ''=>'Select...',
        1=>'Once a Month (Monthly)',
        6=>'Once a Week (Weekly)',
        8=>'Once a Day (Daily)',
        7=>'Once Every Two Weeks (Fortnightly)',
        2=>'Once Every Two Months (Bimonthly)',
        3=>'Once Every Three Months (Quarterly)',
        4=>'Once Every Six Months (Biannually)',
        5=>'Once a Year (Annually)', 
        9=>'Twice Every  Month (After A Date)', 
    );
    public $contribution_days_option = array(
        ''=>'--Select Day of the Month--',
        '1'=>'Every 1st',
        '2'=>'Every 2nd',
        '3'=>'Every 3rd',
        '4'=>'Every 4th',
    );

    public $mobile_frequency_options=array(
        1=>'Monthly',
        6=>'Weekly',
        8=>'Daily',
        7=>'Fortnightly',
        2=>'Bimonthly)',
        3=>'Quarterly',
        4=>'Bi-Annually',
        5=>'Annually', 
        9=>'After A Date', 
    );

    public $days_of_the_month = array(
        ''=>'--Select Day of the Month--',
        '1'=>'Every 1st',
        '2'=>'Every 2nd',
        '3'=>'Every 3rd',
        '4'=>'Every 4th',
        '5'=>'Every 5th',
        '6'=>'Every 6th',
        '7'=>'Every 7th',
        '8'=>'Every 8th',
        '9'=>'Every 9th',
        '10'=>'Every 10th',
        '11'=>'Every 11th',
        '12'=>'Every 12th',
        '13'=>'Every 13th',
        '14'=>'Every 14th',
        '15'=>'Every 15th',
        '16'=>'Every 16th',
        '17'=>'Every 17th',
        '18'=>'Every 18th',
        '19'=>'Every 19th',
        '20'=>'Every 20th',
        '21'=>'Every 21st',
        '22'=>'Every 22nd',
        '23'=>'Every 23rd',
        '24'=>'Every 24th',
        '25'=>'Every 25th',
        '26'=>'Every 26th',
        '27'=>'Every 27th',
        '28'=>'Every 28th',
        '29'=>'Every 29th',
        '30'=>'Every 30th',
        '32'=> 'Every Last',

    );

    public $month_days=array(
        0 => 'Day of the Month',
        1 => 'Sunday of the Month',
        2 => 'Monday of the Month',
        3 => 'Tuesday of the Month',
        4 => 'Wednesday of the Month',
        5 => 'Thursday of the Month',
        6 => 'Friday of the Month',
        7 => 'Saturday of the Month',
    );

    public $week_days = array(
        ''=> '--Select Week Day--',
        1 => 'Every Sunday of the Week',
        2 => 'Every Monday of the Week',
        3 => 'Every Tuesday of the Week',
        4 => 'Every Wednesday of the Week',
        5 => 'Every Thursday of the Week',
        6 => 'Every Friday of the Week',
        7 => 'Every Saturday of the Week',
    );

    public $twice_every_one_month = array(
         '--Select Start Dates --',
         1 => 'After 1st and after 15th',
         2 => 'After 10th and after 20th',
     );

    public $every_two_week_days = array(
        ''=> '--Select Week Day--',
        1 => 'Every Sunday',
        2 => 'Every Monday',
        3 => 'Every Tuesday',
        4 => 'Every Wednesday',
        5 => 'Every Thursday',
        6 => 'Every Friday',
        7 => 'Every Saturday',
    );

    public $week_numbers = array(
        ''=> '--Select Week Number--',
        1 => 'of the First Week',
        2 => 'of the Second Week',
    );

    public $months = array(
        1 => 'January',
        2 => 'February',
        3 => 'March',
        4 => 'April',
        5 => 'May',
        6 => 'June',
        7 => 'July',
        8 => 'August',
        9 => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December',
    );

    public $starting_days = array(
        ''=>'--Select Day of the Month--',
        '1'=>'After  1st',
        '2'=>'After  2nd',
        '3'=>'After  3rd',
        '4'=>'After  4th',
        '5'=>'After  5th',
        '6'=>'After  6th',
        '7'=>'After  7th',
        '8'=>'After  8th',
        '9'=>'After  9th',
        '10'=>'After  10th',
        '11'=>'After  11th',
        '12'=>'After  12th',
        '13'=>'After  13th',
        '14'=>'After  14th',
        '15'=>'After  15th',
        '16'=>'After  16th',
        '17'=>'After  17th',
        '18'=>'After  18th',
        '19'=>'After  19th',
        '20'=>'After  20th',
        '21'=>'After  21st',
        '22'=>'After  22nd',
        '23'=>'After  23rd',
        '24'=>'After  24th',
        '25'=>'After  25th',
        '26'=>'After  26th',
        '27'=>'After  27th',
        '28'=>'After  28th',
        '29'=>'After  29th',
        '30'=>'After  30th',
        );

    
    public $starting_months = array(
        1 => 'starting in January',
        2 => 'starting in February',
        3 => 'starting in March',
        4 => 'starting in April',
        5 => 'starting in May',
        6 => 'starting in June',
        7 => 'starting in July',
        8 => 'starting in August',
        9 => 'starting in September',
        10 => 'starting in October',
        11 => 'starting in November',
        12 => 'starting in December',
    );

    public $invoice_days = array(
        1 => '1 day before Contribution date',
        2 => '2 days before Contribution date',
        3 => '3 days before Contribution date',
        4 => '4 days before Contribution date',
        5 => '5 days before Contribution date',
        6 => '6 days before Contribution date',
        7 => '1 week before Contribution date',
        8 => '8 days before Contribution date',
        9 => '9 days before Contribution date',
        10 => '10 days before Contribution date',
        11 => '11 days before Contribution date',
        12 => '12 days before Contribution date',
        13 => '13 days before Contribution date',
        14 => '2 Weeks before Contribution date',
    );

    public $fine_types = array(
        1=>'Fixed Amount Fine of',
        2=>'Percentage Rate Fine of',
    );

    public $fine_chargeable_on_options = array(
        'Frequently used options' => array(
            1=>'chargeable 1 day after contribution date',
            7=>'chargeable 1 week after contribution date',
            14=>'chargeable 2 weeks after contribution date',
            'last_day_of_the_month'=>'chargeable on the last day of the month',
            'first_day_of_the_month'=>'chargeable on the first day of the month',
        ),
        'Other options' => array(
            2=>'chargeable 2 days after contribution date',
            3=>'chargeable 3 days after contribution date',
            4=>'chargeable 4 days after contribution date',
            5=>'chargeable 5 days after contribution date',
            6=>'chargeable 6 days after contribution date',
            8=>'chargeable 8 days after contribution date',
            9=>'chargeable 9 days after contribution date',
            10=>'chargeable 10 days after contribution date',
            11=>'chargeable 11 days after contribution date',
            12=>'chargeable 12 days after contribution date',
            13=>'chargeable 13 days after contribution date',
            15=>'chargeable 15 days after contribution date',
            16=>'chargeable 16 days after contribution date',
            17=>'chargeable 17 days after contribution date',
            18=>'chargeable 18 days after contribution date',
            19=>'chargeable 19 days after contribution date',
            20=>'chargeable 20 days after contribution date',
            21=>'chargeable 3 weeks after contribution date',
            22=>'chargeable 22 days after contribution date',
            23=>'chargeable 23 days after contribution date',
            24=>'chargeable 24 days after contribution date',
            25=>'chargeable 25 days after contribution date',
            26=>'chargeable 26 days after contribution date',
            27=>'chargeable 27 days after contribution date',
            28=>'chargeable 4 weeks after contribution date',
            29=>'chargeable 29 days after contribution date',
            30=>'chargeable 30 days after contribution date',
            31=>'chargeable 31 days after contribution date',
            32=>'chargeable 32 days after contribution date',
            33=>'chargeable 33 days after contribution date',
            34=>'chargeable 34 days after contribution date',
            35=>'chargeable 5 weeks after contribution date',
            36=>'chargeable 36 days after contribution date',
            37=>'chargeable 37 days after contribution date',
            38=>'chargeable 38 days after contribution date',
            39=>'chargeable 39 days after contribution date',
            40=>'chargeable 40 days after contribution date',
            41=>'chargeable 41 days after contribution date',
            42=>'chargeable 6 weeks after contribution date',
            43=>'chargeable 43 days after contribution date',
            44=>'chargeable 44 days after contribution date',
            45=>'chargeable 45 days after contribution date',
            46=>'chargeable 46 days after contribution date',
            47=>'chargeable 47 days after contribution date',
            48=>'chargeable 48 days after contribution date',
            49=>'chargeable 7 weeks after contribution date',
            50=>'chargeable 50 days after contribution date',
            51=>'chargeable 51 days after contribution date',
            52=>'chargeable 52 days after contribution date',
            53=>'chargeable 53 days after contribution date',
            54=>'chargeable 54 days after contribution date',
            55=>'chargeable 55 days after contribution date',
            56=>'chargeable 8 weeks after contribution date',
        )
    );

    public $fine_frequency_options = array(
        0=>'one time only',
        1=>'per day',
        2=>'per week',
        3=>'per month',
        4=>'per quarter',
        5=>'per half year',
        6=>'per year',
    );

    public $fine_mode_options = array(
        1=>'for each unpaid contribution',
        2=>'for outstanding balance',
    );

    public $fine_limit_options = array(
        0=>'do not limit fines generated per unpaid contribution',
        1=>'limit to 1 fine per unpaid contribution',
        2=>'limit to 2 fines per unpaid contribution',
        3=>'limit to 3 fines per unpaid contribution',
        4=>'limit to 4 fines per unpaid contribution',
        5=>'limit to 5 fines per unpaid contribution',
        6=>'limit to 6 fines per unpaid contribution',
        7=>'limit to 7 fines per unpaid contribution',
        8=>'limit to 8 fines per unpaid contribution',
        9=>'limit to 9 fines per unpaid contribution',
        10=>'limit to 10 fines per unpaid contribution',
        11=>'limit to 11 fines per unpaid contribution',
        12=>'limit to 12 fines per unpaid contribution',
        13=>'limit to 13 fines per unpaid contribution',
        14=>'limit to 14 fines per unpaid contribution',
        15=>'limit to 15 fines per unpaid contribution',
        16=>'limit to 16 fines per unpaid contribution',
        17=>'limit to 17 fines per unpaid contribution',
        18=>'limit to 18 fines per unpaid contribution',
        19=>'limit to 19 fines per unpaid contribution',
        20=>'limit to 20 fines per unpaid contribution',
    );

    public $percentage_fine_on_options = array(
        1 => 'on contribution amount',
        2 => 'on contribution balance',
        3 => 'on contribution balance & contribution fines',
    );

	public function __construct(){
		$this->ci= & get_instance();
        $this->ci->load->library('ion_auth');
        $this->ci->load->library('member_notifications');
		$this->ci->load->library('transactions');
        $this->ci->load->model('contributions/contributions_m');
        $this->ci->load->model('invoices/invoices_m');
        $this->ci->load->model('statements/statements_m');
        $this->ci->load->model('members/members_m');
        $this->ci->load->model('sms/sms_m');
        $this->ci->load->model('emails/emails_m');
        $this->ci->load->model('statements/statements_m');

	}

    public function queue_regular_contribution_invoices($date = 0){
        //select todays group contributions 
        //$date = $date?strtotime($date):time();
        $date = $date?:time();
        $contributions = $this->ci->contributions_m->get_regular_contributions_to_be_invoiced_today($date); 
        $successful_entries = 0;
        $successful_next_invoice_date_updates = 0;
        $unsuccessful_entries = 0;
        $unsuccessful_next_invoice_date_updates = 0;        
        foreach($contributions as $contribution){
            $active_members = $this->ci->members_m->get_active_group_member_options($contribution->group_id);
            $member_ids = array();
            if($contribution->enable_contribution_member_list){
                $member_ids = $this->ci->contributions_m->get_contribution_member_pairings_array($contribution->id,$contribution->group_id);
            }else{
                $member_ids = $this->ci->members_m->get_group_member_ids($contribution->group_id);
            }
            if($member_ids){
                foreach($member_ids as $member_id){
                    if(array_key_exists($member_id, $active_members)){
                        $input = array(
                            'contribution_id'=>$contribution->id,
                            'member_id'=>$member_id,
                            'group_id'=>$contribution->group_id,
                            'invoice_date'=>$contribution->invoice_date,
                            'due_date'=>$contribution->contribution_date,
                            'amount_payable'=>$contribution->amount,
                            'description'=>$contribution->name,
                            'created_on'=>time(),
                        );
                        if($this->ci->invoices_m->insert_contribution_invoicing_queue($input)){
                            $successful_entries++;
                        }else{
                            $unsuccessful_entries++;
                        }
                    }
                }
            }


            if($contribution->contribution_frequency==6){
                $day_multiplier = 8;
            }else{
                $day_multiplier = 1;
            }


            //set next invoice date
            $contribution_date = $this->get_regular_contribution_contribution_date(
                $contribution->contribution_frequency,
                $contribution->month_day_monthly,
                $contribution->week_day_monthly,
                $contribution->week_day_weekly,
                $contribution->week_day_fortnight,
                $contribution->week_number_fortnight,
                $contribution->month_day_multiple,
                $contribution->week_day_multiple,
                $contribution->start_month_multiple,
                $contribution->after_first_contribution_day_option,
                $contribution->after_first_day_week_multiple,
                $contribution->after_first_starting_day,
                $contribution->after_second_contribution_day_option,
                $contribution->after_second_day_week_multiple,
                $contribution->after_second_starting_day,
                ($contribution->contribution_date+(24*60*60*$day_multiplier))
            );
            $invoice_date = $contribution_date - (24*60*60*$contribution->invoice_days);
            $input = array(
                'invoice_date'=>$invoice_date,
                'last_invoice_date'=>$contribution->invoice_date,
                'contribution_date'=>$contribution_date,
                'modified_on'=>time(),
            );
            if($this->ci->contributions_m->update_regular_contribution_setting($contribution->regular_contribution_setting_id,$input)){
                $successful_next_invoice_date_updates++;
            }else{
                $unsuccessful_next_invoice_date_updates++;
            }
        }
        
        if($successful_entries){
            echo  $successful_entries.' invoices queued.<br/> ';
        }
        if($unsuccessful_entries){
            echo  $unsuccessful_entries.' invoices could not be queued.<br/> ';
        }
        if($successful_next_invoice_date_updates){
            echo  $successful_next_invoice_date_updates.' invoice dates updated.<br/> ';
        }
        if($unsuccessful_next_invoice_date_updates){
            echo  $unsuccessful_next_invoice_date_updates.' invoice dates could not be updated.<br/> ';
        }
    }

    public function queue_one_time_contribution_invoices($date = 0){
        //select todays group contributions 
        $date = $date?:time();
        //$date = $date?strtotime($date):time();
        $contributions = $this->ci->contributions_m->get_one_time_contributions_to_be_invoiced_today($date);
        $successful_entries = 0;
        $unsuccessful_entries = 0;  
        $successful_last_invoice_date_updates = 0;
        $unsuccessful_last_invoice_date_updates = 0;

        foreach($contributions as $contribution){
            $member_ids = array();
            if($contribution->enable_contribution_member_list){
                $member_ids = $this->ci->contributions_m->get_contribution_member_pairings_array($contribution->id,$contribution->group_id);
            }else{
                $member_ids = $this->ci->members_m->get_group_member_ids($contribution->group_id);
            }
            if($member_ids){
                foreach($member_ids as $member_id){
                    $input = array(
                        'contribution_id'=>$contribution->id,
                        'member_id'=>$member_id,
                        'group_id'=>$contribution->group_id,
                        'invoice_date'=>$contribution->invoice_date,
                        'due_date'=>$contribution->contribution_date,
                        'amount_payable'=>$contribution->amount,
                        'description'=>$contribution->name,
                        'created_on'=>time(),
                    );
                    if($this->ci->invoices_m->insert_contribution_invoicing_queue($input)){
                        $successful_entries++;
                    }else{
                        $unsuccessful_entries++;
                    }
                }
            }
            //set next invoice date
            $input = array(
                'invoices_queued'=>1,
                'last_invoice_date'=>$contribution->invoice_date,
                'modified_on'=>time(),
            );
            if($this->ci->contributions_m->update_one_time_contribution_setting($contribution->one_time_contribution_setting_id,$input)){
                $successful_last_invoice_date_updates++;
            }else{
                $unsuccessful_last_invoice_date_updates++;
            }
        }
        if($successful_entries){
            echo  $successful_entries.' invoices queued.<br/> ';
        }
        if($unsuccessful_entries){
            echo  $unsuccessful_entries.' invoices could not be queued.<br/> ';
        }
        if($successful_last_invoice_date_updates){
            echo  $successful_last_invoice_date_updates.' last invoice dates updated.<br/> ';
        }
        if($unsuccessful_last_invoice_date_updates){
            echo  $unsuccessful_last_invoice_date_updates.' last invoice dates could not be updated.<br/> ';
        }
    }

    public function process_contribution_invoices_queue($limit = 0){
        //die;
        $information_missing_errors_count = 0;
        $invoice_insert_errors_count = 0;
        $statement_insert_errors_count = 0;
        $successful_statement_entries_count = 0;
        $smses_queued_count = 0;
        $emails_queued_count = 0;
        $sms_queue_error_count = 0; 
        $email_queue_error_count = 0;
        $invoice_entry_ignore_count = 0;
        $delete_contribution_invoice_queue_count = 0;
        $delete_contribution_invoice_queue_error_count = 0;
        $queued_contribution_invoices = $this->ci->invoices_m->get_queued_contribution_invoices($limit);
        $group_ids = array();
        $member_ids = array();
        $contribution_ids = array();
        $contribution_objects_array = array();
        $contribution_settings_array = array();
        $member_objects_array = array();
        $contribution_settings_objects_array = array();
        $invoice_dates_array = array();
        $earliest_invoice_date = time();
        foreach($queued_contribution_invoices as $queued_contribution_invoice):
            if(in_array($queued_contribution_invoice->invoice_date,$invoice_dates_array)){

            }else{
                $invoice_dates_array[] = $queued_contribution_invoice->invoice_date;
            }

            if(in_array($queued_contribution_invoice->group_id,$group_ids)){

            }else{
                $group_ids[] = $queued_contribution_invoice->group_id;
            }

            if($earliest_invoice_date > $queued_contribution_invoice->invoice_date){
                $earliest_invoice_date = $queued_contribution_invoice->invoice_date;
            }
        endforeach;
        $invoices_sent_today_array = $this->ci->invoices_m->get_contribution_invoices_sent_array($invoice_dates_array,$group_ids);

        // print_r($invoices_sent_today_array);
        // die;

        foreach($queued_contribution_invoices as $queued_contribution_invoice){
            if($this->ci->invoices_m->delete_contribution_invoice_queue($queued_contribution_invoice->id)){
            //if(TRUE){
                $delete_contribution_invoice_queue_count++;
            }else{
                $delete_contribution_invoice_queue_error_count++;
            }
            $contribution = $this->ci->contributions_m->get_group_contribution($queued_contribution_invoice->contribution_id,$queued_contribution_invoice->group_id);
            $contribution_setting = array();
            if($contribution->type==1){
                $contribution_setting = $this->ci->contributions_m->get_group_regular_contribution_setting($queued_contribution_invoice->contribution_id,$queued_contribution_invoice->group_id);
            }else if($contribution->type==2){
                $contribution_setting = $this->ci->contributions_m->get_group_one_time_contribution_setting($queued_contribution_invoice->contribution_id,$queued_contribution_invoice->group_id);
            }
            $member = $this->ci->members_m->get_group_member($queued_contribution_invoice->member_id,$queued_contribution_invoice->group_id);
            if($member&&$contribution&&$contribution_setting){
                if(isset($invoices_sent_today_array[$member->id][$queued_contribution_invoice->contribution_id][$queued_contribution_invoice->group_id][$queued_contribution_invoice->invoice_date])){ 
                    $invoice_entry_ignore_count++;
                }else{
                   if($this->ci->transactions->create_invoice(1,
                    $queued_contribution_invoice->group_id,
                    $member,
                    $contribution,
                    $queued_contribution_invoice->invoice_date,
                    $queued_contribution_invoice->due_date,
                    $queued_contribution_invoice->amount_payable,
                    $queued_contribution_invoice->description,
                    $contribution_setting->sms_template,
                    $contribution_setting->sms_notifications_enabled,
                    $contribution_setting->email_notifications_enabled
                    )
                    ){
                        if(in_array($queued_contribution_invoice->group_id,$group_ids)){

                        }else{
                            $group_ids[] = $queued_contribution_invoice->group_id;
                        }
                        if(in_array($queued_contribution_invoice->member_id,$member_ids)){

                        }else{
                            $member_ids[] = $queued_contribution_invoice->member_id;
                            $member_objects_array[$queued_contribution_invoice->member_id] = $member;
                        }
                        if(in_array($queued_contribution_invoice->contribution_id,$contribution_ids)){

                        }else{
                            $contribution_ids[] = $queued_contribution_invoice->contribution_id;
                            $contribution_objects_array[$queued_contribution_invoice->contribution_id] = $contribution;
                            $contribution_settings_objects_array[$queued_contribution_invoice->contribution_id] = $contribution_setting;
                        }
                        $invoices_sent_today_array[$member->user_id][$member->id][$queued_contribution_invoice->contribution_id][$queued_contribution_invoice->group_id][$queued_contribution_invoice->invoice_date] = 1;
                        $successful_statement_entries_count++;
                   }else{
                        $statement_insert_errors_count++;
                   }
                }
            }else{
                $information_missing_errors_count++;
            }
        }
        if($this->ci->transactions->update_group_member_contribution_statement_balances($group_ids,$member_ids,$earliest_invoice_date)){
            echo "Contribution Statement Balances Updated successfully.<br/>";
        }
        if($this->ci->transactions->send_invoice_notifications($queued_contribution_invoices,$group_ids,$member_ids,$contribution_ids,$member_objects_array,$contribution_objects_array,$contribution_settings_objects_array)){
            echo "Notifications sent successfully<br/>";
        }
        if($information_missing_errors_count){
            echo  $information_missing_errors_count.' information missing .<br/> ';
        }
        if($invoice_insert_errors_count){
            echo  $invoice_insert_errors_count.' invoices could not be inserted .<br/> ';
        }
        if($statement_insert_errors_count){
            echo  $statement_insert_errors_count.' statements could not be inserted .<br/> ';
        }
        if($successful_statement_entries_count){
            echo  $successful_statement_entries_count.' statement entries successfully made .<br/> ';
        }
        if($smses_queued_count){
            echo  $smses_queued_count.' smses queued .<br/> ';
        }
        if($emails_queued_count){
            echo  $emails_queued_count.' emails queued .<br/> ';
        }
        if($email_queue_error_count){
            echo  $email_queue_error_count.' emails could not be queued .<br/> ';
        }
        if($sms_queue_error_count){
            echo  $sms_queue_error_count.' smses could not be queued .<br/> ';
        }
        if($invoice_entry_ignore_count){
            echo  $invoice_entry_ignore_count.' invoice entry ignored as invoice already sent.<br/> ';
        }
        if($delete_contribution_invoice_queue_count){
            echo  $delete_contribution_invoice_queue_count.' contributions removed from the queue .<br/> ';
        }
        if($delete_contribution_invoice_queue_error_count){
            echo  $delete_contribution_invoice_queue_error_count.' contributions could not be removed from the queue.<br/> ';
        }
    }

	public function get_regular_contribution_contribution_date($contribution_frequency = 0,$month_day_monthly = 0,$week_day_monthly = 0,$week_day_weekly = 0,$week_day_fortnight = 0,$week_number_fortnight = 0,$month_day_multiple = 0,$week_day_multiple = 0,$starting_month_multiple = 0,$after_first_contribution_days_option=0,$after_first_day_week_multiple=0,$after_first_starting_day=0,$after_second_contribution_day_option=0,$after_second_day_week_multiple=0,$after_second_starting_day=0,$date=0 ){
		$manual = FALSE;
        if($date){
			//do nothing for now
            $manual = TRUE;
		}else{
			$date = time();
        }
		
		$id = $contribution_frequency;
        //echo $id .'Frequency <br>'. date('Y-m-d',$date) .'date parameter<br>'; 
        $next_date = 0;
        $this_date = mktime(0, 0, 0, date('n',$date), date('j',$date), date('Y',$date));
        $weekdays = array('Day',
            'Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
        );

        $months = array('None',
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
        );
        
            if ($id == 1){ //monthly
                $md = $month_day_monthly;
                $wd = $week_day_monthly;
                if ($md > 0 && $md < 5 && $wd){
                    $next_date = mktime(0, 0, 0, date('n',$date), 0, date('Y',$date));
                    $md2 = $md;
                    while ($md2 > 0)
                    {
                            $dt = "Next " . $weekdays[$wd];
                            $next_date = strtotime($dt, $next_date);
                            $md2 --;
                    }
                    if ($this_date > $next_date)
                    {
                            $next_date = mktime(0, 0, 0, (date('n',$date) + 1), 0, date('Y',$date));
                            $md2 = $md;
                            while ($md2 > 0)
                            {
                                    $dt = "Next " . $weekdays[$wd];
                                    $next_date = strtotime($dt, $next_date);
                                    $md2 --;
                            }
                              
                    }
                   
                }elseif ($md==32) {
                    $day_string = isset($this->month_days[$wd])?$this->month_days[$wd]:''; 
                       $next_date = mktime(0, 0, 0, date('n',$date), 0, date('Y',$date));                
                    if($day_string){
                        $day_strings = explode(' ', $day_string);

                        if(isset($day_strings[0])){  
                              $date = $date?:time();
                              $now = date('Y-m-d');
                              $the_next_month = mktime(0, 0, 0, (date('n',$date) + 1), 0, date('Y',$date));
                              $after_one_month = strtotime(" +1 months", $this_date);
                              $this_month = date('M Y',$the_next_month);
                              $new_month = date('M Y',$after_one_month);
                            if( date('Y-m-d',$this_date) > $now) {

                                $next_date = strtotime('last '.$day_strings[0].' of '.$new_month);
                            }else{
                                 $next_date = strtotime('last '.$day_strings[0].' of '.$this_month);
                            }
                                                      
                        }else{
                           $current_day = date("D");
                           $next_date = strtotime('last '.$current_day.' of '.$this_month); 
                        } 
                    }else{
                        return FALSE;
                    }
                }else
                {
                        $day = date('j',$date);
                        if ($md >= $day)
                        {
                                $next_date = mktime(0, 0, 0, date('n',$date), $md, date('Y',$date));
                        }
                        else
                        {
                            $md = (int)$md;
                            //die;
                                $next_date = mktime(0, 0, 0, (date('n',$date) + 1), $md, date('Y',$date));
                        }
                }
            }else if ($id == 2){ //2 monthly
                $md = $month_day_multiple;
                $wd = $week_day_multiple;
                $mn = '';
                $sm = $starting_month_multiple;
                if ($md > 0 && $md < 5 && $wd)
                {
                        $next_date = mktime(0, 0, 0, $sm, 0, date('Y',$date));
                        $md2 = $md;
                        while ($md2 > 0)
                        {
                                $dt = "Next " . $weekdays[$wd];
                                $next_date = strtotime($dt, $next_date);
                                $md2 --;
                        }
                        if ($this_date > $next_date)
                        {
                                $sm2 = $sm;
                                while ($this_date > $next_date)
                                {

                                        $sm2+=2;
                                        $next_date = mktime(0, 0, 0, $sm2, 0, date('Y',$date));
                                        $md2 = $md;
                                        while ($md2 > 0)
                                        {
                                                $dt = "Next " . $weekdays[$wd];
                                                $next_date = strtotime($dt, $next_date);
                                                $md2 --;
                                        }
                                }
                        }
                        $new_date = strtotime(" +2 months", $this_date);
                        if ($next_date > $new_date)
                        {
                                $sm2 = $sm;
                                while ($next_date > $new_date)
                                {

                                        $sm2-=2;
                                        $next_date = mktime(0, 0, 0, $sm2, 0, date('Y',$date));
                                        $md2 = $md;
                                        while ($md2 > 0)
                                        {
                                                $dt = "Next " . $weekdays[$wd];
                                                $next_date2 = strtotime($dt, $next_date);
                                                $md2 --;
                                        }
                                        if ($next_date2 < $this_date)
                                                break;
                                               $next_date = $next_date2;
                                }
                        }
                }elseif ($md==32) {
                    $this_month = date('M Y');                                       
                    $day_string = isset($this->month_days[$wd])?$this->month_days[$wd]:''; 
                    if($day_string){
                        $day_strings = explode(' ', $day_string);
                        $month = $this->months[$sm];                        
                        $now = date('Y-m-d');
                             $x = $sm;
                             $after_two_months = strtotime(" +2 months", $this_date);
                             $new_month = date('Y-m-d',$after_two_months);
                             $the_next_month = mktime(0, 0, 0, (date('n',$date) + 1), 0, date('Y',$date));
                             while($x <= 12) {
                              $month = $this->months[$x]; 
                              if(date('Y-m-d',strtotime('last '.$day_strings[0].' of '.$month)) > $now){
                                 //date is in future
                                 $next_date = strtotime('last '.$day_strings[0].' of '.$month);
                                 //break;
                                 if(date('Y-m-d',$this_date) > $now){
                                    $next_date = strtotime('last '.$day_strings[0].' of '.$new_month);
                                    break;
                                 }else{
                                   $next_date = strtotime('last '.$day_strings[0].' of '.$month);
                                    break; 
                                 } 
                                }else{                                    
                                    //month is in past
                                    //echo $month.' month is past <br>';
                               }
                              $x+=2;
                            } 
                             //echo $sm .' start<br>' .$x .' end<br>';
                    }else{
                        $current_day = date('D');
                        $next_date = strtotime('last '.$current_day.' of '.$this_month);
                    }
                    /*echo $this_month.' next date <br>';
                    echo date('Y-m-d',$the_next_month) .' last day of this month(month from params)<br>';
                    echo date('Y-m-d',$this_date).' date this<br>'; 
                    echo date('Y-m-d',$next_date) .' legit next  contribution <br>'; die(); */
                }
                else
                {
                        $next_date = mktime(0, 0, 0, $sm, $md, date('Y',$date));
                        if ($this_date > $next_date)
                        {
                                $sm2 = $sm;
                                while ($this_date > $next_date)
                                {
                                        $sm2+=2;
                                        $next_date = mktime(0, 0, 0, $sm2, $md, date('Y',$date));
                                }
                        }
                        $new_date = strtotime(" +2 months", $this_date);
                        if ($next_date > $new_date)
                        {
                                $sm2 = $sm;
                                while ($next_date > $new_date)
                                {

                                        $sm2-=2;
                                        $next_date2 = mktime(0, 0, 0, $sm2, 0, date('Y',$date));
                                        if ($next_date2 < $this_date)
                                                break;

                                        $next_date = $next_date2;
                                }
                        }
                }
        }else if ($id == 3){ //3 monthly
                $md = $month_day_multiple;
                $wd = $week_day_multiple;
                $mn = '';
                $sm = $starting_month_multiple;
                
                if ($md > 0 && $md < 5 && $wd)
                {
                        $next_date = mktime(0, 0, 0, $sm, 0, date('Y',$date));
                        $md2 = $md;
                        while ($md2 > 0)
                        {
                                $dt = "Next " . $weekdays[$wd];
                                $next_date = strtotime($dt, $next_date);
                                $md2 --;
                        }

                        if ($this_date > $next_date)
                        {
                                $sm2 = $sm;
                                while ($this_date > $next_date)
                                {

                                        $sm2+=3;
                                        $next_date = mktime(0, 0, 0, $sm2, 0, date('Y',$date));
                                        $md2 = $md;
                                        while ($md2 > 0)
                                        {
                                                $dt = "Next " . $weekdays[$wd];
                                                $next_date = strtotime($dt, $next_date);
                                                $md2 --;
                                        }
                                }
                                //echo date('l, F jS, Y',$next_date);
                        }

                        $new_date = strtotime(" +3 months", $this_date);
                        if ($next_date > $new_date)
                        {
                                $sm2 = $sm;
                                while ($next_date > $new_date)
                                {

                                        $sm2-=3;
                                        $next_date = mktime(0, 0, 0, $sm2, 0, date('Y',$date));
                                        $md2 = $md;
                                        while ($md2 > 0)
                                        {
                                                $dt = "Next " . $weekdays[$wd];
                                                $next_date2 = strtotime($dt, $next_date);
                                                $md2 --;
                                        }
                                        if ($next_date2 < $this_date)
                                                break;

                                        $next_date = $next_date2;
                                }
                        }
                }elseif ($md==32) {
                    $this_month = date('M Y');                                       
                    $day_string = isset($this->month_days[$wd])?$this->month_days[$wd]:''; 
                    if($day_string){
                        $day_strings = explode(' ', $day_string);
                        $month = $this->months[$sm];                        
                        $now = date('Y-m-d');
                            $x = $sm;
                            $after_three_months = strtotime(" +3 months", $this_date);
                            $new_month = date('Y-m-d',$after_three_months);
                            $the_next_month = mktime(0, 0, 0, (date('n',$date) + 1), 0, date('Y',$date)); 
                            while($x <= 12) {
                              $month = $this->months[$x]; 
                              if(date('Y-m-d',strtotime('last '.$day_strings[0].' of '.$month)) > $now){
                                //date is in future
                                    $next_date = strtotime('last '.$day_strings[0].' of '.$month);
                                if(date('Y-m-d',$this_date) > $now){
                                   $next_date = strtotime('last '.$day_strings[0].' of '.$new_month);
                                    break;
                                 }else{
                                   $next_date = strtotime('last '.$day_strings[0].' of '.$month);
                                    break; 
                                 }                  
                                
                                }else{
                                  // month is in past
                                    //print_r($month.'month is past'); 
                                }
                              $x+=3;
                            }  
                    }else{
                        $current_day = date('D');
                        $next_date = strtotime('last '.$current_day.' of '.$this_month);
                    }

            }
            }else if ($id == 4){ //6 monthly
            $md = $month_day_multiple;
            $wd = $week_day_multiple;
            $mn = '';
            $sm = $starting_month_multiple;
            if ($md > 0 && $md < 5 && $wd)
            {
                    $next_date = mktime(0, 0, 0, $sm, 0, date('Y',$date));
                    $md2 = $md;
                    while ($md2 > 0)
                    {
                            $dt = "Next " . $weekdays[$wd];
                            $next_date = strtotime($dt, $next_date);
                            $md2 --;
                    }
                    if ($this_date > $next_date)
                    {
                            $sm2 = $sm;
                            while ($this_date > $next_date)
                            {

                                    $sm2+=6;
                                    $next_date = mktime(0, 0, 0, $sm2, 0, date('Y',$date));
                                    $md2 = $md;
                                    while ($md2 > 0)
                                    {
                                            $dt = "Next " . $weekdays[$wd];
                                            $next_date = strtotime($dt, $next_date);
                                            $md2 --;
                                    }
                            }
                    }
                    $new_date = strtotime(" +6 months", $this_date);
                    if ($next_date > $new_date)
                    {
                            $sm2 = $sm;
                            while ($next_date > $new_date)
                            {

                                    $sm2-=6;
                                    $next_date = mktime(0, 0, 0, $sm2, 0, date('Y',$date));
                                    $md2 = $md;
                                    while ($md2 > 0)
                                    {
                                            $dt = "Next " . $weekdays[$wd];
                                            $next_date2 = strtotime($dt, $next_date);
                                            $md2 --;
                                    }
                                    if ($next_date2 < $this_date)
                                            break;

                                    $next_date = $next_date2;
                            }
                    }
            }elseif ($md==32) {

                    $this_month = date('M Y');                                       
                    $day_string = isset($this->month_days[$wd])?$this->month_days[$wd]:''; 
                    if($day_string){
                        $day_strings = explode(' ', $day_string);
                        $month = $this->months[$sm];                        
                        $now = date('Y-m-d');
                            $x = $sm;
                            $after_six_months = strtotime(" +6 months", $this_date);
                            $new_month = date('Y-m-d',$after_six_months);
                            $the_next_month = mktime(0, 0, 0, (date('n',$date) + 1), 0, date('Y',$date)); 
                            while($x <= 12) {
                              $month = $this->months[$x]; 
                              if(date('Y-m-d',strtotime('last '.$day_strings[0].' of '.$month)) > $now){
                                //date is in future
                                    $next_date = strtotime('last '.$day_strings[0].' of '.$month);
                                if(date('Y-m-d',$this_date) > $now){
                                   $next_date = strtotime('last '.$day_strings[0].' of '.$new_month);
                                    break;
                                 }else{
                                   $next_date = strtotime('last '.$day_strings[0].' of '.$month);
                                    break; 
                                 }                  
                                
                                }else{
                                  // month is in past
                                    //print_r($month.'month is past'); 
                                }
                              $x+=6;
                            }  
                    }else{
                        $current_day = date('D');
                        $next_date = strtotime('last '.$current_day.' of '.$this_month);
                    }
                }
            else
            {
                    $next_date = mktime(0, 0, 0, $sm, $md, date('Y',$date));
                    if ($this_date > $next_date)
                    {
                            $sm2 = $sm;
                            while ($this_date > $next_date)
                            {
                                    $sm2+=6;
                                    $next_date = mktime(0, 0, 0, $sm2, $md, date('Y',$date));
                            }
                    }
                    $new_date = strtotime(" +6 months", $this_date);
                    if ($next_date > $new_date)
                    {
                            $sm2 = $sm;
                            while ($next_date > $new_date)
                            {

                                    $sm2-=6;
                                    $next_date2 = mktime(0, 0, 0, $sm2, 0, date('Y',$date));
                                    if ($next_date2 < $this_date)
                                            break;

                                    $next_date = $next_date2;
                            }
                    }
            }
        }else if ($id == 5){ //annually
            $md = $month_day_multiple;
            $wd = $week_day_multiple;
            $sm = $starting_month_multiple;
            if ($md > 0 && $md < 5 && $wd)
            {
                    $next_date = mktime(0, 0, 0, $sm, 0, date('Y',$date));
                    //echo date('l, F jS, Y',$next_date).'  <br />';
                    $md2 = $md;
                    while ($md2 > 0)
                    {
                            $dt = "Next " . $weekdays[$wd];
                            $next_date = strtotime($dt, $next_date);
                            $md2 --;
                            //echo date('l, F jS, Y',$next_date).' '.$md2.'  <br />';
                    }

                    //echo date('l, F jS, Y',$next_date).'  huinui gnkjkhjk'; die;
                    if ($this_date > $next_date)
                    {
                            $next_date = mktime(0, 0, 0, $sm, 0, (date('Y',$date) + 1));
                            $md2 = $md;
                            while ($md2 > 0)
                            {
                                    $dt = "Next " . $weekdays[$wd];
                                    $next_date = strtotime($dt, $next_date);
                                    $md2 --;
                            }
                    }
            }
            else
            {
                    $month = date('n');
                    $day = date('j');
                    if ($sm >= $month && $md >= $day)
                    {
                            $next_date = mktime(0, 0, 0, $sm, $md, date('Y',$date));
                    }
                    else
                    {
                            $next_date = mktime(0, 0, 0, $sm, $md, (date('Y',$date) + 1));
                    }
            }
        }else if ($id == 6){ //weekly
            $wd = $week_day_weekly;
            /**
            if($manual){
                $gendate = new DateTime();
                $gendate->setISODate(date('Y',$date),date('W',$date),$wd-1); //year , week num , day
                $next_date = strtotime($gendate->format('d-m-Y'));
            }else{
            **/
                if ($wd && $weekdays[$wd] != date('l',$date))
                {
                        $dt = "Next " . $weekdays[$wd];
                        $next_date = strtotime($dt);
                }
                else
                {
                        $next_date = $this_date;
                }

            /**}**/
        }else if ($id == 7){ //fortnightly
            $wd = $week_day_fortnight;
            $wn = $week_number_fortnight;
            if ($wd && $weekdays[$wd] != date('l',$date))
            {
                    $dt = "Next " . $weekdays[$wd];
                    $next_date = strtotime($dt);
            }
            else
            {
                    $next_date = $this_date;
            }
            if ($wn == 2)
            {
                    $next_date = strtotime(" +1 week", $next_date);
            }
        }else if ($id == 8){ //daily
            $next_date = strtotime("+2days");
        }else if($id == 9){
            $fcd = $after_first_contribution_days_option?$after_first_contribution_days_option:0;
            $fdw = $after_first_day_week_multiple?$after_first_day_week_multiple:1;
            $fsd = $after_first_starting_day?$after_first_starting_day:1;
            $scd = $after_second_contribution_day_option?$after_second_contribution_day_option:0;
            $sdw = $after_second_day_week_multiple?$after_second_day_week_multiple:0;
            $ssd = $after_second_starting_day?$after_second_starting_day:0;
            $first_day = $this->month_days[$fdw];
            $after_date = $this->starting_days[$fsd];
            //+ days after 2nd
            $lastday = date('Y-m-d', strtotime('last day of this month'));
            $lastday_next = strtotime('last day of next month');
            $get_month_form_this_date = date('m',$this_date);
            $get_year_from_this_date = date('Y',$this_date);
            
            $ssd_test = array();
            $ssd_test[] = $ssd;
            $second_day_from_this =  mktime(0,0,0,$get_month_form_this_date,$ssd,$get_year_from_this_date);
            $first_day_from_this = mktime(0,0,0,$get_month_form_this_date,$fsd,$get_year_from_this_date);
           
            $start_first_date  = date('Y-m-d',$first_day_from_this); 
            $second_start_date = date('Y-m-d',$second_day_from_this);
            $second_contribution_day = array();
            $month  = date('m');
            $year  = date('Y');
            $lastDateOfMonth = date("Y-m-t",$first_day_from_this);   
             

            $days = cal_days_in_month(CAL_GREGORIAN, $month,$year); // get no of days
            $get_first_week_day = $weekdays[$fdw];
            $get_second_week_day = $weekdays[$sdw];
            $now = time();
            $start_first_ts = strtotime($start_first_date);
            $second_first_ts = strtotime($second_start_date);
            $first_start_day_next =  strtotime('+1 month', $start_first_ts);
            $second_start_date_next =   strtotime('+1 month', $second_first_ts);
            $last_day_next = date("Y-m-t",$first_start_day_next);

            $end_ts = strtotime($lastDateOfMonth);
            $end_ts_next = strtotime($last_day_next);

            $first_contribution_next_date = $this->get_first_contribution_date($start_first_ts , $end_ts , $get_first_week_day ,$fcd);
            $second_contribution_next_date = $this->get_second_contribution_date($second_first_ts , $end_ts , $get_second_week_day ,$scd);
            
            $first_contribution_timestamp = date('Y-m-d',$first_contribution_next_date);
            $second_contribution_timestamp = date('Y-m-d',$second_contribution_next_date);
            $today  = date('Y-m-d',$this_date);

            if($first_contribution_timestamp > $today || $second_contribution_timestamp > $today){

                if($first_contribution_timestamp > $today && $second_contribution_timestamp > $today){

                    if($first_contribution_timestamp > $second_contribution_timestamp){
                       $next_date = $second_contribution_next_date;
                    }else{
                          $next_date = $first_contribution_next_date;
                    }
                }else if($second_contribution_timestamp > $today){
                  
                     $next_date = $second_contribution_next_date;
                    
                }else if($first_contribution_timestamp > $today){
                     $next_date = $first_contribution_next_date;
                      
                }else{
                    // echo 'both dates occure before today'; die();
                }
            }else{
                $first_contribution_next_date = $this->get_first_contribution_date($first_start_day_next , $end_ts_next , $get_first_week_day ,$fcd);
                $second_contribution_next_date = $this->get_second_contribution_date($second_start_date_next , $end_ts_next , $get_second_week_day ,$scd);
                if(date('Y-m-d',$first_contribution_next_date) > date('Y-m-d',$second_contribution_next_date)){
                    $next_date = $second_contribution_next_date;
                }else if (date('Y-m-d',$second_contribution_next_date) > date('Y-m-d',$first_contribution_next_date)) {
                     $next_date = $first_contribution_next_date;
                }
            }
          
            }
  
        if ($next_date > $this_date){
             return  $next_date; 
        }else if ($next_date == $this_date){
             return $next_date; 
        }else{
             return $next_date; 
        }   
	}
    public function get_first_contribution_date($start_first_ts=0 , $end_ts=0 , $get_first_week_day=0 ,$fcd=0){        
      $first_contribution_day  = array();
        //echo date('Y-m-d',$start_first_ts) .'<br>'. date('Y-m-d',$end_ts) .'<br>'. $get_first_week_day.'<br>'. $fcd .'<br>';  
       for ( $i = $start_first_ts; $i <= $end_ts; $i = $i + 86400 ) {
                $thisDate = date( 'Y-m-d', $i );
                $getDate = date('l', strtotime($thisDate));                
                if ($getDate == $get_first_week_day) 
                {
                  $first_contribution_day[] = $thisDate ;
                }
            }

            if(isset($first_contribution_day[$fcd-1])){              
                return $next_date = strtotime($first_contribution_day[$fcd-1]);
            }
    }

    public function get_second_contribution_date($second_first_ts=0 , $end_ts=0 , $get_second_week_day=0 ,$scd=0){        
      $second_contribution_day  = array();
        //echo date('Y-m-d',$second_first_ts) .'<br>'. date('Y-m-d',$end_ts) .'<br>'. $get_second_week_day.'<br>'. $scd .'<br>';
       for ( $i = $second_first_ts; $i <= $end_ts; $i = $i + 86400 ) {
                $thisDate = date( 'Y-m-d', $i );
                $getDate = date('l', strtotime($thisDate));                
                if ($getDate == $get_second_week_day) 
                {
                  $second_contribution_day[] = $thisDate ;
                }
            }

        if(isset($second_contribution_day[$scd-1])){               
           return $next_date = strtotime($second_contribution_day[$scd-1]);
        }

    }

    public function get_second_regular_contribution_contribution_date($contribution_frequency = 0,$month_day_monthly = 0,$week_day_monthly = 0,$week_day_weekly = 0,$week_day_fortnight = 0,$week_number_fortnight = 0,$month_day_multiple = 0,$week_day_multiple = 0,$starting_month_multiple = 0,$after_first_contribution_days_option=0,$after_first_day_week_multiple=0,$after_first_starting_day=0,$after_second_contribution_day_option=0,$after_second_day_week_multiple=0,$after_second_starting_day=0,$date=0 ){
        $manual = FALSE;
        if($date){
            //do notthing for now
            $manual = TRUE;
        }else{
            $date = time();
        }
        $id = $contribution_frequency;
        $next_date = 0;
        $this_date = mktime(0, 0, 0, date('n',$date), date('j',$date), date('Y',$date));
        $weekdays = array('Day',
            'Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
        );

        $months = array('None',
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
        );
        if($id == 9){
            $fcd = $after_first_contribution_days_option;
            $fdw = $after_first_day_week_multiple;
            $fsd = $after_first_starting_day;
            $scd = $after_second_contribution_day_option;
            $sdw = $after_second_day_week_multiple;
            $ssd = $after_second_starting_day;

            $first_contribution_day = $this->contribution_days_option[$fcd];
            $first_day = $this->month_days[$fdw];
            $after_date = $this->starting_days[$fsd];
            //+ days after 2nd
            $lastday = date('Y-m-d', strtotime('last day of this month'));
            //echo mktime(0, 0, 0, $fsd, $get_month, $get_year); 
             $start_date  = date('Y-m-'.$ssd);           
             $get_second_start_date = date('Y-m-d', strtotime('last day of this month')); 
            $specific_days  = array();
            $month  = date('m');
            $year  = date('Y');
            $days = cal_days_in_month(CAL_GREGORIAN, $month,$year); // get no of days
            $get_week_day = $weekdays[$sdw];
            $start_ts = strtotime($start_date);
            $end_ts = strtotime($lastday);
             $diff = $end_ts - $start_ts;
             $count =  round($diff / 86400) + 1;
                 for ( $i = $start_ts; $i <= $end_ts; $i = $i + 86400 ) {
                    $thisDate = date( 'Y-m-d', $i );
                    $getDate = date('l', strtotime($thisDate));
                    //echo $getDate . "<br>";
                    if ($getDate == $get_week_day) 
                    {
                      $specific_days[] = $thisDate ;
                    }
                  }
              
                 if(isset($specific_days[$scd-1])){                 
                     
                     $next_date = strtotime($specific_days[$scd-1]);                    
                }
            }

        if ($next_date > $this_date){
             return  $next_date; 
        }else if ($next_date == $this_date){
             return $next_date; 
        }else{
             return $next_date; 
        }
    }

    public function get_contribution_fine_date($date = 0,$fine_type = 0,$fixed_fine_chargeable_on = 0,$percentage_fine_chargeable_on = 0, $fixed_fine_frequency = 0,$percentage_fine_frequency = 0,$system_request = FALSE,$next_contribution_date = 0){
        if(($system_request&&$percentage_fine_frequency==0)||($system_request&&$fixed_fine_frequency==0)){
            $fine_date = '';
        }else{
            if($fine_type == 1){
                //Fixed
                if($fixed_fine_frequency){
                    if($fixed_fine_frequency==1){
                        //per day
                        $fine_date = $date + 24*60*60;
                    }else if($fixed_fine_frequency==2){
                        //per week
                        $fine_date = $date + 7*24*60*60;
                    }else if($fixed_fine_frequency==3){
                        //per month
                        $fine_date = strtotime('+1 month', strtotime(date('d-m-Y')));
                    }else if($fixed_fine_frequency==4){
                        //per quarter
                        $fine_date = strtotime('+3 months', strtotime(date('d-m-Y')));
                    }else if($fixed_fine_frequency==5){
                        //per half year
                        $fine_date = strtotime('+6 months', strtotime(date('d-m-Y')));
                    }else if($fixed_fine_frequency==6){
                        //per year
                        $fine_date = strtotime('+1 year', strtotime(date('d-m-Y')));
                    }
                }else{
                    if($fixed_fine_chargeable_on=='first_day_of_the_month'){
                        $current_month = date('n');
                        $current_year  = date('Y');
                        if($current_month == 12){
                            $year = $current_year+1;
                            $year = (int)$year;
                            $fine_date = mktime(0,0,0,0,0,$year);
                        }else{
                            $month = $current_month+1;
                            $month = (int)$month;
                            $fine_date = mktime(0,0,0,$month,1,$current_year);
                        }
                    }else if($fixed_fine_chargeable_on=='last_day_of_the_month'){

                        $actual_date = date('d-m-Y',$date);
                        $fine_date = strtotime(date("t-m-Y",strtotime($actual_date)));
                    }else if(is_numeric($fixed_fine_chargeable_on)&&$fixed_fine_frequency!=0){
                        $fine_date = $date + 24*60*60*$fixed_fine_chargeable_on;
                    }else{
                        $fine_date = $next_contribution_date + 24*60*60*$fixed_fine_chargeable_on;
                    }
                }
            }else if($fine_type == 2){
                //Percentage
                if($percentage_fine_frequency){
                    if($percentage_fine_frequency==1){
                        //per day
                        $fine_date = $date + 24*60*60;
                    }else if($percentage_fine_frequency==2){
                        //per week
                        $fine_date = $date + 7*24*60*60;
                    }else if($percentage_fine_frequency==3){
                        //per month
                        $fine_date = strtotime('+1 month', strtotime(date('d-m-Y')));
                    }else if($percentage_fine_frequency==4){
                        //per quarter
                        $fine_date = strtotime('+3 months', strtotime(date('d-m-Y')));
                    }else if($percentage_fine_frequency==5){
                        //per half year
                        $fine_date = strtotime('+6 months', strtotime(date('d-m-Y')));
                    }else if($percentage_fine_frequency==6){
                        //per year
                        $fine_date = strtotime('+1 year', strtotime(date('d-m-Y')));
                    }
                }else{
                    if($percentage_fine_chargeable_on=='first_day_of_the_month'){
                        $current_month = date('n');
                        $current_year  = date('Y');
                        if($current_month == 12){
                            $year = $current_year+1;
                            $year = (int)$year;
                            $fine_date = mktime(0,0,0,0,0,$year);
                        }else{
                            $month = $current_month+1;
                            $month = (int)$month;
                            $fine_date = mktime(0,0,0,$month,1,$current_year);
                        }
                    }else if($percentage_fine_chargeable_on=='last_day_of_the_month'){
                        $actual_date = date('d-m-Y',$date);
                        $fine_date = strtotime(date("t-m-Y",strtotime($actual_date)));
                    }else if(is_numeric($percentage_fine_chargeable_on)&&$percentage_fine_frequency!=0){
                        $fine_date = $date + 24*60*60*$percentage_fine_chargeable_on;
                    }else{
                        $fine_date = $next_contribution_date + 24*60*60*$percentage_fine_chargeable_on;
                    }
                }
            }else{
                $fine_date = '';
            }
        }
        return $fine_date;
    }

    public function queue_contribution_fine_invoices($date = 0,$ignore_contribution_fine_date = 0,$group_id = 0,$contribution_id = 0,$contribution_fine_setting_id = 0,$contribution_fine_date = 0){
        $date = $date?strtotime($date):time();
        if($ignore_contribution_fine_date){
            $contributions = $this->ci->contributions_m->get_contributions_to_be_fined($group_id,$contribution_id,$contribution_fine_setting_id);
            $contribution_fine_date = $date;
        }else{
            $contributions = $this->ci->contributions_m->get_contributions_to_be_fined_today($date);
        }
        print_r($contributions); die;
        $successful_entries = 0;
        $successful_next_fine_date_updates = 0;
        $unsuccessful_entries = 0;
        $unsuccessful_next_fine_date_updates = 0;

        $group_ids = array();  
        $contribution_ids = array();  
        $member_ids = array();
        $enable_contribution_member_list_group_ids = array(); 
        $enable_contribution_member_list_contribution_ids = array(); 
        $enable_contribution_member_list_member_ids = array(); 

        foreach ($contributions as $contribution) {
            $group_ids[] = $contribution->group_id;
            $contribution_ids[] = $contribution->id;
            if($contribution->enable_contribution_member_list){
                $enable_contribution_member_list_group_ids[] = $contribution->group_id;
                $enable_contribution_member_list_contribution_ids[] = $contribution->id;
            }
        }

        $group_ids = array_unique($group_ids);
        
        $contribution_ids = array_unique($contribution_ids);

        $enable_contribution_member_list_group_ids = array_unique($enable_contribution_member_list_group_ids);

        $enable_contribution_member_list_contribution_ids = array_unique($enable_contribution_member_list_contribution_ids);

        $group_member_ids_array = $this->ci->members_m->get_active_group_member_ids_by_group_array($group_ids);

        $member_ids = $this->ci->members_m->get_active_group_member_ids_array($group_ids);

        $contribution_member_ids_pairings_array = $this->ci->contributions_m->get_contribution_member_ids_pairings_array($enable_contribution_member_list_group_ids,$enable_contribution_member_list_contribution_ids);
        
        // $contribution_balances_array = $this->ci->statements_m->get_contribution_balances_array($group_ids,$member_ids,$contribution_ids,$date);
        // $invoice_objects_array = $this->ci->statements_m->get_member_contribution_unpaid_invoice_objects_array($group_ids,$member_ids,$contribution_ids);
        $fine_invoicing_queue = array();
        foreach ($contributions as $contribution) {
            # code...
            $member_ids = array();
            if($contribution->enable_contribution_member_list){
                $member_ids = $contribution_member_ids_pairings_array[$contribution->group_id][$contribution->id];
            }else{
                $member_ids = $group_member_ids_array[$contribution->group_id];
            }

            $contribution_balances_array = $this->ci->statements_m->get_contribution_balances_array(array($contribution->group_id),$member_ids,array($contribution->id),$date);

            $invoice_objects_array = $this->ci->statements_m->get_member_contribution_unpaid_invoice_objects_array(array($contribution->group_id),$member_ids,array($contribution->id));

            if($member_ids&&$contribution_balances_array){
                foreach($member_ids as $member_id){
                    if(isset($contribution_balances_array[$contribution->group_id][$member_id][$contribution->id])){
                        if($contribution_balances_array[$contribution->group_id][$member_id][$contribution->id] > 0){
                            if($contribution->percentage_fine_mode == 1 || $contribution->fixed_fine_mode == 1){
                                //Find all unpaid invoices
                                $invoices = $invoice_objects_array[$contribution->group_id][$member_id][$contribution->id];
                                $count = 1;
                                foreach ($invoices as $invoice) {
                                    # code...
                                    $fine_invoicing_queue[] = array(
                                        'contribution_id'=>$contribution->id,
                                        'parent_invoice_id'=>$invoice->invoice_id,
                                        'member_id'=>$member_id,
                                        'group_id'=>$contribution->group_id,
                                        'fine_date'=>$contribution_fine_date?:$contribution->fine_date,
                                        //'fine_date'=>$fine_date?:time(),
                                        'fine_type'=>$contribution->fine_type,
                                        'fixed_amount'=>$contribution->fixed_amount,
                                        'fixed_fine_chargeable_on'=>$contribution->fixed_fine_chargeable_on,
                                        'percentage_fine_chargeable_on'=>$contribution->percentage_fine_chargeable_on,
                                        'fixed_fine_frequency'=>$contribution->fixed_fine_frequency,
                                        'percentage_fine_frequency'=>$contribution->percentage_fine_frequency,
                                        'fixed_fine_mode'=>$contribution->fixed_fine_mode,
                                        'percentage_rate'=>$contribution->percentage_rate,
                                        'percentage_fine_on'=>$contribution->percentage_fine_on,
                                        'percentage_fine_mode'=>$contribution->percentage_fine_mode,
                                        'fine_limit'=>$contribution->fine_limit,
                                        'fine_sms_notifications_enabled'=>$contribution->fine_sms_notifications_enabled,
                                        'fine_email_notifications_enabled'=>$contribution->fine_email_notifications_enabled,
                                        'created_on'=>time(),
                                    );
                                    // if($this->ci->invoices_m->insert_contribution_fine_invoicing_queue($input)){
                                    //     $successful_entries++;
                                    // }else{
                                    //     $unsuccessful_entries++;
                                    // }
                                    if($contribution->fine_limit == $count){
                                        break;
                                    }else if($count == 12){
                                        break;
                                    }
                                    $count++;
                                }
                            }else{
                                $fine_invoicing_queue[] = array(
                                    'contribution_id'=>$contribution->id,
                                    'parent_invoice_id'=> NULL,
                                    'member_id'=>$member_id,
                                    'group_id'=>$contribution->group_id,
                                    'fine_date'=>$contribution_fine_date?:$contribution->fine_date,
                                    'fine_type'=>$contribution->fine_type,
                                    'fixed_amount'=>$contribution->fixed_amount,
                                    'fixed_fine_chargeable_on'=>$contribution->fixed_fine_chargeable_on,
                                    'percentage_fine_chargeable_on'=>$contribution->percentage_fine_chargeable_on,
                                    'fixed_fine_frequency'=>$contribution->fixed_fine_frequency,
                                    'percentage_fine_frequency'=>$contribution->percentage_fine_frequency,
                                    'fixed_fine_mode'=>$contribution->fixed_fine_mode,
                                    'percentage_rate'=>$contribution->percentage_rate,
                                    'percentage_fine_on'=>$contribution->percentage_fine_on,
                                    'percentage_fine_mode'=>$contribution->percentage_fine_mode,
                                    'fine_limit'=>$contribution->fine_limit,
                                    'fine_sms_notifications_enabled'=>$contribution->fine_sms_notifications_enabled,
                                    'fine_email_notifications_enabled'=>$contribution->fine_email_notifications_enabled,
                                    'created_on'=>time(),
                                );
                                // if($this->ci->invoices_m->insert_contribution_fine_invoicing_queue($input)){
                                //     $successful_entries++;
                                // }else{
                                //     $unsuccessful_entries++;
                                // }
                            }
                        }
                    }
                }
            }
           
            $next_contribution_date = $this->get_regular_contribution_contribution_date(
                $contribution->contribution_frequency,
                $contribution->month_day_monthly,
                $contribution->week_day_monthly,
                $contribution->week_day_weekly,
                $contribution->week_day_fortnight,
                $contribution->week_number_fortnight,
                $contribution->month_day_multiple,
                $contribution->week_day_multiple,
                $contribution->start_month_multiple,
                (time()+24*60*60)
            );

            if($ignore_contribution_fine_date){
                $fine_date = $this->get_contribution_fine_date($contribution_fine_date,$contribution->fine_type,$contribution->fixed_fine_chargeable_on,$contribution->percentage_fine_chargeable_on,$contribution->fixed_fine_frequency,$contribution->percentage_fine_frequency,FALSE,$contribution->contribution_date);
            }else{
                $fine_date = $this->get_contribution_fine_date($date,$contribution->fine_type,$contribution->fixed_fine_chargeable_on,$contribution->percentage_fine_chargeable_on,$contribution->fixed_fine_frequency,$contribution->percentage_fine_frequency,FALSE,$next_contribution_date);
            }            
            $input = array(
                'fine_date'=>$fine_date,
                'modified_on'=>time(),
            );
            if($this->ci->contributions_m->update_contribution_fine_setting($contribution->contribution_fine_setting_id,$input)){
                $successful_next_fine_date_updates++;
            }else{
                $unsuccessful_next_fine_date_updates++;
            }
        }

        if(!empty($fine_invoicing_queue)){
            if($this->ci->invoices_m->batch_insert_contribution_fine_invoicing_queue($fine_invoicing_queue)){
                $successful_entries += count($fine_invoicing_queue);
            }else{
                $unsuccessful_entries += count($fine_invoicing_queue);
            }
        }
        if($successful_entries){
            echo  $successful_entries.' fine invoices queued.<br/> ';
        }
        if($unsuccessful_entries){
            echo  $unsuccessful_entries.' fine invoices could not be queued.<br/> ';
        }
        if($successful_next_fine_date_updates){
            echo  $successful_next_fine_date_updates.' fine dates updated.<br/> ';
        }
        if($unsuccessful_next_fine_date_updates){
            echo  $unsuccessful_next_fine_date_updates.' fine dates could not be updated.<br/> ';
        }
    }

    public function process_contribution_fine_invoices_queue($limit = 10,$group_id = 0){
        
        $statement_insert_errors_count = 0;
        $successful_statement_entries_count = 0;
        $information_missing_errors_count = 0;
        $fine_invoice_entry_ignore_count = 0;
        $fine_negative_count = 0;
        $delete_contribution_fine_invoice_queue_count = 0;
        $delete_contribution_fine_invoice_queue_error_count = 0;
        $queued_contribution_fine_invoices = $this->ci->invoices_m->get_queued_contribution_fine_invoices($limit,$group_id);
        $group_ids = array();
        $member_ids = array();
        $contribution_ids = array();
        $contribution_objects_array = array();
        $member_objects_array = array();
        $fine_dates_array = array();
        $earliest_invoice_date = time();

        foreach($queued_contribution_fine_invoices as $queued_contribution_fine_invoice):
            if(in_array($queued_contribution_fine_invoice->fine_date,$fine_dates_array)){

            }else{
                $fine_dates_array[] = $queued_contribution_fine_invoice->fine_date;
            }

            if(in_array($queued_contribution_fine_invoice->group_id,$group_ids)){

            }else{
                $group_ids[] = $queued_contribution_fine_invoice->group_id;
            }

            if(in_array($queued_contribution_fine_invoice->contribution_id,$contribution_ids)){

            }else{
                $contribution_ids[] = $queued_contribution_fine_invoice->contribution_id;
            }

            if(in_array($queued_contribution_fine_invoice->member_id,$member_ids)){

            }else{
                $member_ids[] = $queued_contribution_fine_invoice->member_id;
            }

            if($earliest_invoice_date > $queued_contribution_invoice->invoice_date){
                $earliest_invoice_date = $queued_contribution_invoice->invoice_date;
            }
        endforeach;

        $contribution_objects_array = $this->ci->contributions_m->get_group_contribution_objects_array($group_ids,$contribution_ids);

        $member_objects_array = $this->ci->members_m->get_group_member_objects_array($group_ids,$member_ids);

        //die;
        $queued_contribution_fine_invoice_ids = array();
        $fine_invoices_sent_today_array = $this->ci->invoices_m->get_contribution_fine_invoices_sent_array($fine_dates_array);
        foreach($queued_contribution_fine_invoices as $queued_contribution_fine_invoice){
            // if($this->ci->invoices_m->delete_contribution_fine_invoice_queue($queued_contribution_fine_invoice->id)){
            //     $delete_contribution_fine_invoice_queue_count++;
            // }else{
            //     $delete_contribution_fine_invoice_queue_error_count++;
            // }
            $queued_contribution_fine_invoice_ids[] = $queued_contribution_fine_invoice->id;
            $contribution = $contribution_objects_array[$queued_contribution_fine_invoice->contribution_id];
            $member = $member_objects_array[$queued_contribution_fine_invoice->member_id];

            if($contribution&&$member){
                if(isset($fine_invoices_sent_today_array[$member->user_id][$member->id][$queued_contribution_fine_invoice->contribution_id][$queued_contribution_fine_invoice->group_id][$queued_contribution_fine_invoice->fine_date][$queued_contribution_fine_invoice->parent_invoice_id])){
                //if(FALSE){    
                    $fine_invoice_entry_ignore_count++;
                }else{
                    //calculate fine payable
                    if($queued_contribution_fine_invoice->fine_type == 1){
                        //fixed
                        $fine_amount_payable = $queued_contribution_fine_invoice->fixed_amount;
                    }else if($queued_contribution_fine_invoice->fine_type == 2){
                        //percentage
                        if($queued_contribution_fine_invoice->percentage_fine_on == 1){
                            //percentage fine on contribution amount
                            $fine_amount_payable = ($queued_contribution_fine_invoice->percentage_rate/100) * $contribution->amount;
                        }else if($queued_contribution_fine_invoice->percentage_fine_on == 2){
                            //percentage fine on contribution balance
                            $contribution_balance = $this->ci->statements_m->get_group_member_contribution_balance($queued_contribution_fine_invoice->group_id,$member->id,$contribution->id,$queued_contribution_fine_invoice->fine_date);

                            $fine_amount_payable = ($queued_contribution_fine_invoice->percentage_rate/100) * $contribution_balance;

                        }else if($queued_contribution_fine_invoice->percentage_fine_on == 3){
                            //percentage fine on contribution balance and contribution fine balance
                            $contribution_balance = $this->ci->statements_m->get_group_member_contribution_balance($queued_contribution_fine_invoice->group_id,$member->id,$contribution->id,$queued_contribution_fine_invoice->fine_date);

                            $contribution_fine_balance = $this->ci->statements_m->get_group_member_contribution_fine_balance($queued_contribution_fine_invoice->group_id,$member->id,$contribution->id,'',$queued_contribution_fine_invoice->fine_date);

                            $balance = $contribution_balance + $contribution_fine_balance;
                            $fine_amount_payable = ($queued_contribution_fine_invoice->percentage_rate/100) * $balance;

                        }else{
                            $fine_amount_payable = 0;
                        }
                    }else{
                        $fine_amount_payable = 0;
                    }

                    if($fine_amount_payable>0){
                        $description = '';
                        $sms_template = '';
                        if($this->ci->transactions->create_contribution_fine_invoice(2,
                            $queued_contribution_fine_invoice->group_id,
                            $member,
                            $contribution,
                            $queued_contribution_fine_invoice->fine_date,
                            $queued_contribution_fine_invoice->fine_date,
                            $fine_amount_payable,
                            $description,
                            $sms_template,
                            $queued_contribution_fine_invoice->fine_sms_notifications_enabled,
                            $queued_contribution_fine_invoice->fine_email_notifications_enabled,
                            $queued_contribution_fine_invoice->parent_invoice_id
                            )
                            ){
                                $fine_invoices_sent_today_array[$member->user_id][$member->id][$queued_contribution_fine_invoice->contribution_id][$queued_contribution_fine_invoice->group_id][$queued_contribution_fine_invoice->fine_date][$queued_contribution_fine_invoice->parent_invoice_id?:NULL] = 1;

                                $queued_contribution_fine_invoice->amount_payable = $fine_amount_payable;
                                if(in_array($queued_contribution_fine_invoice->group_id,$group_ids)){

                                }else{
                                    $group_ids[] = $queued_contribution_fine_invoice->group_id;
                                }

                                if(in_array($queued_contribution_fine_invoice->member_id,$member_ids)){

                                }else{
                                    $member_ids[] = $queued_contribution_fine_invoice->member_id;
                                    $member_objects_array[$queued_contribution_fine_invoice->member_id] = $member;
                                }

                                if(in_array($queued_contribution_fine_invoice->contribution_id,$contribution_ids)){

                                }else{
                                    $contribution_ids[] = $queued_contribution_fine_invoice->contribution_id;
                                    $contribution_objects_array[$queued_contribution_fine_invoice->contribution_id] = $contribution;
                                    //$contribution_settings_objects_array[$queued_contribution_fine_invoice->contribution_id] = $contribution_setting;
                                }
                                $successful_statement_entries_count++;
                        }else{
                            $statement_insert_errors_count++;
                        }
                    }else{
                        $fine_negative_count++;
                    }
                }
            }else{
                $information_missing_errors_count++;
            }
        }

        $queued_contribution_fine_invoice_ids_list = implode(',', $queued_contribution_fine_invoice_ids);
        if($this->ci->invoices_m->delete_contribution_fine_invoices_queue($queued_contribution_fine_invoice_ids_list)){
            echo count($queued_contribution_fine_invoice_ids)." Fine queued invoices deleted successfully successfully.<br/>";
        }

        if($this->ci->transactions->update_group_member_fine_statement_balances($group_ids,$member_ids,$earliest_invoice_date)){
            echo "Fine Statement Balances Updated successfully.<br/>";
        }

        if($this->ci->transactions->send_fine_invoice_notifications($queued_contribution_fine_invoices,$group_ids,$member_ids,$contribution_ids,$member_objects_array,$contribution_objects_array)){
            echo "Notifications sent successfully<br/>";
        }

        if($fine_negative_count){
            echo  $fine_negative_count.' fines ignore as amount payable is below zero.<br/> ';
        }

        if($information_missing_errors_count){
            echo  $information_missing_errors_count.' information missing .<br/> ';
        }

        if($fine_invoice_entry_ignore_count){
            echo  $fine_invoice_entry_ignore_count.' fine invoice entry ignored as fine invoice already sent.<br/> ';
        }

        if($statement_insert_errors_count){
            echo  $statement_insert_errors_count.' statements could not be inserted .<br/> ';
        }
        if($successful_statement_entries_count){
            echo  $successful_statement_entries_count.' statement entries successfully made .<br/> ';
        }
        if($delete_contribution_fine_invoice_queue_count){
            echo  $delete_contribution_fine_invoice_queue_count.' fine contributions removed from the queue .<br/> ';
        }
        if($delete_contribution_fine_invoice_queue_error_count){
            echo  $delete_contribution_fine_invoice_queue_error_count.' fine contributions could not be removed from the queue.<br/> ';
        }
    }

    public function update_next_invoice_dates($date = 0){        
        if($date){
            $date = strtotime($date)+(4*60*60);
        }else{
            $date = time();
        }
        $successful_next_invoice_date_updates = 0;
        $unsuccessful_next_invoice_date_updates = 0;
        //set next invoice date
        $contributions = $this->ci->contributions_m->get_regular_contributions();
        foreach ($contributions as $contribution) {
            # code..
            $contribution_date = $this->get_regular_contribution_contribution_date(
                $contribution->contribution_frequency,
                $contribution->month_day_monthly,
                $contribution->week_day_monthly,
                $contribution->week_day_weekly,
                $contribution->week_day_fortnight,
                $contribution->week_number_fortnight,
                $contribution->month_day_multiple,
                $contribution->week_day_multiple,
                $contribution->start_month_multiple,
                $contribution->after_first_contribution_day_option,
                $contribution->after_first_day_week_multiple,
                $contribution->after_first_starting_day,
                $contribution->after_second_contribution_day_option,
                $contribution->after_second_day_week_multiple,
                $contribution->after_second_starting_day,
                $date
            );
             //echo date('Y-m-d',$date) .'<br>';
             //print_r(date('Y-m-d',$contribution_date));
              //die();
            if($contribution_date==$contribution->contribution_date){
                //do nothing for now
            }else{
                $invoice_date = $contribution_date - (24*60*60*$contribution->invoice_days);
                $input = array(
                    'invoice_date'=>$invoice_date,
                    'last_invoice_date'=>$contribution->invoice_date,
                    'contribution_date'=>$contribution_date,
                    'modified_on'=>time(),
                );
                if($this->ci->contributions_m->update_regular_contribution_setting($contribution->regular_contribution_setting_id,$input)){
                    $successful_next_invoice_date_updates++;
                }else{
                    $unsuccessful_next_invoice_date_updates++;
                }
            }
        }
        //print_r($input); die();
        echo $successful_next_invoice_date_updates.' contribution dates updated <br/>';
    }

    function update_overdue_invoice_date($date){
        if($date){

        }else{
           $date = time(); 
        }
        $successful_next_fine_date_updates =0;
        $unsuccessful_next_fine_date_updates = 0;
        $contributions = $this->ci->contributions_m->get_contributions_with_overdue_fine_date($date);
        if($contributions){
            foreach ($contributions as $key => $contribution):
                $next_contribution_date = $this->get_regular_contribution_contribution_date(
                    $contribution->contribution_frequency,
                    $contribution->month_day_monthly,
                    $contribution->week_day_monthly,
                    $contribution->week_day_weekly,
                    $contribution->week_day_fortnight,
                    $contribution->week_number_fortnight,
                    $contribution->month_day_multiple,
                    $contribution->week_day_multiple,
                    $contribution->start_month_multiple,
                    (time()+24*60*60)
                );               

                $fine_date = $this->get_contribution_fine_date($date,$contribution->fine_type,$contribution->fixed_fine_chargeable_on,$contribution->percentage_fine_chargeable_on,$contribution->fixed_fine_frequency,$contribution->percentage_fine_frequency,FALSE,$next_contribution_date);
                   $input = array(
                    'fine_date'=>$fine_date,
                    'modified_on'=>time(),
                );
                if($this->ci->contributions_m->update_contribution_fine_setting($contribution->contribution_fine_setting_id,$input)){
                    $successful_next_fine_date_updates++;
                }else{
                    $unsuccessful_next_fine_date_updates++;
                }
            endforeach;
            
        }
    }

}