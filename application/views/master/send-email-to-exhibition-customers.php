<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php echo sitetitle; ?> Send Intro Email to Customer</title>
        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 
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
    </head>
    <body>
        <!-- Navigation Bar-->
        <header id="topnav">
            <?php $this->load->view('common/nav-menu');?>
                
            </header>
             <?php $this->load->view('common/info-section.php');?> 
             <div class="wrapper">
                <div class="container">
                 <!-- Page-Title -->

                <div class="row" style="margin-top:20px;">

                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">

                        <div class="page-title-box">
                            <h4 class="page-title text-center">Email Panel to Send Intro Email to Customer</h4>

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
                                        $q = $this->db->select('id, email')->from('customer_detail')->where('id',$this->uri->segment(4))->get();
                                        foreach($q->result() as $rosss);
                                     
                                    ?>

                                    <form method="post" id="loginForm" action="<?php echo page_url;?>Dispatch/sendexhibitionintroemail/<?php echo $this->uri->segment(4);?>"  enctype="multipart/form-data">
										<div class="col-md-3">
                                            <div class="form-group">
                                                <label for="field-1" class="control-label">Select Exhibition</label>
                                                <span id="error_Exhibition" style="color:red;">*</span>
                                                    <select class="form-control" id="Exhibition" onchange="getexhibitionemaildata(); getexhibitionemailbodydata();" name="Exhibition" required>
                                                       
                                                            <?php 
                                                                $q = $this->db->select('id, exhibition')->from('exhibition_info')->order_by('exhibition','asc')->get();
                                                                foreach($q->result() as $rowss){?>    
                                                            <option value="<?php echo $rowss->id;?>"><?php echo  ucwords(strtolower($rowss->exhibition));?></option>
                                                            <?php }?>
                                                            <option value="3" >Common Intro Email</option>
                                                    </select>
                                            </div>
                                        </div>
                                        <script type="text/javascript">
                                            function getexhibitionemaildata(){

                                                var Exhibition = $("#Exhibition").val();
                                                    $.ajax({
                                                        type:"post",
                                                        url:"<?php echo page_url;?>Master/User_management/fetchselectedemailbodysubject",
                                                        data:"Exhibition="+Exhibition,
                                                        success:function(data){

                                                            $("#subject").val(data);
                                                        }

                                                    });
                                                
                                            }

                                            function getexhibitionemailbodydata() {
                                            var Exhibition = $("#Exhibition").val();
                                            $.ajax({
                                            type: "post",
                                            url: "<?php echo page_url; ?>Master/User_management/fetchselectedemailbody",
                                            data: "Exhibition=" + Exhibition,
                                            success: function(data) {
                                            CKEDITOR.instances['email_template'].setData(data);
                                            }
                                            });
                                            }


                                        </script>
                                       
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Customer Email ID</label>
                                                <input type="text" class="form-control" name="customeremailid" id="customeremailid" value="<?php echo $rosss->email;?>" readonly>
                                            </div>
                                        </div>


    									<div class="col-md-6">
    											 <label for="field-2" class="control-label">Subject</label>

    											 <span id="error_reference_title" style="color:red;">*</span>

    											 <input type="text" class="form-control" name="subject" id="subject"  value="" required>

    									</div>

										<div class="col-md-12">
                                            <div class="form-group">
    											<label for="field-2" class="control-label">Email Template</label>
    											<span id="error_email_template" style="color:red;">*</span>

                                              
    											<textarea class="form-control" name="email_template" id="email_template" required></textarea>
												<script>CKEDITOR.replace( 'email_template' );</script>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-9" ><h5 style="color:red; font-weight: bold"></h5></div>

										<div class="col-md-3">

											<div class="form-group pull-right" style="padding-top:24px">

												<label>&nbsp;</label>

												<input type="submit" id="emailupdate" class="btn btn-success" value="Trigger Email">

											</div>

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

$('#example').dataTable({

"bProcessing": true,

"pagination":true,

"sAjaxSource": "<?php echo page_url;?>Master/Business_location/business_loc_listing",

"aoColumns": [

				{ mData: 'sr_no' } ,

				{ mData: 'country_name' },

				{ mData: 'state_name' },

				{ mData: 'city_name' },

				{ mData: 'company_name' },

				{ mData: 'address' },

				{ mData: 'contact_number' },

				{ mData: 'status' },

				{ mData: 'edit' }

				

		]

});   

});



</script>

		

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

		<script>

$(document).ready(function(){

	   $("#emailupdate").attr('disabled',false);

	   $("#emailupdate").val('Update');

  $("#loginForm").on("submit", function(){

   // $("#pageloader").fadeIn();

   $("#emailupdate").attr('disabled',true);

     $("#emailupdate").val('Please Wait...');

  });//submit

});//document ready

</script>

<script language="javascript" type="text/javascript">   

jQuery.noConflict();

$(document).ready(function() {

$("#emailupdate").click(function() {



var Exhibition = $("#Exhibition").val();

if(Exhibition=='')

{

	

	$("#error_Exhibition").html('Required!');

}








if(Exhibition=='')

{

	

	return false;

}



});

});

</script>

<script type="text/javascript">
    function appenddatatoeditor(tag,id) {
      var edi='email_template'+id;
      CKEDITOR.instances[edi].insertText(tag);
    }
</script>

    </body>

</html>