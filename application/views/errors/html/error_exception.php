<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$show_debug = defined('ENVIRONMENT') && ENVIRONMENT === 'development';
$error_label = 'Unexpected application error';
$error_title = 'Something interrupted this page';
$error_message = 'The request stopped unexpectedly. Please refresh the page. If the issue continues, support can investigate it with the reference shown below.';
$error_reference = date('d M Y h:i A');
$error_debug_items = $show_debug ? array(
    array('label' => 'Type', 'value' => get_class($exception)),
    array('label' => 'Message', 'value' => (string) $message),
    array('label' => 'File', 'value' => (string) $exception->getFile()),
    array('label' => 'Line', 'value' => (string) $exception->getLine())
) : array();

include __DIR__ . '/_friendly_error_page.php';
