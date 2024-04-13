<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Billing_m extends MY_Model{

    protected $_table = 'billing';

    function __construct(){
        parent::__construct();
        $this->install();
    }

    function install(){
        $this->db->query("
            create table if not exists billing_packages(
                id int not null auto_increment primary key,
                `name` blob,
                `slug` blob,
                `billing_type` blob,
                `rate` blob,
                `rate_on` blob,
                `monthly_amount` blob,
                `quarterly_amount` blob,
                `annual_amount` blob,
                `monthly_smses` blob,
                `quarterly_smses` blob,
                `annual_smses` blob,
                `enable_tax` blob,
                `percentage_tax` blob,
                `active` blob,
                `is_default` blob,
                `created_by` blob,
                `created_on` blob,
                `modified_on` blob,
                `modified_by` blob
        )");
    }

    function insert_package($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_secure_data('billing_packages',$input);
    }

    function update_package($id,$input,$val=FALSE){
        return $this->update_secure_data($id,'billing_packages',$input);
    }

    function get_package($id=0)
    {
        $this->select_all_secure('billing_packages');
        $this->db->where('id',$id);
        return $this->db->get('billing_packages')->row();
    }
   
    function get_all_packages(){
        $this->select_all_secure('billing_packages');
        return $this->db->get('billing_packages')->result();
    }

    function get_billing_packages_options(){
        $this->select_all_secure('billing_packages');
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $result = $this->db->get('billing_packages')->result();
        $arr = array();
        if($result){
            foreach ($result as $value) {
                
                    $arr[$value->id] = $value->name;
                
            }
        }
        return $arr;
    }

    function get_unique_package_by_slug($slug='',$id=0){
        $this->db->where($this->dx('slug').' = "'.$slug.'"',NULL,FALSE);
        if($id){
            $this->db->where('id !=',$id);
        }
        return $this->db->count_all_results('billing_packages')?:0;
    }

    function count_all_packages(){
        return $this->db->count_all_results('billing_packages');
    }

    function get_default_package(){
        $this->select_all_secure('billing_packages');
        $this->where($this->dx('active').'="1"',NULL,FALSE);
        $this->where($this->dx('is_default').'="1"',NULL,FALSE);
        return $this->db->get('billing_packages')->row();
    }


    /**********************************************Billing invoices*********************************/
    function insert_billing_invoice($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_secure_data('billing_invoices',$input);
    }

    function get_group_invoices($group_id = 0,$order){
        $this->select_all_secure('billing_invoices');
        $this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').' = "1"',NULL,FALSE);
        if($order){
            $this->db->order_by($this->dx('billing_date'),'ASC',FALSE);
        }else{
            $this->db->order_by($this->dx('billing_date'),'DESC',FALSE);
        }
        return $this->db->get('billing_invoices')->result();
    }

}?>