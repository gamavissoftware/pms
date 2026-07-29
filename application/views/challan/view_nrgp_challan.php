<?php 
$query = $this->db->select('a.*,b.name, b.address, b.phone, c.first_name, c.last_name')->from('nrgp_challan a')->join('vendors b','a.supplier_id=b.id','left')->join('system_users c','a.added_by=c.user_id','left')->where('a.id',$this->uri->segment(3))->get();
foreach($query->result() as $row);
if($row->source=='1'){

		$cha=$this->db->select('a.*,b.part as itemname, b.fincode, b.specification')->from(' nrgp_challan_items a')->join('machine_parts_with_picture b','a.item_id=b.id')->where('a.nrgp_id',$this->uri->segment(3))->get();
		if($cha->num_rows()=='0')
					{
					    echo "Invalid Access"; exit;
					}
}else{
	
	$cha=$this->db->select('a.*,b.item_name as itemname')->from('nrgp_challan_items a')->join('house_keeping_items b','a.item_id=b.id')->where('a.nrgp_id',$this->uri->segment(3))->get();
	if($cha->num_rows()=='0')
		{
			echo "Invalid Access"; exit;
		}
}				
				
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?></title>

  <link href="<?php echo assets_url;?>css/invoice/style.css" rel="stylesheet" type="text/css" />
        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/style.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
		<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet"/>
        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
		<style type="text/css" media="print">
  @page {  size: A4;
   margin: 0.11mm 0.29mm 0mm 0.06mm; margin-bottom:0mm;}
   

input,
  textarea {
    border: none !important;
    box-shadow: none !important;
    outline: none !important;
  }

   .pageinside{
    padding: 0.5cm;
    border: 5px #405473 double;
    height: 276mm;
}

  .grnhead
{
    display:block;
}
td {font-size:12px;text-align:center;}
th {font-size:13px;text-align:center;color:#F3F3F3;}
.print:last-child {
     page-break-after: auto;
}

.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    
    padding: 3px;
    text-align:center;
}

.col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12 {
        float: left;
   }
   .col-sm-12 {
        width: 100%;
   }
   .col-sm-11 {
        width: 91.66666667%;
   }
   .col-sm-10 {
        width: 83.33333333%;
   }
   .col-sm-9 {
        width: 75%;
   }
   .col-sm-8 {
        width: 66.66666667%;
   }
   .col-sm-7 {
        width: 58.33333333%;
   }
   .col-sm-6 {
        width: 50%;
   }
   .col-sm-5 {
        width: 41.66666667%;
   }
   .col-sm-4 {
        width: 33.33333333%;
   }
   .col-sm-3 {
        width: 25%;
   }
   .col-sm-2 {
        width: 16.66666667%;
   }
   .col-sm-1 {
        width: 8.33333333%;
   }
    html, body {
        height: 99%;    
    }
}

@media print 
{

table { page-break-inside:auto }
tr    { page-break-inside:avoid; page-break-after:auto }
thead { display:table-header-group }
tfoot { display:table-footer-group }

}
</style>
        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
        
        <script>
function deleteiteminpo(challid,pono)
{
	if(confirm('Do you really want to delete the item from PO'))
	{
	document.location="<?php echo page_url;?>Assets_management/deletepoitem/"+challid+"/"+pono;
	}
}
</script>

<script>
function myFunction() {
  window.print();
}
</script>
		
		
    </head>


    <body>


        <!-- Navigation Bar-->
       
        <!-- End Navigation Bar-->


        <div class="wrapper" style="background-color:#fff;">
            <div class="container">

                <!-- Page-Title -->
				
                <div class="row" style="margin-bottom:15px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                         <div class="col-md-12 page-title-box">
                        <div class="col-md-5"></div>
                        <div class="col-md-2 text-center"><span style="text-align:center"><button class="btn btn-success" onclick="myFunction()">Take Printout</button></span></div>
                        <div class="col-md-5"></div>
                         
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<div>
			<?php
if($cha->num_rows()>0)
					{
					  foreach($cha->result() as $challandata);
					   ?>
<page size="A4">
    	<div class="pageinside-invoice">
			<div class="row">
			  
                    <?php
                    $company=$this->db->select('company_name,address')->from('business_location')->where('business_loc_id',1)->get();
                    foreach($company->result() as $com);
                    ?>
				<div class="col-sm-12">
					<h3 style="text-align:center;font-weight: 700;margin:8px 0;margin-bottom: 0px;"><img src="<?php echo assets_url;?>presto-logo.png" alt="" height="34">&nbsp;</h3>
					<p class="address uppercase" style="color:#000;">I-42, DLF INDUSTRIAL AREA, PHASE-1<br/>
FARIDABAD HARYANA-121003<br>
PHONES: 0129-4272727 / 0129-4083111<br/><strong>GSTN: 07ACLFS3577R1Z2</strong></p>
				</div>
			</div>
			
			<div class="row">
				<div class="col-sm-12">
					<table class="table tablenoborder" style="border-bottom:2px solid #333;margin-bottom:0">
						<thead>
							<tr class="border">
								<th colspan="2" style="color:black;font-size: 17px;"> NON RETURNABLE CHALLAN</th>
							</tr>
						</thead>
						
				
								
						<tbody>
							<tr>
								<td class="w50" style="border-right:1px solid #333">
									<table class="tablepodetail w80" style="text-align:left">
										<tr>
											<th>To: </th>
											<td style="text-align:left"><?php echo $row->name;?></td>
										</tr>
										<tr>
											<th>Address:</th>
											<td style="text-align:left"><?php echo $row->address;?> </td>
										</tr>
										<tr>
											<th>MOB:</th>
											<td style="text-align:left"><?php echo $row->phone;?></td>
										</tr>
										
										
									
										
									</table>
								</td>
								<td class="w50">
									<table class="tablepodetail w120">
										<tr>
											<th style="text-align:left">GATEPASS NO.</th>
											<td style="text-align:left">: <?php echo $row->challan_no;?></td>
										</tr>
										<tr>
											<th style="text-align:left">Date</th>
											<td style="text-align:left">: <?php echo date('d-m-Y',strtotime($row->challan_date));?></td>
												
										
										</tr>
										
									</table>
								</td>
							</tr>
						</tbody>
					</table>

					
					
					<div class="col-md-12" style="background-color:#FAC296;padding:2px 0px 2px 0px;font-weight:bold;text-align:center;">Item Details <div class="page-title pull-right"></div></div>
						<table class="table tablenoborder tabledetails" style="border-top:0;margin-bottom: 5px;" border="1">
						<thead>
							
							
							<tr rowspan="2">
								<th style="text-align:center;border-top:1px solid;">SR NO</th>
								<th style="text-align:center;border-top:1px solid;">FINCODE</th>
								<th style="text-align:center;border-top:1px solid;">ITEM DESCRIPTION</th>
								<th class="currstock" style="text-align:center;border-top:1px solid;">UNIT</th>
								<th class="" style="text-align:center;border-top:1px solid;">QTY SENT</th>
								<th style="text-align:center;border-top:1px solid;">WEIGHT SENT</th>
								<th class="hideprice" style="text-align:center;border-top:1px solid;">RATE / UNIT</th>
							</tr>
							
							
						</thead>
						<tbody>
						
						<?php
						$i=1;
						foreach($cha->result() as $challandata)
						{
							$total[] = $challandata->rate;
						    ?>
						
							     <tr style="text-align:center">
                                    <td><?php echo $i;?></td>
                                    
                                    <td style="text-align:center">
									<?php if($row->source=='1'){
										echo $challandata->fincode;
										
									}else{
										
									}?>
									</td>
									<td style="text-align:center"><?php echo $challandata->itemname;?></td>
									<td><?php echo $challandata->unit_id;?></td>
									<td><?php echo $challandata->sent_qty;?></td>
									<td><?php echo $challandata->weight_of_qty;?></td>
									<td><?php echo $challandata->rate;?></td>
                                    
                                   </tr>
							<?php
					$i++;	}
						?>
						<td colspan="6"></td>
						<td style="text-align:center; font-weight:bold;"><?php echo array_sum($total);?>.00</td>
							
						</tbody>
					</table>
				
				</div>
			
			
				<div class="col-sm-6">
				    <p>&nbsp;&nbsp;Dispatched Through: <strong><?php echo $row->vehicle_type;?></strong></p>
    				 <p>&nbsp;&nbsp;Issued By: <strong><?php echo $row->first_name;?> <?php echo $row->last_name;?></strong></p>
				</div>
			
			
				<div class="col-sm-6">
				    <p>&nbsp;&nbsp;AuthorisedBy: </p>
				    <p>&nbsp;&nbsp;Receivers Signature:</p>
				    
				    
				</div>
		
			
			
        </div>
    </page>
	<?php
					}
					?>



										
										<!-- end row -->
                        </div> <!-- end ard-box -->
                    </div><!-- end col-->
</div>
                </div>
                <!-- end row -->
 <!-- Footer -->

                <!-- End Footer -->

            </div> <!-- end container -->
        </div>
        <!-- end wrapper -->
		<div style="padding-top:200px; background-color:#fff;"></div>

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
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>


    </body>
</html>