<?php
define('BASEPATH', __DIR__);

class CI_Model
{
}

require dirname(__DIR__, 2) . '/application/models/Department_sheet_link_model.php';

$cases = array(
    'https://docs.google.com/spreadsheets/d/abc123/edit#gid=0' => true,
    'https://docs.google.com/spreadsheets/u/0/d/abc123/edit' => true,
    'http://docs.google.com/spreadsheets/d/abc123/edit' => false,
    'https://docs.google.com/document/d/abc123/edit' => false,
    'https://docs.google.com.evil.example/spreadsheets/d/abc123/edit' => false,
    'https://evil.example/spreadsheets/d/abc123/edit' => false,
    'javascript:alert(1)' => false,
    '' => false,
);

foreach ($cases as $url => $expected) {
    $actual = Department_sheet_link_model::valid_google_sheet_url($url);
    if ($actual !== $expected) {
        fwrite(STDERR, 'Validation failed for: ' . $url . PHP_EOL);
        exit(1);
    }
}

echo 'Department Sheet link regression checks passed.' . PHP_EOL;
