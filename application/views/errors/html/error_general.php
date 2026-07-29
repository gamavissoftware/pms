<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$show_debug = defined('ENVIRONMENT') && ENVIRONMENT === 'development';
$error_label = 'Page temporarily unavailable';
$error_title = 'This screen needs a quick retry';
$error_message = 'Something interrupted the request before the page finished loading. Please refresh once or return to the previous screen.';
$error_reference = date('d M Y h:i A');
$error_debug_items = $show_debug ? array(
    array('label' => 'Heading', 'value' => trim(strip_tags((string) $heading))),
    array('label' => 'Message', 'value' => trim(preg_replace('/\s+/', ' ', strip_tags((string) $message))))
) : array();

include __DIR__ . '/_friendly_error_page.php';
