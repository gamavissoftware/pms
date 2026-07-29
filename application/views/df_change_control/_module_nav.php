<?php
$current_page = isset($module_nav['current_page']) ? (string)$module_nav['current_page'] : '';
$show_hod_menu = !empty($module_nav['show_hod_menu']);
$head_queue_count = !empty($module_nav['head_queue_count']) ? (int)$module_nav['head_queue_count'] : 0;
$assigned_queue_count = !empty($module_nav['assigned_queue_count']) ? (int)$module_nav['assigned_queue_count'] : 0;
$can_view_dashboard = !empty($module_nav['can_view_dashboard']);
$can_raise_request = !empty($module_nav['can_raise_request']);
$assigned_only_access = !empty($module_nav['assigned_only_access']);
?>
<style>
    .module-menu {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
        padding: 16px 18px;
        background: #ffffff;
        border: 1px solid #dbe7f3;
        border-radius: 16px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
    }

    .module-menu-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 999px;
        border: 1px solid #dbe7f3;
        background: #f7fbff;
        color: #17365d;
        font-weight: 700;
        text-decoration: none !important;
        transition: all 0.2s ease;
    }

    .module-menu-link:hover,
    .module-menu-link:focus,
    .module-menu-link.active {
        background: #17365d;
        border-color: #17365d;
        color: #ffffff;
    }

    .module-menu-badge {
        min-width: 22px;
        height: 22px;
        padding: 0 7px;
        border-radius: 999px;
        background: #e82646;
        color: #ffffff;
        font-size: 12px;
        line-height: 22px;
        text-align: center;
    }
</style>

<?php if ($can_view_dashboard || $can_raise_request) { ?>
    <div class="module-menu">
        <?php if ($can_view_dashboard) { ?>
            <a class="module-menu-link <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>" href="<?php echo page_url; ?>Df_change_control">Overview</a>
        <?php } ?>
        <?php if ($can_raise_request) { ?>
            <a class="module-menu-link <?php echo $current_page === 'create' ? 'active' : ''; ?>" href="<?php echo page_url; ?>Df_change_control/create">Raise Request</a>
        <?php } ?>
        <?php if ($can_view_dashboard && $show_hod_menu && !$assigned_only_access) { ?>
            <a class="module-menu-link" href="<?php echo page_url; ?>Df_change_control#hod-queue">
                HOD Requests
                <?php if ($head_queue_count > 0) { ?>
                    <span class="module-menu-badge"><?php echo $head_queue_count; ?></span>
                <?php } ?>
            </a>
        <?php } ?>
        <?php if ($can_view_dashboard) { ?>
            <a class="module-menu-link" href="<?php echo page_url; ?>Df_change_control#assigned-queue">
                My Assignments
                <?php if ($assigned_queue_count > 0) { ?>
                    <span class="module-menu-badge"><?php echo $assigned_queue_count; ?></span>
                <?php } ?>
            </a>
            <?php if (!$assigned_only_access) { ?>
                <a class="module-menu-link" href="<?php echo page_url; ?>Df_change_control#recent-requests">All Requests</a>
            <?php } ?>
        <?php } ?>
    </div>
<?php } ?>
