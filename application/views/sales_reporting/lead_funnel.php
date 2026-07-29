<?php
$CI = &get_instance();
$CI->load->model('Salescrm_model');
$DI = &get_instance();
$DI->load->model('Lead_model');    

$from_month=$this->uri->segment(3);
$to_month=$this->uri->segment(4);
$rest=$this->db->select('lead_name,lead_id')->from('lead_stage')->where('show_in_funnel',1)->get();
if($rest->num_rows()>0)
{
$i=1;
foreach($rest->result() as $row)
{
${"current_count_" . $row->lead_id}=$CI->Salescrm_model->check_status(0,$row->lead_id,$to_month);
${"previous_count_" . $row->lead_id}=$CI->Salescrm_model->check_status(1,$row->lead_id,$from_month);
}

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
    </style>
</head>


<body>


    <!-- Navigation Bar-->
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>
    <!-- End Navigation Bar-->


    <div class="wrapper">
        <div class="container">

          
        
            <div class="row">
                <div class="col-md-12 page-title">Overall Company's Leads Progression</div>
               
            </div>
             <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                    
                        <form method="post" action="<?php echo page_url;?>Sales_stats_reporting/filter_salesfunnel">
                            <div class="card-box col-md-12">
                                <div class="">
                                    <div class="col-md-3"></div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>From</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <input type="month" name="from_date" class="form-control" value="" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>To</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <input type="month" name="to_date" class="form-control" value="" required="">
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
           

            <div class="row">
                <!-- <div class="col-sm-6"> -->
                <div class="card-box table-responsive">

                <?php 
                $date = date('Y-m',strtotime($from_month));
                $start_datess=$date;        
                ?>

                <div class="funnel_outer">
                <h3 class="text-center"><?php echo date('M-Y',strtotime($start_datess));?></h3>

                <ul>
                <?php
                $rest=$this->db->select('lead_name,lead_id')->from('lead_stage')->where('show_in_funnel',1)->get();
                if($rest->num_rows()>0)
                {
                    $i=1;
                    foreach($rest->result() as $row)
                    {
                       $date = date('Y-m');
                        $start_datess=date('Y-m',strtotime($from_month));
                        $start_date=date('Y-m-01',strtotime($start_datess));
                        $end_date=date('Y-m-t',strtotime($start_date));

                        $stage="'$row->lead_id'";
                        $st=base64_encode($stage);
                ?>
                
                   <li class="funnel_step_<?php echo $i;?>"><span><p><a href='<?php echo page_url;?>Search/globalfilter/<?php echo $start_date;?>/<?php echo $end_date;?>/NA/<?php echo $st;?>/NA/NA/NA/NA/NA' style="color:white;" target="_blank"><?php echo strtoupper($row->lead_name);?> - <?php echo count(${"previous_count_" . $row->lead_id});?></a></p></span></li>
                <?php 
                $i++;
                }
                }
                ?>

                </ul>
                </div>


                   <div class="funnel_outer">
                    <h3 class="text-center"><?php echo date('M-Y',strtotime($to_month));?></h3>
                <ul>
                     <?php
                $rest=$this->db->select('lead_name,lead_id')->from('lead_stage')->where('show_in_funnel',1)->get();
                if($rest->num_rows()>0)
                {
                    $i=1;
                    foreach($rest->result() as $row)
                    {
                        $start_date=date('Y-m-01',strtotime($to_month));
                        $end_date=date('Y-m-t',strtotime($to_month));

                         $stage="'$row->lead_id'";
                        $st=base64_encode($stage);
                        $curr=count(${"current_count_" . $row->lead_id});
                        $prev=count(${"previous_count_" . $row->lead_id});
                       
                         $new=$curr-$prev;


                ?>
                 <li class="funnel_step_<?php echo $i;?>"><span><a href='<?php echo page_url;?>Search/globalfilter/<?php echo $start_date;?>/<?php echo $end_date;?>/NA/<?php echo $st;?>/NA/NA/NA/NA/NA' style="color:white;" target="_blank"><p><?php echo strtoupper($row->lead_name);?> - <?php echo count(${"current_count_" . $row->lead_id});?></a>
                 <?php if($new<>0){
                    if($new>0)
                        { ?>
                            <i class="fa fa-arrow-up pull-right"><?php echo $new;?></i>
                            
                       <?php }else
                        { ?>
                             <i class="fa fa-arrow-down pull-right"><?php echo $new;?></i>
                        <?php } } ?>

                 </p></span></li>
                <?php 
                $i++;
                }
                }
                ?>
                
              

                </ul>
                </div>



                </div>
                </div>
            

    
            <!-- end row -->
          


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


</body>

</html>