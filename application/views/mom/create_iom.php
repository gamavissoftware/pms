<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php echo sitetitle; ?> Create IOM</title>
        
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
        <link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet"/>
        
        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script>
        
        <style>
            input.largerCheckbox { transform : scale(2); } 
        </style>    
    </head>

    <body>
        <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>

        <div class="wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <h4 class="page-title text-center">CREATE MOM</h4>
                        </div>
                        <div class="col-md-12">
                            <?php echo $this->session->flashdata('message');?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">
                        <form id="loginForm" method="post" action="<?php echo page_url;?>Mom/createiom" enctype="multipart/form-data" onsubmit="return validation();">
                        
                             <div class="row">
                               <?php 
                                $first_name =$this->session->userdata['logged_in']['user_name'];    
                                $last_name =$this->session->userdata['logged_in']['last_name'];
                                ?> 
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>DF Type <span style="color:red;" id="error_dftype">*</span></label>
                                            <select class="form-control" name="dftype" id="dftype" onchange="showhidedfbox();" required>
                                                <option value=""> Select DF Type</option>
                                                <option value="1">Single DF Discussion</option>
                                                <option value="2">Multiple DF Discussion</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4" id="showparticulardfdiv" style="display: none;">
                                        <div class="form-group">
                                            <label>DF No. <span style="color:red;" id="error_dfno">*</span></label>
                                            <select class="form-control select2" name="dfno" id="dfno" onchange="getcompanydetail();">
                                                <option value="">Select DF</option>
                                                <?php 
                                                    $this->db->select('id, df_no, df_description')->from('df_release');
                                                    $q = $this->db->where('df_status',0)->get();
                                                    foreach($q->result() as $row){
                                                ?>
                                                <option value="<?php echo $row->id;?>"><?php echo $row->df_no." ".$row->df_description;?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4" id="companydiv" style="display: none;">
                                        <div class="form-group">
                                            <label>Company Name</label>
                                            <select class="form-control" name="companyname" id="companyname"></select>
                                        </div>
                                    </div>

                                <div class="col-md-4"></div>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <label>Agenda of Meeting <span style="color:red;" id="error_agenda">*</span></label>
                                        <?php echo form_error('agenda'); ?>
                                        <input type="text" class="form-control" name="agenda" id="agenda" value="" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Attachment (if Any)</label>
                                        <input type="file" class="form-control" name="attachment" id="attachment">
                                    </div>
                                </div>
                                 <div class="col-md-3">
                                        <div class="form-group">
                                             <label for="field-2" class="control-label">Employee Name</label>
                                             <span id="error_employee_name" style="color:red;">*</span>
                                             <input type="text" class="form-control" name="employee_name" id="employee_name" value="<?php echo $first_name." ".$last_name;?>" readonly>
                                        </div>
                                    </div>
                                    
                                     <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Date</label>
                                            <span id="error_material_out_date" style="color:red;">*</span>
                                            <input type="date" id="date" name="date" class="form-control" autocomplete="off" value="<?php echo date('d-m-Y');?>" required min="<?php echo date('Y-m-d',strtotime('-7 Days'))?>">
                                        </div>
                                    </div>
                                     <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Time</label>
                                            <span id="error_material_out_date" style="color:red;">*</span>
                                            <input type="time" id="time" name="time" class="form-control" autocomplete="off" value="<?php echo date('h:i A');?>" required>
                                        </div>
                                    </div>
                                     <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Participant</label>
                                            <?php echo form_error('participant'); ?>
                                            <span id="error_participant" style="color:red;">*</span>
                                           <select multiple class="select2" name="participant[]" id="participant" required>
                                           <?php $q = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_status','1')->where('hide_profile','0')->where('business_location',2)->get();
                                           foreach($q->result() as $row){?>
                                           <option value="<?php echo $row->user_id;?>"><?php echo ucwords(strtolower($row->first_name." ".$row->last_name));?></option>
                                           <?php }?>
                                           </select>
                                        </div>
                                    </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Customer Participant (Email ID) <span style="color:red;">(If you have multiple Email Please add with Comma)</span></label>
                                                <input type="text" class="form-control" name="customerparticipant" id="customerparticipant" value="">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Task</label>
                                                <input type="text" class="form-control" name="description[]" id="description" placeholder="Add Your Task Here.." value="">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Due Date <span style="color:red">*</span></label>
                                                <input type="date" class="form-control" name="due_date[]" id="due_date" value="<?php echo date('Y-m-d');?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                 <label>Department</label>
                                                 <select class="form-control" id="department1" name="department[]" onchange="getusers(1);">
                                                    <option value="">--SELECT DEPARTMENT--</option>
                                                    <?php $query = $this->db->select('department_id,status,department')->from('departments')->where('status','1')->where('business_loc_id',2)->get();
                                                    foreach($query->result() as $department){?>
                                                    <option value="<?php echo $department->department_id;?>"><?php echo strtoupper($department->department);?></option>
                                                    <?php }?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                 <label>Accountable Person</label>
                                                 <select class="form-control select3" id="username1" name="username[]">
                                                    <option value="">--CHOOSE--</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-2 particulardfdiv" style="display:none" >
                                            <div class="form-group">
                                                <label>DF No</label>
                                                <select class="form-control select2" name="particulardfno[]" id="particulardfno">
                                                    <option value="">Select DF</option>
                                                    <?php 
                                                        $this->db->select('id, df_no, df_description')->from('df_release');
                                                        $q = $this->db->where('df_status',0)->get();
                                                        foreach($q->result() as $row){
                                                    ?>
                                                    <option value="<?php echo $row->id;?>"><?php echo $row->df_no." ".$row->df_description;?></option>
                                                    <?php }?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-1">
                                            <div class="form-group" style="padding-top:23px">
                                                <button type="button" class="btn btn-warning" name="add" id="addmore_btn1"><i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div id="dynamictasks1"></div>
                                    
                                    <div class="row">
                                        <hr>
                                        <div class="col-md-12">
                                            <div class="form-group pull-right">
                                                <input type="submit" id="save" class="btn btn-info" value="Submit">
                                            </div>                                              
                                        </div>
                                    </div>
                                            
                            </form>            
                        </div> 
                    </div>
                </div>
                
                <?php $this->load->view('common/footer');?>

            </div> 
        </div>

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
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
        <script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

        <script type="text/javascript">
            // Define global functions first
            function showhidedfbox(){
                var dftype = $("#dftype").val();
                if(dftype==1){
                    $("#showparticulardfdiv").show();
                    $("#companydiv").show();
                    $(".particulardfdiv").hide();
                }else if(dftype==2){
                    $(".particulardfdiv").show();
                    $("#showparticulardfdiv").hide();
                    $("#companydiv").hide();
                }else{
                    $(".particulardfdiv").hide();
                    $("#showparticulardfdiv").hide();
                    $("#companydiv").hide();
                }
            }

            function getcompanydetail(){
                var dfno = $("#dfno").val();
                $.ajax({
                    type:"post",
                    url:"<?php echo page_url;?>Mom/getcompanyinfo",
                    data:"dfno="+dfno,
                    success:function(data){
                        $("#companyname").html(data);
                    }
                });
            }

            function getusers(i){
                var department=$("#department"+i).val();
                $.ajax({
                    type:"post",
                    url:"<?php echo page_url;?>Mom/user_list",
                    data:"department="+department,
                    success:function(data){
                        $("#username"+i).html(data);
                    }
                });
            }

            function validation(){
                var participant = $("#participant").val();
                var agenda = $("#agenda").val();
                var dftype = $("#dftype").val();

                if(participant == null || participant == ''){
                    $("#error_participant").html('Required!');
                    return false;
                }
                if(agenda == ''){
                    $("#error_agenda").html('Required!');
                    return false;
                }
                if(dftype == ''){
                    $("#error_dftype").html('Required!');
                    return false;
                }
                return true;
            }

            $(document).ready(function() {
                // Initialize Select2
                $('.select2').select2({ width: '100%' });

                // Validation on submit click
                $("#save").click(function() {
                    var description = $("#description").val();
                    if(description == '') {
                        $("#error_description").html('Required!');
                        return false;
                    }
                });
            });
        </script>

        <script type="text/javascript">
            $(document).ready(function(){
                var i = 2;

                // -------------------------------------------------------------
                // 1. Fetch PHP Data into Clean JSON Objects (Safe from Syntax Errors)
                // -------------------------------------------------------------
                <?php 
                    // Fetch Departments
                    $dept_query = $this->db->select('department_id,status,department')->from('departments')->where('status','1')->where('business_loc_id',2)->get();
                    $dept_array = $dept_query->result_array();
                    
                    // Fetch DF List
                    $this->db->select('id, df_no, df_description')->from('df_release');
                    $df_query = $this->db->where('df_status',0)->get(); 
                    $df_array = $df_query->result_array();
                ?>

                // Convert PHP Array to JS Object using json_encode
                var deptData = <?php echo json_encode($dept_array); ?>;
                var dfData = <?php echo json_encode($df_array); ?>;

                // -------------------------------------------------------------
                // 2. Click Event
                // -------------------------------------------------------------
                $('#addmore_btn1').click(function(){
                    i++;
                    
                    // Build Department Options from JSON
                    var deptOptions = '<option value="">--SELECT DEPARTMENT--</option>';
                    if(deptData){
                        $.each(deptData, function(key, value){
                            deptOptions += '<option value="'+value.department_id+'">'+value.department.toUpperCase()+'</option>';
                        });
                    }

                    // Build DF Options from JSON
                    var dfOptions = '<option value="">Select DF</option>';
                    if(dfData){
                        $.each(dfData, function(key, value){
                            dfOptions += '<option value="'+value.id+'">'+value.df_no+' '+value.df_description+'</option>';
                        });
                    }

                    // Build HTML String
                    var html = '<div id="row'+i+'" class="row">';
                    
                    // Description
                    html += '<div class="col-md-5"><div class="form-group"><input type="text" class="form-control" name="description[]" placeholder="Add Your Task Here.." required></div></div>';
                    
                    // Due Date
                    html += '<div class="col-md-2"><div class="form-group"><input type="date" class="form-control" name="due_date[]" value="<?php echo date("Y-m-d");?>"></div></div>';
                    
                    // Department
                    html += '<div class="col-md-2"><div class="form-group"><select class="form-control" id="department'+i+'" name="department[]" onchange="getusers('+i+');" required>' + deptOptions + '</select></div></div>';
                    
                    // Username (Empty initially)
                    html += '<div class="col-md-2"><div class="form-group"><select class="form-control select3" id="username'+i+'" name="username[]" required><option value="">--CHOOSE--</option></select></div></div>';
                    
                    // DF Part
                    html += '<div class="col-md-2 particulardfdiv" style="display:none"><div class="form-group"><label>DF No</label><select class="form-control select2" name="particulardfno[]">' + dfOptions + '</select></div></div>';
                    
                    // Remove Button
                    html += '<div class="col-md-1"><div class="form-group pull-left" style="padding-top:0px"><label class="control-label">&nbsp;</label><button type="button" class="btn_remove btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div>';
                    
                    html += '</div><br/>'; // End row

                    $('#dynamictasks1').append(html);

                    // Re-apply logic
                    showhidedfbox();
                    
                    // Re-init Select2 on the new row
                    $('#row'+i+' .select2').select2({ width: '100%' });
                });

                // Remove Row Event
                $(document).on('click', '.btn_remove', function(){
                    var button_id = $(this).attr("id");
                    $('#row'+button_id+'').remove();
                });
            });
        </script>
    </body>
</html>