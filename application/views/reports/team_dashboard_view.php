<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title><?php echo sitetitle; ?> &mdash; Team Dashboard</title>
 <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
 <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
 <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
 <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
 <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
 <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
 <link href="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">

 <style>
  body { background-color: #f4f7f6; }
  .card-box { background-color: #ffffff; border-radius: 12px; border: 1px solid #e9ecef; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 25px; margin-bottom: 25px; }
  .filter-card { background-color: #f8f9fa; border: 1px solid #dee2e6; }
  .form-inline .form-group { margin-bottom: 10px; vertical-align: middle; }

  .department-separator {
   font-size: 1.75em;
   font-weight: 600;
   color: #333;
   margin: 30px 0 15px 0;
   border-bottom: 2px solid #007bff;
   padding-bottom: 5px;
   display: inline-block;
  }

  .team-member-card {
   background: #fff;
   border: 1px solid #e9ecef;
   border-radius: 12px;
   box-shadow: 0 4px 15px rgba(0,0,0,0.05);
   padding: 20px;
   margin-bottom: 25px;
   display: flex;
   flex-direction: column;
   height: 100%;
  }
 
  .member-header {
   display: flex;
   align-items: center;
   justify-content: space-between; /* Pushes button to the right */
   border-bottom: 1px solid #f4f4f4;
   padding-bottom: 15px;
   margin-bottom: 15px;
  }
  .member-header-left {
   display: flex;
   align-items: center;
   min-width: 0; /* Prevents long names from breaking layout */
  }
  .member-header img {
   width: 60px;
   height: 60px;
   border-radius: 50%;
   object-fit: cover;
   border: 3px solid #f0f0f0;
   flex-shrink: 0; /* Prevents image from shrinking */
  }
  .member-info {
   margin-left: 15px;
   overflow: hidden; /* Handle long names */
   text-overflow: ellipsis;
   white-space: nowrap;
  }
  .member-info h4 {
   font-size: 1.25em;
   font-weight: 600;
   color: #333;
   margin: 0 0 3px 0;
   overflow: hidden;
   text-overflow: ellipsis;
   white-space: nowrap;
  }
  .member-info p {
   font-size: 0.95em;
   color: #007bff;
   margin: 0;
   font-weight: 500;
  }
 
  .member-df-stats {
   text-align: center;
   padding-left: 10px;
   flex-shrink: 0; /* Prevents button from shrinking */
  }
  .btn-df-details {
   display: block;
   background-color: #f0f5ff;
   border: 1px solid #b3cfff;
   color: #0056b3;
   font-weight: 600;
   border-radius: 8px;
   padding: 10px 14px;
   transition: all 0.2s ease;
   cursor: pointer;
   text-decoration: none;
   min-width: 130px;
  }
  .btn-df-details:hover {
   background-color: #0056b3;
   color: #fff;
   text-decoration: none;
   transform: translateY(-2px);
   box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  }
  .df-stat-summary {
   display: flex;
   align-items: flex-end;
   justify-content: center;
   gap: 10px;
  }
  .df-stat-item {
   display: flex;
   flex-direction: column;
   align-items: center;
  }
  .btn-df-details .df-count {
   font-size: 1.4em;
   display: block;
   line-height: 1.1;
   font-weight: 700;
  }
  .btn-df-details .df-label {
   font-size: 0.72em;
   text-transform: uppercase;
   letter-spacing: 0.5px;
  }
  .df-stat-divider {
   font-size: 1.25em;
   font-weight: 700;
   color: currentColor;
   opacity: 0.7;
   line-height: 1;
   margin-bottom: 10px;
  }
  .df-caption {
   display: block;
   margin-top: 6px;
   font-size: 0.72em;
   text-transform: uppercase;
   letter-spacing: 0.6px;
  }
 
  #df-modal .modal-body {
   max-height: 60vh;
   overflow-y: auto;
  }
  #df-modal-loader {
   text-align: center;
   padding: 40px;
  }

  .member-kpi-stats {
   display: grid;
   grid-template-columns: 1fr 1fr;
   gap: 12px;
   margin-bottom: 15px;
  }
 
  .stat-box {
   background-color: #f9f9f9;
   border-radius: 8px;
   padding: 12px;
   text-align: center;
   transition: all 0.2s ease-in-out;
   display: block;
   text-decoration: none;
  }
  .stat-box:hover {
   transform: translateY(-2px);
   box-shadow: 0 4px 10px rgba(0,0,0,0.08);
   text-decoration: none;
  }
 
  .stat-box .stat-value {
   font-size: 1.75em;
   font-weight: 700;
   line-height: 1.1;
  }
  .stat-box .stat-label {
   font-size: 0.85em;
   color: #777;
   text-transform: uppercase;
  }
 
  .stat-box-assigned .stat-value { color: #007bff; }
  .stat-box-done .stat-value { color: #28a745; }
  .stat-box-pending .stat-value { color: #ffc107; }
  .stat-box-delayed .stat-value { color: #dc3545; }

  .performance-bar-container { margin-top: auto; }
  .performance-bar-container label {
   font-size: 0.9em;
   font-weight: 600;
   color: #555;
   margin-bottom: 5px;
   display: block;
  }
  .progress {
   height: 12px;
   border-radius: 6px;
   background-color: #e9ecef;
   margin-bottom: 0;
  }
  .progress-bar {
   border-radius: 6px;
   font-size: 10px;
   line-height: 12px;
  }

  .member-mis-stats {
   margin-top: 15px;
   padding-top: 15px;
   border-top: 1px solid #f4f4f4;
   display: flex;
   justify-content: space-around;
  }
  .mis-stat-item { text-align: center; }
  .mis-stat-item .stat-value {
   font-size: 1.4em;
   font-weight: 600;
  }
  .mis-stat-item .stat-label {
   font-size: 0.85em;
   color: #777;
  }

    /* --- NEW CSS for Task Modal (Modal 2) --- */
    .btn-task-details.badge {
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s ease;
        font-size: 1em; /* Make it a bit bigger */
        line-height: 1;
        padding: .4em .6em;
    }
    .btn-task-details.badge:hover {
        transform: scale(1.1);
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    /* Ensure second modal appears on top of the first */
    #task-modal {
        z-index: 1060; 
    }
    /* Fix for multiple modal backdrops */
    .modal-backdrop.fade.in + .modal-backdrop.fade.in {
        z-index: 1059; 
    }
    #task-modal-loader {
        text-align: center;
        padding: 40px;
    }
    /* --- End of New CSS --- */

 </style>
</head>
<body>
 <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>

 <div class="wrapper">
  <div class="container-fluid">
   <div class="row"><div class="col-sm-12"><div class="page-title-box"><h4 class="page-title"><?php echo htmlspecialchars($page_title ?? 'Team Dashboard'); ?></h4></div></div></div>

   <div class="row">
    <div class="col-lg-12">
     <div class="card-box filter-card">
      <h4 class="m-t-0 header-title"><b><i class="fa fa-filter"></i> Filter Report</b></h4>
     
      <form method="post" action="<?php echo page_url; ?>Dashboard/team_dashboard" class="form-inline">
       <div class="form-group m-r-10">
        <label for="start_date" class="m-r-10">From:</label>
        <input type="text" class="form-control datepicker" name="start_date" id="start_date" value="<?php echo htmlspecialchars($selected_start_date ?? ''); ?>" placeholder="Start Date">
       </div>
       <div class="form-group m-r-10">
        <label for="end_date" class="m-r-10">To:</label>
        <input type="text" class="form-control datepicker" name="end_date" id="end_date" value="<?php echo htmlspecialchars($selected_end_date ?? ''); ?>" placeholder="End Date">
       </div>
      
       <?php if ($is_super_admin): ?>
        <div class="form-group m-r-10">
         <label for="department_id" class="m-r-10">Department:</label>
         <select name="department_id" id="department_id" class="form-control">
          <option value="">All Departments</option>
          <?php if (isset($departments)): ?>
           <?php foreach($departments as $dept): ?>
            <option value="<?php echo $dept['department_id']; ?>" <?php echo ($dept['department_id'] == $selected_department_id) ? 'selected' : ''; ?>>
             <?php echo htmlspecialchars(ucwords(strtolower($dept['department']))); ?>
            </option>
           <?php endforeach; ?>
          <?php endif; ?>
         </select>
        </div>
       <?php endif; ?>
      
      <button type="submit" class="btn btn-primary waves-effect waves-light"><i class="fa fa-search"></i> Filter</button>
<button type="submit" name="export_btn" value="true" class="btn btn-success waves-effect waves-light m-l-5"><i class="fa fa-file-excel-o"></i> Export Tasks</button>
<a href="<?php echo page_url; ?>Dashboard/team_dashboard" class="btn btn-default waves-effect waves-light m-l-5"><i class="fa fa-refresh"></i> Reset</a>
      </form>
     </div>
    </div>
   </div>
  
   <?php if (isset($error_message)): ?>
    <div class="row">
     <div class="col-lg-12">
      <div class="card-box">
       <div class="text-center text-danger">
        <i class="fa fa-exclamation-triangle fa-3x"></i>
        <h4 class="m-t-20"><?php echo $error_message; ?></h4>
       </div>
      </div>
     </div>
    </div>
  
   <?php elseif (empty($department_groups)): ?>
    <div class="row">
     <div class="col-lg-12">
      <div class="card-box"><div class="text-center"><i class="fa fa-info-circle fa-3x text-muted"></i><h4 class="m-t-20">No Team Data Found</h4><p class="text-muted">No users found for the selected filters.</p></div></div>
     </div>
    </div>
   <?php else: ?>
    <?php
    // Create the query string for the links
    $date_query_string = http_build_query([
     'start_date' => $selected_start_date ?? '',
     'end_date' => $selected_end_date ?? ''
    ]);
    ?>
    <?php foreach ($department_groups as $group): ?>
    
     <div class="row">
      <div class="col-lg-12">
       <h2 class="department-separator"><?php echo htmlspecialchars($group['department_name']); ?></h2>
      </div>
     </div>
    
     <div class="row">
      <?php foreach ($group['members'] as $user): ?>
       <div class="col-md-6 col-lg-4">
        <div class="team-member-card">
        
         <div class="member-header">
          <div class="member-header-left">
           <img src="<?php echo !empty($user['profile_pic']) ? user_profile. $user['profile_pic'] : user_profile . 'userplaceholder.jpeg'; ?>" alt="Profile">
           <div class="member-info">
            <h4><?php echo htmlspecialchars(ucwords(strtolower($user['user_name']))); ?></h4>
            <p><?php echo htmlspecialchars(ucwords(strtolower($user['designation']))); ?></p>
           </div>
          </div>
         
          <div class="member-df-stats">
           <a class="btn-df-details"
            role="button"
            data-user-id="<?php echo $user['user_id']; ?>"
            data-user-name="<?php echo htmlspecialchars(ucwords(strtolower($user['user_name']))); ?>">
            <span class="df-stat-summary">
             <span class="df-stat-item">
              <span class="df-count"><?php echo (int) ($user['active_df_count'] ?? 0); ?></span>
              <span class="df-label">Active</span>
             </span>
             <span class="df-stat-divider">/</span>
             <span class="df-stat-item">
              <span class="df-count"><?php echo (int) ($user['total_df_assigned'] ?? 0); ?></span>
              <span class="df-label">Overall</span>
             </span>
            </span>
            <span class="df-caption">DF Assigned</span>
           </a>
          </div>
         </div>
                   
         <div class="member-kpi-stats">
          <a class="stat-box stat-box-assigned" href="<?php echo page_url; ?>Dashboard/team_task_detail/<?php echo $user['user_id']; ?>/assigned?<?php echo $date_query_string; ?>" target="_blank">
           <div class="stat-value"><?php echo $user['total_assigned']; ?></div>
           <div class="stat-label">Assigned</div>
          </a>
          <a class="stat-box stat-box-done" href="<?php echo page_url; ?>Dashboard/team_task_detail/<?php echo $user['user_id']; ?>/done?<?php echo $date_query_string; ?>" target="_blank">
           <div class="stat-value"><?php echo $user['total_completed']; ?></div>
           <div class="stat-label">Done</div>
          </a>
          <a class="stat-box stat-box-pending" href="<?php echo page_url; ?>Dashboard/team_task_detail/<?php echo $user['user_id']; ?>/pending?<?php echo $date_query_string; ?>" target="_blank">
           <div class="stat-value"><?php echo $user['total_pending']; ?></div>
           <div class="stat-label">Ongoing Task</div>
          </a>
          <a class="stat-box stat-box-delayed" href="<?php echo page_url; ?>Dashboard/team_task_detail/<?php echo $user['user_id']; ?>/delayed?<?php echo $date_query_string; ?>" target="_blank">
           <div class="stat-value"><?php echo $user['total_delayed']; ?></div>
           <div class="stat-label">Completed with Delay</div>
          </a>
         </div>
        
         <div class="member-mis-stats">
          <div class="mis-stat-item">
           <div class="stat-value" style="color: #28a745;"><?php echo $user['on_time_percent']; ?>%</div>
           <div class="stat-label">On-Time</div>
          </div>
          <div class="mis-stat-item">
           <div class="stat-value" style="color: #dc3545;"><?php echo $user['percent_work_delayed']; ?>%</div>
           <div class="stat-label">% Completed with Delay</div>
          </div>
          <div class="mis-stat-item">
           <div class="stat-value" style="color: #ffc107;"><?php echo $user['percent_not_done']; ?>%</div>
           <div class="stat-label">% Ongoing Task</div>
          </div>
         </div>
        
         <div class="performance-bar-container">
          <label>On-Time Performance (<?php echo $user['on_time_percent']; ?>%)</label>
          <div class="progress">
           <div class="progress-bar progress-bar-success" role="progressbar"
            aria-valuenow="<?php echo $user['on_time_percent']; ?>"
            aria-valuemin="0" aria-valuemax="100"
            style="width: <?php echo $user['on_time_percent']; ?>%;">
            <?php echo $user['on_time_percent']; ?>%
           </div>
          </div>
         </div>

        </div>
       </div>
      <?php endforeach; ?>
     </div> <?php endforeach; ?>
   <?php endif; ?>

  </div>
 </div>

<?php $this->load->view('common/footer'); ?>

   <div class="modal fade" id="df-modal" tabindex="-1" role="dialog" aria-labelledby="dfModalLabel">
  <div class="modal-dialog modal-lg" role="document">
   <div class="modal-content">
    <div class="modal-header">
     <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
     <h4 class="modal-title" id="dfModalLabel">DF Details for [User Name]</h4>
    </div>
    <div class="modal-body">
     <div id="df-modal-loader" style="display: none;">
      <i class="fa fa-spinner fa-spin fa-3x text-primary" style="display: block; text-align: center; margin: 0 auto;"></i>
      <p class="text-center m-t-10">Loading details...</p>
     </div>
     <div id="df-modal-content"></div>
    </div>
    <div class="modal-footer">
     <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
    </div>
   </div>
  </div>
 </div>
  <div class="modal fade" id="task-modal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel">
      <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title" id="taskModalLabel">Due Tasks for [DF Name]</h4>
              </div>
              <div class="modal-body">
                  <div id="task-modal-loader" style="display: none;">
                      <i class="fa fa-spinner fa-spin fa-3x text-primary" style="display: block; text-align: center; margin: 0 auto;"></i>
                      <p class="text-center m-t-10">Loading tasks...</p>
                  </div>
                  <div id="task-modal-content"></div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
              </div>
          </div>
      </div>
  </div>
   <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
 <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
 <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
 <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
 <script src="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<script>
  $(document).ready(function() {
   // Initialize Datepickers
   $('.datepicker').datepicker({
    autoclose: true,
    todayHighlight: true,
    format: 'dd-mm-yyyy'
   });

   // --- UPDATED: Click handler for DF Details (Modal 1) ---
   $('.wrapper').on('click', '.btn-df-details', function(e) {
     e.preventDefault();
    
     var userId = $(this).data('user-id');
     var userName = $(this).data('user-name');
    
     var startDate = $('#start_date').val();
     var endDate = $('#end_date').val();
    
     var $modal = $('#df-modal');
     var $loader = $('#df-modal-loader');
     var $content = $('#df-modal-content');
    
     // 1. Set up modal
     $modal.find('#dfModalLabel').text('DF Details for ' + userName);
          // --- NEW: Store user-id on the modal itself for the next click ---
          $modal.data('current-user-id', userId); 
     $content.empty();
     $loader.show();
     $modal.modal('show');
    
     // 2. Make AJAX call
     $.ajax({
            // --- FIXED: Corrected URL to match controller ---
       url: '<?php echo page_url; ?>Df_reports/ajax_get_df_details/' + userId,
       type: 'GET',
       data: {
         start_date: startDate,
         end_date: endDate
       },
       dataType: 'json',
       success: function(response) {
         $loader.hide();
        
         if (response.success && response.dfs.length > 0) {
           var html = '<div class="table-responsive"><table class="table table-striped table-bordered">';
           html += '<thead><tr><th>DF Name</th><th>Marketing Person</th><th>Due Tasks</th><th>Start Date</th><th>End Date</th></tr></thead>';
           html += '<tbody>';
          
           $.each(response.dfs, function(index, df) {
             html += '<tr>';
                          // --- NEW: Added class="df-name" to find this later ---
             html += '<td class="df-name">' + df.df_name + '</td>';
             html += '<td>' + df.marketing_person + '</td>';

                          // --- UPDATED: Badge is now a clickable <a> tag if due_tasks > 0 ---
                          if(df.due_tasks > 0) {
                              html += '<td><a href="#" class="btn-task-details badge" style="background-color: #ffc107; color: #333;" data-df-id="' + df.df_id + '">' + df.due_tasks + '</a></td>';
                          } else {
                              html += '<td><span class="badge" style="background-color: #f0f0f0; color: #888;">0</span></td>';
                          }

             html += '<td>' + df.start_date + '</td>';
             html += '<td>' + df.end_date + '</td>';
             html += '</tr>';
           });
          
           html += '</tbody></table></div>';
           $content.html(html);
          
         } else if (response.success && response.dfs.length === 0) {
           $content.html('<div class="alert alert-info text-center"><i class="fa fa-info-circle"></i> No DFs found for this user in the selected period.</div>');
         } else {
           $content.html('<div class="alert alert-danger text-center"><i class="fa fa-exclamation-triangle"></i> Could not load details.</div>');
         }
       },
       error: function(xhr) {
         $loader.hide();
         console.error("AJAX Error (DF Details): ", xhr.responseText);
         $content.html('<div class="alert alert-danger text-center"><i class="fa fa-exclamation-triangle"></i> An error occurred while fetching data.</div>');
       }
     });
   });
   
      // --- NEW: Click handler for Task Details (Modal 2) ---
      // Use event delegation on the document
      $(document).on('click', '.btn-task-details', function(e) {
          e.preventDefault();

          var $taskModal = $('#task-modal');
          var $taskLoader = $('#task-modal-loader');
          var $taskContent = $('#task-modal-content');

          // 1. Get data
          var dfId = $(this).data('df-id');
          // Get user ID from the first modal
          var userId = $('#df-modal').data('current-user-id'); 
          // Get DF name from the table row
          var dfName = $(this).closest('tr').find('.df-name').text();

          // 2. Set up Task Modal
          $taskModal.find('#taskModalLabel').text('Due Tasks for ' + dfName);
          $taskContent.empty();
          $taskLoader.show();
          $taskModal.modal('show');

          // 3. Make AJAX call to get tasks
          $.ajax({
              url: '<?php echo page_url; ?>Dashboard/ajax_get_task_details', // This is the new controller function
              type: 'GET',
              data: {
                  df_id: dfId,
                  user_id: userId
              },
              dataType: 'json',
              success: function(response) {
                  $taskLoader.hide();
                  
                  if (response.success && response.tasks.length > 0) {
                      var html = '<div class="table-responsive"><table class="table table-striped">';
                      html += '<thead><tr><th>Task Name</th><th>Ticket #</th><th>Start Date</th><th>End Date</th></tr></thead>';
                      html += '<tbody>';
                      
                      $.each(response.tasks, function(index, task) {
                          html += '<tr>';
                          html += '<td>' + task.task_name + '</td>';
                          html += '<td>' + task.ticket_number + '</td>';
                          html += '<td>' + task.start_date + '</td>';
                          html += '<td>' + task.end_date + '</td>';
                          html += '</tr>';
                      });
                      
                      html += '</tbody></table></div>';
                      $taskContent.html(html);

                  } else if (response.success && response.tasks.length === 0) {
                      $taskContent.html('<div class="alert alert-info text-center">No due tasks found.</div>');
                  } else {
                      $taskContent.html('<div class="alert alert-danger text-center">Could not load tasks. ' + (response.message || '') + '</div>');
                  }
              },
              error: function(xhr) {
                  $taskLoader.hide();
                  console.error("AJAX Error (Task Details): ", xhr.responseText);
                  $taskContent.html('<div class="alert alert-danger text-center">An error occurred while fetching task data.</div>');
              }
          });
      });
      // --- End of new JS ---

  });
 </script>
</body>
</html>
