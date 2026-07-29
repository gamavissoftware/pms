<?php
$CI =& get_instance();
$CI->load->model('Store_model');

$UI =& get_instance();
$UI->load->model('Fms_model');
$type=$this->uri->segment(3);
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
				font-size:11px;
			}
			
			.backgroundcolorblack
			{
				background-color: #929292;
				color:#000;
				
			}
			.feedback {
  position: fixed;
  bottom: -4px;
  right: 10px;
  z-index:99999;
}

.numberCirclepurchase {
    border-radius: 50%;
    width: 45px;
    height: 45px;
     padding: 9px 0px 0px 0px;
    background: #fff;
    border: 2px solid #F2652F99;
    color: #666;
    text-align: center;
    font-size: 14px;
   
}

.numberCirclestore {
    border-radius: 50%;
    width: 45px;
    height: 45px;
     padding: 9px 0px 0px 0px;
    background: #fff;
    border: 2px solid #E6E31DCC;
    color: #666;
    text-align: center;
    font-size: 14px;
    margin-left:20px;
}
			
			.backgroundcolorred
			{
				background-color: #ff8686f7;
				color:#000;
				
			}
			
			
			.backgroundcoloryellow
			{
				background-color: #ffff827d;
				color:#000;
				
			}
			
			
			.backgroundcolorgreen
			{
				background-color: #95FF95;
				color:#000;
				
			}
			
			
			.backgroundcolorblue
			{
				background-color: #87cefa;
				color:#000;
				
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
            <div class="container-fluid">

                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-md-12">
                          
                            <h4 class="page-title text-center">IMS</h4>
                            
                    </div>
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                     <div class="col-md-6 card-box">
                                
                               <table class="table table-bordered">
                                <thead>
                                <tr>
                                <th colspan="10" style="text-align:center;">Legend- Color Indicator <span> <a href="<?php echo page_url;?>Store/machineparts"><button class="btn btn-xs pull-right btn-success waves-effect waves-light">Reset Filter</button></a></span></th>
                                
                                </tr>
                                
                                </thead>
                                <tbody>
                                    
                                    <tr>
                                <td style="width: 117px;text-align:center;" colspan="2">Stock 0%</td>
                                <td style="width: 117px;text-align:center;" colspan="2">Stock upto 33%</td>
                                <td  style="width: 117px;text-align:center;"  colspan="2">Stock 34% - 62%</td>
                                <td  style="width: 117px;text-align:center;"  colspan="2">Stock 63% - 100%</td>
                                <td  style="width: 117px;text-align:center;"  colspan="2">Stock>100%</td>
                                </tr>
                                
                                <tr>
                                <td style="background-color:#929292;"></td>
                                <td style="width: 117px;">Stock Mismatch, Inventory Physically Verify</td>
                                
                                
                                
                               
                                <td style="background-color:#FF8A8A;"></td>
                                <td  style="width: 117px;">Emergency Purchase</td>
                                
                               
                                <td style="background-color:#FFFFC2;"></td>
                                <td  style="width: 117px;">Have time to start planning</td>
                                
                                
                                <td style="background-color:#95FF95;"></td>
                                <td  style="width: 117px;">Enough Material in stock</td>
                                
                                
                                <td style="background-color:#87CEFA;"></td>
                                <td  style="width: 117px;">No Need to purchase</td>
                                
                                </tr>
                                
                                <tr>
                              
                                <td colspan="2" class="text-center"><a href="<?php echo page_url;?>Store/filteredims/1">Total Items - <?php echo $CI->Store_model->getcolorcountforims('BLACK');?></a></td>
                                
                                
                                
                               
                              
                                <td colspan="2" class="text-center"><a href="<?php echo page_url;?>Store/filteredims/2">Total Items - <?php echo $CI->Store_model->getcolorcountforims('RED');?></a></td>
                                
                               
                               
                                <td colspan="2" class="text-center"><a href="<?php echo page_url;?>Store/filteredims/3">Total Items - <?php echo $CI->Store_model->getcolorcountforims('YELLOW');?></a></td>
                                
                                
                                
                                <td colspan="2" class="text-center"><a href="<?php echo page_url;?>Store/filteredims/4">Total Items - <?php echo $CI->Store_model->getcolorcountforims('GREEN');?></a></td>
                                
                                
                                
                                <td colspan="2" class="text-center"><a href="<?php echo page_url;?>Store/filteredims/5">Total Items - <?php echo $CI->Store_model->getcolorcountforims('BLUE');?></a></td>
                                
                                </tr>
                                
                                 <tr>
                              
                                <td colspan="2" class="text-center"></td>
                                
                                
                                
                               
                              
                                <td colspan="2" class="text-center">PR Items - <?php echo $CI->Store_model->getprforims('RED');?></td>
                                
                               
                               
                                <td colspan="2" class="text-center">PR Items - <?php echo $CI->Store_model->getprforims('YELLOW');?></td>
                                
                                
                                
                                <td colspan="2" class="text-center"></td>
                                
                                
                                
                                <td colspan="2" class="text-center"></td>
                                
                                </tr>
                                
                                </tbody>
                                </table>
                         
                            </div>
                    <div class="col-md-4">
                        
                            <?php
                       
                        $povspr=$UI->Fms_model->povsprcount();
                        $povsdeli=$UI->Fms_model->povsdelivery();
                        ?>
                         <div class="card-box widget-user" style="min-height:248px;">
                           <div class="text-center">
                                <h4>PURCHASE</h4><hr>
                                 <div class="row">
                                    <a href="<?php echo page_url;?>Reporting/pr_vs_po_report" target="_blank"> <div class="col-md-4 col-sm-4 col-xs-4">
                                        <div class="numberCirclepurchase"><?php echo $povspr;?></div>
                                        <p style="text-align:left;">PR VS PO</p>
                                    </div></a>
                                   
                                    <a href="<?php echo page_url;?>Reporting/po_vs_delivery" target="_blank"> <div class="col-md-4 col-sm-4 col-xs-4">
                                        <div class="numberCirclepurchase"><?php echo $povsdeli;?></div>
                                        <p title="total pending task" style="text-align:left;"> PO VS DELIVERY</p>
                                    </div></a>
                                    
                                       <?php
                                     $indvspr=$UI->Fms_model->indentvspo();
                                    ?>
                                    <a href="<?php echo page_url;?>Reporting/indent_vs_pr" target="_blank"><div class="col-md-4 col-xs-4 col-sm-4">
                                        <div class="numberCirclestore"><?php echo $indvspr;?></div>
                                        <p style="text-align:left">INDENT VS PR</p>
                                    </div></a>
                                   
                                </div>
                            </div>
                        </div> 
                    
                        
                        
                    </div>
                            
                           
                           
                           
                      
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

<?php 
				$user_id =$this->session->userdata['logged_in']['user_id'];	
				
				$query = $this->db->select('qty, vendor, price')->from('ims_header_permission')->where('user_id',$user_id)->get();
					foreach($query->result() as $row);
				
				?>

                 <div class="row">
                    <div class="col-sm-12">
                        <?php
                        if($type=='1')
                        {
                            $filtername='Stock Mismatch, Inventory Physically Verify';
                        }else if($type=='2')
                        {
                             $filtername='Emergency Purchase';
                        }else if($type=='3')
                        {
                             $filtername='Have time to start planning';
                        }else if($type=='4')
                        {
                             $filtername='Enough Material in stock';
                        }else
                        {
                             $filtername='No Need to purchase';
                        }
                        
                        ?>
                        
                         <form method="post" id="frm" action="<?php echo page_url;?>Store/generateimsautopr/<?php echo $type;?>">
                        <div class="card-box table-responsive">
                            
                           <h5 class="text-center">Showing Filtered IMS For Items Marked as <?php echo '"'.$filtername.'"';?> </h5>
                            <div class="col-md-12 text-center">
                           
                            <a href="javascript:;" class="btn btn-warning" onclick="checkformsubmit();">Raise PR</a>
                            </div>
                           
                            <table id="example" class="table manglesh table-bordered" style="font-size: 14px;">
                                <thead>
                                <tr>
                                    <?php
                                    if($type=='2' || $type=='3')
                                    {
                                    ?>
                                    <th>PR <input type="checkbox" name="selall" id="selall" onchange="selectallcheck();"></th>
                                    <?php
                                    }
                                    ?>
                                    <th>SR NO</th>
									<th>PICTURE</th>
									<th>PART NAME</th>
									 <th>CATEGORY</th>
									 <th>SPECIFICATION</th>
									 <th>MAKE</th>
									 <th>SIZE IN MM</th>
									 <th>MATERIAL </th>
									 <th>RAW/BOP</th>
									 <th>FIN CODE</th>
									 <th>UNIT</th>
									 <th>STOCK</th>
									 <th>MIN STOCK</th>
									 <th>MIN STOCK STATUS</th>
									 <?php if($row->qty=='1'){?>
									 <th>QTY</th>
									 <?php }?>
									 
									 <th>RACK LOCATION</th>
									 <?php if($row->vendor=='1'){?>
									 <th style="width:400px">VENDOR/PRICE</th>
									 <?php }?>
									
									 <th>ACTION</th>
									
                                    
                                </tr>
                                </thead>
								<tbody></tbody>
                                
                            </table>
                            
                           
                        </div>
                         </form>
                    </div>
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
<?php
if($type=='2' || $type=='3')
{
?>       
"lengthMenu": [ [200, 300, -1], [200, 300, "All"] ],
<?php
}else
{
?>
"lengthMenu": [ [500, 1000, -1], [500, 1000, "All"] ],
<?php
}
?>
"stateSave": true,
"sAjaxSource": "<?php echo page_url;?>Store/filtered_machine_part_data_with_picture_list/<?php echo $this->uri->segment(3);?>",
"aoColumns": [
            <?php
            if($type=='2' || $type=='3')
            {
            ?>
            { mData: 'pr' } ,
            <?php
            }
            ?>
			{ mData: 'sr_no' } ,
				{ mData: 'image' },
				
				{ mData: 'machine_part' },
				{ mData: 'category' },
				{ mData: 'specification' },
				{ mData: 'makes' },
				{ mData: 'size_in_mm' },
				{ mData: 'material' },
				{ mData: 'raw_bop' },
				{ mData: 'fincode' },
				{ mData: 'unit' },
				{ mData: 'stock' },
				{ mData: 'minstock' },
				{ mData: 'minstockstatus' }
				 <?php if($row->qty=='1'){?>
				,{ mData: 'qty' }
				 <?php } ?>

				,{ mData: 'location' }
 <?php if($row->vendor=='1'){?>
				,{ mData: 'vendor' }
 <?php
 }
 ?>


				,{ mData: 'edit' }
				
				
				
		],"initComplete": function(settings, json) {
  
   getcolors()
  }
  
});   


 $('#example').on('draw.dt', function() {
    // do action here

    getcolors();
});  

  $('#example').on('search.dt', function() {
   
    getcolors();
});  

});

</script>
		
		
<script language="javascript" type="text/javascript">   

$(document).ready(function() {
$("#save").click(function() {
var machine_part = $("#machine_part").val();
if(machine_part=='')
{
	$("#error_machine_part").html('Required!');
}

var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}


if(machine_part=='' || status=='' )
{
	
	return false;
}

});
});


function getcolors()
{

 
$("#example tr").each(function(){
		
	var currentRow=$(this);
	
	    var col1_value=currentRow.find("td:eq(13)").text();
	   col1_value=col1_value.trim();
	   
	  
	if(col1_value=='BLACK')
		{
			currentRow.find("td:eq(2)").addClass("backgroundcolorblack");
		}else if(col1_value=='RED')
		{
			currentRow.find("td:eq(2)").addClass("backgroundcolorred")
		}else if(col1_value=='YELLOW')
		{
			currentRow.find("td:eq(2)").addClass("backgroundcoloryellow")
		}else if(col1_value=='GREEN')
		{
			currentRow.find("td:eq(2)").addClass("backgroundcolorgreen")
		}else if(col1_value=='BLUE')
		{
			currentRow.find("td:eq(2)").addClass("backgroundcolorblue")
		}			
	

	 
});
}


function selectallcheck()
{
   
   
   if($('#selall').is(":checked"))
{
 
    $(".storecheck").prop('checked', true);

}else
{
    $(".storecheck").prop('checked', false);
   
} 
    
    
}

function checkformsubmit()
{
  
    var checkboxes = $('input[class="storecheck"]').length;
    alert(checkboxes);
    if(checkboxes>0)
    {
       $("#frm").submit();
       return true;
    }else
    {
         alert('Select at least once product for PR');
        return false;
        
    }
    
}
</script>
    </body>
</html>