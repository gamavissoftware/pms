<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="NJ Media">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle; ?> Customer Payment Terms Approval</title>



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
                    <h4 class="page-title">CUSTOMER PENDING FOR PAYMENT TERM APPROVAL</h4>
                  </div>
                  <div class="col-md-6 page-title-box" style="margin-top: 30px;">
                    <!-- <a href="<?php echo page_url;?>Customer/discount_approval_history" class="btn btn-warning btn-xs pull-right">VIEW DISCOUNT APPROVAL HISTORY</a> -->
                  </div>
                </div>
                </div>
              <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
              <div class="row">

                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                           
                                <table id="example2" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                <th>SR NO.</th>
                                <th>OUR COMPANY</th>
                                <th>CUSTOMER COMPANY NAME</th>
                                <th>CUSTOMER NAME</th>
                                <th>PREVIOUS TERM</th>
                                <th>PAYMENT TERM</th>
                                <th>ADDED BY</th>
                                <th>ADDED ON</th>
                                <th>ACTION</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                </table>
                        </div>
                    </div>
                </div>

             <div id="payment_approve_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <form id="loginForm" method="post" action="<?php echo page_url;?>Customer/approve_customer_payment_terms_mainpage">
                    <input type="hidden" name="payment_app_id" id="payment_app_id">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title">Payment Term Approval</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Company Name</label>
                                           <input type="text" name="cname" id="cname" readonly class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Payment Term</label>
                                            <span id="error_to_date" style="color:red;">*</span>
                                            <select class="form-control" id="payment_term" name="payment_term" required="" onchange="check_for_credit()">
                                                <option value="">SELECT</option>
                                                <option value="2">Cash</option>
                                                <option value="3">Online</option>
                                                <option value="4">PDC</option>
                                                <option value="5">CREDIT</option>
                                                <option value="6">ADVANCE</option>
                                            </select>
                                        </div>
                                    </div>

                                    <script type="text/javascript">
                                        function check_for_credit()
                                        {
                                             $("#credit_terms_div").css('display','none');
                                            $("#credit_terms").attr('required',false);
                                            var pay=$("#payment_term").val();
                                            if(pay==4 || pay==5)
                                            {
                                                $("#credit_terms_div").css('display','');
                                                $("#credit_terms").attr('required',true);
                                            }else
                                            {
                                                 $("#credit_terms_div").css('display','none');
                                                $("#credit_terms").attr('required',false);
                                            }

                                        }
                                    </script>
                                    <div class="col-md-4" id="credit_terms_div" style="display:none;">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Credit Days</label>
                                            <span class="remove_mand" style="color:red;">*</span>
                                            <input type="text" class="form-control" name="credit_terms" id="credit_terms">
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



             <div id="payment_approve_modal_r" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <form id="loginForm" method="post" action="<?php echo page_url;?>Customer/approve_reject_payment_terms_change_req">
                    <input type="hidden" name="payment_app_id_r" id="payment_app_id_r">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title">Payment Term Approval</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Company Name</label>
                                           <input type="text" name="cname_r" id="cname_r" readonly class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Payment Term</label>
                                            <span id="error_to_date" style="color:red;">*</span>
                                            <select class="form-control" id="payment_term_r" name="payment_term_r" required="" onchange="check_for_credit_r()">
                                                <option value="">SELECT</option>
                                                <option value="2">Cash</option>
                                                <option value="3">Online</option>
                                                <option value="4">PDC</option>
                                                <option value="5">CREDIT</option>
                                                <option value="6">ADVANCE</option>
                                            </select>
                                        </div>
                                    </div>

                                    <script type="text/javascript">
                                        function check_for_credit_r()
                                        {
                                             $("#credit_terms_div_r").css('display','none');
                                            $("#credit_terms_r").attr('required',false);
                                            var pay=$("#payment_term_r").val();
                                            if(pay==4 || pay==5)
                                            {
                                                $("#credit_terms_div_r").css('display','');
                                                $("#credit_terms_r").attr('required',true);
                                            }else
                                            {
                                                 $("#credit_terms_div_r").css('display','none');
                                                $("#credit_terms_r").attr('required',false);
                                            }

                                        }
                                    </script>
                                    <div class="col-md-4" id="credit_terms_div_r" style="display:none;">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Credit Days <span style="color:red">*</span></label>
                                            <span class="remove_mand" style="color:red;">*</span>
                                            <input type="text" class="form-control" name="credit_terms_r" id="credit_terms_r">
                                        </div>
                                    </div>
                                </div> 

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Approve/Reject? <span style="color:red">*</span></label>
                                            <select name="decision" id="decision" class="form-control" onchange="decision_data();" required>
                                                <option value="">Select</option>
                                                <option value="1">Approve</option>
                                                <option value="2">Reject</option>
                                            </select>
                                        </div>
                                    </div>
                                    <script type="text/javascript">
                                        function decision_data()
                                        {
                                           var decision= $("#decision").val();
                                           if(decision==2)
                                           {
                                            $("#rej_rmk").css('display','');
                                            $("#rej_remarks").attr('required',true);
                                           }else
                                           {
                                            $("#rej_rmk").css('display','none');
                                            $("#rej_remarks").attr('required',false);
                                           }
                                        }
                                    </script>
 
                                     <div class="col-md-9" id="rej_rmk" style="display:none">
                                        <div class="form-group">
                                            <label>Reject Remarks? <span style="color:red">*</span></label>
                                           <textarea class="form-control" name="rej_remarks" id="rej_remarks"></textarea>
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

<script type="text/javascript">
    function approve_terms(custid)
{

      $.ajax({
      url: '<?php echo page_url; ?>Customer/get_payment_term/'+custid,
      type: 'get',
      success: function(data) {
        var d=data.split("|");
        $("#payment_approve_modal").modal('show');
        $("#payment_app_id").val(custid);
        if(d[0]!='')
        {
             $('#payment_term').val(d[0]);
             check_for_credit();
        }

        if(d[1]!='')
        {
             $("#credit_terms").val(d[1]);
        }
        if(d[2]!='')
        {
            $("#cname").val(d[2]);
        } 


      }
    });

    

}
</script>
        



     
<script type="text/javascript">
$( document ).ready(function() {
$('#example2').dataTable({
 "bProcessing": false,
 "pagination":true,
 fixedHeader: true,
"bSort": false,
 fixedColumns:   {
  leftColumns: 3
  },

"sAjaxSource": "<?php echo page_url;?>Customer/payment_term_approval",

 "aoColumns": [

                { mData: 'sr_no' },
                { mData: 'company_name' },
                { mData: 'cust_company_name' },
                { mData: 'customer_name' },
                { mData: 'previous' },
                { mData: 'payment_term' },
                { mData: 'addedOn' },
                { mData: 'addedBy' },
                { mData: 'action' }
            ]

        });   
        });   

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

function accept_reject_lead_remarks(detail_id) {
  $('#lead_detail_id').val(detail_id);
  $('#myLeadModal').modal('show');
}

function check_for_remarks() {
   var discount_approval = $('#discount_approval').val();

    $('.remove_mand').text('*');
    $('#remarks').attr('required', true);

    if(discount_approval == 1) {
      $('.remove_mand').text('');
      $('#remarks').attr('required', false);
    }
}

function check_for_lead_remarks() {
   var discount_approval = $('#lead_discount_approval').val();

    $('.remove_mand').text('*');
    $('#lead_remarks').attr('required', true);

    if(discount_approval == 1) {
      $('.remove_mand').text('');
      $('#lead_remarks').attr('required', false);
    }
}
</script>



</body>

</html>
