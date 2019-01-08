<h1>Jobs</h1>
<p><a href="<?=URL?>/jobs" class="button-primary">Back to jobs</a></p>
<h5><?=$this->model->job_name?></h5>
<label>Key access:</label> <?=$this->model->job_key_access?><br>
<div class="row">
    <div class="three columns">
        <label>Visit Frequency:</label> <?=$this->model->frequency_name?><br>
        <label>Agent:</label> <?=$this->model->agent_name?><br>
        <label>Technicians:</label> <?=$this->model->technician_name?><br>
                         
                
        <label>Job Number:</label> <?=$this->model->job_number?><br>  
         
         
        <label>Contact details:</label> <?=$this->model->job_contact_details?><br>
        <label>Email Contacts:</label>
        
        <?foreach(explode(";",$this->model->job_email) as $email){?>           
                <?=$email?><br>
        <?}?>
        <label>Owner details:</label> <?=$this->model->job_owner_details?><br>           
        <label>Group:</label> <?=$this->model->job_group?><br>           
        <label>Round:</label> <?=$this->model->round_name?><br>              
        <label>Agent contact:</label> <?=$this->model->job_agent_contact?><br>       
        <label>Instant Notifications:</label> <?=$this->model->notify_instant?><br>        
    </div>
    <div class="three columns">
        
        <label>Floors:</label> <?=$this->model->job_floors?><br>
<label>Status:</label> <?=$this->model->status_name?><br>        
    </div>
    <div class="six columns">
        <label>Address:</label>
        <?=$this->model->job_address_number?> <?=ucfirst($this->model->job_address)?><br>         
        <?=$this->model->job_suburb?><br>         
        <?mapper($this->model->job_latitude,$this->model->job_longitude,"test")?>
    </div>    
</div>