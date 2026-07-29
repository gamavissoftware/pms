<?php if (!empty($module_nav) && is_array($module_nav)) { ?>
    <div class="tm-nav">
        <?php foreach ($module_nav as $item) { ?>
            <a class="tm-tab <?php echo (!empty($module_nav_active) && $module_nav_active === $item['key']) ? 'active' : ''; ?>" href="<?php echo $item['url']; ?>">
                <?php echo htmlspecialchars($item['label']); ?>
            </a>
        <?php } ?>
    </div>
<?php } ?>
