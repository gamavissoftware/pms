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

                background: <?php //echo $LOGO->colorcode;?>;

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

                           

                            <h4 class="page-title text-center">YEARLY/MONTHLY SALES TREND REPORTING</h4><hr>
                        </div>

                    </div>

                </div>

                <!-- end page title end breadcrumb -->
                <?php 
                $type=$this->uri->segment(3);
                $startdate=$this->uri->segment(4);
                $enddate=$this->uri->segment(5);
                if($type==2)
                {
                if($startdate<>'' && $enddate<>'')
                {
                $dateone=date('d-m-Y',strtotime($startdate));
                $datetwo=date('d-m-Y',strtotime($enddate));
                }else
                {
                  $dateone='';
                $datetwo='';
                }
                }else
                {

                     if($startdate<>'' && $enddate<>'')
                {
                $dateone=$startdate;
                $datetwo=$enddate;
                }else
                {
                  $dateone='';
                $datetwo='';
                }
                }  


        
                ?>
                <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
                <div class="row">
                  <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                    <div class="card-box">
                        <form action="<?php echo page_url; ?>Sales_stats_reporting/get_sales_stats" method="post">
                      <div class="row">
                         
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label for="field-1" class="control-label">TYPE</label>
                          <span style="color:red;">*</span>
                          <select name="type" id="type" class="form-control select2" required onchange="hidedates(1);">
                          <option value="">SELECT TYPE</option>
                             <option value="1" <?php if($type==1){?> selected <?php } ?>>By Year</option>
                             <option value="2" <?php if($type==2){?> selected <?php } ?>>By Month</option>
                         
                          </select>
                        </div>
                      </div>
                      <script>
                          function hidedates(flag)
                          {
                            var t =$("#type").val();
                            if(t==1)
                            {
                                $(".ydates").css('display','');
                                $(".fdates").css('display','none');
                                $("#startdate").attr('required',false);
                                $("#enddate").attr('required',false);
                                $("#startyear").attr('required',true);
                                $("#endyear").attr('required',true);



                            }else if(t==2)
                            {
                                $(".fdates").css('display','');
                                $(".ydates").css('display','none');
                                $("#startdate").attr('required',true);
                                $("#enddate").attr('required',true);
                                $("#startyear").attr('required',false);
                                $("#endyear").attr('required',false);

                                if(flag==1)
                                {
                                    $("#startdate").val('');
                                    $("#enddate").val('');
                                }
                            }else
                            {   
                                

                                $(".fdates").css('display','');
                                $(".ydates").css('display','none');
                                $("#startdate").attr('required',true);
                                $("#enddate").attr('required',true);
                                $("#startyear").attr('required',false);
                                $("#endyear").attr('required',false);
                            }
                          }
                      </script>
                      <div class="col-sm-4 fdates">
                        <div class="form-group">
                          <label for="field-1" class="control-label">START DATE</label>
                          <span style="color:red;">*</span>
                          <input type="text" name="startdate" id="startdate" value="<?php echo $dateone; ?>" class="form-control datepicker" required >
                        </div>
                      </div>
                      <div class="col-sm-4 fdates">
                        <div class="form-group">
                          <label for="field-1" class="control-label">END DATE</label>
                          <span style="color:red;">*</span>
                          <input type="text" name="enddate" id="enddate" value="<?php echo $datetwo; ?>" class="form-control datepicker" required >
                        </div>
                      </div>

                      <?php 
                      $fydate="2020-04-01";
                      $fyedate=date('Y-m-d');
                        $diff = abs(strtotime($fyedate)-strtotime($fydate));
                        $years = floor($diff / (365*60*60*24));
                       
                      ?>
                       <div class="col-sm-4 ydates" style="display:none;">
                        <div class="form-group">
                          <label for="field-1" class="control-label">START FY YEAR</label>
                          <span style="color:red;">*</span>
                          <select name="startyear" id="startyear" class="form-control">
                            <option value="">Select FY YEAR</option>
                            <?php 
                            $base="2020";
                            for($i=0;$i<$years+1;$i++)
                            { 
                            $a=$base+$i;
                            $b=$base+$i+1;
                            ?>
                            <option value="<?php echo $a;?>-<?php echo $b;?>" <?php if($dateone==$a."-".$b){ echo "selected"; };?>><?php echo $a;?>-<?php echo $b;?></option>
                            <?php 
                           
                            }
                            ?>
                          </select>
                        </div>
                      </div>

                         <div class="col-sm-4 ydates" style="display:none;">
                        <div class="form-group">
                          <label for="field-1" class="control-label">END FY YEAR</label>
                          <span style="color:red;">*</span>
                           <select name="endyear" id="endyear" class="form-control">
                            <option value="">Select FY YEAR</option>
                            <?php 
                            $base="2020";
                            for($i=0;$i<$years+1;$i++)
                            { 
                            $a=$base+$i;
                            $b=$base+$i+1;
                            ?>
                            <option value="<?php echo $a;?>-<?php echo $b;?>" <?php if($datetwo==$a."-".$b){ echo "selected"; };?>><?php echo $a;?>-<?php echo $b;?></option>
                            <?php 
                           
                            }
                            ?>
                          </select>
                        </div>
                      </div>
                      
                    </div>
                    <div class="row">
                      <div class="col-sm-12 text-center">
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
            
   <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart1);

        function drawChart1() {
        //alert('hi sele');
        $.ajax({
        url: "<?php echo page_url; ?>Sales_stats_reporting/get_sales_data/<?php echo $type ?>/<?php echo $startdate; ?>/<?php echo $enddate;?>",
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
    data.addColumn('string', 'Month/Year');
    data.addColumn('number', 'QTY in LTR');
    data.addColumn({type:'number', role:'annotation'});
     data.addColumn({type:'string', role:'tooltip'});
     //data.addColumn({type:'string', role:'style'});
  

    $.each(JSON.parse(jsonData), function(i, jsonData){
        var industry = jsonData.indus;
        var leads = parseFloat($.trim(jsonData.count));
        var labels = parseFloat($.trim(jsonData.labels));
        var sdate = parseFloat($.trim(jsonData.startdate));
        var edate = parseFloat($.trim(jsonData.enddate));
         var tools = jsonData.tool;

        data.addRows([[industry, leads,labels,tools]]);
    });

    var options = {
        colors: ['red','blue','orange'],
        hAxis: {
            title: "Month/Year"
        },
        vAxis: {
            title: 'Qty (in LTR)',
            format: '0'
        },
        chartArea:{width:'80%',height:'60%'}

       
    }
      var chart = new google.visualization.ColumnChart(document.getElementById('chart_area2'));

     
      chart.draw(data, options);

       google.visualization.events.addListener(chart, 'select', clickme);

      
        function clickme()
{
   var selection = chart.getSelection();

   for (var i = 0; i < selection.length; i++) {
    var item = selection[i];
    var redata=data.getValue(chart.getSelection()[0].row, 0);
    var prd="ALL";
     $.ajax({
        url: "<?php echo page_url; ?>Sales_stats_reporting/getdates_sorted",
        method: "POST",
        data:"month="+redata,
        success: function(data) {
        var d=data.split('|');
         window.open("<?php echo page_url;?>Sales_stats_reporting/most_selling_product/"+d[0]+"/"+d[1]+"/"+prd, "_blank");

        }

        });


}
}


      }
      


    </script>


  

 <div class="col-sm-12">
    <div class="card-box" style="padding-bottom:150px;" > 

    <div class="tablebutton">
  <button class="gridlist" onclick="opengrid(event, 'graph-btn')" id="defaultopen"><i class="fa fa-pie-chart" aria-hidden="true"></i></button>
 
</div>

<div id="graph-btn" class="gridcontent">

  <div class="ribbon11-wrapper">
  <h5 class="ribbon11 text-center"><b>Sales Statistics
  </b></h5><b>
 </b><p> <?php
if($dateone<>'' && $datetwo<>'')
{
?>
   BETWEEN </b> <?php echo $dateone; ?> to <?php echo $datetwo; ?>
 <br/></p>
 <?php 
}
?> 

</div>




<div class="row">
  <div id="chart_area2" style="width: 100%; height: 420px;"></div>

  </div>

<?php 
if($this->uri->segment(3)<>'')
    {?>
  <div class="col-md-12">
    <h4>Compare Year/Month Sale</h4>
</div>
 <?php if($this->uri->segment(3)==2)
    { ?>
    <form action="<?php echo page_url;?>Sales_stats_reporting/month_wise_comparision/" method="post">
<div class="col-md-12">
   
<?php
    $startdate=$this->uri->segment(4);
    $enddate=$this->uri->segment(5);
    $start = $month = strtotime($startdate);
           
    $end = strtotime($enddate);
    while($month <= $end)
    {

    $monthstart=date('Y-m-01',$month);
    $monthend=date('Y-m-t',$month);
    $monthname=date('M Y',$month);
    $monthvalue=date('Y-m',$month);
    ?>
    <div class="col-md-2">
        <input type="checkbox" name="compare[]" value="<?php echo $monthvalue;?>">&nbsp;<?php echo $monthname;?>
        
    </div>
    <?php  $month = strtotime("+1 month", $month); 
    }
    ?>
   
   
  </div>
  <div class="col-md-12" style="margin-top:20px;">
    <div class="col-md-4 pull-left">
      <input type="submit" name="sub" id="sub" class="btn btn-warning" value="Compare">
  </div></div>
</form>
   <?php }else
   {
 ?>




 <form action="<?php echo page_url;?>Sales_stats_reporting/year_wise_comparision/" method="post">
<div class="col-md-12">
   
<?php
    $startdate=$this->uri->segment(4);
    $enddate=$this->uri->segment(5);
    $date1 = explode('-',$startdate);
    $date2 = explode('-',$enddate);

    $startyear=$date1[0];
    $endyear=$date2[1];

    $date1=date($startyear.'-04-01');
    $date2=date($endyear.'-03-31');

    $diff = abs(strtotime($date2)-strtotime($date1));

    $years = floor($diff / (365*60*60*24));

    $curyear=date('Y',strtotime($date1));

    for($i=0;$i<$years+1;$i++)
    {

            $last=$curyear+1;
            $monthstart=date('Y-m-d',strtotime($curyear."-04-01"));
            $monthend=date('Y-m-d',strtotime($last."-03-31"));
    ?>
    <div class="col-md-2">
        <input type="checkbox" name="compare[]" value="<?php echo $monthstart;?>|<?php echo $monthend;?>">&nbsp;<?php echo $curyear."-".$last;?>
        
    </div>
    <?php 
    $curyear=$curyear+1;
    }
    ?>
   
   
  </div>
  <div class="col-md-12" style="margin-top:20px;">
    <div class="col-md-4 pull-left">
      <input type="submit" name="sub" id="sub" class="btn btn-warning" value="Compare">
  </div></div>
</form>



<?php } ?>
</div>
<?php } ?>



<script>

     $(document).ready(function () {
   $("input[name='compare[]']").change(function () {
      var maxAllowed = 2;
      var cnt = $("input[name='compare[]']:checked").length;
      if (cnt > maxAllowed) 
      {
         $(this).prop("checked", "");
         alert('Select maximum ' + maxAllowed + ' Levels!');
     }
  });
});


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



    $( document ).ready(function() {
    hidedates(0);
});

</script>



    </body>

</html>

