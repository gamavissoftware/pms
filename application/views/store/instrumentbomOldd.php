<?php

$id=$this->uri->segment(3);
if($id<>'')
{
	$this->db->select('a.*,b.id as bomid')->from('machine_parts_with_picture a')->join('machine_bom b','a.id=b.partid')->where('b.mid',$id);
		$query = $this->db->get();
		$res = $query->result();
		
			
			
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

        <title>Prestogroup</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
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
		<link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

<style>

#customers {
  font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

#customers td, #customers th {
  border: 1px solid #ddd;
  padding: 8px;
}

#customers tr:nth-child(even){background-color: #f2f2f2;}

#customers tr:hover {background-color: #ddd;}

#customers th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #4CAF50;
  color: white;
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
                           
                            <h4 class="page-title">Add/Remove Machine BOM</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

 
 
				<div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">

							<div class="row">
							<div class="col-sm-6 col-xs-6 col-md-6">
<h4 class="text-center">Existing BOM</h4>
							<table id="customers">
 


 <tr>
    <th>PART</th>
    <th>SPECIFICATION</th>
    <th>IMAGE</th>
	 <th>REMOVE</th>
  </tr>
  
  <?php
if($query->num_rows()>0)
{

	foreach($res as $editdata)
	{
?>
</tr>

<td><?php echo $editdata->part;?></td>
<td><?php echo $editdata->specification;?></td>
<td><img src="<?php echo product_items;?><?php echo $editdata->picture;?>" style="width:100px"></td>
<td><a href="javascript:;" onclick="deletepart(<?php echo $editdata->bomid;?>);"><i class="fa fa-close"></i><a/></td>
</tr>


  <?php
	}
}
?>
  
 
</table>
</div>

<form method="post" action="<?php echo page_url;?>Store/update_bom/<?php echo $id;?>"  enctype="multipart/form-data">
<div class="col-sm-6 col-xs-6 col-md-6">
<h4 class="text-center">Add New Item To The BOM</h4>
	<select class="form-control select3" style="text-transform: uppercase;" name="parts[]" id="parts" required multiple>
	<option value="">--SELECT PARTS--</option>
	</select>

</div>


</div>
								
							
											<div class="row">
											<div class="col-md-12">
											
											<div class="col-md-9"></div>
											<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
											<label>&nbsp;</label>
											<input type="submit" class="btn btn-success" value="Save">
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
<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript">
function deletesupplier(id)
{
	var recid="<?php echo $this->uri->segment(3);?>";
	if(confirm('Do you really want to delete?'))
	{
		document.location="<?php echo page_url;?>Store/deletesupplier/"+id+"/"+recid;

		
	}else{
		return false;
	}
	
}
 </script>
 
 
 <script language="javascript" type="text/javascript">   

$(document).ready(function() {
 var purl="<?php echo page_url;?>Store/getallparts/<?php echo $this->uri->segment(3);?>";
 $('.select3').select2({

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


function deletepart(id)
{
	if(confirm('Do you really want to delete?'))
	{
		document.location="<?php echo page_url;?>Store/deltebommaterial/"+id+"/<?php echo $this->uri->segment(3);?>";
		
	}else{
		return false;
	}
	
}
</script>
<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>

    </body>
</html>