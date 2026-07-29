<?php 
$CI =& get_instance();
$CI->load->model('Task_model');
$alldf=$CI->Task_model->get_all_df_details();
?>
<!DOCTYPE html>
<html><head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="<?php echo copyright; ?>">
<link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
<title><?php echo sitetitle; ?>Payment Terms</title>
<!-- Table Responsive css -->
<script src="<?php echo assets_url; ?>js/angular.min.js"></script>
<!-- DataTables -->
<link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
<link href="
https://cdn.jsdelivr.net/npm/bootoast@1.1.4/dist/bootoast.min.css
" rel="stylesheet">
<link href="<?php echo dashboard_asset_url; ?>dashboard.css" rel="stylesheet" type="text/css">


<!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
<script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
<style>
#chart-container{
height: 800px; 
}

.colour-box {
    display: inline-block;
    height: 20px;
    width: 20px;
    border: 1px solid #000;
}
.c1 {
    background: #B2FBA5;
}

.c11 {
    background-color: #B2FBA5;
}


.colour-box1 {
    display: inline-block;
    height: 20px;
    width: 20px;
    border: 1px solid #000;
}
.c2 {
    background: #FFA6A1;
}

.c22 {
    background-color: #FFA6A1;
}
.colour-box2 {
    display: inline-block;
    height: 20px;
    width: 20px;
    border: 1px solid hsl(200,100%,45%);
}
.c3 {
    background: hsl(202deg 93% 94%);
}

.c33 {
    background-color:hsl(202deg 93% 94%) ;
}

.colour-box1 {
    display: inline-block;
    height: 20px;
    width: 20px;
    border: 1px solid hsl(57deg 98% 47%);
}


table.pretty thead th {
text-align: center;
background:#a6c9da;
color:#fff;
font-size:12px;
}
table.pretty td {
text-align: center;
font-size:12px;
}

.main__box {
box-shadow: 1px 1px 10px #e5e5e5;
/*background-color: #fff;*/
padding: 10px;
border-radius: 0;
margin-top: 20px;
}

.main__box h2 {
font-size: 25px;
font-weight: 900;
text-align: center;
margin: 0;
}


.main__box p {
font-size: 12px;
margin: 0;
text-transform: capitalize;
color: #000;
/* margin-top: 10px; */
font-weight: 600;
}

.num_blue {
color: #1290ff;
}

.num_orange {
color: orange;
}

.num_yellow {
color: #fad44a;
}

.num_green {
color: #6ac069;
}

.tab {
overflow: hidden;
border-bottom: 1px solid #e5e5e5;
}

/* Style the buttons inside the tab */
.tab button {
float: left;
border: none;
outline: none;
cursor: pointer;
padding: 14px 16px;
transition: 0.3s;
font-size: 12px;
font-weight: 600;
text-align: center;
background-color: #fff;
margin-right: 0px;
color: #000;
width: 100%;
}

.tab button:hover {
color: #000;
background-color: whitesmoke;
}

.tab button.active {
background-color: #ecf8fe;
color: #44c2e9;
border-bottom: 1px solid #44c2e9;
}

.tab button img{
width: 30px;
}

.tabcontent {
display: none;
padding: 6px 12px;
}


.counter {
font-size: 12px;
background-color: #44c2e9;
color: #fff;
padding: 2px 4px;
border-radius: 5px;
margin-left: 10px;
}



.table__header {
margin-top: 15px;
}

.tabcontent table {
border: 1px solid #e5e5e5;
}

.tabcontent table tr th {
color: #000;
text-align: center;
vertical-align: middle;
border: 1px solid #e5e5e5;

}

.tabcontent table tr td {
border: 1px solid #e5e5e5;
text-align: center;
/*background-color: #fff;*/
color: #000;
}

.done_btn {
padding: 0.26rem 0.5rem;
border-radius: 0.25rem;
background-color: #e9f8f4;
color: #26bf94;
border: 1px solid #26bf94;
font-weight: 600;
}

.done_btn:hover {
background-color: #26bf94;
color: #fff;
}



.received_btn {
padding: 0.26rem 0.5rem;
border-radius: 0.25rem;
background-color: #f2eefc;
color: #845adf;
border: 1px solid #845adf;
font-weight: 600;
}

.received_btn:hover {
background-color: #845adf;
color: #fff;
}

.reminder h6{
font-size: 16px;
color: #000;
padding-bottom: 10px;
border-bottom: 1px solid #e5e5e5;
font-weight: 600;
}

.top_box {
border: 1px solid #e5e5e5;
background-color: whitesmoke;
padding: 10px;
border-radius: 15px 15px 0px 15px;
}

.top_box p{
font-size: 11px;
margin: 0;
}



.time_btn p{
font-size: 11px;
font-weight: 600;
text-align: right;
margin: 0;
}


.check_btn {
padding: 2px 3px;
border-radius: 0.25rem;
background-color: #e9f8f4;
color: #26bf94;
border: 1px solid #26bf94;
font-weight: 600;
}

.check_btn:hover {
background-color: #26bf94;
color: #fff;
} 

.cross_btn {
padding: 2px 3px;
border-radius: 0.25rem;
background-color: #fcedeb;
color: #e7533c;
border: 1px solid #e7533c;
font-weight: 600;
}

.cross_btn:hover {
background-color: #e7533c;
color: #fff;
}

.time_btn{
margin-top: 4px;
}

#timer{
color: red;
}

#timer1{
color: red;
}

.read_box{
margin-bottom: 20px;
}
</style>

</head>


<body>


<!-- Navigation Bar-->
<header id="topnav">
<?php $this->load->view('common/nav-menu'); ?>
</header>
<!-- End Navigation Bar-->


<div class="wrapper">
<div class="container-fluid">
<div class="row">
<div class="col-md-12 card-box" style="background-color:aliceblue;">
<div class="col-md-12">
<h3 class="page-title text-center" style="font-weight: 600;color:black;">DF Task Progress</h3>
</div>


<div class="col-md-12">
<div class="col-md-3"></div>
<div class="col-md-6">
<div class="form-group">
<select class="form-control" id="df_no" name="df_no" onchange="filter_df();">
<option value="">Select</option>
<?php 
if($alldf!='')
{
    foreach($alldf as $row)
    { 
?>
<option value="<?php echo $row->id;?>" <?php if($this->uri->segment(3)==$row->id){?> selected <?php } ?>><?php echo $row->df_no;?></option>
<?php
} 
} 
?>

</select>
</div>
</div>
</div>

<?php if($this->uri->segment(3)<>'')
{
    $departments=$CI->Task_model->get_departments($this->uri->segment(3));
    $date_category=$CI->Task_model->getTaskdateIntervals($this->uri->segment(3));
    $dc=explode('~',$date_category);
     $task_data=$CI->Task_model->getTaskStatus($this->uri->segment(3));
     $header_text=$CI->Task_model->getTaskHead1($this->uri->segment(3));
     $header_text1=$CI->Task_model->getHODName($this->uri->segment(3));




?>
<div class="col-md-12"><div id="chart-container"></div></div>
<?php } ?>
</div>
</div>
<!-- Footer -->
<?php $this->load->view('common/footer'); ?>
<!-- End Footer -->

</div> <!-- end container -->
</div>
<!-- end wrapper -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<!-- jQuery  -->
<!-- <script src="<?php echo assets_url; ?>js/jquery.min.js"></script> -->
<script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>js/detect.js"></script>
<script src="<?php echo assets_url; ?>js/fastclick.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.slimscroll.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.blockUI.js"></script>
<script src="<?php echo assets_url; ?>js/waves.js"></script>
<script src="<?php echo assets_url; ?>js/wow.min.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.nicescroll.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.scrollTo.min.js"></script>

<!-- Datatables-->

<script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script>

<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.bootstrap.js"></script>

<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.buttons.min.js"></script>

<script src="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.js"></script>

<script src="<?php echo assets_url; ?>plugins/datatables/jszip.min.js"></script>

<script src="<?php echo assets_url; ?>plugins/datatables/pdfmake.min.js"></script>

<script src="<?php echo assets_url; ?>plugins/datatables/vfs_fonts.js"></script>

<script src="<?php echo assets_url; ?>plugins/datatables/buttons.html5.min.js"></script>

<script src="<?php echo assets_url; ?>plugins/datatables/buttons.print.min.js"></script>

<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.fixedHeader.min.js"></script>

<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.keyTable.min.js"></script>

<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.responsive.min.js"></script>

<script src="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.scroller.min.js"></script>
<!-- Datatable init js -->
<script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>
<!-- App js -->
<script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
<script src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
<script>
function filter_df()
{
    var df=$("#df_no").val();
    if(df!='')
    {
        document.location="<?php echo page_url;?>Task/dfgantchart/"+df;
    }
}
</script>
<?php if($this->uri->segment(3)<>'')
{ ?>

 <!--    <script>
const dataSource = {
chart: {
dateformat: "dd/mm/yyyy",
caption: "Planned vs Actual tasks (with delays)",
subcaption: "Delays are marked in Red",
plottooltext: "<b>$label</b><br>Start: <b>$start</b><br>End: <b>$end</b><br>$PercentComplete progress",
theme: "fusion",
slackFillColor: "#ECF2F7"
},
// connectors: [
// {
// connector: [
// {
// fromtaskid: "1-1",
// totaskid: "3-1",
// color: "#C0C7D0",
// thickness: "2"
// },
// {
// fromtaskid: "2-1",
// totaskid: "3-1",
// color: "#C0C7D0",
// thickness: "2"
// },
// {
// fromtaskid: "3-1",
// totaskid: "4-1",
// color: "#C0C7D0",
// thickness: "2"
// },
// {
// fromtaskid: "5-1",
// totaskid: "7-1",
// color: "#C0C7D0",
// thickness: "2"
// },
// {
// fromtaskid: "6-1",
// totaskid: "7-1",
// color: "#C0C7D0",
// thickness: "2"
// }
// ]
// }
// ],
legend: {
item: [
{
label: "Planned",
color: "#39A7FF"
},
{
label: "Actual",
color: "#5DD99B"
},
{
label: "Slack (Delay)",
color: "#e44a00"
}
]
},
tasks: {
color: "#008000",
task: [
{
label: "Planned",
processid: "1",
start: "5/4/2018",
end: "12/4/2018",
id: "1-1",
color: "#39A7FF",
alpha: "100",
height: "27%",
toppadding: "32%"
},
{
label: "Actual",
processid: "1",
start: "5/4/2018",
end: "12/4/2018",
id: "1",
color: "#5DD99B",
alpha: "100",
height: "27%",
toppadding: "65%",
percentcomplete: "0"
},
{
label: "Planned",
processid: "2",
start: "10/4/2018",
end: "20/4/2018",
id: "2-1",
color: "#39A7FF",
alpha: "100",
height: "27%",
toppadding: "32%"
},
{
label: "Actual",
processid: "2",
start: "10/4/2018",
end: "22/4/2018",
id: "2",
color: "#5DD99B",
alpha: "100",
height: "27%",
toppadding: "65%",
percentcomplete: "30"
},
{
label: "Delay",
processid: "2",
start: "20/4/2018",
end: "22/4/2018",
id: "2-2",
color: "#e44a00",
alpha: "100",
height: "27%",
toppadding: "65%",
tooltext: "Delayed by 2 days."
},
{
label: "Planned",
processid: "3",
start: "21/4/2018",
end: "30/4/2018",
id: "3-1",
color: "#39A7FF",
alpha: "100",
height: "27%",
toppadding: "32%"
},
{
label: "Actual",
processid: "3",
start: "22/4/2018",
end: "1/5/2018",
id: "3",
color: "#5DD99B",
alpha: "100",
height: "27%",
toppadding: "65%",
percentcomplete: "40"
},
{
label: "Delay",
processid: "3",
start: "30/4/2018",
end: "1/5/2018",
id: "3-2",
color: "#e44a00",
alpha: "100",
height: "27%",
toppadding: "65%",
tooltext: "Delayed by 1 day"
},
{
label: "Planned",
processid: "4",
start: "02/5/2018",
end: "10/5/2018",
id: "4-1",
color: "#39A7FF",
alpha: "100",
height: "27%",
toppadding: "32%"
},
{
label: "Actual",
processid: "4",
start: "4/5/2018",
end: "10/5/2018",
id: "4",
color: "#5DD99B",
alpha: "100",
height: "27%",
toppadding: "65%"
},
{
label: "Planned",
processid: "5",
start: "5/5/2018",
end: "16/5/2018",
id: "5-1",
color: "#39A7FF",
alpha: "100",
height: "27%",
toppadding: "32%"
},
{
label: "Actual",
processid: "5",
start: "6/5/2018",
end: "16/5/2018",
id: "5",
color: "#5DD99B",
alpha: "100",
height: "27%",
toppadding: "65%"
},
{
label: "Planned",
processid: "6",
start: "16/5/2018",
end: "27/5/2018",
id: "6-1",
color: "#39A7FF",
alpha: "100",
height: "27%",
toppadding: "32%"
},
{
label: "Actual",
processid: "6",
start: "15/5/2018",
end: "31/5/2018",
id: "6",
color: "#5DD99B",
alpha: "100",
height: "27%",
toppadding: "65%"
},
{
label: "Delay",
processid: "6",
start: "27/5/2018",
end: "1/6/2018",
id: "6-2",
color: "#e44a00",
alpha: "100",
height: "27%",
toppadding: "65%",
tooltext: "Delayed by 4 days"
},
{
label: "Planned",
processid: "7",
start: "1/6/2018",
end: "12/5/2018",
id: "7-1",
color: "#39A7FF",
alpha: "100",
height: "27%",
toppadding: "32%"
},
{
label: "Actual",
processid: "7",
start: "1/6/2018",
end: "12/5/2018",
id: "7",
color: "#5DD99B",
alpha: "100",
height: "27%",
toppadding: "65%"
},
{
label: "Planned",
processid: "8",
start: "12/6/2018",
end: "20/6/2018",
id: "8-1",
color: "#39A7FF",
alpha: "100",
height: "27%",
toppadding: "32%"
},
{
label: "Actual",
processid: "8",
start: "12/6/2018",
end: "19/6/2018",
id: "8",
color: "#5DD99B",
alpha: "100",
height: "27%",
toppadding: "65%"
},
{
label: "Planned",
processid: "9",
start: "20/6/2018",
end: "27/6/2018",
id: "9-1",
color: "#39A7FF",
alpha: "100",
height: "27%",
toppadding: "32%"
},
{
label: "Actual",
processid: "9",
start: "20/6/2018",
end: "30/6/2018",
id: "9",
color: "#5DD99B",
alpha: "100",
height: "27%",
toppadding: "65%"
}
]
},
processes: {
headertext: "Department",
align: "left",
process: [
{
label: "Marketing",
id: "1"
},
{
label: "Design",
id: "2"
},
{
label: "Production PPC",
id: "3"
},
{
label: "Assembly",
id: "4"
},
{
label: "Electrical",
id: "5"
},
{
label: "Automation",
id: "6"
},
{
label: "Trial",
id: "7"
},
{
label: "Dispatch",
id: "8"
}
]
},
categories: [
{
category: [
{
start: "1/4/2018",
end: "30/4/2018",
label: "April"
},
{
start: "1/5/2018",
end: "31/5/2018",
label: "May"
},
{
start: "1/6/2018",
end: "30/6/2018",
label: "June"
}
]
},
{
category: [
{
start: "1/4/2018",
end: "7/4/2018",
label: "W 1"
},
{
start: "8/4/2018",
end: "14/4/2018",
label: "W 2"
},
{
start: "15/4/2018",
end: "21/4/2018",
label: "W 3"
},
{
start: "22/4/2018",
end: "28/4/2018",
label: "W 4"
},
{
start: "29/4/2018",
end: "5/5/2018",
label: "W 5"
},
{
start: "6/5/2018",
end: "12/5/2018",
label: "W 6"
},
{
start: "13/5/2018",
end: "19/5/2018",
label: "W 7"
},
{
start: "20/5/2018",
end: "26/5/2018",
label: "W 8"
},
{
start: "27/5/2018",
end: "2/6/2018",
label: "W 9"
},
{
start: "3/6/2018",
end: "9/6/2018",
label: "W 10"
},
{
start: "10/6/2018",
end: "16/6/2018",
label: "W 11"
},
{
start: "17/6/2018",
end: "23/6/2018",
label: "W 12"
},
{
start: "24/6/2018",
end: "30/6/2018",
label: "W 13"
}
]
}
]
};

FusionCharts.ready(function() {
var myChart = new FusionCharts({
type: "gantt",
renderAt: "chart-container",
width: "100%",
height: "100%",
dataFormat: "json",
dataSource,
events: {
"dataplotClick": function(event, args) {
console.log(args);
},
"processClick": function(event, args) {  
console.log(args);
}
}
}).render();
});

</script> -->
 <script>
const dataSource = {
chart: {
dateformat: "dd/mm/yyyy",
caption: "Planned vs Actual tasks (with delays)",
subcaption: "Delays are marked in Red",
plottooltext: "<b>$label</b><br>Start: <b>$start</b><br>End: <b>$end</b><br>$PercentComplete progress",
"showToolTipShadow":1,
theme: "fusion",
slackFillColor: "#ECF2F7"
},
legend: {
item: [
{
label: "Planned",
color: "#39A7FF"
},
{
label: "Actual",
color: "#5DD99B"
},
{
label: "Slack (Delay)",
color: "#e44a00"
}
]
},

"datatable": {
          "showprocessname": "1",
          "namealign": "left",
          "fontcolor": "#000000",
          "fontsize": "10",
          "valign": "right",
          "align": "center",
          "headervalign": "bottom",
          "headeralign": "center",
          "headerbgcolor": "#008ee4",
          "headerfontcolor": "#ffffff",
          "headerfontsize": "14",
          "datacolumn": [{
              "bgcolor": "#eeeeee",
              "isBold":1,
              "headertext": "PLN.",
              "text": <?php echo $header_text;?>
            },
            {
              "bgcolor": "#eeeeee",
              "isBold":1,
              "headertext": "HOD",
              "text": <?php echo $header_text1;?>
            }
          ]
        },
tasks: {
color: "#008000",
task:<?php echo $task_data;?>
},
processes: {
headertext: "Department",
align: "left",
process: <?php echo $departments;?>
},
categories: [
{
category: <?php echo $dc[0];?>
},
{
category: <?php echo $dc[1];?>
}
]
};

FusionCharts.ready(function() {
var myChart = new FusionCharts({
type: "gantt",
renderAt: "chart-container",
width: "100%",
height: "100%",
dataFormat: "json",
dataSource,
events: {
"dataplotClick": function(event, args) {
console.log(args);
},
"processClick": function(event, args) {  
console.log(args);
}
}
}).render();
});

</script> 
<?php } ?>
</body>
</html>