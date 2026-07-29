<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Sales Tool</title>

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

.inp {
    border:none;
    border-bottom: 1px solid lightgrey;

    outline: none;
    width:65px
 }

[placeholder]:focus::-webkit-input-placeholder {
    transition: text-indent 0.4s 0.4s ease; 
    text-indent: -100%;
    opacity: 1;
 }

 .add-field{
   padding:15px;
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
               
                <h3 style="text-align: center;">Sales Tool Details</h3>
                    <div class="row" id="addAnotherTable">
                      <div class="col-sm-12">
                        <div class="card-box table-responsive">
                          <table id="example" class="table text-center table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th style="text-align: center; width:15%;">MACHINE NAME/USP</th>
                                        <th style="text-align: center; ">PRICE</th>
                                        <th style="text-align: center;">DISCOUNT %</th>
                                        <th style="text-align: center;">DISCOUNTED PRICE</th>
                                        <th style="text-align: center;">FREIGHT</th>
                                        <th style="text-align: center;">PACKING %</th>
                                        <th style="text-align: center;">PACKING PRICE</th>
                                        <!-- <th style="text-align: center;">Installation</th>
                                        <th style="text-align: center;">Courier</th> -->
                                        <th style="text-align: center; ">   MISCELLENEOUS</th>
                                        <th style="text-align: center;">TOTAL PRICE</th>
                                        <th style="text-align: center;">MINIMUM PRICE</th>
                                        <th style="text-align: center; ">	PROMOTIONS</th>
                                        
                                        <th style="text-align: center;">PDF/Video</th>
                                      
                                      
                                    
                                    </tr>
                                    <tr>
                                    <td ><div class="form-group" style="margin-bottom:0px;">
                                    <select class="form-control machine_name sele" name="machine_name" id="machine_id0" required="" onchange="showDetails(0); showMisc(0); showClients(0); showPromotions(0)" ></select>

                                    </div>
                                    <hr>
                                    <div style="width:100px; margin-top:30px;"><strong>USP:</strong> <span id="usp0"></span></div>
                                    <hr>
                                    <table class="table table-bordered">
                                    <thead>
                                      <tr>
                                        <th>CLIENT NAME</th>
                                        <th>CITY</th>
                                        <th>INDUSTRY</th>
                                      </tr>
                                    </thead>
                                    <tbody id="clients0">
                                   </tbody>
                                  </table>
                                    </td>
                                    <td id="machine_price0"></td>
                                    <td><select id="discount0" name="discount" style="border: none;border-bottom: 1px solid lightgray; " onchange="calculateDiscount(0);">
                                    <option value="">SELECT</option>
                                    <?php for($i = 0; $i < 21; $i++) {?>
                                        <option value="<?php echo $i;?>"><?php echo $i;?></option>
                                    <?php } ?>
                                     </select>
                                    </td>
                                    <td id="discounted_price0"></td>
                                    <td><input class="inp" placeholder="Charges" id="freight0" onkeypress="return isNumberKey(event,this)" onkeyup="calculateTotal(0);"/></td>
                                    <td><select  id="packing_discount0" name="packing_discount" style="border: none;border-bottom: 1px solid lightgray; " onchange="calculatePacking(0);">
                                    <option value="">SELECT</option>
                                             <?php for($j = 0; $j < 6; $j++) {?>
                                            <option value="<?php echo $j;?>"><?php echo $j;?></option>
                                        <?php } ?>
                                    </select></td>
                                    <td id="packing_price0"></td>
                                   
                                    <td><table class="table table-bordered">
                                    <thead>
                                      <tr>
                                        <th>NAME</th>
                                        <th>CHARGES</th>
                                      </tr>
                                    </thead>
                                    <tbody id="charges0">
                                   </tbody>
                                  </table></td>
                                    <td id="total_price0"></td>
                                    <td id="minimum_price0"></td>
                                    <td> <table class="table table-bordered">
                                    <thead>
                                      <tr>
                                        <th>PROMOTION NAME</th>
                                        <th>PROMOTION PRICE</th>
                                        <th>DESCRIPTION</th>
                                      </tr>
                                    </thead>
                                    <tbody id="promotions0">
                                     <!--  <tr>
                                      <td>
                                      <form action="">
                                  <input type="checkbox" id="" name="" value=""></td>
                                  </form>
                                        <td> Promotion1 </td>
                                        <td>50</td>
                                        <td>Promotion1 Description</td>
                                      </tr>
                                      <tr>
                                      <td>   <form action="">
                                  <input type="checkbox" id="" name="" value=""></td>
                                  </form></td>
                                        <td>Promotion2</td>
                                        <td>50</td>
                                        <td>Promotion1 Description</td>
                                      </tr>
                                      <tr>
                                      <td>   <form action="">
                                  <input type="checkbox" id="" name="" value=""></td>
                                  </form></td>
                                        <td>Promotion3</td>
                                        <td>50</td>
                                        <td>Promotion1 Description</td>
                                      </tr>
                                 -->    </tbody>
                                  </table>
                                </td>
                                   
                                    <td><a id="pdf0" target="_blank"><div >Click here</div></a>/<a id="video0" target="_blank"><div>Click here</div></a></td>
                                   
                                
                                    </tr>
                                    <tr>
                                      <td colspan="12"><span id="finalresult0" style="color: red;font-size: 15px"></span></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                      
                                    </tbody>
                                    
                                </table>
    </div>
    <span id="addMoreTable"><a href="javascript:;" class="addMoreTable  firstplus"><i class="fa fa-plus" aria-hidden="true" style="float: right;font-size: 20px; margin-right: 10px; color:orange;"></i></a></span>
    </div>
    </div>
</div>
</div>
                            <!-- /.modal -->


                <!-- Footer -->
               <?php $this->load->view('common/footer');?>
                <!-- End Footer -->

          
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

   var purl="<?php echo page_url;?>Sales_tool/getMachineName";
         $('.sele').select2({
             
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
});//document ready

var i=1;
$('.addMoreTable').click(function(){
                $('.addMoreTable').css('display', 'none');
                $('#addAnotherTable').append('<div class="add-field"><div class="row fieldGroup" id="row1"><div class="row" id="addAnotherTable"><div class="col-sm-12"><div class="card-box table-responsive"><table id="example" class="table text-center table-striped table-bordered"><thead><tr><th style="text-align: center; width:15%;">MACHINE NAME/USP</th><th style="text-align: center; ">PRICE</th><th style="text-align: center;">DISCOUNT %</th><th style="text-align: center;">DISCOUNTED PRICE</th><th style="text-align: center;">FREIGHT</th><th style="text-align: center;">PACKING %</th><th style="text-align: center;">PACKING PRICE</th><th style="text-align: center;">MISCELLENEOUS</th><th style="text-align: center;">TOTAL PRICE</th><th style="text-align: center;">MINIMUM PRICE</th><th style="text-align: center; ">  PROMOTIONS</th><th style="text-align: center;">PDF/Video</th></tr><tr><td ><div class="form-group" style="margin-bottom:0px;"> <select class="form-control machine_name select31 checkplus" name="machine_name" id="machine_id1" required="" onchange="showDetails(1); showMisc(1); showClients(1); showPromotions(1)" ></select></div><hr><div style="width:100px; margin-top:30px;"><strong>USP:</strong><span id="usp1"></span></div><hr><table class="table table-bordered"><thead><tr><th>CLIENT NAME</th><th>CITY</th><th>INDUSTRY</th></tr></thead><tbody id="clients1"></tbody></table></td><td id="machine_price1"></td><td><select id="discount1" name="discount" style="border: none;border-bottom: 1px solid lightgray; " onchange="calculateDiscount(1);"><option value="">SELECT</option><?php for($i = 0; $i < 21; $i++) {?><option value="<?php echo $i;?>"><?php echo $i;?></option><?php } ?></select></td><td id="discounted_price1"></td><td><input class="inp" id="freight1" placeholder="Charges"/></td><td><select id="packing_discount1" name="packing_discount" style="border: none;border-bottom: 1px solid lightgray; " onchange="calculatePacking(1);"><option value="">SELECT</option><?php for($j = 0; $j < 6; $j++) {?><option value="<?php echo $j;?>"><?php echo $j;?></option><?php } ?> </select></td><td id="packing_price1"></td><td><table class="table table-bordered"><thead><tr><th>NAME</th><th>CHARGES</th></tr></thead><tbody id="charges1"></tbody></table></td><td id="total_price1"></td><td id="minimum_price1"></td><td> <table class="table table-bordered"><thead><tr><th>PROMOTION NAME</th><th>PROMOTION PRICE</th><th>DESCRIPTION</th></tr></thead><tbody id="promotions1"></tbody></table></td><td><a id="pdf1" target="_blank"><div>Click here</div></a>/<a id="video1" target="_blank"><div>Click here</div></a></td></tr><tr><td colspan="12"><span id="finalresult1" style="color: red;font-size: 15px"></span></td></tr></thead><tbody></tbody></table></div> <a href="javascript:;" class="addMoreTable"><i class="fa fa-minus removeTable" aria-hidden="true" style="float: right;font-size: 20px; color:orange; margin-left: 10px;"></i></a><div class="lastfield"></div></div></div></div></div></div></div>');
              addplustolastfield();
              initializeSelect2('select31');
              
              i++;
                });

                $(document).on('click', '.removeTable', function(){
                    $(this).parents(".fieldGroup").remove();
                      checkplus();
                      addplustolastfield1();
                    });

                var j=i+1;
                $(document).on('click', '.addMoreTable1', function(){
   
                $('#addAnotherTable').append('<div class="add-field"><div class="row fieldGroup" id="row'+j+'"><div class="row" id="addAnotherTable"><div class="col-sm-12"><div class="card-box table-responsive"><table id="example" class="table text-center table-striped table-bordered"><thead><tr><th style="text-align: center; width:15%;">MACHINE NAME/USP</th><th style="text-align: center; ">PRICE</th><th style="text-align: center;">DISCOUNT %</th><th style="text-align: center;">DISCOUNTED PRICE</th><th style="text-align: center;">FREIGHT</th><th style="text-align: center;">PACKING %</th><th style="text-align: center;">PACKING PRICE</th><th style="text-align: center;">MISCELLENEOUS</th><th style="text-align: center;">TOTAL PRICE</th><th style="text-align: center;">MINIMUM PRICE</th><th style="text-align: center; ">  PROMOTIONS</th><th style="text-align: center;">PDF/Video</th></tr><tr><td ><div class="form-group" style="margin-bottom:0px;"> <select class="form-control machine_name select4'+j+' checkplus" name="machine_name" id="machine_id'+j+'" required="" onchange="showDetails('+j+'); showMisc('+j+'); showClients('+j+'); showPromotions('+j+')" ></select></div><hr><div style="width:100px; margin-top:30px;"><strong>USP:</strong><span id="usp'+j+'"></span></div><hr><table class="table table-bordered"><thead><tr><th>CLIENT NAME</th><th>CITY</th><th>INDUSTRY</th></tr></thead><tbody id="clients'+j+'"></tbody></table></td><td id="machine_price'+j+'"></td><td><select id="discount'+j+'" name="discount" style="border: none;border-bottom: 1px solid lightgray; " onchange="calculateDiscount('+j+');"><option value="">SELECT</option><?php for($i = 0; $i < 21; $i++) {?><option value="<?php echo $i;?>"><?php echo $i;?></option><?php } ?></select></td><td id="discounted_price'+j+'"></td><td><input class="inp" id="freight'+j+'" placeholder="Charges"/></td><td><select id="packing_discount'+j+'" name="packing_discount" style="border: none;border-bottom: 1px solid lightgray; " onchange="calculatePacking('+j+');"><option value="">SELECT</option><?php for($j = 0; $j < 6; $j++) {?><option value="<?php echo $j;?>"><?php echo $j;?></option><?php } ?> </select></td><td id="packing_price'+j+'"></td><td><table class="table table-bordered"><thead><tr><th>NAME</th><th>CHARGES</th></tr></thead><tbody id="charges'+j+'"></tbody></table></td><td id="total_price'+j+'"></td><td id="minimum_price'+j+'"></td><td> <table class="table table-bordered"><thead><tr><th>PROMOTION NAME</th><th>PROMOTION PRICE</th><th>DESCRIPTION</th></tr></thead><tbody id="promotions'+j+'"></tbody></table></td><td><a id="pdf'+j+'" target="_blank"><div>Click here</div></a>/<a id="video'+j+'" target="_blank"><div>Click here</div></a></td></tr><tr><td colspan="12"><span id="finalresult'+j+'" style="color: red;font-size: 15px"></span></td></tr></thead><tbody></tbody></table></div> <a href="javascript:;" class="addMoreTable"><i class="fa fa-minus removeTable" aria-hidden="true" style="float: right;font-size: 20px; color:orange; margin-left: 10px;"></i></a><div class="lastfield"></div></div></div></div></div></div></div>');
              addplustolastfield();
              initializeSelect3('select4'+j);
                j++;
                });

  //                  $('#checkpromotion').on('change', function() {
  //      if($(this).prop('checked') === true){
  //         var promoprice = $('.promoprice').text();
  //         alert(promoprice);
  //       }else{
  //         var promoprice = $('.promoprice').text('');
  //         alert(promoprice);
  //       }
  // });

             

</script>

      <script>

          function initializeSelect2(selectElementObj) {
            var purl="<?php echo page_url;?>Sales_tool/getMachineName";
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

           function initializeSelect3(selectElementObj) {
            var purl="<?php echo page_url;?>Sales_tool/getMachineName";
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




    function showDetails(row) {
        var machine_price = $("#machine_price"+row).val();
        var maxdiscount = $("#maxdiscount"+row).val();
        var machine_id = $('#machine_id'+row).val();

        if(machine_id != null) { 
         $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Sales_tool/getAllDetails",
            data:{machine_id:machine_id},
            success:function(data){
                var arr = data.split('|');
                $("#machine_name"+row).val(arr[0]);
                var minimum_price = arr[1] - (arr[1] * arr[3])/100;
                // alert(minimum_price);
                $("#minimum_price"+row).text(minimum_price);
                // alert(arr[1]);
                $("#machine_price"+row).html(arr[1]);
                $("#usp"+row).text(arr[2]);
                $("#maxdiscount"+row).val(arr[3]);
                var path = "<?php echo sfdocument;?>sales_tool/"+arr[4];
                $('#pdf'+row).attr('href', path);
                $('#video'+row).attr('href', arr[5]);
            }
        });

         $("#discount"+row).prop('selectedIndex',0);
         $("#packing_discount"+row).prop('selectedIndex',0);
         $("#freight"+row).val('');

            calculateTotal(row);
          } else {
         $("#discounted_price"+row).text('');
         $("#minimum_price"+row).text('');
         $('#pdf'+row).attr('href', '');
         $('#video'+row).attr('href', '');
         $('#usp'+row).text('');
         $('#machine_price'+row).text('');
          }
    }

    function showMisc(row) {
        var machine_id = $('#machine_id'+row).val();
       
        //var misc_charges = $(".misc_charges").val();
        // if(machine_id != null) {
        // $('.showDetails').css('display', '');
        // } else {
        // $('.showDetails').css('display', 'none');

        // }

         $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Sales_tool/getAllMisc",
            data:{machine_id:machine_id,rowid:row},
            success:function(data){
                $('#charges'+row).html(data);
            }
        });

                     calculateTotal(row);
    }

       function showClients(row) {
        var machine_id = $('#machine_id'+row).val();

        // if(machine_id != null) {
        // $('.showDetails').css('display', '');
        // } else {
        // $('.showDetails').css('display', 'none');

        // }

         $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Sales_tool/getAllClients",
            data:{machine_id:machine_id},
            success:function(data){
                 $('#clients'+row).html(data);
            }
        });
    }

        function showPromotions(row) {
        var machine_id = $('#machine_id'+row).val();
        // alert(machine_id);

        // if(machine_id != null) {
        // $('.showDetails').css('display', '');
        // } else {
        // $('.showDetails').css('display', 'none');

        // }

         $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Sales_tool/getAllPromotions",
            data:{machine_id:machine_id, rowid:row},
            success:function(data){
                 $('#promotions'+row).html(data);
            }
        });

                     calculateTotal(row);
    }

    function deduct_promotions(row, i) {
      // alert(i);
      var total_price = $('#total_price'+row).text();
      
      var checkedValue = $('.checkpromotion'+row+i+':checked').val();
      // alert(checkedValue);
      var total = total_price - checkedValue;
      $('#total_price'+row).text(total);
      var uncheckedValue = $('.checkpromotion'+row+i+':checked').val();
    }

    function calculateTotal(row) {

         $('#finalresult'+row).text('');
        var misc_charges = 0; 
         var promo = 0; 
        var machine_id = $('#machine_id'+row).val();
        var machine_name = $("#machine_name"+row).val();
        var machine_price = $("#machine_price"+row).text();
        var discount = $("#discount"+row).val();
        var discounted_price = $("#discounted_price"+row).text();
        var freight = $("#freight"+row).val();
        var packing_discount = $("#packing_discount"+row).val();
        //var packing_price = $("#packing_price"+row).val();
        var href = $("#pdf"+row).attr("href");
        var video = $("#video"+row).attr("href");
        var usp = $("#usp"+row).text();

        /** Calculate packing price **/
          if (packing_discount != '') {
            var packing_price = (machine_price * packing_discount)/100;
            $("#packing_price"+row).html(packing_price);
            } else {
            $("#packing_price"+row).html('');  
            } 
        /** end **/
        //alert(href);
     

     if(discount!='' && packing_discount!='')
     {

        

        $(".misss"+row).each(function () {                  
        misc_charges += parseFloat($(this).text());  

        });
        





        var total_price = parseFloat(discounted_price) + parseFloat(freight) + parseFloat(packing_price) + parseFloat(misc_charges);

        /** CHECK FOR PROMOTIONS **/



        $("input[class='checkpromotion"+row+"']").each(function(index, checkbox){
        if(checkbox.checked)
        {
        promo += parseFloat(checkbox.value);
        }
        })


       /** $(".checkpromotion"+row+" input:checked").each(function () {                  
        promo += parseFloat($(this).val());  

        }); **/

        // alert(promo);

       var  total_price=parseFloat(total_price)-parseFloat(promo);
        /** END **/
       
         $('#total_price'+row).text(total_price);
         var minimum_price = $('#minimum_price'+row).text();

         if(parseFloat(total_price) < parseFloat(minimum_price)) {
            $('#finalresult'+row).text('You cannot quote less than Rs.'+minimum_price);
          //  $('#finalresult').css('display', 'block');
         }



     }else{


     }
    }

    function calculateDiscount(row) {
        var machine_price = $("#machine_price"+row).text();
        var discount = $("#discount"+row).val();
        //var discounted_price = $("#discounted_price").val();

            if (discount != '') {
            var discounted_price = machine_price - (machine_price * discount)/100;
            $("#discounted_price"+row).html(discounted_price);
            } else {
            $("#discounted_price"+row).html('');  
            } 

            calculateTotal(row);
    }

    function calculatePacking(row) {
        var machine_price = $("#machine_price"+row).text();
        var packing_discount = $("#packing_discount"+row).val();
        //var discounted_price = $("#discounted_price").val();
       
            if (packing_discount != '') {
            var packing_price = (machine_price * packing_discount)/100;
            $("#packing_price"+row).html(packing_price);
            } else {
            $("#packing_price"+row).html('');  
            } 

             calculateTotal(row);

            
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

function addplustolastfield()
{
    $('div.lastfield:not(:last)').css('display','none');

    $('div.lastfield:last').html('<a href="javascript:;" class="addMoreTable1"><i class="fa fa-plus" aria-hidden="true" style="float: right;font-size: 20px; color:orange;"></i></a>');

  
}

function addplustolastfield1()
{
    $('div.lastfield:last').html('<a href="javascript:;" class="addMoreTable1"><i class="fa fa-plus" aria-hidden="true" style="float: right;font-size: 20px; color:orange;"></i></a>');
    $('div.lastfield:last').css('display','');

}

function checkplus() {
  var total_class = $('.checkplus').length;

    if (total_class > 0) {
     
      $('#addMoreTable').css('display', 'none');
      $('.firstplus').css('display','none');

    } else {
      $('#addMoreTable').css('display', '');
      $('.firstplus').css('display','');

     
    }
}


</script>
</body>
</html>
