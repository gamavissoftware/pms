<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="NJ Media">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title>MRN QC</title>



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

                        <div class="page-title-box">

						

                           <h4 class="page-title">MRN QC</h4>

                            <!-- <h4 class="page-title">MRN QC</h4> -->
							<div class="btn-group pull-right">
						  <a href="<?php echo page_url; ?>Reporting/gateentry_history"><span class="btn btn-danger btn-xs pull-right">View History</span></a>
                            </div>

                        </div>

                    </div>

                </div>

                <!-- end page title end breadcrumb -->

	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

		<div class="row">

                    <div class="col-sm-12">

                        <div class="card-box table-responsive">

<form id="qcform" onsubmit="return validatewholeform();" action="<?php echo page_url;?>Store/mergerqcmrn" method="post" enctype="multipart/form-data">

                            <table id="example" class="table table-striped table-bordered manglesh">

                                <thead>

                                <tr>

                          

                                    <th>Sr No.</th>
                                     <th>MRN DATE.</th>
                                     <th>PENDING SINCE</th>
                                     <th>PLANNED DATE.</th>

                                    <th>PO NO.</th>

									<th>VENDOR NAME</th>

									<th>ITEM NAME</th>

									<!--<th>ITEM TYPE</th>-->

									<!--<th>FINCODE</th>-->

									<th>SPECIFICATION</th>

									<!--<th>PREVIOUS INWARDED</th>-->

									<!--<th>PENDING QTY</th>-->

								

									<th style="width:60px;">GATE ENTRY NO.</th>

										<th style="width:60px;">BILL NO.</th>

											<th style="width:60px !important;">RECVD QTY AT GATE</th>

									<th style="width:60px;">QC APPROVED QTY.</th>

									<th style="width:60px;">QC REJECT QTY.</th>

									<th style="width:60px;">QC REJECTION REMAKRS</th>

									<th style="width:60px;">QC REJECTION IMAGE</th>

								

								

									

									

                                </tr>

                                </thead>





                                <tbody>

								

                                </tbody>

                            </table>



<input type="submit" name="sub" id="sub" value="SUBMIT" class="btn btn-warning">

                       </form>



 </div>

                    </div>

                </div>
                
                
                  <div class="row">

                    <div class="col-sm-12">

                        <div class="page-title-box">

						

                           

                            <h4 class="page-title">MRN QC HISTORY</h4>

                        </div>

                    </div>

                </div>
                
                 <div class="row">

                    <div class="col-sm-12">

                        <div class="card-box table-responsive">

                            <table id="example1" class="table table-striped table-bordered manglesh">

                                <thead>

                                <tr>

                                 <th>Sr No.</th>
                                <th>MRN DATE.</th>
                                
                                <th>PO NO.</th>
                                
                            
                                <th>ITEM NAME</th>
                                
                                
                                <th>SPECIFICATION</th>
                                
                                <th style="width:60px;">GATE ENTRY NO.</th>
                                
                                <th style="width:60px;">BILL NO.</th>
                                
                                <th style="width:60px !important;">RECVD QTY AT GATE</th>
                                
                                <th style="width:60px;">QC APPROVED QTY.</th>
                                
                                <th style="width:60px;">QC REJECT QTY.</th>
                                
                                <th style="width:60px;">QC REJECTION REMAKRS</th>
                                
                                <th style="width:60px;">QC REJECTION IMAGE</th>

								
                                </tr>

                                </thead>





                                <tbody>

								

                                </tbody>

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

   fixedColumns:   {

            leftColumns: 3

        },

 "sAjaxSource": "<?php echo page_url;?>Reporting/openpoforgateentry",

 "aoColumns": [

					{ mData: 'sr_no' },

					{ mData: 'mrnon' },
                    { mData: 'pendingsince' },
                    { mData: 'planneddate' },
					{ mData: 'pono' },

	

					{ mData: 'vendorname'},

					{ mData: 'itemname'},

				//	{ mData: 'itemtype'},

				//	{ mData: 'fincode'},

					{ mData: 'specialization'},

				//	{ mData: 'previousinwarded'},

					//{ mData: 'reqty'},

				

					{ mData: 'gateentry'},

					{ mData: 'billno'},

					{ mData: 'recqty'},

					{ mData: 'approved'},

					{ mData: 'reject'},

					{ mData: 'rejremarks'},

					{ mData: 'rejectimage'}

				

						

						

                ]

        }); 
        
        
        
        $('#example1').dataTable({

 "bProcessing": false,

 "pagination":true,

fixedHeader: true,

   fixedColumns:   {

            leftColumns: 3

        },

 "sAjaxSource": "<?php echo page_url;?>Reporting/qchistory",

 "aoColumns": [

					{ mData: 'sr_no' },
						{ mData: 'mrnon' },

					{ mData: 'pono' },

	

				//	{ mData: 'vendorname'},

					{ mData: 'itemname'},

				//	{ mData: 'itemtype'},

				//	{ mData: 'fincode'},

					{ mData: 'specialization'},

				//	{ mData: 'previousinwarded'},

					//{ mData: 'reqty'},

				

					{ mData: 'gateentry'},

					{ mData: 'billno'},

					{ mData: 'recqty'},

					{ mData: 'approved'},

					{ mData: 'reject'},

					{ mData: 'rejremarks'},

					{ mData: 'rejectimage'}

				

						

						

                ]

        });   
  

});





function checkitem(id)

{

	

	if($('#checkone' + id).is(":checked"))

	{

		$(".formdata"+id).css('display','');

		

	}else

	{

		$(".formdata"+id).css('display','none');

		

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

</script>

<script>

function validatewholeform()

{



$("#sub").attr('disabled',true);
    
$("#sub").val('Please Wait..');


var flag=true;



var chlend=$('[name="check[]"]:checked').length;

if(chlend>0)

{

$("input[name='check[]']:checked").each(function (index, obj) {

        // loop all checked items

		var rowid=$(this).val();

		



		var originalqty=$("#originalqty"+rowid).val(); // PO WISE REQUIRED QTY

		var recqty=$("#recvqty"+rowid).val(); // RECIEVED QTY

		var gateentry=$("#gateentry"+rowid).val(); // GATE ENTRY NO

		var approved_qty=$("#approved_qty"+rowid).val(); //APPROVED

		var reject=$("#reject"+rowid).val(); // REJECTED

		var rejectremarks=$("#chtype"+rowid).val(); // REJECT REMARKS

		var rejectfile=$("#rejfile"+rowid).val(); // REJECT FILE



		if(originalqty !='' && recqty !='' && gateentry!='' && approved_qty!='' && reject!='')

		{

			

		

			if(parseFloat(originalqty)<parseFloat(recqty))

			{

			alert('Recieved Qty Cannot be greater than Required QTY');

			flag=false;

			return false;

$("#sub").attr('disabled',false);
            
            $("#sub").val('Submit');
            
			}

			

			var totqty=parseFloat(approved_qty)+parseFloat(reject);

			if(recqty!=totqty)

			{

				alert('APPROVED & REJECT QTY CANNOT BE GREATER THAN RECIEVED QTY');

				flag=false;
 $("#sub").attr('disabled',false);
                
                $("#sub").val('Submit');
                
				return false;

			}



				

			if(reject>0)

			{

				if(rejectremarks=='')

				{

					alert('REJECTION REMARKS REQUIRED IN CASE OF REJECTION');

					flag=false;

 $("#sub").attr('disabled',false);
                    
                    $("#sub").val('Submit');
					return false;

					

				}



				if(rejectfile=='')

				{

					alert('REJECTION IMAGE FILE REQUIRED IN CASE OF REJECTION');

					flag=false;

  $("#sub").attr('disabled',false);
                    
                    $("#sub").val('Submit');
					return false;



				}



			}





		}else

		{

				alert('Please enter all details');

				flag=false;

 $("#sub").attr('disabled',false);
                    
                $("#sub").val('Submit');
				return false;

		}

	});



}else

{



alert('Please select at least one Item for QC');

				flag=false;

 $("#sub").attr('disabled',false);
                
                $("#sub").val('Submit');
				return false;

}



				if(flag==false)

				{

				return false;
				  $("#sub").attr('disabled',false);
                
                $("#sub").val('Submit');

				}else

				{



$("#sub").css('disabled',true);
                $("#sub").val('Please Wait..');
					$("#sub").css('disabled',true);

					$("#qcform").submit();



				}

			

		









}


  function allowdeciamlonly(el)
  {
 var ex = /^[0-9]+\.?[0-9]*$/;
 if(ex.test(el.value)==false){
   el.value = el.value.substring(0,el.value.length - 1);
  }
}
 </script>

</body>

</html>
