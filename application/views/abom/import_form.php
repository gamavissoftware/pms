<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * import_form.php — step 1 of the reference-BOM import.
 *
 * Two things are collected, and the second matters as much as the first.
 *
 * The FILE is obvious. The MACHINE is not: every quantity rule is
 * worked out by testing the sheet's numbers against what each rule would
 * produce for the machine described here. A correct spreadsheet with the
 * wrong track count typed above it produces a wrong catalogue — so the
 * form says that, next to the fields, rather than leaving it to be
 * discovered.
 *
 * @var array $meta
 * @var array $errors
 */
$meta = isset($meta) ? $meta : array();
foreach (array('code','name','description','source_df','machine_model','machine_side',
               'motion_type','panel_location','rule_min_axes','rule_max_axes',
               'rule_min_speed','rule_max_speed','rule_motion','rule_explanation') as $k) {
    if (!isset($meta[$k])) { $meta[$k] = ''; }
}
foreach (array('plc_family_id','axes','tracks','speed_ppm','j4_units','battery_qty') as $k) {
    if (!isset($meta[$k])) { $meta[$k] = ''; }
}
$errors = isset($errors) ? $errors : array();

function abom_ierr($errors, $field)
{
    return isset($errors[$field])
        ? '<span class="field-error">' . abom_e($errors[$field]) . '</span>' : '';
}
function abom_iinv($errors, $field)
{
    return isset($errors[$field]) ? ' is-invalid' : '';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> Import a reference BOM</title>
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet">
    <link href="<?php echo abom_asset('abom/abom.css'); ?>" rel="stylesheet">
</head>
<body>

<div class="abom-wrap">

  <header class="app-header">
    <a class="abom-home" href="<?php echo page_url; ?>Dashboard" title="Back to Dashboard">
      <span class="abom-home-icon" aria-hidden="true">&#8962;</span>
      <span class="abom-home-text">Dashboard</span>
    </a>
    <div>
      <h1>&#128228; Import a Reference BOM</h1>
      <div class="sub">Turn a released DF spreadsheet into a build</div>
    </div>
    <span class="badge review">MASTER DATA</span>
  </header>

  <div class="layout"><main class="main">

    <div class="abom-topbar">
      <div class="abom-actions">
        <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/master_bom">&#8617; Reference BOMs</a>
        <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/import_template">&#128229; Download the template</a>
        <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/guide">&#10068; Guide</a>
      </div>
    </div>

    <div class="abom-area">

      <?php if (!empty($errors['file'])): ?>
        <div class="imp-error"><b>&#9888;&#65039; <?php echo abom_e($errors['file']); ?></b></div>
      <?php endif; ?>

      <div class="abom-master-note">
        <b>Nothing is written until you have seen it.</b> The next screen shows every row
        the sheet contains, the quantity rule worked out for each one, and every problem
        found &mdash; with a confirm button at the bottom. You can change any rule, or
        leave a row out, before anything is created.
        <br><br>
        <b>An import only ever creates a NEW build.</b> No existing build, item or saved
        BOM is changed by it.
      </div>

      <form method="post" enctype="multipart/form-data"
            action="<?php echo page_url; ?>abom/import_preview" class="imp-form">

        <div class="imp-section">
          <div class="imp-legend">1 &mdash; The spreadsheet</div>

          <div class="form-group">
            <label for="impFile">Released DF sheet (.xlsx, .xls or .csv)</label>
            <input type="file" name="sheet" id="impFile" accept=".xlsx,.xls,.csv"
                   class="<?php echo trim(abom_iinv($errors, 'file')); ?>">
            <div class="imp-help">
              The first sheet is read. It needs a heading row with at least
              <b>DESCRIPTION</b>, <b>MODEL NO. / PART NO.</b> and <b>QTY.</b> &mdash; the
              same nine columns this module exports. The heading row does not have to be
              the first row; a title block above it is fine.
              <br>
              A row with text in DESCRIPTION but no part number and no quantity is read as
              a <b>section heading</b>, exactly as a released DF groups its lines.
            </div>
          </div>
        </div>

        <div class="imp-section">
          <div class="imp-legend">2 &mdash; The build this sheet becomes</div>

          <div class="cfg-2col">
            <div class="form-group">
              <label for="impCode">Build code</label>
              <input type="text" name="code" id="impCode" maxlength="24"
                     class="<?php echo trim(abom_iinv($errors, 'code')); ?>"
                     value="<?php echo abom_e($meta['code']); ?>" placeholder="FX5-1808">
              <?php echo abom_ierr($errors, 'code'); ?>
            </div>
            <div class="form-group">
              <label for="impDf">Source DF</label>
              <input type="text" name="source_df" id="impDf" maxlength="96"
                     class="<?php echo trim(abom_iinv($errors, 'source_df')); ?>"
                     value="<?php echo abom_e($meta['source_df']); ?>" placeholder="DF-1808">
              <?php echo abom_ierr($errors, 'source_df'); ?>
            </div>
          </div>

          <div class="form-group">
            <label for="impName">Name</label>
            <input type="text" name="name" id="impName" maxlength="96"
                   class="<?php echo trim(abom_iinv($errors, 'name')); ?>"
                   value="<?php echo abom_e($meta['name']); ?>"
                   placeholder="SPM1200L 6 axis / 12 track / 100 PPM">
            <?php echo abom_ierr($errors, 'name'); ?>
            <div class="imp-help">This is what your team will see in the register and in
              the build picker. Write it so someone can tell it from the others at a
              glance.</div>
          </div>

          <div class="form-group">
            <label for="impDesc">Description</label>
            <textarea name="description" id="impDesc" rows="2" maxlength="255"
                      placeholder="What makes this build different from the others"><?php echo abom_e($meta['description']); ?></textarea>
          </div>

          <div class="cfg-2col">
            <div class="form-group">
              <label for="impFamily">PLC family</label>
              <select name="plc_family_id" id="impFamily"
                      class="<?php echo trim(abom_iinv($errors, 'plc_family_id')); ?>">
                <option value="">Choose…</option>
                <?php foreach ($families as $f): ?>
                  <option value="<?php echo (int) $f->id; ?>"<?php echo abom_sel($meta['plc_family_id'], $f->id); ?>>
                    <?php echo abom_e($f->name); ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <?php echo abom_ierr($errors, 'plc_family_id'); ?>
            </div>
            <div class="form-group">
              <label for="impPanel">Panel location</label>
              <input type="text" name="panel_location" id="impPanel" maxlength="64"
                     value="<?php echo abom_e($meta['panel_location']); ?>"
                     placeholder="Panel With Machine">
            </div>
          </div>
        </div>

        <div class="imp-section imp-machine">
          <div class="imp-legend">3 &mdash; The machine this sheet is for</div>

          <div class="imp-warn">
            <b>&#9888;&#65039; These decide how every quantity is read.</b>
            A sheet says a temperature card quantity of <b>7</b>. Whether that is a fixed
            seven, or the track rule which comes to seven at twelve tracks, is worked out
            by comparing it against the machine you enter here. Get the track count wrong
            and a correct spreadsheet produces a catalogue that is wrong on every other
            machine size.
            <br><br>
            Enter the machine <b>the released DF was drawn for</b> &mdash; not a typical
            one, not a range.
          </div>

          <div class="cfg-2col">
            <div class="form-group">
              <label for="impModel">Machine model</label>
              <select name="machine_model" id="impModel"
                      class="<?php echo trim(abom_iinv($errors, 'machine_model')); ?>">
                <option value="">Choose…</option>
                <?php foreach ($models as $m): ?>
                  <option value="<?php echo abom_e($m); ?>"<?php echo abom_sel($meta['machine_model'], $m); ?>>
                    <?php echo abom_e($m); ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <?php echo abom_ierr($errors, 'machine_model'); ?>
            </div>
            <div class="form-group">
              <label for="impSide">Side</label>
              <select name="machine_side" id="impSide">
                <?php foreach ($sides as $sd): ?>
                  <option value="<?php echo abom_e($sd); ?>"<?php echo abom_sel($meta['machine_side'], $sd); ?>>
                    <?php echo abom_e($sd); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="cfg-3col">
            <div class="form-group">
              <label for="impAxes">Axes</label>
              <input type="number" name="axes" id="impAxes" min="1" max="24"
                     class="<?php echo trim(abom_iinv($errors, 'axes')); ?>"
                     value="<?php echo abom_e($meta['axes']); ?>">
              <?php echo abom_ierr($errors, 'axes'); ?>
            </div>
            <div class="form-group">
              <label for="impTracks">Tracks</label>
              <input type="number" name="tracks" id="impTracks" min="1" max="24"
                     class="<?php echo trim(abom_iinv($errors, 'tracks')); ?>"
                     value="<?php echo abom_e($meta['tracks']); ?>">
              <?php echo abom_ierr($errors, 'tracks'); ?>
            </div>
            <div class="form-group">
              <label for="impSpeed">Speed (PPM)</label>
              <input type="number" name="speed_ppm" id="impSpeed" min="10" max="600"
                     class="<?php echo trim(abom_iinv($errors, 'speed_ppm')); ?>"
                     value="<?php echo abom_e($meta['speed_ppm']); ?>">
              <?php echo abom_ierr($errors, 'speed_ppm'); ?>
            </div>
          </div>

          <div class="cfg-3col">
            <div class="form-group">
              <label for="impMotion">Motion type</label>
              <select name="motion_type" id="impMotion">
                <?php foreach ($motions as $mo): ?>
                  <option value="<?php echo abom_e($mo); ?>"<?php echo abom_sel($meta['motion_type'], $mo); ?>>
                    <?php echo abom_e($mo); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="impJ4">MR-J4 units</label>
              <input type="number" name="j4_units" id="impJ4" min="0" max="99"
                     value="<?php echo abom_e($meta['j4_units'] === '' ? '0' : $meta['j4_units']); ?>">
              <div class="imp-help">0 on an MR-JE machine.</div>
            </div>
            <div class="form-group">
              <label for="impBatt">Battery quantity</label>
              <input type="number" name="battery_qty" id="impBatt" min="0" max="99"
                     value="<?php echo abom_e($meta['battery_qty'] === '' ? '0' : $meta['battery_qty']); ?>">
              <div class="imp-help">Independent of the MR-J4 count.</div>
            </div>
          </div>
        </div>

        <div class="imp-section">
          <div class="imp-legend">4 &mdash; Which machines should reach this build</div>

          <div class="imp-help imp-help-block">
            A build with no selection rule <b>is never used</b> &mdash; it would sit in the
            register while every generated BOM quietly picked a different one. So a rule is
            always created. Leave a bound empty for &ldquo;no limit&rdquo;.
            <br>
            The rule is added <b>last</b> in priority order, so it can never take machines
            away from a build that already claims them. Adjust the order afterwards under
            <a href="<?php echo page_url; ?>abom/config_list/variant_rule">selection rules</a>
            if this build should win over an existing one.
          </div>

          <div class="cfg-2col">
            <div class="form-group">
              <label for="impRMinA">Axes from</label>
              <input type="number" name="rule_min_axes" id="impRMinA" min="1" max="24"
                     value="<?php echo abom_e($meta['rule_min_axes']); ?>" placeholder="no limit">
            </div>
            <div class="form-group">
              <label for="impRMaxA">Axes to</label>
              <input type="number" name="rule_max_axes" id="impRMaxA" min="1" max="24"
                     value="<?php echo abom_e($meta['rule_max_axes']); ?>" placeholder="no limit">
            </div>
          </div>

          <div class="cfg-2col">
            <div class="form-group">
              <label for="impRMinS">Speed from</label>
              <input type="number" name="rule_min_speed" id="impRMinS" min="10" max="600"
                     value="<?php echo abom_e($meta['rule_min_speed']); ?>" placeholder="no limit">
            </div>
            <div class="form-group">
              <label for="impRMaxS">Speed to</label>
              <input type="number" name="rule_max_speed" id="impRMaxS" min="10" max="600"
                     value="<?php echo abom_e($meta['rule_max_speed']); ?>" placeholder="no limit">
            </div>
          </div>

          <div class="form-group">
            <label for="impRMotion">Motion type</label>
            <select name="rule_motion" id="impRMotion">
              <option value="ANY"<?php echo abom_sel($meta['rule_motion'], 'ANY'); ?>>Any</option>
              <?php foreach ($motions as $mo): ?>
                <option value="<?php echo abom_e($mo); ?>"<?php echo abom_sel($meta['rule_motion'], $mo); ?>>
                  <?php echo abom_e($mo); ?> only
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="impRWhy">Why this build (shown to whoever generates a BOM)</label>
            <input type="text" name="rule_explanation" id="impRWhy" maxlength="255"
                   value="<?php echo abom_e($meta['rule_explanation']); ?>"
                   placeholder="6 axes at 90 PPM or above is the DF-1808 build.">
          </div>
        </div>

        <div class="imp-actions">
          <button type="submit" class="btn-sm btn-generate">&#128269; Check the sheet</button>
          <span class="imp-note">Nothing is written yet &mdash; the next screen shows what would be.</span>
        </div>

      </form>
    </div>

  </main></div>
</div>

<?php $this->load->view('common/footer'); ?>
<script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
<script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
</body>
</html>
