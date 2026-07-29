<?php
$CI = &get_instance();
$CI->load->model('Salescrm_model');
$DI = &get_instance();
$DI->load->model('Lead_model'); 
$date1 = strtotime(date('Y-m-d',strtotime('-6 months')));
$date2 = strtotime(date('Y-m-d'));
$arr=array();
while ($date1 <= $date2) {
$arr[]=date('Y-m', $date1);
 $date1 = strtotime('+1 month', $date1); 
}

rsort($arr);

$selected_status=$this->uri->segment(3);


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

        .square {
  width: 100%;
  height: 0;
  min-height: 750px;
  padding-top: 100%;
  background-color: #ff6961;
  position: relative;
}
.content {
  position: absolute;
  top: 0;
  left: 0;
}

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
                <div class="col-md-4 page-title">TYPE REPORTS MONTHWISE</div>
                <div class="col-md-4 page-title"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Filter by Status</label>
                        <select class="form-control" onchange="get_statuswise(this.value);">
                            <option value="ALL"  <?php if($this->uri->segment(3)=='' || $this->uri->segment(3)=='ALL'){?> selected <?php } ?> >ALL</option>
                            <?php
                            $rest=$this->db->select('id,typestatus')->from('typestatus')->where('status',1)->get(); 
                            if($rest->num_rows()>0)
                            {
                                foreach($rest->result() as $rowww)
                                {
                            ?>
                            <option value="<?php echo $rowww->id;?>" <?php if($rowww->id==$this->uri->segment(3)){?> selected <?php } ?>><?php echo $rowww->typestatus;?></option>
                            <?php 
                            } } ?>
                        </select>
                    </div>
                </div>
               
            </div>

             <div class="row">
                <div class="col-md-12 page-title"><?php echo $this->session->flashdata('message');?></div>
               
            </div>
             

                <div class="row">
                <!-- <div class="col-->
                <div class="card-box table-responsive">
        

                    <div class="col-md-12">


                        <?php 
                        $i=0;
                        foreach ($arr as $d) {

                            $show=1;
                            $status=$CI->Salescrm_model->getcurrent_status(date('Y-m-01', strtotime($d)));
                            if($selected_status<>'ALL' && $selected_status<>'')
                            {
                                if($selected_status==$status)
                                {
                                $show=1;
                                }else
                                {
                                $show=0;
                                }
                            }


                    if($show==1)
                    {
                            
                    ?>
                    <div class="col-md-4">
                    <div class="square">
                    <div class="content" style="width:100%">
                    <div class="col-md-12" style="border-bottom:1px dashed #fff;">
                
                    <div class="col-md-8">
                    <h5 class="page-title" style="color:white;"><?php echo date('F Y', strtotime($d)) ;?></h5>
                    </div>
                    <a href='javascript:;'><div class="col-md-4 page-title" style="color:white;margin-top: 10px;"><i class="fa fa-envelope pull-right"></i></div></a>
                    </div>

        

                    <div class="col-md-12" style="margin-bottom: 5px;">
                    <div style="background-color:white;">
                    <p style="font-weight: bold;font-size: 16px;padding-left: 10px;">TYPE 1</p>
                    <p style="padding-left: 10px;"><input type="checkbox" name="docs[]" value="1">&nbsp;<a href="<?php echo page_url;?>ExcelImport/type_one_claim_without_interest/<?php echo date('Y-m-01',strtotime($d));?>/<?php echo date('Y-m-t',strtotime($d));?>/ALL">Claim Sheet (Without Interest)</a></p>
                    <p style="padding-left: 10px;"><input type="checkbox" name="docs[]" value="2">&nbsp;<a href="<?php echo page_url;?>ExcelImport/type_one_claim_with_interest/<?php echo date('Y-m-01',strtotime($d));?>/<?php echo date('Y-m-t',strtotime($d));?>/ALL">Claim Sheet (With Interest)</a></p>
                    <p style="padding-left: 10px;"><input type="checkbox" name="docs[]" value="3">&nbsp;<a href="<?php echo page_url;?>Pdfexample/type_1_invoice/2/<?php echo date('Y-m-01',strtotime($d));?>/<?php echo date('Y-m-01',strtotime($d));?>/ALL/" target="_blank">Invoice </a></p>

                    </div>
                    }
                </div>
 
 
                <div class="col-md-12" style="margin-bottom: 5px;">
                        <div style="background-color:white;">
                    <p style="font-weight: bold;font-size: 16px;padding-left: 10px;">TYPE 2/3</p>
                    <p style="padding-left: 10px;"><input type="checkbox" name="docs[]" value="4">&nbsp;<a href='<?php echo page_url;?>ExcelImport/type_two_claim_format_new/<?php echo date('Y-m-01',strtotime($d));?>/<?php echo date('Y-m-t',strtotime($d));?>/ALL/ALL/'>Claim Sheet</a></p>
                    <p style="padding-left: 10px;"><input type="checkbox" name="docs[]" value="5">&nbsp;<a href="<?php echo page_url;?>Pdfexample/type_2_invoice/<?php echo date('Y-m-01',strtotime($d));?>/<?php echo date('Y-m-t',strtotime($d));?>/2/ALL" target="_blank">Invoice </a></p>
                </div>
                </div>

                  <div class="col-md-12" style="margin-bottom: 5px;border-bottom:3px dashed #fff;">
                        <div style="background-color:white;">
                    <p style="font-weight: bold;font-size: 16px;padding-left: 10px;">TRANSPORT INVOICES</p>
                    <?php 

                    $rest123=$CI->Salescrm_model->getalltransportation_bills(date('Y-m-01',strtotime($d)),date('Y-m-t',strtotime($d)));
                    if(count($rest123)>0)
                    {
                        $t=1;
                        foreach($rest123 as $row)
                        {

                            $link="<a href='".page_url1."taxinvoice/tcpdf/examples/invoice.php?approval_id=".$row."' target='_blank'>Invoice ".$t."</a>";
                    ?>
                   <p style="padding-left: 10px;"><input type="checkbox" name="transportation_docs[]" value="<?php echo $row;?>">&nbsp;<?php echo $link;?></p>
                <?php $t++;
                } }else{ ?>
                    <p style="padding-left: 10px;">&nbsp;No Invoice Available</p>
                <?php } ?>
                </div>
                </div>


                <form action="<?php echo page_url;?>Sales_stats_reporting/set_status" id="frm" method="post">
                <input type="hidden" name="period" value="<?php echo date('Y-m-01', strtotime($d));?>">
                 <div class="col-md-12" style="margin-bottom: 5px;">
                        <div class="form-group">
                        <select name="status" id="status" class="form-control" required>
                            <option value="">Select Status</option>
                            <?php
                            $rest=$this->db->select('id,typestatus')->from('typestatus')->where('status',1)->get(); 
                            if($rest->num_rows()>0)
                            {
                                foreach($rest->result() as $rowww)
                                {
                            ?>
                            <option value="<?php echo $rowww->id;?>" <?php if($rowww->id==$status){?> selected <?php } ?>><?php echo $rowww->typestatus;?></option>
                            <?php 
                            } } ?>
                        </select>
                        </div>
                </div>

                  <div class="col-md-12" style="margin-bottom: 5px;">
                    <div class="form-group">
                        <textarea name="remarks" id="remarks" class="form-control" required placeholder="Enter Remarks"></textarea>
                        <span><a href='<?php echo page_url;?>Sales_stats_reporting/history/<?php echo date('Y-m-01', strtotime($d));?>' style="color:white;font-weight: bold;" target="_blank"><u>View History</u></a></span>
                    </div>
                  </div>
                    <div class="col-md-12 text-center" style="margin-bottom: 5px;">
                        <div class="form-group">
                            <input type="submit" name="sub" id="sub" class="btn btn-warning">
                        </div>
                    </div>
                </form>
                   
                    </div>
                    </div>
                    </div>
                    <?php 
                    if(($i+1)%3==0)
                    {
                    ?>
                    <div style="clear:both;height:10px;"></div>
                    <?php 
                    }
                    
                    $i++;
                    }
                    } ?>


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

function get_statuswise(id)
{
    document.location="<?php echo page_url;?>Sales_stats_reporting/typewisereport/"+id;
}

    </script>


</body>

</html>