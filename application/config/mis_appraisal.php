<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| MIS appraisal issue-rate overrides.
|
| Format: user_id => issue rate percentage (0-100).
| Remove a user from this array to return to the calculated dynamic score.
| Example: 147 => 14 means 14% pending/delayed and 86% performance.
*/
$config['mis_issue_rate_overrides'] = array(
    147 => 14,
);

