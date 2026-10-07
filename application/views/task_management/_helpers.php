<?php
/**
 * Small formatters shared by every Task Management screen.
 *
 * These live in a view partial rather than a CI helper because they emit
 * MARKUP tied to the classes in _head.php - they are part of this module's
 * presentation, not general-purpose utilities. _head.php includes this file,
 * so every page that draws the module chrome has them.
 *
 * All of them are null- and '0000-00-00'-safe: PHP 8 raises a deprecation for
 * strtotime(null), and the date columns in task_management_items are nullable.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('tm_has_date')) {
    function tm_has_date($value)
    {
        $value = trim((string) $value);
        return $value !== '' && $value !== '0000-00-00' && $value !== '0000-00-00 00:00:00';
    }
}

if (!function_exists('tm_date')) {
    /** A date column as text, or a dash when it was never set. */
    function tm_date($value, $format = 'd M Y')
    {
        if (!tm_has_date($value)) {
            return '-';
        }

        $stamp = strtotime((string) $value);
        return $stamp ? date($format, $stamp) : '-';
    }
}

if (!function_exists('tm_days_to_due')) {
    /** Whole days from today to the due date; negative means it has passed. */
    function tm_days_to_due($due_date)
    {
        if (!tm_has_date($due_date)) {
            return null;
        }

        $due = strtotime(date('Y-m-d', strtotime((string) $due_date)) . ' 00:00:00');
        $today = strtotime(date('Y-m-d') . ' 00:00:00');
        if (!$due) {
            return null;
        }

        return (int) round(($due - $today) / 86400);
    }
}

if (!function_exists('tm_due_chip')) {
    /**
     * The due date in words - "3 days left", "2 days overdue", "Due today".
     *
     * A bare date makes every reader do the arithmetic themselves, and that is
     * the one calculation that decides whether a task needs attention now. A
     * completed task gets no chip at all: its due date stopped mattering the
     * moment it closed.
     */
    function tm_due_chip($due_date, $status = '')
    {
        if (strtoupper((string) $status) === 'COMPLETED') {
            return '';
        }

        $days = tm_days_to_due($due_date);
        if ($days === null) {
            return '';
        }

        if ($days < 0) {
            $n = abs($days);
            $class = 'is-late';
            $text = $n . ' day' . ($n === 1 ? '' : 's') . ' overdue';
        } elseif ($days === 0) {
            $class = 'is-today';
            $text = 'Due today';
        } elseif ($days === 1) {
            $class = 'is-soon';
            $text = 'Due tomorrow';
        } elseif ($days <= 7) {
            $class = 'is-soon';
            $text = $days . ' days left';
        } else {
            $class = '';
            $text = $days . ' days left';
        }

        return '<span class="tm-due ' . $class . '">' . html_escape($text) . '</span>';
    }
}

if (!function_exists('tm_status_badge')) {
    function tm_status_badge($status)
    {
        $status = (string) $status;
        $key = strtolower($status);
        $label = ucwords(strtolower(str_replace('_', ' ', $status)));

        return '<span class="tm-status tm-status-' . html_escape($key) . '">' . html_escape($label) . '</span>';
    }
}

if (!function_exists('tm_priority_badge')) {
    function tm_priority_badge($priority)
    {
        $priority = (string) $priority;
        $key = strtolower($priority);
        $label = ucwords(strtolower($priority));

        return '<span class="tm-priority tm-priority-' . html_escape($key) . '">' . html_escape($label) . '</span>';
    }
}

if (!function_exists('tm_progress_cell')) {
    /** Percentage and bar on one line, so the column stays narrow. */
    function tm_progress_cell($percent)
    {
        $percent = max(0, min(100, (int) $percent));

        return '<div class="tm-progress-wrap">'
             . '<span class="tm-strong">' . $percent . '%</span>'
             . '<span class="tm-progress-track"><span class="tm-progress-bar" style="width:' . $percent . '%;"></span></span>'
             . '</div>';
    }
}

if (!function_exists('tm_relative_time')) {
    /** "12 minutes ago" / "3 days ago" - for things that just happened. */
    function tm_relative_time($value)
    {
        if (!tm_has_date($value)) {
            return '';
        }

        $stamp = strtotime((string) $value);
        if (!$stamp) {
            return '';
        }

        $seconds = time() - $stamp;
        if ($seconds < 60) {
            return 'just now';
        }
        if ($seconds < 3600) {
            $m = (int) floor($seconds / 60);
            return $m . ' minute' . ($m === 1 ? '' : 's') . ' ago';
        }
        if ($seconds < 86400) {
            $h = (int) floor($seconds / 3600);
            return $h . ' hour' . ($h === 1 ? '' : 's') . ' ago';
        }
        if ($seconds < 604800) {
            $d = (int) floor($seconds / 86400);
            return $d . ' day' . ($d === 1 ? '' : 's') . ' ago';
        }

        return date('d M Y', $stamp);
    }
}

if (!function_exists('tm_empty_state')) {
    function tm_empty_state($title, $note = '')
    {
        return '<div class="tm-empty">'
             . '<svg width="34" height="34" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">'
             . '<path d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2zm0 16H5V5h14v14zM7 12h10v2H7zm0-4h10v2H7zm0 8h6v2H7z"/></svg>'
             . '<div class="tm-empty-title">' . html_escape($title) . '</div>'
             . ($note !== '' ? '<div>' . html_escape($note) . '</div>' : '')
             . '</div>';
    }
}
