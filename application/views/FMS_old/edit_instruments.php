<?php
$CI =& get_instance();
$CI->load->model('Store_model');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Edit Instruments</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
		<link href="<?php echo assets_url;?>plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

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
  left: 30%;
  margin-left: -10px;
  margin-top: -10px;
  position: absolute;
  top: 30%;
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
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="btn-group pull-right">
                              
                            </div>
                            <h4 class="page-title">EDIT INSTRUMENTS </h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->


                <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">

								<?php
								$id = $this->uri->segment(3);
								$type=$this->uri->segment(4);
								$nofincode=$this->uri->segment(5);
								$this->db->select('*')->from('presto_instruments')->where('id',$id);
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $row)
								
								
								if($row->alias<>'0')
								{
									$prevmach=$CI->Store_model->getmachineitemname($row->alias);
								}else
								{
									$prevmach='';
								}
								
								?>
								
                                   <form method="post" id="loginForm" action="<?php echo page_url;?>FMS/update_instruments/<?php echo $row->id;?>/<?php echo $type;?>/<?php echo $nofincode;?>" enctype="multipart/form-data">
								    <div id="pageloader">
									
   <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
</div>

<input type="hidden" name="oldimg" value="<?php echo $row->image;?>">
			
                                        <div class="col-md-4">
                                        <div class="form-group">
                                        <label for="field-2" class="control-label">TYPE</label>
                                        <span id="error_status" style="color:red;"></span>
                                        <select class="form-control" id="pptype" name="pptype" required>
                                        <?php
                                        if($row->type=='0')
                                        {
                                        ?>
                                        <option value="0" <?php if($row->type=='0'){?> selected <?php } ?>>PRODUCTION</option>
                                        <?php
                                        }
                                        ?>
                                        
                                        <?php
                                        if($row->type=='1')
                                        {
                                        ?>
                                        <option value="1" <?php if($row->type=='LARGE'){?> selected <?php } ?>>IMPORTED</option>
                                        <?php
                                        }
                                        ?>
                                        
                                          <?php
                                        if($row->type=='2')
                                        {
                                        ?>
                                        <option value="2" <?php if($row->type=='MEDIUM'){?> selected <?php } ?>>SERVICE</option>
                                        <?php
                                        } 
                                        ?>
                                        
                                        </select>
                                        </div>
                                        </div>
                                        
                                        <?php
                                        if($row->type=='2')
                                        {
                                        ?>
                                        	<div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">FINCODE</label>
														<span id="error_instrument_name" style="color:red;">*</span>
                                                        <input type="text" class="form-control" id="fincode" name="fincode" style="text-transform: uppercase;" placeholder="" required value="<?php echo $row->fincode;?>">
                                                    </div>
                                                </div>
                                        
                                        <?php
                                        }
                                        ?>
								
										<div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">INSTRUMENT NAME</label>
														<span id="error_instrument_name" style="color:red;">*</span>
                                                        <input type="text" class="form-control" id="instrument_name" name="instrument_name" style="text-transform: uppercase;" placeholder="" required value="<?php echo $row->instruments_name;?>">
                                                    </div>
                                                </div>
							


							<div class="col-md-4">
							<div class="form-group">
							<label for="field-1" class="control-label">INSTRUMENT ALIAS FROM IMS</label>
							<span id="error_instrument_name" style="color:red;">*</span>

							<select name="alias" class="form-control select3" id="alias"></select>
							<span style='color:red;font-weight:bold;'>Previous Selected -  <?php echo $prevmach;?></span>
							</div>
							</div>

                                                				
									   	 <div class="col-md-4">
                                                        <label for="field-1" class="control-label">MODEL NO.</label>
                                                    <div class="form-group">
														<span id="error_instrument_model" style="color:red;"></span>
                                                        <input type="text" class="form-control" id="instrument_model" style="text-transform: uppercase;" name="instrument_model" placeholder="" required value="<?php echo $row->model_number;?>">
                                                    </div>
                                                </div>
												
												
												 <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">FILE NO.</label>
														<span id="error_instrument_file" style="color:red;"></span>
                                                        <input type="text" class="form-control" id="instrument_file" style="text-transform: uppercase;" name="instrument_file" placeholder="" required value="<?php echo $row->file_number;?>">
                                                    </div>
                                                </div>
												
												 <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">STOCK</label>
														<span id="error_instrument_stock" style="color:red;"></span>
                                                        <input type="number" min="0" class="form-control" id="instrument_stock" style="text-transform: uppercase;" name="instrument_stock" placeholder="" required value="<?php echo $row->stock;?>">
                                                    </div>
                                                </div>
												
									   	<div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">MIN STOCK</label>
														<span id="error_instrument_minstock" style="color:red;"></span>
                                                        <input type="number" min="0" class="form-control" id="instrument_minstock" style="text-transform: uppercase;" name="instrument_minstock" placeholder="" required value="<?php echo $row->minstock;?>">
                                                    </div>
                                                </div>
											
												
									   <div class="col-md-4">
												<div class="form-group">
												<label for="field-2" class="control-label">FABRICATION REQUIRED</label><br/>
												<span id="error_status" style="color:red;"></span>
												<input type="checkbox" name="fabrication" id="fabrication" value="1" <?php if($row->fabrication=='1'){?> checked <?php } ?>>
												</div>
											</div>
											
											<div style="clear:both;height:10px"></div>
											
											
											 <div class="col-md-4">
												<div class="form-group">
												<label for="field-2" class="control-label">MACHINE VALUE</label>
												<span id="error_mvalue" style="color:red;"></span>
											
												<input type="text" class="form-control" name="mvalue" id="mvalue" value="<?php echo floatval($row->mvalue);?>" required>
												</div>
												
												<div class="form-group">
												<label for="field-2" class="control-label">INSTRUMENTS OF</label>
												<span id="error_instruments_of" style="color:red;"></span>
											
												<select class="form-control" name="instruments_of" id="instruments_of" required>
												<option value="">Select Option</option>
												<option value="1" <?php if($row->instruments_of=='1'){echo "selected";}?>>Presto Group</option>
												<option value="2" <?php if($row->instruments_of=='2'){echo "selected";}?>>Testronix</option>
												</select>
												</div>
											</div>
											
											
											 <div class="col-md-4">
												<div class="form-group">
												<label for="field-2" class="control-label">CALIBRATION COST</label>
												<span id="error_mvalue" style="color:red;"></span>
											
												<input type="text" class="form-control" name="ccost" id="ccost" value="<?php echo $row->calibrationcost;?>">
												</div>
												<div class="form-group">
												<label>STATUS</label>
												<select class="form-control" id="status" name="status">
													<option value="<?php echo $row->status;?>"><?php if($row->status=='1'){echo "Active";}else{echo "Inactive";}?></option>
													<option value="1" <?php if($row->status=='1'){echo "selected";}?>>ACTIVE</option>
													<option value="0" <?php if($row->status=='0'){echo "selected";}?>>INACTIVE</option>
												</select>
											</div>
											</div>
											

		   <div class="col-md-4">
												<div class="form-group">
												<label for="field-2" class="control-label">UNIT</label><br/>
												<span id="error_status" style="color:red;"></span>
												<select name="unit[]" class="select2" multiple required>
												
												<?php
												$prevdata=array();
												$this->db->select('unitid')->from('instrument_unit')->where('mid',$this->uri->segment('3'));
												$query1 = $this->db->get();
												if($query1->num_rows()>0)
												{
													foreach($query1->result() as $query11)
													{
														$prevdata[]=$query11->unitid;
													}
													
												}
												
												
												
												$this->db->select('*')->from('units');
												$query = $this->db->get();
												$res = $query->result();
												$i=1;
												
												if($query->num_rows()>0)
												{
												foreach($res as $row12)
												{
													if(in_array($row12->id,$prevdata))
													{
														$a="selected";
													}else{
														$a="";
													}
												?>
												<option value="<?php echo $row12->id;?>" <?php echo $a;?>><?php echo strtoupper($row12->shortname);?></option>
												
												<?php
												}
												}
												?>
												</select>
												</div>
											</div>

									
									
										 <div class="col-md-4">
												<div class="form-group">
												<label for="field-2" class="control-label">PLATE SIZE</label>
												<span id="error_status" style="color:red;"></span>
												<select class="form-control" id="psize" name="psize" required>
													<option value="<?php echo $row->psize;?>">--Select Status--</option>
													<option value="SMALL" <?php if($row->psize=='SMALL'){?> selected <?php } ?>>SMALL</option>
													<option value="LARGE" <?php if($row->psize=='LARGE'){?> selected <?php } ?>>LARGE</option>
													<option value="MEDIUM" <?php if($row->psize=='MEDIUM'){?> selected <?php } ?>>MEDIUM</option>
												</select>
												</div>
											</div>
											
											<div class="col-md-4">
											<div class="form-group">
												<label>INSTRUMENT IMAGE</label>
												<input type="file" name="picture" id="picture" class="form-control" value="">
												<img src="<?php echo instrumentimg;?><?php echo $row->image;?>" width="100px">
											</div>
											</div>
											
									
										<div class="col-md-3">
											
										</div>
										<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" id="save" class="btn btn-success" value="Update">
											</div>
										</div>
									
									</form>
                                   

                                </div>

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
		<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
		<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script>

		// Time Picker
            jQuery('#timepicker').timepicker({
                defaultTIme : false
            });
			jQuery('#timepicker4').timepicker({
                defaultTIme : false
            });
            jQuery('#timepicker2').timepicker({
                showMeridian : false
            });
            jQuery('#timepicker3').timepicker({
                minuteStep : 15
            });
			 jQuery('#datepicker').datepicker();
            jQuery('#datepicker-autoclose').datepicker({
                autoclose: true,
                todayHighlight: true
            });
			jQuery('#datepicker-autoclose1').datepicker({
                autoclose: true,
                todayHighlight: true
            });
		
		</script>
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<script language="javascript" type="text/javascript">   
$(document).ready(function() {
    $(".select2").select2({});
    		var purl="<?php echo page_url;?>FMS/getimsitems/";
      $(".select3").select2({


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
    
    
$("#save").click(function() {
var instrument_name = $("#instrument_name").val();
if(instrument_name=='')
{
	$("#error_instrument_name").html('Required!');
}
	
	
	
	var instrument_stock = $("#instrument_stock").val();
if(instrument_stock=='')
{
	$("#error_instrument_stock").html('Required!');
}
	
		var instrument_minstock = $("#instrument_minstock").val();
if(instrument_minstock=='')
{
	$("#error_instrument_minstock").html('Required!');
}
var instruments_of = $("#instruments_of").val();
if(instruments_of=='')
{
	$("#error_instruments_of").html('Required!');
}
	
var status = $("#status").val();
	
if(status=='')
{
	$("#error_status").html('Required!');
}

if(instrument_name=='' || status=='' || instrument_stock=='' || instrument_minstock=='' || instruments_of=='')
{
	
	return false;
}

});
});
</script>
</body>
<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready
</script>
</html>
