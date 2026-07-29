<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$show_debug = false;
$error_label = 'Page not found';
$error_title = 'We could not find that page';
$error_message = 'The link may have changed or the page may no longer be available. Please return home and continue from the main dashboard.';
$error_reference = date('d M Y h:i A');
$error_debug_items = array();

include __DIR__ . '/_friendly_error_page.php';
