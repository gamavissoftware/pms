<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Units</title>

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
    <?php echo $this->session->flashdata('message'); ?>
    <h3 style="text-align: center;">Service Item Request</h3>
 <form method="post" action="<?php echo page_url;?>Serviceissue/add_request" id="frm" onsubmit="return validate()">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="field-1" class="control-label">Service Engineer Name</label>
                <span id="error_order_type" style="color:red;">*</span>
                <select class="form-control engineer_names mand" name="engineer_name" required="">
                </select>
            </div>
        </div> 
        <div class="col-md-4">
            <div class="form-group">
                <label for="field-1" class="control-label">Customer Name</label>
                <span id="error_datepicker1" style="color:red;"></span>
                <input type="text" id="customer_name" name="customer_name" class="form-control">
            </div>
        </div> 
        
        
         <div class="col-md-3">
            <div class="form-group">
                <label for="field-1" class="control-label">Customer Address</label>
                <span id="error_datepicker1" style="color:red;"></span>
                <textarea id="customer_add" name="customer_add" class="form-control" required></textarea>
            </div>
        </div> 
        
        
        <div class="col-md-2">
            <div class="form-group">
                <label for="field-1" class="control-label">IO Number</label>
                <span id="error_datepicker1" style="color:red;"></span>
                <input type="text" id="io_no" name="io_no" class="form-control">
            </div>
        </div> 
    </div>
    <!-- <div class="col-md-6"> -->
        <div class="row" id="repeat">
         <div class="col-md-2">
                <div class="form-group">
                    <label for="field-1" class="control-label">Item Type</label>
                    <span id="error_order_type" style="color:red;">*</span>
                    <select class="form-control mand" name="type[]" id="type0" required="">
                    <option value="">Select Type</option>
                    <option value="1">Store</option>
                    <option value="2">Master</option>
                    
                    </select>
                </div>
            </div> 
            <div class="col-md-3">
                <div class="form-group">
                    <label for="field-1" class="control-label">Item Name</label>
                    <span id="error_order_type" style="color:red;">*</span>
                    <select class="form-control item_names mand" name="item_name[]" id="item_name0" onchange="getUnit(0);" required="">
                    </select>
                </div>
            </div> 
            <div class="col-md-2">
                <div class="form-group fieldGroup">
                    <label for="field-1" class="control-label">Quantity</label>
                    <span id="error_username" style="color:red;">*</span><span id="stock0" style="color:red;"></span>
                    <input type="hidden" id="hidden_stock0" name="hidden_stock[]">
                    <input type="number" id="quantity0" name="quantity[]" class="form-control mand" autocomplete="off" value="" onkeypress="return isNumberKey(event,this)" onblur="checkStock(0)" required="">
                <span style="color:red;"></span>
                </div>
            </div>
            <div class="col-md-1">
                <div class="form-group fieldGroup">
                    <label for="field-1" class="control-label">Unit</label>
                    <span id="error_username" style="color:red;">*</span>
                    <input type="text" id="unit0" name="unit[]" class="form-control mand" autocomplete="off" value="" readonly required="">
                    <input type="hidden" id="hidden_unit_id0" name="hidden_unit_id[]">
                   <span style="color:red;"></span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group fieldGroup">
                    <label for="field-1" class="control-label">Remarks</label>
                    <textarea class="form-control" id="remarks0" name="remarks[]"></textarea>
                   <span style="color:red;"></span>
                </div>
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn btn-warning addMore"><span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span></a>
            </div>
        </div>
    <!-- </div> -->
    <div class="row">
       <div class="col-md-4"> 
            
            <input type="submit" value="Submit" id="sub" class="btn btn-success">
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
  $(".mand").attr('required', false);

    var name_url = "<?php echo page_url;?>Serviceissue/getEngineerNames";

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
	

    var item_url = "<?php echo page_url;?>Serviceissue/getItemNames";

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
                $('#repeat').append('<div style="clear:both;height:5px"></div><div class="row fieldGroup" id="row'+i+'"><div class="col-md-2"><div class="form-group"> <label for="field-1" class="control-label">Item Type</label> <span id="error_order_type" style="color:red;">*</span> <select class="form-control mand" name="type[]" id="type'+i+'" required=""><option value="">Select Type</option><option value="1">Store</option><option value="2">Master</option> </select></div></div><div class="col-md-3"><div class="form-group"><label for="field-1" class="control-label">Item Name</label><span id="error_order_type" style="color:red;">*</span><select class="form-control mand select3'+i+'" name="item_name[]" id="item_name'+i+'" onchange="getUnit('+i+');"></select></div></div><div class="col-md-2"><div class="form-group fieldGroup"><label for="field-1" class="control-label">Quantity</label><span id="error_username" style="color:red;">*</span><span id="stock'+i+'" style="color:red;"></span><input type="hidden" id="hidden_stock'+i+'" name="hidden_stock[]"><input type="number" id="quantity'+i+'" name="quantity[]" class="form-control mand" autocomplete="off" onkeypress="return isNumberKey(event,this)" onblur="checkStock('+i+')"><span style="color:red;"></span></div></div><div class="col-md-1"><div class="form-group fieldGroup"><label for="field-1" class="control-label">Unit</label><span id="error_username" style="color:red;">*</span><input type="text" id="unit'+i+'" name="unit[]" class="form-control mand" autocomplete="off" readonly><input type="hidden" id="hidden_unit_id'+i+'" name="hidden_unit_id[]"><span style="color:red;"></span></div></div><div class="col-md-2"><div class="form-group fieldGroup"><label for="field-1" class="control-label">Remarks</label><textarea class="form-control" name="remarks[]" id="remarks'+i+'"></textarea><span style="color:red;"></span></div></div><div class="col-md-2"> <a href="javascript:void(0)" class="btn btn-danger remove"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span></a> </div></div>');
                initializeSelect2('select3'+i);
                });

                $(document).on('click', '.remove', function(){
                    $(this).parents(".fieldGroup").remove();
                    });
</script>

      <script>
      function initializeSelect2(selectElementObj) {
          var purl="<?php echo page_url;?>Serviceissue/getItemNames";
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
    url:"<?php echo page_url;?>Serviceissue/getItemUnit",
    data:{item:item},
    success:function(data){
    var arr = data.split('|');
    $("#hidden_unit_id"+i).val(arr[0]);
    $("#unit"+i).val(arr[1]);
    $("#stock"+i).text('Current Stock ='+arr[2]);
    $("#hidden_stock"+i).val(arr[2]);
    }
    });
}

    function isNumberKey(evt, element) {
  var charCode = (evt.which) ? evt.which : event.keyCode
  if (charCode > 31 && (charCode < 48 || charCode > 57) && !(charCode == 46 || charCode == 8))
    return false;
  else {
    var len = $(element).val().length;
    var index = $(element).val().indexOf('.');
    if (index > 0 && charCode == 46) {
      return false;
    }
    if (index > 0) {
      var CharAfterdot = (len + 1) - index;
      if (CharAfterdot > 3) {
        return false;
      }
    }

  }
  return true;
}

function checkStock(i) {
    var availableStock = $("#hidden_stock"+i).val();
    var inputQty = $("#quantity"+i).val();

    if(parseFloat(inputQty) > parseFloat(availableStock)) {
         alert('Input Quantity Exceeds the Available Quantity in the stock!');
        $("#quantity"+i).val('');  
    }
}

function validate()
{
  $("#sub").attr('disabled',true);
  $("#sub").val('Please Wait..');
 var isValid=0;
 $(".mand").each(function() {
   var element = $(this).val();
  if (element=="") {
     
    isValid=1;
   }
});
 
 
 if(isValid==0)
 {
  $("#sub").attr('disabled',true);
  $("#sub").val('Please Wait..');
     return true;
 }else
 {
  $("#sub").attr('disabled',false);
  $("#sub").val('Submit');
     alert('All Fields marked with * are mandatory');
     return false;
 }
    
}
</script>
</body>
</html>
