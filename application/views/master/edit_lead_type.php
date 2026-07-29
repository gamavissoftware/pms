<?php 
$CI =& get_instance();
$CI->load->model('Master_model');
$getLeadStageDetails = $CI->Master_model->getLeadStageDetails($this->uri->segment(4));

if ($getLeadStageDetails != '') {
    foreach ($getLeadStageDetails as $row);
    $lead_name = $row->lead_name;
    $sort_order = $row->sort_order;
    $quotation_step = $row->quotation_step;
    $quotation_revised_step = $row->quotation_revised_step;
    $pi_step = $row->pi_step;
    $pi_revised_step = $row->pi_revised_step;
    $conversion_step = $row->conversion_step;
    $comparison_report = $row->comparison_report;
    $reason = $row->reason;
    $followup_date = $row->followup_date;
      $userrole = $row->user_role;
    $dead_end = $row->dead_end;
    $status = $row->status;
    $sample = $row->sample;
    $trail = $row->trail;
    if($row->icon!==''){
      $icon = '<img src="'.ASSETSPATH.'lead_stage_icons/'.$row->icon.'" width="50px">';  
  }else{
    $icon="";
  }
    
    $mis=$row->mis;
    $tat_type=$row->tat_type;
    $day_time=$row->day_time;
    $day_text=$row->day_text;
    $time_text=$row->time_text;
} else {
    $lead_name = '';
    $sort_order = '';
    $quotation_step = 0;
    $quotation_revised_step = 0;
    $pi_step = '';
    $pi_revised_step = '';
    $conversion_step = '';
    $comparison_report = '';
    $reason = '';
    $followup_date = '';
    $userrole=0;
    $dead_end = '';
    $status = '';
    $icon = '';
    $mis=0;
    $tat_type=0;
    $day_time=0;
    $day_text='';
    $time_text='';
    $trail='';
    $sample='';
}

// $mode = $CI->Master_model->getsettings();
// if(count($mode)>0)
// {
// $mode=$mode['mode'];
// }else
// {
//     $mode='';
// }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright;?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle;?></title>

      
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

    </head>


    <body>
    <!-- Navigation Bar-->
    <header id="topnav">
      <?php $this->load->view('common/nav-menu');?>
    </header>
    <!-- End Navigation Bar-->


    <div class="wrapper">
        <div class="container">
            <div class="row" style="margin-top:20px;">
                <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                    <div class="page-title-box">
                        <h4 class="page-title text-center">Edit Lead Stage</h4>
                    </div>
                </div>
            </div>

       <div class="row">
            <div class="col-xs-12">
                <div class="card-box">
                <form id="loginForm" method="post" action="<?php echo page_url;?>Master/Lead_Type/update_lead_type/<?php echo $this->uri->segment(4);?>" enctype="multipart/form-data">
                    <input type="hidden" name="mode" value="">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12 col-md-12">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                             <label for="field-2" class="control-label">Lead Stage Name<span style="color:red;">*</span></label>
                                             <input type="text" class="form-control" name="lead_type" id="lead_type"  value="<?php echo $lead_name;?>" <?php if($sort_order==1){?> readonly <?php } ?> required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="field-2" class="control-label">Sort Order<span style="color:red;">*</span></label><br>
                                            <input type="number" class="form-control" name="sort_order" id="sort_order" onblur="checkifsortnoexists()" value="<?php echo $sort_order;?>" <?php if($sort_order==1){?> readonly <?php } ?> required>
                                        </div>
                                    </div>

                                    <?php 

                                    // if($mode==2)
                                    // { ?>
                                    <div class="col-md-3">

                                    <div class="form-group">
                                    <label for="field-2" class="control-label">User Role<span style="color:red;">*</span></label><br>
                                    <select class="form-control" name="user_role" id="user_role" required="">
                                    <option value="">Please Select</option>
                                    <?php
                                    $res=$this->db->select('user_role_id,user_role')->from('user_role')->where('status',1)->get();
                                    if($res->num_rows() >0)
                                    {
                                    foreach($res->result() as $row11){                             
                                    ?>
                                    <option value="<?php echo $row11->user_role_id ?>" <?php if($row11->user_role_id==$userrole){ echo "selected"; }
                                    ?>><?php echo $row11->user_role;?></option>
                                    <?php }}?>
                                    </select> 
                                    </div>
                                    </div>
                                    <?php //} ?>

                                       <div class="col-md-3" style="display: none;">
                                        <div class="form-group">
                                             <label for="field-2" class="control-label">Upload Icon</label><br>
                                             <input type="hidden" name="old_icon" value="<?php echo $row->icon;?>" >
                                             <input type="file" name="icon" id="icon" class="form-control">
                                             <?php echo $icon;?>
                                        </div>
                                    </div>



                                    <?php
                                    if($sort_order==1)
                                    {
                                        $disno="style='display:none';";
                                    }else
                                    {
                                        $disno="";
                                    }
                                    ?>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                             <label for="field-2" class="control-label">Show in comparison report?</label><br>
                                             <input type="checkbox" name="comparison_report" id="comparison_report" value="1" <?php if($comparison_report == 1) {echo 'checked';}?>>
                                        </div>
                                    </div>

                                   
                                    <div class="col-md-3">
                                        <div class="form-group">
                                             <label for="field-2" class="control-label">Quotation step?</label><br>
                                             <input type="checkbox" name="quotation_step" id="quotation_step" value="1" <?php if($quotation_step == 1) {?> checked <?php } ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                             <label for="field-2" class="control-label">Quotation Revised step?</label><br>
                                             <input type="checkbox" name="quotation_revised_step" id="quotation_revised_step" value="1" <?php if($quotation_revised_step == 1) {?> checked <?php }?>>
                                        </div>
                                    </div>
                                     <div class="col-md-3" <?php echo $disno;?>>
                                        <div class="form-group">
                                             <label for="field-2" class="control-label">Conversion Step?</label><br>
                                             <input type="checkbox" name="conversion_step" id="conversion_step" value="1" <?php if($conversion_step == 1) {echo 'checked';}?>>
                                        </div>
                                    </div>
                                    <div class="col-md-3" <?php echo $disno;?>>
                                        <div class="form-group">
                                             <label for="field-2" class="control-label">Reason?</label><br>
                                             <input type="checkbox" name="reason" id="reason" value="1" <?php if($reason == 1) {echo 'checked';}?>>
                                        </div>
                                    </div>
                                     <div class="col-md-3" >
                                        <div class="form-group">
                                             <label for="field-2" class="control-label">Followup Date?</label><br>
                                             <input type="checkbox" name="followup_date" id="followup_date" value="1" <?php if($followup_date == 1) {echo 'checked';}?>>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                             <label for="field-2" class="control-label">Dead End?</label><br>
                                             <input type="checkbox" name="dead_end" id="dead_end" value="1" <?php if($dead_end == 1) {echo 'checked';}?>>
                                        </div>
                                    </div>


                                    <div class="col-md-3">
                                    <div class="form-group">
                                    <label for="field-2" class="control-label">Sample Req. Step?</label><br>
                                    <input type="checkbox" name="sample" id="sample" value="1" <?php if($sample==1){?> checked <?php } ?>>
                                    </div>
                                    </div>

                                    <div class="col-md-3">
                                    <div class="form-group">
                                    <label for="field-2" class="control-label">Trail Req. Step</label><br>
                                    <input type="checkbox" name="trail" id="trail" value="1" <?php if($trail==1){?> checked <?php } ?>>
                                    </div>
                                    </div>

                                     <div class="col-md-3" style="display:none;">
                                        <div class="form-group">
                                             <label for="field-2" class="control-label">MIS applicable?</label><br>
                                              <input type="checkbox" name="mis" id="mis" value="1"  <?php if($mis == 1) {echo 'checked';}?> onchange="checkMis()">
                                        </div>
                                    </div>

                    <div class="col-md-3" id="tat_type" style="display:none;">
                    <div class="form-group">
                    <label for="field-2" class="control-label">TAT Type</label><br>
                    <select class="form-control" name="tat_type" id="tat" onchange="checkTatType()">
                    <option value="" <?php if($tat_type==0){?> selected <?php } ?>>SELECT</option>
                    <option value="1" <?php if($tat_type==1){?> selected <?php } ?>>Fixed TAT</option>
                    <option value="2" <?php if($tat_type==2){?> selected <?php } ?>>Followup</option>
                    </select>
                    </div>
                    </div>


                    <div class="col-md-3" id="day_time" style="display:none;">
                    <div class="form-group">
                    <label for="field-2" class="control-label">TAT Day/Time WISE?</label><br>
                    <select class="form-control" name="day_time" id="day_times" onchange="checkDayTime();">
                    <option value="" <?php if($day_time==0){?> selected <?php } ?>>SELECT</option>
                    <option value="1" <?php if($day_time==1){?> selected <?php } ?>>Day</option>
                    <option value="2" <?php if($day_time==2){?> selected <?php } ?>>Time</option>
                    </select>
                    </div>
                    </div>

                    <div class="col-md-3" id="days_text" style="display:none;">
                    <div class="form-group">
                    <label for="field-2" class="control-label">Day</label>
                    <input type="text" class="form-control" name="day_text" value="<?php echo $day_text;?>">
                    </div>
                    </div>

                    <div class="col-md-3" id="times_text" style="display:none;">
                    <div class="form-group">
                    <label for="field-2" class="control-label">Time (In Hours)</label>
                    <input type="text" class="form-control" name="time_text" value="<?php echo $time_text;?>">
                    </div>
                    </div>
                                 
                                    <div class="col-md-3" style="display: none;">
                                        <div class="form-group">
                                            <label for="field-2" class="control-label">Status<span style="color:red;">*</span></label><br>
                                            <select class="form-control" id="status" name="status">
                                               <option value="">--Select Status--</option>
                                               <option value="1" <?php if($status == 1) {echo 'selected';}?>>Active</option>
                                               <option value="0" <?php if($status == 0) {echo 'selected';}?>>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-11"></div>
                            <div class="col-md-1 pull-right">
                                <input type="submit" class="btn btn-success" value="Submit">
                            </div>
                        </div>                           
                        </form>

                        </div>

                    </div>
                    
                </div> 
                
 

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

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

       
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script language="javascript" type="text/javascript">   
jQuery.noConflict();
$(document).ready(function() {
$("#save").click(function() {
var lead_type = $("#lead_type").val();
if(lead_type=='')
{
	$("#error_lead_type").html('Required!');
}
var color = $("#color").val();
if(color=='')
{
	
	$("#error_color").html('Required!');
}

var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}



if(lead_type=='' || color=='' || status=='' )
{
	
	return false;
}

});


checkMis();
checkTatType();
checkDayTime();
});
</script>

<script type="text/javascript">
    function checkifsortnoexists() {
        var sort_order = $("#sort_order").val();
            $.ajax({
                type:"post",
                url:"<?php echo page_url;?>Master/Lead_Type/checksortno",
                data:{sort_order: sort_order},
                success:function(data){
                    if (data == 1) {
                        alert('Sort Order No. Already Exits!');
                        $("#sort_order").val('');
                    }
                }
            });
    }


    function checkMis() {

        $("#tat_type").css('display', 'none');

        if($("#mis").is(':checked')) {
    
            $("#tat_type").css('display', '');
        }

    }

    function checkTatType() {
        $("#day_time").css('display', 'none');
        $("#times_text").css('display', 'none');
             $("#day_test").css('display', 'none');
        if($("#tat").val() == 1) {
            $("#day_time").css('display', '');
        } else if($("#tat").val() == 2) {
            $("#day_time").css('display', 'none');
            $("#times_text").css('display', 'none');
             $("#day_test").css('display', 'none');


        }
    }

    function checkDayTime() {
       
        $("#days_text").css('display', 'none');
        $("#times_text").css('display', 'none');
        if($("#day_times").val() == 1) {
        $("#days_text").css('display', '');
        if($("#tat").val() == 1)
        {
            $("#times_text").css('display', 'none');
            } else if($("#day_times").val() == 2){
            $("#days_text").css('display', 'none');
            $("#times_text").css('display', '');
            }

        }
  

    }
</script>
    </body>
</html>