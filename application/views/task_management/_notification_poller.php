<?php if (empty($this->session->userdata['logged_in']['user_id'])) { return; } ?>
<style>
    #tm-growl-root {
        position: fixed;
        top: 84px;
        right: 20px;
        width: 340px;
        max-width: calc(100vw - 32px);
        z-index: 99999;
        display: flex;
        flex-direction: column;
        gap: 12px;
        pointer-events: none;
    }

    .tm-growl {
        pointer-events: auto;
        background: #173153;
        color: #ffffff;
        border-radius: 16px;
        padding: 16px 16px 14px;
        box-shadow: 0 16px 35px rgba(18, 30, 46, 0.28);
        border: 1px solid rgba(255,255,255,0.08);
        animation: tmGrowlIn 0.24s ease;
    }

    .tm-growl-title {
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.45px;
        text-transform: uppercase;
        color: rgba(255,255,255,0.74);
        margin-bottom: 8px;
    }

    .tm-growl-text {
        font-size: 14px;
        line-height: 1.5;
        color: #ffffff;
    }

    .tm-growl-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 14px;
    }

    .tm-growl-btn {
        border: none;
        border-radius: 999px;
        padding: 8px 12px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
    }

    .tm-growl-open {
        background: #ffffff;
        color: #183153;
    }

    .tm-growl-dismiss {
        background: rgba(255,255,255,0.14);
        color: #ffffff;
    }

    @keyframes tmGrowlIn {
        from {
            opacity: 0;
            transform: translateY(-8px) scale(0.98);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
</style>

<div id="tm-growl-root" aria-live="polite" aria-atomic="true"></div>

<script>
    (function () {
        var root = document.getElementById('tm-growl-root');
        if (!root || window.tmGrowlBooted) {
            return;
        }

        window.tmGrowlBooted = true;
        var shownNotifications = {};
        var fetchUrl = "<?php echo page_url . 'Task_management/fetch_notifications'; ?>";
        var markUrl = "<?php echo page_url . 'Task_management/mark_notification_read'; ?>";

        function markRead(notificationId) {
            var body = notificationId ? 'notification_id=' + encodeURIComponent(notificationId) : '';

            return fetch(markUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: body
            }).catch(function () {
                return null;
            });
        }

        function removeToast(toast) {
            if (toast && toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }

        function createToast(options) {
            if (!options) {
                return;
            }

            if (options.id && shownNotifications[options.id]) {
                return;
            }

            if (options.id) {
                shownNotifications[options.id] = true;
            }

            var toast = document.createElement('div');
            toast.className = 'tm-growl';

            var title = document.createElement('div');
            title.className = 'tm-growl-title';
            title.textContent = options.title || 'Task Management Alert';

            var text = document.createElement('div');
            text.className = 'tm-growl-text';
            text.textContent = options.message || 'New task update received.';

            var actions = document.createElement('div');
            actions.className = 'tm-growl-actions';

            var dismissBtn = document.createElement('button');
            dismissBtn.type = 'button';
            dismissBtn.className = 'tm-growl-btn tm-growl-dismiss';
            dismissBtn.textContent = options.dismissLabel || 'Dismiss';
            dismissBtn.addEventListener('click', function () {
                if (options.id) {
                    markRead(options.id);
                }
                removeToast(toast);
            });

            actions.appendChild(dismissBtn);

            if (options.actionUrl) {
                var openBtn = document.createElement('button');
                openBtn.type = 'button';
                openBtn.className = 'tm-growl-btn tm-growl-open';
                openBtn.textContent = options.actionLabel || 'Open';
                openBtn.addEventListener('click', function () {
                    var navigate = function () {
                        window.location.href = options.actionUrl;
                    };

                    if (options.id) {
                        markRead(options.id).finally(navigate);
                    } else {
                        navigate();
                    }
                });
                actions.appendChild(openBtn);
            }

            toast.appendChild(title);
            toast.appendChild(text);
            toast.appendChild(actions);
            root.appendChild(toast);

            if (options.autoDismissMs && options.autoDismissMs > 0) {
                window.setTimeout(function () {
                    removeToast(toast);
                }, options.autoDismissMs);
            }
        }

        function showToast(notification) {
            if (!notification) {
                return;
            }

            createToast({
                id: notification.id,
                title: 'Task Management Alert',
                message: notification.message || 'New task update received.',
                actionUrl: notification.action_url
            });
        }

        function pollNotifications() {
            fetch(fetchUrl, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(function (response) { return response.json(); })
                .then(function (items) {
                    if (!Array.isArray(items)) {
                        return;
                    }

                    items.slice().reverse().forEach(function (notification) {
                        showToast(notification);
                    });
                })
                .catch(function () {
                    return null;
                });
        }

        window.showTaskManagementGrowl = function (options) {
            createToast(options || {});
        };

        pollNotifications();
        window.setInterval(pollNotifications, 15000);
    })();
</script>
