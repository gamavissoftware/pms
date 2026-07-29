<?php

$id=$this->uri->segment(3);
// echo $id;exit;
if($id<>'')
{
	$this->db->select('a.part, a.specification, a.fincode, b.id, b.qty, b.mould, c.name as unit_name')
           ->from('machine_parts_with_picture a')
           ->join('semi_fg_bom_mapping b','a.id=b.semi_fg_bom_id')
           ->join('units c','c.id=b.unit')
           ->where('b.semi_fg_id',$id);
		$query = $this->db->get();
		$res = $query->result();
		

    $dt=$this->db->select('a.part,a.specification,a.fincode')->from('machine_parts_with_picture a')->where('a.id',$id)->get();
		if($dt->num_rows()>0)
    {
      foreach($dt->result() as $dtt);
      $mainpart=$dtt->part;
      $maincode=$dtt->fincode;
      $maincat=$dtt->specification;
    }else
    {
      $mainpart='';
      $maincode='';
      $maincat='';
    }
			
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

        <title><?php echo sitetitle;?> Internal Manufacturing BOM</title>

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
.select2-container--default .select2-selection--single
{
height:40px !important;	
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
                           
                            <h4 class="page-title">Internal BOM FOR <?php echo $mainpart;?> (<?php echo $maincode;?>)</h4>
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
							    <table id="customers" style="width:100%;">
                    <tr>
                      <th>SR NO.</th>
                      <th>CODE</th>
                      <th>PART</th>
                      <th>SPECIFICATION</th>
                      <th>QTY</th>
                      <th>UNIT</th>
                      <th>MOULD</th>
                      <th>REMOVE</th>
                    </tr>
  
                  <?php
                if($query->num_rows()>0)
                {

                $i=1;
                	foreach($res as $editdata)
                	{

                ?>
                </tr>
                  <td><?php echo $i;?></td>
                  <td><?php echo $editdata->fincode;?></td>
                  <td><?php echo $editdata->part;?></td>
                  <td><?php echo $editdata->specification;?></td>
                  <td><?php echo $editdata->qty;?></td>
                  <td><?php echo $editdata->unit_name;?></td>
                  <td><?php echo $editdata->mould;?></td>
                  <td><a href="javascript:;" onclick="deleteinternalmanbom(<?php echo $editdata->id;?>);"><i class="fa fa-close"></i><a/></td>
                </tr>


                  <?php
                $i++;	}
                } ?>
  
              </table>
          </div>

          <div class="col-md-1"></div>
            <div class="col-md-5">
                <h4 class="text-center">Add New Item To The BOM</h4>
                <form method="post" action="<?php echo page_url;?>Store/update_internal_man_bom/<?php echo $id;?>"  enctype="multipart/form-data">
                  <div class="row">
                    <div class="col-sm-4 col-xs-4 col-md-4">
                      <label>Code</label>
                    	<select class="form-control select3" style="text-transform: uppercase;" name="parts[]" id="parts" required>
                    	<option value="">--SELECT CODE--</option>
                    	</select>
                    </div>

                    <div class="col-sm-2 col-xs-2 col-md-2">
                      <label>Qty</label>
                      <input type="text" name="qty[]" value="" class="form-control" required>
                    </div>

                    <div class="col-sm-4 col-xs-4 col-md-4">
                      <label>Unit</label>
                      <select class="form-control select4" style="text-transform: uppercase;" name="unit[]" id="unit" required>
                      <option value="">--SELECT UNIT--</option>
                      </select>
                    </div>
                    <div class="col-sm-2 col-xs-2 col-md-2">
                      <label></label><br/><br/>
                      <button class="btn btn-warning btn-xs" id="add_morebutton1"><i class="fa fa-plus"></i></button>
                    </div>
                  </div>


            <div id="dynamicdiv"></div>

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
		document.location="<?php echo page_url;?>Store/delete_bom_item/"+id+"/"+recid;

		
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

var url = "<?php echo page_url;?>Store/getallunits";

    $('.select4').select2({ 
        placeholder: 'TYPE TO SELECT',
        minmumInputLength:1,
        allowClear: true,

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


function deleteinternalmanbom(id)
{
	if(confirm('Are you sure you want to delete?'))
	{
		document.location="<?php echo page_url;?>Store/deleteinternalmanbom/"+id+"/<?php echo $this->uri->segment(3);?>";
		
	}else{
		return false;
	}
	
}
</script>

<script type="text/javascript">
$(document).ready(function(){
	$(".select0").select2({});
var k=1;

 $('#add_morebutton1').click(function(){
	 
 $('#dynamicdiv').append('<div id="row'+k+'" class="row appendrows"><div class="col-sm-4 col-xs-4 col-md-4"><label>Code</label><select class="form-control select3 iisel itemselect'+k+'" onfocus="getdata('+k+')" style="text-transform: uppercase;" name="parts[]" id="parts" required><option value="">--SELECT PARTS--</option></select></div><div class="col-sm-2 col-xs-2 col-md-2"><label>Qty</label><input type="text" name="qty[]" value="" class="form-control"></div><div class="col-sm-4 col-xs-4 col-md-4"><label>Unit</label><select class="form-control select4 iisel itemselect4'+k+'" onfocus="getdata('+k+')" style="text-transform: uppercase;" name="unit[]" id="unit" required><option value="">--SELECT UNIT--</option></select></div><div class="col-sm-2 col-xs-2 col-md-2"><label></label><br/><br/><button class="btn btn-warning btn-xs btn_remove" id="'+k+'"><i class="fa fa-close"></i></button></div></div>');

 initializeSelect2('itemselect'+k);
 initializeSelect4('itemselect4'+k);

  k++;
  
 });
 
 
 
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id+'').remove();
 });
 
});


function initializeSelect2(selectElementObj) {
	var purl="<?php echo page_url;?>Store/getallparts/<?php echo $this->uri->segment(3);?>";
         $('.'+selectElementObj).select2({
			 
			 
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
         
      }

      function initializeSelect4(selectElementObj) {
  var purl="<?php echo page_url;?>Store/getallunits";
         $('.'+selectElementObj).select2({
       
       
          placeholder: 'TYPE TO SELECT',
          minmumInputLength:1,
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
         
      }

</script>
<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>

    </body>
</html>
