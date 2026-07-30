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
$route['abom/generate']         = 'abom/generate';
$route['abom/generate_ajax']    = 'abom/generate_ajax';      // POST, AJAX
$route['abom/reference/(:any)'] = 'abom/reference/$1';
$route['abom/view/(:num)']      = 'abom/view/$1';
$route['abom/print/(:num)']     = 'abom/printable/$1';
$route['abom/save']             = 'abom/save';                // POST, AJAX
$route['abom/save_line_qty']    = 'abom/save_line_qty';        // POST, AJAX
$route['abom/list']             = 'abom/bom_list';

// Approval workflow (step 6) — all POST, AJAX
$route['abom/submit/(:num)']          = 'abom/submit/$1';
$route['abom/approve/(:num)']         = 'abom/approve/$1';
$route['abom/reject/(:num)']          = 'abom/reject/$1';
$route['abom/reopen/(:num)']          = 'abom/reopen/$1';
$route['abom/create_revision/(:num)'] = 'abom/create_revision/$1';
$route['abom/acknowledge_line']       = 'abom/acknowledge_line';

// Export (step 7) — csv | xlsx | pdf
$route['abom/export/(:any)/(:num)']   = 'abom/export/$1/$2';
