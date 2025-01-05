<?php
class Billing_Model extends CI_Model {

        public $title;
        public $content;
        public $date;
        
        public function __construct(){ 
            $this->load->database();
        }

        public function  vendor()
        {   
            $this->db->select('*')
            ->from('tbl_customer');  
            $query=$this->db->get();
            return $query->result_array();
        
        }
        public function  state()
        {   
            $this->db->select('*')
            ->from('tbl_states');  
            $query=$this->db->get();
            return $query->result_array();        
        }
        public function state_details($cust_state)
        {
            $this->db->select('*')
            ->from('tbl_states'); 
            $this->db->where('id',$cust_state); 
            $query=$this->db->get();
            return $query->result_array();
        } 
        public function vendor_details($vendor_id)
        {
            // $this->db->select('*')
            // ->from('tbl_customer'); 
            // $this->db->where('cust_id',$vendor_id); 
            // $query=$this->db->get();
            // return $query->result_array();

            $this->db->select('tbl_states.id,tbl_states.state_name')
            ->from('tbl_customer_details'); 
            $this->db->join('tbl_states', 'tbl_states.id = tbl_customer_details.cust_state', 'left');
            $this->db->where('tbl_customer_details.customer_id',$vendor_id); 
            $query=$this->db->get();
            return $query->result_array();
        }
        public function vendor_details_state_wise($order_id)
        {
            $this->db->select('*')
            ->from('tbl_order_creation'); 
            $this->db->join('tbl_customer', 'tbl_customer.cust_id = tbl_order_creation.customer_id', 'left');
            $this->db->join('tbl_customer_details', 'tbl_customer_details.customer_id = tbl_customer.cust_id', 'left'); 
            $this->db->join('tbl_states', 'tbl_states.id = tbl_order_creation.state_id', 'left');  
            $this->db->where('tbl_order_creation.order_id',$order_id); 
            $query=$this->db->get();  
            return $query->result_array();
        } 
        public function vendor_state_wise_dtls($vendor_name,$state_id)
        {
            $this->db->select('*')
            ->from('tbl_customer'); 
            // $this->db->join('tbl_customer', 'tbl_customer.cust_id = tbl_order_creation.customer_id', 'left');
            $this->db->join('tbl_customer_details', 'tbl_customer_details.customer_id = tbl_customer.cust_id', 'left'); 
            $this->db->join('tbl_states', 'tbl_states.id = tbl_customer_details.cust_state', 'left');  
            $this->db->where('tbl_customer.cust_id',$vendor_name); 
            $this->db->where('tbl_customer_details.cust_state',$state_id); 
            $query=$this->db->get();  
            return $query->result_array();
        } 
        public function state_wise_vendor_dtls($vendor_name,$cust_state)
        {
            $this->db->select('*')
            ->from('tbl_customer'); 
            // $this->db->join('tbl_customer', 'tbl_customer.cust_id = tbl_order_creation.customer_id', 'left');
            $this->db->join('tbl_customer_details', 'tbl_customer_details.customer_id = tbl_customer.cust_id', 'left'); 
            $this->db->join('tbl_states', 'tbl_states.id = tbl_customer_details.cust_state', 'left');  
            $this->db->where('tbl_customer.cust_id',$vendor_name); 
            $this->db->where('tbl_customer_details.cust_state',$cust_state); 
            $query=$this->db->get();  
            return $query->result_array();
        } 
        
        //model for getting sale mc 
        public function barcode_wise_item_details($vendor_id,$branch_id,$barcode_no)
        {  
            $data =  $this->db->query("SELECT DISTINCT 
                tbl_production_type.production_type_name AS production_type_name, 
                tbl_production_type.production_type_id AS production_type_id, 
                tbl_metal_sub_type.metal_sub_type_name AS metal_sub_type_name, 
                tbl_hm_gcd.item_name, 
                tbl_style.variant_name, 
                tbl_metal_sub_type.hsn_code AS hsn_code, 
                tbl_hm_gcd.purity AS purity, 
                tbl_hm_gcd.hm_gcd_id AS hm_gcd_id, 
                tbl_hm_gcd.gross_weight AS gross_weight, 
                tbl_hm_gcd.stone_wt AS stone_wt, 
                tbl_hm_gcd.net_weight,
                tbl_hm_gcd.barcode, 
                            
                (
                    SELECT sales_mc
                    FROM tbl_customer_mc AS cust_mc_subcat
                    WHERE FIND_IN_SET(tbl_style.product_subcategory_id, cust_mc_subcat.sub_category_id) > 0
                        AND FIND_IN_SET(tbl_style.design_master, cust_mc_subcat.design_id) > 0
                        AND FIND_IN_SET(tbl_style.product_category_id, cust_mc_subcat.category_id) > 0
                        AND cust_mc_subcat.cust_id = $vendor_id
                    LIMIT 1
                ) AS cust_mc_percentage

            FROM tbl_hm_gcd 
            LEFT JOIN tbl_style 
                ON tbl_style.variant_name = tbl_hm_gcd.item_name 
            LEFT JOIN tbl_production_type 
                ON tbl_production_type.production_type_id = tbl_style.production_type_id 
            LEFT JOIN tbl_metal_sub_type 
                ON tbl_metal_sub_type.metal_sub_type_id = tbl_style.metal_sub_type_id 
            WHERE 
                tbl_hm_gcd.branch_id = $branch_id
                AND tbl_hm_gcd.barcode IN ($barcode_no) 
                AND tbl_hm_gcd.gcd_status = 1 
                AND tbl_hm_gcd.finished_good_status = 3
                AND tbl_hm_gcd.gcd_out_status = 0;
                ");
                //    print_r($this->db);exit;
                return $data->result_array();  
        }


        //Model for Getting advance mc query 
        public function barcode_wise_item_details_for_credit_sale($vendor_id,$branch_id,$barcode_no)
        {  
            $data =  $this->db->query("SELECT DISTINCT 
                tbl_production_type.production_type_name AS production_type_name, 
                tbl_production_type.production_type_id AS production_type_id, 
                tbl_metal_sub_type.metal_sub_type_name AS metal_sub_type_name, 
                tbl_hm_gcd.item_name, 
                tbl_style.variant_name, 
                tbl_metal_sub_type.hsn_code AS hsn_code, 
                tbl_hm_gcd.purity AS purity, 
                tbl_hm_gcd.hm_gcd_id AS hm_gcd_id, 
                tbl_hm_gcd.gross_weight AS gross_weight, 
                tbl_hm_gcd.stone_wt AS stone_wt, 
                tbl_hm_gcd.net_weight,
                tbl_hm_gcd.barcode, 
                            
                (
                    SELECT adv_mc
                    FROM tbl_customer_mc AS cust_mc_subcat
                    WHERE FIND_IN_SET(tbl_style.product_subcategory_id, cust_mc_subcat.sub_category_id) > 0
                        AND FIND_IN_SET(tbl_style.design_master, cust_mc_subcat.design_id) > 0
                        AND FIND_IN_SET(tbl_style.product_category_id, cust_mc_subcat.category_id) > 0
                        AND cust_mc_subcat.cust_id = $vendor_id
                    LIMIT 1
                ) AS cust_mc_percentage

            FROM tbl_hm_gcd 
            LEFT JOIN tbl_style 
                ON tbl_style.variant_name = tbl_hm_gcd.item_name 
            LEFT JOIN tbl_production_type 
                ON tbl_production_type.production_type_id = tbl_style.production_type_id 
            LEFT JOIN tbl_metal_sub_type 
                ON tbl_metal_sub_type.metal_sub_type_id = tbl_style.metal_sub_type_id 
            WHERE 
                tbl_hm_gcd.branch_id = $branch_id
                AND tbl_hm_gcd.barcode IN ($barcode_no) 
                AND tbl_hm_gcd.gcd_status = 1 
                AND tbl_hm_gcd.finished_good_status = 3
                AND tbl_hm_gcd.gcd_out_status = 0;
                ");
                //    print_r($this->db);exit;
                return $data->result_array();  
        }


        public function item_details($vendor_id,$branch_id)
        {  
            $data =  $this->db->query("SELECT DISTINCT `tbl_production_type`.`production_type_name` as `production_type_name`,
            `tbl_metal_sub_type`.`metal_sub_type_name` as `metal_sub_type_name`,
            `tbl_hm_gcd`.`item_name`,`tbl_style`.`variant_name`,
            `tbl_metal_sub_type`.`hsn_code` as `hsn_code`,
            `tbl_karat`.`karat_id` as `karat_id`,
            `tbl_hm_gcd`.`purity` as `purity`,`tbl_hm_gcd`.`hm_gcd_id` as `hm_gcd_id`,
              `tbl_hm_gcd`.`gross_weight` as `gross_weight`,
            `tbl_hm_gcd`.`stone_wt` as `stone_wt`, 
            `tbl_hm_gcd`.`net_weight`,
             `tbl_customer_item_dtls`.`cust_mc_percentage` as `cust_mc_percentage`
           FROM `tbl_hm_gcd`
           LEFT JOIN `tbl_style` ON `tbl_style`.`variant_name` = `tbl_hm_gcd`.`item_name` 
           LEFT JOIN `tbl_production_type` ON `tbl_production_type`.`production_type_id` = `tbl_style`.`production_type_id`
           LEFT JOIN `tbl_metal_sub_type` ON `tbl_metal_sub_type`.`metal_sub_type_id` = `tbl_style`.`metal_sub_type_id` 
           LEFT JOIN `tbl_karat` ON `tbl_karat`.`karat` = `tbl_hm_gcd`.`purity` 
           LEFT JOIN `tbl_customer_item_dtls` ON `tbl_customer_item_dtls`.`production_type_category_id` = `tbl_style`.`product_subcategory_id`
           WHERE  `tbl_customer_item_dtls`.`cust_id` = $vendor_id AND tbl_hm_gcd.branch_id=$branch_id  AND tbl_hm_gcd.gcd_status=1
           AND tbl_hm_gcd.finished_good_status=3");
          return $data->result_array();  
            //  $this->db->distinct('tbl_hm_gcd.hm_gcd_id as hm_gcd_id');
            // $this->db->select('tbl_hm_gcd.hm_gcd_id as hm_gcd_id,tbl_hm_gcd.item_name as item_name,tbl_hm_gcd.barcode as barcode,tbl_hm_gcd.hm_gcd_id as hm_gcd_id,
            // tbl_hm_gcd.purity as purity,tbl_hm_gcd.gross_weight as gross_weight,tbl_hm_gcd.stone_wt as stone_wt ,
            // tbl_metal_sub_type.hsn_code as hsn_code,tbl_production_type.production_type_name as production_type_name,
            // tbl_metal_sub_type.metal_sub_type_name as metal_sub_type_name,tbl_production_type.production_type_id  as production_type_id,
            // tbl_style.style_id as style_id,tbl_karat.karat_id as karat_id,`tbl_style`.`variant_name`,
       
            // `tbl_customer_credit_period`.`cust_credit_period` as `cust_credit_period`,tbl_karat.karat,`tbl_customer_item_dtls`.`cust_mc_percentage` as cust_mc_percentage')
            // ->from('tbl_hm_gcd');
                
            // $this->db->join('tbl_style', 'tbl_style.variant_name = tbl_hm_gcd.item_name', 'left');
            // $this->db->join('tbl_karat', 'tbl_karat.karat = tbl_hm_gcd.purity', 'left');
            // $this->db->join('tbl_location_transfer_details', 'tbl_location_transfer_details.gcd_id = tbl_hm_gcd.hm_gcd_id', 'left');
            // $this->db->join('tbl_metal_sub_type', 'tbl_metal_sub_type.metal_sub_type_id = tbl_style.metal_sub_type_id', 'left');
            // $this->db->join('tbl_production_type', 'tbl_production_type.production_type_id = tbl_style.production_type_id', 'left');
            // $this->db->join('tbl_hm_batch', 'tbl_hm_batch.hm_batch_no = tbl_hm_gcd.batch_id', 'left');
            // $this->db->join('tbl_hm_order_details', 'tbl_hm_order_details.hm_order_details_id = tbl_hm_batch.hm_order_details_id', 'left'); 
            // $this->db->join('tbl_order_creation', 'tbl_order_creation.order_id = tbl_hm_order_details.hm_order_id', 'left');   
            // // $this->db->join('tbl_customer_item_dtls', 'tbl_customer_item_dtls.cust_style_id = tbl_hm_gcd.style_id', 'left');  
            // $this->db->join('tbl_customer', 'tbl_customer.cust_id = tbl_order_creation.customer_id', 'left');  
            // $this->db->join('tbl_customer_details', 'tbl_customer_details.customer_id = tbl_customer.cust_id', 'left');  
            // $this->db->join('tbl_customer_credit_period', 'tbl_customer_credit_period.customer_id = tbl_customer_details.customer_id', 'left');  
            // $this->db->join('tbl_customer_item_dtls', 'tbl_customer_item_dtls.production_type_category_id = tbl_style.product_subcategory_id', 'left');  
            // $this->db->where('tbl_customer_item_dtls.cust_id',$vendor_id);          
            // $this->db->where('tbl_hm_gcd.branch_id',$branch_id);
            // $this->db->where('tbl_hm_gcd.status',0);
            // $this->db->where('tbl_hm_gcd.finished_good_status',1);
            // $query=$this->db->get(); //print_r($this->db);exit;
            // return $query->result_array();
        } 
        
        public function get_barcode_details($barcode,$vendor_name,$branch_id)
        {  
            
           
            $data =  $this->db->query("SELECT DISTINCT `tbl_production_type`.`production_type_name` as `production_type_name`,`tbl_metal_sub_type`.`metal_sub_type_name` as `metal_sub_type_name`,`tbl_hm_gcd`.`item_name`,`tbl_metal_sub_type`.`hsn_code` as `hsn_code`,`tbl_karat`.`karat_id` as `karat_id`,`tbl_hm_gcd`.`purity` as `purity`,  `tbl_hm_gcd`.`gross_weight` as `gross_weight`,
 `tbl_hm_gcd`.`stone_wt` as `stone_wt`, 
 `tbl_hm_gcd`.`net_weight`,
  `tbl_customer_item_dtls`.`cust_mc_percentage` as `cust_mc_percentage`
FROM `tbl_hm_gcd`
LEFT JOIN `tbl_style` ON `tbl_style`.`variant_name` = `tbl_hm_gcd`.`item_name` 
LEFT JOIN `tbl_production_type` ON `tbl_production_type`.`production_type_id` = `tbl_style`.`production_type_id`
LEFT JOIN `tbl_metal_sub_type` ON `tbl_metal_sub_type`.`metal_sub_type_id` = `tbl_style`.`metal_sub_type_id` 
LEFT JOIN `tbl_karat` ON `tbl_karat`.`karat` = `tbl_hm_gcd`.`purity` 
LEFT JOIN `tbl_customer_item_dtls` ON `tbl_customer_item_dtls`.`production_type_category_id` = `tbl_style`.`product_subcategory_id`
WHERE  `tbl_customer_item_dtls`.`cust_id` = $vendor_name AND tbl_hm_gcd.barcode='$barcode' AND tbl_hm_gcd.finished_good_status=3 AND tbl_hm_gcd.gcd_status=1 AND tbl_hm_gcd.status!=5 AND tbl_hm_gcd.status!=3  AND tbl_hm_gcd.branch_id=$branch_id");
            return $data->result_array();   
            // $this->db->select('tbl_hm_gcd.hm_gcd_id as hm_gcd_id,tbl_hm_gcd.item_name as item_name ,tbl_hm_gcd.barcode as barcode,tbl_hm_gcd.hm_gcd_id as hm_gcd_id,
            // tbl_hm_gcd.purity as purity,tbl_hm_gcd.gross_weight as gross_weight,tbl_hm_gcd.stone_wt as stone_wt ,
            // tbl_metal_sub_type.hsn_code as hsn_code,tbl_production_type.production_type_name as production_type_name,tbl_production_type.production_type_id  as production_type_id,tbl_style.style_id as style_id,tbl_karat.karat_id as karat_id')
            // ->from('tbl_hm_gcd');
            // $this->db->join('tbl_style', 'tbl_style.style_id = tbl_hm_gcd.style_id', 'left');
            // // $this->db->join('tbl_hm_style_category', 'tbl_hm_style_category.hm_style_category_id = tbl_hm_style.hm_style_category_id', 'left');
            // // $this->db->join('tbl_hm_style_sub_category', 'tbl_hm_style_sub_category.hm_style_sub_category_id = tbl_hm_style.hm_style_sub_category_id', 'left');
            // // $this->db->join('tbl_hm_style_category_item', 'tbl_hm_style_category_item.hm_style_category_item_id = tbl_hm_style.hm_product_item', 'left');
            // $this->db->join('tbl_karat', 'tbl_karat.karat = tbl_hm_gcd.purity', 'left');
            // $this->db->join('tbl_location_transfer_details', 'tbl_location_transfer_details.gcd_id = tbl_hm_gcd.hm_gcd_id', 'left');
            // $this->db->join('tbl_metal_sub_type', 'tbl_metal_sub_type.metal_sub_type_id = tbl_style.metal_sub_type_id', 'left');
            // // $this->db->join('tbl_production_type', 'tbl_production_type.production_type_id = tbl_style.production_type_id', 'left');
            // $this->db->where('tbl_hm_gcd.barcode','$barcode');
            // // $this->db->where('tbl_location_transfer_details.status',1);  
            // // $this->db->where('tbl_hm_gcd.status',1);  
            // // $this->db->or_where('tbl_hm_gcd.branch_id',$branch_id);  
            // $query=$this->db->get(); 
            // // print_r($this->db);exit; 
            // return $query->result_array();
        }
        public function billing_details_insert($data)
        {
            $this->db->insert('tbl_billing',$data);
            // print_r($this->db);exit;
            $insert_id = $this->db->insert_id();
            return  $insert_id; 
        }
        public function select_from_invoice()
        {  
          $query = $this->db->select('max(invoice_id) as invoice_id')
            ->from('tbl_billing') 
            ->get();
            $row = $query->last_row();

            if($row){ 
                //$idPostfix = (int)substr($row->barcode_no,1)+1; 
                $idPostfix =  $row->invoice_id+1; 
                $nextId = STR_PAD((string)$idPostfix,4,"0",STR_PAD_LEFT); 
                }

            // if($row){
            // $idPostfix = (int)substr($row->invoice_id,1)+1;
            // $nextId = STR_PAD((string)$idPostfix,4,"0",STR_PAD_LEFT);
            // }
            else{$nextId = '0001';} // For the first time
            return $nextId;
 
        }

        public function billing_item_details_insert($data)
        {
            $this->db->insert('tbl_billing_item_details',$data);
            // print_r($this->db);exit;
        }
        public function get_billing_details_gcd_id($billing_id)
        {
            $this->db->select('*')
            ->from('tbl_billing_item_details'); 
            $this->db->where('billing_id',$billing_id);   
            $query=$this->db->get();  
            return $query->result_array();
        }
        public function invoice_number()
        {
            $this->db->select('*')
            ->from('tbl_billing'); 
            $this->db->where('billing_status',0);   
            $query=$this->db->get();  
            return $query->result_array();
        }
        public function invoice_number_print()
        {
            $this->db->select('*')
            ->from('tbl_billing'); 
            $this->db->where('billing_status',3);   
            $query=$this->db->get();  
            return $query->result_array();
        }
        public function operation_invoice_number()
        {
            $this->db->select('*')
            ->from('tbl_billing'); 
            $this->db->where('billing_status',1);   
            $query=$this->db->get();  
            return $query->result_array();
        }
        public function invoice_details($billing_id)
        {
            $this->db->distinct('tbl_billing.order_id');
            $this->db->select('*')
            ->from('tbl_billing'); 
            $this->db->join('tbl_customer', 'tbl_customer.cust_id = tbl_billing.bill_vendor_id', 'left');
            $this->db->join('tbl_customer_details', 'tbl_customer_details.customer_id = tbl_customer.cust_id', 'left'); 
            $this->db->join('tbl_states', 'tbl_states.id = tbl_billing.bill_state_id', 'left'); 
            $this->db->join('tbl_billing_item_details', 'tbl_billing_item_details.billing_id = tbl_billing.billing_id', 'left'); 
            $this->db->where('tbl_billing.billing_id',$billing_id)
            ->group_by('tbl_billing_item_details.billing_dtl_id');
            $query=$this->db->get();  //print_r($this->db);exit;
            
            return $query->result_array(); 
        }
        public function mobilization_details($mob_code)
        {
            $this->db->select('*')
            ->from('tbl_employee');
            $this->db->where('emp_code',$mob_code);  
            $query=$this->db->get();  
            // print_r($this->db);
            return $query->result_array();
        }

        public function get_branch_sate($branch_id)
        {
            $this->db->select('*')
            ->from('tbl_branch');
            $this->db->join('tbl_states', 'tbl_states.id  = tbl_branch.state_id', 'left'); 
            $this->db->where('tbl_branch.branch_id',$branch_id);  
            $query=$this->db->get();  
            // print_r($this->db);
            return $query->result_array();
        }

        public function update_billing_status($data,$billing_id)
        {
            $this->db->where('billing_id',$billing_id)
            ->update('tbl_billing',$data);  
           return $this->db->affected_rows();
        }
        public function get_invoice_no($billing_id)
        {
            $this->db->select('*')
            ->from('tbl_billing'); 
            $this->db->where('billing_id',$billing_id);   
            $query=$this->db->get();  
            return $query->result_array();
        }
         public function billing_history($branch_id)
        {
            $this->db->select('*')
            ->from('tbl_billing'); 
            $this->db->join('tbl_customer', 'tbl_customer.cust_id = tbl_billing.bill_vendor_id', 'left'); 
            $this->db->where('branch_id',$branch_id);      
            $this->db->order_by('billing_date', 'DESC');   
            $query=$this->db->get();  
            return $query->result_array();
        }
        public function get_order_no()
        {
            $this->db->select('*')
            ->from('tbl_hm_gcd'); 
            $this->db->join('tbl_hm_batch', 'tbl_hm_batch.hm_batch_id = tbl_hm_gcd.batch_id', 'left');    
            $query=$this->db->get();  //print_r($this->db);exit;
            return $query->result_array();
        }
        public function update_status($data,$hm_gcd_id)
        {  
            $this->db->where('hm_gcd_id',$hm_gcd_id)
            ->update('tbl_hm_gcd',$data);   //print_r($this->db);exit;
           return $this->db->affected_rows(); 
        }
        public function billing_order_list()
        {
            $this->db->select('tbl_hm_gcd.`hm_gcd_id`, tbl_hm_gcd.`branch_id`, tbl_hm_gcd.`stock_code`, tbl_hm_gcd.`barcode`, tbl_hm_gcd.`barcode_no`, tbl_hm_gcd.`batch_id`,
            tbl_hm_gcd.`style_id`, tbl_hm_gcd.`item_name`, tbl_hm_gcd.`purity`, tbl_hm_gcd.`category_id`, tbl_hm_gcd.`product_type_id`, tbl_hm_gcd.`subcategory_id`,
            tbl_hm_gcd.`qty`, tbl_hm_gcd.`gross_weight`, tbl_hm_gcd.`stone_wt`, tbl_hm_gcd.`barcode_date`, tbl_hm_gcd.`status`, tbl_hm_gcd.`finished_good_status`,
            tbl_order_creation.order_no,`tbl_order_creation`.`order_id`,tbl_customer.cust_name')
            ->from('tbl_hm_gcd'); 
            $this->db->join('tbl_hm_batch', 'tbl_hm_batch.hm_batch_no = tbl_hm_gcd.batch_id', 'left'); 
            $this->db->join('tbl_hm_order_details', 'tbl_hm_order_details.hm_order_details_id = tbl_hm_batch.hm_order_details_id', 'left'); 
            $this->db->join('tbl_order_creation', 'tbl_order_creation.order_id = tbl_hm_order_details.hm_order_id', 'left'); 
            $this->db->join('tbl_customer', 'tbl_customer.cust_id = tbl_order_creation.customer_id', 'left'); 
            $this->db->group_by('tbl_hm_order_details.hm_order_id');
            $this->db->where('tbl_hm_gcd.status',0);
            $query=$this->db->get();  // print_r($this->db);//exit;
            return $query->result_array();
 
        }
        public function update_gcd_status($hm_gcd_id_val,$data)
        {
            $this->db->where('hm_gcd_id',$hm_gcd_id_val)
            ->update('tbl_hm_gcd',$data);   //print_r($this->db);exit;
           return $this->db->affected_rows(); 
        }
        public function from_invoice_result($branch_id)
        { 
            $this->db->select('*')
            ->from('tbl_branch');
            $this->db->join('tbl_states', 'tbl_states.id  = tbl_branch.state_id', 'left'); 
            $this->db->where('tbl_branch.branch_id',$branch_id);  
            $query=$this->db->get();  
            // print_r($this->db);
            return $query->result_array();
        }
        public function vendor_details_state($vendor_name)
        {
            $this->db->select('*')
            ->from('tbl_customer'); 
            $this->db->join('tbl_customer_details', 'tbl_customer_details.customer_id = tbl_customer.cust_id', 'left'); 
            $this->db->where('tbl_customer.cust_id',$vendor_name); 
            $query=$this->db->get();  
            return $query->result_array();
        } 
        public function check_value_in_database($barcode,$vendor_name,$branch_id) 

        {

            
            $data =  $this->db->query("SELECT DISTINCT
                tbl_production_type.production_type_name AS production_type_name,
                tbl_metal_sub_type.metal_sub_type_name AS metal_sub_type_name,
                tbl_hm_gcd.item_name,
                tbl_style.variant_name,
                tbl_metal_sub_type.hsn_code AS hsn_code,
                -- tbl_karat.karat_id AS karat_id,
                tbl_hm_gcd.purity AS purity,
                tbl_hm_gcd.hm_gcd_id AS hm_gcd_id,
                tbl_hm_gcd.gross_weight AS gross_weight,
                tbl_hm_gcd.stone_wt AS stone_wt,
                tbl_hm_gcd.net_weight,tbl_hm_gcd.barcode,
                tbl_customer_item_dtls.cust_mc_percentage AS cust_mc_percentage,
                tbl_customer_credit_period.cust_credit_period

                FROM 
                    tbl_hm_gcd
                LEFT JOIN 
                    tbl_style ON tbl_style.variant_name = tbl_hm_gcd.item_name
                LEFT JOIN 
                    tbl_production_type ON tbl_production_type.production_type_id = tbl_style.production_type_id
                LEFT JOIN 
                    tbl_metal_sub_type ON tbl_metal_sub_type.metal_sub_type_id = tbl_style.metal_sub_type_id
                -- LEFT JOIN 
                --     tbl_karat ON tbl_karat.karat = tbl_hm_gcd.purity
                LEFT JOIN 
                    tbl_customer_item_dtls ON tbl_customer_item_dtls.production_type_category_id = tbl_style.product_subcategory_id
                LEFT JOIN 
                    tbl_customer_credit_period ON tbl_customer_credit_period.customer_id = tbl_customer_item_dtls.cust_id
                WHERE 
                    tbl_customer_item_dtls.cust_id = $vendor_name 
                    AND tbl_hm_gcd.branch_id = $branch_id
                    AND tbl_hm_gcd.barcode IN ('$barcode')
                    AND tbl_hm_gcd.gcd_status=1
                    AND tbl_hm_gcd.finished_good_status=3
                    AND tbl_hm_gcd.status!=5
                    AND tbl_hm_gcd.status!=3
                    AND  tbl_hm_gcd.gcd_out_status=0 ");
                    // print_r($this->db);exit;
                return $data->result_array();  
        //     $data =  $this->db->query("SELECT  `tbl_production_type`.`production_type_name` as `production_type_name`,
        //     `tbl_metal_sub_type`.`metal_sub_type_name` as `metal_sub_type_name`,`tbl_hm_gcd`.`item_name`,
        //     `tbl_metal_sub_type`.`hsn_code` as `hsn_code`,`tbl_karat`.`karat_id` as `karat_id`,
        //     `tbl_hm_gcd`.`purity` as `purity`,  `tbl_hm_gcd`.`gross_weight` as `gross_weight`,
        //     `tbl_hm_gcd`.`stone_wt` as `stone_wt`, 
        //     `tbl_hm_gcd`.`net_weight`,`tbl_hm_gcd`.`barcode`,
        //      `tbl_customer_item_dtls`.`cust_mc_percentage` as `cust_mc_percentage`
        //    FROM `tbl_hm_gcd`
        //    LEFT JOIN `tbl_style` ON `tbl_style`.`variant_name` = `tbl_hm_gcd`.`item_name` 
        //    LEFT JOIN `tbl_production_type` ON `tbl_production_type`.`production_type_id` = `tbl_style`.`production_type_id`
        //    LEFT JOIN `tbl_metal_sub_type` ON `tbl_metal_sub_type`.`metal_sub_type_id` = `tbl_style`.`metal_sub_type_id` 
        //    LEFT JOIN `tbl_karat` ON `tbl_karat`.`karat` = `tbl_hm_gcd`.`purity` 
        //    LEFT JOIN `tbl_customer_item_dtls` ON `tbl_customer_item_dtls`.`production_type_category_id` = `tbl_style`.`product_subcategory_id`
        //    WHERE  `tbl_customer_item_dtls`.`cust_id` = $vendor_name AND tbl_hm_gcd.barcode='$barcode_val'");
        //    print_r($this->db);exit;
        //                return $data->result_array();   
            // $this->db->distinct('tbl_hm_gcd.hm_gcd_id as hm_gcd_id');
            // $this->db->select('tbl_hm_gcd.hm_gcd_id as hm_gcd_id,tbl_hm_gcd.item_name as item_name,
            // tbl_hm_gcd.barcode as barcode,
            // tbl_hm_gcd.purity as purity,tbl_hm_gcd.gross_weight as gross_weight,
            // tbl_hm_gcd.stone_wt as stone_wt,tbl_hm_gcd.net_weight,
            // tbl_metal_sub_type.hsn_code as hsn_code,tbl_production_type.production_type_name as production_type_name,
            // tbl_metal_sub_type.metal_sub_type_name as metal_sub_type_name,tbl_production_type.production_type_id  as production_type_id,
            // tbl_style.style_id as style_id,tbl_karat.karat_id as karat_id,`tbl_style`.`variant_name`,
            // `tbl_customer_item_dtls`.`cust_mc_percentage` as `cust_mc_percentage`,tbl_karat.karat')
            // ->from('tbl_hm_gcd');
            // $this->db->join('tbl_style', 'tbl_style.variant_name = tbl_hm_gcd.item_name', 'left');
            // $this->db->join('tbl_karat', 'tbl_karat.karat = tbl_hm_gcd.purity', 'left');
            // $this->db->join('tbl_location_transfer_details', 'tbl_location_transfer_details.gcd_id = tbl_hm_gcd.hm_gcd_id', 'left');
            // $this->db->join('tbl_metal_sub_type', 'tbl_metal_sub_type.metal_sub_type_id = tbl_style.metal_sub_type_id', 'left');
            // $this->db->join('tbl_production_type', 'tbl_production_type.production_type_id = tbl_style.production_type_id', 'left');
            // $this->db->join('tbl_hm_batch', 'tbl_hm_batch.hm_batch_no = tbl_hm_gcd.batch_id', 'left');
            // $this->db->join('tbl_hm_order_details', 'tbl_hm_order_details.hm_order_details_id = tbl_hm_batch.hm_order_details_id', 'left'); 
            // $this->db->join('tbl_order_creation', 'tbl_order_creation.order_id = tbl_hm_order_details.hm_order_id', 'left');   
            // $this->db->join('tbl_customer', 'tbl_customer.cust_id = tbl_order_creation.customer_id', 'left');  
            // $this->db->join('tbl_customer_details', 'tbl_customer_details.customer_id = tbl_customer.cust_id', 'left');  
            // $this->db->join('tbl_customer_credit_period', 'tbl_customer_credit_period.customer_id = tbl_customer_details.customer_id', 'left');  
            // $this->db->join('tbl_customer_item_dtls', 'tbl_customer_item_dtls.production_type_category_id = tbl_style.product_subcategory_id', 'left');  
            // $this->db->where('tbl_customer_item_dtls.cust_id',$vendor_name);  
            // $this->db->where('tbl_hm_gcd.barcode','$barcode_val');
            // // $this->db->where('tbl_hm_gcd.status',0);
            // // $this->db->where('tbl_hm_gcd.finished_good_status',1);
            // $query=$this->db->get(); 
            // // print_r($this->db);exit;
            // return $query->result_array();

        }
        public function get_barcodes_data($barcodes, $vendor_id, $branch_id) {
            $escaped_barcodes = array_map(function($barcode) {
                return "'" . addslashes($barcode) . "'";
            }, $barcodes);
    
            $barcode_list = implode(", ", $escaped_barcodes);
            
            // Updated SQL query
            $sql_query = "
                SELECT DISTINCT
                    tbl_production_type.production_type_name AS production_type_name,
                    tbl_metal_sub_type.metal_sub_type_name AS metal_sub_type_name,
                    tbl_hm_gcd.item_name,
                    tbl_style.variant_name,
                    tbl_metal_sub_type.hsn_code AS hsn_code,
                    tbl_hm_gcd.purity AS purity,
                    tbl_hm_gcd.hm_gcd_id AS hm_gcd_id,
                    tbl_hm_gcd.gross_weight AS gross_weight,
                    tbl_hm_gcd.stone_wt AS stone_wt,
                    tbl_hm_gcd.net_weight,
                    tbl_hm_gcd.barcode,
                    tbl_customer_item_dtls.cust_mc_percentage AS cust_mc_percentage,
                    tbl_customer_credit_period.cust_credit_period
                FROM tbl_hm_gcd
                LEFT JOIN tbl_style ON tbl_style.variant_name = tbl_hm_gcd.item_name
                LEFT JOIN tbl_production_type ON tbl_production_type.production_type_id = tbl_style.production_type_id
                LEFT JOIN tbl_metal_sub_type ON tbl_metal_sub_type.metal_sub_type_id = tbl_style.metal_sub_type_id
                LEFT JOIN tbl_customer_item_dtls ON tbl_customer_item_dtls.production_type_category_id = tbl_style.product_subcategory_id
                LEFT JOIN tbl_customer_credit_period ON tbl_customer_credit_period.customer_id = tbl_customer_item_dtls.cust_id
                WHERE tbl_customer_item_dtls.cust_id = ?
                    AND tbl_hm_gcd.branch_id = ?
                    AND tbl_hm_gcd.barcode IN ($barcode_list)
                    AND tbl_hm_gcd.gcd_status = 1
                    AND tbl_hm_gcd.finished_good_status = 3
                    AND tbl_hm_gcd.status != 5
                    AND tbl_hm_gcd.status != 3
                    AND tbl_hm_gcd.gcd_out_status = 0;
            ";
    
            // Execute the query with vendor_id and branch_id as parameters
            $query = $this->db->query($sql_query, array($vendor_id, $branch_id));
            return $query->result_array();
        }
        // Function to check item names in tbl_style
        public function get_highlighted_items($results) {
            $item_names = array_column($results, 'item_name');
            if (!empty($item_names)) {
                $this->db->select('item_name');
                $this->db->from('tbl_style');
                $this->db->where_in('item_name', $item_names);
                $query = $this->db->get();
                return $query->result_array();
            }
            return [];
        }
    }
    // $this->db->select('tbl_order_creation.`order_id`,tbl_order_creation.`order_no`,tbl_order_creation.`customer_id`,tbl_customer.cust_name')
    // ->from('tbl_order_creation'); ;
    // $this->db->join('tbl_customer', 'tbl_customer.cust_id = tbl_order_creation.customer_id', 'left'); 
    // $this->db->where("tbl_order_creation.`order_id` NOT IN (SELECT `order_id` FROM tbl_billing) ");
    // $query=$this->db->get();    // print_r($this->db);exit;
    // return $query->result_array();