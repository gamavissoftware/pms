<!DOCTYPE html>

<html>

<head>

  <meta charset="utf-8">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <meta name="description" content="">

  <meta name="author" content="<?php echo copyright; ?>">
  <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
  <title><?php echo sitetitle; ?>Turn Around Time Reports</title>



  <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css">

  <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->

  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

  <!--[if lt IE 9]>

        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>

        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>

        <![endif]-->


  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

  <?PHP

  $q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();

  foreach ($q->result() as $LOGO);

  ?>

  <style>

    @media print{
      table{
        border: 1px solid black;
        width: 100%;
      }

      table th, td{
        border: 1px solid black;
        text-align: center;
      }

      table th{
        font-size: 11px;
      }

      table td{
        font-size: 10px;
      }
    }
    table.manglesh thead th {

      background: <?php echo $LOGO->colorcode; ?>;

      color: #fff;

      font-weight: bold;

      text-align: center;

    }

    table.manglesh tbody td {
      text-align: center;
    }

    #preloder1 {

    /*display: none;*/

    /*position: fixed;*/

    width: 100%;

    height: 100%;

    top: 0;

    left: 0;

    z-index: 999999;

    /* background: #000; */

    background: #ffffffad;

}
  </style>

</head>

<body>

  <!-- Navigation Bar-->

  <header id="topnav">

    <?php $this->load->view('common/nav-menu'); ?>

  </header>

  <!-- End Navigation Bar-->



  <?php $this->load->view('common/info-section.php'); ?>

  <div class="wrapper">

    <div class="container">



      <!-- Page-Title -->

      <div class="row" style="margin-top:20px;">

        <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">

          <div class="page-title-box">

            <!-- <div class="btn-group pull-right">

                          <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal" style="background-color: ;">ADD NEW DEPARTMENT</button>

                               

                            </div> -->



            <h4 class="page-title">Turn Around Time</h4>

          </div>

        </div>

      </div>

      <!-- end page title end breadcrumb -->
      <?php
      // $uri = $this->uri->segment(3);
       $startdate = $this->uri->segment(3);
       $enddate = $this->uri->segment(4);
       if($startdate!='')
       {
       
      $dateone = date('d-m-Y',strtotime($startdate));
      $datetwo = date('d-m-Y',strtotime($enddate));
    }else
    {
      $datetwo='';
      $dateone='';
    }
      ?>
      <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
      <div id="myform">
        <div class="row">
          <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
           <form method="post" action="<?php echo page_url; ?>Salesreporting/turn_around_time_form/" autocomplete="off" >
            <div class="card-box">
              <div class="row">
               <div class="col-sm-1"></div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label for="field-1" class="control-label">START DATE</label>
                      <span style="color:red;">*</span>
                      <input type="text" name="startdate" id="startdate" <?php if ($startdate == '') { ?>value="" <?php } else { ?> value="<?php echo $dateone; ?>" <?php } ?> class="form-control datepicker">
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label for="field-1" class="control-label">END DATE</label>
                      <span style="color:red;">*</span>
                      <input type="text" name="enddate" id="enddate" <?php if ($enddate == '') { ?>value="" <?php } else { ?> value="<?php echo $datetwo; ?>" <?php } ?> class="form-control datepicker">
                    </div>
                  </div>
                  <div class="col-sm-1">

                    <div class="form-group">
                      <button type="submit" style="margin-top: 25px; width:100%;" class="btn btn-info btn-sm" onclick="getdatalead();">Submit</button>
                    </div>
                  </div>
                  <div class="col-sm-1"></div>
              </div>
            </div>
          </form>
          </div>

        </div>
      </div>
        <?php 
             
                // $dateone=date('d-m-Y',strtotime($startdate));
                // $datetwo=date('d-m-Y',strtotime($enddate));
                ?>
      <div class="row">
        <div class="col-sm-12">
    <div class="card-box" > 

<div id="table-btn" class="table-graph1">
  <div class="ribbon11-wrapper">
  <h5 class="ribbon11"><b>Turn Around Time</b></h5>
  <p><span ><?php echo $dateone; ?></span> to <span><?php echo $datetwo; ?></span></p>
</div>
<div class="row" >
  <!-- <input type="button" onclick="printDiv('printableArea')" class="btn btn-info pull-right" value="Print Ledger" /> -->
<!-- <div class="col-sm-1"></div> -->
  <!-- <div class="col-sm-12" id="printableArea" style="border: 1px dashed lightgray; margin-top: 20px; border-radius: 5px; padding:10px;min-height: 400px;">
    <div id="preloder1">
    <div class="loader">

    <img src="<?php echo assets_url; ?>images/loader.gif" alt="Logo">

    </div>
</div>
    <div id="html"></div>
</div> -->
  <!-- <div class="col-sm-1"></div> -->

<div class="col-sm-12">
<div class="card-box table-responsive">
<table id="example" class="table table-striped table-bordered pretty">
    <thead>
    <tr>
    <th>Sr No.</th>
    <th>Months</th>
    <th>Total Leads</th>
    <!-- <th>Time to Reponse</th> -->
    <th>Average Time Response</th>
    </tr>
    </thead>
    <tbody></tbody>
</table>
</div>
</div>
</div>
</div>



<!-- <script>
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
</script> -->
    </div>
  </div>
      </div>
      <!-- Footer -->

      <?php $this->load->view('common/footer'); ?>

      <!-- End Footer -->



    </div> <!-- end container -->




  </div>



  <!-- jQuery  -->


  <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>

  <script src="<?php echo assets_url; ?>js/detect.js"></script>

  <script src="<?php echo assets_url; ?>js/fastclick.js"></script>

  <script src="<?php echo assets_url; ?>js/jquery.slimscroll.js"></script>

  <script src="<?php echo assets_url; ?>js/jquery.blockUI.js"></script>

  <script src="<?php echo assets_url; ?>js/waves.js"></script>

  <script src="<?php echo assets_url; ?>js/wow.min.js"></script>

  <script src="<?php echo assets_url; ?>js/jquery.nicescroll.js"></script>

  <script src="<?php echo assets_url; ?>js/jquery.scrollTo.min.js"></script>

  <script src="https://momentjs.com/downloads/moment.js"></script>
  <!-- App js -->

  <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>

  <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
  <script>
    $('.datepicker').datepicker({
      autoclose: true,
      format: 'dd-mm-yyyy'
    });
  </script>
<script type="text/javascript">
    function getdatalead() {
        //alert('ji');
         $('#html').html('');
        var stdate=$('#startdate').val();
        var enddate=$('#enddate').val();
        var url="<?php echo page_url; ?>Salesreporting/salessourcewiselead/"+stdate+"/"+enddate;
       //alert(url);
        $.ajax({
        url: url,
        type: "post",
         beforeSend: function() {
        // setting a timeout
        $('#preloder1').css('display','');
    }, 
        success: function(data) {
        //alert(data);
        $('#preloder1').css('display','none');
        $('#html').html(data);
        $('#dateonefilter').html(stdate);
        $('#datetwofilter').html(enddate);
        }
        });  

    }
$(document).ready(function(){
           //alert("ready - page loaded")
           getdatalead();
       })

function checktdhide(i) {
  //alert(i);
  if($('.display'+i).is(":checked")){
    alert('hi');
    $('.display'+i).hide(200);
  }else
  {
    alert('fail');
    $('.display'+i).show(300);
  }
}
</script>
<script type="text/javascript">
  function printDiv(divName) {
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
}
</script>
<script>

$( document ).ready(function() {

$('#example').dataTable({

"bProcessing": true,

"pagination":true,

"sAjaxSource": "<?php echo page_url;?>Salesreporting/turnaroundtime/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>",

"aoColumns": [
        { mData: 'sr_no' } ,
        {mData:'months'},
        { mData: 'totalleads' },
        // { mData: 'activeleads' },
        { mData: 'average' }
    ]

});   

});
</script>
</body>

</html>