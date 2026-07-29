<?php
$hpcl_locations='';
$products='';
$CI = &get_instance();
$CI->load->model('Salescrm_model');
$getHpclLocations = $CI->Salescrm_model->getHpclLocations();
$getAllProducts = $CI->Salescrm_model->getAllProducts();
$getRackLocation = $CI->salescrm->getRackLocation();
$empty_barrel_count = $CI->salescrm->empty_barrel_report_list();
$empty_barrel_purchase_count = $CI->salescrm->empty_barrel_purchase_report_list();

$start_date=$this->uri->segment(3);
$end_date=$this->uri->segment(4);

if($start_date <> '' && $end_date <> '') {
    $starting_date = $start_date;
    $ending_date = $end_date;
    $hpcl_locations = $hpcl_locations;
  
    $products = $products;
    $company = 'c';

} else {
    $starting_date = date('Y-m-01');
    $ending_date = date('Y-m-t');
    $hpcl_locations = 'ALL';
    $products = 'ALL';
    $company = 'ALL';
 
}
if($this->uri->segment(5)<>'ALL')
{
$loc=$CI->Salescrm_model->get_party_name($this->uri->segment(5));
}else
{
$loc="ALL";
}

if($this->uri->segment(6)<>'ALL')
{
$product=$CI->Salescrm_model->get_product_name($this->uri->segment(6));
}else
{
$product="ALL";
}
if($this->uri->segment(7)<>'ALL')
{
$company=$CI->Salescrm_model->get_company_name($this->uri->segment(7));
}else
{
$company="ALL";
}
$filter_criteria='<table style="border: 1px solid black;" class="table table-bordered">
                <tbody><tr>
                <th style="border: 1px solid black; font-weight:bold;text-align:center;" colspan="6">Report Filter Criteria</th>        
                </tr>
                <tr>
                <th style="border: 1px solid black;text-align:center; width:300px;">Purchase Date</th>
                <th style="border: 1px solid black;text-align:center; width:200px;">Company</th>
                <th style="border: 1px solid black;text-align:center; width:200px;">Party</th>
                <th style="border: 1px solid black;text-align:center; width:200px;">Product</th>
              
                </tr>
                <tr>';


                if($this->uri->segment(8)==1)
                {
                     $filter_criteria.='<td style="border: 1px solid black;text-align:center;color:black;">All Time</td>';
                }else
                {
                $filter_criteria.='<td style="border: 1px solid black;text-align:center;color:black;">'.date('d-M-Y',strtotime($this->uri->segment(3))).' to '.date('d-M-Y',strtotime($this->uri->segment(4))).'</td>';
                }

                $filter_criteria.='<td style="border: 1px solid black;text-align:center;color:black;">'.$company.'</td>
                <td style="border: 1px solid black;text-align:center;color:black;">'.$loc.'</td>
                <td style="border: 1px solid black;text-align:center;color:black;">'.$product.'</td>
                
               
                </tr>
                </tbody></table>';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright;?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle;?> Inventory Report for Empty Barrels</title>

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
        <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">

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
                background:<?php echo $LOGO->colorcode;?>;
                color:#fff;
                font-size:12px;
            }
            table.pretty td {
                text-align: center;
                font-size:12px;
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
                            <h4 class="page-title text-center">Empty Barrels Inventory Report</h4>
                        </div>

                    <div class="page-title-box"> 
                        <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
                        </div>

                    </div> 
                </div>

                <div class="row card-box">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <table style="border: 1px solid black;" class="table table-bordered">
                <tbody><tr>
                <th style="border: 1px solid black; font-weight:bold;text-align:center;" colspan="6">Barrel Stock Report</th>        
                </tr>
                <tr>
                <th style="border: 1px solid black;text-align:center; width:300px;">Opening Stock as on<br> 31-Aug-2023</th>
                <th style="border: 1px solid black;text-align:center; width:200px;">Purchased</th>
                <th style="border: 1px solid black;text-align:center; width:200px;">Used</th>
                <th style="border: 1px solid black;text-align:center; width:200px;">Balanced</th>
              
                </tr>

                <tr>
                <td style="border: 1px solid black;text-align:center; width:300px; font-size: 18px; color:red;">129</td>
                <td style="border: 1px solid black;text-align:center; width:200px; font-size: 18px; color:red;"><a href="<?php echo page_url;?>/Inventory/this_month_purchases/2023-08-01/<?php echo date('Y-m-d');?>/ALL/348/ALL/0"><u><?php echo $empty_barrel_purchase_count;?></u></a></td>
                <td style="border: 1px solid black;text-align:center; width:200px; font-size: 18px; color:red;"><?php echo $empty_barrel_count;?></td>
                <td style="border: 1px solid black;text-align:center; width:200px; font-size: 18px; color:red;"><?php 
                    echo $empty_barrel_purchase_count+129-$empty_barrel_count;
            ?></td>
              
                </tr>
                
            </tbody>
        </table>

                    </div>
                </div>

                <div class="row" style="margin-top:20px; display: none;" >
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <!-- <div class="page-title-box col-md-4"> -->
                         <!-- <div class="btn-group pull-right">
                          <a href="<?php echo page_url;?>Approval/approval" class="btn btn-success waves-effect waves-light">Add Data</a>
                               
                            </div> -->
                           
                            <!-- <h4 class="page-title text-center">Approval List</h4>
                        </div> -->
                        <form method="post" action="<?php echo page_url;?>Inventory/filter_purchase_summary_with_density">
                            <div class="card-box col-md-12">
                                <div class="row">
                                     <div class="col-md-1">
                                         <div class="form-group">
                                              <label>All Time</label><br/>
                                            <input type="checkbox" name="alltime" value="1" <?php if($this->uri->segment(8)==1){?> checked <?php } ?>>


                                         </div>
                                     </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>From</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <input type="date" name="from_date" class="form-control" value="<?php echo $starting_date;?>" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>To</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <input type="date" name="to_date" class="form-control" value="<?php echo $ending_date;?>" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Company</label>
                                            <select class="form-control" name="company" id="company" onchange="getParty();">
                                                <option value="ALL">ALL</option>
                                                <?php if($getRackLocation != '') {
                                                        foreach($getRackLocation as $row1) {?>
                                                <option value="<?php echo $row1->id;?>" <?php if($row1->id == $this->uri->segment(7)) { echo 'selected';};?>><?php echo $row1->companyname;?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Party</label>
                                            <select class="form-control" name="hpcl_locations" id="hpcl_locations">
                                                <option value="ALL">ALL</option>
                                             
                                            </select>
                                        </div>
                                    </div>


                                    <!-- <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Party</label>
                                            <select class="form-control" name="hpcl_locations">
                                                <option value="ALL">ALL</option>
                                                <?php if($getHpclLocations != '') {
                                                        foreach($getHpclLocations as $row1) {?>
                                                <option value="<?php echo $row1->id;?>" <?php if($row1->id == $hpcl_locations) { echo 'selected';};?>><?php echo $row1->name;?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div> -->

                                  
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Product</label>
                                            <select class="form-control" name="products" id="products">
                                                <option value="ALL">ALL</option>
                                                <?php if($getAllProducts != '') {
                                                        foreach($getAllProducts as $row2) {?>
                                                <option value="<?php echo $row2->id;?>" <?php if($row2->id == $products) { echo 'selected';};?>><?php echo $row2->instruments_name;?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>
                                  
                                </div>

                                <div class="row">
                                    <div class="col-md-4"><span style="color:red;font-weight:bold;">Using All time feature will not consider any date selected</span></div>
                                      <div class="col-md-4 text-center">
                                      <input type="submit" class="btn btn-success" value="Filter" style="width:100%">
                                    </div>
                                </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row" style="display:none">
                    <div class="col-md-12">
                        <div class="col-md-3"></div>
                        <div class="col-md-6 card-box"><?php echo $filter_criteria;?></div>
                                            </div>
                </div>



              

                 <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example1" class="table table-striped table-bordered pretty">
                                <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Purchase Date</th>
                                    <th>Company Name</th>
                                    <th>Party</th>
                                    <th>Bill No</th>
                                    <th>Credit Days</th>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Rate</th>
                                   <th>Density</th>
                                   <th>Product Orginal Qty</th>
                                   <th>Bulk to Drum</th>
                                   <th>Volume</th>s
                                   <th>Barrel Used</th>
                                   

                                </tr>
                                </thead>
                                
                            </table>
                        </div>
                    </div>
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
        <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>

        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

       

        <script>
$( document ).ready(function() {

    getPartySelected();
$('#products').select2(); 
$('#example1').dataTable({
"bProcessing": true,
"pagination":true,
dom: 'lBfrtip',
        buttons: [
            'excel'
        ],
          pageLength:50,
"sAjaxSource": "<?php echo page_url;?>Inventory/empty_barrel_report_list/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(6);?>/<?php echo $this->uri->segment(7);?>/<?php echo $this->uri->segment(8);?>",
"aoColumns": [
                { mData: 'sr_no' },
                { mData: 'purchase_date' },
                { mData: 'company_name' },
                { mData: 'party' },
                { mData: 'bill_no' },
                { mData: 'credit_days' },
                { mData: 'product' },
                { mData: 'qty' },
                { mData: 'rate' },
                { mData: 'density' },
                { mData: 'mass' },
                { mData: 'bulktodrum' },
                { mData: 'volume' },
                { mData: 'barrel' }
               
               

               
                

                
        ]
}); 
});

</script>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script language="javascript" type="text/javascript">   
jQuery.noConflict();
$(document).ready(function() {
$("#save").click(function() {
var business_loc = $("#business_loc").val();
if(business_loc=='')
{
    $("#error_business_loc").html('Required!');
}
var department_name = $("#department_name").val();
if(department_name=='')
{
    
    $("#error_department_name").html('Required!');
}

var status = $("#status").val();
if(status=='')
{
    
    $("#error_status").html('Required!');
}


if(business_loc=='' || department_name==''|| status=='' )
{
    
    return false;
}

});
});
</script>
<script type="text/javascript">
    function chk_item_picked_up(id) {
        window.location.href = "<?php echo page_url;?>Approval/chk_item_picked_up/"+id;
    }

  function send_to_tally(id, quote_id) {
    var send_to_tally = $('#send_to_tally'+id).val();


    if(send_to_tally != '') {
      $.ajax({
        type:"post",
        url:"<?php echo page_url;?>Inventory/send_to_tally",
        data:{id: id},
        success:function(data){
          $('#sent_to_tally'+id).html(data);
        }
      });
    }

  }


  function imported_to_tally(id, quote_id) {
    var send_to_tally = $('#billed'+id).val();


    if(send_to_tally != '') {
      $.ajax({
        type:"post",
        url:"<?php echo page_url;?>Inventory/imported_to_tally",
        data:{id: id},
        success:function(data){
          $('#imported_to_tally'+id).html(data);
        }
      });
    }

  }


   function getParty(){
    var pur_company = $('#company').val();
    if(pur_company!='ALL')
    {
        $.ajax({
        type: "post",
        url: "<?php echo page_url; ?>Inventory/get_company_party",
        data: "company=" + pur_company,
        success: function(data) {                  
        $("#hpcl_locations").html(data);                  
        }     

        });
    }else
    {
        $("#hpcl_locations").html("<option value='ALL'>ALL</option>"); 

    }

}


function getPartySelected(){
    var pur_company = $('#company').val();
    var party="<?php echo $this->uri->segment(5);?>";
    if(pur_company!='ALL')
    {
        $.ajax({
        type: "post",
        url: "<?php echo page_url; ?>Inventory/get_company_partySelected",
        data: "company=" + pur_company+"&selectedParty="+party,
        success: function(data) {                  
        $("#hpcl_locations").html(data);                  
        }     

        });
    }else
    {
        $("#hpcl_locations").html("<option value='ALL'>ALL</option>"); 

    }

}
</script>
    </body>
</html>