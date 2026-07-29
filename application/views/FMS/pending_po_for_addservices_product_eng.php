<?php
$CI =& get_instance();
$CI->load->model('Salescrm_model', 'salescrm');
$stage_name=$CI->salescrm->getStageName($this->uri->segment(3));
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

        <!-- Table Responsive css -->
        <script src="<?php echo assets_url;?>js/angular.min.js"></script>
         <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixuedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" >
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
<?PHP 
$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);
?>
<style>
            table.pretty thead th {
                text-align: center;
                background: <?php echo $LOGO->colorcode;?>;
                color:#fff;
                font-size:12px;
            }
            table.pretty td {
                text-align: center;
                font-size:12px;
            }
            .feedback {
  background-color : <?php echo $LOGO->colorcode;?>;
  color: white;
  padding: 10px 20px;
  border-radius: 4px;
  border-color: #46b8da;
}

#mybutton {
  position: fixed;
  bottom: -4px;
  right: 10px;
}
.select2-container {

    width: 100% !important;

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

                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">                           
                            <h4 class="page-title text-center"><?php echo ucwords(strtolower($stage_name));?></h4>
                        </div>
                    </div>
                </div>
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

                 <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered pretty">
                                <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Order No</th>
                                    <th>No. of Moulds</th>
                                    <th>No. of Components</th>
                                    <th>Project Name</th>
                                    <!-- <th>Start Date</th>
                                    <th>End Date</th>     -->                                
                                    <th>View PIS & BOM</th>
                                    <th>View Interactive PDF or Meeting Discussion Visual PPT</th>
                                    <th>View CTQ</th>
                                    <th>View Customer Signed MOM</th>
                                    <th>View BOP/Metting/Insert Moulding</th>
                                   <!--  <th>BOPs</th>
                                    <th>Metting Parts</th>
                                    <th>Insert Moulding</th> -->
                                    <!-- <th>Checkpoints</th> -->
                                    <!-- <th>Services Requested</th> -->
                                    <th>Source to Supplier</th>
                                    <th>Supplier Selection</th>
                                    <!-- <th>Brain Stroming Meeting</th>
                                    <th>PO Generation Checkpoint</th>
                                    <th>Supplier Selection</th>
                                    <th>Generate PO</th> -->
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">                           
                            <h4 class="page-title text-center">&nbsp; HISTORY <?php echo ucwords(strtolower($stage_name));?></h4>
                        </div>
                    </div>
                </div>              

                 <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example1" class="table table-striped table-bordered pretty">
                                <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Order No</th>
                                    <th>No. of Moulds</th>
                                    <th>No. of Components</th>
                                    <th>Project Name</th>
                                   <!--  <th>Start Date</th>
                                    <th>End Date</th>      -->                               
                                    <th>View PIS & BOM</th>
                                    <th>View Interactive PDF or Meeting Discussion Visual PPT</th>
                                    <th>View CTQ</th>
                                    <th>View Customer Signed MOM</th>
                                    <th>View BOP/Metting/Insert Moulding</th>
                                   <!--  <th>BOPs</th>
                                    <th>Metting Parts</th>
                                    <th>Insert Moulding</th> -->
                                    <!-- <th>View Checklist</th> -->
                                    <th>Sources Supplier</th>
                                    <th>Selected Supplier</th>
                                    <th>View PO</th>
                                </tr>
                                </thead>
                            </table>
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
      
        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
 
        <script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
<script>

$( document ).ready(function() {
$('#example').dataTable({
"bProcessing": true,
"pagination":true,
"deferRender": true,
"pageLength": 10,
"stateSave": true,
"sAjaxSource": "<?php echo page_url;?>FMS/pending_po_for_addservices_list_mockup/<?php echo $this->uri->segment(3);?>",
"aoColumns": [
                { mData: 'sr_no' } ,
                { mData: 'order_no' } ,
                { mData: 'no_of_moulds' } ,
                { mData: 'no_of_components' } ,
                { mData: 'project_name' },
                // { mData: 'start_date' },
                // { mData: 'end_date' },                
                { mData: 'view_pis_bom' },
                { mData: 'upload_part_design' },
                { mData: 'upload_ctq' },
                { mData: 'upload_signed_mom' },
                 { mData: 'bop_metting_moulding' },
                // { mData: 'view_bop' },
                // { mData: 'metting_parts' },
                // { mData: 'insert_moulding' },
                // { mData: 'checklist' },
                // { mData: 'services_rqst' },
                { mData: 'source_supplier' },
                { mData: 'select_supplier' }
                // { mData: 'brain_storming' },
                // { mData: 'po_checklist' },
                // { mData: 'upload_po' }
                
        ]
}); 

$('#example1').dataTable({
"bProcessing": true,
"pagination":true,
"deferRender": true,
"pageLength": 10,
"stateSave": true,
"sAjaxSource": "<?php echo page_url;?>FMS/po_for_addservices_history/<?php echo $this->uri->segment(3);?>",
"aoColumns": [
                { mData: 'sr_no' } ,
                { mData: 'order_no' } ,
                { mData: 'no_of_moulds' } ,
                { mData: 'no_of_components' } ,
                { mData: 'project_name' },
                // { mData: 'start_date' },
                // { mData: 'end_date' },                
                { mData: 'view_pis_bom' },
                { mData: 'upload_part_design' },
                { mData: 'upload_ctq' },
                { mData: 'upload_signed_mom' },
                 { mData: 'bop_metting_moulding' },
                // { mData: 'view_bop' },
                // { mData: 'metting_parts' },
                // { mData: 'insert_moulding' },                
                // { mData: 'checklist' },
                { mData: 'source_supplier' },
                { mData: 'selected_supplier_name' },
                { mData: 'select_supplier' }
        
    ]
});
 

});


</script>

    </body>
</html>