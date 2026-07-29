<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!defined('ENVIRONMENT') || ENVIRONMENT !== 'development') {
    return;
}
?>
<style type="text/css">
    .pms-inline-error {
        margin: 14px;
        padding: 18px 20px;
        border-radius: 18px;
        border: 1px solid rgba(185, 28, 28, 0.18);
        background: linear-gradient(180deg, rgba(255, 247, 237, 0.96), rgba(255, 255, 255, 0.98));
        box-shadow: 0 18px 34px rgba(15, 23, 42, 0.08);
        color: #7c2d12;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }

    .pms-inline-error h4 {
        margin: 0 0 10px;
        color: #991b1b;
        font-size: 18px;
    }

    .pms-inline-error p {
        margin: 6px 0;
        line-height: 1.6;
    }

    .pms-inline-error code {
        display: inline-block;
        padding: 2px 6px;
        border-radius: 8px;
        background: rgba(153, 27, 27, 0.08);
        color: #7f1d1d;
        font-family: Consolas, Monaco, "Courier New", monospace;
        font-size: 12px;
    }
</style>

<div class="pms-inline-error">
    <h4>Developer Warning</h4>
    <p><strong>Severity:</strong> <?php echo htmlspecialchars((string) $severity, ENT_QUOTES, 'UTF-8'); ?></p>
    <p><strong>Message:</strong> <?php echo htmlspecialchars((string) $message, ENT_QUOTES, 'UTF-8'); ?></p>
    <p><strong>File:</strong> <code><?php echo htmlspecialchars((string) $filepath, ENT_QUOTES, 'UTF-8'); ?></code></p>
    <p><strong>Line:</strong> <?php echo htmlspecialchars((string) $line, ENT_QUOTES, 'UTF-8'); ?></p>
</div>
