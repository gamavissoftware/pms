<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * master_list.php — the master-item register.
 *
 * THE SEED EVERY BOM IS GENERATED FROM. An edit here changes what every
 * FUTURE BOM contains; it changes no existing one, because abom_bom_line
 * is a frozen snapshot. The banner below says so, because "editing the
 * master list" is exactly the phrase that makes people fear they have
 * altered documents already signed.
 *
 * Reached only through Abom::master(), which requires 'master_edit'.
 *
 * @var array $items     joined to section and variant
 * @var array $filters
 * @var array $variants  id => row
 * @var array $formulas  code => row
 * @var array $counts    variant_id => item count
 */

$filters  = isset($filters) ? $filters : array();
$variants = isset($variants) ? $variants : array();
$formulas = isset($formulas) ? $formulas : array();
$counts   = isset($counts) ? $counts : array();

// abom_sel() now lives in abom_helper.php — a second register needed it.
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> Automation BOM — Master Items</title>
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet">
    <link href="<?php echo abom_asset('abom/abom.css'); ?>" rel="stylesheet">
    <link href="<?php echo abom_asset('abom/abom-print.css'); ?>" rel="stylesheet" media="all">
</head>
<body>

<div class="abom-wrap">

  <header class="app-header">
    <a class="abom-home" href="<?php echo page_url; ?>Dashboard" title="Back to Dashboard">
      <span class="abom-home-icon" aria-hidden="true">&#8962;</span>
      <span class="abom-home-text">Dashboard</span>
    </a>
    <div>
      <h1>&#9881;&#65039; Automation BOM &mdash; Master Items</h1>
      <div class="sub">The catalogue every generated BOM is built from</div>
    </div>
    <span class="badge review">MASTER DATA</span>
  </header>

  <div class="layout">
    <main class="main">

      <div class="abom-topbar">
        <form method="get" action="<?php echo page_url; ?>abom/master" class="abom-filter-form">
          <select name="variant">
            <option value="">All builds</option>
            <?php foreach ($variants as $v): ?>
              <option value="<?php echo (int) $v->id; ?>"<?php echo abom_sel($filters['variant_id'], $v->id); ?>>
                <?php echo abom_e($v->code); ?>
                (<?php echo isset($counts[(int) $v->id]) ? (int) $counts[(int) $v->id] : 0; ?>)
              </option>
            <?php endforeach; ?>
          </select>

          <select name="formula">
            <option value="">All formulas</option>
            <?php foreach ($formulas as $code => $f): ?>
              <option value="<?php echo abom_e($code); ?>"<?php echo abom_sel($filters['formula_code'], $code); ?>>
                <?php echo abom_e($code); ?>
              </option>
            <?php endforeach; ?>
          </select>

          <select name="severity">
            <option value="">All flags</option>
            <?php foreach (array('none' => 'Clean', 'review' => 'Review', 'no_erp' => 'No ERP code',
                                 'conflict' => 'ERP conflict') as $k => $label): ?>
              <option value="<?php echo abom_e($k); ?>"<?php echo abom_sel($filters['severity'], $k); ?>>
                <?php echo abom_e($label); ?>
              </option>
            <?php endforeach; ?>
          </select>

          <select name="active">
            <option value=""<?php echo abom_sel($filters['is_active'], ''); ?>>Active and retired</option>
            <option value="1"<?php echo abom_sel($filters['is_active'], '1'); ?>>Active only</option>
            <option value="0"<?php echo abom_sel($filters['is_active'], '0'); ?>>Retired only</option>
          </select>

          <input type="text" name="search" value="<?php echo abom_e($filters['search']); ?>"
                 placeholder="ERP code, description or part no.">
          <button type="submit" class="btn-sm btn-print">Filter</button>
        </form>

        <div class="abom-actions">
          <a class="btn-sm btn-generate" href="<?php echo page_url; ?>abom/master_form/0">&#43; New item</a>
          <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/master_bom">&#128193; Reference BOMs</a>
          <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/config">&#9881; Build configuration</a>
          <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/generate">Generator</a>
          <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/list">Saved BOMs</a>
          <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/guide">&#10068; Guide</a>
        </div>
      </div>

      <div class="abom-area">
        <div class="abom-master-note">
          <b>This is master data.</b> Changes here affect every BOM generated
          <b>from now on</b>. They do not change any BOM already saved &mdash; each one
          holds its own frozen copy of every line, so documents already reviewed or
          approved stay exactly as they were.
          Retiring an item stops it appearing on new BOMs; it never removes it from an old one.
        </div>

        <?php if (empty($items)): ?>
          <div class="abom-empty">No master items match that filter.</div>
        <?php else: ?>
          <div class="abom-table-wrap">
            <table id="abomTable">
              <thead>
                <tr>
                  <th style="width:52px;">ID</th>
                  <th style="width:90px;">ERP CODE</th>
                  <th class="left">DESCRIPTION</th>
                  <th class="left" style="width:150px;">PART NO.</th>
                  <th style="width:96px;">BUILD</th>
                  <th class="left" style="width:150px;">SECTION</th>
                  <th style="width:56px;">BASE</th>
                  <th style="width:104px;">FORMULA</th>
                  <th style="width:96px;">FLAG</th>
                  <th style="width:150px;">&nbsp;</th>
                </tr>
              </thead>
              <tbody id="abomMasterRows">
                <?php foreach ($items as $item): ?>
                  <tr class="data-row <?php echo abom_row_class($item); ?><?php echo empty($item->is_active) ? ' is-retired' : ''; ?>"
                      data-item="<?php echo (int) $item->id; ?>">
                    <td style="text-align:center;font-weight:700;"><?php echo (int) $item->id; ?></td>
                    <td style="text-align:center;"><?php echo abom_erp_cell($item); ?></td>
                    <td class="desc-cell">
                      <?php echo abom_e($item->description); ?>
                      <?php if (!empty($item->is_optional)): ?>
                        <span class="formula-tag tag-ok">OPTIONAL<?php
                          echo !empty($item->feature_code) ? ' &middot; ' . abom_e($item->feature_code) : ''; ?></span>
                      <?php endif; ?>
                    </td>
                    <td class="part-cell"><?php echo $item->part_no !== '' ? abom_e($item->part_no) : '&mdash;'; ?></td>
                    <td style="text-align:center;">
                      <span class="formula-tag tag-manual"><?php echo abom_e($item->variant_code); ?></span>
                    </td>
                    <td style="font-size:11px;"><?php echo abom_e($item->section_name); ?></td>
                    <td style="text-align:center;"><?php echo (int) $item->base_qty; ?></td>
                    <td style="text-align:center;font-size:10px;"><?php echo abom_e($item->formula_code); ?></td>
                    <td style="text-align:center;"><?php echo abom_status_badges($item); ?></td>
                    <td style="text-align:center;white-space:nowrap;">
                      <a class="btn-sm btn-print"
                         href="<?php echo page_url; ?>abom/master_form/<?php echo (int) $item->id; ?>">Edit</a>
                      <button type="button"
                              class="btn-sm <?php echo empty($item->is_active) ? 'btn-generate' : 'abom-delete'; ?> abom-item-toggle"
                              data-item="<?php echo (int) $item->id; ?>"
                              data-active="<?php echo empty($item->is_active) ? 1 : 0; ?>"
                              data-desc="<?php echo abom_e($item->description); ?>">
                        <?php echo empty($item->is_active) ? 'Restore' : 'Retire'; ?>
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <p class="abom-rowedit-note">
            <b><?php echo count($items); ?></b> item<?php echo count($items) === 1 ? '' : 's'; ?> shown.
            Build variants, selection rules, sections, feature gates and quantity formulas
            are edited under <a href="<?php echo page_url; ?>abom/config">Build configuration</a>,
            and listed on the <a href="<?php echo page_url; ?>abom/guide#g3b">guide</a>.
          </p>
        <?php endif; ?>
      </div>

    </main>
  </div>
</div>

<?php $this->load->view('common/footer'); ?>

<script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
<script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
<script>
  window.ABOM_ITEM_TOGGLE_URL = '<?php echo page_url; ?>abom/master_toggle';
</script>
<script src="<?php echo abom_asset('abom/abom-master.js'); ?>"></script>
</body>
</html>
