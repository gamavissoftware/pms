<!DOCTYPE html>
<html>
<head>
    <title>Safe Data Cleanup</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 900px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; text-align: left; padding: 12px; vertical-align: top; }
        th { background-color: #f7f7f7; }
        .message { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .submit-btn { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .submit-btn:hover { background-color: #0056b3; }
        ul { margin-top: 0; padding-left: 20px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Part Name Cleanup (Safe Mode)</h2>
    <p>This tool groups parts with formatting differences. <strong>Check the 'Update' box</strong> for each group you want to fix and then press the update button.</p>

    <?php if($this->session->flashdata('success_message')): ?>
        <div class="message success"><?php echo $this->session->flashdata('success_message'); ?></div>
    <?php endif; ?>
    <?php if($this->session->flashdata('error_message')): ?>
        <div class="message error"><?php echo $this->session->flashdata('error_message'); ?></div>
    <?php endif; ?>

    <?php if (empty($groups_to_clean)): ?>
        <p>✅ No duplicate part names found. Your data looks clean!</p>
    <?php else: ?>
        <?php echo form_open('data_cleanup/update'); ?>
        <table>
            <tr>
                <th>Update?</th>
                <th>Duplicate Variations Found</th>
                <th>Corrected Name</th>
            </tr>
            <?php foreach ($groups_to_clean as $group): 
                $json_group = htmlspecialchars(json_encode($group), ENT_QUOTES, 'UTF-8');
            ?>
                <tr>
                    <td><input type="checkbox" name="checked_groups[]" value="<?php echo $json_group; ?>"></td>
                    <td>
                        <ul>
                            <?php foreach($group as $name): ?>
                                <li><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                    <td>
                        <input type="text" name="correction[<?php echo $json_group; ?>]" value="<?php echo htmlspecialchars(trim($group[0]), ENT_QUOTES, 'UTF-8'); ?>" style="width: 95%; padding: 5px;">
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <br>
        <button type="submit" class="submit-btn">Update Selected Groups</button>
        <?php echo form_close(); ?>
    <?php endif; ?>
</div>

</body>
</html>