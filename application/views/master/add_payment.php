<?php
$CI =& get_instance();
$CI->load->model('Master_model', 'master');
$getCollectionID = $CI->master->getCollectionID();

$balance = '';
$collection_id = '';
$collection_primary_id = '';
$balance_id = '';

if($getCollectionID != '') {
    $arr = explode('|', $getCollectionID);
    $balance = $arr[0];
    $collection_id = $arr[1];
    $collection_primary_id = $arr[2];
    $balance_id = $arr[3];

}
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
<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

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
                            <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success" name="action_button"><i class="fa fa-arrow-left"></i>Back</button></a>
                            <div class="btn-group">
                               
                            </div>
						    <div class="btn-group pull-right">
						      <button class="btn btn-warning waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">Add Payment</button><br/><br/><br/>
                               <button class="btn btn-danger waves-effect waves-light" data-toggle="modal" data-target="#myModal">Add Debit Note</button>
                            </div>
                           
                            <h4 class="page-title">Sunder Payments to HPCL</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

                 <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered pretty">
                                <thead>

                                <tr>
                                <th>Sr No.</th>
                                <th>Collection Date</th>
                                <th>Collection ID</th>
                                <th>Collection Amount</th>
                                <th>Balance</th>
                                <th>Remarks</th>
                                <th>Credit/Debit Note</th>
                                <th>Added On</th>
                                <th>Added By</th>
                                <th>Delete</th>
                                  
                                  <!--   <th>Edit</th> -->
                                    
                                </tr>
                                </thead>
                                
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
 <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="<?php echo page_url;?>Wallet/add_payment">
<div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">Add Payment</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">
                                       
                                                <div class="col-md-6">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">Payment Date</label>

														<span id="error_interest" style="color:red;"></span>

                                                        <input type="date" class="form-control" id="payment_date" name="payment_date" placeholder="" value="" required autocomplete="off">

                                                    </div>

                                                </div>


                                                    <div class="col-md-6">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">Collection ID</label>

                                                        <span id="error_interest" style="color:red;"></span>

                                                        <input type="text" class="form-control" id="collection_id" name="collection_id" placeholder="" value="" required autocomplete="off" style="text-transform: capitalize;">

                                                    </div>

                                                </div>


                                                 <div class="col-md-6">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">Collection Amount</label>

                                                        <span id="error_interest" style="color:red;"></span>

                                                        <input type="text" class="form-control allow_decimal" id="amount" name="amount" placeholder="" value="" required autocomplete="off">

                                                    </div>

                                                </div>

                                                  <div class="col-md-6">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">Remarks</label>

                                                        <span id="error_interest" style="color:red;"></span>

                                                        <textarea class="form-control" id="remarks" name="remarks" placeholder="" value="" required></textarea>

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

                            <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                <form id="loginForm" method="post" action="<?php echo page_url;?>Wallet/save_credit_debit_note">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    <h4 class="modal-title">Add Debit Note</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="field-1" class="control-label">DEBIT NOTE</label>
                                                            <span id="error_interest" style="color:red;">*</span>
                                                            <select class="form-control" name="credit_debit" id="credit_debit" onchange="show_note_details()" required="">
                                                                <option value="">SELECT</option>
                                                               <!--  <option value="1">CREDIT NOTE</option> -->
                                                                <option value="2">DEBIT NOTE</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="show_details" style="display: none;">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="field-1" id="credit_debit_for">*</label>
                                                                <span id="error_interest" style="color:red;">*</span>
                                                                <input type="text" class="form-control mand" name="credit_debit_for" required>
                                                            </div>
                                                        </div>

                                                         <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="field-1">Invoice Description</label>
                                                                <span id="error_interest" style="color:red;">*</span>
                                                                 <input type="text" class="form-control mand" name="debit_invoice" required>
                                                            </div>
                                                        </div>

                                                           <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="field-1">Invoice Date</label>
                                                                <span id="error_interest" style="color:red;">*</span>
                                                                <input type="date" class="form-control mand" name="debit_date" required>
                                                            </div>
                                                        </div>

                                                         <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="field-1" id="credit_debit_amount"></label>
                                                                <span id="error_interest" style="color:red;">*</span>
                                                                <input type="text" class="form-control allow_decimal mand" name="credit_debit_amount" id="credit_debit_amountS" onblur="get_collection_id();">
                                                                <input type="hidden" name="collection_balance" value="<?php echo $balance;?>">
                                                            </div>
                                                        </div>
                                                     
                                                        <div class="col-md-12" id="collectdata">
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <input type="submit" id="SAVECOLECTION" class="btn btn-info" value="Submit"> 
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div><!-- /.modal -->                            


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

        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

       

        <script>
		$( document ).ready(function() {
			$('#example').dataTable({
				"bProcessing": true,
				"pagination":true,
				"sAjaxSource": "<?php echo page_url;?>Wallet/interest_list",
				"aoColumns": [
							{ mData: 'sr_no' } ,
							{ mData: 'collection_date' },
                            { mData: 'collection_id' },
                            { mData: 'collection_amount' },
                            { mData: 'balance' },
                            { mData: 'remarks' },
                            { mData: 'credit_debit_note' },
                            { mData: 'addedOn' },
                            { mData: 'addedBy' }
							,{ mData: 'delete' }				
						]
			});  

			$(".allow_decimal").on("input", function(evt) {

			   var self = $(this);

			   self.val(self.val().replace(/[^0-9\.]/g, ''));

			   if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 

			   {

			     evt.preventDefault();

			   }

			 });

		});

        function show_note_details() {
            $('.mand').attr('required', false);
            $('.show_details').css('display', 'none');
            $('#credit_debit_for').text('');
            $('#credit_debit_amount').text('');

            if($('#credit_debit').val() == 1) {
                $('.show_details').css('display', '');
                $('#credit_debit_for').text('Credit Reason');
                $('#credit_debit_amount').text('Credit Amount');
                $('#credit_debit_collection').text('The amount will be credited to this Collection ID.');
                $('.mand').attr('required', true);
            } else if($('#credit_debit').val() == 2) {
                $('.show_details').css('display', '');
                $('#credit_debit_for').text('Debit Description');
                $('#credit_debit_amount').text('Debit Amount');
                $('#credit_debit_collection').text('The amount will be debited from this Collection ID.');
                $('.mand').attr('required', true);

            }




        }

        function get_collection_id()
        {
            var credit_debit_amount=$("#credit_debit_amountS").val();
            $("#SAVECOLECTION").attr('disabled',true);
              $("#SAVECOLECTION").val('Submit');
            $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Wallet/getcollection_id_to_adjust",
            data:"amount="+credit_debit_amount,
            success:function(data){

                var d=data.split('|');

                $("#collectdata").html(d[0]);
                if(d[1]==1)
                {
                $("#SAVECOLECTION").attr('disabled',false);
                }else
                {
                    $("#SAVECOLECTION").attr('disabled',true);
                    $("#SAVECOLECTION").val('No Collection ID found, so you cannot add Debit Note');
                }

            }
            });


        }


function deletecollection_id(collection_id,flag)
{
    if(flag==0)
    {
    if(confirm('Do you really want revoke all payments done via this collection ID. All payments related to it will be deleted?'))
    {
         document.location="<?php echo page_url;?>Wallet/delete_collection_id/"+collection_id+"/"+flag;
    }
    }else
    {
        if(confirm('Do you really want revoke all payments done via this collection ID and delete this collection id?'))
    {
         document.location="<?php echo page_url;?>Wallet/delete_collection_id/"+collection_id+"/"+flag;
    }

    }   

}
</script>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script language="javascript" type="text/javascript">   
jQuery.noConflict();
$(document).ready(function() {



});
</script>
    </body>
</html>