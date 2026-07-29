<?php 
  $CI = &get_instance();
  $CI->load->model('Salescrm_model', 'salescrm');
  $get_conveyance_voucher = $CI->salescrm->get_conveyance_voucher($this->uri->segment(3));
// print_r($get_conveyance_voucher);
$restey=$this->db->select('convence,convence_type,convence_rate,first_name,last_name')->from('system_users')->where('user_id',$get_conveyance_voucher->user_id)->get();
if($restey->num_rows()>0)
{
  foreach($restey->result() as $row);
  $convence=$row->convence;
  $convence_type=$row->convence_type;
  if($convence_type==1)
  {
    $km="readonly";
    $km1='';
  }else if($convence_type==2)
  {
    $km1="readonly";
    $km='';

  }else
  {
    $km1='';
    $km='';
  }

  $convence_rate=$row->convence_rate;
  $fname=$row->first_name;
  $lname=$row->last_name;

  $ro=$this->db->select('end_read')->from('employee_convence')->where('user_id',$get_conveyance_voucher->user_id)->order_by('id','DESC')->limit(1)->get();
  if($ro->num_rows()>0)
  {
    foreach($ro->result() as $ro1);
    $last_read=$ro1->end_read;

  }else{

    $last_read=0;
  }

  $resteyy=$this->db->select('convence_date,send_for_approval_On')->from('employee_convence')->where('send_for_approval',1)->where('user_id',$get_conveyance_voucher->user_id)->order_by('convence_date','DESC')->limit(1)->get();
  if($resteyy->num_rows()>0)
  {
    foreach($resteyy->result() as $restte);

    $lastmonth=date('m',strtotime($restte->convence_date));
  }else
  {
    $lastmonth=0;
  }

}else
{
  echo "Invalid Link"; exit;
}

    $start_date =date('Y-m-d',strtotime($this->uri->segment(3)));   
    $end_date =date('Y-m-d',strtotime($this->uri->segment(4))); 
    $user_id =$this->uri->segment(5);   

    $query = $this->db->select('a.*,b.first_name,b.last_name,b.convence_type')->from('employee_convence a')->join('system_users b','a.user_id=b.user_id')->where('a.convence_date>=',$start_date)->where('a.convence_date<=',$end_date)->where('a.user_id',$user_id)->order_by('a.convence_date','desc')->get();
    if($query->num_rows()>0)
    {
        
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">
    <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
    <title><?php echo sitetitle; ?> Local Conveyance Form</title>

        <!-- Table Responsive css -->
    <script src="<?php echo assets_url;?>js/angular.min.js"></script>
     <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
    <link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
    <link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <link href="<?php echo assets_url;?>plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
    <style>
    table.manglesh thead th {
    background: #003366;
    color:#fff;
    font-weight:bold;
    }

    #pageloader
    {
    background: rgba( 255, 255, 255, 0.8 );
    display: none;
    height: 100%;
    position: fixed;
    width: 100%;
    z-index: 9999;
    }
    #pageloader img
    {
    left: 30%;
    margin-left: -10px;
    margin-top: -10px;
    position: absolute;
    top: 30%;
    }
    </style>
    </head>


    <body>


        <!-- Navigation Bar-->
        <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>
        <!-- End Navigation Bar-->


        <div class="wrapper">
            <div class="container">

                <!-- Page-Title -->
                <div class="row" style="padding-top:20px">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="btn-group pull-right">
                              
                            </div>
                            <h4 class="page-title">Conveyance Voucher Edit</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->


                <div class="row" >
                    <div class="col-xs-12">
                        <div class="card-box">
            <?php 
                $first_name =$fname;
                $last_name =$lname;
                ?>
                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">
                <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
                    <form id="loginForm" method="post" action="<?php echo page_url;?>Sales/update_conveyance_voucher_new_admin/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(6);?>" enctype="multipart/form-data">
                      <input type="hidden" name="convence_rate" value="<?php echo $convence_rate;?>">
                      <input type="hidden" name="convence_type" value="<?php echo $convence_type;?>">
                      <div id="pageloader">
                                    <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
                                    </div>

                                    <div class="row">

                                     <div class="col-md-3">
                          <div class="form-group">
                             <label for="field-2" class="control-label">YOUR NAME</label>
                             <span id="error_machine" style="color:red;">*</span>
                             <input type="text" class="form-control" name="yourname" id="yourname" value="<?php echo $first_name;?> <?php echo $last_name;?>" readonly>
                          </div>
                        </div>

                         <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">DATE</label>
                            <span id="error_date" style="color:red;">*</span>
                             <input type="text" id="date" name="date" class="form-control datepicker" value="<?php echo date('d-m-Y',strtotime($get_conveyance_voucher->convence_date)) ; ?>" onchange="checkdate();" max="<?php echo date('Y-m-d');?>" autocomplete="off" required="">
                                                    </div>
                                                </div>

                        
                            <input type="hidden" name="conveyance_voucher_id" value="<?php echo $get_conveyance_voucher->id; ?>">

                                            </div>
                        
                      

                                            <div class="row">
                                              <div class="col-md-4"></div>
                                                  <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Start Reading For the Day</label>
                            <span id="error_proceed_to" style="color:red;">*</span>
                                                        <input type="number" class="form-control" id="start_read" name="start_read" style="text-transform: uppercase;" autocomplete="off" placeholder="" required="" onblur="checkinitial_read();" value="<?php echo $get_conveyance_voucher->start_read ;?>">
                                                        <span style="color:red;font-weight: bold;font-size:22px;">Last End Reading <?php echo $last_read;?></span>
                                                    </div>
                                                </div>
                                                <script type="text/javascript">
                                                  // function checkinitial_read()
                                                  // {
                                                  //  var start_read=$("#start_read").val();
                                                  //  var last_read="<?php //echo $last_read;?>";
                                                  //  if(parseInt(start_read)<parseInt(last_read))
                                                  //  {
                                                  //    alert('Start Reading Cannot be less than last end reading');
                                                  //    $("#start_read").val('');
                                                  //  }
                                                  // }
                                                </script>
                                               

                                                  <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">End Reading For the Day</label>
                            <span id="error_proceed_to" style="color:red;">*</span>
                                                        <input type="number" class="form-control" id="end_read" name="end_read" style="text-transform: uppercase;" autocomplete="off" placeholder="" required="" onblur="check_reading();" value="<?php echo $get_conveyance_voucher->end_read;?>">
                                                    </div>
                                                </div>
                                            </div>
                                             <script>
                                                  function check_reading()
                                                  {
                                                    var start_read=$("#start_read").val();
                                                    var end_read=$("#end_read").val();
                                                    
                                                    if(parseInt(start_read)>parseInt(end_read))
                                                    {
                                                      alert('Start Reading Cannot be less than End Reading');

                              $("#start_read").val('');
                              $("#end_read").val('');
                                                    
                                                    }
                                                  }
                                               </script>
                            
                        
                        
                        
                      
                    
                    <div class="col-md-9"></div>
                    <div class="col-md-3">
                      <div class="form-group pull-right" style="padding-top:24px;">
                        <label>&nbsp;</label>
                        <input type="submit" id="save" class="btn btn-success" value="Save">
                      </div>
                    </div>
                  
                  </form>
                                   

                                </div>

                            </div>
                            <!-- end row -->
                        </div> <!-- end ard-box -->
                    </div><!-- end col-->



                </div>
                <!-- end row -->





                <!-- Footer -->
              <?php $this->load->view('common/footer');?>
                <!-- End Footer -->

            </div> <!-- end container -->
        </div>
        <!-- end wrapper -->


         <!-- jQuery  -->
        <script src="<?php echo assets_url;?>js/jquery.min.js"></script>
        <script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>
        <script src="<?php echo assets_url;?>js/detect.js"></script>
        <script src="<?php echo assets_url;?>js/fastclick.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.slimscroll.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.blockUI.js"></script>
        <script src="<?php echo assets_url;?>js/waves.js"></script>
        <script src="<?php echo assets_url;?>js/wow.min.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.nicescroll.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.scrollTo.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
     $(document).ready(function(){
      getrate();

      if($('#mode').val() == '2') {
      $('#ownconyenace').show();
      $('#publicconveyance').hide();
      } else {
      $('#publicconveyance').show(); 
      $('#ownconyenace').hide();
      
      } 
  var i=1;
 $('#addmore_btn').click(function(){

 i++;
 $('#dynamictasks').append('<div id="row'+i+'" class="row"><div class="col-md-3"><div class="form-group"><label>TRAVEL EXPENSE</label><select class="form-control" name="travel_expense[]" id="travel_expense" onChange="fetch_expense_options(0);" ><option value="">SELECT EXPENSE TYPE</option><?php $query =$this->db->select('id, options')->from('conveyance_type_options')->where('conveyance_type_id','2')->where('status','1')->get();foreach($query->result() as $row){?><option value="<?php echo $row->id;?>"><?php echo $row->options;?></option><?php }?></select></div></div><div class="col-md-2"><div class="form-group"><label>FROM</label><input type="text" class="form-control" name="destination[]" id="destination" value=""></div></div><div class="col-md-2"><div class="form-group"><label>TO</label><input type="text" class="form-control" name="source[]" id="source" value=""></div></div><div class="col-md-2"><div class="form-group"><label>AMOUNT</label><input type="number" class="form-control" name="travel_expense_amount[]" id="travel_expense_amount" value="" step="0.2" ></div></div><div class="col-md-2"><div class="form-group"><label>BILL ATTACHMENT</label><input type="file" class="form-control" name="bill_attachment[]" id="bill_attachment" value="" ></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:30px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div><br/>');
 
 });
 
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id+'').remove();
 });
 
});
    </script>
<script language="javascript" type="text/javascript">   
$(document).ready(function() {
$("#save").click(function() {
  
var date = $("#date").val();
if(date=='')
{
  $("#error_date").html('Required!');
}
var from = $("#from").val();
if(from=='')
{
  $("#error_from").html('Required!');
}
var proceed_to = $("#proceed_to").val();
if(proceed_to=='')
{
  
  $("#error_proceed_to").html('Required!');
}

var mode = $("#mode").val();
if(mode=='')
{
  
  $("#error_mode").html('Required!');
}


if(date=='' || from=='' || proceed_to=='' || mode=='')
{
  
  return false;
}

});
});





  

</script>

   <!-- Datatables-->
        <script src="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.bootstrap.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.buttons.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/jszip.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/pdfmake.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/vfs_fonts.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.html5.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.print.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.fixedHeader.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.keyTable.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.responsive.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.scroller.min.js"></script>
<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(){


});//document ready
</script>

      <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
    <script>
        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy',
            endDate: '+0d',
            autoclose: true,
            todayHighlight: true
        });

       
    </script>
      </body>
</html>