<?                
    class maintenanceModel
    {
        public $maintenance_id; 
        public $maintenance_date; 
        public $technician_id; 
        public $job_id; 
        public $lift_ids; 
        public $service_area_ids; 
        public $service_type_ids; 
        public $task_ids; 
        public $maintenance_notes; 
        public $completed_id; 
        public $maintenance_toa; 
        public $maintenance_tod; 
        public $docket_no; 
        public $order_no; 
        public $customer_signature; 
        public $technician_signature; 
        public $updated; 
        public $user_id; 
        public $is_printed; 
        public $notify_email;
        public $list;

        function create()
        {
            return db::query("INSERT INTO maintenance (maintenance_date,technician_id,job_id,lift_ids,service_area_ids,service_type_ids,task_ids,maintenance_notes,completed_id,maintenance_toa,maintenance_tod,docket_no,order_no,customer_signature,technician_signature,updated,user_id,is_printed,notify_email) VALUES (0$this->maintenance_date,0$this->technician_id,0$this->job_id,'$this->lift_ids','$this->service_area_ids','$this->service_type_ids','$this->task_ids','$this->maintenance_notes',0$this->completed_id,0$this->maintenance_toa,0$this->maintenance_tod,'$this->docket_no','$this->order_no','$this->customer_signature','$this->technician_signature',0$this->updated,0$this->user_id,0$this->is_printed,'$this->notify_email');");
        }
        
        function read($id)
        {
            $maintenance = db::query("select * from maintenance inner join jobs on maintenance.job_id = jobs.job_id 
            where maintenance_id = $id");     
                       
            if($maintenance){
                $maintenance = $maintenance[0]; 
                $this->maintenance_id = $maintenance['maintenance_id'];
                $this->maintenance_date = $maintenance['maintenance_date'];
                $this->technician_id = $maintenance['technician_id'];
                $this->job_id = $maintenance['job_id'];
                $this->lift_ids = $maintenance['lift_ids'];
                $this->service_area_ids = $maintenance['service_area_ids'];
                $this->service_type_ids = $maintenance['service_type_ids'];
                $this->task_ids = $maintenance['task_ids'];
                $this->maintenance_notes = $maintenance['maintenance_notes'];
                $this->completed_id = $maintenance['completed_id'];
                $this->maintenance_toa = $maintenance['maintenance_toa'];
                $this->maintenance_tod = $maintenance['maintenance_tod'];
                $this->docket_no = $maintenance['docket_no'];
                $this->order_no = $maintenance['order_no'];
                $this->customer_signature = $maintenance['customer_signature'];
                $this->technician_signature = $maintenance['technician_signature'];
                $this->updated = $maintenance['updated'];
                $this->user_id = $maintenance['user_id'];
                $this->is_printed = $maintenance['is_printed'];
                $this->notify_email = $maintenance['notify_email'];
                return true;
            }
           
            return false;
        }
        
        function update()
        {
            $query = "UPDATE maintenance SET
                      maintenance_date = 0$this->maintenance_date,        
                      technician_id = 0$this->technician_id,        
                      job_id = 0$this->job_id,        
                      lift_ids = '$this->lift_ids',        
                      service_area_ids = '$this->service_area_ids',        
                      service_type_ids = '$this->service_type_ids',        
                      task_ids = '$this->task_ids',        
                      maintenance_notes = '$this->maintenance_notes',        
                      completed_id = 0$this->completed_id,        
                      maintenance_toa = 0$this->maintenance_toa,        
                      maintenance_tod = 0$this->maintenance_tod,        
                      docket_no = '$this->docket_no',        
                      order_no = '$this->order_no',        
                      customer_signature = '$this->customer_signature',        
                      technician_signature = '$this->technician_signature',        
                      updated = 0$this->updated,        
                      user_id = 0$this->user_id,        
                      is_printed = 0$this->is_printed,
                      notify_email = '$this->notify_email'     
                      WHERE maintenance_id = $this->maintenance_id
            ";
            return db::query($query);
        }
        
        function delete($id = null)
        {
            if($id)
                return db::query("delete from maintenance where maintenance_id = $id");
            return db::query("delete from maintenance where maintenance_id = $this->maintenance_id");
        }

        function readAll($where)
        {
            $this->list = db::query("select * from maintenance
                    inner join jobs on maintenance.job_id = jobs.job_id
                    inner join technicians on maintenance.technician_id = technicians.technician_id 

                    $where
                    ");
        }        
    }
?>