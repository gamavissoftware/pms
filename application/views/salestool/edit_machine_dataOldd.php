<?php
$machineid=$this->uri->segment(3);
$machinesat=$this->db->select('b.machinename')->from('salestoolmachines b')->where('b.id',$machineid)->get();
		if($machinesat->num_rows()>0)
		{
			foreach($machinesat->result() as $restyui1);
			
			
		}else
		{
			echo "INVALID ACCESS";exit;
		}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>UPDATE MACHINE DATA</title>

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
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
						
                           
                            <h4 class="page-title">UPDATE MACHINE DATA</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
		<div class="row">
				<form name="frm" method="post" action="<?php echo page_url;?>Salestool/machinedataupdate/<?php echo $this->uri->segment(3);?>" enctype="multipart/form-data">
				
				<div class="card-box table-responsive">
				<div class="col-sm-12">
					<div class="col-md-3">
					<div class="form-group">
					<label for="field-1" class="control-label">MACHINE NAME</label>
					<span id="error_holidayname" style="color:red;"></span>
					<input type="text" class="form-control" id="machine" style="text-transform: uppercase;" name="machine" placeholder="" value="<?php echo $restyui1->machinename;?>" required>
					</div>
					</div>
					<div class="col-md-9">
					<div class="form-group">
					<label for="field-1" class="control-label">RELATED MACHINES</label>
					<span id="error_holidayname" style="color:red;"></span>
					<select name="relatedmachine[]" class="select2" multiple>
				
					<?php
					/** GET ALREADY ENTERED DATA **/
					$rela=array();
					$related=$this->db->select('relatedmid')->from('relatedmachines')->where('mid',$machineid)->get();
					if($related->num_rows()>0)
					{
						foreach($related->result() as $relatedd)
						{
							$rela[]=$relatedd->relatedmid;
						}
						
					}
					/** END ***/
					$machinesat1=$this->db->select('machinename,id')->from('salestoolmachines')->where('id !=',$machineid)->get();
					if($machinesat1->num_rows()>0)
					{
						foreach($machinesat1->result() as $machinesat11)
						{ 
						if(in_array($machinesat11->id,$rela))
						{
							$a="selected";
						}else{
							$a='';
						}
						
						?>
						
						<option value="<?php echo $machinesat11->id;?>" <?php echo $a;?>><?php echo $machinesat11->machinename;?></option>
					<?php
					}
					}
					
					?>
					</select>
					</div>
					</div>
				</div>
				
				<div class="col-sm-12">
				<div class="col-md-4">
				<?php
				$moddel=$this->db->select('id,model')->from('salestoolmachinesmodel')->where('mid',$this->uri->segment(3))->get();
				if($moddel->num_rows()>0)
				{
				?>
				<div class="existngmodel">
				
				<?php
				foreach($moddel->result() as $models)
				{
					$attach=$this->db->select('attachment')->from('modelwiseattachment')->where('modelid',$models->id)->where('mid',$this->uri->segment(3))->get();
					if($attach->num_rows()>0)
					{
						foreach($attach->result() as $attach1);
						$oldimg=$attach1->attachment;
					}else{
						
						$oldimg='';
						
					}
				?>
				<input type="hidden" name="existingmodel[]" value="<?php echo $models->id;?>">
				<input type="hidden" name="existingmodelattchment<?php echo $models->id;?>" value="<?php echo $oldimg;?>">
				<div class="col-md-6">
					<div class="form-group">
					<label for="field-1" class="control-label">MODEL</label>
					<span id="error_holidayname" style="color:red;"></span>
					<input type="text" class="form-control" id="existingmodel" style="text-transform: uppercase;" name="existmodalname<?php echo $models->id;?>" placeholder="" value="<?php echo $models->model;?>" required>
					</div>
					</div>
					<div class="col-md-4">
					<div class="form-group">
					<label for="field-1" class="control-label">PDF</label>
					<span id="error_holidayname" style="color:red;"></span>
					<input type="file" class="form-control" id="extsingattachment" style="text-transform: uppercase;" name="extsingattachment<?php echo $models->id;?>" placeholder="" value="">
					<span><a href="<?php echo page_url;?>upload/machineattachment/<?php echo $oldimg;?>" target="_blank"><?php echo $oldimg;?></a></span>
					</div>
					</div>
					
					<div class="col-md-2">
					
					</div>
				<?php
				}
				?>
				</div>
				
				<?php
				}else{
				?>
				
				<div class="col-md-12">
				
				<p style="font-weight:bold;text-align:center;">NO MODEL AVAILABLE</p>
				
				</div>
				<?php
				}
				?>
				
				<div class="col-md-12">
				<div class="col-md-2"></div>
				
				<div class="col-md-8">
				<input type="checkbox"  id="newmodelcheck" name="newmodelcheck" value="1" onchange="checkfornewmodel();"><strong>&nbsp;ADD NEW MODEL</strong>
				</div>
				</div>
				
				<div class="newmodel" style="display:none;">
					<div class="col-md-6">
					<div class="form-group">
					<label for="field-1" class="control-label">MODEL</label>
					<span id="error_holidayname" style="color:red;"></span>
					<input type="text" class="form-control" id="model" style="text-transform: uppercase;" name="model[]" placeholder="" value="">
					</div>
					</div>
					<div class="col-md-4">
					<div class="form-group">
					<label for="field-1" class="control-label">PDF</label>
					<span id="error_holidayname" style="color:red;"></span>
					<input type="file" class="form-control" id="attachment" style="text-transform: uppercase;" name="attachment[]" placeholder="" value="">
					</div>
					</div>
					
					<div class="col-md-2">
					<label for="field-1" class="control-label" style="margin-top:45px"></label>
					<span class="btn btn-warning" id="addmore_btn1"><i class="fa fa-plus"></i></span>
					</div>
					
					<div id="dynamicmodel"></div>
					
					</div>
					
					</div>
					
					
					
					

					
					<div class="col-md-2">
					<?php
				$capacity=$this->db->select('id,capacity')->from('salestoolmachinescapacity')->where('mid',$this->uri->segment(3))->get();
				if($capacity->num_rows()>0)
				{
					foreach($capacity->result() as $capacity)
					{
				?>
				<input type="hidden" name="existingcapacity[]" value="<?php echo $capacity->id;?>">
					<div class="existingcapacity">
					
					<div class="col-md-12">
					<div class="form-group">
					<label for="field-1" class="control-label">CAPACITY</label>
					<span id="error_holidayname" style="color:red;"></span>
					<input type="text" class="form-control" id="existcapacity" style="text-transform: uppercase;" name="existcapacity[]" placeholder="" value="<?php echo $capacity->capacity;?>" required>
					</div>
					</div>
					</div>
					
					<?php
					}
				}else{
				?>
				<div class="col-md-12">
				
				<p style="font-weight:bold;text-align:center;">NO CAPACITY AVAILABLE</p>
				
				</div>
				<?php
				}
				?>
				
				<div class="col-md-12">
				
				
				<div class="col-md-12 text-center">
				<input type="checkbox"  id="newcapacitycheck" name="newcapacitycheck" value="1" onchange="checkfornewcapacity();"><strong>&nbsp;ADD NEW CAPACITY</strong>
				</div>
				</div>
					
					<div class="newcapacity" style="display:none">
					<div class="col-md-10">
					<div class="form-group">
					<label for="field-1" class="control-label">CAPACITY</label>
					<span id="error_holidayname" style="color:red;"></span>
					<input type="text" class="form-control" id="capacity" style="text-transform: uppercase;" name="capacity[]" placeholder="" value="">
					</div>
					</div>
					
					
					<div class="col-md-2">
					<label for="field-1" class="control-label" style="margin-top:16px"></label>
					<span class="btn btn-warning"  id="addmore_btn2"><i class="fa fa-plus"></i></span>
					</div>
					
					<div id="dynamiccapacity"></div>
					</div>
					</div>
					
					
					<div class="col-md-6">
					<?php
					$compan=$this->db->select('id,companyname,location,industry')->from('salestoolcompanyhistory')->where('mid',$this->uri->segment(3))->get();
					if($compan->num_rows()>0)
					{
						foreach($compan->result() as $compan)
						{
					?>
					
					<div class="existingcompany">
					<input type="hidden" name="existcom[]" value="<?php echo $compan->id;?>">
					<div class="col-md-4">
					<div class="form-group">
					<label for="field-1" class="control-label">COMPANY</label>
					<span id="error_holidayname" style="color:red;"></span>
					<input type="text" class="form-control" id="company" style="text-transform: uppercase;" name="exitingcompany<?php echo $compan->id;?>" placeholder="" value="<?php echo $compan->companyname;?>">
					</div>
					</div>
					
					<div class="col-md-3">
					<div class="form-group">
					<label for="field-1" class="control-label">LOCATION</label>
					<span id="error_holidayname" style="color:red;"></span>
					<input type="text" class="form-control" id="location" style="text-transform: uppercase;" name="existinglocation<?php echo $compan->id;?>" placeholder="" value="<?php echo $compan->location;?>">
					</div>
					</div>
					
					<div class="col-md-3">
					<div class="form-group">
					<label for="field-1" class="control-label">INDUSTRY</label>
					<span id="error_holidayname" style="color:red;"></span>
					 <select class="form-control" id="industry" name="existingindustry<?php echo $compan->id;?>" style="text-transform: uppercase;" placeholder="" required onkeyup="populatedata();">
							<option value="">SELECT INDUSTRY</option>
							<?php
														$restyu=$this->db->select('id,industry')->from('salestoolindustry')->where('status','1')->get();
														if($restyu->num_rows()>0)
														{
															foreach($restyu->result() as $restyu12)
															{
														?>
														<option value="<?php echo $restyu12->id;?>" <?php if($compan->industry==$restyu12->id){ ?> selected  <?php } ?>><?php echo $restyu12->industry;?></option>
														<?php
															}
														}
														?>
							
					</select>
				
					</div>
					</div>
					
					
					<div class="col-md-2">
					<label for="field-1" class="control-label" style="margin-top:45px"></label>
					<!--<span class="btn btn-warning" id="addmore_btn3"><i class="fa fa-trash"></i></span>-->
					</div>
					
					</div>
					
					<?php
						}
					}else{
					?>
					
					<div class="col-md-12">
				
				<p style="font-weight:bold;text-align:center;">NO COMPANY AVAILABLE</p>
				
				</div>
					
					<?php
					}
					?>
					
					
					<div class="col-md-12">
				
				
				<div class="col-md-12 text-center">
				<input type="checkbox"  name="newcompaycheck" id="newcompaycheck" value="1" onchange="checkfornewcompany();"><strong>&nbsp;ADD NEW COMPANY</strong>
				</div>
				</div>
				
					<div class="newcompany" style="display:none;">
					<div class="col-md-4">
					<div class="form-group">
					<label for="field-1" class="control-label">COMPANY</label>
					<span id="error_holidayname" style="color:red;"></span>
					<input type="text" class="form-control" id="company" style="text-transform: uppercase;" name="company[]" placeholder="" value="">
					</div>
					</div>
					
					<div class="col-md-3">
					<div class="form-group">
					<label for="field-1" class="control-label">LOCATION</label>
					<span id="error_holidayname" style="color:red;"></span>
					<input type="text" class="form-control" id="location" style="text-transform: uppercase;" name="location[]" placeholder="" value="">
					</div>
					</div>
					
					<div class="col-md-3">
					<div class="form-group">
					<label for="field-1" class="control-label">INDUSTRY</label>
					<span id="error_holidayname" style="color:red;"></span>
					<select class="form-control" id="industry" name="industry[]" style="text-transform: uppercase;" placeholder="">
							<option value="">SELECT INDUSTRY</option>
							<?php
														$restyu=$this->db->select('id,industry')->from('salestoolindustry')->where('status','1')->get();
														if($restyu->num_rows()>0)
														{
															foreach($restyu->result() as $restyu12)
															{
														?>
														<option value="<?php echo $restyu12->id;?>"><?php echo $restyu12->industry;?></option>
														<?php
															}
														}
														?>
							
					</select>
					
					</div>
					</div>
					
					
					<div class="col-md-2">
					<label for="field-1" class="control-label" style="margin-top:45px"></label>
					<span class="btn btn-warning" id="addmore_btn3"><i class="fa fa-plus"></i></span>
					</div>
					
					
					<div id="dynamiccompany"></div>
					
					</div>
					</div>
					
				</div>
				
				<div class="col-sm-12">
				<div class="col-md-2"><br/>
				<input type="submit" class="btn btn-sm btn-success" value="UPDATE">
				</div>
				</div>
				</div>
				</div>
				</form>
              
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
		


		
 <script>

$(document).ready(function(){
$('.select2').select2({ });
$('.select3').select2({ });


 var i=1;
 $('#addmore_btn1').click(function(){

 
 $('#dynamicmodel').append('<div id="row'+i+'" class="dynamicrowformodel"><div class="col-md-6"><div class="form-group"><label for="field-1" class="control-label">MODEL</label><span id="error_holidayname" style="color:red;"></span><input type="text" class="form-control" id="model'+i+'" style="text-transform: uppercase;" name="model[]" placeholder="" value="" required></div></div><div class="col-md-4"><div class="form-group"><label for="field-1" class="control-label">PDF</label><input type="file" class="form-control" id="attachment'+i+'" style="text-transform: uppercase;" name="attachment[]" placeholder="" value="" required></div></div><div class="col-md-2"><label for="field-1" class="control-label" style="margin-top:45px"></label><span class="btn btn-warning btn_remove" id="'+i+'"><i class="fa fa-minus"></i></span></div></div>');
 
  i++;
 
 });
 
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id).remove();
	 
  i--;
 });
 
 
 
 var j=1;
 $('#addmore_btn2').click(function(){

 
 $('#dynamiccapacity').append('<div id="row'+j+'" class="dynamicrowforcapacity"><div class="col-md-10"><div class="form-group"><label for="field-1" class="control-label">CAPACITY</label><span id="error_holidayname" style="color:red;"></span><input type="text" class="form-control" id="capacity'+j+'" style="text-transform: uppercase;" name="capacity[]" placeholder="" value="" required></div></div><div class="col-md-2"><label for="field-1" class="control-label" style="margin-top:45px"></label><span class="btn btn-warning btn_remove1" id="'+j+'"><i class="fa fa-minus"></i></span></div></div>');
 
  j++;
 
 });
 
 
 $(document).on('click', '.btn_remove1', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id).remove();
	 
  j--;
 });
 
 
 
  var k=1;
 $('#addmore_btn3').click(function(){

 
 $('#dynamiccompany').append('<div id="row'+k+'" class="dynamicrowforcapacity"><div class="col-md-4"><div class="form-group"><label for="field-1" class="control-label">COMPANY</label><span id="error_holidayname" style="color:red;"></span><input type="text" class="form-control" id="company'+k+'" style="text-transform: uppercase;" name="company[]" placeholder="" value="" required></div></div><div class="col-md-3"><div class="form-group"><label for="field-1" class="control-label">LOCATION</label><span id="error_holidayname" style="color:red;"></span><input type="text" class="form-control" id="location'+k+'" style="text-transform: uppercase;" name="location[]" placeholder="" value="" required></div></div><div class="col-md-3"><div class="form-group"><label for="field-1" class="control-label">INDUSTRY</label><select class="form-control" id="industry'+k+'" name="industry[]" style="text-transform: uppercase;" placeholder="" required><option value="">SELECT INDUSTRY</option><?php $restyu=$this->db->select('id,industry')->from('salestoolindustry')->where('status','1')->get(); if($restyu->num_rows()>0){ foreach($restyu->result() as $restyu12){?><option value="<?php echo $restyu12->id;?>"><?php echo $restyu12->industry;?></option><?php } } ?></select></div></div><div class="col-md-2"><label for="field-1" class="control-label" style="margin-top:45px"></label><span class="btn btn-warning btn_remove1" id="'+k+'"><i class="fa fa-minus"></i></span></div></div>');
 
  k++;
 
 });
 
 
 $(document).on('click', '.btn_remove2', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id).remove();
	 
  k--;
 });
 
});


function checkfornewmodel()
{
	if($('#newmodelcheck').is(":checked"))
	{
		
		$(".newmodel").css('display','');
		$(".newmodel :input").attr('required',true);
		
		
	}else{
		
		$(".newmodel").css('display','none');
		$(".newmodel :input").attr('required',false);
		
	}
	
	
}





function checkfornewcapacity()
{
	
	if($('#newcapacitycheck').is(":checked"))
	{
		
		$(".newcapacity").css('display','');
		$(".newcapacity :input").attr('required',true);
		
		
	}else{
		
		$(".newcapacity").css('display','none');
		$(".newcapacity :input").attr('required',false);
		
	}
	
	
}


function checkfornewcompany()
{

	if($('#newcompaycheck').is(":checked"))
	{
		
		$(".newcompany").css('display','');
		$(".newcompany :input").attr('required',true);
		
		
	}else{
		
		$(".newcompany").css('display','none');
		$(".newcompany :input").attr('required',false);
		
	}
	
	
}


</script>


<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

</body>
</html>