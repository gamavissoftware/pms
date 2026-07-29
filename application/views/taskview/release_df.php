<?php 
$dfno = 0;
    $q = $this->db->select()->from('task_department_wise_scheduling a')->join('poreceived b','a.po_id=b.id','left')->where('a.id',$this->uri->segment(3))->get();
    if($q->num_rows()>0){
        foreach($q->result() as $dfinfo);
        $dfno = $dfinfo->df_number;
    }else{
        echo "DF No. Not Assigned. Please check with technical Team"; exit;
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">

    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> - Release DF</title>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>

    <?PHP
    $q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
    foreach ($q->result() as $LOGO);
    $themeColor = $LOGO->colorcode ? $LOGO->colorcode : '#007bff'; // Fallback color
    ?>
    <style>
        /* Card Styling */
        .card_box {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            padding: 30px;
            margin-bottom: 30px;
            border-top: 4px solid <?php echo $themeColor; ?>;
        }

        /* Section Headers */
        .section-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-weight: 600;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 14px;
        }

        /* Table Styling */
        .table-custom thead th {
            background-color: #f4f8fb;
            color: #333;
            border-bottom: 2px solid <?php echo $themeColor; ?>;
            font-weight: 600;
            vertical-align: middle;
        }
        .table-custom tbody td {
            vertical-align: middle !important;
        }

        /* Input Styling */
        .form-control {
            border-radius: 4px;
            border: 1px solid #e3e3e3;
            box-shadow: none;
            height: 38px;
        }
        .form-control:focus {
            border-color: <?php echo $themeColor; ?>;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25); /* Adjust based on theme color */
        }
        .form-control[readonly] {
            background-color: #f9f9f9;
            cursor: not-allowed;
        }

        /* Button Styling */
        .btn-submit {
            background-color: <?php echo $themeColor; ?>;
            border-color: <?php echo $themeColor; ?>;
            color: #fff;
            font-weight: bold;
            padding: 10px 30px;
            border-radius: 30px; /* Rounded button */
            transition: all 0.3s;
        }
        .btn-submit:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            opacity: 0.9;
            color: #fff;
        }

        /* Loader */
        #pageloader {
            display: none;
            position: fixed;
            z-index: 9999;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255,255,255,0.8);
            text-align: center;
            padding-top: 20%;
        }
    </style>

    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
</head>

<body>

    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>
    <div class="wrapper">
        <div class="container-fluid">

            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title text-center" style="font-weight: 700;">PROJECT RELEASE MANAGEMENT</h4>
                        <p class="text-muted text-center">Define workflow, schedule departments, and release DF</p>
                    </div>
                </div>
            </div>

            <?php 
            $q = $this->db->select('df_number')->from('poreceived')->where('id',$this->uri->segment(3))->get();
            foreach($q->result() as $row);
            ?>
            
            <div class="row">
                <div class="col-sm-12"> <div class="card_box">
                        
                        <form id="loginForm" method="post" action="<?php echo page_url; ?>Task/dfreleasebynewfeature/<?php echo $this->uri->segment(3);?>" enctype="multipart/form-data">
                            
                            <div id="pageloader">
                                <img src="<?php echo assets_url; ?>images/loading.gif" alt="processing..." />
                                <h4>Processing Request...</h4>
                            </div>

                            <input type="hidden" name="dfrecordid" id="dfrecordid" value="<?php echo $this->uri->segment(3);?>">
                            <input type="hidden" name="poid" id="poid" value="<?php echo $this->uri->segment(4);?>">

                            <div class="section-header"><i class="md md-info-outline"></i> Basic Information</div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Upload DF File <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control" name="uploaddf" id="uploaddf">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">DF Number <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="md md-confirmation-number"></i></span>
                                            <input type="text" class="form-control" name="dfno" id="dfno" value="<?php echo $dfno;?>" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Release Type <span class="text-danger">*</span></label>
                                        <select name="df_release" id="df_release" class="form-control select2">
                                            <option value="">-- Select Release Type --</option>
                                            <option value="1">Normal</option>
                                            <option value="3">Customized (R&D)</option>
                                            <!-- <option value="2">Basic</option> -->
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div id="department_list" style="display: none; margin-top: 20px;">
                                <div class="section-header"><i class="md md-schedule"></i> Department Schedule & Workflow</div>
                                
                                <div class="table-responsive">
                                    <table class="table table-custom table-hover">
                                        <thead>
                                            <tr>
                                                <th width="20%">Department</th>
                                                <th width="20%">Head / Lead</th>
                                                <th width="20%">Start Date</th>
                                                <th width="20%">End Date</th>
                                                <th width="10%" class="text-center">Days</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $qry = $this->db->select('a.department_id,a.department,b.team_leader,c.title, c.first_name,c.last_name,c.user_id')
                                                    ->from('departments a')
                                                    ->join('prestogroup_teams b', 'a.department_id=b.department_id')
                                                    ->join('system_users c', 'b.team_leader=c.user_id')
                                                    ->where('a.business_loc_id', 2)->where('show_in_pms', 1)
                                                    ->order_by('a.sort_order_for_customize','asc')
                                                    ->get();

                                            if ($qry->num_rows() > 0) {
                                                $index = 0;
                                                foreach ($qry->result() as $rows) {
                                                    $index++;
                                            ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo ucwords(strtolower($rows->department)); ?></strong>
                                                    <input type="hidden" name="departmentid[]" value="<?php echo $rows->department_id;?>">
                                                    <input type="hidden" name="department_name[]" value="<?php echo $rows->department; ?>">
                                                </td>
                                                <td>
                                                    <span class="text-muted">
                                                        <i class="md md-person"></i> 
                                                        <?php echo ucwords(strtolower($rows->title . ' ' . $rows->first_name . ' ' . $rows->last_name)); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <input type="date" name="start_date[]" id="start_date_<?php echo $index; ?>" class="form-control" min="<?php echo date('Y-m-d');?>" onchange="calculateDays(<?php echo $index; ?>)">
                                                </td>
                                                <td>
                                                    <input type="date" name="end_date[]" id="end_date_<?php echo $index; ?>" class="form-control" min="<?php echo date('Y-m-d');?>" onchange="calculateDays(<?php echo $index; ?>)">
                                                </td>
                                                <td class="text-center">
                                                    <input type="text" name="no_days[]" id="no_days_<?php echo $index; ?>" class="form-control text-center" readonly placeholder="0" style="font-weight:bold;">
                                                </td>
                                            </tr>
                                            <?php 
                                                } // end foreach
                                            } // end if
                                            ?>
                                            <tr style="background-color: #fcfcfc; border-top: 2px solid #ddd;">
                                                <td colspan="3"></td>
                                                <td class="text-right" style="vertical-align: middle;"><strong>GRAND TOTAL DAYS:</strong></td>
                                                <td>
                                                    <input type="text" class="form-control text-center" id="total_days" name="total_days" value="0" readonly style="background-color: #fffbe0; color: #000; font-weight: bold; border: 1px solid #ffd700;">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 20px;">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Release Notes / Description <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="df_description" name="df_description" rows="3" placeholder="Enter details about this release..." required></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 20px;">
                                <div class="col-sm-12 text-right">
                                    <button type="button" class="btn btn-default btn-lg" onclick="window.history.back();">Cancel</button>
                                    <input type="submit" id="savedata" class="btn btn-submit btn-lg" value="Release DF Now">
                                </div>
                            </div>

                        </form>
                    </div> </div>
            </div>
            <?php $this->load->view('common/footer'); ?>
            </div> </div>
    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/detect.js"></script>
    <script src="<?php echo assets_url; ?>js/fastclick.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.slimscroll.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.blockUI.js"></script>
    <script src="<?php echo assets_url; ?>js/waves.js"></script>
    <script src="<?php echo assets_url; ?>js/wow.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.nicescroll.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.scrollTo.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

    <script>
    // --- TOASTR CONFIGURATION ---
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "5000"
    };

    $(document).ready(function () {

        // Submit Button Feedback
        $("#loginForm").on("submit", function () {
            $("#savedata").val('Processing...').prop('disabled', true);
            $("#pageloader").fadeIn();
        });

        // --- VALIDATION LOGIC ---
        $("#savedata").click(function (e) {
            var isValid = true;

            // 1. Upload DF
            if ($("#uploaddf").val() == '') {
                toastr.error('Please select a DF file to upload.', 'Validation Error');
                $("#uploaddf").css("border", "1px solid red");
                isValid = false;
            } else {
                $("#uploaddf").css("border", "");
            }

            // 2. DF No
            if ($("#dfno").val() == '') {
                if(isValid) toastr.error('DF Number is missing.', 'Validation Error');
                $("#dfno").css("border", "1px solid red");
                isValid = false;
            } else {
                $("#dfno").css("border", "");
            }

            // 3. Release Type
            var df_release = $("#df_release").val();
            if (df_release == '') {
                if(isValid) toastr.error('Please select a Release Type.', 'Validation Error');
                $("#df_release").css("border", "1px solid red");
                isValid = false;
            } else {
                $("#df_release").css("border", "");
            }

            // 4. Customized Dates (Parallel Logic)
            if (df_release === '3') {
                let missingField = false;
                
                // Check visible Start Dates
                $("input[name='start_date[]']").each(function () {
                    if ($(this).is(":visible") && $(this).val().trim() === '') {
                        $(this).css("border", "1px solid red");
                        missingField = true;
                    } else {
                        $(this).css("border", "");
                    }
                });

                // Check visible End Dates
                $("input[name='end_date[]']").each(function () {
                    if ($(this).is(":visible") && $(this).val().trim() === '') {
                        $(this).css("border", "1px solid red");
                        missingField = true;
                    } else {
                        $(this).css("border", "");
                    }
                });

                if (missingField) {
                    toastr.warning('All Department dates are required.', 'Incomplete Data');
                    isValid = false;
                }
            }

            // 5. Description
            if ($("#df_description").val().trim() == '') {
                 $("#df_description").css("border", "1px solid red");
                 if(isValid) toastr.error('Please enter a description.', 'Validation Error');
                 isValid = false;
            } else {
                 $("#df_description").css("border", "");
            }

            if (!isValid) {
                e.preventDefault(); 
                return false;
            }
        });

        // --- TOGGLE DEPARTMENT VIEW ---
        $('#df_release').change(function () {
            var selectedValue = $(this).val();
            if (selectedValue == '3') {
                $('#department_list').slideDown();
                toastr.info('Customized Mode: Schedules run parallel.', 'Workflow Active');
            } else {
                $('#department_list').slideUp();
                $("input[name='start_date[]'], input[name='end_date[]'], input[name='no_days[]']").val('');
                $("#total_days").val(0);
            }
        });
    });

    // --- SMART DATE CALCULATION ---
    function calculateDays(index) {
        var startDateInput = document.getElementById('start_date_' + index);
        var endDateInput = document.getElementById('end_date_' + index);
        
        // --- UX ENHANCEMENT START ---
        // As soon as Start Date changes, lock the End Date calendar to start from there.
        if (startDateInput.value) {
            endDateInput.min = startDateInput.value;
            
            // Logic: If the user previously selected an End Date that is now 
            // BEFORE the new Start Date, we clear it to force them to pick a valid one.
            if(endDateInput.value && endDateInput.value < startDateInput.value) {
                endDateInput.value = ''; 
                document.getElementById('no_days_' + index).value = '';
                toastr.warning('End date reset because it was before the new Start date.');
            }
        }
        // --- UX ENHANCEMENT END ---

        var startDate = startDateInput.value;
        var endDate = endDateInput.value;

        // If we don't have both dates yet, stop here (but total might need updating if we cleared a value)
        if (!startDate || !endDate) {
            document.getElementById('no_days_' + index).value = 0;
            updateTotalDays();
            return;
        }

        // AJAX Holiday Check
        $.ajax({
            url: '<?php echo page_url; ?>Dashboard/checkHolidays',
            type: 'POST',
            dataType: 'json',
            data: { start_date: startDate, end_date: endDate },
            success: function (response) {
                var start = new Date(startDate);
                var end = new Date(endDate);
                var timeDiff = end - start;
                var daysDiff = timeDiff / (1000 * 3600 * 24); 

                var holidaysCount = 0;
                if (response.holidays && response.holidays.length > 0) {
                    holidaysCount = response.holidays.length;
                }

                var workingDays = (daysDiff + 1) - holidaysCount;
                document.getElementById('no_days_' + index).value = Math.max(0, workingDays);
                
                updateTotalDays();
            },
            error: function () {
                // Fallback
                var start = new Date(startDate);
                var end = new Date(endDate);
                var timeDiff = end - start;
                var daysDiff = timeDiff / (1000 * 3600 * 24);
                document.getElementById('no_days_' + index).value = Math.max(0, daysDiff + 1);
                updateTotalDays();
            }
        });
    }

    function updateTotalDays() {
        var minDate = null;
        var maxDate = null;

        // 1. Find the Earliest (Smallest) Start Date
        $("input[name='start_date[]']").each(function () {
            var val = $(this).val();
            if (val) {
                var current = new Date(val);
                // If minDate is null OR current is smaller than minDate
                if (minDate === null || current < minDate) {
                    minDate = current;
                }
            }
        });

        // 2. Find the Latest (Biggest) End Date
        $("input[name='end_date[]']").each(function () {
            var val = $(this).val();
            if (val) {
                var current = new Date(val);
                // If maxDate is null OR current is bigger than maxDate
                if (maxDate === null || current > maxDate) {
                    maxDate = current;
                }
            }
        });

        // 3. Calculate Difference
        if (minDate && maxDate && maxDate >= minDate) {
            var timeDiff = maxDate.getTime() - minDate.getTime();
            var dayDiff = timeDiff / (1000 * 3600 * 24); 
            
            // Add 1 to include the start day itself
            var totalSpan = Math.ceil(dayDiff) + 1;
            
            $("#total_days").val(totalSpan);
        } else {
            // If dates are invalid or missing
            $("#total_days").val(0);
        }
    }
</script>
</body>
</html>