<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?></title>

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
        <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
		<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
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
				font-weight:bold;
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

.addMore {
margin-top: 22px;
}

.remove {
margin-top: 22px;
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
            <div class="container-fluid">

 <div class="card-box">
    <h3 style="text-align: center;">Service Item Request</h3>
    <?php foreach($getMaterialRequest as $items);?>
 <form method="post" action="<?php echo page_url;?>Serviceissue/update_stock" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="field-1" class="control-label">Service Engineer Name</label>
                <span id="error_order_type" style="color:red;">*</span>
                <select class="form-control" name="engineer_name">	
                    <option value="<?php echo $items->service_engineer_name;?>"><?php echo $items->first_name." ".$items->last_name;?></option>
                </select>
            </div>
        </div> 
        <div class="col-md-3">
            <div class="form-group">
                <label for="field-1" class="control-label">Customer Name</label>
                <span id="error_datepicker1" style="color:red;"></span>
                <input type="text" id="customer_name" name="customer_name" class="form-control" value="<?php echo $items->customer_name;?>" required readonly>
            </div>
        </div> 
        <div class="col-md-3">
            <div class="form-group">
                <label for="field-1" class="control-label">IO Number</label>
                <span id="error_datepicker1" style="color:red;"></span>
                <input type="text" id="io_no" name="io_no" class="form-control" value="<?php echo $items->io_no;?>" required readonly>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="field-1" class="control-label">Challan Number</label>
                <span id="error_datepicker1" style="color:red;"></span>
                <input type="text" id="challan_no" name="challan_no" value="<?php echo $challanNo;?>" class="form-control" required readonly>
            </div>
        </div> 
    </div>
    <!-- <div class="col-md-6"> -->
        <?php 
        $i = 1;
        foreach($getRequestItems as $row) {
            $i++;?>
        <div class="row" id="repeat">
            <!-- <div class="col-md-1">
                <input type="checkbox" class="checkQty<?php echo $i;?>" name="checkQty" onchange="showInQty(<?php echo $i;?>)">
            </div> -->
            
              <div class="col-md-2">
                 <div class="form-group">
                    <label for="field-1" class="control-label">Item Type</label>
                    <span id="error_order_type" style="color:red;">*</span>
                    <select class="form-control mand" name="type[]" id="type0" required="">
			<?php
			if($row->type=='1')
			{
			?>
			<option value="1">Store</option>
			<?php
			}else
			{
			?>
			<option value="2">Master</option>
			<?php
			}
			?>
                    
                    </select>
                </div>
            </div> 
            
            <div class="col-md-2">
                <div class="form-group">
                    <label for="field-1" class="control-label">Item Name</label>
                    <span id="error_order_type" style="color:red;">*</span>
                    <select class="form-control" name="item_name[]" id="item_name0" onchange="getUnit(0);">
                        <option value="<?php echo $row->itemid;?>"><?php echo $row->part;?></option>
                    </select>
                </div>
            </div> 
            <div class="col-md-1">
                <div class="form-group fieldGroup">
                    <label for="field-1" class="control-label">Out Qty</label>
                    <span id="error_username" style="color:red;">*</span>
                    <input type="text" id="quantity<?php echo $i;?>" name="quantity[]" class="form-control" autocomplete="off" value="<?php echo $row->quantity;?>" readonly>
                <span style="color:red;"></span>
                </div>
            </div>
            <div class="col-md-1">
                <div class="form-group fieldGroup">
                    <label for="field-1" class="control-label">Unit</label>
                    <span id="error_username" style="color:red;">*</span>
                    <input type="text" id="unit0" name="unit[]" class="form-control" autocomplete="off" value="<?php echo $row->shortname;?>" readonly>
                    <input type="hidden" id="hidden_unit_id0" name="hidden_unit_id[]">
                   <span style="color:red;"></span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group fieldGroup">
                    <label for="field-1" class="control-label">Remarks</label>
                    <span id="error_username" style="color:red;">*</span>
                    <textarea class="form-control" id="remarks0" name="remarks[]" readonly><?php echo $row->remarks;?></textarea>
                   <span style="color:red;"></span>
                </div>
            </div>
            <div class="showInQty<?php echo $i;?>">
            <div class="col-md-1">
                <div class="form-group fieldGroup">
                    <label for="field-1" class="control-label">In Quantity</label>
                    <span id="error_username" style="color:red;">*</span>
                    <input type="number" id="in_qty<?php echo $i;?>" name="in_qty[]" class="form-control" autocomplete="off" onblur="checkQuantity(<?php echo $i;?>)">
                <span style="color:red;"></span>
                </div>
            </div>
            </div>
            <div class="showWarranty<?php echo $i;?>" style="display: none;">
            <div class="col-md-2">
                <div class="form-group fieldGroup">
                    <label for="field-1" class="control-label">Select Reason</label>
                    <span id="error_username" style="color:red;">*</span>
                    <select class="form-control" id="reason<?php echo $i;?>" name="reason[]" onchange="showBillNo(<?php echo $i;?>)">
                        <option value="">Select</option>
                        <option value="1">Under Warranty</option>
                        <option value="2">Chargeable</option>
                    </select>
                <span style="color:red;"></span>
                </div>
            </div>
            </div>
            <div class="showBillNo<?php echo $i;?>" style="display: none;">
            <div class="col-md-2">
                <div class="form-group fieldGroup">
                    <label for="field-1" class="control-label">Bill No</label>
                    <span id="error_username" style="color:red;">*</span>
                    <input type="text" name="bill_no[]" class="form-control" id="bill_no<?php echo $i;?>">
                <span style="color:red;"></span>
                </div>
            </div>
            </div>
            <input type="hidden" name="update_id" value="<?php echo $this->uri->segment(3);?>">
            <input type="hidden" name="row_id[]" value="<?php echo $row->id;?>">
            <!-- <div class="col-md-2">
                <a href="javascript:void(0)" class="btn btn-warning addMore"><span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span></a>
            </div> -->
        </div>
    <?php } ?>
    <!-- </div> -->
    <div class="row">
       <div class="col-md-4"> 
            
            <input type="submit" value="Submit" class="btn btn-success">
        </div>
    </div>
    </form>
</div>
</div>
                            <!-- /.modal -->


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
        <script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
        <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
		
 <script>
$( document ).ready(function() {

    var name_url = "<?php echo page_url;?>Master/Units/getEngineerNames";

    $('.engineer_names').select2({ 
        placeholder: 'TYPE TO SELECT',
        minmumInputLength:4,
        allowClear: true,

        ajax: {
          url: name_url,
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
	

    var item_url = "<?php echo page_url;?>Master/Units/getItemNames";

    $('.item_names').select2({ 
        placeholder: 'TYPE TO SELECT',
        minmumInputLength:4,
        allowClear: true,

        ajax: {
          url: item_url,
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
// $('#example').dataTable({
//  "bProcessing": false,
//  "pagination":true,
//  "sAjaxSource": "<?php echo page_url;?>Master/Units/unit_list/",
//  "aoColumns": [
// 						{ mData: 'sr_no' } ,
//                         { mData: 'unit' },
// 						{ mData: 'short_name' },
//                         { mData: 'edit' }
						
//                 ]
//         });   
});

</script>

<script language="javascript" type="text/javascript">   
function validateme()
{

var shortname1 = $("#shortnames").val();
if(shortname1=='')
{
	$("#error_datepicker1").html('Required!');
	
}

var unitname = $("#unitname").val();
if(unitname=='')
{
	$("#error_holidayname").html('Required!');

}


if(unitname=='' || shortname1=='')
{
	
	return false;
}

}
</script>
<script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready

var i=1;
$('.addMore').click(function(){
    i++;
                $('#repeat').append('<div class="row fieldGroup" id="row'+i+'"><div class="col-md-3"><div class="form-group"><label for="field-1" class="control-label">Item Name</label><span id="error_order_type" style="color:red;">*</span><select class="form-control select3'+i+'" name="item_name[]" id="item_name'+i+'" onchange="getUnit('+i+');"></select></div></div><div class="col-md-2"><div class="form-group fieldGroup"><label for="field-1" class="control-label">Quantity</label><span id="error_username" style="color:red;">*</span><input type="text" id="quantity'+i+'" name="quantity[]" class="form-control" autocomplete="off"><span style="color:red;"></span></div></div><div class="col-md-2"><div class="form-group fieldGroup"><label for="field-1" class="control-label">Unit</label><span id="error_username" style="color:red;">*</span><input type="text" id="unit'+i+'" name="unit[]" class="form-control" autocomplete="off" readonly><input type="hidden" id="hidden_unit_id'+i+'" name="hidden_unit_id[]"><span style="color:red;"></span></div></div><div class="col-md-3"><div class="form-group fieldGroup"><label for="field-1" class="control-label">Remarks</label><span id="error_username" style="color:red;">*</span><textarea class="form-control" name="remarks[]" id="remarks'+i+'"></textarea><span style="color:red;"></span></div></div><div class="col-md-2"> <a href="javascript:void(0)" class="btn btn-danger remove"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span></a> </div></div>');
                initializeSelect2('select3'+i);
                });

                $(document).on('click', '.remove', function(){
                    $(this).parents(".fieldGroup").remove();
                    });
</script>

      <script>
      function initializeSelect2(selectElementObj) {
          var purl="<?php echo page_url;?>Master/Units/getItemNames";
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

    function getUnit(i) {
    var item = $("#item_name"+i).val();

    $.ajax({
    type:"post",
    url:"<?php echo page_url;?>Master/Units/getItemUnit",
    data:{item:item},
    success:function(data){
    var arr = data.split('|');
    $("#hidden_unit_id"+i).val(arr[0]);
    $("#unit"+i).val(arr[1]);
    }
    });
}

    // function showInQty(i) {
    //     if ($(".checkQty"+i).is(':checked')) {
    //         $(".showInQty"+i).css("display","");
    //     } else {
    //         $(".showInQty"+i).css("display","none");
    //     }
    // }

    function checkWarranty(i) {
        $(".showWarranty"+i).css("display","");
        $(".showWarranty"+i).attr("required",true);
    }

    function checkQuantity(i) {
    $(".showWarranty"+i).css("display","none");
    $(".showBillNo"+i).css("display","none");
    $( "#reason"+i ).val('');
    var oldQty = $("#quantity"+i).val();
    var newQty = $("#in_qty"+i).val();

    if(parseFloat(newQty) < parseFloat(oldQty)) {
        $(".showWarranty"+i).css("display","");
        $(".showWarranty"+i).attr("required",true);
    } else if(parseFloat(newQty) > parseFloat(oldQty)){
          alert('Input Quantity Exceeding the Quantity taken!');
          $("#in_qty"+i).val('');  
        }
    }

    function showBillNo(i) {
        var type = $( "#reason"+i ).val();
          $(".showBillNo"+i).css("display","none");  
          $("#bill_no"+i).val('');  
        //alert(type);
        if ( type == 2) {
          $(".showBillNo"+i).css("display","");  
        }
    }


      </script>
</body>
</html>
