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
                    <div class="col-md-4 page-title-box">
                    <h4 class="page-title">RUNNING CUSTOMERS QUOTATION</h4>
                  </div>
              
                <form action="<?php echo page_url;?>Customer/filter_running_quote" method="post">
                  <div class="col-sm-8 pull-right">

            <div class="page-title-box card-box" style="min-height: 100px;">
                
<div class="col-md-3">
<div class="form-group">
<label>From</label>
              <input type="date" name="firstdate" id="firstdate" class="form-control" required value="<?php echo $this->uri->segment(3);?>"> 
              </div>
              </div>

              <div class="col-md-3">
              <div class="form-group"><label>to</label> 
              <input type="date" id="lastdate" name="lastdate" class="form-control" required value="<?php echo $this->uri->segment(4);?>">
</div>
</div>

<div class="col-md-4">
                    <div class="form-group"><label>User</label>
                <select class="form-control" name="user" id="user" required >
                                <option value="ALL">ALL</option>
                                <?php 
                                $q = $this->db->select('user_id,first_name,last_name')->from('system_users')->where('user_status',1)->get();
                                foreach($q->result() as $row){
                                ?>
                                <option value="<?php echo $row->user_id;?>" <?php if($this->uri->segment(5)==$row->user_id){?> selected <?php } ?>><?php echo $row->first_name;?> <?php echo $row->last_name;?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>

<div class="col-md-2">
<div class="form-group" style="padding-top:20px">
              <input type="submit" class="btn btn-success" value="Filter">
</div>
</div>


            </div>
          </div>
      </form>
           
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
                            <th>Sales Agent</th>
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
                           
                            <th>Generate Order</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>



             
          

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
  "sAjaxSource": "<?php echo page_url;?>Customer/quotation_running_report/<?php echo $this->uri->segment(3); ?>/<?php echo $this->uri->segment(4); ?>/<?php echo $this->uri->segment(5); ?>",
"aoColumns": [
                { mData: 'sr_no' } ,
                { mData: 'salesagent' } ,
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
