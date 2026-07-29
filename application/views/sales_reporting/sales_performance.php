<?php 
$CI = &get_instance();
$CI->load->model('Salescrm_model');
$DI = &get_instance();
$DI->load->model('Lead_model');    

$from_month=$this->uri->segment(3);
$to_month=$this->uri->segment(4);
$rest=$this->db->select('lead_name,lead_id')->from('lead_stage')->get();
if($rest->num_rows()>0)
{
$i=1;
foreach($rest->result() as $row)
{
${"current_count_". $row->lead_id}=$CI->Salescrm_model->check_status(0,$row->lead_id,$to_month);
${"previous_count_". $row->lead_id}=$CI->Salescrm_model->check_status(1,$row->lead_id,$from_month);
}

}  

if($this->uri->segment(5)<>'')
{
    if($this->uri->segment(5)=='ALL')
    {
        $users="ALL AGENTS COMBINED";
    }else
    {
        $users=$CI->Salescrm_model->getusername($this->uri->segment(5));
        
    }
}else
{
    $users='';
}  
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">

    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

    <title><?php echo sitetitle; ?></title>

    <!-- Table Responsive css -->
    <script src="<?php echo assets_url; ?>js/angular.min.js"></script>
  <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" >
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
      

     <?php   if($_SESSION['logged_in']['role']==7 || $_SESSION['logged_in']['role']==1){ ?>
       <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css">
    <?php } ?>

    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
    <?PHP
    $q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
    foreach ($q->result() as $LOGO);
    ?>
    <style>
        .funnel_outer {
            width: 50%;
            float: left;
            position: relative;
            padding: 0 10%;
        }

        .funnel_outer * {
            box-sizing: border-box
        }

        .funnel_outer ul {
            margin: 0;
            padding: 0;
        }

        .funnel_outer ul li {
            float: left;
            position: relative;
            margin: 2px 0;
            height: 50px;
            clear: both;
            text-align: center;
            width: 100%;
            list-style: none
        }

        .funnel_outer li span {
            border-top-width: 50px;
            border-top-style: solid;
            border-left: 25px solid transparent;
            border-right: 25px solid transparent;
            height: 0;
            display: inline-block;
            vertical-align: middle;
        }

        .funnel_step_1 span {
            width: 100%;
            border-top-color: #8080b6;
        }

        .funnel_step_2 span {
            width: calc(100% - 50px);
            border-top-color: #669966
        }

        .funnel_step_3 span {
            width: calc(100% - 100px);
            border-top-color: #a27417
        }

        .funnel_step_4 span {
            width: calc(100% - 150px);
            border-top-color: #ff66cc
        }

        .funnel_step_5 span {
            width: calc(100% - 200px);
            border-top-color: #0099ff
        }

        .funnel_step_6 span {
            width: calc(100% - 250px);
            border-top-color: #027002
        }

        .funnel_step_7 span {
            width: calc(100% - 300px);
            border-top-color: #ff0000;
        }

        .funnel_outer ul li:last-child span {
            border-left: 0;
            border-right: 0;
            border-top-width: 40px;
        }

        .funnel_outer ul li.not_last span {
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top-width: 50px;
        }

        .funnel_outer ul li span p {
            margin-top: -30px;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }
        .page-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 0px;
    margin-top: 0px;
    line-height: 70px;
}
    </style>
</head>


<body>


    <!-- Navigation Bar-->
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>
    <!-- End Navigation Bar-->


    <div class="wrapper">
        <div class="container-fluid">

          
        
            <div class="row">
                <div class="col-md-12 page-title">Overall Company's Leads Progression</div>
               
            </div>
             <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                    
                        <form method="post" action="<?php echo page_url;?>Salesreporting/filter_salesperformance">
                            <div class="card-box col-md-12">
                                <div class="">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Start Date</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <input type="date" name="from_date" class="form-control" value="<?php echo date('Y-m-d',strtotime($this->uri->segment(3)));?>" required="">
                                        </div>
                                    </div>

                                      <div class="col-md-2">
                                        <div class="form-group">
                                            <label>End Date</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <input type="date" name="todate" class="form-control" value="<?php echo date('Y-m-d',strtotime($this->uri->segment(4)));?>" required="">
                                        </div>
                                    </div>
                                     <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Agent</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <select name="user" id="user" class="form-control" required="">
                                                <?php if($_SESSION['logged_in']['role']==1)
                                                { ?>
                                                    <option value="ALL" <?php if($this->uri->segment(5)=='' || $this->uri->segment(5)=='ALL'){?> selected <?php } ?>>ALL</option>
                                                <?php } ?>
                                            <?php 
                                            $this->db->select('user_id, first_name, last_name')->from('system_users')->where('business_location',1)->where('user_status',1);
                                            if($_SESSION['logged_in']['role']<>1)
                                            {
                                            $this->db->where('user_id',$_SESSION['logged_in']['user_id']);
                                            }
                                            $q1 = $this->db->get();
                                            foreach($q1->result() as $rowss){?>
                                            <option value='<?php echo $rowss->user_id;?>' <?php if($this->uri->segment(5)==$rowss->user_id){?> selected <?php } ?>><?php echo $rowss->first_name." ".$rowss->last_name;?></option>

                                           <?php }
                                            ?>

                                            </select>
                                        </div>
                                    </div>
                               
                                    <div class="col-md-1" style="margin-top: 23px;">
                                        <input type="submit" class="btn btn-success" value="Filter">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
           
             <?php 
                    $date = date('Y-m-d',strtotime($from_month));
                    $start_datess=$date;
                    ?>

                <div class="row">
                <!-- <div class="col-sm-6"> -->
                <div class="card-box table-responsive">
                    <div class="col-md-12"><h5 class="text-center"><?php echo strtoupper($users);?> SALES PERFORMANCE</h5></div>


                    <?php 
                        $start_date=date('Y-m-d',strtotime($from_month));
                        $end_date=date('Y-m-d',strtotime($to_month));
                        $start_date1=date('Y-m-d',strtotime($from_month));
                        $end_date1=date('Y-m-d',strtotime($to_month));
                      
                      
                        //echo $start_date."<br>". $end_date."<br>".$start_date1."<br>".$end_date1; exit;
                    ?>
                    
                   
                    <div class="col-md-12">
                        <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th style="width: 10%;">Sr No.</th>
                        <th style="width: 40%;">Parameter</th>
                       
                        <th style="width: 40%;"><?php echo date('D-M-Y',strtotime($to_month));?></th>
                        <!-- <th style="width: 25%;">Total Activities</th> -->
                        </tr>
                        </thead>
                        <tbody>
                            <?php  
                               

                        
                          
                                $start_date1=date('Y-m-d',strtotime($from_month));
                                $end_date1=date('Y-m-d',strtotime($to_month));
                                if($this->uri->segment(5)<>'ALL')
                                {
                                $u=$this->uri->segment(5);
                                }else
                                {
                                $u="ALL";
                                }
                                //echo $start_date1."<br>".$end_date1;
                            ?>
                        
                       
                        <?php 
                        $total=array();
                        $total[]=0;
                        $rest=$this->db->select('lead_name,lead_id,quotation_step,conversion_step')->from('lead_stage')->get();
                        if($rest->num_rows()>0)
                        {
                        $i=1;
                        foreach($rest->result() as $row)
                        {
                            ${"current_count_" . $row->lead_id}=$CI->Salescrm_model->check_status_via_user(1,$row->lead_id,$to_month,$this->uri->segment(5),$from_month);
                          
                           

                            $curr=count(${"current_count_" . $row->lead_id});
                           
                        

                        $start_date1=date('Y-m-d',strtotime($from_month));
                        $end_date1=date('Y-m-d',strtotime($to_month));
                        $stage="'$row->lead_id'";
                        $st=base64_encode($stage);

                        if($this->uri->segment(5)<>'ALL')
                        {
                            $u=base64_encode($this->uri->segment(5));
                        }else
                        {
                            $u="NA";
                        }
                        $total[]=$curr;
                        ?>
                        <tr>
                            <td><?php echo $i;?></td>
                        <td><?php echo strtoupper($row->lead_name);?></td>
                       
                        <td><a href='<?php echo page_url;?>Search/globalfilter/<?php echo $start_date1;?>/<?php echo $end_date1;?>/<?php echo $u;?>/<?php echo $st;?>/NA/NA/NA/NA/NA/0' style="color:black;font-weight:bold;text;text-decoration: underline;" target="_blank"><?php echo $curr;?></a></td>
                
                        </tr>
                           
                            


                


                        <?php $i++;} } ?>

                        <tr style="background-color:lightblue;">
                            <td></td>
                            <td style="font-weight: bold;color:black">Total Lead Worked</td>
                            <td style="font-weight: bold;color:black"><?php echo array_sum($total);?></td>
                        </tr>
                        </tbody>
                        </table>
                    </div>

                   

                  


            <!-- Footer -->
            <?php $this->load->view('common/footer'); ?>
            <!-- End Footer -->


        </div> <!-- end container -->
    </div>
    <!-- end wrapper -->


    <!-- jQuery  -->
    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
   
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/detect.js"></script>
    <script src="<?php echo assets_url; ?>js/fastclick.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.slimscroll.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.blockUI.js"></script>
    <script src="<?php echo assets_url; ?>js/waves.js"></script>
    <script src="<?php echo assets_url; ?>js/wow.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.nicescroll.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.scrollTo.min.js"></script>

     <!-- Datatables-->
        <script src="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.bootstrap.js"></script>
        <?php   if($_SESSION['logged_in']['role']==7 || $_SESSION['logged_in']['role']==1){ ?>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.buttons.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/jszip.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/pdfmake.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/vfs_fonts.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.html5.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.print.min.js"></script>
    <?php } ?>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.fixedHeader.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.keyTable.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.responsive.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.scroller.min.js"></script>

  
     <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
    
    <script>
        $(document).ready(function() {
            
        
            $('#example').dataTable({
                "bProcessing": true,
                "pagination": true,
                "deferRender": true,
                 dom: 'lBfrtip',
        buttons: [
         {
                extend: 'excelHtml5',
                title: 'Lead Data Dump'
            }

        
        ],

            "sAjaxSource": "<?php echo page_url; ?>Billing/tds_input_report_list/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(6);?>",
                "aoColumns": [
                        { mData: 'sr_no' } ,
                        { mData: 'billing_company'},
                        { mData: 'lead_manager' },
                        { mData: 'company_name' },
                        { mData: 'customer_name' },
                        { mData: 'invoice_no' },
                        { mData: 'product' },
                        { mData: 'basic_order_amount' },
                        { mData: 'gst_order_amount' },
                        { mData: 'order_amount' },
                        { mData: 'tds_per' },
                        { mData: 'tds_input' }
                    

                ]
            });


           
        });



function validate()
{

    var isValid=0;
    $(".mand").each(function() {
    var element = $(this).val();
    if (element=="") {

    isValid=1;
    }
    });


    if(isValid==0)
    {
    return true;
    }else
    {
    alert('Fields marked with (*) are mandatory');
    return false;
    }
    
}

$( document ).ready(function() {

$('.select8').select2({});
});
    </script>
<?php if($this->uri->segment(5)<>'ALL'){?>
    <script type="text/javascript">
    $( document ).ready(function() {
$('#example3').dataTable({
 "bProcessing": false,
 "pagination":true,
 "sAjaxSource": "<?php echo page_url;?>Sales/conveyancecommonreporting/1/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>",
 "aoColumns": [
                        { mData: 'sr_no' } ,
                        { mData: 'period' },
                        { mData: 'user' },
                        { mData: 'type' },
                        { mData: 'start_reading' },
                        { mData: 'end_reading' },
                        { mData: 'per_km_rate' },
                        { mData: 'net_km' },
                        { mData: 'petrol_used' },
                        { mData: 'misc_charges' },
                        { mData: 'total_amount' },
                        { mData: 'average' },
                        { mData: 'bifurcation' },
                        { mData: 'status' }

            
                        
                ]
        }); 
        }); 


</script>
<?php }?>

</body>

</html>