<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>MATERIAL ISSUED BUT NOT ENTERED BY STORE</title>

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
				<form method="post" action="<?php echo page_url;?>Store/getissueconfirmationagainstjobcardformultiplecheck">
				<div class="row">
    				<div class="col-sm-6">
        				<div class="page-title-box">
        				<!-- <h4 class="page-title">MATERIAL ISSUED BUT NOT ENTERED BY STORE (MACHINE ITEM) (OVERALL)</h4> -->
        				</div>
    				</div>
                     <div class="col-md-1"></div>
                    <div class="col-sm-2">
                    <input type="submit" class="btn btn-success" style="margin-top: 10px" value="Submit to mark as accepted">
                </div>
                <div class="col-md-1"></div>

                 <div class="col-sm-2">
                    <a href="<?php echo page_url; ?>Store/materialissuedstorehistory" class="btn btn-danger pull-right" style="margin-top: 10px">MATERIAL ISSUED HISTORY</a>
                </div>
				</div>
				<!-- end page title end breadcrumb -->

				<div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                   
                                    <th>Sr No.</th>
									<th>ACCEPT<input type="checkbox" class="selectall1" onchange="checkallitem1()"></th>
                                    <!-- <th>SELECT FOR CHALLAN</th> -->
                                    <th>PENDING SINCE</th>
									<th>ISSUE SLIP</th>
                                    <th>JOBCARD NO.</th>
                                    <th>INSTRUMENT NAME</th>
									<th>ITEM NAME</th>
									<th>ISSUED QTY</th>
									<th>FINCODE</th>
									<th>SPECIFICATION</th>
									<th>RACK LOCATION</th>
									
									<th>ISSUED TO</th>
									<th>ISSUED ON</th>
                                    <th>ISSUED REASON</th>
                                    <th>Roll Back</th>
							
                                </tr>
                                </thead>


                                <tbody>
								   
                                </tbody>
                            </table>



 </div>
                    </div>
                </div>
          </form>
		  <!-- Footer -->


       

		  				<!-- <div class="row">
				<div class="col-sm-12">
				<div class="page-title-box">


				<h4 class="page-title">MATERIAL ISSUED ENTERED BY STORE HISTORY FOR 7 DAYS</h4>
				</div>


				</div>
				</div> -->
				
				
				<!-- <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example1" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                   
                                    <th>Sr No.</th>
					
									<th>ISSUE SLIP</th>
                                    <th>JOBCARD NO.</th>
                                     <th>INSTRUMENT NAME</th>
									<th>ITEM NAME</th>
									<th>ISSUED QTY</th>
									<th>FINCODE</th>
									<th>SPECIFICATION</th>
									<th>RACK LOCATION</th>
									
									<th>ISSUED TO</th>
									<th>ISSUED ON</th>
							
                                </tr>
                                </thead>


                                <tbody>
								
                                </tbody>
                            </table>



 </div>
                    </div>
                </div> -->
          
          
				<!-- end page title end breadcrumb -->

		  
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
 "bSort": false,
 "pageLength": 25,
 "bLengthChange" : false,
fixedHeader: true,
   fixedColumns:   {
            leftColumns: 3
        },
 "sAjaxSource": "<?php echo page_url;?>Reporting/storeunaccpteditems",
 "aoColumns": [
				
				{ mData: 'sr_no' },
				{ mData: 'accept' },
                // { mData: 'challanselect'},
                { mData: 'pendingsince'},
				{ mData: 'issueslip' },
				{ mData: 'jobcard'},
                { mData: 'instrument'},
				{ mData: 'itemname' },
				{ mData: 'qty'},
				{ mData: 'fincode'},
				{ mData: 'specialization'},
				{ mData: 'racklocation'},
				
				{ mData: 'issuedto'},
				{ mData: 'issuedOn'},
                { mData: 'issuedreason'},
                { mData: 'rollback'}
				
						
						
                ]
        });   
		




$('#exampleh1').dataTable({
 "bProcessing": false,
 "pagination":true,
 "bSort": false,
 "pageLength": 50,
fixedHeader: true,
   fixedColumns:   {
            leftColumns: 3
        },
 "sAjaxSource": "<?php echo page_url;?>Reporting/storeunaccpteditems/1",
 "aoColumns": [
                
                { mData: 'sr_no' },
                { mData: 'accept' },
                  { mData: 'pendingsince'},
                { mData: 'issueslip' },
                { mData: 'jobcard'},
                { mData: 'instrument'},
                { mData: 'itemname' },
                { mData: 'qty'},
                { mData: 'fincode'},
                { mData: 'specialization'},
                { mData: 'racklocation'},
                
                { mData: 'issuedto'},
                { mData: 'issuedOn'},
                { mData: 'issuedreason'}

                
                        
                        
                ]
        });   
        






$('#exampleh2').dataTable({
 "bProcessing": false,
 "pagination":true,
 "bSort": false,
 "pageLength": 50,
fixedHeader: true,
   fixedColumns:   {
            leftColumns: 3
        },
 "sAjaxSource": "<?php echo page_url;?>Reporting/storeunaccpteditems/2",
 "aoColumns": [
                
                { mData: 'sr_no' },
                { mData: 'accept' },
                  { mData: 'pendingsince' },
                { mData: 'issueslip' },
                { mData: 'jobcard'},
                { mData: 'instrument'},
                { mData: 'itemname' },
                { mData: 'qty'},
                { mData: 'fincode'},
                { mData: 'specialization'},
                { mData: 'racklocation'},
                
                { mData: 'issuedto'},
                { mData: 'issuedOn'},
                 { mData: 'issuedreason'}
                
                        
                        
                ]
        });   
        



		
		$('#example1').dataTable({
 "bProcessing": false,
 "pagination":true,
fixedHeader: true,
   fixedColumns:   {
            leftColumns: 3
        },
 "sAjaxSource": "<?php echo page_url;?>Reporting/storeunaccpteditemshistory",
 "aoColumns": [
				
				{ mData: 'sr_no' },

				{ mData: 'issueslip' },
				{ mData: 'jobcard'},
                { mData: 'instruments_name'},
				{ mData: 'itemname' },
				{ mData: 'qty'},
				{ mData: 'fincode'},
				{ mData: 'specialization'},
				{ mData: 'racklocation'},
				
				{ mData: 'issuedto'},
				{ mData: 'issuedOn'},
                 { mData: 'issuedreason'}
				
						
						
                ]
        });   
});


</script>

<script>

    function checkallitem1()
{
    if($('.selectall1').is(":checked"))
    {
        $(".checkitems1").prop('checked', true);
    }else
    {
        $(".checkitems1").attr('checked',false);
    }
    
}

function checkallitem2()
{
	if($('.selectall2').is(":checked"))
	{
		$(".checkitems2").prop('checked', true);
	}else
	{
		$(".checkitems2").attr('checked',false);
	}
	
}

function checkallitem3()
{
    if($('.selectall3').is(":checked"))
    {
        $(".checkitems3").prop('checked', true);
    }else
    {
        $(".checkitems3").attr('checked',false);
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

function acceptitems(id)
{
/**	if($('#accept'+id).is(":checked"))
	{ **/
		$("#myModal").modal('show');
		$("#issueid").val(id);
		 $(".blockeddata").html(''); 
		$.ajax({
        type:"post",
        url:"<?php echo page_url;?>Store/getblockedlistforthisitem",
        data:"id="+id,
        success:function(data){
            if(data!='')
            {
               $(".blockeddata").html(data); 
                
            }
            
        }
        
        });
        
        
        
        
        	$.ajax({
        type:"post",
        url:"<?php echo page_url;?>Store/getthisitemdetails",
        data:"id="+id,
        success:function(data){
            if(data!='')
            {
               var s=data.split('|');
               $("#iname").text(s[0]);
               $("#jobcards").text(s[2]);
               $("#qtyy").text(s[1]);
                
            }
            
        }
        
        });
        
        
		
	/** $.ajax({
        type:"post",
        url:"<?php echo page_url;?>Store/acceptissueditems",
        data:"id="+id,
        success:function(data){
        if(data==1)
		{
			
			$("#c"+id).css('display','none');
			$("#ce"+id).css('display','');
		}
         
        }
        }); **/
		
/**	}else{
		
		
		$("#myModal").modal('hide');
	} **/
	
}



function acceptnonjobcarditems(id)
{
    
    $.ajax({
        type:"post",
        url:"<?php echo page_url;?>Store/acceptissueditems",
        data:"id="+id,
        success:function(data){
        if(data==1)
		{
			
			$("#c"+id).css('display','none');
			$("#ce"+id).css('display','');
		}
         
        }
        });
    
}
function acceptitems1(id)
{

	if($('#accept1'+id).is(":checked"))
	{
		
	 $.ajax({
        type:"post",
        url:"<?php echo page_url;?>Store/acceptissueditems1",
        data:"id="+id,
        success:function(data){
        if(data==1)
		{
			
			$("#c1"+id).css('display','none');
			$("#ce1"+id).css('display','');
		}
         
        }
        });
		
	}else{
		
		
	}
	
}

function selectitemforchallan()
{
    if($('input[class=forchallan]').is(":checked")) {


    var valuesArray = $('input[class="forchallan"]:checked').map(function () {  
    return this.value;
    }).get().join(",");


    $.ajax({
    type:"post",
    url:"<?php echo page_url;?>Reporting/encodedata",
    data:"val="+valuesArray,
    success:function(data){
    if(data!="NA")
    {

        document.location="<?php echo page_url;?>Challan/rgpchallanbyissue/"+data;
    }       
    }
    });

    }else {
    //none is checked
    alert('Atleast one items needs to be selected for generating challan');
    }

}

function rollbackrecord(issueid,blockid)
{

    if($('#rollback' + issueid).is(":checked"))
    {
              
        $.ajax({
        type:"post",
        url:"<?php echo page_url;?>Store/rollback_issue",
        data:"issueid="+issueid+"&blockid="+blockid,
        success:function(data){
            if(data>0)
            {
                $("#removed"+issueid).html('Issue has been Roll Backed');
                $("#c"+issueid).html('');
            }
        }
        });

    }else
    {
    
    }


}
</script>

</body>
</html>
