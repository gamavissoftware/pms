<?php
/**
 * name_case.php — display casing for people's names.
 *
 * `system_users` has been filled in by many hands over many years, so the same
 * list shows "AAKASH SHARMA" next to "Abhay Pathak". chat_name_case() evens
 * that out at read time, for display only — nothing is ever written back.
 *
 * These assertions exist because the rule has a deliberate SHAPE that is easy
 * to "simplify" into something worse: a plain ucwords() would turn "McDonald"
 * into "Mcdonald" and "R.K. Sharma" into "R.k. Sharma". The mixed-case
 * passthrough and the after-punctuation capital are the whole point.
 *
 *   php CHATMODULEDEVELOPMENT/tests/name_case.php
 *
 * Exit code 0 = all passed, 1 = at least one failure.
 *
 * PHP 7.4 compatible.
 */

$root = dirname(dirname(__DIR__));

defined('BASEPATH') OR define('BASEPATH', TRUE);
defined('user_profile') OR define('user_profile', 'https://example.invalid/users/');

require $root . '/application/helpers/chat_access_helper.php';

$cases = array(
    // the problem this was written for
    'AAKASH SHARMA'      => 'Aakash Sharma',
    'SHIV OM KAUSHIK'    => 'Shiv Om Kaushik',
    'AJAY PANCHAL'       => 'Ajay Panchal',
    'abhishek kumar'     => 'Abhishek Kumar',

    // already correct -> must come through untouched
    'Abhay Pathak'       => 'Abhay Pathak',
    'Manglesh Upadhyay'  => 'Manglesh Upadhyay',

    // mixed case is deliberate and must survive; a naive ucwords breaks these
    'McDonald'           => 'McDonald',
    'DeSouza'            => 'DeSouza',

    // capital after punctuation, not just after a space
    'R.K. SHARMA'        => 'R.K. Sharma',
    'MOHD. ASIF'         => 'Mohd. Asif',
    "D'SOUZA"            => "D'Souza",
    "O'BRIEN"            => "O'Brien",
    'ABDUL-RAHIM'        => 'Abdul-Rahim',

    // tidying
    '  EXTRA   SPACES  ' => 'Extra Spaces',
    ''                   => '',
    '   '                => '',

    // KNOWN LIMIT, asserted so it is a decision rather than a surprise:
    // a lowercase particle is capitalised, because an all-lowercase word is
    // exactly what needs fixing in the common case. See chat_name_case().
    'van der Berg'       => 'Van Der Berg',
);

$fail = 0;
$pass = 0;

foreach ($cases as $in => $want) {
    $got = chat_name_case($in);
    if ($got === $want) {
        printf("  ok    %-22s -> %s\n", '"' . $in . '"', '"' . $got . '"');
        $pass++;
    } else {
        printf("  FAIL  %-22s -> %s, expected %s\n",
            '"' . $in . '"', '"' . $got . '"', '"' . $want . '"');
        $fail++;
    }
}

// Idempotence: the function runs on every read, and a name that has already
// been cased must not drift if it is passed through a second time.
foreach ($cases as $in => $want) {
    $twice = chat_name_case(chat_name_case($in));
    if ($twice !== $want) {
        printf("  FAIL  not idempotent: \"%s\" -> \"%s\" on second pass\n", $in, $twice);
        $fail++;
    }
}
if (!$fail) print "  ok    idempotent on every case\n";

printf("\n%s  name_case — %d case%s, %d failed\n",
    $fail ? 'FAIL' : 'PASS', $pass, $pass === 1 ? '' : 's', $fail);

exit($fail ? 1 : 0);
