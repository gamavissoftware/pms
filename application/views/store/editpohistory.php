<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Prestogroup Units</title>

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
    <style>
table.manglesh thead th {
				background: #003366;
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

<?php foreach($pohistory as $row);?>
        <div class="wrapper">
            <div class="container-fluid">
 <div class="card-box">
    <?php echo $this->session->flashdata('message'); ?>
    <h3 style="text-align: center;">EDIT PO HISTORY</h3>

                      <div class="col-sm-12">
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                        <div class="card-box table-responsive">
                          <h5 style="text-align: center;">REJECTED PO</h5>
                            <table id="example" class="table text-center table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th style="text-align: center;">Sr No.</th>
                                    <th style="text-align: center;">VENDOR NAME</th>
                                    <th style="text-align: center;">PRICE</th>
                                    <th style="text-align: center;">DISCOUNT</th>
                                </tr>
                                </thead> 
                                 <tbody>
                                    <tr>     
                                      <td style="text-align: center;">1</td>
                                      <td style="text-align: center;"><?php echo $row->name?></td>
                                      <td style="text-align: center;"><?php echo $row->price;?></td>
                                      <td style="text-align: center;"><?php echo $row->discount;?></td>
                                    </tr>
                                  </tbody>
                            </table>
                        </div>
                      </div>
                      <div class="col-md-4"></div>
                    </div>

 <form method="post" action="<?php echo page_url;?>Reporting/update_po_history/<?php echo $this->uri->segment(3);?>" id="frm" onsubmit="return validate()">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="field-1" class="control-label">ITEM NAME</label>
                <span id="error_order_type" style="color:red;">*</span>
                <input type="text" id="item_name" name="item_name" class="form-control" value="<?php echo $row->part;?>" readonly>
                <input type="hidden" name="item_id" value="<?php echo $row->itemid;?>">
            </div>
        </div> 
        <div class="col-md-4">
            <div class="form-group">
                <label for="field-1" class="control-label">VENDOR NAME</label>
                <span id="error_datepicker1" style="color:red;"></span>
                <select class="form-control" id="vendor" name="vendor" class="vendor" onchange="getPriceDetails()" required="">
                  <option value="">---SELECT VENDOR---</option>
                  <?php 
                            $query = $this->db->select('a.id, a.name')
                                              ->from('vendors a')
                                              ->join('vendors_price b', 'b.vendorid=a.id')
                                              ->where('b.itemid', $row->itemid)
                                              ->get();
                            foreach($query->result() as $vendors) { ?>
                            <option value="<?php echo $vendors->id;?>"><?php echo $vendors->name;?></option>
                            <?php }?>
                </select>
            </div>
        </div> 
    </div>
    <!-- <div class="col-md-6"> -->
        <div class="row" id="repeat">
            <div class="col-md-2">
                <div class="form-group fieldGroup">
                    <label for="field-1" class="control-label">LISTPRICE</label>
                    <span id="error_username" style="color:red;">*</span>
                    <input type="text" id="listprice" name="listprice" class="form-control mand" autocomplete="off" onkeyup="calculateprice()">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group fieldGroup">
                    <label for="field-1" class="control-label">DISCOUNT</label>
                    <span id="error_username" style="color:red;">*</span><span id="stock0" style="color:red;"></span>
                    <input type="text" id="discount" name="discount" class="form-control mand" autocomplete="off" value="" onkeypress="return isNumberKey(event,this)" onkeyup="calculateprice();" required="">
                <span style="color:red;"></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="field-1" class="control-label">QUANTITY</label>
                    <span id="error_order_type" style="color:red;">*</span>
                    <input type="text" id="quantity" name="quantity" class="form-control mand" autocomplete="off" value="<?php echo $row->qty;?>" onkeyup="calculateprice()">
                </div>
            </div> 
            <div class="col-md-3">
                <div class="form-group fieldGroup">
                    <label for="field-1" class="control-label">TOTAL</label>
                    <input type="text" id="totalprice" name="totalprice" class="form-control mand" autocomplete="off" value="0" readonly>
                </div>
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
$(document).ready(function(){

  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready
                $(document).on('click', '.remove', function(){
                    $(this).parents(".fieldGroup").remove();
                    });
</script>

      <script>

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

function getPriceDetails() {
  var vendor = $("#vendor").val();

  if (vendor != '') {
    $.ajax({
        type:"post",
        url:"<?php echo page_url;?>Reporting/getPriceDetails",
        data:{vendor:vendor},
        success:function(data) {
        var arr = data.split('|');
        $("#listprice").val(arr[0]);
        $("#discount").val(arr[1]);

        calculatetotalprice(arr[0],arr[1]);

        }
    });
  } else {
    $("#listprice").val('');
    $("#discount").val('');
  }
}


function calculatetotalprice(listprice,discount)
{
  var disval=parseFloat(discount)/100;

  var listdis=parseFloat(listprice)-parseFloat(disval);

  $("#totalprice").val(listdis);

}
 function calculateprice() {
  var qty = $("#quantity").val();
  var listprice = $("#listprice").val();
  var discount = $("#discount").val();
  var total = '';

  if(parseFloat(qty) != '' || parseFloat(listprice) != '' || parseFloat(discount) != '') {
  if(parseFloat(qty) > 0 && parseFloat(listprice) > 0) {
    if((parseFloat(discount) == '') ||  (parseFloat(discount) == 0)) {
      //$("#itemprice").val(listprice);
      var total = parseFloat(listprice) * parseFloat(qty);
      $("#totalprice").val(total);
    } else {
      var getdiscounterper1 = parseFloat(discount) / 100;
      var getdiscounterper2 = listprice * getdiscounterper1;
      var getdiscounterper3 = listprice - getdiscounterper2;
      //$("#itemprice").val(getdiscounterper3);
      var total = parseFloat(getdiscounterper3) * parseFloat(qty);
      $("#totalprice").val(total);  
    }
  
  }
} else {
  $("#discount").val(0);
  $("#totalprice").val(total);  
}

}
</script>
</body>
</html>
