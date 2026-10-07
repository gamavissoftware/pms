<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'User';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['generate-barcode'] = 'BarcodeController/generate';
$route['scan-barcode'] = 'BarcodeController/scan';
$route['update-record'] = 'BarcodeController/update_record';

/*
| -------------------------------------------------------------------------
| Automation BOM Generator  (application/controllers/Abom.php)
| -------------------------------------------------------------------------
| Own URL namespace: /abom/... . The class is named Abom, not Bom, so it
| cannot collide with the BOM controller that Leads.php links to, and so
| CI's default routing does not expose a second /bom/... namespace.
| The existing DF register (DF_revision, Df_change_control,
| Df_dispatch_plan) and the Store/FMS BOM screens are untouched.
*/
$route['abom']                  = 'abom/index';
$route['abom/guide']            = 'abom/guide';
$route['abom/generate']         = 'abom/generate';   // ?build=<id> prefills from a build
$route['abom/generate_ajax']    = 'abom/generate_ajax';      // POST, AJAX
$route['abom/reference/(:any)'] = 'abom/reference/$1';
$route['abom/view/(:num)']      = 'abom/view/$1';
$route['abom/print/(:num)']     = 'abom/printable/$1';
$route['abom/save']             = 'abom/save';                // POST, AJAX
$route['abom/save_line_qty']    = 'abom/save_line_qty';        // POST, AJAX
$route['abom/list']             = 'abom/bom_list';

// Version history: every revision of one document, what each contained,
// and what changed between them. Both READS — gated on any of the three
// permissions, like view and export.
$route['abom/history/(:num)']   = 'abom/history/$1';
$route['abom/version/(:num)']   = 'abom/version/$1';   // one archived snapshot

// Approval workflow (step 6) — all POST, AJAX
$route['abom/submit/(:num)']          = 'abom/submit/$1';
$route['abom/approve/(:num)']         = 'abom/approve/$1';
$route['abom/reject/(:num)']          = 'abom/reject/$1';
$route['abom/reopen/(:num)']          = 'abom/reopen/$1';
$route['abom/create_revision/(:num)'] = 'abom/create_revision/$1';
$route['abom/acknowledge_line']       = 'abom/acknowledge_line';

// Register management. Both POST, both AJAX.
//   delete    — SOFT delete; sets deleted_at, keeps the row and its trail
//   duplicate — a NEW draft BOM with its own number; the source is left
//               untouched and current (unlike create_revision, which
//               supersedes it)
$route['abom/save_lines']             = 'abom/save_lines';        // POST, AJAX

// Re-specify a saved draft (or a clone) and re-derive its lines. What
// makes duplicate() worth having: a copy that cannot be changed is only
// a photocopy. Gated to draft/rejected in the controller.
$route['abom/save_config']            = 'abom/save_config';       // POST, AJAX

// "Is this DF reference free?", asked as the field is typed. Advisory
// only — save and save_config re-run the same check on the write.
$route['abom/check_df_ref']           = 'abom/check_df_ref';      // POST, AJAX

// Master data (build order step 8). All four gated on 'master_edit' —
// see the block comment above Abom::master().
$route['abom/master']                 = 'abom/master';
$route['abom/master/(:num)']          = 'abom/master_form/$1';
$route['abom/master_form/(:num)']     = 'abom/master_form/$1';
$route['abom/master_save']            = 'abom/master_save';       // POST

// Reference-BOM import. Three steps: form, PREVIEW (writes nothing),
// commit. All gated on 'master_edit' — this is the most consequential
// write in the module.
$route['abom/import']                 = 'abom/import';
$route['abom/import_preview']         = 'abom/import_preview';    // POST, multipart
$route['abom/import_commit']          = 'abom/import_commit';     // POST
$route['abom/import_template']        = 'abom/import_template';
$route['abom/master_toggle']          = 'abom/master_toggle';     // POST, AJAX
$route['abom/master_bom_delete']      = 'abom/master_bom_delete'; // POST, AJAX
$route['abom/delete/(:num)']          = 'abom/delete_bom/$1';
$route['abom/duplicate/(:num)']       = 'abom/duplicate/$1';

// Export (step 7) — csv | xlsx | pdf
$route['abom/export/(:any)/(:num)']   = 'abom/export/$1/$2';

/*
| -------------------------------------------------------------------------
| DF Progress Gantt  (application/controllers/Gantt_chart.php)
| -------------------------------------------------------------------------
| Short /gantt/<df_id> namespace for the rebuilt board. The controller also
| answers on its own name, so /index.php/Gantt_chart/index/<df_id> works too.
| Task::finalgantchartwithDetails() now redirects here, which upgrades every
| dashboard link at once; ?legacy=1 still opens the old chart.
*/
$route['gantt/(:num)']                = 'gantt_chart/index/$1';
$route['gantt/export/(:any)/(:num)']  = 'gantt_chart/export/$1/$2';   // csv | xlsx | pdf
$route['gantt/export/(:num)']         = 'gantt_chart/export/csv/$1';  // original CSV link
$route['gantt/tasks']                 = 'gantt_chart/tasks';          // POST, AJAX
$route['gantt/tickets']               = 'gantt_chart/tickets';        // POST, AJAX

/*
| -------------------------------------------------------------------------
| CHAT  (application/controllers/Chat.php)
| -------------------------------------------------------------------------
| Model: Chat_model.php  |  Views: application/views/chatmodule/
| Helper: chat_access_helper.php  |  Schema: Database/chat_001_schema.sql
|
| Only friendly aliases live here — every controller method is already
| reachable at /Chat/<method>, which is what the module's own JS calls.
*/
$route['chat']                        = 'Chat/index';
$route['chat/open/(:num)']            = 'Chat/index/$1';

// Find-or-create the group for one DF, seeded with the person who raised it
// and everyone holding a task on it. This is the "Discuss this DF" entry
// point — see Chat::df().
$route['chat/df/(:num)']              = 'Chat/df/$1';

// Backfill screen for DFs released before groups became automatic.
// Inside the marked block, like the rest of the module's routes — it sat
// below the end marker, where a tidy-up that trusts the markers would drop
// it and take Chat::df_groups() offline with no other sign.
$route['chat/df-groups']              = 'Chat/df_groups';

// One group's attachments as a single .zip, built when the group was
// archived. Membership-checked in the controller — see Chat::archive_zip().
$route['chat/archive/(:num)']         = 'Chat/archive_zip/$1';
/* end chat module routes */

/* Department Google Sheet directory */
$route['department-sheets']                 = 'Department_sheet_links/index';
$route['department-sheets/save']            = 'Department_sheet_links/save';
$route['department-sheets/delete/(:num)']   = 'Department_sheet_links/delete/$1';
