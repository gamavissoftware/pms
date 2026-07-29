<?php
$uri=$this->uri->segment(4);
if($uri=='')
{
    echo "Invalid Link"; exit;
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
		<title><?php echo sitetitle; ?>DF Meeting</title>
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
        <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
        <?PHP 

$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();

foreach($q->result() as $LOGO);

?>

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

		table.manglesh thead th {

				background: <?php echo $LOGO->colorcode;?>;

				color:#fff;

				font-weight:bold;

				text-align:center;

			}
				table.manglesh tbody td {
				    text-align:center;
				}

        .dfmeeting-admin-tools {
            margin-top: 8px;
            text-align: right;
        }
        .dfmeeting-admin-note {
            display: block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: #7a8795;
            margin-bottom: 2px;
        }
        .dfmeeting-admin-link {
            display: inline-block;
            font-size: 12px;
            color: #17324d;
            border-bottom: 1px solid #17324d;
            text-decoration: none;
            margin-bottom: 2px;
        }
        .dfmeeting-admin-link:hover,
        .dfmeeting-admin-link:focus {
            color: #17324d;
            text-decoration: none;
        }
        .dfmeeting-admin-date {
            display: block;
            font-size: 11px;
            color: #7a8795;
        }

        .mom-dynamic-row {
            clear: both;
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
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right">
						 	<a href="<?php echo page_url;?>Task/dfweeklymeetingpoints"><span class="btn btn-info btn-xs">DF Weekly Meeting Points</span></a>
						 	<a href="<?php echo page_url;?>Task/viewdfmeetingmom/<?php echo $this->uri->segment(4);?>"><span class="btn btn-danger btn-xs">View DF Meeting MOM</span></a>
						 	<?php if (!empty($can_manage_friday_reset)) { ?>
						 		<div class="dfmeeting-admin-tools">
						 			<span class="dfmeeting-admin-note">Admin backup tool only</span>
						 			<a href="<?php echo page_url; ?>Task/dfmeeting_friday_reset" class="dfmeeting-admin-link">Open Friday reset utility</a>
						 			<span class="dfmeeting-admin-date">Legacy running DFs only | Target: <?php echo date('d-M-Y', strtotime($friday_reset_target_date)); ?></span>
						 		</div>
						 	<?php } ?>
						 </div>
                           <h4 class="text-center" style="background-color:#fbeeee; padding:10px; 10px; 10px; 10px;">DF Weekly Meeting</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
				<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

               <div class="row">

                <?php 
                $q = $this->db->select('df_no, df_upload')->from('df_release a')->where('id',$this->uri->segment(4))->get();
                
                if($q->num_rows()>0){
                    foreach($q->result() as $rowss);
                    $df_no=$rowss->df_no;
                }else
                {
                    $df_no='';
                }

                $q1 = $this->db->select('dfid, particular, recordid, cc_record, to_record')->from('dfwise_mom')->where('dfid',$this->uri->segment(4))->get();
                $selected_participants = array();
                $selected_to_emails = array();
                $selected_cc_emails = array();
                if($q1->num_rows()>0){
                    foreach($q1->result() as $row1);
                    $particular = $row1->particular;
                    $dfid = $row1->dfid;
                    $rid = $row1->recordid;
                    foreach (preg_split('/[\s,;]+/', (string) $row1->to_record) as $to_email) {
                        $to_email = strtolower(trim((string) $to_email));
                        if ($to_email !== '' && filter_var($to_email, FILTER_VALIDATE_EMAIL) && !in_array($to_email, $selected_to_emails, true)) {
                            $selected_to_emails[] = $to_email;
                        }
                    }
                    foreach (preg_split('/[\s,;]+/', (string) $row1->cc_record) as $cc_email) {
                        $cc_email = strtolower(trim((string) $cc_email));
                        if ($cc_email !== '' && filter_var($cc_email, FILTER_VALIDATE_EMAIL) && !in_array($cc_email, $selected_cc_emails, true)) {
                            $selected_cc_emails[] = $cc_email;
                        }
                    }
                }else{
                   $particular =  "DF Meeting - DF No.".$df_no;
                }

                $participant_query = $this->db->select('user_id')
                    ->from('dfwise_mom_participant')
                    ->where('dfid', $this->uri->segment(4))
                    ->group_by('user_id')
                    ->get();

                if ($participant_query->num_rows() > 0) {
                    foreach ($participant_query->result() as $participant_row) {
                        $participant_user_id = (int) $participant_row->user_id;
                        if ($participant_user_id > 0 && !in_array($participant_user_id, $selected_participants, true)) {
                            $selected_participants[] = $participant_user_id;
                        }
                    }
                }

                $businessloc = 2;
                $company_users = $this->db->select('user_id, title, first_name, last_name, email')
                    ->from('system_users')
                    ->where('business_location', $businessloc)
                    ->where('user_status', 1)
                    ->order_by('first_name', 'ASC')
                    ->order_by('last_name', 'ASC')
                    ->get()
                    ->result();

                $participant_email_map = array();
                $responsible_options_html = '<option value="">--Select Responsible Person--</option>';
                foreach ($company_users as $company_user) {
                    $normalized_company_email = strtolower(trim((string) $company_user->email));
                    if ($normalized_company_email !== '' && filter_var($normalized_company_email, FILTER_VALIDATE_EMAIL)) {
                        $participant_email_map[(string) $company_user->user_id] = $normalized_company_email;
                    }
                    $responsible_label = strtoupper(trim($company_user->title . ' ' . $company_user->first_name . ' ' . $company_user->last_name));
                    $responsible_options_html .= '<option value="' . (int) $company_user->user_id . '">' . htmlspecialchars($responsible_label, ENT_QUOTES, 'UTF-8') . '</option>';
                }
                ?>

                        <div class="card-box">

						<form method="post" id="loginForm" action="<?php echo page_url;?>Task/savedfmeeting/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>" enctype="multipart/form-data">
                            <div id="pageloader">
                            <img src="<?php echo assets_url;?>images/loading.gif" alt="Please Wait..." />
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">
												<div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Agenda of the Meeting</label>
														<span id="error_agenceofthemeeting" style="color:red;">*</span>
														<input type="text" class="form-control" name="agenceofthemeeting" id="agenceofthemeeting" value="<?php echo  $particular;?>" readonly>
												    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Date</label>
														<span id="error_date" style="color:red;">*</span>
														<input type="date" class="form-control" name="date" id="date" value="<?php echo date('Y-m-d');?>" readonly>
														
												    </div>
                                                </div>

                                                 <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Time</label>
                                                        <span id="error_time" style="color:red;">*</span>
                                                        <input type="time" class="form-control" name="time" id="time" value="<?php echo date('H:i:s');?>" readonly>
                                                        
                                                    </div>
                                                </div>

                                                 <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Participant</label>
                                                        <span id="error_participant" style="color:red;">*</span>
                                                       <select class="form-control multipleselect" name="participant[]" multiple id="participant">
                                                           <option value=""></option>
                                                           <?php foreach($company_users as $row){ 
                                                           ?>
                                                           <option value="<?php echo $row->user_id;?>" <?php if (in_array((int) $row->user_id, $selected_participants, true)) { echo 'selected'; } ?>><?php echo $row->first_name." ".$row->last_name;?></option>
                                                           <?php }?>
                                                       </select>
                                                        
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Add Email in To</label>
                                                        <span style="color:red" id="error_emailto"></span>
                                                        <select class="form-control email-multipleselect" name="emailto[]" id="emailto" multiple>
                                                            <?php foreach ($selected_to_emails as $selected_to_email) { ?>
                                                                <option value="<?php echo htmlspecialchars($selected_to_email, ENT_QUOTES, 'UTF-8'); ?>" selected><?php echo htmlspecialchars($selected_to_email, ENT_QUOTES, 'UTF-8'); ?></option>
                                                            <?php } ?>
                                                        </select>
                                                        <small style="color:#666;">Selected participant emails appear here automatically. Use this field for any extra To recipients.</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Add Email in CC</label>
                                                        <span style="color:red" id="error_emailcc"></span>
                                                        <select class="form-control email-multipleselect" name="emailcc[]" id="emailcc" multiple>
                                                            <?php foreach ($selected_cc_emails as $selected_cc_email) { ?>
                                                                <option value="<?php echo htmlspecialchars($selected_cc_email, ENT_QUOTES, 'UTF-8'); ?>" selected><?php echo htmlspecialchars($selected_cc_email, ENT_QUOTES, 'UTF-8'); ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    
                                                </div>


                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label>MOM Point / Task</label> 
                                                        <span style="color: red;" id="error_messagefordepartment">*</span>
                                                        <input type="text" name="dfmompoints[]" class="form-control mom-point-input" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Responsible Person</label>
                                                        <span style="color: red;" class="error_responsible_person">*</span>
                                                        <select class="form-control mom-responsible-select" name="responsible_person[]" required>
                                                            <?php echo $responsible_options_html; ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label>Due Date</label>
                                                        <span style="color: red;" class="error_due_date">*</span>
                                                        <input type="date" name="due_date[]" class="form-control mom-due-date-input" min="<?php echo date('Y-m-d'); ?>" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-1">
                                                <div class="form-group" style="padding-top:23px">
                                                    <button type="button" class="btn btn-warning" name="add" id="addmore_btn1"><i class="fa fa-plus"></i></button>
                                                </div>
                                                
                                                </div>

                                                

                                                
                                                
                                                <div id="dynamictasks1"></div>
												
												
												<div class="col-md-4">
													<div class="form-group" style="padding-top:23px;">
													<input type="submit" class="btn btn-success" name="Save" id="savedata">
												</div>
												</div>
											

                            </div>
                            	

                            </form>
                            <!-- end row -->
                        </div> <!-- end ard-box -->
                    

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




        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script>
$(document).ready(function(){
	   $("#teamupdate").attr('disabled',false);
	   $("#teamupdate").val('Update');
  $("#loginForm").on("submit", function(){
   // $("#pageloader").fadeIn();
   $("#teamupdate").attr('disabled',true);
     $("#teamupdate").val('Please Wait...');
  });//submit
});//document ready
</script>

<script language="javascript" type="text/javascript">   
$(document).ready(function() {
$("#savedata").click(function() {
var agenceofthemeeting = $("#agenceofthemeeting").val();
var isValid = true;
if(agenceofthemeeting=='')
{
	$("#error_agenceofthemeeting").html('Required!');
	$("#agenceofthemeeting").css("border", "1px solid red");
	isValid = false;
}
else
{
	$("#error_agenceofthemeeting").html('*');
	$("#agenceofthemeeting").css("border", "");
}

var participant = $("#participant").val();
if(!participant || participant.length===0)
{
	$("#error_participant").html('Required!');
	$("#participant").next('.select2-container').find('.select2-selection').css("border", "1px solid red");
	isValid = false;
}
else
{
	$("#error_participant").html('*');
	$("#participant").next('.select2-container').find('.select2-selection').css("border", "");
}

$('.mom-point-input').each(function(index) {
	var pointInput = $(this);
	var rowWrapper = pointInput.closest('[data-mom-row], .row, [id^="row"]');
	var responsibleSelect = rowWrapper.find('.mom-responsible-select').first();
	var dueDateInput = rowWrapper.find('.mom-due-date-input').first();

	if($.trim(pointInput.val()) === '') {
		pointInput.css("border", "1px solid red");
		isValid = false;
	} else {
		pointInput.css("border", "");
	}

	if(!responsibleSelect.val()) {
		responsibleSelect.next('.select2-container').find('.select2-selection').css("border", "1px solid red");
		isValid = false;
	} else {
		responsibleSelect.next('.select2-container').find('.select2-selection').css("border", "");
	}

	if(!dueDateInput.val()) {
		dueDateInput.css("border", "1px solid red");
		isValid = false;
	} else {
		dueDateInput.css("border", "");
	}
});

if(!isValid)
{
	return false;
}

});
});
</script>   

<script type="text/javascript">
$(document).ready(function(){
 var responsibleOptions = <?php echo json_encode($responsible_options_html); ?>;
 var minDueDate = '<?php echo date('Y-m-d'); ?>';
 var i=1;
 $('#addmore_btn1').click(function(){
 i++;
 
 $('#dynamictasks1').append(
 	'<div id="row'+i+'" class="row mom-dynamic-row" data-mom-row="1">' +
 		'<div class="col-md-5"><div class="form-group"><label>MOM Point / Task</label><span style="color: red;">*</span><input type="text" class="form-control mom-point-input" name="dfmompoints[]"></div></div>' +
 		'<div class="col-md-4"><div class="form-group"><label>Responsible Person</label><span style="color: red;">*</span><select class="form-control mom-responsible-select" name="responsible_person[]">' + responsibleOptions + '</select></div></div>' +
 		'<div class="col-md-2"><div class="form-group"><label>Due Date</label><span style="color: red;">*</span><input type="date" class="form-control mom-due-date-input" name="due_date[]" min="' + minDueDate + '"></div></div>' +
 		'<div class="col-md-1"><div class="form-group pull-left" style="padding-top:23px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div>' +
 	'</div>'
 );

 $('#row'+i).find('.mom-responsible-select').select2({
     width: '100%',
     placeholder: '--Select Responsible Person--'
 });
 
 });
 
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id+'').remove();
 });
 
});
      </script>
       <script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready
</script> 
 <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
       <script type="text/javascript">
            $( document ).ready(function() {
                    var participantEmailMap = <?php echo json_encode($participant_email_map); ?>;
                    var isSyncingParticipantEmails = false;

                    function normalizeEmail(email) {
                        return $.trim(String(email || '')).toLowerCase();
                    }

                    function findEmailOption($select, normalizedEmail) {
                        var matchedOption = $();

                        $select.find('option').each(function() {
                            if (normalizeEmail($(this).val()) === normalizedEmail) {
                                matchedOption = $(this);
                                return false;
                            }
                        });

                        return matchedOption;
                    }

                    function syncParticipantEmailsToToField() {
                        if (isSyncingParticipantEmails) {
                            return;
                        }

                        isSyncingParticipantEmails = true;

                        var $participant = $('#participant');
                        var $emailTo = $('#emailto');
                        var participantIds = $participant.val() || [];
                        var activeEmails = [];
                        var activeEmailLookup = {};
                        var hasChanges = false;

                        $.each(participantIds, function(index, participantId) {
                            var normalizedEmail = normalizeEmail(participantEmailMap[String(participantId)]);
                            if (normalizedEmail !== '' && !activeEmailLookup[normalizedEmail]) {
                                activeEmailLookup[normalizedEmail] = true;
                                activeEmails.push(normalizedEmail);
                            }
                        });

                        $emailTo.find('option[data-auto-email="1"]').each(function() {
                            var $option = $(this);
                            var optionEmail = normalizeEmail($option.val());

                            if (!activeEmailLookup[optionEmail]) {
                                $option.remove();
                                hasChanges = true;
                                return;
                            }

                            if (!$option.prop('selected')) {
                                $option.prop('selected', true);
                                hasChanges = true;
                            }
                        });

                        $.each(activeEmails, function(index, email) {
                            var $existingOption = findEmailOption($emailTo, email);

                            if ($existingOption.length > 0) {
                                if (!$existingOption.prop('selected')) {
                                    $existingOption.prop('selected', true);
                                    hasChanges = true;
                                }
                                return;
                            }

                            var newOption = new Option(email, email, true, true);
                            $(newOption).attr('data-auto-email', '1');
                            $emailTo.append(newOption);
                            hasChanges = true;
                        });

                        if (hasChanges) {
                            $emailTo.trigger('change.select2');
                        }

                        isSyncingParticipantEmails = false;
                    }

                    $('.multipleselect').select2({
                        width: '100%'
                    });
                    $('.mom-responsible-select').select2({
                        width: '100%',
                        placeholder: '--Select Responsible Person--'
                    });
                    $('.email-multipleselect').select2({
                        width: '100%',
                        tags: true,
                        tokenSeparators: [',', ' '],
                        placeholder: 'Type email and press enter'
                    });

                    $('#participant').on('change', syncParticipantEmailsToToField);
                    $('#emailto').on('change', function() {
                        if (!isSyncingParticipantEmails) {
                            syncParticipantEmailsToToField();
                        }
                    });
                    $('#loginForm').on('submit', function() {
                        $('#emailto option[data-auto-email="1"]').remove();
                    });

                    syncParticipantEmailsToToField();
            });
        </script>

</body>
</html>
