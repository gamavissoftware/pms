<?php
$CI =& get_instance();
$CI->load->model('Salescrm_model', 'salescrm');
$DI =& get_instance();
$DI->load->model('Dashboard_model');
$resteyu=$this->db->select('a.company_name,a.id,b.first_name,b.last_name,b.user_id')->from('daily_visits a')->join('system_users b','a.added_by=b.user_id')->where('id',$this->uri->segment(3))->get();
if($resteyu->num_rows()>0)
{
	foreach($resteyu->result() as $roweoe);
	$company_name=$roweoe->company_name;
	$current_person=$roweoe->first_name." ".$roweoe->last_name;
	$user_id=$roweoe->user_id;
}else
{
	$company_name='';
	$current_person='';
	$user_id=0;
}

?>

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


				#mybutton {
				  position: fixed;
				  bottom: -4px;
				  right: 10px;
				}
				.select2-container {
				    width: 100% !important;
				}

			   .add_more {
		        margin-top: 33px;
		        }

		        .remove {
		        margin-top: 33px;
		        }

		        .delete_product {
		        margin-top: 33px;
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
	                   
	                    <h4 class="page-title">Change Visit Ownership for <?php echo $company_name;?></h4>
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

							
								

                            <form method="post" action="<?php echo page_url;?>Leads/update_visit_transfer/<?php echo $this->uri->segment(3);?>">
                            	<input type="hidden" name="from" id="from" value="<?php echo $user_id;?>">
								  

							<div class="row">
								<div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">Current Owner</label>
										<span style="color:red;">*</span>
										<span id="error_create_date" style="color:red;"></span>
										<input type="text" id="current" readonly name="current" class="form-control" value="<?php echo $current_person;?>">
                                    </div>
                                </div>

                                	<div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">Transfer To</label>
										<span style="color:red;">*</span>
										<span id="error_create_date" style="color:red;"></span>
										<select name="to" id="to" required class="form-control">
											<option value="">Select</option>
											<?php 
											$ret=$this->db->select('user_id,first_name,last_name')->from('system_users')->where('user_id !=',$user_id)->where('user_status',1)->where('department_id',6)->get();
											if($ret->num_rows()>0)
											{
												foreach($ret->result() as $row)
												{
											?>
											<option value="<?php echo $row->user_id;?>"><?php echo $row->first_name;?> <?php echo $row->last_name;?></option>
										<?php } } ?>
											
										</select>
                                    </div>
                                </div>

							

							

								
							</div>
									 
										
											

                                              

												 <div class="col-md-3">
                                                   
                                                </div>
												
									
                                                <div class="col-md-3">
                                                  
                                                </div>

                                                <div class="col-md-3">
												
												</div>
												
												 
												<div class="col-md-3">
													
												</div>
												<div class="col-md-3">
													
                                                </div>
												
											
												<div class="col-md-3">
													
												</div>

												<div class="col-md-3">
													
												</div>

												<div class="col-md-3">
                                                   
                                                </div>
									   
									   			<div class="col-md-3">
                                                    
                                                </div>

                                                <div class="col-md-3">
                                                  
                                                </div>

                                                <div class="col-md-3">
                                                   
                                                </div>

												<div class="col-md-3">
												
												</div>
												<div class="col-md-3">
														

														</div>
											
												
												
												<div class="col-md-4">
												<!-- <div class="form-group">
														 <label for="field-2" class="control-label">Message </label>
														 <Span id="error_spacification" style="color:red;"></span>
														 <textarea class="form-control" name="message1" id="message1"  style="overflow:hidden" placeholder="Message"><?php echo $row->message; ?></textarea>
														 
													</div> -->
													
												</div>
												
												<div class="col-md-5">
												
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

// var assign_team_member = $("#assign_team_member").val();
// if(assign_team_member=='')
// {
// 	$("#error_assign_team_member").html('Required!');
// } else {
// 	$("#error_assign_team_member").html('');
// }


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
		if(confirm('Are you sure you want to delete this product?')) {
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
	}

    function check_business() {
        var patient_type = $("#patient_type").val();
        $("#show_other_business").css('display', 'none');
        $("#other_business").removeClass('mand');

        if(patient_type == 7) {
            $("#show_other_business").css('display', '');
            $("#other_business").addClass('mand');
        }
    }

	    var j=1;

        $('.add_more').click(function(){
            getProductsOfCompany(j, 1);
            $('.productss').append('<div class="row fieldGroups"><div class="col-md-12"><div class="col-md-5"> <div class="form-group"> <label for="field-1" class="control-label">Competitor Product</label> <span style="color:red;">*</span> <input type="text" name="competitor_product[]" class="form-control"> </div></div><div class="col-md-5"> <div class="form-group"> <label for="field-1" class="control-label">Products</label> <span style="color:red;">*</span> <span id="error_product" style="color:red;"></span> <select class="form-control product_name'+j+' product_refresh" name="products[]"> </select> </div></div><div class="col-md-2"><a href="javascript:void(0)" class="btn btn-danger btn-xs remove"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span></a> </div></div></div>');
            j++;
        });

        $(document).on('click', '.remove', function(){
            $(this).parents(".fieldGroups").remove();
        });

    function getProductsOfCompany(j, i) {
        var company_location = $("#company_location").val();

                 $.ajax({
                    type:"post",
                    url:"<?php echo page_url;?>Leads/get_products",
                    data:{company_location:company_location},

                    success:function(data) {
                    	if(i == 1) {
                        	$(".product_name"+j).html(data);
                    	} else {
                    		$(".product_refresh").html(data);
                    	}
                    }
                });
    }

	// function getProductsOfCompany() {
	// 	     var url = "<?php echo page_url;?>Leads/get_products";

 //             $('.select3').select2({ 
 //                placeholder: 'TYPE TO SELECT',
 //                minmumInputLength:4,
 //                allowClear: true,
 //                multiple: true,
 //                    ajax: {
 //                        url: url,
 //                        dataType: 'json',
 //                        delay: 250,

 //                            data: function (params) {

 //                                return {
 //                                    searchTerm: params.term,
 //                                    company_location: $("#company_location").val()
 //                                    };

 //                                },processResults: function (data) {

 //                                    return {
 //                                        results: data
 //                                        };

 //                                    },

 //                                 cache: true

 //                                }

 //                            });
	// }
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
                minmumInputLength:4,
                allowClear: true,
                multiple: true,
                    ajax: {
                        url: url,
                        dataType: 'json',
                        delay: 250,

                            data: function (params) {

                                return {
                                    searchTerm: params.term,
                                    company_location: $("#company_location").val()
                                    };

                                },processResults: function (data) {

                                    return {
                                        results: data
                                        };

                                    },

                                 cache: true

                                }

                            });

                // $('.select3').select2({ 
                //     placeholder: 'TYPE TO SELECT',
                //     minmumInputLength:3,
                //     allowClear: true,
                //     multiple: true,

                //     ajax: {
                //       url: url,
                //       dataType: 'json',
                //       delay: 250,

                //       processResults: function (data) {
                //         return {
                //           results: data
                //         };
                //       },
                //       cache: true
                //     }

                // });
        });
        </script>
    </body>
</html>