<?php
$CI = &get_instance();
$CI->load->model('Salescrm_model');
$getLeadInfo = $CI->Salescrm_model->getLeadInfo($this->uri->segment(4));
$product_name = $CI->Salescrm_model->gettrialproductInfo($this->uri->segment(3));
$company_name = '';
$sales_person = '';

if($getLeadInfo != '') {
    foreach ($getLeadInfo as $rows);
    $company_name = $rows->company_name;
    $sales_person = $rows->first_name." ".$rows->last_name;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trial Readings</title>

    <script src="<?php echo assets_url;?>js/angular.min.js"></script>
     <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" >
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
        <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

    <style>
.bom{
    border: 1px solid black;
    height: 400px;
margin-top:125px;
    overflow-x: auto;
    padding: 0px;
}

.bom p{
    font-size: 20px;
    text-align: left;
    font-weight: 600;
}

table{
    padding: 5px;
   width: 100%;
  
}
.mandatoryclass{
    border-bottom: 1px solid red !important;
}

th{
    font-weight: 600;
    background-color: lightgray;
    border: 1px solid black;
    padding: 3px;
    text-align: center;
}

td{
   
    
    border: 1px solid black;
    padding: 3px;
    text-align: center;
}

select {
            width: 100%;
            border: none;
            outline: none;
        }
        
        .inp {
            border: none;
        
            outline: none;
            width: 100%;
        }

        textarea{
            resize: none;
        }


        
 .header-heading{
    background-color: lightgrey;
    text-align: center;
    color: white;
    padding: 0px;
    margin-bottom: 24px;
    position: fixed;
    width: 100%;
    top: 0;
    
    z-index: 100;
    
}

::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
  color: grey;
  opacity: 0.5; /* Firefox */
}

.header-heading h1{
    font-weight: 900;
    color: black;
    font-size: 24px;
    margin: 0;
} 

.bom-left{
       padding-top:20px;
       background-color:whitesmoke;
       padding: 1px;
       height:800px;
       overflow-y:auto;
       position: fixed;
    width: 24%;
     padding-bottom:20px; 
     margin-bottom:50px;
     margin-top:55px;
     
   }

   .bom-left h5{
       text-align:center;
       font-weight:900;
   }
   

    </style>
</head>
<body>

<div class="container-fluid">
<div class="row">
<div class="header-heading">
<div class="col-sm-2">

</div>
<div class="col-sm-10">
<div class="text-center">
    <h1>TRAIL READINGS</h1>
</div>

</div>
</div>
</div>
</div>

<div class="container-fluid" style="margin-top: 50px;">
    <div class="row">
      

       <div class="col-sm-4"></div>
            <div class="col-sm-4">
        <div class="card-box search-page" style="min-height: 160px;">
          

         

           <div class="row">
            <div class="col-sm-6 col-xs-6">
              <p><strong>Sales Contact's Person :-</strong><?php echo $sales_person;?></p>
            </div>
            <div class="col-sm-6 col-xs-6">
              <p></p>
            </div>
          </div>

          <div class="row">
            <div class="col-sm-6 col-xs-6">
              <p><strong>Company's name : </strong><?php echo $company_name;?></p>
            </div>
            <div class="col-sm-6 col-xs-6">
              <p></p>
            </div>
          </div>

           <div class="row">
            <div class="col-sm-6 col-xs-6">
              <p><strong>Product's Name : </strong><?php echo $product_name;?></p>
            </div>
            <div class="col-sm-6 col-xs-6">
              <p></p>
            </div>
          </div>

        

          <div class="clearfix"></div>
        </div>

      </div>

            <div class="col-sm-4">
        

      </div>
    </div>
</div>

<?php if($this->uri->segment(5) != 't4r4i4a4l') {?>
    <div class="container-fluid">
      <form action="<?php echo page_url;?>Sampling/add_trial_readings/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>" method="post" enctype="multipart/form-data" id="formid" onsubmit="return validatebom();">
 

      <div class="row">
      <div class="col-sm-12">
        <div class="bom" id="repeat" style="height: auto">
            <h4 class="text-center"><strong style="font-weight: 1000;">Add Trial Readings</strong></h4>
        <div class="card-box">
            <table id="my_table">
                <tr>
                    <th><div style="width: 50px;">Sr. No.</div></th>
                    <th><div style="width: 100px;">Date</div></th>
                    <th><div style="width: 100px;">Shift</div></th>
                    <th><div style="width: 100px;">Machine Name</div></th>
                    <th><div style="width: 100px;">Water Used</div></th>
                    <th><div style="width: 150px;">Oil Top Up</div></th>       
                    <th><div style="width: 150px;">Final Conc%</div></th>       
                    <th><div style="width: 150px;">Final Conc%</div></th>       
                    <th><div style="width: 150px;">Remarks if any</div></th>       
                </tr>

                <tbody class="partsrow">

     
                <?php
                $k=0;
                $q = $this->db->select('machine_name')->from('trial_readings')->where('lead_id',$this->uri->segment(4))->get();
                if($q->num_rows()>0){
                    foreach($q->result() as $rowss){
                
                ?>
                    <tr class="totalrows trow<?php echo $k;?>" data-id="<?php echo $k;?>">
                        <td><div class="col-md-12"><?php echo $k+1;?></div><div class="col-md-12"><a href="javascript:;" style="cursor:pointer;"  class="btn_add_row"><span class="btn btn-xs btn-success"><strong style="font-size:14px;">+</strong></span></div></td>
                         <td><input type="date" class="inp mandatoryclass" name="current_date[]" autocomplete="off"></td>
                         <td><select name="shift[]"  class="inp mandatoryclass"><option value=""></option><option value="Morning">Morning</option><option value="Evening">Evening</option></select></td>
                         <td><input type="text" class="inp mandatoryclass" name="machine_name[]" value="<?php echo $rowss->machine_name;?>" readonly autocomplete="off"></td>
                        <td><input type="text" class="inp mandatoryclass" name="water_used[]" autocomplete="off"></td>
                        <td><input type="text" class="inp mandatoryclass" name="oil_top_up[]" autocomplete="off"></td>
                        <td><input type="text" class="inp mandatoryclass" name="final_conc_1[]" autocomplete="off"></td>
                        <td><input type="text" class="inp mandatoryclass" name="final_conc_2[]" autocomplete="off"></td>
                        <td><textarea class="inp mandatoryclass" name="remarks[]"></textarea></td>
                        

                    </tr>
                <?php
               $k++; }
              }else{
                ?>



                <?php
                for($i=0;$i<1;$i++)
                {
                ?>
                    <tr class="totalrows trow<?php echo $i;?>" data-id="<?php echo $i;?>">
                        <td><div class="col-md-12"><?php echo $i+1;?></div><div class="col-md-12"><a href="javascript:;" style="cursor:pointer;"  class="btn_add_row"><span class="btn btn-xs btn-success"><strong style="font-size:14px;">+</strong></span></div></td>
                         <td><input type="date" class="inp mandatoryclass" name="current_date[]" autocomplete="off"></td>
                         <td><select name="shift[]"  class="inp mandatoryclass"><option value=""></option><option value="Morning">Morning</option><option value="Evening">Evening</option></select></td>
                         <td><input type="text" class="inp mandatoryclass" name="machine_name[]" autocomplete="off"></td>
                        <td><input type="text" class="inp mandatoryclass" name="water_used[]" autocomplete="off"></td>
                        <td><input type="text" class="inp mandatoryclass" name="oil_top_up[]" autocomplete="off"></td>
                        <td><input type="text" class="inp mandatoryclass" name="final_conc_1[]" autocomplete="off"></td>
                        <td><input type="text" class="inp mandatoryclass" name="final_conc_2[]" autocomplete="off"></td>
                        <td><textarea class="inp mandatoryclass" name="remarks[]"></textarea></td>
                        

                    </tr>
                <?php
                }
            }
                ?>
                </tbody>
            </table>
        </div>
        </div>
        </div>
    </div>
</div>
<div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <div class="form-group">
                <label><strong>Media</strong></label>
                 <input type="file" class="form-control" name="evidence" value="">
            </div>
        </div>
        <div class="col-md-4"></div>
</div>
    <div class="row">
        <div class="col-md-5">
           
        </div>
        <div class="col-md-2">
            <div class="text-center">
                  <input type="submit" class="btn btn-success" style="width:100%; margin-top:10px;" value="Submit">
            </div>
        </div>
    </div>
<?php } ?>
<div style="clear:both;height:30px"></div>

</form>
</div>

        <!-- <div class="wrapper"> -->
            <div class="container-fluid">
                <!-- Page-Title -->

              <div class="row">
                <div class="col-sm-12">
                    <h4 class="page-title text-center">Trial Readings</h4>
                </div>
              </div>

               <div class="row">
                        <form action="<?php echo page_url;?>Sampling/filtertrial/<?php echo $this->uri->segment(3)?>/<?php echo $this->uri->segment(4);?>" method="post">
                        <div class="col-sm-12">
                        <div class="col-sm-3"></div>
                        <div class="col-sm-6 card-box">
                        <div class="col-md-6">
                        <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" class="form-control" name="start_date" id="start_date" required value="<?php echo $this->uri->segment(5);?>">
                        </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
                        <label>End Date</label>
                        <input type="date" class="form-control" name="end_date" id="end_date" required value="<?php echo $this->uri->segment(6);?>">
                        </div>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4 text-center"><input type="submit" class="btn btn-success" value="Filter"></div>


                        </div>

                        </div>
                        </form>
              </div>


              <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
              <div class="row">
                <div class="col-sm-12">
                  <div class="card-box table-responsive">
                    <table id="example2" class="table table-striped table-bordered manglesh">
                      <thead>
                        <tr>
                          <th>SR NO.</th>
                          <th>DATE</th>
                          <th>SHIFT</th>
                          <th>MACHINE NAME</th>
                          <th>WATER USED</th>
                          <th>OIL TOP-UP</th>
                          <th>FINAL CONC%</th>
                          <th>FINAL CONC%</th>
                          <th>REMARKS</th>    
                          <?php if($this->uri->segment(5) != 't4r4i4a4l') {?>              
                          <th>ACTION</th>  
                          <?php } ?> 
                          <th>Media</th>               
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>



        <!-- Modal -->
        <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <form action="<?php echo page_url;?>Sampling/update_reading/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>" method="post">
          <input type="hidden" name="reading_id" id="reading_id" value="">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">EDIT READING</h4>
        </div>
        <div class="modal-body">
       <div class="row">
         <div class="col-md-12">
            <div class="col-md-2">
                <div class="form-group">
                    <label>Date</label>
                    <input type="text" class="form-control" class="mandatoryclass" id="current_date" name="current_date" autocomplete="off">
                </div>
            </div>

             <div class="col-md-2">
                <div class="form-group">
                    <label>Shift</label>
                   <select name="shift" id="shifts" class="form-control" required>
                    <option value=""></option>
                  <option value="Morning">Morning</option>
                  <option value="Evening">Evening</option>
              </select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Machine Name</label>
                    <input type="text" class="form-control" class="mandatoryclass" id="machine_name" name="machine_name" autocomplete="off">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Water Used</label>
                    <input type="text" class="form-control" class="mandatoryclass" id="water_used" name="water_used" autocomplete="off">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Oil Top-Up</label>
                    <input type="text" class="form-control" class="mandatoryclass" id="oil_top_up" name="oil_top_up" autocomplete="off">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Final Conc%</label>
                    <input type="text" class="form-control" class="mandatoryclass" id="final_conc_1" name="final_conc_1" autocomplete="off">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Final Conc%</label>
                    <input type="text" class="form-control" class="mandatoryclass" id="final_conc_2" name="final_conc_2" autocomplete="off">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Remarks</label>
                    <textarea class="form-control" class="mandatoryclass" id="remarks" name="remarks"></textarea>
                </div>
            </div>
         </div>
       </div>
        </div>
        <div class="modal-footer">
        <input type="submit" name="sub" value="Submit" class="btn btn-success">
        </div>
        </div>
       </form>

        </div>
        </div>
          

               <?php $this->load->view('common/footer');?>

                <!-- End Footer -->



            </div> <!-- end container -->

        <!-- </div> -->


      <!-- Footer -->
<?php $this->load->view('common/footer');?>
                <!-- End Footer -->

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
        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
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
        <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
        <script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>

 <script type="text/javascript">
            $(document).ready(function(){
                jQuery('#current_date').datepicker();
                var l=1;
                var i=2;
                $('.btn_add_row').click(function(){ 

                $('.partsrow').append(' <tr class="totalrows trow'+l+'" data-id="'+l+'"> <td><div class="col-md-12">'+i+'</div><div class="col-md-12"><a href="javascript:;" style="cursor:pointer;"  class="btn_remove_row" id="'+l+'"><span class="btn btn-xs btn-success"><strong style="font-size:14px;">-</strong></span></div></td><td><input type="date" class="inp mandatoryclass" name="current_date[]" autocomplete="off"></td><td><select name="shift[]"  class="inp mandatoryclass"><option value=""></option><option value="Morning">Morning</option><option value="Evening">Evening</option></select></td><td><input type="text" class="inp mandatoryclass" name="machine_name[]" autocomplete="off"></td><td><input type="text" class="inp mandatoryclass" name="water_used[]" autocomplete="off"></td><td><input type="text" class="inp mandatoryclass" name="oil_top_up[]" autocomplete="off"></td><td><input type="text" class="inp mandatoryclass" name="final_conc_1[]" autocomplete="off"></td><td><input type="text" class="inp mandatoryclass" name="final_conc_2[]" autocomplete="off"></td><td><textarea class="inp mandatoryclass" name="remarks[]"></textarea></td></tr>');

                l++;
                i++;
                });

                $(document).on('click', '.btn_remove_row', function(){
                    var button_id = $(this).attr("id");

                    if(confirm('Do You really want to delete the row?'))
                    {
                    $('.trow'+button_id).remove();
                    }
                        

                    });


            var cname="<?php echo str_replace(" ","_",$company_name);?>";
            var product="<?php echo str_replace(" ","_",$product_name);?>";
            var flag5="<?php echo $this->uri->segment(5);?>";
            var flag6="<?php echo $this->uri->segment(6);?>";


                $('#example2').dataTable({
                     "bProcessing": false,
                     "pagination":true,
                     fixedHeader: true,
                    "bSort": false,
                     fixedColumns:   {
                      leftColumns: 3
                      },
                       dom: 'lBfrtip',
                       "buttons": [
   { 
      extend: 'excel', 
      title: 'TRAIL_DATA_'+cname+'_'+product+'_'+flag5+'_'+flag6,
      exportOptions: {
        rows: ':visible'
      } 
   } 
],

                     "sAjaxSource": "<?php echo page_url;?>Sampling/trial_readings_list/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(6);?>",


                     "aoColumns": [

                                    { mData: 'sr_no' },
                                    { mData: 'date' },
                                    { mData: 'shift' },
                                    { mData: 'machine_name' },
                                    { mData: 'water_used' },
                                    { mData: 'oil_topup' },
                                    { mData: 'final_conc_1' },
                                    { mData: 'final_conc_2' },
                                    { mData: 'remarks' }
                                    <?php if($this->uri->segment(5) != 't4r4i4a4l') {?>
                                   ,{ mData: 'action' }
                                    <?php } ?>
                                    ,{mData:'media'}
                                ]

                    });
                    
            });

            function validatebom() {
                var isValid=0;
                $(".mandatoryclass").each(function() {
                var element = $(this).val();
                if (element=="") {

                isValid=1;
                }
                });


                if(isValid==0)
                {
                return true;
                }else
                {
                    // alert($(this));
                alert('All Fields marked with red line are mandatory');
                return false;
                }
            }

              function edit_reading(id)
              {
                $("#myModal").modal('show');
                $("#reading_id").val(id);

                 $.ajax({
                    type: "post",
                    url: "<?php echo page_url;?>Sampling/get_readings",
                    data: {id: id},
                    success:function(data) {
                        var arr = data.split('|');
                        $('#current_date').val(arr[0]);
                        $('#machine_name').val(arr[1]);
                        $('#water_used').val(arr[2]);
                        $('#oil_top_up').val(arr[3]);
                        $('#final_conc_1').val(arr[4]);
                        $('#final_conc_2').val(arr[5]);
                       
                        $('#remarks').val(arr[6]);
                       
                        $('#shifts option[value="'+arr[7]+'"]').attr("selected", "selected");
                    }
                    
                });

              }

        </script>
 </script>
</body>
</html>