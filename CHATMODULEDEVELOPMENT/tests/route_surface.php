<?php
/**
 * route_surface.php — does every URL the module can produce actually exist?
 *
 * THE BUG CLASS THIS EXISTS TO CATCH
 * ----------------------------------
 * The messenger is one large view that talks to the controller through
 * `BASE + 'endpoint'` strings. Nothing connects those strings to the PHP
 * methods they name: rename a method and the call site keeps compiling,
 * keeps loading, and fails as a 404 the moment a user presses that one
 * button. The port renamed `Chat::job()` to `Chat::df()`, so this is not
 * hypothetical.
 *
 * Three surfaces are checked:
 *   1. every $route['chat/...'] target resolves to a public method
 *   2. every BASE + '<endpoint>' in the three views resolves to one
 *   3. every site_url()/page_url target the MODEL builds for a record card
 *      resolves to a real controller method elsewhere in PMS
 *
 * (3) is what catches the CoreTech lead URL: `Leads/update_lead_information`
 * exists as a method, but it is the form's POST target rather than a page —
 * a class of mistake only a human can judge, so the check reports the method
 * it resolved to and the reviewer confirms it renders something.
 *
 *   php CHATMODULEDEVELOPMENT/tests/route_surface.php
 *
 * Exit code 0 = all resolved, 1 = at least one dead target.
 *
 * PHP 7.4 compatible.
 */

$root = dirname(dirname(__DIR__));
$fail = 0;
$pass = 0;

/** public method names declared in a controller file */
function methods_of($file)
{
    if (!is_file($file)) return NULL;
    $src = file_get_contents($file);
    preg_match_all('/^\s*public\s+function\s+([a-zA-Z0-9_]+)\s*\(/m', $src, $m);
    return array_map('strtolower', $m[1]);
}

$chat_methods = methods_of($root . '/application/controllers/Chat.php');
if ($chat_methods === NULL) {
    fwrite(STDERR, "FATAL: application/controllers/Chat.php not found\n");
    exit(2);
}

// =====================================================================
// 1. ROUTES
// =====================================================================
print "routes:\n";
$routes_src = file_get_contents($root . '/application/config/routes.php');
preg_match_all(
    "/\\\$route\\['chat[^']*'\\]\s*=\s*'([^']+)'/i",
    $routes_src, $m
);
if (empty($m[1])) {
    print "  FAIL  no chat routes found in application/config/routes.php\n";
    $fail++;
}
foreach ($m[1] as $target) {
    // 'Chat/index/$1' -> method 'index'
    $parts  = explode('/', $target);
    $method = isset($parts[1]) ? strtolower($parts[1]) : 'index';
    if (strcasecmp($parts[0], 'chat') !== 0) {
        printf("  FAIL  route target %s does not point at the Chat controller\n", $target);
        $fail++;
        continue;
    }
    if (in_array($method, $chat_methods, TRUE)) {
        printf("  ok    %-28s -> Chat::%s()\n", $target, $method);
        $pass++;
    } else {
        printf("  FAIL  %-28s -> Chat::%s() DOES NOT EXIST\n", $target, $method);
        $fail++;
    }
}

// =====================================================================
// 2. THE VIEWS' AJAX SURFACE
// =====================================================================
print "\nview endpoints:\n";
$views = array(
    'chatmodule/index.php',
    'chatmodule/_dock.php',
    'chatmodule/_navwidget.php',
);
$seen = array();
foreach ($views as $v) {
    $src = @file_get_contents($root . '/application/views/' . $v);
    if ($src === FALSE) {
        printf("  FAIL  missing view %s\n", $v);
        $fail++;
        continue;
    }
    // TWO shapes reach the controller, and missing either defeats the point
    // of this test:
    //
    //   $.getJSON(BASE + 'directory')   -> matched by the BASE pattern
    //   post('ai_polish', {...})        -> the helper is `function post(url,
    //                                      data){ return $.post(BASE + url,
    //                                      data); }`, so the endpoint name
    //                                      never appears next to BASE
    //
    // The second form is how MOST of the messenger talks to the server. An
    // earlier version of this file matched only the first, so those call
    // sites were silently unchecked while the summary line still said
    // everything resolved.
    $patterns = array(
        '/BASE\s*\+\s*[\'"]([a-zA-Z0-9_\/]+)[\'"]/',   // BASE + 'endpoint'
        '/\bpost\(\s*[\'"]([a-zA-Z0-9_\/]+)[\'"]/',     // post('endpoint', ...)
    );
    foreach ($patterns as $re) {
        preg_match_all($re, $src, $mm);
        foreach ($mm[1] as $ep) {
            $method = strtolower(explode('/', $ep)[0]);
            if ($method === '') continue;
            if (isset($seen[$method])) continue;
            $seen[$method] = $v;
        }
    }
}
ksort($seen);
foreach ($seen as $method => $v) {
    if (in_array($method, $chat_methods, TRUE)) {
        printf("  ok    %-20s -> Chat::%s()\n", $method, $method);
        $pass++;
    } else {
        printf("  FAIL  %-20s called from %s but Chat::%s() DOES NOT EXIST\n", $method, $v, $method);
        $fail++;
    }
}

// =====================================================================
// 3. RECORD-CARD LINKS INTO THE REST OF PMS
// =====================================================================
print "\nrecord card links:\n";
$model = file_get_contents($root . '/application/models/Chat_model.php');

// Two shapes appear in the model:
//   site_url('Leads/edit_leads/' . $id)   -> controller + literal method
//   site_url('gantt/' . $id)              -> a route ALIAS carrying the id
// The second group is empty for the alias form, which is how they are told
// apart. Matching only the first form would silently skip every alias link —
// the DF and task cards are all alias links, so that would be most of them.
preg_match_all("/site_url\\('([A-Za-z_]+)\\/([a-zA-Z0-9_]*)/", $model, $mm, PREG_SET_ORDER);

// routes.php aliases, e.g. $route['gantt/(:num)'] = 'gantt_chart/index/$1'
$aliases = array();
preg_match_all(
    "/\\\$route\\['([a-z_]+)\\/[^']*'\\]\s*=\s*'([a-z_]+)\\/([a-z_]+)/i",
    $routes_src, $ra, PREG_SET_ORDER
);
foreach ($ra as $r) {
    $key = strtolower($r[1]);
    if (!isset($aliases[$key])) $aliases[$key] = array(strtolower($r[2]), strtolower($r[3]));
}

$checked = array();
foreach ($mm as $hit) {
    $seg1 = $hit[1];
    $seg2 = isset($hit[2]) ? $hit[2] : '';
    $key  = $seg1 . '/' . ($seg2 !== '' ? $seg2 : '<id>');
    if (isset($checked[$key])) continue;
    $checked[$key] = TRUE;

    $note = '';
    if ($seg2 !== '') {
        // A literal method was named. Resolve it directly — do NOT consult
        // the alias table, or 'Chat/download' would be rewritten by the
        // 'chat' alias into Chat::index() and the real target never checked.
        $target_ctrl = strtolower($seg1);
        $method      = strtolower($seg2);
    } elseif (isset($aliases[strtolower($seg1)])) {
        list($target_ctrl, $method) = $aliases[strtolower($seg1)];
        $note = sprintf('via route alias %s/', strtolower($seg1));
    } else {
        printf("  FAIL  %-34s no literal method and no route alias for '%s/'\n", $key, $seg1);
        $fail++;
        continue;
    }

    $file = NULL;
    foreach (glob($root . '/application/controllers/*.php') as $cand) {
        if (strcasecmp(basename($cand, '.php'), $target_ctrl) === 0) { $file = $cand; break; }
    }
    if ($file === NULL) {
        printf("  FAIL  %-34s controller %s not found\n", $key, $target_ctrl);
        $fail++;
        continue;
    }
    $ms = methods_of($file);
    if (in_array($method, $ms, TRUE)) {
        printf("  ok    %-34s -> %s::%s() %s\n", $key, basename($file, '.php'), $method, $note);
        $pass++;
    } else {
        printf("  FAIL  %-34s -> %s::%s() DOES NOT EXIST\n", $key, basename($file, '.php'), $method);
        $fail++;
    }
}

printf("\n%s  route_surface — %d resolved, %d dead\n",
    $fail ? 'FAIL' : 'PASS', $pass, $fail);

exit($fail ? 1 : 0);
