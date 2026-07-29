<?php
$type=$this->uri->segment(3);
//$product_id=$this->uri->segment(6);
// $row=$this->db->select('id,instruments_name,pack_size,unit,model_number')->from('presto_instruments')->where('id',$product_id)->get();
// if($row->num_rows()>0)
// {
//     foreach($row->result() as $rows);
//     $instrument_name=$rows->instruments_name."-".$rows->pack_size."-".$rows->model_number;
// }else
// {
//     $instrument_name='';
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

        <!-- Table Responsive css -->
        <script src="<?php echo assets_url;?>js/angular.min.js"></script>
         <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css">
           <link href="<?php echo assets_url; ?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
<?PHP 
$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);
?>
        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
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
            </style>
              <style>
        .select2-container
        {
            height: 44px !important;
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single
        {
            height: 36px !important;
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
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
                         
                
                         
                                 <h4 class="page-title text-center">CUSTOMER LEDGER</h4>
                           
                        </div>
                    </div>
                </div>


                <form action="<?php echo page_url;?>Customer/get_ledger" name="frm" id="frm" method="post">
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                      
            
                    
                        <div class="col-md-12 card-box">
                            <div class="col-md-2"></div>

                              <div class="col-md-2">
                             <div class="form-group">
                            <label>Company</label>
                          <select class="form-control mand" name="company" id="company" required onchange="get_customer();">
                                            <option value="">Select Company</option>
                                            <?php
                                            $q = $this->db->select('id, companyname')->from('store_rack_location')->where('status', 1)->get();
                                            foreach ($q->result() as $row) {
                                            ?>
                                                <option value="<?php echo $row->id; ?>"><?php echo $row->companyname; ?></option>
                                            <?php } ?>
                                        </select>
                       </div>
                   </div>

                     <div class="col-md-2">
                             <div class="form-group">
                            <label>Customer</label>
                            <select class="form-control mand select3" name="customer" id="customer" required>
                                            <option value="">Select</option>
                                        </select>
                       </div>
                   </div>


                       <div class="col-md-2">

                        <div class="form-group">
                            <label>Start Date</label>
                           <input type="text" name="stdate" id="stdate" class="form-control datepicker" value="<?php if($this->uri->segment(4)!=''){ date('d-m-Y',strtotime($this->uri->segment(4)));}else{}?>">
                       </div>
                       </div> 

                         <div class="col-md-2">
                             <div class="form-group">
                            <label>End Date</label>
                           <input type="text" name="etdate" id="etdate" class="form-control datepicker" value="<?php if($this->uri->segment(5)!=''){ date('d-m-Y',strtotime($this->uri->segment(5)));}else{}?>">
                       </div>
                   </div>

                   
                   <div style="clear:both;height:5px;"></div>
                
                   <div class="col-md-4"></div>
                   <div class="col-md-4 text-center">
                       <input type="submit" style="width:100%" class="btn btn-success btn-sm" value="Filter" onclick="validate();" >
                   </div>
                       </div>
            
                        </div>
                        </div>
                  
                    </form>



              
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
         <script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js" type="text/javascript"></script>

        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

       


<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
<script>

$( document ).ready(function() {

    $('.datepicker').datepicker({
     autoclose: true,
 format:'dd-mm-yyyy'
   });

});

 
function get_customer()
  {
    var compid=$("#company").val();

    if(compid!='')
    {
     var purl = "<?php echo page_url;?>Customer/get_customer_by_company";
             $(".select3").select2({
              placeholder: 'TYPE TO SELECT',
              minmumInputLength: 4,
              allowClear: true,
              ajax: {
                url: purl,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                  return {
                    searchTerm: params.term,
                    compid: compid,
                  };

                },
                processResults: function(data) {
                  return {
                    results: data
                  };
                },
                cache: true

              }
            });    

         }
    }


</script>
        


    </body>
</html>