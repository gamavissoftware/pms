<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * guide.php — user guide / training screen.
 *
 * The application chrome is not loaded here, for the same reasons as the
 * other module screens (see the comment at the top of generate.php).
 *
 * EVERYTHING FACTUAL ON THIS PAGE COMES FROM THE DATABASE OR THE ENGINE.
 * The formula list, the PLC rules, the feature switches, the item counts
 * and both worked examples are read or computed as the page renders. A
 * guide that restates the rules in prose is wrong the first time someone
 * edits abom_plc_rule, and a training document that is quietly wrong is
 * worse than no document.
 *
 * @var array $formulas  code => row, with ->item_count
 * @var array $rules     readable PLC selection rules, priority order
 * @var array $features  feature switches, with ->item_count
 * @var array $families  PLC families
 * @var array $stages    the workflow ladder from Abom_approval_model
 * @var array $examples  live engine output for the two reference machines
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> Automation BOM — User Guide</title>
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>abom/abom.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>abom/abom-print.css" rel="stylesheet" media="all">
</head>
<body>

<div class="abom-wrap">

  <header class="app-header">
    <a class="abom-home" href="<?php echo page_url; ?>Dashboard" title="Back to Dashboard">
      <span class="abom-home-icon" aria-hidden="true">&#8962;</span>
      <span class="abom-home-text">Dashboard</span>
    </a>
    <div>
      <h1>&#9881;&#65039; Automation BOM Generator</h1>
      <div class="sub">User guide &amp; training reference</div>
    </div>
    <span class="badge">GUIDE</span>
  </header>

  <div class="layout">
    <main class="main">

      <div class="abom-topbar">
        <span class="config-chip">Training reference</span>
        <span class="config-chip is-on">Live — reads the running configuration</span>
        <div class="abom-actions">
          <a class="btn-sm btn-generate" href="<?php echo page_url; ?>abom/generate">Open the generator</a>
          <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/list">Saved BOMs</a>
          <button type="button" class="btn-sm btn-print" onclick="window.print()">&#128424;&#65039; Print this guide</button>
        </div>
      </div>

      <div class="abom-area">
        <div id="abomContent" class="abom-guide-doc">

          <div class="doc-title">
            &#128218; <b>AUTOMATION BOM GENERATOR — HOW IT WORKS</b>
            <span class="dt-date">Everything below is read live from the system</span>
          </div>

          <!-- ============================ CONTENTS ==================== -->
          <nav class="abom-toc">
            <b>Contents</b>
            <ol>
              <li><a href="#g1">The one idea to understand first</a></li>
              <li><a href="#g2">Making a BOM, step by step</a></li>
              <li><a href="#g3">How the PLC family is chosen</a></li>
              <li><a href="#g4">The quantity formulas</a></li>
              <li><a href="#g5">Machine feature switches</a></li>
              <li><a href="#g6">Reading the sheet — colours and badges</a></li>
              <li><a href="#g7">Changing a quantity</a></li>
              <li><a href="#g8">Before you can submit</a></li>
              <li><a href="#g9">The approval workflow</a></li>
              <li><a href="#g10">Printing and exporting</a></li>
              <li><a href="#g11">Who can do what</a></li>
              <li><a href="#g12">Check yourself — two worked examples</a></li>
              <li><a href="#g13">Common questions</a></li>
            </ol>
          </nav>

          <!-- ============================ 1 =========================== -->
          <h2 class="abom-h2" id="g1">1. The one idea to understand first</h2>

          <div class="abom-keypoint">
            <b>Quantities are calculated, never stored.</b>
            The master list holds <i>how to work out</i> each quantity, not the
            quantity itself. Enter 8 axes and the SSCNET cable line says 7.
            Change it to 15 and the same line says 14 — nobody edits any
            master data in between.
          </div>

          <p class="abom-p">
            This is why the module exists. Two SPM1200L machines with
            different axis counts produce different bills of material
            automatically. The alternative — a spreadsheet copied and
            hand-edited per machine — is where the mistakes come from.
          </p>

          <p class="abom-p">
            The master list currently holds
            <b><?php echo (int) array_sum(array_map(function ($f) { return (int) $f->item_count; }, $formulas)); ?> items</b>
            across
            <b><?php echo count($families); ?> PLC families</b><?php
              $fam_names = array();
              foreach ($families as $fam) { $fam_names[] = $fam->name; }
              echo ' (' . abom_e(implode(', ', $fam_names)) . ')';
            ?>. A generated BOM never contains all of them: it contains the
            items for the chosen family, minus anything switched off by a
            feature.
          </p>

          <!-- ============================ 2 =========================== -->
          <h2 class="abom-h2" id="g2">2. Making a BOM, step by step</h2>

          <ol class="abom-steps">
            <li>
              <b>Open the generator.</b> The panel on the left is the machine
              configuration. Everything on the right redraws as you change it.
            </li>
            <li>
              <b>Fill in the machine.</b> Model, DF reference, axes, tracks,
              speed in PPM, side and motion type. These drive both the PLC
              family choice and the quantities.
            </li>
            <li>
              <b>Check the PLC family.</b> The module picks it for you and
              shows why. Override it only with a reason — an override is
              flagged on the sheet so a checker sees it.
            </li>
            <li>
              <b>Set MR-J4 units and Battery Qty.</b>
              <span class="abom-warn">These are two separate numbers on purpose</span>
              — see section 4. Do not assume they match.
            </li>
            <li>
              <b>Turn machine features on or off.</b> Each switch adds or
              removes its items from the sheet.
            </li>
            <li>
              <b>Press Recalculate</b> to redraw, and read the review banner
              at the top of the sheet.
            </li>
            <li>
              <b>Deal with every MANUAL and conflict line</b> — section 8.
              You cannot submit until these are cleared.
            </li>
            <li>
              <b>Save BOM.</b> It gets a number and enters <i>Draft</i>.
            </li>
            <li>
              <b>Submit for checking</b> when you are satisfied. From here the
              approval ladder takes over — section 9.
            </li>
          </ol>

          <!-- ============================ 3 =========================== -->
          <h2 class="abom-h2" id="g3">3. How the PLC family is chosen</h2>

          <p class="abom-p">
            The module does not guess. It walks a list of rules in priority
            order and <b>the first rule that matches wins</b> — the rest are
            not consulted. These are the rules the system is running right
            now:
          </p>

          <table class="abom-guide-table">
            <thead>
              <tr>
                <th style="width:70px;">Priority</th>
                <th>If&hellip;</th>
                <th style="width:150px;">Then use</th>
                <th>Why</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($rules as $r): ?>
                <tr<?php echo $r['is_catch_all'] ? ' class="is-catchall"' : ''; ?>>
                  <td><b><?php echo (int) $r['priority']; ?></b></td>
                  <td>
                    <?php if ($r['is_catch_all']): ?>
                      <i>nothing above matched</i>
                    <?php else: ?>
                      <?php echo abom_e($r['condition']); ?>
                    <?php endif; ?>
                  </td>
                  <td><b><?php echo abom_e($r['family']); ?></b></td>
                  <td class="abom-muted"><?php echo abom_e($r['explanation']); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <p class="abom-p abom-muted">
            Read that table top to bottom, the way the module does. The last
            row is the fallback: a machine that trips none of the conditions
            above gets that family.
          </p>

          <div class="abom-keypoint">
            <b>Teaching point.</b> If someone asks "why did this machine come
            out as <?php echo abom_e(isset($rules[0]) ? $rules[0]['family'] : ''); ?>?",
            the answer is always one row of that table, and the module prints
            it on the sheet. You never have to guess.
          </div>

          <!-- ============================ 4 =========================== -->
          <h2 class="abom-h2" id="g4">4. The quantity formulas</h2>

          <p class="abom-p">
            Every master item carries a formula code. That code decides where
            its quantity comes from. There are
            <b><?php echo count($formulas); ?></b>, and this is what each one
            does and how many items use it today:
          </p>

          <table class="abom-guide-table">
            <thead>
              <tr>
                <th style="width:130px;">Formula</th>
                <th style="width:150px;">Quantity is&hellip;</th>
                <th>What that means in practice</th>
                <th style="width:70px;">Items</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // The explanations are keyed to the formula CODE, which is what
              // Abom_engine::calc_qty() switches on. If a new code is ever
              // added to abom_formula it still appears in this table, with
              // the fallback text, rather than silently going missing.
              $howto = array(
                  'FIXED' => array(
                      'reads' => 'the item&rsquo;s own base quantity',
                      'text'  => 'Always the same number whatever the machine. One PLC, one power supply. This is the most common case.',
                  ),
                  'AXES' => array(
                      'reads' => 'the <b>Axes</b> field',
                      'text'  => 'One per axis. Enter 12 axes and the quantity is 12.',
                  ),
                  'AXES_MINUS_1' => array(
                      'reads' => 'the <b>Axes</b> field, minus 1',
                      'text'  => 'For daisy-chained items — an 8-axis machine has 7 links between amplifiers. It never goes below 1, so a single-axis machine still gets one.',
                  ),
                  'J4_STO' => array(
                      'reads' => 'the <b>MR-J4 Units</b> field',
                      'text'  => 'One per servo amplifier. This is NOT the axis count — a machine can have more axes than J4 units.',
                  ),
                  'BATTERY' => array(
                      'reads' => 'the <b>Battery Qty</b> field',
                      'text'  => 'One per servo battery. Deliberately its own field: DF-1826 has 11 MR-J4 units but 12 battery sets. Driving both from one number would produce a wrong BOM.',
                  ),
                  'MANUAL' => array(
                      'reads' => 'a starting suggestion only',
                      'text'  => 'The module cannot work this one out — cable lengths, layout-dependent items. It shows a suggested number and <b>a person must confirm it or type a different one</b>. A BOM cannot be submitted while any MANUAL line is unconfirmed.',
                  ),
              );
              ?>
              <?php foreach ($formulas as $code => $f): ?>
                <tr<?php echo $code === 'MANUAL' ? ' class="is-manual-row"' : ''; ?>>
                  <td><code class="abom-code"><?php echo abom_e($code); ?></code></td>
                  <td><?php echo isset($howto[$code]) ? $howto[$code]['reads'] : abom_e($f->name); ?></td>
                  <td>
                    <?php echo isset($howto[$code]) ? $howto[$code]['text'] : abom_e($f->description); ?>
                    <?php if (!empty($f->needs_review)): ?>
                      <span class="status-badge review">NEEDS REVIEW</span>
                    <?php endif; ?>
                  </td>
                  <td><b><?php echo (int) $f->item_count; ?></b></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <div class="abom-keypoint">
            <b>The two-number rule — the single most important thing to teach.</b>
            <b>MR-J4 Units</b> and <b>Battery Qty</b> are separate fields
            because on real machines they differ. DF-1826 carries 11 MR-J4
            units and 12 battery sets. If a trainee fills one in and assumes
            the other follows, the BOM is wrong and nothing on screen will
            complain — the module trusts what you type.
          </div>

          <p class="abom-p">
            <b>Worked example.</b> A 15-axis machine with 11 MR-J4 units and
            12 batteries produces: a <code class="abom-code">FIXED</code> line of 1
            &rarr; <b>1</b>; an <code class="abom-code">AXES_MINUS_1</code> line
            &rarr; <b>14</b>; a <code class="abom-code">J4_STO</code> line
            &rarr; <b>11</b>; a <code class="abom-code">BATTERY</code> line
            &rarr; <b>12</b>. Four different numbers, one configuration, no
            master data touched.
          </p>

          <!-- ============================ 5 =========================== -->
          <h2 class="abom-h2" id="g5">5. Machine feature switches</h2>

          <p class="abom-p">
            Some items only belong on a machine that has a particular feature.
            Switching a feature off removes its items from the sheet entirely;
            switching it on brings them back with their quantities calculated
            as normal.
          </p>

          <table class="abom-guide-table">
            <thead>
              <tr>
                <th style="width:230px;">Feature</th>
                <th style="width:110px;">Default</th>
                <th style="width:90px;">Items</th>
                <th>Notes</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($features as $f): ?>
                <tr>
                  <td><b><?php echo abom_e($f->label); ?></b></td>
                  <td>
                    <?php if (!empty($f->default_on)): ?>
                      <span class="status-badge">ON</span>
                    <?php else: ?>
                      <span class="abom-muted">off</span>
                    <?php endif; ?>
                  </td>
                  <td><b><?php echo (int) $f->item_count; ?></b></td>
                  <td class="abom-muted"><?php echo abom_e($f->help_text); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <p class="abom-p abom-muted">
            Feature-gated lines are tinted green on the sheet so a checker can
            see at a glance which items are there because of a switch.
          </p>

          <!-- ============================ 6 =========================== -->
          <h2 class="abom-h2" id="g6">6. Reading the sheet — colours and badges</h2>

          <p class="abom-p">
            A row gets <b>at most one</b> colour. When more than one could
            apply the more serious wins, so a conflict on an optional line
            shows as a conflict:
          </p>

          <table class="abom-guide-table">
            <thead>
              <tr>
                <th style="width:60px;">Order</th>
                <th style="width:170px;">Colour</th>
                <th>Means</th>
                <th>What to do</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1st</td>
                <td><span class="abom-swatch is-conflict"></span> Red</td>
                <td>The same ERP code is on two different parts</td>
                <td><b>Must be acknowledged with a comment</b> before submitting</td>
              </tr>
              <tr>
                <td>2nd</td>
                <td><span class="abom-swatch is-noerp"></span> Orange</td>
                <td>No ERP code yet — shows <b>PENDING</b> or <b>NEW</b></td>
                <td>Purchasing raises a code; the BOM can still proceed</td>
              </tr>
              <tr>
                <td>3rd</td>
                <td><span class="abom-swatch is-optional"></span> Green</td>
                <td>Item is here because a feature is switched on</td>
                <td>Confirm the feature genuinely applies</td>
              </tr>
              <tr>
                <td>4th</td>
                <td><span class="abom-swatch is-manual"></span> Blue</td>
                <td>Quantity needs a human decision</td>
                <td><b>Confirm it or type a different number</b> — blocks submission</td>
              </tr>
              <tr>
                <td>&mdash;</td>
                <td><span class="abom-swatch is-plain"></span> No tint</td>
                <td>Calculated, nothing outstanding</td>
                <td>Nothing</td>
              </tr>
            </tbody>
          </table>

          <p class="abom-p">
            <b>Why some rows carry a REVIEW badge but no colour.</b> Review
            notes apply to a large share of the master list. Tinting them all
            would put most of the sheet in a warning colour on a document
            a customer signs, and the colour would stop meaning anything.
            Review appears as a badge in the STATUS column instead — nothing
            is hidden, because the STATUS column shows <i>every</i> badge that
            applies to a line regardless of which colour won the row.
          </p>

          <p class="abom-p">
            The yellow you may see on a single quantity box is different again:
            it marks a quantity <b>edited during review</b>, with a &#9998;
            pencil. It colours the box, never the whole row.
          </p>

          <!-- ============================ 7 =========================== -->
          <h2 class="abom-h2" id="g7">7. Changing a quantity</h2>

          <p class="abom-p">
            Quantity boxes are editable while a BOM is in <b>Draft</b>. Once it
            is submitted the boxes disappear for everyone — that is deliberate,
            not a permissions fault. A BOM under approval must not move under
            the approver.
          </p>

          <ul class="abom-list">
            <li>Type a number and it saves as you leave the box.</li>
            <li>An edited quantity is marked with a &#9998; pencil and the
                calculated value is kept, so a checker can see both.</li>
            <li>Every change is written to the audit log with who and when.</li>
            <li>Editing an approved BOM is not possible. Use
                <b>Create revision</b> — the original stays as the record and a
                new draft is opened from it.</li>
          </ul>

          <div class="abom-keypoint">
            <b>Worth saying in training:</b> anyone who can build BOMs can edit
            anyone else's draft. That is intentional — a colleague being away
            should not block a correction — and every change carries a name in
            the audit log.
          </div>

          <!-- ============================ 8 =========================== -->
          <h2 class="abom-h2" id="g8">8. Before you can submit</h2>

          <p class="abom-p">
            The module refuses to submit a draft with unresolved data. Two
            things block it, and both are listed on screen with counts:
          </p>

          <ol class="abom-steps">
            <li>
              <b>Every MANUAL quantity must be confirmed</b> — either accept
              the suggestion or type your own. This is the point of the MANUAL
              formula: it forces a person to look.
            </li>
            <li>
              <b>Every ERP conflict must be acknowledged with a comment.</b>
              A tick is not enough; you have to say what you checked.
            </li>
          </ol>

          <p class="abom-p abom-muted">
            If the Submit button refuses, read the panel above the sign-off
            block — it names exactly what is outstanding.
          </p>

          <!-- ============================ 9 =========================== -->
          <h2 class="abom-h2" id="g9">9. The approval workflow</h2>

          <p class="abom-p">
            A BOM climbs a fixed ladder. Each rung needs its own permission:
          </p>

          <table class="abom-guide-table">
            <thead>
              <tr>
                <th style="width:60px;">Step</th>
                <th style="width:170px;">From</th>
                <th style="width:170px;">To</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($stages as $i => $st): ?>
                <tr>
                  <td><b><?php echo $i + 1; ?></b></td>
                  <td><code class="abom-code"><?php echo abom_e($st['from']); ?></code></td>
                  <td><code class="abom-code"><?php echo abom_e($st['to']); ?></code></td>
                  <td><?php echo abom_e($st['label']); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <div class="abom-keypoint">
            <b>Two rules stop one person pushing a BOM through alone, and they
            are enforced by the system, not by etiquette:</b>
            <ul class="abom-list">
              <li>The same person <b>cannot do two stages in a row</b> on one
                  BOM.</li>
              <li>Whoever <b>created</b> a BOM cannot give it the
                  <b>final approval</b>.</li>
            </ul>
            Between them, at least two people — normally three — must touch
            every released BOM. If you are refused with a message about this,
            the system is working correctly; ask a colleague to take the step.
          </div>

          <ul class="abom-list">
            <li><b>Reject</b> is available at any stage to anyone with approval
                rights, and sends the BOM back with a comment.</li>
            <li><b>Reopen</b> returns a rejected BOM to draft so it can be
                edited again.</li>
            <li><b>Create revision</b> is how an approved BOM is changed —
                the approved one is kept and marked superseded.</li>
          </ul>

          <!-- ============================ 10 ========================== -->
          <h2 class="abom-h2" id="g10">10. Printing and exporting</h2>

          <ul class="abom-list">
            <li><b>Print</b> gives the customer-facing sheet — buttons, filters
                and menus drop away, and the sign-off boxes are laid out for
                signature.</li>
            <li><b>Export CSV</b> for a quick list.</li>
            <li><b>Export Excel</b> for purchasing.</li>
            <li><b>Export PDF</b> for sending out — this is the one to attach
                to an email.</li>
          </ul>

          <p class="abom-p abom-muted">
            A superseded BOM can still be printed and exported. That is on
            purpose: the record of what was approved at the time must remain
            available.
          </p>

          <!-- ============================ 11 ========================== -->
          <h2 class="abom-h2" id="g11">11. Who can do what</h2>

          <table class="abom-guide-table">
            <thead>
              <tr>
                <th style="width:250px;">Permission</th>
                <th>Lets you</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><b>AUTOMATION BOM GENERATOR</b></td>
                <td>Build, save and edit BOMs; confirm quantities; reopen a
                    rejected BOM; create revisions</td>
              </tr>
              <tr>
                <td><b>AUTOMATION BOM APPROVALS</b></td>
                <td>Move a BOM through the stages, and reject one</td>
              </tr>
              <tr>
                <td><b>AUTOMATION BOM MASTER ITEMS</b></td>
                <td class="abom-muted">Reserved for master-data editing screens,
                    which are not built yet. Granting it today gives read
                    access and nothing more.</td>
              </tr>
            </tbody>
          </table>

          <p class="abom-p">
            <b>Holding any one of the three lets you read and export every
            BOM.</b> The three differ in what you may <i>change</i>, not in
            what you may <i>see</i>. Someone with none of them cannot reach
            the module at all.
          </p>

          <!-- ============================ 12 ========================== -->
          <h2 class="abom-h2" id="g12">12. Check yourself — two worked examples</h2>

          <p class="abom-p">
            These two were produced by the engine as this page loaded. Set the
            same configuration in the generator and you must get the same
            numbers — it is the quickest way to prove to a trainee that the
            module is behaving, and the quickest way to spot that it is not.
          </p>

          <?php foreach ($examples as $key => $ex): ?>
            <table class="abom-guide-table abom-example">
              <thead>
                <tr><th colspan="2"><?php echo abom_e($ex['label']); ?></th></tr>
              </thead>
              <tbody>
                <tr>
                  <td style="width:200px;">Set these</td>
                  <td>
                    <?php
                    $cfg   = $ex['cfg'];
                    $shown = array(
                        'Axes'         => isset($cfg['axes']) ? $cfg['axes'] : null,
                        'Tracks'       => isset($cfg['tracks']) ? $cfg['tracks'] : null,
                        'Speed (PPM)'  => isset($cfg['speed_ppm']) ? $cfg['speed_ppm'] : null,
                        'Motion type'  => isset($cfg['motion_type']) ? $cfg['motion_type'] : null,
                        'MR-J4 units'  => isset($cfg['j4_units']) ? $cfg['j4_units'] : null,
                        'Battery qty'  => isset($cfg['battery_qty']) ? $cfg['battery_qty'] : null,
                    );
                    $bits = array();
                    foreach ($shown as $label => $val) {
                        if ($val !== null && $val !== '') {
                            $bits[] = $label . ' <b>' . abom_e($val) . '</b>';
                        }
                    }
                    echo implode(' &middot; ', $bits);
                    ?>
                  </td>
                </tr>
                <tr>
                  <td>You should get</td>
                  <td>
                    <b><?php echo (int) $ex['lines']; ?> line items</b>,
                    total quantity <b><?php echo (int) $ex['qty']; ?></b>,
                    PLC family <b><?php echo abom_e($ex['family']); ?></b>
                  </td>
                </tr>
                <?php if (!empty($ex['why'])): ?>
                  <tr>
                    <td>Family chosen because</td>
                    <td class="abom-muted"><?php echo abom_e($ex['why']); ?></td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          <?php endforeach; ?>

          <p class="abom-p abom-muted">
            You can open either of these fully built:
            <a href="<?php echo page_url; ?>abom/reference/fx5">FX5 reference</a> &middot;
            <a href="<?php echo page_url; ?>abom/reference/iqr">iQ-R reference</a>.
          </p>

          <!-- ============================ 13 ========================== -->
          <h2 class="abom-h2" id="g13">13. Common questions</h2>

          <dl class="abom-faq">
            <dt>I changed the axes but a quantity did not move.</dt>
            <dd>That line is <code class="abom-code">FIXED</code> or
                <code class="abom-code">MANUAL</code>. Only
                <code class="abom-code">AXES</code> and
                <code class="abom-code">AXES_MINUS_1</code> follow the axis
                count. Check the formula column.</dd>

            <dt>The quantity boxes have disappeared.</dt>
            <dd>The BOM is no longer a draft. That is by design — see section 7.
                Use <b>Create revision</b> if it needs to change.</dd>

            <dt>Submit is refusing.</dt>
            <dd>Unconfirmed MANUAL lines or unacknowledged ERP conflicts. The
                panel above the sign-off block lists them with counts.</dd>

            <dt>It says I cannot approve this one.</dt>
            <dd>Either you took the previous stage yourself, or you created it
                and this is the final approval. Both are separation-of-duty
                rules — section 9. Ask a colleague.</dd>

            <dt>The PLC family is not what I expected.</dt>
            <dd>Read the rule table in section 3 top to bottom; the first
                matching row wins. The sheet also prints the reason. If it is
                genuinely wrong for this machine, override it and say why.</dd>

            <dt>Two parts show the same ERP code.</dt>
            <dd>Known, and deliberate — the module surfaces it rather than
                hiding it. Acknowledge it with a comment saying what you
                verified, then you can submit.</dd>

            <dt>An item shows PENDING instead of an ERP code.</dt>
            <dd>No code has been raised yet. It does not block the BOM.
                Purchasing raises the code.</dd>

            <dt>Can I delete a BOM?</dt>
            <dd>No. Reject it, or supersede it with a revision. The history is
                the point.</dd>
          </dl>

          <div class="abom-keypoint">
            <b>If something looks wrong, do not correct it silently in a
            spreadsheet afterwards.</b> The value of this module is that the
            sheet and the record agree. Reject it, or raise a revision, so the
            reason is captured.
          </div>

        </div>
      </div>
    </main>
  </div>
</div>

<script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
<script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
</body>
</html>
