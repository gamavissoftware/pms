<?php
// Run: php tests/spare_quotation_expiry/regression.php (no application database needed).
define('BASEPATH', __DIR__);

class CI_Model
{
}

require dirname(__DIR__, 2) . '/application/models/Spare_quotation_expiry_model.php';

function check_spare_quote_expiry($condition, $message)
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
    $actual_cutoff = Spare_quotation_expiry_model::expiry_cutoff_date($as_of_date);
    check_spare_quote_expiry(
        $actual_cutoff === $expected_cutoff,
        'Unexpected one-month cutoff for ' . $as_of_date . ': ' . var_export($actual_cutoff, true)
    );
}

$spares_controller = file_get_contents(dirname(__DIR__, 2) . '/application/controllers/Spares.php');
$dashboard_controller = file_get_contents(dirname(__DIR__, 2) . '/application/controllers/Dashboard.php');
$dashboard_model = file_get_contents(dirname(__DIR__, 2) . '/application/models/Dashboard_model.php');
$list_view = file_get_contents(dirname(__DIR__, 2) . '/application/views/spares/opportunity_list_view.php');
$stage_view = file_get_contents(dirname(__DIR__, 2) . '/application/views/spares/opportunity_stage_wise_view.php');
$detail_view = file_get_contents(dirname(__DIR__, 2) . '/application/views/spares/opportunity_detail_view.php');

check_spare_quote_expiry(
    substr_count($spares_controller, 'refresh_expired_spare_quotations();') >= 4,
    'Expiry refresh must run before Spares list, stage, detail, and follow-up views.'
);
check_spare_quote_expiry(
    strpos($dashboard_controller, 'expire_stale_quotations') !== false,
    'The Spares dashboard must refresh stale quotations before rendering.'
);
check_spare_quote_expiry(
    strpos($spares_controller, 'reopen_cancelled_quotation') !== false,
    'Reopen endpoint is missing.'
);
check_spare_quote_expiry(
    strpos($dashboard_model, "'cancelled quotation', 'order won', 'lead lost'") !== false,
    'Terminal Spares stages must be excluded from follow-up counts.'
);
check_spare_quote_expiry(
    strpos($list_view, 'Reopen') !== false && strpos($stage_view, 'Reopen') !== false,
    'Both Spares opportunity tables must expose the reopen action.'
);
check_spare_quote_expiry(
    strpos($detail_view, 'Quotation Automatically Cancelled') !== false,
    'The Spares detail view must explain the cancellation and reopen flow.'
);

echo "PASS: spare quotation expiry cutoff, refresh, follow-up exclusion, guards, and reopen wiring.\n";
