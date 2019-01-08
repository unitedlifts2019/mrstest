<?ob_start();?>
  <html>
  <style>
    a {
      color: #0087C3;
      text-decoration: none;
    }

    body {
      position: relative;
      margin: 0 auto;
      color: #555555;
      background: #FFFFFF;
      font-family: sans-serif;
      font-size: 12px;
      font-family: SourceSansPro;
      width: 21cm;
      height: 29.7cm;
    }

    header {
      padding: 10px 0;
      margin-bottom: 20px;
      border-bottom: 1px solid #AAAAAA;
    }

    #logo {
      float: left;
    }

    #logo img {
      margin-top: 8px;
      float: left;
    }

    #company {
      text-align: right;
      margin-right: 80px;
      margin-top: 8px;
    }


    #details {
      margin-bottom: 50px;
    }

    #client {
      padding-left: 6px;
      border-left: 6px solid #0087C3;
      float: left;
    }

    #client .to {
      color: #777777;
    }

    h2.name {
      font-size: 1.4em;
      font-weight: normal;
      margin: 0;
    }

    #invoice {
      padding-right: 6px;
      text-align: right;
      margin-right: 80px;
    }

    #invoice h1 {
      color: #0087C3;
      font-size: 2.4em;
      line-height: 1em;
      font-weight: normal;
      margin: 0 0 10px 0;
    }

    #invoice .date {
      font-size: 1.1em;
      color: #777777;
    }

    #line {
      height: 1px;
      width: 100%;
      background-color: #0087C3;
      margin-bottom: 10px;
      position: absolute;
      bottom: 10px
    }

    fotter {
      position: absolute;
      bottom: 19px;
    }
  </style>

  <body>
    <header class="clearfix">
      <div id="logo">
        <img src="<?=URL?>/public/img/logo.png">
      </div>
      <div id="company">
        <div>Unit 3 / 260 Hyde St YARRAVILLE VIC 3013
        </div>
        <div>03 9687 9099</div>
        <div>
          info@unitedlifts.com.au
        </div>
      </div>
      </div>
    </header>
    <main>
      <div id="details" class="clearfix">
        <div id="client">
          <div class="to">Customer Details:</div>
          <h2 class="name">
            <?=$jobs->model->job_name?>
          </h2>
          <div class="address">
            <?=$jobs->model->job_address_number?>
              <?=$jobs->model->job_address?>
                <?=$jobs->model->job_suburb?>
          </div>
          <div class="email">
            Contract No: <?=$jobs->model->job_number?>
          </div>
        </div>
        <div id="invoice">
          <h1>Maintenance:
            <?=$this->model->maintenance_id?>
          </h1>
          <div class="date">Date of Call:
            <?=date(toDate($this->model->maintenance_date))?>
              </p>
          </div>
          <div class="date">Time of Arrival:
            <?=toTime($this->model->maintenance_toa)?>
              </p>
          </div>
          <div class="date">Time of Departure:
            <?=toTime($this->model->maintenance_tod)?>
              </p>
          </div>
        </div>
      </div>
      <table>
        <tr>
          <td colspan="2">
            <div style="border:0px solid black;height:300px;padding:10px;">
              <p>
                <b style="color:#0087C3">
                  <u>Maintenance Details</u>
                </b>
              </p>

              <p>
                <b>Maintenance Description: </b>
                <?=$this->model->maintenance_notes?>
              </p>

              <div style="height:1px;width:100%;background-color:#0087C3;margin-bottom:10px;position:center;"></div>
				
              <table width="100%" border="1" style="border-collapse:collapse">
                <tr>
                  <th>Maintenance Task</th>
                  <?foreach($lifts as $lift){?>
                    <th>
                      <?=$lift['lift_name']?>
                    </th>
                    <?}?>
                </tr>
                <?
                  //Manual entry here because my brain is not fucking working today. Needs to be done tho
                  $tasks = trim($this->model->task_ids,"|");
                  $tasks = explode("|",$tasks);
              ?>

                  <?foreach($tasks as $task){?>
                    <?$task_name = get_query("select * from _new_tasks where task_id =".$task);?>
                      <?$task_name = $task_name[0]?>
                        <tr>
                          <td>
                            <?=$task_name["task_name"]?>
                          </td>
                          <?foreach($lifts as $lift){?>
                            <td>
                              <?if (strstr($this->model->lift_ids,$lift['lift_id'])){?>
                                Y
                                <?}?>
                            </td>
                            <?}?>
                        </tr>
                        <?}?>


              </table>
	
					

              <table style="margin-top:80px;">
                <tr>
                  <td style="width:300px">
                    <p>
                      <b>Service Technician</b>
                    </p>
                    <?=$user["realname"]?>
                  </td>
                  <td style="width:300px">
                    <p>
                      <b>Customer Email</b>
                    </p>
                    <?=$jobs->model->job_email?>
                  </td>
                </tr>
              </table>
    </main>
    <div id="line"></div>
    <fotter>
      Thanks for choosing  United Lifts Services! 24 Hour Service, Phone 1300161740
    </fotter>
  </body>

  </html>
  <?
	$contents = ob_get_contents();
	ob_end_clean();
    
	require_once("dompdf/dompdf_config.inc.php");
	$dompdf = new DOMPDF();
	$dompdf->load_html($contents);
	$dompdf->render();
  $file_location = $_SERVER['DOCUMENT_ROOT']."/melbournemrs/functions/pdfReports/".$this->model->maintenance_date.".pdf";
  file_put_contents($file_location, $dompdf->output());
  header('Location: ' . $_SERVER['HTTP_REFERER']);
?>