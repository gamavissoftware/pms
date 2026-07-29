<?php
$itemID = $this->uri->segment(3);

	$rest=$this->db->select('d.conversion_unit,d.conversion_weight,a.id,a.prno,a.masterid,a.prraisereason,a.type,a.sourceid,a.source,a.jobcardid,d.part as machine_part,d.fincode,d.size_in_mm,d.specification,d.current_stock,e.first_name,e.last_name,a.itemid,a.unit,a.qty,e.department_id,d.conversion_unit,d.conversion_weight')->from('purchase_request a')->join('machine_parts_with_picture d','d.id=a.masterid')->join('system_users e','e.user_id=a.addedBy')->where('a.approvalstatus','0')->where('a.closed','0');

	if ($itemID <> '') {
		$this->db->where('a.itemid', $itemID);
	}
	$rest = $this->db->order_by('a.source','ASC')->order_by('a.id','DESC')->limit(50)->get();
	if($rest->num_rows()==0)

	{

// 		echo "NO PENDING PR ITEM AVAILABLE"'.<a href='".page_url."'Store/createbulkpo'>GO BACK</a>.'";exit;
       echo "NO PENDING PR ITEM AVAILABLE". "<a href='".page_url."Store/createbulkpo'>GO BACK</a>";exit;

	}

	



$CI =& get_instance();

$CI->load->model('Store_model');


$itemcount=$CI->Store_model->getpendpocount();


?>

<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="NJ Media">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title>CREATE PO</title>



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

.defaultnone

{

	display:none;

}

.select2-container {
	width: 100% !important;
}


.loader {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: url('<?php echo assets_url;?>images/loadingpo.gif') 50% 50% no-repeat rgb(249,249,249);

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
                    	<?php foreach($rest->result() as $row);?>
                        <div class="page-title-box col-sm-10">
                        	<?php if($itemID == '') {?>
                            <h4>CREATE PO DASHBOARD - <span style="color:red;font-weight:bold;"><?php echo $itemcount;?></span></h4>
                        <?php } else { ?>
                        	<h4>CREATE PO DASHBOARD - <span style="color:red;font-weight:bold;"><?php echo $rest->num_rows;?></span> FILTER PO FOR <?php echo $row->machine_part; ?></h4>
                        	<?php } ?>
                        </div>
                        <div class="col-sm-2">

                           <a href="javascript:;" class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal">SEARCH ITEM</a>

                        </div>

                    </div>

                </div>

                <!-- end page title end breadcrumb -->

	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

		<div class="row">

	



   

                        <div class="card-box table-responsive">

<div class="loader"></div>
                           <div class="col-sm-12">

						   <div class="waitmsg" style="display:none">Please Wait...</div>

						   <form onsubmit="return validate();" action="<?php echo page_url;?>Store/bulkgeneratepo" method="post" id="pocreateform">

						 

  

				 <table class="table table-bordered potable">

  <thead>

    <tr>

      <th scope="col">#</th>

       <th scope="col">Sno.</th>

      <th scope="col">PR No.</th>

      <th scope="col">Source</th>

      <th scope="col">Indenter Ref<br>Remarks</th>

      

      <th scope="col">Item<br>Product Code<br>Specification</th>

    
      <th scope="col">Quantity</th>

      <!--<th scope="col">Category</th>-->

	  <th scope="col">Supplier</th>

	    <th scope="col">List Price</th>

	  <th scope="col">Discount</th>

	  <th scope="col">Price</th>

	  <th scope="col">Total</th>

	  <th scope="col">Remarks (If Any)</th>

    </tr>

  </thead>

  <tbody>

  <?php

						$i=1;

						$cost=array();

						foreach($rest->result() as $data)

						{
							$remarks='';

							$restyu=$this->db->select('shortname')->from('units')->where('id',$data->unit)->get();

							if($restyu->num_rows()>0)

							{

								foreach($restyu->result() as $restyu1);

								$unival=$restyu1->shortname;

							}else{

							

							$unival='';							

							}

							

							$suppliers=$CI->Store_model->getallsupplier($data->itemid,$data->masterid);

							$suppliersgreen=$CI->Store_model->checkforgreensupplier($data->itemid,$data->masterid);



							$jobcard='';

							if($data->source==1)

							{
							$job=$this->db->select('job_card_no,item_id as machineid')->from('order_instruments')->where('id',$data->jobcardid)->get();

							if($job->num_rows()>0)

							{

							foreach($job->result() as $job1);
							$jobcard='Jobcard-'.$job1->job_card_no;

							$macid=$job1->machineid;

							}else{

							$jobcard='';

							$macid=0;

							}

							}else if($data->source==2)

							{

							/** Intend **/


							$jobcard='INDENT- IND'.$data->sourceid;

							$macid=0;

							/** GET REMARKS FROM INDENT **/
							$remk=$this->db->select('remarks')->from('intend_request')->where('indendno',$data->sourceid)->get();
							if($remk->num_rows()>0)
							{
								foreach($remk->result() as $remk1);

								$remarks=$remk1->remarks;

							}
							/** END **/


							}else if($data->source==3)

							{

							/** Intend **/
							$jobcard='AUTO PR';

							$macid=0;
							}


						

							?>

					<input type="hidden" name="currentstock<?php echo $i;?>" value="<?php echo $data->current_stock;?>">

		

					<input type="hidden" name="unit<?php echo $i;?>" value="<?php echo $data->unit;?>">
					<input type="hidden" name="pr<?php echo $i;?>" value="<?php echo $data->prno;?>">

					<input type="hidden" name="source<?php echo $i;?>" value="<?php echo $data->source;?>">



					<input type="hidden" name="jobcardno<?php echo $i;?>" value="<?php echo $data->jobcardid;?>">

					<input type="hidden" name="instrumentid<?php echo $i;?>" value="<?php echo $macid;?>">

					<input type="hidden" name="type<?php echo $i;?>" value="<?php echo $data->type;?>">





    <tr>

        <td scope="row"><input type="checkbox" name="checkit[]"   class="checkitemid<?php echo $i;?> checkme" id="checkit<?php echo $i;?>" value="<?php echo $i;?>" onchange="makepo(<?php echo $i;?>);"><br/><a href="javascript:;" onclick="showRemarksModal(<?php echo $data->id;?>)"><i class="fa fa-close"></i></a>
		<input type="hidden" name="hiddenitem<?php echo $i;?>" id="hiddenitem<?php echo $i;?>" value="<?php echo $data->masterid;?>">

        </td>

      <td scope="row" style="width:3%"><?php echo $i;?></td>

      <td style="width:3%"><?php echo $data->prno;?></td>

       <td style="width:4%"><?php echo $jobcard;?></td>
        <td style="width:5%"><?php echo ucwords($data->first_name);?> <?php echo ucwords($data->last_name);?><br><br><?php echo $remarks;?></td>
         

      <td style="width:10%"><?php echo strtoupper($data->machine_part);?><br><?php echo $data->fincode;?><br><?php echo $data->specification;?><br/><?php echo $data->size_in_mm;?></td>

     
      <td style="width:15%"><?php echo floatval($data->qty);?><?php echo $unival;?>

<div class="col-md-8"><input type="text" name="qty<?php echo $i;?>" readonly id="qty<?php echo $i;?>" style="width:70%" class="form-control qtyval<?php echo $i;?> allow_numeric" value="<?php echo floatval($data->qty);?>" onkeyup="calculateprice(<?php echo $data->masterid;?>,<?php echo $i;?>)";></div><div class="col-md-4"><?php //echo $unival;?></div><br/>
	  <!--<input type="hidden" class="qtyval<?php echo $i;?>" name="qty<?php echo $data->masterid;?>" id="qty<?php echo $data->masterid;?>" value="<?php echo floatval($data->qty);?>">-->
			<div>
			<?php if($data->conversion_unit>0)
			{ 

				$restyuss=$this->db->select('shortname')->from('units')->where('id',$data->conversion_unit)->get();

				if($restyuss->num_rows()>0)
				{
				foreach($restyuss->result() as $restyuss1);
				$unival12=$restyuss1->shortname;
				}else{
				$unival12='';							
				}
					
				?>

				Conversion Weight- <?php echo $data->conversion_weight;?> <?php echo $unival12;?> per <?php echo $unival;?><br/>Total - <?php echo $data->conversion_weight*floatval($data->qty);?> <?php echo $unival12;?>

			<?php
			}
			?>
			</div>

	  </td>

	  <td style="width:13%">

	   <?php

	  if(count($suppliers)>0)

	  {
 
		  $col="12";

	  }else{

		  $col="12";

	  }

	  ?>

	  <div class="col-md-<?php echo $col;?>">

	  <select name="supplier[]" id="supplier<?php echo $i;?>" class="form-control sup" style="width:100%;display:none;" onchange="getsupplierprice('<?php echo $data->itemid;?>','<?php echo $data->masterid;?>','<?php echo $i;?>')">

	      <option value="">Select Supplier</option>

	

	 <?php

	 $checksupplll[]=0;

	 if(count($suppliers)>0)

	 {

		$a='';

		

		 foreach($suppliers as $suppliersdata)

		 {

		    

			if(count($suppliers)==1)

			{

			$a="Selected";

			//$checksupplll[]=1;

			}else{

				$a='';

				/**if($suppliersgreen==$suppliersdata->id)

				{

					$a="Selected";

					$checksupplll[]=1;

				} **/

			}

			

			

	 ?>

	  

	  <option  value="<?php echo $suppliersdata->id;?>" <?php echo $a;?>><?php echo $suppliersdata->name;?></option>

	  <?php

		 }

	 }

	 ?>

	  </select>

	 

	  

	 

	  <?php

	  if(count($suppliers)==0)

	  {

	  ?>

	  <a href="<?php echo page_url;?>Vendor/vendorform/<?php echo $data->prno;?>/<?php echo $data->masterid;?>" style="display:none;" id="vendnoprice<?php echo $i;?>" target="_blank"><span class="btn btn-warning btn-xs">Add Vendors Quotes</span></a>

	  <?php

	  }

	  ?>

	   </div>

	  </td>

	  <td style="width:10%"><input type="text" class="form-control" readonly name="listprice" value="" id="listper<?php echo $i;?>" style="display:none;"> <span style="color:red" id="listper"></span></td>
	 <td style="width:10%"><input type="text" name="discountper<?php echo $i;?>"  id="discountper<?php echo $i;?>" style="width:70%" class="form-control allow_numeric" value="" onkeyup="calculateprice(<?php echo $data->masterid;?>,<?php echo $i;?>);" style="display:none"/></td>

	 <td style="width:7%"><input type="text" name="itemprice<?php echo $i;?>" id="itemprice<?php echo $i;?>" class="form-control itempaisa<?php echo $i;?> priceforpo" style="display:none" onkeyup="gettotalvalue('<?php echo $data->masterid;?>','<?php echo floatval($data->qty);?>');"><input type="hidden" id="originalprice<?php echo $i;?>" style="display:none" name="originalprice<?php echo $i;?>"></td>

	  <td style="width:10%"><input type="text" name="totalitemprice<?php echo $i;?>" id="totalitemprice<?php echo $i;?>" class="form-control" readonly style="display:none"></td>

	    <td style="width:10%"><textarea name="itemremarks<?php echo $i;?>" id="itemremarks<?php echo $i;?>" class="form-control" rows="4" cols="50" style="display:none" ></textarea></td>

    </tr>

	

<?php

$i++;

}

?>						

	<tr>

	

      <th colspan="15"><!--<span class="pull-right" style="font-size:18px">If all data is filled PO will be created automatically.</span>--></th>

	  <td><input type="submit" name="save" id="gpo" value="Generate PO" class="btn btn-success pull-right btn-sm"></td>

    </tr>

   

  </tbody>

</table>

		</form>		

		  <!-- Modal -->
		  <div class="modal fade" id="myModal" role="dialog">
		    <div class="modal-dialog">
		    
		      <!-- Modal content-->
		      <div class="modal-content">
		        <div class="modal-header">
		          <button type="button" class="close" data-dismiss="modal">&times;</button>
		          <h4 class="modal-title">SEARCH ITEM</h4>
		        </div>
		        <div class="modal-body">
		          <form method="post" id="search_form">
		          	<div class="row">
		          		<div class="col-md-12">
		          			<div class="form-group">
		          				<label>ITEMS</label>
		          				<select class="form-control" id="searchitem" required></select>
		          			</div>
		          		</div>
		          	</div>
		          </form>
		        	<input type="submit" class="btn btn-success pull-right" id="search" value="SEARCH">
		        </div>
		      </div>
		      
		    </div>
		  </div> 

<script>



function gettotalvalue(masterid,qty)

{

    

  var itemprice= $("#itemprice"+masterid).val();

  if(itemprice!='' || itemprice!=0)

  {

  var total=parseFloat(qty)*parseFloat(itemprice);

  $("#totalitemprice"+masterid).val(total.toFixed(2));

  }else

  {

      $("#totalitemprice"+masterid).val('');

  }

  

}







$(".priceforpo").on("input", function(evt) {

   var self = $(this);

   self.val(self.val().replace(/[^0-9\.]/g, ''));

   if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 

   {

     evt.preventDefault();

   }

 });

 

 

function getsupplierprice(partid,masterid,rowid)

{

	var suppid=$("#supplier"+rowid).val();

		

	$.ajax({

	type:"post",

	url:"<?php echo page_url;?>Store/getitemprice",

	data:"suppid="+suppid+"&partid="+partid+"&masterid="+masterid,

	success:function(data){

		if(data=="NA")

		{

			//alert('Price Not Set');

			$("#totalitemprice"+rowid).val('');

			$("#itemprice"+rowid).val('');

			$("#discountper"+rowid).html('');

			$("#originalprice"+rowid).val('');

		   

		}else{

			

		

			var qtyu=$("#qty"+rowid).val();

			var total=parseFloat(qtyu)*parseFloat(data);

			$("#totalitemprice"+rowid).val(total);

			$("#itemprice"+rowid).val(data);

			$("#originalprice"+rowid).val(data);

			

		

			getdiscountpercent(partid,masterid,rowid,suppid);

			getdiscountlistprice(partid,masterid,rowid,suppid);



			

		}

	

		

		getuniquesupp(suppid,rowid);

	}

	});

		

	

	

	

}





function getuniquesupp(suppid,rowid)

{

	$("#gpo").attr('disabled',true);

	

	/** GET ALL ROW INPUT AND CHANGE THERE NAME **/

	var itemname="item"+suppid+"[]";

	$(".itemid"+rowid).attr('name',itemname);

	var itemname112="checkit"+suppid+"[]";

	$(".checkitemid"+rowid).attr('name',itemname112);

	/**var qtname="qty"+suppid+"[]";

	$(".qtyval"+rowid).attr('name',qtname);

	var pri="itemprice"+suppid+"[]";

	$(".itempaisa"+rowid).attr('name',pri); **/

	$("#gpo").attr('disabled',false);

	/** END **/

	

	

	

	

	

}



function validate()

{

	

	var isValid;

$(".potable :input.mand").each(function() {

   var element = $(this);

   if (element.val() == "") {

     

	  isValid=1;

   }

});



if(isValid==1)

{

	$("#gpo").attr('disabled',false);

	 alert('All Fields are mandatory');

	return false;

}else{

	

	$("#gpo").attr('disabled',true);

	return true;

	

}

	

}



</script>						   

						   

						   

						   

						   

						   

						   

                        </div>

                    </div>

                </div>
                
                
                
                 <!-- Modal -->
  <div class="modal fade" id="showRemarksModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Closed Remarks</h4>
        </div>
        <form method="post" action="" id="closedForm">
        <div class="modal-body">
	        <div class="row">
	            <div class="col-md-12">
					<div class="form-group">
						<label for="field-1" class="control-label">Remarks</label>
						<span id="error_order_type" style="color:red;">*</span>	
						<textarea class="form-control" name="closedRemarks" cols="20" rows="3"></textarea>
					</div>
				</div>
			</div>
					<input type="submit" class="btn btn-primary" value="Submit">

	     </div>
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

		



	 





<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>

<script>

$(document).ready(function(){

  $("#loginForm").on("submit", function(){

    $("#pageloader").fadeIn();

  });//submit


	var url="<?php echo page_url;?>Store/getitems";

	$('#searchitem').select2({ 

			    placeholder: 'TYPE TO SELECT',
				minmumInputLength:4,
				allowClear: true,

			        ajax: {

			          url: url,

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

$("#search").on("click", function(e) {
	var itemid = $('#searchitem').val();
	var action = "<?php echo page_url;?>Store/createbulkpo/"+itemid;
	//alert(action);

	if(itemid != '') {
	    e.preventDefault();
	    $('#search_form').attr('action', action).submit();
	}
	   
});



});//document ready

</script>

<!--<script>

$(document).ready(function(){

	$(".potable").css('display','');

	$(".waitmsg").css('display','none');

var values = [];

var i=1;

$("input[name='item[]']").each(function() {

 var itemid=$(this).val();

 if(itemid!='')

 {

	getsupplierprice(itemid,itemid,i);

	

 }else{

	 alert('Item Not Found');

 }

i++;

});







	 setTimeout(function() {

	//$("#pocreateform").submit();

   

  }, 3000);



});

</script>-->

<script>

    

    function getdiscountpercent(partid,masterid,rowid,suppid)

    {

    

   

        $.ajax({

        type:"post",

        url:"<?php echo page_url;?>Store/getitemdiscountforajax",

        data:"suppid="+suppid+"&partid="+partid+"&masterid="+masterid+"&flag=0",

        success:function(data){

        if(data!="NA")

        {

         $("#discountper"+rowid).val(data);

        }else

        {

        $("#discountper"+rowid).val('');

        }

         

        }

        });

   

    

    

    }

    

    

    function  getdiscountlistprice(partid,masterid,rowid,suppid)

 {

		$.ajax({

		type:"post",

		url:"<?php echo page_url;?>Store/getitemlistpriceforajax",

		data:"suppid="+suppid+"&partid="+partid+"&masterid="+masterid+"&flag=0",

		success:function(data){
		if(data!="NA")

		{
			

		$("#listper"+rowid).val(data);

		}else

		{

		$("#listper"+rowid).val('');

		}



		}

		});

   

	 

 }

 

    

    function makepo(masterid)

{

	if($("#checkit"+masterid).is(":checked"))

	{


	var i=$("#checkit"+masterid).val();

	//makepo(i);

	var su=$("#supplier"+i).val();
	//alert(su);
	if(su!='')
	{
	var itemid=$("#hiddenitem"+i).val();

	getsupplierprice(itemid,itemid,i);

	calculateprice(itemid,i);

	}

		$("#qty"+masterid).attr('readonly',false);
		$("#supplier"+masterid).css('display','');

		$("#supplier"+masterid).addClass('mand');

		

		$("#itemprice"+masterid).css('display','');

		$("#itemprice"+masterid).addClass('mand');

		

		$("#totalitemprice"+masterid).css('display','');

		$("#totalitemprice"+masterid).addClass('mand');

		$("#itemremarks"+masterid).css('display','');

		$("#originalprice"+masterid).css('display','');

		$("#originalprice"+masterid).addClass('mand');

		$("#vendnoprice"+masterid).css('display','');

		$("#listper"+masterid).addClass('mand');

		$("#listper"+masterid).css('display','');

		$("#discountper"+masterid).addClass('mand');

		$("#discountper"+masterid).css('display','');

		

		//$(".defaultnone").css('display','');

		

	}else{

		

		$("#qty"+masterid).attr('readonly',true);
		$("#supplier"+masterid).css('display','none');

		$("#supplier"+masterid).removeClass('mand');

		

		$("#itemprice"+masterid).css('display','none');

		$("#itemprice"+masterid).removeClass('mand');

		

		$("#totalitemprice"+masterid).css('display','none');

		$("#totalitemprice"+masterid).removeClass('mand');

		$("#itemremarks"+masterid).css('display','none');

		$("#originalprice"+masterid).css('display','none');

		$("#originalprice"+masterid).removeClass('mand');

		$("#vendnoprice"+masterid).css('display','none');

		$("#listper"+masterid).removeClass('mand');

		$("#listper"+masterid).css('display','none');

		$("#discountper"+masterid).removeClass('mand');

		$("#discountper"+masterid).css('display','');
		
		$("#discountper"+masterid).val('');

		//$(".defaultnone").css('display','none');

		

	}

}


$(".allow_numeric").on("input", function(evt) {
	var self = $(this);
	self.val(self.val().replace(/[^0-9\.]/g, ''));
	if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
	{
	evt.preventDefault();
	}
 });
 
 
 function calculateprice(masterid,rowid)
{

	
	var qty=$("#qty"+rowid).val();
	var listprice=$("#listper"+rowid).val();
	var discountper=$("#discountper"+rowid).val();
	


	if(parseFloat(qty)>0 && parseFloat(listprice)>0)
	{
		if((parseFloat(discountper)=='') ||  (parseFloat(discountper)==0))
		{
		

			$("#itemprice"+rowid).val(listprice);
			var total=parseFloat(listprice)*parseFloat(qty);
			$("#totalitemprice"+rowid).val(total);
			
			
		}else{

			var getdiscounterper1=parseFloat(discountper)/100;
			var getdiscounterper2=listprice*getdiscounterper1;
			var getdiscounterper3=listprice-getdiscounterper2;
			$("#itemprice"+rowid).val(getdiscounterper3);
			var total=parseFloat(getdiscounterper3)*parseFloat(qty);
			$("#totalitemprice"+rowid).val(total);
			
			
		}
		
	
	
	}

	
}


$(window).load(function() {
        $(".loader").fadeOut("slow");

checkalldataandgetit();




});


function checkalldataandgetit()
{
	$.each($("input[name='checkit[]']:checked"), function(){
	var i=$(this).val();

	makepo(i);

	var su=$("#supplier"+i).val();
	//alert(su);
	if(su!='')
	{
		var itemid=$("#hiddenitem"+i).val();

		getsupplierprice(itemid,itemid,i);

		calculateprice(itemid,i);

	}
	});


}

function showRemarksModal(id) {
	var pgurl = "<?php echo page_url;?>";
	$("#showRemarksModal").modal('show');
	$("#hidden_id").val(id);
	$('#closedForm').attr('action', pgurl+"Store/closepritem/"+id );
}
</script>

</body>

</html>
