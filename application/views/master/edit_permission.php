<?php
$permission_user = isset($permission_user) && is_array($permission_user) ? $permission_user : array();
$permission_modules = isset($permission_modules) && is_array($permission_modules) ? $permission_modules : array();
$permission_summary = isset($permission_summary) && is_array($permission_summary) ? $permission_summary : array();
$permission_summary = array_merge(array(
    'total_modules' => 0,
    'active_modules' => 0,
    'total_shortcuts' => 0,
    'allowed_shortcuts' => 0,
    'df_shortcuts' => 0
), $permission_summary);

$user_id = !empty($permission_user['user_id']) ? (int)$permission_user['user_id'] : 0;
$user_full_name = trim(
    (!empty($permission_user['title']) ? $permission_user['title'] . ' ' : '') .
    (!empty($permission_user['first_name']) ? $permission_user['first_name'] . ' ' : '') .
    (!empty($permission_user['last_name']) ? $permission_user['last_name'] : '')
);
$user_full_name = $user_full_name !== '' ? $user_full_name : 'Selected User';
$department_name = !empty($permission_user['department']) ? $permission_user['department'] : 'Department not mapped';
$company_name = !empty($permission_user['company_name']) ? $permission_user['company_name'] : 'Business unit not mapped';
$user_email = !empty($permission_user['email']) ? $permission_user['email'] : 'Email not available';
$avatar_letter = strtoupper(substr($user_full_name, 0, 1));
$permission_page_title = !empty($permission_page_title) ? $permission_page_title : 'Edit Access Permission';
$permission_form_action = !empty($permission_form_action) ? $permission_form_action : page_url . 'Master/User_management/edit_capablities/' . $user_id;
$permission_submit_label = !empty($permission_submit_label) ? $permission_submit_label : 'Update Permission';
$permission_back_url = !empty($permission_back_url) ? $permission_back_url : page_url . 'Master/User_management/userwise_permission_dashboard/';
$permission_save_heading = !empty($permission_save_heading) ? $permission_save_heading : 'Save changes for ' . $user_full_name;
$permission_save_note = !empty($permission_save_note) ? $permission_save_note : 'Turn the module on, then allow only the submodules this user should actually use.';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> <?php echo htmlspecialchars($permission_page_title, ENT_QUOTES, 'UTF-8'); ?></title>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>

    <style>
        body {
            background: #f3f6f9;
        }

        .permission-page {
            padding-bottom: 110px;
        }

        .permission-shell {
            background: #fff;
            border: 1px solid #dde6ef;
            border-radius: 20px;
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.06);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .permission-header {
            padding: 22px 24px 18px;
            border-bottom: 1px solid #e7eef5;
            background: linear-gradient(180deg, #fbfdff 0%, #f7fbff 100%);
        }

        .permission-header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 18px;
            flex-wrap: wrap;
        }

        .permission-user {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .permission-avatar {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: #17365d;
            color: #fff;
            font-size: 24px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .permission-user-copy h3 {
            margin: 0;
            color: #0f172a;
            font-size: 24px;
            font-weight: 800;
        }

        .permission-user-copy p,
        .permission-user-copy span {
            margin: 4px 0 0;
            color: #64748b;
            line-height: 1.6;
            display: block;
        }

        .permission-meta {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 18px;
        }

        .meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: 999px;
            background: #eef4fb;
            border: 1px solid #d7e4f0;
            color: #17365d;
            font-size: 12px;
            font-weight: 700;
        }

        .permission-tools {
            padding: 18px 24px;
            border-bottom: 1px solid #e7eef5;
            background: #fff;
        }

        .permission-tools-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .search-wrap {
            position: relative;
            flex: 1 1 340px;
            max-width: 520px;
        }

        .search-wrap input {
            height: 46px;
            border-radius: 14px;
            border: 1px solid #ccd8e4;
            padding-left: 44px;
            box-shadow: none;
        }

        .search-wrap .search-icon {
            position: absolute;
            left: 16px;
            top: 14px;
            color: #64748b;
            font-size: 15px;
        }

        .tool-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .tool-buttons .btn {
            border-radius: 12px;
            font-weight: 700;
        }

        .permission-accordion {
            padding: 20px 24px 24px;
        }

        .permission-accordion-item {
            border: 1px solid #dfe7ef;
            border-radius: 18px;
            background: #fff;
            margin-bottom: 16px;
            overflow: hidden;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .permission-accordion-item:last-child {
            margin-bottom: 0;
        }

        .permission-accordion-item.is-open {
            border-color: #bcd1e5;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
        }

        .permission-accordion-item.is-disabled {
            background: #fcfdff;
        }

        .permission-accordion-item.is-hidden {
            display: none;
        }

        .accordion-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 16px 18px;
            background: #fff;
        }

        .accordion-trigger {
            flex: 1 1 auto;
            display: flex;
            align-items: center;
            gap: 14px;
            width: 100%;
            border: 0;
            background: transparent;
            padding: 0;
            text-align: left;
            color: inherit;
        }

        .accordion-trigger:focus {
            outline: none;
        }

        .accordion-index {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: #eaf2fb;
            color: #1d4ed8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .accordion-copy {
            min-width: 0;
            flex: 1 1 auto;
        }

        .accordion-title {
            display: block;
            color: #0f172a;
            font-size: 18px;
            font-weight: 800;
            line-height: 1.4;
        }

        .accordion-meta {
            display: block;
            margin-top: 4px;
            color: #64748b;
            font-size: 13px;
            line-height: 1.6;
        }

        .accordion-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 84px;
            padding: 7px 12px;
            border-radius: 999px;
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .accordion-status.is-off {
            background: #f1f5f9;
            color: #475569;
            border-color: #cbd5e1;
        }

        .accordion-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: #f1f5f9;
            color: #17365d;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }

        .permission-accordion-item.is-open .accordion-icon {
            transform: rotate(180deg);
        }

        .module-switch {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            flex-shrink: 0;
            user-select: none;
        }

        .module-switch input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .module-switch-track {
            width: 46px;
            height: 26px;
            border-radius: 999px;
            background: #cbd5e1;
            position: relative;
            transition: background 0.2s ease;
        }

        .module-switch-track:after {
            content: "";
            position: absolute;
            left: 3px;
            top: 3px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 3px 8px rgba(15, 23, 42, 0.16);
            transition: transform 0.2s ease;
        }

        .module-switch input[type="checkbox"]:checked + .module-switch-track {
            background: #17365d;
        }

        .module-switch input[type="checkbox"]:checked + .module-switch-track:after {
            transform: translateX(20px);
        }

        .module-switch-label {
            color: #17365d;
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
        }

        .accordion-body {
            display: none;
            padding: 0 18px 18px;
            border-top: 1px solid #e7eef5;
            background: #fbfdff;
        }

        .module-quick-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
            padding: 14px 0;
        }

        .module-quick-actions .btn {
            border-radius: 10px;
            font-weight: 700;
        }

        .submodule-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .submodule-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            border: 1px solid #e3ebf3;
            border-radius: 14px;
            background: #fff;
        }

        .submodule-copy {
            min-width: 0;
            flex: 1 1 auto;
        }

        .submodule-title {
            color: #0f172a;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.5;
        }

        .submodule-note {
            margin-top: 4px;
            color: #64748b;
            font-size: 13px;
            line-height: 1.6;
        }

        .submodule-badge {
            display: inline-block;
            margin-left: 8px;
            padding: 4px 9px;
            border-radius: 999px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            font-size: 11px;
            font-weight: 700;
            vertical-align: middle;
        }

        .submodule-toggle {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            color: #17365d;
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
            margin: 0;
        }

        .submodule-toggle input {
            width: 18px;
            height: 18px;
        }

        .empty-module {
            padding: 18px;
            border: 1px dashed #cbd5e1;
            border-radius: 14px;
            background: #fff;
            color: #64748b;
            line-height: 1.7;
        }

        .no-result-card {
            display: none;
            margin: 0 24px 24px;
            padding: 22px;
            border: 1px dashed #cbd5e1;
            border-radius: 16px;
            background: #fff;
            text-align: center;
            color: #64748b;
        }

        .permission-savebar {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            padding: 12px 0;
            background: rgba(255, 255, 255, 0.96);
            border-top: 1px solid #dbe4ed;
            box-shadow: 0 -10px 24px rgba(15, 23, 42, 0.08);
            backdrop-filter: blur(10px);
            z-index: 999;
        }

        .savebar-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
        }

        .savebar-copy strong {
            display: block;
            color: #0f172a;
            font-size: 15px;
            margin-bottom: 3px;
        }

        .savebar-copy span {
            display: block;
            color: #64748b;
            font-size: 13px;
            line-height: 1.6;
        }

        .savebar-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .savebar-actions .btn {
            min-width: 160px;
            height: 44px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
        }

        @media (max-width: 991px) {
            .accordion-head,
            .submodule-row,
            .savebar-inner {
                flex-direction: column;
                align-items: stretch;
            }

            .module-switch {
                justify-content: space-between;
            }

            .module-quick-actions {
                justify-content: flex-start;
            }

            .savebar-actions .btn {
                min-width: 0;
                width: 100%;
            }
        }

        @media (max-width: 767px) {
            .permission-header,
            .permission-tools,
            .permission-accordion {
                padding-left: 16px;
                padding-right: 16px;
            }

            .no-result-card {
                margin-left: 16px;
                margin-right: 16px;
            }

            .permission-user-copy h3 {
                font-size: 20px;
            }

            .accordion-title {
                font-size: 16px;
            }

            .permission-page {
                padding-bottom: 140px;
            }
        }
    </style>
</head>

<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>

    <div class="wrapper permission-page">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title text-center"><?php echo htmlspecialchars($permission_page_title, ENT_QUOTES, 'UTF-8'); ?></h4>
                    </div>
                </div>
            </div>

            <?php echo $this->session->flashdata('message'); ?>

            <div class="permission-shell">
                <div class="permission-header">
                    <div class="permission-header-top">
                        <div class="permission-user">
                            <div class="permission-avatar"><?php echo htmlspecialchars($avatar_letter, ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="permission-user-copy">
                                <h3><?php echo htmlspecialchars($user_full_name, ENT_QUOTES, 'UTF-8'); ?></h3>
                                <p><?php echo htmlspecialchars($department_name, ENT_QUOTES, 'UTF-8'); ?> | <?php echo htmlspecialchars($company_name, ENT_QUOTES, 'UTF-8'); ?></p>
                                <span><?php echo htmlspecialchars($user_email, ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="permission-meta">
                        <span class="meta-pill"><?php echo (int)$permission_summary['total_modules']; ?> Modules</span>
                        <span class="meta-pill"><?php echo (int)$permission_summary['active_modules']; ?> Enabled</span>
                        <span class="meta-pill"><?php echo (int)$permission_summary['allowed_shortcuts']; ?> Allowed Submodules</span>
                        <span class="meta-pill"><?php echo (int)$permission_summary['df_shortcuts']; ?> DF Change Items</span>
                    </div>
                </div>

                <div class="permission-tools">
                    <div class="permission-tools-row">
                        <div class="search-wrap">
                            <span class="search-icon glyphicon glyphicon-search"></span>
                            <input type="text" class="form-control" id="permission-search" placeholder="Search module or submodule">
                        </div>
                        <div class="tool-buttons">
                            <button type="button" class="btn btn-default" id="expand-all">Expand All</button>
                            <button type="button" class="btn btn-default" id="collapse-all">Collapse All</button>
                        </div>
                    </div>
                </div>

                <form name="frm" action="<?php echo $permission_form_action; ?>" method="post">
                    <div class="permission-accordion" id="permission-module-list">
                        <?php foreach ($permission_modules as $module_index => $module_row) { ?>
                            <?php
                            $module_id = (int)$module_row['id'];
                            $is_enabled = !empty($module_row['is_enabled']);
                            $submodules = !empty($module_row['submodules']) ? $module_row['submodules'] : array();
                            $is_open = $module_index === 0;
                            $module_search = strtolower($module_row['name']);
                            foreach ($submodules as $submodule_row) {
                                $module_search .= ' ' . strtolower($submodule_row['label']);
                                if (!empty($submodule_row['description'])) {
                                    $module_search .= ' ' . strtolower($submodule_row['description']);
                                }
                                if (!empty($submodule_row['badge'])) {
                                    $module_search .= ' ' . strtolower($submodule_row['badge']);
                                }
                            }
                            ?>
                            <div
                                class="permission-accordion-item <?php echo $is_open ? 'is-open' : ''; ?> <?php echo $is_enabled ? '' : 'is-disabled'; ?>"
                                data-module-card
                                data-module-id="<?php echo $module_id; ?>"
                                data-module-wrapper="<?php echo $module_id; ?>"
                                data-search="<?php echo htmlspecialchars($module_search, ENT_QUOTES, 'UTF-8'); ?>"
                            >
                                <input type="hidden" name="module[]" value="<?php echo $module_id; ?>">

                                <div class="accordion-head">
                                    <button
                                        type="button"
                                        class="accordion-trigger"
                                        data-accordion-trigger="<?php echo $module_id; ?>"
                                        aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>"
                                    >
                                        <span class="accordion-index"><?php echo str_pad($module_index + 1, 2, '0', STR_PAD_LEFT); ?></span>
                                        <span class="accordion-copy">
                                            <span class="accordion-title"><?php echo htmlspecialchars($module_row['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                            <span class="accordion-meta"><?php echo count($submodules); ?> submodule permission(s)</span>
                                        </span>
                                        <span class="accordion-status <?php echo $is_enabled ? '' : 'is-off'; ?>" data-module-status="<?php echo $module_id; ?>">
                                            <?php echo $is_enabled ? 'Enabled' : 'Disabled'; ?>
                                        </span>
                                        <span class="accordion-icon">
                                            <i class="fa fa-angle-down"></i>
                                        </span>
                                    </button>

                                    <label class="module-switch" onclick="event.stopPropagation();">
                                        <input type="hidden" name="moduleaccess<?php echo $module_id; ?>" value="0">
                                        <input
                                            type="checkbox"
                                            name="moduleaccess<?php echo $module_id; ?>"
                                            value="1"
                                            data-module-toggle="<?php echo $module_id; ?>"
                                            <?php if ($is_enabled) { ?>checked<?php } ?>
                                        >
                                        <span class="module-switch-track"></span>
                                        <span class="module-switch-label">Module Access</span>
                                    </label>
                                </div>

                                <div
                                    class="accordion-body"
                                    id="show_detail<?php echo $module_id; ?>"
                                    data-accordion-body="<?php echo $module_id; ?>"
                                    style="<?php echo $is_open ? 'display:block;' : 'display:none;'; ?>"
                                >
                                    <?php if (!empty($submodules)) { ?>
                                        <div class="module-quick-actions">
                                            <button type="button" class="btn btn-default btn-xs module-action-btn-<?php echo $module_id; ?>" data-module-select-all="<?php echo $module_id; ?>">Allow All</button>
                                            <button type="button" class="btn btn-default btn-xs module-action-btn-<?php echo $module_id; ?>" data-module-clear-all="<?php echo $module_id; ?>">Clear All</button>
                                        </div>

                                        <div class="submodule-list">
                                            <?php foreach ($submodules as $submodule_row) { ?>
                                                <?php $submodule_id = (int)$submodule_row['id']; ?>
                                                <div class="submodule-row">
                                                    <div class="submodule-copy">
                                                        <div class="submodule-title">
                                                            <?php echo htmlspecialchars($submodule_row['label'], ENT_QUOTES, 'UTF-8'); ?>
                                                            <?php if (!empty($submodule_row['badge'])) { ?>
                                                                <span class="submodule-badge"><?php echo htmlspecialchars($submodule_row['badge'], ENT_QUOTES, 'UTF-8'); ?></span>
                                                            <?php } ?>
                                                        </div>
                                                        <div class="submodule-note">
                                                            <?php echo !empty($submodule_row['description']) ? htmlspecialchars($submodule_row['description'], ENT_QUOTES, 'UTF-8') : 'Allow this submodule to appear in the user flow and related navigation.'; ?>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <input type="hidden" name="submodule<?php echo $module_id; ?>[]" value="<?php echo $submodule_id; ?>">
                                                        <label class="submodule-toggle">
                                                            <input
                                                                type="checkbox"
                                                                name="add<?php echo $module_id; ?><?php echo $submodule_id; ?>"
                                                                value="1"
                                                                class="module-checkbox module-checkbox-<?php echo $module_id; ?>"
                                                                <?php if (!empty($submodule_row['allow'])) { ?>checked<?php } ?>
                                                            >
                                                            <span>Allow</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } else { ?>
                                        <div class="empty-module">
                                            No submodule permissions are configured for this module yet. You can still keep the module enabled if this area uses module-level access only.
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="no-result-card" id="permission-no-results">
                        No module matched your search. Try a module name like <strong>MOM Module</strong> or a submodule like <strong>DF Change Control Dashboard</strong>.
                    </div>

                    <div class="permission-savebar">
                        <div class="container">
                            <div class="savebar-inner">
                                <div class="savebar-copy">
                                    <strong><?php echo htmlspecialchars($permission_save_heading, ENT_QUOTES, 'UTF-8'); ?></strong>
                                    <span><?php echo htmlspecialchars($permission_save_note, ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <div class="savebar-actions">
                                    <a href="<?php echo $permission_back_url; ?>" class="btn btn-default">Back</a>
                                    <button type="submit" class="btn btn-success"><?php echo htmlspecialchars($permission_submit_label, ENT_QUOTES, 'UTF-8'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php $this->load->view('common/footer'); ?>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/detect.js"></script>
    <script src="<?php echo assets_url; ?>js/fastclick.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.slimscroll.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.blockUI.js"></script>
    <script src="<?php echo assets_url; ?>js/waves.js"></script>
    <script src="<?php echo assets_url; ?>js/wow.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.nicescroll.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.scrollTo.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

    <script>
        (function() {
            function getModuleItem(moduleId) {
                return document.querySelector('[data-module-id="' + moduleId + '"]');
            }

            function getAccordionBody(moduleId) {
                return document.querySelector('[data-accordion-body="' + moduleId + '"]');
            }

            function getAccordionTrigger(moduleId) {
                return document.querySelector('[data-accordion-trigger="' + moduleId + '"]');
            }

            function getModuleToggle(moduleId) {
                return document.querySelector('[data-module-toggle="' + moduleId + '"]');
            }

            function setAccordionState(moduleId, shouldOpen) {
                var item = getModuleItem(moduleId);
                var body = getAccordionBody(moduleId);
                var trigger = getAccordionTrigger(moduleId);

                if (!item || !body || !trigger) {
                    return;
                }

                item.classList.toggle('is-open', shouldOpen);
                body.style.display = shouldOpen ? 'block' : 'none';
                trigger.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
            }

            function updateModuleState(moduleId, autoOpen) {
                var toggle = getModuleToggle(moduleId);
                var item = getModuleItem(moduleId);
                var status = document.querySelector('[data-module-status="' + moduleId + '"]');
                var checkboxes = document.querySelectorAll('.module-checkbox-' + moduleId);
                var actionButtons = document.querySelectorAll('.module-action-btn-' + moduleId);
                var isEnabled = !!(toggle && toggle.checked);

                if (item) {
                    item.classList.toggle('is-disabled', !isEnabled);
                }

                if (status) {
                    status.textContent = isEnabled ? 'Enabled' : 'Disabled';
                    status.classList.toggle('is-off', !isEnabled);
                }

                for (var i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].disabled = !isEnabled;
                }

                for (var j = 0; j < actionButtons.length; j++) {
                    actionButtons[j].disabled = !isEnabled;
                }

                if (isEnabled && autoOpen) {
                    setAccordionState(moduleId, true);
                }
            }

            function applySearch() {
                var searchField = document.getElementById('permission-search');
                var query = searchField ? searchField.value.toLowerCase().trim() : '';
                var cards = document.querySelectorAll('[data-module-card]');
                var visibleCount = 0;
                var firstMatchId = null;

                for (var i = 0; i < cards.length; i++) {
                    var haystack = cards[i].getAttribute('data-search') || '';
                    var matches = query === '' || haystack.indexOf(query) !== -1;
                    cards[i].classList.toggle('is-hidden', !matches);

                    if (matches) {
                        visibleCount++;
                        if (!firstMatchId) {
                            firstMatchId = cards[i].getAttribute('data-module-id');
                        }
                    }
                }

                var emptyCard = document.getElementById('permission-no-results');
                if (emptyCard) {
                    emptyCard.style.display = visibleCount === 0 ? 'block' : 'none';
                }

                if (query !== '' && firstMatchId) {
                    setAccordionState(firstMatchId, true);
                }
            }

            function toggleVisibleAccordions(shouldOpen) {
                var cards = document.querySelectorAll('[data-module-card]');
                for (var i = 0; i < cards.length; i++) {
                    if (!cards[i].classList.contains('is-hidden')) {
                        setAccordionState(cards[i].getAttribute('data-module-id'), shouldOpen);
                    }
                }
            }

            var accordionTriggers = document.querySelectorAll('[data-accordion-trigger]');
            for (var i = 0; i < accordionTriggers.length; i++) {
                accordionTriggers[i].addEventListener('click', function() {
                    var moduleId = this.getAttribute('data-accordion-trigger');
                    var item = getModuleItem(moduleId);
                    setAccordionState(moduleId, !(item && item.classList.contains('is-open')));
                });
            }

            var moduleToggles = document.querySelectorAll('[data-module-toggle]');
            for (var j = 0; j < moduleToggles.length; j++) {
                updateModuleState(moduleToggles[j].getAttribute('data-module-toggle'), false);
                moduleToggles[j].addEventListener('change', function() {
                    updateModuleState(this.getAttribute('data-module-toggle'), true);
                });
            }

            var selectAllButtons = document.querySelectorAll('[data-module-select-all]');
            for (var k = 0; k < selectAllButtons.length; k++) {
                selectAllButtons[k].addEventListener('click', function() {
                    var moduleId = this.getAttribute('data-module-select-all');
                    var checkboxes = document.querySelectorAll('.module-checkbox-' + moduleId);
                    for (var index = 0; index < checkboxes.length; index++) {
                        if (!checkboxes[index].disabled) {
                            checkboxes[index].checked = true;
                        }
                    }
                });
            }

            var clearAllButtons = document.querySelectorAll('[data-module-clear-all]');
            for (var l = 0; l < clearAllButtons.length; l++) {
                clearAllButtons[l].addEventListener('click', function() {
                    var moduleId = this.getAttribute('data-module-clear-all');
                    var checkboxes = document.querySelectorAll('.module-checkbox-' + moduleId);
                    for (var index = 0; index < checkboxes.length; index++) {
                        if (!checkboxes[index].disabled) {
                            checkboxes[index].checked = false;
                        }
                    }
                });
            }

            var searchField = document.getElementById('permission-search');
            if (searchField) {
                searchField.addEventListener('input', applySearch);
            }

            var expandButton = document.getElementById('expand-all');
            if (expandButton) {
                expandButton.addEventListener('click', function() {
                    toggleVisibleAccordions(true);
                });
            }

            var collapseButton = document.getElementById('collapse-all');
            if (collapseButton) {
                collapseButton.addEventListener('click', function() {
                    toggleVisibleAccordions(false);
                });
            }

            applySearch();
        })();
    </script>
</body>
</html>
