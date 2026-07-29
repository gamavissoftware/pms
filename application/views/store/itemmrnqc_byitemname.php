<!DOCTYPE html>

<html>
<head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="NJ Media">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title>MRN</title>



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

<!-- <link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css"> -->
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
                <!-- <form method="post" action="https://www.prestomitr.com/index.php/FMS/bulksalesforceorderapproval"> -->
                <div class="card-box ">
                
                <div class="form-group">
                <div class="row">
                    <div class="col-sm-3">                    
                </div>
                <div class="col-sm-6">
                    <label>Filter By:</label>
                    <?php
                    $uri=$this->uri->segment(4);
                    $dis="";
                    $dis1="";
                    if($uri <>'')
                    {
                       if($uri==1)
                       {
                        $dis="checked";

                       }elseif($uri==2)
                       {
                        $dis1="checked";
                       } 
                    }else
                    {
                        $dis="checked";
                    }

                     if($uri <>'')
                    {
                       if($uri==1)
                       {
                        $b="display:none;";
                        $a= "";
                        
                       }else
                       {

                        $b = "";

                        $a = "display:none;";
                       } 

                       
                    }else
                    {
                        $b="display:none;";
                        $a = "";

                    }

                    ?>

                    &nbsp;
                    &nbsp;
                    <input type="radio" name="selecton" id="selecton" value="1" onchange="getdataby(this.value);" <?php echo $dis; ?>>Filter By Item Name 
                    &nbsp; &nbsp;
                    <input type="radio" name="selecton" id="selecton" value="2" onchange="getdataby(this.value);" <?php echo $dis1; ?> >Filter By Vendor
                </div>
                <div class="col-sm-3"></div>
                </div>
                <div class="row">
                <div class="col-sm-3">
                </div>
                <div class="col-sm-6" id="item" style="<?php echo $a;?>"><label>Please Enter Item Name:</label><select class="form-control select21 mand" name="ionumber" id="ionumber"  onchange="window.location.href='<?php echo page_url; ?>Reporting/itemmrn_byitemname/'+this.value+'/1'"></select></div>
                <div class="col-sm-6" id="vendor" style="<?php echo $b;?>" ><label>Please Enter Vendor Name:</label><select style="width: 100%"  class="form-control select22 mand" id="byvendor" name="byvendor"   onchange="window.location.href='<?php echo page_url; ?>Reporting/itemmrn_byitemname/'+this.value+'/2'"></select></div>
                <div class="col-sm-3"></div>
                </div>
                </div>
                <!-- <div class="col-sm-2"><input type="submit" class="form-control btn btn-success btn-xs" name="submit" value="search" ></div> -->
                </div>
                </div>
                <!-- Page-Title -->

                <div class="row">

                    <div class="col-sm-12">

                        <div class="page-title-box  col-md-8">
                            <?php
                            $uri=$this->uri->segment(3);
                            $filterby=$this->uri->segment(4);
                            if($filterby==1){
                            $iname='';
                            if($uri <>'')
                            {
                                $resty=$this->db->select('part')->from('machine_parts_with_picture_view')->where('id',$uri)->get();
                                    if($resty->num_rows()>0)
                                    {
                                        foreach($resty->result() as $resty1);
                                        
                                        $iname='Item Name:'.$resty1->part;
                                    }
                            }
                        }else{
                            
                            $iname='';
                            if($uri <>'')
                            {
                                $resty=$this->db->select('name')->from('vendors')->where('id',$uri)->get();
                                    if($resty->num_rows()>0)
                                    {
                                        foreach($resty->result() as $resty1);
                                        
                                        $iname='Vendor Name:'.$resty1->name;
                                    }
                            }

                        }
                            ?>
                            <h4 class="page-title">MRN REQUEST For (<?php echo $iname;?>)</h4>

                        </div>
                        <!-- <div class="col-md-4 pull-right">
                          <a href="<?php echo page_url; ?>Reporting/itemmrn_history/"><span class="btn btn-danger btn-xs">VIEW HISTORY</span></a>
                      </div> -->

                    </div>

                </div>

                <!-- end page title end breadcrumb -->

	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

		<div class="row">
        <form onsubmit="return validate();" action="<?php echo page_url;?>Store/mrngateentrydone_item" method="post" onsubmit="return validate();" id="frm">
                    <div class="col-sm-12">
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                    <div class="form-group">
                    <label>Gate Entry No. <span style="color:red">*</span></label>
                    <input type="text" name="gateentry" class="form-control mand" placeholder="Gate entry no" required>
                    </div>
                    </div>

                    <div class="col-md-4">
                    <div class="form-group">
                    <label>Bill No. <span style="color:red">*</span></label>
                    <input type="text" name="billno" id="billno" class="form-control mand" placeholder="Bill no" required>
                    </div>                    
                    </div>
                        <div class="card-box table-responsive">

                            <table id="example" class="table table-striped table-bordered manglesh">

                                <thead>

                                <tr>

                                    <th>SR NO.</th>

                                    <th>ALL <input type="checkbox"  id="checkAll" onchange="checkall();"></th>
 
									<th>PO NO.</th>

									<th>ITEM</th>

									<th>ITEM TYPE</th>

									<th>REQ QTY</th>

									<th>PREV INWARDED</th>

									<th>FINCODE</th>

									<th>SPECIALIZATION</th>

									<th>VENDOR NAME</th>

									<th>RECVIED ITEM QTY</th>

								</tr>

                                </thead>





                                <tbody>

								

                                </tbody>

                            </table>

                        </div>
                        <input type="submit" name="save" id="sub" value="SUBMIT" class="btn btn-success pull-right btn-sm">
                    </div>
                    
                </div>
                </form>

              





          <!-- Footer -->

               <?php $this->load->view('common/footer');?>

                <!-- End Footer -->



            </div> <!-- end container -->

        </div>

        <!-- end wrapper -->





<!-- Modal -->

<div id="myModal" class="modal fade" role="dialog">

  <div class="modal-dialog">



<!-- <form action="<?php echo page_url;?>Store/mrngateentrydone" method="post" >

    <input type="hidden" name="poid" id="poid" value="">

    <input type="hidden" name="pono" id="pono" value="">

    

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal">&times;</button>

        <h4 class="modal-title">CREATE GATE ENTRY FOR PO <span id="po"></span></h4>

      </div>

      <div class="modal-body">

      <div class="row">

          

           <div class="col-md-6">

            <div class="form-group">

                <label>Pending Qty <span class="uni" style="color:red;font-size:10px;"></span> <span style="color:red">*</span></label>

              <input type="text" name="pendqty" id="pendqty" class="form-control mand" readonly required>

            </div>

              

          </div>

          

          

          <div class="col-md-6">

            <div class="form-group">

                <label>Recieved Qty <span class="uni" style="color:red;font-size:10px;"></span> <span style="color:red">*</span></label>

              <input type="text" name="recqty" id="recqty" class="form-control mand" onkeyup="checkifitsvalid(this.value);" required>

            </div>

              

          </div>



   <div class="col-md-6 weightsection">

            <div class="form-group">

                <label>Recieved Weight <span class="unityu" style="color:red;font-size:10px;"></span> <span style="color:red">*</span></label>

              <input type="text" name="recweight" id="recweight" class="form-control" required>

            </div>

              

          </div>

          <script>

          

          function checkifitsvalid(vaaal)

          {

              var req=$("#pendqty").val();

              

              if(parseFloat(vaaal)>parseFloat(req))

              {

                  alert('Recieved Qty cannot be greater than pending Qty');

                  

                  $("#recqty").val('');

              }

              

          }

          

          

          $( document ).ready(function() {

              $("#recqty").on("input", function(evt) {

   var self = $(this);

   self.val(self.val().replace(/[^0-9\.]/g, ''));

   if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 

   {

     evt.preventDefault();

   }

 });

 

});

 
	
          </script>

          

            <div class="col-md-6">

            <div class="form-group">

            <label>Gate Entry No. <span style="color:red">*</span></label>

            <input type="text" name="gateentry" class="form-control mand" placeholder="Gate entry no" required>

            </div>

            

            </div>

            

               <div class="col-md-6">

            <div class="form-group">

            <label>Bill No. <span style="color:red">*</span></label>

            <input type="text" name="billno" id="billno" class="form-control mand" placeholder="Bill no" required>

            </div>

            

            </div>

          

      </div>

      </div>

      <div class="modal-footer">

        <input type="submit" name="sub" id="sub" class="btn btn-success btn-sm">

      </div>

    </div>

</form> -->

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
<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
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
 "iDisplayLength": 100,

fixedHeader: true,

   scrollCollapse: true,

   fixedColumns:   {

            leftColumns: 3

        },
        "ordering": false,

 "sAjaxSource": "<?php echo page_url;?>Reporting/mrnitemrequest_byitemname/<?php echo $this->uri->segment(3); ?>/<?php echo $this->uri->segment(4); ?>",

 "aoColumns": [

					{ mData: 'sr_no' },

	                { mData: 'checkbox'},

	                { mData: 'pono'},

					{ mData: 'itemname'},

					{ mData: 'itemtype'},

					{ mData: 'qty'},


					{ mData: 'prevqty'},

					{ mData: 'fincode'},

					{ mData: 'specialization'},

					{ mData: 'vendorname'},

					{ mData: 'gentry'}

				

						

						

                ]

        });   





   


});







function opemmodalpopup(poid,pono,pendqty,unit,conunit,conweight,conunitvalue)

{
  $(".weightsection").css('display','none');
  $("#recweight").removeClass('mand'); 
  $("#recweight").attr('required',false); 
  $(".unityu").text(""); 

  $("#po").text(pono);

  $("#pono").val(pono);

  $("#poid").val(poid);

  $("#pendqty").val(pendqty);

  $(".uni").text("(in "+unit+")");

  if(conunit>0)
  {
    $(".weightsection").css('display','');
    $("#recweight").addClass('mand');
    $("#recweight").attr('required',true); 
    $(".unityu").text("(in "+conunitvalue+")"); 
  }

    $("#myModal").modal('show');

    

    

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

     alert('All Fields are mandatory');

 $("#sub").attr('disabled',false);
  $("#sub").val('Submit');
     return false;

 }

    

}



</script>
<script>
function checkifitsvalid(vaaal,i,y)

{
    var req=$("#pendqty"+i).val();
    //alert(req); 
    if(parseFloat(vaaal)>parseFloat(req))
    {
        alert('Recieved Qty cannot be greater than pending Qty');            

        $("#receviedqty"+y).val('');
    }

}

function checkifvalid(evt)
{
    var self = $(evt);
self.val(self.val().replace(/[^0-9\.]/g, ''));
if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
{
evt.preventDefault();
}
}
// $( document ).ready(function() {
// $(".itemreq").on("input", function(evt) {
// var self = $(this);
// self.val(self.val().replace(/[^0-9\.]/g, ''));
// if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
// {
// evt.preventDefault();
// }
// });
// });
 </script>
<script>
//     $("#checkAll").click(function () {
//      if($('input:checkbox').not(this).prop('checked', this.checked)){
//         $(".itemrequest").css('display','');
//         $(".itemreq").attr('required',true);
//      }else{
//         $(".itemrequest").css('display','none');
//         $(".itemreq").attr('required',false);
//      }
//  });
    function showqty(i)
    {
        //alert(i);
        if($("#show"+i).is(':checked'))
        {
            $("#showdis"+i).css('display','');  
            $("#receviedqty"+i).attr('required',true);  
        }else{
            $("#showdis"+i).css('display','none'); 
            $("#receviedqty"+i).attr('required',false);  
        }
    }
</script>
<script>
     var purl2="<?php echo page_url ?>Reporting/getmrnitemname";
       $('.select21').select2({ 
        placeholder: 'TYPE TO SELECT',
        minmumInputLength:4,
        allowClear: true,
        ajax: {
        url: purl2,
        dataType: 'json',
        delay: 250,
        data: function (params) {
        return {
        searchTerm: params.term,
        type: $("#type"+$(this).attr("data-id")).val() //here your company data
        };
        },processResults: function (data) {
        return {
        results: data
        };
        },
        cache: true
        }
      });
       var purl3="<?php echo page_url ?>Reporting/getmrnvendor";
       $('.select22').select2({ 
        placeholder: 'TYPE TO SELECT',
        minmumInputLength:4,
        allowClear: true,
        ajax: {
        url: purl3,
        dataType: 'json',
        delay: 250,
        data: function (params) {
        return {
        searchTerm: params.term,
        type: $("#type"+$(this).attr("data-id")).val() //here your company data
        };
        },processResults: function (data) {
        return {
        results: data
        };
        },
        cache: true
        }
      });
</script>

<!-- <script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script> -->


<script>

$(document).ready(function(){

  $("#loginForm").on("submit", function(){

    $("#pageloader").fadeIn();

  });//submit

});//document ready
 function checkall()
 {
     //alert('hi'); 
    if($("#checkAll").is(':checked'))
    
        {
           $(".subcheckbox").prop('checked',true);
           $(".itemrequest").css('display','');
            $(".itemreq").attr('required',true);
        }else{
            $(".subcheckbox").prop('checked',false);
            $(".itemrequest").css('display','none');
            $(".itemreq").attr('required',false);
        }
 }
</script>
<script type="text/javascript">
    function getdataby(i) 
    {
        if($("#selecton").is(':checked'))
        {
            $('#item').css('display','');
            $('#vendor').css('display','none');
        }else
        {
            $('#item').css('display','none');
            $('#vendor').css('display','');
        }
    }
</script>
</body>

</html>
