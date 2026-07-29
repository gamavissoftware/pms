<?php
$CI =& get_instance();
$CI->load->model('Store_model');
$jbvalue=$CI->Store_model->jobcardunapprovedpo();
$indentvalue=$CI->Store_model->indentunapprovedpo();
$imsvalue=$CI->Store_model->imsunapprovedpo();
$totalmonthlypo=$CI->Store_model->totalapprovedpothismonth();
?>

<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>PENDING PO FOR APPROVAL</title>

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

  font-size: 14px;

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

#fixedbutton {
position: fixed;
top: 50%;
right: 0%;
z-index: 999;
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




                <div class="row">

                    <div class="col-sm-12">

                        <div class="page-title-box">

                            <h4 class="page-title text-center">LAST 90 DAYS PO HISTORY</h4>

                        </div>
<div class="row">
						<div class="col-md-6"></div>
						<div class="col-md-6"></div>
					</div>
					<div class="row card-box">
						<form method="post" action="<?php echo page_url;?>Reporting/filter_po_history">
							
							<div class="col-md-3">
								<div class="form-group">
									<label>From</label>
									<input type="date" class="form-control" name="date" id="date" value="" required="required">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>To</label>
									<input type="date" class="form-control" name="enddate" id="date" value="" required="required">
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" style="padding-top:20px">
									<label></label>
									<input type="submit" class="btn btn-success" value="Search">
								</div>
							</div>
							</form>
						</div>
                    </div>
					

                </div>

		  

		  

		  <div class="row">

                    <div class="col-sm-12">

                        <div class="card-box table-responsive">

                            <table id="example1" class="table table-bordered manglesh">

                                <thead>

                                <tr>

                                   <th>Sr No.</th>

                                   <th>PR NO</th>

									<th>PO NO.</th>

									<th>SOURCE</th>

									<th>VENDOR NAME</th>

									<th>ITEM</th>

									<th>QTY</th>

									<th>PRICE PER UNIT</th>

									<th>LAST TIME PURCHASE RATE</th>

									<th>DIFFRENCE</th>

									<th>CREATED BY</th>

									<th>CREATED ON</th>
									<th>APPROVED ON</th>

									<th>STATUS</th>

									<th>REMARKS</th>

									

                                </tr>

                                </thead>





                                <tbody>

								

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

        

        

        

        <!-- Modal -->

<div id="myModal" class="modal fade" role="dialog">

  <div class="modal-dialog">



    <!-- Modal content-->

    <form action="<?php echo page_url;?>Reporting/addporemarks" method="post">

        <input type="hidden" name="pono" id="pono" value="">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal">&times;</button>

        <h4 class="modal-title">Adding Remarks for PO- <span id="ponum"></span></h4>

      </div>

      <div class="modal-body">

        <div class="row">

        <textarea class="form-control" name="remarks" id="remarks" placeholder="Remarks"></textarea>

        </div>

      </div>

      <div class="modal-footer">

        <input type="submit" class="btn btn-sm btn-success" value="Update">

    </div>

    </form>



  </div>

</div>

          <!-- Footer -->

               <?php $this->load->view('common/footer');?>

                <!-- End Footer -->



            </div> <!-- end container -->

        </div>
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

 

$('#example123445').dataTable({"bSort" : false,"iDisplayLength": 100});
$('#example1234455').dataTable({"bSort" : false,"iDisplayLength": 100});
$('#example1234456').dataTable({"bSort" : false,"iDisplayLength": 100});

        

$('#example1').dataTable({

 "bProcessing": false,

 "pagination":true,

fixedHeader: true,

   scrollCollapse: true,

   fixedColumns:   {

            leftColumns: 3

        },

	
	"sAjaxSource": "<?php echo page_url;?>Reporting/polisthistory",

	"aoColumns": [

					{ mData: 'sr_no' },

					{mData:'prnumber'},

					{ mData: 'prno'},

					{ mData: 'jobcardno'},

					{mData:'vendor'},

					{mData:'itemname'},

					{mData:'qty'},

					{mData:'price'},

					{mData:'oldprice'},

					{mData:'diff'},

					{ mData: 'createdby'},

					{ mData: 'createdon'},
					{ mData: 'approvredon'},

					{ mData: 'status'},

					{ mData: 'remarks'}

						

						

                ]

        });     

   

        

});





</script>





<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

<script>

$(document).ready(function(){

  $("#loginForm").on("submit", function(){

    $("#pageloader").fadeIn();

  });//submit

});//document ready



function approvepo(pono)

{

		document.location="<?php echo page_url;?>Store/approvepo/"+pono;

		return true;



}



function showpopup(pono)

{

    

    $("#myModal").modal('show');

    $("#ponum").text(pono);

     $("#pono").val(pono);

    

    

}







</script>

<script>

function markasapproved(i,j,itemid){

    

    if($("#approvedrecord"+i+itemid).is(":checked"))

    {

        var a="1";

    }else

    {

        var a="0";

    }

   

	$.ajax({

	type:"post",

	url:"<?php echo page_url;?>Store/checktoapprovepo",

	data:"id="+i+"&pono="+j+"&itemid="+itemid+"&type="+a,

	success:function(data){
$("#app"+i).html('Approved');

$('#'+i).css({ 'background-color' : '#CCFDC1'});

	}

	});

}
																																																																				




function sendmail(pono)

{

    if($('#sendmails').is(":checked"))

    {

           $.ajax({																	

            type:"post",

            url:"<?php echo page_url;?>Store/sendemailtosupplier",

            data:"pono="+pono,

            success:function(data){

            //	alert(data);																																																																																																																			
            $("#mail"+pono).css('color','green');
            $("#mail"+pono).css('font-weight','bold');
            $("#mail"+pono).html('Mail Send');

            }

            });

    }

    

}



function checkalldata(id)
{
    
    if($(".appdata"+id).is(":checked"))
    {
       $('.approval'+id).prop('checked',true);
        
    }else
    {
         $('.approval'+id).prop('checked',false);
        
    }
    
}


function checkforsingleboxapproval()
{

  var checklen=$('[name="approvedrecord[]"]:checked').length;
    if(checklen>0)
    {
    return true;
    }else
    {
    alert('At least one item selection is mandatory');
    return false;  
    
    }
    
}
</script>

</body>

</html>	

