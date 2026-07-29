<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('friendly_error_escape')) {
    function friendly_error_escape($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

$error_label = isset($error_label) ? (string) $error_label : 'Temporary service issue';
$error_title = isset($error_title) ? (string) $error_title : 'This page needs a quick retry';
$error_message = isset($error_message) ? (string) $error_message : 'Something interrupted the page load. Please refresh once and try again.';
$error_reference = isset($error_reference) ? (string) $error_reference : date('d M Y h:i A');
$error_debug_items = (isset($error_debug_items) && is_array($error_debug_items)) ? $error_debug_items : array();
$show_debug = !empty($show_debug);
$base_path = isset($_SERVER['SCRIPT_NAME']) ? rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/') : '';
$home_url = ($base_path === '' ? '' : $base_path) . '/index.php';
$request_uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo friendly_error_escape($error_title); ?></title>
<style type="text/css">
    :root {
        --page-bg: #f3f6fb;
        --card-bg: rgba(255, 255, 255, 0.96);
        --card-border: rgba(15, 23, 42, 0.08);
        --heading: #102542;
        --body: #4a5a74;
        --muted: #72819a;
        --accent: #0f766e;
        --accent-soft: rgba(15, 118, 110, 0.14);
        --warm: #f59e0b;
        --warm-soft: rgba(245, 158, 11, 0.16);
        --shadow: 0 28px 60px rgba(15, 23, 42, 0.12);
    }

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        min-height: 100vh;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        color: var(--body);
        background:
            radial-gradient(circle at top left, rgba(15, 118, 110, 0.12), transparent 34%),
            radial-gradient(circle at bottom right, rgba(245, 158, 11, 0.12), transparent 28%),
            linear-gradient(160deg, #eef4fb 0%, #f9fbfd 52%, #f2f6fb 100%);
    }

    .error-stage {
        width: min(920px, calc(100% - 32px));
        margin: 0 auto;
        padding: 48px 0;
    }

    .error-card,
    .debug-card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 28px;
        box-shadow: var(--shadow);
        backdrop-filter: blur(12px);
    }

    .error-card {
        position: relative;
        overflow: hidden;
        padding: 36px;
    }

    .error-card::before,
    .error-card::after {
        content: "";
        position: absolute;
        border-radius: 999px;
        pointer-events: none;
    }

    .error-card::before {
        width: 240px;
        height: 240px;
        top: -90px;
        right: -60px;
        background: rgba(15, 118, 110, 0.08);
    }

    .error-card::after {
        width: 180px;
        height: 180px;
        bottom: -90px;
        left: -50px;
        background: rgba(245, 158, 11, 0.11);
    }

    .brand-row {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 26px;
    }

    .brand-mark {
        width: 50px;
        height: 50px;
        border-radius: 16px;
        background: linear-gradient(135deg, #0f766e 0%, #0b4f6c 100%);
        color: #fff;
        font-size: 18px;
        font-weight: 700;
        letter-spacing: 0.12em;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 18px 34px rgba(11, 79, 108, 0.24);
    }

    .brand-copy {
        min-width: 0;
    }

    .brand-name {
        color: var(--heading);
        font-size: 15px;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .brand-note {
        margin-top: 4px;
        color: var(--muted);
        font-size: 13px;
    }

    .status-pill {
        position: relative;
        z-index: 1;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 999px;
        background: var(--accent-soft);
        color: var(--accent);
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .status-pill::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: var(--accent);
        box-shadow: 0 0 0 6px rgba(15, 118, 110, 0.12);
    }

    h1 {
        position: relative;
        z-index: 1;
        margin: 22px 0 14px;
        color: var(--heading);
        font-size: clamp(28px, 4.6vw, 42px);
        line-height: 1.08;
        letter-spacing: -0.03em;
    }

    .lead {
        position: relative;
        z-index: 1;
        margin: 0;
        max-width: 700px;
        color: var(--body);
        font-size: 17px;
        line-height: 1.7;
    }

    .reassurance-row {
        position: relative;
        z-index: 1;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin: 26px 0 0;
    }

    .reassurance-chip {
        padding: 11px 15px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.78);
        border: 1px solid rgba(15, 23, 42, 0.07);
        color: var(--body);
        font-size: 14px;
        line-height: 1.5;
    }

    .reassurance-chip strong {
        color: var(--heading);
    }

    .actions {
        position: relative;
        z-index: 1;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 30px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 46px;
        padding: 0 18px;
        border-radius: 14px;
        border: 1px solid transparent;
        text-decoration: none;
        font-size: 14px;
        font-weight: 700;
        transition: transform 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    .btn-primary {
        background: linear-gradient(135deg, #0f766e 0%, #0b4f6c 100%);
        color: #fff;
        box-shadow: 0 16px 28px rgba(11, 79, 108, 0.24);
    }

    .btn-secondary {
        background: #fff;
        border-color: rgba(15, 23, 42, 0.12);
        color: var(--heading);
    }

    .btn-secondary:hover {
        box-shadow: 0 12px 22px rgba(15, 23, 42, 0.08);
    }

    .btn-ghost {
        background: var(--warm-soft);
        color: #9a6700;
    }

    .reference {
        position: relative;
        z-index: 1;
        margin-top: 20px;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.6;
    }

    .debug-card {
        margin-top: 20px;
        padding: 22px 24px;
    }

    .debug-card h2 {
        margin: 0 0 14px;
        color: var(--heading);
        font-size: 18px;
    }

    .debug-grid {
        display: grid;
        grid-template-columns: 180px 1fr;
        gap: 10px 16px;
        margin: 0;
    }

    .debug-grid dt {
        color: var(--muted);
        font-weight: 700;
    }

    .debug-grid dd {
        margin: 0;
        color: var(--heading);
        font-family: Consolas, Monaco, "Courier New", monospace;
        font-size: 13px;
        overflow-wrap: anywhere;
        white-space: pre-wrap;
    }

    @media (max-width: 680px) {
        .error-stage {
            width: min(100% - 24px, 920px);
            padding: 24px 0;
        }

        .error-card,
        .debug-card {
            border-radius: 22px;
        }

        .error-card {
            padding: 24px;
        }

        .debug-grid {
            grid-template-columns: 1fr;
            gap: 6px;
        }

        .actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }
    }
</style>
</head>
<body>
    <div class="error-stage">
        <section class="error-card">
            <div class="brand-row">
                <div class="brand-mark">SP</div>
                <div class="brand-copy">
                    <div class="brand-name">Shubham Pack PMS</div>
                    <div class="brand-note">Professional workflow management</div>
                </div>
            </div>

            <div class="status-pill"><?php echo friendly_error_escape($error_label); ?></div>

            <h1><?php echo friendly_error_escape($error_title); ?></h1>
            <p class="lead"><?php echo friendly_error_escape($error_message); ?></p>

            <div class="reassurance-row">
                <div class="reassurance-chip"><strong>Safe to retry:</strong> refreshing the page usually restores the screen.</div>
                <div class="reassurance-chip"><strong>No need to panic:</strong> this message is shown so users are not exposed to technical system output.</div>
                <div class="reassurance-chip"><strong>If it continues:</strong> your support team can investigate it using the reference below.</div>
            </div>

            <div class="actions">
                <a class="btn btn-primary" href="javascript:window.location.reload();">Try Again</a>
                <a class="btn btn-secondary" href="<?php echo friendly_error_escape($home_url); ?>">Go To Home</a>
                <a class="btn btn-ghost" href="javascript:window.history.back();">Go Back</a>
            </div>

            <div class="reference">
                Reference: <?php echo friendly_error_escape($error_reference); ?>
                <?php if ($request_uri !== ''): ?>
                    | Request: <?php echo friendly_error_escape($request_uri); ?>
                <?php endif; ?>
            </div>
        </section>

        <?php if ($show_debug && !empty($error_debug_items)): ?>
            <section class="debug-card">
                <h2>Developer Details</h2>
                <dl class="debug-grid">
                    <?php foreach ($error_debug_items as $debug_item): ?>
                        <?php
                        $debug_label = isset($debug_item['label']) ? (string) $debug_item['label'] : '';
                        $debug_value = isset($debug_item['value']) ? (string) $debug_item['value'] : '';
                        if ($debug_label === '' || $debug_value === '') {
                            continue;
                        }
                        ?>
                        <dt><?php echo friendly_error_escape($debug_label); ?></dt>
                        <dd><?php echo friendly_error_escape($debug_value); ?></dd>
                    <?php endforeach; ?>
                </dl>
            </section>
        <?php endif; ?>
    </div>
</body>
</html>
