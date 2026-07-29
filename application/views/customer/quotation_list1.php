<?php
$user_role =$this->session->userdata['logged_in']['role'];
?>
<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="NJ Media">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle; ?> Quotation list</title>



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

    </head>





    <body>





        <!-- Navigation Bar-->

                <header id="topnav">

          <?php $this->load->view('common/nav-menu');?>

        </header>

        <!-- End Navigation Bar-->


        <div class="wrapper">
            <div class="container-fluid">
                <!-- Page-Title -->
              <div class="row">
                <div class="col-sm-12">
                    <div class="col-md-6 page-title-box">
                    <h4 class="page-title">QUOTATION LIST</h4>
                  </div>
                <?php if($user_role == 1) {?>
                  <div class="col-sm-6 pull-right">

            <div class="page-title-box pull-right card-box">
                <div class="col-md-4">
                    <div class="form-group"><label>Company</label>
                <select class="form-control" name="curl" id="curl" required >
                                <option value="">--Please Select--</option>
                                <?php 
                                $uri=$this->uri->segment(3);
                                $datestart=base64_decode($this->uri->segment(4));
                                $dateend=base64_decode($this->uri->segment(5));
                                $q = $this->db->select('id,company_name')->from('customer_detail')->where('status',1)->get();
                                foreach($q->result() as $row){
                                ?>
                                <option value="<?php echo $row->id;?>" <?php if($uri==$row->id){?> selected <?php } ?>><?php echo $row->company_name;?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
<div class="col-md-3">
<div class="form-group">
<label>From</label>
              <input type="date" name="firstdate" id="firstdate" class="form-control" required value="<?php if($datestart<>''){ echo $datestart; } ?>"> 
              </div>
              </div>
              <div class="col-md-3">
              <div class="form-group"><label>to</label> 
              <input type="date" id="lastdate" name="lastdate" class="form-control" required value="<?php if($dateend<>''){ echo $dateend; } ?>">
</div>
</div>
<div class="col-md-2">
<div class="form-group" style="padding-top:20px">
              <button type="submit" class="btn btn-danger" onclick="getdatewisedata();">Submit</button>
</div>
</div>
            </div>
          </div>
              <?php } ?>
                </div>
                </div>
              <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
              <div class="row">
                <div class="col-sm-12">
                  <div class="card-box table-responsive">
                    <table id="example1" class="table table-striped table-bordered manglesh">
                      <thead>
                        <tr>
                            <th>SR NO</th>
                            <th>Create date</th>
                            <th>Comapany Name</th>
                            <th>Company Customer Name</th>
                            <th>Contact Person</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>City</th>
                            <th>Address</th>
                            <th>Product</th>
                            <th>Preview Quote</th>
                            <th>Action</th>
                            <th>Generate Order</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-sm-12">
                    <div class="col-md-6 page-title-box">
                       <h4 class="page-title text-center">QUOTATION HISTORY</h4>
                    </div>
                </div>
              </div>

              <div class="row">
                <div class="col-sm-12">
                  <div class="card-box table-responsive">
                    <table id="example2" class="table table-striped table-bordered manglesh">
                      <thead>
                        <tr>
                            <th>SR NO</th>
                            <th>Create date</th>
                            <th>Comapany Name</th>
                            <th>Company Customer Name</th>
                            <th>Contact Person</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>City</th>
                            <th>Address</th>
                            <th>Product</th>
                            <th>Preview Quote</th>
                            <th>Action</th>
                            <th>Generate Order</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <form id="loginForm" method="post" action="<?php echo page_url;?>Customer/save_discount_remarks" enctype="multipart/form-data">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title">Approve/Reject Discount</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Approve/Reject</label>
                                            <span id="error_to_date" style="color:red;">*</span>
                                            <input type="hidden" name="detail_id" id="detail_id">
                                            <select class="form-control" name="discount_approval" required="">
                                                <option value="">SELECT</option>
                                                <option value="1">Approve</option>
                                                <option value="2">Reject</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Remarks</label>
                                            <span id="error_to_date" style="color:red;">*</span>
                                            <textarea class="form-control" name="remarks" required=""></textarea>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                            </div>
                        </div>
                    </div>
                </form>
            </div><!-- /.modal -->  
          

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
$('#example1').dataTable({
 "bProcessing": false,
 "pagination":true,
 fixedHeader: true,
"bSort": false,
  pageLength:50,
  "sAjaxSource": "<?php echo page_url;?>Customer/quotation_list/<?php echo $this->uri->segment(3); ?>/<?php echo $this->uri->segment(4); ?>/<?php echo $this->uri->segment(5); ?>",
"aoColumns": [
                { mData: 'sr_no' } ,
                { mData: 'create_date' },
                { mData: 'company' },
                { mData: 'company_name' },
                { mData: 'customer_name' },
                { mData: 'email' },
                { mData: 'mobile' },
                { mData: 'city' },
                { mData: 'address' },
                { mData: 'products' },
                { mData: 'quotation' },
                { mData: 'edit' },
                { mData: 'generate_order' }
                
                
        ]

        }); 

    $('#example2').dataTable({
      "bProcessing": false,
      "pagination":true,
      fixedHeader: true,
      "bSort": false,
        pageLength:50,
      "sAjaxSource": "<?php echo page_url;?>Customer/quotation_history_list/<?php echo $this->uri->segment(3); ?>/<?php echo $this->uri->segment(4); ?>/<?php echo $this->uri->segment(5); ?>",
      "aoColumns": [
                { mData: 'sr_no' } ,
                { mData: 'create_date' },
                { mData: 'company' },
                { mData: 'company_name' },
                { mData: 'customer_name' },
                { mData: 'email' },
                { mData: 'mobile' },
                { mData: 'city' },
                { mData: 'address' },
                { mData: 'products' },
                { mData: 'quotation' },
                { mData: 'edit' },
                { mData: 'generate_order' }
                
                
        ]

        });  

});

function getdatewisedata(vaal) {
      //alert('ujhyhb');
      var productid = $('#curl').val();
      var fdate = $('#firstdate').val();
      //alert(fdate);
      var firstdate = btoa(fdate);
      //alert(firstdate);
      var ldate = $('#lastdate').val();
      var lastdate = btoa(ldate);
      if(productid !='' && firstdate!='' && lastdate !='')
      {
      document.location = "<?php echo page_url;?>Customer/quotation_dashboard/"+productid+"/" + firstdate + "/" + lastdate;
        }else
        {
            alert('Please Select company,First date and Last date. ');
        }

    }



</script>



<script>

function checkallitem()

{

    if($('.selectall').is(":checked"))

    {

        $(".checkitems").prop('checked', true);

    }else

    {

        $(".checkitems").attr('checked',false);

    }

    


}



function validateitems()
{
    
     $("#sub").attr('disabled',true);
        
        
      $("#sub").val('Please Wait..');
  
   var checkeditem=$('#storefrm input:checked').length;
   if(checkeditem>0)
   {
        $("#sub").attr('disabled',true);
        
        
        $("#sub").val('Please Wait..');
     
       return true;
   }else
   {
        $("#sub").attr('disabled',false);
        
        $("#sub").val('Mark as recieved');
     
       alert('Select at least one item');
       return false;
   }
     
    
    
}
</script>




<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

<script>

$(document).ready(function(){

  $("#loginForm").on("submit", function(){

    $("#pageloader").fadeIn();

  });//submit

});//document ready


function accept_reject_remarks(detail_id) {
  $('#detail_id').val(detail_id);
  $('#myModal').modal('show');
}
</script>



</body>

</html>
