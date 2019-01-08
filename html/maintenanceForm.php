<script>
function showTask(str) {
    if (str == "") {
        document.getElementById("txtHint").innerHTML = "";
        return;
    } else { 
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHint").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET","gettask.php?q="+str,true);
        xmlhttp.send();
    }
}
</script>

<h1>Maintenance</h1>
<p><a href="<?=URL?>/maintenance" class="button-primary">Back to maintenance</a></p>
<label>Maintenance Call for</label>
<h5><?=$jobs->model->job_name?> - <?=$jobs->model->job_address_number?> <?=$jobs->model->job_address?></h5>


<form role="form" action="<?=URL?>/maintenance/action/" method="post">
        <input type="hidden" name="maintenance_id" value="<?=$this->model->maintenance_id?>">
        <input type="hidden" name="technician_id" placeholder="technician_id" class="form-control" value="<?=sess('user_id')?>">
        <input type="hidden" name="job_id" placeholder="job_id" class="form-control" value="<?=$jobs->model->job_id?>">
        <input type="hidden" name="updated" placeholder="updated" class="form-control" value="<?=time()?>">         
        <input type="hidden" name="user_id" placeholder="user_id" class="form-control" value="<?=$jobs->model->job_id?>">

        <label>Staus</label><?dropList('_completed','completed_id','completed_value',$this->model->completed_id);?><br>
        <label>Date: </label><input id="maintenance_date" type="datetime-local" class="date" name="maintenance_date" placeholder="maintenance_date" class="form-control" value="<?=toDateTime($this->model->maintenance_date)?>" <?=$disabled?> required ><br>
        <label>Docket no: </label><input type="text" name="docket_no" placeholder="docket_no" class="form-control" value="<?=$this->model->docket_no?>" <?=$disabled?> required ><br>    
        <label>Order no: </label><input type="text" name="order_no" placeholder="order_no" class="form-control" value="<?=$this->model->order_no?>" <?=$disabled?> required ><br>      
        
        
        <label>Lift ids: </label>
        <input name="liftcheck" id="liftcheck" style="width:1px;height:1px;border:0px;">
        <?checkList('lifts','lift_id','lift_name',$this->model->lift_ids,'where job_id = '.$jobs->model->job_id);?>
        
        
        <label>Toa: </label><input id="toa" type="datetime-local" class="date"  name="maintenance_toa" placeholder="maintenance_toa" class="form-control" value="<?=toDateTime($this->model->maintenance_toa)?>" <?=$disabled?> required ><br>              
        <label>Tod: </label><input id="tod" type="datetime-local" class="date"  name="maintenance_tod" placeholder="maintenance_tod" class="form-control" value="<?=toDateTime($this->model->maintenance_tod)?>" <?=$disabled?> required ><br>           
         <select name="users" onchange="showTask(this.value)">
		  <option value="">Select task:</option>
		  <option value="oldl">Old Lift Task</option>
		  <option value="1monthl">1 Month Lift</option>
		  <option value="1monthe">1 Month Escalator</option>
		  <option value="3monthl">3 Month Lift</option>
		 </select>   
		<?
            $where = 'where type <> "Escalator"';
            if(strstr($jobs->model->job_name,"Escalators"))
            $where = "where type <> 'Lift'";
        ?>
		<br>
		<div id="txtHit"><b>Task info will be listed here...</b></div>
        <label>Task ids: </label>
        <input name="taskcheck" id="taskcheck" style="width:1px;height:1px;border:0px;">
        <?checkList('_new_tasks','task_id','task_name',$this->model->task_ids,$where);?> 
        
        <label>Notes: </label><textarea name="maintenance_notes" placeholder="maintenance_notes" class="form-control" <?=$disabled?> required ><?=$this->model->maintenance_notes?></textarea><br>         
        
        <label>Customer signature:</label>
        <input type="hidden" id="customer_signature" name="customer_signature" type="text" placeholder="customer_signature"  value="<?=$this->model->customer_signature?>" <?=$disabled?> required >       
        <?if($this->model->customer_signature == ''){?>
            <div id="cust_sig_box" style="border:1px solid #666"></div>
        <?}else{?>
            <img src="<?=$this->model->customer_signature?>">
        <?}?>
        
        <!-- <label>Technician signature: </label>
        <input type="hidden" id="technician_signature" name="technician_signature" type="text" placeholder="technician_signature"  value="<?=$this->model->technician_signature?>" ><br>
        <?if($this->model->technician_signature == ''){?>
            <div id="tech_sig_box" style="border:1px solid #666;"></div>
        <?}else{?>
            <img src="<?=$this->model->technician_signature?>">
        <?}?>         -->
        
        <label>Notify Email: </label><input id="notify_email" autocomplete="off" <?=$disabled?>  type="text" name="notify_email" placeholder="notify_email"  value="<?=$this->model->notify_email?>"><br>
        <label>Technician Email: </label><?=$user_email?><br>
		<input type="submit" class="button-primary" value="Save" <?=$disabled?> required >
</form>
<script>
    $(document).ready(function(){
        if(!isMobile()){
			$('#maintenance_date').datetimepicker({ dateFormat: 'dd-mm-yy',timeFormat:'HH:mm:ss',showSecond: false });
			$('#toa').datetimepicker({ dateFormat: 'dd-mm-yy',timeFormat:'HH:mm:ss',showSecond: false });        
			$('#tod').datetimepicker({ dateFormat: 'dd-mm-yy',timeFormat:'HH:mm:ss',showSecond: false });
		}
		
        $("#tech_sig_box").jSignature();
        $("#cust_sig_box").jSignature();
        
        $("#tech_sig_box").bind('change', function(e){ 
            var datapair = $("#tech_sig_box").jSignature("getData", "default");
            $("#technician_signature").val(datapair);
        });

        $("#cust_sig_box").bind('change', function(e){ 
            var datapair = $("#cust_sig_box").jSignature("getData", "default");
            $("#customer_signature").val(datapair);
        });

        //Lift checkbox validation
        $(".chk_lifts").on("click",function(event,ui)
        {
            $("#liftcheck").val(1);
        });   


        //Task checkbox validation
        $(".chk__new_tasks").on("click",function(event,ui)
        {
            $("#taskcheck").val(1);
        });   
        
    });
</script>

