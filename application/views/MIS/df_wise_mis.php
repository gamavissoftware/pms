 <?php 
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="<?php echo copyright; ?>">

<link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

<title><?php echo sitetitle; ?> Userwise MIS</title>

<!-- Table Responsive css -->
<script src="<?php echo assets_url;?>js/angular.min.js"></script>
<!-- DataTables -->
<link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
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
<script src="//cdnjs.cloudflare.com/ajax/libs/gsap/latest/TweenMax.min.js"></script>
<style>

#pageloader
{
background: rgba( 255, 255, 255, 0.8 );
display: none;
height: 100%;
position: fixed;
width: 100%;
z-index: 9999;
}
#pageloader img
{
left: 30%;
margin-left: -10px;
margin-top: -10px;
position: absolute;
top: 30%;
}
.stepwizard-step p {
margin-top: 10px;
}
.stepwizard-row {
display: table-row;
}
.stepwizard {
display: table;
width: 50%;
position: relative;
}
.stepwizard-step button[disabled] {
opacity: 1 !important;
filter: alpha(opacity=100) !important;
}
.stepwizard-row:before {
top: 14px;
bottom: 0;
position: absolute;
content: " ";
width: 100%;
height: 1px;
background-color: #ccc;
z-order: 0;
}
.stepwizard-step {
display: table-cell;
text-align: center;
position: relative;
}
.btn-circle {
width: 30px;
height: 30px;
text-align: center;
padding: 6px 0;
font-size: 12px;
line-height: 1.428571429;
border-radius: 15px;
}
table{

    font-size: 12px !important;

}

.accordion {
  max-width: 500px;
  margin: 0 auto;
}
.accordion__title {
  font-family: 'industry', sans-serif;
  font-weight: 300;
  color: #fff;
  text-transform: uppercase;
  font-size: 1.125em;
}
.accordion__list {
  list-style: none;
  margin: 0;
  padding: 0;
}
.accordion__item {
  border-bottom: 1px solid #000;
  visibility: hidden;
  margin-bottom: 20px;
}
.accordion__item:last-child {
  border-bottom: 0;
}
.accordion__item.is-active .accordion__itemTitleWrap::after {
  -webkit-transform: translateX(-20%);
          transform: translateX(-20%);
}
.accordion__item.is-active .accordion__itemIconWrap {
  -webkit-transform: rotate(180deg);
          transform: rotate(180deg);
}
.accordion__itemTitleWrap {
  display: flex;
  height: 100%;
  color:black;
  align-items: center;
  padding: 0 1em;
  color: #000;
  font-weight: bold;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}
.accordion__itemTitleWrap::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 200%;
  height: 100%;
  background: #3bade3;
  background: rgb(235, 235, 235);
  z-index: 1;
  transition: -webkit-transform .4s ease;
  transition: transform .4s ease;
  transition: transform .4s ease, -webkit-transform .4s ease;
}
.accordion__itemTitleWrap.is-active::after, .accordion__itemTitleWrap:hover::after {
  -webkit-transform: translateX(-20%);
          transform: translateX(-20%);
}
.accordion__itemIconWrap {
  width: 1.25em;
  height: 1.25em;
  margin-left: auto;
  position: relative;
  z-index: 10;
}
.accordion__itemTitle {
  margin: 0;
  font-family: 'industry', sans-serif;
  font-weight: 300;
  font-size: 1em;
  position: relative;
  z-index: 10;
}
.accordion__itemContent {
  font-size: 0.875em;
  height: 0;
  overflow: hidden;
  background-color: #fff;
  padding: 0 1.25em;
}
.accordion__itemContent p {
  margin: 2em 0;
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
<div class="divheight hidden-xs"></div>
<div class="row">
<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
<div class="page-title-box">

<h4 class="page-title text-center">DF WISE MIS DASHBOARD</h4>
</div>
</div>


<form name="frm" action="<?php echo page_url;?>MIS/filter_mis" method="post">
<div class="row">
<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 card-box">
	<div class="col-md-4"></div>
<div class="col-md-4">
	<div class="form-group">
		<label>SELECT DF NO</label>
		<select class="form-control multipleselect" name="filterbydf" id="filterbydf">
			<option value="ALL" <?php if($this->uri->segment(3)=='ALL'){echo "selected";}?>>ALL</option>
			<?php 
				$q = $this->db->select('df_no, id')->from('df_release')->where('df_status',0)->get();
				if($q->num_rows()>0){
					foreach($q->result() as $row){
			?>
			<option value="<?php echo $row->id;?>" <?php if($this->uri->segment(3)==$row->id){echo "selected";}?> ><?php echo $row->df_no;?></option>
		<?php } }?>
		</select>
	</div>
</div>
</div>
</div>
</form>




<!-- end page title end breadcrumb -->
<span style="color:red;"><?php //echo   $this->session->flashdata('message'); ?></span>

<div class="row" style="margin-top:20px;">
	<div class="card-box">
		
		<ul class="accordion__list">
		<li class="accordion__item" id="AccordianQuestion">
		<div class="accordion__itemTitleWrap">
		<div class="col-md-3">
		<h3 class="accordion__itemTitle" style="color:grey;font-weight: bold;font-size:25px"><i>MARKETING&nbsp;</i><span style="color:red;">55%</span><br/>
			<span style="z-index: 999 !important;font-size:13px;">HOD: Shubham Sharma</span></h3>
		</div>
		<div class="col-md-3" style="z-index: 999 !important;">Planned 27th March 2024/2nd April 2024</div>
		<div class="col-md-3" style="z-index: 999 !important;">
			<div class="w3-light-grey" style="background-color: lightblue !important;margin-top:20px;">
			<div class="w3-container w3-green w3-center" style="width:25%;">25%</div>
			</div><br>

		</div>
		<div class="col-md-2" style="z-index: 999 !important;">
		<strong style="color:red;">Delayed 3 Days</strong>
		</div>

		<div class="col-md-3" style="z-index: 999 !important;">
		<strong style="color:green;">Estimated Completion By 5th April 2024</strong>
		</div>

		<div class="accordion__itemIconWrap"><i class="fa fa-plus"></i></div>
		</div>
		<div class="accordion__itemContent">
			
			<table class="table table-bordered text-center" style="font-size:15px;margin-top:20px;">
<thead>
<tr style="background-color: #f2f0ec;">
<th class="text-center">PARAMETER</th>
<th class="text-center">TOTAL ASSIGNED/DONE</th>
<th class="text-center">TOTAL DONE ON TIME</th>
<th class="text-center">DIFF</th>
<th class="text-center">% OF WORK NOT DONE &amp; DELAYED</th>
</tr>
</thead>
<tbody>
<tr>
<td>% OF WORK NOT DONE</td>
<td>10</td>
<td>4</td>
<td><a href="javascript:;" onclick="gettaskmodel('61','2024-03-29','2024-04-05',1,'ALL');"><u>6</u></a></td>
<td>
	60%
</td>
</tr>


<tr>
<td>% OF WORK DELAYED</td>

<td>4</td>
<td>2</td>
<td><a href="javascript:;" onclick="gettaskmodel('61','2024-03-29','2024-04-05',2,'ALL');">
	2<u></u></a></td>
<td>
	50%</td>

</tr>

<tr>
<td colspan="4" style="color:red; font-weight:bold;">AVERAGE DELAY 1 DAYS</td>
</tr>
<tr>
<td colspan="4"></td>
<td style="background-color: #efecec;">
	55%</td>
</tr>



</tbody>
</table>
<h4 class="page-title text-center">Task Details</h4>
<table class="table table-bordered text-center" style="font-size:15px;margin-top:20px;">
<thead>
<tr style="background-color: #f2f0ec;">
<th class="text-center">Task Detail</th>
<th class="text-center">Assigned To</th>
<th class="text-center">Planned Start/End Date</th>
<th class="text-center">TAT</th>
<th class="text-center">Assigned On</th>
<th class="text-center">Current Status</th>
<th class="text-center">Days Left</th>
<th class="text-center">Delay</th>
</tr>
</thead>
<tbody>
<tr>
<td>Release DF</td>
<td>Manoj Dubey</td>
<td>27 March 2024/28 March 2024</td>
<td>1 Day</td>
<td>28 March 2024</td>
<td>Completed</td>
<td>0</td>
<td>0</td>
</tr>
<tr>
<td>Creation of Proforma Invoice</td>
<td>Manoj Dubey</td>
<td>28 March/29 March 2024</td>
<td>1 Day</td>
<td>28 March 2024</td>
<td>In Process</td>
<td>0</td>
<td><strong style="color:red;font-weight: bold;">+6 Days</strong></td>
</tr>


</tbody>
</table>
</div>
		</li>


		<li class="accordion__item" id="AccordianQuestion">
		<div class="accordion__itemTitleWrap">
		<div class="col-md-3">
		<h3 class="accordion__itemTitle" style="color:grey;font-weight: bold;font-size:25px"><i>DESIGN&nbsp;</i><span style="color:red;">35%</span><br/>
			<span style="z-index: 999 !important;font-size:13px;">HOD: Puran Lal sharma</span></h3>
		</div>
		<div class="col-md-3" style="z-index: 999 !important;">Planned 27th March 2024/2nd April 2024</div>
		<div class="col-md-3" style="z-index: 999 !important;">
			<div class="w3-light-grey" style="background-color: lightblue !important;margin-top:20px;">
			<div class="w3-container w3-green w3-center" style="width:25%;">25%</div>
			</div><br>

		</div>
		<div class="col-md-2" style="z-index: 999 !important;">
		<strong style="color:red;">Delayed 3 Days</strong>
		</div>

		<div class="col-md-3" style="z-index: 999 !important;">
		<strong style="color:green;">Estimated Completion By 5th April 2024</strong>
		</div>

		<div class="accordion__itemIconWrap"><i class="fa fa-plus"></i></div>
		</div>
		<div class="accordion__itemContent">
			
			<table class="table table-bordered text-center" style="font-size:15px;margin-top:20px;">
<thead>
<tr style="background-color: #f2f0ec;">
<th class="text-center">PARAMETER</th>
<th class="text-center">TOTAL ASSIGNED/DONE</th>
<th class="text-center">TOTAL DONE ON TIME</th>
<th class="text-center">DIFF</th>
<th class="text-center">% OF WORK NOT DONE &amp; DELAYED</th>
</tr>
</thead>
<tbody>
<tr>
<td>% OF WORK NOT DONE</td>
<td>10</td>
<td>4</td>
<td><a href="javascript:;" onclick="gettaskmodel('61','2024-03-29','2024-04-05',1,'ALL');"><u>6</u></a></td>
<td>
	60%
</td>
</tr>


<tr>
<td>% OF WORK DELAYED</td>

<td>4</td>
<td>2</td>
<td><a href="javascript:;" onclick="gettaskmodel('61','2024-03-29','2024-04-05',2,'ALL');">
	2<u></u></a></td>
<td>
	50%</td>

</tr>

<tr>
<td colspan="4" style="color:red; font-weight:bold;">AVERAGE DELAY 1 DAYS</td>
</tr>
<tr>
<td colspan="4"></td>
<td style="background-color: #efecec;">
	55%</td>
</tr>



</tbody>
</table>
<h4 class="page-title text-center">Task Details</h4>
<table class="table table-bordered text-center" style="font-size:15px;margin-top:20px;">
<thead>
<tr style="background-color: #f2f0ec;">
<th class="text-center">Task Detail</th>
<th class="text-center">Assigned To</th>
<th class="text-center">Planned Start/End Date</th>
<th class="text-center">TAT</th>
<th class="text-center">Assigned On</th>
<th class="text-center">Current Status</th>
<th class="text-center">Days Left</th>
<th class="text-center">Delay</th>
</tr>
</thead>
<tbody>
<tr>
<td>Release DF</td>
<td>Manoj Dubey</td>
<td>27 March 2024/28 March 2024</td>
<td>1 Day</td>
<td>28 March 2024</td>
<td>Completed</td>
<td>0</td>
<td>0</td>
</tr>
<tr>
<td>Creation of Proforma Invoice</td>
<td>Manoj Dubey</td>
<td>28 March/29 March 2024</td>
<td>1 Day</td>
<td>28 March 2024</td>
<td>In Process</td>
<td>0</td>
<td><strong style="color:red;font-weight: bold;">+6 Days</strong></td>
</tr>


</tbody>
</table>
</div>
		</li>

		<li class="accordion__item" id="AccordianQuestion">
		<div class="accordion__itemTitleWrap">
		<div class="col-md-3">
		<h3 class="accordion__itemTitle" style="color:grey;font-weight: bold;font-size:25px"><i>PRODUCTION PPC&nbsp;</i><span style="color:red;">25%</span><br/>
			<span style="z-index: 999 !important;font-size:13px;">HOD: Shubham Sharma</span></h3>
		</div>
		<div class="col-md-3" style="z-index: 999 !important;">Planned 27th March 2024/2nd April 2024</div>
		<div class="col-md-3" style="z-index: 999 !important;">
			<div class="w3-light-grey" style="background-color: lightblue !important;margin-top:20px;">
			<div class="w3-container w3-green w3-center" style="width:25%;">25%</div>
			</div><br>

		</div>
		<div class="col-md-2" style="z-index: 999 !important;">
		<strong style="color:red;">Delayed 3 Days</strong>
		</div>

		<div class="col-md-3" style="z-index: 999 !important;">
		<strong style="color:green;">Estimated Completion By 5th April 2024</strong>
		</div>

		<div class="accordion__itemIconWrap"><i class="fa fa-plus"></i></div>
		</div>
		<div class="accordion__itemContent">
			
			<table class="table table-bordered text-center" style="font-size:15px;margin-top:20px;">
<thead>
<tr style="background-color: #f2f0ec;">
<th class="text-center">PARAMETER</th>
<th class="text-center">TOTAL ASSIGNED/DONE</th>
<th class="text-center">TOTAL DONE ON TIME</th>
<th class="text-center">DIFF</th>
<th class="text-center">% OF WORK NOT DONE &amp; DELAYED</th>
</tr>
</thead>
<tbody>
<tr>
<td>% OF WORK NOT DONE</td>
<td>10</td>
<td>4</td>
<td><a href="javascript:;" onclick="gettaskmodel('61','2024-03-29','2024-04-05',1,'ALL');"><u>6</u></a></td>
<td>
	60%
</td>
</tr>


<tr>
<td>% OF WORK DELAYED</td>

<td>4</td>
<td>2</td>
<td><a href="javascript:;" onclick="gettaskmodel('61','2024-03-29','2024-04-05',2,'ALL');">
	2<u></u></a></td>
<td>
	50%</td>

</tr>

<tr>
<td colspan="4" style="color:red; font-weight:bold;">AVERAGE DELAY 1 DAYS</td>
</tr>
<tr>
<td colspan="4"></td>
<td style="background-color: #efecec;">
	55%</td>
</tr>



</tbody>
</table>
<h4 class="page-title text-center">Task Details</h4>
<table class="table table-bordered text-center" style="font-size:15px;margin-top:20px;">
<thead>
<tr style="background-color: #f2f0ec;">
<th class="text-center">Task Detail</th>
<th class="text-center">Assigned To</th>
<th class="text-center">Planned Start/End Date</th>
<th class="text-center">TAT</th>
<th class="text-center">Assigned On</th>
<th class="text-center">Current Status</th>
<th class="text-center">Days Left</th>
<th class="text-center">Delay</th>
</tr>
</thead>
<tbody>
<tr>
<td>Release DF</td>
<td>Manoj Dubey</td>
<td>27 March 2024/28 March 2024</td>
<td>1 Day</td>
<td>28 March 2024</td>
<td>Completed</td>
<td>0</td>
<td>0</td>
</tr>
<tr>
<td>Creation of Proforma Invoice</td>
<td>Manoj Dubey</td>
<td>28 March/29 March 2024</td>
<td>1 Day</td>
<td>28 March 2024</td>
<td>In Process</td>
<td>0</td>
<td><strong style="color:red;font-weight: bold;">+6 Days</strong></td>
</tr>


</tbody>
</table>
</div>
		</li>

		</ul>



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
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- Datatable init js -->
<script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

<!-- App js -->
<script src="<?php echo assets_url;?>js/jquery.core.js"></script>
<script src="<?php echo assets_url;?>js/jquery.app.js"></script>
</body>
<script type="text/javascript"> 

function gettaskmodel(user_id,sdate,edate,flag,dfid)
{
if(flag==1)
{
var dr="Work Not Done";
}else 
{
var dr="Work done by Delayed";
}
$.ajax({
url: '<?php echo page_url;?>MIS/gettaskdata',
type: 'post',
data: {user_id: user_id,sdate:sdate,edate:edate,flag:flag,dfid:dfid},
success: function(data){
$("#delegationModal").modal('show');
$("#deldata").html(data);
$("#type").text(dr);

}
});

}
</script>
<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
	   <script type="text/javascript">
            $( document ).ready(function() {
                    $('.multipleselect').select2();
            });
        </script>
        <script type="text/javascript">
  var Accordion = function() {
  
  var
    toggleItems,
    items;
  
  var _init = function() {
    toggleItems     = document.querySelectorAll('.accordion__itemTitleWrap');
    toggleItems     = Array.prototype.slice.call(toggleItems);
    items           = document.querySelectorAll('.accordion__item');
    items           = Array.prototype.slice.call(items);
    
    _addEventHandlers();
    TweenLite.set(items, {visibility:'visible'});
    TweenMax.staggerFrom(items, 0.9,{opacity:0, x:-100, ease:Power2.easeOut}, 0.3)
  }
  
  var _addEventHandlers = function() {
    toggleItems.forEach(function(element, index) {
      element.addEventListener('click', _toggleItem, false);
    });
  }
  
  var _toggleItem = function() {
    var parent = this.parentNode;
    var content = parent.children[1];
    if(!parent.classList.contains('is-active')) {
      parent.classList.add('is-active');
      TweenLite.set(content, {height:'auto'})
      TweenLite.from(content, 0.6, {height: 0, immediateRender:false, ease: Back.easeOut})
      
    } else {
      parent.classList.remove('is-active');
      TweenLite.to(content, 0.3, {height: 0, immediateRender:false, ease: Power1.easeOut})
    }
  }
  
  return {
    init: _init
  }
  
}();

Accordion.init();

</script>
</html>	