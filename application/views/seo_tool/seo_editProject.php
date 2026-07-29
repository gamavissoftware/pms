<?php //echo "<pre>";print_r($editRecord);exit;?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Prestogroup</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
         <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
         <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css">

<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>  
        <link rel="stylesheet" type="text/css" href="<?php echo assets_url;?>css/seo_steps.css">
   <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>  
   <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script> 
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
        <script src="<?php echo assets_url;?>js/seo_steps.js"></script>
		<style>
			.divheight{
			padding-top:100px;
			}

.vertical {
  border-left: 4px solid #A9A9A9;
  height: 150px;
}

.addMore {
margin-top: 22px;
}
.remove {
margin-top: 22px;
}

.addMore2 {
margin-top: 22px;
}
.remove2 {
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


    <div class="wrapper">
        <div class="container">
		  <div class="divheight hidden-xs"></div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="card-box">
                    
                <?php foreach($editRecord as $row);?>
                        <form id="loginForm" method="post" action="<?php echo page_url;?>seo_tool/seo_createProject/updateSeoTool" enctype="multipart/form-data">
						  <span style="color:red;"><?php echo $this->session->flashdata('message');?></span>
                            <div class="row">
                                <div class="row">
                                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                                        <div class="page-title-box">
                                            <h4 class="page-title text-center">CREATE A PROJECT</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">Company Name</label>
										<span id="error_username" style="color:red;">*</span>
                                        <input type="text" id="company_name" name="company_name" class="form-control" autocomplete="off" value="<?php echo $row->company_name;?>">
                                        <!-- <span style="color:red;"><?php echo form_error('company_name');?></span> -->
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">Contact Person</label>
										<span id="error_department" style="color:red;">*</span>
                                        <input type="text" id="contact_person" name="contact_person" class="form-control" autocomplete="off" value="<?php echo $row->contact_person;?>">
                                        <!-- <span style="color:red;"><?php echo form_error('contact_person');?></span> -->
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">Phone No.</label>
                                        <input type="text" id="phone_number" name="phone_number" class="form-control" autocomplete="off" value="<?php echo $row->phone_number;?>">
                                        <!-- <span style="color:red;"><?php echo form_error('phone_number');?></span> -->
                                    </div>
                                </div>
								<div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">Company Logo</label>
										<span id="error_designation" style="color:red;">*</span>
                                        <input type="file" id="logo" name="logo" class="form-control" autocomplete="off" value="<?php echo $row->company_logo;?>"><img src="<?php echo page_url;?>assets/images/seo_images/<?php echo $row->company_logo;?>" width="50px">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">Company Website Link</label>
                                                    
                                        <input type="text" id="website_link" name="website_link" class="form-control" autocomplete="off" value="<?php echo $row->website_link;?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">Admin Email ID</label>
                                        <span id="error_designation" style="color:red;">*</span>
                                        <input type="email" id="admin_email" name="admin_email" class="form-control" autocomplete="off" value="<?php echo $row->admin_email;?>">
                                        <span style="color:red;"><?php echo form_error('admin_email');?></span>
                                    </div>
                                </div>	
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">Reporting Person Email ID</label>
                                        <input type="email" id="reporting_email" name="reporting_email" class="form-control" autocomplete="off" value="<?php echo $row->reporting_email;?>">
                                    </div>
                                </div>	
                            </div>
                             <div class="row">
                                <div class="row">
                                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                                        <div class="page-title-box">                        
                                            <h4 class="page-title text-center">COMPANY PRODUCT/SERVICE DETAILS</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <?php foreach ($getRows as $comp) {?>
                                      <div id="removeRow">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="field-1" class="control-label">Competitors</label>
                                                <span id="error_username" style="color:red;">*</span>
                                                <input type="text" id="competitors" name="competitors[]" class="form-control" autocomplete="off" value="<?php echo $comp->competitors;?>" readonly>
                                                <!-- <span style="color:red;"><?php echo form_error('competitors');?></span> -->
                                            </div>
                                        </div>
                                        <div class="col-md-5 ">
                                            <div class="form-group">
                                                <label for="field-1" class="control-label">Websites</label>
                                                <span id="error_username" style="color:red;">*</span>
                                                <input type="text" id="websites" name="websites[]" class="form-control" autocomplete="off" value="<?php echo $comp->websites;?>" readonly>
                                                <!-- <span style="color:red;"><?php echo form_error('websites');?></span> -->
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <a href="javascript:void(0)" class="btn btn-danger remove" onclick="deleteRow(<?php echo $comp->id;?>)"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    </div>
                                    <div class="row" id="repeat">
                                       
                                        <div class="col-md-5">
                                            <div class="form-group fieldGroup">
                                                <label for="field-1" class="control-label">Competitors</label>
                                                <span id="error_username" style="color:red;">*</span>
                                                <input type="text" id="competitors" name="competitors[]" class="form-control" autocomplete="off" value="">
                                                <!-- <span style="color:red;"><?php echo form_error('competitors');?></span> -->
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group fieldGroup">
                                                <label for="field-1" class="control-label">Websites</label>
                                                <span id="error_username" style="color:red;">*</span>
                                                <input type="text" id="websites" name="websites[]" class="form-control" autocomplete="off" value="">
                                                <!-- <span style="color:red;"><?php echo form_error('websites');?></span> -->
                                            </div>
                                        </div>
                                   
                                        <div class="col-md-2">
                                            <a href="javascript:void(0)" class="btn btn-success addMore"><span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span></a>
                                        </div>
                                    </div>
                                </div>
                          
                                <div class="col-md-6">
                                    <div class="row">
                                         <h4 class="text-center">TARGET LOCATION</h4>
                                     </div>
                                     <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group fieldGroup">
                                                <label for="field-1" class="control-label">India</label>
                                                <span id="error_username" style="color:red;">*</span>
                                                <input type="radio" class="cities" name="city" autocomplete="off" value="1" onchange="getCities()">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group fieldGroup">
                                                <label for="field-1" class="control-label">Outside India</label>
                                                <span id="error_username" style="color:red;">*</span>
                                                <input type="radio" class="cities" name="city" autocomplete="off" value="2" onchange="getCities()">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group fieldGroup">
                                                <label for="field-1" class="control-label">Both</label>
                                                <span id="error_username" style="color:red;">*</span>
                                                <input type="radio" class="cities" name="city" autocomplete="off" value="3" onchange="getCities()">
                                            </div>
                                        </div>
                                     </div>     
                                        <?php foreach($getMatchedCities as $match) {?>
                                        <span id="removeSelectedCity"><?php echo $match->city_name;?><a href="javascript:void(0)" onclick="removeCity(<?php echo $match->id;?>)"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span></a></span>&nbsp;&nbsp;&nbsp;
                                    <?php } ?>
                                     <div class="row" id="hideSection">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <select class="form-control editSelect" name="cities[]" multiple="multiple"></select>
                                            </div>
                                        </div>
                                     </div>  
                                     <div class="row" id="showSection">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <select class="form-control editSelect" name="" style="width: 100%;"></select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <select class="form-control editSelect" name="" style="width: 100%;"></select>
                                            </div>
                                        </div>
                                     </div>
                                     <div class="row">
                                        <div class="form-group">
                                            <input type="hidden" name="project_id" value="<?php echo $this->uri->segment(4); ?>">
                                            <input type="hidden" name="old_image" value="<?php echo $row->company_logo;?>">
                                            <button class="btn btn-primary pull-right">Submit</button>
                                        </div>
                                     </div>                                    
                                </div>
                            </div>
                        </form>
                            <!-- </div> -->
                    
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

                <script type="text/javascript">
$( document ).ready(function() {
   $('#showSection').hide();
 var purl="<?php echo page_url;?>seo_tool/Seo_createProject/getCities"

$(".editSelect").select2({
        placeholder: 'TYPE TO SELECT',
        minmumInputLength:4,
        // width: '100%',
        allowClear: true,
        tags: true,
        multiple:true,
        maximumSelectionLength: 5,
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
              function getCities() {
                var radioValue = $('input[name="city"]:checked').val();
                // alert(radioValue);
                if (radioValue == '3') {
                    $('#hideSection').hide();
                    $('#showSection').show();
                } else {
                    $('#hideSection').show();
                    $('#showSection').hide();
                }
                
              }

              function deleteRow(id) {
                
                    $.ajax({
                    type:"post",
                    url:"<?php echo page_url;?>seo_tool/Seo_createProject/delDetails",
                    data:{id:id},
                    success:function(data){
                        if (data == 1) {
                            $("#removeRow").remove();
                        }
                    }
                    });
                
              }

               function removeCity(id) {
                
                    $.ajax({
                    type:"post",
                    url:"<?php echo page_url;?>seo_tool/Seo_createProject/delCity",
                    data:{id:id},
                    success:function(data){
                        if (data == 1) {
                            $("#removeSelectedCity").remove();
                        }
                    }
                    });
                
              }
            
                $('.addMore').click(function(){
                $('#repeat').append('<div class="row fieldGroup"> <div class="col-md-5"> <div class="form-group fieldGroup"> <label for="field-1" class="control-label">Competitors</label> <span id="error_username" style="color:red;">*</span> <input type="text" id="competitors" name="competitors[]" class="form-control" autocomplete="off"> </div></div><div class="col-md-5"> <div class="form-group fieldGroup"> <label for="field-1" class="control-label">Websites</label> <span id="error_username" style="color:red;">*</span> <input type="text" id="websites" name="websites[]" class="form-control" autocomplete="off"> </div></div><div class="col-md-2"> <a href="javascript:void(0)" class="btn btn-danger remove"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span></a> </div></div>');
                });
             
              $(document).on('click', '.remove', function(){
                    $(this).parents(".fieldGroup").remove();
                    });


        </script>
        <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
    </body>
</html>