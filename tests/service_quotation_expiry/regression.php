<?php
// Run: php tests/service_quotation_expiry/regression.php (no application database needed).
define('BASEPATH', __DIR__);

class CI_Model
{
}

require dirname(__DIR__, 2) . '/application/models/Service_quotation_expiry_model.php';

function check_service_quote_expiry($condition, $message)
{
    if (!$condition) {
        throw new Exception($message);
    }
}

$cutoff_cases = array(
    '2026-09-22' => '2026-08-22',
    '2026-03-31' => '2026-02-28',
    '2024-03-31' => '2024-02-29',
    '2026-01-31' => '2025-12-31',
    'invalid-date' => null
);

foreach ($cutoff_cases as $as_of_date => $expected_cutoff) {
    $actual_cutoff = Service_quotation_expiry_model::expiry_cutoff_date($as_of_date);
    check_service_quote_expiry(
        $actual_cutoff === $expected_cutoff,
        'Unexpected one-month cutoff for ' . $as_of_date . ': ' . var_export($actual_cutoff, true)
    );
}

$controller_source = file_get_contents(dirname(__DIR__, 2) . '/application/controllers/ServiceLeads.php');
$dashboard_model_source = file_get_contents(dirname(__DIR__, 2) . '/application/models/Dashboard_model.php');
$list_view_source = file_get_contents(dirname(__DIR__, 2) . '/application/views/spares/service_opportunity_list_view.php');

check_service_quote_expiry(
    substr_count($controller_source, 'refresh_expired_service_quotations();') >= 3,
    'Expiry refresh must run before the dashboard, list, and detail views.'
);
check_service_quote_expiry(
    strpos($controller_source, 'reopen_cancelled_quotation') !== false,
    'Reopen endpoint is missing.'
);
check_service_quote_expiry(
    strpos($dashboard_model_source, "'cancelled quotation', 'po received', 'order won', 'create pi'") !== false,
    'Terminal stages must be excluded from follow-up counts.'
);
check_service_quote_expiry(
    strpos($list_view_source, 'Reopen') !== false,
    'Cancelled quotation list must expose the reopen action.'
);

echo "PASS: service quotation expiry cutoff, refresh, follow-up exclusion, and reopen wiring.\n";
