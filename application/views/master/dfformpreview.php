<?php 
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies
$CI=&get_instance();

$poid=$this->uri->segment(3);
$lead_id=$this->uri->segment(4);
$uri6 = $this->uri->segment(5);
$date = base64_decode($uri6);

$qry = $this->db->select('*')->from('df_design_form_table')->where('po_id',$poid)->where('lead_id',$lead_id)->get();
if($qry->num_rows()>0){
    foreach($qry->result() as $rows);

            $df_form_name= $rows->design_form_name;
            $design_form_date = $rows->design_form_date;
            $ref_df_date = $rows->ref_df_date;
            $reference_no = $rows->reference_no;
           
}else{


    $qry = $this->db->select('*')->from('df_design_form_600_table')->where('po_id',$poid)->where('lead_id',$lead_id)->get();
if($qry->num_rows()>0){
    foreach($qry->result() as $rows);

            $df_form_name= $rows->design_form_name;
            $design_form_date = $rows->design_form_date;
            $ref_df_date = $rows->ref_df_date;
            $reference_no = $rows->reference_no;
           }else
           {
    echo "There is some issue with the Preview. Please check with Gamavis Team"; exit;
}
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
                                

                               
                            </div>
                    
                        <div>
                    <div class="col-md-12" style="color:red;"><h5 style="color:red;text-align:center;font-weight:bold;"><?php echo $this->session->flashdata('message');?></h5></div><br/>
                        <div class="col-md-8">
                        <?php
                        $filelocation = softwarepath.'designform/';
                        $fileNL=$filelocation.$date.'?nocache='.time();
                        ?>


                             <iframe src="<?php echo $fileNL;?>#toolbar=0" width="1200" height="800" ></iframe>
                          <!--   <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: transparent;"
                            oncontextmenu="return false;"></div> -->
                        
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

      
<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
		


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