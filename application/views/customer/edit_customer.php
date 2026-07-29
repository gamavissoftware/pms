<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright;?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle;?></title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
        <style>

              .loading
        {
        position: absolute;
        left:0px;
        top: -1px;
        }
            table.pretty thead th {
                text-align: center;
                background: <?php echo $LOGO->colorcode;?>;
                color:#fff;
				font-size:12px;
            }
			table.pretty td {
                text-align: center;
                font-size:12px;
            }
			.feedback {
				  background-color : <?php echo $LOGO->colorcode;?>;
				  color: white;
				  padding: 10px 20px;
				  border-radius: 4px;
				  border-color: #46b8da;
				}

				#mybutton {
				  position: fixed;
				  bottom: -4px;
				  right: 10px;
				}
				.select2-container {

				    width: 100% !important;

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
	                    <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success" name="action_button"><i class="fa fa-arrow-left"></i>Back</button></a>
					 <div class="btn-group pull-right"></div>
	                   
	                    <h4 class="page-title">Edit Customer</h4>
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

								<?php
								$id = $this->uri->segment(3);
								$this->db->select('*')
										 ->from('customer_detail')
										 ->where('id',$id);
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $editcustomer)
									$status = $editcustomer->status;
                                $gst_verified=$editcustomer->gst_verified;
									
								?>
								

  <form method="post" action="<?php echo page_url;?>Customer/update_customer_information/<?php echo $editcustomer->id;?>">
                                   
                                    <input type="hidden" name="lasturl" value="<?php echo $this->uri->segment(4);?>">
                                    <input type="hidden" name="gst_verified" id="gst_verified" value="<?php echo $gst_verified;?>">

                                     <div class="row">
                                               
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Customer Name</label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                         <input type="text" class="form-control" name="companyname" id="companyname"  value="<?php echo $editcustomer->company_name ?>" required>
                                                          <img style="float:right;display:none;" id='loading' class="loading" width="100px" src="http://rpg.drivethrustuff.com/shared_images/ajax-loader.gif" /> 
                                                    </div>
                                                </div>

                                                  <div class="col-md-3">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Customer Alias</label>
                                                         <span id="error_rack_location" style="color:red;"></span>
                                                         <input type="text" class="form-control" name="alias" id="alias"  value="<?php echo $editcustomer->customer_alias ?>">
                                                        
                                                    </div>
                                                </div>


                                               

                                          
                                            </div>

                                            <div class="row">                                           

                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                <label for="field-2" class="control-label">Title <span style="color:red">*</span></label>
                                                <span id="error_state" style="color:red;"></span>
                                                <select class="form-control" name="title" id="title" required>
                                                <option value="">Select</option>
                                                <option value="Mr." <?php if($editcustomer->title=='Mr.'){ echo "selected";} ?>>Mr.</option>
                                                <option value="Mrs." <?php if($editcustomer->title=='Mrs.'){ echo "selected";} ?>>Mrs.</option>
                                                <option value="Miss." <?php if($editcustomer->title=='Miss.'){ echo "selected";} ?>>Miss.</option>
                                                </select>
                                                </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Contact Person</label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                        <input type="text" class="form-control" name="contact_person" id="contact_person"  value="<?php echo $editcustomer->gst ?>" required>
                                                    </div>
                                                </div>
                                                  <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Mobile</label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                        <input type="number" class="form-control" name="mobile" id="mobile"  value="<?php echo $editcustomer->contact_no ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Alternate Mobile</label>
                                                         <span id="error_rack_location" style="color:red;"></span>
                                                        <input type="number" class="form-control" name="alt_contact" id="alt_contact"  value="<?php echo $editcustomer->alt_contact ?>" >
                                                    </div>
                                                </div>


                                                   <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Email</label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                        <input type="email" class="form-control" name="email" id="email"  value="<?php echo $editcustomer->email ?>" required>
                                                    </div>
                                                </div>
                                               
                     

                       

                     
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Billing Address</label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                        <textarea name="address" required class="form-control"><?php echo $editcustomer->address; ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                <div class="form-group">
                                                <label for="field-2" class="control-label"> Billing State <span style="color:red">*</span></label>
                                                <span id="error_state" style="color:red;"></span>
                                                <select class="form-control" name="state" id="state" required onchange="getcity();">
                                                <option value="">Select State</option>
                                                <?php 
                                                $q = $this->db->select('state_id, state_name')->from('states')->where('country_id','101')->get();
                                                foreach($q->result() as $row){
                                                ?>
                                                <option value="<?php echo $row->state_id;?>" <?php if($editcustomer->bill_state==$row->state_id){ echo "selected";} ?>><?php echo $row->state_name;?></option>
                                                <?php }?>
                                                </select>
                                                </div>
                                                </div>
                                                <div class="col-md-4">
                                                <div class="form-group">
                                                <label for="field-2" class="control-label"> Billing City <span style="color:red">*</span></label>
                                                <span id="error_state" style="color:red;"></span>
                                                <!-- <select class="form-control select2" name="cityname" id="cityname" required onchange="getcity();"> -->
                                                <!-- <option value="">Select City</option> -->
                                                
                                                <!-- </select> -->
                                                <input type="text" name="cityname" class="form-control " id="cityname" required  value="<?php echo $editcustomer->city; ?>" >
                                                </div>
                                                </div>

                                                <div class="col-md-4">
                            <div class="form-group">
                                 <label for="field-2" class="control-label">Billing Pincode</label>
                                 <span id="error_rack_location" style="color:red;">*</span>
                                 <input name="pincode" type="text" class="form-control allow_decimal" name="pincode" id="pincode"  value="<?php echo $editcustomer->pincode;?>" required  onblur="validate_pincode()">
                            </div>
                        </div>




                              <div class="col-md-2">
                            <div class="form-group">
                                 <label for="field-2" class="control-label">TDS Applicable?</label>
                                 <span id="error_rack_location" style="color:red;">*</span>
                                <select class="form-control" name="tds_appl" id="tds_appl" required onchange="tds_applicable();">
                                    <option value="0" <?php if($editcustomer->tds_appl==0){?> selected <?php } ?>>No</option>
                                    <option value="1" <?php if($editcustomer->tds_appl==1){?> selected <?php } ?>>Yes</option>
                                   
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2 tds_data" style="display:none;">
                            <div class="form-group">
                                 <label for="field-2" class="control-label">TDS %</label>
                                 <span id="error_rack_location" style="color:red;">*</span>
                                <input type="number" step="any" class="form-control allow_decimal" name="tds_per" id="tds_per" value="<?php echo $editcustomer->tds_per;?>">
                                   
                            </div>
                        </div>


                            

                                                 <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Status</label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                        <select class="form-control" name="status" id="status">
                                                            <option value="1" <?php if($status==1){ echo "selected";}?>>ACTIVE</option>
                                                            <option value="0" <?php if($status==0){ echo "selected";}?>>INACTIVE</option>
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                    
                                        <div class="col-md-12">
                                            <div class="form-group pull-right" style="padding-top:24px;">
                                                <label>&nbsp;</label>
                                                <input type="submit" id="saves_form" class="btn btn-success" value="Update">
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
         <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
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

      

        <!-- App js -->

        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

 

		<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

        <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
		
<script language="javascript" type="text/javascript">   

$(document).ready(function() {

 tds_applicable();
			var lead_source = $("#lead_source").val();
			var leadID = "<?php echo $id;?>";

			if(lead_source == 1 || lead_source == 2 || lead_source == 4 || lead_source == 7 || lead_source == 8 || lead_source == 11) {

				$('#assign_team').empty().append('<option value="2">Sales NBD</option>');
				getTeamMembers(2, leadID);

			} else if(lead_source == '') {
				$('#assign_team option[value=""]').attr('selected','selected');
			} else {
				$('#assign_team').empty().append('<option value="3">Sales CRR</option>');
				getTeamMembers(3, leadID);

			}
});
	
	$("#company_name").keypress(function(event) {
    var character = String.fromCharCode(event.keyCode);
    return isValidCompany(character);     
});

function isValidCompany(str) {
    return !/[~`!@#$%\^&*()+=\-\[\]\\';,/{}|\\":<>\?]/g.test(str);
}

$("#contact_person").keypress(function(event) {
    var character = String.fromCharCode(event.keyCode);
    return isValidPerson(character);     
});

function isValidPerson(str) {
    return !/[~`!@#$%\^&*()+=\-\[\]\\';,/{}|\\":<>\?]/g.test(str);
}

$("#cust_name").keypress(function(event) {
    var character = String.fromCharCode(event.keyCode);
    return isValidCustomer(character);     
});

function isValidCustomer(str) {
    return !/[~`!@#$%\^&*()+=\-\[\]\\';,/{}|\\":<>\?]/g.test(str);
}

$(function() {
  var regExp = /[0-9\.\,]/;
  $('#mobile').on('keydown keyup', function(e) {
    var value = String.fromCharCode(e.which) || e.key;
    console.log(e);
    // Only numbers, dots and commas
    if (!regExp.test(value)
      && e.which != 188 // ,
      && e.which != 190 // .
      && e.which != 8   // backspace
      && e.which != 46  // delete
      && (e.which < 37  // arrow keys
        || e.which > 40)) {
          e.preventDefault();
          return false;
    }
  });
});

$(function() {
  var regExp = /[0-9\.\,]/;
  $('#mobile_no').on('keydown keyup', function(e) {
    var value = String.fromCharCode(e.which) || e.key;
    console.log(e);
    // Only numbers, dots and commas
    if (!regExp.test(value)
      && e.which != 188 // ,
      && e.which != 190 // .
      && e.which != 8   // backspace
      && e.which != 46  // delete
      && (e.which < 37  // arrow keys
        || e.which > 40)) {
          e.preventDefault();
          return false;
    }
  });
});
</script>

<script type="text/javascript">
	
$("#saves_form").click(function() {
var create_date = $("#create_date").val();
if(create_date=='')
{
	$("#error_create_date").html('Required!');
} else {
	$("#error_create_date").html('');
}

var lead_source = $("#lead_source").val();
if(lead_source=='')
{
	$("#error_lead_source").html('Required!');
} else {
	$("#error_lead_source").html('');
}

var cust_name = $("#cust_name").val();
if(cust_name=='')
{
	$("#error_cust_name").html('Required!');
} else {
	$("#error_cust_name").html('');
}


var country = $("#country_name").val();
if(country=='')
{
	$("#error_country").html('Required!');
} else {
	$("#error_country").html('');
}


var email_id = $("#email_id").val();
if(email_id=='')
{
	$("#error_email_id").html('Required!');
} else {
	$("#error_email_id").html('');
}

var mobile_no = $("#mobile_no").val();
if(mobile_no=='')
{
	$("#error_mobile_no").html('Required!');
} else {
	$("#error_mobile_no").html('');
}

var assign_team = $("#assign_team").val();
if(assign_team=='')
{
	$("#error_assign_team").html('Required!');
} else {
	$("#error_assign_team").html('');
}

var assign_team_member = $("#assign_team_member").val();
if(assign_team_member=='')
{
	$("#error_assign_team_member").html('Required!');
} else {
	$("#error_assign_team_member").html('');
}


var status = $("#status").val();
if(status=='')
{	
	$("#error_status").html('Required!');
} else {
	$("#error_status").html('');
}

if(create_date==''  || lead_source==''|| country=='' || cust_name=='' || email_id == '' || mobile_no=='' || status=='' || assign_team == '' || assign_team_member == '')
{
	
	return false;
}

});
</script>

<script type="text/javascript">
		function getTeamMembers(assign_team, leadID) {
		// var assign_team = $("#assign_team").val();
			$.ajax({
				type:"post",
				url:"<?php echo page_url;?>Leads/getTeamMembers",
				data: {assign_team: assign_team, leadID: leadID},
				success:function(data){
					$("#assign_team_member").html(data);
				}
				});
		}

	function getTeamViaSource() {
		var lead_source = $("#lead_source").val();

			if(lead_source == 1 || lead_source == 2 || lead_source == 4 || lead_source == 7 || lead_source == 8 || lead_source == 11) {

				$('#assign_team').empty().append('<option value="2">Sales NBD</option>');
				getTeamMembers(2);

			} else {
				$('#assign_team').empty().append('<option value="3">Sales CRR</option>');
				getTeamMembers(3);

			}
	}

	function delete_product(product_id) {
		$.ajax({
				type:"post",
				url:"<?php echo page_url;?>Leads/delete_product",
				data: {product_id: product_id},
					success:function(data){
						if (data == 1) {
							$(".products"+product_id).remove();
						// $("#assign_team_member").html(data);
						}
					}
			});
	}
</script>

<script type="text/javascript">
	$( document ).ready(function() {
  		$('#create_date').datepicker({
		 	autoclose: true,
		 	todayHighlight: true,
		 	format: 'dd-mm-yyyy'
		 });
	});
</script>

<script type="text/javascript">
         $( document ).ready(function() {

            $('.select2').select2({ });
                 var url = "<?php echo page_url;?>Leads/get_products";

                $('.select3').select2({ 
                    placeholder: 'TYPE TO SELECT',
                    minmumInputLength:3,
                    allowClear: true,
                    multiple: true,

                    ajax: {
                      url: url,
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

          function tds_applicable()
                            {
                                $(".tds_data").css('display','none');
                                $("#tds_per").attr('required',false);

                                var tds=$("#tds_appl").val();
                              
                                if(tds!='')
                                {
                                if(tds==1)
                                {
                                    $(".tds_data").css('display','');
                                    $("#tds_per").attr('required',true);
                                }else if(tds==0)
                                {
                                     $(".tds_data").css('display','none');
                                    $("#tds_per").attr('required',false);
                                }
                                }         

                            }




        function check_gstnew(gst)
{
    var a=0;
     var statecode = gst.substring(0, 2);
            var pancarno = gst.substring(2, 12);
            var entityNumber = gst.substring(12, 13);
            var defaultZvalue =gst.substring(13, 14);
            var checksumdigit = gst.substring(14, 15);
            if (gst.length != 15) {
                alert('GST Number is invalid');
            
                var a=1;
                           }
            if (pancarno.length != 10) {
                alert('GST number is invalid ');
              
                var a=1;
                
            }
            if (defaultZvalue !== 'Z') {
                alert('GST Number is invalid Z not in Entered Gst Number');
                
                var a=1;
            }

            if ($.isNumeric(statecode)) {
                
            } else {
                alert('Please Enter Valid State Code');
               
                var a=1;
            }

            // if ($.isNumeric(checksumdigit)) {
               
            // } else {
            //     alert('GST number is invalid last character must be digit');
              
            //     var a=1;

            // }


           
            if(a==0)
            {
                check_gst(gst);
            }else
            {
                $("#company_gstn").val('');
            }

}
 function check_gst(gst){

    $("#loading").css('display','none');
    if(gst!='')
    {

    $.ajax({
                    type:"post",
                    url:"<?php echo page_url;?>Open_leads/getGSTData",
                    data:{gst:gst},
                    beforeSend: function() {
                    $("#loading").css('display','');
                
                    },
                    success:function(data) {

                        if(data!='NA')
                        {
                            $("#companyname").val(data);
                            $("#gst_verified").val(1);
                        }else
                        {
                            alert('Invalid GST No. Provided');
                            $("#companyname").val('');
                        }
                          $("#loading").css('display','none');
                      
                    }
                });
    }

}

        </script>
    </body>
</html>