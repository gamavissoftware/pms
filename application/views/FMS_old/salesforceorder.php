<!DOCTYPE html>



<html>



    <head>



        <meta charset="utf-8">



        <meta name="viewport" content="width=device-width, initial-scale=1.0">



        <meta name="description" content="">



        <meta name="author" content="<?php echo copyright; ?>">







        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">







        <title><?php echo sitetitle; ?> SALESFORCE ORDER MANAGEMENT</title>







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



<link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">



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



		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">



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



table.manglesh thead th {



				background: <?php echo $LOGO->colorcode;?>;



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







.select2-container {



width: 100% !important;



padding: 0;



}











.backgroundcolor{ background-color:#FEFDCD !important; }



</style>



    </head>











    <body>











        <!-- Navigation Bar-->



                <header id="topnav">



          <?php $this->load->view('common/nav-menu');?>



        </header>



        <!-- End Navigation Bar-->




        <?php $this->load->view('common/info-section.php');?>






        <div class="wrapper">



            <div class="container-fluid">







                <!-- Page-Title -->



                <div class="row">



                    <div class="col-sm-12">



<div class="col-sm-4">

	<div class="page-title-box">



	<h4 class="page-title">SALESFORCE UNAPPROVED ORDER LIST</h4>



	</div>

      	</div>

      	      	

	<div class="col-sm-4">

	<div class="page-title-box">

	<span class="btn btn-warning" onclick="openmissorder();">FETCH MISSING ORDER</div>

	</div>

	</div>



                    </div>



                    <div class="col-md-12"><?php echo $this->session->flashdata('message'); ?></div>



                </div>



                <!-- end page title end breadcrumb -->







		<div class="row">



                    <div class="col-sm-12">



                        <div class="card-box table-responsive">



                            <table id="example" class="table table-bordered manglesh">



                                <thead>



                                <tr>



                                    <th>Sr No.</th>



									<th>PLAN ORDER</th>



									<th>ADDED ON</th>



									



									   <th>ORDER TYPE</th>



									<th style="width:50%">INSTRUMENTS</th>



                                 



                                    <th>REGION MARKETING PERSON</th>



                                    <th>PO NUMBNER</th>



                                    <th>COMPANY NAME</th>



									<!-- <th>CONTACT PERSON</th>



									 <th>DESIGNATION</th>



									<th>ADDRESS WITH PINCODE</th>



									<th>EMAIL ID</th>



									<th>MOBILE NUMBER</th>



									<th>PHONE</th>-->



									<th>INTERNAL ORDER NUMBER</th>



							<!--<th>DISCOUNT</th>

									<th>ORDER VALUE AFTER DISCOUNT</th>



									<th>ADVANCE AMOUNT RECEIVED</th>



									<th>PAYMENT TERMS</th>



									<th>INSTALLATION CHARGES TYPE</th>-->



									<th>PACKING CHARGES</th>



									<th>FREIGHT TYPE</th>



									<th>REMARKS</th>



									



									



                                </tr>



                                </thead>











                                <tbody>



								



                                </tbody>



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









<div id="edit_instruments" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

 <form method="post" action="<?php echo page_url;?>FMS/updateInstrumentID/<?php echo $this->uri->segment(3);?>">

    <div class="modal-dialog">

      <div class="modal-content">

        <div class="modal-header">

            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

            <h4 class="modal-title" id="modal_title"></h4>

        </div>

        <div class="modal-body">

            <div class="row">

                <div class="col-md-12">

                    <div class="form-group">

                      <label for="field-1" class="control-label">Instruments</label><br>

                        <span id="error_business_loc" style="color:red;"></span>

                        <select id="getInstruments" class="form-control" name="getInstruments">

                        </select>

                    </div>

                </div>

            </div>  

        </div>

        <div class="modal-footer">

            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

            <input type="hidden" name="hidden_id" id="hidden_id" class="btn btn-info" value=""> 

            <input type="submit" id="save" class="btn btn-info" value="Submit"> 

        </div>

     </div>

   </div>

</form>

</div>









<!-- Modal -->

<div id="missorder" class="modal fade" role="dialog">

  <div class="modal-dialog">

<form action="<?php echo page_url;?>Salesforceorder/fetchmissingorder" method="post" onsubmit="return validate();">

    <!-- Modal content-->

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal">&times;</button>

        <h4 class="modal-title">Fetch Missing Order</h4>

      </div>

      <div class="modal-body">

       <div class="row">

       

       <div class="col-md-4">

       <input type="number" name="iono" id="intno" class="form-control" placeholder="ENTER IO NO."></div>

       <input type="hidden" name="uri" value="<?php echo $this->uri->segment(3);?>">

       </div>

      </div>

      <div class="modal-footer">

        <input type="submit" name="sub" id="sub" value='Submit' class="btn btn-success">

      </div>

    </div>

    </form>



  </div>

</div>



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



		<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>



		



		<script type="text/javascript">



$(document).ready(function(){



	











 var i=2;



 $('#addmore_btn1').click(function(){



 i++;



 



 $('#dynamictasks1').append('<div id="row'+i+'" class="row"><div class="col-md-9"><div class="form-group"><label for="field-1" class="control-label">INSTRUMENT NAME</label><span id="error_instruments" style="color:red;">*</span><select class="form-control select3'+i+'" style="text-transform: uppercase;" name="instruments[]" id="instruments'+i+'"></select></div></div><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">QUANTITY</label><input type="text" class="form-control" id="qty" name="qty[]" placeholder="" required></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:30px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div><br/>');



 initializeSelect2('select3'+i);



 });



 



 



 



 $(document).on('click', '.btn_remove', function(){



 var button_id = $(this).attr("id");



 $('#row'+button_id+'').remove();



 });



});



	  </script>



	  <script>



	  function initializeSelect2(selectElementObj) {



		  var purl="<?php echo page_url;?>FMS/getmachines/";



         $('.'+selectElementObj).select2({



			 



			







			    placeholder: 'TYPE TO SELECT',



minmumInputLength:4,



		allowClear: true,







        ajax: {







          url: purl,







          dataType: 'json',







          delay: 250,



			



          processResults: function (data) {







			 







            return {







              results: data







            };







          },







          cache: true







        }



			



			 



			 



			 



		 });



         



      }



	  </script>



		<script>



		// Time Picker



            jQuery('#timepicker').timepicker({



                defaultTIme : false



            });



			jQuery('#timepicker4').timepicker({



                defaultTIme : false



            });



            jQuery('#timepicker2').timepicker({



                showMeridian : false



            });



            jQuery('#timepicker3').timepicker({



                minuteStep : 15



            });



		</script>



 <script>



$( document ).ready(function() {



	



	var ot=$("#order_type").val();



	var purl="<?php echo page_url;?>FMS/getmachines/";



$('.select2').select2({ });



$('.select3').select2({ });



$('#sele').select2({ 











			    placeholder: 'TYPE TO SELECT',



minmumInputLength:4,



		allowClear: true,







        ajax: {







          url: purl,







          dataType: 'json',







          delay: 250,







          processResults: function (data) {







			 







            return {







              results: data







            };







          },







          cache: true







        }







			 



			 



			 



			 















});





var instruments_url ="<?php echo page_url;?>FMS/getAllInstruments/";

$('#getInstruments').select2({

          placeholder: 'TYPE TO SELECT',

          minmumInputLength:4,

          allowClear: true,



        ajax: {

          url: instruments_url,

          dataType: 'json',

          delay: 250,



          processResults: function (data) {

            return {

              results: data

            };

          },

          cache: true

        }

});







$('.select4').select2({ });



  



$('#example').dataTable({



 "bProcessing": false,



 "pagination":true,



 fixedHeader: {



            header: true



        },



   scrollCollapse: true,



   fixedColumns:   {



            leftColumns: 3



        },



        createdRow: function (row, data, dataIndex) {



    $(row).addClass('parenttr');



},



 "sAjaxSource": "<?php echo page_url;?>FMS/sforder_list/<?php echo $this->uri->segment(3);?>",



 "aoColumns": [



						{ mData: 'sr_no' } ,



	



						{ mData: 'planorder' },



                        { mData: 'added_on' },



                          { mData: 'order_type' },



						{ mData: 'itemname' },



                      



                        { mData: 'marketing_person' },



						{ mData: 'po_number' },



						{ mData: 'company_name' },



	/** { mData: 'contactperson' },



	 { mData: 'designation' },



						{ mData: 'address' },



						{ mData: 'email' },



						{ mData: 'mobile_number' },



	 { mData: 'phone' }, **/



						{ mData: 'internal_order_no' },



					/**	{ mData: 'discount' },



						{ mData: 'order_value_after_discount' },



						{ mData: 'advance_amount' },



						{ mData: 'payment_terms' },



						{ mData: 'installation_charges' }, **/



						{ mData: 'packingcharges' }, 



						{ mData: 'freigntcharges' },



						{ mData: 'remarks' }



				



						



						



                ],"initComplete": function(settings, json) {



  



   getcolors()



  }



  



        });  



   



   



   $('#example').on('draw.dt', function() {



    // do action here







    getcolors();



});  







  $('#example').on('search.dt', function() {



   



    getcolors();



});  



});







</script>







<script language="javascript" type="text/javascript">   



function getcolors()



{







 



$("#example tr.parenttr").each(function(){



		



	var currentRow=$(this);



	



	    var col1_value=currentRow.find("td:eq(6)").text();



	   col1_value=col1_value.trim();



	 



		if(col1_value=='SERVICE' || col1_value=='service' )



		{



			currentRow.addClass("backgroundcolor");



		} 



	







	 



});



}



</script>







<script>



$(document).ready(function(){



  $("#loginForm").on("submit", function(){



    $("#pageloader").fadeIn();



  });//submit



});//document ready





function agreethis(id)

{

  if(confirm('Sure for Migration?'))

  {

    var ty="<?php echo $this->uri->segment(3);?>";

    document.location="<?php echo page_url;?>FMS/migrateorder/"+id+"/"+ty;

    return true;

  }







}





function showModalInstruments(internal_order_no, id) {

  $("#edit_instruments").modal('show');

  $("#modal_title").text('Add Intrument for Internal Order No: '+internal_order_no);

  $("#hidden_id").val(id);

}



function deleteOrderInstruments(id) {

  if(confirm('Are you sure you want to delete this instrument?')) {

    //var st=$("#city"+id).val();

      $.ajax({

      type:"post",

      url:"<?php echo page_url;?>FMS/deleteOrderInstrument/",

      data:{id:id},

        success:function(data) {

          if (data == 1) {

              location.reload();

          }

        }

      });  

  }

}



function openmissorder()

{

$("#missorder").modal('show');



}



function validate()

{

$("#sub").attr('disabled',true);

$("#sub").val('Fetching please wait...');

var ino=$("#intno").val();



if(ino=='')

{

alert('Internal Order No. is mandatory');

$("#sub").attr('disabled',false);

$("#sub").val('Submit');

return false;



}else

{

$("#sub").attr('disabled',true);

$("#sub").val('Fetching please wait...');

return true;

}

}

</script>









</body>



</html>

