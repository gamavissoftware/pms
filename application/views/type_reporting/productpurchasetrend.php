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



        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css">
        <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->

        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

        <!--[if lt IE 9]>

        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>

        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>

        <![endif]-->
        	

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>	


		<style>

		table.manglesh thead th {

				background: <?php echo $LOGO->colorcode;?>;

				color:#fff;

				font-weight:bold;

				text-align:center;

			}
				table.manglesh tbody td {
				    text-align:center;
				}

		</style>
 
    </head>
    <body>

        <!-- Navigation Bar-->

        <header id="topnav">

          <?php $this->load->view('common/nav-menu');?>

        </header>

        <!-- End Navigation Bar-->



        <?php $this->load->view('common/info-section.php');?>

        <div class="wrapper">

            <div class="container">



                <!-- Page-Title -->

                <div class="row" style="margin-top:20px;">

                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">

                        <div class="page-title-box">

						 <!-- <div class="btn-group pull-right">

						  <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal" style="background-color: ;">ADD NEW DEPARTMENT</button>

                               

                            </div> -->

                           

                            <h4 class="page-title text-center">PRODUCT PURCHASE TREND REPORTING</h4><hr>
                        </div>

                    </div>

                </div>

                <!-- end page title end breadcrumb -->
                <?php 
                $uri=$this->uri->segment(3);
                $user=$this->uri->segment(4);
                
                 $users=base64_decode($user);
                 $check_user  = explode(',', str_replace("'", "", $users));
                 // echo "<pre>";print_r($check_user);exit;
                $startdate=$this->uri->segment(5);
                $enddate=$this->uri->segment(6);
                $dateone=date('d-m-Y',strtotime($startdate));
                $datetwo=date('d-m-Y',strtotime($enddate));
				
				if(isset($result)){
					
					
					
				}
                ?>
				<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
                <div class="row">
                  <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                    <div class="card-box">
                        <form action="<?php echo page_url; ?>Type_reporting/filterproductpurchasetrend/1" method="post">
                      <div class="row">
                         
                      
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label for="field-1" class="control-label">START DATE</label>
                          <span style="color:red;">*</span>
                          <input type="text" name="startdate" id="startdate" <?php if($startdate==''){ ?>value="dd-mm-yyyy" <?php }else {?> value="<?php echo $dateone; ?>" <?php } ?> class="form-control datepicker" >
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label for="field-1" class="control-label">END DATE</label>
                          <span style="color:red;">*</span>
                          <input type="text" name="enddate" id="enddate" <?php if($enddate==''){ ?>value="dd-mm-yyyy" <?php }else {?> value="<?php echo $datetwo; ?>" <?php } ?> class="form-control datepicker" >
                        </div>
                      </div>
                      <div class="col-sm-3 text-center">
                          <div class="form-group">
                            <button type="submit" style="margin-top: 25px;"class="btn btn-info btn-sm">Submit</button>
                          </div>
                      </div>
                    </div>
                   
                  </form>
                    </div>
                  </div>
                  
                </div>
                
                <div class="row">
                  <?php 
				  if($uri){
                  ?>
				     <?php
		
		
      $productvalue=$data['productvalue'];
		$startdate=$data['startdate'];
      $enddate=$data['enddate'];
	  if($productvalue){
		  	$q = $this->db->select('id,instruments_name')->from('presto_instruments')->where('id',$productvalue)->get();
			foreach($q->result() as $rowss);
			$productnameinfo = $rowss->instruments_name;
		  
	  }else{
		  $productnameinfo = "";
	  }
	  
	  ?>
   <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart1);

        function drawChart1() {
        //alert('hi sele');
        $.ajax({
        url: "<?php echo page_url; ?>Type_reporting/getdata1/<?php echo $startdate; ?>/<?php echo $enddate;?>",
        method: "GET",
        success: function(data) {
        //alert(data);
        drawMonthwiseChart8(data);
        }

        });

        }

          
      function drawMonthwiseChart8(chart_data)
{
    var jsonData = chart_data;
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Product');
    data.addColumn('number', 'Approved Price');
    data.addColumn({type:'string', role:'annotation'});
     //data.addColumn({type:'string', role:'style'});
  

    $.each(JSON.parse(jsonData), function(i, jsonData){
        var industry = jsonData.indus;
        var leads = parseFloat($.trim(jsonData.count));
        var labels = parseFloat($.trim(jsonData.labels));
        data.addRows([[industry, leads,labels+" "]]);
    });

    var options = {
        colors: ['gray'],
        hAxis: {
            title: "Agents"
        },
        vAxis: {
            title: 'Leads (count)',
            format: '0'
        },
        chartArea:{width:'80%',height:'60%'}
    }

      var chart = new google.visualization.ColumnChart(document.getElementById('chart_area2'));

      chart.draw(data, options);
      }
      
    </script>


   <!-- <div class="col-sm-12">
        <div class="card-box">
            <?php 
                $uri=$this->uri->segment(3);
                $stdate=$this->uri->segment(5)." 00:00:00";
                $enddate=$this->uri->segment(6)." 23:59:59";
                $dateone=date('d-m-Y',strtotime($stdate));
                $datetwo=date('d-m-Y',strtotime($enddate));
                ?>
                <div class="ribbon11-wrapper">
                  <h5 class="ribbon11"><b>Leads Handled by Agents</b></h5>
                  <p><?php echo date('d-M-Y',strtotime($dateone)); ?> to <?php echo date('d-M-Y',strtotime($datetwo)); ?></p>
                </div>

          <canvas id="cha" style="height: 300px; width: 100%;"></canvas> -->
          <!-- <div id="chart_area2" style="width: 100%; height: 420px;"></div>
        </div>
      </div>  -->

 <div class="col-sm-12">
    <div class="card-box" > 

    <div class="tablebutton">
  <button class="gridlist" onclick="opengrid(event, 'graph-btn')" id="defaultopen"><i class="fa fa-pie-chart" aria-hidden="true"></i></button>
  <button class="gridlist" onclick="opengrid(event, 'table-btn')"><i class="fa fa-table" aria-hidden="true"></i></button>
</div>

<div id="graph-btn" class="gridcontent">
<div class="ribbon11-wrapper">
  <h4 class="ribbon11 text-center"><b>PRODUCT <?php echo $productnameinfo;?> APPROVAL TREND BETWEEN </b> <?php echo date('d-M-Y',strtotime($startdate)); ?> to <?php echo date('d-M-Y',strtotime($enddate)); ?></h4><hr>
 
</div>

<div class="row">
  <div id="chart_area2" style="width: 100%; height: 420px;"></div>

  </div>
</div>
<div id="table-btn" class="gridcontent table-graph">
  
<div class="row">
  <div class="col-md-12">
    <div class="col-md-1"></div>
      <div class="col-md-10">
	 <table class="table table-bordered" style="text-align:center">
  <thead>
    <tr>
      
      <th scope="col" style="text-align:center">SR NO</th>
      <th scope="col" style="text-align:center">PRODUCT NAME</th>
      <th scope="col" style="text-align:center">APPROVED PRICE</th>
      <th scope="col" style="text-align:center">APPROVAL DATE</th>
    </tr>
  </thead>
  <tbody>
   <?php 
   $i=1;
			$q = $this->db->select('a.current_date,b.approved_price, c.instruments_name')->from('approval_form a')->join('approval_product_details b','a.id=b.approval_id')->join('presto_instruments c','b.product_id=c.id')->where('b.product_id',$productvalue)->where('a.current_date BETWEEN "'. date('Y-m-d', strtotime($startdate)). '" and "'. date('Y-m-d', strtotime($enddate)).'"')->get();
		//echo "<pre>"; print_r($q->result()); exit;
				foreach($q->result() as $row){
			?>
           
                        <tr>
                          <td><?php echo $i; ?></td>
                          <td><?php echo $row->instruments_name; ?></td>
                          <td><?php echo $row->approved_price; ?></td>
                          <td><?php echo date('d-m-Y',strtotime($row->current_date)); ?></td>
                        </tr>
		<?php $i++;}?>
   
   
  </tbody>
</table>
         
        </div>
      </div>
    </div>
</div>



<script>
function opengrid(evt, cityName) {
  var i, gridcontent, gridlist;
  gridcontent = document.getElementsByClassName("gridcontent");
  for (i = 0; i < gridcontent.length; i++) {
    gridcontent[i].style.display = "none";
  }
  gridlist = document.getElementsByClassName("gridlist");
  for (i = 0; i < gridlist.length; i++) {
    gridlist[i].className = gridlist[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}
document.getElementById("defaultopen").click();
</script>
    </div>
  </div>
				  
				  
				  <?php  }?>
                  
                  
                </div>
                <!-- Footer -->

<?php $this->load->view('common/footer');?>

                <!-- End Footer -->



            </div> <!-- end container -->

        </div>



         <!-- jQuery  -->

         <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
        <script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>

        <script src="<?php echo assets_url;?>js/detect.js"></script>

        <script src="<?php echo assets_url;?>js/fastclick.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.slimscroll.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.blockUI.js"></script>

        <script src="<?php echo assets_url;?>js/waves.js"></script>

        <script src="<?php echo assets_url;?>js/wow.min.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.nicescroll.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.scrollTo.min.js"></script>

        <!-- App js -->

        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
<script>
    $('.datepicker').datepicker({
     autoclose: true,
 format:'dd-mm-yyyy'
   });

    $('.select2').select2({ });

</script>



    </body>

</html>

