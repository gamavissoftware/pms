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


                           

                            <h4 class="page-title text-center">MONTHLY CLAIM TREND REPORTING-TYPE 1</h4><hr>
                        </div>

                    </div>

                </div>

                <!-- end page title end breadcrumb -->
                <?php 
                $startdate=$this->uri->segment(3);
                $enddate=$this->uri->segment(4);
                if($startdate<>'' && $enddate<>'')
                {
                $dateone=date('d-m-Y',strtotime($startdate));
                $datetwo=date('d-m-Y',strtotime($enddate));
                }else
                {
                  $dateone='';
                $datetwo='';
                }
		
  
                ?>
				<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
                <div class="row">
                  <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                    <div class="card-box">
                        <form action="<?php echo page_url; ?>Type_reporting/filter_monthly_claim_type_1/" method="post">
                      <div class="row">
                         
                      <div class="col-sm-3"></div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label for="field-1" class="control-label">START DATE</label>
                          <span style="color:red;">*</span>
                          <input type="text" name="startdate" id="startdate" value="<?php echo $dateone; ?>" class="form-control datepicker" required >
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label for="field-1" class="control-label">END DATE</label>
                          <span style="color:red;">*</span>
                          <input type="text" name="enddate" id="enddate" value="<?php echo $datetwo; ?>" class="form-control datepicker" required >
                        </div>
                      </div>
                        <div class="col-sm-3"></div>
                      
                    </div>
                    <div class="row">
                      <div class="col-sm-12 text-center">
                          <div class="form-group">
                            <button type="submit" style="margin-top: 25px;"class="btn btn-success btn-sm">Submit</button>
                          </div>
                      </div>
                    </div>
                  </form>
                    </div>
                  </div>
                  
                </div>
                
                <div class="row">
              <?php
	
		$startdate=$startdate;
      $enddate=$enddate;
	
	  
	  ?>
   <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart1);

        function drawChart1() {
        //alert('hi sele');
        $.ajax({
        url: "<?php echo page_url; ?>Type_reporting/getClaimdata_type1/<?php echo $startdate; ?>/<?php echo $enddate;?>",
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
    data.addColumn('string', 'Month');
    data.addColumn('number', 'Claim Amount');
    data.addColumn({type:'string', role:'annotation'});
     //data.addColumn({type:'string', role:'style'});
  

    $.each(JSON.parse(jsonData), function(i, jsonData){
        var industry = jsonData.indus;
        var leads = parseFloat($.trim(jsonData.count));
        var labels = parseFloat($.trim(jsonData.labels));
        data.addRows([[industry, leads,labels+" "]]);
    });

    var options = {
        colors: ['red','blue','orange'],
        hAxis: {
            title: "Months"
        },
        vAxis: {
            title: 'Type 1 Claim Amount (in ₹)',
            format: '0'
        },
        chartArea:{width:'80%',height:'60%'}
    }

      var chart = new google.visualization.ColumnChart(document.getElementById('chart_area2'));

      chart.draw(data, options);
      }
      
    </script>


  

 <div class="col-sm-12">
    <div class="card-box" > 

    <div class="tablebutton">
  <button class="gridlist" onclick="opengrid(event, 'graph-btn')" id="defaultopen"><i class="fa fa-pie-chart" aria-hidden="true"></i></button>
  <button class="gridlist" onclick="opengrid(event, 'table-btn')"><i class="fa fa-table" aria-hidden="true"></i></button>
</div>

<div id="graph-btn" class="gridcontent">
<div class="ribbon11-wrapper">
  <h5 class="ribbon11 text-center"><b>MONTLY CLAIM TREND REPORT
  </h5>
 <p><?php
if($dateone<>'' && $datetwo<>'')
{
?>
   BETWEEN </b> <?php echo $dateone; ?> to <?php echo $datetwo; ?>
<?php 
}
?></p>

</div>

<div class="row">
  <div id="chart_area2" style="width: 100%; height: 420px;"></div>

  </div>
</div>
<div id="table-btn" class="gridcontent table-graph">

<div class="row">
<div class="ribbon11-wrapper">
  <h5 class="ribbon11 text-center"><b>MONTHLY CLAIM TREND REPORT
  </b></h5><b>
 </b><p><b>   BETWEEN </b> 11-05-2022 to 13-09-2022</p>

</div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="col-md-1"></div>
      <div class="col-md-10">
	 <table class="table table-bordered" style="text-align:center">
  <thead>
    <tr>
      
      <th scope="col" style="text-align:center">SR NO</th>
      <th scope="col" style="text-align:center">MONTH</th>
      <th scope="col" style="text-align:center">CLAIM AMOUNT</th>
  
    </tr>
  </thead>
  <tbody>
    <?php
    $startdate = date('Y-m-d',strtotime($this->uri->segment(3)));
    $enddate = date('Y-m-d',strtotime($this->uri->segment(4)));
    
    $date1 = strtotime($startdate);
    $date2 = strtotime($enddate);
   
   $i=1;
    while ($date1 <= $date2) {
      $credit_note_sum=array();
      $credit_note_sum[]=0;
      $start_date=date('Y-m-01',$date1);
      $end_date=date('Y-m-t',$date1);
      
       $this->db->select('a.id, a.bill_no, b.name as party, c.auto_gen_code, d.name')
              ->from('purchase_entry a')
              ->join('vendors b', 'b.id=a.party')
              ->join('approval_form c', 'c.id=a.approval_id')
              ->join('hpcl_location d', 'd.id=c.hpcl_location');

      if($start_date <> '' && $end_date <> '') {
         $this->db->where('a.currentdate >=', $start_date);
         $this->db->where('a.currentdate <=', $end_date);
      }
       $query =  $this->db->get();  
      if($query->num_rows() > 0) {
      foreach($query->result() as $row) {

        $sql = $this->db->select('a.qty, b.approved_price, b.price_validity, b.credit_vli')
                  ->from('purchase_entry_details a')
                  ->join('approval_product_details b', 'b.id=a.approval_detail_id')
                  ->join('presto_instruments c', 'c.id=b.product_id')
                  ->join('units d', 'd.id=a.pack_size')
                  ->where('a.entry_id', $row->id)
                  ->get();

          if($sql->num_rows() > 0) {
            foreach ($sql->result() as $rows) {

              $credit_note_sum[] = $rows->qty * $rows->credit_vli;

            }

          }

      }

    }
 
      $sum=array_sum($credit_note_sum);
?>
    

                  <tr>
                          <td><?php echo $i; ?></td>
                          <td><?php echo date('M', $date1); ?></td>
                          <td>₹<?php echo $sum ?></td>
                         
                        </tr>

<?php
            
        $date1 = strtotime('+1 month', $date1);
         
        $i++;
      }
    
    ?>
           
                      

   
   
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

