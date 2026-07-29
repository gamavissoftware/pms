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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
        <link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
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
        <style type="text/css">
              table.pretty thead th {
                text-align: center;
                background:<?php echo $LOGO->colorcode;?>;
                color:#fff;
                font-size:12px;
            }
            table.pretty td {
                text-align: center;
                font-size:12px;
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
                            <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success" name="action_button" style="background-color: ;"><i class="fa fa-arrow-left" ></i>Back</button></a>
						 <div class="btn-group pull-right">
                             

						 <a href="<?php echo softwarepath;?>Lead Import Format.xlsx" download><span class="btn btn-success" style="background-color: ;">Download Excel Format</span></a>
						  </div>
                        <div class="row">
                            <div class="col-md-3"> <h4 class="page-title">Import Lead in Bulk </h4></div>
                            <div class="col-md-9"><br><br><h5 style="color:red;">Note: With this import feature you can only upload exhibition Data </h5></div>
                        </div>
                           

                        </div>
                    </div>
                </div>

               
                <!-- end page title end breadcrumb -->
                <div class="row">
                    <div class="col-xs-12">
                        <?php echo $this->session->flashdata('message');?>
                    </div>
                </div>

				<div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">
							<div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">
									<form method="post" action="<?php echo page_url;?>ExcelImport/uploaddataNew/" enctype="multipart/form-data">
										
                                          <div class="col-md-3">
                                            <div class="form-group">
                                                <label>LEAD SOURCE TYPE<span style="color:red;">*</span></label>
                                                <select class="form-control" name="leadsoruce" id="leadsoruce" >
                                                    <option value="">--Please select--</option>
                                                    <?php
                                                     $res=$this->db->select('source_id,lead_source')->from('lead_source')->where('source_id',8)->get();
                                                        if($res->num_rows()>0)
                                                        {
                                                            foreach($res->result() as $source)
                                                            {

                                                        ?>
                                                    <option value="<?php echo $source->source_id; ?>"><?php echo ucwords(strtolower($source->lead_source)); ?></option>
                                                <?php }} ?>
                                                </select>
                                            </div>
                                        </div>
                                       

                                        <div class="col-md-3" id="exhibitiondiv">
                                            <div class="form-group">
                                              <label>Select Exhibition <i class="fa fa-plus" data-toggle="modal" data-target="#con-close-modal"></i></label> 
                                                <select class="form-control" name="exhibitionname" id="exhibitionname">
                                                    <option value="">Select Exhibition</option>
                                                    <?php 
                                                        $q = $this->db->select('id, exhibition')->from('exhibition_info')->get();
                                                        foreach($q->result() as $exh){?>
                                                            <option value="<?php echo $exh->id;?>"><?php echo $exh->exhibition;?></option>

                                                       <?php  }
                                                    ?>
                                                   
                                                </select>
                                            </div>
                                        </div>

                                         <div class="col-md-3" style="display:none">
                                            <div class="form-group">
                                                <label>LEAD ASSIGN TO<span style="color:red;">*</span></label>
                                                <select class="form-control" name="assign" id="assign">
                                                  <?php
                                                    $res=$this->db->select('title, user_id,first_name,last_name')->from('system_users')->where('user_status',1)->where('department_id',9)->get();
                                                     if($res->num_rows()>0)
                                                        {
                                                            foreach($res->result() as $users){
                                                        ?>
                                                           <option value="<?php echo $users->user_id; ?>"><?php echo $users->title;?> <?php echo ucwords(strtolower($users->first_name)); ?> <?php echo ucwords(strtolower($users->last_name));?></option>
                                                       <?php }}?>
                                               
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Opportunity Type</label>
                                                <select class="form-control" name="opportunitytype" id="opportunitytype" required>
                                                    <option value="">Opportunity Type</option>
                                                    <option value="2">Export</option>
                                                    <option value="1">Domestic</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Attach File</label>
                                                     <input type="file" name="creditfile" class="form-control" required="">
                                                </div>
                                        </div>
                                                
                                                <div style="clear:both;height: 10px;"></div>
											
												
										<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" class="btn btn-success" value="Update" id="save" style="background-color: ;">
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

<div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

 <form id="loginForm" method="post" action="<?php echo page_url;?>ExcelImport/createexhibition/"  enctype="multipart/form-data">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">Add Exhibition Detail</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">

                                               <div class="co-md-12">
                                                   <div class="form-group">
                                                       <label>Exhibition Name<span style="color:red">*</span></label>
                                                       <input type="text" class="form-control" name="exhibitionname" id="exhibitionname" required>
                                                   </div>
                                               </div>
                                               <div class="col-md-6">
                                                   <div class="form-group">
                                                       <label>Country<span style="color:red">*</span></label>
                                                       <select class="form-control" name="country" id="country" required>
                                                           <?php $q = $this->db->select('country_id, country_name')->from('countries')->get();
                                                           foreach($q->result() as $row){
                                                           ?>
                                                           <option value="<?php echo $row->country_id;?>"><?php echo $row->country_name;?></option>
                                                       <?php }?>
                                                       </select>
                                                   </div>
                                               </div>
                                               <div class="col-md-6">
                                                   <div class="form-group">
                                                       <label>Exhibition Date<span style="color:red">*</span></label>
                                                       <input type="date" class="form-control" name="exhibitiondate" id="exhibitiondate" value="" required>
                                                   </div>
                                               </div>
                                            
                                            </div>

                                            

                                            

                                        </div>

                                        <div class="modal-footer">

                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                                            <input type="submit" id="depsave" class="btn btn-info" value="Submit"> 

                                        </div>

                                    </div>

                               

                                </form>

                            </div><!-- /.modal -->

 

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
		$(document).ready(function() {
			
			$('.select2').select2({ });
		});
		</script>
		 <script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
		 
<script language="javascript" type="text/javascript">   
$(document).ready(function() {
$("#save").click(function() {
var my_multi_select1 = $("#my_multi_select1").val();
if(my_multi_select1=='')
{
	$("#error_my_multi_select1").html('Required!');
}


if(category_name=='' || product_name=='')
{
	
	return false;
}

});
});
</script>
   </body>
</html>