<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>SALES TOOL LIBRARY</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
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

    </head>


     <body>





        <!-- Navigation Bar-->

        <header id="topnav">
	<?php $this->load->view('common/nav-menu.php');?>


        </header>

        <!-- End Navigation Bar-->





        <div class="wrapper">

            <div class="container">



                <!-- Page-Title -->

                <div class="row">

                    <div class="col-sm-12">

                        <div class="page-title-box">

                            <h4 class="page-title">SALES TOOL LIBRARY</h4>
							

                        </div>
						<div class="col-md-12">
						<span style="text-align:center;color:red;"><?php echo $this->session->flashdata('message');?></span>
						</div>

                    </div>

                </div>

                <!-- end page title end breadcrumb -->





                <div class="row" style="padding-top:50px;">

                    <div class="col-xs-12">

                        <div class="card-box">



                            <div class="row">
							<form method="post" action="<?php echo page_url;?>Salestool/updatelibrary">

							<?php
							
							$restyu=$this->db->select('*')->from('salestoolsettings')->get();
							if($restyu->num_rows()>0)
							{
								foreach($restyu->result() as $restyu1)
								{
							?>
							<input type="hidden" name="existing[]" value="<?php echo $restyu1->id;?>">
									<div class="col-md-12">
											
										<div class="col-md-3">
										<div class="form-group">
										<label>Communication Type</label><br/>	
										<select name="existingtype<?php echo $restyu1->id;?>" class="form-control" required>
										<option value="">Communication Type</option>
										<?php
										$restye=$this->db->select('id,type')->from('salestoolcommunication')->where('status','1')->get();
										if($restye->num_rows()>0)
										{
											foreach($restye->result() as $restye1)
											{
										?>
										<option value="<?php echo $restye1->id;?>" <?php if($restye1->id==$restyu1->type){?> selected <?php } ?>><?php echo $restye1->type;?></option>
										<?php
											}
										}
										?>
									
										
										</select>
										</div>
										</div>


	<div class="col-md-2">
										<div class="form-group">
										<label>Days<span>&nbsp;(After Calling Date)</span></label><br/>	
										<input type="number" required min='1' name='exitingdays<?php echo $restyu1->id;?>' class="form-control" maxlength="2" value="<?php echo $restyu1->days;?>">
									
										
										</select>
										</div>
										</div>
										
										 <div class="col-md-2">
										<div class="form-group">
										<label>Communication Source</label><br/>	
											<input type="checkbox"  name="exitingintrow<?php echo $restyu1->id;?>"    value="w" <?php if($restyu1->whatsapp=='1'){?> checked <?php } ?>>&nbsp; Whatsapp&nbsp;
											<input type="checkbox"  name="existingintroe<?php echo $restyu1->id;?>"   value="e" <?php if($restyu1->email=='1'){?> checked <?php } ?>>&nbsp; Email&nbsp;
											<input type="checkbox"  name="existingintros<?php echo $restyu1->id;?>"  value="s" <?php if($restyu1->sms=='1'){?> checked <?php } ?>>&nbsp; SMS
										</div>
									</div>
									
									 <div class="col-md-1">
										<div class="form-group"><br/>
										<a href="javascript:;" onclick="deletelibrary(<?php echo $restyu1->id;?>)"><span class="btn btn-xs btn-success"><i class="fa fa-close"></i></span></a>
										</div>
									</div>
									
									
									</div>
										<?php
										}
										}
										?>

										<div class="col-md-12">
										<div class="col-md-3">
										<div class="form-group">
										<label></label><br/>	
										<input type="checkbox" name="addnew" class="addnew" onchange="getnewrow();" value="1"> Add new
										
										</div>
										</div>

							          <div class="col-md-12 addrow" style="display:none">
											
										<div class="col-md-3">
										<div class="form-group">
										<label>Communication Type</label><br/>	
										<select name="type[]" class="form-control">
										<option value="">Communication Type</option>
										<?php
										$restye=$this->db->select('id,type')->from('salestoolcommunication')->where('status','1')->get();
										if($restye->num_rows()>0)
										{
											foreach($restye->result() as $restye1)
											{
										?>
										<option value="<?php echo $restye1->id;?>"><?php echo $restye1->type;?></option>
										<?php
											}
										}
										?>
									
										
										</select>
										</div>
										</div>


	<div class="col-md-2">
										<div class="form-group">
										<label>Days<span>&nbsp;(After Calling Date)</span></label><br/>	
										<input type="number" min='1' name='days[]' class="form-control" maxlength="2">
									
										
										</select>
										</div>
										</div>
										
										 <div class="col-md-4">
										<div class="form-group">
										<label>Communication Source</label><br/>	
											<input type="checkbox"  name="introw0"   checked value="w">&nbsp; Whatsapp&nbsp;
											<input type="checkbox"  name="introe0"  checked value="e">&nbsp; Email&nbsp;
											<input type="checkbox"  name="intros0"  checked value="s">&nbsp; SMS
										</div>
									</div>
									
									<div class="col-md-1">
									<label>&nbsp;</label><br/>
									<a href="javascript:"><span class="btn btn-warning add_input"><i class="fa fa-plus"></i></span></a>
									
									</div>
									</div>
									
									<div id="dynamic"></div>
								
																
											

									
										<div class="col-md-12">

											<div class="form-group pull-right" style="padding-top:24px;">

												<label>&nbsp;</label>

												<input type="submit" class="btn btn-success" value="Update">

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
        <script src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>
<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

<script language="javascript" type="text/javascript">   


$(document).ready(function() {

var k=1;
 $('.add_input').click(function(){

 
 $('#dynamic').append('<div class="col-md-12" id="row'+k+'"><div class="col-md-3"><div class="form-group"><label>Communication Type</label><br/><select name="type[]" required class="form-control"><option value="">Communication Type</option><?php $restye=$this->db->select("id,type")->from("salestoolcommunication")->where("status","1")->get(); if($restye->num_rows()>0){ foreach($restye->result() as $restye1){ ?><option value="<?php echo $restye1->id;?>"><?php echo $restye1->type;?></option><?php } }?></select></div></div><div class="col-md-2"><div class="form-group"><label>Days<span>&nbsp;(After Calling Date)</span></label><br/><input type="number" min="1"  required name="days[]" class="form-control" maxlength="2"></select></div></div><div class="col-md-4"><div class="form-group"><label>Communication Source</label><br/><input type="checkbox"  checked name="introw'+k+'" value="w">&nbsp; Whatsapp&nbsp;<input type="checkbox"  checked name="introe'+k+'" value="e">&nbsp; Email&nbsp;<input type="checkbox" checked name="intros'+k+'" value="s">&nbsp; SMS</div></div><div class="col-md-1"><label>&nbsp;</label><br/><span class="btn btn-warning btn_remove2" id="'+k+'"><i class="fa fa-minus"></i></span></div></div>');
 
  k++;
 
 });
 
 
 $(document).on('click', '.btn_remove2', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id).remove();
	 
  k--;
 });
 
 
 
});

function getnewrow()
{
	if($('.addnew').is(":checked"))
	{
	$(".addrow").css('display','');
	$(".addrow :input").attr('required',true);
	
	$(".addrow :checkbox").attr('required',false);
	
	}else
	{
		$(".addrow").css('display','none');
		$(".addrow :input").attr('required',false);
	}
	
}

function deletelibrary(id)
{
	if(confirm('Do you really want to delete?'))
	{
		document.location="<?php echo page_url;?>Salestool/deletelib/"+id;
		return true;
	}else{
		
		return false;
	}
	
}
</script>

    </body>
</html>