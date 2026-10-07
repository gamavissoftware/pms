<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*

|--------------------------------------------------------------------------

| Display Debug backtrace

|--------------------------------------------------------------------------

|

| If set to TRUE, a backtrace will be displayed along with php errors. If

| error_reporting is disabled, the backtrace will not display, regardless

| of this setting

|

*/

defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*

|--------------------------------------------------------------------------

| File and Directory Modes

|--------------------------------------------------------------------------

|

| These prefs are used when checking and setting modes when working

| with the file system.  The defaults are fine on servers with proper

| security, but you may wish (or even need) to change the values in

| certain environments (Apache running a separate process for each

| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should

| always be used to set the mode correctly.

|

*/

defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);

defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);

defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);

defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*

|--------------------------------------------------------------------------

| File Stream Modes

|--------------------------------------------------------------------------

|

| These modes are used when working with fopen()/popen()

|

*/

defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');

defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');

defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care

defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care

defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');

defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');

defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');

defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');



/*

|--------------------------------------------------------------------------

| Exit Status Codes

|--------------------------------------------------------------------------

|

| Used to indicate the conditions under which the script is exit()ing.

| While there is no universal standard for error codes, there are some

| broad conventions.  Three such conventions are mentioned below, for

| those who wish to make use of them.  The CodeIgniter defaults were

| chosen for the least overlap with these conventions, while still

| leaving room for others to be defined in future versions and user

| applications.

|

| The three main conventions used for determining exit status codes

| are as follows:

|

|    Standard C/C++ Library (stdlibc):

|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html

|       (This link also contains other GNU-specific conventions)

|    BSD sysexits.h:

|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits

|    Bash scripting:

|       http://tldp.org/LDP/abs/html/exitcodes.html

|

*/

defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors

defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error

defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code
defined('assets_url') OR define('assets_url','https://'.$_SERVER['HTTP_HOST'].'/assets/');
defined('dashboard_asset_url') OR define('dashboard_asset_url','https://'.$_SERVER['HTTP_HOST'].'/styleasset/');
defined('assets_upload') OR define('assets_upload',$_SERVER['DOCUMENT_ROOT'].'/assets/');
defined('page_url') OR define('page_url','https://'.$_SERVER['HTTP_HOST'].'/index.php/');
defined('softwarepath') OR define('softwarepath','https://'.$_SERVER['HTTP_HOST'].'/');
defined('site_http_root') OR define('site_http_root','https://'.$_SERVER['HTTP_HOST'].'/');
defined('image_url') OR define('image_url','https://'.$_SERVER['HTTP_HOST'].'/upload');
defined('user_profile') OR define('user_profile','https://'.$_SERVER['HTTP_HOST'].'/image_bank/users/');
defined('product_path') OR define('product_path','https://'.$_SERVER['HTTP_HOST'].'/image_bank/compititor/product_img/');
defined('report_url') OR define('report_url','https://'.$_SERVER['HTTP_HOST'].'/image_bank/audit_report/');
defined('techpath') OR define('techpath','https://'.$_SERVER['HTTP_HOST'].'/image_bank/techsupport/');
defined('maintenance') OR define('maintenance','https://'.$_SERVER['HTTP_HOST'].'/image_bank/maintenance/');
defined('itassets_path') OR define('itassets_path','https://'.$_SERVER['HTTP_HOST'].'/image_bank/itassets/');
defined('qc_checklist') OR define('qc_checklist','https://'.$_SERVER['HTTP_HOST'].'/image_bank/qcchecklist/');
defined('dynamicformdata') OR define('dynamicformdata','https://'.$_SERVER['HTTP_HOST'].'/image_bank/dynamicfile/');
defined('delegationfile') OR define('delegationfile','https://'.$_SERVER['HTTP_HOST'].'/image_bank/delegation/');
defined('referencefilepath') OR define('referencefilepath','https://'.$_SERVER['HTTP_HOST'].'/image_bank/reference/');
defined('eventimgpath') OR define('eventimgpath','https://'.$_SERVER['HTTP_HOST'].'/image_bank/eventimg/');
defined('plugins_url') OR define('plugins_url','https://'.$_SERVER['HTTP_HOST'].'/assets/plugins/');
defined('tourbills') OR define('tourbills','https://'.$_SERVER['HTTP_HOST'].'/image_bank/tourbills/');
defined('product_items') OR define('product_items','https://'.$_SERVER['HTTP_HOST'].'/image_bank/product_item/');
defined('housekeeping') OR define('housekeeping','https://'.$_SERVER['HTTP_HOST'].'/image_bank/housekeeping/');
defined('sale_visit') OR define('sale_visit','https://'.$_SERVER['HTTP_HOST'].'/image_bank/sale_visit/');
defined('selfiepath') OR define('selfiepath','https://'.$_SERVER['HTTP_HOST'].'/image_bank/attendance/');
defined('service_visit_report') OR define('service_visit_report','https://'.$_SERVER['HTTP_HOST'].'/image_bank/visitreport/');
defined('dashboardimg') OR define('dashboardimg','https://'.$_SERVER['HTTP_HOST'].'/image_bank/dashboardimg/');
defined('homepage_image') OR define('homepage_image','https://'.$_SERVER['HTTP_HOST'].'/image_bank/home/');
defined('SITE_ROOT') OR define ('SITE_ROOT', $_SERVER['DOCUMENT_ROOT'].'/');
defined('UPLOADPATH') OR define('UPLOADPATH',$_SERVER['DOCUMENT_ROOT'].'/image_bank/');
defined('docketpath') OR define('docketpath','https://'.$_SERVER['HTTP_HOST'].'/image_bank/dynamicfile/docket/');
defined('anytimerejection') OR define('anytimerejection','https://'.$_SERVER['HTTP_HOST'].'/image_bank/anytime_rejection/');
defined('quotationimg') OR define('quotationimg','https://'.$_SERVER['HTTP_HOST'].'/image_bank/quotation/');
defined('daily_reporting') OR define('daily_reporting','https://'.$_SERVER['HTTP_HOST'].'/image_bank/daily_reporting/');
defined('instrumentimg') OR define('instrumentimg','https://'.$_SERVER['HTTP_HOST'].'/image_bank/instrumentimg/');
defined('calibration_image') OR define('calibration_image','https://'.$_SERVER['HTTP_HOST'].'/image_bank/calibration/');
defined('SEOUPLOADPATH') OR define('SEOUPLOADPATH',$_SERVER['DOCUMENT_ROOT'].'/assets/images/seo_images/');
defined('upload_url') OR define('upload_url',$_SERVER['DOCUMENT_ROOT'].'/upload/');
defined('sfdocument') OR define('sfdocument','https://'.$_SERVER['HTTP_HOST'].'/image_bank/');
defined('sfpo') OR define('sfpo',$_SERVER['DOCUMENT_ROOT'].'/sfpo/');
defined('sitetitle') OR define('sitetitle','SHUBHAM PACKAGING PMS');
defined('copyright') OR define('copyright','GAMAVIS SOFTECH LLP');
defined('companylogo') OR define('companylogo','https://'.$_SERVER['HTTP_HOST'].'/image_bank/registration/');
defined('dashboard_icon') OR define('dashboard_icon','https://'.$_SERVER['HTTP_HOST'].'/image_bank/dashboard_icon/');
defined('form_icons') OR define('form_icons','https://'.$_SERVER['HTTP_HOST'].'/image_bank/form_icons/');
defined('exceluploads') OR define('exceluploads',$_SERVER['DOCUMENT_ROOT'].'/image_bank/excel/');
defined('evidence_image') OR define('evidence_image','https://'.$_SERVER['HTTP_HOST'].'/image_bank/checklist/');
defined('invoiceattachment') OR define('invoiceattachment','https://'.$_SERVER['HTTP_HOST'].'/image_bank/storefile/');
defined('whatsappuser') OR define('whatsappuser','shubhamflexible');
defined('whatsapppass') OR define('whatsapppass','$shuBhampck@21');
defined('whatsappuser1') OR define('whatsappuser1','shubhampacktaskmanagement');
defined('whatsapppass1') OR define('whatsapppass1','$shuBhampckTask@2024');
defined('whatsappuser2') OR define('whatsappuser2','shubhamsharma');
defined('whatsapppass2') OR define('whatsapppass2','Shubham#59@');
defined('sap_service_layer_base_url') OR define('sap_service_layer_base_url', getenv('SAP_SERVICE_LAYER_BASE_URL') ?: 'https://analytics23.ubshanacloud.in:50000/b1s/v1');
defined('sap_company_db') OR define('sap_company_db', getenv('SAP_COMPANY_DB') ?: '');
defined('sap_username') OR define('sap_username', getenv('SAP_USERNAME') ?: '');
defined('sap_password') OR define('sap_password', getenv('SAP_PASSWORD') ?: '');
defined('sap_customer_series') OR define('sap_customer_series', getenv('SAP_CUSTOMER_SERIES') ?: 109);
defined('sap_customer_group_code') OR define('sap_customer_group_code', getenv('SAP_CUSTOMER_GROUP_CODE') ?: 100);
defined('sap_customer_currency') OR define('sap_customer_currency', getenv('SAP_CUSTOMER_CURRENCY') ?: 'INR');
defined('sap_customer_sales_person_code') OR define('sap_customer_sales_person_code', getenv('SAP_CUSTOMER_SALES_PERSON_CODE') ?: -1);
defined('sap_customer_pay_terms_group_code') OR define('sap_customer_pay_terms_group_code', getenv('SAP_CUSTOMER_PAY_TERMS_GROUP_CODE') ?: '');
defined('sap_customer_debitor_account') OR define('sap_customer_debitor_account', getenv('SAP_CUSTOMER_DEBITOR_ACCOUNT') ?: '');
defined('sap_ssl_verify') OR define('sap_ssl_verify', getenv('SAP_SSL_VERIFY') === '1');
/* Overtime module outgoing mail. Point these at whichever automated PMS mailbox should
   send overtime notifications. Set overtime_mail_pass to the app password for that
   account - the same one application/controllers/Auto_reports.php already uses for the
   task reports. With it empty, overtime mail is logged as FAILED and nothing else breaks. */
defined('overtime_mail_host') OR define('overtime_mail_host','ssl://smtp.googlemail.com');
defined('overtime_mail_port') OR define('overtime_mail_port',465);
defined('overtime_mail_user') OR define('overtime_mail_user','taskmanagement@shubhampack.com');
defined('overtime_mail_pass') OR define('overtime_mail_pass','ficihlqnfcdrrqkb');
defined('page_url22') OR define('page_url22','https://'.$_SERVER['HTTP_HOST'].'/');
defined('page_url1') OR define('page_url1','https://'.$_SERVER['HTTP_HOST'].'/');
defined('lead_uploads') OR define('lead_uploads',$_SERVER['DOCUMENT_ROOT'].'/assets/lead_uploads/');
defined('lead_pictures') OR define('lead_pictures','https://'.$_SERVER['HTTP_HOST'].'/assets/lead_uploads/');
defined('iomfilesdata') OR define('iomfilesdata','https://'.$_SERVER['HTTP_HOST'].'/image_bank/iomfile/');
defined('uploadsurl') OR define('uploadsurl','https://'.$_SERVER['HTTP_HOST'].'/uploads/');
/* ===== CHAT MODULE ==================================================
 |  Uploads. Attachments live under image_bank/, where every other
 |  PMS module keeps its files, NOT under assets/ — assets/ is served
 |  as static site content and carries no upload conventions here.
 |
 |  Nothing in this directory is ever served directly by name: the
 |  .htaccess inside it disables script execution, and downloads go
 |  through Chat/download/<id>, which checks conversation membership
 |  first. The randomised stored_name means a leaked URL cannot be
 |  guessed from the original filename either.
 |==================================================================== */
defined('chat_upload_path') OR define('chat_upload_path', $_SERVER['DOCUMENT_ROOT'].'/image_bank/chat_uploads/');
defined('chat_upload_url')  OR define('chat_upload_url', 'https://'.$_SERVER['HTTP_HOST'].'/image_bank/chat_uploads/');

/* ---------------------------------------------------------------------
 |  GO-LIVE SWITCH.  TRUE = Chat is live for everyone: floating dock on
 |  every page, sidebar link, topbar icon and unread badge.
 |
 |  FALSE (the default) hides every entry point but leaves the
 |  controller running, so /index.php/Chat can still be opened by URL
 |  to pilot the module before announcing it.
 |
 |  Requires Database/chat_001_schema.sql to have been imported first.
 |------------------------------------------------------------------- */
defined('CHAT_MODULE_VISIBLE') OR define('CHAT_MODULE_VISIBLE', TRUE);

/* ---------------------------------------------------------------------
 |  ARCHIVED DF GROUPS.
 |
 |  When a DF is dispatched its group is archived and every attachment
 |  in it is collected into ONE .zip, so the room's files stop costing
 |  storage as a few hundred loose originals.
 |
 |  The zip lives INSIDE chat_uploads/ on purpose: the .htaccess in
 |  that directory already disables script execution, so the bundle
 |  inherits the protection rather than needing its own. It is still
 |  never served by name — the download goes through
 |  Chat/archive_zip/<id>, which checks membership first.
 |
 |  GRACE, in days, between a group being archived and its ORIGINAL
 |  files being deleted from disk. The zip is built and verified
 |  immediately; this is only about when the loose copies go. Until the
 |  grace runs out, unarchiving costs nothing and a mistake is free.
 |
 |    30  the default: a month to notice a group was archived wrongly
 |     0  delete as soon as the zip is verified - maximum storage back,
 |        and the zip becomes the only copy that moment
 |    -1  never delete. The zip is then only a convenience download and
 |        NO storage is saved.
 |------------------------------------------------------------------- */
defined('chat_archive_dir')        OR define('chat_archive_dir', '_archives/');
defined('CHAT_ARCHIVE_GRACE_DAYS') OR define('CHAT_ARCHIVE_GRACE_DAYS', 30);
/* end chat module constants */
