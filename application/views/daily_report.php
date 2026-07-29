<?php
// $uri=$this->uri->segment(3);
// if($uri=='')
// {
//   echo "Invalid Access"; exit;
// }else
// {
//         $rest=$this->db->select('d.conversion_unit,d.conversion_weight,d.size_in_mm,a.remarks,a.prno,a.potype,a.approved,d.gst,a.source,a.sourceid,a.jobcard,d.part as machine_part,d.fincode,d.specification,d.hsn,e.first_name,e.last_name,a.itemid,a.qty,e.department_id,a.vendor,a.price,a.addedOn as orderdate,a.unit,a.discount,a.approvedBy')->from('purchase_order_view a')->join('machine_parts_with_picture_view d','d.id=a.itemid')->join('system_users_view e','e.user_id=a.addedBy')->where('a.pono',$this->uri->segment(3))->order_by('d.part','ASC')->get();
        


// }
$CI =& get_instance();
$CI->load->model('Daily_report_model');
$user_id =$this->session->userdata['logged_in']['user_id'];
$create_date = '2022-10-15';
$visit_counts_userwise_today = $CI->Daily_report_model->visit_counts_userwise_today($user_id ,$create_date );
$agent_list = $CI->Daily_report_model->visit_agent_list($user_id );
?>
<!DOCTYPE html>
<html>
<head>
<title>PURCHASE ORDER</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700" rel="stylesheet">
<link rel="stylesheet" href="<?php echo assets_url;?>css/invoice/style.css">

<style type="text/css" media="print">
  @page {  size: A4;
   margin: 0mm 0mm 0mm 0mm; margin-bottom:0mm;}
   

.printhide
{
  display:none;
}


.col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12 {
        float: left;
   }
   .col-sm-12 {
        width: 100%;
   }
   .col-sm-11 {
        width: 91.66666667%;
   }
   .col-sm-10 {
        width: 83.33333333%;
   }
   .col-sm-9 {
        width: 75%;
   }
   .col-sm-8 {
        width: 66.66666667%;
   }
   .col-sm-7 {
        width: 58.33333333%;
   }
   .col-sm-6 {
        width: 50%;
   }
   .col-sm-5 {
        width: 41.66666667%;
   }
   .col-sm-4 {
        width: 33.33333333%;
   }
   .col-sm-3 {
        width: 25%;
   }
   .col-sm-2 {
        width: 16.66666667%;
   }
   .col-sm-1 {
        width: 8.33333333%;
   }
    html, body {
        height: auto;    
    }


}


</style>
<style type="text/css">
  .w250 tr th{
      width: 250px;
    }
</style>
</head>

<body>

<div class="container printhide" style="margin-top:20px;margin-bottom:20px">
    <div class="row text-center">
    <span class="btn btn-success" style="text-align:center" onclick="printDiv(0);">PRINT PO</span>    
        
    </div>
    
</div>        
    <page size="A4">
      <div class="pageinside-invoice" id="poinvoice">
      <div class="row">
                <div class="col-sm-12">
          <h3 style="text-align:center;font-weight: 700;margin:8px 0;margin-bottom: 0px;"><!--<img src="http://localhost/prestomitr/assets/logo.png" alt="" height="50">-->
          HPCL PVT. LTD.</h3>
          <p class="address uppercase">I-42, DLF INDUSTRIAL Area, PHASE-1
<br/>FARIDABAD HARYANA-121003<br/>PHONES: 0129-4272727 / 0129-4083111<br/>Email: purchase@prestogroup.com<br/><strong>GSTN:  06AAACP9778K1ZR</strong></p>
        </div>
      </div>
      
      <div class="row">
        <div class="col-sm-12">
          <table class="table tablenoborder" style="border-bottom:2px solid #333;margin-bottom:0">
            <thead>
              <tr class="border">
                <th colspan="2" style="font-size: 17px;">DAILY REPORT <div id="potypees"></div></th>
              </tr>
            </thead>
            

            <tbody>
              <tr>
                <td class="w50" style="border-right:1px solid #333">
                  <table class="tablepodetail w250" style="text-align:left">
                    <tr>
                      <ol>
                        <li>Today Visits</li>
                      </ol>

                      <ol>
                        <li>Today Visits</li>
                      </ol>

                    </tr>
                     <!--  <th style="text-align:left;width:250px">Today Visits</th>
                      <td style="text-align:left">: <?php echo $visit_counts_userwise_today;?>
                        <?php
                        if($agent_list){
                          echo "<ul>";
                          foreach($agent_list as $agent){
                            echo "<li>$agent->first_name</li>";
                          }
                          echo "</ul>";
                        }


                        ?>
                        </td>
                    </tr>
                    
                        <tr>
                      <th style="text-align:left;width:250px">Todays Absent</th>
                      <td style="text-align:left">aaa</td>
                      </tr>
                                            
                                            
                      <tr>
                      <th style="text-align:left">Todays early going</th>
                      <td style="text-align:left">:</td>
                                            </tr>
                                              <tr>
                      <th style="text-align:left">Total Dispatched with value</th>
                      <td style="text-align:left">:</td>
                     </tr>
                      <tr>
                      <th>Pending Orders value</th>
                      <td style="text-align:left">: </td>
                    </tr>
                      <tr>
                      <th>Payment Overdue count with value</th>
                      <td style="text-align:left">: </td>
                    </tr>
                      <tr>
                      <th>Samples Collected</th>
                      <td style="text-align:left">: </td>
                    </tr>
                      <tr>
                      <th>New Customer Add</th>
                      <td style="text-align:left">: </td>
                    </tr> -->
                    
                    
                    
                  
                    
                  </table>
                </td>
               <!--  <td class="w50">
                  <table class="tablepodetail w120">
                    <tr>
                      <th style="text-align:left">PO NO.</th>
                      <td style="text-align:left">: PRESPO22556</td>
                    </tr>
                      <tr>
                      <th>ORDER DATE</th>
                      <td style="text-align:left">: 07-01-2023</td>
                    </tr>
                    
                    <tr>
                      <th>SOURCE</th>
                      <td style="text-align:left">: Jobcard-</td>
                    </tr>
                    
                  
                    
                  </table>
                </td> -->
              </tr>
            </tbody>
          </table>

          <!--<table class="table tablenoborder" style="border-top:0;border-bottom:2px solid #333;margin-bottom:0px">
            <tbody>
                          <tr>
                <td style="width:100%;font-size: 16px; font-weight: 500;text-align:left;padding-top:0px;padding-bottom:0px; ">
                  <u>Delivery at</u><br>
                  SK EXPORTS<br>
                  <span style="font-size: 14px;">D-5/3, OKHLA
PHASE-2
NEW DELHI-110020</span><br>
                  GSTIN: 07ACLFS3577R1Z2                </td>
                              </tr>
            </tbody>
          </table>-->
                      <div class="col-md-12" style="background-color:#457ee95c;padding:2px 0px 2px 0px;font-weight:bold;text-align:center;">Item Details </div>

        
        </div>
              

        
        
        <div class="col-sm-5">
            
        </div>
      <div class="col-sm-1"></div>
      <br/><br/><br/><br/>
      
      
        </div>
    </page>
  
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
   <!--<script type="text/javascript" src="http://localhost/prestomitr/assets/js/html2canvas.js"></script>-->
<script>

function printDiv(status) 
{
if(status!=1)
  {
    alert('Po cannot be printed since it is unapproved');
    return false;
  }else{

  window.print();
  
  }

}
  
  /*$( document ).ready(function() {
    
  

var pono="PRESPO22556";
html2canvas(document.getElementById('poinvoice')).then(function(canvas) {
var base64URL = canvas.toDataURL('image/png').replace('image/png', 'image/octet-stream');


// AJAX request
$.ajax({
url: 'http://localhost/prestomitr/index.php/Store/savepoimage',
type: 'post',
data: {image: base64URL,ponumber:pono},
success: function(data){
}
});
});



    
  //  window.print();
  });*/
</script>
  </body>

</html>
