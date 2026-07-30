<?php
/**
 * guard_matrix.php — enumerate every public entry point in the module and
 * report which guards actually execute on each.
 *
 * WHY THIS EXISTS
 * ---------------
 * Two permissions were declared in the spec and enforced nowhere, and
 * both were found by accident — while checking a sentence that was about
 * to be written into a document. Reading code path by path did not
 * surface them. A table does, because an empty cell is visible.
 *
 * This resolves delegation: a public method that calls a private helper
 * inherits that helper's guards, so `approve()` shows the guards inside
 * `workflow_action()`. Brace depth is tracked, so guards do not bleed
 * from one method into the next.
 *
 *   php BOMMODULEDEVELOPMENT/tests/guard_matrix.php
 *
 * PHP 7.4 compatible.
 */

$root = dirname(dirname(__DIR__));
$file = $root . '/application/controllers/Abom.php';

/** Guard signatures, in the order they are meaningful. */
$SIGNS = array(
    'tables'    => '/require_tables\(\)/',
    'config'    => '/require_permissions_configured\(\)/',
    'perm'      => '/require_perm\(\s*.([a-z_]+)./',
    'any_perm'  => '/require_any_perm\(\)/',
    'has_perm'  => '/has_perm\(\s*.?([a-z_$\[\]\'\w]*)/',
    'ajax'      => '/json_only\(\)/',
    'wf_state'  => '/abom_qty_editable\(/',
    'wf_block'  => '/->blockers\(/',
    'wf_trans'  => '/next_transition\(/',
    'not_found' => '/show_404\(\)/',
    'own_bom'   => '/->where\(\s*.bom_id./',
    'soft_del'  => '/deleted_at IS NULL/',
);

/**
 * Parse the file into methods with their body line ranges, tracking
 * brace depth so nothing bleeds.
 */
function parse_methods($path)
{
    $lines   = file($path);
    $methods = array();
    $cur     = null;

    foreach ($lines as $i => $line) {
        if ($cur === null) {
            if (preg_match('/^\s*(public|private|protected)\s+function\s+(\w+)\s*\(/', $line, $m)) {
                $cur = array(
                    'name'       => $m[2],
                    'visibility' => $m[1],
                    'body'       => '',
                    'depth'      => 0,
                    'started'    => false,
                );
            }
            continue;
        }

        $cur['body'] .= $line;
        $opens  = substr_count($line, '{');
        $closes = substr_count($line, '}');

        if (!$cur['started'] && $opens > 0) { $cur['started'] = true; }

        $cur['depth'] += $opens - $closes;

        if ($cur['started'] && $cur['depth'] <= 0) {
            $methods[$cur['name']] = $cur;
            $cur = null;
        }
    }

    return $methods;
}

/** Guards literally present in one method body. */
function guards_in($body, array $signs)
{
    $found = array();

    foreach ($signs as $key => $re) {
        if (preg_match_all($re, $body, $m)) {
            if ($key === 'perm' && !empty($m[1])) {
                foreach (array_unique($m[1]) as $p) { $found[] = 'perm:' . $p; }
            } elseif ($key === 'has_perm') {
                $found[] = 'has_perm';
            } else {
                $found[] = $key;
            }
        }
    }

    return array_values(array_unique($found));
}

/** Private methods a body calls, so delegated guards are credited. */
function calls_in($body, array $method_names)
{
    $out = array();

    foreach ($method_names as $n) {
        if (preg_match('/\$this->' . preg_quote($n, '/') . '\s*\(/', $body)) {
            $out[] = $n;
        }
    }

    return $out;
}

$methods = parse_methods($file);
$names   = array_keys($methods);

/** Resolve guards transitively, one level of private delegation deep. */
function effective_guards($name, array $methods, array $signs, array $names, $seen = array())
{
    if (isset($seen[$name])) { return array(); }
    $seen[$name] = true;

    if (!isset($methods[$name])) { return array(); }

    $body   = $methods[$name]['body'];
    $guards = guards_in($body, $signs);

    // Follow delegation, public or private: printable() literally calls
    // view(), so it inherits view()'s guards. $seen prevents recursion.
    foreach (calls_in($body, $names) as $callee) {
        if ($callee === $name) { continue; }
        foreach (effective_guards($callee, $methods, $signs, $names, $seen) as $g) {
            $guards[] = $g;
        }
    }

    return array_values(array_unique($guards));
}

$COLS = array(
    'tables'   => 'tables_ready',
    'config'   => 'config/ids',
    'perm'     => 'permission',
    'wf'       => 'workflow',
    'object'   => 'object/id',
    'other'    => 'other',
);

echo "\nGUARD COVERAGE MATRIX — public entry points on Abom\n";
echo str_repeat('=', 108) . "\n";
printf("%-20s %-13s %-11s %-22s %-14s %-12s %s\n",
    'ENTRY POINT', 'tables_ready', 'config/ids', 'permission', 'workflow', 'object/id', 'other');
echo str_repeat('-', 108) . "\n";

$rows = array();
foreach ($methods as $name => $m) {
    if ($m['visibility'] !== 'public') { continue; }
    if ($name === '__construct') {
        $rows[$name] = array('tables' => '', 'config' => '', 'perm' => '', 'wf' => '',
                             'object' => '', 'other' => 'session logged_in');
        continue;
    }

    $g = effective_guards($name, $methods, $SIGNS, $names);

    $perm = array();
    foreach ($g as $x) { if (strpos($x, 'perm:') === 0) { $perm[] = substr($x, 5); } }
    if (in_array('any_perm', $g, true)) { $perm[] = 'any-of-3'; }

    // A has_perm() reached only via workflow_state() decides whether a
    // BUTTON is shown; it does not refuse the request. Distinguish it,
    // because counting it as enforcement is how a gap hides.
    if (in_array('has_perm', $g, true) && empty($perm)) {
        $gates = in_array('wf_trans', $g, true) && !in_array('wf_state', $g, true);
        $perm[] = $gates ? 'per-stage' : 'ui-only';
    }

    $wf = array();
    if (in_array('wf_state', $g, true)) { $wf[] = 'status'; }
    if (in_array('wf_block', $g, true)) { $wf[] = 'blockers'; }
    if (in_array('wf_trans', $g, true)) { $wf[] = 'transition'; }

    $obj = array();
    if (in_array('not_found', $g, true)) { $obj[] = '404'; }
    if (in_array('own_bom', $g, true))   { $obj[] = 'bom_id'; }
    if (in_array('soft_del', $g, true))  { $obj[] = 'not-deleted'; }

    $other = array();
    if (in_array('ajax', $g, true)) { $other[] = 'ajax-only'; }

    $rows[$name] = array(
        'tables' => in_array('tables', $g, true) ? 'yes' : '',
        'config' => in_array('config', $g, true) ? 'yes' : '',
        'perm'   => implode('+', $perm),
        'wf'     => implode('+', $wf),
        'object' => implode('+', $obj),
        'other'  => implode(', ', $other),
    );
}

foreach ($rows as $name => $r) {
    printf("%-20s %-13s %-11s %-22s %-14s %-12s %s\n",
        $name,
        $r['tables'] !== '' ? $r['tables'] : '—',
        $r['config'] !== '' ? $r['config'] : '—',
        $r['perm']   !== '' ? $r['perm']   : '—',
        $r['wf']     !== '' ? $r['wf']     : '—',
        $r['object'] !== '' ? $r['object'] : '—',
        $r['other']  !== '' ? $r['other']  : '—');
}
echo str_repeat('=', 108) . "\n";
printf("  %d public entry points. '—' means that guard does NOT execute.\n\n", count($rows));
