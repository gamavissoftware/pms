<?php 
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies
$CI=&get_instance();
$lead_id=$this->uri->segment(4);
$CI->load->model('Salescrm_model','salescrm');
$getopprtunityuniquecode = $CI->salescrm->getopportunitygeneraterefno($this->uri->segment(3));
$version = $CI->salescrm->getopportunityversionfno($this->uri->segment(3));
$refrencenumber = str_replace('/', '_', $getopprtunityuniquecode);
$currentstatus=$CI->salescrm->getcurrent_statusLead($this->uri->segment(4));
$notallowed=array('39','36','38');
$permisson=$CI->salescrm->checkOwnervsViewer($lead_id);
if($permisson==0)
{
    $this->salescrm->showMsg('You do not have permission to view this quote.');
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
		<title>PREVIEW PDF & SEND MAIL</title>
		<!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.dataTables.min.css">
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
		<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script>
		<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
		<style>

             @media print {
        body {
            display: none;
        }
    }

            .pdf-container {
      width: 100%;
      height: 100vh;
      overflow: hidden;
      position: relative;
    }
    .pdf-object {
      width: 100%;
      height: 100%;
    }

                table.manglesh thead th {
                background: #003366;
                color:#fff;
                font-size:11px;
                font-weight:bold;
                }
                table tbody tr td {
                font-size: 11px;
                color:#000;
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
                left: 50%;
                margin-left: -32px;
                margin-top: -32px;
                position: absolute;
                top: 50%;
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
            <div class="container-fluid">

            
		<div class="row">
           
                    <div class="col-sm-12" style="margin-top:30px;">
                            <div class="row">
                                <div class="col-md-8"></div>

                                <?php 
                                // $q = $this->db->select('id')->from('progress_remarks')->where('lead_status',37)->where('lead_id',$this->uri->segment(4))->get();
                                // if($q->num_rows()>0){
                                // $flagforversion = 1;
                                // }else{
                                // $flagforversion =0;
                                // }
                                ?>

                                <?php 

                                    $q = $this->db->select('id')->from('progress_remarks')->where('lead_status',39)->where('lead_id',$this->uri->segment(4))->order_by('id','DESC')->limit(1)->get();
                                    if($q->num_rows()==0)
                                    {
                                   
                                    $flagforversion = 0;

                                    } else {

                                    $q = $this->db
                                    ->select('id')
                                    ->from('progress_remarks')
                                    ->where('lead_id', $this->uri->segment(4))
                                    ->where_in('lead_status', array(37, 38))
                                    ->get();

                                    $flagforversion = ($q->num_rows() > 0) ? 1 : 0;
                                    }
                                   

                                ?>



                                <div class="col-md-2"><a href="<?php echo page_url;?>Opportunity/edit_opportunity/<?php echo $this->uri->segment(4);?>/<?php echo  $flagforversion;?>"><span class="btn btn-warning">Revise Quotation</span></a> </div>

                               <?php 
                               $rest=$this->db->select('id')->from('progress_remarks')->where('lead_id',$this->uri->segment(4))->where('lead_status',39)->order_by('id','DESC')->limit(1)->get();
                               if($rest->num_rows()>0)
                               {
                                ?>
                                <div class="col-md-2"><a href="<?php echo page_url;?>Whatsapp_module/sendnotificationforapproval/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>"><span class="btn btn-success">Send for Approval</span></a> </div>
                                <?php } ?>
                            </div>
                    
                        <div>
 <div class="col-md-12" style="color:red;"><h5 style="color:red;text-align:center;font-weight:bold;"><?php echo $this->session->flashdata('message');?></h5></div><br/>
                        <div class="col-md-8">
                        <?php
                        $filelocation = softwarepath.'shubhamquotation/';
                        $fileNL=$filelocation.'Quotation_'.$refrencenumber.'_V'.$version.'.pdf?nocache='.time();
                        if(in_array($currentstatus,$notallowed))
                        {
                            
                        ?>


                             <iframe src="<?php echo $fileNL;?>#toolbar=0" width="1300" height="800"  style="border: none;"></iframe>
                          <!--   <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: transparent;"
                            oncontextmenu="return false;"></div> -->
                        <?php
                        }else
                        {
                       
                        ?>
                        
                        <iframe src="<?php echo $fileNL;?>" width="1300" height="800"></iframe>
                    <?php } ?>
                        </div>


                        <div class="col-md-4">
                        <div class="col-md-12">
                       <!--  <a href="javascript:;" data-toggle="modal" data-target="#myModal" class="pull-right"><button class="btn btn-info btn-xs">View Mail History</button></a> -->
                        </div>
                        

                        </div>
                    </div>
                </div>



                 <!-- <a href="<?php //echo page_url;?>Store/sendmailtosupplierwithpdf/<?php //echo $id;?>"><span class="btn btn-warning">Send to Client</span></a> -->

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
        <script src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>
<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
		

	 
 <script>
$( document ).ready(function() {
 
$('#example').dataTable({
 "bProcessing": false,
 "pagination":true,
fixedHeader: true,
   scrollCollapse: true,
   fixedColumns:   {
            leftColumns: 3
        },
 "sAjaxSource": "<?php echo page_url;?>Reporting/yourcreated_indent/",
 "aoColumns": [
					{ mData: 'sr_no' },
					{ mData: 'indent_type'},
					{ mData: 'prno'},
					{ mData: 'itemdetail'},
					{ mData: 'createdby'},
					{ mData: 'createdon'},
					{ mData: 'markrecvd'}
						
						
                ]
        });   
});


</script>

</script>

<script type="text/jscript">
// function injectJS(){    
//     // var frame =  $('iframe');
//     // var contents =  frame.contents();
//     // var body = contents.find('body').attr("oncontextmenu", "return false");
//     // var body = contents.find('body').append('<div>New Div</div>');    
// }
</script>


<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready
</script>

<script>
    //document.addEventListener('contextmenu', (e) => e.preventDefault());
</script>

<script>
    // document.addEventListener('keydown', (e) => {
    //     if (e.ctrlKey && (e.key === 's' || e.key === 'p')) {
    //         e.preventDefault();
    //     }
    // });
</script>
</body>
</html>