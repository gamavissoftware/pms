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
	                   
	                    <h4 class="page-title text-center">Add New Customer</h4>
	                </div>
                </div>
            </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

        <div class="row">
            <div class="col-xs-12">
                <div class="card-box">

                    <form method="post" action="<?php echo page_url;?>Customer/feednewcustomerdata">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12 col-md-12">


                              <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                 <label for="field-2" class="control-label">Company Name</label>
                                 <span id="error_rack_location" style="color:red;">*</span>
                                 <input type="text" class="form-control" id="new_companyname" name="new_companyname"  value="">
                                  <!-- <img style="float:right;display:none;" id='loading' class="loading" width="100px" src="http://rpg.drivethrustuff.com/shared_images/ajax-loader.gif" />  -->
                            </div>
                        </div>

                         <div class="col-md-3">
                            <div class="form-group">
                                 <label for="field-2" class="control-label">Company Brand</label>
                                 <span id="error_rack_location" style="color:red;">*</span>
                                 <select class="form-control " id="new_company_brand" name="new_company_brand" style="width:100%">
                                    <option value=""></option>
                                    <?php 
                                    $rty=$this->db->select('id,name')->from('company_brand')->get();
                                    if($rty->num_rows()>0)
                                    {
                                        foreach($rty->result() as $rtyy)
                                        {
                                    ?>
                                    <option value="<?php echo $rtyy->id;?>"><?php echo $rtyy->name;?></option>
                                    <?php
                                    } 
                                    } 
                                    ?>
                                 </select>
                            </div>
                        </div>

                         <div class="col-md-3" id="gstdiv">
                             

                            <div class="form-group">
                                <label>Country<span id="gstrequiredsymbol" style="color:red;">*</span></label>
                                <select class="form-control" name="country" id="country" onchange="getcountrytax();" required>
                                    <option value="">Select</option>
                                    <?php 
                                        $q = $this->db->select('country_name, country_id')->from('countries')->where('country_status',1)->get();
                                        foreach($q->result() as $row){
                                    ?>
                                    <option value="<?php echo $row->country_id;?>"><?php echo $row->country_name;?></option>
                                <?php }?>
                                </select>
                                <script type="text/javascript">
                                    function getcountrytax(){
                                        var countryid = $("#country").val();
                                        if(countryid!==''){
                                                $.ajax({
                                                type:"post",
                                                url:"<?php echo page_url;?>Leads/getcountrytaxinfo",
                                                data:"countryid="+countryid,
                                                success:function(data){
                                                    if(data){
                                                         $("#showhidetaxtype").css('display','block');
                                                        $("#taxt").html(data);
                                                    }else{
                                                        $("#showhidetaxtype").css('display','none');
                                                    }
                                                    }
                                                });
                                        }
                                    }

                                </script>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" id="showhidetaxtype" style="display:none">
                                <label id="taxt"><span id="gstrequiredsymbol"></span></label>
                                <input type="text" name="gst" id="gst" class="form-control" autocomplete="off">  
                                    <!-- <span style="color:red;"><?php echo form_error('gst');?></span> -->
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                 <label for="field-2" class="control-label">Email (add multiple email seperated by comma(,))</label>
                                 <span id="error_rack_location" style="color:red;">*</span>
                                <input type="email" class="form-control"  id="" name="new_email"  value="">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Contact Person<span style="color:red">*</span></label>
                                <input type="text" class="form-control" name="contactpersonname" id="contactpersonname" value="" required>
                            </div>
                        </div>
                         <div class="col-md-4">
                            <div class="form-group">
                                <label>Contact No<span style="color:red">*</span></label>
                                <input type="text" class="form-control" name="personcontactno" id="personcontactno" value="" required>
                            </div>
                        </div>
                         <div class="col-md-4">
                            <div class="form-group">
                                <label>Alternate Contact No</label>
                                <input type="text" class="form-control" name="acontactno" id="acontactno" value="">
                            </div>
                        </div>
                    </div>

                    <div class="row"> 

                          <div class="col-md-12">
                            <div class="form-group">
                                 <label for="field-2" class="control-label"> Address</label>
                                 <span id="error_rack_location" style="color:red;">*</span>
                                <textarea id="" name="new_address" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="modal-footer">
                    
                    <input type="submit" id="save" onclick="add_customer();" class="btn btn-info" value="Submit"> 
                </div>
                    </div>
								


                                   

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
        <script>
$( document ).ready(function() {
$('#new_company_brand').select2({tags:true });
$('#country').select2();

});

</script>
<script type="text/javascript">
    function add_customer()
    {
    var new_companyname=$("#new_companyname").val();
    var new_company_brand=$("#new_company_brand").val();
    var new_email=$("#new_email").val();
    var new_address=$("#new_address").val();
    var contactpersonname11=$("#contactpersonname").val();
    var personcontactno11 =$("#personcontactno").val();
    var acontactno11 =$("#acontactno").val();
    //alert(contactpersonname11);

    if(new_companyname!='' && new_company_brand!='' && new_email!='' && new_address!='' && personcontactno11!='' && contactpersonname11!='')
    {

       return true;

    }else
    {
        alert('All Fields with (*) are mandatory'); 
        return false;
    }
    }

</script>


    </body>
</html>