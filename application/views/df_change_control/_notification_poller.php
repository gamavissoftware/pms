<link href="<?php echo assets_url; ?>plugins/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<script src="<?php echo assets_url; ?>plugins/toastr/toastr.min.js"></script>
<script>
    (function() {
        var shownDfSupportNotifications = {};

        function showDfSupportToast(message, onHidden) {
            if (!window.toastr) {
                return;
            }

            var toast = window.toastr.info(message, "New Notification", {
                timeOut: 0,
                extendedTimeOut: 0,
                closeButton: true,
                tapToDismiss: false,
                progressBar: true,
                newestOnTop: true,
                positionClass: "toast-bottom-right",
                onHidden: onHidden || null
            });

            if (toast && typeof toast.css === "function") {
                toast.css("cursor", "default");
            }
        }

        function postJson(url, body) {
            return fetch(url, {
                method: "POST",
                credentials: "same-origin",
                cache: "no-store",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: body || ""
            }).then(function(response) {
                return response.json();
            }).catch(function() {
                return [];
            });
        }

        function postQuiet(url, body) {
            if (navigator.sendBeacon) {
                var payload = new Blob([body || ""], {
                    type: "application/x-www-form-urlencoded; charset=UTF-8"
                });
                navigator.sendBeacon(url, payload);
                return;
            }

            fetch(url, {
                method: "POST",
                credentials: "same-origin",
                cache: "no-store",
                keepalive: true,
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: body || ""
            }).catch(function() {});
        }

        function markDfSupportNotificationAsRead(notificationId) {
            postQuiet(
                "<?php echo page_url . 'Maintenance_support/mark_notifications_read'; ?>",
                "notification_id=" + encodeURIComponent(notificationId)
            );
        }

        function pollDfSupportNotifications() {
            postJson("<?php echo page_url . 'Maintenance_support/fetch_notificationsofusers'; ?>").then(function(data) {
                var notifications = Array.isArray(data) ? data : [];

                notifications.forEach(function(notification) {
                    if (shownDfSupportNotifications[notification.id]) {
                        return;
                    }

                    shownDfSupportNotifications[notification.id] = true;
                    showDfSupportToast(notification.message, function() {
                        markDfSupportNotificationAsRead(notification.id);
                    });
                });
            });
        }

        function startDfSupportNotifications() {
            pollDfSupportNotifications();
            setInterval(pollDfSupportNotifications, 12000);
        }

        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", startDfSupportNotifications);
        } else {
            startDfSupportNotifications();
        }
    })();
</script>
