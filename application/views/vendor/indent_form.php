<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>INDENT</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
        <style>
        body{
        	font-size:11px;
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
            <div class="container">

                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right">
						  
                               
                            </div>
                           
                            <h4 class="page-title">INDENT FORM</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">

                            <div class="row">
							<div class="col-sm-12 col-xs-12 col-md-12">
								
								<!--<div class="col-md-2"><input type="radio" name="indenttype" value="2" onchange="openform(0); removeotherrows(0);">&nbsp;<strong>General Purpose Item</strong></div>-->
								</div>
							<div style="clear:both;height:20px;"></div>
							
								<form method="post" action="<?php echo page_url;?>Store/saveindent/" action="post">
							<div id="indentform">
							
							<div class="col-sm-12 col-xs-12 col-md-12">

							<div class="col-md-2">
							<div class="form-group">
							<label for="field-2" class="control-label">Indent For</label>
							<span id="error_item_name" style="color:red;"></span>
							<select class="form-control" name="type" id="type" onChange="fetchindenttype(this.value);" style="font-size:11px;" required>
							<option value=''>Indent Type</option>
							<option value='1'>Machine Related Items</option>
							<option value='2'>General Items</option>

							</select>
							</div>
							</div>
							</div>
							<script>
							
							function fetchindenttype(vaaal)
							{
								if(vaaal!='')
								{
								$("#testtttt").css('display','');
								fetch_machine_parts('0');
									
								}else{
									
									$("#testtttt").css('display','none');
								}
								
							}
							
							</script>
							
                                <div class="col-sm-12 col-xs-12 col-md-12" style="display:none" id="testtttt">
								
											
												
												
												<div class="col-md-3 housekeepingfields">
												<div class="form-group">
												<label for="field-2" class="control-label">ITEM NAME</label>
												<span id="error_item_name" style="color:red;"></span>
												<select class="form-control select0" name="item_names[]" id="item_nameww0" style="font-size:11px;">

												</select>
												</div>
												</div>
												
												
												<div class="col-md-2">
													<div class="form-group">
														<label>QTY <span class="unit0" style="color:red;">(In PCS)</span>
														
														</label>
														<input type="number" class="form-control" min="1" name="qty[]" id="qty" value="" required>
													</div>
												</div>
												<div class="col-md-1">
													<div class="form-group" style="margin-top:25px">
														<button type="button" class="btn btn-warning" name="add" id="addmore_btn1"><i class="fa fa-plus"></i></button>
													</div>
												</div>
												
                                            </div>
											
											
											
											<div class="col-md-12">
												<div id="dynamictasks1"></div>
											</div>
											
											
											
											
										<div class="row">	
										<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" class="btn btn-success" value="Update">
											</div>
										</div>
									
									
                                   

                                </div>
								
								</div>
								</form>
								

                            </div>
                            <!-- end row -->
                        </div> <!-- end ard-box -->
                    </div><!-- end col-->

                </div>
                <!-- end row -->
 

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

        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

 <script>
$( document ).ready(function() {
	
var ot=$("#type").val();
var purl="<?php echo page_url;?>Store/fetch_items/"+$ot;
$('.select0').select2({ 
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


});

</script>
<script type="text/javascript">
$(document).ready(function(){
var k=1;

 $('#addmore_btn1').click(function(){
	 
 $('#dynamictasks1').append('<div id="row'+k+'" class="row appendrows"><div class="col-md-12"><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">CATEGORY</label><span id="error_item_name" style="color:red;"></span><select class="form-control" name="category[]" id="category0" onChange="fetch_machine(0);" style="font-size:11px;" required><option value="">--SELECT CATEGORY--</option><?php $query = $this->db->select('id, category')->from('presto_machine_part_category')->where('cattype','2')->get();foreach($query->result() as $row){?><option value="<?php echo $row->id;?>"><?php echo $row->category;?></option><?php }?></select></div></div><div class="col-md-3 housekeepingfields"><div class="form-group"><label for="field-2" class="control-label">ITEM NAME</label><span id="error_item_name" style="color:red;"></span><select class="form-control select0" name="item_names[]" id="item_nameww'+k+'" style="font-size:11px;"><?php $query = $this->db->select('item_name, id')->from('house_keeping_items')->where('status','1')->get();foreach($query->result() as $row){?><option value="<?php echo $row->id;?>"><?php echo $row->item_name;?></option><?php }?></select></div></div><div class="col-md-2"><div class="form-group"><label>QTY <span class="unit0" style="color:red;">(In PCS)</span></label><input type="number" class="form-control" min="1" name="qty[]" id="qty" value="" required></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:30px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+k+'"><i class="fa fa-close"></i></button></div></div></div></div><br/>');
 
 openform(k);
 initializeSelect2('itemselect'+k);

  k++;
  
 });
 
 
 
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id+'').remove();
 });
 
});
</script>
<script language="javascript" type="text/javascript">   

$(document).ready(function() {
	$('.select2').select2({ });
$("#save").click(function() {
	
var machine = $("#machine").val();
if(machine=='')
{
	$("#error_machine").html('Required!');
}

if(machine=='')
{
	
	return false;
}

});
});

function openform(id)
{

	$("#indentform").css('display','');
	var type=$("input[name='indenttype']:checked").val();
	if(type==1)
	{
		$(".machinefields").css('display','');
		$(".housekeepingfields").css('display','none');
	}else
	{
		$(".machinefields").css('display','none');
		$(".housekeepingfields").css('display','');
	}
	
	$.ajax({
	type:"post",
	url:"<?php echo page_url;?>Store/getcategory",
	data:"category="+type,
	success:function(data){
		
	$("#category"+id).html(data);
	}
	});
	
	
	
			
	
}

function removeotherrows(id)
{
	
$(".appendrows").remove();
	
}
</script>

<script>
	  function initializeSelect2(selectElementObj) {
         $('.'+selectElementObj).select2({});
         
      }
	  
	  function validateall()
	  {
		  
		 $('#indentform input').each(function() {
        if(!$(this).val()){
            alert('All fields are mandatory');
           return false;
        }
		
    });
		  
	  }
	  </script>
<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
    </body>
</html>