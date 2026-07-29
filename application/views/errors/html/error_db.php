<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$show_debug = defined('ENVIRONMENT') && ENVIRONMENT === 'development';
$error_label = 'Database connection issue';
$error_title = 'We are reconnecting the page';
$error_message = 'The page could not reach its data source just now. Please refresh once or try again in a moment.';
$error_reference = date('d M Y h:i A');
$error_debug_items = $show_debug ? array(
    array('label' => 'Heading', 'value' => trim(strip_tags((string) $heading))),
    array('label' => 'Message', 'value' => trim(preg_replace('/\s+/', ' ', strip_tags((string) $message))))
) : array();

include __DIR__ . '/_friendly_error_page.php';
