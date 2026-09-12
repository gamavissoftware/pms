<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * config_list.php — one configuration table's rows.
 *
 * Generic: driven entirely by the descriptor, so all six tables render
 * through this one view and cannot drift apart.
 *
 * Reached only through Abom::config_list(), which requires 'master_edit'.
 *
 * @var string $entity
 * @var array  $d       descriptor
 * @var array  $rows
 * @var string $key     primary key column
 * @var array  $refs    ref name => [key => label]
 * @var string $message flashdata
 */

$addable = !isset($d['addable']) || $d['addable'] !== false;

// Renders one cell for the list, resolving references and booleans to
// something a human reads rather than an id or a 1.
$cell = function ($row, $field) use ($d, $refs) {
    $spec  = isset($d['fields'][$field]) ? $d['fields'][$field] : array('type' => 'text');
    $value = isset($row->$field) ? $row->$field : null;

    if ($spec['type'] === 'ref') {
        $opts = isset($refs[$spec['ref']]) ? $refs[$spec['ref']] : array();
        return isset($opts[$value]) ? abom_e($opts[$value]) : '<span class="abom-null">any</span>';
    }

    if ($spec['type'] === 'bool') {
        return !empty($value)
            ? '<span class="formula-tag tag-ok">YES</span>'
            : '<span class="formula-tag tag-review">NO</span>';
    }

    if ($value === null || $value === '') {
        return '<span class="abom-null">&mdash;</span>';
    }

    return abom_e($value);
};
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> Automation BOM — <?php echo abom_e($d['plural']); ?></title>
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
    <a class="abom-home" href="<?php echo page_url; ?>abom/config" title="Build configuration">
      <span class="abom-home-icon" aria-hidden="true">&#8592;</span>
      <span class="abom-home-text">Configuration</span>
    </a>
    <div>
      <h1>&#9881;&#65039; <?php echo abom_e($d['plural']); ?></h1>
      <div class="sub"><?php echo abom_e($d['blurb']); ?></div>
    </div>
    <span class="badge review">MASTER DATA</span>
  </header>

  <div class="layout"><main class="main">

    <div class="abom-topbar">
      <span class="config-chip"><?php echo count($rows); ?> row<?php echo count($rows) === 1 ? '' : 's'; ?></span>
      <div class="abom-actions">
        <?php if ($addable): ?>
          <a class="btn-sm btn-generate"
             href="<?php echo page_url; ?>abom/config_form/<?php echo abom_e($entity); ?>">&#43; New</a>
        <?php endif; ?>
        <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/config">All configuration</a>
        <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/master">Master items</a>
        <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/guide">&#10068; Guide</a>
      </div>
    </div>

    <div class="abom-area">
      <?php if (!empty($message)): ?>
        <div class="abom-master-ok">&#10003; <?php echo abom_e($message); ?></div>
      <?php endif; ?>

      <?php if (!$addable): ?>
        <div class="abom-master-note">
          <b>Rows cannot be added or removed here.</b> Each formula code is dispatched on in
          the engine; a code the engine does not know silently behaves as a fixed quantity.
          Adding one is a code change, not a data change. The wording below is editable.
        </div>
      <?php endif; ?>

      <?php if (empty($rows)): ?>
        <div class="abom-empty">Nothing here yet.</div>
      <?php else: ?>
        <div class="abom-table-wrap">
          <table id="abomTable">
            <thead>
              <tr>
                <?php foreach ($d['columns'] as $col): ?>
                  <th class="left"><?php echo abom_e(isset($d['fields'][$col]['label'])
                      ? $d['fields'][$col]['label'] : strtoupper($col)); ?></th>
                <?php endforeach; ?>
                <th class="left">DESCRIPTION</th>
                <th style="width:140px;">&nbsp;</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($rows as $row): ?>
                <?php
                $inactive = !empty($d['active']) && empty($row->{$d['active']});
                $rid      = $row->$key;
                ?>
                <tr class="data-row<?php echo $inactive ? ' is-retired' : ''; ?>">
                  <?php foreach ($d['columns'] as $col): ?>
                    <td class="<?php echo $col === 'priority' ? '' : 'left'; ?>"
                        style="<?php echo $col === 'priority' ? 'text-align:center;font-weight:700;' : ''; ?>">
                      <?php echo $cell($row, $col); ?>
                    </td>
                  <?php endforeach; ?>
                  <td class="left" style="font-size:11px;color:#555;">
                    <?php
                    $desc = isset($row->{$d['display']}) ? $row->{$d['display']} : '';
                    echo abom_e(mb_substr((string) $desc, 0, 150));
                    ?>
                  </td>
                  <td style="text-align:center;white-space:nowrap;">
                    <a class="btn-sm btn-print"
                       href="<?php echo page_url; ?>abom/config_form/<?php echo abom_e($entity); ?>/<?php echo abom_e($rid); ?>">Edit</a>
                    <?php if (!empty($d['deletable'])): ?>
                      <button type="button" class="btn-sm abom-delete abom-config-delete"
                              data-entity="<?php echo abom_e($entity); ?>"
                              data-row="<?php echo abom_e($rid); ?>"
                              data-label="<?php echo abom_e((string) $row->{$d['display']}); ?>">Delete</button>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  </main></div>
</div>
<?php $this->load->view('common/footer'); ?>
<script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
<script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
<script>window.ABOM_CONFIG_DELETE_URL = '<?php echo page_url; ?>abom/config_delete';</script>
<script src="<?php echo abom_asset('abom/abom-master.js'); ?>"></script>
</body>
</html>
